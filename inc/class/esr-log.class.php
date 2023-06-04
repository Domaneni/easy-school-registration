<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Log {

	public function esr_get_all_logs() {
		global $wpdb;

		return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}esr_log");
	}

	public function esr_get_logs_by_subtype($subtype) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_log WHERE subtype = %s", $subtype));
	}

	public static function esr_log_message($main_plugin, $subtype, $status, $message) {
		global $wpdb;

		if (intval(ESR()->settings->esr_get_option('log_enabled', -1)) !== -1) {
			$wpdb->insert($wpdb->prefix . 'esr_log', [
				'main_plugin'  => $main_plugin,
				'subtype' => $subtype,
				'status'  => $status,
				'user_id' => get_current_user_id(),
				'message' => $message,
			]);
		}
	}


	public static function esr_log_esr_message($subtype, $status, $message) {
		do_action('esr_log_message', 'easy_school_registration', $subtype, $status, $message);
	}

}

add_action('esr_log_message', ['ESR_Log', 'esr_log_message'], 10, 4);
add_action('esr_log_esr_message', ['ESR_Log', 'esr_log_esr_message'], 10, 3);
