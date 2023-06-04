<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_GDPR_Email {

	private $template_student_export;

	private $worker_email;


	public function __construct() {
		$this->template_student_export = new ESR_Template_Student_Export();
		$this->worker_email = new ESR_Email_Worker();
	}


	public function send_email($user_id) {
		if (intval(ESR()->settings->esr_get_option('gdpr_email_enabled', -1)) !== -1) {

			$subject = stripcslashes(ESR()->settings->esr_get_option('gdpr_email_subject'));
			$body = stripcslashes(ESR()->settings->esr_get_option('gdpr_email_body', null));

			$user_info = get_userdata(intval($user_id));
			$student_email = $user_info->user_email;

			if ($subject) {
				$tags = ESR()->tags->get_tags('email_title');

				foreach ($tags as $key => $tag) {
					$parameter = null;
					if (isset($tag['parameter'])) {
						switch ($tag['parameter']) {
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
				$tags = ESR()->tags->get_tags('email_gdpr');

				foreach ($tags as $key => $tag) {
					$parameter = null;
					if (isset($tag['parameter'])) {
						switch ($tag['parameter']) {
							case 'user_info':
								{
									$parameter = $this->template_student_export->export_student_data($user_id);
									break;
								}
							case 'user_registration_info':
								{
									$parameter = $user_info;
									break;
								}
						}
						$body = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $body, $parameter);
					} else if (!isset($tag['type']) || ($tag['type'] !== 'double')) {
						$body = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $body);
					}
				}

				return $this->worker_email->send_email($student_email, $subject, $body, '_gdpr');
			}
		}
		return false;
	}

}
