<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESR_Registration_Test extends PHPUnit_Framework_TestCase {

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


	public function test_registration_errors() {
		$wave_id = $this->base_test->add_wave();

		$registration = new stdClass();

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('user_info.name', $errors['errors']->errors);
		$this->assertArrayHasKey('user_info.surname', $errors['errors']->errors);
		$this->assertArrayHasKey('user_info.email.required', $errors['errors']->errors);
		$this->assertArrayHasKey('user_info.phone', $errors['errors']->errors);
		$this->assertArrayHasKey('courses.all.empty', $errors['errors']->errors);

		$registration->user_info       = new stdClass();
		$registration->user_info->name = 'A';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('user_info.surname', $errors['errors']->errors);
		$this->assertArrayHasKey('user_info.email.required', $errors['errors']->errors);
		$this->assertArrayHasKey('user_info.phone', $errors['errors']->errors);
		$this->assertArrayHasKey('courses.all.empty', $errors['errors']->errors);

		$registration->user_info->surname = 'B';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('user_info.email.required', $errors['errors']->errors);
		$this->assertArrayHasKey('user_info.phone', $errors['errors']->errors);
		$this->assertArrayHasKey('courses.all.empty', $errors['errors']->errors);

		$registration->user_info->email = 'a';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('user_info.email.invalid', $errors['errors']->errors);
		$this->assertArrayHasKey('user_info.phone', $errors['errors']->errors);
		$this->assertArrayHasKey('courses.all.empty', $errors['errors']->errors);

		$registration->user_info->email = 'unittest@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('user_info.phone', $errors['errors']->errors);
		$this->assertArrayHasKey('courses.all.empty', $errors['errors']->errors);

		$registration->user_info->phone = '1';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('courses.all.empty', $errors['errors']->errors);

		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10]);

		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_with   = 'kamila.novak@';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('courses.' . $course_id . '.email_invalid', $errors['errors']->errors);

		unset($registration->courses[$course_id]);
		$registration->courses[$course_id]                 = new stdClass();
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course_id]->choose_partner = 1;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('courses.' . $course_id . '.email_invalid', $errors['errors']->errors);
		$this->assertArrayHasKey('courses.' . $course_id . '.email_not_filled', $errors['errors']->errors);

		unset($registration->courses[$course_id]);
		$registration->courses[$course_id]                 = new stdClass();
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_with   = 'unittest@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('courses.' . $course_id . '.partner_email', $errors['errors']->errors);

		unset($registration->courses[$course_id]);
		$registration->courses[$course_id]                 = new stdClass();
		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_with   = 'karel.novak@easyschoolregsitration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('courses.' . $course_id . '.dancing_as_invalid', $errors['errors']->errors);
	}


	public function test_new_user_registration() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10]);
		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$this->assertEquals(3, $this->base_test->fetch_users_count());
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
	}


	public function test_solo_registration() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		global $esr_settings;
		$esr_settings['floating_price_enabled'] = true;

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$this->assertEquals(3, $this->base_test->fetch_users_count());
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());

		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_solo);
	}


	public function test_pair_students() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$errors = $this->worker_registration->process_registration($registration);

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

		$errors = $this->worker_registration->process_registration($registration);

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


	public function test_pair_students_more_followers_waiting() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'petra.nov1@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(3, $this->base_test->fetch_users_count());
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$user1_id = $this->base_test->get_user_id_by_email('petra.nov1@easyschoolregistration.com');
		$reg_data = $this->get_user_registration((int) $user1_id, $wave_id);

		$this->assertEquals(ESR_Registration_Status::WAITING, $reg_data->status);
		$this->assertEquals($course_id, $reg_data->course_id);
		$this->assertNull($reg_data->partner_id);
		$this->assertEquals(1, $reg_data->reg_position);

		$registration->user_info->name    = 'Martina';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'martina.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(2, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$user2_id = $this->base_test->get_user_id_by_email('martina.n@easyschoolregistration.com');

		$reg_data = $this->get_user_registration((int) $user2_id, $wave_id);
		$this->assertEquals(ESR_Registration_Status::WAITING, $reg_data->status);
		$this->assertEquals($course_id, $reg_data->course_id);
		$this->assertNull($reg_data->partner_id);
		$this->assertEquals(2, $reg_data->reg_position);

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'karel.n@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(5, $this->base_test->fetch_users_count());
		$this->assertEquals(3, $this->base_test->fetch_registrations_count());
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$user3_id = $this->base_test->get_user_id_by_email('karel.n@easyschoolregistration.com');

		$reg_data = $this->get_user_registration((int) $user3_id, $wave_id);
		$this->assertEquals(ESR_Registration_Status::CONFIRMED, $reg_data->status);
		$this->assertEquals($course_id, $reg_data->course_id);
		$this->assertEquals($user1_id, $reg_data->partner_id);
		$this->assertEquals(3, $reg_data->reg_position);

		$reg_data = $this->get_user_registration((int) $user1_id, $wave_id);
		$this->assertEquals(ESR_Registration_Status::CONFIRMED, $reg_data->status);
		$this->assertEquals($course_id, $reg_data->course_id);
		$this->assertEquals($user3_id, $reg_data->partner_id);
		$this->assertEquals(1, $reg_data->reg_position);

		$reg_data = $this->get_user_registration((int) $user2_id, $wave_id);
		$this->assertEquals(ESR_Registration_Status::WAITING, $reg_data->status);
		$this->assertEquals($course_id, $reg_data->course_id);
		$this->assertNull($reg_data->partner_id);
		$this->assertEquals(2, $reg_data->reg_position);
	}


	public function test_pair_students_by_email() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'karel.no@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course_id]->dancing_with   = 'petra.no@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(3, $this->base_test->fetch_users_count());
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		unset($registration->courses[$course_id]);
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Matina';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'martina.no@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		unset($registration->courses[$course_id]);
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'petra.no@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course_id]->dancing_with   = 'karel.no@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(5, $this->base_test->fetch_users_count());
		$this->assertEquals(3, $this->base_test->fetch_registrations_count());
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);
	}


	public function test_register_with_newsletter() {
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course3_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'karel.no@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course_id]->dancing_with   = 'petra.no@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$user      = get_user_by('email', 'karel.no@easyschoolregistration.com');
		$user_meta = get_user_meta($user->ID);

		$this->assertEquals(false, isset($user_meta['esr-course-registration-newsletter']));

		$registration->user_info->newsletter = true;

		unset($registration->courses[$course_id]);
		$registration->courses[$course2_id]                 = new stdClass();
		$registration->courses[$course2_id]->choose_partner = 1;
		$registration->courses[$course2_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course2_id]->dancing_with   = 'petra.no@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$user_meta = get_user_meta($user->ID);
		$this->assertEquals(true, isset($user_meta['esr-course-registration-newsletter']));
	}


	public function test_register_with_terms() {
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'karel.no@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course_id]->dancing_with   = 'petra.no@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$user      = get_user_by('email', 'karel.no@easyschoolregistration.com');
		$user_meta = get_user_meta($user->ID);

		$this->assertEquals(false, isset($user_meta['esr-course-registration-terms-conditions']));

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name             = 'Karel';
		$registration->user_info->surname          = 'Novak';
		$registration->user_info->email            = 'karel.no@easyschoolregistration.com';
		$registration->user_info->phone            = '1';
		$registration->user_info->terms_conditions = true;

		unset($registration->courses[$course_id]);
		$registration->courses[$course2_id]                 = new stdClass();
		$registration->courses[$course2_id]->choose_partner = 1;
		$registration->courses[$course2_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course2_id]->dancing_with   = 'petra.no@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$user_meta = get_user_meta($user->ID);
		$this->assertEquals(true, isset($user_meta['esr-course-registration-terms-conditions']));
	}


	public function test_pair_students_by_email_wrong_email() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'karel.nov@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course_id]->dancing_with   = 'petra.nov@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);


		$this->assertEquals(3, $this->base_test->fetch_users_count());
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'petra.nov@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course_id]->dancing_with   = 'karel.nov@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);
	}


	public function test_pair_students_by_email_leader_forget_email() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'karel.nova@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(3, $this->base_test->fetch_users_count());
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'petra.nova@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course_id]->dancing_with   = 'karel.nova@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);
	}


	public function test_pair_students_by_email_follower_forget_email() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'karel.novak@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course_id]->dancing_with   = 'petra.novak4@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(3, $this->base_test->fetch_users_count());
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'petra.novak4@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		unset($registration->courses[$course_id]->dancing_with);

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);
	}


	public function test_register_to_full_course() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'karel.novak2@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$errors = $this->worker_registration->process_registration($registration);

		$kn_user_id         = $this->base_test->get_user_id_by_email('karel.novak2@easyschoolregistration.com');
		$kn_registration_id = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $kn_user_id)[0]->registration_id;

		/*$this->assertEquals([
			'registered' => [
				$course_id => [
					'id'        => (string) $course_id,
					'dancingAs' => (string) ESR_Dancing_As::LEADER,
				]
			]
		], $return);*/
		$this->assertEquals(3, $this->base_test->fetch_users_count());
		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$registration->user_info->name    = 'Petra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'petra.novak2@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$errors = $this->worker_registration->process_registration($registration);

		$pn_user_id         = $this->base_test->get_user_id_by_email('petra.novak2@easyschoolregistration.com');
		$pn_registration_id = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $pn_user_id)[0]->registration_id;

		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$registration->user_info->name    = 'John';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'john.novak1@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('courses.' . $course_id . '.full', $errors['errors']->errors);

		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);

		$registration->user_info->name    = 'Maria';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'maria.novak1@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertArrayHasKey('courses.' . $course_id . '.full', $errors['errors']->errors);

		$this->assertEquals(4, $this->base_test->fetch_users_count());
		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_leaders);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_leaders);
		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_followers);
		$this->assertEquals(0, ESR()->course_summary->get_course_summary($course_id)->waiting_followers);
	}


	public function test_register_to_passed_wave() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);
		$this->base_test->update_wave($wave_id, ['is_passed' => true]);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'karel.novak2@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(2, $this->base_test->fetch_users_count());
		$this->assertEquals(0, $this->base_test->fetch_registrations_count());
	}


	public function test_register_partially_full_course_follower() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(0, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'mn1@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course_id]->dancing_with   = 'sn1@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn1@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		unset($registration->courses[$course_id]->dancing_with);

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(2, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$registration->user_info->name    = 'Sandra';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'sn1@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course_id]->dancing_with   = 'mn1@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(3, $this->base_test->fetch_registrations_count());
		$this->assertEquals(false, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$user_id = $this->base_test->get_user_id_by_email('sn1@easyschoolregistration.com');
		$this->worker_ajax->remove_user_course_registration_callback(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]);

		$this->assertEquals(3, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$registration->user_info->name    = 'Alena';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'an1@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		unset($registration->courses[$course_id]->dancing_with);

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(4, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(1, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$registration->user_info->name    = 'Barbora';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'bn1@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course_id]->dancing_with   = 'mn1@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(5, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(2, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);
	}


	public function test_confirmed_removed_registration() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(0, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'mn2@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);
		$this->assertEquals(0, $summary->waiting_solo);

		$registration->user_info->name    = 'Kamila';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn2@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(false, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		//remove registrations
		$user_id = $this->base_test->get_user_id_by_email('mn2@easyschoolregistration.com');
		$this->worker_ajax->remove_user_course_registration_callback(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]);

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$user_id = $this->base_test->get_user_id_by_email('kn2@easyschoolregistration.com');
		$this->worker_ajax->remove_user_course_registration_callback(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]);

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		//confirm registrations
		$user_id = $this->base_test->get_user_id_by_email('mn2@easyschoolregistration.com');
		$this->worker_ajax->process_add_user_course_registration(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]);

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$user_id = $this->base_test->get_user_id_by_email('kn2@easyschoolregistration.com');
		$this->worker_ajax->process_add_user_course_registration(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]);

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(false, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);
	}


	public function test_register_partially_full_course_leader() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['max_leaders' => 1, 'max_followers' => 1, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(0, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Barbora';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'bn3@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		$registration->courses[$course_id]->dancing_with   = 'mn3@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(1, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$registration->user_info->name    = 'Alena';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'an3@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		unset($registration->courses[$course_id]->dancing_with);

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(2, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$registration->user_info->name    = 'Martin';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'mn3@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course_id]->dancing_with   = 'bn3@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(3, $this->base_test->fetch_registrations_count());
		$this->assertEquals(false, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(1, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(1, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$user_id = $this->base_test->get_user_id_by_email('mn3@easyschoolregistration.com');
		$this->worker_ajax->remove_user_course_registration_callback(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]);

		$this->assertEquals(3, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(1, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$registration->user_info->name    = 'Karel';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'kn3@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		unset($registration->courses[$course_id]->dancing_with);

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(4, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(1, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(1, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);

		$registration->user_info->name    = 'Richard';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'rn3@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 1;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::LEADER;
		$registration->courses[$course_id]->dancing_with   = 'bn3@easyschoolregistration.com';

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(5, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_leader_registration_enabled($course_id));
		$this->assertEquals(false, ESR()->dance_as->is_followers_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(2, $summary->waiting_leaders);
		$this->assertEquals(1, $summary->registered_followers);
		$this->assertEquals(1, $summary->waiting_followers);
		$this->assertEquals(0, $summary->registered_solo);
	}


	public function test_get_registrations_by_wave() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		global $esr_settings;
		$esr_settings['floating_price_enabled'] = true;

		$this->assertEquals(0, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_solo_registration_enabled($course_id));

		$this->assertEquals([], ESR()->registration->get_registrations_by_wave($wave_id));

		$registration                      = new stdClass();
		$registration->user_info           = new stdClass();
		$registration->courses             = [];
		$registration->courses[$course_id] = new stdClass();

		$registration->user_info->name    = 'Barbora';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'bn4@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(1, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_solo_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(1, $summary->registered_solo);

		$this->assertEquals(1, count(ESR()->registration->get_registrations_by_wave($wave_id)));

		$registration->user_info->name    = 'Alena';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'an4@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course_id]->choose_partner = 0;
		$registration->courses[$course_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_solo_registration_enabled($course_id));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(2, $summary->registered_solo);

		$this->assertEquals(2, count(ESR()->registration->get_registrations_by_wave($wave_id)));

		$wave2_id   = $this->base_test->add_wave();
		$course2_id = $this->base_test->add_course($wave2_id, ['is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->assertEquals(2, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course_id));
		$this->assertEquals(true, ESR()->dance_as->is_solo_registration_enabled($course_id));
		$this->assertEquals(true, ESR()->course->is_course_enabled($course2_id));
		$this->assertEquals(true, ESR()->dance_as->is_solo_registration_enabled($course2_id));

		$this->assertEquals(2, count(ESR()->registration->get_registrations_by_wave($wave_id)));
		$this->assertEquals([], ESR()->registration->get_registrations_by_wave($wave2_id));

		$registration->user_info->name    = 'Barbora';
		$registration->user_info->surname = 'Novak';
		$registration->user_info->email   = 'bn4@easyschoolregistration.com';
		$registration->user_info->phone   = '1';

		$registration->courses[$course2_id]                 = new stdClass();
		$registration->courses[$course2_id]->choose_partner = 0;
		$registration->courses[$course2_id]->dancing_as     = ESR_Dancing_As::FOLLOWER;
		unset($registration->courses[$course_id]);

		$errors = $this->worker_registration->process_registration($registration);

		$this->assertEquals(3, $this->base_test->fetch_registrations_count());
		$this->assertEquals(true, ESR()->course->is_course_enabled($course2_id));
		$this->assertEquals(true, ESR()->dance_as->is_solo_registration_enabled($course2_id));

		$this->assertEquals(2, count(ESR()->registration->get_registrations_by_wave($wave_id)));
		$this->assertEquals(1, count(ESR()->registration->get_registrations_by_wave($wave2_id)));

		$summary = ESR()->course_summary->get_course_summary($course_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(2, $summary->registered_solo);

		$summary = ESR()->course_summary->get_course_summary($course2_id);
		$this->assertEquals(0, $summary->registered_leaders);
		$this->assertEquals(0, $summary->waiting_leaders);
		$this->assertEquals(0, $summary->registered_followers);
		$this->assertEquals(0, $summary->waiting_followers);
		$this->assertEquals(1, $summary->registered_solo);
	}


	public function test_registration_position_generation() {
		$wave_id   = $this->base_test->add_wave();
		$course_id = $this->base_test->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com');
		$reg_data = $this->get_user_registration_by_course($course_id, (int) $this->base_test->get_user_id_by_email('unittest@easyschoolregistration.com'), $wave_id);
		$this->assertEquals(1, $reg_data->reg_position);

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest2@easyschoolregistration.com');
		$reg_data_2 = $this->get_user_registration_by_course($course_id, (int) $this->base_test->get_user_id_by_email('unittest2@easyschoolregistration.com'), $wave_id);
		$this->assertEquals(2, $reg_data_2->reg_position);

		$this->worker_ajax->remove_user_course_registration_callback(ESR()->registration->get_registration($reg_data_2->id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest3@easyschoolregistration.com');
		$reg_data_3 = $this->get_user_registration_by_course($course_id, (int) $this->base_test->get_user_id_by_email('unittest3@easyschoolregistration.com'), $wave_id);
		$this->assertEquals(3, $reg_data_3->reg_position);

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest4@easyschoolregistration.com');
		$reg_data_4 = $this->get_user_registration_by_course($course_id, (int) $this->base_test->get_user_id_by_email('unittest4@easyschoolregistration.com'), $wave_id);
		$this->assertEquals(4, $reg_data_4->reg_position);

		$this->worker_ajax->remove_user_course_registration_callback(ESR()->registration->get_registration($reg_data_4->id));
		$this->worker_ajax->remove_registration_forever_callback(ESR()->registration->get_registration($reg_data_4->id));

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest5@easyschoolregistration.com');
		$reg_data_5 = $this->get_user_registration_by_course($course_id, (int) $this->base_test->get_user_id_by_email('unittest5@easyschoolregistration.com'), $wave_id);
		$this->assertEquals(4, $reg_data_5->reg_position);

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest6@easyschoolregistration.com');
		$reg_data_6 = $this->get_user_registration_by_course($course_id, (int) $this->base_test->get_user_id_by_email('unittest6@easyschoolregistration.com'), $wave_id);
		$this->assertEquals(5, $reg_data_6->reg_position);

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest7@easyschoolregistration.com');
		$reg_data_7 = $this->get_user_registration_by_course($course_id, (int) $this->base_test->get_user_id_by_email('unittest7@easyschoolregistration.com'), $wave_id);
		$this->assertEquals(6, $reg_data_7->reg_position);
	}


	public function test_free_solo_registration() {
		$wave_id    = $this->base_test->add_wave();
		$course_id  = $this->base_test->add_course($wave_id, ['is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->base_test->add_course($wave_id, ['is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');

		$this->assertEquals(800, $payment->to_pay);
		$this->assertNull($payment->payment);

		$this->base_test->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER);

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');

		$this->assertEquals(1600, $payment->to_pay);
		$this->assertNull($payment->payment);

		$userID        = $this->base_test->get_user_id_by_email('unittest@easyschoolregistration.com');
		$registration1 = $this->get_user_registration_by_course($course_id, $userID, $wave_id);
		$registration2 = $this->get_user_registration_by_course($course2_id, $userID, $wave_id);

		apply_filters('esr_set_free_registration_value', [
			'registration_id'   => $registration1->id,
			'free_registration_value' => 1
		]);

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');

		$this->assertEquals(800, $payment->to_pay);
		$this->assertNull($payment->payment);

		apply_filters('esr_set_free_registration_value', [
			'registration_id'   => $registration1->id,
			'free_registration_value' => 0
		]);

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');

		$this->assertEquals(1600, $payment->to_pay);
		$this->assertNull($payment->payment);

		apply_filters('esr_set_free_registration_value', [
			'registration_id'   => $registration1->id,
			'free_registration_value' => 1
		]);

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');

		$this->assertEquals(800, $payment->to_pay);
		$this->assertNull($payment->payment);

		apply_filters('esr_set_free_registration_value', [
			'registration_id'   => $registration2->id,
			'free_registration_value' => 1
		]);

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');

		$this->assertNull($payment);
	}


	private function get_user_registration($user_id, $wave_id) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT * FROM (SELECT c.*, @row_number:=CASE WHEN @wave_no = c.wave_id THEN @row_number + 1 ELSE 1 END AS reg_position, @wave_no:=c.wave_id as WaveId
FROM ( 
SELECT cr.*, cd.wave_id 
  FROM {$wpdb->prefix}esr_course_registration AS cr
  JOIN {$wpdb->prefix}esr_course_data AS cd ON cd.id = cr.course_id
 WHERE wave_id = %d
ORDER BY cr.id) AS c, (SELECT @wave_no:=0,@row_number:=0) as n) AS r WHERE r.user_id = %d", [$wave_id, $user_id]));
	}


	private function get_user_registration_by_course($course_id, $user_id, $wave_id) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT * FROM (SELECT c.*, @row_number:=CASE WHEN @wave_no = c.wave_id THEN @row_number + 1 ELSE 1 END AS reg_position, @wave_no:=c.wave_id as WaveId
FROM ( 
SELECT cr.*, cd.wave_id
  FROM {$wpdb->prefix}esr_course_registration AS cr
  JOIN {$wpdb->prefix}esr_course_data AS cd ON cd.id = cr.course_id
 WHERE wave_id = %d
ORDER BY cr.id) AS c, (SELECT @wave_no:=0,@row_number:=0) as n) AS r WHERE r.course_id = %d AND r.user_id = %d", [$wave_id, $course_id, $user_id]));
	}


	private function load_user_payment_by_email($user_email) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT up.* FROM {$wpdb->prefix}esr_user_payment AS up JOIN {$wpdb->users} AS u ON u.ID = up.user_id AND u.user_email = %s", [$user_email]));
	}
}
