<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Course_Worker {

	public function __construct() {
		add_action('esr_course_add', [get_called_class(), 'add_course_action'], 10, 2);
		add_action('esr_course_update', [get_called_class(), 'update_course_action'], 10, 3);
	}


	public function process_course($data) {
		if (current_user_can('esr_course_edit')) {
			if (isset($data['course_id']) && ($data['course_id'] !== '')) {
				$prepared_data = $this->prepare_data($data, isset($data['course_id']));
				do_action('esr_course_update', intval($data['course_id']), $prepared_data, $data);
			} else {
				do_action('esr_course_add', $this->prepare_data($data), $data);
			}
		}
	}


	public static function add_course_action($data, $form_data) {
		if (current_user_can('esr_course_edit')) {
			global $wpdb;

			$result = $wpdb->insert($wpdb->prefix . 'esr_course_data', $data);

			if ($result !== false) {
				$course_id = $wpdb->insert_id;
				$wpdb->insert($wpdb->prefix . 'esr_course_summary', ['course_id' => $course_id]);
				do_action('esr_module_course_add', $course_id, $data);
				do_action('esr_process_multiple_dates', $course_id, $form_data);
			}
		}
	}


	public static function update_course_action($course_id, $data, $form_data) {
		if (current_user_can('esr_course_edit')) {
			global $wpdb;

			$old_course = ESR()->course->get_course_data($course_id);

			$wpdb->update($wpdb->prefix . 'esr_course_data', $data, [
				'id' => $course_id
			]);

			if (isset($data['price']) && ($old_course->price != $data['price'])) {
				$worker_payment = new ESR_Payments_Worker();
				$worker_payment->esr_course_price_update($course_id);
			}

			do_action('esr_module_course_update', $course_id, $data);
			do_action('esr_process_multiple_dates', $course_id, $form_data);
		}
	}


	private function prepare_data($data, $is_update = false) {
		$fields      = ESR()->course->get_fields();
		$return_data = [];

		foreach ($fields as $key => $field) {
			if ($field['required'] && !$is_update) {
				$return_data[$key] = $this->sanitize($field['type'], $data[$key]);
			} else if (($field['type'] === 'boolean') && (!isset($data[$key]) || (isset($data[$key]) && !is_bool($data[$key])))) {
				if ($is_update && ($key === 'is_passed')) {
					continue;
				} else {
					$return_data[$key] = isset($data[$key]);
				}
			} else if (isset($data[$key])) {
				$return_data[$key] = $this->sanitize($field['type'], $data[$key]);
			}
		}

        if (intval(ESR()->settings->esr_get_option('disable_couples', -1)) === 1) {
            $return_data['is_solo'] = true;
        }

		if ((intval(ESR()->settings->esr_get_option('multiple_dates', -1)) === 1) && isset($data['course-days'])) {
			$date                     = reset($data['course-days']);
			$return_data['day']       = $this->sanitize('int', $date['day']);
			$return_data['time_from'] = $this->sanitize('string', $date['time_from']);
			$return_data['time_to']   = $this->sanitize('string', $date['time_to']);
		}

		$course_settings = [];
		if (isset($data['course_settings'])) {
			foreach (apply_filters('esr_get_course_settings_preferences', []) as $key => $setting) {
				if ($setting['type'] === 'checkbox') {
					$course_settings[$key] = isset($data['course_settings'][$key]) && ($data['course_settings'][$key] === 'on');
				} else if (isset($data['course_settings'][$key])) {
					$course_settings[$key] = sanitize_text_field($data['course_settings'][$key]);
				} else {
					$course_settings[$key] = '';
				}
			}
		}

		$return_data['course_settings'] = json_encode($course_settings);

		return $return_data;
	}


	private function sanitize($type, $value) {
		switch ($type) {
			case 'int':
				return $value === '' ? null : (int) $value;
			case 'boolean':
				return filter_var($value, FILTER_VALIDATE_BOOLEAN);
			case 'datetime':
				return $value !== '' ? strftime('%Y-%m-%d %H:%M:%S', strtotime($value)) : null;
			default:
				return sanitize_text_field($value);
		}
	}


	public static function esr_remove_course_forever_callback($course_id) {
		$course_data = ESR()->course->get_course_data($course_id);

		if ($course_data && $course_data->is_passed) {
			$registrations = ESR()->registration->get_confirmed_registrations_by_course($course_data->id);

			do_action('esr_remove_course_summary', $course_data->id);
			do_action('esr_remove_course_registrations', $course_data->id);
			do_action('esr_remove_course_data', $course_data->id);

			if ($registrations !== null) {
				$worker_payment = new ESR_Payments_Worker();
				foreach ($registrations as $r_key => $registration) {
					$worker_payment->update_user_payment($registration->user_id, $course_data->wave_id);
				}
			}

			return 1;
		}

		return -1;
	}


	public static function esr_process_multiple_dates_callback($course_id, $data) {
		if ((intval(ESR()->settings->esr_get_option('multiple_dates', -1)) === 1) && isset($data['course-days'])) {
			global $wpdb;

			$wpdb->delete("{$wpdb->prefix}esr_course_dates", ['course_id' => $course_id]);

			foreach ($data['course-days'] as $key => $date) {
				$insert_data              = [];
				$insert_data['course_id'] = (int) $course_id;
				$insert_data['day']       = (int) $date['day'];
				$insert_data['time_from'] = sanitize_text_field($date['time_from']);
				$insert_data['time_to']   = sanitize_text_field($date['time_to']);

				$wpdb->insert("{$wpdb->prefix}esr_course_dates", $insert_data);
			}

		}
	}

}

add_filter('esr_remove_course_forever', ['ESR_Course_Worker', 'esr_remove_course_forever_callback']);
add_action('esr_process_multiple_dates', ['ESR_Course_Worker', 'esr_process_multiple_dates_callback'], 10, 2);
