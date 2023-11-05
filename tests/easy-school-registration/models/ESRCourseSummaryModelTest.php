<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESRCourseSummaryModelTest extends PHPUnit_Framework_TestCase {

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


	public function test_get_course_summary() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));
	}


	public function test_recount_course_in_numbers_all_to_zero() {
		global $wpdb;

		$worker_cin = new ESR_Course_In_Numbers_Worker();
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id);

		$wpdb->update($wpdb->prefix . 'esr_course_summary', [
			'registered_leaders'   => 10,
			'registered_followers' => 5,
			'registered_solo'      => 3,
			'waiting_leaders'      => 12,
			'waiting_followers'    => 7,
			'waiting_solo'         => 8,
		], ['course_id' => $course_id]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '10',
			'registered_followers' => '5',
			'registered_solo'      => '3',
			'waiting_leaders'      => '12',
			'waiting_followers'    => '7',
			'waiting_solo'         => '8',
		], ESR()->course_summary->get_course_summary($course_id));

		$worker_cin->esr_recount_wave_statistics($wave_id);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));
	}


	public function test_recount_cin_registered_solo() {
		global $wpdb;

		$worker_cin = new ESR_Course_In_Numbers_Worker();
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id, ['is_solo' => true, 'max_solo' => 3]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::SOLO);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '1',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::SOLO, 'unittest1@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '2',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$wpdb->update($wpdb->prefix . 'esr_course_summary', [
			'registered_leaders'   => 10,
			'registered_followers' => 5,
			'registered_solo'      => 13,
			'waiting_leaders'      => 12,
			'waiting_followers'    => 7,
			'waiting_solo'         => 8,
		], ['course_id' => $course_id]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '10',
			'registered_followers' => '5',
			'registered_solo'      => '13',
			'waiting_leaders'      => '12',
			'waiting_followers'    => '7',
			'waiting_solo'         => '8',
		], ESR()->course_summary->get_course_summary($course_id));

		$worker_cin->esr_recount_wave_statistics($wave_id);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '2',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));
	}


	public function test_recount_cin_registered_leader_follower() {
		global $wpdb;

		$worker_cin = new ESR_Course_In_Numbers_Worker();
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '1',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest1@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '1',
			'registered_followers' => '1',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$wpdb->update($wpdb->prefix . 'esr_course_summary', [
			'registered_leaders'   => 10,
			'registered_followers' => 5,
			'registered_solo'      => 13,
			'waiting_leaders'      => 12,
			'waiting_followers'    => 7,
			'waiting_solo'         => 8,
		], ['course_id' => $course_id]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '10',
			'registered_followers' => '5',
			'registered_solo'      => '13',
			'waiting_leaders'      => '12',
			'waiting_followers'    => '7',
			'waiting_solo'         => '8',
		], ESR()->course_summary->get_course_summary($course_id));

		$worker_cin->esr_recount_wave_statistics($wave_id);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '1',
			'registered_followers' => '1',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest2@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '1',
			'registered_followers' => '1',
			'registered_solo'      => '0',
			'waiting_leaders'      => '1',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest3@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '1',
			'registered_followers' => '1',
			'registered_solo'      => '0',
			'waiting_leaders'      => '2',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest4@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '2',
			'registered_followers' => '2',
			'registered_solo'      => '0',
			'waiting_leaders'      => '1',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest5@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '3',
			'registered_followers' => '3',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest6@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '3',
			'registered_followers' => '3',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '1',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$wpdb->update($wpdb->prefix . 'esr_course_summary', [
			'registered_leaders'   => 10,
			'registered_followers' => 5,
			'registered_solo'      => 13,
			'waiting_leaders'      => 12,
			'waiting_followers'    => 7,
			'waiting_solo'         => 8,
		], ['course_id' => $course_id]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '10',
			'registered_followers' => '5',
			'registered_solo'      => '13',
			'waiting_leaders'      => '12',
			'waiting_followers'    => '7',
			'waiting_solo'         => '8',
		], ESR()->course_summary->get_course_summary($course_id));

		$worker_cin->esr_recount_wave_statistics($wave_id);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '3',
			'registered_followers' => '3',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '1',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

	}


	public function test_recount_cin_waiting_leader_follower() {
		global $wpdb;

		$worker_cin = new ESR_Course_In_Numbers_Worker();
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'pairing_mode' => ESR_Enum_Pairing_Mode::MANUAL]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '1',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest1@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '1',
			'waiting_followers'    => '1',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$wpdb->update($wpdb->prefix . 'esr_course_summary', [
			'registered_leaders'   => 10,
			'registered_followers' => 5,
			'registered_solo'      => 13,
			'waiting_leaders'      => 12,
			'waiting_followers'    => 7,
			'waiting_solo'         => 8,
		], ['course_id' => $course_id]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '10',
			'registered_followers' => '5',
			'registered_solo'      => '13',
			'waiting_leaders'      => '12',
			'waiting_followers'    => '7',
			'waiting_solo'         => '8',
		], ESR()->course_summary->get_course_summary($course_id));

		$worker_cin->esr_recount_wave_statistics($wave_id);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '1',
			'waiting_followers'    => '1',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest2@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '2',
			'waiting_followers'    => '1',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest3@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '3',
			'waiting_followers'    => '1',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest4@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '3',
			'waiting_followers'    => '2',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest5@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '3',
			'waiting_followers'    => '3',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest6@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '3',
			'waiting_followers'    => '4',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$wpdb->update($wpdb->prefix . 'esr_course_summary', [
			'registered_leaders'   => 10,
			'registered_followers' => 5,
			'registered_solo'      => 13,
			'waiting_leaders'      => 12,
			'waiting_followers'    => 7,
			'waiting_solo'         => 8,
		], ['course_id' => $course_id]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '10',
			'registered_followers' => '5',
			'registered_solo'      => '13',
			'waiting_leaders'      => '12',
			'waiting_followers'    => '7',
			'waiting_solo'         => '8',
		], ESR()->course_summary->get_course_summary($course_id));

		$worker_cin->esr_recount_wave_statistics($wave_id);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '3',
			'waiting_followers'    => '4',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));
	}


	public function test_recount_cin_waiting_solo() {
		global $wpdb;

		$worker_cin = new ESR_Course_In_Numbers_Worker();
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id, ['max_solo' => 10, 'is_solo' => true, 'pairing_mode' => ESR_Enum_Pairing_Mode::MANUAL]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '1',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest1@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '2',
		], ESR()->course_summary->get_course_summary($course_id));

		$wpdb->update($wpdb->prefix . 'esr_course_summary', [
			'registered_leaders'   => 10,
			'registered_followers' => 5,
			'registered_solo'      => 13,
			'waiting_leaders'      => 12,
			'waiting_followers'    => 7,
			'waiting_solo'         => 8,
		], ['course_id' => $course_id]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '10',
			'registered_followers' => '5',
			'registered_solo'      => '13',
			'waiting_leaders'      => '12',
			'waiting_followers'    => '7',
			'waiting_solo'         => '8',
		], ESR()->course_summary->get_course_summary($course_id));

		$worker_cin->esr_recount_wave_statistics($wave_id);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '2',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest2@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '3',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest3@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '4',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest4@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '5',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest5@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '6',
		], ESR()->course_summary->get_course_summary($course_id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::FOLLOWER, 'unittest6@easyschoolregsitration.com');

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '7',
		], ESR()->course_summary->get_course_summary($course_id));

		$wpdb->update($wpdb->prefix . 'esr_course_summary', [
			'registered_leaders'   => 10,
			'registered_followers' => 5,
			'registered_solo'      => 13,
			'waiting_leaders'      => 12,
			'waiting_followers'    => 7,
			'waiting_solo'         => 8,
		], ['course_id' => $course_id]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '10',
			'registered_followers' => '5',
			'registered_solo'      => '13',
			'waiting_leaders'      => '12',
			'waiting_followers'    => '7',
			'waiting_solo'         => '8',
		], ESR()->course_summary->get_course_summary($course_id));

		$worker_cin->esr_recount_wave_statistics($wave_id);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '7',
		], ESR()->course_summary->get_course_summary($course_id));
	}


	public function test_remove_course_summary() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_solo' => 10, 'is_solo' => true, 'pairing_mode' => ESR_Enum_Pairing_Mode::MANUAL]);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		do_action('esr_remove_course_summary', $course_id);

		$this->assertEquals(null, ESR()->course_summary->get_course_summary($course_id));


	}

}
