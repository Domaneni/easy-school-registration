<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Student_Payment {

	public static function esr_print_student_payment_page_callback($attr) {
		if (isset($attr['stp'])) {
			$payment = ESR()->payment->esr_get_payment_by_md5($attr['stp']);
			$is_processing = apply_filters('esr_student_payment_process', ['payment' => $payment, 'attr' => $attr, 'is_processing' => false]);

			if ($payment === null) {
                esc_html_e('No payment found', 'easy-school-registration');
			} else {
				$already_paid = max(floatval($payment->payment), 0);
				if (floatval($payment->to_pay) > $already_paid) {
					$full_price = 0;
					?>
					<table>
						<thead>
						<tr>
							<th><?php esc_html_e('Course', 'easy-school-registration') ?></th>
							<th><?php esc_html_e('Price', 'easy-school-registration') ?></th>
						</tr>
						</thead>
						<?php
						$courses = ESR()->registration->get_confirmed_registrations_by_wave_and_user($payment->wave_id, $payment->user_id);
						$to_pay = floatval($payment->to_pay);
						foreach ($courses as $ck => $course) {
							$full_price += floatval($course->price);
							?>
							<tr>
								<td><?php echo esc_html($course->title) ?></td>
								<td><?php echo esc_html($course->price) ?></td>
							</tr>
							<?php
						}
						if ($full_price !== $to_pay) {
							?>
							<tr>
								<td><?php esc_html_e('Discount', 'easy-school-registration') ?></td>
								<td><?php echo esc_html($full_price - $to_pay); ?></td>
							</tr>
							<?php
						}
						if ($already_paid != 0) {
							?>
							<tr>
								<td><?php esc_html_e('Already paid', 'easy-school-registration') ?></td>
								<td><?php echo esc_html($already_paid); ?></td>
							</tr>
							<?php
						}
						?>
						<tr>
							<td><?php esc_html_e('To Pay') ?></td>
							<td><?php echo esc_html($to_pay - $already_paid); ?></td>
						</tr>
					</table>
					<?php
					do_action('esr_student_payment_button', $payment, $attr);
				} else {
					esc_html_e('Already paid', 'easy-school-registration');
				}
			}
		} else {
            esc_html_e('No payment found', 'easy-school-registration');
		}
	}

}

add_action('esr_print_student_payment_page', ['ESR_Template_Student_Payment', 'esr_print_student_payment_page_callback']);