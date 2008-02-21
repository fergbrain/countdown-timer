=== Countdown Timer ===
Contributors: fergbrain
Donate link: http://www.andrewferguson.net/2007/03/08/general-note/
Tags: countdown, timer, count, date, event, widget, countup, age, fun, time, international, i18n
Requires at least: 2.0
Tested up to: 2.3.3
Stable tag: 2.1.0

This plugin allows you to setup a series of dates to count to or from in terms of years, months, weeks, days, hours, minutes, and/or seconds.

== Description ==

Countdown Timer allows you to setup one or more dates to count down to or away from.

Events can be inserted into the sidebar, either manually or as a widget, or within posts and pages.

Version 2.1 includes all the great features of past versions plus:

* New and updated UI to match native WordPress interface
* The regular round of bug fixes
* Better i18n support

Special thanks to:
* Mattias Tengblad (Swedish translation)

== Installation ==

Delete any previous version of Countdown Timer and associated files.

Download and install the timer into your plugins directory.

Activate the timer and add the widget or add the following code into your sidebar.php file:

`<li id='countdown'><h2>Countdown:</h2>
<ul>
<?php afdn_countdownTimer(); ?>
</ul>
</li>`

If you want to call the timer from within the WordPress Loop, make sure The Loop function is enabled in the plugin configuration and then insert:

`<!--afdn_countdownTimer-->`

where you want the timer(s) displayed.

You can also create a single event by using:

`<!--afdn_countdownTimer_single("ENTER_DATE_HERE")-->`

replacing "ENTER\_DATE\_HERE" with the appropriate PHP strtodate() parseable string.

== Frequently Asked Questions ==

= Where I am supposed to set the count down time? =

Log into your WordPress Dashboard. Click on Manage, click on Countdown Timer. Scroll down to One Time Events. In the dates field, type the date you want to count down to. Fill in the event title field with what text you want displayed. Click Update Events.

= How do I limit the number of countdown timers displayed? =

If you're using the widget, there is an option to set the maximum number of timers shown. If you are not using the widget, replace

`afdn_countdownTimer()`

with

`afdn_countdownTimer(##)`

where ## is the maximum number of events you wish to be displayed.

Events are automatically sorted by date of occurrence.

== Screenshots ==
1. Administration Interface
2. Example on Blog