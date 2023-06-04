<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Payment_Table_Subblock_Templater {

	public function print_table($selected_wave) {
		$user_can_edit = current_user_can('esr_payment_edit');
		$show_phone    = intval(ESR()->settings->esr_get_option('show_phone_in_payments', -1)) === 1;

		$payments = ESR()->payment->get_payments_by_wave($selected_wave);

		?>
		<table id="datatable" class="esr-datatable table table-default table-bordered esr-payments-table esr-email-export esr-copy-table esr-excel-export">
			<colgroup>
				<col width="10">
				<?php if ($user_can_edit) { ?>
					<col width="100">
				<?php } ?>
			</colgroup>
			<thead>
			<tr>
				<th class="no-sort"><?php esc_html_e('Status', 'easy-school-registration') ?></th>
				<?php if ($user_can_edit) { ?>
					<th class="esr-filter-disabled skip-filter no-sort esr-hide-print"><?php esc_html_e('Actions', 'easy-school-registration') ?></th>
				<?php } ?>
				<th class="no-sort"><?php esc_html_e('Payment Method', 'easy-school-registration') ?></th>
				<th class="esr-filter-disabled skip-filter"><?php esc_html_e('Note', 'easy-school-registration') ?></th>
				<th class="esr-filter-disabled skip-filter"><?php esc_html_e('Student Name', 'easy-school-registration') ?></th>
				<th class="esr-filter-disabled skip-filter esr-student-email"><?php esc_html_e('Student Email', 'easy-school-registration') ?></th>
				<?php if ($show_phone) { ?>
					<th class="esr-filter-disabled esr-student-phone"><?php esc_html_e('Phone', 'easy-school-registration'); ?></th>
				<?php } ?>
				<th class="esr-filter-disabled skip-filter"><?php esc_html_e('Variable Symbol', 'easy-school-registration') ?></th>
				<!--<th class="esr-filter-disabled skip-filter"><?php /*_e('Registration VS', 'easy-school-registration') */?></th>-->
				<th class="skip-filter"><?php esc_html_e('To pay', 'easy-school-registration') ?></th>
				<th class="skip-filter"><?php esc_html_e('Paid', 'easy-school-registration') ?></th>
			</tr>
			</thead>
			<tbody class="list">
			<?php
			foreach ($payments as $k => $user_payment) {
			$user_data   = get_userdata($user_payment['user_id']);
			$user_email  = $user_data ? $user_data->user_email : esc_html__('deleted student', 'easy-school-registration');
			$user_name   = $user_data ? $user_data->display_name : esc_html__('deleted student', 'easy-school-registration');
			$user_phone = get_user_meta($user_data->ID, 'esr-course-registration-phone');
			$paid_status = ESR()->payment_status->get_status($user_payment);

			?>
			<tr class="esr-row esr-payment-row <?php echo 'paid-status-' . esc_attr($paid_status); ?>"
				<?php if ($user_can_edit) { ?>
					data-id="<?php echo esc_attr($user_payment['id']); ?>"
					data-email="<?php echo esc_attr($user_email); ?>"
					data-to_pay="<?php echo esc_attr($user_payment['to_pay']); ?>"
					data-payment="<?php echo esc_attr($user_payment['payment']); ?>"
					data-wave_id="<?php echo esc_attr($user_payment['wave_id']) ?>"
					data-note="<?php echo esc_attr($user_payment['note']) ?>"
				<?php } ?>
			>
				<td class="status"><?php echo esc_html(ESR()->payment_status->get_title($paid_status)); ?></td>
				<td class="actions esr-payment">
					<?php if ($user_can_edit && $user_data) { ?>
						<div class="esr-relative">
							<button class="page-title-action"><?php esc_html_e('Actions', 'easy-school-registration') ?></button>
							<?php $this->print_action_box($user_payment); ?>
						</div>
					<?php } ?>
				</td>
				<td class="payment-type"><?php echo esc_html(ESR()->payment_type->get_title($user_payment['payment_type'])); ?></td>
				<td class="esr-note"><?php if (($user_payment['note'] !== null) && ($user_payment['note'] !== "")) { ?>
						<span class="dashicons dashicons-admin-comments esr-show-note" title="<?php echo esc_html(htmlspecialchars($user_payment['note'], ENT_QUOTES, 'UTF-8')); ?>"></span>
						<span class="dashicons dashicons-welcome-comments esr-hide-note"></span>
						<span class="esr-note-message"><?php echo esc_html($user_payment['note']); ?></span><?php } ?></td>
				<td class="student-surname"><?php echo esc_html($user_name); ?></td>
				<td class="student-email"><?php echo esc_html($user_email); ?></td>
				<?php if ($show_phone) { ?>
					<td class="student-phone"><?php echo (!empty($user_phone) ? esc_html($user_phone[0]) : ''); ?></td>
				<?php } ?>
				<td class="variable-symbol"><?php echo esc_html($selected_wave . sprintf("%04s", $user_payment['user_id'])); ?></td>
				<!--<td></td>-->
				<td><?php echo esc_html(ESR()->currency->prepare_price($user_payment['to_pay'])); ?></td>
				<td class="student-paid"><?php echo (($user_payment && isset($user_payment['payment']) && (!in_array($paid_status, [ESR_Enum_Payment::NOT_PAYING, ESR_Enum_Payment::VOUCHER]))) ? esc_html(ESR()->currency->prepare_price($user_payment['payment'])) : ''); ?></td>
				<?php } ?>
			</tbody>
		</table>
		<?php
	}


	private function print_action_box($user_payment) {
		?>
		<ul class="esr-actions-box dropdown-menu" data-id="<?php echo esc_attr($user_payment['id']); ?>">
			<li class="esr-action edit">
				<a href="javascript:;">
					<span class="dashicons dashicons-edit"></span>
					<span><?php esc_html_e('Edit', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<li class="esr-action confirm-payment">
				<a href="javascript:;">
					<span class="dashicons dashicons-yes"></span>
					<span><?php esc_html_e('Confirm Payment', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<?php do_action('esr_payment_table_action_box_item', $user_payment) ?>
		</ul>
		<?php
	}

}
