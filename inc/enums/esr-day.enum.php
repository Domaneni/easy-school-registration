<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Enum_Day
{
	const
		MONDAY = 1, TUESDAY = 2, WEDNESDAY = 3, THURSDAY = 4, FRIDAY = 5, SATURDAY = 6, SUNDAY = 7;

	private $items = [];


	/**
	 * @codeCoverageIgnore
	 */
	public function __construct()
	{
		$this->items = [
			self::MONDAY => [
				'title' => esc_html__('Monday', 'easy-school-registration'),
				'short_title' => esc_html__('Mon', 'easy-school-registration'),
			],
			self::TUESDAY => [
				'title' => esc_html__('Tuesday', 'easy-school-registration'),
				'short_title' => esc_html__('Tue', 'easy-school-registration'),
			],
			self::WEDNESDAY => [
				'title' => esc_html__('Wednesday', 'easy-school-registration'),
				'short_title' => esc_html__('Wed', 'easy-school-registration'),
			],
			self::THURSDAY => [
				'title' => esc_html__('Thursday', 'easy-school-registration'),
				'short_title' => esc_html__('Thu', 'easy-school-registration'),
			],
			self::FRIDAY => [
				'title' => esc_html__('Friday', 'easy-school-registration'),
				'short_title' => esc_html__('Fri', 'easy-school-registration'),
			],
			self::SATURDAY => [
				'title' => esc_html__('Saturday', 'easy-school-registration'),
				'short_title' => esc_html__('Sat', 'easy-school-registration'),
			],
			self::SUNDAY => [
				'title' => esc_html__('Sunday', 'easy-school-registration'),
				'short_title' => esc_html__('Sun', 'easy-school-registration'),
			],
		];
	}


	public function get_items()
	{
		return $this->items;
	}


	public function get_item($key)
	{
		return isset($this->get_items()[$key]) ? $this->get_items()[$key] : NULL;
	}


	public function get_day_title($key, $use_short = false)
	{
		$item = $this->get_item($key);
		if ($item && isset($item['title'])) {
			return $use_short ? $item['short_title'] : $item['title'];
		}
		return '';
	}
}