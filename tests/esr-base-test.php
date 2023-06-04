<?php
/**
 * Class SampleTest
 *
 * @package ESR
 */

/**
 * Sample test case.
 */
class ESR_Base_Test {

	/** var int */
	private $teacher_first;

	/** var int */
	private $teacher_second;


	public function __construct() {
	}


	public function setUp() {
		global $wpdb;

		global $esr_settings;
		$esr_settings['floating_price_enabled'] = true;

		$this->teacher_first = $wpdb->get_var("SELECT ID FROM {$wpdb->users} WHERE user_email LIKE 'teacher_first@b.cz'");

		if (!$this->teacher_first) {
			$wpdb->insert($wpdb->users, [
				'user_login'    => 'teacher_first',
				'user_pass'     => 'teacher_first',
				'user_nicename' => 'teacher_first',
				'user_email'    => 'teacher_first@b.cz'
			]);

			$this->teacher_first = $wpdb->insert_id;
		}

		$this->teacher_second = $wpdb->get_var("SELECT ID FROM {$wpdb->users} WHERE user_email LIKE 'teacher_second@b.cz'");

		if (!$this->teacher_second) {
			$wpdb->insert($wpdb->users, [
				'user_login'    => 'teacher_second',
				'user_pass'     => 'teacher_second',
				'user_nicename' => 'teacher_second',
				'user_email'    => 'teacher_second@b.cz'
			]);

			$this->teacher_second = $wpdb->insert_id;
		}

		$u = wp_get_current_user();
		$u->add_role('administrator');
	}


	public function delete_all_data() {
		global $wpdb;
		global $esr_settings;

		$esr_settings = [];
		delete_option('esr_settings');

		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esr_course_summary");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esr_user_payment");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esr_course_registration");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esr_course_data");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esr_wave_data");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esr_teacher_data");

		$wpdb->query("TRUNCATE TABLE {$wpdb->usermeta}");
		$wpdb->query("TRUNCATE TABLE {$wpdb->users}");

		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esrd_discount");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esrd_wave_discount");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esrd_time_discount");
		$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}esrd_checkbox_discount");

		wp_cache_flush();
	}


	public function add_wave($title = 'Wave', $registration_from = null, $registration_to = null, $registration_from_time = null, $registration_to_time = null) {
		global $wpdb;
		$worker_wave = new ESR_Wave_Worker();

		if ($registration_from == null) {
			$registration_from = date('Y-m-d');
			$registration_from_time = date('H:i:s');
		}
		if ($registration_to == null) {
			$registration_to = date('Y-m-d', strtotime("+1 day"));
			$registration_to_time = date('H:i:s', strtotime("+1 day"));
		}

		$worker_wave->process_wave([
			'title'             => $title,
			'registration_from' => $registration_from,
			'registration_from_time' => $registration_from_time,
			'registration_to'   => $registration_to,
			'registration_to_time'   => $registration_to_time,
		]);

		return $wpdb->insert_id;
	}


	public function update_wave($wave_id, $data) {
		global $wpdb;
		$wpdb->update($wpdb->prefix . 'esr_wave_data', $data, [
			'id' => $wave_id
		]);
	}


	public function add_course($wave_id = null, $data = []) {
		global $wpdb;
		$worker = new ESR_Course_Worker();

		if ($wave_id == null) {
			$wave_id = $this->add_wave();
		}

		if (!isset($data['price'])) {
			$data['price'] = 800;
		}

		if (!isset($data['group_id'])) {
			$data['group_id'] = 1;
		}

		if (!isset($data['time_from'])) {
			$data['time_from'] = '10:00';
		}

		if (!isset($data['time_to'])) {
			$data['time_to'] = '15:00';
		}

		$worker->process_course([
			                        'wave_id' => $wave_id,
			                        'title'   => 'Course name'
		                        ] + $data);

		return $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}esr_course_data WHERE wave_id = %d ORDER BY id DESC LIMIT 1", [$wave_id]));
	}


	public function update_course($course_id, $data) {
		$worker = new ESR_Course_Worker();
		$worker->process_course(['course_id' => $course_id] + $data);
	}


	public function get_user_id_by_email($email = 'unittest@easyschoolregistration.com') {
		global $wpdb;

		return $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->users} WHERE user_email LIKE %s", [$email]));
	}


	public function process_registration($wave_id, $course_id, $dancing_as, $user_email = 'unittest@easyschoolregistration.com', $checkbox_discount = 0) {
		$worker_registration = new ESR_Registration_Worker();

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name                             = 'A';
		$registration->user_info->surname                          = 'B';
		$registration->user_info->email                            = $user_email;
		$registration->user_info->phone                            = '1';
		$registration->user_info->{"checkbox_discount_{$wave_id}"} = $checkbox_discount;

		$registration->courses[$course_id] = new stdClass();

		if ($dancing_as !== null) {
			$registration->courses[$course_id]->dancing_as = $dancing_as;
		}

		return $worker_registration->process_registration($registration);
	}


	public function process_registration_new($wave_id, $course_id, $data) {
		$worker_registration = new ESR_Registration_Worker();

		$registration            = new stdClass();
		$registration->user_info = new stdClass();
		$registration->courses   = [];

		$registration->user_info->name                             = isset($data['name']) ? $data['name'] : 'John';
		$registration->user_info->surname                          = isset($data['surname']) ? $data['surname'] : 'Born';
		$registration->user_info->email                            = isset($data['user_email']) ? $data['user_email'] : 'unittest@easyschoolregistration.com';
		$registration->user_info->phone                            = isset($data['phone']) ? $data['phone'] : '132456789';
		$registration->user_info->{"checkbox_discount_{$wave_id}"} = isset($data['checkbox_discount']) ? $data['checkbox_discount'] : 0;

		$registration->courses[$course_id] = new stdClass();

		if (isset($data['dancing_as'])) {
			$registration->courses[$course_id]->dancing_as = $data['dancing_as'];
		}

		return $worker_registration->process_registration($registration);
	}


	public function process_registrations($registration) {
		$worker_registration = new ESR_Registration_Worker();

		return $worker_registration->process_registration($registration);
	}


	public function get_registrations() {
		global $wpdb;

		return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}esr_course_registration");
	}


	public function get_users() {
		global $wpdb;

		return $wpdb->get_results("SELECT * FROM $wpdb->users");
	}


	public function fetch_users_count() {
		return count($this->get_users());
	}


	public function fetch_registrations_count() {
		return count($this->get_registrations());
	}


	public function update_settings($new_settings) {
		global $esr_settings;

		update_option('esr_settings', $new_settings);
		$esr_settings = $new_settings;
	}


	public function load_user_payment_by_email($user_email) {
		global $wpdb;

		return $wpdb->get_row($wpdb->prepare("SELECT up.* FROM {$wpdb->prefix}esr_user_payment AS up JOIN {$wpdb->users} AS u ON u.ID = up.user_id AND u.user_email = %s", [$user_email]));
	}
}
