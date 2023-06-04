<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Enum_Hover_Option extends ESR_Enum {
	const
		REGISTRATIONS = 'registrations', PLACES_LEFT = 'places_left';

	/**
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		$this->esr_set_items($this->esr_set_up_items());
	}


	private function esr_set_up_items() {
		return apply_filters('esr_set_up_hover_option_items', [
			self::REGISTRATIONS => [
				'title' => esc_html__('Number of registrations', 'easy-school-registration'),
			],
			self::PLACES_LEFT => [
				'title' => esc_html__('Places left', 'easy-school-registration'),
			]
		]);
	}

}
