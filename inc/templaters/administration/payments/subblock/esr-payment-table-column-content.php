<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Payment_Table_Column_Content {

    public static function esr_print_status_column_content_callback($payment)
    {
        $paid_status = ESR()->payment_status->get_status( $payment );
        ?><td class="status"><?php echo esc_html(ESR()->payment_status->get_title( $paid_status )); ?></td><?php
    }

    public static function esr_print_actions_column_content_callback($payment, $user_data)
    {
        if (current_user_can( 'esr_payment_edit' ) && $user_data) { ?>
            <td class="actions esr-payment">
                <div class="esr-relative">
                    <button class="page-title-action"><?php esc_html_e( 'Actions', 'easy-school-registration' ) ?></button>
                    <?php do_action('esr_print_action_box', $payment); ?>
                </div>
            </td>
        <?php }
    }

    public static function esr_print_payment_type_column_content_callback($payment)
    {
        ?><td class="payment-type"><?php echo esc_html(ESR()->payment_type->get_title( $payment['payment_type'] )); ?></td><?php
    }

    public static function esr_print_note_column_content_callback($payment)
    {
        ?><td class="esr-note"><?php if ( ( $payment['note'] !== null ) && ( $payment['note'] !== "" ) ) { ?>
        <span class="dashicons dashicons-admin-comments esr-show-note" title="<?php echo esc_html(htmlspecialchars( $payment['note'] )); ?>"></span>
        <span class="dashicons dashicons-welcome-comments  esr-hide-note"></span>
        <span class="esr-note-message"><?php echo esc_html(htmlspecialchars( $payment['note'] )); ?></span><?php } ?></td><?php
    }

    public static function esr_print_student_name_column_content_callback($payment, $user_data)
    {
        $user_name = $user_data ? $user_data->display_name : esc_html__( 'deleted student', 'easy-school-registration' );
        $separate_name_enabled = intval( ESR()->settings->esr_get_option( 'show_separated_names_enabled', -1 ) ) != -1;

        if ($separate_name_enabled) {
            $first_name  = $user_data ? $user_data->first_name : esc_html__( 'deleted student', 'easy-school-registration' );
            $last_name  = $user_data ? $user_data->last_name : esc_html__( 'deleted student', 'easy-school-registration' );

        ?>
            <td class="student-name"><?php echo esc_html($first_name); ?></td>
            <td class="student-name"><?php echo esc_html($last_name); ?></td>
        <?php
        } else {
        ?>
            <td class="student-name"><?php echo esc_html($user_name); ?></td>
        <?php
        }
    }

    public static function esr_print_student_email_column_content_callback($payment, $user_data)
    {
        $user_email = $user_data ? $user_data->user_email : esc_html__( 'deleted student', 'easy-school-registration' );
        if ( current_user_can( 'esr_show_student_emails' ) ) { ?>
            <td class="student-email"><?php echo esc_html($user_email); ?></td>
        <?php }
    }

    public static function esr_print_student_phone_column_content_callback($payment, $user_data)
    {
        $user_phone  = get_user_meta( $user_data->ID, 'esr-course-registration-phone' );
        if ( intval( ESR()->settings->esr_get_option( 'show_phone_in_payments', -1 ) ) === 1 ) { ?>
            <td class="student-phone"><?php echo ! empty( $user_phone ) ? esc_html($user_phone[0]) : ''; ?></td>
        <?php }
    }

    public static function esr_print_variable_symbol_column_content_callback($payment, $user_data, $selected_wave)
    {
        ?><td class="variable-symbol"><?php echo esc_html($selected_wave . sprintf( "%04s", $payment['user_id'] )); ?></td><?php
    }

    public static function esr_print_courses_column_content_callback($payment)
    {
        if ( intval( ESR()->settings->esr_get_option( 'show_courses', - 1 ) ) === 1 ) { ?>
            <td>
                <?php
                $courses = ESR()->registration->get_confirmed_registrations_by_wave_and_user( $payment['wave_id'], $payment['user_id'] );

                $course_titles = [];
                foreach ( $courses as $course_key => $course ) {
                    $course_titles[] = $course->course_id . ' - ' . $course->title;
                }
                if ( ! empty( $courses ) ) {
                    echo wp_kses_post(implode( '<br>', $course_titles ));
                }
                ?>
            </td>
        <?php }
    }

    public static function esr_print_to_pay_column_content_callback($payment)
    {
        ?><td><?php echo esc_html(ESR()->currency->prepare_price( $payment['to_pay'] )); ?></td><?php
    }

    public static function esr_print_paid_column_content_callback($payment)
    {
        $paid_status = ESR()->payment_status->get_status( $payment );
        ?><td class="student-paid"><?php echo( ( $payment && isset( $payment['payment'] ) && ( ! in_array( $paid_status, [ ESR_Enum_Payment::NOT_PAYING, ESR_Enum_Payment::VOUCHER ] ) ) ) ? esc_html(ESR()->currency->prepare_price( $payment['payment'] )) : '' ); ?></td><?php
    }
}

add_action('esr_payment_table_column_content', ['ESR_Payment_Table_Column_Content', 'esr_print_status_column_content_callback'], 10, 3);
add_action('esr_payment_table_column_content', ['ESR_Payment_Table_Column_Content', 'esr_print_actions_column_content_callback'], 20, 3);
add_action('esr_payment_table_column_content', ['ESR_Payment_Table_Column_Content', 'esr_print_payment_type_column_content_callback'], 30, 3);
add_action('esr_payment_table_column_content', ['ESR_Payment_Table_Column_Content', 'esr_print_note_column_content_callback'], 40, 3);
add_action('esr_payment_table_column_content', ['ESR_Payment_Table_Column_Content', 'esr_print_student_name_column_content_callback'], 50, 3);
add_action('esr_payment_table_column_content', ['ESR_Payment_Table_Column_Content', 'esr_print_student_email_column_content_callback'], 60, 3);
add_action('esr_payment_table_column_content', ['ESR_Payment_Table_Column_Content', 'esr_print_student_phone_column_content_callback'], 70, 3);
add_action('esr_payment_table_column_content', ['ESR_Payment_Table_Column_Content', 'esr_print_variable_symbol_column_content_callback'], 80, 3);
add_action('esr_payment_table_column_content', ['ESR_Payment_Table_Column_Content', 'esr_print_courses_column_content_callback'], 90, 3);
add_action('esr_payment_table_column_content', ['ESR_Payment_Table_Column_Content', 'esr_print_to_pay_column_content_callback'], 100, 3);
add_action('esr_payment_table_column_content', ['ESR_Payment_Table_Column_Content', 'esr_print_paid_column_content_callback'], 110, 3);
