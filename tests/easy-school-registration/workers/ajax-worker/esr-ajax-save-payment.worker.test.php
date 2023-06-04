<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESR_Ajax_Save_Payment_Worker_Test extends PHPUnit_Framework_TestCase {

	private $base_test;

	private $worker_ajax;

	private $worker_registration;


	public function __construct() {
		parent::__construct();
		$this->base_test           = new ESR_Base_Test();
		$this->worker_ajax         = new ESR_Ajax_Worker();
	}


	public function setUp() {
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	public function test_process_add_user_course_registration_user_not_exists() {
		$user_email  = 'unittest@easyschoolregistration.com';

		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, $user_email);

		$this->assertEquals([
			'error' => [
				'student' => 'Student with this email do not exist'
			]
		], $this->worker_ajax->save_payment('unittest2@easyschoolregistration.com', ESR_Enum_Payment::PAID, []));
	}


	/*public function test_process_add_user_course_registration_user() {
		$user_email  = 'unittest@easyschoolregistration.com';

		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, $user_email);

		$payment = $this->base_test->load_user_payment_by_email($user_email);


		$this->assertEquals([
			'error' => [
				'student' => 'Student with this email do not exist'
			]
		], $this->worker_ajax->save_payment('unittest2@easyschoolregistration.com', ESR_Enum_Payment::PAID, []));
	}*/


}
