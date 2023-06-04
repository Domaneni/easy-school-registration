<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Wave {

	/**
	 * @codeCoverageIgnore
	 */
	public function esr_get_wave_settings_preferences() {
		return [
			'courses_from' => [
				'type' => 'datetime'
			],
			'courses_to' => [
				'type' => 'datetime'
			]
		];
	}

	/**
	 * @return array
	 */
	public function get_waves_to_process_ids() {
		global $wpdb;
		$results = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}esr_wave_data AS wd WHERE NOT wd.is_passed");

		return $this->get_waves_ids_as_array($results);
	}


	/**
	 * @return array
	 */
	public function get_all_waves_ids() {
		return $this->get_waves_ids_as_array($this->get_waves_data());
	}


	/**
	 * @param int $wave_id
	 *
	 * @return object
	 */
	public function get_wave_data($wave_id) {
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_wave_data WHERE id = %s", [$wave_id]));
	}


	/**
	 * @return array
	 */
	public function get_waves_data($as_array = false) {
		global $wpdb;
		$data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}esr_wave_data ORDER BY id DESC");

		if (!$as_array) {
			return $data;
		}

		return $this->get_waves_as_array($data);
	}


	/**
	 *
	 * @return object
	 */
	public function esr_get_active_waves_data() {
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}esr_wave_data WHERE NOT is_passed");
	}


	/**
	 *
	 * @return object
	 */
	public function esr_get_waves_with_active_registration() {
		global $wpdb;
		$current_time = current_time('Y-m-d H:i:s');
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_wave_data WHERE registration_from <= %s AND registration_to >= %s AND NOT is_passed", [$current_time, $current_time]));
	}


	/**
	 * @param int $wave_id
	 *
	 * @return bool
	 */
	public function is_wave_registration_active($wave_id) {
		$data         = $this->get_wave_data($wave_id);
		$current_time = current_time('Y-m-d H:i:s');

		return $data && !$data->is_passed && ($data->registration_from <= $current_time) && ($data->registration_to >= $current_time);
	}



	/**
	 * @param int $wave_id
	 *
	 * @return bool
	 */
	public function is_wave_registration_closed($wave_id) {
		$data         = $this->get_wave_data($wave_id);
		$current_time = current_time('Y-m-d H:i:s');

		return $data && ($data->is_passed || ($data->registration_to < $current_time));
	}


	/**
	 * @param int $wave_id
	 *
	 * @return bool
	 */
	public function is_wave_closed($wave_id, $wave_data = null) {
		if (empty($wave_data)) {
			$data = $this->get_wave_data( $wave_id );
		} else {
			$data = $wave_data;
		}
		$current_time = current_time('Y-m-d H:i:s');
		$wave_settings = json_decode($data->wave_settings);

		return $data && ($data->is_passed || (isset($wave_settings->courses_to) && $wave_settings->courses_to < $current_time));
	}



	/**
	 * @param int $wave_id
	 *
	 * @return bool
	 */
	public function is_wave_registration_not_opened_yet($wave_id) {
		$data         = $this->get_wave_data($wave_id);
		$current_time = current_time('Y-m-d H:i:s');

		return $data && !$data->is_passed && ($data->registration_from > $current_time);
	}


	/**
	 * @param array $results
	 *
	 * @return array
	 */
	private function get_waves_ids_as_array($results) {
		$waves = [];
		foreach ($results as $result) {
			$waves[$result->id] = $result->id;
		}

		return $waves;
	}


	/**
	 * @param array $results
	 *
	 * @return array
	 */
	private function get_waves_as_array($results) {
		$waves = [];
		foreach ($results as $result) {
			$waves[$result->id] = $result;
		}

		return $waves;
	}


	/**
	 * @return array|null|object
	 */
	public function esr_load_tinymce_events() {
		global $wpdb;

		return $wpdb->get_results("SELECT title AS text, id AS value FROM {$wpdb->prefix}esr_wave_data ORDER BY id DESC");
	}

	public function esr_can_be_removed($wave_id) {
		global $wpdb;

		return intval($wpdb->get_var($wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}esr_wave_data AS w WHERE w.id = %d AND NOT EXISTS (SELECT * FROM {$wpdb->prefix}esr_course_data WHERE wave_id = w.id) AND NOT EXISTS (SELECT * FROM {$wpdb->prefix}esr_user_payment WHERE wave_id = w.id)", intval($wave_id)))) === 1;
	}

}
