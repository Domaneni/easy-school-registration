<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Fields {

	private $fields;


	public function add_field($key, $type, $required) {
		if (!isset($this->fields[$key])) {
			$this->fields[$key] = [
				'type'     => $type,
				'required' => $required,
			];
		}
	}


	/**
	 * @return object
	 */
	public function get_fields() {
		return (object) $this->fields;
	}

}