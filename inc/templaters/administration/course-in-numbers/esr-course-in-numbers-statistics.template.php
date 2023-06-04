<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Courses_In_Numbers_Statistics {

	public static function esr_print_statistics($wave_id) {
		if (intval(ESR()->settings->esr_get_option('show_basic_statistics', -1)) !== -1) {
			$summary_statistics = ESR()->statistics->esr_get_course_summary_statistics($wave_id);
			if ($summary_statistics) {
				?>
				<div class="esr-statistics-box">
					<h4><?php esc_html_e('Basic Statistics', 'easy-school-registration'); ?></h4>
					<table>
						<tr>
							<th><?php esc_html_e('Registrations', 'easy-school-registration'); ?>:</th>
							<td><?php echo esc_html($summary_statistics->registrations) ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e('Waiting', 'easy-school-registration'); ?>:</th>
							<td><?php echo esc_html($summary_statistics->waiting) ?></td>
						</tr>
					</table>
				</div>
				<?php
			}
		}
	}
}

add_action('esr_print_course_in_numbers_statistics', ['ESR_Template_Courses_In_Numbers_Statistics', 'esr_print_statistics']);
