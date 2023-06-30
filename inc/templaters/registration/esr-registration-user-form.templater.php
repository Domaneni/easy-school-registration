<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Registration_User_Form_Templater {

	public function print_courses_registration_form( $default_data, $wave_ids, $show_groups = false ) {
		$show_phone_input         = intval( ESR()->settings->esr_get_option( 'show_phone_input', 1 ) ) !== - 1;
		$show_confirm_email_input = intval( ESR()->settings->esr_get_option( 'reconfirm_email_required', - 1 ) ) === 1;
		$is_one_column            = $show_phone_input || $show_confirm_email_input;
		?>
		<table class="esr-user-form">
			<tbody>
			<tr>
				<th class="required"><?php esc_html_e( 'First Name', 'easy-school-registration' ); ?></th>
				<th class="required"><?php esc_html_e( 'Surname', 'easy-school-registration' ); ?></th>
			</tr>
			<tr>
				<td>
					<input required type="text" name="name"
						value="<?php echo esc_attr($default_data['name']); ?>">
				</td>
				<td>
					<input required type="text" name="surname"
						value="<?php echo esc_attr($default_data['surname']); ?>">
				</td>
			</tr>
			</tbody>
			<tbody>
			<tr>
				<th class="required" <?php if ( ! $is_one_column ) {
					echo 'colspan="2"';
				} ?>><?php esc_html_e( 'Email', 'easy-school-registration' ); ?></th>
				<?php
				if ( ! $show_confirm_email_input ) {
					$this->esr_print_phone_header( $show_phone_input );
				} else {
					$this->esr_print_reconfirm_email_header();
				}
				?>
			</tr>
			<tr>
				<td <?php if ( ! $is_one_column ) {
					echo 'colspan="2"';
				} ?>>
					<input required type="text" name="email"
						value="<?php echo esc_attr($default_data['email']); ?>">
				</td>
				<?php
				if ( ! $show_confirm_email_input ) {
					$this->esr_print_phone_input( $show_phone_input, $default_data );
				} else {
					$this->esr_print_confirm_email_input();
				}
				?>
			</tr>
			<?php
			if ( $show_confirm_email_input && $show_phone_input ) {
				?>
				<tr>
					<?php $this->esr_print_phone_header( $show_phone_input ); ?>
					<th></th>
				</tr>
				<tr>
					<?php $this->esr_print_phone_input( $show_phone_input, $default_data ); ?>
					<td></td>
				</tr>
			<?php } ?>
			</tbody>
			<?php
			if ( intval( ESR()->settings->esr_get_option( 'newsletter_enabled', - 1 ) ) !== - 1 ) {
				?>
				<tbody>
				<tr>
					<td colspan="2"><label for="newsletter">
							<input name="newsletter" type="checkbox"
								value="1"
								<?php
								if ( ( $default_data['newsletter'] == 1 ) || ( $default_data['newsletter'] === "" && ( intval( ESR()->settings->esr_get_option( 'newsletter_default', 1 ) ) !== - 1 ) ) ) {
									echo 'checked';
								}
								?>>
							<?php echo wp_kses_post(nl2br( stripcslashes( ESR()->settings->esr_get_option( 'newsletter_text', '' )))); ?></label>
					</td>
				</tr>
				</tbody>
				<?php
			}
			if ( intval( ESR()->settings->esr_get_option( 'terms_conditions_enabled', - 1 ) ) != - 1 ) {
				?>
				<tbody>
				<tr>
					<td colspan="2"><label for="terms-conditions">
							<input name="terms-conditions" type="checkbox" value="1"
								<?php echo( intval( ESR()->settings->esr_get_option( 'terms_conditions_required', - 1 ) ) != - 1 ? ' required' : '' ); ?>>
							<?php echo wp_kses_post(nl2br( stripcslashes( ESR()->settings->esr_get_option( 'terms_conditions_text', '' ) ) )); ?></label>
					</td>
				</tr>
				</tbody>
				<?php
			}
			?>
			<?php do_action( 'esr_front_page_registration_form_input', $wave_ids ); ?>
			<?php if ( intval( ESR()->settings->esr_get_option( 'note_disabled', - 1 ) ) !== 1 ) { ?>
				<tbody>
				<tr>
					<th colspan="2"><?php echo esc_html(ESR()->settings->esr_get_option( 'registration_note_title', esc_html__( 'Note', 'easy-school-registration' ) )); ?></th>
				</tr>
				<tr>
					<?php $note_length = ESR()->settings->esr_get_option( 'note_limit', 0 ); ?>
					<td colspan="2" <?php if ( $note_length > 0 ) { ?>class="esr-textarea-limit"<?php } ?>><textarea name="note" <?php if ( $note_length > 0 ) { ?>maxlength="<?php echo esc_attr($note_length); ?>"<?php } ?>></textarea></td>
				</tr>
				</tbody>
			<?php } ?>
		</table>
	<?php
	}



	/**
	 * @codeCoverageIgnore
	 */
	private function esr_print_phone_header( $show_phone_input ) {
		if ( $show_phone_input ) { ?>
			<th class="<?php if ( intval( ESR()->settings->esr_get_option( 'phone_required', 1 ) ) !== - 1 ) {
				echo 'required';
			} ?>"><?php esc_html_e( 'Phone', 'easy-school-registration' ); ?></th>
		<?php }
	}


	/**
	 * @codeCoverageIgnore
	 */
	private function esr_print_phone_input( $show_phone_input, $default_data ) {
		if ( $show_phone_input ) { ?>
			<td>
				<input type="text" name="phone"
				       value="<?php echo( $default_data['phone'] ? esc_attr($default_data['phone']) : '' ); ?>"
					<?php if ( intval( ESR()->settings->esr_get_option( 'phone_required', 1 ) ) !== - 1 ) {
						echo 'required';
					} ?>>
			</td>
		<?php }
	}


	/**
	 * @codeCoverageIgnore
	 */
	private function esr_print_reconfirm_email_header() {
		?>
		<th class="required"><?php esc_html_e( 'Confirm Email', 'easy-school-registration' ); ?></th>
		<?php
	}


	/**
	 * @codeCoverageIgnore
	 */
	private function esr_print_confirm_email_input() {
		?>
		<td>
			<input class="esr-confirm-email" required type="text" name="esr-confirm-email"
			       data-error-message="<?php esc_attr_e( 'Emails are not same', 'easy-school-registration' ); ?>">
		</td>
		<?php
	}


	/**
	 * @codeCoverageIgnore
	 */
	private function get_user_default_value( $user, $key ) {
		return ( $user->ID != 0 ) ? $user->$key : '';
	}


	/**
	 * @codeCoverageIgnore
	 */
	private function get_user_default_meta_value( $user, $key ) {
		return ( ( $user->ID != 0 ) && get_user_meta( $user->ID, $key ) ? get_user_meta( $user->ID, $key )[0] : '' );
	}

	public static function esr_user_form_start_callback() {
		echo '<div class="esr-user-form">';
	}

	public static function esr_user_form_end_callback() {
		echo '</div>';
	}

	public static function esr_user_form_name_callback($default_data) {
		?>
			<div class="esr-user-form-element">
				<label class="required"><?php esc_html_e( 'First Name', 'easy-school-registration' ); ?></label>
				<div class="esr-form-input">
				<input required type="text" name="name"
						value="<?php echo esc_attr($default_data['name']); ?>">
				</div>
			</div>
			<div class="esr-user-form-element">
				<label class="required"><?php esc_html_e( 'Surname', 'easy-school-registration' ); ?></label>
				<div class="esr-form-input">
				<input required type="text" name="surname"
						value="<?php echo esc_attr($default_data['surname']); ?>">
				</div>
			</div>
		<?php
	}

	public static function esr_user_form_email_callback($default_data) {
		$show_phone_input         = intval( ESR()->settings->esr_get_option( 'show_phone_input', 1 ) ) !== - 1;
		$show_confirm_email_input = intval( ESR()->settings->esr_get_option( 'reconfirm_email_required', - 1 ) ) === 1;
		$classes = ['esr-user-form-element'];

		if (!($show_phone_input || $show_confirm_email_input)) {
			$classes[] = 'stretch';
		}

		?>
			<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
				<label class="required"><?php esc_html_e( 'Email', 'easy-school-registration' ); ?></label>
				<div class="esr-form-input">
					<input required type="text" name="email" value="<?php echo esc_attr($default_data['email']); ?>">
				</div>
			</div>
			<?php if ($show_confirm_email_input) { ?>
				<div class="esr-user-form-element">
					<label class="required"><?php esc_html_e( 'Confirm Email', 'easy-school-registration' ); ?></label>
					<div class="esr-form-input">
						<input class="esr-confirm-email" required type="text" name="esr-confirm-email" data-error-message="<?php esc_attr_e( 'Emails are not same', 'easy-school-registration' ); ?>">
					</div>
				</div>
			<?php } ?>
		<?php
	}

	public static function esr_user_form_phone_callback($default_data) {
		$show_phone_input         = intval( ESR()->settings->esr_get_option( 'show_phone_input', 1 ) ) !== - 1;

		if ($show_phone_input) {
			$show_confirm_email_input = intval( ESR()->settings->esr_get_option( 'reconfirm_email_required', - 1 ) ) === 1;
			$classes = ['esr-user-form-element'];

			if ($show_confirm_email_input) {
				$classes[] = 'stretch';
			}

			?>
				<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
					<label class="required"><?php esc_html_e( 'Phone', 'easy-school-registration' ); ?></label>
					<div class="esr-form-input">
						<input type="text" name="phone"
						value="<?php echo( $default_data['phone'] ? esc_attr($default_data['phone']) : '' ); ?>"
						<?php if ( intval( ESR()->settings->esr_get_option( 'phone_required', 1 ) ) !== - 1 ) {
							echo 'required';
						} ?>>
					</div>
				</div>
			<?php
		}
	}

}

add_action('esr-registration-user-form-start', ['ESR_Registration_User_Form_Templater', 'esr_user_form_start_callback'], 10, 0);
add_action('esr-registration-user-form-end', ['ESR_Registration_User_Form_Templater', 'esr_user_form_end_callback'], 10, 0);

add_action('esr-registration-user-form-element', ['ESR_Registration_User_Form_Templater', 'esr_user_form_name_callback']);
add_action('esr-registration-user-form-element', ['ESR_Registration_User_Form_Templater', 'esr_user_form_email_callback']);
add_action('esr-registration-user-form-element', ['ESR_Registration_User_Form_Templater', 'esr_user_form_phone_callback']);

