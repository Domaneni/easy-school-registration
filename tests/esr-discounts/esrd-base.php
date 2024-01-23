<?php

include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';

class ESRD_Base {

	public $base;


	public function __construct() {
		$this->base = new ESR_Base_Test();
	}


    public function delete_all_data() {
        global $wpdb;

        $this->base->delete_all_data();

        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esrd_discount");
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esrd_wave_discount");
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esrd_time_discount");
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esrd_checkbox_discount");

        wp_cache_flush();
    }


	public function add_discount($wave_ids, $data) {
		global $wpdb;
		$worker = new ESRD_Discount_Worker();

		$worker->process_discount([
			                          'esrd_wave' => $wave_ids,
		                          ]+$data);

		return $wpdb->get_var("SELECT id FROM {$wpdb->prefix}esrd_discount ORDER BY id DESC LIMIT 1");
	}


	public function add_time_discount($data) {
		global $wpdb;
		$worker = new ESRD_Time_Discount_Worker();

		$worker->process_discount($data);

		return $wpdb->get_var("SELECT id FROM {$wpdb->prefix}esrd_time_discount ORDER BY id DESC LIMIT 1");
	}


	public function add_checkbox_discount($data) {
		global $wpdb;
		$worker = new ESRD_Checkbox_Discount_Worker();

		$worker->process_discount($data);

		return $wpdb->get_var("SELECT id FROM {$wpdb->prefix}esrd_checkbox_discount ORDER BY id DESC LIMIT 1");
	}

	public function load_user_payment_by_email($user_email) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT up.* FROM {$wpdb->prefix}esr_user_payment AS up
							   JOIN {$wpdb->users} AS u ON u.ID = up.user_id AND u.user_email = %s", [$user_email]));
	}

	public function load_user_payment_by_email_and_wave($user_email, $wave_id) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT up.* FROM {$wpdb->prefix}esr_user_payment AS up
							   JOIN {$wpdb->users} AS u ON u.ID = up.user_id AND u.user_email = %s WHERE up.wave_id = %d", [$user_email, $wave_id]));
	}

}
