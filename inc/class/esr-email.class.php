<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Email {

	private $templater_course_confirmation_email;

	private $templater_payment_email;

	private $template_student_export_email;

	private $templater_user_registration_email;


	/**
	 * ESR_Email constructor.
	 *
	 */
	public function __construct() {
		$this->templater_course_confirmation_email = new ESR_Course_Confirmation_Email_Templater();
		$this->templater_payment_email             = new ESR_Payment_Email_Templater();
		$this->template_student_export_email       = new ESR_Template_GDPR_Email();
		$this->templater_user_registration_email   = new ESR_User_Registration_Email_Templater();
	}


	/**
	 * Send user email about his registration into system
	 *
	 * @param string $user_name
	 * @param string $email - User nick and where to send email
	 * @param string $password - User password
	 *
	 * @return boolean
	 */
	public function send_user_registration_email($user_name, $email, $password) {
		if (intval(ESR()->settings->esr_get_option('user_registration_email_enabled', 1)) !== -1) {
			return $this->templater_user_registration_email->send_email($user_name, $email, $password);
		}

		return false;
	}


	public function send_course_registration_email($registration_data) {
		if (intval(ESR()->settings->esr_get_option('confirmation_email_enabled', 1)) !== -1) {
			if ($registration_data && isset($registration_data['reg_id']) && ($registration_data['reg_id'] != null)) {
				return $this->templater_course_confirmation_email->send_email($registration_data);
			}
		}
	}


	/**
	 * @param $registration_list - List of courses ids
	 */
	public function send_course_confirmation_emails($registration_list) {
		if (intval(ESR()->settings->esr_get_option('confirmation_email_enabled', 1)) !== -1) {
			if ($registration_list) {
				foreach ($registration_list as $course_id => $course_registration) {
					if (isset($course_registration['student'])) {
						$this->templater_course_confirmation_email->send_email($course_registration['student']);
					}
					if (isset($course_registration['partner'])) {
						$this->templater_course_confirmation_email->send_email($course_registration['partner']);
					}
				}
			}
		}
	}


	public function send_payment_email($wave_id, $courses, $payment) {
		if (intval(ESR()->settings->esr_get_option('payment_email_enabled', 1)) !== -1) {
			return $this->templater_payment_email->send_payment_email($wave_id, $courses, $payment);
		}

		return false;
	}


	public static function send_payment_confirmation_email($wave_id, $email) {
		if (intval(ESR()->settings->esr_get_option('payment_confirmation_email_enabled', -1)) !== -1) {
			$template_payment_confirmation_email = new ESR_Template_Payment_Confirmation_Email();
			return $template_payment_confirmation_email->send_email($wave_id, $email);
		}

		return false;
	}


	public static function send_registration_email_callback($wave_ids, $user_id, $registration_data, $user_info) {
		if (intval(ESR()->settings->esr_get_option('registration_email_enabled', -1)) !== -1) {
			$template_registration_email = new ESR_Template_Registration_Email();
			$template_registration_email->send_email($wave_ids, $user_id, $registration_data, $user_info);
		}

		return false;
	}


	public function esr_send_student_export_email($user_id) {
		if ($user_id && (intval(ESR()->settings->esr_get_option('gdpr_email_enabled', -1)) !== -1)) {
			return $this->template_student_export_email->send_email($user_id);
		}

		return -1;
	}


	public static function send_payment_email_callback($wave_id, $courses, $payment) {
		$template_payment_email = new ESR_Payment_Email_Templater();
		if (intval(ESR()->settings->esr_get_option('payment_email_enabled', 1)) !== -1) {
			return $template_payment_email->send_payment_email($wave_id, $courses, $payment);
		}

		return false;
	}


	public static function send_payment_reminder_email_callback($wave_id, $courses, $payment) {
		$template_payment_email = new ESR_Payment_Email_Templater();
		if (intval(ESR()->settings->esr_get_option('payment_reminder_email_enabled', -1)) !== -1) {
			return $template_payment_email->send_payment_reminder_email($wave_id, $courses, $payment);
		}

		return false;
	}


	public static function esr_send_waiting_email_callback($wave_id, $user_id) {
		if (intval(ESR()->settings->esr_get_option('waiting_email_enabled', -1)) !== -1) {
			return apply_filters('esr_process_waiting_email', $wave_id, $user_id);
		}

		return false;
	}


	public static function esr_send_admin_note_email_callback($user_id, $wave_ids, $user_info, $note) {
		if (!empty($note) && (intval(ESR()->settings->esr_get_option('registration_note_email_enabled', -1)) !== -1)) {
			return apply_filters('esr_process_registration_note_email', $user_id, $wave_ids, $user_info, $note);
		}

		return false;
	}

}

add_action('esr_send_registration_email', ['ESR_Email', 'send_registration_email_callback'], 10, 4);
add_action('esr_send_payment_confirmation_email', ['ESR_Email', 'send_payment_confirmation_email'], 10, 2);
add_filter('esr_send_payment_email', ['ESR_Email', 'send_payment_email_callback'], 10, 3);
add_filter('esr_send_payment_reminder_email', ['ESR_Email', 'send_payment_reminder_email_callback'], 10, 3);
add_filter('esr_send_waiting_email', ['ESR_Email', 'esr_send_waiting_email_callback'], 10, 2);
add_action('esr_send_admin_note_email', ['ESR_Email', 'esr_send_admin_note_email_callback'], 10, 4);
