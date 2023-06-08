<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Registration_Table_Header {

    public static function esr_print_timestamp_header_callback ($wave) {
        ?><th class="esr-filter-disabled" data-key="esr_reg_date"><?php esc_html_e( 'Timestamp', 'easy-school-registration' ); ?></th><?php
    }

    public static function esr_print_action_header_callback ($wave) {
        if ( current_user_can( 'esr_registration_edit' ) ) { ?>
            <th 
                id="esr_reg_actions"
                class="esr-hide-print esr-filter-disabled no-sort"
                data-key="esr_reg_actions"
                data-visibility="1"><?php esc_html_e( 'Actions', 'easy-school-registration' ); ?></th>
        <?php }
    }

    public static function esr_print_note_header_callback ($wave) {
        ?>
            <th class="esr-filter-disabled esr-header-note esr-note"><?php esc_html_e( 'Note', 'easy-school-registration' ); ?>
                <span class="dashicons dashicons-admin-comments esr-show-all-notes" data-class="esr-note"></span>
                <span class="dashicons dashicons-welcome-comments esr-hide-all-notes" data-class="esr-note"></span>
            </th>
        <?php
    }

    public static function esr_print_user_note_header_callback ($wave) {
        $show_user_note_enabled = intval( ESR()->settings->esr_get_option( 'show_user_note_enabled', - 1 ) ) !== - 1;

        if ( $show_user_note_enabled ) { ?>
            <th class="esr-filter-disabled esr-header-note esr-user-note"><?php esc_html_e( 'User Note', 'easy-school-registration' ); ?>
                <span class="dashicons dashicons-admin-comments esr-show-all-notes" data-class="esr-user-note"></span>
                <span class="dashicons dashicons-welcome-comments esr-hide-all-notes" data-class="esr-user-note"></span>
            </th>
        <?php }
    }

    public static function esr_print_status_header_callback ($wave) {
        ?>
            <th class="no-sort"><?php esc_html_e( 'Status', 'easy-school-registration' ); ?></th>
        <?php
    }

    public static function esr_print_student_name_header_callback ($wave) {
        ?>
            <th class="esr-filter-disabled"><?php esc_html_e( 'Student Name', 'easy-school-registration' ); ?></th>
        <?php
    }

    public static function esr_print_email_header_callback ($wave) {
        if ( current_user_can( 'esr_show_student_emails' ) ) { ?>
            <th class="esr-filter-disabled esr-student-email"><?php esc_html_e( 'Email', 'easy-school-registration' ); ?></th>
        <?php }
    }

    public static function esr_print_phone_header_callback ($wave) {
        $show_phone = intval( ESR()->settings->esr_get_option( 'show_phone', - 1 ) ) === 1;

        if ( $show_phone ) { ?>
            <th class="esr-filter-disabled esr-student-phone"><?php esc_html_e( 'Phone', 'easy-school-registration' ); ?></th>
        <?php }
    }

    public static function esr_print_course_header_callback ($wave) {
        ?><th class="no-sort course"><?php esc_html_e( 'Course', 'easy-school-registration' ); ?></th><?php
    }

    public static function esr_print_day_header_callback ($wave) {
        ?><th class="no-sort esr-multiple-filters"><?php esc_html_e( 'Day', 'easy-school-registration' ); ?></th><?php
    }

    public static function esr_print_time_header_callback ($wave) {
        ?><th class="no-sort esr-multiple-filters"><?php esc_html_e( 'Time', 'easy-school-registration' ); ?></th><?php
    }

    public static function esr_print_dance_role_header_callback ($wave) {
        ?><th class="no-sort"><?php esc_html_e( 'Dancing Role', 'easy-school-registration' ); ?></th><?php
    }

    public static function esr_print_registered_partner_header_callback ($wave) {
        if ( current_user_can( 'esr_show_student_emails' ) ) { ?>
            <th class="esr-filter-disabled"><?php esc_html_e( 'Registered Partner', 'easy-school-registration' ); ?></th>
        <?php }
    }

    public static function esr_print_paired_partner_header_callback ($wave) {
        ?><th class="esr-filter-disabled"><?php esc_html_e( 'Paired Partner', 'easy-school-registration' ); ?></th><?php
    }

    public static function esr_print_free_registration_header_callback ($wave) {
        $free_registration_enabled = intval( ESR()->settings->esr_get_option( 'free_registrations_enabled', - 1 ) ) != - 1;
        
        if ( $free_registration_enabled ) { ?>
            <th class="no-sort"><?php esc_html_e( 'Free Registration', 'easy-school-registration' ); ?></th>
        <?php }
    }

    public static function esr_print_payment_status_header_callback ($wave) {
        $show_payment_enabled   = intval( ESR()->settings->esr_get_option( 'show_payment_enabled', - 1 ) ) !== - 1;

        if ( $show_payment_enabled ) { ?>
            <th class="payment-status"><?php esc_html_e( 'Payment Status', 'easy-school-registration' ); ?></th>
        <?php }
    }

}

add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_timestamp_header_callback'], 10);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_action_header_callback'], 20);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_note_header_callback'], 30);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_user_note_header_callback'], 40);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_status_header_callback'], 50);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_student_name_header_callback'], 60);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_email_header_callback'], 70);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_phone_header_callback'], 80);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_course_header_callback'], 90);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_day_header_callback'], 90);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_time_header_callback'], 90);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_dance_role_header_callback'], 90);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_registered_partner_header_callback'], 90);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_paired_partner_header_callback'], 90);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_free_registration_header_callback'], 90);
add_action('esr_template_registration_table_header', ['ESR_Registration_Table_Header', 'esr_print_payment_status_header_callback'], 90);