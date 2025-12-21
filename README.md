# Tobalt Lessons Timer

Real-time lesson schedule timer with countdown and current lesson indicator.

## Features

- **Real-time Countdown:** Live countdown to end of current lesson or start of next lesson
- **Schedule Profiles:** Create multiple schedule profiles for different timetables
- **Flexible Scheduling:** Configure lesson start times and durations
- **School Days:** Select which days of the week have lessons
- **Break Detection:** Automatically shows break time between lessons
- **Next Lesson Preview:** Optional display of upcoming lesson
- **Responsive Design:** Mobile-friendly timer display
- **Multiple Display Modes:** Normal and compact layouts
- **AJAX Updates:** Real-time data refresh without page reload
- **Shortcode Support:** Display timer anywhere with `[tobalt_lessons_timer]`
- **Standalone or Integrated:** Works independently or with Tobalt School Pack hub

## Installation

1. Upload the `tobalt-lessons-timer` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Lessons Timer to create schedule profiles
4. Set one profile as active
5. Use the shortcode to display the timer

## Creating a Schedule

1. Navigate to **Lessons Timer** in the admin menu
2. Click **New Profile**
3. Enter a profile name (e.g., "Regular Week Schedule")
4. Select school days (Monday–Friday)
5. Add lessons with start times and durations:
   - Click "Add Lesson" for each lesson
   - Enter start time (e.g., 08:00)
   - Enter duration in minutes (e.g., 45)
6. Check "Set as Active" to use this profile
7. Click **Save Profile**

## Shortcodes

### Basic Timer
```
[tobalt_lessons_timer]
```

### Timer with Options
```
[tobalt_lessons_timer show_next="yes" compact="no"]
```

**Parameters:**
- `show_next` - Show next lesson info (default: "yes")
- `compact` - Use compact layout (default: "no")

## Timer States

The timer automatically detects and displays:

- **Current Lesson:** Shows lesson number, countdown, and time range
- **Break Time:** Shows countdown until next lesson starts
- **After School:** Displays when all lessons are finished
- **No School:** Shows on non-school days
- **No Schedule:** Appears when no profile is configured

## Use Cases

- Display on school homepage
- Show in classroom dashboards
- Embed in student portals
- Include in teacher admin areas
- Add to digital signage

## Version

1.0.0

## Author

Tobalt — https://tobalt.lt
