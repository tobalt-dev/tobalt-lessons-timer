/**
 * Tobalt Lessons Timer - Frontend Script
 *
 * @package TobaltLessonsTimer
 * @since 1.0.0
 * Author: Tobalt — https://tobalt.lt
 */

(function($) {
	'use strict';

	let updateInterval;

	function updateCountdown() {
		$('.tobalt-timer .timer-countdown').each(function() {
			const $countdown = $(this);
			const endTime = $countdown.data('end-time');
			const startTime = $countdown.data('start-time');

			let targetTime;
			if (endTime) {
				targetTime = endTime;
			} else if (startTime) {
				targetTime = startTime;
			} else {
				return;
			}

			const now = new Date();
			const target = new Date();
			const timeParts = targetTime.split(':');
			target.setHours(parseInt(timeParts[0], 10));
			target.setMinutes(parseInt(timeParts[1], 10));
			target.setSeconds(0);

			let diff = Math.floor((target - now) / 1000);

			if (diff < 0) {
				// Time has passed, refresh data
				refreshTimerData();
				return;
			}

			const hours = Math.floor(diff / 3600);
			const minutes = Math.floor((diff % 3600) / 60);
			const seconds = diff % 60;

			let display;
			if (hours > 0) {
				display = String(hours).padStart(2, '0') + ':' +
				         String(minutes).padStart(2, '0') + ':' +
				         String(seconds).padStart(2, '0');
			} else {
				display = String(minutes).padStart(2, '0') + ':' +
				         String(seconds).padStart(2, '0');
			}

			$countdown.find('.countdown-display').text(display);
		});
	}

	function refreshTimerData() {
		$.ajax({
			url: tobaltTimer.ajaxurl,
			type: 'POST',
			data: {
				action: 'tobalt_timer_get_current',
				nonce: tobaltTimer.nonce
			},
			success: function(response) {
				if (response.success) {
					// Reload the page to update the timer display
					location.reload();
				}
			}
		});
	}

	function initTimer() {
		updateCountdown();
		updateInterval = setInterval(updateCountdown, 1000);
	}

	$(document).ready(function() {
		if ($('.tobalt-timer').length) {
			initTimer();
		}
	});

})(jQuery);
