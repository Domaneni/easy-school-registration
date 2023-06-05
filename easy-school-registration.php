<?php
/**
 * Plugin Name:     Easy School Registration
 * Plugin URI:      https://easyschoolregistration.com/
 * Description:     Tools to help you run your school better
 *
 * Version:         3.9.4
 * Tested up to:    6.2.2
 *
 * Author:          Zbynek Nedoma
 * Author URI:      https://domaneni.cz
 * Plugin Slug:     easy-school-registration
 *
 * Text Domain:     easy-school-registration
 * Domain Path:     /languages
 *
 * License: GPL 3
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Easy_School_Registration' ) ) {

	/**
	 * Main Easy_School_Registration Class.
	 *
	 */
	final class Easy_School_Registration {

		/**
		 * @var Easy_School_Registration
		 */
		private static $instance;

		/**
		 * @var object|ESR_Course
		 */
		public $course;

		/**
		 * @var object|ESR_Enum_Course_Group
		 */
		public $course_group;

		/**
		 * @var object|ESR_Enum_Course_Level
		 */
		public $course_level;

		/**
		 * @var object|ESR_Course_Summary
		 */
		public $course_summary;

		/**
		 * @var object|ESR_Currency
		 */
		public $currency;

		/**
		 * @var object|ESR_Dancing_As
		 */
		public $dance_as;

		/**
		 * @var object|ESR_Enum_Day
		 */
		public $day;

		/**
		 * @var object|ESR_Email
		 */
		public $email;

		/**
		 * @var object|ESR_Enum_Free_Registration
		 */
		public $enum_free_registration;

		/**
		 * @var object|ESR_Hall
		 */
		public $hall;

		/**
		 * @var object|ESR_Enum_Hover_Option
		 */
		public $hover_option;

		/**
		 * @var object|ESR_Log
		 */
		public $log;

		/**
		 * @var object|ESR_Multiple_Dates
		 */
		public $multiple_dates;

		/**
		 * @var object|ESR_Enum_Pairing_Mode
		 */
		public $pairing_mode;

		/**
		 * @var object|ESR_Payment
		 */
		public $payment;

		/**
		 * @var object|ESR_Enum_Payment_Emails
		 */
		public $payment_emails;

		/**
		 * @var object|ESR_Enum_Payment
		 */
		public $payment_status;

		/**
		 * @var object|ESR_Enum_Payment_Type
		 */
		public $payment_type;

		/**
		 * @var object|ESR_Registration
		 */
		public $registration;

		/**
		 * @var object|ESR_Registration_Status
		 */
		public $registration_status;

		/**
		 * @var object|ESR_Role
		 */
		public $role;

		/**
		 * @var object|ESR_Schedule
		 */
		public $schedule;

		/**
		 * @var object|ESR_Enum_Schedule_Style
		 */
		public $schedule_style;

		/**
		 * @var object|ESR_Settings
		 */
		public $settings;

		/**
		 * @var object|ESR_Basic_Statistics
		 */
		public $statistics;

		/**
		 * @var object|ESR_Tags
		 */
		public $tags;

		/**
		 * @var object|ESR_Teacher
		 */
		public $teacher;

		/**
		 * @var object|ESR_User
		 */
		public $user;

		/**
		 * @var object|ESR_Wave
		 */
		public $wave;


		/**
		 * Main Easy_School_Registration Instance.
		 *
		 * Insures that only one instance of Easy_School_Registration exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @static
		 * @staticvar array $instance
		 * @return object|Easy_School_Registration
		 * @see ESR()
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Easy_School_Registration ) ) {
				self::$instance = new Easy_School_Registration;
				self::$instance->setup_constants();

				self::$instance->includes();

                register_activation_hook(ESR_PLUGIN_FILE, array('ESR_Database', 'esr_database_install_callback'));

				self::$instance->init();

				self::$instance->settings               = new ESR_Settings();
				self::$instance->course                 = new ESR_Course();
				self::$instance->course_group           = new ESR_Enum_Course_Group();
				self::$instance->course_level           = new ESR_Enum_Course_Level();
				self::$instance->course_summary         = new ESR_Course_Summary();
				self::$instance->currency               = new ESR_Currency();
				self::$instance->dance_as               = new ESR_Dancing_As();
				self::$instance->day                    = new ESR_Enum_Day();
				self::$instance->email                  = new ESR_Email();
				self::$instance->enum_free_registration = new ESR_Enum_Free_Registration();
				self::$instance->hall                   = new ESR_Hall();
				self::$instance->hover_option           = new ESR_Enum_Hover_Option();
				self::$instance->log                    = new ESR_Log();
				self::$instance->multiple_dates         = new ESR_Multiple_Dates();
				self::$instance->pairing_mode           = new ESR_Enum_Pairing_Mode();
				self::$instance->payment                = new ESR_Payment();
				self::$instance->payment_emails         = new ESR_Enum_Payment_Emails();
				self::$instance->payment_status         = new ESR_Enum_Payment();
				self::$instance->payment_type           = new ESR_Enum_Payment_Type();
				self::$instance->registration           = new ESR_Registration();
				self::$instance->registration_status    = new ESR_Registration_Status();
				self::$instance->role                   = new ESR_Role();
				self::$instance->schedule               = new ESR_Schedule();
				self::$instance->schedule_style         = new ESR_Enum_Schedule_Style();
				self::$instance->statistics             = new ESR_Basic_Statistics();
				self::$instance->tags                   = new ESR_Tags();
				self::$instance->teacher                = new ESR_Teacher();
				self::$instance->user                   = new ESR_User();
				self::$instance->wave                   = new ESR_Wave();

				new ESR_Cron_Payment_Email();
				new ESR_Cron_Payment_Reminder_Email();
				new ESR_Cron_Waiting_Email();

				new ESR_Ribbon();
			}

			return self::$instance;
		}


		/**
		 * Throw error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @return void
		 * @since 1.6
		 * @access protected
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'easy-school-registration' ), '1.0.0' );
		}


		/**
		 * Disable unserializing of the class.
		 *
		 * @return void
		 * @since 1.6
		 * @access protected
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'easy-school-registration' ), '1.0.0' );
		}


		private function setup_constants() {
			define( 'ESR_SLUG', 'esr' );
			define( 'ESR_VERSION', '3.9.4' );
			// Plugin Root File.
			if ( ! defined( 'ESR_PLUGIN_FILE' ) ) {
				define( 'ESR_PLUGIN_FILE', __FILE__ );
			}

			define( 'ESR_PLUGIN_PATH', dirname( __FILE__ ) );
			define( 'ESR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			define( 'ESR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}


		/**
		 * Include required files.
		 *
		 * @access private
		 * @return void
		 * @since 1.4
		 */
		private function includes() {
			global $esr_settings;

			require_once ESR_PLUGIN_PATH . '/inc/database/esr-database.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-admin.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-ajax.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-cron.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-basic-statistics.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-currency.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-email.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-front-end-functions.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-gutenberg.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-hall.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-ics-generator.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-log.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-registration.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-ribbon.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-settings.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-shortcodes.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-schedule.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-user.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-payment.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-tags.class.php';
			require_once ESR_PLUGIN_PATH . '/inc/class/esr-multiple-dates.class.php';

			require_once ESR_PLUGIN_PATH . '/inc/class/esr-tiny-mce.php';

			require_once ESR_PLUGIN_PATH . '/inc/cron/esr-payment-email.cron.php';
			require_once ESR_PLUGIN_PATH . '/inc/cron/esr-payment-reminder-email.cron.php';
			require_once ESR_PLUGIN_PATH . '/inc/cron/esr-waiting-email.cron.php';

			// Enums
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-db.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-course-group.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-course-level.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-dancing-as.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-day.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-free-registration.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-hover-option.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-pairing-mode.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-payment.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-payment-emails.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-payment-type.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-registration-status.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-role.enum.php';
			require_once ESR_PLUGIN_PATH . '/inc/enums/esr-schedule-style.enum.php';

			// Models
			require_once ESR_PLUGIN_PATH . '/inc/models/esr-course.php';
			require_once ESR_PLUGIN_PATH . '/inc/models/esr-course-summary.php';
			require_once ESR_PLUGIN_PATH . '/inc/models/esr-fields.php';
			require_once ESR_PLUGIN_PATH . '/inc/models/esr-teacher.php';
			require_once ESR_PLUGIN_PATH . '/inc/models/esr-wave.php';

			//Support
			require_once ESR_PLUGIN_PATH . '/inc/support/esr-user-role-editor.php';

			// Templater
			require_once ESR_PLUGIN_PATH . '/inc/templaters/esr-registration.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/add-over-limit/esr-add-over-limit.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/add-over-limit/subblock/esr-add-over-limit-form.subblock.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/course-in-numbers/esr-course-in-numbers.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/course-in-numbers/esr-course-in-numbers-course.helper.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/course-in-numbers/esr-course-in-numbers-table.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/course-in-numbers/esr-course-in-numbers-statistics.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/courses/esr-courses.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/courses/subblock/esr-courses-edit-form.subblock.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/courses/subblock/esr-courses-table.subblock.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/payment-emails/esr-payment-emails.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/payments/esr-payments.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/payments/esr-payment-statistics.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/payments/tabs/esr-payment-debts-tab.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/payments/tabs/esr-payment-table-tab.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/payments/subblock/esr-payment-form.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/payments/subblock/esr-payment-table-header.php';
            require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/payments/subblock/esr-payment-action-box.php';
            require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/payments/subblock/esr-payment-table-column-content.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/registration/esr-registrations.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/registration/esr-registration-statistics.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/registration/subblock/esr-registration-table.subblock.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/registration/subblock/esr-registration-form.subblock.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/settings/esr.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/settings/esr-settings-helper.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/school/esr-school.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/students/esr-students.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/students/subblock/esr-students-table.subblock.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/teachers/esr-teachers.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/teachers/subblock/esr-teachers-edit-form.subblock.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/teachers/subblock/esr-teachers-table.subblock.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/waves/esr-waves.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/waves/subblock/esr-waves-table.subblock.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/administration/waves/subblock/esr-waves-edit-form.subblock.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/emails/esr-course-confirmation-email-templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/emails/esr-gdpr-email.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/emails/esr-payment-confirmation-email.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/emails/esr-payment-email-templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/emails/esr-registration-email.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/emails/esr-registration-note-email-templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/emails/esr-user-registration-email-templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/emails/esr-waiting-email-templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/frontend/course/esr-course-info.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/frontend/student_payment/esr-student-payment.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/schedule/filters/esr-schedule-group.filter.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/schedule/filters/esr-schedule-level.filter.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/schedule/filters/esr-schedule-wave.filter.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/schedule/esr-schedule.helper.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/schedule/esr-schedule.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/schedule/esr-schedule-by-days.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/schedule/esr-schedule-by-hours.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/schedule/esr-schedule-by-hours-compact.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/schedule/esr-schedule-mobile.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/teacherinfo/esr-teacher-info.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/teacherinfo/esr-teacher-info-courses-table.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/user/esr-user.template.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/userinfo/courses/esr.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/userinfo/courses/subblock/esr-user-info-courses-table.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/userinfo/courses/subblock/esr-user-courses-schedule.subblock.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/userinfo/payments/esr.templater.php';

			require_once ESR_PLUGIN_PATH . '/inc/templaters/helpers/esr-all-waves-select.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/helpers/esr-registration-thank-you-text.helper.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/helpers/esr-settings-tag.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/helpers/esr-student-export.templater.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/helpers/esr-tooltip.templater.helper.php';
			require_once ESR_PLUGIN_PATH . '/inc/templaters/helpers/esr-wave-courses-select.template.php';

			// Other
			require_once ESR_PLUGIN_PATH . '/inc/esr-enqueue-scripts.php';

			// Workers
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-add-over-limit.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-ajax.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-course.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-course-in-numbers.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-debts.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-email.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-pairing.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-payment-emails.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-payments.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-registration.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-registration-couple.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-registration-solo.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-teacher.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-user.worker.php';
			require_once ESR_PLUGIN_PATH . '/inc/workers/esr-wave.worker.php';

			$settings_class = new ESR_Settings();
			$esr_settings   = $settings_class->esr_get_settings();
		}


        /**
         * Load actions
         *
         * @access private
         * @return void
         */
        private function init() {
            add_action( 'plugins_loaded', array( $this, 'load_text_domain' ), 99 );
        }


        /**
         * Load text domain
         *
         * @access public
         * @return void
         */
		public function load_text_domain() {
            $locale = apply_filters( 'plugin_locale', get_locale(), 'easy-school-registration' );

            $mofile = sprintf( '%1$s-%2$s.mo', 'easy-school-registration', $locale );
            $mofile_local  = ESR_PLUGIN_DIR . 'languages/' . $mofile;

            load_textdomain( 'easy-school-registration', $mofile_local );
            load_plugin_textdomain( 'easy-school-registration', false, dirname( plugin_basename( ESR_PLUGIN_DIR ) ) . '/languages/' );
		}

	}
}

function ESR() {
	return Easy_School_Registration::instance();
}

// Get ESR Running.
ESR();