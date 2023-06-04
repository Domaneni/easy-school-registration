<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Wave_Courses_Select_Template {

	public static function esr_print_select() {
		$courses = ESR()->course->get_courses_data_by_wave(apply_filters('esr_all_waves_select_get', []));
		$selected_course = apply_filters('esr_wave_courses_select_get', null);

		?>
		<form action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="post" class="esr-select-course">
			<span><?php echo esc_html__('Select Course', 'easy-school-registration') . ': '; ?></span>
			<select name="esr_course">
				<?php
				foreach ($courses as $key => $course) {
					?>
					<option
					value="<?php echo esc_attr($course->id) ?>" <?php if ($course->id == $selected_course) { ?>selected="selected"<?php } ?>>
					<?php echo esc_html(stripslashes($course->title) . ' - ' . ESR()->day->get_day_title($course->day) . ' (' . $course->time_from . '/' . $course->time_to .')'); ?>
					</option><?php
				}
				?>
			</select>
			<input type="submit" name="esr_choose_course_submit" class="page-title-action"  value="<?php esc_attr_e('Select', 'easy-school-registration'); ?>">
		</form>
		<?php
	}


	/**
	 * @param int $wave_id
	 *
	 * @return int
	 */
	public static function esr_get_selected_course($wave_id) {
		$user_saved_course = get_user_meta(get_current_user_id(), 'esr_user_course_id');
		$course_data = null;
		$selected_wave = ($wave_id !== null) ? intval($wave_id) : intval(apply_filters('esr_all_waves_select_get', []));

		if (count($user_saved_course) > 0) {
			$course_data = ESR()->course->get_course_data($user_saved_course[0]);
		}

		if (isset($_POST['esr_choose_course_submit']) && isset($_POST['esr_course'])) {
            $course_id = intval($_POST['esr_course']);
			update_user_meta(get_current_user_id(), 'esr_user_course_id', $course_id);
			return $course_id;
		} else if ((count($user_saved_course) > 0) && $course_data && (intval($course_data->wave_id) === $selected_wave)) {
			return $user_saved_course[0];
		} else {
			$courses = ESR()->course->get_courses_data_by_wave(($selected_wave !== null) ? $selected_wave : apply_filters('esr_all_waves_select_get', []));
			$course  = reset($courses);
			if ($course) {
				update_user_meta(get_current_user_id(), 'esr_user_course_id', $course->id);
				return $course->id;
			}
		}

		return null;
	}
}

add_filter('esr_wave_courses_select_get', ['ESR_Wave_Courses_Select_Template', 'esr_get_selected_course']);
add_action('esr_wave_courses_select_print', ['ESR_Wave_Courses_Select_Template', 'esr_print_select']);