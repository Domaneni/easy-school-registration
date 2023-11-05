<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

/**
 * Sample test case.
 */
class ESRScheduleStyleEnumTest extends PHPUnit_Framework_TestCase {

	public function __construct() {
		parent::__construct();
	}


	public function test_get_items_for_settings() {
		$enum = new ESR_Enum_Schedule_Style();
		$this->assertEquals([
			ESR_Enum_Schedule_Style::BY_HOURS         => 'By Hours',
			ESR_Enum_Schedule_Style::BY_DAYS          => 'By Days',
			ESR_Enum_Schedule_Style::BY_HOURS_COMPACT => 'By Hours Compact'
		], $enum->get_items_for_settings());
	}


	public function test_get_items_for_tinymce() {
		$enum = new ESR_Enum_Schedule_Style();
		$this->assertEquals([
			0 => [
				'text' => 'By Hours',
				'value' => ESR_Enum_Schedule_Style::BY_HOURS
			],
			1 => [
				'text' => 'By Days',
				'value' => ESR_Enum_Schedule_Style::BY_DAYS
			],
			2 => [
				'text' => 'By Hours Compact',
				'value' => ESR_Enum_Schedule_Style::BY_HOURS_COMPACT
			]
		], $enum->get_items_for_tinymce());
	}

}
