<?php
/**
 * Rate limiter for AJAX requests
 *
 * Author: Tobalt — https://tobalt.lt
 *
 * @package Tobalt_Lessons_Timer
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Simple rate limiter using transients
 */
class Tobalt_Timer_Rate_Limiter {

	/**
	 * Check if request should be rate limited
	 *
	 * @param string $action Action identifier.
	 * @param int    $max_requests Maximum requests allowed.
	 * @param int    $window_seconds Time window in seconds.
	 * @return bool True if rate limited, false if allowed.
	 */
	public static function is_rate_limited( $action, $max_requests = 60, $window_seconds = 60 ) {
		$ip = self::get_client_ip();
		$key = 'tobalt_timer_rl_' . md5( $action . '_' . $ip );

		$data = get_transient( $key );

		if ( false === $data ) {
			// First request in window
			set_transient( $key, array(
				'count'   => 1,
				'started' => time(),
			), $window_seconds );
			return false;
		}

		if ( $data['count'] >= $max_requests ) {
			return true;
		}

		// Increment counter
		$data['count']++;
		set_transient( $key, $data, $window_seconds );

		return false;
	}

	/**
	 * Get client IP address
	 *
	 * @return string
	 */
	private static function get_client_ip() {
		// Only trust REMOTE_ADDR for security (prevent IP spoofing)
		return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
	}

	/**
	 * Clear rate limit data for cleanup
	 */
	public static function cleanup() {
		global $wpdb;

		// Delete expired transients related to rate limiting
		$wpdb->query(
			"DELETE FROM {$wpdb->options}
			 WHERE option_name LIKE '_transient_tobalt_timer_rl_%'
			 OR option_name LIKE '_transient_timeout_tobalt_timer_rl_%'"
		);
	}
}
