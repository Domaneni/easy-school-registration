<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Courses_In_Numbers {

	const MENU_SLUG = 'esr_admin_sub_page_course_in_numbers';


	public static function print_page() {
		$template_schedule = new ESR_Schedule_Templater();

		$selected_wave = apply_filters('esr_all_waves_select_get', []);

		if (isset($_POST['esr_edit_teacher_submit'])) {
			$selected_wave = intval($_POST['esr_wave']);
		}
		if (isset($_POST['esr_recount_wave']) && isset($_POST['esr_recount_wave_id'])) {
			$worker_cin = new ESR_Course_In_Numbers_Worker();
			$worker_cin->esr_recount_wave_statistics(intval($_POST['esr_recount_wave_id']));
		}

		?>
		<div class="wrap esr-settings esr-course-in-numbers">
			<?php do_action('esr_all_waves_select_print', $selected_wave); ?>
			<div class="esr-settings-header esr-clearfix">
				<h2 class="esr-title"><?php esc_html_e('Course In Numbers', 'easy-school-registration'); ?></h2>
				<?php if (current_user_can('esr_course_in_number_edit')) { ?>
					<form action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="post" class="esr-recount-button">
						<input type="hidden" name="esr_recount_wave_id" value="<?php echo esc_attr($selected_wave); ?>">
						<input type="submit" name="esr_recount_wave" class="page-title-action" value="<?php esc_attr_e('Recount Statistics', 'easy-school-registration'); ?>">
					</form>
				<?php } ?>
			</div>
			<div class="content">
				<?php
				do_action('esr_print_course_in_numbers_statistics', $selected_wave);
				if (ESR()->settings->esr_get_option('courses_schedule_style', 'as_table') === 'as_table') {
					do_action('esr_print_course_in_numbers_table', $selected_wave);
				} else {
					$template_schedule->print_content($selected_wave, [], false, new ESR_Courses_In_Numbers_Course_Helper());
				}
				?>
			</div>
		</div>
		<?php
	}
}
