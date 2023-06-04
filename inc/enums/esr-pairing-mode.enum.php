<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Enum_Pairing_Mode {
	const
		AUTOMATIC = 1, MANUAL = 2, CONFIRM_ALL = 3;

	private $items = [];


	/**
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		$this->items = [
			self::AUTOMATIC => [
				'title' => esc_html__('Automatic', 'easy-school-registration'),
				'tooltip' => esc_html__('Students will be automatically paired until the course is full.', 'easy-school-registration'),
			],
			self::MANUAL => [
				'title' => esc_html__('Manual', 'easy-school-registration'),
				'tooltip' => esc_html__('Students will be on a Waiting List until they are manually confirmed. Pairing is disabled.', 'easy-school-registration'),
			],
			self::CONFIRM_ALL => [
				'title' => esc_html__('Confirm All', 'easy-school-registration'),
				'tooltip' => esc_html__('Students will be automatically confirmed immediately after registration until the course is full. Pairing is disabled.', 'easy-school-registration'),
			],
		];
	}


	public function get_items() {
		return $this->items;
	}


	public function get_items_for_settings() {
		$return_items = [];
		foreach ($this->get_items() as $key => $item) {
			$return_items[$key] = $item['title'];
		}

		return $return_items;
	}


	public function get_item($key) {
		return $this->get_items()[$key];
	}


	public function get_title($key) {
		return $this->get_item($key)['title'];
	}

	public function is_pairing_enabled($key) {
		return  self::AUTOMATIC === intval($key);
	}

	public function is_auto_confirmation_enabled($key) {
		return self::CONFIRM_ALL === intval($key);
	}

	public function get_solo_course_default_status($key) {
		return self::MANUAL === intval($key) ? ESR_Registration_Status::WAITING : ESR_Registration_Status::CONFIRMED;
	}

	public function is_solo_manual($key) {
		return self::MANUAL === intval($key);
	}
}