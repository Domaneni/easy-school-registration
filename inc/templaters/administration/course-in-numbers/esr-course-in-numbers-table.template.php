<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Template_Courses_In_Numbers_Table {

	public static function esr_print_table( $wave_id ) {
		$user_hide_passed_courses = filter_var(get_user_meta(get_current_user_id(), 'esr_hide_passed_courses', true), FILTER_VALIDATE_BOOLEAN);

		if (($user_hide_passed_courses !== '') && $user_hide_passed_courses) {
			$summaries = ESR()->course_summary->get_active_course_summary_by_wave( $wave_id );
		} else {
			$summaries = ESR()->course_summary->get_course_summary_by_wave( $wave_id );
		}
		$user_can_view_registrations = current_user_can( 'esr_registration_view' );

		if ( $summaries !== null ) { ?>
			<label id="esr_hide_passed_courses" class="esr_checkbox_input"><input id="esr_hide_passed_courses" type="checkbox" name="esr_hide_passed_courses" <?php checked(true, $user_hide_passed_courses) ?>><?php esc_html_e('Hide passed courses', 'easy-school-registration'); ?></label>
			<table id="datatable" class="wp-list-table widefat fixed striped esr-datatable esr-course-in-numbers-datatable esr-copy-table esr-excel-export">
			<colgroup>
				<col width="300">
				<col width="150">
			</colgroup>
			<thead>
			<tr>
				<th class="esr-filter-disabled no-sort"><?php esc_html_e( 'Course', 'easy-school-registration' ); ?></th>
				<th class="no-sort"><?php esc_html_e( 'Status', 'easy-school-registration' ); ?></th>
				<th class="no-sort"><?php esc_html_e( 'Day', 'easy-school-registration' ); ?></th>
				<th><?php esc_html_e( 'Time', 'easy-school-registration' ); ?></th>
				<th class="esr-filter-disabled no-sort"><?php esc_html_e( 'Solo', 'easy-school-registration' ); ?></th>
				<th class="esr-filter-disabled no-sort"><?php esc_html_e( 'Leaders', 'easy-school-registration' ); ?></th>
				<th class="esr-filter-disabled no-sort"><?php esc_html_e( 'Followers', 'easy-school-registration' ); ?></th>
				<th class="no-sort"><?php esc_html_e( 'Level', 'easy-school-registration' ); ?></th>
				<th class="no-sort"><?php esc_html_e( 'Group', 'easy-school-registration' ); ?></th>
				<th class="no-sort"><?php esc_html_e( 'Hall', 'easy-school-registration' ); ?></th>
				<th class="no-sort"><?php esc_html_e( 'First Teacher', 'easy-school-registration' ); ?></th>
				<th class="no-sort"><?php esc_html_e( 'Second Teacher', 'easy-school-registration' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $summaries as $key => $summary ) {
				$hall    = ESR()->hall->get_hall( $summary->hall_key );
				$is_full = ESR()->course_summary->esr_is_course_full( $summary->summary_course_id );
				$classes = [];
				if ( $is_full ) {
					$classes = [ 'esr-full' ];
				}
				?>
				<tr class="<?php echo esc_attr(implode( ' ', $classes) ) ?>">
					<td>
						<?php if ( $user_can_view_registrations ) { ?>
							<a target="_blank" href="admin.php?page=esr_admin_sub_page_registrations&cin_course_id=<?php echo esc_attr(intval($summary->summary_course_id)) ?>&cin_wave_id=<?php echo esc_attr(intval($summary->wave_id)) ?>">
								<?php echo esc_html($summary->title) ?>
							</a>
						<?php } else {
							echo esc_html($summary->title);
						} ?>
					</td>
					<td><?php echo ($is_full ? esc_html__( 'Course Full', 'easy-school-registration' ) : esc_html__( 'Spots Available', 'easy-school-registration' )) ?></td>
					<td><?php echo esc_html(ESR()->day->get_day_title( $summary->day )) ?></td>
					<td><?php echo esc_html($summary->time_from . '/' . $summary->time_to )?></td>
					<?php if ( $summary->is_solo ) { ?>
						<td><?php echo esc_html($summary->registered_solo . '/' . $summary->max_solo . ( ( ESR()->pairing_mode->is_solo_manual( $summary->pairing_mode ) ) ? ' (' . $summary->waiting_solo . ')' : '' )); ?></td>
						<td></td>
						<td></td>
					<?php } else { ?>
						<td></td>
						<td><?php echo esc_html($summary->registered_leaders . '/' . $summary->max_leaders . ' (' . $summary->waiting_leaders . ')'); ?></td>
						<td><?php echo esc_html($summary->registered_followers . '/' . $summary->max_followers . ' (' . $summary->waiting_followers . ')'); ?></td>
					<?php } ?>
					<td><?php echo esc_html(ESR()->course_level->get_title( $summary->level_id )); ?></td>
					<td><?php echo esc_html(ESR()->course_group->get_title( $summary->group_id )); ?></td>
					<td><?php echo ($hall !== [] ? esc_html($hall['name']) : '-') ?></td>
					<td><?php echo esc_html(ESR()->teacher->get_teacher_name( $summary->teacher_first )) ?></td>
					<td><?php echo esc_html(ESR()->teacher->get_teacher_name( $summary->teacher_second )) ?></td>
				</tr>
				<?php
			}
		}
		?></tbody></table><?php
	}
}

add_action( 'esr_print_course_in_numbers_table', [ 'ESR_Template_Courses_In_Numbers_Table', 'esr_print_table' ] );
