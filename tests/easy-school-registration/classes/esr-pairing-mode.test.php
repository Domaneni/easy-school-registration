<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESR_Pairing_Mode_Test extends PHPUnit_Framework_TestCase
{

	private $base_test;

	private $worker_ajax;

	private $worker_registration;


	public function __construct()
	{
		parent::__construct();
		$this->base_test = new ESR_Base_Test();
		$this->worker_ajax = new ESR_Ajax_Worker();
		$this->worker_registration = new ESR_Registration_Worker();
	}


	public function setUp()
	{
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	public function test_pairing_mode_settings_data()
	{
		$this->assertEquals([
			ESR_Enum_Pairing_Mode::AUTOMATIC => ESR()->pairing_mode->get_title(ESR_Enum_Pairing_Mode::AUTOMATIC),
			ESR_Enum_Pairing_Mode::MANUAL => ESR()->pairing_mode->get_title(ESR_Enum_Pairing_Mode::MANUAL),
			ESR_Enum_Pairing_Mode::CONFIRM_ALL => ESR()->pairing_mode->get_title(ESR_Enum_Pairing_Mode::CONFIRM_ALL),
		], ESR()->pairing_mode->get_items_for_settings());
	}


	public function test_manual_pairing_mode()
	{
		$wave_id = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['pairing_mode' => ESR_Enum_Pairing_Mode::MANUAL, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$this->assertEquals(3, $this->base_test->fetch_users_count());
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$this->assertNull($this->load_user_payment_by_email('kn@easyschoolregistration.com'));

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$this->assertNull($this->load_user_payment_by_email('kn@easyschoolregistration.com'));
		$this->assertNull($this->load_user_payment_by_email('pn@easyschoolregistration.com'));
	}


	public function test_manual_pairing_mode_solo_course()
	{
		$wave_id = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['is_solo' => true, 'pairing_mode' => ESR_Enum_Pairing_Mode::MANUAL, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(3, $this->base_test->fetch_users_count());
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->waiting_solo);

		$this->assertNull($this->load_user_payment_by_email('kn@easyschoolregistration.com'));

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(2, $summary->waiting_solo);

		$this->assertNull($this->load_user_payment_by_email('kn@easyschoolregistration.com'));
		$this->assertNull($this->load_user_payment_by_email('pn@easyschoolregistration.com'));

		$user_id = $this->base_test->get_user_id_by_email('kn@easyschoolregistration.com');
		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$this->worker_ajax->process_add_user_course_registration($registration_data);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(1, $summary->registered_solo);
		$this->assertEquals(1, $summary->waiting_solo);
	}


	public function test_confirm_all_pairing_mode()
	{
		$wave_id = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['pairing_mode' => ESR_Enum_Pairing_Mode::CONFIRM_ALL, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$this->assertEquals(3, $this->base_test->fetch_users_count());
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$payment1 = $this->load_user_payment_by_email('kn@easyschoolregistration.com');

		$this->assertEquals(800, $payment1->to_pay);
		$this->assertNull($payment1->payment);

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'pn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$payment1 = $this->load_user_payment_by_email('kn@easyschoolregistration.com');
		$payment2 = $this->load_user_payment_by_email('pn@easyschoolregistration.com');

		$this->assertEquals(800, $payment1->to_pay);
		$this->assertNull($payment1->payment);
		$this->assertEquals(800, $payment2->to_pay);
		$this->assertNull($payment2->payment);
	}


	public function test_is_solo_manual()
	{
		$this->assertEquals(true, ESR()->pairing_mode->is_solo_manual(ESR_Enum_Pairing_Mode::MANUAL));
		$this->assertEquals(false, ESR()->pairing_mode->is_solo_manual(ESR_Enum_Pairing_Mode::CONFIRM_ALL));
		$this->assertEquals(false, ESR()->pairing_mode->is_solo_manual(ESR_Enum_Pairing_Mode::AUTOMATIC));
	}


	private function load_user_payment_by_email($user_email)
	{
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT up.* FROM {$wpdb->prefix}esr_user_payment AS up
							   JOIN $wpdb->users AS u ON u.ID = up.user_id AND u.user_email = %s", [$user_email]));
	}
}
