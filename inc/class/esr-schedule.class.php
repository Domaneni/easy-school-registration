<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Schedule {

	public function get_wave_schedule_to_print($wave_id, $group_id = null) {
		if (intval(ESR()->settings->esr_get_option('multiple_dates', -1)) === -1) {
			return $this->prepare_data(ESR()->course->get_active_courses_data_by_wave($wave_id, $group_id));
		} else {
			return $this->prepare_data(ESR()->course->esr_get_courses_for_registration_by_wave_and_group($wave_id, $group_id));
		}
	}


	public function get_all_wave_schedule_to_print($wave_id, $group_id = null) {
		if ($group_id !== null) {
			if (intval(ESR()->settings->esr_get_option('multiple_dates', -1)) === -1) {
				return $this->prepare_data(ESR()->course->get_course_data_by_wave_and_group($wave_id, $group_id));
			} else {
				return $this->prepare_data(ESR()->course->esr_get_courses_for_registration_by_wave_and_group($wave_id, $group_id));
			}
		} else {
			if (intval(ESR()->settings->esr_get_option('multiple_dates', -1)) === -1) {
				return $this->prepare_data(ESR()->course->get_active_courses_data_by_wave($wave_id));
			} else {
				return $this->prepare_data(ESR()->course->esr_get_courses_for_schedule_by_wave($wave_id));
			}
		}
	}


	public function get_user_wave_schedule_to_print($wave_id, $user_id) {
		return $this->prepare_data(ESR()->course->get_courses_data_by_wave_and_user($wave_id, $user_id));
	}


	public function get_lowest_course_start_time($wave_id) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT MIN(times.time_from) AS time_from FROM (SELECT MIN(CASE WHEN cds.time_from = null THEN cd.time_from ELSE cds.time_from END) AS time_from FROM {$wpdb->prefix}esr_course_data AS cd LEFT JOIN {$wpdb->prefix}esr_course_dates AS cds ON cd.id = cds.course_id WHERE cd.wave_id = %d AND NOT cd.is_passed UNION SELECT MIN(time_from) FROM {$wpdb->prefix}esr_course_data AS cd WHERE cd.wave_id = %d AND NOT cd.is_passed) AS times", [intval($wave_id), intval($wave_id)]));
	}


	public function get_highest_course_end_time($wave_id) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT MAX(times.time_to) AS time_to FROM (SELECT MAX(CASE WHEN cds.time_to = null THEN cd.time_to ELSE cds.time_to END) AS time_to FROM {$wpdb->prefix}esr_course_data AS cd LEFT JOIN {$wpdb->prefix}esr_course_dates AS cds ON cd.id = cds.course_id WHERE cd.wave_id = %d AND NOT cd.is_passed UNION SELECT MAX(time_to) FROM {$wpdb->prefix}esr_course_data AS cd WHERE cd.wave_id = %d AND NOT cd.is_passed) AS times", [intval($wave_id), intval($wave_id)]));
	}


	private function prepare_data($courses) {
		$courses_by_day = [];

		if (!empty($courses)) {
			foreach ($courses as $key => $course) {
				$courses_by_day[$course->day][$course->hall_key][$course->time_from] = $course;
			}

			ksort($courses_by_day);
		}

		return $courses_by_day;
	}

}