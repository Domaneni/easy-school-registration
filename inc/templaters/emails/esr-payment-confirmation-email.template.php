<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ESR_Template_Payment_Confirmation_Email
{

    private $worker_email;


    public function __construct()
    {
        $this->worker_email = new ESR_Email_Worker();
    }


    public function send_email($wave_id, $email)
    {
        $student = get_user_by('email', trim($email));
        $payment = ESR()->payment->get_payment_by_wave_and_user($wave_id, $student->ID);

        $subject = apply_filters('esr_get_payment_confirmation_email_title', stripcslashes(ESR()->settings->esr_get_option('payment_confirmation_email_subject')), $wave_id);
        $body = apply_filters('esr_get_payment_confirmation_email_body', stripcslashes(ESR()->settings->esr_get_option('payment_confirmation_email_body', null)), $wave_id);


        if ($subject) {
            $tags = ESR()->tags->get_tags('email_title');

            foreach ($tags as $key => $tag) {
                $parameter = null;
                if (isset($tag['parameter'])) {
                    switch ($tag['parameter']) {
                        case 'wave_id':
                        {
                            $parameter = $wave_id;
                            break;
                        }
                        case 'user_registration_info':
                        {
                            $parameter = $student;
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
            $tags = ESR()->tags->get_tags('email_payment_confirmation');

            foreach ($tags as $key => $tag) {
                $parameter = null;
                $template = 'ESR_Settings_Tag_Templater';

                if (isset($tag['template'])) {
                    $template = $tag['template'];
                }

                if (isset($tag['parameter'])) {
                    switch ($tag['parameter']) {
                        case 'courses_list':
                        {
                            $parameter = ESR()->registration->get_confirmed_registrations_by_wave_and_user($wave_id, $student->ID);;
                            break;
                        }
                        case 'to_pay':
                        {
                            $parameter = $payment->to_pay;
                            break;
                        }
                        case 'payment_value':
                        {
                            $parameter = $payment->payment;
                            break;
                        }
                        case 'payment':
                        {
                            $parameter = $payment;
                            break;
                        }
                        case 'variable_symbol':
                        {
                            $parameter = $wave_id . sprintf("%04s", $student->ID);
                            break;
                        }
                        case 'wave_id':
                        {
                            $parameter = $wave_id;
                            break;
                        }
                        case 'user_registration_info':
                        {
                            $parameter = $student;
                            break;
                        }
                    }

                    $body = call_user_func([$template, $tag['function']], $tag, $body, $parameter);
                } else {
                    $body = call_user_func([$template, $tag['function']], $tag, $body);
                }
            }

            return $this->worker_email->send_email($email, $subject, $body, '_payment_confirmation');
        }

        return false;
    }

}