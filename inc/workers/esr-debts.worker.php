<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Debts_Worker {

	public static function esr_disable_student_registrations_callback($user_id) {
		return add_user_meta($user_id, 'esr_user_registration_disabled', current_time('Y-m-d H:i:s'));
	}

	public static function esr_enable_student_registrations_callback($user_id) {
		return delete_user_meta($user_id, 'esr_user_registration_disabled');
	}

}

add_filter('esr_disable_student_registrations', ['ESR_Debts_Worker', 'esr_disable_student_registrations_callback']);
add_filter('esr_enable_student_registrations', ['ESR_Debts_Worker', 'esr_enable_student_registrations_callback']);
