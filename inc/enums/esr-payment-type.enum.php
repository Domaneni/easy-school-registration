<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Enum_Payment_Type extends ESR_Enum {
	const
		CASH = 0, BANK = 1;

	/**
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		$this->esr_set_items($this->esr_set_up_items());
	}


	private function esr_set_up_items() {
		return apply_filters('esr_set_up_payment_type_items', [
			self::CASH => [
				'key'   => 'cash',
				'title' => esc_html__('Cash', 'easy-school-registration'),
			],
			self::BANK => [
				'key'   => 'bank',
				'title' => esc_html__('Bank Transfer', 'easy-school-registration'),
			]
		]);
	}


	public static function esr_add_setting_payment_types_callback($types) {
		$setting_types = ESR()->settings->esr_get_option('payment_types', []);
		if (!empty($setting_types)) {
			foreach ($setting_types as $key => $type) {
				if (!in_array($key, [ESR_Enum_Payment_Type::CASH, ESR_Enum_Payment_Type::BANK])) {
					$types[$key] = [
						'key'   => 'spt_' . $key,
						'title' => $type
					];
				}
			}
		}

		return $types;
	}

}

add_filter('esr_set_up_payment_type_items', ['ESR_Enum_Payment_Type', 'esr_add_setting_payment_types_callback']);