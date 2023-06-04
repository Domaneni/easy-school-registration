<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ESR_Ajax
{

    public static function esr_process_registration_callback()
    {
        $worker_registration = new ESR_Registration_Worker();
        wp_send_json($worker_registration->process_registration(json_decode(stripslashes($_POST['registration_data']))), 200);
    }


    public static function esr_remove_user_course_registration_callback()
    {
        if (isset($_POST['registration_id'])) {
            $worker_ajax = new ESR_Ajax_Worker();

            // get registration
            $registration = ESR()->registration->get_registration(intval($_POST['registration_id']));

            if ($registration) {
                wp_send_json($worker_ajax->remove_user_course_registration_callback($registration));
                wp_die();
            }
        }
        echo -1;
        wp_die();
    }


    public static function esr_remove_registration_forever_callback()
    {
        if (isset($_POST['registration_id'])) {
            $worker_ajax = new ESR_Ajax_Worker();

            // get registration
            $registration = ESR()->registration->get_registration(intval($_POST['registration_id']));

            if ($registration && ($registration->status == ESR_Registration_Status::DELETED)) {
                echo esc_html($worker_ajax->remove_registration_forever_callback($registration));
                wp_die();
            }
        }
        echo -1;
        wp_die();
    }


    public static function esr_add_user_course_registration_callback()
    {
        if (isset($_POST['esr_registration_id'])) {
            $worker_ajax = new ESR_Ajax_Worker();

            $registration = ESR()->registration->get_registration(intval($_POST['esr_registration_id']));
            wp_send_json($worker_ajax->process_add_user_course_registration($registration));

            wp_die();
        }
        echo -1;
        wp_die();
    }


    public static function esr_edit_registration_callback()
    {
        if (isset($_POST['registration_id'])) {
            $worker_ajax = new ESR_Ajax_Worker();

            echo json_encode($worker_ajax->edit_course_registration_callback($_POST));
            wp_die();
        }
        echo -1;
        wp_die();
    }


    // Payment page
    public static function esr_payment_save_payment_callback()
    {
        if (isset($_POST['user_email']) && isset($_POST['payment_status'])) {
            $worker_ajax = new ESR_Ajax_Worker();
            echo json_encode($worker_ajax->save_payment(sanitize_email($_POST['user_email']), sanitize_text_field($_POST['payment_status']), $_POST));
            wp_die();
        }

        echo -1;
        wp_die();
    }


    public static function esr_set_wave_passed()
    {
        if (isset($_POST['wave_id'])) {
            $worker_ajax = new ESR_Ajax_Worker();
            echo $worker_ajax->change_wave_passed(intval($_POST['wave_id']), true);
            wp_die();
        }
        echo -1;
        wp_die();
    }


    public static function esr_set_wave_active()
    {
        if (isset($_POST['wave_id'])) {
            $worker_ajax = new ESR_Ajax_Worker();
            echo $worker_ajax->change_wave_passed(intval($_POST['wave_id']), false);
            wp_die();
        }
        echo -1;
        wp_die();
    }


    public static function esr_set_course_passed()
    {
        if (isset($_POST['course_id'])) {
            $worker_ajax = new ESR_Ajax_Worker();
            wp_send_json($worker_ajax->change_course_passed(intval($_POST['course_id']), true));
            wp_die();
        }
        echo -1;
        wp_die();
    }


    public static function esr_set_course_active()
    {
        if (isset($_POST['course_id'])) {
            $worker_ajax = new ESR_Ajax_Worker();
            wp_send_json($worker_ajax->change_course_passed(intval($_POST['course_id']), false));
            wp_die();
        }
        echo -1;
        wp_die();
    }


    public static function esr_teacher_deactivate()
    {
        if (isset($_POST['teacher_id'])) {
            $worker_ajax = new ESR_Ajax_Worker();
            echo $worker_ajax->change_teacher_active(intval($_POST['teacher_id']), false);
            wp_die();
        }
        echo -1;
        wp_die();
    }


    public static function esr_teacher_activate()
    {
        if (isset($_POST['teacher_id'])) {
            $worker_ajax = new ESR_Ajax_Worker();
            echo $worker_ajax->change_teacher_active(intval($_POST['teacher_id']), true);
            wp_die();
        }
        echo -1;
        wp_die();
    }


    public static function esr_load_student_data()
    {
        if (isset($_POST['student_id'])) {
            $worker_ajax = new ESR_Ajax_Worker();
            echo $worker_ajax->esr_load_student_data(intval($_POST['student_id']));
            wp_die();
        }
        echo '';
        wp_die();
    }


    public static function esr_send_student_export_callback()
    {
        $status = false;

        if (isset($_POST['student_id'])) {
            $status = ESR()->email->esr_send_student_export_email(intval($_POST['student_id']));
        }

        if ($status) {
            wp_send_json([
                'type' => 'success',
                'message' => esc_html__('Email with export was sent.', 'easy-school-registration')
            ]);
        } else {
            wp_send_json([
                'type' => 'danger',
                'message' => esc_html__('Some error occured.', 'easy-school-registration')
            ]);
        }

        wp_die();
    }


    public static function esr_resend_confirmation_email_callback()
    {
        $status = false;

        if (isset($_POST['student_id'])) {
            $status = ESR()->email->send_course_registration_email(intval($_POST['student_id']));
        }

        wp_die();
    }


    public static function esr_set_free_registration_callback()
    {
        if (isset($_POST['registration_id'])) {
            apply_filters('esr_set_free_registration_value', [
                'registration_id' => intval($_POST['registration_id']),
                'free_registration_value' => ESR_Enum_Free_Registration::FREE
            ]);

            wp_send_json([
                'free_registration' => ESR_Enum_Free_Registration::FREE,
                'message' => ESR()->enum_free_registration->esr_get_change_message(ESR_Enum_Free_Registration::FREE),
                'type' => 'success',
            ]);
        }

        wp_die();
    }


    public static function esr_set_paid_registration_callback()
    {
        if (isset($_POST['registration_id'])) {
            apply_filters('esr_set_free_registration_value', [
                'registration_id' => intval($_POST['registration_id']),
                'free_registration_value' => ESR_Enum_Free_Registration::PAID
            ]);

            wp_send_json([
                'free_registration' => ESR_Enum_Free_Registration::PAID,
                'message' => ESR()->enum_free_registration->esr_get_change_message(ESR_Enum_Free_Registration::PAID),
                'type' => 'success',
            ]);
        }

        wp_die();
    }


    public static function esr_remove_course_forever_callback()
    {
        if (isset($_POST['course_id'])) {
            wp_send_json(apply_filters('esr_remove_course_forever', intval($_POST['course_id'])));
        }
        wp_die();
    }


    public static function esr_ics_generate_full_calendar_callback()
    {
        if (isset($_POST['wave_id'])) {
            wp_send_json(apply_filters('esr_ics_generate_full_calendar', intval($_POST['wave_id'])));
        }
        wp_die();
    }


    public static function esr_ics_generate_hall_calendar_callback()
    {
        if (isset($_POST['wave_id']) && isset($_POST['hall_key'])) {
            wp_send_json(apply_filters('esr_ics_generate_hall_calendar', intval($_POST['wave_id']), sanitize_text_field($_POST['hall_key'])));
        }
        wp_die();
    }


    public static function esr_ics_generate_student_calendar_callback()
    {
        if (isset($_POST['wave_id'])) {
            wp_send_json(apply_filters('esr_ics_generate_student_calendar', intval($_POST['wave_id']), get_current_user_id()));
        }
        wp_die();
    }


    public static function esr_ics_generate_teacher_calendar_callback()
    {
        if (isset($_POST['wave_id'])) {
            wp_send_json(apply_filters('esr_ics_generate_teacher_calendar', intval($_POST['wave_id']), get_current_user_id()));
        }
        wp_die();
    }


    public static function esr_tinymce_load_settings_callback()
    {
        $settings = json_decode("{}");
        $settings->waves = ESR()->wave->esr_load_tinymce_events();
        $settings->styles = ESR()->schedule_style->get_items_for_tinymce();
        $settings->groups = ESR()->course_group->get_items_for_tinymce();

        wp_send_json($settings);
        wp_die();
    }


    public static function esr_forgive_payment_callback()
    {
        if (isset($_POST['payment_id'])) {
            wp_send_json(apply_filters('esr_forgive_payment', intval($_POST['payment_id'])));
        }
        wp_die();
    }


    public static function esr_disable_student_registrations_callback()
    {
        if (isset($_POST['user_id'])) {
            wp_send_json(apply_filters('esr_disable_student_registrations', intval($_POST['user_id'])));
        }
        wp_die();
    }


    public static function esr_enable_student_registrations_callback()
    {
        if (isset($_POST['user_id'])) {
            wp_send_json(apply_filters('esr_enable_student_registrations', intval($_POST['user_id'])));
        }
        wp_die();
    }


    public static function esr_remove_wave_callback()
    {
        if (isset($_POST['wave_id']) && current_user_can('esr_wave_edit') && ESR()->wave->esr_can_be_removed(intval($_POST['wave_id']))) {
            wp_send_json(apply_filters('esr_remove_wave', intval($_POST['wave_id'])));
            wp_die();
        }
        echo -1;
        wp_die();
    }

    public static function esr_toggle_passed_courses_callback()
    {
        if (isset($_POST['is_checked'])) {
            update_user_meta(get_current_user_id(), 'esr_hide_passed_courses', filter_var($_POST['is_checked'], FILTER_VALIDATE_BOOLEAN ));
        }
    }

    public static function esr_save_student_note_callback()
    {
        if (isset($_POST['note']) && isset($_POST['user_id'])) {
            update_user_meta(intval($_POST['user_id']), 'esr_student_note', sanitize_textarea_field(esc_html(htmlspecialchars($_POST['note']))));
        }
    }
}

//Frontend
add_action('wp_ajax_esr_process_registration', ['ESR_Ajax', 'esr_process_registration_callback']);
add_action('wp_ajax_nopriv_esr_process_registration', ['ESR_Ajax', 'esr_process_registration_callback']);

//Backend
add_action('wp_ajax_esr_remove_user_course_registration', ['ESR_Ajax', 'esr_remove_user_course_registration_callback']);
add_action('wp_ajax_esr_remove_registration_forever', ['ESR_Ajax', 'esr_remove_registration_forever_callback']);
add_action('wp_ajax_esr_add_user_course_registration', ['ESR_Ajax', 'esr_add_user_course_registration_callback']);
add_action('wp_ajax_esr_edit_registration', ['ESR_Ajax', 'esr_edit_registration_callback']);
add_action('wp_ajax_esr_payment_save_payment', ['ESR_Ajax', 'esr_payment_save_payment_callback']);
add_action('wp_ajax_esr_set_wave_passed', ['ESR_Ajax', 'esr_set_wave_passed']);
add_action('wp_ajax_esr_set_wave_active', ['ESR_Ajax', 'esr_set_wave_active']);
add_action('wp_ajax_esr_set_course_passed', ['ESR_Ajax', 'esr_set_course_passed']);
add_action('wp_ajax_esr_set_course_active', ['ESR_Ajax', 'esr_set_course_active']);
add_action('wp_ajax_esr_teacher_deactivate', ['ESR_Ajax', 'esr_teacher_deactivate']);
add_action('wp_ajax_esr_teacher_activate', ['ESR_Ajax', 'esr_teacher_activate']);
add_action('wp_ajax_esr_load_student_data', ['ESR_Ajax', 'esr_load_student_data']);
add_action('wp_ajax_esr_send_student_export', ['ESR_Ajax', 'esr_send_student_export_callback']);
add_action('wp_ajax_esr_resend_confirmation_email', ['ESR_Ajax', 'esr_resend_confirmation_email_callback']);
add_action('wp_ajax_esr_set_free_registration', ['ESR_Ajax', 'esr_set_free_registration_callback']);
add_action('wp_ajax_esr_set_paid_registration', ['ESR_Ajax', 'esr_set_paid_registration_callback']);
add_action('wp_ajax_esr_remove_course_forever', ['ESR_Ajax', 'esr_remove_course_forever_callback']);
add_action('wp_ajax_esr_remove_wave', ['ESR_Ajax', 'esr_remove_wave_callback']);
add_action('wp_ajax_esr_toggle_passed_courses', ['ESR_Ajax', 'esr_toggle_passed_courses_callback']);
add_action('wp_ajax_esr_save_student_note', ['ESR_Ajax', 'esr_save_student_note_callback']);

// Student debts
add_action('wp_ajax_esr_forgive_payment', ['ESR_Ajax', 'esr_forgive_payment_callback']);
add_action('wp_ajax_esr_disable_student_registrations', ['ESR_Ajax', 'esr_disable_student_registrations_callback']);
add_action('wp_ajax_esr_enable_student_registrations', ['ESR_Ajax', 'esr_enable_student_registrations_callback']);

add_action('wp_ajax_esr_tinymce_load_settings', ['ESR_Ajax', 'esr_tinymce_load_settings_callback']);

//Generate ICS calendars
add_action('wp_ajax_esr_ics_generate_full_calendar', ['ESR_Ajax', 'esr_ics_generate_full_calendar_callback']);
add_action('wp_ajax_esr_ics_generate_hall_calendar', ['ESR_Ajax', 'esr_ics_generate_hall_calendar_callback']);
add_action('wp_ajax_esr_ics_generate_student_calendar', ['ESR_Ajax', 'esr_ics_generate_student_calendar_callback']);
add_action('wp_ajax_esr_ics_generate_teacher_calendar', ['ESR_Ajax', 'esr_ics_generate_teacher_calendar_callback']);