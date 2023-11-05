<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESRCourseModelTest extends PHPUnit_Framework_TestCase {

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


	public function test_get_course_data() {
		$wave_id     = $this->base_test->add_wave();
		$course_id   = $this->base_test->add_course($wave_id, [
			'sub_header' => 'Beginners',
			'day'        => ESR_Enum_Day::MONDAY
		]);
		$course_data = ESR()->course->get_course_data($course_id);

		$this->assertEquals($wave_id, $course_data->wave_id);
		$this->assertEquals('Beginners', $course_data->sub_header);
		$this->assertEquals(ESR_Enum_Day::MONDAY, $course_data->day);
	}


	public function test_course_price_update() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, [
			'sub_header'  => 'Beginners',
			'day'         => ESR_Enum_Day::MONDAY,
			'course_from' => '2017-05-12 17:00:00',
			'price'       => 25
		]);

		$this->assertEquals(25, ESR()->course->get_course_data($course_id)->price);

		$this->base_test->update_course($course_id, ['wave_id' => $wave_id, 'price' => 85]);

		$this->assertEquals(85, ESR()->course->get_course_data($course_id)->price);
	}


	public function test_is_course_solo() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id);

		$this->assertFalse(ESR()->course->is_course_solo($course_id));

		$this->base_test->update_course($course_id, ['wave_id' => $wave_id, 'is_solo' => true]);

		$this->assertTrue(ESR()->course->is_course_solo($course_id));

		$this->base_test->update_course($course_id, ['wave_id' => $wave_id, 'is_solo' => false]);

		$this->assertFalse(ESR()->course->is_course_solo($course_id));
	}


	public function test_get_course_by_wave() {
		$wave_id     = $this->base_test->add_wave();
		$course_id   = $this->base_test->add_course($wave_id);
		$course_data = ESR()->course->get_courses_data_by_wave($wave_id);

		$this->assertEquals(1, count($course_data));
		$this->assertEquals($course_id, $course_data[0]->id);
		$this->assertEquals($wave_id, $course_data[0]->wave_id);
		$this->assertEquals([$course_id => $course_data[0]], ESR()->course->get_courses_data_by_wave($wave_id, true));

		$wave_id     = $this->base_test->add_wave();
		$course_data = ESR()->course->get_courses_data_by_wave($wave_id);

		$this->assertEquals(0, count($course_data));
	}


	public function test_get_active_course_by_wave() {
		$wave_id = $this->base_test->add_wave();
		$this->base_test->add_course($wave_id, ['is_passed' => true]);
		$course_data = ESR()->course->get_active_courses_data_by_wave($wave_id);

		$this->assertEquals(0, count($course_data));
		$this->assertEquals([], ESR()->course->get_active_courses_data_by_wave($wave_id, NULL, true));

		$course_id   = $this->base_test->add_course($wave_id);
		$course_data = ESR()->course->get_active_courses_data_by_wave($wave_id);

		$this->assertEquals(1, count($course_data));
		$this->assertEquals($course_id, $course_data[0]->id);
		$this->assertEquals($wave_id, $course_data[0]->wave_id);
		$this->assertEquals([$course_id => $course_data[0]], ESR()->course->get_active_courses_data_by_wave($wave_id, NULL, true));
	}


	public function test_is_course_with_no_place_enabled() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id);

		$this->assertFalse(ESR()->course->is_course_enabled($course_id));

		$this->base_test->update_course($course_id, ['wave_id' => $wave_id, 'max_leaders' => 10]);

		$this->assertTrue(ESR()->course->is_course_enabled($course_id));

		$this->base_test->update_course($course_id, ['wave_id' => $wave_id, 'is_solo' => true]);

		$this->assertFalse(ESR()->course->is_course_enabled($course_id));

		$this->base_test->update_course($course_id, ['wave_id' => $wave_id, 'max_solo' => 10]);

		$this->assertTrue(ESR()->course->is_course_enabled($course_id));
	}


	/*public function test_prepare_all_courses_by_wave() {
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id);
		$wave2_id   = $this->base_test->add_wave();
		$course2_id = $this->base_test->add_course($wave2_id);

		$this->assertEquals([
			$wave2_id => [$course2_id => ESR()->course->get_course_data($course2_id)],
			$wave_id  => [$course_id => ESR()->course->get_course_data($course_id)]
		], ESR()->course->prepare_all_courses_by_waves());
	}
*/

	public function test_add_filed() {
		$this->assertEquals(false, property_exists(ESR()->course->get_fields(), 'test_field'));

		ESR()->course->add_field('test_field', 'string', false);

		$this->assertEquals(true, property_exists(ESR()->course->get_fields(), 'test_field'));
	}


	public function test_set_course_passed() {
		$worker_ajax = new ESR_Ajax_Worker();
		$wave_id     = $this->base_test->add_wave();
		$course_id   = $this->base_test->add_course($wave_id);
		$wave2_id    = $this->base_test->add_wave();
		$course2_id  = $this->base_test->add_course($wave2_id);

		$this->assertEquals(false, (boolean) ESR()->course->get_course_data($course_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->course->get_course_data($course2_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave2_id)->is_passed);

		$this->assertEquals(['status' => 'Passed'], $worker_ajax->change_course_passed($course_id, true));

		$this->assertEquals(true, (boolean) ESR()->course->get_course_data($course_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->course->get_course_data($course2_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave2_id)->is_passed);

		$this->assertEquals(['status' => 'Active'], $worker_ajax->change_course_passed($course_id, false));

		$this->assertEquals(false, (boolean) ESR()->course->get_course_data($course_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->course->get_course_data($course2_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave2_id)->is_passed);

		$this->assertEquals(['status' => 'Passed'], $worker_ajax->change_course_passed($course_id, true));
		$this->assertEquals(['status' => 'Passed'], $worker_ajax->change_course_passed($course2_id, true));

		$this->assertEquals(true, (boolean) ESR()->course->get_course_data($course_id)->is_passed);
		$this->assertEquals(true, (boolean) ESR()->course->get_course_data($course2_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave2_id)->is_passed);

		$this->assertEquals(-1, $worker_ajax->change_course_passed(null, true));
	}

}
