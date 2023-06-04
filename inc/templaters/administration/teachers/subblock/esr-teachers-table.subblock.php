<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Teachers_Table_Subblock_Templater
{

	public function print_table()
	{
		$teachers = ESR()->teacher->get_teachers_data();
		$user_can_edit = current_user_can('esr_teacher_edit');

		?>
		<table id="datatable" class="wp-list-table widefat fixed striped esr-datatable">
			<colgroup>
				<col width="90">
				<col width="90">
			</colgroup>
			<thead>
			<tr>
				<?php if ($user_can_edit) { ?>
					<th class="esr-filter-disabled" data-key="esr_actions"><?php esc_html_e('ID', 'easy-school-registration'); ?></th>
					<th class="esr-filter-disabled no-sort" data-key="esr_actions"><?php esc_html_e('Actions', 'easy-school-registration'); ?></th>
				<?php } ?>
				<th class="esr-filter-disabled" data-key="esr_reg_date"><?php esc_html_e('Name', 'easy-school-registration'); ?></th>
				<th class="esr-filter-disabled" data-key="esr_dancing_as"><?php esc_html_e('Nickname', 'easy-school-registration'); ?></th>
			</tr>
			</thead>
			<tbody class="list">
			<?php foreach ($teachers as $teacher) { ?>
				<tr class="esr-row<?php echo !$teacher->active ? ' passed' : ''; ?>"
					<?php if ($user_can_edit) { ?>
						data-id="<?php echo esc_attr($teacher->id) ?>"
						data-user-id="<?php echo esc_attr($teacher->user_id) ?>"
						<?php foreach ($teacher as $key => $setting) {
							echo 'data-' . esc_attr($key) . '="' . esc_attr($setting) . '"';
						} ?>
					<?php } ?>>
					<?php if ($user_can_edit) { ?>
						<td><?php echo esc_html($teacher->id); ?></td>
						<td class="esr-teacher actions">
							<div class="esr-relative">
								<button class="page-title-action"><?php esc_html_e('Actions', 'easy-school-registration') ?></button>
								<?php $this->print_action_box($teacher->id); ?>
							</div>
						</td>
					<?php } ?>
					<td class="name"><?php echo esc_html($teacher->name); ?></td>
					<td class="nickname"><?php echo esc_html($teacher->nickname); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php
	}


	private function print_action_box($id)
	{
		?>
		<ul class="esr-actions-box dropdown-menu" data-id="<?php echo esc_attr($id); ?>">
			<li class="esr-action edit">
				<a href="javascript:;">
					<span class="dashicons dashicons-edit"></span>
					<span><?php esc_html_e('Edit', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<li class="esr-action duplicate">
				<a href="javascript:;">
					<span class="dashicons dashicons-admin-page"></span>
					<span><?php esc_html_e('Duplicate', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<li class="esr-action deactivate">
				<a href="javascript:;">
					<span class="dashicons dashicons-hidden"></span>
					<span><?php esc_html_e('Deactivate', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<li class="esr-action activate">
				<a href="javascript:;">
					<span class="dashicons dashicons-visibility"></span>
					<span><?php esc_html_e('Activate', 'easy-school-registration'); ?></span>
				</a>
			</li>
		</ul>
		<?php
	}

}
