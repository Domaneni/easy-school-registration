<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-discounts/esrd-base.php';

class ESRD_Payment_Worker_Test extends PHPUnit_Framework_TestCase {

	private $esrd_base;

	private $worker_payment;


	public function __construct() {
		parent::__construct();
		$this->esrd_base      = new ESRD_Base();
		$this->worker_payment = new ESRD_Payment_Worker();
	}


	public function setUp() {
		$this->esrd_base->base->delete_all_data();
		$this->esrd_base->base->setUp();
	}


	public function test_change_discount_how() {
		$wave_id   = $this->esrd_base->base->add_wave();
		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 500, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$discount_id = $this->esrd_base->add_discount([$wave_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '1',
			'esrd_disc_cond_how'                => '3',
			'esrd_disc_cond_operator'           => ['1'],
			'esrd_disc_cond_count'              => ['1'],
			'esrd_disc_price'                   => ['10']
		]);

		$this->esrd_base->base->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);
		$user_id = $this->esrd_base->base->get_user_id_by_email();

		$this->assertEquals(3, $this->esrd_base->base->fetch_users_count());
		$this->assertEquals(1, $this->esrd_base->base->fetch_registrations_count());

		$this->assertEquals(1, ESR()->course_summary->get_course_summary($course_id)->registered_solo);

		$payment1 = ESR()->payment->get_payment_by_wave_and_user($wave_id, $user_id);
		$this->assertEquals(10, $payment1->to_pay);

		$this->esrd_base->add_discount([$wave_id], [
			'discount_id'                       => $discount_id,
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '1',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['1'],
			'esrd_disc_cond_count'              => ['1'],
			'esrd_disc_price'                   => ['10']
		]);

		$payment1 = ESR()->payment->get_payment_by_wave_and_user($wave_id, $user_id);
		$this->assertEquals(490, $payment1->to_pay);

		$this->esrd_base->add_discount([$wave_id], [
			'discount_id'                       => $discount_id,
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '1',
			'esrd_disc_cond_how'                => '1',
			'esrd_disc_cond_operator'           => ['1'],
			'esrd_disc_cond_count'              => ['1'],
			'esrd_disc_price'                   => ['10']
		]);

		$payment1 = ESR()->payment->get_payment_by_wave_and_user($wave_id, $user_id);
		$this->assertEquals(450, $payment1->to_pay);
	}

}
