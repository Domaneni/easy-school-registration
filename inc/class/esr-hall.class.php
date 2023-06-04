<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Hall {


	/**
	 * @return array
	 */
	public function get_hall_names() {
		$result = ESR()->settings->esr_get_option('halls', []);
		$halls = [];

		foreach ($result as $key => $hall) {
			if (is_array($hall)) {
				$halls[$key] = $hall['name'];
			} else {
				$halls[$key] = $hall;
			}
		}

		return $halls;
	}


	/**
	 * @return array
	 */
	public function get_halls() {
		return ESR()->settings->esr_get_option('halls', []);
	}


	public function get_hall($key) {
		$halls = ESR()->settings->esr_get_option('halls', []);

		return isset($halls[$key]) ? $halls[$key] : [];
	}


	public function get_hall_name($key) {
		$halls = self::get_hall_names();

		return isset($halls[$key]) ? $halls[$key] : '';
	}

}

