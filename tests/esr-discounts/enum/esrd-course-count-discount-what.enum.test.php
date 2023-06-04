<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-discounts/esrd-base.php';

/**
 * Sample test case.
 */
class ESRD_Course_Count_Discount_What_Enum_Test extends PHPUnit_Framework_TestCase {

	public function __construct() {
		parent::__construct();
	}


	public function test_get_day_title() {
		$this->assertEquals('Number of courses', ESRD()->dcc_what->get_title(ESRD_Enum_Course_Count_Discount_What::NUMBER_OF_COURSES));
		$this->assertEquals('Number of courses in group', ESRD()->dcc_what->get_title(ESRD_Enum_Course_Count_Discount_What::COURSES_IN_GROUP));
	}

}
