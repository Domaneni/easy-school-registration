<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-discounts/esrd-base.php';

class ESRDDiscountWorkerTest extends PHPUnit_Framework_TestCase {

	private $esrd_base;

	private $worker_discount;


	public function __construct() {
		parent::__construct();
		$this->esrd_base       = new ESRD_Base();
		$this->worker_discount = new ESRD_Discount_Worker();
	}


    public function setUp(): void
    {
        parent::setUp();
        $this->esrd_base->delete_all_data();
        $this->esrd_base->base->setUp();
    }


	public function test_get_course_data() {
		$wave_id = $this->esrd_base->base->add_wave();

		$discount_id = $this->esrd_base->add_discount([$wave_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['1', '1', '2'],
			'esrd_disc_cond_count'              => ['1', '2', '2'],
			'esrd_disc_price'                   => ['800', '1300', '1600']
		]);

		$this->assertEquals([$wave_id], ESRD()->discount->esrd_load_discount_waves($discount_id));
	}


	public function test_update_waves_discount_data() {
		$wave_id = $this->esrd_base->base->add_wave();

		$discount_id = $this->esrd_base->add_discount([$wave_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['1', '1', '2'],
			'esrd_disc_cond_count'              => ['1', '2', '2'],
			'esrd_disc_price'                   => ['800', '1300', '1600']
		]);

		$wave2_id = $this->esrd_base->base->add_wave();

		// Add second wave to discount
		$discount_id = $this->esrd_base->add_discount([$wave_id, $wave2_id], [
			'discount_id'                       => $discount_id,
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['1', '1', '2'],
			'esrd_disc_cond_count'              => ['1', '2', '2'],
			'esrd_disc_price'                   => ['800', '1300', '1600']
		]);

		$this->assertEquals([$wave_id, $wave2_id], ESRD()->discount->esrd_load_discount_waves($discount_id));

		// Delete wave from discount
		$discount_id = $this->esrd_base->add_discount([$wave2_id], [
			'discount_id'                       => $discount_id,
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['1', '1', '2'],
			'esrd_disc_cond_count'              => ['1', '2', '2'],
			'esrd_disc_price'                   => ['800', '1300', '1600']
		]);

		$this->assertEquals([$wave2_id], ESRD()->discount->esrd_load_discount_waves($discount_id));
	}


	public function test_solo_registration() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_discount([$wave_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '1',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['4'],
			'esrd_disc_cond_count'              => ['2'],
			'esrd_disc_price'                   => ['5']
		]);


		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 20, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(20, $payment->to_pay);

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 20, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(35, $payment->to_pay);
	}


	public function test_solo_registration_multiple_discounts() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_discount([$wave_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '1',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['4'],
			'esrd_disc_cond_count'              => ['1'],
			'esrd_disc_price'                   => ['50']
		]);


		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(750, $payment->to_pay);


		$discount_id = $this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-1 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+3 hour')),
					'value' => '10'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('+5 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+7 hour')),
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(670, $payment->to_pay);
	}


	public function test_solo_multiple_registrations() {
		$wave_id  = $this->esrd_base->base->add_wave();
		$wave2_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_discount([$wave_id, $wave2_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '1',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['4'],
			'esrd_disc_cond_count'              => ['1'],
			'esrd_disc_price'                   => ['50']
		]);


		$course_id  = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->esrd_base->base->add_course($wave2_id, ['price' => 100, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name                              = 'A';
		$registration->user_info->surname                           = 'B';
		$registration->user_info->email                             = 'unittest@easyschoolregistration.com';
		$registration->user_info->phone                             = '1';
		$registration->user_info->{"checkbox_discount_{$wave_id}"}  = 1;
		$registration->user_info->{"checkbox_discount_{$wave2_id}"} = 1;

		$registration->courses[$course_id]  = new stdClass();
		$registration->courses[$course2_id] = new stdClass();

		$registration->courses[$course_id]->dancing_as  = ESR_Dancing_As::LEADER;
		$registration->courses[$course2_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->esrd_base->base->process_registrations($registration);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(125, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(125, $payment->to_pay);
	}


	public function test_solo_multiple_registrations_multiple_discounts() {
		$wave_id  = $this->esrd_base->base->add_wave();
		$wave2_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_discount([$wave_id, $wave2_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '1',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['4'],
			'esrd_disc_cond_count'              => ['1'],
			'esrd_disc_price'                   => ['50']
		]);

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-1 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+4 hour')),
					'value' => '10'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('+5 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+7 hour')),
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$course_id  = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->esrd_base->base->add_course($wave2_id, ['price' => 100, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name                              = 'A';
		$registration->user_info->surname                           = 'B';
		$registration->user_info->email                             = 'unittest@easyschoolregistration.com';
		$registration->user_info->phone                             = '1';
		$registration->user_info->{"checkbox_discount_{$wave_id}"}  = 1;
		$registration->user_info->{"checkbox_discount_{$wave2_id}"} = 1;

		$registration->courses[$course_id]  = new stdClass();
		$registration->courses[$course2_id] = new stdClass();

		$registration->courses[$course_id]->dancing_as  = ESR_Dancing_As::LEADER;
		$registration->courses[$course2_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->esrd_base->base->process_registrations($registration);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(115, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(115, $payment->to_pay);

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave2_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-1 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+4 hour')),
					'value' => '50'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('+5 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+7 hour')),
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(90, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(90, $payment->to_pay);

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

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(80, $payment->to_pay);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(70, $payment->to_pay);

		$userID1 = $this->esrd_base->base->get_user_id_by_email('unittest@easyschoolregistration.com');
		$registration1 = ESR()->registration->get_registrations_by_wave_and_user($wave_id, $userID1)[0];

		apply_filters('esr_set_free_registration_value', [
			'registration_id'   => $registration1->id,
			'free_registration_value' => 1
		]);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(null, $payment);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(0, $payment->to_pay);
	}


	public function test_discount_by_group() {
		$wave_id = $this->esrd_base->base->add_wave();
		global $esr_settings;
		$esr_settings["groups"] = [
			0 => "Basic",
			1 => "Extra"
		];

		$this->esrd_base->add_discount([$wave_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '3',
			'esrd_disc_cond_operator'           => ['1', '1', '2'],
			'esrd_disc_cond_count'              => ['1', '2', '2'],
			'esrd_disc_price'                   => ['800', '1300', '1600']
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(800, $payment->to_pay);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(1600, $payment->to_pay);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(2100, $payment->to_pay);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(2600, $payment->to_pay);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(2900, $payment->to_pay);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(2900, $payment->to_pay);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(3200, $payment->to_pay);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(3200, $payment->to_pay);

	}


	public function test_group_discount_with_checkbox() {
		$wave_id = $this->esrd_base->base->add_wave();
		$wave2_id = $this->esrd_base->base->add_wave();

		global $esr_settings;
		$esr_settings["groups"] = [
			0 => "Basic",
			1 => "Extra"
		];

		$this->esrd_base->add_discount([$wave_id, $wave2_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '1',
			'esrd_disc_cond_operator'           => ['1', '1', '1'],
			'esrd_disc_cond_count'              => ['2', '3', '4'],
			'esrd_disc_price'                   => ['25', '33', '40']
		]);

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 10,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER, 'unittest@easyschoolregistration.com', 1);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(790, $payment->to_pay);
	}


	public function test_multiple_waves_multiple_discounts() {
		$wave_id  = $this->esrd_base->base->add_wave();
		$wave2_id = $this->esrd_base->base->add_wave();
		$wave3_id = $this->esrd_base->base->add_wave();
		$wave4_id = $this->esrd_base->base->add_wave();
		$wave5_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_discount([$wave_id, $wave2_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '1',
			'esrd_disc_cond_operator'           => ['1', '1', '1', '1'],
			'esrd_disc_cond_count'              => ['2', '3', '4', '5'],
			'esrd_disc_price'                   => ['10', '15', '20', '25']
		]);

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '2',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-1 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+3 hour')),
					'value' => '10'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('+5 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+7 hour')),
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);


		$this->esrd_base->add_discount([$wave3_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '1',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['1', '4'],
			'esrd_disc_cond_count'              => ['1', '2'],
			'esrd_disc_price'                   => ['100', '150']
		]);

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave4_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-1 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+3 hour')),
					'value' => '50'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('+5 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+7 hour')),
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave_id,
			'esrd_disc_cond_how'  => 1,
			'esrd_discount_value' => 20,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave2_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 80,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave3_id,
			'esrd_disc_cond_how'  => 2,
			'esrd_discount_value' => 75,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		$this->esrd_base->add_checkbox_discount([
			'esrd_wave'           => $wave4_id,
			'esrd_disc_cond_how'  => 3,
			'esrd_discount_value' => 750,
			'esrd_discount_text'  => 'Checkbox discount text',
		]);

		global $esr_settings;
		$esr_settings["groups"] = [
			0 => "Basic",
			1 => "Extra"
		];

		$course_id  = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => false, 'group_id' => 0, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course3_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => false, 'group_id' => 0, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course4_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => false, 'group_id' => 0, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$course5_id  = $this->esrd_base->base->add_course($wave2_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course6_id  = $this->esrd_base->base->add_course($wave2_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course7_id = $this->esrd_base->base->add_course($wave2_id, ['price' => 800, 'is_solo' => false, 'group_id' => 0, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course8_id = $this->esrd_base->base->add_course($wave2_id, ['price' => 800, 'is_solo' => false, 'group_id' => 0, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$course9_id  = $this->esrd_base->base->add_course($wave3_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course10_id  = $this->esrd_base->base->add_course($wave3_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course11_id = $this->esrd_base->base->add_course($wave3_id, ['price' => 800, 'is_solo' => false, 'group_id' => 0, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course12_id = $this->esrd_base->base->add_course($wave3_id, ['price' => 800, 'is_solo' => false, 'group_id' => 0, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$course13_id  = $this->esrd_base->base->add_course($wave4_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course14_id  = $this->esrd_base->base->add_course($wave4_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course15_id = $this->esrd_base->base->add_course($wave4_id, ['price' => 800, 'is_solo' => false, 'group_id' => 0, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course16_id = $this->esrd_base->base->add_course($wave4_id, ['price' => 800, 'is_solo' => false, 'group_id' => 0, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$course17_id  = $this->esrd_base->base->add_course($wave5_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course18_id  = $this->esrd_base->base->add_course($wave5_id, ['price' => 800, 'is_solo' => true, 'group_id' => 1, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course19_id = $this->esrd_base->base->add_course($wave5_id, ['price' => 800, 'is_solo' => false, 'group_id' => 0, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course20_id = $this->esrd_base->base->add_course($wave5_id, ['price' => 800, 'is_solo' => false, 'group_id' => 0, 'max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name                              = 'A';
		$registration->user_info->surname                           = 'B';
		$registration->user_info->email                             = 'unittest@easyschoolregistration.com';
		$registration->user_info->phone                             = '1';
		$registration->user_info->{"checkbox_discount_{$wave_id}"}  = 1;
		$registration->user_info->{"checkbox_discount_{$wave2_id}"} = 1;
		$registration->user_info->{"checkbox_discount_{$wave3_id}"} = 0;
		$registration->user_info->{"checkbox_discount_{$wave4_id}"} = 1;

		$registration->courses[$course_id]  = new stdClass();
		$registration->courses[$course2_id] = new stdClass();
		$registration->courses[$course3_id] = new stdClass();
		$registration->courses[$course4_id] = new stdClass();
		$registration->courses[$course5_id] = new stdClass();
		$registration->courses[$course6_id] = new stdClass();
		$registration->courses[$course7_id] = new stdClass();
		$registration->courses[$course8_id] = new stdClass();
		$registration->courses[$course9_id] = new stdClass();
		$registration->courses[$course10_id] = new stdClass();
		$registration->courses[$course11_id] = new stdClass();
		$registration->courses[$course12_id] = new stdClass();
		$registration->courses[$course13_id] = new stdClass();
		$registration->courses[$course14_id] = new stdClass();
		$registration->courses[$course15_id] = new stdClass();
		$registration->courses[$course16_id] = new stdClass();
		$registration->courses[$course17_id] = new stdClass();
		$registration->courses[$course18_id] = new stdClass();
		$registration->courses[$course19_id] = new stdClass();
		$registration->courses[$course20_id] = new stdClass();

		$registration->courses[$course2_id]->dancing_as = ESR_Dancing_As::LEADER;
		$registration->courses[$course3_id]->dancing_as = ESR_Dancing_As::LEADER;
		$registration->courses[$course4_id]->dancing_as = ESR_Dancing_As::LEADER;
		$registration->courses[$course7_id]->dancing_as = ESR_Dancing_As::LEADER;
		$registration->courses[$course8_id]->dancing_as = ESR_Dancing_As::LEADER;
		$registration->courses[$course11_id]->dancing_as = ESR_Dancing_As::LEADER;
		$registration->courses[$course12_id]->dancing_as = ESR_Dancing_As::LEADER;
		$registration->courses[$course15_id]->dancing_as = ESR_Dancing_As::LEADER;
		$registration->courses[$course16_id]->dancing_as = ESR_Dancing_As::LEADER;
		$registration->courses[$course19_id]->dancing_as = ESR_Dancing_As::LEADER;
		$registration->courses[$course20_id]->dancing_as = ESR_Dancing_As::LEADER;

		$this->esrd_base->base->process_registrations($registration);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(812.6, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(935.75, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(1450, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave4_id);
		$this->assertEquals(750, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave5_id);
		$this->assertEquals(1600, $payment->to_pay);


		$registration2            = new stdClass();
		$registration2->user_info = new stdClass();
		$registration2->courses   = [];

		$registration2->user_info->name                              = 'A';
		$registration2->user_info->surname                           = 'B';
		$registration2->user_info->email                             = 'unittest2@easyschoolregistration.com';
		$registration2->user_info->phone                             = '1';
		$registration2->user_info->{"checkbox_discount_{$wave_id}"}  = 0;
		$registration2->user_info->{"checkbox_discount_{$wave2_id}"} = 0;
		$registration2->user_info->{"checkbox_discount_{$wave3_id}"} = 1;
		$registration2->user_info->{"checkbox_discount_{$wave4_id}"} = 0;

		$registration2->courses[$course_id]  = new stdClass();
		$registration2->courses[$course2_id] = new stdClass();
		$registration2->courses[$course3_id] = new stdClass();
		$registration2->courses[$course4_id] = new stdClass();
		$registration2->courses[$course5_id] = new stdClass();
		$registration2->courses[$course6_id] = new stdClass();
		$registration2->courses[$course7_id] = new stdClass();
		$registration2->courses[$course8_id] = new stdClass();
		$registration2->courses[$course9_id] = new stdClass();
		$registration2->courses[$course10_id] = new stdClass();
		$registration2->courses[$course11_id] = new stdClass();
		$registration2->courses[$course12_id] = new stdClass();
		$registration2->courses[$course13_id] = new stdClass();
		$registration2->courses[$course14_id] = new stdClass();
		$registration2->courses[$course15_id] = new stdClass();
		$registration2->courses[$course16_id] = new stdClass();
		$registration2->courses[$course17_id] = new stdClass();
		$registration2->courses[$course18_id] = new stdClass();
		$registration2->courses[$course19_id] = new stdClass();
		$registration2->courses[$course20_id] = new stdClass();

		$registration2->courses[$course2_id]->dancing_as = ESR_Dancing_As::FOLLOWER;
		$registration2->courses[$course3_id]->dancing_as = ESR_Dancing_As::FOLLOWER;
		$registration2->courses[$course4_id]->dancing_as = ESR_Dancing_As::FOLLOWER;
		$registration2->courses[$course7_id]->dancing_as = ESR_Dancing_As::FOLLOWER;
		$registration2->courses[$course8_id]->dancing_as = ESR_Dancing_As::FOLLOWER;
		$registration2->courses[$course11_id]->dancing_as = ESR_Dancing_As::FOLLOWER;
		$registration2->courses[$course12_id]->dancing_as = ESR_Dancing_As::FOLLOWER;
		$registration2->courses[$course15_id]->dancing_as = ESR_Dancing_As::FOLLOWER;
		$registration2->courses[$course16_id]->dancing_as = ESR_Dancing_As::FOLLOWER;
		$registration2->courses[$course19_id]->dancing_as = ESR_Dancing_As::FOLLOWER;
		$registration2->courses[$course20_id]->dancing_as = ESR_Dancing_As::FOLLOWER;

		$this->esrd_base->base->process_registrations($registration2);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(2003.6, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(2424.5, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(3050, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave4_id);
		$this->assertEquals(750, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave5_id);
		$this->assertEquals(3200, $payment->to_pay);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest2@easyschoolregistration.com', $wave_id);
		$this->assertEquals(2504.5, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest2@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(2504.5, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest2@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(2975, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest2@easyschoolregistration.com', $wave4_id);
		$this->assertEquals(1600, $payment->to_pay);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest2@easyschoolregistration.com', $wave5_id);
		$this->assertEquals(3200, $payment->to_pay);
	}


	public function test_multiple_waves_multiple_discounts2() {
		$wave_id  = $this->esrd_base->base->add_wave();
		$wave2_id = $this->esrd_base->base->add_wave();
		$wave3_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_discount([$wave_id, $wave2_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '1',
			'esrd_disc_cond_how'                => '1',
			'esrd_disc_cond_operator'           => ['1', '1', '1'],
			'esrd_disc_cond_count'              => ['2', '3', '4'],
			'esrd_disc_price'                   => ['10', '15', '20']
		]);


		$course_id  = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->esrd_base->base->add_course($wave2_id, ['price' => 950, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course3_id = $this->esrd_base->base->add_course($wave3_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name                              = 'A';
		$registration->user_info->surname                           = 'B';
		$registration->user_info->email                             = 'unittest@easyschoolregistration.com';
		$registration->user_info->phone                             = '1';

		$registration->courses[$course_id]  = new stdClass();
		$registration->courses[$course2_id] = new stdClass();
		$registration->courses[$course3_id] = new stdClass();

		$this->esrd_base->base->process_registrations($registration);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(787.5, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(787.5, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);
	}


	public function test_multiple_waves_multiple_discounts_price_no_discount() {
		$wave_id  = $this->esrd_base->base->add_wave();
		$wave2_id = $this->esrd_base->base->add_wave();
		$wave3_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_discount([$wave_id, $wave2_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '1',
			'esrd_disc_cond_how'                => '1',
			'esrd_disc_cond_operator'           => ['1', '1', '1'],
			'esrd_disc_cond_count'              => ['2', '3', '4'],
			'esrd_disc_price'                   => ['10', '15', '20']
		]);


		$course_id  = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->esrd_base->base->add_course($wave2_id, ['price' => 950, 'price_no_discount' => 1, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course3_id = $this->esrd_base->base->add_course($wave3_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name                              = 'A';
		$registration->user_info->surname                           = 'B';
		$registration->user_info->email                             = 'unittest@easyschoolregistration.com';
		$registration->user_info->phone                             = '1';

		$registration->courses[$course_id]  = new stdClass();
		$registration->courses[$course3_id] = new stdClass();

		$this->esrd_base->base->process_registrations($registration);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(800, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(NULL, $payment);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name                              = 'A';
		$registration->user_info->surname                           = 'B';
		$registration->user_info->email                             = 'unittest@easyschoolregistration.com';
		$registration->user_info->phone                             = '1';

		$registration->courses[$course2_id] = new stdClass();

		$this->esrd_base->base->process_registrations($registration);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(875, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(875, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);

		$course4_id = $this->esrd_base->base->add_course($wave2_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$registration3            = new stdClass();
		$registration3->user_info = new stdClass();
		$registration3->courses   = [];

		$registration3->user_info->name                              = 'A';
		$registration3->user_info->surname                           = 'B';
		$registration3->user_info->email                             = 'unittest@easyschoolregistration.com';
		$registration3->user_info->phone                             = '1';

		$registration3->courses[$course4_id]  = new stdClass();

		$this->esrd_base->base->process_registrations($registration3);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(1195, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(1195, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);

		$worker_ajax = new ESR_Ajax_Worker();
		$user_id = $this->esrd_base->base->get_user_id_by_email('unittest@easyschoolregistration.com');
		$worker_ajax->remove_user_course_registration_callback(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(NULL, $payment);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(1750, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);
	}


	public function test_multiple_waves_multiple_discounts_4() {
		$wave_id  = $this->esrd_base->base->add_wave();
		$wave2_id = $this->esrd_base->base->add_wave();
		$wave3_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_discount([$wave_id, $wave2_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '1',
			'esrd_disc_cond_how'                => '1',
			'esrd_disc_cond_operator'           => ['1', '1', '1'],
			'esrd_disc_cond_count'              => ['2', '3', '4'],
			'esrd_disc_price'                   => ['10', '15', '20']
		]);


		$course_id  = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course2_id = $this->esrd_base->base->add_course($wave2_id, ['price' => 950, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$course3_id = $this->esrd_base->base->add_course($wave3_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name                              = 'A';
		$registration->user_info->surname                           = 'B';
		$registration->user_info->email                             = 'unittest@easyschoolregistration.com';
		$registration->user_info->phone                             = '1';

		$registration->courses[$course_id]  = new stdClass();
		$registration->courses[$course3_id] = new stdClass();

		$this->esrd_base->base->process_registrations($registration);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(800, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(NULL, $payment);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name                              = 'A';
		$registration->user_info->surname                           = 'B';
		$registration->user_info->email                             = 'unittest@easyschoolregistration.com';
		$registration->user_info->phone                             = '1';

		$registration->courses[$course2_id] = new stdClass();

		$this->esrd_base->base->process_registrations($registration);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(787.5, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(787.5, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);

		$course4_id = $this->esrd_base->base->add_course($wave2_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$registration3            = new stdClass();
		$registration3->user_info = new stdClass();
		$registration3->courses   = [];

		$registration3->user_info->name                              = 'A';
		$registration3->user_info->surname                           = 'B';
		$registration3->user_info->email                             = 'unittest@easyschoolregistration.com';
		$registration3->user_info->phone                             = '1';

		$registration3->courses[$course4_id]  = new stdClass();

		$this->esrd_base->base->process_registrations($registration3);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(1083.75, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(1083.75, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);

		$worker_ajax = new ESR_Ajax_Worker();
		$user_id = $this->esrd_base->base->get_user_id_by_email('unittest@easyschoolregistration.com');
		$worker_ajax->remove_user_course_registration_callback(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(NULL, $payment);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(1575, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);

		$worker_ajax->process_add_user_course_registration(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(1083.75, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(1083.75, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);

		$worker_ajax = new ESR_Ajax_Worker();
		$user_id = $this->esrd_base->base->get_user_id_by_email('unittest@easyschoolregistration.com');
		$worker_ajax->remove_user_course_registration_callback(ESR()->registration->get_registrations_by_wave_and_user($wave_id, $user_id)[0]);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(NULL, $payment);
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(1575, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);

		$course5_id = $this->esrd_base->base->add_course($wave_id, ['price' => 800, 'is_solo' => true, 'group_id' => 0, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$registration4            = new stdClass();
		$registration4->user_info = new stdClass();
		$registration4->courses   = [];

		$registration4->user_info->name                              = 'A';
		$registration4->user_info->surname                           = 'B';
		$registration4->user_info->email                             = 'unittest@easyschoolregistration.com';
		$registration4->user_info->phone                             = '1';

		$registration4->courses[$course5_id]  = new stdClass();

		$this->esrd_base->base->process_registrations($registration4);

		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave_id);
		$this->assertEquals(1083.75, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave2_id);
		$this->assertEquals(1083.75, floatval($payment->to_pay));
		$payment = $this->esrd_base->load_user_payment_by_email_and_wave('unittest@easyschoolregistration.com', $wave3_id);
		$this->assertEquals(800, $payment->to_pay);
	}

}
