<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-discounts/esrd-base.php';

class ESRD_Checkbox_Discount_Worker_Test extends PHPUnit_Framework_TestCase {

	private $esrd_base;

	private $worker_discount;


	public function __construct() {
		parent::__construct();
		$this->esrd_base       = new ESRD_Base();
		$this->worker_discount = new ESRD_Time_Discount_Worker();
	}


	public function setUp() {
		$this->esrd_base->base->delete_all_data();
		$this->esrd_base->base->setUp();
	}


	public function test_solo_registration_with_discount_later() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 1,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 0);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(200, $payment->to_pay);

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 150, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(315, $payment->to_pay);

		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest2@easyschoolregsitration.com');
		$payment = $this->esrd_base->load_user_payment_by_email('unittest2@easyschoolregsitration.com');
		$this->assertEquals(200, $payment->to_pay);
	}


	public function test_solo_registration_with_discount_first() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 1,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(180, $payment->to_pay);

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 150, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 0);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(315, $payment->to_pay);
	}


	public function test_couple_registration_manual_confirm() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 1,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);


		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(180, $payment->to_pay);

		$this->assertEquals(3, $this->esrd_base->base->fetch_users_count());
		$this->assertEquals(1, $this->esrd_base->base->fetch_registrations_count());

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(180, $payment->to_pay);

		$this->assertEquals(3, $this->esrd_base->base->fetch_users_count());
		$this->assertEquals(2, $this->esrd_base->base->fetch_registrations_count());

		$worker_ajax         = new ESR_Ajax_Worker();
		$user_id = $this->esrd_base->base->get_user_id_by_email('unittest@easyschoolregistration.com');
		$worker_ajax->process_add_user_course_registration(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[1]);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(360, $payment->to_pay);
	}


	public function test_couple_registration_manual_confirm2() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 1,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);


		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(180, $payment->to_pay);

		$this->assertEquals(3, $this->esrd_base->base->fetch_users_count());
		$this->assertEquals(1, $this->esrd_base->base->fetch_registrations_count());

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 0);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(180, $payment->to_pay);

		$this->assertEquals(3, $this->esrd_base->base->fetch_users_count());
		$this->assertEquals(2, $this->esrd_base->base->fetch_registrations_count());

		$worker_ajax         = new ESR_Ajax_Worker();
		$user_id = $this->esrd_base->base->get_user_id_by_email('unittest@easyschoolregistration.com');
		$worker_ajax->process_add_user_course_registration(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[1]);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(360, $payment->to_pay);
	}


	public function test_couple_registration_manual_confirm3() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 1,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);


		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 0);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(200, $payment->to_pay);

		$this->assertEquals(3, $this->esrd_base->base->fetch_users_count());
		$this->assertEquals(1, $this->esrd_base->base->fetch_registrations_count());

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(200, $payment->to_pay);

		$this->assertEquals(3, $this->esrd_base->base->fetch_users_count());
		$this->assertEquals(2, $this->esrd_base->base->fetch_registrations_count());

		$worker_ajax         = new ESR_Ajax_Worker();
		$user_id = $this->esrd_base->base->get_user_id_by_email('unittest@easyschoolregistration.com');
		$worker_ajax->process_add_user_course_registration(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[1]);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(360, $payment->to_pay);
	}



	public function test_solo_registration_minus_percentage() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 1,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(180, $payment->to_pay);

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 150, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(315, $payment->to_pay);
	}


	public function test_solo_registration_minus_price() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(190, $payment->to_pay);

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 150, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 0);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(340, $payment->to_pay);
	}


	public function test_solo_registration_final_price() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 3,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(10, $payment->to_pay);

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 150, 'is_solo' => true, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 0);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(10, $payment->to_pay);
	}


	public function test_solo_registration_update_discount_type() {
		$wave_id = $this->esrd_base->base->add_wave();

		$discount_id = $this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 1,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(180, $payment->to_pay);

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 150, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 0);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(315, $payment->to_pay);

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'discount_id'         => $discount_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(340, $payment->to_pay);
	}


	public function test_solo_registration_update_value() {
		$wave_id = $this->esrd_base->base->add_wave();

		$discount_id = $this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(190, $payment->to_pay);

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 150, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 0);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(340, $payment->to_pay);

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'discount_id'         => $discount_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 20,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(330, $payment->to_pay);
	}


	public function test_solo_registration_update_wave_id() {
		$wave_id  = $this->esrd_base->base->add_wave();
		$wave2_id = $this->esrd_base->base->add_wave();

		$discount_id = $this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(190, $payment->to_pay);

		$course2_id = $this->esrd_base->base->add_course($wave2_id, ['price' => 150, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave2_id, $course2_id, ESR_Dancing_As::LEADER, 'unittest2@easyschoolregsitration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(190, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email('unittest2@easyschoolregsitration.com');
		$this->assertEquals(150, $payment->to_pay);

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave2_id,
			'discount_id'         => $discount_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 20,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(200, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email('unittest2@easyschoolregsitration.com');
		$this->assertEquals(130, $payment->to_pay);
	}


	public function test_multiple_solo_registrations() {
		$wave_id  = $this->esrd_base->base->add_wave();
		$wave2_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave2_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 20,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);


		$course_id  = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->esrd_base->base->add_course($wave2_id, ['price' => 150, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);


		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name                          = 'A';
		$registration->user_info->surname                       = 'B';
		$registration->user_info->email                         = 'unittest@easyschoolregistration.com';
		$registration->user_info->phone                         = '1';
		$registration->user_info->{"checkbox_discount_{$wave_id}"}  = 1;
		$registration->user_info->{"checkbox_discount_{$wave2_id}"} = 1;

		$registration->courses[$course_id]  = new stdClass();
		$registration->courses[$course2_id] = new stdClass();

		$registration->courses[$course_id]->dancing_as  = ESR_Dancing_As::LEADER;
		$registration->courses[$course2_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->esrd_base->base->process_registrations($registration);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(190, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(130, $payment->to_pay);
	}


	public function test_remove_discount() {
		$wave_id = $this->esrd_base->base->add_wave();

		$discount_id = $this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 110,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(90, $payment->to_pay);

		do_action('esrd_remove_checkbox_discount', $discount_id);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(200, $payment->to_pay);
	}


	public function test_round_payments() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 1,
			'esrd_discount_value' => 15,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 123, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(104.55, $payment->to_pay);

		global $esr_settings;
		$esr_settings['round_payments'] = true;

		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest2@easyschoolregsitration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest2@easyschoolregsitration.com');
		$this->assertEquals(105, $payment->to_pay);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 131, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest3@easyschoolregsitration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest3@easyschoolregsitration.com');
		$this->assertEquals(111, $payment->to_pay);
	}

}
