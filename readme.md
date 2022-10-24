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

Mittlerweile ist die EU-Vorschrift zur Arbeitszeiterfassung auch in deutsches Recht umgesetzt.
Um die Zeiterfassung digital zu gestalten und den Mitarbeitenden und dem Admin Auswertungen und Listen zu erstellen,
habe ich das o.g. GPL2-lizensierte Wordpress Plugin ins deutsche übersetzt und um eine komplette Benutzeroberfläche
(Frontend) ergänzt. Darin können Mitarbietende wie bei einer klassischen Stempeluhr nach Eingabe iher ID und PIN 
die aktuelle Zeit erfassen (einstempeln, ausstelmepln, Pause beginnen, Pause beenden - 
oder eine Stempelzeit mit angeben (die auch in der Zukunft liegen kann) oder Zeiten nacherfassen.
Das neue Ansicht Frontend listet die Stempelvorgänge nach Tagen gruppiert übersichtlich und bildet Zwischensummen und eine Gesamtsumme.
Die Aktivitäten kann jeder Mitarbeitende für sich nach Excel sportieren.
Admins können auf Mitarbeitende und auf Monat filtern, User nur auf Monat.

Ist ein (Wordpress) Administrator angemeldet (z.B. Personalchef), kann er die Aktivitäten von allen Mitarbeitenden sehen,
nach Excel exportieren und auswerten.

So können Arbeitszeitverstöße (mehr als 10 Stunden pro Tag arbeiten) leicht erkannt werden (Arbeitszeit ist dann rot hinterlegt).

## Beschreibung Raumplaner / Sitzreservierung

[roombooking] shortcode
* admins can create and delete rooms with max-seats. and delete seat reservations
* users can create seat reservations in rooms (one per date)


## Disclaimer, Haftungsausschluss

Die Benutzung erfolgt auf eigenes Risiko. Diese Open-Source Lösung stellt nur ein Werkzeug dar, um die Forderungen digital zu erfüllen.
Der Autor schließt die Haftung für matierelle und immaterielle Schäden aus (vgl. auch Lizenztext GPL2).
