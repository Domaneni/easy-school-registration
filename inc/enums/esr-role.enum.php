<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Role {
	const
		STUDENT = 1, TEACHER = 2;

	private $items = [
		self::STUDENT => [
			'key'          => 'esr_student',
			'title'        => 'Student',
			'capabilities' => [
				'read'        => true,
				'esr_student' => true
			]
		],
		self::TEACHER => [
			'key'          => 'esr_teacher',
			'title'        => 'Teacher',
			'capabilities' => [
				'esr_course_view'                 => true,
				'esr_course_in_number_view'       => true,
				'esr_registration_view'           => true,
				'esr_registrations_teacher_limit' => true,
				'esr_teacher_view'                => true,
				'esr_teacher_info'                => true,
				'esr_school'                      => true,
				'esr_student'                     => true,
				'read'                            => true,
			]
		],
	];

	public $capabilities = [
		'esr_add_over_limit'              => true,
		'esr_course_edit'                 => true,
		'esr_course_view'                 => true,
		'esr_course_in_number_edit'       => true,
		'esr_course_in_number_view'       => true,
		'esr_payment_emails'              => true,
		'esr_payment_edit'                => true,
		'esr_payment_view'                => true,
		'esr_payment_debts_view'          => true,
		'esr_registration_edit'           => true,
		'esr_registration_view'           => true,
		'esr_settings'                    => true,
		'esr_school'                      => true,
		'esr_show_student_emails'         => true,
		'esr_student'                     => true,
		'esr_students_view'               => true,
		'esr_teacher_edit'                => true,
		'esr_teacher_info'                => true,
		'esr_teacher_view'                => true,
		'esr_wave_edit'                   => true,
		'esr_wave_view'                   => true,
		'esr_registrations_teacher_limit' => false,
	];


	public function getItems() {
		return $this->items;
	}


	public function getItem( $key ) {
		return $this->getItems()[ $key ];
	}


	public function get_title( $key ) {
		return $this->getItem( $key )['title'];
	}

	public function esr_get_capabilities() {
		return $this->capabilities;
	}


	/**
	 * @codeCoverageIgnore
	 */
	public static function init_roles() {
		foreach ( ESR()->role->getItems() as $key => $role ) {
			if ( ! get_role( $role['key'] ) ) {
				add_role( $role['key'], $role['title'], $role['capabilities'] );
			} else {
				$existing_role = get_role( $role['key'] );
				foreach ( $role['capabilities'] as $role_key => $cap ) {
					$existing_role->add_cap( $role_key, $cap );
				}
			}
		}

		//add capabilities for admin
		$admin = get_role( 'administrator' );

		foreach ( ESR()->role->esr_get_capabilities() as $key => $cap ) {
			if ($cap) {
				$admin->add_cap( $key, $cap );
			}
		}
	}
}

add_action( 'init', [ 'ESR_role', 'init_roles' ] );