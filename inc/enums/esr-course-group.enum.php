<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Enum_Course_Group extends ESR_Enum_Db {

	const KEY = 'groups';


	public function __construct() {
		$this->setKey(self::KEY);
	}


	/**
	 * Load groups used in selected wave
	 *
	 * @param $wave_id
	 *
	 * @return array
	 */
	public function get_groups_by_wave($wave_id) {
		global $wpdb;

		return $wpdb->get_results($wpdb->prepare("SELECT DISTINCT group_id FROM {$wpdb->prefix}esr_course_data WHERE wave_id = %d ORDER BY group_id", [intval($wave_id)]), ARRAY_A);
	}


	public function get_items_for_tinymce() {
		$return_items = [];
		array_push($return_items, [
			'text'  => esc_html__('Choose an option', 'easy-school-registration'),
			'value' => ''
		]);
		foreach ($this->get_items() as $key => $title) {
			array_push($return_items, [
				'text'  => $title,
				'value' => $key
			]);
		}

		return $return_items;
	}


	public function get_items_for_gutenberg() {
		$return_items = [];
		foreach ($this->get_items() as $key => $title) {
			array_push($return_items, [
				'title'  => $title,
				'id' => $key
			]);
		}

		return $return_items;
	}
}