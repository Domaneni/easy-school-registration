<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Template_Schedule_By_Hours_Compact {

	private $schedule_helper;


	public function __construct() {
		$this->schedule_helper = new ESR_Schedule_Helper();
	}


	public function print_content($waves_data, $settings, $for_registration = true, $course_print = null) {
		$halls = ESR()->hall->get_hall_names();

		$disable_empty_space = (isset($settings['disable_empty']) && ($settings['disable_empty'] == 1));
		$hour_limits         = null;
		if (isset($settings['hour_limits']) && $settings['hour_limits']) {
			$limits = explode(';', $settings['hour_limits']);

			foreach ($limits as $k => $v) {
				$hour_limits[$k] = explode('-', $v);
			}
		}

		if ($course_print == null) {
			$course_print = new ESR_Schedule_Helper();
		}

		if ($waves_data) {
			foreach ($waves_data as $wave_id => $data) {
				?>
				<div class="esr-schedule-calendar esr-disable-js-width schedule-by-hours-compact <?php if (!$for_registration) {
					echo 'esr-disable-registration';
				} ?> esr-clearfix"
				     data-wave-id="<?php echo esc_attr($wave_id); ?>"
					<?php apply_filters('esr_schedule_wave_discount', $wave_id); ?>>
					<?php

					if (!$for_registration || ESR()->wave->is_wave_registration_active($wave_id) || (isset($settings['test']) && (intval($settings['test']) === 1))) {
						foreach ($waves_data[$wave_id]['courses'] as $day_id => $halls_schedule) {
							?>
							<div class="esr-row">
								<div class="esr-day">
									<div class="esr-day-wrapper"><span class="esr-day-title"><?php echo esc_html(ESR()->day->get_day_title($day_id, isset($settings['day_type']) && ($settings['day_type'] === 'short'))); ?></span></div>
								</div>
								<div class="esr-halls-schedule">
									<?php
									foreach ($halls_schedule as $hall_key => $schedule) {
										?>
										<div class="esr-day-hall-schedule">
										<div class="esr-hall"><span class="esr-hall-title"><?php echo isset($halls[$hall_key]) ? esc_html($halls[$hall_key]) : ' '; ?></span></div>
										<?php
										if ($hour_limits) {
											$last_key = null;
											foreach ($hour_limits as $hl_key => $limit) {
												$course_found = false;
												foreach ($schedule as $s_key => $course) {
													if (($limit[0] <= $course->time_from) && ($limit[1] >= $course->time_from)) {
														$course->settings = $settings;
														$course_print->print_course_html($course, $for_registration, 'width', false);
														$course_found = true;
														continue;
													}
												}
												if (!$course_found) {
													$this->schedule_helper->print_empty_space_html(null, null, 'width', false);
												}
											}
										} else {
											$last_end_time = $data['lowest_time']->time_from;
											foreach ($schedule as $key => $course) {
												$course->settings = $settings;

												if (!$disable_empty_space && (($last_end_time || ($last_end_time >= $course->time_from)) && (((abs(strtotime($course->time_from) - strtotime($last_end_time)) / 60) >= 45)))) {
													$this->schedule_helper->print_empty_space_html($last_end_time, $course->time_from, 'width', false);
												}

												$course_print->print_course_html($course, $for_registration, 'width', false);

												$last_end_time = $course->time_to;
											}
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