<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESRWaveWorkerTest extends PHPUnit_Framework_TestCase {

	private $base_test;

	private $worker_wave;


	public function __construct() {
		parent::__construct();
		$this->base_test   = new ESR_Base_Test();
		$this->worker_wave = new ESR_Wave_Worker();
	}


	public function setUp(): void {
        parent::setUp();
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	/**
	 * Test adding teacher
	 */
	public function test_wave_add() {
		$this->assertEquals(0, $this->count_waves());

		$this->worker_wave->process_wave([
			'title'                  => 'Wave 1',
			'registration_from'      => date('Y-m-d H:i:s'),
			'registration_from_time' => date('H:i:s'),
			'registration_to'        => date('Y-m-d H:i:s', strtotime("+1 day")),
			'registration_to_time'   => date('H:i:s', strtotime("+1 day")),
		]);

		$this->assertEquals(1, $this->count_waves());

		$this->worker_wave->process_wave([
			'title'                  => 'Wave 2',
			'registration_from'      => date('Y-m-d H:i:s', strtotime("+1 month")),
			'registration_from_time' => date('H:i:s', strtotime("+1 month")),
			'registration_to'        => date('Y-m-d H:i:s', strtotime("+1 month +1 day")),
			'registration_to_time'   => date('H:i:s', strtotime("+1 month +1 day")),
		]);

		$this->assertEquals(2, $this->count_waves());
	}


	/**
	 * Test edit teacher
	 */
	public function test_teacher_edit() {
		$this->assertEquals(0, $this->count_waves());

		$this->worker_wave->process_wave([
			'title'             => 'Wave 1',
			'registration_from'      => date('Y-m-d H:i:s'),
			'registration_from_time' => date('H:i:s'),
			'registration_to'        => date('Y-m-d H:i:s', strtotime("+1 day")),
			'registration_to_time'   => date('H:i:s', strtotime("+1 day")),
		]);

		$this->assertEquals(1, $this->count_waves());
		$this->assertTrue($this->wave_exists_by_tite('Wave 1'));
		$this->assertFalse($this->wave_exists_by_tite('Wave 2'));

		$wave_id = $this->fetch_wave_id_by_title('Wave 1');

		$this->worker_wave->process_wave([
			'title'             => 'Wave 2',
			'registration_from'      => date('Y-m-d H:i:s'),
			'registration_from_time' => date('H:i:s'),
			'registration_to'        => date('Y-m-d H:i:s', strtotime("+1 day")),
			'registration_to_time'   => date('H:i:s', strtotime("+1 day")),
			'wave_id'           => $wave_id
		]);

		$this->assertEquals(1, $this->count_waves());
		$this->assertFalse($this->wave_exists_by_tite('Wave 1'));
		$this->assertTrue($this->wave_exists_by_tite('Wave 2'));
	}


	private function count_waves() {
		global $wpdb;

		return $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}esr_wave_data");
	}


	private function wave_exists_by_tite($title) {
		global $wpdb;

		return filter_var($wpdb->get_var($wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}esr_wave_data WHERE title LIKE %s", [$title])), FILTER_VALIDATE_BOOLEAN);
	}


	private function fetch_wave_id_by_title($title) {
		global $wpdb;

		return $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}esr_wave_data WHERE title LIKE %s", [$title]));
	}

}
