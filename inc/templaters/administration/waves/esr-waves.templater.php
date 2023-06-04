<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Templater_Waves {

	const MENU_SLUG = 'esr_admin_waves';


	public static function print_content() {
		$subblock_wave_table = new ESR_Waves_Table_Subblock_Templater();
		$subblock_edit_form  = new ESR_Waves_Edit_Form_Subblock_Templater();
		$worker_teacher      = new ESR_Wave_Worker();
		$user_can_edit       = current_user_can('esr_wave_edit');

		if (isset($_POST['esr_save_wave']) && $user_can_edit) {
			$worker_teacher->process_wave($_POST);
		}

		$esr_edited_wave_id = isset($_GET['wave_id']) ? intval($_GET['wave_id']) : null;
		$esr_edited_duplicate = isset($_GET['esr_duplicate']) ? intval($_GET['esr_duplicate']) : 0;

		?>
		<div class="wrap esr-settings ">
			<?php
			if (($esr_edited_wave_id !== null) && $user_can_edit) {
				$subblock_edit_form->print_content($esr_edited_wave_id, $esr_edited_duplicate);
			} else {
				$subblock_wave_table->print_table();
			}
			?>
		</div>
		<?php
	}

}
