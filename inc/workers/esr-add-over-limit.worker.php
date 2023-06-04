<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Add_Over_Limit_Worker {

	private $worker_registration;

	private $worker_payment;


	public function __construct() {
		$this->worker_registration = new ESR_Registration_Worker();
		$this->worker_payment      = new ESR_Payments_Worker();
	}


	public function process_form($data) {
		if (isset($data['esr_add_over_limit_submit'])) {
			$leader   = $this->add_user($data, 'esr_leader_email', 'esr_leader_name', 'esr_leader_surname', 'esr_leader_phone');
			$follower = $this->add_user($data, 'esr_follower_email', 'esr_follower_name', 'esr_follower_surname', 'esr_follower_phone');
			$disable_emails = isset($data['esr_disable_emails']) && (intval($data['esr_disable_emails']) === 1);

            $course_id = intval($data['esr_course_id']);
			$course_data = ESR()->course->get_course_data($course_id);

			if ($leader && !ESR()->course->is_course_already_registered($course_id, $leader->ID)) {
				$registration_data = [];
				if (!$course_data->is_solo) {
					$registration_data['reg_id'] = $this->add_registrations($course_id, $leader, $follower, ESR_Dancing_As::LEADER, isset($data['esr_leader_free_registration']));
				} else {
					$registration_data['reg_id'] = $this->add_registrations($course_id, $leader, null, ESR_Dancing_As::SOLO, isset($data['esr_leader_free_registration']));
				}

				if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
					$old_payment = apply_filters('esr_get_student_payment', ['wave_id' => $course_data->wave_id, 'user_id' => $leader->ID])['to_pay'];

					if ($old_payment) {
						$registration_data['previous_price'] = $old_payment;
					}
				}

				do_action('esr_after_student_registration', $registration_data['reg_id'], $course_data->id, $registration_data);

				$this->worker_payment->update_user_payment($leader->ID, $course_data->wave_id);

				if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
					$new_payment = apply_filters('esr_get_student_payment', ['wave_id' => $course_data->wave_id, 'user_id' => $leader->ID])['to_pay'];

					if ($new_payment) {
						$registration_data['actual_price'] = $new_payment;
					}
				}

				$this->update_summary($course_id, ESR_Dancing_As::LEADER);
				if (!$disable_emails) {
                    ESR()->email->send_course_registration_email($registration_data);
                }
			}

			if ($follower && !ESR()->course->is_course_already_registered($course_id, $follower->ID)) {
				$registration_data = [];
				if (!$course_data->is_solo) {
					$registration_data['reg_id'] = $this->add_registrations($course_id, $follower, $leader, ESR_Dancing_As::FOLLOWER, isset($data['esr_follower_free_registration']));
				} else {
					$registration_data['reg_id'] = $this->add_registrations($course_id, $follower, null, ESR_Dancing_As::SOLO, isset($data['esr_follower_free_registration']));
				}

				if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
					$old_payment = apply_filters('esr_get_student_payment', ['wave_id' => $course_data->wave_id, 'user_id' => $follower->ID])['to_pay'];

					if ($old_payment) {
						$registration_data['previous_price'] = $old_payment;
					}
				}

				do_action('esr_after_student_registration', $registration_data['reg_id'], $course_data->id, $registration_data);

				$this->worker_payment->update_user_payment($follower->ID, $course_data->wave_id);

				if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
					$new_payment = apply_filters('esr_get_student_payment', ['wave_id' => $course_data->wave_id, 'user_id' => $follower->ID])['to_pay'];

					if ($new_payment) {
						$registration_data['actual_price'] = $new_payment;
					}
				}

				$this->update_summary($course_id, ESR_Dancing_As::FOLLOWER);
                if (!$disable_emails) {
                    ESR()->email->send_course_registration_email($registration_data);
                }
			}
		}
	}


	public function add_user($data, $key_email, $key_name, $key_surname, $key_phone) {
		if (isset($data[$key_email]) && !empty(trim($data[$key_email]))) {
			$user_email = sanitize_email(trim($data[$key_email]));
			$user       = get_user_by('email', $user_email);
			if (!$user) {
				$user = ESR()->user->create_new_user($user_email, sanitize_text_field($data[$key_name]), sanitize_text_field($data[$key_surname]));

				if (isset($data[$key_phone]) && !empty(trim($data[$key_phone]))) {
					update_user_meta($user->ID, 'esr-course-registration-phone', sanitize_text_field(trim($data[$key_phone])));
				}
			}

			return $user;
		}

		return null;
	}


	public function add_registrations($course_id, $user, $partner, $dancing_as, $free_registration) {
		return $this->worker_registration->save_registration($course_id, $user, $partner, $dancing_as, ESR_Registration_Status::CONFIRMED, $free_registration);
	}


	public function update_summary($course_id, $dancing_as) {
		$summary = ESR()->course_summary->get_course_summary($course_id);

		if (!ESR()->course->is_course_solo($course_id)) {
			if ($dancing_as === ESR_Dancing_As::LEADER) {
				ESR()->course_summary->update_course_summary($course_id, [
					'registered_leaders' => intval($summary->registered_leaders) + 1,
				]);
			} else if ($dancing_as === ESR_Dancing_As::FOLLOWER) {
				ESR()->course_summary->update_course_summary($course_id, [
					'registered_followers' => intval($summary->registered_followers) + 1,
				]);
			}
		} else {
			ESR()->course_summary->update_course_summary($course_id, [
				'registered_solo' => intval($summary->registered_solo) + 1,
			]);
		}
	}

}
