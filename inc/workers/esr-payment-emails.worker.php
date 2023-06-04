<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Worker_Payment_Emails {

	public function process_sending($wave_id, $data) {
		global $wpdb;

		if (isset($data['esr_send_payment_email_submit'])) {
			$results        = ESR()->payment->load_payments_for_emails_by_wave($wave_id);
			$selected_users = array_flip($data['esr_choosed_users']);

			foreach ($results as $key => $result) {
				if (isset($selected_users[$result->user_id])) {
					$user_courses = explode(',', sanitize_text_field($result->courses));

					$status = ESR()->email->send_payment_email($wave_id, $user_courses, $result);

					if ($status) {
						$wpdb->update($wpdb->prefix . 'esr_user_payment', [
							'confirmation_email_sent_timestamp' => current_time('Y-m-d H:i:s')
						], [
							'user_id' => $result->user_id,
							'wave_id' => $wave_id
						]);
					}
				}
			}
		}
	}

}
