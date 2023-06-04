<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Registrations_Edit_Form_Subblock_Templater {

	public function __construct() {
		add_action('esr_registration_form_input', [get_called_class(), 'input_student_email']);
		add_action('esr_registration_form_input', [get_called_class(), 'input_course']);
		add_action('esr_registration_form_input', [get_called_class(), 'input_dancing_as']);
		add_action('esr_registration_form_input', [get_called_class(), 'input_dancing_with']);
		add_action('esr_registration_form_input', [get_called_class(), 'input_partner_email']);
		add_action('esr_registration_form_submit', [get_called_class(), 'input_submit']);
	}


	public function print_content($wave_id) {
		?>
		<div id="esr-edit-box" class="esr-edit-box">
			<span class="close"><span class="dashicons dashicons-no-alt"></span></span>
			<form action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="post">
				<table>
					<?php
					do_action('esr_registration_form_input', $wave_id);
					do_action('esr_registration_form_submit');
					?>
				</table>
			</form>
		</div>
		<?php
	}


	public static function input_student_email() {
		?>
		<tr class="esr-student">
			<th><?php esc_html_e('Student Email', 'easy-school-registration'); ?></th>
			<td><input required type="email" name="student_email">
				<?php ESR_Tooltip_Templater_Helper::print_tooltip(esc_html__('If you want to change the email address, use the Users section in your WordPress. Follow the Documentation on ESR website for more details.', 'easy-school-registration')); ?>
			</td>
		</tr>
		<?php
	}


	public static function input_course($wave_id) {
		?>
		<tr class="esr-student">
			<th><?php esc_html_e('Course', 'easy-school-registration'); ?></th>
			<td>
				<select name="course_id">
					<option value=""><?php esc_html_e('- select -', 'easy-school-registration'); ?></option>
					<?php
					foreach (ESR()->course->get_courses_data_by_wave($wave_id) as $id => $course) {
						?>
						<option value="<?php echo esc_attr($course->id); ?>"><?php echo esc_html(stripslashes($course->title) . ' - ' . ESR()->day->get_day_title($course->day) . ' ' . $course->time_from . '/' . $course->time_to); ?></option><?php
					}
					?>
				</select>
			</td>
		</tr>
		<?php
	}


	public static function input_dancing_with() {
		?>
		<tr class="esr-registered-partner">
			<th><?php esc_html_e('Registered Partner Email', 'easy-school-registration'); ?></th>
			<td><input required type="email" name="dancing_with">
				<?php ESR_Tooltip_Templater_Helper::print_tooltip(esc_html__('This is the email address filled out in the registration form. If the Course uses Automatic pairing mode, the system will be waiting for a student with this email address to register and then pair them together.', 'easy-school-registration')); ?>
			</td>
		</tr>
		<?php
	}


	public static function input_dancing_as() {
		$items = ESR()->dance_as->get_items();
		?>
		<tr class="esr-dancing-role">
			<th><?php esc_html_e('Dancing Role', 'easy-school-registration'); ?></th>
			<td>
				<select name="dancing_as">
					<option value=""><?php esc_html_e('- select -', 'easy-school-registration'); ?></option>
					<?php
					foreach ($items as $id => $item) {
						?>
						<option value="<?php echo esc_attr($id); ?>"><?php echo esc_html($item['title']); ?></option><?php
					}
					?>
				</select>
			</td>
		</tr>
		<?php
	}


	public static function input_partner_email() {
		?>
		<tr class="esr-partner-email">
			<th><?php esc_html_e('Paired Partner Email', 'easy-school-registration'); ?></th>
			<td><input required type="email" name="partner_email">
				<?php ESR_Tooltip_Templater_Helper::print_tooltip(esc_html__('The email address of a Student the system paired this Student with.', 'easy-school-registration')); ?>
			</td>
		</tr>
		<?php
	}


	public static function input_submit() {
		?>
		<tr>
			<th></th>
			<td>
				<input type="hidden" name="registration_id">
				<input type="submit" name="esr_registration_edit_submit" value="<?php esc_attr_e('Save', 'easy-school-registration'); ?>">
			</td>
		</tr>
		<?php
	}
}
