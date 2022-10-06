=== Time Clock - A WordPress Employee & Volunteer Time Clock Plugin ===
Contributors: scottpaterson,wp-plugin,PBMod
Author URI: https://github.com/svenbolte/
Plugin URI: https://github.com/svenbolte/time-clock/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: timeclock, time, clock, employee, volunteer, stempeluhr, zeiterfassung
Requires at least: 5.0
Tested up to: 6.0.2
Requires PHP: 5.7
Stable tag: 9.1.2.1.45
Version: 9.1.2.1.45

An MOD with german translations and more functions of the cool employee / volunteer time clock for WordPress

== Description ==

= Overview =

This plugin allows employees or volunteers to clock in and out for their work shifts and review and export their bookings to excel.

= Time Clock Features =

Users can:
*	clock in and out from shifts
*	clock in and out from lunch breaks
*	set time/date for clock in out and take break (even in future for planning)
*	review their own bookings, work time, break time and total time (work and break)
*	filter on month basis

Admins can: 
*	 view user bookings and filter on username and/or on month basis
*	 easily make new user accounts
*	 view the number of hours worked for each day
*	 change the color and text of the time clock
*    Export Activity to CSV File
*	 list all activities of all users

*	if worktime is >10hrs time field will be marked red
* 	if using html in widgets enabled in your wordpress theme you can use time clock in a widget

== Installation ==

= Automatic Installation =
1. Go to Github on project page and download zip from releases
2. Sign in to your WordPress site as an administrator.
3. In the main menu go to Plugins -> Add New --> Upload and upload the plugin zip you downloaded from GitHub.
4. Search for TimeClock and click install.
5. Create a page (or post, page is better) and add a title like "Stempeluhr" and the shortcode [timeclock]

== Changelog ==

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
