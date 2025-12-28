/**
 * Tobalt Lessons Timer - Admin Script
 *
 * @package TobaltLessonsTimer
 * @since 1.0.0
 * Author: Tobalt — https://tobalt.lt
 */

(function($) {
	'use strict';

	let currentProfileId = '';
	let profilesData = {};

	function showEditor() {
		$('.tobalt-profiles-list').hide();
		$('.tobalt-profile-editor').show();
	}

	function hideEditor() {
		$('.tobalt-profile-editor').hide();
		$('.tobalt-profiles-list').show();
		resetForm();
	}

	function resetForm() {
		$('#profile-form')[0].reset();
		$('#profile_id').val('');
		currentProfileId = '';
		$('#lessons-container').empty();
		$('.editor-title').text(tobaltTimerAdmin.strings?.newProfile || 'New Profile');
	}

	function loadProfile(profileId) {
		const $profileItem = $(`.profile-item[data-profile-id="${profileId}"]`);

		// Parse profile data from DOM
		$.ajax({
			url: tobaltTimerAdmin.ajaxurl,
			type: 'POST',
			data: {
				action: 'tobalt_timer_get_profile',
				nonce: tobaltTimerAdmin.nonce,
				profile_id: profileId
			},
			success: function(response) {
				if (response.success && response.data) {
					const profile = response.data;
					currentProfileId = profileId;

					$('#profile_id').val(profileId);
					$('#profile_name').val(profile.name);

					// Check days
					$('input[name="days[]"]').prop('checked', false);
					if (profile.days) {
						profile.days.forEach(function(day) {
							$(`input[name="days[]"][value="${day}"]`).prop('checked', true);
						});
					}

					// Load lessons
					$('#lessons-container').empty();
					if (profile.lessons) {
						profile.lessons.forEach(function(lesson) {
							addLessonRow(lesson.start_time, lesson.duration);
						});
					}

					// Set active checkbox
					const isActive = $profileItem.hasClass('active');
					$('#active').prop('checked', isActive);

					$('.editor-title').text(profile.name);
					showEditor();
				}
			}
		});
	}

	function addLessonRow(startTime = '', duration = 45) {
		const lessonNumber = $('#lessons-container .lesson-row').length + 1;

		const html = `
			<div class="lesson-row">
				<span class="lesson-number">${lessonNumber}.</span>
				<input type="time" name="lessons[${lessonNumber - 1}][start_time]" value="${startTime}" required>
				<input type="number" name="lessons[${lessonNumber - 1}][duration]" value="${duration}" min="1" max="240" required>
				<span class="duration-label">min.</span>
				<button type="button" class="button remove-lesson">×</button>
			</div>
		`;

		$('#lessons-container').append(html);
		updateLessonNumbers();
	}

	function updateLessonNumbers() {
		$('#lessons-container .lesson-row').each(function(index) {
			$(this).find('.lesson-number').text((index + 1) + '.');
			$(this).find('input[type="time"]').attr('name', `lessons[${index}][start_time]`);
			$(this).find('input[type="number"]').attr('name', `lessons[${index}][duration]`);
		});
	}

	// Event handlers
	$('.new-profile').on('click', function() {
		resetForm();
		showEditor();
	});

	$('.cancel-edit').on('click', function(e) {
		e.preventDefault();
		hideEditor();
	});

	$(document).on('click', '.edit-profile', function() {
		const profileId = $(this).closest('.profile-item').data('profile-id');
		loadProfile(profileId);
	});

	$(document).on('click', '.delete-profile', function() {
		const $item = $(this).closest('.profile-item');
		const profileId = $item.data('profile-id');
		const profileName = $item.find('strong').text();

		if (!confirm(`Delete profile "${profileName}"?`)) {
			return;
		}

		$.ajax({
			url: tobaltTimerAdmin.ajaxurl,
			type: 'POST',
			data: {
				action: 'tobalt_timer_delete_profile',
				nonce: tobaltTimerAdmin.nonce,
				profile_id: profileId
			},
			success: function(response) {
				if (response.success) {
					location.reload();
				} else {
					alert(response.data?.message || 'Error deleting profile');
				}
			}
		});
	});

	$('.add-lesson').on('click', function() {
		addLessonRow();
	});

	$(document).on('click', '.remove-lesson', function() {
		$(this).closest('.lesson-row').remove();
		updateLessonNumbers();
	});

	$('#profile-form').on('submit', function(e) {
		e.preventDefault();

		const formData = $(this).serializeArray();
		const data = {
			action: 'tobalt_timer_save_settings',
			nonce: tobaltTimerAdmin.nonce
		};

		formData.forEach(function(field) {
			if (field.name.includes('[')) {
				// Handle array fields
				if (!data[field.name.split('[')[0]]) {
					data[field.name.split('[')[0]] = [];
				}
				if (field.name.includes('lessons')) {
					const matches = field.name.match(/\[(\d+)\]\[(\w+)\]/);
					if (matches) {
						const index = parseInt(matches[1]);
						const key = matches[2];
						if (!data.lessons) data.lessons = [];
						if (!data.lessons[index]) data.lessons[index] = {};
						data.lessons[index][key] = field.value;
					}
				} else {
					data[field.name.split('[')[0]].push(field.value);
				}
			} else {
				data[field.name] = field.value;
			}
		});

		$.ajax({
			url: tobaltTimerAdmin.ajaxurl,
			type: 'POST',
			data: data,
			success: function(response) {
				if (response.success) {
					location.reload();
				} else {
					alert(response.data?.message || 'Error saving profile');
				}
			}
		});
	});

})(jQuery);
