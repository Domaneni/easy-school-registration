<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

/**
 * Sample test case.
 */
class ESR_Day_Enum_Test extends PHPUnit_Framework_TestCase {

	public function __construct() {
		parent::__construct();
	}


	public function test_get_day_title() {
		$enum_day = new ESR_Enum_Day();
		$this->assertEquals('Monday', $enum_day->get_day_title(ESR_Enum_Day::MONDAY));
		$this->assertEquals('Tuesday', $enum_day->get_day_title(ESR_Enum_Day::TUESDAY));
		$this->assertEquals('Wednesday', $enum_day->get_day_title(ESR_Enum_Day::WEDNESDAY));
		$this->assertEquals('Thursday', $enum_day->get_day_title(ESR_Enum_Day::THURSDAY));
		$this->assertEquals('Friday', $enum_day->get_day_title(ESR_Enum_Day::FRIDAY));
		$this->assertEquals('Saturday', $enum_day->get_day_title(ESR_Enum_Day::SATURDAY));
		$this->assertEquals('Sunday', $enum_day->get_day_title(ESR_Enum_Day::SUNDAY));
		$this->assertEquals('', $enum_day->get_day_title(null));
	}

}
