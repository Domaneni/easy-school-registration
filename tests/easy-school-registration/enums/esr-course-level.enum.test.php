<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

/**
 * Sample test case.
 */
class ESR_Course_Level_Enum_Test extends PHPUnit_Framework_TestCase {

	private $base_test;


	public function __construct() {
		parent::__construct();
		$this->base_test = new ESR_Base_Test();
	}


	public function test_get_levels_by_wave_empty() {
		$course_level = new ESR_Enum_Course_Level();
		$this->assertEquals([], $course_level->get_items());
	}


	public function test_get_levels_by_wave() {
		$wave_id  = $this->base_test->add_wave();
		$wave2_id = $this->base_test->add_wave();

		$this->assertEquals([], ESR()->course_level->get_levels_by_wave($wave_id));
		$this->assertEquals([], ESR()->course_level->get_levels_by_wave($wave2_id));

		$this->base_test->add_course($wave_id, [
			'sub_header'  => 'Beginners',
			'day'         => ESR_Enum_Day::MONDAY,
			'course_from' => '2017-05-12 17:00:00',
			'price'       => 25,
			'level_id'    => 1
		]);

		$this->assertEquals([0 => ['level_id' => 1]], ESR()->course_level->get_levels_by_wave($wave_id));
		$this->assertEquals([], ESR()->course_level->get_levels_by_wave($wave2_id));


		$this->base_test->add_course($wave_id, [
			'sub_header'  => 'Beginners',
			'day'         => ESR_Enum_Day::MONDAY,
			'course_from' => '2017-05-12 17:00:00',
			'price'       => 25,
			'level_id'    => 2
		]);


		$this->base_test->add_course($wave_id, [
			'sub_header'  => 'Beginners',
			'day'         => ESR_Enum_Day::MONDAY,
			'course_from' => '2017-05-12 17:00:00',
			'price'       => 25,
			'level_id'    => 3
		]);


		$this->base_test->add_course($wave2_id, [
			'sub_header'  => 'Beginners',
			'day'         => ESR_Enum_Day::MONDAY,
			'course_from' => '2017-05-12 17:00:00',
			'price'       => 25,
			'level_id'    => 1
		]);

		$this->assertEquals([0 => ['level_id' => 1], 1 => ['level_id' => 2], 2 => ['level_id' => 3]], ESR()->course_level->get_levels_by_wave($wave_id));
		$this->assertEquals([0 => ['level_id' => 1]], ESR()->course_level->get_levels_by_wave($wave2_id));
	}

}
