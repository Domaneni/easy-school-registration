<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Templater_Courses {

	const MENU_SLUG = 'esr_admin_courses';


	public static function print_content() {
		$subblock_course_table = new ESR_Courses_Table_Subblock_Templater();
		$subblock_edit_form    = new ESR_Courses_Edit_Form_Subblock_Templater();
		$worker_course         = new ESR_Course_Worker();
		$user_can_edit         = current_user_can('esr_course_edit');

		if (isset($_POST['esr_choose_course_submit']) && $user_can_edit) {
			$worker_course->process_course($_POST);
		}

		$esr_edited_course_id = isset($_GET['course_id']) ? sanitize_text_field($_GET['course_id']) : null;
		$esr_edited_duplicate = isset($_GET['esr_duplicate']) ? intval(sanitize_text_field($_GET['esr_duplicate'])) : 0;

		?>
		<div class="wrap esr-settings">
			<?php
				if (($esr_edited_course_id !== null) && $user_can_edit) {
					$subblock_edit_form->print_content($esr_edited_course_id, $esr_edited_duplicate);
				} else {
					$subblock_course_table->print_table();
				}
			?>
		</div>
		<?php
	}

}
