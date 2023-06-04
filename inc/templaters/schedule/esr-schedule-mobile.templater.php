<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Schedule_Mobile_Templater
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
				<div class="esr-schedule-calendar <?php if (!$for_registration) {
					echo 'esr-disable-registration';
				} ?> esr-clearfix" data-wave-id="<?php echo esc_attr($wave_id); ?>">
					<?php
					if (!$for_registration || ESR()->wave->is_wave_registration_active($wave_id) || (isset($settings['test']) && (intval($settings['test']) === 1))) { ?>
						<?php foreach ($waves_data[$wave_id]['courses'] as $day_id => $halls_schedule) { ?>
							<div class="esr-row">
								<div class="esr-halls-schedule">
									<h3 class="esr-day"><?php echo esc_html(ESR()->day->get_day_title($day_id, isset($settings['day_type']) && ($settings['day_type'] === 'short'))); ?></h3>
									<?php foreach ($halls_schedule as $hall_key => $schedule) { ?>
										<div class="esr-day-hall-schedule">
										<h4 class="esr-hall"><?php echo isset($halls[$hall_key]) ? esc_html($halls[$hall_key]) : ''; ?></h4>
										<ul>
											<?php
											foreach ($schedule as $key => $course) {
												$course_print->print_mobile_course_html($course, $for_registration);
											}
											?></ul>
										</div><?php
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