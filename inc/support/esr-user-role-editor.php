<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Support_User_Role_Editor {

	CONST ESR_CAP_GROUP_KEY = 'easyschoolregistration';

	public static function esr_add_custom_group_to_tree_callback( $groups = array() ) {
		$groups[self::ESR_CAP_GROUP_KEY] = array(
			'caption' => esc_html__( 'Easy School Registration', 'easy-school-registration' ),
			'parent'  => 'custom',
			'level'   => 2,
		);

		return $groups;
	}

	public static function esrt_add_capabilities_to_group_callback ( $groups = array(), $cap_id = '' ) {
		$caps = ESR()->role->esr_get_capabilities();

		// If capability belongs to Add-On, register it to group.
		if ( isset($caps[$cap_id]) ) {
			$groups[] = self::ESR_CAP_GROUP_KEY;
		}

		return $groups;

	}
}

add_filter( 'ure_capabilities_groups_tree', [ 'ESR_Support_User_Role_Editor', 'esr_add_custom_group_to_tree_callback' ], 99 );
add_filter( 'ure_custom_capability_groups', [ 'ESR_Support_User_Role_Editor', 'esrt_add_capabilities_to_group_callback' ], 99, 2 );
