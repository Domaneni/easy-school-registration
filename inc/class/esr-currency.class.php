<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Currency {

    private $currencies = [];

    public function __construct() {
        $this->currencies = apply_filters('esr_currencies', [
            'USD' => [
                'title' => esc_html__('US Dollars (&#36;)', 'easy-school-registration'),
                'symbol' => '&#36;'
            ],
            'EUR' => [
                'title' => esc_html__('Euros (&euro;)', 'easy-school-registration'),
                'symbol' => '&euro;'
            ],
            'GBP' => [
                'title' => esc_html__('Pound Sterling (&pound;)', 'easy-school-registration'),
                'symbol' => '&pound;'
            ],
            'CZK' => [
                'title' => esc_html__('Czech Crown (Kč)', 'easy-school-registration'),
                'symbol' => 'Kč'
            ],
            'DKK' => [
                'title' => esc_html__('Danish Krone (kr)', 'easy-school-registration'),
                'symbol' => 'kr'
            ],
            'HUF' => [
                'title' => esc_html__('Hungarian Forint (Ft)', 'easy-school-registration'),
                'symbol' => 'Ft'
            ],
            'PLN' => [
                'title' => esc_html__('Polish Zloty (Z&#x142;)', 'easy-school-registration'),
                'symbol' => 'Z&#x142;'
            ],
            'THB' => [
                'title' => esc_html__('Thai Baht (฿)', 'easy-school-registration'),
                'symbol' => '฿'
            ],
        ]);
    }

	/**
	 * @codeCoverageIgnore
	 */
	public function esr_get_currencies() {
		return $this->currencies;
	}

    public function esr_get_currencies_for_select() {
        $result = [];

        foreach ($this->currencies as $key => $currency) {
            $result[$key] = $currency['title'];
        }

        return $result;
    }


	public function esr_get_currency() {
		$currency = ESR()->settings->esr_get_option('currency', 'USD');

		return apply_filters('esr_currency', $currency);
	}


	public function esr_get_currency_position() {
		$currency_position = ESR()->settings->esr_get_option('currency_position', 'after_with_space');

		return apply_filters('esr_currency_position', $currency_position);
	}


	public function esr_currency_symbol($currency = '') {
		if (empty($currency)) {
			$currency = $this->esr_get_currency();
		}

        if (!isset($this->currencies[$currency]['symbol'])) {
            return '';
        }

        return $this->currencies[$currency]['symbol'];
	}


	public function prepare_price($price) {
		$currency_position = $this->esr_get_currency_position();

		switch ($currency_position) {
			case 'before':
				{
					$price_with_currency = $this->esr_currency_symbol() . $price;
					break;
				}
			case 'before_with_space':
				{
					$price_with_currency = $this->esr_currency_symbol() . ' ' . $price;
					break;
				}
			case 'after':
				{
					$price_with_currency = $price . $this->esr_currency_symbol();
					break;
				}
			default :
				{
					$price_with_currency = $price . ' ' . $this->esr_currency_symbol();
				}
		}

		return apply_filters('esr_prepare_price', $price_with_currency);
	}

}