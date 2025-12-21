<?php
/**
 * Timer Display Handler
 *
 * @package TobaltLessonsTimer
 * @since 1.0.0
 * Author: Tobalt — https://tobalt.lt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Tobalt_Timer_Display {

	public function __construct() {
		// Auto-injection can be added here if needed
		// For now, timer is only shown via shortcode
	}

	public static function format_time( $seconds ) {
		$hours = floor( $seconds / 3600 );
		$minutes = floor( ( $seconds % 3600 ) / 60 );
		$seconds = $seconds % 60;

		if ( $hours > 0 ) {
			return sprintf( '%02d:%02d:%02d', $hours, $minutes, $seconds );
		} else {
			return sprintf( '%02d:%02d', $minutes, $seconds );
		}
	}

	public static function get_lesson_label( $number ) {
		/* translators: %d: lesson number */
		return sprintf( __( 'Lesson %d', 'tobalt-lessons-timer' ), $number );
	}
}
