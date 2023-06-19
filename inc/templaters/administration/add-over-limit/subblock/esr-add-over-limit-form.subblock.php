<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class ESR_Add_Over_Limit_Subblock_Templater
{

	public static function esr_print_add_over_limit_tab_callback()
	{
		$worker_add_over_limit = new ESR_Add_Over_Limit_Worker();

		if (isset($_POST['esr_add_over_limit_submit'])) {
			$worker_add_over_limit->process_form($_POST);
		}
        

        $disable_couples = !(intval(ESR()->settings->esr_get_option('disable_couples', -1)) === -1);

        $selected_wave = apply_filters('esr_all_waves_select_get', []);
		?>
		<div class="wrap esr-settings">
			<div class="esr_controls">
				<?php do_action('esr_all_waves_select_print', $selected_wave); ?>
			</div>

            <form id="esr-add-over-limit" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="post"
                  data-parsley-validate="" class="form-horizontal form-label-left">

                <div class="esr-column">
                    <h2>
                        <?php
                            if ($disable_couples) {
                                esc_html_e('Student', 'easy-school-registration');
                            } else {
                                esc_html_e('Leader', 'easy-school-registration');
                            }
                        ?>
                    </h2>
                    <div class="form-fields">
                        <div class="form-row">
                            <label><?php esc_html_e('First Name', 'easy-school-registration'); ?> <span class="required">*</span></label>
                            <input type="text" id="first-name" name="esr_leader_name">
                        </div>
                        <div class="form-row">
                            <label><?php esc_html_e('Last Name', 'easy-school-registration'); ?> <span class="required">*</span></label>
                            <input type="text" id="last-name" name="esr_leader_surname">
                        </div>
                        <div class="form-row">
                            <label><?php esc_html_e('Email', 'easy-school-registration'); ?> <span class="required">*</span></label>
                            <input type="email" id="leader-email" name="esr_leader_email">
                        </div>
                        <div class="form-row">
                            <label><?php esc_html_e('Phone', 'easy-school-registration'); ?></label>
                            <input type="text" id="leader-phone" name="esr_leader_phone">
                        </div>
                        <?php if (intval(ESR()->settings->esr_get_option('free_registrations_enabled', -1)) != -1) { ?>
                            <div class="form-row">
                                <label><?php esc_html_e('Free Course', 'easy-school-registration'); ?></label>
                                <input type="checkbox" id="leader-free-course" name="esr_leader_free_registration">
                            </div>
                        <?php } ?>
                        <div class="form-row">
                            <label><?php esc_html_e('Course', 'easy-school-registration') ?></label>

                            <select name="esr_course_id">
                                <?php
                                $courses = ESR()->course->get_courses_data_by_wave($selected_wave);
                                foreach ($courses as $id => $course) { ?>
                                    <option value="<?php echo esc_attr($course->id); ?>"><?php echo esc_html(stripslashes($course->title) . ' - ' . ESR()->day->get_day_title($course->day) . ' (' . $course->time_from . '/' . $course->time_to . ')'); ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <label><?php esc_html_e('Disable emails?', 'easy-school-registration'); ?></label>
                            <input id="disable-emails" class="form-control col-md-7 col-xs-12" type="checkbox" name="esr_disable_emails" value="1">
                        </div>
                        <div class="form-row">
                            <button type="submit" class="btn btn-success" name="esr_add_over_limit_submit"><?php esc_html_e('Send', 'easy-school-registration'); ?></button>
                        </div>
                        <?php do_action('esr_add_leader_over_limit_form'); ?>
                    </div>
                </div>
                <?php if (intval(ESR()->settings->esr_get_option('disable_couples', -1)) === -1) { ?>
                    <div class="esr-column">
                        <h2><?php esc_html_e('Follower', 'easy-school-registration'); ?></h2>
                        <div class="form-fields">
                            <div class="form-row">
                                <label><?php esc_html_e('First Name', 'easy-school-registration'); ?>
                                    <span class="required">*</span></label>

                                <input type="text" id="first-name" name="esr_follower_name"
                                       class="form-control col-md-7 col-xs-12">
                            </div>
                            <div class="form-row">
                                <label><?php esc_html_e('Last Name', 'easy-school-registration'); ?> <span
                                            class="required">*</span></label>
                                <input type="text" id="last-name" name="esr_follower_surname"
                                       class="form-control col-md-7 col-xs-12">
                            </div>
                            <div class="form-row">
                                <label><?php esc_html_e('Email', 'easy-school-registration'); ?>
                                    <span class="required">*</span></label>
                                <input id="follower-email" class="form-control col-md-7 col-xs-12" type="email"
                                       name="esr_follower_email">
                            </div>
                            <div class="form-row">
                                <label><?php esc_html_e('Phone', 'easy-school-registration'); ?></label>

                                <input id="follower-phone" class="form-control col-md-7 col-xs-12" type="text"
                                       name="esr_follower_phone">
                            </div>
                            <?php if (intval(ESR()->settings->esr_get_option('free_registrations_enabled', -1)) != -1) { ?>
                                <div class="form-row">
                                    <label><?php esc_html_e('Free Course', 'easy-school-registration'); ?></label>
                                        <input id="follower-free-course" class="form-control col-md-7 col-xs-12" type="checkbox" name="esr_follower_free_registration">
                                </div>
                            <?php } ?>
                            <?php do_action('esr_add_follower_over_limit_form'); ?>
                        </div>
                    </div>
                <?php } ?>
            </form>
		</div>
		<?php
	}

}

add_action('esr_print_add_over_limit_tab', ['ESR_Add_Over_Limit_Subblock_Templater', 'esr_print_add_over_limit_tab_callback']);
