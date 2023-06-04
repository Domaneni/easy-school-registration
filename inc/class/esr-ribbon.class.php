<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Ribbon {

	/**
	 * ESR_Ribbon constructor.
	 */
	public function __construct() {
		if (intval(ESR()->settings->esr_get_option('enable_full_course_ribbon', -1)) === 1) {
			add_action('esr_registration_schedule_print', ['ESR_Ribbon', 'esr_print_full_ribbon_callback'], 10, 3);
		}
		if (intval(ESR()->settings->esr_get_option('enable_disabled_course_ribbon', -1)) === 1) {
			add_action('esr_registration_schedule_print', ['ESR_Ribbon', 'esr_print_disabled_ribbon_callback'], 10, 3);
		}
	}


	public static function esr_print_full_ribbon_callback($course, $course_enabled, $for_registration) {
		if (!$course_enabled && $for_registration) {
			$ribbon_text = ESR()->settings->esr_get_option('full_ribbon_text', esc_html__('Course is Full', 'easy-school-registration'));
			?>
			<span class="esr-ribbon esr-ribbon-full"><?php esc_html_e($ribbon_text, 'easy-school-registration'); ?></span>
			<?php
		}
	}


	public static function esr_print_disabled_ribbon_callback($course, $course_enabled, $for_registration) {
		if (isset($course->disable_registration) && $course->disable_registration && $for_registration) {
			$ribbon_text = ESR()->settings->esr_get_option('disabled_ribbon_text', esc_html__('Course is Disabled', 'easy-school-registration'));
			?>
			<span class="esr-ribbon esr-ribbon-full"><?php esc_html_e($ribbon_text, 'easy-school-registration'); ?></span>
			<?php
		}
	}

}