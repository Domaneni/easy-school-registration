<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESR_Payment_Worker_Test extends PHPUnit_Framework_TestCase {

	private $base_test;


	public function __construct() {
		parent::__construct();
		$this->base_test   = new ESR_Base_Test();
	}


	public function setUp() {
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	/**
	 * Test adding teacher
	 */
	public function test_forgive_payment_callback() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->base_test->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(ESR_Enum_Payment::NOT_PAID, $payment->status);

		$this->assertEquals([
			'status' => ESR_Enum_Payment::FORGIVEN,
			'status_title' => ESR()->payment_status->get_title(ESR_Enum_Payment::FORGIVEN)
		], apply_filters('esr_forgive_payment', $payment->id));

		$payment = $this->base_test->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(ESR_Enum_Payment::FORGIVEN, $payment->status);

		$this->assertEquals(-1, apply_filters('esr_forgive_payment', -1));
	}


}
