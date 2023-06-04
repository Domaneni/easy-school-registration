<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_User_Registration_Email_Templater {

	private $worker_email;


	public function __construct() {
		$this->worker_email = new ESR_Email_Worker();
	}


	public function send_email($user_name, $email, $password) {
		$student = get_user_by('email', trim($email));
		$subject = stripcslashes(ESR()->settings->esr_get_option('user_registration_email_subject'));
		$body    = stripcslashes(ESR()->settings->esr_get_option('user_registration_email_body', null));

        if ($subject) {
			$tags = ESR()->tags->get_tags('student_registration_email_title');

			foreach ($tags as $key => $tag) {
				$parameter = null;
				if (isset($tag['parameter'])) {
					switch ($tag['parameter']) {
						case 'user_registration_info':
							{
								$parameter = $student;
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
			$tags = ESR()->tags->get_tags('email_user_registration');

			foreach ($tags as $key => $tag) {
				$parameter = null;
				switch ($tag['parameter']) {
					case 'password':
						{
							$parameter = $password;
							break;
						}
					case 'user_name':
						{
							$parameter = $user_name;
							break;
						}
					case 'user_registration_info':
						{
							$parameter = $student;
							break;
						}
				}

				$body = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $body, $parameter);
			}

			return $this->worker_email->send_email($email, $subject, $body, '_user_registration');
		}

		return false;
	}

}