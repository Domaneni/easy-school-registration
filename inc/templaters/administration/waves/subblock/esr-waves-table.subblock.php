<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Waves_Table_Subblock_Templater {

	public function print_table() {
		$waves              = ESR()->wave->get_waves_data();
		$user_can_edit      = current_user_can('esr_wave_edit');
		$hall_calendar_html = $this->esr_print_download_calendar_buttons();
		?>
		<h1 class="wp-heading-inline"><?php esc_html_e('Waves', 'easy-school-registration'); ?></h1>
		<?php if ($user_can_edit) { ?>
			<a href="<?php echo esc_url(add_query_arg('wave_id', -1)) ?>" class="esr-add-new page-title-action"><?php esc_html_e('Add New Wave', 'easy-school-registration'); ?></a>
		<?php } ?>
		<table id="datatable" class="wp-list-table widefat fixed striped esr-datatable">
			<colgroup>
				<col width="90">
				<col width="90">
			</colgroup>
			<thead>
			<tr>
				<?php if ($user_can_edit) { ?>
					<th class="esr-filter-disabled" data-key="esr_id"><?php esc_html_e('ID', 'easy-school-registration'); ?></th>
					<th class="esr-filter-disabled no-sort" data-key="esr_actions"><?php esc_html_e('Actions', 'easy-school-registration'); ?></th>
				<?php } ?>
				<th class="esr-filter-disabled" data-key="esr_reg_date"><?php esc_html_e('Title', 'easy-school-registration'); ?></th>
				<th class="esr-filter-disabled" data-key="esr_dancing_as"><?php esc_html_e('Registration From', 'easy-school-registration'); ?></th>
				<th class="esr-filter-disabled no-sort" data-key="esr_dancing_with"><?php esc_html_e('Registration To', 'easy-school-registration'); ?></th>
				<th class="esr-filter-disabled no-sort" data-key="esr_dancing_with"><?php esc_html_e('Download', 'easy-school-registration'); ?></th>
			</tr>
			</thead>
			<tbody class="list">
			<?php foreach ($waves as $wave) { ?>
				<tr class="esr-row<?php echo esc_attr($this->get_passed_class($wave)); ?>"
				    data-id="<?php echo esc_attr($wave->id) ?>">
					<?php if ($user_can_edit) { ?>
						<td><?php echo esc_html($wave->id); ?></td>
						<td class="actions esr-wave">
							<div class="esr-relative">
								<button class="page-title-action"><?php esc_html_e('Actions', 'easy-school-registration') ?></button>
								<?php $this->print_action_box($wave->id); ?>
							</div>
						</td>
					<?php } ?>
					<td class="title" data-value="<?php echo esc_attr($wave->title); ?>"><?php echo esc_html($wave->title); ?></td>
					<td class="registration_from">
						<?php echo esc_html(date(get_option('date_format') . ' ' . get_option('time_format'), strtotime($wave->registration_from))); ?>
					</td>
					<td class="registration_to">
						<?php echo esc_html(date(get_option('date_format') . ' ' . get_option('time_format'), strtotime($wave->registration_to))); ?>
					</td>
					<td class="esr-relative">
						<a href="#" class="esr_choose_calendar"><?php esc_html_e('Choose calendar', 'easy-school-registration'); ?></a>
						<?php echo wp_kses_post($hall_calendar_html); ?>
					</td>
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
				<a href="<?php echo esc_url(add_query_arg('wave_id', $id)) ?>">
					<span class="dashicons dashicons-edit"></span>
					<span><?php esc_html_e('Edit', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<li class="esr-action duplicate">
				<a href="<?php echo esc_url(add_query_arg(['wave_id'=> $id, 'esr_duplicate' => true])) ?>">
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
			<li class="esr-action activate">
				<a href="javascript:;">
					<span class="dashicons dashicons-visibility"></span>
					<span><?php esc_html_e('Set active', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<?php if (ESR()->wave->esr_can_be_removed($id)) { ?>
				<li class="esr-action remove">
					<a href="javascript:;">
						<span class="dashicons dashicons-trash"></span>
						<span><?php esc_html_e('Remove', 'easy-school-registration'); ?></span>
					</a>
				</li>
			<?php } ?>
		</ul>
		<?php
	}


	private function esr_print_download_calendar_buttons() {
		$buttons = '<ul class="esr-download-calendar-buttons"><li><a class="esr-full-calendar-generation"><span class="dashicons dashicons-calendar-alt"></span> ' . esc_html__('Full calendar', 'easy-school-registration') . '</a></li>';

		foreach (ESR()->hall->get_hall_names() as $key => $hall_name) {
			$buttons .= '<li><a class="esr-hall-calendar-generation" data-hall-key="' . esc_attr($key) . '"><span class="dashicons dashicons-calendar-alt"></span> ' . esc_html($hall_name) . '</a></li>';
		}
		$buttons .= '</ul>';

		return $buttons;

	}


	private function get_passed_class($wave) {
		return filter_var($wave->is_passed) ? ' passed' : '';
	}

}
