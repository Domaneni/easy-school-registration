<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Add_Over_Limit {

	const MENU_SLUG = 'esr_admin_sub_page_add_over_limit';

	public static function print_page() {

		$tabs = apply_filters('esr_add_over_limit_admin_tabs', [
			'add_over_limit' => [
				'title'  => esc_html__('Add Over Limit', 'easy-school-registration'),
				'action' => 'esr_print_add_over_limit_tab'
			],
		]);
		?><div class="wrap esr-settings"><?php

		if (count($tabs) === 1) {
			do_action('esr_print_add_over_limit_tab');
		} else {
			$active_tab = isset($_GET['tab']) && isset($tabs[sanitize_text_field($_GET['tab'])]) ? sanitize_text_field($_GET['tab']) : key($tabs);
			?>
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
				<?php do_action($tabs[$active_tab]['action']); ?>
			</div>
			<?php
		}

		?></div><?php
	}
}
