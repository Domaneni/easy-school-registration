<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Course_In_Numbers_Worker
{

	/**
	 * Recount all courses statistics for specific wave
	 *
	 * @param int $wave_id
	 */
	public function esr_recount_wave_statistics($wave_id)
	{
		global $wpdb;

		// Recount registered_leaders
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cs.course_id = cd.id JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.registered_leaders = nc.count WHERE cd.wave_id = %d", [
			$wave_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::LEADER,
			$wave_id
		]));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cs.course_id = cd.id SET cs.registered_leaders = 0 WHERE cd.wave_id = %d AND cs.course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cr.dancing_as = %d)", [
			$wave_id,
			$wave_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::LEADER
		]));

		// Recount registered_followers
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cs.course_id = cd.id JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.registered_followers = nc.count WHERE cd.wave_id = %d", [
			$wave_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::FOLLOWER,
			$wave_id
		]));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cs.course_id = cd.id SET cs.registered_followers = 0 WHERE cd.wave_id = %d AND cs.course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cr.dancing_as = %d)", [
			$wave_id,
			$wave_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::FOLLOWER
		]));

		// Recount waiting_leaders
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cs.course_id = cd.id JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.waiting_leaders = nc.count WHERE cd.wave_id = %d", [
			$wave_id,
			ESR_Registration_Status::WAITING,
			ESR_Dancing_As::LEADER,
			$wave_id
		]));

		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cs.course_id = cd.id SET cs.waiting_leaders = 0 WHERE cd.wave_id = %d AND cs.course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cr.dancing_as = %d)", [
			$wave_id,
			$wave_id,
			ESR_Registration_Status::WAITING,
			ESR_Dancing_As::LEADER
		]));

		// Recount waiting_followers
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cs.course_id = cd.id JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.waiting_followers = nc.count WHERE cd.wave_id = %d", [
			$wave_id,
			ESR_Registration_Status::WAITING,
			ESR_Dancing_As::FOLLOWER,
			$wave_id
		]));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cs.course_id = cd.id SET cs.waiting_followers = 0 WHERE cd.wave_id = %d AND cs.course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cr.dancing_as = %d)", [
			$wave_id,
			$wave_id,
			ESR_Registration_Status::WAITING,
			ESR_Dancing_As::FOLLOWER
		]));

		// Recount registered_solo
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cs.course_id = cd.id JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.registered_solo = nc.count WHERE cd.wave_id = %d", [
			$wave_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::SOLO,
			$wave_id
		]));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN {$wpdb->prefix}esr_course_data AS cd ON cs.course_id = cd.id SET cs.registered_solo = 0 WHERE cd.wave_id = %d AND cs.course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cr.dancing_as = %d)", [
			$wave_id,
			$wave_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::SOLO
		]));

		// Recount waiting_solo
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cd.is_solo AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.waiting_solo = nc.count", [$wave_id, ESR_Registration_Status::WAITING, ESR_Dancing_As::SOLO]));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs SET cs.waiting_solo = 0 WHERE course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.status = %d AND cd.is_solo AND cr.dancing_as = %d)", [$wave_id, ESR_Registration_Status::WAITING, ESR_Dancing_As::SOLO]));
	}


	/**
	 * Recount specific course statistics
	 *
	 * @param int $course_id
	 */
	public function esr_recount_course_statistics_callback($course_id)
	{
		global $wpdb;

		// Recount registered_leaders
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.registered_leaders = nc.count WHERE cs.course_id = %d", [
			$course_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::LEADER,
			$course_id
		]));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs SET cs.registered_leaders = 0 WHERE cs.course_id = %d AND cs.course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d AND cr.dancing_as = %d)", [
			$course_id,
			$course_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::LEADER
		]));

		// Recount registered_followers
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.registered_followers = nc.count WHERE cs.course_id = %d", [
			$course_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::FOLLOWER,
			$course_id
		]));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs SET cs.registered_followers = 0 WHERE cs.course_id = %d AND cs.course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d AND cr.dancing_as = %d)", [
			$course_id,
			$course_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::FOLLOWER
		]));

		// Recount waiting_leaders
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.waiting_leaders = nc.count WHERE cs.course_id = %d", [
			$course_id,
			ESR_Registration_Status::WAITING,
			ESR_Dancing_As::LEADER,
			$course_id
		]));

		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs SET cs.waiting_leaders = 0 WHERE cs.course_id = %d AND cs.course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d AND cr.dancing_as = %d)", [
			$course_id,
			$course_id,
			ESR_Registration_Status::WAITING,
			ESR_Dancing_As::LEADER
		]));

		// Recount waiting_followers
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.waiting_followers = nc.count WHERE cs.course_id = %d", [
			$course_id,
			ESR_Registration_Status::WAITING,
			ESR_Dancing_As::FOLLOWER,
			$course_id
		]));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs SET cs.waiting_followers = 0 WHERE cs.course_id = %d AND cs.course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d AND cr.dancing_as = %d)", [
			$course_id,
			$course_id,
			ESR_Registration_Status::WAITING,
			ESR_Dancing_As::FOLLOWER
		]));

		// Recount registered_solo
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.registered_solo = nc.count WHERE cs.course_id = %d", [
			$course_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::SOLO,
			$course_id
		]));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs SET cs.registered_solo = 0 WHERE cs.course_id = %d AND cs.course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d AND cr.dancing_as = %d)", [
			$course_id,
			$course_id,
			ESR_Registration_Status::CONFIRMED,
			ESR_Dancing_As::SOLO
		]));

		// Recount waiting_solo
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs JOIN (SELECT cr.course_id AS course_id, COUNT(cr.course_id) AS count FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cr.course_id = %d AND cr.status = %d AND cd.is_solo AND cr.dancing_as = %d GROUP BY cr.course_id) AS nc ON cs.course_id = nc.course_id SET cs.waiting_solo = nc.count", [$course_id, ESR_Registration_Status::WAITING, ESR_Dancing_As::SOLO]));
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}esr_course_summary AS cs SET cs.waiting_solo = 0 WHERE course_id NOT IN (SELECT cr.course_id AS course_id FROM {$wpdb->prefix}esr_course_registration AS cr WHERE cd.course_id = %d AND cr.status = %d AND cd.is_solo AND cr.dancing_as = %d)", [$course_id, ESR_Registration_Status::WAITING, ESR_Dancing_As::SOLO]));
	}
}

add_action('esr_recount_course_statistics', ['ESR_Course_In_Numbers_Worker', 'esr_recount_course_statistics_callback']);
