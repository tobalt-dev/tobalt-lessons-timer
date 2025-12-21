<?php
/**
 * Activator
 *
 * @package TobaltLessonsTimer
 * @since 1.0.0
 * Author: Tobalt — https://tobalt.lt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Tobalt_Timer_Activator {

	public static function activate() {
		self::set_default_settings();
		flush_rewrite_rules();
	}

	private static function set_default_settings() {
		$default_settings = array(
			'active_profile' => '',
			'profiles' => array(),
		);

		if ( ! get_option( 'tobalt_timer_settings' ) ) {
			add_option( 'tobalt_timer_settings', $default_settings );
		}
	}
}
