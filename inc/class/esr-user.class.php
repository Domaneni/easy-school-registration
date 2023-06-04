<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_User {

	public function create_new_user($email, $name, $surname) {
        $email = sanitize_email($email);
        $name = sanitize_text_field($name);
        $surname = sanitize_text_field($surname);

		$random_password = wp_generate_password();
		$user_id         = wp_create_user($email, $random_password, $email);
		wp_update_user([
			'ID'           => $user_id,
			'first_name'   => $name,
			'last_name'    => $surname,
			'display_name' => $name . ' ' . $surname,
		]);
		$new_user        = get_user_by('email', $email);

		$new_user->set_role('esr_student'); //set role to student

		ESR()->email->send_user_registration_email($new_user->user_login, $email, $random_password);

		return $new_user;
	}


	public function process_user_registration($data) {
		$email   = sanitize_email(htmlspecialchars(strtolower(trim($data->email))));
		$phone   = strtolower(isset($data->phone) ? sanitize_text_field(htmlspecialchars(trim($data->phone))) : NULL);
		$user_id = null;

		if (email_exists($email)) {
			$user = get_user_by('email', $email);
			$user_id = $user->ID;
		} else {
			$user_id = $this->create_new_user($email, sanitize_text_field(htmlspecialchars($data->name)), sanitize_text_field(htmlspecialchars($data->surname)))->ID;
		}

		$actual_phone = get_user_meta($user_id, 'esr-course-registration-phone');
		if (!$actual_phone || ($phone && ($actual_phone != $phone))) {
			update_user_meta($user_id, 'esr-course-registration-phone', $phone);
		}

		$newsletter = isset($data->newsletter) ? boolval($data->newsletter) : false;
		$actual_newsletter = get_user_meta($user_id, 'esr-course-registration-newsletter');
		if (!$actual_newsletter && $newsletter) {
			update_user_meta($user_id, 'esr-course-registration-newsletter', current_time('Y-m-d H:i:s'));
		}

		$terms_conditions = isset($data->terms_conditions) ? boolval($data->terms_conditions) : false;
		$actual_terms_conditions = get_user_meta($user_id, 'esr-course-registration-terms-conditions');
		if (!$actual_terms_conditions && $terms_conditions) {
			update_user_meta($user_id, 'esr-course-registration-terms-conditions', current_time('Y-m-d H:i:s'));
		}

		return $user_id;
	}

}