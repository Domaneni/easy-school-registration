<?php

function esr_add_tinymce_plugin($registered_plugins) {
	$registered_plugins['esr_mce_button'] = ESR_PLUGIN_URL . 'inc/assets/admin/js/esr-mce-plugin.js?version=' . ESR_VERSION;

	return $registered_plugins;
}

add_filter('mce_external_plugins', 'esr_add_tinymce_plugin');


function esr_register_tinymce_button($registered_buttons) {
	array_push($registered_buttons, "esr_mce_button");
	array_push($registered_buttons, "esr_mce_button_preset1");

	return $registered_buttons;
}

add_filter('mce_buttons', 'esr_register_tinymce_button');


function esr_add_mce_scripts() {
	wp_enqueue_style('esr_mce_button_style', ESR_PLUGIN_URL . 'inc/assets/admin/css/esr-tinymce.min.css', [], ESR_VERSION);
	wp_localize_script('esr_tinymce_script', 'esr_tinymce_ajax_object', ['ajaxurl' => admin_url('admin-ajax.php')]);
}

add_action('admin_enqueue_scripts', 'esr_add_mce_scripts');
