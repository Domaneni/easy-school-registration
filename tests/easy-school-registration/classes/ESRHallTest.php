<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESRHallTest extends WP_UnitTestCase {

	private $base_test;


	public function __construct() {
		parent::__construct();
		$this->base_test = new ESR_Base_Test();
	}


	public function setUp(): void {
        parent::setUp();
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	public function test_no_halls() {
		$this->assertEquals([], ESR()->hall->get_hall_names());
	}


	public function test_not_existing_hall() {
		$this->assertEquals([], ESR()->hall->get_hall('huge'));
	}


	public function test_halls_names() {
		global $esr_settings;
		$hall_settings = [
			'halls' => [
				[
					'name'    => 'Headquater',
					'address' => 'Dance room',
				]
			]
		];

		$this->assertEquals([], ESR()->hall->get_hall_names());

		update_option('esr_settings', $hall_settings);
		$esr_settings = $hall_settings;

		$this->assertEquals(['Headquater'], ESR()->hall->get_hall_names());

		$hall_settings = [
			'halls' => [
				[
					'name'    => 'Headquater',
					'address' => 'Dance room',
				],
				[
					'name'    => 'Small room',
					'address' => 'Small dance room',
				]
			]
		];

		update_option('esr_settings', $hall_settings);
		$esr_settings = $hall_settings;

		$this->assertEquals(['Headquater', 'Small room'], ESR()->hall->get_hall_names());

		$hall_settings = [
			'halls' => [
				'Headquater',
				'Small room'
			]
		];

		update_option('esr_settings', $hall_settings);
		$esr_settings = $hall_settings;

		$this->assertEquals(['Headquater', 'Small room'], ESR()->hall->get_hall_names());
	}


	public function test_halls_name() {
		global $esr_settings;
		$hall_settings = [
			'halls' => [
				[
					'name'    => 'Headquater',
					'address' => 'Dance room',
				]
			]
		];

		$this->assertEquals([], ESR()->hall->get_hall_names());

		update_option('esr_settings', $hall_settings);
		$esr_settings = $hall_settings;

		$this->assertEquals('Headquater', ESR()->hall->get_hall_name(0));

		$hall_settings = [
			'halls' => [
				[
					'name'    => 'Headquater',
					'address' => 'Dance room',
				],
				[
					'name'    => 'Small room',
					'address' => 'Small dance room',
				]
			]
		];

		update_option('esr_settings', $hall_settings);
		$esr_settings = $hall_settings;

		$this->assertEquals('Headquater', ESR()->hall->get_hall_name(0));
		$this->assertEquals('Small room', ESR()->hall->get_hall_name(1));
	}


	public function test_halls() {
		global $esr_settings;
		$hall_settings = [
			'halls' => [
				[
					'name'    => 'Headquater',
					'address' => 'Dance room',
				]
			]
		];

		$this->assertEquals([], ESR()->hall->get_hall(0));

		update_option('esr_settings', $hall_settings);
		$esr_settings = $hall_settings;

		$this->assertEquals(['name' => 'Headquater', 'address' => 'Dance room'], ESR()->hall->get_hall(0));
	}

}
