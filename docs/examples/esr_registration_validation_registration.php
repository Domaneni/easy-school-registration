<?php

function esr_registration_validation_registration_callback($stop_registration, $data) {
    $limit = 3;

    if (!isset($data->courses) || !$data->courses || !isset( $data->user_info->email ) ) { //STOP: No Courses selected or email is empty
        return true;
    }

    $course_count = count((array)$data->courses);

    if ($course_count > $limit) { //STOP: Student selected more than $limit courses
        return true;
    }

    if (!email_exists($data->user_info->email)) { //CONTINUE: Student selected <= $limit courses and this is first registration
        return false;
    }

    $user    = get_user_by( 'email', $data->user_info->email );
    $user_id = $user->ID;
    $wave_ids = [];

    global $wpdb;

    foreach ( $data->courses as $course_id => $course ) {
        $course_data = ESR()->course->get_course_data($course_id);

        if (!isset($wave_ids[$course_data->wave_id])) {
            $wave_id = $course_data->wave_id;

            $registrations_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}esr_course_registration AS cr JOIN {$wpdb->prefix}esr_course_data AS cd ON cr.course_id = cd.id WHERE cd.wave_id = %d AND cr.user_id = %d AND cr.status IN (%d, %d)", [intval($wave_id), intval($user_id), ESR_Registration_Status::CONFIRMED, ESR_Registration_Status::WAITING]));

            if (($registrations_count + $course_count) > $limit) {
                return true; //STOP: Student already has some registrations and with this it will be more than $limit
            }
        }
    }

    return false; //Default validation if there are some courses selected
}

add_filter('esr_registration_validation_registration', 'esr_registration_validation_registration_callback', 30, 2);