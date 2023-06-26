<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Registrations_Table_Subblock_Templater {


	public function print_table( $selected_wave ) {
		$user_can_edit          = current_user_can( 'esr_registration_edit' );
		$user_show_emails       = current_user_can( 'esr_show_student_emails' );
		$show_phone             = intval( ESR()->settings->esr_get_option( 'show_phone', - 1 ) ) === 1;
		$multiple_dates_enabled = intval( ESR()->settings->esr_get_option( 'multiple_dates', - 1 ) ) !== - 1;
		$show_user_note_enabled = intval( ESR()->settings->esr_get_option( 'show_user_note_enabled', - 1 ) ) !== - 1;
		$show_payment_enabled   = intval( ESR()->settings->esr_get_option( 'show_payment_enabled', - 1 ) ) !== - 1;
		$separate_name_enabled = intval( ESR()->settings->esr_get_option( 'show_separated_names_enabled', -1 ) ) != -1;


		$users_data = get_users( [ 'fields' => [ 'ID', 'display_name', 'user_email', 'user_firstname' ] ] );
		$user_data  = [];

		foreach ( $users_data as $u ) {
			$user_data[ $u->ID ] = $u;

			if ( $show_phone && $user_data[ $u->ID ] ) {
				$phone = get_user_meta( $u->ID, 'esr-course-registration-phone' );
				if ( isset( $phone[0] ) && $phone[0] ) {
					$user_data[ $u->ID ]->phone = $phone[0];
				}
			}

			if ( $show_user_note_enabled && $user_data[ $u->ID ] ) {
				$user_note = get_user_meta( $u->ID, 'esr_student_note' );
				if ( isset( $user_note[0] ) && $user_note[0] ) {
					$user_data[ $u->ID ]->user_note = $user_note[0];
				}
			}

			if ( $separate_name_enabled && $user_data[ $u->ID ] ) {
				$firstName = get_user_meta($u->ID, 'first_name', true);
				$lastName = get_user_meta($u->ID, 'last_name', true);

				$user_data[ $u->ID ]->first_name = $firstName;
				$user_data[ $u->ID ]->last_name = $lastName;
				
			}

		}

		$teacher = ESR()->teacher->get_teacher_data_by_user( get_current_user_id() );
		if ( ( $teacher !== null ) && isset( $teacher->limit_registrations ) && $teacher->limit_registrations ) {
			$registrations = ESR()->registration->get_registrations_by_wave_and_teacher( $selected_wave, get_current_user_id() );
		} else {
			$registrations = ESR()->registration->get_registrations_by_wave( $selected_wave );
		}

		$classes = [ 'wp-list-table widefat fixed striped esr-datatable esr-registered-courses esr-copy-table esr-excel-export esr-enable-scroll' ];

		if ( $user_show_emails ) {
			$classes[] = 'esr-email-export';
		}

		?>
		<table id="datatable" class="<?php echo esc_attr(implode( ' ', $classes )) ?>">
			<thead>
			<tr>
				<?php do_action( 'esr_template_registration_table_header', $selected_wave ); ?>
			</tr>
			</thead>
			<tbody class="list">
			<?php
			$enabled_courses            = [];
			$courses_data               = ESR()->course->get_courses_data_by_wave( $selected_wave, true );
			$free_registrations_enabled = intval( ESR()->settings->esr_get_option( 'free_registrations_enabled', - 1 ) ) != - 1;
			$multiple_dates             = [];
			foreach ( $registrations as $registration_id => $registration ) {
				if ( ! isset( $enabled_courses[ $registration->course_id ] ) ) {
					$enabled_courses[ $registration->course_id ] = ESR()->course->is_course_enabled( $registration->course_id );
				}

				$student_name  = isset( $user_data[ $registration->user_id ] ) ? $user_data[ $registration->user_id ]->display_name : esc_html__( 'deleted student', 'easy-school-registration' );
				$student_email = isset( $user_data[ $registration->user_id ] ) ? $user_data[ $registration->user_id ]->user_email : esc_html__( 'deleted student', 'easy-school-registration' );

				$partner_name  = '';
				$partner_email = '';
				if ( $registration->partner_id != null ) {
					$partner_name  = isset( $user_data[ $registration->partner_id ] ) ? $user_data[ $registration->partner_id ]->display_name : esc_html__( 'deleted student', 'easy-school-registration' );
					$partner_email = isset( $user_data[ $registration->partner_id ] ) ? $user_data[ $registration->partner_id ]->user_email : esc_html__( 'deleted student', 'easy-school-registration' );
				}

				$classes = [ "esr-row", "registration-row", "status-" . $registration->status ];

				if ( $free_registrations_enabled ) {
					$classes[] = "free-registration-status-" . $registration->free_registration;
				}

				if ( $multiple_dates_enabled && ! isset( $multiple_dates[ $registration->course_id ] ) ) {
					$multiple_dates[ $registration->course_id ] = ESR()->multiple_dates->esr_get_multiple_dates( $registration->course_id );
				}

				?>
				<tr
						class="<?php echo esc_attr(implode( " ", $classes )); ?>"
					<?php if ( $user_can_edit ) { ?>
						data-id="<?php echo esc_attr($registration->id); ?>"
						data-course-id="<?php echo esc_attr($registration->course_id); ?>"
						data-student-name="<?php echo esc_attr($student_name); ?>"
						data-student-email="<?php echo esc_attr($student_email); ?>"
						data-partner-email="<?php echo esc_attr($partner_email); ?>"
						data-dancing-with="<?php echo esc_attr($registration->dancing_with); ?>"
						data-partner-name="<?php echo esc_attr($partner_name); ?>"
						data-dancing-as="<?php echo esc_attr($registration->dancing_as); ?>"
						<?php do_action( 'esr_template_registration_row_data', $registration ); ?>
					<?php } ?>>
					<?php do_action( 'esr_template_registration_table_content', $selected_wave, $registration, $user_data, $courses_data, $multiple_dates ); ?>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
		<?php $this->print_action_box( $selected_wave );
	}


	private function print_action_box( $wave_id ) {
		$class_data = apply_filters( 'esr_admin_registrations_action_box_class', [ 'wave_id' => $wave_id, 'classes' => [ 'esr-actions-box', 'esr-registrations-actions', 'dropdown-menu' ] ] );
		?>
		<ul class="<?php echo esc_attr(implode( ' ', $class_data['classes'] )) ?>">
			<li class="esr-action edit">
				<a href="javascript:;">
					<span class="dashicons dashicons-edit"></span>
					<span><?php esc_html_e( 'Edit', 'easy-school-registration' ); ?></span>
				</a>
			</li>
			<?php if ( intval( ESR()->settings->esr_get_option( 'free_registrations_enabled', - 1 ) ) != - 1 ) { ?>
				<li class="esr-action update-free-registration">
					<a href="javascript:;">
						<span class="dashicons dashicons-tickets-alt"></span>
						<span class="esr-show-status-0"><?php esc_html_e( 'Paid Registration', 'easy-school-registration' ); ?></span>
						<span class="esr-show-status-1"><?php esc_html_e( 'Free Registration', 'easy-school-registration' ); ?></span>
					</a>
				</li>
			<?php } ?>
			<li class="esr-action confirm">
				<a href="javascript:;">
					<span class="dashicons dashicons-yes"></span>
					<span><?php esc_html_e( 'Confirm', 'easy-school-registration' ); ?></span>
				</a>
			</li>
			<li class="esr-action remove">
				<a href="javascript:;">
					<span class="dashicons dashicons-no-alt"></span>
					<span><?php esc_html_e( 'Cancel', 'easy-school-registration' ); ?></span>
				</a>
			</li>
			<li class="esr-action remove-forever">
				<a href="javascript:;">
					<span class="dashicons dashicons-trash"></span>
					<span><?php esc_html_e( 'Remove Forever', 'easy-school-registration' ); ?></span>
				</a>
			</li>
			<?php do_action( 'esr_admin_registrations_action_box', $wave_id ) ?>
		</ul>
		<?php
	}

}
