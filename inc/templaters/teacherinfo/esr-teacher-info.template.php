<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Teacher_Info_Template {

	const MENU_SLUG = 'esr_teacher_info';


	public static function print_page() {
		$subblock_courses_table = new ESR_Template_Teacher_Info_Courses_Table();

		$selected_wave = apply_filters('esr_all_waves_select_get', []);

		?>
		<div class="wrap esr-settings esr-teacher-info">
			<div class="esr_controls">
				<?php do_action('esr_all_waves_select_print', $selected_wave); ?>
			</div>
			<div class="esr-settings-header esr-clearfix">
				<h2><?php esc_html_e('Teacher info', 'easy-school-registration'); ?></h2>
				<a class="esr-teacher-calendar-generation esr-download-button" href="#" data-wave-id="<?php echo esc_attr($selected_wave); ?>" title="<?php esc_attr_e('Download calendar', 'easy-school-registration'); ?>"><span class="dashicons dashicons-calendar-alt"></span></a>
			</div>
			<?php
			$subblock_courses_table->print_content($selected_wave);
			?>
		</div>
		<?php

	}
}
