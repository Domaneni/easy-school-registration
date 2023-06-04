<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_User_Info_Payments_Templater {

	const MENU_SLUG = 'esr_user_info_payments';


	public static function print_page() {
		$user_payments = ESR()->payment->get_payments_by_user(get_current_user_id(), ARRAY_A);
		?>
		<div class="wrap">
			<?php if ($user_payments) { ?>
				<div class="esr_user_settings payments">
					<h2><?php esc_html_e('Payments', 'easy-school-registration'); ?></h2>
					<table id="datatable" class="table table-default table-bordered esr-datatable">
						<thead>
						<tr>
							<th><?php esc_html_e('Wave', 'easy-school-registration'); ?></th>
							<th><?php esc_html_e('Status', 'easy-school-registration'); ?></th>
							<th><?php esc_html_e('To pay', 'easy-school-registration'); ?></th>
							<?php if (intval(ESR()->settings->esr_get_option('show_already_paid', -1)) != -1) { ?>
								<th><?php esc_html_e('Paid', 'easy-school-registration'); ?></th>
							<?php } ?>
						</tr>
						</thead>
						<tbody class="list">
						<?php
						foreach ($user_payments as $id => $payment) {
							$wave_data   = ESR()->wave->get_wave_data($payment['wave_id']);
							$paid_status = ESR()->payment_status->get_status($payment);
							?>
							<tr>
								<td><?php echo esc_html($wave_data->title) ?></td>
								<td><?php echo esc_html(ESR()->payment_status->get_title($paid_status)); ?></td>
								<td><?php echo esc_html(ESR()->currency->prepare_price($payment['to_pay'])); ?></td>
								<?php if (intval(ESR()->settings->esr_get_option('show_already_paid', -1)) != -1) { ?>
									<td><?php echo esc_html(ESR()->currency->prepare_price($payment['payment'] ? $payment['payment'] : 0)); ?></td>
								<?php } ?>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>
				</div>
			<?php } ?>
		</div>
		<?php
	}
}
