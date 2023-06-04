<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Enum_Payment {
	const
		NOT_PAID = 0, PAID = 1, OVER_PAID = 2, NOT_PAYING = 3, VOUCHER = 4, NOT_PAID_ALL = 5, FORGIVEN = 6;

	private $items = [];


	/**
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		$this->items = [
			self::NOT_PAID     => [
				'key'   => 'not_paid',
				'title' => esc_html__('Not Paid', 'easy-school-registration'),
			],
			self::PAID         => [
				'key'   => 'paid',
				'title' => esc_html__('Paid', 'easy-school-registration'),
			],
			self::OVER_PAID    => [
				'key'   => 'over_paid',
				'title' => esc_html__('Overpaid', 'easy-school-registration'),
			],
			self::NOT_PAYING   => [
				'key'   => 'not_paying',
				'title' => esc_html__('Not Paying', 'easy-school-registration'),
			],
			self::VOUCHER      => [
				'key'   => 'voucher',
				'title' => esc_html__('Voucher', 'easy-school-registration'),
			],
			self::NOT_PAID_ALL => [
				'key'   => 'not_paid_all',
				'title' => esc_html__('Partially Paid', 'easy-school-registration'),
			],
			self::FORGIVEN     => [
				'key'   => 'forgiven',
				'title' => esc_html__('Forgiven', 'easy-school-registration'),
			],
		];
	}


	public function get_items() {
		return $this->items;
	}


	public function getItem($key) {
		return $this->get_items()[$key];
	}


	public function get_title($key) {
		return $this->getItem($key)['title'];
	}


	public function get_status($user_payment) {
		if (is_object($user_payment)) {
			$user_payment = (array) $user_payment;
		}

		if (isset($user_payment['status']) && (intval($user_payment['status']) === self::FORGIVEN)) {
			return self::FORGIVEN;
		}

		if ($user_payment !== null) {
			if (!((boolean) $user_payment['is_paying'])) {
				return self::NOT_PAYING;
			}
			if ((boolean) $user_payment['is_voucher']) {
				return self::VOUCHER;
			}
			if ($user_payment['payment'] !== null) {
				if (floatval($user_payment['to_pay']) == $user_payment['payment']) {
					return self::PAID;
				} else if (floatval($user_payment['to_pay']) > $user_payment['payment']) {
					return self::NOT_PAID_ALL;
				} else {
					return self::OVER_PAID;
				}
			}
		}

		return self::NOT_PAID;
	}


	public function get_student_status($user_payment) {
		if (isset($user_payment['status']) && (intval($user_payment['status']) === self::FORGIVEN)) {
			return self::PAID;
		} else {
			return $this->get_status($user_payment);
		}
	}


	public function get_admin_status($user_payment) {
		if (isset($user_payment['status']) && (intval($user_payment['status']) === self::FORGIVEN)) {
			return self::FORGIVEN;
		} else {
			return $this->get_status($user_payment);
		}
	}
}