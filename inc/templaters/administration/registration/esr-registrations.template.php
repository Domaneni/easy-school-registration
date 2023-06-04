<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Registrations {

	const MENU_SLUG = 'esr_admin_sub_page_registrations';


	public static function print_page() {
		$subblock_registrations_table     = new ESR_Registrations_Table_Subblock_Templater();
		$subblock_registrations_edit_form = new ESR_Registrations_Edit_Form_Subblock_Templater();
		$user_can_edit                    = current_user_can('esr_registration_edit');

		$selected_wave = apply_filters('esr_all_waves_select_get', ESR()->wave->get_all_waves_ids());

		if (isset($_GET['cin_wave_id'])) {
			$selected_wave = intval($_GET['cin_wave_id']);
		}

		?>
		<div class="wrap esr-settings-registrations esr-settings"
		     <?php if (isset($_GET['cin_course_id'])) { ?>data-cin-course-id="<?php echo esc_attr(intval($_GET['cin_course_id'])); ?>"<?php } ?>
		     <?php if (isset($_GET['cin_student_id'])) { ?>data-cin-student-id="<?php
		        $student = get_user_by('ID', intval($_GET['cin_student_id']));
		        if (!empty($student)) {
		        	echo esc_attr($student->user_email);
		        }
		     ?>"<?php } ?>>
			<div class="esr_controls">
				<?php do_action('esr_all_waves_select_print', $selected_wave); ?>
			</div>
			<div class="esr-settings-header esr-clearfix">
				<h1 class="wp-heading-inline"><?php esc_html_e('Registration', 'easy-school-registration'); ?></h1>
			</div>
			<?php
			do_action('esr_print_registration_statistics', $selected_wave);
			if ($user_can_edit) {
				$subblock_registrations_edit_form->print_content($selected_wave);
			}
			$subblock_registrations_table->print_table($selected_wave);
			?>
		</div>
		<?php
	}

}
