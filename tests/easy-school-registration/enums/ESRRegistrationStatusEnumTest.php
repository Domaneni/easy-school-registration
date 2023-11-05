<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

/**
 * Sample test case.
 */
class ESRRegistrationStatusEnumTest extends PHPUnit_Framework_TestCase  {

	public function __construct() {
		parent::__construct();
	}


	public function test_get_title() {
		$this->assertEquals('Waiting', ESR()->registration_status->get_title(ESR_Registration_Status::WAITING));
		$this->assertEquals('Confirmed', ESR()->registration_status->get_title(ESR_Registration_Status::CONFIRMED));
		$this->assertEquals('Canceled', ESR()->registration_status->get_title(ESR_Registration_Status::DELETED));
	}

}
