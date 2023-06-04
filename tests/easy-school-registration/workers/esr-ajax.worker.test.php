<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESR_Ajax_Worker_Test extends PHPUnit_Framework_TestCase {

	private $base_test;

	private $worker_ajax;

	private $worker_registration;


	public function __construct() {
		parent::__construct();
		$this->base_test           = new ESR_Base_Test();
		$this->worker_ajax         = new ESR_Ajax_Worker();
		$this->worker_registration = new ESR_Registration_Worker();
	}


	public function setUp() {
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}

	public function test_process_add_user_course_null_registration() {
		$this->assertEquals(-1, $this->worker_ajax->process_add_user_course_registration(null));
	}


	public function test_process_add_user_course_registration() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('k.n@easyschoolregistration.com');
		$registration_id   = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]->registration_id;
		$registration_data = ESR()->registration->get_registration($registration_id);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$this->worker_ajax->process_add_user_course_registration($registration_data);
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}


	public function test_remove_user_course_registration_null() {
		$this->assertEquals(-1, $this->worker_ajax->remove_user_course_registration_callback(null));
	}


	public function test_remove_user_course_registration_waiting_leader() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('k.n@easyschoolregistration.com');
		$registration_id   = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]->registration_id;
		$registration_data = ESR()->registration->get_registration($registration_id);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		//Set registration deleted
		$this->assertEquals([
			'status_title'         => ESR()->registration_status->get_title(ESR_Registration_Status::DELETED),
			'partner_registration' => 0,
		], $this->worker_ajax->remove_user_course_registration_callback($registration_data));

		//Registration will be deleted
		$this->assertEquals(true, $this->worker_ajax->remove_registration_forever_callback($registration_data));
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}


	public function test_remove_user_course_registration_waiting_follower() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Sandra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 's.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('s.n@easyschoolregistration.com');
		$registration_id   = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]->registration_id;
		$registration_data = ESR()->registration->get_registration($registration_id);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->waiting_followers);

		//Set registration deleted
		$this->assertEquals([
			'status_title'         => ESR()->registration_status->get_title(ESR_Registration_Status::DELETED),
			'partner_registration' => 0,
		], $this->worker_ajax->remove_user_course_registration_callback($registration_data));

		//Registration will be deleted
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}


	public function test_remove_user_course_registration_confirmed_leader_and_follower() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Sandra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 's.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$user_id          = $this->base_test->get_user_id_by_email('s.n@easyschoolregistration.com');
		$user2_id         = $this->base_test->get_user_id_by_email('k.n@easyschoolregistration.com');
		$registration_id  = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]->registration_id;
		$registration2_id = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user2_id)[0]->registration_id;

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		//Set registration deleted
		$registration_data = ESR()->registration->get_registration($registration_id);
		$this->assertEquals([
			'status_title'         => ESR()->registration_status->get_title(ESR_Registration_Status::DELETED),
			'partner_registration' => $registration2_id,
		], $this->worker_ajax->remove_user_course_registration_callback($registration_data));

		//Registration will be deleted
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$registration_id = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user2_id)[0]->registration_id;

		//Set registration deleted
		$registration_data = ESR()->registration->get_registration($registration_id);
		$this->assertEquals([
			'status_title'         => ESR()->registration_status->get_title(ESR_Registration_Status::DELETED),
			'partner_registration' => 0,
		], $this->worker_ajax->remove_user_course_registration_callback($registration_data));

		//Registration will be deleted
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}


	public function test_remove_registration_forever_solo_confirmed() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('k.n@easyschoolregistration.com');
		$registration_id   = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]->registration_id;
		$registration_data = ESR()->registration->get_registration($registration_id);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(1, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$this->assertEquals(false, $this->worker_ajax->remove_registration_forever_callback(null));

		//Registration should not be deleted
		$this->assertEquals(false, $this->worker_ajax->remove_registration_forever_callback($registration_data));
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(1, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		//Set registration deleted
		$this->worker_ajax->remove_user_course_registration_callback($registration_data);

		//Registration will be deleted
		$this->assertEquals(true, $this->worker_ajax->remove_registration_forever_callback($registration_data));
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}


	public function test_remove_registration_forever_couple_confirmed() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('k.n@easyschoolregistration.com');
		$registration_id   = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]->registration_id;
		$registration_data = ESR()->registration->get_registration($registration_id);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		//Registration should not be deleted
		$this->assertEquals(false, $this->worker_ajax->remove_registration_forever_callback($registration_data));
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$registration->user_info->name    = 'Sandra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 's.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$this->assertEquals(false, $this->worker_ajax->remove_registration_forever_callback($registration_data));

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		//Set registration deleted
		$registration_data = ESR()->registration->get_registration($registration_id);
		$this->worker_ajax->remove_user_course_registration_callback($registration_data);

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$this->assertEquals(true, $this->worker_ajax->remove_registration_forever_callback($registration_data));

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}


	public function test_edit_course_registration_dancing_as() {

		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

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

		$user_id           = $this->base_test->get_user_id_by_email('k.n@easyschoolregistration.com');
		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$registration_id   = $registration_data->registration_id;

		$this->assertNull($registration_data->dancing_with);
		$this->assertNull($registration_data->partner_id);
		$this->assertEquals(ESR_Dancing_As::LEADER, $registration_data->dancing_as);

		$result = $this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::FOLLOWER
		]);

		$this->assertEquals([
			'student'    => [
				'name'  => 'Karel Novak',
				'email' => 'k.n@easyschoolregistration.com'
			],
			'dancing_as' => [
				'id'   => ESR_Dancing_As::FOLLOWER,
				'text' => ESR()->dance_as->get_title(ESR_Dancing_As::FOLLOWER)
			],
		], $result);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '1',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];

		$this->assertNull($registration_data->dancing_with);
		$this->assertNull($registration_data->partner_id);
		$this->assertEquals(ESR_Dancing_As::FOLLOWER, $registration_data->dancing_as);

		$result = $this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::LEADER
		]);

		$this->assertEquals([
			'student'    => [
				'name'  => 'Karel Novak',
				'email' => 'k.n@easyschoolregistration.com'
			],
			'dancing_as' => [
				'id'   => ESR_Dancing_As::LEADER,
				'text' => ESR()->dance_as->get_title(ESR_Dancing_As::LEADER)
			],
		], $result);

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

		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];

		$this->assertNull($registration_data->dancing_with);
		$this->assertNull($registration_data->partner_id);
		$this->assertEquals(ESR_Dancing_As::LEADER, $registration_data->dancing_as);

		$this->worker_ajax->process_add_user_course_registration($registration_data);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '1',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$result = $this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::FOLLOWER
		]);

		$this->assertEquals([
			'student'    => [
				'name'  => 'Karel Novak',
				'email' => 'k.n@easyschoolregistration.com'
			],
			'dancing_as' => [
				'id'   => ESR_Dancing_As::FOLLOWER,
				'text' => ESR()->dance_as->get_title(ESR_Dancing_As::FOLLOWER)
			],
		], $result);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '0',
			'registered_followers' => '1',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));

		$result = $this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::LEADER
		]);

		$this->assertEquals([
			'student'    => [
				'name'  => 'Karel Novak',
				'email' => 'k.n@easyschoolregistration.com'
			],
			'dancing_as' => [
				'id'   => ESR_Dancing_As::LEADER,
				'text' => ESR()->dance_as->get_title(ESR_Dancing_As::LEADER)
			],
		], $result);

		$this->assertEquals((object) [
			'id'                   => '1',
			'course_id'            => $course_id,
			'registered_leaders'   => '1',
			'registered_followers' => '0',
			'registered_solo'      => '0',
			'waiting_leaders'      => '0',
			'waiting_followers'    => '0',
			'waiting_solo'         => '0',
		], ESR()->course_summary->get_course_summary($course_id));
	}


	public function test_edit_course_registration_dancing_with() {

		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Camila';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'cn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course_id]->dancing_with   = 'sn@easyschoolregistration.com';

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('k.n@easyschoolregistration.com');
		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$registration_id   = $registration_data->registration_id;

		$this->assertNull($registration_data->dancing_with);
		$this->assertNull($registration_data->partner_id);
		$this->assertEquals(ESR_Dancing_As::LEADER, $registration_data->dancing_as);

		$result = $this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => 'cn@easyschoolregistration.com',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::LEADER
		]);

		$this->assertEquals([
			'student'      => [
				'name'  => 'Karel Novak',
				'email' => 'k.n@easyschoolregistration.com'
			],
			'dancing_as'   => [
				'id'   => ESR_Dancing_As::LEADER,
				'text' => ESR()->dance_as->get_title(ESR_Dancing_As::LEADER)
			],
			'dancing_with' => [
				'email' => 'cn@easyschoolregistration.com'
			]
		], $result);


		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$user2_id           = $this->base_test->get_user_id_by_email('cn@easyschoolregistration.com');

		$this->assertEquals('cn@easyschoolregistration.com', $registration_data->dancing_with);
		$this->assertEquals($user2_id, $registration_data->partner_id);
		$this->assertEquals(ESR_Dancing_As::LEADER, $registration_data->dancing_as);
	}


	public function test_edit_course_registration_partner() {

		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Camila';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'cn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course_id]->dancing_with   = 'sn@easyschoolregistration.com';

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('k.n@easyschoolregistration.com');
		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$registration_id   = $registration_data->registration_id;

		$this->assertNull($registration_data->dancing_with);
		$this->assertNull($registration_data->partner_id);
		$this->assertEquals(ESR_Dancing_As::LEADER, $registration_data->dancing_as);

		$result = $this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => 'cn@easyschoolregistration.com',
			'dancing_as'      => ESR_Dancing_As::LEADER
		]);

		$this->assertEquals([
			'student'    => [
				'name'  => 'Karel Novak',
				'email' => 'k.n@easyschoolregistration.com'
			],
			'dancing_as' => [
				'id'   => ESR_Dancing_As::LEADER,
				'text' => ESR()->dance_as->get_title(ESR_Dancing_As::LEADER)
			],
			'partner'    => [
				'name'  => 'Camila Novak',
				'email' => 'cn@easyschoolregistration.com'
			]
		], $result);


		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$partner_id        = $this->base_test->get_user_id_by_email('cn@easyschoolregistration.com');

		$this->assertNull($registration_data->dancing_with);
		$this->assertEquals($partner_id, $registration_data->partner_id);
		$this->assertEquals(ESR_Dancing_As::LEADER, $registration_data->dancing_as);
	}


	public function test_edit_course_registration_course_id() {
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::TUESDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('k.n@easyschoolregistration.com');
		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$registration_id   = $registration_data->registration_id;

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::LEADER,
			'course_id'       => $course2_id
		]);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::FOLLOWER,
			'course_id'       => $course_id
		]);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$course3_id = $this->base_test->add_course($wave_id, ['max_solo' => 1, 'is_solo' => true, 'day' => ESR_Enum_Day::WEDNESDAY]);

		$this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::SOLO,
			'course_id'       => $course3_id
		]);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course3_id);
		$this->assertEquals(1, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::LEADER,
			'course_id'       => $course_id
		]);

		$summary = ESR()->course_summary->get_course_summary($course3_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::LEADER,
			'course_id'       => $course2_id
		]);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}


	public function test_edit_course_registration_course_id_pairing() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Camila';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'cn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('cn@easyschoolregistration.com');
		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$registration_id   = $registration_data->registration_id;

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(2, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'cn@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::FOLLOWER,
			'course_id'       => $course_id
		]);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}


	public function test_edit_course_registration_course_id_pairing_2() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Camila';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'cn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('k.n@easyschoolregistration.com');
		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$registration_id   = $registration_data->registration_id;

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(2, $summary->waiting_followers);

		$this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'k.n@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::LEADER,
			'course_id'       => $course_id
		]);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}


	public function test_edit_course_registration_course_id_pairing_3() {
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Camila';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'cn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		unset($registration->courses[$course_id]);
		$registration->courses[$course2_id] = new stdClass();

		$registration->courses[$course2_id]->choose_partner = 0;
		$registration->courses[$course2_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('cn@easyschoolregistration.com');
		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$registration_id   = $registration_data->registration_id;

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->waiting_followers);

		$this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'cn@easyschoolregistration.com',
			'dancing_with'    => '',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::FOLLOWER,
			'course_id'       => $course_id
		]);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}


	public function test_edit_course_registration_course_id_pairing_4() {
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course_id]->dancing_with   = 'cn@easyschoolregistration.com';

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Camila';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'cn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		unset($registration->courses[$course_id]);
		$registration->courses[$course2_id] = new stdClass();

		$registration->courses[$course2_id]->choose_partner = 1;
		$registration->courses[$course2_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course2_id]->dancing_with   = 'k.n@easyschoolregistration.com';

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('cn@easyschoolregistration.com');
		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$registration_id   = $registration_data->registration_id;

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->waiting_followers);

		$this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'cn@easyschoolregistration.com',
			'dancing_with'    => 'k.n@easyschoolregistration.com',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::FOLLOWER,
			'course_id'       => $course_id
		]);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}


	public function test_edit_course_registration_course_id_pairing_5() {
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'k.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course_id]->dancing_with   = 'cn@easyschoolregistration.com';

		$this->worker_registration->process_registration($registration);

		$registration->user_info->name    = 'Camila';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'cn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		unset($registration->courses[$course_id]);
		$registration->courses[$course2_id] = new stdClass();

		$registration->courses[$course2_id]->choose_partner = 0;
		$registration->courses[$course2_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$this->worker_registration->process_registration($registration);

		$user_id           = $this->base_test->get_user_id_by_email('cn@easyschoolregistration.com');
		$registration_data = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0];
		$registration_id   = $registration_data->registration_id;

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->waiting_followers);

		$this->worker_ajax->edit_course_registration_callback([
			'registration_id' => $registration_id,
			'student_email'   => 'cn@easyschoolregistration.com',
			'dancing_with'    => 'k.n@easyschoolregistration.com',
			'partner_email'   => '',
			'dancing_as'      => ESR_Dancing_As::FOLLOWER,
			'course_id'       => $course_id
		]);

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_solo);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->waiting_followers);
	}

}
