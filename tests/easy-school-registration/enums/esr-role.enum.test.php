<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

/**
 * Sample test case.
 */
class ESR_Role_Enum_Test extends PHPUnit_Framework_TestCase  {

	public function test_get_title() {
		$enum_role = new ESR_Role();
		$this->assertEquals('Student', $enum_role->get_title(ESR_Role::STUDENT));
		$this->assertEquals('Teacher', $enum_role->get_title(ESR_Role::TEACHER));
	}

}
