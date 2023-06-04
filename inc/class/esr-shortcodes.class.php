<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Shortcodes {

	// [esr_course_registration]
	public static function esr_registration_form_shortcode($attr) {
		do_action('esr_load_external_scripts');
		ob_start();
		if (isset($attr['waves'])) {
			$templater_registration = new ESR_Registration_Templater();
			$templater_registration->print_courses_registration($attr);
		}

		return ob_get_clean();
	}


	// [esr_wave_schedule]
	public static function esr_wave_schedule_shortcode($attr, $content = null) {
		do_action('esr_load_external_scripts');
		ob_start();
		if (isset($attr['waves'])) {
			$templater_schedule = new ESR_Schedule_Templater();
			$templater_schedule->print_content(explode(',', $attr['waves']), $attr, false);
		}

		return ob_get_clean();
	}

}

add_shortcode('esr_course_registration', ['ESR_Shortcodes', 'esr_registration_form_shortcode']);
add_shortcode('esr_wave_schedule', ['ESR_Shortcodes', 'esr_wave_schedule_shortcode']);