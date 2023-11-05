<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESRLevelTest extends PHPUnit_Framework_TestCase {

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


	public function test_get_items() {
		$this->assertEquals([], ESR()->course_level->get_items());

		$this->base_test->update_settings(['levels' => ['Beginners']]);

		$this->assertEquals(['Beginners'], ESR()->course_level->get_items());
	}


	public function test_get_item_title() {
		$this->assertEquals('', ESR()->course_level->get_title(null));
		$this->assertEquals('', ESR()->course_level->get_title(''));

		$this->base_test->update_settings(['levels' => ['Beginners']]);

		$this->assertEquals('Beginners', ESR()->course_level->get_title(0));
	}

}
