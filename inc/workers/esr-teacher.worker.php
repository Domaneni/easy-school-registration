<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Teacher_Worker {

	/**
	 * ESR_Teacher_Worker constructor.
	 */
	public function __construct() {
		add_action('esr_teacher_add', [get_called_class(), 'add_teacher_action']);
		add_action('esr_teacher_update', [get_called_class(), 'update_teacher_action'], 10, 2);
	}


	public function process_teacher($data) {
		if (current_user_can('esr_teacher_edit')) {
			if (isset($data['teacher_id']) && ($data['teacher_id'] !== '')) {
				$prepared_data = $this->prepare_data($data);
				do_action('esr_teacher_update', intval($data['teacher_id']), $prepared_data);
			} else {
				do_action('esr_teacher_add', $this->prepare_data($data));
			}
		}
	}


	public static function add_teacher_action($data) {
		if (current_user_can('esr_teacher_edit')) {

			global $wpdb;
			$result = $wpdb->insert($wpdb->prefix . 'esr_teacher_data', $data);

			if ($result !== false) {
				do_action('esr_module_teacher_add', $wpdb->insert_id, $data);
			}
		}
	}


	public static function update_teacher_action($teacher_id, $data) {
		if (current_user_can('esr_teacher_edit')) {

			global $wpdb;
			$wpdb->update($wpdb->prefix . 'esr_teacher_data', $data, [
				'id' => $teacher_id
			]);

			do_action('esr_module_teacher_update', $teacher_id, $data);
		}
	}


	private function prepare_data($data) {
		$return_data = [];

		$return_data['name']     = sanitize_text_field($data['name']);
		$return_data['nickname'] = isset($data['nickname']) ? sanitize_text_field($data['nickname']) : null;
		$return_data['user_id']  = isset($data['user_id']) && ($data['user_id'] !== '') ? intval($data['user_id']) : null;

		$teacher_settings = [];
		if (isset($data['teacher_settings'])) {
			foreach (ESR()->teacher->esr_get_teacher_settings_preferences() as $key => $setting) {
				if ($setting['type'] === 'checkbox') {
					$teacher_settings[$key] = isset($data['teacher_settings'][$key]);
				} else if (isset($teacher_settings[$key])) {
					$teacher_settings[$key] = sanitize_text_field($data['teacher_settings'][$key]);
				} else {
					$teacher_settings[$key] = '';
				}
			}
		}

		$return_data['teacher_settings'] = json_encode($teacher_settings);


		return $return_data;
	}

}
