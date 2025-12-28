<?php
/**
 * Main Plugin Class
 *
 * @package TobaltLessonsTimer
 * @since 1.0.0
 * Author: Tobalt — https://tobalt.lt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Tobalt_Timer_Plugin {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->load_dependencies();
		$this->setup_hooks();
	}

	private function load_dependencies() {
		require_once TOBALT_TIMER_DIR . 'includes/class-rate-limiter.php';
		require_once TOBALT_TIMER_DIR . 'includes/class-schedule-manager.php';

		if ( is_admin() ) {
			require_once TOBALT_TIMER_DIR . 'admin/class-timer-admin.php';
		}

		require_once TOBALT_TIMER_DIR . 'frontend/class-timer-shortcode.php';
		require_once TOBALT_TIMER_DIR . 'frontend/class-timer-display.php';
	}

	private function setup_hooks() {
		add_action( 'init', array( $this, 'init_components' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	public function init_components() {
		new Tobalt_Timer_Schedule_Manager();

		if ( is_admin() ) {
			new Tobalt_Timer_Admin();
		}

		new Tobalt_Timer_Shortcode();
		new Tobalt_Timer_Display();
	}

	public function run() {
		do_action( 'tobalt_timer_initialized' );
	}

	public function enqueue_frontend_assets() {
		if ( ! function_exists( 'tobalt_hub_is_active' ) ) {
			wp_enqueue_style(
				'tobalt-timer-design-system',
				TOBALT_TIMER_URL . 'assets/css/design-system.css',
				array(),
				TOBALT_TIMER_VERSION
			);
		}

		wp_enqueue_style(
			'tobalt-timer',
			TOBALT_TIMER_URL . 'assets/css/timer.css',
			array(),
			TOBALT_TIMER_VERSION
		);

		wp_enqueue_script(
			'tobalt-timer',
			TOBALT_TIMER_URL . 'assets/js/timer.js',
			array( 'jquery' ),
			TOBALT_TIMER_VERSION,
			true
		);

		wp_localize_script(
			'tobalt-timer',
			'tobaltTimer',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'tobalt_timer' ),
			)
		);
	}

	public function enqueue_admin_assets( $hook ) {
		if ( 'toplevel_page_tobalt-timer' !== $hook ) {
			return;
		}

		if ( ! function_exists( 'tobalt_hub_is_active' ) ) {
			wp_enqueue_style(
				'tobalt-timer-design-system',
				TOBALT_TIMER_URL . 'assets/css/design-system.css',
				array(),
				TOBALT_TIMER_VERSION
			);
		}

		wp_enqueue_style(
			'tobalt-timer-admin',
			TOBALT_TIMER_URL . 'assets/css/timer-admin.css',
			array(),
			TOBALT_TIMER_VERSION
		);

		wp_enqueue_script(
			'tobalt-timer-admin',
			TOBALT_TIMER_URL . 'assets/js/timer-admin.js',
			array( 'jquery' ),
			TOBALT_TIMER_VERSION,
			true
		);

		wp_localize_script(
			'tobalt-timer-admin',
			'tobaltTimerAdmin',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'tobalt_timer_admin' ),
				'strings' => array(
					'newProfile'       => __( 'Naujas profilis', 'tobalt-lessons-timer' ),
					'minutes'          => __( 'min.', 'tobalt-lessons-timer' ),
					'confirmDelete'    => __( 'Ištrinti profilį "%s"?', 'tobalt-lessons-timer' ),
					'errorDeleting'    => __( 'Klaida trinant profilį', 'tobalt-lessons-timer' ),
					'errorSaving'      => __( 'Klaida išsaugant profilį', 'tobalt-lessons-timer' ),
				),
			)
		);
	}

	public function load_textdomain() {
		load_plugin_textdomain(
			'tobalt-lessons-timer',
			false,
			dirname( TOBALT_TIMER_BASENAME ) . '/languages'
		);
	}
}
