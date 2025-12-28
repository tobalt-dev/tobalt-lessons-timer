<?php
/**
 * Deactivator
 *
 * @package TobaltLessonsTimer
 * @since 1.0.0
 * Author: Tobalt — https://tobalt.lt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Tobalt_Timer_Deactivator {

	public static function deactivate() {
		flush_rewrite_rules();
	}
}
