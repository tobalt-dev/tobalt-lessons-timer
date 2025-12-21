<?php
/**
 * Schedule Manager
 *
 * Manages lesson schedules and calculates current/next lessons.
 *
 * @package TobaltLessonsTimer
 * @since 1.0.0
 * Author: Tobalt — https://tobalt.lt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Tobalt_Timer_Schedule_Manager {

	public function __construct() {
		add_action( 'wp_ajax_tobalt_timer_get_current', array( $this, 'ajax_get_current_lesson' ) );
		add_action( 'wp_ajax_nopriv_tobalt_timer_get_current', array( $this, 'ajax_get_current_lesson' ) );
	}

	/**
	 * Get active schedule profile.
	 *
	 * @return array|false Schedule data or false.
	 */
	public function get_active_schedule() {
		$settings = get_option( 'tobalt_timer_settings', array() );
		$active_profile = isset( $settings['active_profile'] ) ? $settings['active_profile'] : '';

		if ( empty( $active_profile ) || ! isset( $settings['profiles'][ $active_profile ] ) ) {
			return false;
		}

		return $settings['profiles'][ $active_profile ];
	}

	/**
	 * Get current lesson information.
	 *
	 * @return array Current lesson data.
	 */
	public function get_current_lesson() {
		$schedule = $this->get_active_schedule();

		if ( ! $schedule ) {
			return array(
				'status' => 'no_schedule',
				'message' => __( 'Tvarkaraštis nesukonfigūruotas', 'tobalt-lessons-timer' ),
			);
		}

		$current_time = current_time( 'H:i' );
		$current_day = strtolower( date( 'l' ) ); // monday, tuesday, etc.

		// Check if today is a school day.
		if ( ! isset( $schedule['days'] ) || ! in_array( $current_day, $schedule['days'] ) ) {
			return array(
				'status' => 'no_school',
				'message' => __( 'Šiandien nėra pamokų', 'tobalt-lessons-timer' ),
			);
		}

		$lessons = isset( $schedule['lessons'] ) ? $schedule['lessons'] : array();

		if ( empty( $lessons ) ) {
			return array(
				'status' => 'no_lessons',
				'message' => __( 'Pamokos nesukonfigūruotos', 'tobalt-lessons-timer' ),
			);
		}

		// Find current or next lesson.
		$current_lesson = null;
		$next_lesson = null;

		foreach ( $lessons as $index => $lesson ) {
			$start_time = $lesson['start_time'];
			$duration = isset( $lesson['duration'] ) ? intval( $lesson['duration'] ) : 45;
			$end_time = date( 'H:i', strtotime( $start_time ) + ( $duration * 60 ) );

			if ( $current_time >= $start_time && $current_time < $end_time ) {
				// Currently in this lesson.
				$current_lesson = array(
					'number' => $index + 1,
					'start_time' => $start_time,
					'end_time' => $end_time,
					'duration' => $duration,
					'time_remaining' => $this->calculate_time_remaining( $end_time ),
				);

				// Get next lesson.
				if ( isset( $lessons[ $index + 1 ] ) ) {
					$next_lesson = array(
						'number' => $index + 2,
						'start_time' => $lessons[ $index + 1 ]['start_time'],
					);
				}

				break;
			} elseif ( $current_time < $start_time ) {
				// Upcoming lesson (break time).
				$next_lesson = array(
					'number' => $index + 1,
					'start_time' => $start_time,
				);
				break;
			}
		}

		if ( $current_lesson ) {
			return array(
				'status' => 'lesson',
				'current' => $current_lesson,
				'next' => $next_lesson,
			);
		} elseif ( $next_lesson ) {
			return array(
				'status' => 'break',
				'next' => $next_lesson,
				'time_until' => $this->calculate_time_until( $next_lesson['start_time'] ),
			);
		} else {
			return array(
				'status' => 'after_school',
				'message' => __( 'Pamokos baigėsi', 'tobalt-lessons-timer' ),
			);
		}
	}

	/**
	 * Calculate time remaining until end time.
	 *
	 * @param string $end_time End time (H:i format).
	 * @return int Seconds remaining.
	 */
	private function calculate_time_remaining( $end_time ) {
		$current_timestamp = current_time( 'timestamp' );
		$end_timestamp = strtotime( date( 'Y-m-d' ) . ' ' . $end_time );
		return max( 0, $end_timestamp - $current_timestamp );
	}

	/**
	 * Calculate time until start time.
	 *
	 * @param string $start_time Start time (H:i format).
	 * @return int Seconds until.
	 */
	private function calculate_time_until( $start_time ) {
		$current_timestamp = current_time( 'timestamp' );
		$start_timestamp = strtotime( date( 'Y-m-d' ) . ' ' . $start_time );
		return max( 0, $start_timestamp - $current_timestamp );
	}

	/**
	 * AJAX handler for getting current lesson.
	 *
	 * @return void
	 */
	public function ajax_get_current_lesson() {
		check_ajax_referer( 'tobalt_timer', 'nonce' );

		$data = $this->get_current_lesson();
		wp_send_json_success( $data );
	}
}
