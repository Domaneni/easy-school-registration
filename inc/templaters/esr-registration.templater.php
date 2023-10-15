<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Registration_Templater {

	/**
	 * @codeCoverageIgnore
	 */
	public function print_courses_registration( $attr ) {
		if ( ! empty( $attr ) ) {
			$templater_schedule = new ESR_Schedule_Templater();

			$wave_ids = is_array( $attr['waves'] ) ? $attr['waves'] : explode( ',', $attr['waves'] );

			$user         = wp_get_current_user();
			$default_data = [
				'name'       => $this->get_user_default_value( $user, 'first_name' ),
				'surname'    => $this->get_user_default_value( $user, 'last_name' ),
				'email'      => $this->get_user_default_value( $user, 'user_email' ),
				'phone'      => $this->get_user_default_meta_value( $user, 'esr-course-registration-phone' ),
				'newsletter' => $this->get_user_default_meta_value( $user, 'esr-course-registration-newsletter' ),
			];

			?>
			<div class="esr-registration-form-container" style="position: relative">
			<?php
			$templater_schedule->print_content( $wave_ids, $attr, true, null );
			$this->print_courses_registration_form( $default_data, $wave_ids, isset( $attr['show_groups'] ) );
			?>
			<div class="spinner-bg" style="background: #000;opacity: 0.5;position: absolute;bottom: -10px;left: -10px;right: -10px;top: -10px;z-index: 5000;display: none;"></div></div><?php
		}
	}


	public function print_courses_registration_form( $default_data, $wave_ids, $show_groups = false ) {
		global $reg_errors;
		$registration_open = false;

		foreach ( $wave_ids as $wave_id ) {
			if ( ESR()->wave->is_wave_registration_active( $wave_id ) ) {
				$registration_open = true;
			}
		}

		if ( $registration_open ) {
			?>
			<form id="esr-course-registration-form"
			      method="post"
			      class="esr-course-registration-form"
			      data-show-groups="<?php echo esc_attr($show_groups); ?>"
			      data-round-payments="<?php echo esc_attr(ESR()->settings->esr_get_option( 'round_payments', - 1 )); ?>"
			      data-no-courses="<?php esc_attr_e( 'At least one course required to select.', 'easy-school-registration' ); ?>">
				<div class="esr-choosed-courses"
				     data-price-template="<?php echo esc_attr(ESR()->currency->prepare_price( '[price]' )); ?>">
					<div class="esr-header">
						<span><?php esc_html_e( 'Course', 'easy-school-registration' ); ?></span>
						<span class="esr-hide-mobile"><?php esc_html_e( 'Registration info', 'easy-school-registration' ); ?></span>
						<span><?php esc_html_e( 'Price', 'easy-school-registration' ); ?></span>
					</div>
					<?php
					foreach ( $wave_ids as $wave_id ) {
						if ( ESR()->wave->is_wave_registration_active( $wave_id ) ) {
							$wave_data = ESR()->wave->get_wave_data( $wave_id );
							?>
							<div class="esr-clearfix esr-group wave_<?php echo esc_attr(trim($wave_id)); ?>"><?php

							?>
							<div class="esr-clearfix esr-group-header">
								<?php echo esc_html($wave_data->title); ?>
							</div>
							<?php
							?>
							<div class="esr-clearfix esr-group-content"><?php
								if ( $show_groups ) {
									$groups = ESR()->course_group->get_groups_by_wave( $wave_id );
									foreach ( $groups as $group_id ) {
										$group = ESR()->course_group->get_item( $group_id['group_id'] );
										?>
										<div class="esr-clearfix esr-group-<?php echo esc_attr($group_id['group_id']); ?>">
											<div class="esr-clearfix esr-group-header">
												<?php echo esc_html($group); ?>
											</div>
											<div class="esr-clearfix esr-sub-group-content">
											</div>
										</div>
										<?php
									}
								}
								?>
							</div>
							<?php if ( count( $wave_ids ) > 1 ) { ?>
								<div class="esr-clearfix esr-wave-price esr-footer">
									<div class="esr-price-label"><?php esc_html_e( 'Wave price', 'easy-school-registration' ); ?></div>
									<div class="esr-price-value esr-wave-price-count"></div>
								</div>
							<?php } ?>
							</div><?php
						}
					}
					?>
					<div class="esr-clearfix esr-footer">
						<div class="esr-price-label"><?php esc_html_e( 'Final price', 'easy-school-registration' ); ?></div>
						<div class="esr-price-value esr-total-price-count"></div>
					</div>
					<?php do_action( 'esr_front_page_registration_under_selected_courses', $wave_ids ); ?>
				</div>
				<?php
				$show_phone_input         = intval( ESR()->settings->esr_get_option( 'show_phone_input', 1 ) ) !== - 1;
				$show_confirm_email_input = intval( ESR()->settings->esr_get_option( 'reconfirm_email_required', - 1 ) ) === 1;
				$is_one_column            = $show_phone_input || $show_confirm_email_input;

				do_action('esr_registration_user_form_start');
				do_action('esr-registration-user-form-element', $default_data);
				do_action('esr_registration_user_form_end');
				?>

					<?php do_action( 'esr_front_page_registration_form_input', $wave_ids ); ?>

				<p><input type="submit" name="esr-registration-submitted" value="<?php esc_attr_e( 'Register', 'easy-school-registration' ); ?>"/></p>
			</form>
			<div class="esr-prep-form-row" style="display: none;">
				<?php self::print_course_registration_row(); ?>
			</div> <?php
		}
	}


	private function print_course_registration_row() {
		?>
		<div class="esr-course-row course-%course-id%" data-course="%course-id%">
			<div class="name">
				<span class="main">%course-name%</span>
				<span class="sub">%course-day% %course-start%</span>
			</div>
			<div class="mobile-price"><?php echo esc_html(ESR()->currency->prepare_price( '%course-price%' )); ?></div>
			<div class="registration-info">
				<div class="esr-info-row">
					<span class="esr-info-row-label"><?php esc_html_e( 'Dancing as', 'easy-school-registration' ); ?>:</span>
					<?php
					$leader_settings_label   = ESR()->settings->esr_get_option( 'leader_label', '' );
					$follower_settings_label = ESR()->settings->esr_get_option( 'follower_label', '' );
					?>
					<select class="esr-info-row-input esr-dancing-as" required
					        name="dancing-as-%course-id%">
						<option value=""><?php echo esc_html__( '- choose -', 'easy-school-registration' ); ?></option>
						<option value="<?php echo esc_attr(ESR_Dancing_As::LEADER); ?>"><?php echo (empty( $leader_settings_label ) ? esc_html(ESR()->dance_as->get_title( ESR_Dancing_As::LEADER )) : esc_html($leader_settings_label) ); ?></option>
						<option value="<?php echo esc_attr(ESR_Dancing_As::FOLLOWER); ?>"><?php echo (empty( $follower_settings_label ) ? esc_html(ESR()->dance_as->get_title( ESR_Dancing_As::FOLLOWER )) : esc_html($follower_settings_label) ); ?></option>
					</select>
				</div>
				<?php if ( intval( ESR()->settings->esr_get_option( 'dancing_with_enforce', 1 ) ) !== 1 ) { ?>
					<div class="esr-info-row esr-row-choose-partner">
						<span class="esr-info-row-label"><?php esc_html_e( 'Do you have partner?', 'easy-school-registration' ); ?></span>
						<div class="esr-info-row-input">
							<label class="esr-choose-partner"><input type="radio" class="choose_partner" name="choose-partner-%course-id%" value="1" required> <?php esc_html_e( 'Yes', 'easy-school-registration' ); ?></label>
							<label class="esr-choose-partner"><input type="radio" class="choose_partner" name="choose-partner-%course-id%" value="0" required> <?php esc_html_e( 'No', 'easy-school-registration' ); ?></label>
						</div>
					</div>
                <?php } ?>
					<div class="esr-info-row esr-row-dancing-with" style="display: none;">
						<span class="esr-info-row-label"><?php esc_html_e( 'Partner email', 'easy-school-registration' ); ?>:</span>
						<input class="esr-info-row-input esr-dancing-with" type="email" name="dancing-with">
					</div>
			</div>
			<div class="price esr-hide-mobile" data-price="%course-price%"><?php echo esc_html(ESR()->currency->prepare_price( '%course-price%' )); ?></div>
			<input type="hidden" name="course_id" value="%course-id%">
			<input type="hidden" name="wave_id" value="%course-wave%">
		</div>
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


}