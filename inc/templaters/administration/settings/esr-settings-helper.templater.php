<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Settings_Helper_Templater {

	public static function esr_email_callback( $args ) {
		$esr_settings = ESR()->settings->esr_get_option( $args['id'] );

		if ( $esr_settings ) {
			$value = $esr_settings;
		} elseif ( ! empty( $args['allow_blank'] ) && empty( $esr_settings ) ) {
			$value = '';
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$name = 'name="esr_settings[' . esc_attr( $args['id'] ) . ']"';

		$class = self::esr_esc_html_class( $args['field_class'] );

		$html = '<input type="email" class="' . esc_attr($class) . ' ' . 'regular-text" id="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<label class="esr-settings-description" for="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

		echo apply_filters( 'esr_after_setting_output', $html, $args );
	}


	public static function esr_text_callback( $args ) {
		$esr_settings = ESR()->settings->esr_get_option( $args['id'] );
		$has_tags     = isset( $args['desc_tags'] );

		if ( $esr_settings ) {
			$value = $esr_settings;
		} elseif ( ! empty( $args['allow_blank'] ) && empty( $esr_settings ) ) {
			$value = '';
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$name = 'name="esr_settings[' . esc_attr( $args['id'] ) . ']"';

		$class = self::esr_esc_html_class( $args['field_class'] );

		$html = '<input type="text" class="' . esc_attr($class) . ' ' . 'regular-text" id="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"/>';

		if ( $has_tags ) {
			$html .= '<br/>';
		}

		$html .= '<label class="esr-settings-description' . ( $has_tags ? ' esr-has-tags' : '' ) . '" for="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

		if ( $has_tags ) {
			$html .= '<br/>' . wp_kses_post( $args['desc_tags'] );
		}

		echo apply_filters( 'esr_after_setting_output', $html, $args );
	}


	public static function esr_number_callback( $args ) {
		$esr_settings = ESR()->settings->esr_get_option( $args['id'] );

		if ( $esr_settings ) {
			$value = $esr_settings;
		} elseif ( ! empty( $args['allow_blank'] ) && empty( $esr_settings ) ) {
			$value = '';
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$name = 'name="esr_settings[' . esc_attr( $args['id'] ) . ']"';

		$class = self::esr_esc_html_class( $args['field_class'] );

		$html = '<input type="number" min="' . esc_attr($args['min']) . '" class="' . esc_attr($class) . ' ' . 'regular-text" id="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']" ' . $name . ' value="' . intval( esc_attr( stripslashes( $value ) ) ) . '"/>';
		$html .= '<label class="esr-settings-description" for="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

		echo apply_filters( 'esr_after_setting_output', $html, $args );
	}


	public static function esr_color_picker_callback( $args ) {
		$esr_settings = ESR()->settings->esr_get_option( $args['id'] );

		if ( $esr_settings ) {
			$value = $esr_settings;
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$name = 'name="esr_settings[' . esc_attr( $args['id'] ) . ']"';

		$class = self::esr_esc_html_class( $args['field_class'] );

		$html = '<input type="text" class="' . esc_attr($class) . ' esr-color-picker" id="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		$html .= '<label class="esr-settings-description" for="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

		echo apply_filters( 'esr_after_setting_output', $html, $args );
	}


	public static function esr_style_course_box_callback( $args ) {
		$esr_settings = ESR()->settings->esr_get_option( $args['id'] );

		if ( $esr_settings ) {
			$values = $esr_settings;
		} else {
			$values = isset( $args['std'] ) ? $args['std'] : '';
		}

		$name = esc_attr( $args['id'] );
		$id   = self::esr_esc_key( $args['id'] );

		$class = self::esr_esc_html_class( $args['field_class'] );

		$value_opacity      = esc_attr( stripslashes( isset( $values['background_opacity'] ) ? $values['background_opacity'] : '' ) );
		$value_border_width = esc_attr( stripslashes( isset( $values['border_width'] ) ? $values['border_width'] : '' ) );

		$html = '<table>
					<tr>
						<th>' . esc_html__( 'Background Color', 'easy-school-registration' ) . '</th>
						<th>' . esc_html__( 'Background Opacity', 'easy-school-registration' ) . '</th>
						<th>' . esc_html__( 'Border Color', 'easy-school-registration' ) . '</th>
						<th>' . esc_html__( 'Border Width', 'easy-school-registration' ) . '</th>
					</tr>
					<tr>
						<td><input type="text" class="' . esc_attr($class) . ' esr-color-picker" id="esr_settings[' . esc_attr($id) . '_background_color]" name="esr_settings[' . $name . '][background_color]" value="' . esc_attr( stripslashes( isset( $values['background_color'] ) ? $values['background_color'] : '' ) ) . '"/></td>
						<td><span class="esr-range-value">' . esc_html($value_opacity) . '</span><input type="range" min="0" max="1" step="0.1" class="' . esc_attr($class) . ' esr-range" id="esr_settings[' . esc_attr($id) . '_background_opacity]" name="esr_settings[' . $name . '][background_opacity]" value="' . esc_attr($value_opacity) . '"/></td>
						<td><input type="text" class="' . esc_attr($class) . ' esr-color-picker" id="esr_settings[' . esc_attr($id) . '_border_color]" name="esr_settings[' . $name . '][border_color]" value="' . esc_attr( stripslashes( isset( $values['border_color'] ) ? $values['border_color'] : '' ) ) . '"/></td>
						<td><span class="esr-range-value">' . esc_html($value_border_width) . '</span><input type="range" min="0" max="20" step="1" class="' . esc_attr($class) . ' esr-range" id="esr_settings[' . esc_attr($id) . '_border_width]" name=esr_settings[' . $name . '][border_width]" value="' . esc_attr($value_border_width) . '"/></td>
					</tr>
				</table>';

		echo apply_filters( 'esr_after_setting_output', $html, $args );
	}


	public static function esr_style_course_box_with_font_callback( $args ) {
		$esr_settings = ESR()->settings->esr_get_option( $args['id'] );

		if ( $esr_settings ) {
			$values = $esr_settings;
		} else {
			$values = isset( $args['std'] ) ? $args['std'] : '';
		}

		$name = esc_attr( $args['id'] );
		$id   = self::esr_esc_key( $args['id'] );

		$class = self::esr_esc_html_class( $args['field_class'] );

		$value_opacity      = esc_attr( stripslashes( isset( $values['background_opacity'] ) ? $values['background_opacity'] : '' ) );
		$value_border_width = esc_attr( stripslashes( isset( $values['border_width'] ) ? $values['border_width'] : '' ) );
		$value_font_size    = esc_attr( stripslashes( isset( $values['size'] ) ? $values['size'] : '' ) );

		$html = '<table>';
		if ( isset( $args['optional'] ) && $args['optional'] ) {
			$is_checked = isset( $values['enable_style'] ) && $values['enable_style'];
			$html       .= '<tr>
						<th colspan="4" class="esr-settings-enable-style"><label for="esr_settings[' . $name . '][enable_style]"><input type="checkbox" id="esr_settings[' . esc_attr($id) . '_enable_style]" name="esr_settings[' . $name . '][enable_style]"' . ( $is_checked ? ' checked' : '' ) . '/>' . esc_html__( 'enable style', 'easy-school-registration' ) . '</label></th>
					</tr>';
		}
		$html .= '<tr>
						<th>' . esc_html__( 'Background Color', 'easy-school-registration' ) . '</th>
						<th>' . esc_html__( 'Background Opacity', 'easy-school-registration' ) . '</th>
						<th>' . esc_html__( 'Border Color', 'easy-school-registration' ) . '</th>
						<th>' . esc_html__( 'Border Width', 'easy-school-registration' ) . '</th>
					</tr>
					<tr>
						<td><input type="text" class="' . esc_attr($class) . ' esr-color-picker" id="esr_settings[' . esc_attr($id) . '_background_color]" name="esr_settings[' . $name . '][background_color]" value="' . esc_attr( stripslashes( isset( $values['background_color'] ) ? $values['background_color'] : '' ) ) . '"/></td>
						<td><span class="esr-range-value">' . esc_html($value_opacity) . '</span><input type="range" min="0" max="1" step="0.1" class="' . esc_attr($class) . ' esr-range" id="esr_settings[' . esc_attr($id) . '_background_opacity]" name="esr_settings[' . $name . '][background_opacity]" value="' . esc_attr($value_opacity) . '"/></td>
						<td><input type="text" class="' . esc_attr($class) . ' esr-color-picker" id="esr_settings[' . esc_attr($id) . '_border_color]" name="esr_settings[' . $name . '][border_color]" value="' . esc_attr( stripslashes( isset( $values['border_color'] ) ? $values['border_color'] : '' ) ) . '"/></td>
						<td><span class="esr-range-value">' . esc_html($value_border_width) . '</span><input type="range" min="0" max="20" step="1" class="' . esc_attr($class) . ' esr-range" id="esr_settings[' . esc_attr($id) . '_border_width]" name=esr_settings[' . $name . '][border_width]" value="' . esc_attr($value_border_width) . '"/></td>
					</tr>
					<tr>
						<th>' . esc_html__( 'Text Color', 'easy-school-registration' ) . '</th>
						<th>' . esc_html__( 'Text Size', 'easy-school-registration' ) . '</th>
					</tr>
					<tr>
						<td><input type="text" class="' . esc_attr($class) . ' esr-color-picker" id="esr_settings[' . esc_attr($id) . '_color]" name="esr_settings[' . $name . '][color]" value="' . esc_attr( stripslashes( isset( $values['color'] ) ? $values['color'] : '' ) ) . '"/></td>
						<td><span class="esr-range-value">' . esc_html($value_font_size) . '</span><input type="range" min="1" max="40" step="1" class="' . esc_attr($class) . ' esr-range" id="esr_settings[' . esc_attr($id) . '_size]" name="esr_settings[' . $name . '][size]" value="' . esc_attr($value_font_size) . '"/></td>
					</tr>
				</table>';

		echo apply_filters( 'esr_after_setting_output', $html, $args );
	}


	public static function esr_style_course_font_callback( $args ) {
		$esr_settings = ESR()->settings->esr_get_option( $args['id'] );

		if ( $esr_settings ) {
			$values = $esr_settings;
		} else {
			$values = isset( $args['std'] ) ? $args['std'] : '';
		}

		$name = esc_attr( $args['id'] );
		$id   = self::esr_esc_key( $args['id'] );

		$class = self::esr_esc_html_class( $args['field_class'] );

		$value_font_size = esc_attr( stripslashes( isset( $values['size'] ) ? $values['size'] : '' ) );

		$html = '<table>
					<tr>
						<th>' . esc_html__( 'Text Color', 'easy-school-registration' ) . '</th>
						<th>' . esc_html__( 'Text Size', 'easy-school-registration' ) . '</th>
					</tr>
					<tr>
						<td><input type="text" class="' . esc_attr($class) . ' esr-color-picker" id="esr_settings[' . esc_attr($id) . '_color]" name="esr_settings[' . $name . '][color]" value="' . esc_attr( stripslashes( isset( $values['color'] ) ? $values['color'] : '' ) ) . '"/></td>
						<td><span class="esr-range-value">' . esc_html($value_font_size) . '</span><input type="range" min="1" max="40" step="1" class="' . esc_attr($class) . ' esr-range" id="esr_settings[' . esc_attr($id) . '_size]" name="esr_settings[' . $name . '][size]" value="' . esc_attr($value_font_size) . '"/></td>
					</tr>
				</table>';

		echo apply_filters( 'esr_after_setting_output', $html, $args );
	}


	public static function esr_full_editor_callback( $args ) {
		$esr_settings = ESR()->settings->esr_get_option( $args['id'] );
		$has_tags     = isset( $args['desc_tags'] );

		if ( $esr_settings ) {
			$value = $esr_settings;
		} else {
			if ( ! empty( $args['allow_blank'] ) && empty( $esr_settings ) ) {
				$value = '';
			} else {
				$value = isset( $args['std'] ) ? $args['std'] : '';
			}
		}

		$rows = isset( $args['size'] ) ? $args['size'] : 20;

		$class = self::esr_esc_html_class( $args['field_class'] );

		ob_start();
		wp_editor( stripslashes( $value ), 'esr_settings_' . esc_attr( $args['id'] ), [ 'textarea_name' => 'esr_settings[' . esc_attr( $args['id'] ) . ']', 'textarea_rows' => absint( $rows ), 'editor_class' => esc_attr($class) ] );
		$html = ob_get_clean();

		if ( $has_tags ) {
			$html .= '<br/>';
		}

		$html .= '<label class="esr-settings-description' . ( $has_tags ? ' esr-has-tags' : '' ) . '" for="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

		if ( $has_tags ) {
			$html .= '<br/>' . wp_kses_post( $args['desc_tags'] );
		}

		echo apply_filters( 'esr_after_setting_output', $html, $args );
	}


	public static function esr_checkbox_callback( $args ) {
		$esr_settings = ESR()->settings->esr_get_option( $args['id'] );

		$name = 'name="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']"';

		$class = self::esr_esc_html_class( $args['field_class'] );

		$checked = ! empty( $esr_settings ) ? checked( 1, $esr_settings, false ) : ( isset( $args['std'] ) && $args['std'] ? 'checked' : '' );
		$html    = '<input type="hidden"' . $name . ' value="-1" />';
		$html    .= '<input type="checkbox" id="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']"' . $name . ' value="1" ' . esc_attr($checked) . ' class="' . esc_attr($class) . '"/>';
		$html    .= '<label class="esr-settings-description checkbox-label" for="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

		echo apply_filters( 'esr_after_setting_output', $html, $args );
	}


	public static function esr_select_callback( $args ) {
		$esr_settings = ESR()->settings->esr_get_option( $args['id'] );

		if ( $esr_settings ) {
			$value = $esr_settings;
		} else {

			// Properly set default fallback if the Select Field allows Multiple values
			if ( empty( $args['multiple'] ) ) {
				$value = isset( $args['std'] ) ? $args['std'] : '';
			} else {
				$value = ! empty( $args['std'] ) ? $args['std'] : [];
			}

		}

		$class = self::esr_esc_html_class( $args['field_class'] );

		// If the Select Field allows Multiple values, save as an Array
		$name_attr = 'esr_settings[' . esc_attr( $args['id'] ) . ']';

		$html = '<select id="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']" name="' . $name_attr . '" class="' . esc_attr($class) . '">';

		foreach ( $args['options'] as $option => $name ) {
			$html .= '<option value="' . esc_attr( $option ) . '" ' . selected( $option, $value, false ) . '>' . esc_html( $name ) . '</option>';
		}

		$html .= '</select>';
		$html .= '<label class="esr-settings-description" for="esr_settings[' . self::esr_esc_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

		echo apply_filters( 'esr_after_setting_output', $html, $args );
	}


	public static function esr_add_list_callback( $args ) {
		$items = ESR()->settings->esr_get_option( $args['id'] );

		$class = self::esr_esc_html_class( $args['field_class'] );

		ob_start(); ?>
		<div class="esr_list_items">
			<table class="wp-list-table fixed posts <?php echo $class; ?>">
				<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'ID', 'easy-school-registration' ); ?></th>
					<th scope="col" class="esr_tax_country"><?php esc_html_e( 'Name', 'easy-school-registration' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Remove', 'easy-school-registration' ); ?></th>
				</tr>
				</thead>
				<?php if ( ! empty( $items ) ) : ?>
					<?php foreach ( $items as $key => $item ) : ?>
						<tr data-key="<?php echo self::esr_esc_key( $key ) ?>">
							<td class="esr_list_item esr-key-container">
								<?php echo esc_html($key); ?>
							</td>
							<td class="esr_list_item">
								<input type="text"
								       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][<?php echo self::esr_esc_key( $key ) ?>]"
								       value="<?php echo esc_html( $item ); ?>"/>
							</td>
							<td>
								<span class="esr_remove_list_item button-secondary"><?php echo esc_html__( 'Remove', 'easy-school-registration' ) . ' ' . esc_html($args['singular']); ?></span>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr data-key="0">
						<td class="esr_list_item esr-key-container">0</td>
						<td class="esr_list_item">
							<input type="text" name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][]"
							       value=""/>
						</td>
						<td>
							<span class="esr_remove_list_item button-secondary"><?php echo esc_html__( 'Remove', 'easy-school-registration' ) . ' ' .esc_html( $args['singular']); ?></span>
						</td>
					</tr>
				<?php endif; ?>
			</table>
			<span class="button-secondary esr-add-list-item"><?php echo esc_html__( 'Add', 'easy-school-registration' ) . ' ' . esc_html($args['singular']); ?></span>
		</div>
		<?php
		echo ob_get_clean();
	}


	public static function esr_add_payment_types_list_callback( $args ) {
		$items = ESR()->settings->esr_get_option( $args['id'] );

		$class = self::esr_esc_html_class( $args['field_class'] );

		ob_start(); ?>
		<div class="esr_list_items">
			<table class="wp-list-table fixed posts <?php echo $class; ?>">
				<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'ID', 'easy-school-registration' ); ?></th>
					<th scope="col" class="esr_tax_country"><?php esc_html_e( 'Name', 'easy-school-registration' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Remove', 'easy-school-registration' ); ?></th>
				</tr>
				</thead>
				<?php if ( ! empty( $items ) ) : ?>
					<?php foreach ( $items as $key => $item ) : ?>
						<tr data-key="<?php echo self::esr_esc_key( $key ) ?>">
							<td class="esr_list_item esr-key-container">
								<?php echo esc_html($key); ?>
							</td>
							<td class="esr_list_item">
								<input type="text"
								       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][<?php echo self::esr_esc_key( $key ) ?>]"
								       value="<?php echo esc_html( $item ); ?>"/>
							</td>
							<td>
								<span class="esr_remove_list_item button-secondary"><?php echo esc_html__( 'Remove', 'easy-school-registration' ) . ' ' . esc_html($args['singular']); ?></span>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr data-key="2">
						<td class="esr_list_item esr-key-container">2</td>
						<td class="esr_list_item">
							<input type="text" name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][2]"
							       value=""/>
						</td>
						<td>
							<span class="esr_remove_list_item button-secondary"><?php echo esc_html__( 'Remove', 'easy-school-registration' ) . ' ' . esc_html($args['singular']); ?></span>
						</td>
					</tr>
				<?php endif; ?>
			</table>
			<span class="button-secondary esr-add-list-item"><?php echo esc_html__( 'Add', 'easy-school-registration' ) . ' ' . esc_html($args['singular']); ?></span>
		</div>
		<?php
		echo ob_get_clean();
	}


	public static function esr_add_list_times_callback( $args ) {
		$items = ESR()->settings->esr_get_option( $args['id'] );

		$class = self::esr_esc_html_class( $args['field_class'] );

		ob_start(); ?>
		<div class="esr_list_items">
			<table class="wp-list-table fixed posts <?php echo $class; ?>">
				<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'ID', 'easy-school-registration' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Time From', 'easy-school-registration' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Time To', 'easy-school-registration' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Remove', 'easy-school-registration' ); ?></th>
				</tr>
				</thead>
				<?php if ( ! empty( $items ) ) : ?>
					<?php foreach ( $items as $key => $item ) : ?>
						<tr data-key="<?php echo self::esr_esc_key( $key ) ?>">
							<td class="esr_list_item esr-key-container">
								<?php echo esc_html($key); ?>
							</td>
							<td class="esr_list_item">
								<input type="time"
								       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][<?php echo self::esr_esc_key( $key ) ?>][from]"
								       value="<?php echo esc_html( $item['from'] ); ?>"/>
							</td>
							<td class="esr_list_item">
								<input type="time"
								       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][<?php echo self::esr_esc_key( $key ) ?>][to]"
								       value="<?php echo esc_html( $item['to'] ); ?>"/>
							</td>
							<td>
								<span class="esr_remove_list_item button-secondary"><?php echo esc_html__( 'Remove', 'easy-school-registration' ) . ' ' . esc_html($args['singular']); ?></span>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr data-key="0">
						<td class="esr_list_item esr-key-container">0</td>
						<td class="esr_list_item">
							<input type="time"
							       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][0][from]"
							/>
						</td>
						<td class="esr_list_item">
							<input type="time"
							       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][0][to]"
							/>
						</td>
						<td>
							<span class="esr_remove_list_item button-secondary"><?php echo esc_html__( 'Remove', 'easy-school-registration' ) . ' ' . esc_html($args['singular']); ?></span>
						</td>
					</tr>
				<?php endif; ?>
			</table>
			<span class="button-secondary esr-add-list-item"><?php echo esc_html__( 'Add', 'easy-school-registration' ) . ' ' . esc_html($args['singular']); ?></span>
		</div>
		<?php
		echo ob_get_clean();
	}


	public static function esr_add_list_halls_callback( $args ) {
		$items = ESR()->settings->esr_get_option( $args['id'] );

		$class = self::esr_esc_html_class( $args['field_class'] );

		ob_start(); ?>
		<div class="esr_list_items">
			<table class="wp-list-table fixed posts <?php echo $class; ?>">
				<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'ID', 'easy-school-registration' ); ?></th>
					<th scope="col" class="esr_tax_country"><?php esc_html_e( 'Name', 'easy-school-registration' ); ?></th>
					<th scope="col" class="esr_tax_country"><?php esc_html_e( 'Address', 'easy-school-registration' ); ?></th>
					<th scope="col" class="esr_tax_country"><?php esc_html_e( 'Latitude', 'easy-school-registration' ); ?></th>
					<th scope="col" class="esr_tax_country"><?php esc_html_e( 'Longitude', 'easy-school-registration' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Default Couples', 'easy-school-registration' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Default Solo', 'easy-school-registration' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Remove', 'easy-school-registration' ); ?></th>
				</tr>
				</thead>
				<?php if ( ! empty( $items ) ) { ?>
					<?php foreach ( $items as $key => $item ) {
						$new_item = $item;
						if ( ! is_array( $item ) ) {
							$new_item              = [];
							$new_item['name']      = $item;
							$new_item['address']   = '';
							$new_item['latitude']  = '';
							$new_item['longitude'] = '';
						}
						?>
						<tr data-key="<?php echo self::esr_esc_key( $key ) ?>">
							<td class="esr_list_item esr-key-container">
								<?php echo esc_html($key); ?>
							</td>
							<td class="esr_list_item">
								<input type="text"
								       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][<?php echo self::esr_esc_key( $key ) ?>][name]"
								       value="<?php echo esc_html( $new_item['name'] ); ?>"/>
							</td>
							<td class="esr_list_item">
								<input type="text"
								       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][<?php echo self::esr_esc_key( $key ) ?>][address]"
								       value="<?php echo esc_html( isset( $new_item['address'] ) ? $new_item['address'] : '' ); ?>"/>
							</td>
							<td class="esr_list_item">
								<input type="text"
								       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][<?php echo self::esr_esc_key( $key ) ?>][latitude]"
								       value="<?php echo esc_html( isset( $new_item['latitude'] ) ? $new_item['latitude'] : '' ); ?>"/>
							</td>
							<td class="esr_list_item">
								<input type="text"
								       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][<?php echo self::esr_esc_key( $key ) ?>][longitude]"
								       value="<?php echo esc_html( isset( $new_item['longitude'] ) ? $new_item['longitude'] : '' ); ?>"/>
							</td>
							<td class="esr_list_item">
								<input type="number"
								       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][<?php echo self::esr_esc_key( $key ) ?>][couples]"
								       value="<?php echo esc_html( isset( $new_item['couples'] ) ? $new_item['couples'] : 0 ); ?>"/>
							</td>
							<td class="esr_list_item">
								<input type="number"
								       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][<?php echo self::esr_esc_key( $key ) ?>][solo]"
								       value="<?php echo esc_html( isset( $new_item['solo'] ) ? $new_item['solo'] : 0 ); ?>"/>
							</td>
							<td>
								<span class="esr_remove_list_item button-secondary"><?php echo esc_html__( 'Remove', 'easy-school-registration' ) . ' ' . esc_html($args['singular']); ?></span>
							</td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr data-key="0">
						<td class="esr_list_item esr-key-container">0</td>
						<td class="esr_list_item">
							<input type="text"
							       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][0][name]"
							       value=""/>
						</td>
						<td class="esr_list_item">
							<input type="text"
							       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][0][address]"
							       value=""/>
						</td>
						<td class="esr_list_item">
							<input type="text"
							       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][0][latitude]"
							       value=""/>
						</td>
						<td class="esr_list_item">
							<input type="text"
							       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][0][longitude]"
							       value=""/>
						</td>
						<td class="esr_list_item">
							<input type="number"
							       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][0][couples]"
							       value=""/>
						</td>
						<td class="esr_list_item">
							<input type="number"
							       name="esr_settings[<?php echo self::esr_esc_key( $args['id'] ); ?>][0][solo]"
							       value=""/>
						</td>
						<td>
							<span class="esr_remove_list_item button-secondary"><?php echo esc_html__( 'Remove', 'easy-school-registration' ) . ' ' . esc_html($args['singular']); ?></span>
						</td>
					</tr>
				<?php } ?>
			</table>
			<span class="button-secondary esr-add-list-item"><?php echo esc_html__( 'Add', 'easy-school-registration' ) . ' ' . esc_html($args['singular']); ?></span>
		</div>
		<?php
		echo ob_get_clean();
	}


	public static function esr_description_callback( $args ) {

		$html = '<span> ' . wp_kses_post( $args['desc'] ) . '</span>';

		echo apply_filters( 'esr_after_setting_output', $html, $args );
	}


	public static function esr_esc_html_class($class = '' ) {

		if ( is_string( $class ) ) {
			$class = sanitize_html_class( $class );
		} else if ( is_array( $class ) ) {
			$class = array_values( array_map( 'sanitize_html_class', $class ) );
			$class = implode( ' ', array_unique( $class ) );
		}

		return $class;

	}


	public static function esr_esc_key($key ) {
		$raw_key = $key;
		$key     = esc_attr(preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key ));

		return apply_filters( 'esr_sanitize_key', $key, $raw_key );
	}

}
