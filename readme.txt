=== Time Clock - A WordPress Employee & Volunteer Time Clock Plugin ===
Contributors: scottpaterson,wp-plugin,PBMod
Author URI: https://github.com/svenbolte/
Plugin URI: https://github.com/svenbolte/time-clock/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: timeclock, time, clock, employee, volunteer, stempeluhr, zeiterfassung, sitzreservierung, seat booking, events, rooms
Version: 9.1.2.2.84
Stable tag: 9.1.2.2.84
Requires at least: 5.0
Tested up to: 6.6
Requires PHP: 8.2

a time clock and seat reservation (desksharing) plugin for WordPress

== Description ==

= Overview =

This plugin allows employees or external coworkers to clock in and out and review and export their bookings to excel.
Use it for Desksharing and events: You can book seats (or desktops) in rooms (for mobile workers and events)
See a room reservations calendar for all rooms and past 30 days and future bookings

= Time Clock Features =

Users can:
*	clock in and out from shifts
*	clock in and out from lunch breaks
*	set time/date for clock in out and take break (even in future for planning)
*	review their own bookings, work time, break time and total time (work and break)
*	filter on month basis
* 	show their bookings in a calendar view

Admins (wordpress admin role) can: 
*	 view user bookings and filter on username and/or on month basis
*	 easily make new user accounts
*	 view the number of hours worked for each day
*	 change the color and text of the time clock
*    Export Activity to CSV File
*	 list all activities of all users
* 	 show all users bookings in a calendar view

*	if worktime is >10hrs time field will be marked red
* 	if using html in widgets enabled in your wordpress theme you can use time clock in a widget

= Roombooking Features =

Users can: 
*  book seats in rooms (or book a desktop in a room in office)
*  review rooms status in a month calendar (red when fully booked, green if free seats available)
*  select a room and day and see the reserved status aof seats in room
*  delete their own bookings

Admins can:
*  create and delete rooms (with capacity)
*  create and delete seats for existing users and guests

== Installation ==

= Automatic Installation =
1. Go to Github on project page and download zip from releases
2. Sign in to your WordPress site as an administrator.
3. In the main menu go to Plugins -> Add New --> Upload and upload the plugin zip you downloaded from GitHub.
4. Search for TimeClock and click install.
5. Create a page (or post, page is better) and add a title like "Stempeluhr" and the shortcode [timeclock]

If you want to use the seat reservation/room planner:
6. Create a page (or post, page is better) and add a title like "Raumbuchungen" and the shortcode [roombooking]


== Changelog ==

= 9.1.2.2.82 = 09.06.2023
* Fixes - Compatibility with PHP 8.2.x, Testing with WP 6.2.2

= 9.1.2.1.81 = 18.03.2023
load etime scripts only if needed, clear time default in stempelmaske

= 9.1.2.1.80 = 08.01.2023
Form postings switched to _POST method, delroom (admins only) and delseat (user and admin) set to form POST method
optimized code and css

= 9.1.2.1.63 = 04.01.2023
Cookie setting for user session repaired
redirect after action switched to javascript

= 9.1.2.1.63 = 02.11.2022
Belegt und Freianzeige alle Räume pro Tag (daily totals in calendar)
Kalendervorschau 1 Monat vor und 1 Monat nach ausgewähltem Datum (3 months view bases on selected date)

= 9.1.2.1.60 = 31.10.2022
added cookie hash based login system. hashs are valid on current day. cookies are stored 6 hrs
login is valid for time clock and room bookings

= 9.1.2.1.50 = 24.10.2022
[roombooking] shortcode added.
* admins can create and delete rooms with max-seats. and delete seat reservations
* users can create seat reservations in rooms (one per date)

add calendar view with month calendars showing users per day with total times (admin) or for logged in user/selected month
login name will be stored in a session cookie "etime_usercookie" and displayed in login mask
admin panel to see last users status (last booking/working status for each user)

= 9.1.2.1.44 = 05.10.2022
Admin backend activity list with work time, pause time and total times
worktime marked red when over 10 hrs/day

= 9.1.2.1.33 = 03.10.2022
* over 10 hrs work red warning
* admin filter activity frontend by users and a month
* user filter activity frontend by a month
* user login for activity frontent and csv exports
* sanitizing of form inputs

= 9.1.2.1.22 = 29.09.2022 PBMOD
* german translation completed
* csv export for admins (planned for users to export their own bookings to csv)
* admin activity view
* removed some stuff
* users can enter bookings in future or past manually on frontend (Buchungen nachtragen)

= 1.2.1 = 7/18/18
* New - Added abiilty to change event type when editing user activity view

= 1.2 = 7/16/18
* New - Users can work past midnight
* New - Set date and time format on plugin's settings page
* New - Admin can now add modify / delete times worked
* Fix - Reworked total time worked caculation
* Fix - Many bug fixes

= 1.1.2 = 5/31/18
* Fix - Fixed issue with multiple users logging into the timeclock at the same time.

= 1.1.1 = 5/23/18
* New - Added a deactivation survey to the plugin.

= 1.1 = 5/13/18
* New - Added the ability to see total time worked on the Activity Page.
* New - Added a dropdown menu on the Activity Page to sort by user.
* Fix - Fixed the total time worked caculations. Now it allows for mutiple shifts worked in any order per day.

= 1.0.1 = 4/17/18
* Fix - Time Clock shortcode theme styling issues.

= 1.0 = 4/17/18
* Initial release
