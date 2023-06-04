<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESR_Payment_Reminder_Email_Cron_Test extends PHPUnit_Framework_TestCase {

	private $base_test;


	public function __construct() {
		parent::__construct();
		$this->base_test = new ESR_Base_Test();
	}


	public function setUp() {
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	public function test_can_send_payment_reminder_emails() {
		$this->assertEquals(true, apply_filters('esr_can_send_payment_emails', (object) [
			'courses_from' => date('Y-m-d H:i:s', strtotime("+1 day")),
			'courses_to'   => date('Y-m-d H:i:s', strtotime("+20 days"))
		], -1, date('Y-m-d H:i:s')));

		$this->assertEquals(true, apply_filters('esr_can_send_payment_emails', (object) [
			'courses_from' => date('Y-m-d H:i:s'),
			'courses_to'   => date('Y-m-d H:i:s', strtotime("+20 days"))
		], -1, date('Y-m-d H:i:s')));

		$this->assertEquals(true, apply_filters('esr_can_send_payment_emails', (object) [
			'courses_from' => date('Y-m-d H:i:s', strtotime("-1 day")),
			'courses_to'   => date('Y-m-d H:i:s', strtotime("+20 days"))
		], -1, date('Y-m-d H:i:s')));
	}

}
