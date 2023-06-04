<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Schedule_Level_Filter {

	public function print_filter( $wave_ids, $settings = [], $for_registration = true ) {
		if ( $wave_ids && is_array( $wave_ids ) && ( count( $wave_ids ) > 0 ) ) {
			?>
			<div class="esr-level-filters">
				<?php
				$levels = ESR()->course_level->get_items();
				foreach ( $wave_ids as $wave_id ) {
					if ( ! $for_registration || ESR()->wave->is_wave_registration_active( $wave_id ) || ( isset( $settings['test'] ) && ( intval( $settings['test'] ) === 1 ) ) ) {

						$wave_levels = ESR()->course_level->get_levels_by_wave( $wave_id );
						?>
					<div class="esr-level-filter<?php echo( isset( $settings['level_filter_hide_courses'] ) && $settings['level_filter_hide_courses'] ? ' esr-level-filter-hide' : ' esr-level-filter-fade' ); ?>" data-wave="<?php echo esc_attr($wave_id) ?>">
						<span class="esr-level-filter-button" data-level-id="all"><?php esc_html_e( 'All', 'easy-school-registration' ); ?></span>
						<?php
						foreach ( $wave_levels as $level_id ) {
							if ( isset( $levels[ $level_id['level_id'] ] ) ) {
								?>
								<span class="esr-level-filter-button esr-level-<?php echo esc_attr($level_id['level_id']); ?>" data-level-id="<?php echo esc_attr($level_id['level_id']); ?>"><?php echo esc_html($levels[ $level_id['level_id'] ]); ?></span>
								<?php
							}
						}
						?></div><?php
					}
				}
				?>
			</div>
			<?php
		}
	}

}