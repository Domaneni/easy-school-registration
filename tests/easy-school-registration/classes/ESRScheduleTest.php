<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESRScheduleTest extends PHPUnit_Framework_TestCase {

	private $base_test;


	public function __construct() {
		parent::__construct();
		$this->base_test = new ESR_Base_Test();
	}


	public function setUp(): void {
        parent::setUp();
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	public function test_get_times() {
		$wave_id   = $this->base_test->add_wave();
		$this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY, 'time_from' => '10:00', 'time_to' => '12:00']);

		$this->assertEquals('10:00', ESR()->schedule->get_lowest_course_start_time($wave_id)->time_from);
		$this->assertEquals('12:00', ESR()->schedule->get_highest_course_end_time($wave_id)->time_to);

		$this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY, 'time_from' => '09:00', 'time_to' => '11:00']);

		$this->assertEquals('09:00', ESR()->schedule->get_lowest_course_start_time($wave_id)->time_from);
		$this->assertEquals('12:00', ESR()->schedule->get_highest_course_end_time($wave_id)->time_to);

		$this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY, 'time_from' => '07:00', 'time_to' => '14:00']);

		$this->assertEquals('07:00', ESR()->schedule->get_lowest_course_start_time($wave_id)->time_from);
		$this->assertEquals('14:00', ESR()->schedule->get_highest_course_end_time($wave_id)->time_to);
	}

}
