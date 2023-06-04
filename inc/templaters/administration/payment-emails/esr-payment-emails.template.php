<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Payment_Emails {

	const MENU_SLUG = 'esr_admin_sub_page_payment_emails';


	public static function print_page() {
		$worker_payment_emails = new ESR_Worker_Payment_Emails();

		$selected_wave = apply_filters('esr_all_waves_select_get', []);

		if (isset($_POST['esr_wave'])) {
			$worker_payment_emails->process_sending(intval($_POST['esr_wave']), $_POST);
		}

		?>
		<div class="wrap esr-settings esr-payment-emails">
		<?php do_action('esr_all_waves_select_print', $selected_wave); ?>
		<div class="esr-not-paid">
			<form action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="post">
					<h2><?php esc_html_e('Not Paid', 'easy-school-registration'); ?></h2>
					<table class="wp-list-table widefat fixed striped esr-datatable" data-iDisplayLength="25">
						<thead>
						<tr>
							<th class="esr-filter-disabled no-sort"><input type="checkbox" name="esr-select-all"/><label><?php esc_html_e('All', 'easy-school-registration'); ?></label></th>
							<th><?php esc_html_e('Status', 'easy-school-registration'); ?></th>
							<th class="esr-filter-disabled"><?php esc_html_e('Student Name', 'easy-school-registration'); ?></th>
							<th class="esr-filter-disabled"><?php esc_html_e('Email', 'easy-school-registration'); ?></th>
							<th class="esr-filter-disabled"><?php esc_html_e('Variable Symbol', 'easy-school-registration'); ?></th>
							<th class="esr-filter-disabled"><?php esc_html_e('Last Reminder Email Sent', 'easy-school-registration'); ?></th>
							<th class="esr-filter-disabled"><?php esc_html_e('Last Confirmation Change', 'easy-school-registration'); ?></th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach (ESR()->payment->get_not_paid_users_for_payment_emails($selected_wave) as $user) {
							$email_sent = new DateTime($user->confirmation_email_sent_timestamp);
							$last_time = new DateTime($user->last_time);

							if ($user->confirmation_email_sent_timestamp === null) {
								$class = 'not-send';
							} else if ($email_sent >= $last_time) {
								$class = 'no-change';
							} else {
								$class = 'has-change';
							}

							?>
							<tr class="<?php echo esc_attr($class); ?>">
								<td><input type="checkbox" name="esr_choosed_users[]" value="<?php echo esc_attr($user->user_id); ?>"></td>
								<td><?php echo esc_html(ESR()->payment_emails->esr_get_status($user->confirmation_email_sent_timestamp, $user->last_time)); ?></td>
								<td><?php echo esc_html($user->display_name); ?></td>
								<td><?php echo esc_html($user->user_email); ?></td>
								<td><?php echo esc_html($selected_wave . sprintf("%04s", $user->user_id)); ?></td>
								<td><?php echo esc_html($user->confirmation_email_sent_timestamp); ?></td>
								<td><?php echo esc_html($user->last_time); ?></td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>
				<input type="hidden" name="esr_wave" value="<?php echo esc_attr($selected_wave); ?>">
				<input type="submit" name="esr_send_payment_email_submit" value="<?php esc_attr_e('Send emails', 'easy-school-registration'); ?>">
			</form>
		</div>
		<div class="esr-partially-paid">
			<h2><?php esc_html_e('Partially Paid', 'easy-school-registration'); ?></h2>
			<form action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="post">

					<table class="wp-list-table widefat fixed striped esr-datatable" data-iDisplayLength="25">
						<thead>
						<tr>
							<th class="esr-filter-disabled no-sort"><input type="checkbox" name="esr-select-all"/><label><?php esc_html_e('All', 'easy-school-registration'); ?></label></th>
							<th class="esr-filter-disabled"><?php esc_html_e('Student Name', 'easy-school-registration'); ?></th>
							<th class="esr-filter-disabled"><?php esc_html_e('Email', 'easy-school-registration'); ?></th>
							<th class="esr-filter-disabled"><?php esc_html_e('Variable Symbol', 'easy-school-registration'); ?></th>
							<th class="esr-filter-disabled"><?php esc_html_e('Paid So Far', 'easy-school-registration'); ?></th>
							<th class="esr-filter-disabled"><?php esc_html_e('Full Price', 'easy-school-registration'); ?></th>
							<th class="esr-filter-disabled no-sort"><?php esc_html_e('Note', 'easy-school-registration'); ?></th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach (ESR()->payment->get_partially_paid_users_for_payment_emails($selected_wave) as $user) {
							?>
							<tr>
								<td><input type="checkbox" name="esr_choosed_users[]" value="<?php echo esc_attr($user->user_id); ?>"></td>
								<td><?php echo esc_html($user->display_name); ?></td>
								<td><?php echo esc_html($user->user_email); ?></td>
								<td><?php echo esc_html($selected_wave . sprintf("%04s", $user->user_id)); ?></td>
								<td><?php echo esc_html($user->payment); ?></td>
								<td><?php echo esc_html($user->to_pay); ?></td>
								<td><?php if (($user->note !== null) && ($user->note !== "")) { ?><span class="dashicons dashicons-admin-comments" title="<?php echo esc_html(htmlspecialchars($user->note, ENT_QUOTES, 'UTF-8')); ?>"></span><?php } ?></td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>
				<input type="hidden" name="esr_wave" value="<?php echo esc_attr($selected_wave); ?>">
				<input type="submit" name="esr_send_payment_email_submit" value="<?php esc_attr_e('Send emails', 'easy-school-registration'); ?>">
			</form>
		</div>
		</div><?php
	}
}
