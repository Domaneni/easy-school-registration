<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Gutenberg {

	public static function esr_render_wave_schedule_block_callback( $attributes = array() ) {
		return self::esr_render_block( 'esr_wave_schedule', $attributes );
	}


	public static function esr_render_course_registration_block_callback( $attributes = array() ) {
		return self::esr_render_block( 'esr_course_registration', $attributes );
	}

	private static function esr_render_block( $key, $attributes = array() ) {
		// Prepare variables.
		$wave_id               = isset( $attributes['waveId'] ) ? $attributes['waveId'] : false;
		$style                 = isset( $attributes['styleKey'] ) ? $attributes['styleKey'] : 'by_hours';
		$zoom                  = isset( $attributes['zoom'] ) ? $attributes['zoom'] : false;
		$groupFilter           = isset( $attributes['groupFilter'] ) ? $attributes['groupFilter'] : false;
		$hideNotSelectedGroups = isset( $attributes['hideNotSelectedGroups'] ) ? $attributes['hideNotSelectedGroups'] : false;
		$levelFilter           = isset( $attributes['levelFilter'] ) ? $attributes['levelFilter'] : false;
		$hideNotSelectedLevels = isset( $attributes['hideNotSelectedLevels'] ) ? $attributes['hideNotSelectedLevels'] : false;
		$showSpecificGroup     = isset( $attributes['showSpecificGroup'] ) ? $attributes['showSpecificGroup'] : false;
		$showHover             = isset( $attributes['showHover'] ) ? $attributes['showHover'] : false;

		// If form ID was not provided or form does not exist, return.
		if ( ! $wave_id ) {
			return '';
		}

		$shortcode_items = [
			'waves="' . $wave_id . '"',
			'type="' . $style . '"'
		];

		$shortcode_items = self::esr_set_not_required_shortcode_item( $shortcode_items, 'automatic_zoom', $zoom );
		$shortcode_items = self::esr_set_not_required_shortcode_item( $shortcode_items, 'show_group_filter', $groupFilter );
		$shortcode_items = self::esr_set_not_required_shortcode_item( $shortcode_items, 'group_filter_hide_courses', $hideNotSelectedGroups );
		$shortcode_items = self::esr_set_not_required_shortcode_item( $shortcode_items, 'show_level_filter', $levelFilter );
		$shortcode_items = self::esr_set_not_required_shortcode_item( $shortcode_items, 'level_filter_hide_courses', $hideNotSelectedLevels );
		$shortcode_items = self::esr_set_not_required_shortcode_item( $shortcode_items, 'filter_group', $showSpecificGroup );
		$shortcode_items = self::esr_set_not_required_shortcode_item( $shortcode_items, 'hover_option', $showHover );

		return '[' . $key . ' ' . implode( ' ', $shortcode_items ) . ' ]';
	}

	private static function esr_set_not_required_shortcode_item( $shortcode_items, $key, $value ) {
		if ( $value !== false ) {
			$shortcode_items[] = $key . '="' . $value . '"';
		}

		return $shortcode_items;
	}

}

register_block_type( 'easy-school-registration/schedule', array(
	'render_callback' => array( 'ESR_Gutenberg', 'esr_render_wave_schedule_block_callback' ),
	'editor_script'   => 'esr_wave_schedule_block_form',
	'attributes'      => array(
		'waveId'                => array( 'type' => 'integer' ),
		'style'                 => array( 'type' => 'string' ),
		'zoom'                  => array( 'type' => 'boolean' ),
		'groupFilter'           => array( 'type' => 'boolean' ),
		'hideNotSelectedGroups' => array( 'type' => 'boolean' ),
		'levelFilter'           => array( 'type' => 'boolean' ),
		'hideNotSelectedLevels' => array( 'type' => 'boolean' ),
		'showSpecificGroup'     => array( 'type' => 'string' ),
		'showHover'             => array( 'type' => 'string' ),
	),
) );

register_block_type( 'easy-school-registration/registration', array(
	'render_callback' => array( 'ESR_Gutenberg', 'esr_render_course_registration_block_callback' ),
	'editor_script'   => 'esr_course_registration_block_form',
	'attributes'      => array(
		'waveId'                => array( 'type' => 'integer' ),
		'style'                 => array( 'type' => 'string' ),
		'zoom'                  => array( 'type' => 'boolean' ),
		'groupFilter'           => array( 'type' => 'boolean' ),
		'hideNotSelectedGroups' => array( 'type' => 'boolean' ),
		'levelFilter'           => array( 'type' => 'boolean' ),
		'hideNotSelectedLevels' => array( 'type' => 'boolean' ),
		'showSpecificGroup'     => array( 'type' => 'string' ),
		'showHover'             => array( 'type' => 'string' ),
	),
) );
