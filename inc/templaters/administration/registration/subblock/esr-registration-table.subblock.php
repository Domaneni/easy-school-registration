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

		$users_data = get_users( [ 'fields' => [ 'ID', 'display_name', 'user_email' ] ] );
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
				<th class="esr-filter-disabled" data-key="esr_reg_date"><?php esc_html_e( 'Timestamp', 'easy-school-registration' ); ?></th>
				<?php if ( $user_can_edit ) { ?>
					<th id="esr_reg_actions" class="esr-hide-print esr-filter-disabled no-sort"
					    data-key="esr_reg_actions" data-visibility="1"><?php esc_html_e( 'Actions', 'easy-school-registration' ); ?></th>
				<?php } ?>
				<th class="esr-filter-disabled esr-header-note esr-note"><?php esc_html_e( 'Note', 'easy-school-registration' ); ?>
					<span class="dashicons dashicons-admin-comments esr-show-all-notes" data-class="esr-note"></span>
					<span class="dashicons dashicons-welcome-comments esr-hide-all-notes" data-class="esr-note"></span>
				</th>
				<?php if ( $show_user_note_enabled ) { ?>
					<th class="esr-filter-disabled esr-header-note esr-user-note"><?php esc_html_e( 'User Note', 'easy-school-registration' ); ?>
						<span class="dashicons dashicons-admin-comments esr-show-all-notes" data-class="esr-user-note"></span>
						<span class="dashicons dashicons-welcome-comments esr-hide-all-notes" data-class="esr-user-note"></span>
					</th>
				<?php } ?>
				<th class="no-sort"><?php esc_html_e( 'Status', 'easy-school-registration' ); ?></th>
				<th class="esr-filter-disabled"><?php esc_html_e( 'Student Name', 'easy-school-registration' ); ?></th>
				<?php if ( $user_show_emails ) { ?>
					<th class="esr-filter-disabled esr-student-email"><?php esc_html_e( 'Email', 'easy-school-registration' ); ?></th>
				<?php } ?>
				<?php if ( $show_phone ) { ?>
					<th class="esr-filter-disabled esr-student-phone"><?php esc_html_e( 'Phone', 'easy-school-registration' ); ?></th>
				<?php } ?>
				<th class="no-sort course"><?php esc_html_e( 'Course', 'easy-school-registration' ); ?></th>
				<th class="no-sort esr-multiple-filters"><?php esc_html_e( 'Day', 'easy-school-registration' ); ?></th>
				<th class="no-sort esr-multiple-filters"><?php esc_html_e( 'Time', 'easy-school-registration' ); ?></th>
				<th class="no-sort"><?php esc_html_e( 'Dancing Role', 'easy-school-registration' ); ?></th>
				<?php if ( $user_show_emails ) { ?>
					<th class="esr-filter-disabled"><?php esc_html_e( 'Registered Partner', 'easy-school-registration' ); ?></th>
				<?php } ?>
				<th class="esr-filter-disabled"><?php esc_html_e( 'Paired Partner', 'easy-school-registration' ); ?></th>
				<?php if ( intval( ESR()->settings->esr_get_option( 'free_registrations_enabled', - 1 ) ) != - 1 ) { ?>
					<th class="no-sort"><?php esc_html_e( 'Free Registration', 'easy-school-registration' ); ?></th>
				<?php } ?>
				<?php if ( $show_payment_enabled ) { ?>
					<th class="payment-status"><?php esc_html_e( 'Payment Status', 'easy-school-registration' ); ?></th>
				<?php } ?>
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
				$course_meta = $courses_data[ $registration->course_id ];

				if ( ! isset( $enabled_courses[ $registration->course_id ] ) ) {
					$enabled_courses[ $registration->course_id ] = ESR()->course->is_course_enabled( $registration->course_id );
				}

				$student_name  = isset( $user_data[ $registration->user_id ] ) ? $user_data[ $registration->user_id ]->display_name : esc_html__( 'deleted student', 'easy-school-registration' );
				$student_email = isset( $user_data[ $registration->user_id ] ) ? $user_data[ $registration->user_id ]->user_email : esc_html__( 'deleted student', 'easy-school-registration' );
				$student_phone = isset( $user_data[ $registration->user_id ] ) && isset( $user_data[ $registration->user_id ]->phone ) ? $user_data[ $registration->user_id ]->phone : '';
				$student_note  = isset( $user_data[ $registration->user_id ] ) && isset( $user_data[ $registration->user_id ]->user_note ) ? $user_data[ $registration->user_id ]->user_note : '';

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

				$has_multiple_days = isset( $multiple_dates[ $registration->course_id ] ) && ( count( $multiple_dates[ $registration->course_id ] ) > 1 );

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
					<td class="registration-date"><?php echo esc_html($registration->time); ?></td>
					<?php if ( $user_can_edit ) { ?>
						<td class="actions no-sort esr-registration esr-hide-print">
							<div class="esr-relative">
								<button class="page-title-action"><?php esc_html_e( 'Actions', 'easy-school-registration' ) ?></button>
							</div>
						</td>
					<?php } ?>
					<td class="esr-note">
						<?php if ( ( $registration->note !== null ) && ( $registration->note !== "" ) ) { ?>
							<span class="dashicons dashicons-admin-comments esr-show-note" title="<?php echo esc_html(htmlspecialchars( $registration->note, ENT_QUOTES, 'UTF-8' )); ?>"></span>
							<span class="dashicons dashicons-welcome-comments esr-hide-note"></span>
							<span class="esr-note-message"><?php echo esc_html(htmlspecialchars( $registration->note, ENT_QUOTES, 'UTF-8' )); ?></span>
						<?php } ?>
					</td>
					<?php if ( $show_user_note_enabled ) { ?>
						<td class="esr-user-note">
							<?php if ( ! empty( $student_note ) ) { ?>
								<span class="dashicons dashicons-admin-comments esr-show-note" title="<?php echo esc_html(htmlspecialchars( $student_note, ENT_QUOTES, 'UTF-8' )); ?>"></span>
								<span class="dashicons dashicons-welcome-comments esr-hide-note"></span>
								<span class="esr-note-message"><?php echo esc_html(htmlspecialchars( $student_note, ENT_QUOTES, 'UTF-8' )); ?></span>
							<?php } ?>
						</td>
					<?php } ?>
					<td class="status"><?php echo esc_html(ESR()->registration_status->get_title( $registration->status )); ?></td>
					<td class="student-name"><?php echo esc_html($student_name); ?></td>
					<?php if ( $user_show_emails ) { ?>
						<td class="student-email"><?php echo esc_html($student_email); ?></td>
					<?php } ?>
					<?php if ( $show_phone ) { ?>
						<td class="student-phone"><?php echo esc_html($student_phone); ?></td>
					<?php } ?>
					<td class="course"><?php echo esc_html($registration->course_id . ' - ' . stripslashes( $course_meta->title ) . ' (' . ESR()->day->get_day_title( $course_meta->day ) . ')'); ?></td>
					<td><?php
						if ( $has_multiple_days ) {
							$days = [];
							foreach ( $multiple_dates[ $registration->course_id ] as $dk => $dv ) {
								$days[] = ESR()->day->get_day_title( $dv->day );
							}
							echo wp_kses_post(implode( '<br>', $days ));
						} else {
							echo esc_html(ESR()->day->get_day_title( $course_meta->day ));
						}
						?></td>
					<td><?php
						if ( $has_multiple_days ) {
							$times = [];
							foreach ( $multiple_dates[ $registration->course_id ] as $tk => $tv ) {
								$times[] = $tv->time_from . '/' . $tv->time_to;
							}
							echo wp_kses_post(implode( '<br>', $times ));
						} else {
							echo esc_html($course_meta->time_from . '/' . $course_meta->time_to);
						}
						?></td>
					<td class="dancing-as"
					    data-as="<?php echo esc_attr($registration->dancing_as); ?>"><?php echo esc_html(ESR()->dance_as->get_title( $registration->dancing_as )); ?></td>
					<?php if ( $user_show_emails ) { ?>
						<td class="dancing-with"><?php echo esc_html($registration->dancing_with); ?></td>
					<?php } ?>
					<td class="partner-name" data-partner="<?php echo esc_attr($partner_name); ?>"
					    data-email="<?php echo( $user_show_emails ? esc_attr($partner_email) : '' ); ?>"><?php echo esc_html($partner_name); ?></td>
					<?php if ( intval( ESR()->settings->esr_get_option( 'free_registrations_enabled', - 1 ) ) === 1 ) { ?>
						<td class="free-registration"><?php echo esc_html(ESR()->enum_free_registration->get_title( $registration->free_registration )); ?></td>
					<?php } ?>
					<?php if ( $show_payment_enabled ) { ?>
						<td class="payment-status"><?php echo esc_html(ESR()->payment_status->get_title( $registration->payment_status )); ?></td>
					<?php } ?>
					<?php do_action( 'esr_template_registration_table_content', $selected_wave, $registration ); ?>
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
