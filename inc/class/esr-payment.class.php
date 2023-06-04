<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Payment {

	/**
	 * @param int $wave_id
	 *
	 * @return array
	 */
	public function get_payments_by_wave($wave_id, $object = ARRAY_A) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_user_payment WHERE wave_id = %d ORDER BY id", [intval($wave_id)]), $object);
	}


	/**
	 * @param int $wave_id
	 *
	 * @return array
	 */
	public function load_payments_for_emails_by_wave($wave_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT c.user_id, c.courses, up.to_pay, up.payment FROM {$wpdb->prefix}esr_user_payment AS up JOIN (SELECT cr.user_id, GROUP_CONCAT(cd.id) AS courses FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id AND cd.wave_id = %d WHERE cr.status = %d GROUP BY cr.user_id) AS c ON c.user_id = up.user_id WHERE up.wave_id = %d", [intval($wave_id), ESR_Registration_Status::CONFIRMED, intval($wave_id)]));
	}


	/**
	 * @param int $user_id
	 *
	 * @return array
	 */
	public function get_payments_by_user($user_id, $object = OBJECT_K) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_user_payment WHERE user_id = %d ORDER BY id", [intval($user_id)]), $object);
	}


	/**
	 * @param int $user_id
	 *
	 * @return array
	 */
	public function get_payments_for_export($user_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT up.*, wd.title AS wave_name FROM {$wpdb->prefix}esr_user_payment AS up JOIN {$wpdb->prefix}esr_wave_data AS wd ON wd.id = up.wave_id WHERE up.user_id = %d ORDER BY id", [intval($user_id)]), OBJECT_K);
	}


	/**
	 * @param int $variable_symbol
	 *
	 * @return array
	 */
	public function get_payment_by_variable_symbol($variable_symbol) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_user_payment WHERE CONCAT(wave_id, CASE WHEN CHAR_LENGTH(user_id) = 1 THEN CONCAT('000', user_id) WHEN CHAR_LENGTH(user_id) = 2 THEN CONCAT('00', user_id) WHEN CHAR_LENGTH(user_id) = 3 THEN CONCAT('0', user_id) ELSE user_id END) = %s", [$variable_symbol]));
	}


	/**
	 * @param int $variable_symbol
	 *
	 * @return array
	 */
	public function get_payment_by_registration_variable_symbol($variable_symbol) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT up.* FROM {$wpdb->prefix}esr_user_payment AS up JOIN (SELECT c.*, @row_number:=CASE WHEN @wave_no = c.wave_id THEN @row_number + 1 ELSE 1 END AS reg_position, @wave_no:=c.wave_id as WaveId FROM (SELECT cr.user_id, cr.course_id, cd.wave_id AS wave_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id ORDER BY cd.wave_id, cr.id) AS c, (SELECT @wave_no:=0,@row_number:=0) as n) AS r ON up.wave_id = r.wave_id AND up.user_id = r.user_id WHERE CONCAT(CONCAT(up.wave_id, CASE WHEN CHAR_LENGTH(up.user_id) = 1 THEN CONCAT('000', up.user_id) WHEN CHAR_LENGTH(up.user_id) = 2 THEN CONCAT('00', up.user_id) WHEN CHAR_LENGTH(up.user_id) = 3 THEN CONCAT('0', up.user_id) ELSE up.user_id END), r.reg_position) = %s", [$variable_symbol]));
	}


	/**
	 * @param int $wave_id
	 * @param int $user_id
	 *
	 * @return null|object
	 */
	public function get_payment_by_wave_and_user($wave_id, $user_id) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_user_payment WHERE user_id = %d AND wave_id = %d ORDER BY id", [intval($user_id), intval($wave_id)]));
	}


	public function get_not_paid_users_for_payment_emails($wave_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT up.user_id, u.display_name, u.user_email, up.confirmation_email_sent_timestamp, lt.last_time FROM {$wpdb->prefix}esr_user_payment AS up LEFT JOIN (SELECT cr.user_id as user_id, MAX(cr.confirmation_time) AS last_time FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d GROUP BY cr.user_id) AS lt ON up.user_id = lt.user_id JOIN {$wpdb->prefix}users AS u ON up.user_id = u.id WHERE up.status = %d AND up.wave_id = %d GROUP BY up.user_id", [
            intval($wave_id),
			ESR_Enum_Payment::NOT_PAID,
            intval($wave_id)
		]));
	}


	public function get_partially_paid_users_for_payment_emails($wave_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT user_id,  u.display_name, u.user_email, up.note, up.payment, up.to_pay FROM {$wpdb->prefix}esr_user_payment AS up JOIN {$wpdb->users} AS u ON up.user_id = u.id WHERE up.status = %d AND up.payment < up.to_pay AND up.wave_id = %d GROUP BY up.user_id", [ESR_Enum_Payment::PAID, intval($wave_id)]));
	}


	public function esr_get_price_sql($wave_id) {
		return apply_filters('esr_sql_price_string', ['wave_id' => $wave_id, 'sql' => 'cd.price'])['sql'];
	}


	public static function esr_get_student_payment($settings) {
		$payment = ESR()->payment->get_payment_by_wave_and_user($settings['wave_id'], $settings['user_id']);
		$settings['to_pay'] = 0;

		if ($payment) {
			$settings['to_pay'] = $payment->to_pay;
		}

		return $settings;
	}


	public function esr_get_payment_by_md5($md5) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_user_payment WHERE %s LIKE MD5(CONCAT(wave_id, '-', user_id))", [$md5]));
	}


	public function esr_get_payment_debts() {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT up.*, w.title FROM {$wpdb->prefix}esr_user_payment AS up JOIN {$wpdb->prefix}esr_wave_data AS w ON up.wave_id = w.id WHERE to_pay > COALESCE(payment, 0) AND is_paying AND status != %d ORDER BY up.user_id, up.wave_id", [ESR_Enum_Payment::FORGIVEN]));
	}
}

add_filter('esr_get_student_payment', ['ESR_Payment', 'esr_get_student_payment']);
