<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Subblock_User_Courses_Schedule {


	public function print_table($selected_wave) {
		$to_print       = ESR()->schedule->get_user_wave_schedule_to_print($selected_wave, get_current_user_id());
		$courses_times  = $to_print['courses_times'];
		$courses_by_day = $to_print['courses_by_day'];

		if ($to_print !== null) {
			?>
			<div class="esr-schedule-calendar esr-clearfix">
				<div class="esr-row esr-header">
					<div></div>
					<div class="esr-hall-header"></div>
					<?php
					foreach ($courses_times as $key => $value) {
						echo '<div class="esr-time">' . esc_html($key) . '</div>';
					}
					?>
				</div>
				<?php
				foreach ($courses_by_day as $day_id => $halls_schedule) {
					?>
					<div class="esr-row">
						<div class="esr-day"><?php echo esc_html(ESR()->day->get_day_title($day_id)); ?></div>
						<div class="esr-halls-schedule">
							<?php
							foreach ($halls_schedule as $hall_key => $schedule) {
								?>
								<div class="esr-day-hall-schedule">
								<?php
								foreach ($courses_times as $key => $value) {
									if (isset($schedule[$key])) {
										$course = $schedule[$key]; ?>
										<div class="esr-course esr-add">
											<span class="esr-title"><?php echo esc_html(stripslashes($course->title)); ?></span>
											<span class="esr-sub-title"><?php echo esc_html($course->sub_header); ?></span>
											<span class="esr-level"><?php echo esc_html(($course->level_id ? '(' . ESR()->course_level->get_title($course->level_id) . ')' : '') . ' *' . substr(ESR()->course_group->get_title($course->group_id), 0, 1)); ?></span>
											<span class="esr-teachers"><?php echo esc_html(($course->teacher_first ? ESR()->teacher->get_teacher_name($course->teacher_first) : '') . ($course->teacher_second ? ($course->teacher_second ? ' & ' : '') . ESR()->teacher->get_teacher_name($course->teacher_second) : '')); ?></span>
										</div>
									<?php } else { ?>
										<div class="esr-course esr-empty"></div>
									<?php }
								}
								?></div><?php
							}
							?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		<?php }
	}


}
