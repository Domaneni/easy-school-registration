<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Registration_User_Form_Templater {

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
				<label for="esr-name" class="required"><?php esc_html_e( 'First Name', 'easy-school-registration' ); ?></label>
				<div class="esr-form-input">
                    <input id="esr-name" required type="text" name="name"
                            value="<?php echo esc_attr($default_data['name']); ?>">
				</div>
			</div>
			<div class="esr-user-form-element">
				<label for="esr-surname" class="required"><?php esc_html_e( 'Surname', 'easy-school-registration' ); ?></label>
				<div class="esr-form-input">
                    <input id="esr-surname" required type="text" name="surname"
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
				<label for="esr-email" class="required"><?php esc_html_e( 'Email', 'easy-school-registration' ); ?></label>
				<div class="esr-form-input">
					<input id="esr-email" required type="text" name="email" value="<?php echo esc_attr($default_data['email']); ?>">
				</div>
			</div>
			<?php if ($show_confirm_email_input) { ?>
				<div class="esr-user-form-element">
					<label for="esr-confirm-email" class="required"><?php esc_html_e( 'Confirm Email', 'easy-school-registration' ); ?></label>
					<div class="esr-form-input">
						<input id="esr-confirm-email" class="esr-confirm-email" required type="text" name="esr-confirm-email" data-error-message="<?php esc_attr_e( 'Emails are not same', 'easy-school-registration' ); ?>">
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
					<label for="esr-phone" class="required"><?php esc_html_e( 'Phone', 'easy-school-registration' ); ?></label>
					<div class="esr-form-input">
						<input id="esr-phone" type="text" name="phone"
						value="<?php echo( $default_data['phone'] ? esc_attr($default_data['phone']) : '' ); ?>"
						<?php if ( intval( ESR()->settings->esr_get_option( 'phone_required', 1 ) ) !== - 1 ) {
							echo 'required';
						} ?>>
					</div>
				</div>
			<?php
		}
	}
    public static function esr_user_form_note_callback($default_data) {
        $show_note         = intval( ESR()->settings->esr_get_option( 'note_disabled', -1 ) ) !== 1;

        if ( $show_note ) {
            $classes = ['esr-user-form-element', 'stretch'];

            $note_length = ESR()->settings->esr_get_option( 'note_limit', 0 );
            $input_classes = ['esr-form-input'];

            if ( $note_length > 0 ) {
                $input_classes[] = 'esr-textarea-limit';
            }
			?>
				<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
					<label for="esr-note"><?php echo esc_html(ESR()->settings->esr_get_option( 'registration_note_title', esc_html__( 'Note', 'easy-school-registration' ) )); ?></label>
					<div class="<?php echo esc_attr(implode(' ', $input_classes)); ?>">
                        <textarea id="esr-note" name="note" <?php if ( $note_length > 0 ) { ?>maxlength="<?php echo esc_attr($note_length); ?>"<?php } ?>></textarea>
					</div>
				</div>
        <?php }
    }
    public static function esr_user_form_newsletter_callback($default_data) {
        $show_checkbox = intval( ESR()->settings->esr_get_option( 'newsletter_enabled', - 1 ) ) !== - 1;

        if ( $show_checkbox ) {
            $classes = ['esr-user-form-element', 'stretch'];
            ?>
            <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
                <div class="esr-form-input checkbox">
                    <label for="esr-newsletter">
                    <input id="esr-newsletter" name="newsletter" type="checkbox"
                           value="1"
                        <?php
                        if ( ( $default_data['newsletter'] == 1 ) || ( $default_data['newsletter'] === "" && ( intval( ESR()->settings->esr_get_option( 'newsletter_default', 1 ) ) !== - 1 ) ) ) {
                            echo 'checked';
                        }
                        ?>>
                    <?php echo wp_kses_post(nl2br( stripcslashes( ESR()->settings->esr_get_option( 'newsletter_text', '' )))); ?></label>
                </div>
            </div>
        <?php }
    }
    public static function esr_user_form_terms_callback($default_data) {
        $show_checkbox = intval( ESR()->settings->esr_get_option( 'terms_conditions_enabled', - 1 ) ) != - 1;

        if ( $show_checkbox ) {
            $classes = ['esr-user-form-element', 'stretch'];
            ?>
            <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
                <div class="esr-form-input checkbox">
                    <label for="esr-terms-conditions">
                        <input id="esr-terms-conditions" name="terms-conditions" type="checkbox" value="1"
                            <?php echo( intval( ESR()->settings->esr_get_option( 'terms_conditions_required', - 1 ) ) != - 1 ? ' required' : '' ); ?>>
                        <?php echo wp_kses_post(nl2br( stripcslashes( ESR()->settings->esr_get_option( 'terms_conditions_text', '' ) ) )); ?></label>
                </div>
            </div>
        <?php }
    }
}

add_action('esr_registration_user_form_start', ['ESR_Registration_User_Form_Templater', 'esr_user_form_start_callback'], 10, 0);
add_action('esr_registration_user_form_end', ['ESR_Registration_User_Form_Templater', 'esr_user_form_end_callback'], 10, 0);

add_action('esr-registration-user-form-element', ['ESR_Registration_User_Form_Templater', 'esr_user_form_name_callback'], 10);
add_action('esr-registration-user-form-element', ['ESR_Registration_User_Form_Templater', 'esr_user_form_email_callback'], 20);
add_action('esr-registration-user-form-element', ['ESR_Registration_User_Form_Templater', 'esr_user_form_phone_callback'], 30);
add_action('esr-registration-user-form-element', ['ESR_Registration_User_Form_Templater', 'esr_user_form_newsletter_callback'], 40);
add_action('esr-registration-user-form-element', ['ESR_Registration_User_Form_Templater', 'esr_user_form_terms_callback'], 50);
add_action('esr-registration-user-form-element', ['ESR_Registration_User_Form_Templater', 'esr_user_form_note_callback'], 60);

