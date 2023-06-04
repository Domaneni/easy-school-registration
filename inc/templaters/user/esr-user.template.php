<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_User_Template {

	public static function esr_add_user_fields_callback($user) {
		$user_phone      = get_user_meta($user->ID, 'esr-course-registration-phone', true);
		$user_newsletter = get_user_meta($user->ID, 'esr-course-registration-newsletter', true);

		?>
		<h3><?php esc_html_e('Easy School Registration', 'easy-school-registration'); ?></h3>
		<table class="form-table">
			<tr class="user-job-title-wrap">
				<th><label for="esr_phone"><?php esc_html_e('Phone Number', 'easy-school-registration'); ?></label></th>
				<td><input type="text" name="esr_phone" id="esr_phone" value="<?php echo esc_attr($user_phone) ?>" class="regular-text ltr"/></td>
			</tr>
			<tr class="user-description-wrap">
				<th><label for="esr_newsletter"><?php esc_html_e('Newsletter', 'easy-school-registration'); ?></label></th>
				<td><input type="checkbox" name="esr_newsletter" id="esr_newsletter" value="<?php echo (!empty($user_newsletter) ? esc_attr($user_newsletter) : current_time('Y-m-d H:i:s')); ?>" id="esr_newsletter" <?php checked(1, !empty($user_newsletter)) ?>></td>
			</tr>
		</table>
		<?php

	}
}

add_action('show_user_profile', ['ESR_User_Template', 'esr_add_user_fields_callback']);
add_action('edit_user_profile', ['ESR_User_Template', 'esr_add_user_fields_callback']);