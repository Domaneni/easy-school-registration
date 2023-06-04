<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Enum_Payment_Emails extends ESR_Enum {
	const
		NOT_SEND = 0, ALREADY_SENT = 1, CHANGE_FROM_LAST = 2;


	/**
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		$this->esr_set_items($this->esr_set_up_items());
	}


	private function esr_set_up_items() {
		return apply_filters('esr_set_up_payment_emails_items', [
			self::NOT_SEND         => [
				'title' => esc_html__('Not Sent', 'easy-school-registration'),
			],
			self::ALREADY_SENT     => [
				'title' => esc_html__('Already Sent', 'easy-school-registration'),
			],
			self::CHANGE_FROM_LAST => [
				'title' => esc_html__('Registration Changes', 'easy-school-registration'),
			]
		]);
	}


	public function esr_get_status($last_sent, $last_change) {
		if (!$last_sent) {
			return $this->get_item(self::NOT_SEND)['title'];
		} else if ($last_sent >= $last_change) {
			return $this->get_item(self::ALREADY_SENT)['title'];
		} else if ($last_sent < $last_change) {
			return $this->get_item(self::CHANGE_FROM_LAST)['title'];
		}

		return '';
	}

}