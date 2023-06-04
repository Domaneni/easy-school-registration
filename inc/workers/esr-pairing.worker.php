<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Pairing_Worker {

	public static function esr_process_couple_pairing_callback($data) {
		global $wpdb;
		$worker_payment = new ESR_Payments_Worker();

		$summary           = ESR()->course_summary->get_course_summary($data['course_id']);
		$leaders_enabled   = ESR()->dance_as->is_leader_registration_enabled($data['course_id']);
		$followers_enabled = ESR()->dance_as->is_followers_registration_enabled($data['course_id']);
		$course_data       = ESR()->course->get_course_data($data['course_id']);

		if ((ESR()->dance_as->is_leader($data['dancing_as']) && $leaders_enabled) || (ESR()->dance_as->is_follower($data['dancing_as']) && $followers_enabled)) {
			$partner_reg = null;

			if ($leaders_enabled && $followers_enabled && ESR()->pairing_mode->is_pairing_enabled($course_data->pairing_mode)) {
				if (($data['dancing_with'] == null) || ($data['dancing_with'] === '')) {
					$data['dancing_with'] = '';
				}

				$partner_reg = $wpdb->get_results($wpdb->prepare("SELECT cr.id, cr.user_id, CASE  WHEN (cr.dancing_with IS NOT NULL AND %s NOT LIKE '' AND cr.dancing_with LIKE %s AND u.user_email LIKE %s) THEN 1 WHEN (cr.dancing_with IS NOT NULL AND cr.dancing_with LIKE %s) THEN 2 WHEN (%s NOT LIKE '' AND u.user_email LIKE %s) THEN 2 WHEN ((cr.dancing_with IS NULL OR cr.dancing_with LIKE '') AND %s LIKE '') THEN 3 END AS ord FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->users} AS u ON cr.user_id = u.id WHERE cr.id != %d AND cr.course_id = %d AND cr.partner_id IS NULL AND cr.status = %d AND cr.dancing_as != %d AND ((cr.dancing_with IS NOT NULL AND %s NOT LIKE '' AND cr.dancing_with LIKE %s AND u.user_email LIKE %s) OR (cr.dancing_with IS NOT NULL AND cr.dancing_with LIKE %s) OR (%s NOT LIKE '' AND u.user_email LIKE %s) OR ((cr.dancing_with IS NULL OR cr.dancing_with LIKE '') AND %s LIKE '')) ORDER BY ord, cr.time, cr.id ASC LIMIT 1;", [
					$data['dancing_with'],
					$data['student_email'],
					$data['dancing_with'],
					$data['student_email'],
					$data['dancing_with'],
					$data['dancing_with'],
					$data['dancing_with'],
					$data['registration_id'],
					$data['course_id'],
					ESR_Registration_Status::WAITING,
					$data['dancing_as'],
					$data['dancing_with'],
					$data['student_email'],
					$data['dancing_with'],
					$data['student_email'],
					$data['dancing_with'],
					$data['dancing_with'],
					$data['dancing_with']
				]));
			}
			if ($partner_reg && $partner_reg[0]->id && $partner_reg[0]->user_id) {
				$confirmation_time = current_time('Y-m-d H:i:s');

				$wpdb->update($wpdb->prefix . 'esr_course_registration', [
					'partner_id'        => $partner_reg[0]->user_id,
					'status'            => ESR_Registration_Status::CONFIRMED,
					'confirmation_time' => $confirmation_time,
				], [
					'id' => $data['registration_id']
				]);

				$wpdb->update($wpdb->prefix . 'esr_course_registration', [
					'partner_id'        => $data['user_id'],
					'status'            => ESR_Registration_Status::CONFIRMED,
					'confirmation_time' => $confirmation_time,
				], [
					'id' => $partner_reg[0]->id
				]);

				ESR()->course_summary->update_course_summary($data['course_id'], [
					'registered_leaders'   => intval($summary->registered_leaders) + 1,
					'registered_followers' => intval($summary->registered_followers) + 1,
					'waiting_leaders'      => (!ESR()->dance_as->is_leader($data['dancing_as']) ? intval($summary->waiting_leaders) - 1 : intval($summary->waiting_leaders)),
					'waiting_followers'    => (!ESR()->dance_as->is_follower($data['dancing_as']) ? intval($summary->waiting_followers) - 1 : intval($summary->waiting_followers)),
				]);


				if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
					$old_payment = apply_filters('esr_get_student_payment', ['wave_id' => $course_data->wave_id, 'user_id' => $data['user_id']])['to_pay'];

					if ($old_payment) {
						$data['return_courses']['paired'][$data['course_id']]['student']['previous_price'] = $old_payment;
					}

					$old_payment = apply_filters('esr_get_student_payment', ['wave_id' => $course_data->wave_id, 'user_id' => $partner_reg[0]->user_id])['to_pay'];

					if ($old_payment) {
						$data['return_courses']['paired'][$data['course_id']]['partner']['previous_price'] = $old_payment;
					}
				}

				$worker_payment->update_user_payment((int) $data['user_id'], (int) $course_data->wave_id);
				$worker_payment->update_user_payment((int) $partner_reg[0]->user_id, (int) $course_data->wave_id);

				if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
					$new_payment = apply_filters('esr_get_student_payment', ['wave_id' => $course_data->wave_id, 'user_id' => $data['user_id']])['to_pay'];

					if ($new_payment) {
						$data['return_courses']['paired'][$data['course_id']]['student']['actual_price'] = $new_payment;
					}

					$new_payment = apply_filters('esr_get_student_payment', ['wave_id' => $course_data->wave_id, 'user_id' => $partner_reg[0]->user_id])['to_pay'];

					if ($new_payment) {
						$data['return_courses']['paired'][$data['course_id']]['partner']['actual_price'] = $new_payment;
					}
				}

				$data['return_courses']['paired'][$data['course_id']]['student']['reg_id'] = (int) $data['registration_id'];
				$data['return_courses']['paired'][$data['course_id']]['partner']['reg_id'] = (int) $partner_reg[0]->id;
			} else if (ESR()->pairing_mode->is_auto_confirmation_enabled($course_data->pairing_mode)) {
				// Confirm all registrations until course is full
				$wpdb->update($wpdb->prefix . 'esr_course_registration', [
					'status'            => ESR_Registration_Status::CONFIRMED,
					'confirmation_time' => current_time('Y-m-d H:i:s')
				], [
					'id' => $data['registration_id']
				]);

				if (ESR()->dance_as->is_leader($data['dancing_as'])) {
					ESR()->course_summary->update_course_summary($data['course_id'], [
						'registered_leaders' => intval($summary->registered_leaders) + 1,
					]);
				} else {
					ESR()->course_summary->update_course_summary($data['course_id'], [
						'registered_followers' => intval($summary->registered_followers) + 1,
					]);
				}

				if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
					$old_payment = apply_filters('esr_get_student_payment', ['wave_id' => $course_data->wave_id, 'user_id' => $data['user_id']])['to_pay'];

					if ($old_payment) {
						$data['return_courses']['paired'][$data['course_id']]['student']['previous_price'] = $old_payment;
					}
				}

				$worker_payment->update_user_payment((int) $data['user_id'], (int) $course_data->wave_id);

				if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
					$actual_payment = apply_filters('esr_get_student_payment', ['wave_id' => $course_data->wave_id, 'user_id' => $data['user_id']])['to_pay'];

					if ($actual_payment) {
						$data['return_courses']['paired'][$data['course_id']]['student']['actual_price'] = $actual_payment;
					}
				}

				$data['return_courses']['paired'][$data['course_id']]['student']['reg_id'] = (int) $data['registration_id'];


			} else {
				if (!isset($data['disable_wcu']) || (isset($data['disable_wcu']) && !$data['disable_wcu'])) {
					if (ESR()->dance_as->is_leader($data['dancing_as'])) {
						ESR()->course_summary->update_course_summary($data['course_id'], [
							'waiting_leaders' => intval($summary->waiting_leaders) + 1,
						]);
					} else {
						ESR()->course_summary->update_course_summary($data['course_id'], [
							'waiting_followers' => intval($summary->waiting_followers) + 1,
						]);
					}
				}
			}
		}

		return $data;
	}

	public static function esr_process_solo_pairing_callback($data) {
		global $wpdb;
		$worker_payment = new ESR_Payments_Worker();
		$summary = ESR()->course_summary->get_course_summary($data['course_id']);

		if (intval(ESR()->pairing_mode->get_solo_course_default_status($data['course_data']->pairing_mode)) === ESR_Registration_Status::CONFIRMED) {

			$wpdb->update($wpdb->prefix . 'esr_course_registration', [
				'status' => ESR_Registration_Status::CONFIRMED,
				'confirmation_time' => current_time('Y-m-d H:i:s'),
			], [
				'id' => $data['registration_id']
			]);

			ESR()->course_summary->update_course_summary($data['course_id'], [
				'registered_solo' => intval($summary->registered_solo) + 1,
			]);

			if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
				$old_payment = apply_filters('esr_get_student_payment', ['wave_id' => $data['course_data']->wave_id, 'user_id' => $data['user_id']])['to_pay'];

				if ($old_payment) {
					$data['return_courses']['paired'][$data['course_id']]['student']['previous_price'] = $old_payment;
				}
			}

			$data['return_courses']['paired'][$data['course_id']]['student']['reg_id'] = $data['registration_id'];

			$worker_payment->update_user_payment((int)$data['user_id'], (int)$data['course_data']->wave_id);

			if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
				$new_payment = apply_filters('esr_get_student_payment', ['wave_id' => $data['course_data']->wave_id, 'user_id' => $data['user_id']])['to_pay'];

				if ($new_payment) {
					$data['return_courses']['paired'][$data['course_id']]['student']['actual_price'] = $new_payment;
				}
			}
		} else {
			ESR()->course_summary->update_course_summary($data['course_id'], [
				'waiting_solo' => intval($summary->waiting_solo) + 1,
			]);
		}

		return $data;
	}
}

add_filter('esr_process_couple_pairing', ['ESR_Pairing_Worker', 'esr_process_couple_pairing_callback']);
add_filter('esr_process_solo_pairing', ['ESR_Pairing_Worker', 'esr_process_solo_pairing_callback']);