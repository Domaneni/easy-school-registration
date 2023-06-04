<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Students_Table {

	public function print_table() {
		?>

			<table id="datatable" class="wp-list-table widefat fixed striped esr-datatable esr-settings-students esr-newsletter-email-export">
				<thead>
				<tr>
					<th class="esr-filter-disabled no-sort esr-hide-print"><?php esc_html_e('Actions', 'easy-school-registration'); ?></th>
					<th class="esr-filter-disabled no-sort"><?php esc_html_e('Student Name', 'easy-school-registration'); ?></th>
					<th class="esr-filter-disabled no-sort esr-student-email"><?php esc_html_e('Student Email', 'easy-school-registration'); ?></th>
					<th class="esr-filter-disabled no-sort"><?php esc_html_e('Phone Number', 'easy-school-registration'); ?></th>
					<th class="no-sort"><?php esc_html_e('Newsletter', 'easy-school-registration'); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach (get_users() as $key => $user) {
					$newsletter = get_user_meta($user->ID, 'esr-course-registration-newsletter', true);
					$phone = get_user_meta($user->ID, 'esr-course-registration-phone', true);
					?>
					<tr class="esr-row <?php echo ($newsletter ? 'esr-has-newsletter' : ''); ?>">
						<td class="actions esr-student esr-hide-print">
							<div class="esr-relative">
								<button class="page-title-action"><?php esc_html_e('Actions', 'easy-school-registration') ?></button>
								<?php $this->print_action_box($user->ID); ?>
							</div>
						</td>
						<td><?php echo esc_html($user->display_name); ?></td>
						<td><?php echo esc_html($user->user_email); ?></td>
						<td><?php echo esc_html((!empty($phone) ? $phone : '')); ?></td>
						<td><?php echo ($newsletter ? esc_html__('Yes', 'easy-school-registration') : esc_html__('No', 'easy-school-registration')); ?></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		<?php
	}


	private function print_action_box($id) {
		?>
		<ul class="esr-actions-box dropdown-menu" data-id="<?php echo esc_attr($id); ?>">
			<li class="esr-action show">
				<a href="javascript:;">
					<span class="dashicons dashicons-visibility"></span>
					<span><?php esc_html_e('Show', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<?php if (intval(ESR()->settings->esr_get_option('gdpr_email_enabled', -1)) !== -1) { ?>
			<li class="esr-action download">
				<a href="javascript:;">
					<span class="dashicons dashicons-download"></span>
					<span><?php esc_html_e('Send Export', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<?php } ?>
		</ul>
		<?php
	}

}
