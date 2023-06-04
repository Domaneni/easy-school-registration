<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESR_Add_Over_Limit_Test extends PHPUnit_Framework_TestCase {

	private $base_test;


	public function __construct() {
		parent::__construct();
		$this->base_test   = new ESR_Base_Test();
	}


	public function setUp() {
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	public function test_add_leader() {
		$worker_aol = new ESR_Add_Over_Limit_Worker();

		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$worker_aol->process_form([
			'esr_add_over_limit_submit' => TRUE,
			'esr_leader_email' => 'l@aol.cz',
			'esr_leader_name' => 'John',
			'esr_leader_surname' => 'Max',
			'esr_course_id' => $course_id
		]);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());

		$payment = $this->load_user_payment_by_email('l@aol.cz');

		$this->assertEquals(800, $payment->to_pay);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
	}


	public function test_add_follower() {
		$worker_aol = new ESR_Add_Over_Limit_Worker();

		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$worker_aol->process_form([
			'esr_add_over_limit_submit' => TRUE,
			'esr_follower_email' => 'f@aol.cz',
			'esr_follower_name' => 'Maria',
			'esr_follower_surname' => 'Max',
			'esr_course_id' => $course_id
		]);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());

		$payment = $this->load_user_payment_by_email('f@aol.cz');

		$this->assertEquals(800, $payment->to_pay);
		$this->assertNull($payment->payment);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
	}


	public function test_add_solo_as_leader() {
		$worker_aol = new ESR_Add_Over_Limit_Worker();

		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_solo' => 10, 'is_solo' => true, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$worker_aol->process_form([
			'esr_add_over_limit_submit' => TRUE,
			'esr_leader_email' => 'l@aol.cz',
			'esr_leader_name' => 'John',
			'esr_leader_surname' => 'Max',
			'esr_course_id' => $course_id
		]);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());

		$payment = $this->load_user_payment_by_email('l@aol.cz');

		$this->assertEquals(800, $payment->to_pay);
		$this->assertNull($payment->payment);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_solo);
	}


	public function test_add_solo_as_follower() {
		$worker_aol = new ESR_Add_Over_Limit_Worker();

		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_solo' => 10, 'is_solo' => true, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$worker_aol->process_form([
			'esr_add_over_limit_submit' => TRUE,
			'esr_follower_email' => 'f@aol.cz',
			'esr_follower_name' => 'Maria',
			'esr_follower_surname' => 'Max',
			'esr_course_id' => $course_id
		]);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());

		$payment = $this->load_user_payment_by_email('f@aol.cz');

		$this->assertEquals(800, $payment->to_pay);
		$this->assertNull($payment->payment);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_solo);
	}


	public function test_add_couple() {
		$worker_aol = new ESR_Add_Over_Limit_Worker();

		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$worker_aol->process_form([
			'esr_add_over_limit_submit' => TRUE,
			'esr_leader_email' => 'l@aol.cz',
			'esr_leader_name' => 'John',
			'esr_leader_surname' => 'Max',
			'esr_follower_email' => 'f@aol.cz',
			'esr_follower_name' => 'Maria',
			'esr_follower_surname' => 'Max',
			'esr_course_id' => $course_id
		]);

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());

		$payment1 = $this->load_user_payment_by_email('f@aol.cz');
		$payment2 = $this->load_user_payment_by_email('l@aol.cz');

		$this->assertEquals(800, $payment1->to_pay);
		$this->assertEquals(800, $payment2->to_pay);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_followers);

		$course_id = $this->base_test->add_course($wave_id, ['is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$worker_aol->process_form([
			'esr_add_over_limit_submit' => TRUE,
			'esr_leader_email' => 'l@aol.cz',
			'esr_leader_name' => 'John',
			'esr_leader_surname' => 'Max',
			'esr_follower_email' => 'f@aol.cz',
			'esr_follower_name' => 'Maria',
			'esr_follower_surname' => 'Max',
			'esr_course_id' => $course_id
		]);

		$this->assertEquals(4, $this->base_test->fetch_registrations_count());

		$payment1 = $this->load_user_payment_by_email('f@aol.cz');
		$payment2 = $this->load_user_payment_by_email('l@aol.cz');

		$this->assertEquals(1600, $payment1->to_pay);
		$this->assertEquals(1600, $payment2->to_pay);
		$this->assertEquals(2, ESR()->course_summary->get_course_summary($course_id)->registered_solo);
	}


	private function load_user_payment_by_email($user_email) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT up.* FROM {$wpdb->prefix}esr_user_payment AS up
							   JOIN $wpdb->users AS u ON u.ID = up.user_id AND u.user_email = %s", [$user_email]));
	}

}
