<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Course
{

	private $fields;


	/**
	 * @codeCoverageIgnore
	 */
	public function __construct()
	{
		$this->fields = new ESR_Fields();

		$this->add_field('title', 'string', true);
		$this->add_field('wave_id', 'int', true);
		$this->add_field('sub_header', 'string', false);
		$this->add_field('teacher_first', 'int', false);
		$this->add_field('teacher_second', 'int', false);
		$this->add_field('course_from', 'datetime', false);
		$this->add_field('course_to', 'datetime', false);
		$this->add_field('day', 'int', false);
		$this->add_field('time_from', 'string', false);
		$this->add_field('time_to', 'string', false);
		$this->add_field('is_solo', 'boolean', false);
		$this->add_field('max_leaders', 'int', false);
		$this->add_field('max_followers', 'int', false);
		$this->add_field('max_solo', 'int', false);
		$this->add_field('price', 'int', false);
		$this->add_field('is_passed', 'boolean', false);
		$this->add_field('hall_key', 'string', false);
		$this->add_field('group_id', 'int', false);
		$this->add_field('level_id', 'int', false);
		$this->add_field('pairing_mode', 'int', false);
		$this->add_field('enforce_partner', 'boolean', false);
	}


	public static function esr_get_course_settings_preferences_callback() {
		return [
			'disable_registration' => [
				'type' => 'checkbox'
			],
			'dance_as_solo_rewrite' => [
				'type' => 'text'
			],
		];
	}


	/**
	 * @param int $course_id
	 *
	 * @return object
	 */
	public function get_course_data($course_id)
	{
		global $wpdb;

		$course = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_course_data WHERE id = %d", [intval($course_id)]));

        if ($course !== null) {
			$course->real_price = $this->get_course_price($course_id, $course->wave_id);
			return $this->esr_prepare_course_settings($course);
		}

		return $course;
	}


	/**
	 * @param int $wave_id
	 * @param boolean $as_array
	 *
	 * @return array
	 */
	public function get_courses_data_by_wave($wave_id, $as_array = false, $transform_settings = false)
	{
		global $wpdb;
		$data = $wpdb->get_results($wpdb->prepare("SELECT *, " . $this->esr_get_course_price_sql($wave_id) . " AS real_price FROM {$wpdb->prefix}esr_course_data WHERE wave_id = %d ORDER BY day, hall_key, time_from, time_to, id", [intval($wave_id)]));

        $courses_data = $this->get_courses_as_array($data);

        if ($as_array && $transform_settings) {
            foreach ($courses_data as $key => $course) {
                $courses_data[$key] = $this->esr_prepare_course_settings($course);
            }
        }

        return $courses_data;
	}


	/**
	 * @param int $wave_id
	 * @param string $hall_key
	 *
	 * @return array
	 */
	public function get_courses_data_by_wave_and_hall($wave_id, $hall_key)
	{
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_course_data WHERE wave_id = %d AND hall_key = %d", [intval($wave_id), intval($hall_key)]));
	}


	/**
	 * @param int $wave_id
	 * @param int $group_id
	 * @param boolean $as_array
	 *
	 * @return array
	 */
	public function get_course_data_by_wave_and_group($wave_id, $group_id, $as_array = false)
	{
		global $wpdb;
		$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}esr_course_data WHERE wave_id = %d AND group_id = %d ORDER BY day, hall_key, time_from, time_to, id", [intval($wave_id), intval($group_id)]));

		return $this->check_array_transform($data, $as_array);
	}


	/**
	 * @param int $wave_id
	 * @param int $group_id
	 * @param boolean $as_array
	 *
	 * @return array
	 */
	public function get_active_courses_data_by_wave($wave_id, $group_id = null, $as_array = false)
	{
		global $wpdb;
		$args = [intval($wave_id)];
		$sql = "SELECT *, " . $this->esr_get_course_price_sql($wave_id) . " AS real_price FROM {$wpdb->prefix}esr_course_data WHERE wave_id = %d ";

		if ($group_id !== null) {
			$sql .= " AND group_id IN (" . implode(',', $group_id) . ") ";
		}

		$sql .= " AND NOT is_passed ORDER BY day, hall_key, time_from, time_to, id";

		$data = $wpdb->get_results($wpdb->prepare($sql, $args));

		return $this->check_array_transform($data, $as_array);
	}


	/**
	 * @param $wave_id
	 * @param $user_id
	 * @param bool $as_array
	 *
	 * @return array
	 *
	 * @codeCoverageIgnore
	 */
	public function get_courses_data_by_wave_and_user($wave_id, $user_id, $as_array = false)
	{
		global $wpdb;
		$data = $wpdb->get_results($wpdb->prepare("SELECT cd.* FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.is_passed = %d AND cd.wave_id = %d AND cr.user_id = %d ORDER BY cd.wave_id DESC", [1, intval($wave_id), intval($user_id)]));

		return $this->check_array_transform($data, $as_array);
	}


	/**
	 * @param int $course_id
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public function is_course_already_registered($course_id, $user_id)
	{
		global $wpdb;

        if (intval(ESR()->settings->esr_get_option('allow_multiple_registrations', -1)) === 1) {
            return false;
        }

		return (boolean)$wpdb->query($wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}esr_course_registration WHERE course_id = %d AND user_id = %d", [intval($course_id), intval($user_id)]));
	}


	/**
	 * @param int $course_id
	 *
	 * @return bool
	 */
	public function is_course_solo($course_id)
	{
		global $wpdb;

		return (boolean)$wpdb->query($wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}esr_course_data WHERE id = %d AND is_solo = %d", [intval($course_id), 1]));
	}


	/**
	 * @return array
	 */
	public function get_courses_data()
	{
		global $wpdb;

		return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}esr_course_data ORDER BY wave_id DESC, day, time_from");
	}


	/**
	 * @param int $course_id
	 * @param int $wave_id
	 *
	 * @return int
	 */
	public function get_course_price($course_id, $wave_id)
	{
		global $wpdb;

		return $wpdb->get_var($wpdb->prepare("SELECT " . $this->esr_get_course_price_sql($wave_id) . " AS price FROM {$wpdb->prefix}esr_course_data WHERE id = %d", [intval($course_id)]));
	}


	/**
	 * @param int $course_id
	 *
	 * @return bool
	 */
	public function is_course_enabled($course_id)
	{
		if ($this->is_course_solo($course_id)) {
			return ESR()->dance_as->is_solo_registration_enabled($course_id);
		} else {
			return ESR()->dance_as->is_leader_registration_enabled($course_id) || ESR()->dance_as->is_followers_registration_enabled(intval($course_id));
		}
	}


	/**
	 * @param int $course_id
	 *
	 * @return bool
	 */
	public function is_partner_enforcement_enabled($course_id)
	{
		global $wpdb;

		return (boolean)$wpdb->get_var($wpdb->prepare("SELECT enforce_partner FROM {$wpdb->prefix}esr_course_data AS cd WHERE cd.id = %d", [intval($course_id)]));
	}


	/**
	 * @return array
	 */
	public function prepare_all_courses_by_waves()
	{
		$results = $this->get_courses_data();
		$courses = [];

		foreach ($results as $result) {
			$courses[$result->wave_id][$result->id] = $result;
		}

		return $courses;
	}


	/**
	 * @return object
	 */
	public function get_fields()
	{
		return $this->fields->get_fields();
	}


	public function esr_get_course_price_sql($wave_id)
	{
		return apply_filters('esr_sql_course_price_string', ['wave_id' => $wave_id, 'sql' => 'price'])['sql'];
	}


	/**
	 * @param array $results
	 *
	 * @return array
	 */
	private function get_courses_as_array($results)
	{
		$courses = [];
		foreach ($results as $result) {
            $courses[$result->id] = $result;
		}

		return $courses;
	}


	private function check_array_transform($data, $as_array)
	{
		if (!$as_array) {
			return $data;
		}

		return $this->get_courses_as_array($data);
	}


	public function add_field($key, $type, $required)
	{
		$this->fields->add_field($key, $type, $required);
	}


	public static function esr_remove_course_data_callback($course_id)
	{
		global $wpdb;
		$wpdb->delete($wpdb->prefix . 'esr_course_data', ['id' => intval($course_id)]);
	}


	/**
	 * @param int $wave_id
	 * @param int $user_id
	 *
	 * @return array
	 */
	public function esr_get_teacher_courses_by_wave($wave_id, $user_id)
	{
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT cd.*, CASE WHEN firsttd.user_id = %d THEN secondtd.name ELSE firsttd.name END AS teacher_name FROM {$wpdb->prefix}esr_course_data AS cd LEFT JOIN {$wpdb->prefix}esr_teacher_data AS firsttd ON cd.teacher_first = firsttd.id LEFT JOIN {$wpdb->prefix}esr_teacher_data AS secondtd ON cd.teacher_second = secondtd.id WHERE (firsttd.user_id = %d OR secondtd.user_id = %d) AND cd.wave_id = %d", [intval($user_id), intval($user_id), intval($user_id), intval($wave_id)]));
	}


	public function esr_get_courses_for_schedule_by_wave($wave_id)
	{
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM (SELECT cd.id AS id, max_leaders, pairing_mode, max_followers, max_solo, group_id, level_id, title, wave_id, sub_header, teacher_first, teacher_second, course_from, course_to, is_solo, price, cds.day, cd.hall_key, cds.time_from, cds.time_to, course_settings, description_type, course_link, description FROM {$wpdb->prefix}esr_course_data AS cd JOIN {$wpdb->prefix}esr_course_dates AS cds ON cd.id = cds.course_id WHERE cd.wave_id = %d AND NOT cd.is_passed UNION SELECT cd.id AS id, max_leaders, pairing_mode, max_followers, max_solo, group_id, level_id, title, wave_id, sub_header, teacher_first, teacher_second, course_from, course_to, is_solo, price, cd.day, cd.hall_key, cd.time_from, cd.time_to, course_settings, description_type, course_link, description FROM {$wpdb->prefix}esr_course_data AS cd WHERE cd.wave_id = %d AND NOT cd.is_passed AND NOT EXISTS (SELECT 1 FROM {$wpdb->prefix}esr_course_dates WHERE course_id = cd.id)) AS courses ORDER BY courses.day, courses.hall_key, courses.time_from, courses.time_to, courses.id", [intval($wave_id), intval($wave_id)]));
	}


	public function esr_get_courses_for_registration_by_wave_and_group($wave_id, $group_id)
	{
		global $wpdb;
		$args = [intval($wave_id)];
		$sql = "SELECT * FROM (SELECT cd.id AS id, enforce_partner, max_leaders, pairing_mode, max_followers, max_solo, group_id, level_id, title, wave_id, sub_header, description_type, course_link, description, teacher_first, teacher_second, course_from, course_to, is_solo, price, cds.day, cd.hall_key, cds.time_from, cds.time_to, course_settings, " . $this->esr_get_course_price_sql($wave_id) . " AS real_price FROM {$wpdb->prefix}esr_course_data AS cd JOIN {$wpdb->prefix}esr_course_dates AS cds ON cd.id = cds.course_id WHERE wave_id = %d ";

		if ($group_id !== null) {
			$sql .= " AND group_id IN (" . implode(',', $group_id) . ") ";
		}


		$sql .= " AND NOT is_passed UNION SELECT cd.id AS id, enforce_partner, max_leaders, pairing_mode, max_followers, max_solo, group_id, level_id, title, wave_id, sub_header, description_type, course_link, description, teacher_first, teacher_second, course_from, course_to, is_solo, price, cd.day, cd.hall_key, cd.time_from, cd.time_to, course_settings, " . $this->esr_get_course_price_sql($wave_id) . " AS real_price FROM {$wpdb->prefix}esr_course_data AS cd WHERE wave_id = %d ";

		$args[] = intval($wave_id);

		if ($group_id !== null) {
			$sql .= " AND group_id IN (" . implode(',', $group_id) . ") ";
		}

		$sql .= " AND NOT is_passed AND NOT EXISTS (SELECT 1 FROM {$wpdb->prefix}esr_course_dates WHERE course_id = cd.id)) AS courses ORDER BY courses.day, courses.hall_key, courses.time_from, courses.time_to, courses.id";

		return $wpdb->get_results($wpdb->prepare($sql, $args));
	}


	public function esr_prepare_course_settings($result) {
		$course = clone $result;
		if (isset($course->course_settings)) {
			$settings = $course->course_settings;
			unset($course->course_settings);

			return (object) array_merge((array) $course, (array) json_decode($settings, true));
		} else { //Historical check for not updated tables
			return $course;
		}
	}

}

add_action('esr_remove_course_data', ['ESR_Course', 'esr_remove_course_data_callback']);

add_filter('esr_get_course_settings_preferences', ['ESR_Course', 'esr_get_course_settings_preferences_callback']);