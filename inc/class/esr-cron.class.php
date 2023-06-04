<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Cron {

	public function __construct() {
		add_action('wp', [$this, 'esr_schedule_cron']);
	}


	public function esr_schedule_cron() {
		$this->daily_cron();
	}


	private function daily_cron() {
		if (!wp_next_scheduled('esr_daily_scheduled_cron')) {
			wp_schedule_event(current_time('timestamp', true), 'daily', 'esr_daily_scheduled_cron');
		}
	}

}

$esr_cron = new ESR_Cron();