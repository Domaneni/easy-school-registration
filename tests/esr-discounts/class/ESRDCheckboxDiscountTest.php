<?php

include_once ESR_PLUGIN_PATH . '/tests/esr-discounts/esrd-base.php';

class ESRDCheckboxDiscountTest extends WP_UnitTestCase
{

    private $esrd_base;


    public function __construct()
    {
        parent::__construct();
        $this->esrd_base = new ESRD_Base();
    }


    public function setUp(): void
    {
        parent::setUp();
        $this->esrd_base->delete_all_data();
        $this->esrd_base->base->setUp();
    }


    public function test_load_all_time_discounts()
    {
        $wave_id = $this->esrd_base->base->add_wave();

        $discount_id = $this->esrd_base->add_checkbox_discount([
            'esrd_wave' => $wave_id,
            'esrd_disc_cond_how' => 2,
            'esrd_discount_value' => 10,
            'esrd_discount_text' => 'Checkbox discount text',
        ]);

        $this->assertEquals(1, count(ESRD()->checkbox_discount->esrd_load_all_discounts()));

        $wave2_id = $this->esrd_base->base->add_wave();

        $this->esrd_base->add_checkbox_discount([
            'esrd_wave' => $wave2_id,
            'esrd_disc_cond_how' => 2,
            'esrd_discount_value' => 20,
            'esrd_discount_text' => 'Checkbox discount text',
        ]);

        $this->assertEquals(2, count(ESRD()->checkbox_discount->esrd_load_all_discounts()));

        do_action('esrd_remove_checkbox_discount', $discount_id);

        $this->assertEquals(1, count(ESRD()->checkbox_discount->esrd_load_all_discounts()));
    }


    public function test_load_waves_with_discounts()
    {
        $wave_id = $this->esrd_base->base->add_wave();
        $wave2_id = $this->esrd_base->base->add_wave();

        $this->assertEquals([], ESRD()->checkbox_discount->esrd_load_waves_with_discounts());

        $discount_id = $this->esrd_base->add_checkbox_discount([
            'esrd_wave' => $wave_id,
            'esrd_disc_cond_how' => 2,
            'esrd_discount_value' => 10,
            'esrd_discount_text' => 'Checkbox discount text',
        ]);

        $this->assertEquals([$wave_id => $wave_id], ESRD()->checkbox_discount->esrd_load_waves_with_discounts());

        $this->esrd_base->add_checkbox_discount([
            'esrd_wave' => $wave2_id,
            'esrd_disc_cond_how' => 2,
            'esrd_discount_value' => 20,
            'esrd_discount_text' => 'Checkbox discount text',
        ]);

        $this->assertEquals([$wave_id => $wave_id, $wave2_id => $wave2_id], ESRD()->checkbox_discount->esrd_load_waves_with_discounts());

        do_action('esrd_remove_checkbox_discount', $discount_id);

        $this->assertEquals([$wave2_id => $wave2_id], ESRD()->checkbox_discount->esrd_load_waves_with_discounts());
    }


    public function test_get_discount_by_wave()
    {
        $wave_id = $this->esrd_base->base->add_wave();
        $wave2_id = $this->esrd_base->base->add_wave();

        $this->assertEquals(null, ESRD()->checkbox_discount->esrd_get_discount_by_wave($wave_id));
        $this->assertEquals(null, ESRD()->checkbox_discount->esrd_get_discount_by_wave($wave2_id));

        $this->esrd_base->add_checkbox_discount([
            'esrd_wave' => $wave_id,
            'esrd_disc_cond_how' => 2,
            'esrd_discount_value' => 10,
            'esrd_discount_text' => 'Checkbox discount text',
        ]);

        $this->assertNotEquals(null, ESRD()->checkbox_discount->esrd_get_discount_by_wave($wave_id));
        $this->assertEquals(null, ESRD()->checkbox_discount->esrd_get_discount_by_wave($wave2_id));

        $this->esrd_base->add_checkbox_discount([
            'esrd_wave' => $wave2_id,
            'esrd_disc_cond_how' => 2,
            'esrd_discount_value' => 20,
            'esrd_discount_text' => 'Checkbox discount text',
        ]);

        $this->assertNotEquals(null, ESRD()->checkbox_discount->esrd_get_discount_by_wave($wave_id));
        $this->assertNotEquals(null, ESRD()->checkbox_discount->esrd_get_discount_by_wave($wave2_id));
    }

}
