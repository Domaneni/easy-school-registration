<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-discounts/esrd-base.php';

/**
 * Sample test case.
 */
class ESRD_Course_Count_Discount_How_Enum_Test extends PHPUnit_Framework_TestCase {

	public function __construct() {
		parent::__construct();
	}


	public function test_get_day_title() {
		$this->assertEquals('Minus percentage', ESRD()->dcc_how->get_title(ESRD_Enum_Course_Count_Discount_How::IN_PERCENTAGE));
		$this->assertEquals('Minus price', ESRD()->dcc_how->get_title(ESRD_Enum_Course_Count_Discount_How::IN_PRICE));
		$this->assertEquals('Final price', ESRD()->dcc_how->get_title(ESRD_Enum_Course_Count_Discount_How::FINAL_PRICE));
		$this->assertEquals(null, ESRD()->dcc_how->get_title(0));
	}


	public function test_get_value_with_currency() {
		$this->assertEquals(20, ESRD()->dcc_how->get_value_with_currency(0, 20));

		$this->assertEquals('- 20 %', ESRD()->dcc_how->get_value_with_currency(ESRD_Enum_Course_Count_Discount_How::IN_PERCENTAGE, 20));

		global $esr_settings;
		$esr_settings['currency'] = 'CZK';
		$esr_settings['currency_position'] = 'before_with_space';

		$this->assertEquals('- Kč 20', ESRD()->dcc_how->get_value_with_currency(ESRD_Enum_Course_Count_Discount_How::IN_PRICE, 20));

		$esr_settings['currency_position'] = 'after';

		$this->assertEquals('200Kč', ESRD()->dcc_how->get_value_with_currency(ESRD_Enum_Course_Count_Discount_How::FINAL_PRICE, 200));
	}

}
