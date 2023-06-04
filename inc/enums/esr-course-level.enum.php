<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Enum_Course_Level extends ESR_Enum_Db {

	const KEY = 'levels';

	public function __construct() {
		$this->setKey(self::KEY);
	}



	/**
	 * Load levels used in selected wave
	 *
	 * @param $wave_id
	 *
	 * @return array
	 */
	public function get_levels_by_wave($wave_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT DISTINCT level_id FROM {$wpdb->prefix}esr_course_data WHERE wave_id = %d ORDER BY level_id", [intval($wave_id)]), ARRAY_A);
	}

}