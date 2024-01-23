<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Registration_Worker {

	private $worker_registration_couple;

	private $worker_registration_solo;


	public function __construct() {
		$this->worker_registration_couple = new ESR_Registration_Couple_Worker();
		$this->worker_registration_solo   = new ESR_Registration_Solo_Worker();
	}


	public function process_registration( $data ) {
		$status              = false;
		$return_data         = [];
		$registration_return = [];

		if ( $this->course_registration_validation( $data ) ) {
			$registration_return = $this->complete_registration( $data );

			$status = $registration_return != [];
		}

		if ( $status ) {
			$template_thank_you_page       = new ESR_Registration_Thank_You_Text_Helper();
			$return_data['thank_you_text'] = $template_thank_you_page->print_content( $registration_return );
		}

		global $esr_reg_errors;
		if ( count( $esr_reg_errors->get_error_messages() ) !== 0 ) {
			$return_data['errors'] = $esr_reg_errors;
		}

		return $return_data;
	}


	private function complete_registration( $data ) {
		global $esr_reg_errors, $wpdb;
		$return_courses = [];

		if ( 1 > count( $esr_reg_errors->get_error_messages() ) ) {
			$return_data = $this->form_data_validation( $data );
			if ( isset( $return_data['valid'] ) ) {
				$user_id  = ESR()->user->process_user_registration( $data->user_info );
				$email    = htmlspecialchars( strtolower( trim( $data->user_info->email ) ) );
				$note     = isset( $data->user_info->note ) ? htmlspecialchars( $data->user_info->note, ENT_QUOTES, 'UTF-8' ) : null;
				$wave_ids = [];

				foreach ( $return_data['valid'] as $id => $course ) {
					$status                       = null;
					$partner                      = null;
					$partner_reg                  = null;
					$course_id                    = null;
					$new_registration             = [];
					$wave_ids[ $course->wave_id ] = $course->wave_id;

					if ( ! ESR()->course->is_course_already_registered( $id, $user_id ) ) {
						$course_id = intval( $id );
						$isSolo    = ESR()->course->is_course_solo( $course_id );

						$wpdb->query( "START TRANSACTION;" );

						if ( ! $isSolo ) {
							$new_registration = $this->worker_registration_couple->process_registration( $course_id, $user_id, $email, $course, $data, $note );
						} else {
							$new_registration = $this->worker_registration_solo->process_registration( $course_id, $user_id, $course, $data, $note );
						}

						if ( isset( $new_registration['paired'] ) ) {
							$return_courses['paired'][ $course_id ] = $new_registration['paired'][ $course_id ];
						}

						$wpdb->query( "COMMIT;" );

						$return_courses['registered'][ $course_id ] = $course;

					} else {
						$return_courses['already_registered'][ intval( $id ) ] = $course;
					}
				}

				do_action( 'esr_after_wave_registration_complete', $user_id, $wave_ids );

				do_action( 'esr_send_registration_email', $wave_ids, $user_id, $return_courses, $data->user_info );

				if ( ! empty( $note ) ) {
					do_action( 'esr_send_admin_note_email', $user_id, $wave_ids, $data->user_info, $note );
				}
			}

			if ( isset( $return_data['full'] ) ) {
				$return_courses['full'] = $return_data['full'];
			}

			if ( isset( $return_courses['paired'] ) && $return_courses['paired'] ) {
				ESR()->email->send_course_confirmation_emails( $return_courses['paired'] );
			}

			return $return_courses;
		} else {
			return [];
		}
	}


	private function course_registration_validation( $data ) {
		global $esr_reg_errors;
		$esr_reg_errors = new WP_Error;

		if ( $this->check_required( 'name', $data ) ) {
			$esr_reg_errors->add( 'user_info.name', esc_html__( 'Name is required.', 'easy-school-registration' ) );
		}
		if ( $this->check_required( 'surname', $data ) ) {
			$esr_reg_errors->add( 'user_info.surname', esc_html__( 'Surname is required.', 'easy-school-registration' ) );
		}
		if ( $this->check_required( 'email', $data ) ) {
			$esr_reg_errors->add( 'user_info.email.required', esc_html__( 'Email is required.', 'easy-school-registration' ) );
		} else {
			if ( ! is_email( $data->user_info->email ) ) {
				$esr_reg_errors->add( 'user_info.email.invalid', esc_html__( 'Email is not valid', 'easy-school-registration' ) );
			}
		}
		if ( ( intval( ESR()->settings->esr_get_option( 'show_phone_input', 1 ) ) !== - 1 ) && ( intval( ESR()->settings->esr_get_option( 'phone_required', 1 ) ) !== - 1 ) && $this->check_required( 'phone', $data ) ) {
			$esr_reg_errors->add( 'user_info.phone', esc_html__( 'Phone is required.', 'easy-school-registration' ) );
		}

		if ( apply_filters('esr_registration_validation_registration', true, $data) ) {
			$esr_reg_errors->add( 'courses.all.empty', esc_html__(apply_filters('esr_registration_validation_registration_message', __( 'You must choose at least one course.', 'easy-school-registration' ))));
		} else {
			foreach ( $data->courses as $course_id => $course ) {
				if ( ! ESR()->course->is_course_solo( intval( $course_id ) ) ) {
					$dancing_with = isset( $course->dancing_with ) ? trim( $course->dancing_with ) : '';
					if ( ( isset( $course->choose_partner ) && ( intval( $course->choose_partner ) === 1 ) ) || ESR()->course->is_partner_enforcement_enabled( intval( $course_id ) ) ) {
						if ( $dancing_with === '' ) {
							$esr_reg_errors->add( 'courses.' . $course_id . '.email_not_filled', esc_html__( 'Partner email is not filled', 'easy-school-registration' ) );
						}
						if ( ! is_email( $dancing_with ) ) {
							$esr_reg_errors->add( 'courses.' . $course_id . '.email_invalid', esc_html__( 'Partner email is not valid', 'easy-school-registration' ) );
						}
						if ( isset( $data->user_info->email ) && ( trim( $data->user_info->email ) == $dancing_with ) ) {
							$esr_reg_errors->add( 'courses.' . $course_id . '.partner_email', esc_html__( 'Please write your partner email not yours', 'easy-school-registration' ) );
						}
					} else {

						if ( ESR()->course->is_partner_enforcement_enabled( intval( $course_id ) ) && ( $dancing_with === '' ) ) {
							$esr_reg_errors->add( 'courses.' . $course_id . '.partner_required', esc_html__( 'Partner is required for this course', 'easy-school-registration' ) );
						}
					}
					if ( ! isset( $course->dancing_as ) || ( $course->dancing_as === '' ) || ( $course->dancing_as === null ) ) {
						$esr_reg_errors->add( 'courses.' . $course_id . '.dancing_as_invalid', esc_html__( 'Please choose Dancing as', 'easy-school-registration' ) );
					}
				}
			}
		}

		//Check full and already registered courses
		if ( isset( $data->courses ) ) {
			$enabled_waves = ESR()->wave->get_waves_to_process_ids();
			$user_id       = null;
			if ( isset( $data->user_info->email ) && email_exists( $data->user_info->email ) ) {
				$user    = get_user_by( 'email', $data->user_info->email );
				$user_id = $user->ID;
			}
			foreach ( $data->courses as $course_id => $course ) {
				// Check course id
				$course_data = ESR()->course->get_course_data( $course_id );

				if ( ! in_array( $course_data->wave_id, $enabled_waves ) ) {
					$esr_reg_errors->add( 'courses.' . $course_id . '.closed', esc_html__( 'Registration for this waves was closed', 'easy-school-registration' ) );
				}

				// Check dancing_as
				$test_full = true;

				if ( ESR()->course->is_course_solo( $course_id ) ) {
					$test_full = ESR()->dance_as->is_solo_registration_enabled( intval( $course_id ) );
				} else {
					if ( isset( $course->dancing_as ) ) {
						if ( ESR()->dance_as->is_leader( $course->dancing_as ) ) {
							$test_full = ESR()->dance_as->is_leader_registration_enabled( intval( $course_id ) );
						} else if ( ESR()->dance_as->is_follower( $course->dancing_as ) ) {
							$test_full = ESR()->dance_as->is_followers_registration_enabled( intval( $course_id ) );
						}
					}
				}

				if ( ! $test_full ) {
					$esr_reg_errors->add( 'courses.' . $course_id . '.full', esc_html__( 'Course is full', 'easy-school-registration' ) );
				}

				if ( $user_id !== null ) {
					if ( ESR()->course->is_course_already_registered( $course_id, $user_id ) ) {
						$esr_reg_errors->add( 'courses.' . $course_id . '.already_registered', esc_html__( 'You have already registered this course', 'easy-school-registration' ) );
					}
				}

			}

		}

		return count( $esr_reg_errors->get_error_messages() ) === 0;
	}


	private function form_data_validation( $data ) {
		$return_data = [];

		$enabled_waves = ESR()->wave->get_waves_to_process_ids();

		if ( isset( $data->courses ) ) {
			foreach ( $data->courses as $course_id => $course ) {
				// Check course id
				$course_data = ESR()->course->get_course_data( $course_id );

				$test1_valid = in_array( $course_data->wave_id, $enabled_waves );

				if ( ! $test1_valid ) {
					$return_data['full'][ $course_id ] = $course;
				}

				// Check dancing_as
				$test2_valid = true;

				if ( ESR()->course->is_course_solo( $course_id ) ) {
					$test2_valid = ESR()->dance_as->is_solo_registration_enabled( $course_id );
				} else {
					if ( ESR()->dance_as->is_leader( $course->dancing_as ) ) {
						$test2_valid = ESR()->dance_as->is_leader_registration_enabled( $course_id );
					} else if ( ESR()->dance_as->is_follower( $course->dancing_as ) ) {
						$test2_valid = ESR()->dance_as->is_followers_registration_enabled( $course_id );
					}
				}

				if ( ! $test2_valid ) {
					$return_data['full'][ $course_id ] = $course;
				}

				if ( $test1_valid && $test2_valid ) {
					$course->wave_id                    = $course_data->wave_id;
					$return_data['valid'][ $course_id ] = $course;
				}
			}
		}

		return $return_data;
	}


	private function check_required( $key, $data ) {
		return ! isset( $data->user_info->$key ) || trim( $data->user_info->$key ) == '';
	}


	public function save_registration( $course_id, $user, $partner, $dancing_as, $status, $free_registration = ESR_Enum_Free_Registration::PAID ) {
		global $wpdb;

		$wpdb->insert( $wpdb->prefix . 'esr_course_registration', [
			'user_id'           => $user->ID,
			'course_id'         => $course_id,
			'partner_id'        => $partner ? $partner->ID : null,
			'dancing_as'        => $dancing_as,
			'dancing_with'      => $partner ? $partner->user_email : null,
			'status'            => $status,
			'free_registration' => $free_registration,
			'confirmation_time' => current_time( 'Y-m-d H:i:s' )
		] );

		return $wpdb->insert_id;
	}

    public static function esr_registration_validation_registration_callback($stop_registration, $data) {
        return !isset($data->courses) || !$data->courses; //Default validation if there are some courses selected
    }
}

add_filter('esr_registration_validation_registration', ['ESR_Registration_Worker', 'esr_registration_validation_registration_callback'], 10, 2);
