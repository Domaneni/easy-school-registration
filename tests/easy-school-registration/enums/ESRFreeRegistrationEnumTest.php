<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

/**
 * Sample test case.
 */
class ESRFreeRegistrationEnumTest extends PHPUnit_Framework_TestCase {

	public function __construct() {
		parent::__construct();
	}


	public function test_get_change_message() {
		$enum = new ESR_Enum_Free_Registration();
		$this->assertEquals('Registration set to paid', $enum->esr_get_change_message(ESR_Enum_Free_Registration::PAID));
		$this->assertEquals('Registration set to free', $enum->esr_get_change_message(ESR_Enum_Free_Registration::FREE));
		$this->assertEquals('', $enum->esr_get_change_message(2));
	}

}
