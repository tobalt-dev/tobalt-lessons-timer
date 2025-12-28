<?php
/**
 * Admin Interface
 *
 * @package TobaltLessonsTimer
 * @since 1.0.0
 * Author: Tobalt — https://tobalt.lt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Tobalt_Timer_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'wp_ajax_tobalt_timer_save_settings', array( $this, 'ajax_save_settings' ) );
		add_action( 'wp_ajax_tobalt_timer_delete_profile', array( $this, 'ajax_delete_profile' ) );
		add_action( 'wp_ajax_tobalt_timer_get_profile', array( $this, 'ajax_get_profile' ) );
	}

	public function add_menu_page() {
		if ( function_exists( 'tobalt_hub_is_active' ) && tobalt_hub_is_active() ) {
			add_submenu_page(
				'tobalt-hub',
				__( 'Tobalt Lessons Timer', 'tobalt-lessons-timer' ),
				__( 'Lessons Timer', 'tobalt-lessons-timer' ),
				'manage_options',
				'tobalt-timer',
				array( $this, 'render_settings_page' )
			);
		} else {
			add_menu_page(
				__( 'Tobalt Lessons Timer', 'tobalt-lessons-timer' ),
				__( 'Lessons Timer', 'tobalt-lessons-timer' ),
				'manage_options',
				'tobalt-timer',
				array( $this, 'render_settings_page' ),
				'dashicons-clock',
				30
			);
		}
	}

	public function render_settings_page() {
		$settings = get_option( 'tobalt_timer_settings', array() );
		$profiles = isset( $settings['profiles'] ) ? $settings['profiles'] : array();
		$active_profile = isset( $settings['active_profile'] ) ? $settings['active_profile'] : '';

		include TOBALT_TIMER_DIR . 'admin/views/schedule-editor.php';
	}

	public function ajax_save_settings() {
		check_ajax_referer( 'tobalt_timer_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'tobalt-lessons-timer' ) ) );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below
		$profile_id = isset( $_POST['profile_id'] ) ? sanitize_text_field( wp_unslash( $_POST['profile_id'] ) ) : '';
		$profile_name = isset( $_POST['profile_name'] ) ? sanitize_text_field( wp_unslash( $_POST['profile_name'] ) ) : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized in array_map
		$days = isset( $_POST['days'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['days'] ) ) : array();
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized in sanitize_lessons
		$lessons = isset( $_POST['lessons'] ) ? $this->sanitize_lessons( wp_unslash( $_POST['lessons'] ) ) : array();
		$active = isset( $_POST['active'] ) && $_POST['active'] === 'true';

		if ( empty( $profile_name ) ) {
			wp_send_json_error( array( 'message' => __( 'Profile name is required', 'tobalt-lessons-timer' ) ) );
		}

		$settings = get_option( 'tobalt_timer_settings', array() );

		if ( empty( $profile_id ) ) {
			$profile_id = sanitize_title( $profile_name ) . '-' . time();
		}

		if ( ! isset( $settings['profiles'] ) ) {
			$settings['profiles'] = array();
		}

		$settings['profiles'][ $profile_id ] = array(
			'name' => $profile_name,
			'days' => $days,
			'lessons' => $lessons,
		);

		if ( $active ) {
			$settings['active_profile'] = $profile_id;
		}

		update_option( 'tobalt_timer_settings', $settings );

		// Clear schedule cache
		Tobalt_Timer_Schedule_Manager::clear_cache();

		wp_send_json_success( array(
			'message' => __( 'Profile saved successfully', 'tobalt-lessons-timer' ),
			'profile_id' => $profile_id,
		) );
	}

	public function ajax_delete_profile() {
		check_ajax_referer( 'tobalt_timer_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'tobalt-lessons-timer' ) ) );
		}

		$profile_id = isset( $_POST['profile_id'] ) ? sanitize_text_field( wp_unslash( $_POST['profile_id'] ) ) : '';

		if ( empty( $profile_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid profile ID', 'tobalt-lessons-timer' ) ) );
		}

		$settings = get_option( 'tobalt_timer_settings', array() );

		if ( isset( $settings['profiles'][ $profile_id ] ) ) {
			unset( $settings['profiles'][ $profile_id ] );

			if ( $settings['active_profile'] === $profile_id ) {
				$settings['active_profile'] = '';
			}

			update_option( 'tobalt_timer_settings', $settings );

			// Clear schedule cache
			Tobalt_Timer_Schedule_Manager::clear_cache();

			wp_send_json_success( array( 'message' => __( 'Profile deleted', 'tobalt-lessons-timer' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Profile not found', 'tobalt-lessons-timer' ) ) );
		}
	}

	/**
	 * Get profile data for editing
	 */
	public function ajax_get_profile() {
		check_ajax_referer( 'tobalt_timer_admin', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'tobalt-lessons-timer' ) ) );
		}

		$profile_id = isset( $_POST['profile_id'] ) ? sanitize_text_field( wp_unslash( $_POST['profile_id'] ) ) : '';

		if ( empty( $profile_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid profile ID', 'tobalt-lessons-timer' ) ) );
		}

		$settings = get_option( 'tobalt_timer_settings', array() );

		if ( ! isset( $settings['profiles'][ $profile_id ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Profile not found', 'tobalt-lessons-timer' ) ) );
		}

		$profile = $settings['profiles'][ $profile_id ];
		$active_profile = isset( $settings['active_profile'] ) ? $settings['active_profile'] : '';

		wp_send_json_success( array(
			'profile_id'   => $profile_id,
			'profile_name' => $profile['name'],
			'days'         => $profile['days'] ?? array(),
			'lessons'      => $profile['lessons'] ?? array(),
			'is_active'    => $profile_id === $active_profile,
		) );
	}

	private function sanitize_lessons( $lessons ) {
		$sanitized = array();

		foreach ( $lessons as $lesson ) {
			$start_time = sanitize_text_field( $lesson['start_time'] ?? '' );
			$duration   = intval( $lesson['duration'] ?? 45 );

			// Validate time format (HH:MM, 00:00 to 23:59)
			if ( ! preg_match( '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $start_time ) ) {
				$start_time = '08:00'; // Default to 8:00 if invalid
			}

			// Validate duration range (1-240 minutes)
			if ( $duration < 1 || $duration > 240 ) {
				$duration = 45; // Default to 45 minutes if out of range
			}

			$sanitized[] = array(
				'start_time' => $start_time,
				'duration'   => $duration,
			);
		}

		return $sanitized;
	}
}
