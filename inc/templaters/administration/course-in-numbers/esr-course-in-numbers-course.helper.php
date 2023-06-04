<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Courses_In_Numbers_Course_Helper {

	public function print_course_html( $course, $for_registration, $position = 'width' ) {
		$course_meta                 = ESR()->course_summary->get_course_summary( $course->id );
		$user_can_view_registrations = current_user_can( 'esr_registration_view' );
		if ( $course->is_solo ) {
			$is_course_enabled = $course->max_solo > $course_meta->registered_solo;
		} else {
			$is_course_enabled = ( $course->max_leaders > $course_meta->registered_leaders ) || ( $course->max_followers > $course_meta->registered_followers );
		} ?>
		<?php if ( $user_can_view_registrations ) { ?>
			<a href="admin.php?page=esr_admin_sub_page_registrations&cin_course_id=<?php echo esc_attr($course->id) ?>&cin_wave_id=<?php echo esc_attr($course->wave_id) ?>" class="esr-course <?php echo( $is_course_enabled ? "esr-add" : "esr-full" ); ?>"
			style="<?php echo esc_attr($position . ':' . $this->get_time_width( $course->time_from, $course->time_to ) . 'px;'); ?>">
		<?php } else { ?>
			<div class="esr-course <?php echo( $is_course_enabled ? "esr-add" : "esr-full" ); ?>" style="<?php echo esc_attr($position . ':' . $this->get_time_width( $course->time_from, $course->time_to ) . 'px;'); ?>">
		<?php } ?>
		<span class="esr-title"><?php echo esc_html(stripslashes( $course->title )); ?></span>
		<div class="esr-position-bottom">
			<?php if ( $course->is_solo ) { ?>
				<div class="esr-counts"><span class="esr-count"><?php esc_html_e( 'Solo', 'easy-school-registration' ); ?> <span class="esr-counts-number"><?php echo esc_html($course_meta->registered_solo . '/' . $course->max_solo . ( ( ESR()->pairing_mode->is_solo_manual( $course->pairing_mode ) ) ? ' (' . $course_meta->waiting_solo . ')' : '' )); ?></span></span></div>
			<?php } else { ?>
				<div class="esr-counts">
					<span class="esr-count"><?php esc_html_e( 'Leaders', 'easy-school-registration' ); ?> <span class="esr-counts-number"><?php echo esc_html($course_meta->registered_leaders . '/' . $course->max_leaders . ' (' . $course_meta->waiting_leaders . ')'); ?></span></span>
					<span class="esr-count"><?php esc_html_e( 'Followers', 'easy-school-registration' ); ?> <span class="esr-counts-number"><?php echo esc_html($course_meta->registered_followers . '/' . $course->max_followers . ' (' . $course_meta->waiting_followers . ')'); ?></span></span>
				</div>
			<?php } ?>
			<span class="esr-teachers"><?php echo esc_html(ESR()->teacher->get_teachers_names( $course->teacher_first, $course->teacher_second )); ?></span>
		</div>
		<?php if ( $user_can_view_registrations ) { ?>
			</a>
		<?php } else { ?>
			</div>
		<?php }
	}


	public function print_mobile_course_html( $course ) {
		$course_meta = ESR()->course_summary->get_course_summary( $course->id );
		?>
		<li class="esr-course">
			<span class="esr-title"><?php echo esc_html(stripslashes( $course->title )); ?></span>
			<span class="esr-count">
			<?php if ( $course->is_solo ) { ?>
				<span class="esr-counts"><span class="esr-count"><?php esc_html_e( 'Solo', 'easy-school-registration' ); ?> <span class="esr-counts-number"><?php echo esc_html($course_meta->registered_solo . '/' . $course->max_solo); ?></span></span></span>
			<?php } else { ?>
				<span class="esr-counts">
						<span class="esr-count"><?php esc_html_e( 'Leaders', 'easy-school-registration' ); ?> <span class="esr-counts-number"><?php echo esc_html($course_meta->registered_leaders . '/' . $course->max_leaders . ' (' . $course_meta->waiting_leaders . ')'); ?></span></span>
						<span class="esr-count"><?php esc_html_e( 'Followers', 'easy-school-registration' ); ?> <span class="esr-counts-number"><?php echo esc_html($course_meta->registered_followers . '/' . $course->max_followers . ' (' . $course_meta->waiting_followers . ')'); ?></span></span>
					</span>
			<?php } ?>
			</span>
		</li>
		<?php
	}


	public function get_time_width( $start_time, $end_time, $subtract = 0 ) {
		$to_time   = strtotime( $start_time );
		$from_time = strtotime( $end_time );

		return ( round( abs( $to_time - $from_time ) / 60, 2 ) * 2 ) - $subtract;
	}

}
