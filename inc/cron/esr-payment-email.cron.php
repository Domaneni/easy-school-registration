<?php

class ESR_Cron_Payment_Email {

	public function __construct() {
		if (intval(ESR()->settings->esr_get_option('payment_email_automatic', -1)) != -1) {
			add_action('esr_daily_scheduled_cron', ['ESR_Cron_Payment_Email', 'esr_run_payment_email_callback']);
		}
	}


	public static function esr_run_payment_email_callback() {
		global $wpdb;
		$today           = current_time('Y-m-d H:i:s');
		$waves           = ESR()->wave->esr_get_active_waves_data();
		$update_payments = [];

		foreach ($waves as $key => $wave) {
			$wave_settings = json_decode($wave->wave_settings);
			$when_send     = ESR()->settings->esr_get_option('payment_email_days', -1);

			if (!apply_filters('esr_can_send_payment_emails', $wave_settings, $when_send, $today)) {
				continue;
			}

			foreach (self::esr_load_payments($wave->id) as $pkey => $payment) {
				$status = apply_filters('esr_send_payment_email', $wave->id, explode(',', $payment->courses), $payment);

				if ($status) {
					$update_payments[] = $payment->id;
				}
			}
		}

		if (!empty($update_payments)) {
			$update_payments = implode(',', $update_payments);

			$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_user_payment SET confirmation_email_sent_timestamp = %s WHERE id IN ({$update_payments})", $today));
		}
	}


	private static function esr_date_difference($date_1, $date_2) {
		$datetime1 = date_create($date_1);
		$datetime2 = date_create($date_2);

		$interval = date_diff($datetime1, $datetime2);

		return $interval->format('%d');
	}


	public static function esr_can_send_payment_emails_callback($wave_settings, $when_send, $today) {

		if (!isset($wave_settings->courses_from) || !isset($wave_settings->courses_to)) {
			return false;
		}

		if (empty($wave_settings->courses_from) || empty($wave_settings->courses_to)) {
			return false;
		}

		if ($wave_settings->courses_to <= $today) {
			return false;
		}

		if ($when_send == 0) { // Send in day of wave start
			if ($wave_settings->courses_from > $today) {
				return false;
			}
		} else if ($when_send < 0) { // Send before wave starts
			if (($wave_settings->courses_from > $today) && (abs($when_send) < self::esr_date_difference($wave_settings->courses_from, $today))) {
				return false;
			}
		} else if ($when_send > 0) { // Send after wave starts
			if (($wave_settings->courses_from >= $today) || (abs($when_send) > self::esr_date_difference($wave_settings->courses_from, $today))) {
				return false;
			}
		}

		return true;
	}


	/**
	 * @param int $wave_id
	 *
	 * @return array
	 */
	public static function esr_load_payments($wave_id) {
		global $wpdb;

		$sql = "SELECT c.user_id, c.courses, up.to_pay, up.payment, up.id 
FROM {$wpdb->prefix}esr_user_payment AS up 
JOIN (SELECT cr.user_id, GROUP_CONCAT(cd.id) AS courses 
	FROM {$wpdb->prefix}esr_course_registration AS cr 
	JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id AND cd.wave_id = %d 
	WHERE cr.status = %d GROUP BY cr.user_id) AS c ON c.user_id = up.user_id 
WHERE up.wave_id = %d AND up.confirmation_email_sent_timestamp IS NULL AND up.status = %d";

		$params = [intval($wave_id), ESR_Registration_Status::CONFIRMED, intval($wave_id), ESR_Enum_Payment::NOT_PAID];

		return $wpdb->get_results($wpdb->prepare($sql, $params));
	}

}

add_filter('esr_can_send_payment_emails', ['ESR_Cron_Payment_Email', 'esr_can_send_payment_emails_callback'], 10, 3);
