<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

/**
 * Sample test case.
 */
class ESRPaymentEnumTest extends PHPUnit_Framework_TestCase {


	public function __construct() {
		parent::__construct();
	}


	public function test_get_title() {
		$enum_payment = new ESR_Enum_Payment();
		$this->assertEquals('Not Paid', $enum_payment->get_title(ESR_Enum_Payment::NOT_PAID));
		$this->assertEquals('Paid', $enum_payment->get_title(ESR_Enum_Payment::PAID));
		$this->assertEquals('Overpaid', $enum_payment->get_title(ESR_Enum_Payment::OVER_PAID));
		$this->assertEquals('Not Paying', $enum_payment->get_title(ESR_Enum_Payment::NOT_PAYING));
		$this->assertEquals('Voucher', $enum_payment->get_title(ESR_Enum_Payment::VOUCHER));
		$this->assertEquals('Partially Paid', $enum_payment->get_title(ESR_Enum_Payment::NOT_PAID_ALL));
	}


	public function test_get_status() {
		$enum_payment = new ESR_Enum_Payment();
		$this->assertEquals(ESR_Enum_Payment::PAID, $enum_payment->get_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => 800,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::NOT_PAID_ALL, $enum_payment->get_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => 400,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::NOT_PAID, $enum_payment->get_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => null,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::OVER_PAID, $enum_payment->get_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => 900,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::VOUCHER, $enum_payment->get_status([
			'is_paying'  => true,
			'is_voucher' => true,
			'payment'    => 0,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::NOT_PAYING, $enum_payment->get_status([
			'is_paying'  => false,
			'is_voucher' => false,
			'payment'    => 0,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::FORGIVEN, $enum_payment->get_status((object) [
			'is_paying'  => false,
			'is_voucher' => false,
			'payment'    => 0,
			'to_pay'     => 800,
			'status'     => ESR_Enum_Payment::FORGIVEN
		]));
	}


	public function test_get_student_status() {
		$this->assertEquals(ESR_Enum_Payment::PAID, ESR()->payment_status->get_student_status([
			'is_paying'  => false,
			'is_voucher' => false,
			'payment'    => 0,
			'to_pay'     => 800,
			'status'     => ESR_Enum_Payment::FORGIVEN
		]));

		$this->assertEquals(ESR_Enum_Payment::PAID, ESR()->payment_status->get_student_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => 800,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::NOT_PAID_ALL, ESR()->payment_status->get_student_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => 400,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::NOT_PAID, ESR()->payment_status->get_student_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => null,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::OVER_PAID, ESR()->payment_status->get_student_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => 900,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::VOUCHER, ESR()->payment_status->get_student_status([
			'is_paying'  => true,
			'is_voucher' => true,
			'payment'    => 0,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::NOT_PAYING, ESR()->payment_status->get_student_status([
			'is_paying'  => false,
			'is_voucher' => false,
			'payment'    => 0,
			'to_pay'     => 800
		]));
	}


	public function test_get_admin_status() {
		$this->assertEquals(ESR_Enum_Payment::FORGIVEN, ESR()->payment_status->get_admin_status([
			'is_paying'  => false,
			'is_voucher' => false,
			'payment'    => 0,
			'to_pay'     => 800,
			'status'     => ESR_Enum_Payment::FORGIVEN
		]));

		$this->assertEquals(ESR_Enum_Payment::PAID, ESR()->payment_status->get_student_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => 800,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::NOT_PAID_ALL, ESR()->payment_status->get_admin_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => 400,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::NOT_PAID, ESR()->payment_status->get_admin_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => null,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::OVER_PAID, ESR()->payment_status->get_admin_status([
			'is_paying'  => true,
			'is_voucher' => false,
			'payment'    => 900,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::VOUCHER, ESR()->payment_status->get_admin_status([
			'is_paying'  => true,
			'is_voucher' => true,
			'payment'    => 0,
			'to_pay'     => 800
		]));

		$this->assertEquals(ESR_Enum_Payment::NOT_PAYING, ESR()->payment_status->get_admin_status([
			'is_paying'  => false,
			'is_voucher' => false,
			'payment'    => 0,
			'to_pay'     => 800
		]));
	}
}
