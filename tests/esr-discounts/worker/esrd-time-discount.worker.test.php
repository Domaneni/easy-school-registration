<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-discounts/esrd-base.php';

class ESRD_Time_Discount_Worker_Test extends PHPUnit_Framework_TestCase {

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


	public function test_solo_registration() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_time_discount([
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


		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(180, $payment->to_pay);

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER);

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(360, $payment->to_pay);
	}


	public function test_solo_registration_one_apply() {
		$wave_id = $this->esrd_base->base->add_wave();

		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(200, $payment->to_pay);

		$course2_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course2_id, ESR_Dancing_As::LEADER);

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(400, $payment->to_pay);

		global $wpdb;
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_registration SET time = '" . date('Y-m-d H:i:s', strtotime('+4 hour')) . "' WHERE course_id = %d ", [$course2_id]));

		$discount_id = $this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-2 hour')),
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

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(380, $payment->to_pay);

		$this->esrd_base->add_time_discount([
			'discount_id'               => $discount_id,
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-2 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+5 hour')),
					'value' => '10'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('+7 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+9 hour')),
					'value' => '20'
				]
			],
		]);

		$payment = $this->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(360, $payment->to_pay);
	}


	public function test_remove_discount() {
		$wave_id = $this->esrd_base->base->add_wave();

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


		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 200, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);
		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(180, $payment->to_pay);

		do_action('esrd_remove_time_discount', $discount_id);

		$payment = $this->esrd_base->load_user_payment_by_email('unittest@easyschoolregistration.com');
		$this->assertEquals(200, $payment->to_pay);
	}


	private function load_user_payment_by_email($user_email) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT up.* FROM {$wpdb->prefix}esr_user_payment AS up
							   JOIN {$wpdb->users} AS u ON u.ID = up.user_id AND u.user_email = %s", [$user_email]));
	}

}
