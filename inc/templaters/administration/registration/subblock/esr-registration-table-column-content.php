<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Registration_Table_Column_Content {

    public static function esr_print_timestamp_content_callback ($wave, $registration) {
        ?>
            <td class="registration-date"><?php echo esc_html($registration->time); ?></td>
        <?php
    }

    public static function esr_print_action_content_callback ($wave, $registration) {
        if ( current_user_can( 'esr_registration_edit' ) ) { ?>
            <td class="actions no-sort esr-registration esr-hide-print">
                <div class="esr-relative">
                    <button class="page-title-action"><?php esc_html_e( 'Actions', 'easy-school-registration' ) ?></button>
                </div>
            </td>
        <?php }
    }

    public static function esr_print_note_content_callback ($wave, $registration) {
        ?>
        <td class="esr-note">
            <?php if ( ( $registration->note !== null ) && ( $registration->note !== "" ) ) { ?>
                <span class="dashicons dashicons-admin-comments esr-show-note" title="<?php echo esc_html(htmlspecialchars( $registration->note, ENT_QUOTES, 'UTF-8' )); ?>"></span>
                <span class="dashicons dashicons-welcome-comments esr-hide-note"></span>
                <span class="esr-note-message"><?php echo esc_html(htmlspecialchars( $registration->note, ENT_QUOTES, 'UTF-8' )); ?></span>
            <?php } ?>
        </td>
        <?php
    }

    public static function esr_print_user_note_content_callback ($wave, $registration, $user_data) {
        $show_user_note_enabled = intval( ESR()->settings->esr_get_option( 'show_user_note_enabled', - 1 ) ) !== - 1;
        $student_note  = isset( $user_data[ $registration->user_id ] ) && isset( $user_data[ $registration->user_id ]->user_note ) ? $user_data[ $registration->user_id ]->user_note : '';

        if ( $show_user_note_enabled ) { ?>
            <td class="esr-user-note">
                <?php if ( ! empty( $student_note ) ) { ?>
                    <span class="dashicons dashicons-admin-comments esr-show-note" title="<?php echo esc_html(htmlspecialchars( $student_note, ENT_QUOTES, 'UTF-8' )); ?>"></span>
                    <span class="dashicons dashicons-welcome-comments esr-hide-note"></span>
                    <span class="esr-note-message"><?php echo esc_html(htmlspecialchars( $student_note, ENT_QUOTES, 'UTF-8' )); ?></span>
                <?php } ?>
            </td>
        <?php }
    }

    public static function esr_print_status_content_callback ($wave, $registration) {
        ?>
        <td class="status"><?php echo esc_html(ESR()->registration_status->get_title( $registration->status )); ?></td>
        <?php
    }

    public static function esr_print_student_name_content_callback ($wave, $registration, $user_data) {
        $student_name  = isset( $user_data[ $registration->user_id ] ) ? $user_data[ $registration->user_id ]->display_name : esc_html__( 'deleted student', 'easy-school-registration' );
        $separate_name_enabled = intval( ESR()->settings->esr_get_option( 'show_separated_names_enabled', -1 ) ) != -1;

        if ($separate_name_enabled) {
            $first_name  = isset( $user_data[ $registration->user_id ] ) ? $user_data[ $registration->user_id ]->first_name : esc_html__( 'deleted student', 'easy-school-registration' );
            $last_name  = isset( $user_data[ $registration->user_id ] ) ? $user_data[ $registration->user_id ]->last_name : esc_html__( 'deleted student', 'easy-school-registration' );

        ?>
            <td class="student-name"><?php echo esc_html($first_name); ?></td>
            <td class="student-name"><?php echo esc_html($last_name); ?></td>
        <?php
        } else {
        ?>
            <td class="student-name"><?php echo esc_html($student_name); ?></td>
        <?php
        }

        ?>
        <?php
    }

    public static function esr_print_email_content_callback ($wave, $registration, $user_data) {
        $student_email = isset( $user_data[ $registration->user_id ] ) ? $user_data[ $registration->user_id ]->user_email : esc_html__( 'deleted student', 'easy-school-registration' );
       
        if ( current_user_can( 'esr_show_student_emails' ) ) { ?>
            <td class="student-email"><?php echo esc_html($student_email); ?></td>
        <?php }
    }

    public static function esr_print_phone_content_callback ($wave, $registration, $user_data) {
        $show_phone = intval( ESR()->settings->esr_get_option( 'show_phone', - 1 ) ) === 1;
        $student_phone = isset( $user_data[ $registration->user_id ] ) && isset( $user_data[ $registration->user_id ]->phone ) ? $user_data[ $registration->user_id ]->phone : '';
        

        if ( $show_phone ) { ?>
        <td class="student-phone"><?php echo esc_html($student_phone); ?></td>
        <?php }
    }

    public static function esr_print_course_content_callback ($wave, $registration, $user_data, $courses_data) {
        $course_meta = $courses_data[ $registration->course_id ];
        ?>
        	<td class="course"><?php echo esc_html($registration->course_id . ' - ' . stripslashes( $course_meta->title ) . ' (' . ESR()->day->get_day_title( $course_meta->day ) . ')'); ?></td>
        <?php
    }

    public static function esr_print_day_content_callback ($selected_wave, $registration, $user_data, $courses_data, $multiple_dates) {
        $has_multiple_days = isset( $multiple_dates[ $registration->course_id ] ) && ( count( $multiple_dates[ $registration->course_id ] ) > 1 );
        $course_meta = $courses_data[ $registration->course_id ];
        ?>
            <td>
                <?php
                    if ( $has_multiple_days ) {
                        $days = [];
                        foreach ( $multiple_dates[ $registration->course_id ] as $dk => $dv ) {
                            $days[] = ESR()->day->get_day_title( $dv->day );
                        }
                        echo wp_kses_post(implode( '<br>', $days ));
                    } else {
                        echo esc_html(ESR()->day->get_day_title( $course_meta->day ));
                    }
                ?>
            </td>
        <?php
    }

    public static function esr_print_time_content_callback ($selected_wave, $registration, $user_data, $courses_data, $multiple_dates) {
        $has_multiple_days = isset( $multiple_dates[ $registration->course_id ] ) && ( count( $multiple_dates[ $registration->course_id ] ) > 1 );
        $course_meta = $courses_data[ $registration->course_id ];
       ?>
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
        <?php
    }

    public static function esr_print_dance_role_content_callback ($wave, $registration) {
        ?>
            <td class="dancing-as"
                data-as="<?php echo esc_attr($registration->dancing_as); ?>">
                <?php echo esc_html(ESR()->dance_as->get_title( $registration->dancing_as )); ?>
            </td>
        <?php
    }

    public static function esr_print_registered_partner_content_callback ($wave, $registration) {
        if ( current_user_can( 'esr_show_student_emails' ) ) { ?>
            <td class="dancing-with"><?php echo esc_html($registration->dancing_with); ?></td>
        <?php }
    }

    public static function esr_print_paired_partner_content_callback ($wave, $registration, $user_data) {
        $partner_name  = '';
        $partner_email = '';
        if ( $registration->partner_id != null ) {
            $partner_name  = isset( $user_data[ $registration->partner_id ] ) ? $user_data[ $registration->partner_id ]->display_name : esc_html__( 'deleted student', 'easy-school-registration' );
            $partner_email = isset( $user_data[ $registration->partner_id ] ) ? $user_data[ $registration->partner_id ]->user_email : esc_html__( 'deleted student', 'easy-school-registration' );
        }

        ?>
            <td 
                class="partner-name"
                data-partner="<?php echo esc_attr($partner_name); ?>"
                data-email="<?php echo( current_user_can( 'esr_show_student_emails' ) ? esc_attr($partner_email) : '' ); ?>">
                    <?php echo esc_html($partner_name); ?>
            </td>
        <?php
    }

    public static function esr_print_free_registration_content_callback ($wave, $registration) {
        $free_registration_enabled = intval( ESR()->settings->esr_get_option( 'free_registrations_enabled', - 1 ) ) != - 1;
        
        if ( $free_registration_enabled ) { ?>
            <td class="free-registration"><?php echo esc_html(ESR()->enum_free_registration->get_title( $registration->free_registration )); ?></td>
        <?php }
    }

    public static function esr_print_payment_status_content_callback ($wave, $registration) {
        $show_payment_enabled   = intval( ESR()->settings->esr_get_option( 'show_payment_enabled', - 1 ) ) !== - 1;

        if ( $show_payment_enabled ) { ?>
			<td class="payment-status"><?php echo esc_html(ESR()->payment_status->get_title( $registration->payment_status )); ?></td>
        <?php }
    }

}

add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_timestamp_content_callback'], 10, 2);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_action_content_callback'], 20, 2);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_note_content_callback'], 30, 2);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_user_note_content_callback'], 40, 3);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_status_content_callback'], 50, 2);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_student_name_content_callback'], 60, 3);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_email_content_callback'], 70, 3);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_phone_content_callback'], 80, 3);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_course_content_callback'], 90, 4);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_day_content_callback'], 100, 5);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_time_content_callback'], 110, 5);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_dance_role_content_callback'], 120, 2);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_registered_partner_content_callback'], 130, 2);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_paired_partner_content_callback'], 140, 3);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_free_registration_content_callback'], 150, 2);
add_action('esr_template_registration_table_content', ['ESR_Registration_Table_Column_Content', 'esr_print_payment_status_content_callback'], 160, 2);