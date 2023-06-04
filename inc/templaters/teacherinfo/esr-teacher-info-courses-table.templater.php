<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Teacher_Info_Courses_Table {

	public function print_content($wave_id) {
		$courses = ESR()->course->esr_get_teacher_courses_by_wave($wave_id, get_current_user_id());
		?>
		<table id="datatable" class="table table-default table-bordered esr-datatable">
		<thead>
		<tr>
			<th><?php esc_html_e('Course', 'easy-school-registration'); ?></th>
			<th><?php esc_html_e('Day', 'easy-school-registration'); ?></th>
			<th><?php esc_html_e('Time', 'easy-school-registration'); ?></th>
			<th><?php esc_html_e('Hall', 'easy-school-registration'); ?></th>
			<th><?php esc_html_e('Teaching with', 'easy-school-registration'); ?></th>
		</tr>
		</thead>
		<tbody class="list">
		<?php
		if (!empty($courses)) {
			foreach ($courses as $key => $course) { ?>
				<tr class="esr-row">
					<td><?php echo esc_html(stripslashes($course->title) . ($course->sub_header ? ' - ' . $course->sub_header : '')) ?></td>
					<td><?php echo esc_html(ESR()->day->get_day_title($course->day)) ?></td>
					<td><?php echo esc_html($course->time_from . ' - ' . $course->time_to) ?></td>
					<td><?php echo esc_html(ESR()->hall->get_hall_name($course->hall_key)) ?></td>
					<td><?php echo esc_html($course->teacher_name) ?></td>
				</tr><?php
			}
		}
		?>
		</tbody>
		</table><?php
	}

}