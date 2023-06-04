<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Payments_Templater {

	const MENU_SLUG = 'esr_admin_sub_page_payments';


	public static function print_page() {
		$selected_wave = apply_filters('esr_all_waves_select_get', []);

		$tabs = apply_filters('esr_payments_admin_tabs', [
			'payments' => [
				'title'  => esc_html__('Payments', 'easy-school-registration'),
				'action' => 'esr_print_payment_table_tab'
			],
		]);

		if (current_user_can('esr_payment_debts_view')) {
			$tabs['debts']    = [
				'title'  => esc_html__('Student Debts', 'easy-school-registration'),
				'action' => 'esr_print_payment_debts_tab'
			];
		}

		$active_tab = isset($_GET['tab']) && isset($tabs[sanitize_text_field($_GET['tab'])]) ? sanitize_text_field($_GET['tab']) : key($tabs);

		?>
		<div class="wrap esr-settings esr-payments">
			<?php if (intval(ESR()->settings->esr_get_option('debts_enabled', -1)) === -1) {
				do_action('esr_print_payment_table_tab', $selected_wave);
			} else { ?>
				<h2 class="nav-tab-wrapper"><?php
					foreach ($tabs as $tab_key => $tab) {
						$tab_url = add_query_arg([
							'tab' => $tab_key,
						]);
						$active  = $active_tab == $tab_key ? ' nav-tab-active' : '';
						?>
						<a href="<?php echo esc_url($tab_url); ?>" class="nav-tab<?php echo esc_attr($active); ?>"><?php echo esc_html($tab['title']); ?></a>
						<?php
					}
					?></h2>
				<div id="tab_container">
					<?php do_action($tabs[$active_tab]['action'], $selected_wave); ?>
				</div>
			<?php } ?>
		</div>

		<?php
	}

}
