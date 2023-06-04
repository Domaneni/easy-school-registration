<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Registration_Email {

	private $worker_email;


	public function __construct() {
		$this->worker_email = new ESR_Email_Worker();
	}


	public function send_email($wave_ids, $user_id, $registration_data, $user_info) {
		if (intval(ESR()->settings->esr_get_option('registration_email_enabled', -1)) !== -1) {
			$student = get_user_by('ID', $user_id);

			$subject = apply_filters('esr_get_registration_email_title', stripcslashes(ESR()->settings->esr_get_option('registration_email_subject')), $wave_ids);
			$body = apply_filters('esr_get_registration_email_body', stripcslashes(ESR()->settings->esr_get_option('registration_email_body', null)), $wave_ids);

			if ($subject) {
				$tags = ESR()->tags->get_tags('registration_email_title');

				foreach ($tags as $key => $tag) {
					$parameter = null;
					if (isset($tag['parameter'])) {
						switch ($tag['parameter']) {
							case 'wave_ids':
								{
									$parameter = $wave_ids;
									break;
								}
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
				$tags = ESR()->tags->get_tags('email_registration');
				foreach ($tags as $key => $tag) {
					$parameter = null;
					if (isset($tag['parameter'])) {
						switch ($tag['parameter']) {
							case 'list_registered':
								{
									$parameter = isset($registration_data['registered']) ? $registration_data['registered'] : [];
									break;
								}
							case 'wave_ids':
								{
									$parameter = $wave_ids;
									break;
								}
							case 'user_registration_info':
								{
									$parameter = $user_info;
									break;
								}
							case 'checkbox_option':
								{
									$parameter = ((isset($user_info->{$tag['id']}) && (($user_info->{$tag['id']} === '') || !$user_info->{$tag['id']})) || !isset($user_info->{$tag['id']})) ? esc_html__('No', 'easy-school-registration') : esc_html__('Yes', 'easy-school-registration');
									break;
								}
						}

						$body = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $body, $parameter);
					} else if (!isset($tag['type']) || ($tag['type'] !== 'double')) {
						$body = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $body);
					}
				}

				return $this->worker_email->send_email($student->user_email, $subject, $body, '_registration');
			}
		}
		return false;
	}


	private function prepare_course_info($course_data) {
		$content = $course_data->title . '<br>';
		if ($course_data->sub_header) {
			$content .= $course_data->sub_header . '<br>';
		}

		$first_teacher  = $course_data->teacher_first;
		$second_teacher = $course_data->teacher_second;
		$content        .= ($first_teacher ? ESR()->teacher->get_teacher_name($first_teacher) : '') . ($second_teacher ? ($first_teacher ? ' & ' : '') . ESR()->teacher->get_teacher_name($second_teacher) : '') . '<br>';
		$content        .= ESR()->day->get_day_title($course_data->day) . ' ' . $course_data->time_from . ' - ' . $course_data->time_to . '<br>';
		$content        .= date('d.m.Y', strtotime($course_data->course_from)) . ' / ' . date('d.m.Y', strtotime($course_data->course_to)) . '<br>';

		return wp_kses_post($content);
	}

}
