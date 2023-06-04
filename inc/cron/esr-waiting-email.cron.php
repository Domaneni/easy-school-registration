<?php

class ESR_Cron_Waiting_Email {

	public function __construct() {
		if (intval(ESR()->settings->esr_get_option('waiting_email_automatic', 1)) != -1) {
			//add_action('esr_daily_scheduled_cron', ['ESR_Cron_Waiting_Email', 'esr_run_waiting_email_callback']);
		}
	}


	public static function esr_run_waiting_email_callback() {
		global $wpdb;
		$today           = current_time('Y-m-d H:i:s');
		$waves           = ESR()->wave->esr_get_waves_with_active_registration();

		foreach ($waves as $key => $wave) {
			$update_registrations = [];

			$when_send     = ESR()->settings->esr_get_option('waiting_email_days', 1);

			if (!apply_filters('esr_can_send_waiting_emails', $wave, $when_send, $today)) {
				continue;
			}

			foreach (self::esr_load_registrations($wave->id, $when_send) as $pkey => $registration) {
				$status = apply_filters('esr_send_waiting_email', $wave->id, $registration->user_id);

				if ($status) {
					$update_registrations[] = $registration->user_id;
				}
			}

			if (!empty($update_registrations)) {
				$update_registrations_string = implode(',', $update_registrations);

				$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id SET waiting_email_sent_timestamp = %s WHERE user_id IN ({$update_registrations_string}) AND cd.wave_id = %d AND cr.status = %d", [$today, intval($wave->id), ESR_Registration_Status::WAITING]));
			}
		}

	}


	private static function esr_date_difference($date_1, $date_2) {
		$datetime1 = date_create($date_1);
		$datetime2 = date_create($date_2);

		$interval = date_diff($datetime1, $datetime2);

		return $interval->format('%d');
	}


	public static function esr_can_send_waiting_emails_callback($wave_settings, $when_send, $today) {
		if (!isset($wave_settings->registration_from) || !isset($wave_settings->registration_to)) {
			return false;
		}

		if (empty($wave_settings->registration_from) || empty($wave_settings->registration_to)) {
			return false;
		}

		if ($wave_settings->registration_from > $today) {
			return false;
		}

		if ($wave_settings->registration_to < $today) {
			return false;
		}

		if (abs($when_send) > self::esr_date_difference($wave_settings->registration_from, $today)) {
			return false;
		}

		return true;
	}


	public static function esr_load_registrations($wave_id, $when_send) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT DISTINCT user_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cr.status = %d AND cd.wave_id = %d AND cr.waiting_email_sent_timestamp IS NULL AND DATEDIFF(%s, up.time) >= %d GROUP BY user_id", [ESR_Registration_Status::WAITING, intval($wave_id), current_time('Y-m-d'), $when_send]));
	}

}

add_filter('esr_can_send_waiting_emails', ['ESR_Cron_Waiting_Email', 'esr_can_send_waiting_emails_callback'], 10, 3);
