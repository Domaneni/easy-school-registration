<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

/**
 * Sample test case.
 */
class ESR_Dancing_As_Enum_Test extends PHPUnit_Framework_TestCase  {

	public function __construct() {
		parent::__construct();
	}


	public function test_is_leader() {
		$enum_dancing_as = new ESR_Dancing_As();
		$this->assertTrue($enum_dancing_as->is_leader(ESR_Dancing_As::LEADER));
		$this->assertFalse($enum_dancing_as->is_leader(ESR_Dancing_As::FOLLOWER));
		$this->assertFalse($enum_dancing_as->is_leader(ESR_Dancing_As::SOLO));
	}


	public function test_is_follower() {
		$enum_dancing_as = new ESR_Dancing_As();
		$this->assertTrue($enum_dancing_as->is_follower(ESR_Dancing_As::FOLLOWER));
		$this->assertFalse($enum_dancing_as->is_follower(ESR_Dancing_As::LEADER));
		$this->assertFalse($enum_dancing_as->is_follower(ESR_Dancing_As::SOLO));
	}


	public function test_is_solo() {
		$this->assertTrue(ESR()->dance_as->is_solo(ESR_Dancing_As::SOLO));
		$this->assertFalse(ESR()->dance_as->is_solo(ESR_Dancing_As::LEADER));
		$this->assertFalse(ESR()->dance_as->is_solo(ESR_Dancing_As::FOLLOWER));
	}


	public function test_get_title() {
		$enum_dancing_as = new ESR_Dancing_As();
		$this->assertEquals('Leader', $enum_dancing_as->get_title(ESR_Dancing_As::LEADER));
		$this->assertEquals('Follower', $enum_dancing_as->get_title(ESR_Dancing_As::FOLLOWER));
		$this->assertEquals('Solo', $enum_dancing_as->get_title(ESR_Dancing_As::SOLO));
		$this->assertEquals('', $enum_dancing_as->get_title(null));
	}

}
