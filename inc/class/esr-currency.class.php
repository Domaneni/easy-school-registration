<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ESR_Currency
{

    private $currencies = [];

    public function __construct()
    {
        $this->currencies = apply_filters('esr_currencies', [
            'AED' => ['title' => esc_html__('UAE dirham', 'easy-school-registration'), 'symbol' => ' &#1583;. &#1573;'],
            'AFN' => ['title' => esc_html__('Afghan afghani', 'easy-school-registration'), 'symbol' => 'Afs'],
            'ALL' => ['title' => esc_html__('Albanian lek', 'easy-school-registration'), 'symbol' => 'L'],
            'AMD' => ['title' => esc_html__('Armenian dram', 'easy-school-registration'), 'symbol' => 'AMD'],
            'ANG' => ['title' => esc_html__('Netherlands Antillean gulden', 'easy-school-registration'), 'symbol' => 'NA&#402;'],
            'AOA' => ['title' => esc_html__('Angolan kwanza', 'easy-school-registration'), 'symbol' => 'Kz'],
            'ARS' => ['title' => esc_html__('Argentine peso', 'easy-school-registration'), 'symbol' => '$'],
            'AUD' => ['title' => esc_html__('Australian dollar', 'easy-school-registration'), 'symbol' => '$'],
            'AWG' => ['title' => esc_html__('Aruban florin', 'easy-school-registration'), 'symbol' => '&#402;'],
            'AZN' => ['title' => esc_html__('Azerbaijani manat', 'easy-school-registration'), 'symbol' => 'AZN'],
            'BAM' => ['title' => esc_html__('Bosnia and Herzegovina konvertibilna marka', 'easy-school-registration'), 'symbol' => 'KM'],
            'BBD' => ['title' => esc_html__('Barbadian dollar', 'easy-school-registration'), 'symbol' => 'Bds$'],
            'BDT' => ['title' => esc_html__('Bangladeshi taka', 'easy-school-registration'), 'symbol' => '&#2547;'],
            'BGN' => ['title' => esc_html__('Bulgarian lev', 'easy-school-registration'), 'symbol' => 'BGN'],
            'BHD' => ['title' => esc_html__('Bahraini dinar', 'easy-school-registration'), 'symbol' => '. &#1583;.&#1576;'],
            'BIF' => ['title' => esc_html__('Burundi franc', 'easy-school-registration'), 'symbol' => 'FBu'],
            'BMD' => ['title' => esc_html__('Bermudian dollar', 'easy-school-registration'), 'symbol' => 'BD$'],
            'BND' => ['title' => esc_html__('Brunei dollar', 'easy-school-registration'), 'symbol' => 'B$'],
            'BOB' => ['title' => esc_html__('Bolivian boliviano', 'easy-school-registration'), 'symbol' => 'Bs.'],
            'BRL' => ['title' => esc_html__('Brazilian real', 'easy-school-registration'), 'symbol' => 'R$'],
            'BSD' => ['title' => esc_html__('Bahamian dollar', 'easy-school-registration'), 'symbol' => 'B$'],
            'BTN' => ['title' => esc_html__('Bhutanese ngultrum', 'easy-school-registration'), 'symbol' => 'Nu.'],
            'BWP' => ['title' => esc_html__('Botswana pula', 'easy-school-registration'), 'symbol' => 'P'],
            'BYR' => ['title' => esc_html__('Belarusian ruble', 'easy-school-registration'), 'symbol' => 'Br'],
            'BZD' => ['title' => esc_html__('Belize dollar', 'easy-school-registration'), 'symbol' => 'BZ$'],
            'CAD' => ['title' => esc_html__('Canadian dollar', 'easy-school-registration'), 'symbol' => '$'],
            'CDF' => ['title' => esc_html__('Congolese franc', 'easy-school-registration'), 'symbol' => 'F'],
            'CHF' => ['title' => esc_html__('Swiss franc', 'easy-school-registration'), 'symbol' => 'Fr.'],
            'CLP' => ['title' => esc_html__('Chilean peso', 'easy-school-registration'), 'symbol' => '$'],
            'CNY' => ['title' => esc_html__('Chinese/Yuan renminbi', 'easy-school-registration'), 'symbol' => '&#165;'],
            'COP' => ['title' => esc_html__('Colombian peso', 'easy-school-registration'), 'symbol' => 'Col$'],
            'CRC' => ['title' => esc_html__('Costa Rican colon', 'easy-school-registration'), 'symbol' => '&#8353;'],
            'CUC' => ['title' => esc_html__('Cuban peso', 'easy-school-registration'), 'symbol' => '$'],
            'CVE' => ['title' => esc_html__('Cape Verdean escudo', 'easy-school-registration'), 'symbol' => 'Esc'],
            'CZK' => ['title' => esc_html__('Czech koruna', 'easy-school-registration'), 'symbol' => 'K&#269;'],
            'DJF' => ['title' => esc_html__('Djiboutian franc', 'easy-school-registration'), 'symbol' => 'Fdj'],
            'DKK' => ['title' => esc_html__('Danish krone', 'easy-school-registration'), 'symbol' => 'Kr'],
            'DOP' => ['title' => esc_html__('Dominican peso', 'easy-school-registration'), 'symbol' => 'RD$'],
            'DZD' => ['title' => esc_html__('Algerian dinar', 'easy-school-registration'), 'symbol' => ' &#1583;.&#1580;'],
            'EEK' => ['title' => esc_html__('Estonian kroon', 'easy-school-registration'), 'symbol' => 'KR'],
            'EGP' => ['title' => esc_html__('Egyptian pound', 'easy-school-registration'), 'symbol' => '&#163;'],
            'ERN' => ['title' => esc_html__('Eritrean nakfa', 'easy-school-registration'), 'symbol' => 'Nfa'],
            'ETB' => ['title' => esc_html__('Ethiopian birr', 'easy-school-registration'), 'symbol' => 'Br'],
            'EUR' => ['title' => esc_html__('European Euro', 'easy-school-registration'), 'symbol' => '&#8364;'],
            'FJD' => ['title' => esc_html__('Fijian dollar', 'easy-school-registration'), 'symbol' => 'FJ$'],
            'FKP' => ['title' => esc_html__('Falkland Islands pound', 'easy-school-registration'), 'symbol' => '&#163;'],
            'GBP' => ['title' => esc_html__('British pound', 'easy-school-registration'), 'symbol' => '&pound;'],
            'GEL' => ['title' => esc_html__('Georgian lari', 'easy-school-registration'), 'symbol' => 'GEL'],
            'GHS' => ['title' => esc_html__('Ghanaian cedi', 'easy-school-registration'), 'symbol' => 'GH&#8373;'],
            'GIP' => ['title' => esc_html__('Gibraltar pound', 'easy-school-registration'), 'symbol' => '&#163;'],
            'GMD' => ['title' => esc_html__('Gambian dalasi', 'easy-school-registration'), 'symbol' => 'D'],
            'GNF' => ['title' => esc_html__('Guinean franc', 'easy-school-registration'), 'symbol' => 'FG'],
            'GQE' => ['title' => esc_html__('Central African CFA franc', 'easy-school-registration'), 'symbol' => 'CFA'],
            'GTQ' => ['title' => esc_html__('Guatemalan quetzal', 'easy-school-registration'), 'symbol' => 'Q'],
            'GYD' => ['title' => esc_html__('Guyanese dollar', 'easy-school-registration'), 'symbol' => 'GY$'],
            'HKD' => ['title' => esc_html__('Hong Kong dollar', 'easy-school-registration'), 'symbol' => 'HK$'],
            'HNL' => ['title' => esc_html__('Honduran lempira', 'easy-school-registration'), 'symbol' => 'L'],
            'HRK' => ['title' => esc_html__('Croatian kuna', 'easy-school-registration'), 'symbol' => 'kn'],
            'HTG' => ['title' => esc_html__('Haitian gourde', 'easy-school-registration'), 'symbol' => 'G'],
            'HUF' => ['title' => esc_html__('Hungarian forint', 'easy-school-registration'), 'symbol' => 'Ft'],
            'IDR' => ['title' => esc_html__('Indonesian rupiah', 'easy-school-registration'), 'symbol' => 'Rp'],
            'ILS' => ['title' => esc_html__('Israeli new sheqel', 'easy-school-registration'), 'symbol' => '&#8362;'],
            'INR' => ['title' => esc_html__('Indian rupee', 'easy-school-registration'), 'symbol' => '&#8377;'],
            'IQD' => ['title' => esc_html__('Iraqi dinar', 'easy-school-registration'), 'symbol' => ' &#1583;.&#1593;'],
            'IRR' => ['title' => esc_html__('Iranian rial', 'easy-school-registration'), 'symbol' => 'IRR'],
            'ISK' => ['title' => esc_html__('Icelandic krona', 'easy-school-registration'), 'symbol' => 'kr'],
            'JMD' => ['title' => esc_html__('Jamaican dollar', 'easy-school-registration'), 'symbol' => 'J$'],
            'JOD' => ['title' => esc_html__('Jordanian dinar', 'easy-school-registration'), 'symbol' => 'JOD'],
            'JPY' => ['title' => esc_html__('Japanese yen', 'easy-school-registration'), 'symbol' => '&#165;'],
            'KES' => ['title' => esc_html__('Kenyan shilling', 'easy-school-registration'), 'symbol' => 'KSh'],
            'KGS' => ['title' => esc_html__('Kyrgyzstani som', 'easy-school-registration'), 'symbol' => '&#1089;&#1086;&#1084;'],
            'KHR' => ['title' => esc_html__('Cambodian riel', 'easy-school-registration'), 'symbol' => '&#6107;'],
            'KMF' => ['title' => esc_html__('Comorian franc', 'easy-school-registration'), 'symbol' => 'KMF'],
            'KPW' => ['title' => esc_html__('North Korean won', 'easy-school-registration'), 'symbol' => 'W'],
            'KRW' => ['title' => esc_html__('South Korean won', 'easy-school-registration'), 'symbol' => 'W'],
            'KWD' => ['title' => esc_html__('Kuwaiti dinar', 'easy-school-registration'), 'symbol' => 'KWD'],
            'KYD' => ['title' => esc_html__('Cayman Islands dollar', 'easy-school-registration'), 'symbol' => 'KY$'],
            'KZT' => ['title' => esc_html__('Kazakhstani tenge', 'easy-school-registration'), 'symbol' => 'T'],
            'LAK' => ['title' => esc_html__('Lao kip', 'easy-school-registration'), 'symbol' => 'KN'],
            'LBP' => ['title' => esc_html__('Lebanese lira', 'easy-school-registration'), 'symbol' => '&#163;'],
            'LKR' => ['title' => esc_html__('Sri Lankan rupee', 'easy-school-registration'), 'symbol' => 'Rs'],
            'LRD' => ['title' => esc_html__('Liberian dollar', 'easy-school-registration'), 'symbol' => 'L$'],
            'LSL' => ['title' => esc_html__('Lesotho loti', 'easy-school-registration'), 'symbol' => 'M'],
            'LTL' => ['title' => esc_html__('Lithuanian litas', 'easy-school-registration'), 'symbol' => 'Lt'],
            'LVL' => ['title' => esc_html__('Latvian lats', 'easy-school-registration'), 'symbol' => 'Ls'],
            'LYD' => ['title' => esc_html__('Libyan dinar', 'easy-school-registration'), 'symbol' => 'LD'],
            'MAD' => ['title' => esc_html__('Moroccan dirham', 'easy-school-registration'), 'symbol' => 'MAD'],
            'MDL' => ['title' => esc_html__('Moldovan leu', 'easy-school-registration'), 'symbol' => 'MDL'],
            'MGA' => ['title' => esc_html__('Malagasy ariary', 'easy-school-registration'), 'symbol' => 'FMG'],
            'MKD' => ['title' => esc_html__('Macedonian denar', 'easy-school-registration'), 'symbol' => 'MKD'],
            'MMK' => ['title' => esc_html__('Myanma kyat', 'easy-school-registration'), 'symbol' => 'K'],
            'MNT' => ['title' => esc_html__('Mongolian tugrik', 'easy-school-registration'), 'symbol' => '&#8366;'],
            'MOP' => ['title' => esc_html__('Macanese pataca', 'easy-school-registration'), 'symbol' => 'P'],
            'MRO' => ['title' => esc_html__('Mauritanian ouguiya', 'easy-school-registration'), 'symbol' => 'UM'],
            'MUR' => ['title' => esc_html__('Mauritian rupee', 'easy-school-registration'), 'symbol' => 'Rs'],
            'MVR' => ['title' => esc_html__('Maldivian rufiyaa', 'easy-school-registration'), 'symbol' => 'Rf'],
            'MWK' => ['title' => esc_html__('Malawian kwacha', 'easy-school-registration'), 'symbol' => 'MK'],
            'MXN' => ['title' => esc_html__('Mexican peso', 'easy-school-registration'), 'symbol' => '$'],
            'MYR' => ['title' => esc_html__('Malaysian ringgit', 'easy-school-registration'), 'symbol' => 'RM'],
            'MZM' => ['title' => esc_html__('Mozambican metical', 'easy-school-registration'), 'symbol' => 'MTn'],
            'NAD' => ['title' => esc_html__('Namibian dollar', 'easy-school-registration'), 'symbol' => 'N$'],
            'NGN' => ['title' => esc_html__('Nigerian naira', 'easy-school-registration'), 'symbol' => '&#8358;'],
            'NIO' => ['title' => esc_html__('Nicaraguan cordoba', 'easy-school-registration'), 'symbol' => 'C$'],
            'NOK' => ['title' => esc_html__('Norwegian krone', 'easy-school-registration'), 'symbol' => 'kr'],
            'NPR' => ['title' => esc_html__('Nepalese rupee', 'easy-school-registration'), 'symbol' => 'NRs'],
            'NZD' => ['title' => esc_html__('New Zealand dollar', 'easy-school-registration'), 'symbol' => 'NZ$'],
            'OMR' => ['title' => esc_html__('Omani rial', 'easy-school-registration'), 'symbol' => 'OMR'],
            'PAB' => ['title' => esc_html__('Panamanian balboa', 'easy-school-registration'), 'symbol' => 'B./'],
            'PEN' => ['title' => esc_html__('Peruvian nuevo sol', 'easy-school-registration'), 'symbol' => 'S/.'],
            'PGK' => ['title' => esc_html__('Papua New Guinean kina', 'easy-school-registration'), 'symbol' => 'K'],
            'PHP' => ['title' => esc_html__('Philippine peso', 'easy-school-registration'), 'symbol' => '&#8369;'],
            'PKR' => ['title' => esc_html__('Pakistani rupee', 'easy-school-registration'), 'symbol' => 'Rs.'],
            'PLN' => ['title' => esc_html__('Polish zloty', 'easy-school-registration'), 'symbol' => 'Z&#x142;'],
            'PYG' => ['title' => esc_html__('Paraguayan guarani', 'easy-school-registration'), 'symbol' => '&#8370;'],
            'QAR' => ['title' => esc_html__('Qatari riyal', 'easy-school-registration'), 'symbol' => 'QR'],
            'RON' => ['title' => esc_html__('Romanian leu', 'easy-school-registration'), 'symbol' => 'L'],
            'RSD' => ['title' => esc_html__('Serbian dinar', 'easy-school-registration'), 'symbol' => 'din.'],
            'RUB' => ['title' => esc_html__('Russian ruble', 'easy-school-registration'), 'symbol' => 'R'],
            'SAR' => ['title' => esc_html__('Saudi riyal', 'easy-school-registration'), 'symbol' => 'SR'],
            'SBD' => ['title' => esc_html__('Solomon Islands dollar', 'easy-school-registration'), 'symbol' => 'SI$'],
            'SCR' => ['title' => esc_html__('Seychellois rupee', 'easy-school-registration'), 'symbol' => 'SR'],
            'SDG' => ['title' => esc_html__('Sudanese pound', 'easy-school-registration'), 'symbol' => 'SDG'],
            'SEK' => ['title' => esc_html__('Swedish krona', 'easy-school-registration'), 'symbol' => 'kr'],
            'SGD' => ['title' => esc_html__('Singapore dollar', 'easy-school-registration'), 'symbol' => 'S$'],
            'SHP' => ['title' => esc_html__('Saint Helena pound', 'easy-school-registration'), 'symbol' => '&#163;'],
            'SLL' => ['title' => esc_html__('Sierra Leonean leone', 'easy-school-registration'), 'symbol' => 'Le'],
            'SOS' => ['title' => esc_html__('Somali shilling', 'easy-school-registration'), 'symbol' => 'Sh.'],
            'SRD' => ['title' => esc_html__('Surinamese dollar', 'easy-school-registration'), 'symbol' => '$'],
            'SYP' => ['title' => esc_html__('Syrian pound', 'easy-school-registration'), 'symbol' => 'LS'],
            'SZL' => ['title' => esc_html__('Swazi lilangeni', 'easy-school-registration'), 'symbol' => 'E'],
            'THB' => ['title' => esc_html__('Thai baht', 'easy-school-registration'), 'symbol' => '&#3647;'],
            'TJS' => ['title' => esc_html__('Tajikistani somoni', 'easy-school-registration'), 'symbol' => '฿'],
            'TMT' => ['title' => esc_html__('Turkmen manat', 'easy-school-registration'), 'symbol' => 'm'],
            'TND' => ['title' => esc_html__('Tunisian dinar', 'easy-school-registration'), 'symbol' => 'DT'],
            'TRY' => ['title' => esc_html__('Turkish new lira', 'easy-school-registration'), 'symbol' => 'TRY'],
            'TTD' => ['title' => esc_html__('Trinidad and Tobago dollar', 'easy-school-registration'), 'symbol' => 'TT$'],
            'TWD' => ['title' => esc_html__('New Taiwan dollar', 'easy-school-registration'), 'symbol' => 'NT$'],
            'TZS' => ['title' => esc_html__('Tanzanian shilling', 'easy-school-registration'), 'symbol' => 'TZS'],
            'UAH' => ['title' => esc_html__('Ukrainian hryvnia', 'easy-school-registration'), 'symbol' => 'UAH'],
            'UGX' => ['title' => esc_html__('Ugandan shilling', 'easy-school-registration'), 'symbol' => 'USh'],
            'USD' => ['title' => esc_html__('United States dollar', 'easy-school-registration'), 'symbol' => '&#36;'],
            'UYU' => ['title' => esc_html__('Uruguayan peso', 'easy-school-registration'), 'symbol' => '$U'],
            'UZS' => ['title' => esc_html__('Uzbekistani som', 'easy-school-registration'), 'symbol' => 'UZS'],
            'VEB' => ['title' => esc_html__('Venezuelan bolivar', 'easy-school-registration'), 'symbol' => 'Bs'],
            'VND' => ['title' => esc_html__('Vietnamese dong', 'easy-school-registration'), 'symbol' => '&#8363;'],
            'VUV' => ['title' => esc_html__('Vanuatu vatu', 'easy-school-registration'), 'symbol' => 'VT'],
            'WST' => ['title' => esc_html__('Samoan tala', 'easy-school-registration'), 'symbol' => 'WS$'],
            'XAF' => ['title' => esc_html__('Central African CFA franc', 'easy-school-registration'), 'symbol' => 'CFA'],
            'XCD' => ['title' => esc_html__('East Caribbean dollar', 'easy-school-registration'), 'symbol' => 'EC$'],
            'XDR' => ['title' => esc_html__('Special Drawing Rights', 'easy-school-registration'), 'symbol' => 'SDR'],
            'XOF' => ['title' => esc_html__('West African CFA franc', 'easy-school-registration'), 'symbol' => 'CFA'],
            'XPF' => ['title' => esc_html__('CFP franc', 'easy-school-registration'), 'symbol' => 'F'],
            'YER' => ['title' => esc_html__('Yemeni rial', 'easy-school-registration'), 'symbol' => 'YER'],
            'ZAR' => ['title' => esc_html__('South African rand', 'easy-school-registration'), 'symbol' => 'R'],
            'ZMK' => ['title' => esc_html__('Zambian kwacha', 'easy-school-registration'), 'symbol' => 'ZK'],
            'ZWR' => ['title' => esc_html__('Zimbabwean dollar', 'easy-school-registration'), 'symbol' => 'Z$']
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    public function esr_get_currencies()
    {
        return $this->currencies;
    }

    public function esr_get_currencies_for_select()
    {
        $result = [];

        foreach ($this->currencies as $key => $currency) {
            $result[$key] = $currency['title'] . ' (' . $currency['symbol'] . ')';
        }

        return $result;
    }


    public function esr_get_currency()
    {
        $currency = ESR()->settings->esr_get_option('currency', 'USD');

        return apply_filters('esr_currency', $currency);
    }


    public function esr_get_currency_position()
    {
        $currency_position = ESR()->settings->esr_get_option('currency_position', 'after_with_space');

        return apply_filters('esr_currency_position', $currency_position);
    }


    public function esr_currency_symbol($currency = '')
    {
        if (empty($currency)) {
            $currency = $this->esr_get_currency();
        }

        if (!isset($this->currencies[$currency]['symbol'])) {
            return '';
        }

        return $this->currencies[$currency]['symbol'];
    }


    public function prepare_price($price)
    {
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