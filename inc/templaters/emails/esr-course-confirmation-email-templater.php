<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ESR_Course_Confirmation_Email_Templater
{

    private $worker_email;


    public function __construct()
    {
        $this->worker_email = new ESR_Email_Worker();
    }


    public function send_email($registration_data)
    {
        $registration = ESR()->registration->get_registration_with_reg_position($registration_data['reg_id']);
        $floating_price = null;

        $user_info = get_userdata(intval($registration->user_id));
        $student_email = $user_info->user_email;
        $course_data = ESR()->course->get_course_data($registration->course_id);

        $subject = apply_filters('esr_get_confirmation_email_title', stripcslashes(ESR()->settings->esr_get_option('confirmation_email_subject')), $course_data->wave_id);
        $body = apply_filters('esr_get_confirmation_email_body', stripcslashes(ESR()->settings->esr_get_option('confirmation_email_body', null)), $course_data->wave_id);

        if (intval(ESR()->settings->esr_get_option('floating_price_enabled', -1)) !== -1) {
            $previous_price = isset($registration_data['previous_price']) ? $registration_data['previous_price'] : 0;
            $actual_price = isset($registration_data['actual_price']) ? $registration_data['actual_price'] : apply_filters('esr_get_student_payment', ['wave_id' => $course_data->wave_id, 'user_id' => intval($registration->user_id)])['to_pay'];

            $floating_price = floatval($actual_price) - floatval($previous_price);
            $floating_price = $floating_price > 0 ? $floating_price : 0;
        }


        if (!empty($subject)) {
            $tags = ESR()->tags->get_tags('confirmation_email_title');

            foreach ($tags as $key => $tag) {
                $parameter = null;
                if (isset($tag['parameter'])) {
                    switch ($tag['parameter']) {
                        case 'wave_id':
                        {
                            $parameter = $course_data->wave_id;
                            break;
                        }
                        case 'course_title':
                        {
                            $parameter = $course_data->title;
                            break;
                        }
                        case 'user_registration_info':
                        {
                            $parameter = $user_info;
                            break;
                        }
                    }

                    $subject = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $subject, $parameter);
                } else {
                    $subject = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $subject);
                }
            }
        }

        if (!empty($body)) {
            $tags = ESR()->tags->get_tags('email_confirmation');

            foreach ($tags as $key => $tag) {
                $parameter = null;
                if (isset($tag['parameter'])) {
                    switch ($tag['parameter']) {
                        case 'course':
                        {
                            $parameter = $course_data;
                            break;
                        }
                        case 'price':
                        {
                            $parameter = $course_data->real_price;
                            break;
                        }
                        case 'floating_price':
                        {
                            $parameter = $floating_price;
                            break;
                        }
                        case 'variable_symbol':
                        {
                            $parameter = $course_data->wave_id . sprintf("%04s", $registration->user_id);
                            break;
                        }
                        case 'registration_variable_symbol':
                        {
                            $parameter = $course_data->wave_id . sprintf("%04s", $registration->user_id) . $registration->reg_position;
                            break;
                        }
                        case 'wave_id':
                        {
                            $parameter = $course_data->wave_id;
                            break;
                        }
                        case 'hall':
                        {
                            $parameter = $course_data->hall_key;
                            break;
                        }
                        case 'dancing_as':
                        {
                            $parameter = $registration->dancing_as;
                            break;
                        }
                        case 'user_registration_info':
                        {
                            $parameter = $user_info;
                            break;
                        }
                        case 'course_days_times':
                        {
                            $parameter = ESR()->multiple_dates->esr_get_all_course_dates($registration->course_id);
                            break;
                        }
                    }

                    $body = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $body, $parameter);
                } else {
                    $body = call_user_func(['ESR_Settings_Tag_Templater', $tag['function']], $tag, $body);
                }
            }

            return $this->worker_email->send_email($student_email, $subject, $body, '_course_confirmation');
        }

        return false;
    }
}
