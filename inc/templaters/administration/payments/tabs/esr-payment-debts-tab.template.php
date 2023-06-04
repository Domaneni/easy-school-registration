<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Tab_Template_Payment_Debts {

	public static function esr_print_debts($selected_wave) {
		$user_can_edit         = current_user_can('esr_payment_edit');
		$template_payment_form = new ESR_Payment_Form_Subblock_Templater();

		$payments = ESR()->payment->esr_get_payment_debts();

		?>
		<h1 class="wp-heading-inline"><?php esc_html_e('Debts', 'easy-school-registration'); ?></h1>
		<?php
		if ($user_can_edit) {
			$template_payment_form->print_form();
		}
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
				<?php if ($user_can_edit) { ?>
					<th class="esr-filter-disabled skip-filter no-sort esr-hide-print"><?php esc_html_e('Actions', 'easy-school-registration') ?></th>
				<?php } ?>
				<th><?php esc_html_e('Student Name', 'easy-school-registration') ?></th>
				<th class="esr-student-email"><?php esc_html_e('Student Email', 'easy-school-registration') ?></th>
				<th class="no-sort"><?php esc_html_e('Wave', 'easy-school-registration') ?></th>
				<th class="no-sort"><?php esc_html_e('Payment Type', 'easy-school-registration') ?></th>
				<th class="esr-filter-disabled skip-filter"><?php esc_html_e('Note', 'easy-school-registration') ?></th>
				<th class="skip-filter"><?php esc_html_e('To pay', 'easy-school-registration') ?></th>
				<th class="skip-filter"><?php esc_html_e('Paid', 'easy-school-registration') ?></th>
			</tr>
			</thead>
			<tbody class="list">
			<?php

			$users = [];

			foreach ($payments as $key => $user_payment) {
				if (isset($users[$user_payment->user_id])) {
					$user_data             = $users[$user_payment->user_id]['user_data'];
					$user_email            = $users[$user_payment->user_id]['user_email'];
					$user_name             = $users[$user_payment->user_id]['user_name'];
					$paid_status           = $users[$user_payment->user_id]['paid_status'];
					$disable_registrations = $users[$user_payment->user_id]['disable_registrations'];
				} else {
					$user_data             = get_userdata($user_payment->user_id);
					$user_email            = $user_data ? $user_data->user_email : esc_html__('deleted student', 'easy-school-registration');
					$user_name             = $user_data ? $user_data->display_name : esc_html__('deleted student', 'easy-school-registration');
					$paid_status           = ESR()->payment_status->get_status($user_payment);
					$disable_registrations = get_user_meta($user_payment->user_id, 'esr_user_registration_disabled');

					$users[$user_payment->user_id]['user_data']             = $user_data;
					$users[$user_payment->user_id]['user_email']            = $user_email;
					$users[$user_payment->user_id]['user_name']             = $user_name;
					$users[$user_payment->user_id]['paid_status']           = $paid_status;
					$users[$user_payment->user_id]['disable_registrations'] = $disable_registrations;
				}

				$classes = ['esr-row', 'paid-status-' . $paid_status];

				if (!empty($disable_registrations)) {
					$classes[] = 'esr-disable-registrations';
				}

			?>
			<tr class="<?php echo esc_attr(implode(' ', $classes)); ?>"
				<?php if ($user_can_edit) { ?>
					data-id="<?php echo esc_attr($user_payment->id); ?>"
					data-email="<?php echo esc_attr($user_email); ?>"
					data-user_id="<?php echo esc_attr($user_payment->user_id); ?>"
					data-to_pay="<?php echo esc_attr($user_payment->to_pay); ?>"
					data-payment="<?php echo esc_attr($user_payment->payment); ?>"
					data-wave_id="<?php echo esc_attr($user_payment->wave_id); ?>"
					data-note="<?php echo esc_attr($user_payment->note); ?>"
				<?php } ?>
			>
				<td class="actions esr-payment">
					<?php if ($user_can_edit && $user_data) { ?>
						<div class="esr-relative">
							<button class="page-title-action"><?php esc_html_e('Actions', 'easy-school-registration') ?></button>
							<?php self::print_action_box($user_payment); ?>
						</div>
					<?php } ?>
				</td>
				<td class="student-surname"><?php echo esc_html($user_name); ?></td>
				<td class="student-email"><?php echo esc_html($user_email); ?></td>
				<td><?php echo esc_html($user_payment->title); ?></td>
				<td class="payment-type"><?php echo esc_html(ESR()->payment_type->get_title($user_payment->payment_type)); ?></td>
				<td class="esr-note"><?php if (($user_payment->note !== null) && ($user_payment->note !== "")) { ?>
						<span class="dashicons dashicons-admin-comments esr-show-note" title="<?php echo esc_html(htmlspecialchars($user_payment->note, ENT_QUOTES, 'UTF-8')); ?>"></span>
						<span class="dashicons dashicons-welcome-comments esr-hide-note"></span>
						<span class="esr-note-message"><?php echo esc_html($user_payment->note); ?></span><?php } ?></td>
				<td><?php echo esc_html(ESR()->currency->prepare_price($user_payment->to_pay)); ?></td>
				<td class="student-paid"><?php echo (($user_payment && isset($user_payment->payment) && (!in_array($paid_status, [ESR_Enum_Payment::NOT_PAYING, ESR_Enum_Payment::VOUCHER]))) ? esc_html(ESR()->currency->prepare_price($user_payment->payment)) : ''); ?></td>
				<?php } ?>
			</tbody>
		</table>
		<?php
	}


	private static function print_action_box($user_payment) {
		?>
		<ul class="esr-actions-box dropdown-menu" data-id="<?php echo esc_attr($user_payment->id); ?>">
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
			<?php if (intval(ESR()->settings->esr_get_option('debts_enabled', -1)) === 1) { ?>
				<li class="esr-action forgive-payment">
					<a href="javascript:;">
						<span class="dashicons dashicons-thumbs-up"></span>
						<span><?php esc_html_e('Forgive Payment', 'easy-school-registration'); ?></span>
					</a>
				</li>
			<?php } ?>
			<li class="esr-action disable-registration">
				<a href="javascript:;">
					<span class="dashicons dashicons-lock"></span>
					<span><?php esc_html_e('Disable Registrations', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<li class="esr-action enable-registration">
				<a href="javascript:;">
					<span class="dashicons dashicons-unlock"></span>
					<span><?php esc_html_e('Enable Registrations', 'easy-school-registration'); ?></span>
				</a>
			</li>
			<?php do_action('esr_debts_table_action_box_item', $user_payment) ?>
		</ul>
		<?php
	}

}

add_action('esr_print_payment_debts_tab', ['ESR_Tab_Template_Payment_Debts', 'esr_print_debts']);