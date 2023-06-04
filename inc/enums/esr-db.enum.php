<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Enum_Db {

	private $key;

	public function get_items() {
		$items = ESR()->settings->esr_get_option($this->key, []);
		return $items;
	}


	public function get_item($id) {
		return $this->get_items()[$id];
	}

	/**
	 * @param mixed $key
	 */
	public function setKey($key) {
		$this->key = $key;
	}


	public function get_title($id) {
		return ($id !== null) && ($id !== '') ? $this->get_item($id) : '';
	}


}