<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Payment_Action_Box {

    public static function esr_print_action_box_callback ($payment) {
        ?>
        <ul class="esr-actions-box dropdown-menu" data-id="<?php echo esc_attr($payment['id']); ?>">
            <?php do_action('esr_print_action_box_item', $payment); ?>
        </ul>
        <?php
    }

    public static function esr_print_action_box_edit_item_callback()
    {
        ?>
            <li class="esr-action edit">
            <a href="javascript:;">
                <span class="dashicons dashicons-edit"></span>
                <span><?php esc_html_e( 'Edit', 'easy-school-registration' ); ?></span>
            </a>
            </li>
        <?php
    }

    public static function esr_print_action_box_confirm_item_callback()
    {
        ?>
            <li class="esr-action confirm-payment">
                <a href="javascript:;">
                    <span class="dashicons dashicons-yes"></span>
                    <span><?php esc_html_e( 'Confirm Payment', 'easy-school-registration' ); ?></span>
                </a>
            </li>
        <?php
    }

    public static function esr_print_action_box_forgive_item_callback()
    {
        if ( intval( ESR()->settings->esr_get_option( 'debts_enabled', - 1 ) ) === 1 ) { ?>
            <li class="esr-action forgive-payment">
                <a href="javascript:;">
                    <span class="dashicons dashicons-thumbs-up"></span>
                    <span><?php esc_html_e( 'Forgive Payment', 'easy-school-registration' ); ?></span>
                </a>
            </li>
        <?php }
    }

}

add_action('esr_print_action_box', ['ESR_Payment_Action_Box', 'esr_print_action_box_callback'], 10, 1);
add_action('esr_print_action_box_item', ['ESR_Payment_Action_Box', 'esr_print_action_box_edit_item_callback'], 10, 1);
add_action('esr_print_action_box_item', ['ESR_Payment_Action_Box', 'esr_print_action_box_confirm_item_callback'], 20, 1);
add_action('esr_print_action_box_item', ['ESR_Payment_Action_Box', 'esr_print_action_box_forgive_item_callback'], 30, 1);
