<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Course_Summary {

	/**
	 * @param int $course_id
	 * @param array $data
	 */
	public function update_course_summary($course_id, $data) {
		global $wpdb;
		$wpdb->update($wpdb->prefix . 'esr_course_summary', $data, ['course_id' => $course_id]);
	}


	/**
	 * @param int $course_id
	 *
	 * @return object
	 */
	public function get_course_summary($course_id) {
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_course_summary WHERE course_id = %d", [intval($course_id)]));
	}


	/**
	 * @param int $course_id
	 *
	 * @return object
	 */
	public function get_course_summary_for_hover($course_id) {
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare("SELECT cd.max_leaders, cd.max_followers, cd.max_solo, cs.registered_leaders, cs.registered_followers, cs.registered_solo, cd.max_leaders - cs.registered_leaders AS ll, cd.max_followers - cs.registered_followers AS fl, cd.max_solo - cs.registered_solo AS sl FROM {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cd.id = cs.course_id WHERE cs.course_id = %d", [intval($course_id)]));
	}


	/**
	 * @param int $wave_id
	 *
	 * @return object
	 */
	public function get_course_summary_by_wave($wave_id) {
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT *, cd.id AS summary_course_id  FROM {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cd.id = cs.course_id WHERE cd.wave_id = %d ORDER BY cd.day, cd.course_from, cd.course_to, cd.id", [intval($wave_id)]));
	}


	/**
	 * @param int $wave_id
	 *
	 * @return object
	 */
	public function get_active_course_summary_by_wave($wave_id) {
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT *, cd.id AS summary_course_id  FROM {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cd.id = cs.course_id WHERE cd.wave_id = %d AND NOT cd.is_passed ORDER BY cd.day, cd.course_from, cd.course_to, cd.id", [intval($wave_id)]));
	}


	public function esr_is_course_full($course_id) {
		$course = ESR()->course->get_course_data($course_id);
		$course_summery = ESR()->course_summary->get_course_summary($course_id);
		if ($course->is_solo) {
			return !($course->max_solo > $course_summery->registered_solo);
		} else {
			return !(($course->max_leaders > $course_summery->registered_leaders) || ($course->max_followers > $course_summery->registered_followers));
		}
	}

	public static function esr_remove_course_summary_callback($course_id) {
		global $wpdb;
		$wpdb->delete($wpdb->prefix . 'esr_course_summary', ['course_id' => intval($course_id)]);
	}

}

add_action('esr_remove_course_summary', ['ESR_Course_Summary', 'esr_remove_course_summary_callback']);