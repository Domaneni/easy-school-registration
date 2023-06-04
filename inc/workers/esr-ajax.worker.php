<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Ajax_Worker {

	public function process_add_user_course_registration( $registration ) {
		global $wpdb;

		if ( $registration ) {
			$is_solo = ESR()->course->is_course_solo( $registration->course_id );

			$wpdb->update( $wpdb->prefix . 'esr_course_registration', [
				'partner_id'        => null,
				'status'            => ESR_Registration_Status::CONFIRMED,
				'confirmation_time' => current_time( 'Y-m-d H:i:s' ),
			], [
				'id' => $registration->id,
			] );

			$update  = [];
			$summary = ESR()->course_summary->get_course_summary( $registration->course_id );

			if ( $is_solo ) {
				$update['registered_solo'] = $summary->registered_solo + 1;
				if ( ESR()->registration_status->is_waiting( $registration->status ) ) {
					$update['waiting_solo'] = $summary->waiting_solo - 1;
				}
			} else if ( ESR()->dance_as->is_follower( $registration->dancing_as ) ) {
				$update['registered_followers'] = $summary->registered_followers + 1;
				if ( ESR()->registration_status->is_waiting( $registration->status ) ) {
					$update['waiting_followers'] = $summary->waiting_followers - 1;
				}
			} else if ( ESR()->dance_as->is_leader( $registration->dancing_as ) ) {
				$update['registered_leaders'] = $summary->registered_leaders + 1;
				if ( ESR()->registration_status->is_waiting( $registration->status ) ) {
					$update['waiting_leaders'] = $summary->waiting_leaders - 1;
				}
			}

			if ( $update !== [] ) {
				$wpdb->update( $wpdb->prefix . 'esr_course_summary', $update, [
					'course_id' => $registration->course_id,
				] );
			}

			do_action( 'esr_log_esr_message', 'registration_confirmed', 'info', sprintf( 'Registration %d was confirmed.', $registration->id ) );

			// Update payments
			$worker_payment = new ESR_Payments_Worker();
			$course_data    = ESR()->course->get_course_data( $registration->course_id );

			$registration_data['reg_id'] = $registration->id;
			if ( intval( ESR()->settings->esr_get_option( 'floating_price_enabled', - 1 ) ) !== - 1 ) {
				$old_payment = apply_filters( 'esr_get_student_payment', [ 'wave_id' => $course_data->wave_id, 'user_id' => $registration->user_id ] )['to_pay'];
				if ( $old_payment ) {
					$registration_data['previous_price'] = $old_payment;
				}
			}

			$worker_payment->update_user_payment( $registration->user_id, $course_data->wave_id );

			if ( intval( ESR()->settings->esr_get_option( 'floating_price_enabled', - 1 ) ) !== - 1 ) {
				$new_payment = apply_filters( 'esr_get_student_payment', [ 'wave_id' => $course_data->wave_id, 'user_id' => $registration->user_id ] )['to_pay'];
				if ( $new_payment ) {
					$registration_data['actual_price'] = $new_payment;
				}
			}

			ESR()->email->send_course_registration_email( $registration_data );

			return [
				'status_title' => ESR()->registration_status->get_title( ESR_Registration_Status::CONFIRMED ),
			];
		}

		return - 1;
	}


	public function remove_user_course_registration_callback( $registration ) {
		global $wpdb;

		if ( $registration ) {
			$partner_registration = null;

			$is_solo = ESR()->course->is_course_solo( $registration->course_id );
			if ( $registration->partner_id && ! $is_solo ) {
				$wpdb->update( $wpdb->prefix . 'esr_course_registration', [
					'partner_id' => null,
				], [
					'user_id'   => $registration->partner_id,
					'course_id' => $registration->course_id,
				] );

				$partner_registration = ESR()->registration->esr_get_user_course_callback( $registration->partner_id, $registration->course_id );
			}

			$wpdb->update( $wpdb->prefix . 'esr_course_registration', [
				'partner_id' => null,
				'status'     => ESR_Registration_Status::DELETED
			], [
				'id' => $registration->id,
			] );

			$update  = [];
			$summary = ESR()->course_summary->get_course_summary( $registration->course_id );

			if ( in_array( $registration->status, [ ESR_Registration_Status::WAITING, ESR_Registration_Status::CONFIRMED ] ) ) {
				if ( $registration->status == ESR_Registration_Status::CONFIRMED ) {
					if ( $is_solo ) {
						$update['registered_solo'] = $summary->registered_solo - 1;
					} else if ( ESR()->dance_as->is_follower( $registration->dancing_as ) ) {
						$update['registered_followers'] = $summary->registered_followers - 1;
					} else if ( ESR()->dance_as->is_leader( $registration->dancing_as ) ) {
						$update['registered_leaders'] = $summary->registered_leaders - 1;
					}
				} else if ( $registration->status == ESR_Registration_Status::WAITING ) {
					if ( ESR()->dance_as->is_follower( $registration->dancing_as ) ) {
						$update['waiting_followers'] = $summary->waiting_followers - 1;
					} else if ( ESR()->dance_as->is_leader( $registration->dancing_as ) ) {
						$update['waiting_leaders'] = $summary->waiting_leaders - 1;
					}
				}

				if ( $update !== [] ) {
					$wpdb->update( $wpdb->prefix . 'esr_course_summary', $update, [
						'course_id' => $registration->course_id,
					] );
				}
			}

			// Update payments
			$worker_payment = new ESR_Payments_Worker();
			$course_data    = ESR()->course->get_course_data( $registration->course_id );

			do_action( 'esr_on_registration_remove', $registration, $course_data->wave_id );

			$worker_payment->update_user_payment( $registration->user_id, $course_data->wave_id );

			do_action( 'esr_log_esr_message', 'registration_cancel', 'info', sprintf( 'Registration %d was canceled.', $registration->id ) );

			return [
				'status_title'         => ESR()->registration_status->get_title( ESR_Registration_Status::DELETED ),
				'partner_registration' => $registration->partner_id !== null ? $partner_registration->id : 0,
			];
		}

		return - 1;
	}


	public function remove_registration_forever_callback( $registration ) {
		global $wpdb;

		if ( $registration ) {

			do_action( 'esr_log_esr_message', 'registration_removed', 'info', sprintf( 'Registration %d was removed forever.', $registration->id ) );

			return $wpdb->delete( $wpdb->prefix . 'esr_course_registration', [
				'id'     => $registration->id,
				'status' => ESR_Registration_Status::DELETED,
			] );
		}

		return false;
	}


	public function edit_course_registration_callback( $data ) {
		global $wpdb;

		$registration_id  = intval( $data['registration_id'] );
		$old_registration = ESR()->registration->get_registration( $registration_id );
		$new_registration = null;

		$student        = get_user_by( 'email', trim( sanitize_email($data['student_email'] ) ));
        $dancing_with_email = trim( sanitize_email($data['dancing_with']) );
		$dancing_with   = get_user_by( 'email', $dancing_with_email);
		$partner        = get_user_by( 'email', trim( sanitize_email($data['partner_email'] ) ));
		$new_dancing_as = sanitize_text_field($data['dancing_as']);
		$old_course_id  = intval( $old_registration->course_id );
		$new_course_id  = isset( $data['course_id'] ) ? intval( $data['course_id'] ) : intval( $old_registration->course_id );

		$course_old_is_solo = ESR()->course->is_course_solo( $old_course_id );
		$course_new_is_solo = ESR()->course->is_course_solo( $new_course_id );

		if ( ! $student ) {
			return [
				'error' => [
					'student' => esc_html__( 'Student with this email do not exist', 'easy-school-registration' )
				]
			];
		}

		if ( ( trim( $data['partner_email'] ) !== '' ) && ! $partner ) {
			return [
				'error' => [
					'partner' => esc_html__( 'Partner with this email do not exist', 'easy-school-registration' )
				]
			];
		}

		//TODO: check course_id exists
		if ( ! $new_course_id || ( $new_course_id < 0 ) ) {
			return [
				'error' => [
					'course' => esc_html__( 'Course ' . $new_course_id . ' does not exist', 'easy-school-registration' )
				]
			];
		}

		if ($course_new_is_solo) {
			$new_dancing_as = ESR_Dancing_As::SOLO;
		}

		$wpdb->update( $wpdb->prefix . 'esr_course_registration', [
			'user_id'      => $student->ID,
			'dancing_with' => !empty($dancing_with_email) ? $dancing_with_email : null,
			'dancing_as'   => $new_dancing_as,
			'partner_id'   => $partner ? $partner->ID : null
		], [
			'id' => $registration_id,
		] );

		$new_registration = ESR()->registration->get_registration( $registration_id );

		//UPDATE summary
		if ( ( intval( $new_dancing_as ) !== intval( $old_registration->dancing_as ) ) && ( $new_course_id === $old_course_id ) ) {
			$summary = ESR()->course_summary->get_course_summary( $old_registration->course_id );
			$update  = [];
			if ( $old_registration->status == ESR_Registration_Status::CONFIRMED ) {
				if ( ESR()->dance_as->is_follower( $old_registration->dancing_as ) ) {
					$update['registered_followers'] = $summary->registered_followers - 1;
					$update['registered_leaders']   = $summary->registered_leaders + 1;
				} else if ( ESR()->dance_as->is_leader( $old_registration->dancing_as ) ) {
					$update['registered_leaders']   = $summary->registered_leaders - 1;
					$update['registered_followers'] = $summary->registered_followers + 1;
				}
			} else if ( $old_registration->status == ESR_Registration_Status::WAITING ) {
				if ( ESR()->dance_as->is_follower( $old_registration->dancing_as ) ) {
					$update['waiting_followers'] = $summary->waiting_followers - 1;
					$update['waiting_leaders']   = $summary->waiting_leaders + 1;
				} else if ( ESR()->dance_as->is_leader( $old_registration->dancing_as ) ) {
					$update['waiting_leaders']   = $summary->waiting_leaders - 1;
					$update['waiting_followers'] = $summary->waiting_followers + 1;
				}
			}

			if ( ! empty( $update ) ) {
				$wpdb->update( $wpdb->prefix . 'esr_course_summary', $update, [
					'course_id' => $old_registration->course_id,
				] );
			}
		}


		if ( $new_course_id !== $old_course_id ) {
			$wpdb->update( $wpdb->prefix . 'esr_course_registration', [
				'course_id' => $new_course_id,
			], [
				'id' => $registration_id,
			] );

			$summary_old = ESR()->course_summary->get_course_summary( $old_course_id );
			$summary_new = ESR()->course_summary->get_course_summary( $new_course_id );
			$update_old  = [];
			$update_new  = [];

			$new_registration = ESR()->registration->get_registration( $registration_id );
			if ( intval( $new_registration->status ) == ESR_Registration_Status::CONFIRMED ) {
				if ( $course_old_is_solo ) {// Update old data
					$update_old['registered_solo'] = $summary_old->registered_solo - 1;
				} else {
					if ( ESR()->dance_as->is_follower( $old_registration->dancing_as ) ) {
						$update_old['registered_followers'] = $summary_old->registered_followers - 1;
					} else if ( ESR()->dance_as->is_leader( $old_registration->dancing_as ) ) {
						$update_old['registered_leaders'] = $summary_old->registered_leaders - 1;
					}
				}
				if ( $course_new_is_solo ) {// Update new data
					$update_new['registered_solo'] = $summary_new->registered_solo + 1;
				} else {
					if ( ESR()->dance_as->is_follower( $new_registration->dancing_as ) ) {
						$update_new['registered_followers'] = $summary_new->registered_followers + 1;
					} else if ( ESR()->dance_as->is_leader( $new_registration->dancing_as ) ) {
						$update_new['registered_leaders'] = $summary_new->registered_leaders + 1;
					}
				}
			} else if ( intval( $new_registration->status ) == ESR_Registration_Status::WAITING ) {
				if ( $course_old_is_solo ) {// Update old data
					$update_old['waiting_solo'] = $summary_old->waiting_solo - 1;
				} else {
					if ( ESR()->dance_as->is_follower( $old_registration->dancing_as ) ) {
						$update_old['waiting_followers'] = $summary_old->waiting_followers - 1;
					} else if ( ESR()->dance_as->is_leader( $old_registration->dancing_as ) ) {
						$update_old['waiting_leaders'] = $summary_old->waiting_leaders - 1;
					}
				}
				if ( $course_new_is_solo ) {// Update new data
					$update_new['waiting_solo'] = $summary_new->waiting_solo + 1;
				} else {
					if ( ESR()->dance_as->is_follower( $new_registration->dancing_as ) ) {
						$update_new['waiting_followers'] = $summary_new->waiting_followers + 1;
					} else if ( ESR()->dance_as->is_leader( $new_registration->dancing_as ) ) {
						$update_new['waiting_leaders'] = $summary_new->waiting_leaders + 1;
					}
				}
			}

			if ( ! empty( $update_old ) ) {
				$wpdb->update( $wpdb->prefix . 'esr_course_summary', $update_old, [
					'course_id' => $old_course_id,
				] );
			}

			if ( ! empty( $update_new ) ) {
				$wpdb->update( $wpdb->prefix . 'esr_course_summary', $update_new, [
					'course_id' => $new_course_id,
				] );
			}
		}

		$registrations_status_changed = [];

		if ( intval( $new_registration->status ) == ESR_Registration_Status::WAITING ) {
			if ( $course_new_is_solo ) {
				$result = apply_filters( 'esr_process_solo_pairing', [
					'course_id'       => $new_course_id,
					'user_id'         => $new_registration->user_id,
					'course_data'     => ESR()->course->get_course_data( $new_course_id ),
					'registration_id' => $new_registration->id,
					'return_courses'  => []
				] )['return_courses'];

				if ( isset( $result['paired'] ) ) {
					$summary_new = ESR()->course_summary->get_course_summary( $new_course_id );
					$wpdb->update( $wpdb->prefix . 'esr_course_summary', [
						'waiting_solo' => $summary_new->waiting_solo - 1
					], [
						'course_id' => $new_course_id,
					] );

					ESR()->email->send_course_confirmation_emails( $result['paired'] );

					$registrations_status_changed = $result['paired'][$new_course_id];
				}
			} else {
				$result = apply_filters( 'esr_process_couple_pairing', [
					'course_id'       => $new_course_id,
					'user_id'         => intval( $new_registration->user_id ),
					'student_email'   => get_userdata( $new_registration->user_id )->user_email,
					'dancing_as'      => intval( $new_registration->dancing_as ),
					'dancing_with'    => $new_registration->dancing_with,
					'registration_id' => intval( $new_registration->id ),
					'disable_wcu'     => true,
					'return_courses'  => []
				] )['return_courses'];

				if ( isset( $result['paired'] ) ) {
					$summary_new    = ESR()->course_summary->get_course_summary( $new_course_id );
					$update_pairing = [];
					if ( ESR()->dance_as->is_follower( $new_registration->dancing_as ) ) {
						$update_pairing['waiting_followers'] = $summary_new->waiting_followers - 1;
					} else if ( ESR()->dance_as->is_leader( $new_registration->dancing_as ) ) {
						$update_pairing['waiting_leaders'] = $summary_new->waiting_leaders - 1;
					}

					$wpdb->update( $wpdb->prefix . 'esr_course_summary', $update_pairing, [
						'course_id' => $new_course_id,
					] );

					ESR()->email->send_course_confirmation_emails( $result['paired'] );

					$registrations_status_changed = $result['paired'][$new_course_id];
				}
			}
		}

		$json_data = [
			'student'    => [
				'name'  => $student->display_name,
				'email' => $student->user_email
			],
			'dancing_as' => [
				'id'   => $new_dancing_as,
				'text' => ESR()->dance_as->get_title( $new_dancing_as ),
			],
		];

		if ( $partner ) {
			$json_data['partner'] = [
				'name'  => $partner->display_name,
				'email' => $partner->user_email
			];
		}

		if ( ! empty( $dancing_with_email ) ) {
			$json_data['dancing_with'] = [
				'email' => $dancing_with_email
			];
		}

		if (!empty($registrations_status_changed)) {
			$json_data['confirm_registrations'] = $registrations_status_changed;
			$json_data['status_title'] = ESR()->registration_status->get_title(ESR_Registration_Status::CONFIRMED);
		}

		//UPDATE payments
		$worker_payment = new ESR_Payments_Worker();
		$course_data    = ESR()->course->get_course_data( $new_course_id );
		$worker_payment->update_user_payment( $old_registration->user_id, $course_data->wave_id );
		$worker_payment->update_user_payment( $student->ID, $course_data->wave_id );

		if ( isset( $data['course_id'] ) && ( $old_course_id !== $new_course_id ) ) {
			$json_data['course']            = $course_data;
			$json_data['course']->day_title = ESR()->day->get_day_title( $course_data->day );
		}

		return $json_data;
	}


	/**
	 * @param int $wave_id
	 * @param boolean $passed
	 *
	 * @return int
	 */
	public function change_wave_passed( $wave_id, $passed ) {
		if ( $wave_id ) {
			global $wpdb;
			$wpdb->update( $wpdb->prefix . 'esr_wave_data', [
				'is_passed' => $passed
			], [
				'id' => $wave_id
			] );

			$wpdb->update( $wpdb->prefix . 'esr_course_data', [
				'is_passed' => $passed
			], [
				'wave_id' => $wave_id
			] );

			return 1;
		}

		return - 1;
	}


	/**
	 * @param int $course_id
	 * @param boolean $passed
	 *
	 * @return int|array
	 */
	public function change_course_passed( $course_id, $passed ) {
		if ( $course_id ) {
			global $wpdb;
			$wpdb->update( $wpdb->prefix . 'esr_course_data', [
				'is_passed' => $passed
			], [
				'id' => $course_id
			] );

			return [
				'status' => $passed ? esc_html__( 'Passed', 'easy-school-registration' ) : esc_html__( 'Active', 'easy-school-registration' )
			];
		}

		return - 1;
	}


	/**
	 * @param int $teacher_id
	 * @param boolean $active
	 *
	 * @return int
	 */
	public function change_teacher_active( $teacher_id, $active ) {
		if ( $teacher_id ) {
			global $wpdb;
			$wpdb->update( $wpdb->prefix . 'esr_teacher_data', [
				'active' => $active
			], [
				'id' => $teacher_id
			] );

			return 1;
		}

		return - 1;
	}


	public function save_payment( $user_email, $payment_status, $data ) {
		if ( $user_email && $payment_status ) {
			$student = get_user_by( 'email', $user_email );
			$price   = null;

			if ( ! $student ) {
				return [
					'error' => [
						'student' => esc_html__( 'Student with this email do not exist', 'easy-school-registration' )
					]
				];
			}

			if ( ( $payment_status === 'paid' ) && isset( $data['payment'] ) ) {
				$price = !empty( $data['payment'] ) ? floatval($data['payment']) : 0;
			}

            $note = isset( $data['note'] ) ? htmlspecialchars(sanitize_textarea_field($data['note'])) : null;
            $payment_type = isset( $data['note'] ) ? sanitize_text_field($data['payment_type']) : null;
            $wave_id = isset( $data['wave_id'] ) ? intval($data['wave_id']) : null;

			if ( ( $price !== null ) || ( $payment_status === 'not_paying' ) ) {
				do_action( 'esr_save_user_payment', [
					'payment'            => $price,
					'is_paying'          => ( $payment_status === 'paid' ),
					'note'               => $note,
					'confirm_by_user_id' => true,
					'confirm_user_id'    => get_current_user_id(),
					'confirm_timestamp'  => current_time( 'Y-m-d H:i:s' ),
					'status'             => ESR_Enum_Payment::PAID,
					'payment_type'       => $payment_type,
				], [
					'user_id' => $student->ID,
					'wave_id' => $wave_id,
				], ESR()->payment->get_payment_by_wave_and_user( $wave_id, $student->ID ) );
			}

			if ( isset( $data['esr_payment_email_confirmation'] ) && filter_var( $data['esr_payment_email_confirmation'], FILTER_VALIDATE_BOOLEAN ) ) {
				do_action( 'esr_send_payment_confirmation_email', $wave_id, $user_email );
			}

			$new_status = ESR()->payment_status->get_status( ESR()->payment->get_payment_by_wave_and_user( $wave_id, $student->ID ) );

			return [
				'payment'              => ESR()->currency->prepare_price( $price ),
				'payment_status'       => $new_status,
				'payment_status_title' => ESR()->payment_status->get_title( $new_status ),
				'price'                => $price,
				'payment_type'         => ESR()->payment_type->get_title( $payment_type ),
				'payment_note'         => $note
			];
		}

		return - 1;
	}


	public function esr_load_student_data( $user_id ) {
		$user_data = [];

		if ( $user_id ) {
			$user      = get_user_by( 'id', $user_id );
			$user_meta = get_user_meta( $user_id );

			if ( $user ) {
				$user_data['data']['name']  = esc_html($user->display_name);
				$user_data['data']['email'] = esc_html($user->user_email);
			}

			$user_data['note'] = esc_html(get_user_meta( $user_id, 'esr_student_note', true ));
			$user_data['user_id'] = esc_html($user_id);

			if ( $user_meta ) {
				$user_data['data']['phone'] = isset( $user_meta['esr-course-registration-phone'] ) ? esc_html($user_meta['esr-course-registration-phone'][0]) : '';
			}

			$user_data['registrations'] = ESR()->registration->get_registrations_by_user( $user_id );
			$user_data['payments']      = ESR()->payment->get_payments_for_export( $user_id );

			$user_data['registration_status'] = ESR()->registration_status->get_items();
			$user_data['payment_status']      = ESR()->payment_status->get_items();
		}

		return json_encode( $user_data );
	}


	public static function esr_set_free_registration_value_callback( $data ) {
		if ( isset( $data['registration_id'] ) && isset( $data['free_registration_value'] ) ) {
			global $wpdb;

			$wpdb->update( $wpdb->prefix . 'esr_course_registration', [ 'free_registration' => intval( $data['free_registration_value'] ) ], [ 'id' => intval( $data['registration_id'] ) ] );

			do_action( 'esr_update_user_payment_by_registration', $data['registration_id'] );
		}

		return $data;
	}

}

add_filter( 'esr_set_free_registration_value', [ 'ESR_Ajax_Worker', 'esr_set_free_registration_value_callback' ] );
