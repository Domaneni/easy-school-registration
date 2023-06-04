<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

function esr_get_registration_form($settings) {
	if (isset($settings['waves'])) {
		do_action('esr_load_external_scripts');
		$template_registration = new ESR_Registration_Templater();
		$template_registration->print_courses_registration($settings);
	}
}


function esr_get_schedule($settings) {
	if (isset($settings['waves'])) {
		do_action('esr_load_external_scripts');
		$template_schedule = new ESR_Schedule_Templater();
		$waves              = is_array($settings['waves']) ? $settings['waves'] : explode(',', $settings['waves']);
		$template_schedule->print_content($waves, $settings, false);
	}
}