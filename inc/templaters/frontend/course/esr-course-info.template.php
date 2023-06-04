<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Course_Info {

	public static function esr_print_info_icon_callback($course, $course_enabled) {
		if (intval($course->description_type) != 0) {
			?>
			<a class="esr-course-info-icon">
				<svg width="100%" height="100%" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
					<circle cx="9.598" cy="9.598" r="8.398" style="fill:#fff;"/>
					<rect x="7.911" y="4.32" width="3.12" height="3.12" style="fill:#0a4450;width:3px;"/>
					<rect x="7.92" y="8.64" width="3.12" height="6.72" style="fill:#0a4450;width:3px;"/>
				</svg>
			</a>
			<?php
		}
	}
}

add_action('esr_registration_schedule_print', ['ESR_Template_Course_Info', 'esr_print_info_icon_callback'], 11, 3);