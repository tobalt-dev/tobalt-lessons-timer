<?php
/**
 * Uninstall handler for Tobalt Lessons Timer
 *
 * Cleans up plugin data when the plugin is uninstalled.
 *
 * Author: Tobalt — https://tobalt.lt
 *
 * @package Tobalt_Lessons_Timer
 */

// Prevent direct access and verify it's a legitimate uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options.
delete_option( 'tobalt_timer_settings' );

// Clean up rate limiting transients.
global $wpdb;

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query(
	"DELETE FROM {$wpdb->options}
	 WHERE option_name LIKE '_transient_tobalt_timer_rl_%'
	 OR option_name LIKE '_transient_timeout_tobalt_timer_rl_%'"
);
