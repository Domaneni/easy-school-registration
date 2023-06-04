<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class ESR_Payment_Email_Templater
{

    private $worker_email;


    public function __construct()
    {
        $this->worker_email = new ESR_Email_Worker();
    }


    public function send_payment_email($wave_id, $courses, $payment)
    {
        $subject = apply_filters('esr_get_payment_email_title', stripcslashes(ESR()->settings->esr_get_option('payment_email_subject')), $wave_id);
        $body = apply_filters('esr_get_payment_email_body', stripcslashes(ESR()->settings->esr_get_option('payment_email_body', null)), $wave_id);

        return $this->send_email($wave_id, $courses, $payment, $body, $subject);
    }


    public function send_payment_reminder_email($wave_id, $courses, $payment)
    {
        $subject = stripcslashes(ESR()->settings->esr_get_option('payment_reminder_email_subject'));
        $body = stripcslashes(ESR()->settings->esr_get_option('payment_reminder_email_body', null));

        return $this->send_email($wave_id, $courses, $payment, $body, $subject);
    }


    public function send_email($wave_id, $courses, $payment, $body, $subject)
    {
        $student = get_user_by('id', $payment->user_id);

        $payment->variable_symbol = $wave_id . sprintf("%04s", $student->ID);

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
            $tags = ESR()->tags->get_tags('email_payment');

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
                            $parameter = $courses;
                            break;
                        }
                        case 'to_pay':
                        {
                            $parameter = $payment->to_pay;
                            break;
                        }
                        case 'payment_value':
                        {
                            $parameter = (($payment->payment === null) || ($payment->payment === '')) ? 0 : $payment->payment;
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

            return $this->worker_email->send_email($student->user_email, $subject, $body, '_payment');
        }

        return false;
    }

}