<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_ICS_Generator {

	public static function esr_ics_generate_full_calendar_callback($wave_id) {
		return self::esr_prepare_course_data(ESR()->course->get_active_courses_data_by_wave($wave_id));
	}

	public static function esr_ics_generate_hall_calendar_callback($wave_id, $hall_key) {
		return self::esr_prepare_course_data(ESR()->course->get_courses_data_by_wave_and_hall($wave_id, $hall_key));
	}

	public static function esr_ics_generate_student_calendar_callback($wave_id, $user_id) {
		return self::esr_prepare_course_data(ESR()->registration->get_confirmed_registrations_by_wave_and_user($wave_id, $user_id));
	}

	public static function esr_ics_generate_teacher_calendar_callback($wave_id, $user_id) {
		return self::esr_prepare_course_data(ESR()->course->esr_get_teacher_courses_by_wave($wave_id, $user_id));
	}

	private static function esr_get_weeks_between_two_dates($date1, $date2)
	{
		$first = new \DateTime($date1);
		$second = new \DateTime($date2);
		if($date1 > $date2) return self::esr_get_weeks_between_two_dates($date2, $date1);
		return floor($first->diff($second)->days/7) + 1;
	}

	private static function esr_prepare_course_data($courses)
	{
		$result = [];
		$timezone = get_option('timezone_string');
		foreach ($courses as $key => $course) {
			if (!isset($result[$course->hall_key])) {
				$result['halls'][$course->hall_key]['hall'] = ESR()->hall->get_hall($course->hall_key);
				$result['halls'][$course->hall_key]['timezone'] = $timezone;
			}
			$teachers = ESR()->teacher->get_teachers_names($course->teacher_first, $course->teacher_second);
			$result['halls'][$course->hall_key]['courses'][$course->id] = [
				'id' => $course->id,
				'title' => stripslashes($course->title) . ($course->sub_header ? ' - ' . $course->sub_header : '') . (($teachers !== '') ? (' (' . ESR()->teacher->get_teachers_names($course->teacher_first, $course->teacher_second) . ')') : '') ,
				'from' => str_replace('00:00:00', $course->time_from, $course->course_from),
				'to' => str_replace('00:00:00', $course->time_to, $course->course_from),
				'date_to' => $course->course_to,
				'weeks' => self::esr_get_weeks_between_two_dates($course->course_from, $course->course_to),
			];
		}

		$result['timezone'] = $timezone;

		return $result;
	}

}

add_filter('esr_ics_generate_full_calendar', ['ESR_ICS_Generator', 'esr_ics_generate_full_calendar_callback']);
add_filter('esr_ics_generate_hall_calendar', ['ESR_ICS_Generator', 'esr_ics_generate_hall_calendar_callback'], 10, 2);
add_filter('esr_ics_generate_student_calendar', ['ESR_ICS_Generator', 'esr_ics_generate_student_calendar_callback'], 10, 2);
add_filter('esr_ics_generate_teacher_calendar', ['ESR_ICS_Generator', 'esr_ics_generate_teacher_calendar_callback'], 10, 2);