<?php
/**
 * Tobalt Lessons Timer
 *
 * Real-time lesson schedule timer with countdown and current lesson indicator.
 *
 * @package TobaltLessonsTimer
 * @version 1.0.0
 * @author Tobalt — https://tobalt.lt
 *
 * @wordpress-plugin
 * Plugin Name: Tobalt Lessons Timer
 * Plugin URI: https://tobalt.lt/tobalt-lessons-timer
 * Description: Real-time lesson schedule timer displaying current lesson, time remaining, and next lesson preview. Perfect for educational institutions. Works standalone or integrates with Tobalt School Pack hub.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Tobalt
 * Author URI: https://tobalt.lt
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tobalt-lessons-timer
 * Domain Path: /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'TOBALT_TIMER_VERSION', '1.0.0' );
define( 'TOBALT_TIMER_DIR', plugin_dir_path( __FILE__ ) );
define( 'TOBALT_TIMER_URL', plugin_dir_url( __FILE__ ) );
define( 'TOBALT_TIMER_BASENAME', plugin_basename( __FILE__ ) );

function tobalt_timer_activate() {
	require_once TOBALT_TIMER_DIR . 'includes/class-activator.php';
	Tobalt_Timer_Activator::activate();
}
register_activation_hook( __FILE__, 'tobalt_timer_activate' );

function tobalt_timer_deactivate() {
	require_once TOBALT_TIMER_DIR . 'includes/class-deactivator.php';
	Tobalt_Timer_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'tobalt_timer_deactivate' );

function tobalt_timer_run() {
	require_once TOBALT_TIMER_DIR . 'includes/class-plugin.php';
	$plugin = Tobalt_Timer_Plugin::get_instance();
	$plugin->run();
}
add_action( 'plugins_loaded', 'tobalt_timer_run' );

add_action( 'init', function() {
	if ( function_exists( 'tobalt_hub_register_plugin' ) ) {
		tobalt_hub_register_plugin( 'lessons-timer', array(
			'name'            => __( 'Tobalt Lessons Timer', 'tobalt-lessons-timer' ),
			'version'         => TOBALT_TIMER_VERSION,
			'icon'            => 'dashicons-clock',
			'description'     => __( 'Real-time lesson schedule timer', 'tobalt-lessons-timer' ),
			'stats_callback'  => 'tobalt_timer_get_stats',
			'admin_url'       => admin_url( 'admin.php?page=tobalt-timer' ),
		) );
	}
}, 20 );

function tobalt_timer_get_stats() {
	$settings = get_option( 'tobalt_timer_settings', array() );
	$profiles = isset( $settings['profiles'] ) ? $settings['profiles'] : array();
	$active_profile = isset( $settings['active_profile'] ) ? $settings['active_profile'] : '';

	return array(
		'count' => count( $profiles ),
		'Profiles' => count( $profiles ),
		'Active' => $active_profile ? 1 : 0,
	);
}
