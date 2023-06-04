<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESR_Email_Worker_Test extends PHPUnit_Framework_TestCase {

	private $base_test;

	private $worker_wave;


	public function __construct() {
		parent::__construct();
		$this->base_test   = new ESR_Base_Test();
		$this->worker_wave = new ESR_Wave_Worker();
	}


	public function setUp() {
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	/**
	 * Test adding teacher
	 */
	public function test_mail_callback_callback() {
		$this->assertEquals('mail', apply_filters('esr_mail_callback', 'mail'));

		$this->base_test->update_settings(['use_wp_mail' => true]);

		$this->assertEquals('wp_mail', apply_filters('esr_mail_callback', 'mail'));

		$this->base_test->update_settings(['use_wp_mail' => false]);

		$this->assertEquals('mail', apply_filters('esr_mail_callback', 'mail'));
	}


}
