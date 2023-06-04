<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Schedule_Templater {

	/** @var ESR_Schedule_Wave_Filter */
	private $filter_wave;

	/** @var ESR_Schedule_Helper */
	private $schedule_helper;

	/** @var ESR_Schedule_Mobile_Templater */
	private $templater_mobile;


	public function __construct() {
		$this->templater_mobile = new ESR_Schedule_Mobile_Templater();
		$this->filter_wave      = new ESR_Schedule_Wave_Filter();
		$this->schedule_helper  = new ESR_Schedule_Helper();
	}


	public function print_content($wave_ids, $settings, $for_registration = true, $course_print = null) {
		$templater = null;
		$type      = (isset($settings['type']) && $settings['type']) ? $settings['type'] : ESR()->settings->esr_get_option('courses_schedule_style', 'by_hours');

		$waves_data = [];
		if (!is_array($wave_ids)) {
			$wave_ids = [$wave_ids];
		}

		foreach ($wave_ids as $wave_id) {
			$wave_id = intval(trim($wave_id));
			$group_id = isset($settings['filter_group']) ? strpos($settings['filter_group'], ',') ? explode(',', $settings['filter_group']) : [intval($settings['filter_group'])] : null;
			if ($for_registration) {
				$waves_data[$wave_id]['courses'] = ESR()->schedule->get_wave_schedule_to_print($wave_id, $group_id);
			} else {
				$waves_data[$wave_id]['courses'] = ESR()->schedule->get_all_wave_schedule_to_print($wave_id, $group_id);
			}
			$waves_data[$wave_id]['lowest_time']  = ESR()->schedule->get_lowest_course_start_time($wave_id);
			$waves_data[$wave_id]['highest_time'] = ESR()->schedule->get_highest_course_end_time($wave_id);
		}

		switch ($type) {
			case 'by_days':
				$templater = new ESR_Schedule_By_Days_Templater();
				break;
			case 'by_hours':
				$templater = new ESR_Schedule_By_Hours_Templater();
				break;
			case 'by_hours_compact':
				$templater = new ESR_Template_Schedule_By_Hours_Compact();
				break;
			default:
				$templater = new ESR_Schedule_By_Hours_Templater();
		}

		$classes = ['esr-schedules', 'esr-hide-mobile'];
		if (isset($settings['automatic_zoom']) && $settings['automatic_zoom']) {
			$classes[] = 'esr-automatic-zoom';
		}

		if (isset($settings['hover_option'])) {
			$classes[] = 'esr-show-hover';
		}

		?>
		<div class="esr-schedule-wrapper esr-clearfix">
		<div class="esr-schedule-filters">
			<?php
			$this->filter_wave->print_filter($wave_ids);

			if (isset($settings['show_group_filter']) && (intval($settings['show_group_filter']) === 1)) {
				$filter_group = new ESR_Schedule_Group_Filter();
				$filter_group->print_filter($wave_ids, $settings, $for_registration);
			}
			if (isset($settings['show_level_filter']) && (intval($settings['show_level_filter']) === 1)) {
				$filter_level = new ESR_Schedule_Level_Filter();
				$filter_level->print_filter($wave_ids, $settings, $for_registration);
			}
			?>
		</div>
		<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
			<?php
			$templater->print_content($waves_data, $settings, $for_registration, $course_print);
			?>
		</div>
		<div class="esr-schedules-mobile esr-show-mobile">
			<?php
			$this->templater_mobile->print_content($waves_data, $settings, $for_registration, $course_print);
			?>
		</div>
		</div><?php
	}

}