<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Basic_Statistics {

	/**
	 * @param int $wave_id
	 *
	 * @return object
	 */
	public function esr_get_course_summary_statistics($wave_id) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT SUM(registered_leaders) + SUM(registered_followers) + SUM(registered_solo) AS registrations, SUM(waiting_leaders) + SUM(waiting_followers) + SUM(waiting_solo) AS waiting FROM {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cd.id = cs.course_id WHERE cd.wave_id = %d", [intval($wave_id)]));
	}


	/**
	 * @param int $wave_id
	 *
	 * @return object
	 */
	public function esr_get_registration_statistics($wave_id) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT SUM(CASE WHEN cr.status = %d THEN 1 ELSE 0 END) AS waiting,  SUM(CASE WHEN cr.status = %d THEN 1 ELSE 0 END) AS confirmed,  SUM(CASE WHEN cr.status = %d THEN 1 ELSE 0 END) AS deleted FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cd.id = cr.course_id WHERE cd.wave_id = %d", [ESR_Registration_Status::WAITING, ESR_Registration_Status::CONFIRMED, ESR_Registration_Status::DELETED, intval($wave_id)]));
	}


	/**
	 * @param int $wave_id
	 *
	 * @return object
	 */
	public function esr_get_payment_statistics($wave_id) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT SUM(COALESCE(to_pay, 0)) AS total, SUM(CASE WHEN NOT is_paying THEN COALESCE(to_pay, 0) ELSE 0 END) AS not_paying, SUM(COALESCE(payment, 0)) AS paid, SUM(CASE WHEN is_paying AND payment IS NULL THEN COALESCE(to_pay, 0) ELSE 0 END) AS no_paid FROM {$wpdb->prefix}esr_user_payment WHERE wave_id = %d", [intval($wave_id)]));
	}


}
