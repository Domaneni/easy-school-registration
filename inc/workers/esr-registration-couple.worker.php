<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Registration_Couple_Worker {

	private $worker_payment;


	public function __construct() {
		$this->worker_payment = new ESR_Payments_Worker();
	}


	public function process_registration($course_id, $user_id, $email, $course, $registration_data, $note = null) {
		global $wpdb;
		$return_courses = [];

		$dancing_as        = (isset($course->dancing_as) && ($course->dancing_as !== '')) ? intval($course->dancing_as) : null;
		$dancing_with      = (isset($course->dancing_with) && ($course->dancing_with !== '')) ? filter_var(strtolower(trim($course->dancing_with)), FILTER_SANITIZE_EMAIL) : null;
		$leaders_enabled   = ESR()->dance_as->is_leader_registration_enabled($course_id);
		$followers_enabled = ESR()->dance_as->is_followers_registration_enabled($course_id);

		if ((ESR()->dance_as->is_leader($dancing_as) && $leaders_enabled) || (ESR()->dance_as->is_follower($dancing_as) && $followers_enabled)) {
			$status = $wpdb->insert($wpdb->prefix . 'esr_course_registration', [
				'user_id'      => $user_id,
				'course_id'    => $course_id,
				'dancing_as'   => $dancing_as,
				'dancing_with' => $dancing_with,
				'status'       => ESR_Registration_Status::WAITING,
				'note'         => $note
			]);

			if ($status !== false) {

				$registration_id = $wpdb->insert_id;
				$partner_reg     = null;

				do_action('esr_after_student_registration', $registration_id, $course_id, $registration_data);
				$return_courses = apply_filters('esr_process_couple_pairing', [
					'course_id' => $course_id,
					'user_id' => $user_id,
					'student_email' => $email,
					'dancing_as' => $dancing_as,
					'dancing_with' => $dancing_with,
					'registration_id' => $registration_id,
					'return_courses' => []
				])['return_courses'];
			}

		}

		return $return_courses;
	}


}