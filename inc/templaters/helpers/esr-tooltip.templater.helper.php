<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Tooltip_Templater_Helper {

	public static function print_tooltip($text) {
		?><span class="esr-question" data-tippy-interactive="true" title="<?php echo esc_attr($text); ?>"><span class="dashicons dashicons-editor-help"></span></span><?php
	}
}