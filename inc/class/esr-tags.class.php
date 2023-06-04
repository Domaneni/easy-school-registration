<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Tags {

	public function get_tags($category = null) {
		$tags = $this->set_tags();
		if (($category != null) && (isset($tags[$category]))) {
			return $tags[$category];
		} else {
			return $category;
		}
	}


	private function set_tags() {
		$preset_tags = [
			'email_confirmation'               => apply_filters('esr_tags_email_confirmation', [
				'first_name'             => [
					'id'          => 'first_name',
					'tag'         => 'student_name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'last_name'              => [
					'id'          => 'last_name',
					'tag'         => 'student_surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'course_title'           => [
					'id'          => 'title',
					'tag'         => 'course_title',
					'description' => esc_html__('Course title.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_course_data',
					'parameter'   => 'course'
				],
				'course_subtitle'        => [
					'id'          => 'sub_header',
					'tag'         => 'course_subtitle',
					'description' => esc_html__('Course subtitle.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_course_data',
					'parameter'   => 'course'
				],
				'course_teachers'        => [
					'id'          => 'teachers',
					'tag'         => 'course_teachers',
					'description' => esc_html__('Teacher names for this course.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_course_teachers',
					'parameter'   => 'course'
				],
				'course_day'             => [
					'id'          => 'day',
					'tag'         => 'course_day',
					'description' => esc_html__('Day when the course is happening.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_course_day',
					'parameter'   => 'course'
				],
				'course_from'            => [
					'id'          => 'course_from',
					'tag'         => 'course_from',
					'description' => esc_html__('Date when the course is starting.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_course_date',
					'parameter'   => 'course'
				],
				'course_to'              => [
					'id'          => 'course_to',
					'tag'         => 'course_to',
					'description' => esc_html__('Date when the course is ending.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_course_date',
					'parameter'   => 'course'
				],
				'course_time_from'       => [
					'id'          => 'time_from',
					'tag'         => 'course_time_from',
					'description' => esc_html__('Start time of the course.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_course_time',
					'parameter'   => 'course'
				],
				'course_time_to'         => [
					'id'          => 'time_to',
					'tag'         => 'course_time_to',
					'description' => esc_html__('End time of the course.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_course_time',
					'parameter'   => 'course'
				],
				'course_days_times'      => [
					'id'          => 'days_times',
					'tag'         => 'course_days_times',
					'description' => esc_html__('List of days and times when the course is happening.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_course_days_times',
					'parameter'   => 'course_days_times'
				],
				'course_price'           => [
					'id'          => 'price',
					'tag'         => 'course_price',
					'description' => esc_html__('Course price with currency. (example: 10 $)', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_price',
					'parameter'   => 'price'
				],
				'dancing_as'             => [
					'id'          => 'dancing_as',
					'tag'         => 'dancing_as',
					'description' => esc_html__('Dancing Role (leader/follower).', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_dancing_as',
					'parameter'   => 'dancing_as'
				],
				'hall_name'              => [
					'id'          => 'name',
					'tag'         => 'hall_name',
					'description' => esc_html__('Name of the hall where the course is taking place.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_hall_data',
					'parameter'   => 'hall'
				],
				'hall_address'           => [
					'id'          => 'address',
					'tag'         => 'hall_address',
					'description' => esc_html__('Hall address.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_hall_data',
					'parameter'   => 'hall'
				],
				'wave_title'             => [
					'id'          => 'wave_title',
					'tag'         => 'wave_title',
					'description' => esc_html__('Name of the wave the course belongs to.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_wave_title',
					'parameter'   => 'wave_id'
				],
				'bank_account'           => [
					'id'          => 'bank_account',
					'tag'         => 'bank_account',
					'description' => esc_html__('Bank account number for sending money.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
				'iban'                   => [
					'id'          => 'iban',
					'tag'         => 'iban',
					'description' => esc_html__('IBAN code.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
				'bic'                    => [
					'id'          => 'bic',
					'tag'         => 'bic',
					'description' => esc_html__('BIC code.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
				'vs_symbol'              => [
					'id'          => 'vs_symbol',
					'tag'         => 'vs_symbol',
					'description' => esc_html__('Unique variable symbol to identify each payment. One student has one unique VS per wave.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_variable_symbol',
					'parameter'   => 'variable_symbol'
				],
				'vs_registration_symbol' => [
					'id'          => 'vs_registration_symbol',
					'tag'         => 'vs_registration_symbol',
					'description' => esc_html__('Unique variable symbol to identify each payment. One student has one unique VS per course registration.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_variable_symbol',
					'parameter'   => 'registration_variable_symbol'
				],
				'cs_symbol'              => [
					'id'          => 'cs_symbol',
					'tag'         => 'cs_symbol',
					'description' => esc_html__('Fixed constant symbol to identify course payments in your bank statement (among other transfers to your account).', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
			]),
			'email_payment'                    => apply_filters('esr_tags_email_payment', [
				'first_name'       => [
					'id'          => 'first_name',
					'tag'         => 'student_name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'last_name'        => [
					'id'          => 'last_name',
					'tag'         => 'student_surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'courses_list'     => [
					'id'          => 'courses_list',
					'tag'         => 'courses_list',
					'description' => esc_html__('List of courses the student is confirmed to.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_courses_list_by_id',
					'parameter'   => 'courses_list'
				],
				'wave_title'       => [
					'id'          => 'wave_title',
					'tag'         => 'wave_title',
					'description' => esc_html__('Name of the wave the course belongs to.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_wave_title',
					'parameter'   => 'wave_id'
				],
				'total_price'      => [
					'id'          => 'total_price',
					'tag'         => 'total_price',
					'description' => esc_html__('Total price of all confirmed courses.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_price',
					'parameter'   => 'to_pay'
				],
				'already_paid'     => [
					'id'          => 'already_paid',
					'tag'         => 'already_paid',
					'description' => esc_html__('How much has already been paid (see Payments section).', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_price',
					'parameter'   => 'payment_value'
				],
				'price_difference' => [
					'id'          => 'price_difference',
					'tag'         => 'price_difference',
					'description' => esc_html__('Difference between the Price To Pay and the price Already Paid.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_price_difference',
					'parameter'   => 'payment'
				],
				'bank_account'     => [
					'id'          => 'bank_account',
					'tag'         => 'bank_account',
					'description' => esc_html__('Bank account number for sending money.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
				'iban'             => [
					'id'          => 'iban',
					'tag'         => 'iban',
					'description' => esc_html__('IBAN code.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
				'bic'              => [
					'id'          => 'bic',
					'tag'         => 'bic',
					'description' => esc_html__('BIC code.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
				'vs_symbol'        => [
					'id'          => 'vs_symbol',
					'tag'         => 'vs_symbol',
					'description' => esc_html__('Unique variable symbol to identify each payment. One student has one unique VS per wave.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_variable_symbol',
					'parameter'   => 'variable_symbol'
				],
				'cs_symbol'        => [
					'id'          => 'cs_symbol',
					'tag'         => 'cs_symbol',
					'description' => esc_html__('Fixed constant symbol to identify course payments in your bank statement (among other transfers to your account).', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
			]),
			'email_payment_confirmation'       => apply_filters('esr_tags_email_payment_confirmation', [
				'first_name'       => [
					'id'          => 'first_name',
					'tag'         => 'student_name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'last_name'        => [
					'id'          => 'last_name',
					'tag'         => 'student_surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'courses_list'     => [
					'id'          => 'courses_list',
					'tag'         => 'courses_list',
					'description' => esc_html__('List of courses the student is confirmed to.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_courses_list',
					'parameter'   => 'courses_list'
				],
				'wave_title'       => [
					'id'          => 'wave_title',
					'tag'         => 'wave_title',
					'description' => esc_html__('Name of the wave the course belongs to.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_wave_title',
					'parameter'   => 'wave_id'
				],
				'total_price'      => [
					'id'          => 'total_price',
					'tag'         => 'total_price',
					'description' => esc_html__('Total price of all confirmed courses.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_price',
					'parameter'   => 'to_pay'
				],
				'already_paid'     => [
					'id'          => 'already_paid',
					'tag'         => 'already_paid',
					'description' => esc_html__('How much has already been paid (see Payments section).', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_price',
					'parameter'   => 'payment_value'
				],
				'price_difference' => [
					'id'          => 'price_difference',
					'tag'         => 'price_difference',
					'description' => esc_html__('Difference between the Price To Pay and the price Already Paid.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_price_difference',
					'parameter'   => 'payment'
				],
				'bank_account'     => [
					'id'          => 'bank_account',
					'tag'         => 'bank_account',
					'description' => esc_html__('Bank account number for sending money.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
				'iban'             => [
					'id'          => 'iban',
					'tag'         => 'iban',
					'description' => esc_html__('IBAN code.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
				'bic'              => [
					'id'          => 'bic',
					'tag'         => 'bic',
					'description' => esc_html__('BIC code.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
				'vs_symbol'        => [
					'id'          => 'vs_symbol',
					'tag'         => 'vs_symbol',
					'description' => esc_html__('Unique variable symbol to identify each payment. One student has one unique VS per wave.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_variable_symbol',
					'parameter'   => 'variable_symbol'
				],
				'cs_symbol'        => [
					'id'          => 'cs_symbol',
					'tag'         => 'cs_symbol',
					'description' => esc_html__('Fixed constant symbol to identify course payments in your bank statement (among other transfers to your account).', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_settings_parameter',
				],
			]),
			'email_gdpr'                       => apply_filters('esr_tags_email_gdpr', [
				'first_name' => [
					'id'          => 'first_name',
					'tag'         => 'student_name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'last_name'  => [
					'id'          => 'last_name',
					'tag'         => 'student_surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'user_info'  => [
					'id'          => 'user_info',
					'tag'         => 'user_info',
					'description' => esc_html__('Tables with student information.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_string',
					'parameter'   => 'user_info'
				],
			]),
			'email_registration'               => apply_filters('esr_tags_email_registration', [
				'wave_title'        => [
					'id'          => 'wave_title',
					'tag'         => 'wave_title',
					'description' => esc_html__('Name of the wave the course belongs to.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_waves_title',
					'parameter'   => 'wave_ids'
				],
				'registered_exists' => [
					'id'          => 'registered_exists',
					'tag'         => 'registered_exists',
					'type'        => 'double',
					'description' => esc_html__('Anything written between these two tags will be displayed only if at least some courses were registered.', 'easy-school-registration'),
				],
				'list_registered'   => [
					'id'          => 'list_registered',
					'tag'         => 'list_registered',
					'description' => esc_html__('List of registered courses.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_registration_repeat_course_list',
					'parameter'   => 'list_registered'
				],
				'name'              => [
					'id'          => 'name',
					'tag'         => 'name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'surname'           => [
					'id'          => 'surname',
					'tag'         => 'surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'email'             => [
					'id'          => 'email',
					'tag'         => 'email',
					'description' => esc_html__('Student email.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'phone'             => [
					'id'          => 'phone',
					'tag'         => 'phone',
					'description' => esc_html__('Student phone.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'note'              => [
					'id'          => 'note',
					'tag'         => 'note',
					'description' => esc_html__('Note left during the registration.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'newsletter_option' => [
					'id'          => 'newsletter',
					'tag'         => 'newsletter_option',
					'description' => esc_html__('Newsletter checkbox selected. (Yes/No)', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_string',
					'parameter'   => 'checkbox_option'
				],
				'terms_option'      => [
					'id'          => 'terms-conditions',
					'tag'         => 'terms_option',
					'description' => esc_html__('Terms&Conditions checkbox selected. (Yes/No)', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_string',
					'parameter'   => 'checkbox_option'
				],
			]),
			'email_waiting'               => apply_filters('esr_tags_email_registration', [
				'wave_title'        => [
					'id'          => 'wave_title',
					'tag'         => 'wave_title',
					'description' => esc_html__('Name of the wave the course belongs to.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_wave_title',
					'parameter'   => 'wave_id'
				],
				'list_waiting'   => [
					'id'          => 'list_waiting',
					'tag'         => 'list_waiting',
					'description' => esc_html__('List of waiting courses.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_registration_repeat_course_list',
					'parameter'   => 'list_waiting'
				],
			]),
			'email_user_registration'          => apply_filters('esr_tags_email_user_registration', [
				'first_name' => [
					'id'          => 'first_name',
					'tag'         => 'student_name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'last_name'  => [
					'id'          => 'last_name',
					'tag'         => 'student_surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'user_name'  => [
					'id'          => 'user_name',
					'tag'         => 'user_name',
					'description' => esc_html__('Student username for login into the system. Students can see only the Student Info section. (..yourwebsite.com/wp-admin)', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_string',
					'parameter'   => 'user_name'
				],
				'password'   => [
					'id'          => 'password',
					'tag'         => 'password',
					'description' => esc_html__('Student password for login into the system. Students can see only the Student Info section. (..yourwebsite.com/wp-admin)', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_string',
					'parameter'   => 'password'
				],
			]),
			'thank_you_page'                   => apply_filters('esr_tags_thank_you_page', [
				'registered_exists' => [
					'id'          => 'registered_exists',
					'tag'         => 'registered_exists',
					'type'        => 'double',
					'description' => esc_html__('Anything written between these two tags will be displayed only if at least some courses were registered.', 'easy-school-registration'),
				],
				'list_registered'   => [
					'id'          => 'list_registered',
					'tag'         => 'list_registered',
					'description' => esc_html__('List of registered courses.', 'easy-school-registration'),
				],
				/*'text_for_level' => [
					'id'          => 'text_for_level',
					'tag'         => 'text_for_level',
					'type'        => 'double',
					'description' => esc_html__('Display text for specific level.', 'easy-school-registration'),
				],*/
			]),
			'email_title'                      => apply_filters('esr_tags_email_title', [
				'wave_title' => [
					'id'          => 'wave_title',
					'tag'         => 'wave_title',
					'description' => esc_html__('Name of the wave the course belongs to.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_wave_title',
					'parameter'   => 'wave_id'
				],
				'first_name' => [
					'id'          => 'first_name',
					'tag'         => 'student_name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'last_name'  => [
					'id'          => 'last_name',
					'tag'         => 'student_surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
			]),
			'confirmation_email_title'         => apply_filters('esr_tags_confirmation_email_title', [
				'wave_title'   => [
					'id'          => 'wave_title',
					'tag'         => 'wave_title',
					'description' => esc_html__('Name of the wave the course belongs to.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_wave_title',
					'parameter'   => 'wave_id'
				],
				'course_title' => [
					'id'          => 'course_title',
					'tag'         => 'course_title',
					'description' => esc_html__('Course name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_string',
					'parameter'   => 'course_title'
				],
				'first_name'   => [
					'id'          => 'first_name',
					'tag'         => 'student_name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'last_name'    => [
					'id'          => 'last_name',
					'tag'         => 'student_surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
			]),
			'waiting_email_title'         => apply_filters('esr_tags_waiting_email_title', [
				'wave_title'   => [
					'id'          => 'wave_title',
					'tag'         => 'wave_title',
					'description' => esc_html__('Name of the wave the course belongs to.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_wave_title',
					'parameter'   => 'wave_id'
				],
				'first_name'   => [
					'id'          => 'first_name',
					'tag'         => 'student_name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'last_name'    => [
					'id'          => 'last_name',
					'tag'         => 'student_surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
			]),
			'registration_email_title'         => apply_filters('esr_tags_registration_email_title', [
				'wave_title' => [
					'id'          => 'wave_title',
					'tag'         => 'wave_title',
					'description' => esc_html__('Name of the wave the course belongs to.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_waves_title',
					'parameter'   => 'wave_ids'
				],
				'first_name' => [
					'id'          => 'first_name',
					'tag'         => 'student_name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'last_name'  => [
					'id'          => 'last_name',
					'tag'         => 'student_surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
			]),
			'student_registration_email_title' => apply_filters('esr_tags_student_registration_email_title', [
				'first_name' => [
					'id'          => 'first_name',
					'tag'         => 'student_name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'last_name'  => [
					'id'          => 'last_name',
					'tag'         => 'student_surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
			]),
			'gdpr_email_title'                 => apply_filters('esr_tags_gdpr_email_title', [
				'first_name' => [
					'id'          => 'first_name',
					'tag'         => 'student_name',
					'description' => esc_html__('Student first name.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
				'last_name'  => [
					'id'          => 'last_name',
					'tag'         => 'student_surname',
					'description' => esc_html__('Student surname.', 'easy-school-registration'),
					'function'    => 'esr_tag_replace_user_registration_info',
					'parameter'   => 'user_registration_info'
				],
			])
		];

		if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
			$preset_tags['email_confirmation']['floating_price'] = [
				'id'          => 'floating_price',
				'tag'         => 'floating_price',
				'description' => esc_html__('Course price after considering all applicable discounts.', 'easy-school-registration'),
				'function'    => 'esr_tag_replace_floating_price',
				'parameter'   => 'floating_price'
			];
		}

		return $preset_tags;
	}

}
