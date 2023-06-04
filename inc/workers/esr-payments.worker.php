<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Payments_Worker {

	public function update_user_payment($user_id, $wave_id) {
		do_action('esr_update_user_payment', $user_id, $wave_id, ESR()->payment->esr_get_price_sql($wave_id));
	}


	public static function update_user_payment_filter($user_id, $wave_id, $price_sql) {
		do_action('esr_update_payment', $user_id, $wave_id, $price_sql);
		do_action('esr_insert_payment', $user_id, $wave_id, $price_sql);
		do_action('esr_delete_payment', $user_id, $wave_id);

		return $user_id;
	}


	public function esr_course_price_update($course_id) {
		$registrations = ESR()->registration->get_confirmed_registrations_by_course($course_id);
		$course_data   = ESR()->course->get_course_data($course_id);
		$price_sql = ESR()->payment->esr_get_price_sql($course_data->wave_id);

		foreach ($registrations as $key => $registration) {
			do_action('esr_update_payment', $registration->user_id, $course_data->wave_id, $price_sql);
		}
	}


	public static function esr_update_payment($user_id, $wave_id, $price_sql) {
		global $wpdb;
		$round_number = 2;
		if (intval(ESR()->settings->esr_get_option('round_payments', -1)) !== -1) {
			$round_number = 0;
		}

		$price_string = 'ROUND(price.to_pay, ' . $round_number . ')';
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_user_payment AS up JOIN (SELECT cr.user_id, SUM(" . $price_sql . ") AS to_pay FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id  WHERE cr.status = %d AND cr.free_registration = 0 AND cd.price > 0 AND cd.wave_id = %d AND cr.user_id = %d GROUP BY cr.user_id, cd.wave_id) AS price ON up.user_id = price.user_id AND up.wave_id = %d SET discount_info = '', up.to_pay = {$price_string}", [
			ESR_Registration_Status::CONFIRMED,
			$wave_id,
			$user_id,
			$wave_id
		]));
	}


	public static function esr_insert_payment($user_id, $wave_id, $price_sql) {
		global $wpdb;
		$price_string = 'SUM(' . $price_sql . ')';
		if (intval(ESR()->settings->esr_get_option('round_payments', -1)) !== -1) {
			$price_string = 'ROUND(' . $price_string . ')';
		}
		$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}esr_user_payment(user_id, payment, wave_id, to_pay, discount_info) SELECT cr.user_id, NULL, %d, {$price_string} AS to_pay, '' FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cr.status = %d AND cr.free_registration = 0 AND cd.price > 0 AND cd.wave_id = %d AND cr.user_id = %d AND NOT EXISTS (SELECT 1 FROM {$wpdb->prefix}esr_user_payment WHERE wave_id = %d AND user_id = %d) GROUP BY cr.user_id, cd.wave_id", [
			$wave_id,
			ESR_Registration_Status::CONFIRMED,
			$wave_id,
			$user_id,
			$wave_id,
			$user_id
		]));
	}


	public static function esr_delete_payment($user_id, $wave_id) {
		global $wpdb;
		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}esr_user_payment WHERE user_id = %d AND wave_id = %d AND NOT EXISTS (SELECT 1 FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cr.user_id = %d AND cr.status = %d AND cr.free_registration = 0 AND cd.wave_id = %d)", [$user_id, $wave_id, $user_id, ESR_Registration_Status::CONFIRMED, $wave_id]));
	}


	public static function esr_save_user_payment_callback($data, $where, $old_payment_data) {
		global $wpdb;
		$wpdb->update($wpdb->prefix . 'esr_user_payment', $data, $where);

		do_action('esr_after_save_user_payment', $data, $where, $old_payment_data);
	}


	public static function esr_update_user_payment_by_registration_callback($registration_id) {
		$registration_id     = intval($registration_id);
		$actual_registration = ESR()->registration->get_registration($registration_id);
		$course_data         = ESR()->course->get_course_data($actual_registration->course_id);
		do_action('esr_update_user_payment', $actual_registration->user_id, $course_data->wave_id, ESR()->payment->esr_get_price_sql($course_data->wave_id));
	}


	public static function esr_forgive_payment_callback($payment_id) {
		global $wpdb;
		$status = $wpdb->update($wpdb->prefix . 'esr_user_payment', [
			'status' => ESR_Enum_Payment::FORGIVEN
		], [
			'id' => $payment_id
		]);

		if ($status === 1) {
			return [
				'status' => ESR_Enum_Payment::FORGIVEN,
				'status_title' => ESR()->payment_status->get_title(ESR_Enum_Payment::FORGIVEN)
			];
		} else {
			return -1;
		}
	}

}

add_action('esr_update_user_payment', ['ESR_Payments_Worker', 'update_user_payment_filter'], 10, 3);
add_action('esr_update_payment', ['ESR_Payments_Worker', 'esr_update_payment'], 10, 3);
add_action('esr_insert_payment', ['ESR_Payments_Worker', 'esr_insert_payment'], 10, 3);
add_action('esr_delete_payment', ['ESR_Payments_Worker', 'esr_delete_payment'], 10, 2);
add_action('esr_save_user_payment', ['ESR_Payments_Worker', 'esr_save_user_payment_callback'], 10, 3);
add_action('esr_update_user_payment_by_registration', ['ESR_Payments_Worker', 'esr_update_user_payment_by_registration_callback'], 10, 1);
add_filter('esr_forgive_payment', ['ESR_Payments_Worker', 'esr_forgive_payment_callback']);
