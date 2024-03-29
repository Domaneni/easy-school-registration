<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Dancing_As {
	const
		LEADER = 0, FOLLOWER = 1, SOLO = 2;

	private $items = [];


	public function __construct() {
		$this->items = [
			self::LEADER   => [
                'id' => self::LEADER,
				'title' => esc_html__('Leader', 'easy-school-registration'),
                'settings_label' => 'leader_label'
			],
			self::FOLLOWER => [
                'id' => self::FOLLOWER,
				'title' => esc_html__('Follower', 'easy-school-registration'),
                'settings_label' => 'follower_label'
			],
			self::SOLO => [
                'id' => self::SOLO,
				'title' => esc_html__('Solo', 'easy-school-registration')
			]
		];
	}


	public function get_items() {
		return $this->items;
	}


	public function get_item($key) {
		$items = $this->get_items();

		if (isset($items[$key])) {
			return $items[$key];
		}

		return false;
	}


	public function get_title($key, $course_data = null) {
		$item = $this->get_item($key);

		if ($item) {
            if ($this->is_solo($key) && !empty($course_data) && !empty($course_data->dance_as_solo_rewrite)) {
                return $course_data->dance_as_solo_rewrite;
            }

            if (!empty($item['settings_label'])) {
                return ESR()->settings->esr_get_option( $item['settings_label'], $item['title']);
            }

			return $item['title'];
		}

		return '';
	}


	/**
	 * @param int $key
	 *
	 * @return bool
	 */
	public function is_leader($key) {
		return $key == self::LEADER;
	}


	/**
	 * @param int $key
	 *
	 * @return bool
	 */
	public function is_follower($key) {
		return $key == self::FOLLOWER;
	}


	/**
	 * @param int $key
	 *
	 * @return bool
	 */
	public function is_solo($key) {
		return $key == self::SOLO;
	}


	/**
	 * @param int $course_id
	 *
	 * @return bool
	 */
	public function is_leader_registration_enabled($course_id) {
		global $wpdb;
		return (boolean) $wpdb->query($wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}esr_course_data AS cd JOIN {$wpdb->prefix}esr_course_summary AS cs ON cd.id = cs.course_id WHERE cd.id = %d AND cd.max_leaders > cs.registered_leaders", [intval($course_id)]));
	}


	/**
	 * @param int $course_id
	 *
	 * @return bool
	 */
	public function is_followers_registration_enabled($course_id) {
		global $wpdb;
		return (boolean) $wpdb->query($wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}esr_course_data AS cd JOIN {$wpdb->prefix}esr_course_summary AS cs ON cd.id = cs.course_id WHERE cd.id = %d AND cd.max_followers > cs.registered_followers", [intval($course_id)]));
	}


	/**
	 * @param int $course_id
	 *
	 * @return bool
	 */
	public function is_solo_registration_enabled($course_id) {
		global $wpdb;
		return (boolean) $wpdb->query($wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}esr_course_data AS cd JOIN {$wpdb->prefix}esr_course_summary AS cs ON cd.id = cs.course_id WHERE cd.id = %d AND cd.max_solo > cs.registered_solo", [intval($course_id)]));
	}
}