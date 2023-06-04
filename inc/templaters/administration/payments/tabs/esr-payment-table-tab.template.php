<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Tab_Template_Payment_Table {

	public static function esr_print_table( $selected_wave ) {
		$template_payment_form = new ESR_Payment_Form_Subblock_Templater();
		$user_can_edit         = current_user_can( 'esr_payment_edit' );
		$user_show_emails      = current_user_can( 'esr_show_student_emails' );
		$show_phone            = intval( ESR()->settings->esr_get_option( 'show_phone_in_payments', - 1 ) ) === 1;

		$payments = ESR()->payment->get_payments_by_wave( $selected_wave );

		?>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Payments', 'easy-school-registration' ); ?></h1>
		<?php if ( $user_can_edit ) { ?>
			<a href="#" class="esr-add-new page-title-action"><?php esc_html_e( 'Add New Payment', 'easy-school-registration' ); ?></a>
		<?php }

		do_action( 'esr_all_waves_select_print', $selected_wave );
		do_action( 'esr_print_payment_statistics', $selected_wave );

		if ( $user_can_edit ) {
			$template_payment_form->print_form();
		}

		$classes = [ 'wp-list-table widefat fixed striped esr-datatable esr-payments-table' ];

		if ( $user_can_edit ) {
			$classes[] = 'esr-copy-table esr-excel-export';
		}
		if ( $user_show_emails ) {
			$classes[] = 'esr-email-export';
		}

		?>


		<table id="datatable" class="<?php echo esc_attr(implode( ' ', $classes )) ?>">
			<colgroup>
				<col width="150">
				<?php if ( $user_can_edit ) { ?>
					<col width="100">
				<?php } ?>
			</colgroup>
			<thead>
			<tr>
                <?php do_action('esr_payment_table_header'); ?>
				<?php
                    $other_columns = apply_filters( 'esr_payment_table_other_columns_header', [] );
                    foreach ( $other_columns as $key => $column ) {
                        echo '<th class="' . ( isset( $column['classes'] ) ? esc_attr($column['classes']) : '' ) . '">' . esc_html($column['title']) . '</th>';
                    }
				?>
			</tr>
			</thead>
			<tbody class="list">
			<?php
			foreach ( $payments

			as $k => $user_payment ) {
			$user_data   = get_userdata( $user_payment['user_id'] );
			$user_email  = $user_data ? $user_data->user_email : esc_html__( 'deleted student', 'easy-school-registration' );
			$paid_status = ESR()->payment_status->get_status( $user_payment );
			?>
			<tr class="esr-row esr-payment-row <?php echo 'paid-status-' . esc_attr($paid_status); ?>"
				<?php if ( $user_can_edit ) { ?>
					data-id="<?php echo esc_attr($user_payment['id']); ?>"
					data-email="<?php echo esc_attr($user_email); ?>"
					data-to_pay="<?php echo esc_attr($user_payment['to_pay']); ?>"
					data-payment="<?php echo esc_attr($user_payment['payment']); ?>"
					data-wave_id="<?php echo esc_attr($user_payment['wave_id']) ?>"
					data-note="<?php echo esc_attr(htmlspecialchars( $user_payment['note'] )) ?>"
				<?php } ?>
			>
                <?php do_action('esr_payment_table_column_content', $user_payment, $user_data, $selected_wave); ?>
				<?php
				$actions = apply_filters( 'esr_payment_table_other_columns_body_calls', [] );
				foreach ( $actions as $key => $action ) {
					do_action( $action['action'], $user_payment );
				}
				?>
				<?php } ?>
			</tbody>
		</table>
		<?php
	}


	private static function print_action_box( $user_payment )
    {
    }

}

add_action( 'esr_print_payment_table_tab', [ 'ESR_Tab_Template_Payment_Table', 'esr_print_table' ] );