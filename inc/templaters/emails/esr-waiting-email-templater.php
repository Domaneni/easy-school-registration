<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Waiting_Email_Templater {

	public static function esr_process_waiting_email_callback($wave_id, $user_id) {
		$subject = stripcslashes(ESR()->settings->esr_get_option('waiting_email_subject'));
		$body    = stripcslashes(ESR()->settings->esr_get_option('waiting_email_body', null));

		return self::esr_send_email($wave_id, $user_id, $body, $subject);
	}


	public static function esr_send_email($wave_id, $user_id, $body, $subject) {
		$student = get_user_by('id', $user_id);
		$user_info = get_userdata(intval($user_id));

		if ($subject) {
			$tags = ESR()->tags->get_tags('email_title');

			foreach ($tags as $key => $tag) {
				$parameter = null;
				if (isset($tag['parameter'])) {
					switch ($tag['parameter']) {
						case 'wave_id':
						{
							$parameter = $wave_id;
							break;
						}
						case 'user_registration_info':
						{
							$parameter = $user_info;
							break;
						}
					}

					$subject = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $subject, $parameter);
				} else {
					$subject = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $subject);
				}
			}
		}

		if (!empty($body)) {
			$tags = ESR()->tags->get_tags('email_waiting');

			foreach ($tags as $key => $tag) {
				$parameter = null;
				if (isset($tag['parameter'])) {
					switch ($tag['parameter']) {
						case 'list_waiting':
						{
							$parameter = self::esr_get_waiting_registrations($wave_id, $user_id);
							break;
						}
						case 'wave_id':
						{
							$parameter = $wave_id;
							break;
						}
					}

					$body = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $body, $parameter);
				} else {
					$body = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $body);
				}
			}

			$worker_email = new ESR_Email_Worker();
			return $worker_email->send_email($student->user_email, $subject, $body, '_waiting');
		}

		return false;
	}


	public static function esr_get_waiting_registrations($wave_id, $user_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT *, cr.id AS registration_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.user_id = %d AND cr.status = %d AND cr.waiting_email_sent_timestamp IS NULL", [$wave_id, $user_id, ESR_Registration_Status::WAITING]));
	}

}

add_filter('esr_process_waiting_email', ['ESR_Waiting_Email_Templater', 'esr_process_waiting_email_callback'], 10, 2);