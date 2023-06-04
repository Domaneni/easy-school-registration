<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Settings_Templater {

	const MENU_SLUG = 'esr_admin_sub_page_settings';


	public static function print_page() {
		$settings_tabs = ESR()->settings->esr_get_settings_tabs();
		$settings_tabs = empty($settings_tabs) ? [] : $settings_tabs;
		$active_tab    = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
		$active_tab    = array_key_exists($active_tab, $settings_tabs) ? $active_tab : 'general';
		$sections      = ESR()->settings->esr_get_settings_tab_sections($active_tab);
		$key = $sections ? key($sections) : 'main';

		$registered_sections = ESR()->settings->esr_get_settings_tab_sections($active_tab);
		$section             = isset($_GET['section']) && !empty($registered_sections) && array_key_exists(sanitize_text_field($_GET['section']), $registered_sections) ? sanitize_text_field($_GET['section']) : $key;

		ob_start();
		?>
		<div class="wrap esr-settings <?php echo 'wrap-' . esc_attr($active_tab); ?>">
			<h2><?php esc_html_e('Easy School Registration Settings', 'easy-school-registration'); ?></h2>
			<h2 class="nav-tab-wrapper">
				<?php
				foreach (ESR()->settings->esr_get_settings_tabs() as $tab_id => $tab_name) {
					$tab_url = add_query_arg([
						'settings-updated' => false,
						'tab'              => $tab_id,
					]);

					// Remove the section from the tabs so we always end up at the main section
					$tab_url = remove_query_arg('section', $tab_url);

					$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

					echo '<a href="' . esc_url($tab_url) . '" class="nav-tab' . esc_attr($active) . '">';
					echo esc_html($tab_name);
					echo '</a>';
				}
				?>
			</h2>
			<?php

			$number_of_sections = count($sections);
			$number             = 0;
			if ($sections && $number_of_sections > 0) {
				echo '<div><ul class="subsubsub">';
				foreach ($sections as $section_id => $section_name) {
					echo '<li>';
					$number++;
					$tab_url = add_query_arg([
						'settings-updated' => false,
						'tab'              => $active_tab,
						'section'          => $section_id
					]);
					$class   = '';
					if ($section == $section_id) {
						$class = 'current';
					}
					echo '<a class="' . esc_attr($class) . '" href="' . esc_url($tab_url) . '">' . esc_html($section_name) . '</a>';

					if ($number != $number_of_sections) {
						echo ' | ';
					}
					echo '</li>';
				}
				echo '</ul></div>';
			}
			?>
			<div id="tab_container">
				<form method="post" action="options.php">
					<table class="form-table">
						<?php

						settings_fields('esr_settings');

						do_action('esr_settings_tab_top_' . $active_tab . '_' . $section);

						do_settings_sections('esr_settings_' . $active_tab . '_' . $section);

						do_action('esr_settings_tab_bottom_' . $active_tab . '_' . $section);

						?>
					</table>
					<?php submit_button(); ?>
				</form>
			</div><!-- #tab_container-->

		</div><!-- .wrap -->
		<?php
		echo ob_get_clean();
	}

}
