<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-discounts/esrd-base.php';

class ESRD_Discount_Test extends PHPUnit_Framework_TestCase {

	private $esrd_base;


	public function __construct() {
		parent::__construct();
		$this->esrd_base       = new ESRD_Base();
	}


	public function setUp() {
		$this->esrd_base->base->delete_all_data();
		$this->esrd_base->base->setUp();
	}


	public function test_get_waves_without_discounts() {
		$this->assertEquals([], ESRD()->discount->esrd_load_waves_without_discounts());
		$this->assertEquals([], ESRD()->discount->esrd_load_waves_with_discounts());

		$wave_id = $this->esrd_base->base->add_wave();

		$this->assertEquals([$wave_id => $wave_id], ESRD()->discount->esrd_load_waves_without_discounts());
		$this->assertEquals([], ESRD()->discount->esrd_load_waves_with_discounts());

		$this->esrd_base->add_discount([$wave_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['1', '1', '2'],
			'esrd_disc_cond_count'              => ['1', '2', '2'],
			'esrd_disc_price'                   => ['800', '1300', '1600']
		]);

		$this->assertEquals([], ESRD()->discount->esrd_load_waves_without_discounts());
		$this->assertEquals([$wave_id => $wave_id], ESRD()->discount->esrd_load_waves_with_discounts());

		$wave2_id = $this->esrd_base->base->add_wave();

		$this->assertEquals([$wave2_id => $wave2_id], ESRD()->discount->esrd_load_waves_without_discounts());
		$this->assertEquals([$wave_id => $wave_id], ESRD()->discount->esrd_load_waves_with_discounts());

		$this->esrd_base->add_discount([$wave2_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['1', '1', '2'],
			'esrd_disc_cond_count'              => ['1', '2', '2'],
			'esrd_disc_price'                   => ['800', '1300', '1600']
		]);

		$this->assertEquals([], ESRD()->discount->esrd_load_waves_without_discounts());
		$this->assertEquals([$wave_id => $wave_id, $wave2_id => $wave2_id], ESRD()->discount->esrd_load_waves_with_discounts());
	}


	/*public function test_prepare_waves() {
		$wave_id = $this->esrd_base->base->add_wave();

		//TODO: dopsat testy
	}*/


	public function test_load_discount_waves_by_wave() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->assertEquals([$wave_id], ESRD()->discount->esrd_load_discount_waves_by_wave($wave_id));

		$wave2_id = $this->esrd_base->base->add_wave();

		$this->assertEquals([$wave_id], ESRD()->discount->esrd_load_discount_waves_by_wave($wave_id));

		$this->esrd_base->add_discount([$wave_id, $wave2_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['1', '1', '2'],
			'esrd_disc_cond_count'              => ['1', '2', '2'],
			'esrd_disc_price'                   => ['800', '1300', '1600']
		]);

		$this->assertEquals([$wave_id, $wave2_id], ESRD()->discount->esrd_load_discount_waves_by_wave($wave_id));
		$this->assertEquals([$wave_id, $wave2_id], ESRD()->discount->esrd_load_discount_waves_by_wave($wave2_id));
	}


	public function test_load_all_discounts() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->assertEquals([], ESRD()->discount->esrd_load_all_discounts());

		$this->esrd_base->add_discount([$wave_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['1', '1', '2'],
			'esrd_disc_cond_count'              => ['1', '2', '2'],
			'esrd_disc_price'                   => ['800', '1300', '1600']
		]);

		$this->assertEquals(1, count(ESRD()->discount->esrd_load_all_discounts()));
		$this->assertEquals([$wave_id], array_keys(ESRD()->discount->esrd_load_all_discounts()));

		$wave2_id = $this->esrd_base->base->add_wave();

		$this->esrd_base->add_discount([$wave2_id], [
			'esrd_enable_course_count_discount' => 'on',
			'esrd_disc_cond_what'               => '2',
			'esrd_disc_cond_how'                => '2',
			'esrd_disc_cond_operator'           => ['1', '1', '2'],
			'esrd_disc_cond_count'              => ['1', '2', '2'],
			'esrd_disc_price'                   => ['800', '1300', '1600']
		]);

		$this->assertEquals(2, count(ESRD()->discount->esrd_load_all_discounts()));
		$this->assertEquals([$wave_id, $wave2_id], array_keys(ESRD()->discount->esrd_load_all_discounts()));
	}

}
