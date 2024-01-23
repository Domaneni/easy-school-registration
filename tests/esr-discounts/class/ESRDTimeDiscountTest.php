<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-discounts/esrd-base.php';

class ESRDTimeDiscountTest extends PHPUnit_Framework_TestCase {

	private $esrd_base;


	public function __construct() {
		parent::__construct();
		$this->esrd_base = new ESRD_Base();
	}


	public function setUp(): void
    {
        parent::setUp();
        $this->esrd_base->delete_all_data();
		$this->esrd_base->base->setUp();
	}


	public function test_get_course_price_sql_minus_percentage() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->assertEquals('price', ESR()->course->esr_get_course_price_sql($wave_id));

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => '2018-07-16T18:00',
					'to'    => '2018-07-23T20:00',
					'value' => '10'
				],
				1 => [
					'from'  => '2018-07-23T20:00',
					'to'    => '2018-07-30T20:00',
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);


		$this->assertEquals("CASE WHEN CURRENT_TIMESTAMP() BETWEEN '2018-07-16 18:00:00' AND '2018-07-23 20:00:00' THEN price * 0.9 WHEN CURRENT_TIMESTAMP() BETWEEN '2018-07-23 20:00:00' AND '2018-07-30 20:00:00' THEN price * 0.8 ELSE price END", ESR()->course->esr_get_course_price_sql($wave_id));
	}


	public function test_get_course_price_sql_minus_price() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->assertEquals('price', ESR()->course->esr_get_course_price_sql($wave_id));

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '2',
			'esrd_time_disc'            => [
				0 => [
					'from'  => '2018-07-16T18:00',
					'to'    => '2018-07-23T20:00',
					'value' => '10'
				],
				1 => [
					'from'  => '2018-07-23T20:00',
					'to'    => '2018-07-30T20:00',
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);


		$this->assertEquals("CASE WHEN CURRENT_TIMESTAMP() BETWEEN '2018-07-16 18:00:00' AND '2018-07-23 20:00:00' THEN price - 10 WHEN CURRENT_TIMESTAMP() BETWEEN '2018-07-23 20:00:00' AND '2018-07-30 20:00:00' THEN price - 20 ELSE price END", ESR()->course->esr_get_course_price_sql($wave_id));
	}


	public function test_get_course_price_sql_final_price() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->assertEquals('price', ESR()->course->esr_get_course_price_sql($wave_id));

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '3',
			'esrd_time_disc'            => [
				0 => [
					'from'  => '2018-07-16T18:00',
					'to'    => '2018-07-23T20:00',
					'value' => '10'
				],
				1 => [
					'from'  => '2018-07-23T20:00',
					'to'    => '2018-07-30T20:00',
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);


		$this->assertEquals("CASE WHEN CURRENT_TIMESTAMP() BETWEEN '2018-07-16 18:00:00' AND '2018-07-23 20:00:00' THEN 10 WHEN CURRENT_TIMESTAMP() BETWEEN '2018-07-23 20:00:00' AND '2018-07-30 20:00:00' THEN 20 ELSE price END", ESR()->course->esr_get_course_price_sql($wave_id));
	}


	public function test_get_price_sql_minus_percentage() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->assertEquals('cd.price', ESR()->payment->esr_get_price_sql($wave_id));

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => '2018-07-16T18:00',
					'to'    => '2018-07-23T20:00',
					'value' => '10'
				],
				1 => [
					'from'  => '2018-07-23T20:00',
					'to'    => '2018-07-30T20:00',
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);


		$this->assertEquals("CASE WHEN cr.time BETWEEN '2018-07-16 18:00:00' AND '2018-07-23 20:00:00' THEN cd.price * 0.9 WHEN cr.time BETWEEN '2018-07-23 20:00:00' AND '2018-07-30 20:00:00' THEN cd.price * 0.8 ELSE cd.price END", ESR()->payment->esr_get_price_sql($wave_id));
	}


	public function test_get_price_sql_minus_price() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->assertEquals('cd.price', ESR()->payment->esr_get_price_sql($wave_id));

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '2',
			'esrd_time_disc'            => [
				0 => [
					'from'  => '2018-07-16T18:00',
					'to'    => '2018-07-23T20:00',
					'value' => '10'
				],
				1 => [
					'from'  => '2018-07-23T20:00',
					'to'    => '2018-07-30T20:00',
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);


		$this->assertEquals("CASE WHEN cr.time BETWEEN '2018-07-16 18:00:00' AND '2018-07-23 20:00:00' THEN cd.price - 10 WHEN cr.time BETWEEN '2018-07-23 20:00:00' AND '2018-07-30 20:00:00' THEN cd.price - 20 ELSE cd.price END", ESR()->payment->esr_get_price_sql($wave_id));
	}


	public function test_get_price_sql_final_price() {
		$wave_id = $this->esrd_base->base->add_wave();

		$this->assertEquals('cd.price', ESR()->payment->esr_get_price_sql($wave_id));

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '3',
			'esrd_time_disc'            => [
				0 => [
					'from'  => '2018-07-16T18:00',
					'to'    => '2018-07-23T20:00',
					'value' => '10'
				],
				1 => [
					'from'  => '2018-07-23T20:00',
					'to'    => '2018-07-30T20:00',
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);


		$this->assertEquals("CASE WHEN cr.time BETWEEN '2018-07-16 18:00:00' AND '2018-07-23 20:00:00' THEN 10 WHEN cr.time BETWEEN '2018-07-23 20:00:00' AND '2018-07-30 20:00:00' THEN 20 ELSE cd.price END", ESR()->payment->esr_get_price_sql($wave_id));
	}


	public function test_get_course_price() {
		$wave_id = $this->esrd_base->base->add_wave();
		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 500, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$courses = ESR()->course->get_courses_data_by_wave($wave_id);
		$this->assertEquals(500, (int) $courses[0]->price);
		$this->assertEquals(500, (int) $courses[0]->real_price);

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

		$courses = ESR()->course->get_courses_data_by_wave($wave_id);
		$this->assertEquals(500, (int) $courses[0]->price);
		$this->assertEquals(450, (int) $courses[0]->real_price);
	}


	public function test_get_course_price_second_option() {
		$wave_id = $this->esrd_base->base->add_wave();
		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 500, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$courses = ESR()->course->get_courses_data_by_wave($wave_id);
		$this->assertEquals(500, (int) $courses[0]->price);
		$this->assertEquals(500, (int) $courses[0]->real_price);

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-5 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('-3 hour')),
					'value' => '10'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-1 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('+5 hour')),
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$courses = ESR()->course->get_courses_data_by_wave($wave_id);
		$this->assertEquals(500, (int) $courses[0]->price);
		$this->assertEquals(400, (int) $courses[0]->real_price);
	}


	public function test_get_course_price_third_option() {
		$wave_id = $this->esrd_base->base->add_wave();
		$course_id = $this->esrd_base->base->add_course($wave_id, ['price' => 500, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$courses = ESR()->course->get_courses_data_by_wave($wave_id);
		$this->assertEquals(500, (int) $courses[0]->price);
		$this->assertEquals(500, (int) $courses[0]->real_price);

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-5 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('-4 hour')),
					'value' => '10'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-3 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('-2 hour')),
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$courses = ESR()->course->get_courses_data_by_wave($wave_id);
		$this->assertEquals(500, (int) $courses[0]->price);
		$this->assertEquals(500, (int) $courses[0]->real_price);
	}


	public function test_load_all_time_discounts() {
		$wave_id = $this->esrd_base->base->add_wave();
		$this->esrd_base->base->add_course($wave_id, ['price' => 500, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$discount_id = $this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-5 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('-4 hour')),
					'value' => '10'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-3 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('-2 hour')),
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$this->assertEquals(1, count(ESRD()->time_discount->esrd_load_all_discounts()));

		$wave2_id = $this->esrd_base->base->add_wave();
		$this->esrd_base->base->add_course($wave2_id, ['price' => 500, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave2_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-5 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('-4 hour')),
					'value' => '10'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-3 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('-2 hour')),
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$this->assertEquals(2, count(ESRD()->time_discount->esrd_load_all_discounts()));

		do_action('esrd_remove_time_discount', $discount_id);

		$this->assertEquals(1, count(ESRD()->time_discount->esrd_load_all_discounts()));
	}


	public function test_load_all_waves_for_time_discounts() {
		$wave_id = $this->esrd_base->base->add_wave();
		$this->esrd_base->base->add_course($wave_id, ['price' => 500, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$discount_id = $this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-5 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('-4 hour')),
					'value' => '10'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-3 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('-2 hour')),
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$this->assertEquals([$wave_id => $wave_id], ESRD()->time_discount->esrd_load_waves_with_discounts());

		$wave2_id = $this->esrd_base->base->add_wave();
		$this->esrd_base->base->add_course($wave2_id, ['price' => 500, 'is_solo' => true, 'max_solo' => 10, 'day' => ESR_Enum_Day::MONDAY]);

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave2_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-5 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('-4 hour')),
					'value' => '10'
				],
				1 => [
					'from'  => date('Y-m-d H:i:s', strtotime('-3 hour')),
					'to'    => date('Y-m-d H:i:s', strtotime('-2 hour')),
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$this->assertEquals([$wave_id => $wave_id, $wave2_id => $wave2_id], ESRD()->time_discount->esrd_load_waves_with_discounts());

		do_action('esrd_remove_time_discount', $discount_id);

		$this->assertEquals([$wave2_id => $wave2_id], ESRD()->time_discount->esrd_load_waves_with_discounts());
	}


	public function test_prepare_waves_sql_case() {
		$wave_id = $this->esrd_base->base->add_wave();
		$wave2_id = $this->esrd_base->base->add_wave();

		$this->assertEquals('cd.price', apply_filters('esr_waves_sql_price_string', ['sql' => 'cd.price'])['sql']);

		$this->assertEquals('CASE WHEN cd.wave_id = ' . $wave_id . ' THEN (cd.price) WHEN cd.wave_id = ' . $wave2_id . ' THEN (cd.price) ELSE cd.price END', apply_filters('esr_waves_sql_price_string', ['wave_ids' => [$wave_id, $wave2_id], 'sql' => 'cd.price'])['sql']);

		$date1 = date('Y-m-d H:i:s', strtotime('-1 hour'));
		$date2 = date('Y-m-d H:i:s', strtotime('+3 hour'));
		$date3 = date('Y-m-d H:i:s', strtotime('+5 hour'));
		$date4 = date('Y-m-d H:i:s', strtotime('+7 hour'));

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '1',
			'esrd_time_disc'            => [
				0 => [
					'from'  => $date1,
					'to'    => $date2,
					'value' => '50'
				],
				1 => [
					'from'  => $date3,
					'to'    => $date4,
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$this->assertEquals('CASE WHEN cd.wave_id = ' . $wave_id . ' THEN (CASE WHEN cr.time BETWEEN \'' . $date1 . '\' AND \'' . $date2 . '\' THEN cd.price * 0.5 WHEN cr.time BETWEEN \'' . $date3 . '\' AND \'' . $date4 . '\' THEN cd.price * 0.8 ELSE cd.price END) WHEN cd.wave_id = ' . $wave2_id . ' THEN (cd.price) ELSE cd.price END', apply_filters('esr_waves_sql_price_string', ['wave_ids' => [$wave_id, $wave2_id], 'sql' => 'cd.price'])['sql']);

		$this->esrd_base->add_time_discount([
			'esrd_wave'                 => [0 => $wave2_id],
			'esrd_enable_time_discount' => 'on',
			'esrd_disc_cond_how'        => '2',
			'esrd_time_disc'            => [
				0 => [
					'from'  => $date1,
					'to'    => $date2,
					'value' => '50'
				],
				1 => [
					'from'  => $date3,
					'to'    => $date4,
					'value' => '20'
				]
			],
			'discount_id'               => ''
		]);

		$this->assertEquals('CASE WHEN cd.wave_id = ' . $wave_id . ' THEN (CASE WHEN cr.time BETWEEN \'' . $date1 . '\' AND \'' . $date2 . '\' THEN cd.price * 0.5 WHEN cr.time BETWEEN \'' . $date3 . '\' AND \'' . $date4 . '\' THEN cd.price * 0.8 ELSE cd.price END) WHEN cd.wave_id = ' . $wave2_id . ' THEN (CASE WHEN cr.time BETWEEN \'' . $date1 . '\' AND \'' . $date2 . '\' THEN cd.price - 50 WHEN cr.time BETWEEN \'' . $date3 . '\' AND \'' . $date4 . '\' THEN cd.price - 20 ELSE cd.price END) ELSE cd.price END', apply_filters('esr_waves_sql_price_string', ['wave_ids' => [$wave_id, $wave2_id], 'sql' => 'cd.price'])['sql']);
	}

}
