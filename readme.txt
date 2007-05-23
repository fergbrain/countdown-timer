=== Countdown Timer ===
Contributors: fergbrain
Donate link: http://www.andrewferguson.net/2007/03/08/general-note/
Tags: countdown, timer, count, date, event, widget, countup, age, fun, time, international
Requires at least: 2.0
Tested up to: 2.2
Stable tag: 1.8

This plugin allows you to setup a series of dates to count to or from in terms of years, days, hours, minutes, and seconds.

== Description ==

Countdown Timer allows you to setup one or more dates to count down to or away from.

Events can be inserted into the sidebar, either manually or as a widget, or within posts and pages.

Version 1.8 includes great new features, including:

* Built in widget! No need to download and activate another plugin!
* Ability to select any combination of years, days, hours, minutes, and seconds to display
* Internationalization support
* Default settings automatically set on activation
* More accurate countdown mechanism (you wouldn't think counting could be so hard)
* Numerous bug fixes
* Reorganized management page

Note to those upgrading: Recurring events have been removed!

== Installation ==

Delete any previous version of Countdown Timer and/or Countdown Timer Widget.

Download and install afdn_countdownTimer.php into your plugins directory.

Activate.

Here's the code you need in insert into your sidebar.php file:

`<li id='countdown'><h2>Countdown:</h2>
<ul>
<?php afdn_countdownTimer(); ?>
</ul>
</li>`

The plugin also has a built-in widget you can use instead of the above code.

If you want to call the timer from within the WP Loop, make sure The Loop function is enabled in the plugin configuration and then insert:

`<!--afdn_countdownTimer-->`

where you want the timer displayed.

== Frequently Asked Questions ==

= Where I am supposed to set the count down time? =

Log into your WordPress Dashboard. Click on Manage, click on Countdown Timer. Scroll down to One Time Events. In the dates field, type the date you want to count down to. Fill in the event title field with what text you want displayed. Click Update Events.
