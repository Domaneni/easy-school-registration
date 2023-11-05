<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESRPaymentTest extends PHPUnit_Framework_TestCase {

	private $base_test;

	private $worker_registration;


	public function __construct() {
		parent::__construct();
		$this->base_test           = new ESR_Base_Test();
		$this->worker_registration = new ESR_Registration_Worker();
	}


	public function setUp(): void {
        parent::setUp();
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	public function test_payment_auto_count() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		global $esr_settings;
		$esr_settings['floating_price_enabled'] = true;

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]             = new stdClass();
		$registration->courses[$course_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$this->assertNull($this->base_test->load_user_payment_by_email('kn@test.test'));

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]             = new stdClass();
		$registration->courses[$course_id]->dancing_as = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$payment1 = $this->base_test->load_user_payment_by_email('kn1@test.test');
		$payment2 = $this->base_test->load_user_payment_by_email('pn1@test.test');

		$this->assertEquals(800, $payment1->to_pay);
		$this->assertNull($payment1->payment);
		$this->assertEquals(800, $payment2->to_pay);
		$this->assertNull($payment2->payment);

		$course2_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);

		unset($registration->courses[$course_id]);

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course2_id]             = new stdClass();
		$registration->courses[$course2_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Petr';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course2_id]             = new stdClass();
		$registration->courses[$course2_id]->dancing_as = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$payment1 = $this->base_test->load_user_payment_by_email('kn1@test.test');
		$payment2 = $this->base_test->load_user_payment_by_email('pn1@test.test');

		$this->assertEquals(1600, $payment1->to_pay);
		$this->assertNull($payment1->payment);
		$this->assertEquals(1600, $payment2->to_pay);
		$this->assertNull($payment2->payment);

		$course3_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);

		unset($registration->courses[$course2_id]);

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course3_id]             = new stdClass();
		$registration->courses[$course3_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Petr';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course3_id]             = new stdClass();
		$registration->courses[$course3_id]->dancing_as = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$payment1 = $this->base_test->load_user_payment_by_email('kn1@test.test');
		$payment2 = $this->base_test->load_user_payment_by_email('pn1@test.test');

		$this->assertEquals(2400, $payment1->to_pay);
		$this->assertNull($payment1->payment);
		$this->assertEquals(2400, $payment2->to_pay);
		$this->assertNull($payment2->payment);

		$course4_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);

		unset($registration->courses[$course3_id]);

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course4_id]             = new stdClass();
		$registration->courses[$course4_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Petr';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course4_id]             = new stdClass();
		$registration->courses[$course4_id]->dancing_as = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$payment1 = $this->base_test->load_user_payment_by_email('kn1@test.test');
		$payment2 = $this->base_test->load_user_payment_by_email('pn1@test.test');

		$this->assertEquals(3200, $payment1->to_pay);
		$this->assertNull($payment1->payment);
		$this->assertEquals(3200, $payment2->to_pay);
		$this->assertNull($payment2->payment);

		$course5_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);


		unset($registration->courses[$course4_id]);

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course5_id]             = new stdClass();
		$registration->courses[$course5_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Petr';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course5_id]             = new stdClass();
		$registration->courses[$course5_id]->dancing_as = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$payment1 = $this->base_test->load_user_payment_by_email('kn1@test.test');
		$payment2 = $this->base_test->load_user_payment_by_email('pn1@test.test');

		$this->assertEquals(4000, $payment1->to_pay);
		$this->assertNull($payment1->payment);
		$this->assertEquals(4000, $payment2->to_pay);
		$this->assertNull($payment2->payment);

		$course6_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);


		unset($registration->courses[$course5_id]);

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course6_id]             = new stdClass();
		$registration->courses[$course6_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Petr';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course6_id]             = new stdClass();
		$registration->courses[$course6_id]->dancing_as = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$payment1 = $this->base_test->load_user_payment_by_email('kn1@test.test');
		$payment2 = $this->base_test->load_user_payment_by_email('pn1@test.test');

		$this->assertEquals(4800, $payment1->to_pay);
		$this->assertNull($payment1->payment);
		$this->assertEquals(4800, $payment2->to_pay);
		$this->assertNull($payment2->payment);

		$course7_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);

		unset($registration->courses[$course6_id]);

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course7_id]             = new stdClass();
		$registration->courses[$course7_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Petr';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course7_id]             = new stdClass();
		$registration->courses[$course7_id]->dancing_as = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$payment1 = $this->base_test->load_user_payment_by_email('kn1@test.test');
		$payment2 = $this->base_test->load_user_payment_by_email('pn1@test.test');

		$this->assertEquals(5600, $payment1->to_pay);
		$this->assertNull($payment1->payment);
		$this->assertEquals(5600, $payment2->to_pay);
		$this->assertNull($payment2->payment);

		$course8_id = $this->base_test->add_course($wave_id, ['price' => 300, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);

		unset($registration->courses[$course7_id]);

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course8_id]             = new stdClass();
		$registration->courses[$course8_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Petr';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course8_id]             = new stdClass();
		$registration->courses[$course8_id]->dancing_as = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$payment1 = $this->base_test->load_user_payment_by_email('kn1@test.test');
		$payment2 = $this->base_test->load_user_payment_by_email('pn1@test.test');

		$this->assertEquals(5900, $payment1->to_pay);
		$this->assertNull($payment1->payment);
		$this->assertEquals(5900, $payment2->to_pay);
		$this->assertNull($payment2->payment);

		$this->base_test->update_course($course8_id, ['wave_id' => $wave_id, 'price' => 500]);

		$payment1 = $this->base_test->load_user_payment_by_email('kn1@test.test');
		$payment2 = $this->base_test->load_user_payment_by_email('pn1@test.test');

		$this->assertEquals(6100, $payment1->to_pay);
		$this->assertNull($payment1->payment);
		$this->assertEquals(6100, $payment2->to_pay);
		$this->assertNull($payment2->payment);
	}


	public function test_get_payment_by_variable_symbol() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		global $esr_settings;
		$esr_settings['floating_price_enabled'] = true;

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]             = new stdClass();
		$registration->courses[$course_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn1@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]             = new stdClass();
		$registration->courses[$course_id]->dancing_as = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$userID1 = $this->base_test->get_user_id_by_email('kn1@test.test');
		$userID2 = $this->base_test->get_user_id_by_email('pn1@test.test');

		$this->assertEquals(null, ESR()->payment->get_payment_by_variable_symbol($wave_id . $this->get_user_id_prefix(0)));

		$this->assertEquals($this->base_test->load_user_payment_by_email('kn1@test.test'), ESR()->payment->get_payment_by_variable_symbol($wave_id . $this->get_user_id_prefix($userID1)));
		$this->assertEquals($this->base_test->load_user_payment_by_email('pn1@test.test'), ESR()->payment->get_payment_by_variable_symbol($wave_id . $this->get_user_id_prefix($userID2)));
	}


	public function test_get_payment_by_registration_variable_symbol() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		global $esr_settings;
		$esr_settings['floating_price_enabled'] = true;

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn2@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]             = new stdClass();
		$registration->courses[$course_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn2@test.test';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]             = new stdClass();
		$registration->courses[$course_id]->dancing_as = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$userID1 = $this->base_test->get_user_id_by_email('kn2@test.test');
		$userID2 = $this->base_test->get_user_id_by_email('pn2@test.test');

		$registration1 = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $userID1)[0];
		$registration2 = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $userID2)[0];

		$this->assertEquals(null, ESR()->payment->get_payment_by_registration_variable_symbol($wave_id . $this->get_user_id_prefix(0) . '0'));

		$this->assertEquals($this->base_test->load_user_payment_by_email('kn2@test.test'), ESR()->payment->get_payment_by_registration_variable_symbol($wave_id . $this->get_user_id_prefix($userID1) . $registration1->reg_position));
		$this->assertEquals($this->base_test->load_user_payment_by_email('pn2@test.test'), ESR()->payment->get_payment_by_registration_variable_symbol($wave_id . $this->get_user_id_prefix($userID2) . $registration2->reg_position));
	}

	private function get_user_id_prefix($user_id) {
		if (strlen($user_id) === 1) {
			return '000' . $user_id;
		} else if (strlen($user_id) === 2) {
			return '00' . $user_id;
		} else if (strlen($user_id) === 3) {
			return '0' . $user_id;
		}

		return $user_id;
	}
}
