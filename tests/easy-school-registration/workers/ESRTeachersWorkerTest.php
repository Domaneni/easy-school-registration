<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESRTeachersWorkerTest extends PHPUnit_Framework_TestCase {

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

	/**
	 * Test adding teacher
	 */
	public function test_teacher_add() {
		$worker_teacher = new ESR_Teacher_Worker();

		$this->assertEquals(0, $this->count_teachers());

		$worker_teacher->process_teacher([
			'name'        => 'Karel Novak',
			'nickname'    => 'Novak',
			'description' => 'Test description'
		]);

		$this->assertEquals(1, $this->count_teachers());

		$worker_teacher->process_teacher([
			'name'        => 'Klara Novak',
			'nickname'    => 'Klara',
			'description' => 'Test description'
		]);

		$this->assertEquals(2, $this->count_teachers());
	}

	/**
	 * Test edit teacher
	 */
	public function test_teacher_edit() {
		$worker_teacher = new ESR_Teacher_Worker();

		$this->assertEquals(0, $this->count_teachers());

		$worker_teacher->process_teacher([
			'name'        => 'Karel Novak',
			'nickname'    => 'Novak',
			'description' => 'Test description'
		]);

		$this->assertEquals(1, $this->count_teachers());
		$this->assertTrue($this->teacher_exists_by_nickname('Novak'));
		$this->assertFalse($this->teacher_exists_by_nickname('Kavon'));

		$teacher_id = $this->fetch_teacher_id_by_name('Karel Novak');

		$worker_teacher->process_teacher([
			'name'        => 'Karel Novak',
			'nickname'    => 'Kavon',
			'description' => 'Test description',
			'teacher_id'  => $teacher_id
		]);

		$this->assertEquals(1, $this->count_teachers());
		$this->assertFalse($this->teacher_exists_by_nickname('Novak'));
		$this->assertTrue($this->teacher_exists_by_nickname('Kavon'));
	}

	private function count_teachers() {
		global $wpdb;

		return $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}esr_teacher_data");
	}


	private function teacher_exists_by_nickname($nickname) {
		global $wpdb;

		return filter_var($wpdb->get_var($wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}esr_teacher_data WHERE nickname LIKE %s", [$nickname])), FILTER_VALIDATE_BOOLEAN);
	}

	private function fetch_teacher_id_by_name($name) {
		global $wpdb;

		return $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}esr_teacher_data WHERE name LIKE %s", [$name]));
	}

}
