<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_User_Worker {

	public static function esr_save_user_fields_callback($user_id) {
		if (isset($_POST['esr_phone'])) {
			update_user_meta($user_id, 'esr-course-registration-phone', sanitize_text_field($_POST['esr_phone']));
		} else {
			delete_user_meta($user_id, 'esr-course-registration-phone');
		}

		if (isset($_POST['esr_newsletter'])) {
			update_user_meta($user_id, 'esr-course-registration-newsletter', sanitize_text_field($_POST['esr_newsletter']));
		} else {
			delete_user_meta($user_id, 'esr-course-registration-newsletter');
		}
	}

}

add_action('personal_options_update', ['ESR_User_Worker', 'esr_save_user_fields_callback']);
add_action('edit_user_profile_update', ['ESR_User_Worker', 'esr_save_user_fields_callback']);