<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Waves_Edit_Form_Subblock_Templater {

	public function __construct() {
		add_action('esr_wave_edit_form_input', [get_called_class(), 'input_title']);
		add_action('esr_wave_edit_form_input', [get_called_class(), 'input_registration_from']);
		add_action('esr_wave_edit_form_input', [get_called_class(), 'input_registration_to']);
		add_action('esr_wave_edit_form_input', [get_called_class(), 'input_courses_from']);
		add_action('esr_wave_edit_form_input', [get_called_class(), 'input_courses_to']);
		add_action('esr_wave_edit_form_submit', [get_called_class(), 'input_submit'], 10, 2);
	}


	public function print_content($wave_id = null, $duplicate = 0) {
		$wave = ESR()->wave->get_wave_data($wave_id);

		if ($wave !== null) {
			$wave->wave_settings = json_decode($wave->wave_settings);
		}
		?>
		<div>
			<form action="<?php echo remove_query_arg(['esr_duplicate', 'wave_id']) ?>" method="post" class="esr-edit-form">
				<h3><?php esc_html_e('Edit Wave', 'easy-school-registration'); ?></h3>
				<table>
					<?php
					do_action('esr_wave_edit_form_input', $wave);
					do_action('esr_wave_edit_form_submit', $wave, $duplicate);
					?>
				</table>
			</form>
		</div>
		<?php
	}


	public static function input_title($wave) {
		?>
		<tr>
			<th><?php esc_html_e('Title', 'easy-school-registration'); ?></th>
			<td><input type="text" name="title" value="<?php echo ($wave !== null ? esc_attr($wave->title) : ''); ?>"></td>
		</tr>
		<?php
	}


	public static function input_registration_from($wave) {
		$registration_from = isset($wave->registration_from) ? date('Y-m-d', strtotime($wave->registration_from)) : '';
		$registration_time_from = isset($wave->registration_from) ? date('H:i', strtotime($wave->registration_from)) : '';
		?>
		<tr>
			<th><?php esc_html_e('Registration From', 'easy-school-registration'); ?></th>
			<td><input name="registration_from" type="date" value="<?php echo esc_attr($registration_from); ?>"><input name="registration_from_time" type="time" value="<?php echo esc_attr($registration_time_from); ?>">
            <?php ESR_Tooltip_Templater_Helper::print_tooltip(esc_html__('Select time and date when the Registration Form becomes available. Before this date, studens will see only the text defined in the Settings -> Schedule/Registration -> Open/Closed.', 'easy-school-registration')); ?>
            </td>
		</tr>
		<?php
	}


	public static function input_registration_to($wave) {
		$registration_to = isset($wave->registration_to) ? date('Y-m-d', strtotime($wave->registration_to)) : '';
		$registration_time_to = isset($wave->registration_to) ? date('H:i', strtotime($wave->registration_to)) : '';
		?>
		<tr>
			<th><?php esc_html_e('Registration To', 'easy-school-registration'); ?></th>
			<td><input name="registration_to" type="date" value="<?php echo esc_attr($registration_to); ?>"><input name="registration_to_time" type="time" value="<?php echo esc_attr($registration_time_to); ?>">
            <?php ESR_Tooltip_Templater_Helper::print_tooltip(esc_html__('Select time and date when the Registration Form becomes not available. After this date, studens will see only the text defined in the Settings -> Schedule/Registration -> Open/Closed.', 'easy-school-registration')); ?>
            </td>
		</tr>
		<?php
	}


	public static function input_courses_from($wave) {
		?>
		<tr>
			<th><?php esc_html_e('Courses From', 'easy-school-registration'); ?></th>
			<td><input name="wave_settings[courses_from]" type="date" class="esr-course-date-from" value="<?php echo isset($wave->wave_settings->courses_from) ? esc_attr($wave->wave_settings->courses_from) : ''; ?>">
				<?php ESR_Tooltip_Templater_Helper::print_tooltip(esc_html__('Select time and date when the Courses assigned to this Wave start.', 'easy-school-registration')); ?>
			</td>
		</tr>
		<?php
	}


	public static function input_courses_to($wave) {
		?>
		<tr>
			<th><?php esc_html_e('Courses To', 'easy-school-registration'); ?></th>
			<td><input name="wave_settings[courses_to]" type="date" class="esr-course-date-to" value="<?php echo isset($wave->wave_settings->courses_to) ? esc_attr($wave->wave_settings->courses_to) : ''; ?>">
				<?php ESR_Tooltip_Templater_Helper::print_tooltip(esc_html__('Select time and date when the Courses assigned to this Wave end.', 'easy-school-registration')); ?>
			</td>
		</tr>
		<?php
	}


	public static function input_submit($wave, $duplicate) {
		?>
		<tr>
			<th></th>
			<td>
				<input type="hidden" name="wave_id" value="<?php echo ($wave !== null) && ($duplicate === 0) ? esc_attr($wave->id) : ''; ?>">
				<input type="submit" name="esr_save_wave" value="<?php esc_attr_e('Save', 'easy-school-registration'); ?>">
			</td>
		</tr>
		<?php
	}
}
