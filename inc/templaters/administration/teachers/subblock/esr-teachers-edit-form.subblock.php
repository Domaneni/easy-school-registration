<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Teachers_Edit_Form_Subblock_Templater {

	public function __construct() {
		add_action('esr_teacher_edit_form_input', [get_called_class(), 'input_full_name']);
		add_action('esr_teacher_edit_form_input', [get_called_class(), 'input_nickname']);
		add_action('esr_teacher_edit_form_input', [get_called_class(), 'input_user']);
		add_action('esr_teacher_edit_form_input', [get_called_class(), 'input_teacher_settings']);
		add_action('esr_teacher_edit_form_submit', [get_called_class(), 'input_submit']);
	}


	public function print_content() {
		?>
		<div id="esr-edit-box" class="esr-edit-box esr-teacher-edit-box">
			<span class="close"><span class="dashicons dashicons-no-alt"></span></span>
			<form action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="post">
				<h3><?php esc_html_e('Main Info', 'easy-school-registration'); ?></h3>
				<table>
					<colgroup>
						<col width="110">
						<col width="250">
					</colgroup>
					<?php
					do_action('esr_teacher_edit_form_input');
					do_action('esr_teacher_edit_form_submit');
					?>
				</table>
			</form>
		</div>
		<?php
	}


	public static function input_full_name() {
		?>
		<tr>
			<th><?php esc_html_e('Full Name', 'easy-school-registration'); ?></th>
			<td><input type="text" name="name" data-name="name"></td>
		</tr>
		<?php
	}


	public static function input_description() {
		?>
		<tr>
			<th><?php esc_html_e('Description', 'easy-school-registration'); ?></th>
			<td><textarea name="description" data-name="description"></textarea></td>
		</tr>
		<?php
	}


	public static function input_nickname() {
		?>
		<tr>
			<th><?php esc_html_e('Nickname', 'easy-school-registration'); ?></th>
			<td><input type="text" name="nickname" value="" data-name="nickname">
            <?php ESR_Tooltip_Templater_Helper::print_tooltip(esc_html__('Teacher nicknames are displayed in the Registration Form.', 'easy-school-registration')); ?>
            </td>
		</tr>
		<?php
	}


	public static function input_teacher_settings() {
		?>
		<tr>
			<th><?php esc_html_e('Limit Registration View', 'easy-school-registration'); ?></th>
			<td><input type="checkbox" name="teacher_settings[limit_registrations]" value="1" data-name="limit_registrations">
				<?php ESR_Tooltip_Templater_Helper::print_tooltip(esc_html__('If selected, this Teacher will see only students who registered to his classes in the Registration section. The System User in the dropdown list above needs to be assigned.', 'easy-school-registration')); ?>
			</td>
		</tr>
		<?php
	}


	public static function input_user() {
		global $wpdb, $wp_roles;

		$meta_query = ['relation' => 'OR',];
		foreach ($wp_roles->roles as $key => $role) {
			$role_object = get_role( $key );
			if ($role_object->has_cap( 'esr_school' )) {
				array_push($meta_query, [
					'key'     => $wpdb->prefix . 'capabilities',
					'value'   => $key,
					'compare' => 'like'
				]);
			}
		}

		$user_query = new WP_User_Query([
			'meta_query' =>
				$meta_query
			,
			'fields'     => [
				'ID',
				'display_name',
				'user_email'
			]
		]);

		$users = $user_query->get_results();
		?>
		<tr>
			<th><label for="user_id"><?php esc_html_e('System User', 'easy-school-registration'); ?></label></th>
			<td>
				<select name="user_id" data-name="user_id">
					<option value=""><?php esc_html_e('- select -', 'easy-school-registration'); ?></option>
					<?php foreach ($users as $user) { ?>
						<option value="<?php echo esc_attr($user->ID); ?>"><?php echo esc_html($user->display_name . ' / ' . $user->user_email); ?></option>
					<?php } ?>
				</select>
                <?php ESR_Tooltip_Templater_Helper::print_tooltip(esc_html__('If you want to use the Limit Registration View function, the Teacher needs to be a WordPress user.', 'easy-school-registration')); ?>
			</td>
		</tr>
		<?php
	}


	public static function input_submit() {
		?>
		<tr>
			<th></th>
			<td>
				<input type="hidden" name="teacher_id">
				<input type="submit" name="esr_save_teacher" value="<?php esc_attr_e('Save', 'easy-school-registration'); ?>">
			</td>
		</tr>
		<?php
	}
}
