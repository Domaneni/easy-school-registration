<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Schedule_Group_Filter {

	public function print_filter($wave_ids, $settings = [], $for_registration = true) {
		if ($wave_ids && is_array($wave_ids) && (count($wave_ids) > 0)) {
			?>
			<div class="esr-group-filters">
				<?php
				$groups = ESR()->course_group->get_items();
				foreach ($wave_ids as $wave_id) {
					if (!$for_registration || ESR()->wave->is_wave_registration_active($wave_id) || (isset($settings['test']) && (intval($settings['test']) === 1))) {
						$wave_groups = ESR()->course_group->get_groups_by_wave( $wave_id );
						?>
					<div class="esr-group-filter<?php echo( isset( $settings['group_filter_hide_courses'] ) && $settings['group_filter_hide_courses'] ? ' esr-group-filter-hide' : ' esr-group-filter-fade' ); ?>" data-wave="<?php echo esc_attr($wave_id) ?>">
						<span class="esr-group-filter-button<?php echo ! isset( $settings['group_preload'] ) ? ' esr-group-filter-active' : ''; ?>" data-group-id="all"><?php esc_html_e( 'All', 'easy-school-registration' ); ?></span>
						<?php
						foreach ( $wave_groups as $group_id ) {
							if ( isset( $groups[ $group_id['group_id'] ] ) ) {
								?>
								<span class="esr-group-filter-button esr-group-<?php echo esc_attr($group_id['group_id']); ?><?php echo isset( $settings['group_preload'] ) && ( intval( $settings['group_preload'] ) == $group_id['group_id'] ) ? ' esr-group-filter-active' : ''; ?>" data-group-id="<?php echo esc_attr($group_id['group_id']); ?>"><?php echo esc_attr($groups[ $group_id['group_id'] ]); ?></span>
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