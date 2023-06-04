<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Enum_Schedule_Style extends ESR_Enum {
	const
		BY_HOURS = 'by_hours', BY_DAYS = 'by_days', BY_HOURS_COMPACT = 'by_hours_compact';

	private $items = [];


	public function __construct() {
		$this->esr_set_items($this->esr_set_up_items());
	}


	private function esr_set_up_items() {
		return apply_filters('esr_set_up_schedule_style_items', [
			self::BY_HOURS => [
				'title' => esc_html__('By Hours', 'easy-school-registration'),
			],
			self::BY_DAYS => [
				'title' => esc_html__('By Days', 'easy-school-registration'),
			],
			self::BY_HOURS_COMPACT => [
				'title' => esc_html__('By Hours Compact', 'easy-school-registration'),
			]
		]);
	}

	public function get_items_for_settings() {
		$return_items = [];
		foreach ($this->get_items() as $key => $item) {
			$return_items[$key] = $item['title'];
		}

		return $return_items;
	}

	public function get_items_for_tinymce() {
		$return_items = [];
		foreach ($this->get_items() as $key => $item) {
			array_push($return_items, [
				'text' => $item['title'],
				'value' => $key
			]);
		}

		return $return_items;
	}

}