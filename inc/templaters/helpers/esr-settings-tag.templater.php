<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Settings_Tag_Templater {

    /**
     * @param [] $tags
     * @return string
     */
	public function esr_print_tags_table($tags)
    {
		$tags_list = '';

		if (!empty($tags)) {
			$tags_list .= '<table class="esr-tags-table">';
			foreach ($tags as $tag) {
				if (isset($tag['type']) && ($tag['type'] === 'double')) {
					$tags_list .= '<tr><td>[' . $tag['tag'] . '][/' . $tag['tag'] . ']</td><td>' . $tag['description'] . '</td></tr>';
				} else {
					$tags_list .= '<tr><td>[' . $tag['tag'] . ']</td><td>' . $tag['description'] . '</td></tr>';
				}
			}
			$tags_list .= '</table>';
		}

		return wp_kses($tags_list, [
            'table' => [],
            'tr' => [],
            'td' => []
        ]);
	}


	public static function esr_tag_replace_string($tag, $body, $replacement) {
		return str_replace("[" . $tag['tag'] . "]", esc_html($replacement), $body);
	}


	public static function esr_tag_replace_courses_list_by_id($tag, $body, $courses) {
		$replacement = "";

		if ($courses) {
			$replacement .= "<ul>";
			foreach ($courses as $id) {
				$course_data = ESR()->course->get_course_data($id);
				if (intval(ESR()->settings->esr_get_option('multiple_dates', -1)) === -1) {
					$replacement .= "<li>" . esc_html($course_data->title . " - " . ESR()->day->get_day_title($course_data->day) . " (" . $course_data->time_from . ' / ' . $course_data->time_to . ")") . "</li>";
				} else {
					$replacement .= "<li>" . esc_html($course_data->title);
					$replacement .= "<ul>";
					foreach (ESR()->multiple_dates->esr_get_all_course_dates($course_data->id) as $key => $date) {
						$replacement .= "<li>" . esc_html(ESR()->day->get_day_title($date->day) . " (" . $date->time_from . ' / ' . $date->time_to . ")") . "</li>";
					}
					$replacement .= "</ul></li>";
				}
			}
			$replacement .= "</ul>";
		}

		return wp_kses_post(str_replace("[" . $tag['tag'] . "]", $replacement, $body));
	}


	public static function esr_tag_replace_courses_list($tag, $body, $courses) {
		$replacement = "";

		if ($courses) {
			$replacement .= "<ul>";
			foreach ($courses as $id => $course) {
				$course_data = ESR()->course->get_course_data($course->id);
				if (intval(ESR()->settings->esr_get_option('multiple_dates', -1)) === -1) {
					$replacement .= "<li>" . esc_html($course_data->title . " - " . ESR()->day->get_day_title($course_data->day) . " (" . $course_data->time_from . ' / ' . $course_data->time_to . ")") . "</li>";
				} else {
					$replacement .= "<li>" . esc_html($course_data->title);
					$replacement .= "<ul>";
					foreach (ESR()->multiple_dates->esr_get_all_course_dates($course->id) as $key => $date) {
						$replacement .= "<li>" . esc_html(ESR()->day->get_day_title($date->day) . " (" . $date->time_from . ' / ' . $date->time_to . ")") . "</li>";
					}
					$replacement .= "</ul></li>";
				}
			}
			$replacement .= "</ul>";
		}

		return wp_kses_post(str_replace("[" . $tag['tag'] . "]", $replacement, $body));
	}


	public static function esr_tag_replace_wave_title($tag, $body, $wave_id) {
		$wave = ESR()->wave->get_wave_data($wave_id);

		return str_replace("[" . $tag['tag'] . "]", esc_html($wave->title), $body);
	}


	public static function esr_tag_replace_waves_title($tag, $body, $wave_ids) {
		$waves_title = [];
		foreach ($wave_ids as $wave_id) {
			$wave          = ESR()->wave->get_wave_data($wave_id);
			$waves_title[] = $wave->title;
		}

		return str_replace("[" . $tag['tag'] . "]", esc_html(implode(', ', $waves_title)), $body);
	}


	public static function esr_tag_replace_price($tag, $body, $price) {
		return str_replace("[" . $tag['tag'] . "]", esc_html(ESR()->currency->prepare_price($price)), $body);
	}


	public static function esr_tag_replace_price_difference($tag, $body, $payment) {
		return str_replace("[" . $tag['tag'] . "]", esc_html(ESR()->currency->prepare_price($payment->to_pay - $payment->payment)), $body);
	}


	public static function esr_tag_replace_floating_price($tag, $body, $price) {
		return str_replace("[" . $tag['tag'] . "]", esc_html(ESR()->currency->prepare_price($price)), $body);
	}


	public static function esr_tag_replace_settings_parameter($tag, $body) {
		return str_replace("[" . $tag['tag'] . "]", esc_html(ESR()->settings->esr_get_option($tag['id'], null)), $body);
	}


	public static function esr_tag_replace_variable_symbol($tag, $body, $variable_symbol) {
		return str_replace("[" . $tag['tag'] . "]", esc_html($variable_symbol), $body);
	}


	public static function esr_tag_replace_course_data($tag, $body, $course) {
		$replacement = "";

		if ($course->{$tag['id']}) {
			$replacement = stripslashes($course->{$tag['id']});
		}

		return str_replace("[" . $tag['tag'] . "]", esc_html($replacement), $body);
	}


	public static function esr_tag_replace_hall_data($tag, $body, $hall_key) {
		$replacement = "";
		if ($hall_key !== null) {
			$hall = ESR()->hall->get_hall($hall_key);
			if ($hall) {
				$replacement = isset($hall[$tag['id']]) ? $hall[$tag['id']] : "";
			}
		}

		return str_replace("[" . $tag['tag'] . "]", esc_html($replacement), $body);
	}


	public static function esr_tag_replace_course_teachers($tag, $body, $course) {
		return str_replace("[" . $tag['tag'] . "]", esc_html(ESR()->teacher->get_teachers_names($course->teacher_first, $course->teacher_second)), $body);
	}


	public static function esr_tag_replace_course_day($tag, $body, $course) {
		return str_replace("[" . $tag['tag'] . "]", esc_html(ESR()->day->get_day_title($course->{$tag['id']})), $body);
	}


	public static function esr_tag_replace_course_date($tag, $body, $course) {
		$date        = date_create($course->{$tag['id']});
		$replacement = date_format($date, get_option('date_format'));

		return str_replace("[" . $tag['tag'] . "]", esc_html($replacement), $body);
	}


	public static function esr_tag_replace_course_time($tag, $body, $course) {
		$date        = new DateTime($course->{$tag['id']});
		$replacement = $date->format(get_option('time_format'));

		return str_replace("[" . $tag['tag'] . "]", esc_html($replacement), $body);
	}


	public static function esr_tag_replace_course_days_times($tag, $body, $days_times) {
		$replacement = "";
		foreach ($days_times as $key => $day_data)
		{
			$time_from    = new DateTime($day_data->time_from);
			$time_to      = new DateTime($day_data->time_to);
			$replacement .= "<li>" . esc_html(ESR()->day->get_day_title($day_data->day) . " (" . $time_from->format(get_option('time_format')) . " / " . $time_to->format(get_option('time_format'))) . ")</li>";
		}

		return wp_kses_post(str_replace("[" . $tag['tag'] . "]", $replacement !== "" ? "<ul>" . $replacement . "</ul>" : "", $body));
	}


	public static function esr_tag_replace_dancing_as($tag, $body, $dancing_as) {
		return str_replace("[" . $tag['tag'] . "]", esc_html(ESR()->dance_as->get_title($dancing_as)), $body);
	}


	public static function esr_tag_replace_registration_course_list($tag, $body, $courses) {
		$replacement = "";
		$tag_code    = str_replace('list_', '', $tag['tag']);
		if ($courses) {
			$replacement .= "<ul>";
			foreach ($courses as $course_id => $course) {
				$course_data = ESR()->course->get_course_data($course_id);
				$replacement .= "<li>" . esc_html($course_data->title) . "</li>";
			}
			$replacement .= "</ul>";

			$body = str_replace('[' . $tag_code . '_exists]', '', $body);
			$body = str_replace('[/' . $tag_code . '_exists]', '', $body);
		} else {
			$body = preg_replace('/\[' . $tag_code . '_exists\].*\[\/' . $tag_code . '_exists\]/is', '', $body);
		}

		return wp_kses_post(str_replace("[" . $tag['tag'] . "]", $replacement, $body));
	}


	public static function esr_tag_replace_registration_repeat_course_list($tag, $body, $courses) {
		$replacement = "";
		$tag_code    = str_replace('list_', '', $tag['tag']);
		if ($courses) {
			$replacement .= "<ul>";
			foreach ($courses as $course_id => $course) {
				$course_data = ESR()->course->get_course_data(isset($course->course_id) ? $course->course_id : $course_id);
				$replacement .= "<li>" . esc_html($course_data->title);
				if ((isset($course->dancing_with) && $course->dancing_with) || (isset($course->dancing_as) && !ESR()->dance_as->is_solo($course->dancing_as))) {
					$replacement .= "<br><ul style='margin-left: 20px'>";
					if (isset($course->dancing_as) && !ESR()->dance_as->is_solo($course->dancing_as)) {
						$replacement .= "<li>" . esc_html__('Role', 'easy-school-registration') . ": " . esc_html(ESR()->dance_as->get_title($course->dancing_as)) . "</li>";
					}
					if (isset($course->dancing_with) && $course->dancing_with) {
						$replacement .= "<li>" . esc_html__('Partner', 'easy-school-registration') . ": " . esc_html($course->dancing_with) . "</li>";
					}
					$replacement .= "</ul>";
				}
				$replacement .= "</li>";
			}
			$replacement .= "</ul>";

			$body = str_replace('[' . $tag_code . '_exists]', '', $body);
			$body = str_replace('[/' . $tag_code . '_exists]', '', $body);
		} else {
			$body = preg_replace('/\[' . $tag_code . '_exists\].*\[\/' . $tag_code . '_exists\]/is', '', $body);
		}

		return wp_kses_post(str_replace("[" . $tag['tag'] . "]", $replacement, $body));
	}


	public static function esr_tag_replace_user_registration_info($tag, $body, $user_registration_info) {
		$replacement = "";

		if ($user_registration_info->{$tag['id']}) {
			$replacement = $user_registration_info->{$tag['id']};
		}

		return str_replace("[" . $tag['tag'] . "]", esc_html($replacement), $body);
	}
}