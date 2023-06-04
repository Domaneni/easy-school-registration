<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Registration {

	public function get_confirmed_registrations_by_course($course_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT cr.* FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d", [intval($course_id), ESR_Registration_Status::CONFIRMED]));
	}


	public function get_confirmed_registration_user_ids_by_wave($wave_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT DISTINCT cr.user_id AS user_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d", [intval($wave_id), ESR_Registration_Status::CONFIRMED]));
	}


	/**
	 * @param int $wave_id
	 *
	 * @return array
	 */
	public function get_registrations_by_wave($wave_id) {
		global $wpdb;

		if (intval(ESR()->settings->esr_get_option('show_payment_enabled', -1)) === 1) {
			return apply_filters('esr_get_registrations_with_payment_status_by_wave', $wpdb->get_results($wpdb->prepare("SELECT cr.*, CASE WHEN up.id IS NULL THEN %d WHEN NOT up.is_paying THEN %d WHEN up.is_voucher THEN %d WHEN up.to_pay = up.payment THEN %d WHEN up.to_pay > up.payment THEN %d WHEN up.to_pay < up.payment THEN %d ELSE %d END AS payment_status FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id LEFT JOIN {$wpdb->prefix}esr_user_payment AS up ON cr.user_id = up.user_id AND cd.wave_id = up.wave_id WHERE cd.wave_id = %d", [ESR_Enum_Payment::NOT_PAID, ESR_Enum_Payment::NOT_PAYING, ESR_Enum_Payment::VOUCHER, ESR_Enum_Payment::PAID, ESR_Enum_Payment::NOT_PAID_ALL, ESR_Enum_Payment::OVER_PAID, ESR_Enum_Payment::NOT_PAID, intval($wave_id)]), OBJECT_K), intval($wave_id));
		} else {
			return apply_filters('esr_get_registrations_by_wave', $wpdb->get_results($wpdb->prepare("SELECT * FROM (SELECT c.*, @row_number:=CASE WHEN @wave_no = c.wave_id THEN @row_number + 1 ELSE 1 END AS reg_position, @wave_no:=c.wave_id as WaveId FROM (SELECT cr.*, cd.wave_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d) AS c, (SELECT @wave_no:=0,@row_number:=0) as n) AS r", [intval($wave_id)]), OBJECT_K), intval($wave_id));
		}
	}


	/**
	 * @param int $wave_id
	 * @param int $teacher_id
	 *
	 * @return array
	 */
	public function get_registrations_by_wave_and_teacher($wave_id, $teacher_id) {
		global $wpdb;

		if (intval(ESR()->settings->esr_get_option('show_payment_enabled', -1)) === 1) {
			return apply_filters('esr_get_registrations_with_payment_status_by_wave_and_teacher', $wpdb->get_results($wpdb->prepare("SELECT cr.*, CASE WHEN up.id IS NULL THEN %d WHEN NOT up.is_paying THEN %d WHEN up.is_voucher THEN %d WHEN up.to_pay = up.payment THEN %d WHEN up.to_pay > up.payment THEN %d WHEN up.to_pay < up.payment THEN %d ELSE %d END AS payment_status FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id JOIN {$wpdb->prefix}esr_teacher_data AS td ON cd.teacher_first = td.id OR cd.teacher_second = td.id LEFT JOIN {$wpdb->prefix}esr_user_payment AS up ON cr.user_id = up.user_id AND cd.wave_id = up.wave_id WHERE cd.wave_id = %d AND td.user_id = %d", [ESR_Enum_Payment::NOT_PAID, ESR_Enum_Payment::NOT_PAYING, ESR_Enum_Payment::VOUCHER, ESR_Enum_Payment::PAID, ESR_Enum_Payment::NOT_PAID_ALL, ESR_Enum_Payment::OVER_PAID, ESR_Enum_Payment::NOT_PAID, intval($wave_id), intval($teacher_id)]), OBJECT_K), intval($wave_id), intval($teacher_id));
		} else {
			return apply_filters('esr_get_registrations_by_wave_and_teacher', $wpdb->get_results($wpdb->prepare("SELECT cr.* FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id JOIN {$wpdb->prefix}esr_teacher_data AS td ON cd.teacher_first = td.id OR cd.teacher_second = td.id WHERE cd.wave_id = %d AND td.user_id = %d", [intval($wave_id), intval($teacher_id)]), OBJECT_K), intval($wave_id), intval($teacher_id));
		}
	}


	/**
	 * @param int $user_id
	 *
	 * @return array
	 */
	public function get_registrations_by_user($user_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT cr.*, cd.title AS course_name, wd.title AS wave_name FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id  JOIN {$wpdb->prefix}esr_wave_data AS wd ON cd.wave_id = wd.id  WHERE cr.user_id = %d", [intval($user_id)]), OBJECT_K);
	}


	/**
	 * @param int $wave_id
	 * @param int $user_id
	 *
	 * @return array
	 */
	public function get_registrations_by_wave_and_user($wave_id, $user_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT *, WaveId as wave_id FROM (SELECT c.*, @row_number:=CASE WHEN @wave_no = c.wave_id THEN @row_number + 1 ELSE 1 END AS reg_position, @wave_no:=c.wave_id as WaveId FROM (SELECT cr.*, cd.title, cd.day, cd.time_from, cd.time_to, cd.wave_id, cr.id AS registration_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cd.id = cr.course_id WHERE wave_id = %d ORDER BY cr.id) AS c, (SELECT @wave_no:=0,@row_number:=0) as n) AS r WHERE r.user_id = %d", [intval($wave_id), intval($user_id)]));
	}


	/**
	 * @param int $wave_id
	 * @param int $user_id
	 *
	 * @return array
	 */
	public function get_confirmed_registrations_by_wave_and_user($wave_id, $user_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT *, cr.id AS registration_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.user_id = %d AND cr.status = %d", [intval($wave_id), intval($user_id), ESR_Registration_Status::CONFIRMED]));
	}


	public function get_registration($id) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_course_registration WHERE id = %d", [intval($id)]));
	}


	public function get_registration_with_reg_position($id) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT c.*, @row_number:=CASE WHEN @wave_no = c.wave_id THEN @row_number + 1 ELSE 1 END AS reg_position, @wave_no:=c.wave_id as WaveId FROM (SELECT cr.*, cd.wave_id AS wave_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id ORDER BY cd.wave_id, cr.id) AS c, (SELECT @wave_no:=0,@row_number:=0) as n WHERE c.id = %d", [intval($id)]));
	}


	public static function esr_remove_course_registrations_callback($course_id) {
		global $wpdb;
		$wpdb->delete($wpdb->prefix . 'esr_course_registration', ['course_id' => intval($course_id)]);
	}


	public static function esr_get_user_course_callback($user_id, $course_id) {
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_course_registration WHERE user_id = %d AND course_id = %d", [intval($user_id), intval($course_id)]));
	}

}

add_action('esr_remove_course_registrations', ['ESR_Registration', 'esr_remove_course_registrations_callback']);