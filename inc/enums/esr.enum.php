<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Enum {

	private $items = [];

	public function get_items() {
		return $this->items;
	}


	public function esr_set_items($items) {
		$this->items = $items;
	}


	public function get_item($key) {
		$items = $this->get_items();

		if (isset($items[$key])) {
			return $items[$key];
		}

		return false;
	}


	public function get_title($key) {
		$item = $this->get_item($key);

		if ($item) {
			return $item['title'];
		}

		return '';
	}


	public function get_items_for_gutenberg() {
		$return_items = [];
		foreach ($this->get_items() as $key => $item) {
			array_push($return_items, [
				'title'  => $item['title'],
				'id' => $key
			]);
		}

		return $return_items;
	}


	public function esr_get_key_title_items() {
		$return_items = [];
		foreach ($this->get_items() as $key => $item) {
			$return_items[$key] = $item['title'];
		}

		return $return_items;
	}

}