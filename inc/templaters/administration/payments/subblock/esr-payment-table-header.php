<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Payment_Table_Header {

    public static function esr_print_status_header_callback () {
        ?><th class="no-sort"><?php esc_html_e( 'Status', 'easy-school-registration' ) ?></th><?php
    }

    public static function esr_print_actions_header_callback () {
        if ( current_user_can( 'esr_payment_edit' ) ) {
            ?><th class="esr-filter-disabled skip-filter no-sort esr-hide-print"><?php esc_html_e( 'Actions', 'easy-school-registration' ) ?></th><?php
        }
    }

    public static function esr_print_payment_method_header_callback () {
        ?><th class="no-sort"><?php esc_html_e( 'Payment Method', 'easy-school-registration' ) ?></th><?php
    }

    public static function esr_print_note_header_callback () {
        ?><th class="esr-filter-disabled skip-filter"><?php esc_html_e( 'Note', 'easy-school-registration' ) ?></th><?php
    }

    public static function esr_print_student_name_header_callback () {
        $separate_name_enabled = intval( ESR()->settings->esr_get_option( 'show_separated_names_enabled', -1 ) ) != -1;

        if ($separate_name_enabled) {
        ?>
            <th class="esr-filter-disabled skip-filter"><?php esc_html_e( 'Student Name', 'easy-school-registration' ); ?></th>
            <th class="esr-filter-disabled skip-filter"><?php esc_html_e( 'Student Surname', 'easy-school-registration' ); ?></th>
        <?php
        } else {
        ?>
            <th class="esr-filter-disabled skip-filter"><?php esc_html_e( 'Student Name', 'easy-school-registration' ); ?></th>
        <?php
        }
    }

    public static function esr_print_student_email_header_callback () {
        if ( current_user_can( 'esr_show_student_emails' ) ) {
            ?><th class="esr-filter-disabled skip-filter esr-student-email"><?php esc_html_e( 'Student Email', 'easy-school-registration' ) ?></th><?php
        }
    }

    public static function esr_print_phone_header_callback () {
        if ( intval( ESR()->settings->esr_get_option( 'show_phone_in_payments', -1 ) ) === 1 ) {
            ?><th class="esr-filter-disabled esr-student-phone"><?php esc_html_e( 'Phone', 'easy-school-registration' ); ?></th><?php
        }
    }

    public static function esr_print_variable_symbol_header_callback () {
        ?><th class="esr-filter-disabled skip-filter"><?php esc_html_e( 'Variable Symbol', 'easy-school-registration' ) ?></th><?php
    }

    public static function esr_print_courses_header_callback () {
        if ( intval( ESR()->settings->esr_get_option( 'show_courses', -1 ) ) === 1 ) {
            ?><th class="esr-multiple-filters"><?php esc_html_e( 'Courses', 'easy-school-registration' ) ?></th><?php
        }
    }

    public static function esr_print_to_pay_header_callback () {
        ?><th class="skip-filter"><?php esc_html_e( 'To pay', 'easy-school-registration' ) ?></th><?php
    }

    public static function esr_print_paid_header_callback () {
        ?><th class="skip-filter"><?php esc_html_e( 'Paid', 'easy-school-registration' ) ?></th><?php
    }

}

add_action('esr_payment_table_header', ['ESR_Payment_Table_Header', 'esr_print_status_header_callback'], 10);
add_action('esr_payment_table_header', ['ESR_Payment_Table_Header', 'esr_print_actions_header_callback'], 20);
add_action('esr_payment_table_header', ['ESR_Payment_Table_Header', 'esr_print_payment_method_header_callback'], 30);
add_action('esr_payment_table_header', ['ESR_Payment_Table_Header', 'esr_print_note_header_callback'], 40);
add_action('esr_payment_table_header', ['ESR_Payment_Table_Header', 'esr_print_student_name_header_callback'], 50);
add_action('esr_payment_table_header', ['ESR_Payment_Table_Header', 'esr_print_student_email_header_callback'], 60);
add_action('esr_payment_table_header', ['ESR_Payment_Table_Header', 'esr_print_phone_header_callback'], 70);
add_action('esr_payment_table_header', ['ESR_Payment_Table_Header', 'esr_print_variable_symbol_header_callback'], 80);
add_action('esr_payment_table_header', ['ESR_Payment_Table_Header', 'esr_print_courses_header_callback'], 90);
add_action('esr_payment_table_header', ['ESR_Payment_Table_Header', 'esr_print_to_pay_header_callback'], 100);
add_action('esr_payment_table_header', ['ESR_Payment_Table_Header', 'esr_print_paid_header_callback'], 110);
