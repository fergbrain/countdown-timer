=== Countdown Timer ===
Contributors: fergbrain
Donate link: http://www.andrewferguson.net/2007/03/08/general-note/
Tags: countdown, timer, count, date, event, widget, countup, age, fun, time, international, i18n
Requires at least: 2.5
Tested up to: 2.5.1
Stable tag: 2.2

This plugin allows you to setup a series of dates to count to or from in terms of years, months, weeks, days, hours, minutes, and/or seconds.

== Description ==

Countdown Timer allows you to setup one or more dates to count down to or away from.

Events can be inserted into the sidebar, either manually or as a widget, or within posts and pages.

Version 2.2 includes all the great features of past versions plus:

* Updated some phrases that missed being i18n.
* Updated i18n to use use sprintf's to assist in proper translation.
* Update the admin page to WordPress 2.5 Look and Feel.
* Users are now able to define the data (text/HTML) that comes after the title and before the actual countdown.
* Implemented a new function, fergcorp_countdownTimer_single($date), that allows users to create a one-off event outside of The Loop. $date should be PHP strtotime parseable string.
* Plugin output is now XHTML 1.0 Strict compliant.

Special thanks to:

* Mattias Tengblad (Swedish translation)
* Joan Piqué (Spanish translation)
* Sascha Grams (German translation)
* Serge (French translation)
* [Caio Oliveira](http://www.caiooliveira.com.br/) (Portuguese [Brasil] translation)
* [Atamert Ölçgen](http://www.muhuk.com) (Turkish translation)
* [singha](http://singha.cz) (Czech translation)
* Kobe Van Looveren (Dutch translation)


If you'd like to translate Countdown Timer in to your language, please visit: [http://fergcorp.com/project/phPo/phPo.php?poFileName=afdn_countdownTimer.po](http://fergcorp.com/project/phPo/phPo.php?poFileName=afdn_countdownTimer.po)

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

= Your program is broken! The count down is of by XX days! =

Well, not quite. As it turns out, determining the number of months between two dates is harder than one might think. As you know, all months don't have the same number of days. Thus, some months have 31 days, others have 30 days, and then there's February. It's pretty trivial to figure out the number of complete months between two days (if complete months exist).

However, how many months exist between January 15 and February 20? There are 36 days, which is obviously more than the number of days in any given month we have, so the timer should display 1 month and how many days? Six days (30 days/month)? Five days (31 days/month)? Eight days (28 days/month since the date does end in February)?

I happened to mention my problem to a friend who said that the US military decided that there were 30 days in every month and to prorate the the addition day (or less day(s)) for all the months that have more (or less) than 30 days.

= Wait, so how /do/ you count months? =

Using the above example of January 15 to February 20, there would be one month and five days. February 15 to March 20 would also be one month and five days. Why? January 15 to February 15 is one month. February 15 to February 20 is 5 days. Put them together and you get one month and five days.


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