<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESRWaveModelTest extends PHPUnit_Framework_TestCase {

	private $base_test;

	private $model_wave;


	public function __construct() {
		parent::__construct();
		$this->base_test  = new ESR_Base_Test();
		$this->model_wave = new ESR_Wave();
	}


	public function setUp(): void {
        parent::setUp();
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	public function test_get_wave_data() {
		$registration_from      = date('Y-m-d H:i:s');
		$registration_from_time = date('H:i:s');
		$registration_to        = date('Y-m-d H:i:s', strtotime("+1 day"));
		$registration_to_time   = date('H:i:s', strtotime("+1 day"));
		$wave_id                = $this->base_test->add_wave('Wave 1', $registration_from, $registration_to, $registration_from_time, $registration_to_time);
		$wave_data              = $this->model_wave->get_wave_data($wave_id);

		$this->assertEquals('Wave 1', $wave_data->title);
		$this->assertEquals($registration_from, $wave_data->registration_from);
		$this->assertEquals($registration_to, $wave_data->registration_to);
	}


	public function test_get_waves_data() {
		$registration1_from      = date('Y-m-d H:i:s');
		$registration1_from_time = date('H:i:s');
		$registration1_to        = date('Y-m-d H:i:s', strtotime("+1 day"));
		$registration1_to_time   = date('H:i:s', strtotime("+1 day"));
		$wave1_id                = $this->base_test->add_wave('Wave 1', $registration1_from, $registration1_to, $registration1_from_time, $registration1_to_time);

		$registration2_from      = date('Y-m-d H:i:s', strtotime("+1 month"));
		$registration2_from_time = date('H:i:s', strtotime("+1 month"));
		$registration2_to        = date('Y-m-d H:i:s', strtotime("+1 month +1 day"));
		$registration2_to_time   = date('H:i:s', strtotime("+1 month +1 day"));
		$wave2_id                = $this->base_test->add_wave('Wave 1', $registration2_from, $registration2_to, $registration2_from_time, $registration2_to_time);

		$waves_data[$wave1_id] = $this->model_wave->get_wave_data($wave1_id);
		$waves_data[$wave2_id] = $this->model_wave->get_wave_data($wave2_id);

		$this->assertEquals($this->model_wave->get_waves_data(true), $waves_data);
	}


	public function test_is_registration_active() {
		$registration_from      = date('Y-m-d H:i:s');
		$registration_from_time = date('H:i:s');
		$registration_to        = date('Y-m-d H:i:s', strtotime("+1 day"));
		$registration_to_time   = date('H:i:s', strtotime("+1 day"));
		$wave_id                = $this->base_test->add_wave('Wave 1', $registration_from, $registration_to, $registration_from_time, $registration_to_time);

		$this->assertTrue($this->model_wave->is_wave_registration_active($wave_id));


		$registration_from      = date('Y-m-d H:i:s', strtotime("+1 day"));
		$registration_from_time = date('H:i:s', strtotime("+1 day"));
		$registration_to        = date('Y-m-d H:i:s', strtotime("+2 days"));
		$registration_to_time   = date('H:i:s', strtotime("+2 days"));
		$wave_id                = $this->base_test->add_wave('Wave 1', $registration_from, $registration_to, $registration_from_time, $registration_to_time);

		$this->assertFalse($this->model_wave->is_wave_registration_active($wave_id));

		$this->base_test->update_wave($wave_id, ['registration_from' => date('Y-m-d H:i:s', strtotime("-1 day"))]);

		$this->assertTrue($this->model_wave->is_wave_registration_active($wave_id));
	}


	public function test_is_registration_closed() {
		$registration_from      = date('Y-m-d H:i:s', strtotime("-1 day"));
		$registration_from_time = date('H:i:s', strtotime("-1 day"));
		$registration_to        = date('Y-m-d H:i:s', strtotime("+1 day"));
		$registration_to_time   = date('H:i:s', strtotime("+1 day"));
		$wave_id                = $this->base_test->add_wave('Wave 1', $registration_from, $registration_to, $registration_from_time, $registration_to_time);

		$this->assertFalse($this->model_wave->is_wave_registration_closed($wave_id));

		$this->base_test->update_wave($wave_id, ['registration_to' => date('Y-m-d H:i:s', strtotime("-1 hour"))]);

		$this->assertTrue($this->model_wave->is_wave_registration_closed($wave_id));

		$this->base_test->update_wave($wave_id, ['registration_to' => date('Y-m-d H:i:s', strtotime("+1 day")), 'is_passed' => true]);

		$this->assertTrue($this->model_wave->is_wave_registration_closed($wave_id));
	}


	public function test_is_registration_not_opened_yet() {
		$registration_from      = date('Y-m-d H:i:s', strtotime("-2 day"));
		$registration_from_time = date('H:i:s', strtotime("-2 day"));
		$registration_to        = date('Y-m-d H:i:s', strtotime("+2 day"));
		$registration_to_time   = date('H:i:s', strtotime("+2 day"));
		$wave_id                = $this->base_test->add_wave('Wave 1', $registration_from, $registration_to, $registration_from_time, $registration_to_time);

		$this->assertFalse($this->model_wave->is_wave_registration_not_opened_yet($wave_id));

		$this->base_test->update_wave($wave_id, ['registration_from' => date('Y-m-d H:i:s', strtotime("+1 hour"))]);

		$this->assertTrue($this->model_wave->is_wave_registration_not_opened_yet($wave_id));

		$this->base_test->update_wave($wave_id, ['registration_from' => date('Y-m-d H:i:s', strtotime("+1 hour")), 'is_passed' => true]);

		$this->assertFalse($this->model_wave->is_wave_registration_not_opened_yet($wave_id));
	}


	public function test_get_all_waves_ids() {
		$this->assertEquals([], $this->model_wave->get_all_waves_ids());
		$this->assertEquals([], $this->model_wave->get_waves_to_process_ids());

		$registration_from      = date('Y-m-d H:i:s');
		$registration_from_time = date('H:i:s');
		$registration_to        = date('Y-m-d H:i:s', strtotime("+1 day"));
		$registration_to_time   = date('H:i:s', strtotime("+1 day"));
		$wave_id                = $this->base_test->add_wave('Wave 1', $registration_from, $registration_to, $registration_from_time, $registration_to_time);

		$ids[$wave_id] = $wave_id;

		$this->assertEquals($ids, $this->model_wave->get_all_waves_ids());
		$this->assertEquals($ids, $this->model_wave->get_all_waves_ids());

		$registration2_from      = date('Y-m-d H:i:s', strtotime("+1 month"));
		$registration2_from_time = date('H:i:s', strtotime("+1 month"));
		$registration2_to        = date('Y-m-d H:i:s', strtotime("+1 month +1 day"));
		$registration2_to_time   = date('H:i:s', strtotime("+1 month +1 day"));
		$wave2_id                = $this->base_test->add_wave('Wave 1', $registration2_from, $registration2_to, $registration2_from_time, $registration2_to_time);

		$ids[$wave2_id] = $wave2_id;

		$this->assertEquals($ids, $this->model_wave->get_all_waves_ids());
		$this->assertEquals($ids, $this->model_wave->get_all_waves_ids());
	}


	public function test_set_wave_passed() {
		$worker_ajax = new ESR_Ajax_Worker();
		$wave_id     = $this->base_test->add_wave();
		$course_id   = $this->base_test->add_course($wave_id);
		$wave2_id    = $this->base_test->add_wave();
		$course2_id  = $this->base_test->add_course($wave2_id);

		$this->assertEquals(false, (boolean) ESR()->course->get_course_data($course_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->course->get_course_data($course2_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave2_id)->is_passed);

		$this->assertEquals(1, $worker_ajax->change_wave_passed($wave_id, true));

		$this->assertEquals(true, (boolean) ESR()->course->get_course_data($course_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->course->get_course_data($course2_id)->is_passed);
		$this->assertEquals(true, (boolean) ESR()->wave->get_wave_data($wave_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave2_id)->is_passed);

		$this->assertEquals(1, $worker_ajax->change_wave_passed($wave_id, false));

		$this->assertEquals(false, (boolean) ESR()->course->get_course_data($course_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->course->get_course_data($course2_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave_id)->is_passed);
		$this->assertEquals(false, (boolean) ESR()->wave->get_wave_data($wave2_id)->is_passed);

		$this->assertEquals(1, $worker_ajax->change_wave_passed($wave_id, true));
		$this->assertEquals(1, $worker_ajax->change_wave_passed($wave2_id, true));

		$this->assertEquals(true, (boolean) ESR()->course->get_course_data($course_id)->is_passed);
		$this->assertEquals(true, (boolean) ESR()->course->get_course_data($course2_id)->is_passed);
		$this->assertEquals(true, (boolean) ESR()->wave->get_wave_data($wave_id)->is_passed);
		$this->assertEquals(true, (boolean) ESR()->wave->get_wave_data($wave2_id)->is_passed);

		$this->assertEquals(-1, $worker_ajax->change_wave_passed(null, true));
	}
}
