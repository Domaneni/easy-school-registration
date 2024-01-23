<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

/**
 * Sample test case.
 */
class ESRCourseGroupEnumTest extends PHPUnit_Framework_TestCase {

	private $base_test;


	public function __construct() {
		parent::__construct();
		$this->base_test = new ESR_Base_Test();
	}


	public function test_get_groups_by_wave_empty() {
		$course_group = new ESR_Enum_Course_Group();
		$wave_id      = $this->base_test->add_wave();
		$this->assertEquals([], $course_group->get_groups_by_wave($wave_id));
	}


	public function test_get_groups_by_wave() {
		$wave_id = $this->base_test->add_wave();
		$this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'group_id' => 2]);
		$this->assertEquals([0 => ['group_id' => 2]], ESR()->course_group->get_groups_by_wave($wave_id));

		$this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'group_id' => 1]);
		$this->assertEquals([
			0 => ['group_id' => 1],
			1 => ['group_id' => 2]
		], ESR()->course_group->get_groups_by_wave($wave_id));

		$this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'group_id' => 1]);
		$this->assertEquals([
			0 => ['group_id' => 1],
			1 => ['group_id' => 2]
		], ESR()->course_group->get_groups_by_wave($wave_id));

		$this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'group_id' => 3]);
		$this->assertEquals([
			0 => ['group_id' => 1],
			1 => ['group_id' => 2],
			2 => ['group_id' => 3]
		], ESR()->course_group->get_groups_by_wave($wave_id));
	}


	public function test_get_items_for_tinymce() {

		$this->assertEquals([0 => ['text' => 'Choose an option', 'value' => '']], ESR()->course_group->get_items_for_tinymce());

		$this->base_test->update_settings(['groups' => ['Basic']]);
		$this->assertEquals([
			0 => ['text' => 'Choose an option', 'value' => ''],
			1 => ['text' => 'Basic', 'value' => 0]
		], ESR()->course_group->get_items_for_tinymce());
	}

}
