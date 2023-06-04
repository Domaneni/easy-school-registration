<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Multiple_Dates {

	public function esr_get_multiple_dates($course_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_course_dates WHERE course_id = %d ORDER BY id", [intval($course_id)]));
	}

	public function esr_get_all_course_dates($course_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT day, time_from, time_to FROM {$wpdb->prefix}esr_course_dates WHERE course_id = %d UNION SELECT day, time_from, time_to FROM {$wpdb->prefix}esr_course_data WHERE id = %d ORDER BY day, time_from, time_to", [intval($course_id), intval($course_id)]));
	}
}
