<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ESR_Enqueue_Scripts {

	public static function esr_add_theme_scripts() {
		if ( intval( ESR()->settings->esr_get_option( 'preload_styles', - 1 ) ) === - 1 ) {
			if ( intval( ESR()->settings->esr_get_option( 'disable_styles', - 1 ) ) !== 1 ) {
				wp_enqueue_style( 'esr_page_style' );
			}
            wp_enqueue_script( 'esr_spin_js_script' );
            wp_enqueue_script( 'esr_page_script' );
			wp_localize_script( 'esr_page_script', 'esr_ajax_object', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );
		}
	}


	public static function esr_register_theme_scripts() {
		if ( intval( ESR()->settings->esr_get_option( 'preload_styles', - 1 ) ) === - 1 ) {
			if ( intval( ESR()->settings->esr_get_option( 'disable_styles', - 1 ) ) !== 1 ) {
				wp_register_style( 'esr_page_style', ESR_PLUGIN_URL . 'inc/assets/web/css/esr-style.min.css', [], ESR_VERSION );
			}
            wp_register_script( 'esr_spin_js_script', ESR_PLUGIN_URL . 'libs/spin/js/spin.min.js', [ 'jquery' ], ESR_VERSION );
            wp_register_script( 'esr_page_script', ESR_PLUGIN_URL . 'inc/assets/web/js/esr-script.min.js', [ 'jquery' ], ESR_VERSION );
			wp_localize_script( 'esr_page_script', 'esr_ajax_object', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );
		} else {
			if ( intval( ESR()->settings->esr_get_option( 'disable_styles', - 1 ) ) !== 1 ) {
				wp_enqueue_style( 'esr_page_style', ESR_PLUGIN_URL . 'inc/assets/web/css/esr-style.min.css', [], ESR_VERSION );
			}
            wp_enqueue_script( 'esr_spin_js_script', ESR_PLUGIN_URL . 'libs/spin/js/spin.min.js', [ 'jquery' ], ESR_VERSION );
            wp_enqueue_script( 'esr_page_script', ESR_PLUGIN_URL . 'inc/assets/web/js/esr-script.min.js', [ 'jquery' ], ESR_VERSION );
			wp_localize_script( 'esr_page_script', 'esr_ajax_object', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );
		}
	}


	public static function add_admin_scripts() {
		wp_enqueue_style( 'esr_menu_separator_style', ESR_PLUGIN_URL . 'inc/assets/admin/css/esr-menu-separator.min.css', [], ESR_VERSION );

		$esr_scripts = [
			ESR_Template_Registrations::MENU_SLUG       => [
				'esr_scripts_data_changing',
				'esr_scripts_default_admin',
				'esr_scripts_datatable',
				'esr_scripts_spin',
				'esr_scripts_notify',
				'esr_scripts_tippy',
			],
			ESR_Template_Courses_In_Numbers::MENU_SLUG  => [
				'esr_scripts_default_admin',
				'esr_scripts_datatable',
			],
			ESR_Payments_Templater::MENU_SLUG           => [
				'esr_scripts_default_admin',
				'esr_scripts_datatable',
			],
			ESR_Templater_Courses::MENU_SLUG            => [
				'esr_scripts_course_admin',
				'esr_scripts_datatable',
				'esr_scripts_notify',
				'esr_scripts_tippy',
				'esr_scripts_moment',
			],
			ESR_Templater_Waves::MENU_SLUG              => [
				'esr_scripts_wave_admin',
				'esr_scripts_data_changing',
				'esr_scripts_datatable',
				'esr_scripts_notify',
				'esr_scripts_tippy',
				'esr_scripts_ical',
			],
			ESR_Templater_Teachers::MENU_SLUG           => [
				'esr_scripts_teacher_admin',
				'esr_scripts_data_changing',
				'esr_scripts_datatable',
				'esr_scripts_notify',
				'esr_scripts_tippy',
			],
			ESR_Template_Add_Over_Limit::MENU_SLUG      => [
				'esr_scripts_default_admin',
			],
			ESR_Settings_Templater::MENU_SLUG           => [
				'esr_scripts_wp_color',
				'esr_scripts_default_admin',
				'esr_scripts_admin',
			],
			ESR_Template_Payment_Emails::MENU_SLUG      => [
				'esr_scripts_default_admin',
				'esr_scripts_datatable',
			],
			ESR_User_Info_Payments_Templater::MENU_SLUG => [
				'esr_scripts_default_admin',
				'esr_scripts_datatable',
			],
			ESR_User_Info_Courses_Templater::MENU_SLUG  => [
				'esr_scripts_default_admin',
				'esr_scripts_datatable',
				'esr_scripts_ical',
				'esr_scripts_student',
			],
			ESR_Teacher_Info_Template::MENU_SLUG        => [
				'esr_scripts_default_admin',
				'esr_scripts_datatable',
				'esr_scripts_ical',
			],
			ESR_Template_Students::MENU_SLUG            => [
				'esr_scripts_default_admin',
				'esr_scripts_datatable',
				'esr_scripts_notify',
				'esr_scripts_student_admin',
			],
			ESR_Admin::ADMIN_MENU_SLUG                  => [
				'esr_scripts_admin',
			],
		];


		foreach ( $esr_scripts as $key => $scripts ) {
			if ( self::check_page_base( $key ) ) {
				foreach ( $scripts as $script ) {
					do_action( $script );
				}
				continue;
			}
		}
	}


	private static function check_page_base( $base_to_check ) {
		return strpos( get_current_screen()->base, $base_to_check ) !== false;
	}


	public static function esr_scripts_data_changing_callback() {
		if ( current_user_can( 'esr_registration_edit' ) ) {
			wp_enqueue_script( 'esr_admin_data_changing_script', ESR_PLUGIN_URL . 'inc/assets/admin/js/esr-data-changing.js', [ 'jquery' ], ESR_VERSION );
		}
	}


	public static function esr_load_other_default_scripts() {
		wp_enqueue_style( 'esr_admin_style', ESR_PLUGIN_URL . 'inc/assets/admin/css/esr-admin-settings.min.css', [], ESR_VERSION );

		//wp_enqueue_style( 'esr_admin_bootstrap_style', ESR_PLUGIN_URL . 'libs/bootstrap/css/bootstrap-ofic.css', [], ESR_VERSION );
		//wp_enqueue_script( 'esr_bootstrap_script', ESR_PLUGIN_URL . 'libs/bootstrap/js/bootstrap.min.js', [ 'jquery' ], ESR_VERSION );
		wp_localize_script( 'esr_admin_ajax_script', 'esr_ajax_object', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ] );
	}


	public static function esr_scripts_default_admin_callback() {
		wp_enqueue_script( 'esr_admin_script', ESR_PLUGIN_URL . 'inc/assets/admin/js/esr-production.js', [ 'jquery' ], ESR_VERSION );
		self::esr_load_other_default_scripts();
	}


	public static function esr_scripts_course_admin_callback() {
		wp_enqueue_script( 'esr_course_admin_script', ESR_PLUGIN_URL . 'inc/assets/admin/js/esr-course-admin.js', [ 'jquery' ], ESR_VERSION );
		self::esr_load_other_default_scripts();
	}


	public static function esr_scripts_wave_admin_callback() {
		wp_enqueue_script( 'esr_wave_admin_script', ESR_PLUGIN_URL . 'inc/assets/admin/js/esr-wave-admin.js', [ 'jquery' ], ESR_VERSION );
		self::esr_load_other_default_scripts();
	}


	public static function esr_scripts_teacher_admin_callback() {
		wp_enqueue_script( 'esr_teacher_admin_script', ESR_PLUGIN_URL . 'inc/assets/admin/js/esr-teacher-admin.js', [ 'jquery' ], ESR_VERSION );
		self::esr_load_other_default_scripts();
	}


	public static function esr_scripts_datatable_callback() {
		wp_enqueue_script( 'esr_dataTables_script', ESR_PLUGIN_URL . 'libs/datatable/js/datatables.min.js', [ 'jquery' ], ESR_VERSION );
		wp_enqueue_style( 'esr_dataTables_bootstrap_style', ESR_PLUGIN_URL . 'libs/datatable/css/datatables.min.css', [], ESR_VERSION );
	}


	public static function esr_scripts_ical_callback() {
		wp_enqueue_script( 'esr_ical_script', ESR_PLUGIN_URL . 'libs/ical/ics.min.js', [ 'jquery' ], ESR_VERSION );
	}


	public static function esr_scripts_spin_callback() {
		wp_enqueue_script( 'esr_spin', ESR_PLUGIN_URL . 'libs/spin/js/spin.min.js', [ 'jquery' ], ESR_VERSION );
	}


	public static function esr_scripts_moment_callback() {
		wp_enqueue_script( 'esr_moment_script', ESR_PLUGIN_URL . 'libs/moment/moment-with-locales.min.js', [ 'jquery' ], ESR_VERSION );
		wp_enqueue_script( 'esr_moment_range_script', ESR_PLUGIN_URL . 'libs/moment/moment-range.js', [ 'jquery' ], ESR_VERSION );
	}


	public static function esr_scripts_tippy_callback() {
		wp_enqueue_script( 'esr_tippy_script', ESR_PLUGIN_URL . 'libs/tippy/tippy.js', [ 'jquery' ], ESR_VERSION );
		wp_enqueue_style( 'esr_tippy_style', ESR_PLUGIN_URL . 'libs/tippy/tippy.css', [], ESR_VERSION );
	}


	public static function esr_scripts_student_callback() {
		wp_enqueue_script( 'esr_student_admin_script', ESR_PLUGIN_URL . 'inc/assets/admin/js/esr-student-admin.js', [ 'jquery' ], ESR_VERSION );
		wp_enqueue_style( 'esr_student_admin_style', ESR_PLUGIN_URL . 'inc/assets/admin/css/esr-student-admin.min.css', [], ESR_VERSION );
	}


	public static function esr_scripts_student_admin_callback() {
		wp_enqueue_script( 'esr_admin_student_script', ESR_PLUGIN_URL . 'inc/assets/admin/js/esr-students.js', [ 'jquery' ], ESR_VERSION );
	}


	public static function esr_scripts_admin_callback() {
		wp_enqueue_style( 'esr_admin_style', ESR_PLUGIN_URL . 'inc/assets/admin/css/esr-admin-settings.min.css', [], ESR_VERSION );
	}


	public static function esr_scripts_wp_color_callback() {
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
	}


	public static function esr_add_custom_styles_callback() {
		do_action( 'esr_print_styles' );
	}


	public static function esr_enqueue_block_editor_assets_callbacks() {
		wp_enqueue_script( 'esr_wave_schedule_block_form', ESR_PLUGIN_URL . 'inc/assets/admin/gutenberg/esr-wave-schedule-block.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ), ESR_VERSION, false );
		wp_enqueue_script( 'esr_course_registration_block_form', ESR_PLUGIN_URL . 'inc/assets/admin/gutenberg/esr-course-registration-block.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor' ), ESR_VERSION, false );
		wp_enqueue_style( 'esr_wave_schedule_block_style', ESR_PLUGIN_URL . 'inc/assets/admin/css/esr-blocks-styles.min.css', [], ESR_VERSION );
		wp_localize_script( 'esr_wave_schedule_block_form', 'esr_block_form', [
			'waves' => ESR()->wave->esr_get_active_waves_data(),
			'styles' => ESR()->schedule_style->get_items_for_gutenberg(),
			'groups' => ESR()->course_group->get_items_for_gutenberg(),
			'hoverOptions' => ESR()->hover_option->get_items_for_gutenberg()
		] );
	}
}

add_action( 'enqueue_block_editor_assets', [ 'ESR_Enqueue_Scripts', 'esr_enqueue_block_editor_assets_callbacks' ] );

add_action( 'wp_footer', [ 'ESR_Enqueue_Scripts', 'esr_add_custom_styles_callback' ], 99 );

add_action( 'esr_load_external_scripts', [ 'ESR_Enqueue_Scripts', 'esr_add_theme_scripts' ] );
add_action( 'admin_enqueue_scripts', [ 'ESR_Enqueue_Scripts', 'add_admin_scripts' ] );
add_action( 'wp_enqueue_scripts', [ 'ESR_Enqueue_Scripts', 'esr_register_theme_scripts' ] );

//Script calls
add_action( 'esr_scripts_course_admin', [ 'ESR_Enqueue_Scripts', 'esr_scripts_course_admin_callback' ] );
add_action( 'esr_scripts_default_admin', [ 'ESR_Enqueue_Scripts', 'esr_scripts_default_admin_callback' ] );
add_action( 'esr_scripts_teacher_admin', [ 'ESR_Enqueue_Scripts', 'esr_scripts_teacher_admin_callback' ] );
add_action( 'esr_scripts_wave_admin', [ 'ESR_Enqueue_Scripts', 'esr_scripts_wave_admin_callback' ] );
add_action( 'esr_scripts_datatable', [ 'ESR_Enqueue_Scripts', 'esr_scripts_datatable_callback' ] );
add_action( 'esr_scripts_ical', [ 'ESR_Enqueue_Scripts', 'esr_scripts_ical_callback' ] );
add_action( 'esr_scripts_data_changing', [ 'ESR_Enqueue_Scripts', 'esr_scripts_data_changing_callback' ] );
add_action( 'esr_scripts_spin', [ 'ESR_Enqueue_Scripts', 'esr_scripts_spin_callback' ] );
add_action( 'esr_scripts_moment', [ 'ESR_Enqueue_Scripts', 'esr_scripts_moment_callback' ] );
add_action( 'esr_scripts_tippy', [ 'ESR_Enqueue_Scripts', 'esr_scripts_tippy_callback' ] );
add_action( 'esr_scripts_student', [ 'ESR_Enqueue_Scripts', 'esr_scripts_student_callback' ] );
add_action( 'esr_scripts_admin', [ 'ESR_Enqueue_Scripts', 'esr_scripts_admin_callback' ] );
add_action( 'esr_scripts_wp_color', [ 'ESR_Enqueue_Scripts', 'esr_scripts_wp_color_callback' ] );
add_action( 'esr_scripts_student_admin', [ 'ESR_Enqueue_Scripts', 'esr_scripts_student_admin_callback' ] );