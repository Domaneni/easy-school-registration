<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Settings {

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @return array
	 * @since 3
	 */
	public function esr_get_registered_settings() {
		$templater_settings_tag = new ESR_Settings_Tag_Templater();

		$style_settings = $this->get_default_style_settings();

		$esr_settings = [
			/** General Settings */
			'general'               => apply_filters( 'esr_settings_general', [
				'courses'       => [
					'pairing_mode' => [
						'id'      => 'pairing_mode',
						'name'    => esc_html__( 'Default Pairing Mode', 'easy-school-registration' ),
						'type'    => 'select',
						'options' => ESR()->pairing_mode->get_items_for_settings(),
						'chosen'  => true,
					],
					'halls'        => [
						'id'       => 'halls',
						'name'     => esc_html__( 'Halls', 'easy-school-registration' ),
						'type'     => 'add_list_halls',
						'singular' => esc_html__( 'Hall', 'easy-school-registration' ),
					],
					'groups'       => [
						'id'       => 'groups',
						'name'     => esc_html__( 'Groups', 'easy-school-registration' ),
						'desc'     => esc_html__( 'Assigning courses to groups, you are able to define group bulk discounts as well as specific colors and filters in the Registration Form.', 'easy-school-registration' ),
						'type'     => 'add_list',
						'singular' => esc_html__( 'Group', 'easy-school-registration' ),
					],
					'levels'       => [
						'id'       => 'levels',
						'name'     => esc_html__( 'Levels', 'easy-school-registration' ),
						'desc'     => esc_html__( 'Assigning courses to levels, you are able to define specific colors and filters in the Registration Form.', 'easy-school-registration' ),
						'type'     => 'add_list',
						'singular' => esc_html__( 'Level', 'easy-school-registration' ),
					],
				],
				'currency'      => [
					'currency'          => [
						'id'      => 'currency',
						'name'    => esc_html__( 'Currency', 'easy-school-registration' ),
						'desc'    => esc_html__( 'Choose your currency.', 'easy-school-registration' ),
						'type'    => 'select',
						'options' => ESR()->currency->esr_get_currencies_for_select(),
						'chosen'  => true,
					],
					'currency_position' => [
						'id'      => 'currency_position',
						'name'    => esc_html__( 'Currency Position', 'easy-school-registration' ),
						'desc'    => esc_html__( 'Choose the position of the currency symbol.', 'easy-school-registration' ),
						'type'    => 'select',
						'options' => [
							'before'            => esc_html__( 'Before - $10', 'easy-school-registration' ),
							'before_with_space' => esc_html__( 'Before with space - $ 10', 'easy-school-registration' ),
							'after'             => esc_html__( 'After - 10$', 'easy-school-registration' ),
							'after_with_space'  => esc_html__( 'After with space - 10 $', 'easy-school-registration' ),
						],
					]
				],
				'payments'      => [
					'bank_account'   => [
						'id'      => 'bank_account',
						'name'    => esc_html__( 'Bank Account', 'easy-school-registration' ),
						'type'    => 'text',
						'options' => 'small',
					],
					'iban'           => [
						'id'      => 'iban',
						'name'    => esc_html__( 'IBAN', 'easy-school-registration' ),
						'type'    => 'text',
						'options' => 'small',
					],
					'bic'            => [
						'id'      => 'bic',
						'name'    => esc_html__( 'BIC', 'easy-school-registration' ),
						'type'    => 'text',
						'options' => 'small',
					],
					'cs_symbol'      => [
						'id'   => 'cs_symbol',
						'name' => esc_html__( 'Constant Symbol', 'easy-school-registration' ),
						'type' => 'text',
						'size' => 'small',
					],
					'round_payments' => [
						'id'   => 'round_payments',
						'name' => esc_html__( 'Round Payments', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, all new payments will be rounded to whole numbers.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
				],
				'debts'         => [
					'debts_enabled' => [
						'id'   => 'debts_enabled',
						'name' => esc_html__( 'Enable Debts Tab', 'easy-school-registration' ),
						'desc' => esc_html__( 'Debts tab will be shown in Payment menu.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
				],
				'registrations' => [
					'show_phone'                 => [
						'id'   => 'show_phone',
						'name' => esc_html__( 'Show Phone Number', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, phone numbers will be displayed in the Registration table.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'free_registrations_enabled' => [
						'id'   => 'free_registrations_enabled',
						'name' => esc_html__( 'Free Course', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Free Course functionality will become available. Visit Documentation page to learn about this feature.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
				],
				'advanced'      => [
					'preload_styles' => [
						'id'   => 'preload_styles',
						'name' => esc_html__( 'Pre-load CSS Styles', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, all scripts will be loaded on every page. Caution, use only if you fully understand the impact!', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
				],
				'logs'          => [
					'log_enabled' => [
						'id'   => 'log_enabled',
						'name' => esc_html__( 'Enabled Log', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => true,
					],
				],
			] ),
			'admin'                 => apply_filters( 'esr_settings_admin', [
				'main'              => [
					'show_basic_statistics' => [
						'id'   => 'show_basic_statistics',
						'name' => esc_html__( 'Show Basic Statistics', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
				],
				'course'            => [
					'multiple_dates' => [
						'id'   => 'multiple_dates',
						'name' => esc_html__( 'Enable Multiple Dates', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'disable_couples' => [
						'id'   => 'disable_couples',
						'name' => esc_html__( 'Disable couples registration', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
				],
				'course_in_numbers' => [
					'courses_schedule_style' => [
						'id'      => 'courses_schedule_style',
						'name'    => esc_html__( 'Display Style', 'easy-school-registration' ),
						'type'    => 'select',
						'options' => ESR()->schedule_style->get_items_for_settings() + [ 'as_table' => 'As Table' ],
						'std'     => 'as_table',
						'chosen'  => true,
					],
				],
				'registration'      => [
					'show_payment_enabled' => [
						'id'   => 'show_payment_enabled',
						'name' => esc_html__( 'Payment Status', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Payment Status will be shown in registrations tab.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'show_user_note_enabled' => [
						'id'   => 'show_user_note_enabled',
						'name' => esc_html__( 'User Note', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, User Note will be shown in registrations tab.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'show_separated_names_enabled' => [
						'id'   => 'show_separated_names_enabled',
						'name' => esc_html__( 'User Name', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, User Name will be separated to two columns.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
				],
				'payment'           => [
					'show_courses'           => [
						'id'   => 'show_courses',
						'name' => esc_html__( 'Show Courses', 'easy-school-registration' ),
						'desc' => esc_html__( 'By enabling, courses column will be shown in payments table.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'show_phone_in_payments' => [
						'id'   => 'show_phone_in_payments',
						'name' => esc_html__( 'Show Phone Number', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, phone numbers will be displayed in the Payment table.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'default_payment_method' => [
						'id'      => 'default_payment_method',
						'name'    => esc_html__( 'Default Payment Method', 'easy-school-registration' ),
						'desc'    => esc_html__( 'Select which payment method will be used as default in payment editor.', 'easy-school-registration' ),
						'type'    => 'select',
						'options' => ESR()->payment_type->esr_get_key_title_items(),
						'std'     => false,
					],
					'payment_types'          => [
						'id'       => 'payment_types',
						'name'     => esc_html__( 'Payment Methods', 'easy-school-registration' ),
						'desc'     => esc_html__( '.', 'easy-school-registration' ),
						'type'     => 'add_payment_types_list',
						'singular' => esc_html__( 'Payment Method', 'easy-school-registration' ),
					],
				],
				'default_values'    => [
					'course_price' => [
						'id'   => 'course_price',
						'name' => esc_html__( 'Course Price', 'easy-school-registration' ),
						'type' => 'number',
						'min'  => 0,
					],
					'course_times' => [
						'id'       => 'course_times',
						'name'     => esc_html__( 'Course Times', 'easy-school-registration' ),
						'type'     => 'add_list_times',
						'singular' => esc_html__( 'Course Time', 'easy-school-registration' ),
					],
				],
			] ),
			/** Emails Settings */
			'emails'                => apply_filters( 'esr_settings_emails', [
				'main'                       => [
					'from_name'              => [
						'id'          => 'from_name',
						'name'        => esc_html__( 'From Name', 'easy-school-registration' ),
						'desc'        => esc_html__( 'Sender name for automated emails. This should ideally be your site name.', 'easy-school-registration' ),
						'type'        => 'text',
						'std'         => get_bloginfo( 'name' ),
						'allow_blank' => false
					],
					'from_email'             => [
						'id'          => 'from_email',
						'name'        => esc_html__( 'From Email', 'easy-school-registration' ),
						'desc'        => esc_html__( 'Email address for automated emails. It will also act as "from" and "reply-to" address.', 'easy-school-registration' ),
						'type'        => 'email',
						'std'         => get_bloginfo( 'admin_email' ),
						'allow_blank' => false
					],
					'bcc_email'              => [
						'id'          => 'bcc_email',
						'name'        => esc_html__( 'Bcc Email Address', 'easy-school-registration' ),
						'desc'        => esc_html__( 'Email address to receive a secret copy of every email sent by the system. Allows you to back up and re-send any email.', 'easy-school-registration' ),
						'type'        => 'email',
						'allow_blank' => true
					],
					'floating_price_enabled' => [
						'id'   => 'floating_price_enabled',
						'name' => esc_html__( 'Enable Floating Price', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, floting_price tag will be available for Course Confirmation Emails.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'email_description_01'   => [
						'id'   => 'email_description_01',
						'desc' => esc_html__( '<strong>Important:</strong> Every web hosting has a different default limit for sending emails. During the registration process, based on which email templates you are using, each Student can receive several emails from the system. Make sure to check your web hosting settings and increase the limit to at least 500 emails/hour. If you run many courses, we recommend even higher number just to be safe during the rush hours after opening registration. Having a low limit can result in Students not getting their emails as the system will not be able to send emails over the limit.', 'easy-school-registration' ),
						'type' => 'description',
					],
					'use_wp_mail'            => [
						'id'   => 'use_wp_mail',
						'name' => esc_html__( 'Enable WP mail', 'easy-school-registration' ),
						'desc' => esc_html__( 'This plugin is using PHP mail function to sending emails. If you want you can change it to Wordpress wp_mail function.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
				],
				'user_registration_email'    => [
					'user_registration_email_enabled' => [
						'id'   => 'user_registration_email_enabled',
						'name' => esc_html__( 'Enable User Registration Emails', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, login details to Student Info section will be sent. The email is sent only once during the first sign-up.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => true,
					],
					'user_registration_email_subject' => [
						'id'        => 'user_registration_email_subject',
						'name'      => esc_html__( 'Email Subject', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'student_registration_email_title' ) ),
						'type'      => 'text',
					],
					'user_registration_email_body'    => [
						'id'        => 'user_registration_email_body',
						'name'      => esc_html__( 'Email Body', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'email_user_registration' ) ),
						'type'      => 'full_editor',
					],
				],
				'registration_email'         => [
					'registration_email_enabled' => [
						'id'   => 'registration_email_enabled',
						'name' => esc_html__( 'Enable Registration Overview Emails', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, an overview email of registered courses will be sent.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'registration_email_subject' => [
						'id'        => 'registration_email_subject',
						'name'      => esc_html__( 'Email Subject', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'registration_email_title' ) ),
						'type'      => 'text',
					],
					'registration_email_body'    => [
						'id'        => 'registration_email_body',
						'name'      => esc_html__( 'Email Body', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'email_registration' ) ),
						'type'      => 'full_editor',
					],
				],
				'confirmation_email'         => [
					'confirmation_email_enabled' => [
						'id'   => 'confirmation_email_enabled',
						'name' => esc_html__( 'Enable Course Confirmation Emails', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Confirmation Email is sent for every course with status "Confirmed".', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => true,
					],
					'confirmation_email_subject' => [
						'id'        => 'confirmation_email_subject',
						'name'      => esc_html__( 'Email Subject', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'confirmation_email_title' ) ),
						'type'      => 'text',
					],
					'confirmation_email_body'    => [
						'id'        => 'confirmation_email_body',
						'name'      => esc_html__( 'Email Body', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'email_confirmation' ) ),
						'type'      => 'full_editor',
					],
				],
				'waiting_email'              => [
					'waiting_email_enabled'   => [
						'id'   => 'waiting_email_enabled',
						'name' => esc_html__( 'Enable Waiting Emails', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Waiting Email is sent for every course with status "Waiting".', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'waiting_email_automatic' => [
						'id'   => 'waiting_email_automatic',
						'name' => esc_html__( 'Enable Automatic Waiting Emails', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Waiting Email will be sent automatically.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'waiting_email_days'      => [
						'id'          => 'waiting_email_days',
						'name'        => esc_html__( 'Days', 'easy-school-registration' ),
						'desc'        => esc_html__( 'How many days before wave starts do you want to send this email?', 'easy-school-registration' ),
						'type'        => 'select',
						'options'     => [
							'1'  => esc_html__( '1 day after registration starts', 'easy-school-registration' ),
							'2'  => esc_html__( '2 days after registration starts', 'easy-school-registration' ),
							'3'  => esc_html__( '3 days after registration starts', 'easy-school-registration' ),
							'4'  => esc_html__( '4 days after registration starts', 'easy-school-registration' ),
							'5'  => esc_html__( '5 days after registration starts', 'easy-school-registration' ),
							'6'  => esc_html__( '6 days after registration starts', 'easy-school-registration' ),
							'7'  => esc_html__( '7 days after registration starts', 'easy-school-registration' ),
							'8'  => esc_html__( '8 days after registration starts', 'easy-school-registration' ),
							'9'  => esc_html__( '9 days after registration starts', 'easy-school-registration' ),
							'10' => esc_html__( '10 days after registration starts', 'easy-school-registration' ),
						],
						'std'         => 2,
						'allow_blank' => true
					],
					'waiting_email_subject'   => [
						'id'        => 'waiting_email_subject',
						'name'      => esc_html__( 'Email Subject', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'waiting_email_title' ) ),
						'type'      => 'text',
					],
					'waiting_email_body'      => [
						'id'        => 'waiting_email_body',
						'name'      => esc_html__( 'Email Body', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'email_waiting' ) ),
						'type'      => 'full_editor',
					],
				],
				'payment_email'              => [
					'payment_email_enabled'          => [
						'id'   => 'payment_email_enabled',
						'name' => esc_html__( 'Enable Payment Emails', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Payment Email can be sent via Payment section.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => true,
					],
					'payment_email_automatic'        => [
						'id'   => 'payment_email_automatic',
						'name' => esc_html__( 'Enable Automatic Payment Emails', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Payment Email will be sent automatically.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'payment_email_days'             => [
						'id'          => 'payment_email_days',
						'name'        => esc_html__( 'Days', 'easy-school-registration' ),
						'desc'        => esc_html__( 'How many days before wave starts do you want to send this email?', 'easy-school-registration' ),
						'type'        => 'select',
						'options'     => [
							'-10' => esc_html__( '10 days before wave starts', 'easy-school-registration' ),
							'-9'  => esc_html__( '9 days before wave starts', 'easy-school-registration' ),
							'-8'  => esc_html__( '8 days before wave starts', 'easy-school-registration' ),
							'-7'  => esc_html__( '7 days before wave starts', 'easy-school-registration' ),
							'-6'  => esc_html__( '6 days before wave starts', 'easy-school-registration' ),
							'-5'  => esc_html__( '5 days before wave starts', 'easy-school-registration' ),
							'-4'  => esc_html__( '4 days before wave starts', 'easy-school-registration' ),
							'-3'  => esc_html__( '3 days before wave starts', 'easy-school-registration' ),
							'-2'  => esc_html__( '2 days before wave starts', 'easy-school-registration' ),
							'-1'  => esc_html__( '1 day before wave starts', 'easy-school-registration' ),
						],
						'std'         => - 1,
						'allow_blank' => true
					],
					'payment_email_subject'          => [
						'id'        => 'payment_email_subject',
						'name'      => esc_html__( 'Email Subject', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'email_title' ) ),
						'type'      => 'text',
					],
					'payment_email_body'             => [
						'id'        => 'payment_email_body',
						'name'      => esc_html__( 'Email Body', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'email_payment' ) ),
						'type'      => 'full_editor',
					],
					'payment_reminder_email_enabled' => [
						'id'   => 'payment_reminder_email_enabled',
						'name' => esc_html__( 'Enable Automatic Payment Reminder Emails', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Payment Reminder Email will be sent automatically.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'payment_reminder_email_days'    => [
						'id'          => 'payment_reminder_email_days',
						'name'        => esc_html__( 'Days', 'easy-school-registration' ),
						'desc'        => esc_html__( 'How many days after last payment email you want to send next one?', 'easy-school-registration' ),
						'type'        => 'select',
						'options'     => [
							'1'  => esc_html__( '1 day after last payment email', 'easy-school-registration' ),
							'2'  => esc_html__( '2 days after last payment email', 'easy-school-registration' ),
							'3'  => esc_html__( '3 days after last payment email', 'easy-school-registration' ),
							'4'  => esc_html__( '4 days after last payment email', 'easy-school-registration' ),
							'5'  => esc_html__( '5 days after last payment email', 'easy-school-registration' ),
							'6'  => esc_html__( '6 days after last payment email', 'easy-school-registration' ),
							'7'  => esc_html__( '7 days after last payment email', 'easy-school-registration' ),
							'8'  => esc_html__( '8 days after last payment email', 'easy-school-registration' ),
							'9'  => esc_html__( '9 days after last payment email', 'easy-school-registration' ),
							'10' => esc_html__( '10 days after last payment email', 'easy-school-registration' ),
						],
						'std'         => 5,
						'allow_blank' => true
					],
					'payment_reminder_email_subject' => [
						'id'        => 'payment_reminder_email_subject',
						'name'      => esc_html__( 'Email Subject', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'email_title' ) ),
						'type'      => 'text',
					],
					'payment_reminder_email_body'    => [
						'id'        => 'payment_reminder_email_body',
						'name'      => esc_html__( 'Email Body', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'email_payment' ) ),
						'type'      => 'full_editor',
					],
				],
				'payment_confirmation_email' => [
					'payment_confirmation_email_enabled' => [
						'id'   => 'payment_confirmation_email_enabled',
						'name' => esc_html__( 'Enable Payment Confirmation Emails', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Payment Confirmation Email is sent when the payment is confirmed in the Payment section.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'payment_confirmation_email_subject' => [
						'id'        => 'payment_confirmation_email_subject',
						'name'      => esc_html__( 'Email Subject', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'email_title' ) ),
						'type'      => 'text',
					],
					'payment_confirmation_email_body'    => [
						'id'        => 'payment_confirmation_email_body',
						'name'      => esc_html__( 'Email Body', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'email_payment_confirmation' ) ),
						'type'      => 'full_editor',
					],
				],
				'gdpr_email'                 => [
					'gdpr_email_enabled' => [
						'id'   => 'gdpr_email_enabled',
						'name' => esc_html__( 'Enable GDPR Emails', 'easy-school-registration' ),
						'desc' => esc_html__( 'General Data Protection Regulation - any user can request an overview and deletion of personal data stored about him. If enabled, such overview can be sent via Student section.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'gdpr_email_subject' => [
						'id'        => 'gdpr_email_subject',
						'name'      => esc_html__( 'Email Subject', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'gdpr_email_title' ) ),
						'type'      => 'text',
					],
					'gdpr_email_body'    => [
						'id'        => 'gdpr_email_body',
						'name'      => esc_html__( 'Email Body', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'email_gdpr' ) ),
						'type'      => 'full_editor',
					],
				],
				'admin_note_email'           => [
					'registration_note_email_enabled' => [
						'id'   => 'registration_note_email_enabled',
						'name' => esc_html__( 'Enable Email', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, administrator will receive email with note from registration.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'admin_note_email'         => [
						'id'   => 'admin_note_email',
						'name' => esc_html__( 'Email', 'easy-school-registration' ),
						'desc' => esc_html__( 'Email where the note will be sent.', 'easy-school-registration' ),
						'type' => 'text',
					],
				],
			] ),
			'schedule_registration' => apply_filters( 'esr_settings_schedule_registration', [
				'main'                 => [
					'schedule_style'                    => [
						'id'      => 'schedule_style',
						'name'    => esc_html__( 'Default Schedule Style', 'easy-school-registration' ),
						'type'    => 'select',
						'options' => ESR()->schedule_style->get_items_for_settings(),
						'chosen'  => true,
					],
					'show_schedule_before_registration' => [
						'id'   => 'show_schedule_before_registration',
						'name' => esc_html__( 'Show Schedule Before Registration', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, wave schedule will be shown in registration form before registration starts.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'dancing_with_enforce'              => [
						'id'   => 'dancing_with_enforce',
						'name' => esc_html__( 'Partner Selection Required', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, students have to select during the registration whether they have a partner or not. This greatly reduces mistakes during registration.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => true,
					],
					'show_phone_input'                  => [
						'id'   => 'show_phone_input',
						'name' => esc_html__( 'Show Phone Input', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, show Phone Number input during registration.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => true,
					],
					'phone_required'                    => [
						'id'   => 'phone_required',
						'name' => esc_html__( 'Phone Number Required', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Phone Number is required during registration.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => true,
					],
					'reconfirm_email_required'          => [
						'id'   => 'reconfirm_email_required',
						'name' => esc_html__( 'Re-confirm Student Email', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, emaill address will be required 2x in the registration form. Helps to prevent misspells in the email address - recommended feature.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
                    'allow_multiple_registrations'          => [
                        'id'   => 'allow_multiple_registrations',
                        'name' => esc_html__( 'Allow multiple registrations', 'easy-school-registration' ),
                        'desc' => esc_html__( 'If enabled, user will be able to register multiple times on the same course.', 'easy-school-registration' ),
                        'type' => 'checkbox',
                        'std'  => false,
                    ],
				],
				'registration_opening' => [
					'registration_not_opened' => [
						'id'        => 'registration_not_opened',
						'name'      => esc_html__( 'Registration Not Opened Yet', 'easy-school-registration' ),
						'desc'      => esc_html__( 'Available template tags:', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( [
							'registration_start' => [
								'id'          => 'registration_start',
								'tag'         => 'registration_start',
								'description' => esc_html__( 'Date and time of wave registration.', 'easy-school-registration' ),
							]
						] ),
						'type'      => 'full_editor',
					],
					'registration_closed'     => [
						'id'   => 'registration_closed',
						'name' => esc_html__( 'Registration Closed', 'easy-school-registration' ),
						'type' => 'full_editor',
					],
				],
				'student_form'         => [
					'leader_label'            => [
						'id'   => 'leader_label',
						'name' => esc_html__( 'Dancing Us Leader', 'easy-school-registration' ),
						'desc' => esc_html__( 'Change default Leader translation in registration form', 'easy-school-registration' ),
						'type' => 'text',
					],
					'follower_label'          => [
						'id'   => 'follower_label',
						'name' => esc_html__( 'Dancing Us Follower', 'easy-school-registration' ),
						'desc' => esc_html__( 'Change default Follower translation in registration form', 'easy-school-registration' ),
						'type' => 'text',
					],
					'registration_note_title' => [
						'id'   => 'registration_note_title',
						'name' => esc_html__( 'Note Title', 'easy-school-registration' ),
						'desc' => esc_html__( 'Customize the title of the Note section in the Registration Form. If left blank, the title is "Note".', 'easy-school-registration' ),
						'type' => 'text',
						'std'  => esc_html__( 'Note', 'easy-school-registration' ),
					],
					'note_disabled'              => [
						'id'   => 'note_disabled',
						'name' => esc_html__( 'Disable Note', 'easy-school-registration' ),
						'desc' => esc_html__( 'If checked, note field will be removed from registration form', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'note_limit'              => [
						'id'   => 'note_limit',
						'name' => esc_html__( 'Note Limit', 'easy-school-registration' ),
						'desc' => esc_html__( 'Set how many characters can students write to note.', 'easy-school-registration' ),
						'type' => 'number',
						'std'  => 0,
						'min'  => 0
					],
				],
				'thank_you'            => [
					'thank_you_text' => [
						'id'        => 'thank_you_text',
						'name'      => esc_html__( 'Thank You Text', 'easy-school-registration' ),
						'desc_tags' => $templater_settings_tag->esr_print_tags_table( ESR()->tags->get_tags( 'thank_you_page' ) ),
						'type'      => 'full_editor',
					]
				],
				'newsletter'           => [
					'newsletter_enabled' => [
						'id'   => 'newsletter_enabled',
						'name' => esc_html__( 'Enable Newsletter Consent', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, newsletter confirmation checkbox will be displayed during registration.', 'easy-school-registration' ),
						'type' => 'checkbox',
					],
					'newsletter_default' => [
						'id'   => 'newsletter_default',
						'name' => esc_html__( 'Newsletter Opt-out', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, newsletter checkbox will be ticked by default and students can opt-out.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => true,
					],
					'newsletter_text'    => [
						'id'   => 'newsletter_text',
						'name' => esc_html__( 'Confirmation Text', 'easy-school-registration' ),
						'type' => 'full_editor',
					],
				],
				'terms_conditions'     => [
					'terms_conditions_enabled'  => [
						'id'   => 'terms_conditions_enabled',
						'name' => esc_html__( 'Enable Terms & Conditions', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Terms & Conditions checkbox will be displayed during registration.', 'easy-school-registration' ),
						'type' => 'checkbox',
					],
					'terms_conditions_required' => [
						'id'   => 'terms_conditions_required',
						'name' => esc_html__( 'Require Terms & Conditions', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, Terms & Conditions confirmation will be required to finish registration.', 'easy-school-registration' ),
						'type' => 'checkbox',
					],
					'terms_conditions_text'     => [
						'id'   => 'terms_conditions_text',
						'name' => esc_html__( 'Terms & Conditions Text', 'easy-school-registration' ),
						'type' => 'full_editor',
					],
				],
				'ribbons'              => [
					'enable_full_course_ribbon'     => [
						'id'   => 'enable_full_course_ribbon',
						'name' => esc_html__( 'Enable Ribbon For Full Courses', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, ribbon will be show for full courses.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'full_ribbon_text'              => [
						'id'   => 'full_ribbon_text',
						'name' => esc_html__( 'Full Ribbon Text', 'easy-school-registration' ),
						'type' => 'text',
					],
					'enable_disabled_course_ribbon' => [
						'id'   => 'enable_disabled_course_ribbon',
						'name' => esc_html__( 'Enable Ribbon For Disabled Courses', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled, ribbon will be show for disabled courses.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'disabled_ribbon_text'          => [
						'id'   => 'disabled_ribbon_text',
						'name' => esc_html__( 'Disabled Ribbon Text', 'easy-school-registration' ),
						'type' => 'text',
					],
				],
			] ),
			'student_section'       => apply_filters( 'esr_settings_student_section', [
				'payments'      => [
					'show_already_paid' => [
						'id'   => 'show_already_paid',
						'name' => esc_html__( 'Show Already Paid', 'easy-school-registration' ),
						'desc' => esc_html__( 'Upon loggin to the account, the student can see how much money was received by the school for his registration in comparison to how much he needs to pay in total.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
				],
				'registrations' => [
					'show_partner' => [
						'id'   => 'show_partner',
						'name' => esc_html__( 'Show Paired Partner', 'easy-school-registration' ),
						'desc' => esc_html__( 'Show automatically paired partner name.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
				],
			] ),
			// please enable Show Already Paid by default

			'style'      => apply_filters( 'esr_settings_style', $style_settings ),
			'licenses'   => apply_filters( 'esr_settings_licenses', [] ),
			'extensions' => apply_filters( 'esr_settings_extensions', [] ),
		];

		return apply_filters( 'esr_registered_settings', $esr_settings );
	}


	public function esr_get_registered_settings_sections() {

		static $sections = false;

		if ( false !== $sections ) {
			return $sections;
		}

		$style_sections = [];

		if ( ! class_exists( 'ESR_Styling' ) ) {
			$style_sections = [
				'day'             => esc_html__( 'Day', 'easy-school-registration' ),
				'hall'            => esc_html__( 'Hall', 'easy-school-registration' ),
				'normal_course'   => esc_html__( 'Normal Course', 'easy-school-registration' ),
				'selected_course' => esc_html__( 'Selected Course', 'easy-school-registration' ),
				'full_course'     => esc_html__( 'Full Course', 'easy-school-registration' ),
				'disabled_course' => esc_html__( 'Disabled Course', 'esr-styling' ),
				'user_form'       => esc_html__( 'Registration Form', 'easy-school-registration' ),
			];
		}

		$sections = [
			'general'               => apply_filters( 'esr_settings_sections_general', [
				'courses'       => esc_html__( 'Courses', 'easy-school-registration' ),
				'currency'      => esc_html__( 'Currency', 'easy-school-registration' ),
				'payments'      => esc_html__( 'Payments', 'easy-school-registration' ),
				'debts'         => esc_html__( 'Student Debts', 'easy-school-registration' ),
				'registrations' => esc_html__( 'Registration Section', 'easy-school-registration' ),
				'advanced'      => esc_html__( 'Advanced', 'easy-school-registration' ),
				'logs'          => esc_html__( 'Logs', 'easy-school-registration' ),
			] ),
			'emails'                => apply_filters( 'esr_settings_sections_emails', [
				'main'                       => esc_html__( 'General', 'easy-school-registration' ),
				'user_registration_email'    => esc_html__( 'User Registration Email', 'easy-school-registration' ),
				'registration_email'         => esc_html__( 'Registration Overview Email', 'easy-school-registration' ),
				'confirmation_email'         => esc_html__( 'Course Confirmation Email', 'easy-school-registration' ),
				//'waiting_email'              => esc_html__('Waiting Email', 'easy-school-registration'),
				'payment_email'              => esc_html__( 'Payment Email', 'easy-school-registration' ),
				'payment_confirmation_email' => esc_html__( 'Payment Confirmation Email', 'easy-school-registration' ),
				'gdpr_email'                 => esc_html__( 'GDPR Email', 'easy-school-registration' ),
				'admin_note_email'           => esc_html__( 'Registration Note Email', 'easy-school-registration' ),
			] ),
			'schedule_registration' => apply_filters( 'esr_settings_sections_schedule_registration', [
				'main'                 => esc_html__( 'General', 'easy-school-registration' ),
				'registration_opening' => esc_html__( 'Open / Closed', 'easy-school-registration' ),
				'student_form'         => esc_html__( 'Registration Form', 'easy-school-registration' ),
				'thank_you'            => esc_html__( 'Thank You Page', 'easy-school-registration' ),
				'newsletter'           => esc_html__( 'Newsletter', 'easy-school-registration' ),
				'terms_conditions'     => esc_html__( 'Terms & Conditions', 'easy-school-registration' ),
				'ribbons'              => esc_html__( 'Ribbons', 'easy-school-registration' ),
			] ),
			'admin'                 => apply_filters( 'esr_settings_sections_admin', [
				'main'              => esc_html__( 'General', 'easy-school-registration' ),
				'course'            => esc_html__( 'Course', 'easy-school-registration' ),
				'course_in_numbers' => esc_html__( 'Course In Numbers', 'easy-school-registration' ),
				'registration'      => esc_html__( 'Registration', 'easy-school-registration' ),
				'payment'           => esc_html__( 'Payments', 'easy-school-registration' ),
				'default_values'    => esc_html__( 'Default Values', 'easy-school-registration' ),
			] ),
			'student_section'       => apply_filters( 'esr_settings_sections_student_section', [
				'payments' => esc_html__( 'Payments', 'easy-school-registration' ),
			] ),
			'style'                 => apply_filters( 'esr_settings_sections_style', $style_sections ),
			'licenses'              => apply_filters( 'esr_settings_sections_licenses', [] ),
			'extensions'            => apply_filters( 'esr_settings_sections_extensions', [] ),
		];

		$sections = apply_filters( 'esr_settings_sections', $sections );

		return $sections;
	}


	public function esr_get_settings_tabs() {
		$settings = $this->esr_get_registered_settings();

		$tabs                          = [];
		$tabs['general']               = esc_html__( 'General', 'easy-school-registration' );
		$tabs['emails']                = esc_html__( 'Emails', 'easy-school-registration' );
		$tabs['schedule_registration'] = esc_html__( 'Schedule / Registration', 'easy-school-registration' );
		$tabs['admin']                 = esc_html__( 'Administration', 'easy-school-registration' );
		$tabs['student_section']       = esc_html__( 'Student Section', 'easy-school-registration' );

		if ( ! empty( $settings['style'] ) ) {
			$tabs['style'] = esc_html__( 'Style', 'easy-school-registration' );
		}
		if ( ! empty( $settings['extensions'] ) ) {
			$tabs['extensions'] = esc_html__( 'Extensions', 'easy-school-registration' );
		}
		if ( ! empty( $settings['licenses'] ) ) {
			$tabs['licenses'] = esc_html__( 'Licenses', 'easy-school-registration' );
		}

		return apply_filters( 'esr_settings_tabs', $tabs );
	}


	public function esr_get_settings_tab_sections( $tab = false ) {

		$tabs     = [];
		$sections = $this->esr_get_registered_settings_sections();

		if ( $tab && ! empty( $sections[ $tab ] ) ) {
			$tabs = $sections[ $tab ];
		} else if ( $tab ) {
			$tabs = [];
		}

		return $tabs;
	}


	public static function esr_register_settings() {
		if ( false == get_option( 'esr_settings' ) ) {
			update_option( 'esr_settings', [] );
		}

		foreach ( ESR()->settings->esr_get_registered_settings() as $tab => $sections ) {
			foreach ( $sections as $section => $settings ) {

				// Check for backwards compatibility
				$section_tabs = ESR()->settings->esr_get_settings_tab_sections( $tab );
				if ( ! is_array( $section_tabs ) || ! array_key_exists( $section, $section_tabs ) ) {
					$section  = 'main';
					$settings = $sections;
				}

				add_settings_section( 'esr_settings_' . $tab . '_' . $section, __return_null(), '__return_false', 'esr_settings_' . $tab . '_' . $section );

				foreach ( $settings as $option ) {
					// For backwards compatibility
					if ( empty( $option['id'] ) ) {
						continue;
					}

					$args = wp_parse_args( $option, [
						'section'       => $section,
						'id'            => null,
						'desc'          => '',
						'name'          => '',
						'size'          => null,
						'options'       => '',
						'std'           => '',
						'min'           => null,
						'max'           => null,
						'step'          => null,
						'chosen'        => null,
						'multiple'      => null,
						'placeholder'   => null,
						'allow_blank'   => true,
						'readonly'      => false,
						'faux'          => false,
						'tooltip_title' => false,
						'tooltip_desc'  => false,
						'field_class'   => '',
						'prefix'        => 'esr_',
						'template'      => 'ESR_Settings_Helper_Templater'
					] );

					$callback = $args['prefix'] . $args['type'] . '_callback';
					add_settings_field( 'esr_settings[' . $args['id'] . ']', $args['name'], method_exists( $args['template'], $callback ) ? [ $args['template'], $callback ] : '', 'esr_settings_' . $tab . '_' . $section, 'esr_settings_' . $tab . '_' . $section, $args );
				}
			}
		}

		// Creates our settings in the options table
		register_setting( 'esr_settings', 'esr_settings', [ 'ESR_Settings', 'esr_settings_sanitize' ] );
	}


	public function esr_get_option( $key = '', $default = false ) {
		global $esr_settings;
		$value = !empty( $esr_settings[ $key ] ) ? $esr_settings[ $key ] : $default;
		$value = apply_filters( 'esr_get_option', $value, $key, $default );

		return apply_filters( 'esr_get_option_' . $key, $value, $key, $default );
	}


	public function esr_remove_option( $key ) {
		global $esr_settings;

		if ( empty( $key ) ) {
			return false;
		}

		$settings = get_option( 'esr_settings' );

		if ( isset( $settings[ $key ] ) ) {
			unset( $settings[ $key ] );
		}

		if ( isset( $esr_settings[ $key ] ) ) {
			unset( $esr_settings[ $key ] );
		}

		$did_update = update_option( 'esr_settings', $settings );

		// If it updated, let's update the global variable
		if ( $did_update ) {
			global $esr_settings;
			$esr_settings = $settings;
		}

		return $did_update;
	}


	public function esr_get_settings() {
		$settings = get_option( 'esr_settings' );

		if ( empty( $settings ) ) {
			update_option( 'esr_settings', [] );
		}

		return apply_filters( 'esr_get_settings', $settings );
	}


	public static function esr_settings_sanitize( $input = [] ) {
		global $esr_settings;

		$input = $input ? $input : [];

		// Merge our new settings with the existing
		$output = array_merge( $esr_settings, $input );

		return $output;
	}


	/**
	 * @return array
	 */
	private function get_default_style_settings() {
		if ( ! class_exists( 'ESR_Styling' ) ) {
			return [
				'main'            => [
					'disable_styles'         => [
						'id'   => 'disable_styles',
						'name' => esc_html__( 'Disable Styles', 'easy-school-registration' ),
						'desc' => esc_html__( 'If enabled all frontend styles will be disabled. Please use only if you know what you are doing.', 'easy-school-registration' ),
						'type' => 'checkbox',
						'std'  => false,
					],
					'style_empty_background' => [
						'id'       => 'style_empty_background',
						'name'     => esc_html__( 'Empty Course Box', 'easy-school-registration' ),
						'desc'     => esc_html__( 'Backgroud box color when the time slot has no course in it.', 'easy-school-registration' ),
						'type'     => 'style_course_box',
						'std'      => [
							'background_color'   => ESR()->settings->esr_get_option( 'style_course_background', '#0a4450' ),
							'background_opacity' => 0,
							'border_width'       => 0,
							'border_color'       => '#000',
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-empty',
						'property' => 'background',
					],
				],
				'day'             => [
					'style_day_background' => [
						'id'       => 'style_day_background',
						'name'     => esc_html__( 'Day Box Style', 'easy-school-registration' ),
						'type'     => 'style_course_box',
						'std'      => [
							'background_color'   => ESR()->settings->esr_get_option( 'style_course_background', '#0a4450' ),
							'background_opacity' => 0,
							'border_width'       => 0,
							'border_color'       => '#000',
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-day',
						'property' => 'background',
					],
					'style_day_title'      => [
						'id'       => 'style_day_title',
						'name'     => esc_html__( 'Day Text Style', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#273a49',
							'size'  => 16,
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-day',
						'property' => 'font',
					],
				],
				'hall'            => [
					'style_hall_background' => [
						'id'       => 'style_hall_background',
						'name'     => esc_html__( 'Box Style', 'easy-school-registration' ),
						'type'     => 'style_course_box',
						'std'      => [
							'background_color'   => ESR()->settings->esr_get_option( 'style_course_background', '#0a4450' ),
							'background_opacity' => 0,
							'border_width'       => 0,
							'border_color'       => '#000',
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-hall',
						'property' => 'background',
					],
					'style_hall_title'      => [
						'id'       => 'style_hall_title',
						'name'     => esc_html__( 'Text Style', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#273a49',
							'size'  => 16,
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-hall',
						'property' => 'font',
					],
				],
				'user_form'       => [
					'style_user_form_selected_tickets_main'       => [
						'id'       => 'style_user_form_selected_tickets_main',
						'name'     => esc_html__( 'Selected Tickets Main Text Style', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#1a1a1a',
							'size'  => 16,
						],
						'selector' => '.esr-choosed-courses .esr-course-row .registration-info, .esr-choosed-courses .esr-course-row .name .main, .esr-choosed-courses .esr-course-row .price',
						'property' => 'font',
					],
					'style_user_form_selected_tickets_additional' => [
						'id'       => 'style_user_form_selected_tickets_additional',
						'name'     => esc_html__( 'Selected Tickets Additional Text Style', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#1a1a1a',
							'size'  => 16,
						],
						'selector' => '.esr-choosed-courses .esr-course-row .name .sub',
						'property' => 'font',
					],
				],
				'normal_course'   => [
					'style_add_course_background'  => [
						'id'       => 'style_add_course_background',
						'name'     => esc_html__( 'Normal Course', 'easy-school-registration' ),
						'type'     => 'style_course_box',
						'std'      => [
							'background_color'   => ESR()->settings->esr_get_option( 'style_course_background', '#0a4450' ),
							'background_opacity' => 1,
							'border_width'       => 0,
							'border_color'       => '#000',
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-add, .esr-schedule-calendar .esr-row .esr-course.esr-schedule-course, .esr-group-filter-button, .esr-level-filter-button',
						'property' => 'background',
					],
					'style_add_course_title'       => [
						'id'       => 'style_add_course_title',
						'name'     => esc_html__( 'Normal Course Title', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#fff',
							'size'  => 16,
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-add .esr-title, .esr-schedule-calendar .esr-row .esr-course.esr-schedule-course .esr-title, .esr-group-filter-button, .esr-level-filter-button',
						'property' => 'font',
					],
					'style_add_course_description' => [
						'id'       => 'style_add_course_description',
						'name'     => esc_html__( 'Normal Course Description', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#c6c5c5',
							'size'  => 14,
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-add, .esr-schedule-calendar .esr-row .esr-course.esr-schedule-course',
						'property' => 'font',
					],
				],
				'selected_course' => [
					'style_selected_course_background'  => [
						'id'       => 'style_selected_course_background',
						'name'     => esc_html__( 'Selected Course', 'easy-school-registration' ),
						'type'     => 'style_course_box',
						'std'      => [
							'background_color'   => ESR()->settings->esr_get_option( 'style_course_selected_background', '#fbc934' ),
							'background_opacity' => 1,
							'border_width'       => 0,
							'border_color'       => '#000',
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-remove',
						'property' => 'background',
					],
					'style_selected_course_title'       => [
						'id'       => 'style_selected_course_title',
						'name'     => esc_html__( 'Selected Course Title', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#000',
							'size'  => 16,
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-remove .esr-title',
						'property' => 'font',
					],
					'style_selected_course_description' => [
						'id'       => 'style_selected_course_description',
						'name'     => esc_html__( 'Selected Course Description', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#454545',
							'size'  => 14,
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-remove',
						'property' => 'font',
					],
				],
				'full_course'     => [
					'style_full_course_background'  => [
						'id'       => 'style_full_course_background',
						'name'     => esc_html__( 'Full Course', 'easy-school-registration' ),
						'type'     => 'style_course_box',
						'desc'     => esc_html__( 'Color of the course which is not selectable for registration due to being full.', 'easy-school-registration' ),
						'std'      => [
							'background_color'   => ESR()->settings->esr_get_option( 'style_course_full_background', '#7a8d91' ),
							'background_opacity' => 1,
							'border_width'       => 0,
							'border_color'       => '#000',
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-full',
						'property' => 'background',
					],
					'style_full_course_title'       => [
						'id'       => 'style_full_course_title',
						'name'     => esc_html__( 'Full Course Title', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#fff',
							'size'  => 16,
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-full .esr-title',
						'property' => 'font',
					],
					'style_full_course_description' => [
						'id'       => 'style_full_course_description',
						'name'     => esc_html__( 'Full Course Description', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#c6c5c5',
							'size'  => 14,
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-full',
						'property' => 'font',
					],
				],
				'disabled_course' => [
					'style_disabled_course_background'  => [
						'id'       => 'style_disabled_course_background',
						'name'     => esc_html__( 'Disabled Course', 'easy-school-registration' ),
						'type'     => 'style_course_box',
						'desc'     => esc_html__( 'Color of the course which is not selectable for registration due to being disabled.', 'easy-school-registration' ),
						'std'      => [
							'background_color'   => ESR()->settings->esr_get_option( 'style_course_disabled_background', '#7a8d91' ),
							'background_opacity' => 1,
							'border_width'       => 0,
							'border_color'       => '#000',
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-disabled-registration',
						'property' => 'background',
					],
					'style_disabled_course_title'       => [
						'id'       => 'style_disabled_course_title',
						'name'     => esc_html__( 'Disabled Course Title', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#fff',
							'size'  => 16,
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-disabled-registration .esr-title',
						'property' => 'font',
					],
					'style_disabled_course_description' => [
						'id'       => 'style_disabled_course_description',
						'name'     => esc_html__( 'Disabled Course Description', 'easy-school-registration' ),
						'type'     => 'style_course_font',
						'std'      => [
							'color' => '#c6c5c5',
							'size'  => 14,
						],
						'selector' => '.esr-schedule-calendar .esr-row .esr-course.esr-disabled-registration',
						'property' => 'font',
					],
				],
			];
		}

		return [];
	}
}

add_action( 'admin_init', [ 'ESR_Settings', 'esr_register_settings' ] );