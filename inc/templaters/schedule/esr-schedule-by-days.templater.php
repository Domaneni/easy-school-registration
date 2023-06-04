<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Schedule_By_Days_Templater
{

	private $schedule_helper;


	public function __construct()
	{
		$this->schedule_helper = new ESR_Schedule_Helper();
	}


	public function print_content($waves_data, $settings, $for_registration = true, $course_print = null)
	{
		$halls = ESR()->hall->get_hall_names();


		if ($course_print == null) {
			$course_print = new ESR_Schedule_Helper();
		}

		if ($waves_data) {
			foreach ($waves_data as $wave_id => $data) {
				?>
				<div class="esr-schedule-calendar schedule-by-day <?php if (!$for_registration) {
					echo 'esr-disable-registration';
				} ?> esr-clearfix"
				     data-wave-id="<?php echo esc_attr($wave_id); ?>"
					<?php apply_filters('esr_schedule_wave_discount', $wave_id); ?>>
					<?php


					if (!$for_registration || ESR()->wave->is_wave_registration_active($wave_id) || (isset($settings['test']) && (intval($settings['test']) === 1))) {
						foreach ($data['courses'] as $day_id => $halls_schedule) {
							?>
							<div class="esr-row">
								<div class="esr-halls-schedule">
								<div class="esr-day"><?php echo esc_html(ESR()->day->get_day_title($day_id, isset($settings['day_type']) && ($settings['day_type'] === 'short'))); ?></div>
									<?php
									foreach ($halls_schedule as $hall_key => $schedule) {
										?>
										<div class="esr-day-hall-schedule">
										<div class="esr-hall"><span class="esr-hall-title"><?php echo isset($halls[$hall_key]) ? esc_html($halls[$hall_key]) : ' '; ?></span></div>
										<?php
										$last_end_time = $data['lowest_time']->time_from;
										foreach ($schedule as $key => $course) {
											if ($last_end_time || ($last_end_time >= $course->time_from)) {
												$this->schedule_helper->print_empty_space_html($last_end_time, $course->time_from, 'height');
											}

											$course->settings = $settings;
											$course_print->print_course_html($course, $for_registration, 'height');
											$last_end_time = $course->time_to;
										}
										?></div><?php
									}
									?>
								</div>
							</div>
							<?php
						}
					} else {
						$this->schedule_helper->print_wave_closed_text($wave_id);
					}
					?>
				</div>
				<?php
			}
		}
	}

}