<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_User_Info_Courses_Templater {

	const MENU_SLUG = 'esr_user_info_course_list';


	public static function print_page() {
		$subblock_courses_table = new ESR_Templater_User_Info_Courses_Table();

		$selected_wave = apply_filters('esr_all_waves_select_get', []);

		?>
		<div class="wrap esr-settings esr-course-in-numbers">
			<div class="esr_controls">
				<?php do_action('esr_all_waves_select_print', $selected_wave); ?>
			</div>
			<div class="esr-settings-header esr-clearfix">
				<h2><?php esc_html_e('Student info', 'easy-school-registration'); ?></h2>
				<a class="esr-student-download-button" href="#" data-wave-id="<?php echo esc_attr($selected_wave); ?>" title="<?php esc_attr_e('Download Calendar', 'easy-school-registration'); ?>"><span class="dashicons dashicons-calendar-alt"></span></a>
			</div>
			<?php
			$subblock_courses_table->print_content($selected_wave);
			?>
		</div>
		<?php

	}
}
