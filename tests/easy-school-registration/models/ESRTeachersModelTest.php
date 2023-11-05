<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESRTeachersModelTest extends PHPUnit_Framework_TestCase {

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


	public function test_get_teacher_name() {
		$this->assertEquals('', ESR()->teacher->get_teacher_name(999));

		$teacher_id = $this->add_teacher();
		$this->assertEquals('Novak', ESR()->teacher->get_teacher_name($teacher_id));

		$teacher_id = $this->add_teacher('Jane Novak', null);
		$this->assertEquals('Jane Novak', ESR()->teacher->get_teacher_name($teacher_id));
	}


	public function test_get_teachers_name() {
		$teacher1_id = $this->add_teacher();

		$this->assertEquals('Novak', ESR()->teacher->get_teachers_names($teacher1_id, null));

		$teacher2_id = $this->add_teacher('Jane Novak', 'Jane');

		$this->assertEquals('Jane', ESR()->teacher->get_teachers_names(null, $teacher2_id));

		$this->assertEquals('Novak & Jane', ESR()->teacher->get_teachers_names($teacher1_id, $teacher2_id));

		$this->update_teacher($teacher1_id, ['nickname' => null]);

		$this->assertEquals('Peter Novak & Jane', ESR()->teacher->get_teachers_names($teacher1_id, $teacher2_id));

		$this->update_teacher($teacher2_id, ['nickname' => null]);

		$this->assertEquals('Peter Novak & Jane Novak', ESR()->teacher->get_teachers_names($teacher1_id, $teacher2_id));
	}


	public function test_get_teacher_data() {
		$teacher1_id   = $this->add_teacher();
		$teacher1_data = ESR()->teacher->get_teacher_data($teacher1_id);

		$this->assertEquals('Peter Novak', $teacher1_data->name);
		$this->assertEquals('Novak', $teacher1_data->nickname);

		$teacher2_id   = $this->add_teacher('Jane Novak', 'Jane');
		$teacher2_data = ESR()->teacher->get_teacher_data($teacher2_id);

		$this->assertEquals('Jane Novak', $teacher2_data->name);
		$this->assertEquals('Jane', $teacher2_data->nickname);
	}


	public function test_get_teachers_data() {
		$teacher1_id     = $this->add_teacher();
		$teacher2_id     = $this->add_teacher('Jane Novak', 'Jane');
		$teachers_data[] = ESR()->teacher->get_teacher_data($teacher1_id);
		$teachers_data[] = ESR()->teacher->get_teacher_data($teacher2_id);

		$this->assertEquals(ESR()->teacher->get_teachers_data(), $teachers_data);
	}


	public function test_set_teacher_active() {
		$worker_ajax = new ESR_Ajax_Worker();
		$teacher1_id = $this->add_teacher();

		$this->assertEquals(true, (boolean) ESR()->teacher->get_teacher_data($teacher1_id)->active);

		$this->assertEquals(1, $worker_ajax->change_teacher_active($teacher1_id, true));
		$this->assertEquals(true, (boolean) ESR()->teacher->get_teacher_data($teacher1_id)->active);

		$this->assertEquals(1, $worker_ajax->change_teacher_active($teacher1_id, false));
		$this->assertEquals(false, (boolean) ESR()->teacher->get_teacher_data($teacher1_id)->active);

		$this->assertEquals(1, $worker_ajax->change_teacher_active($teacher1_id, true));
		$this->assertEquals(true, (boolean) ESR()->teacher->get_teacher_data($teacher1_id)->active);

		$this->assertEquals(-1, $worker_ajax->change_teacher_active(null, true));
		$this->assertEquals(true, (boolean) ESR()->teacher->get_teacher_data($teacher1_id)->active);
	}


	public function test_is_user_teacher() {
		$user_id     = wp_create_user('esr_test', 'esr_test', 'teacher_test@easyschoolregistration.com');
		$this->add_teacher('Peter', 'Novak', $user_id);

		$user2_id     = wp_create_user('esr_test2', 'esr_test2', 'teacher_test2@easyschoolregistration.com');

		$this->assertEquals(true, ESR()->teacher->esr_is_user_teacher($user_id));
		$this->assertEquals(false, ESR()->teacher->esr_is_user_teacher($user2_id));

		wp_delete_user($user_id);
		wp_delete_user($user2_id);
	}


	public function test_get_teacher_data_by_user() {
		$user_id     = wp_create_user('esr_test', 'esr_test', 'teacher_test@easyschoolregistration.com');
		$teacher_id = $this->add_teacher('Peter', 'Novak', $user_id);

		$this->assertEquals(null, ESR()->teacher->get_teacher_data_by_user(999));
		$this->assertEquals(ESR()->teacher->get_teacher_data($teacher_id), ESR()->teacher->get_teacher_data_by_user($user_id));

		wp_delete_user($user_id);
	}


	private function add_teacher($name = 'Peter Novak', $nickname = 'Novak', $user_id = null) {
		global $wpdb;
		$worker_teacher = new ESR_Teacher_Worker();

		$worker_teacher->process_teacher([
			'name'     => $name,
			'nickname' => $nickname,
			'user_id'  => $user_id
		]);

		return $wpdb->insert_id;
	}


	private function update_teacher($teacher_id, $data) {
		global $wpdb;
		$wpdb->update($wpdb->prefix . 'esr_teacher_data', $data, [
			'id' => $teacher_id
		]);
	}
}
