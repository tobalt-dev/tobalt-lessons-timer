<?php
/**
 * Timer Display Template
 *
 * @package TobaltLessonsTimer
 * @since 1.0.0
 * Author: Tobalt — https://tobalt.lt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$show_next = isset( $atts['show_next'] ) && $atts['show_next'] === 'yes';
$compact = isset( $atts['compact'] ) && $atts['compact'] === 'yes';
?>

<div class="tobalt-timer <?php echo $compact ? 'compact' : ''; ?>" data-show-next="<?php echo $show_next ? '1' : '0'; ?>">
	<?php if ( $data['status'] === 'no_schedule' || $data['status'] === 'no_school' || $data['status'] === 'no_lessons' ) : ?>
		<div class="timer-message">
			<p><?php echo esc_html( $data['message'] ); ?></p>
		</div>

	<?php elseif ( $data['status'] === 'lesson' ) : ?>
		<div class="timer-current">
			<div class="timer-label"><?php echo esc_html( Tobalt_Timer_Display::get_lesson_label( $data['current']['number'] ) ); ?></div>
			<div class="timer-countdown" data-end-time="<?php echo esc_attr( $data['current']['end_time'] ); ?>">
				<div class="countdown-display">
					<?php echo esc_html( Tobalt_Timer_Display::format_time( $data['current']['time_remaining'] ) ); ?>
				</div>
				<div class="countdown-label"><?php esc_html_e( 'Time remaining', 'tobalt-lessons-timer' ); ?></div>
			</div>
			<div class="timer-time-range">
				<?php
				printf(
					/* translators: 1: start time, 2: end time */
					esc_html__( '%1$s – %2$s', 'tobalt-lessons-timer' ),
					esc_html( $data['current']['start_time'] ),
					esc_html( $data['current']['end_time'] )
				);
				?>
			</div>
		</div>

		<?php if ( $show_next && ! empty( $data['next'] ) ) : ?>
			<div class="timer-next">
				<div class="next-label"><?php esc_html_e( 'Next:', 'tobalt-lessons-timer' ); ?></div>
				<div class="next-info">
					<?php
					printf(
						/* translators: 1: lesson number, 2: start time */
						esc_html__( 'Lesson %1$d at %2$s', 'tobalt-lessons-timer' ),
						esc_html( $data['next']['number'] ),
						esc_html( $data['next']['start_time'] )
					);
					?>
				</div>
			</div>
		<?php endif; ?>

	<?php elseif ( $data['status'] === 'break' ) : ?>
		<div class="timer-break">
			<div class="timer-label"><?php esc_html_e( 'Break', 'tobalt-lessons-timer' ); ?></div>
			<div class="timer-countdown" data-start-time="<?php echo esc_attr( $data['next']['start_time'] ); ?>">
				<div class="countdown-display">
					<?php echo esc_html( Tobalt_Timer_Display::format_time( $data['time_until'] ) ); ?>
				</div>
				<div class="countdown-label"><?php esc_html_e( 'Until next lesson', 'tobalt-lessons-timer' ); ?></div>
			</div>
			<div class="timer-next-info">
				<?php
				printf(
					/* translators: 1: lesson number, 2: start time */
					esc_html__( 'Lesson %1$d starts at %2$s', 'tobalt-lessons-timer' ),
					esc_html( $data['next']['number'] ),
					esc_html( $data['next']['start_time'] )
				);
				?>
			</div>
		</div>

	<?php elseif ( $data['status'] === 'after_school' ) : ?>
		<div class="timer-message after-school">
			<p><?php echo esc_html( $data['message'] ); ?></p>
		</div>
	<?php endif; ?>
</div>
