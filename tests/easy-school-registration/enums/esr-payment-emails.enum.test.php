<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

/**
 * Sample test case.
 */
class ESR_Payment_Emails_Enum_Test extends PHPUnit_Framework_TestCase {

	public function __construct() {
		parent::__construct();
	}


	public function test_get_status() {
		$enum = new ESR_Enum_Payment_Emails();
		$this->assertEquals($enum->get_title(ESR_Enum_Payment_Emails::NOT_SEND), $enum->esr_get_status(null, '2019-08-05'));
		$this->assertEquals($enum->get_title(ESR_Enum_Payment_Emails::ALREADY_SENT), $enum->esr_get_status('2019-08-05', '2019-08-05'));
		$this->assertEquals($enum->get_title(ESR_Enum_Payment_Emails::ALREADY_SENT), $enum->esr_get_status('2019-08-10', '2019-08-05'));
		$this->assertEquals($enum->get_title(ESR_Enum_Payment_Emails::ALREADY_SENT), $enum->esr_get_status('2019-08-05', null));
		$this->assertEquals($enum->get_title(ESR_Enum_Payment_Emails::CHANGE_FROM_LAST), $enum->esr_get_status('2019-08-05', '2019-08-10'));
	}

}
