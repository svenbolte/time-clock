## Time Clock MOD Stempeluhr mit Raumplaner/Desksharing

![Stempeluhr](https://github.com/svenbolte/time-clock/blob/main/assets/Screenshot-1.jpg?raw=true)

### A WordPress Employee & Volunteer Time Clock Plugin MOD German

Contributors: scottpaterson,wp-plugin,PBMod
License: GPLv2 or later
Tags: timeclock, time, clock, employee, volunteer, stempeluhr, zeiterfassung, sitzreservierung, seat booking, events, rooms


## Warum dieser Fork (Mod) des Plugins?

Die ursprüngliche Lösung von Herrn Paterson in der Wordpress plugin repository war nur in englisch
und bot auch nicht die benötigten Funktionen. Dennoch eine tolle und schlanke Basis, um das Werkzeug zu erweitern.

Darüber hinaus kommen immer mehr Desksharing Bürokonzepte heraus. Um nicht nur seine Zeiten zu erfassen, sondern auch 
noch einen Schreibtisch zu buchen für den Einsatztag im Büro, wurde der Raumplaner mit eigenem Shortcode integriert
Dieser kann auch genutzt werden, Sitze von (Ganztages-) Veranstaltungen nach Raum und Datum zu belegen.


## Beschreibung Stempeluhr

[timeclock] Shortcode

## Beschreibung Raumplaner / Sitzreservierung

[roombooking] shortcode

### Time Clock Features 

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

### Roombooking Features

Users can: 
*  use their login from time-clock to book a room or desk in a room
*  book seats in rooms (or book a desktop in a room in office)
*  review rooms status in a month calendar (red when fully booked, green if free seats available)
*  select a room and day and see the reserved status aof seats in room
*  delete their own bookings

Admins can:
*  create and delete rooms (with capacity)
*  create and delete seats for existing users and guests
*  view statistics on roob % usage, usage graphs and usage calendar for selected room.
*  export lists



* admins can create and delete rooms with max-seats. and delete seat reservations
* users can create seat reservations in rooms (one per date)


## Disclaimer, Haftungsausschluss

Die Benutzung erfolgt auf eigenes Risiko. Diese Open-Source Lösung stellt nur ein Werkzeug dar, um die Forderungen digital zu erfüllen.
Der Autor schließt die Haftung für matierelle und immaterielle Schäden aus (vgl. auch Lizenztext GPL2).
