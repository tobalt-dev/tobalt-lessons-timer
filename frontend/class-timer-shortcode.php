<?php
/**
 * Timer Shortcode
 *
 * @package TobaltLessonsTimer
 * @since 1.0.0
 * Author: Tobalt — https://tobalt.lt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Tobalt_Timer_Shortcode {

	public function __construct() {
		add_shortcode( 'tobalt_lessons_timer', array( $this, 'render_timer' ) );
		add_shortcode( 'tobalt_timer', array( $this, 'render_timer' ) );
	}

	public function render_timer( $atts ) {
		$atts = shortcode_atts( array(
			'show_next' => 'yes',
			'compact' => 'no',
		), $atts );

		$manager = new Tobalt_Timer_Schedule_Manager();
		$data = $manager->get_current_lesson();

		ob_start();
		include TOBALT_TIMER_DIR . 'frontend/views/timer.php';
		return ob_get_clean();
	}
}
