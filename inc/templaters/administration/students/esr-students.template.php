<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Students {

	const MENU_SLUG = 'esr_admin_sub_page_students';

	public static function print_page() {
		$template_table = new ESR_Template_Students_Table();

		?>
		<div class="wrap esr-settings esr-students">
			<h1 class="wp-heading-inline"><?php esc_html_e('Students Overview', 'easy-school-registration'); ?></h1>
			<div class="esr-student-data">
				<table>
					<tr>
						<th><?php esc_html_e('Name', 'easy-school-registration'); ?></th>
						<td class="esr-user-name"></td>
					</tr>
					<tr>
						<th><?php esc_html_e('Email', 'easy-school-registration'); ?></th>
						<td class="esr-user-email"></td>
					</tr>
					<tr>
						<th><?php esc_html_e('Phone', 'easy-school-registration'); ?></th>
						<td class="esr-user-phone"></td>
					</tr>
					<tr>
						<th><?php esc_html_e('Note', 'easy-school-registration'); ?></th>
						<td class="esr-user-note">
							<textarea name="esr_user_note"></textarea>
							<i class="esr_save_spinner"></i>
							<span class="esr_save_confirmed dashicons dashicons-yes"></span>
							<button name="esr_save_student_note"><?php esc_html_e('Save Note', 'easy-school-registration'); ?></button>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e('Registered Courses', 'easy-school-registration'); ?></th>
						<td class="esr-user-registrations">
							<table class="wp-list-table widefat striped table table-default table-bordered">
								<thead>
								<tr>
									<th><?php esc_html_e('Status', 'easy-school-registration'); ?></th>
									<th><?php esc_html_e('Wave', 'easy-school-registration'); ?></th>
									<th><?php esc_html_e('Course', 'easy-school-registration'); ?></th>
								</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e('Payments', 'easy-school-registration'); ?></th>
						<td class="esr-user-payments">
							<table class="wp-list-table widefat striped table table-default table-bordered">
								<thead>
								<tr>
									<th><?php esc_html_e('Wave', 'easy-school-registration'); ?></th>
									<th><?php esc_html_e('Status', 'easy-school-registration'); ?></th>
									<th><?php esc_html_e('To Pay', 'easy-school-registration'); ?></th>
									<th><?php esc_html_e('Paid', 'easy-school-registration'); ?></th>
								</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<?php $template_table->print_table(); ?>
		</div>
		<?php
	}
}
