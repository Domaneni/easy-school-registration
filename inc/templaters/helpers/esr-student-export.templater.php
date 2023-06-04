<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Student_Export {

	public function export_student_data($user_id) {
		$html = '';

		$html = apply_filters('esr_student_export_user_info', $html, $user_id);
		$html = apply_filters('esr_student_export_table', $html, $user_id);

		return  wp_kses_post($html);
	}

	public static function esr_print_user_info($html, $user_id) {
		$user = get_user_by('ID', $user_id);

		$html .= '<h2>' . esc_html__('User data', 'easy-school-registration') . '</h2>';
		$html .= '
		<table>
			<tr>
				<th>' . esc_html__('Display name', 'easy-school-registration') . '</th>
				<td>' . esc_html($user->data->display_name) . '</td>
			</tr>
			<tr>
				<th>' . esc_html__('User login', 'easy-school-registration') . '</th>
				<td>' . esc_html($user->data->user_login) . '</td>
			</tr>
			<tr>
				<th>' . esc_html__('User Email', 'easy-school-registration') . '</th>
				<td>' . esc_html($user->data->user_email) . '</td>
			</tr>
			<tr>
				<th>' . esc_html__('User Url', 'easy-school-registration') . '</th>
				<td>' . esc_html($user->data->user_url) . '</td>
			</tr>
			<tr>
				<th>' . esc_html__('User Registered', 'easy-school-registration') . '</th>
				<td>' . esc_html($user->data->user_registered) . '</td>
			</tr>
		</table>
		';


		$user_meta = get_user_meta($user_id);
		$html .= '<h2>' . esc_html__('User meta', 'easy-school-registration') . '</h2>';
		$html .= '
		<table>
			<tr>
				<th>' . esc_html__('Nickname', 'easy-school-registration') . '</th>
				<td>' . esc_html($user_meta['nickname'][0]) . '</td>
			</tr>
			<tr>
				<th>' . esc_html__('First name', 'easy-school-registration') . '</th>
				<td>' . esc_html($user_meta['first_name'][0]) . '</td>
			</tr>
			<tr>
				<th>' . esc_html__('Last name', 'easy-school-registration') . '</th>
				<td>' . esc_html($user_meta['last_name'][0]) . '</td>
			</tr>
			<tr>
				<th>' . esc_html__('Description', 'easy-school-registration') . '</th>
				<td>' . esc_html($user_meta['description'][0]) . '</td>
			</tr>
			<tr>
				<th>' . esc_html__('Phone', 'easy-school-registration') . '</th>
				<td>' . (isset($user_meta['esr-course-registration-phone'][0]) ? esc_html($user_meta['esr-course-registration-phone'][0]) : '') . '</td>
			</tr>
			<tr>
				<th>' . esc_html__('Terms & Conditions', 'easy-school-registration') . '</th>
				<td>' . (isset($user_meta['esr-course-registration-terms-conditions'][0]) ? esc_html($user_meta['esr-course-registration-terms-conditions'][0]) : '') . '</td>
			</tr>
		</table>
		';

		return wp_kses_post($html);
	}


	public static function esr_print_registrations($html, $user_id) {
		$registrations = ESR()->registration->get_registrations_by_user($user_id);


		if ($registrations) {
			$html .= '<h2>' . esc_html__('Registrations', 'easy-school-registration') . '</h2>';
			$html .= '<table>
						<tr>
						<th>' . esc_html__('Registration time', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Status', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Course', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Wave', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Partner', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Dancing As', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Dancing With', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Note', 'easy-school-registration') . '</th>
						</tr>';
			foreach ($registrations as $id => $registration) {
				$user = get_user_by('ID', $registration->partner_id);
				$html .= '
						<tr>
						<td>' . esc_html($registration->time) . '</td>
						<td>' . esc_html(ESR()->registration_status->get_title($registration->status)) . '</td>
						<td>' . esc_html($registration->course_name) . '</td>
						<td>' . esc_html($registration->wave_name) . '</td>
						<td>' . ($user ? esc_html($user->data->display_name) : '') . '</td>
						<td>' . esc_html(ESR()->dance_as->get_title($registration->dancing_as)) . '</td>
						<td>' . esc_html($registration->dancing_with) . '</td>
						<td>' . esc_html($registration->note) . '</td>
						</tr>';
			}
			$html .= '</table>';
		} else {
			$html = '<div>' . esc_html__('No registrations', 'easy-school-registration') . '</div>';
		}

		return wp_kses_post($html);
	}


	public static function esr_print_payments($html, $user_id) {
		$payments = ESR()->payment->get_payments_by_user($user_id);
		$html     .= '';

		if ($payments) {
			$html .= '<h2>' . esc_html__('Payments', 'easy-school-registration') . '</h2>';
			$html .= '<table>
						<tr>
						<th>' . esc_html__('Status', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Wave', 'easy-school-registration') . '</th>
						<th>' . esc_html__('To Pay', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Payment', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Is Paying', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Is Voucher', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Insert Timestamp', 'easy-school-registration') . '</th>
						<th>' . esc_html__('Confirm Timestamp', 'easy-school-registration') . '</th>
						</tr>';
			foreach ($payments as $id => $payment) {
				$wave = ESR()->wave->get_wave_data($payment->wave_id);
				$html .= '
						<tr>
						<td>' . esc_html(ESR()->payment_status->get_title($payment->status)) . '</td>
						<td>' . ($wave ? esc_html($wave->title) : '') . '</td>
						<td>' . esc_html($payment->to_pay) . '</td>
						<td>' . esc_html($payment->payment) . '</td>
						<td>' . esc_html($payment->is_paying) . '</td>
						<td>' . esc_html($payment->is_voucher) . '</td>
						<td>' . esc_html($payment->insert_timestamp) . '</td>
						<td>' . esc_html($payment->confirm_timestamp) . '</td>
						</tr>';
			}
			$html .= '</table>';
		} else {
			$html .= '<div>' . esc_html__('No payments', 'easy-school-registration') . '</div>';
		}

		return wp_kses_post($html);
	}

}

add_filter('esr_student_export_user_info', ['ESR_Template_Student_Export', 'esr_print_user_info'], 10, 2);
add_filter('esr_student_export_table', ['ESR_Template_Student_Export', 'esr_print_registrations'], 10, 2);
add_filter('esr_student_export_table', ['ESR_Template_Student_Export', 'esr_print_payments'], 11, 2);