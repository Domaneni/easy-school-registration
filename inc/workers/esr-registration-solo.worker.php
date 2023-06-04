<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Registration_Solo_Worker
{
	private $worker_payment;


	public function __construct()
	{
		$this->worker_payment = new ESR_Payments_Worker();
	}


	public function process_registration($course_id, $user_id, $course, $registration_data, $note = null)
	{
		global $wpdb;
		$return_courses = [];
		$solo_enabled = ESR()->dance_as->is_solo_registration_enabled($course_id);
		$course_data = ESR()->course->get_course_data($course_id);
		if ($solo_enabled) {
			$insert_data = [
				'user_id' => $user_id,
				'course_id' => $course_id,
				'dancing_as' => ESR_Dancing_As::SOLO,
				'dancing_with' => null,
				'status' => ESR_Registration_Status::WAITING,
				'note' => $note
			];

			$status = $wpdb->insert($wpdb->prefix . 'esr_course_registration', $insert_data);

			if ($status !== false) {
				$registration_id = $wpdb->insert_id;
				do_action('esr_after_student_registration', $registration_id, $course_id, $registration_data);

				$return_courses = apply_filters('esr_process_solo_pairing', [
					'course_id' => $course_id,
					'user_id' => $user_id,
					'course_data' => $course_data,
					'registration_id' => $registration_id,
					'return_courses' => []
				])['return_courses'];
			}
		}

		return $return_courses;
	}


}