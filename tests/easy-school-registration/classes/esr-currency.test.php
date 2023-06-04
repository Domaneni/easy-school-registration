<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESR_Currency_Test extends PHPUnit_Framework_TestCase {

	private $base_test;


	public function __construct() {
		parent::__construct();
		$this->base_test = new ESR_Base_Test();
	}


	public function setUp() {
		$this->base_test->delete_all_data();
		$this->base_test->setUp();
	}


	public function test_get_currency() {
		$this->assertEquals('USD', ESR()->currency->esr_get_currency());

		$this->base_test->update_settings([
			'currency' => 'CZK'
		]);

		$this->assertEquals('CZK', ESR()->currency->esr_get_currency());
	}


	public function test_get_currency_symbol() {
		$this->assertEquals('&#36;', ESR()->currency->esr_currency_symbol());
		$this->assertEquals('Kč', ESR()->currency->esr_currency_symbol('CZK'));
		$this->assertEquals('&pound;', ESR()->currency->esr_currency_symbol('GBP'));
		$this->assertEquals('&euro;', ESR()->currency->esr_currency_symbol('EUR'));
		$this->assertEquals('&#36;', ESR()->currency->esr_currency_symbol('USD'));
		$this->assertEquals('Ft', ESR()->currency->esr_currency_symbol('HUF'));
		$this->assertEquals('Z&#x142;', ESR()->currency->esr_currency_symbol('PLN'));
		$this->assertEquals('rubl', ESR()->currency->esr_currency_symbol('rubl'));

		$this->base_test->update_settings([
			'currency' => 'CZK'
		]);

		$this->assertEquals('CZK', ESR()->currency->esr_get_currency());

		$this->assertEquals('Kč', ESR()->currency->esr_currency_symbol());
	}


	public function test_prepare_price() {
		$this->assertEquals('10 &#36;', ESR()->currency->prepare_price(10));

		$this->base_test->update_settings(['currency_position' => 'before']);
		$this->assertEquals('&#36;10', ESR()->currency->prepare_price(10));

		$this->base_test->update_settings(['currency_position' => 'before_with_space']);
		$this->assertEquals('&#36; 10', ESR()->currency->prepare_price(10));

		$this->base_test->update_settings(['currency_position' => 'after']);
		$this->assertEquals('10&#36;', ESR()->currency->prepare_price(10));

		$this->base_test->update_settings(['currency_position' => 'after_with_space']);
		$this->assertEquals('10 &#36;', ESR()->currency->prepare_price(10));

		$this->base_test->update_settings(['currency_position' => 'test']);
		$this->assertEquals('10 &#36;', ESR()->currency->prepare_price(10));

	}

}
