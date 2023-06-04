<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Schedule_Helper {

	public function print_course_html( $course, $for_registration, $position = 'width', $position_enabled = true ) {
		$leader_registration_enabled = $follower_registration_enabled = $solo_registration_enabled = false;
		$course                      = ESR()->course->esr_prepare_course_settings( $course );

		if ( $course->is_solo ) {
			$course_enabled = $solo_registration_enabled = ESR()->dance_as->is_solo_registration_enabled( $course->id );
		} else {
			$leader_registration_enabled   = ESR()->dance_as->is_leader_registration_enabled( $course->id );
			$follower_registration_enabled = ESR()->dance_as->is_followers_registration_enabled( $course->id );
			$course_enabled                = $leader_registration_enabled || $follower_registration_enabled;
		}

		$course_classes = $this->esr_get_course_classes( $course, $for_registration, $course_enabled );

		$styles = [
			$position . ':' . $this->get_time_width( $course->time_from, $course->time_to ) . 'px;'
		];
		if ( isset( $course->settings['group_preload'] ) && ( intval( $course->settings['group_preload'] ) != $course->group_id ) ) {
			$styles [] = 'opacity: 0.4;';
		}
		?>

		<div
			<?php if ( $position_enabled ) { ?>
				style="<?php echo esc_attr(implode( ' ', $styles )); ?>"
			<?php } ?>
				class="<?php echo esc_attr(implode( ' ', $course_classes )); ?>"
            <?php if ( $for_registration ) { ?>
				data-group="<?php echo esc_attr($course->group_id); ?>"
				data-level="<?php echo esc_attr($course->level_id); ?>"
				data-id="<?php echo esc_attr($course->id); ?>"
				data-wave="<?php echo esc_attr($course->wave_id); ?>"
				data-day="<?php echo esc_attr(ESR()->day->get_day_title( $course->day )); ?>"
				data-start="<?php echo esc_attr($course->time_from); ?>"
				data-price="<?php echo esc_attr($course->real_price); ?>"
				data-enforce-partner="<?php echo esc_attr($course->enforce_partner); ?>"
			    <?php apply_filters( 'esr_schedule_course_data', $course->id ); ?>
                <?php if ( ! boolval( $course->is_solo ) ) { ?>
                    data-leader-enabled="<?php echo esc_attr(( $leader_registration_enabled ? 1 : 0 )); ?>"
                    data-follower-enabled="<?php echo esc_attr(( $follower_registration_enabled ? 1 : 0 )); ?>">
                <?php } else { ?>
                    data-solo-enabled="<?php echo esc_attr(( $solo_registration_enabled ? 1 : 0 )); ?>">
                <?php }
			} else { ?>
				data-group="<?php echo esc_attr($course->group_id); ?>"
				data-level="<?php echo esc_attr($course->level_id); ?>">
            <?php } ?>
			<span class="esr-title"><?php echo esc_html(stripslashes( $course->title )); ?></span>
			<span class="esr-sub-title"><?php echo ( $course->sub_header ? esc_html($course->sub_header) : '' ); ?></span>
			<span class="esr-course-hide-hover">
				<span class="esr-sub-title"><?php echo esc_html($course->time_from . ' - ' . $course->time_to); ?></span>
				<span class="esr-teachers"><?php echo esc_html(ESR()->teacher->get_teachers_names( $course->teacher_first, $course->teacher_second )); ?></span>
			</span>
			<?php if ( $course_enabled && isset( $course->settings['hover_option'] ) ) {
				$summary = ESR()->course_summary->get_course_summary_for_hover( $course->id );
				?>
				<span class="esr-course-show-hover">
			<?php if ( $course->is_solo ) { ?>
				<span class="esr-count">
							<?php
                            esc_html_e( 'Students', 'easy-school-registration' ) . ': ';
							if ( $course->settings['hover_option'] === 'registrations' ) {
								echo $solo_registration_enabled ? esc_html( $summary->registered_solo . '/' . $summary->max_solo ) : esc_html__( 'Full', 'easy-school-registration' );
							} else {
								echo $solo_registration_enabled ? esc_html__( 'left', 'easy-school-registration' ) . ' ' . esc_html($summary->sl) : esc_html__( 'Full', 'easy-school-registration' );
							}
							?>
						</span>
			<?php } else { ?>
				<span class="esr-count">

							<?php
                            esc_html_e( 'Leaders', 'easy-school-registration' ) . ': ';
							if ( $course->settings['hover_option'] === 'registrations' ) {
								echo $leader_registration_enabled ? esc_html( $summary->registered_leaders . '/' . $summary->max_leaders ) : esc_html__( 'Full', 'easy-school-registration' );
							} else {
								echo $leader_registration_enabled ? esc_html__( 'left', 'easy-school-registration' ) . ' ' . esc_html($summary->ll) : esc_html__( 'Full', 'easy-school-registration' );
							}
							?>
					</span>
				<span class="esr-count">
							<?php
                            esc_html_e( 'Followers', 'easy-school-registration' ) . ': ';
							if ( $course->settings['hover_option'] === 'registrations' ) {
								echo $follower_registration_enabled ? esc_html( $summary->registered_followers . '/' . $summary->max_followers ) : esc_html__( 'Full', 'easy-school-registration' );
							} else {
								echo $follower_registration_enabled ? esc_html__( 'left', 'easy-school-registration' ) . ' ' . esc_html($summary->fl) : esc_html__( 'Full', 'easy-school-registration' );
							}
							?>
					</span>
			<?php } ?>
				</span>
			<?php } ?>
			<?php do_action( 'esr_registration_schedule_print', $course, $course_enabled, $for_registration ); ?>
		</div>
		<?php
	}


	public function print_mobile_course_html( $course, $for_registration ) {
		$leader_registration_enabled = $follower_registration_enabled = $solo_registration_enabled = false;
		$course                      = ESR()->course->esr_prepare_course_settings( $course );

		if ( $course->is_solo ) {
			$course_enabled = $solo_registration_enabled = ESR()->dance_as->is_solo_registration_enabled( $course->id );
		} else {
			$leader_registration_enabled   = ESR()->dance_as->is_leader_registration_enabled( $course->id );
			$follower_registration_enabled = ESR()->dance_as->is_followers_registration_enabled( $course->id );
			$course_enabled                = $leader_registration_enabled || $follower_registration_enabled;
		}

		$course_classes = $this->esr_get_course_classes( $course, $for_registration, $course_enabled );
		?>
		<li class="<?php echo esc_attr(implode( ' ', $course_classes )); ?>"
			<?php if ( $for_registration ) { ?>
			data-group="<?php echo esc_attr($course->group_id); ?>"
			data-id="<?php echo esc_attr($course->id); ?>"
			data-wave="<?php echo esc_attr($course->wave_id); ?>"
			data-day="<?php echo esc_attr(ESR()->day->get_day_title( $course->day )); ?>"
			data-start="<?php echo esc_attr($course->time_from); ?>"
			data-price="<?php echo esc_attr($course->real_price); ?>"
			<?php if ( ! boolval( $course->is_solo ) ) { ?>
			data-leader-enabled="<?php echo( $leader_registration_enabled ? 1 : 0 ); ?>"
			data-follower-enabled="<?php echo( $follower_registration_enabled ? 1 : 0 ); ?>">
			<?php } else { ?>
				data-solo-enabled="<?php echo( $solo_registration_enabled ? 1 : 0 ); ?>">
			<?php }
			} else { ?>"><?php } ?>
			<span class="esr-course-info">
				<span class="esr-title"><?php echo esc_html(stripslashes( $course->title )); ?></span>
				<span class="esr-sub-title"><?php echo ($course->sub_header ? esc_html($course->sub_header) : ''); ?></span>
				<?php
				$teacher_string = ESR()->teacher->get_teachers_names( $course->teacher_first, $course->teacher_second );
				?>
				<span class="esr-teachers"><?php echo (strpos($teacher_string, ' & ' ) !== false ? esc_html__( 'Teachers', 'easy-school-registration' ) : esc_html__( 'Teacher', 'easy-school-registration' ) ) . ': ' . esc_html($teacher_string); ?></span>
			</span>
			<span class="esr-time"><?php echo esc_html($course->time_from . ' - ' . $course->time_to); ?></span>
			<?php do_action( 'esr_registration_schedule_print', $course, $course_enabled, $for_registration ); ?>
		</li>
		<?php
	}


	public function print_empty_space_html( $end_time, $start_time, $position = 'width', $position_enabled = true ) {
		?>
	<div class="esr-course esr-empty"
		<?php if ( $position_enabled ) { ?>
			style="<?php echo esc_attr($position . ':' . $this->get_time_width( $end_time, $start_time ) . 'px;'); ?>"
		<?php } ?>></div><?php

	}


	public static function esr_print_styles_callback() {
		if ( ( intval( ESR()->settings->esr_get_option( 'disable_styles', - 1 ) ) !== 1 ) && ! is_admin() ) {
			$styles = ESR()->settings->esr_get_registered_settings()['style'];
			?>
			<style>
				<?php
					foreach ($styles as $group_key => $group_values) {
						foreach ($group_values as $key => $value) {
							if (isset($value['selector'])) {
								$esr_settings = ESR()->settings->esr_get_option($value['id']);

								if (!isset($value['optional']) || (isset($value['optional']) && isset($esr_settings['enable_style']) && ($esr_settings['enable_style'] == 'on'))) {
									$property = is_array($value['property']) ? $value['property'] : [$value['property']];
									if (in_array('background', $property)) {
										$css_value = $esr_settings;
										$default_css_values = $value['std'];
										$rule = $value['selector'] . '{';

										if (isset($css_value['background_color']) && ($css_value['background_color'] !== '')) {
											$rule .= 'background-color:' . self::hex2rgba(isset($css_value['background_color']) ? $css_value['background_color'] : $default_css_values['background_color'], isset($css_value['background_opacity']) ? (float) $css_value['background_opacity'] : (float) $default_css_values['background_opacity']) . ';';
										}
										if (isset($css_value['border_color']) && ($css_value['border_color'] !== '')) {
											$rule .= 'border: ' . (isset($css_value['border_width']) ? $css_value['border_width'] : $default_css_values['border_width']) . 'px solid ' . (isset($css_value['border_color']) ? $css_value['border_color'] : $default_css_values['border_color']) . ';';
										}

										$rule .= '}';

										echo esc_html($rule);
									}
									if (in_array('font', $property)) {
										$css_value = $esr_settings;
										$default_css_values = $value['std'];

										$rule = $value['selector'] . '{';

										if (isset($default_css_values['color']) || isset($css_value['color'])) {
											$rule .= 'color:' . (isset($css_value['color']) ? $css_value['color'] : $default_css_values['color']) . ';';
										}

										if (isset($default_css_values['size']) || isset($css_value['size'])) {
											$rule .= 'font-size: ' . (isset($css_value['size']) ? $css_value['size'] : $default_css_values['size']) . 'px;';
										}

										$rule .= '}';

										echo esc_html($rule);
									}
								}
							}
						}
					}
				?>
			</style>
			<?php
		}
	}


	public function get_time_width( $start_time, $end_time, $subtract = 0 ) {
		$to_time   = strtotime( $start_time );
		$from_time = strtotime( $end_time );

		return ( round( abs( $to_time - $from_time ) / 60, 2 ) * 2 ) - $subtract;
	}


	public function hours_range( $start, $end, $step = '+60 minutes' ) {
		$tStart = strtotime( $start );
		$tEnd   = strtotime( $end );
		$times  = [];

		$tNow    = $tStart;
		$times[] = date( "H:i", $tNow );

		while ( $tNow <= $tEnd ) {
			$tNow    = strtotime( $step, $tNow );
			$times[] = date( "H:i", $tNow );
		}

		return $times;
	}


	public function print_wave_closed_text( $wave_id ) {
		if ( ESR()->wave->is_wave_registration_not_opened_yet( $wave_id ) ) {
			$to_return = ESR()->settings->esr_get_option( 'registration_not_opened' );

			if ( strpos( $to_return, '[registration_start]' ) !== false ) {
				$data      = ESR()->wave->get_wave_data( $wave_id );
				$date      = new DateTime( $data->registration_from );
				$to_return = str_replace( "[registration_start]", $date->format( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ), $to_return );
			}
			?>
			<div class="esr-registration-not-opened-yet"><?php
			echo wp_kses_post(nl2br( stripcslashes( $to_return ) ));
			?></div><?php
		} elseif ( ESR()->wave->is_wave_registration_closed( $wave_id ) ) {
			?>
			<div class="esr-registration-closed"><?php
			echo wp_kses_post(nl2br( stripcslashes( ESR()->settings->esr_get_option( 'registration_closed' ) ) ));
			?></div><?php
		}
	}


	private static function hex2rgba( $color, $opacity = - 1 ) {
		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if ( empty( $color ) ) {
			return $default;
		}

		//Sanitize $color if "#" is provided
		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		//Check if color has 6 or 3 characters and get values
		if ( strlen( $color ) == 6 ) {
			$hex = [ $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] ];
		} elseif ( strlen( $color ) == 3 ) {
			$hex = [ $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] ];
		} else {
			return $default;
		}

		//Convert hexadec to rgb
		$rgb = array_map( 'hexdec', $hex );

		//Check if opacity is set(rgba or rgb)
		if ( $opacity !== - 1 ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}
			$output = 'rgba(' . implode( ",", $rgb ) . ',' . $opacity . ')';
		} else {
			$output = 'rgb(' . implode( ",", $rgb ) . ')';
		}

		//Return rgb(a) color string
		return $output;
	}

	private function esr_get_course_classes( $course, $for_registration, $course_enabled ) {
		$course_classes = [ 'esr-course' ];
		if ( $for_registration ) {
			if ( ! isset( $course->disable_registration ) || ( isset( $course->disable_registration ) && ! $course->disable_registration ) ) {
				if ( $course_enabled ) {
					$course_classes[] = 'esr-add';
				} else {
					$course_classes[] = 'esr-full';
				}
			} else {
				$course_classes[] = 'esr-disabled-registration';
			}
		} else {
			$course_classes[] = 'esr-schedule-course';
		}
		if ( $course->group_id !== null ) {
			$course_classes[] = 'esr-group-' . $course->group_id;
		}
		if ( $course->level_id !== null ) {
			$course_classes[] = 'esr-level-' . $course->level_id;
		}

		return $course_classes;
	}

}

add_action( 'esr_print_styles', [ 'ESR_Schedule_Helper', 'esr_print_styles_callback' ] );