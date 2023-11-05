<?php
include_once ESR_PLUGIN_PATH . '/tests/esr-base-test.php';
include_once ESR_PLUGIN_PATH . '/docs/examples/esr_registration_validation_registration.php';

class ESRRegistrationValidationRegistrationTest extends WP_UnitTestCase {

    private $base_test;


    public function __construct() {
        parent::__construct();
        $this->base_test = new ESR_Base_Test();
    }

    public function setUp(): void {
        parent::setUp();
        $this->base_test->delete_all_data();
        $this->base_test->setUp();
    }

    public function test_no_courses() {
        $data = (object) [];

        $this->assertTrue(esr_registration_validation_registration_callback(true, $data));
    }

    public function test_empty_courses() {
        $data = (object) ['courses' => []];

        $this->assertTrue(esr_registration_validation_registration_callback(true, $data));
    }

    public function test_no_user_email() {
        $data = (object) ['courses' => [], 'user_info' => []];

        $this->assertTrue(esr_registration_validation_registration_callback(true, $data));
    }

    public function test_too_much_courses() {
        $data = (object) ['courses' => [1 => [], 2 => [], 3 => [], 4 => []], 'user_info' => (object) ['email' => 'unittest@easyschoolregistration.com']];

        $this->assertTrue(esr_registration_validation_registration_callback(true, $data));
    }

    public function test_new_user_on_limit() {
        $data = (object) ['courses' => [1 => [], 2 => [], 3 => []], 'user_info' => (object) ['email' => 'unittest@easyschoolregistration.com']];

        $this->assertFalse(esr_registration_validation_registration_callback(true, $data));
    }

    public function test_new_user_already_registered_over_limit() {
        $wave_id    = $this->base_test->add_wave();
        $course_id  = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);
        $course2_id  = $this->base_test->add_course($wave_id);
        $course3_id  = $this->base_test->add_course($wave_id);
        $course4_id  = $this->base_test->add_course($wave_id);

        $this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

        $data = (object) ['courses' => [$course2_id => [], $course3_id => [], $course4_id => []], 'user_info' => (object) ['email' => 'unittest@easyschoolregistration.com']];

        $this->assertTrue(esr_registration_validation_registration_callback(true, $data));
    }

    public function test_new_user_already_registered_on_limit() {
        $wave_id    = $this->base_test->add_wave();
        $course_id  = $this->base_test->add_course($wave_id, ['max_leaders' => 10, 'max_followers' => 10, 'day' => ESR_Enum_Day::TUESDAY]);
        $course2_id  = $this->base_test->add_course($wave_id);
        $course3_id  = $this->base_test->add_course($wave_id);

        $this->base_test->process_registration($wave_id, $course_id, ESR_Dancing_As::LEADER);

        $data = (object) ['courses' => [$course2_id => [], $course3_id => []], 'user_info' => (object) ['email' => 'unittest@easyschoolregistration.com']];

        $this->assertFalse(esr_registration_validation_registration_callback(true, $data));
    }

}
