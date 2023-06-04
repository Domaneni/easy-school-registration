<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Courses_Table_Subblock_Templater {

	public function print_table() {
		$courses = ESR()->course->get_courses_data();
		$waves   = ESR()->wave->get_waves_data(true);
		$user_can_edit = current_user_can('esr_course_edit');

		?>
		<h1 class="wp-heading-inline"><?php esc_html_e('Courses', 'easy-school-registration'); ?></h1>
		<?php if ($user_can_edit) { ?>
			<a href="<?php echo esc_url(add_query_arg('course_id', -1)) ?>" class="esr-add-new page-title-action"><?php esc_html_e('Add New Course', 'easy-school-registration'); ?></a>
		<?php } ?>
		<table id="datatable" class="wp-list-table widefat fixed striped esr-datatable">
			<colgroup>
				<col width="90">
				<col width="90">
				<col width="100">
			</colgroup>
			<thead>
			<tr>
				<?php if ($user_can_edit) { ?>
					<th class="esr-filter-disabled" data-key="esr_id"><?php esc_html_e('ID', 'easy-school-registration'); ?></th>
					<th class="esr-filter-disabled no-sort" data-key="esr_actions"><?php esc_html_e('Actions', 'easy-school-registration'); ?></th>
				<?php } ?>
				<th class="no-sort" data-key="esr_status"><?php esc_html_e('Status', 'easy-school-registration'); ?></th>
				<th class="no-sort" data-key="esr_title"><?php esc_html_e('Title', 'easy-school-registration'); ?></th>
				<th class="no-sort" data-key="esr_date"><?php esc_html_e('Date', 'easy-school-registration'); ?></th>
				<th class="no-sort" data-key="esr_wave"><?php esc_html_e('Wave', 'easy-school-registration'); ?></th>
			</tr>
			</thead>
			<tbody class="list">
			<?php foreach ($courses as $course) { ?>
				<tr class="esr-row<?php echo ($course->is_passed ? ' passed' : ''); ?>"
					data-id="<?php echo esc_attr($course->id); ?>">
					<?php if ($user_can_edit) { ?>
						<td><?php echo esc_html($course->id); ?></td>
						<td class="actions esr-course">
							<div class="esr-relative">
								<button class="page-title-action"><?php esc_html_e('Actions', 'easy-school-registration') ?></button>
								<?php $this->print_action_box($course->id); ?>
							</div>
						</td>
					<?php } ?>
					<td class="esr_course_status"><?php echo ($course->is_passed ? esc_html__('Passed', 'easy-school-registration') : esc_html__('Active', 'easy-school-registration')); ?></td>
					<td><?php echo esc_html(stripslashes($course->title)); ?></td>
					<td><?php if ($course->day && $course->time_from && $course->time_to) { echo esc_html(ESR()->day->get_day_title($course->day) . ' - ' . $course->time_from . '/' . $course->time_to); } ?></td>
					<td><?php echo esc_html('(' . $course->wave_id . ') ' . $waves[$course->wave_id]->title); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php
	}


	private function print_action_box($id) {
		?>
		<ul class="esr-actions-box dropdown-menu" data-id="<?php echo esc_attr($id); ?>">
			<li class="esr-action edit">
				<a href="<?php echo esc_url(add_query_arg('course_id', $id)) ?>">
					<span class="dashicons dashicons-edit"></span>
					<span><?php esc_html_e('Edit', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<li class="esr-action duplicate">
				<a href="<?php echo esc_url(add_query_arg(['course_id'=> $id, 'esr_duplicate' => true])) ?>">
					<span class="dashicons dashicons-admin-page"></span>
					<span><?php esc_html_e('Duplicate', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<li class="esr-action deactivate">
				<a href="javascript:;">
					<span class="dashicons dashicons-hidden"></span>
					<span><?php esc_html_e('Set passed', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<li class="esr-action remove-forever">
				<a href="javascript:;">
					<span class="dashicons dashicons-trash"></span>
					<span><?php esc_html_e('Delete forever', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<li class="esr-action activate">
				<a href="javascript:;">
					<span class="dashicons dashicons-visibility"></span>
					<span><?php esc_html_e('Set active', 'easy-school-registration'); ?></span>
				</a>
			</li>
		</ul>
		<?php
	}

}