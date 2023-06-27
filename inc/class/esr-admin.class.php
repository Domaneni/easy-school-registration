<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Admin {

	const ADMIN_MENU_SLUG = 'esr_admin', USER_MENU_SLUG = 'esr_user_info';

	public static function add_admin_menu() {
		add_menu_page(esc_html__('School', 'easy-school-registration'), esc_html__('School', 'easy-school-registration'), 'esr_school', self::ADMIN_MENU_SLUG, ['ESR_Templater_School', 'print_page'], 'dashicons-welcome-learn-more', 2);

		add_submenu_page(self::ADMIN_MENU_SLUG, esc_html__('Teachers', 'easy-school-registration'), esc_html__('Teachers', 'easy-school-registration'), 'esr_teacher_view', ESR_Templater_Teachers::MENU_SLUG, ['ESR_Templater_Teachers', 'print_content']);
		add_submenu_page(self::ADMIN_MENU_SLUG, esc_html__('Waves', 'easy-school-registration'), esc_html__('Waves', 'easy-school-registration'), 'esr_wave_view', ESR_Templater_Waves::MENU_SLUG, ['ESR_Templater_Waves', 'print_content']);
		add_submenu_page(self::ADMIN_MENU_SLUG, esc_html__('Courses', 'easy-school-registration'), esc_html__('Courses', 'easy-school-registration'), 'esr_course_view', ESR_Templater_Courses::MENU_SLUG, ['ESR_Templater_Courses', 'print_content']);

		add_submenu_page(self::ADMIN_MENU_SLUG, esc_html__('Registration', 'easy-school-registration'), esc_html__('Registration', 'easy-school-registration'), 'esr_registration_view', ESR_Template_Registrations::MENU_SLUG, ['ESR_Template_Registrations', 'print_page']);
		add_submenu_page(self::ADMIN_MENU_SLUG, esc_html__('Course in numbers', 'easy-school-registration'), esc_html__('Course in numbers', 'easy-school-registration'), 'esr_course_in_number_view', ESR_Template_Courses_In_Numbers::MENU_SLUG, ['ESR_Template_Courses_In_Numbers', 'print_page']);
		add_submenu_page(self::ADMIN_MENU_SLUG, esc_html__('Add over limit', 'easy-school-registration'), esc_html__('Add over limit', 'easy-school-registration'), 'esr_add_over_limit', ESR_Template_Add_Over_Limit::MENU_SLUG, ['ESR_Template_Add_Over_Limit', 'print_page']);

		add_submenu_page(self::ADMIN_MENU_SLUG, esc_html__('Payments', 'easy-school-registration'), esc_html__('Payments', 'easy-school-registration'), 'esr_payment_view', ESR_Payments_Templater::MENU_SLUG, ['ESR_Payments_Templater', 'print_page']);
		add_submenu_page(self::ADMIN_MENU_SLUG, esc_html__('Payment emails', 'easy-school-registration'), esc_html__('Payment emails', 'easy-school-registration'), 'esr_payment_emails', ESR_Template_Payment_Emails::MENU_SLUG, ['ESR_Template_Payment_Emails', 'print_page']);

		add_submenu_page(self::ADMIN_MENU_SLUG, esc_html__('Settings', 'easy-school-registration'), esc_html__('Settings', 'easy-school-registration'), 'esr_settings', ESR_Settings_Templater::MENU_SLUG, ['ESR_Settings_Templater', 'print_page']);
        add_submenu_page(self::ADMIN_MENU_SLUG, esc_html__('Addons', 'easy-school-registration'), esc_html__('Addons', 'easy-school-registration'), 'esr_school', ESR_Addons_Template::MENU_SLUG, ['ESR_Addons_Template', 'print_page']);
		add_submenu_page(self::ADMIN_MENU_SLUG, esc_html__('Students', 'easy-school-registration'), esc_html__('Students', 'easy-school-registration'), 'esr_students_view', ESR_Template_Students::MENU_SLUG, ['ESR_Template_Students', 'print_page']);

		do_action('esr_add_admin_menu');
	}

	public static function add_user_menu() {
		add_menu_page(esc_html__('Student info', 'easy-school-registration'), esc_html__('Student info', 'easy-school-registration'), 'esr_student', ESR_User_Info_Courses_Templater::MENU_SLUG, ['ESR_User_Info_Courses_Templater', 'print_page'], 'dashicons-welcome-learn-more', 6);
		add_submenu_page(ESR_User_Info_Courses_Templater::MENU_SLUG, esc_html__('Payments', 'easy-school-registration'), esc_html__('Payments', 'easy-school-registration'), 'esr_student', ESR_User_Info_Payments_Templater::MENU_SLUG, ['ESR_User_Info_Payments_Templater', 'print_page']);

		do_action('esr_add_student_menu');
	}

	public static function esr_add_teacher_menu() {
		if (current_user_can('esr_teacher_info') && ESR()->teacher->esr_is_user_teacher(get_current_user_id())) {
			add_menu_page(esc_html__('Teacher info', 'easy-school-registration'), esc_html__('Teacher info', 'easy-school-registration'), 'esr_teacher_info', ESR_Teacher_Info_Template::MENU_SLUG, ['ESR_Teacher_Info_Template', 'print_page'], 'dashicons-welcome-learn-more', 6);

			do_action('esr_add_teacher_menu');
		}
	}
}

add_action('admin_menu', ['ESR_Admin', 'add_admin_menu'], 10);
add_action('admin_menu', ['ESR_Admin', 'add_user_menu'], 10);
add_action('admin_menu', ['ESR_Admin', 'esr_add_teacher_menu'], 11);