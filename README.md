## Doctor Logger
This is a simple interactive app that physicians can use to schedule the times they are on call.
You can add or remove physicians, automatically generate a schedule, clear schedules, and generate a printer-friendly version of
your work schedule.

App is live at http://www.juanitotaveras.com/doctor_logger/index.php

## Files
Written in PHP, Javascript (with Jquery), and SQL.
./indexphp is the main file, builds page
other php files are used for AJAX.

## TODO
1. Number of weekends worked per doctor is only updated when you refresh the page. Need to write functions that update without refreshing.
2. When new doctor is added, days and weekends are not evenly distributed anymore. Not a big deal, since current doctors won't really change.
3. Add support for viewing on mobile device.

## Created by Juanito Taveras
