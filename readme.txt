=== Countdown Timer ===
Contributors: fergbrain
Donate link: http://www.andrewferguson.net/2007/03/08/general-note/
Tags: countdown, timer, count, date, event
Requires at least: 1.5
Tested up to: 2.1.2
Stable tag: 1.7.1

This plugin allows you to setup a series of dates to countdown to in terms of years, days, hours, minutes, and seconds.

== Description ==

== Installation ==

Here's the code you need in insert into your sidebar.php file:

`<li id='countdown'><h2>Countdown:</h2>
<ul>
<?php afdn_countdownTimer(); ?>
</ul>
</li>`

Instead of using the above code, you can also use the included Widget

If you want to call the timer from within the WP Loop, make sure The Loop function is enabled in the plugin configuration and then insert:

`<!--afdn_countdownTimer-->`

where you want the timer displayed.

== Frequently Asked Questions ==

= Where I am supposed to set the count down time? =

Log into your WordPress Dashboard. Click on Manage, click on Countdown Timer. Scroll down to One Time Events. In the dates field, type the date you want to count down to. Fill in the event title field with what text you want displayed. Click Update Events.

= How do I get the two timers I have setup separate themselves into two different lines? =

In the management page, there's a prefix and suffix option. You'll need to use that to prefix and suffix each event with the appropriate HTML code, such as `<p>` and `</p>` or `<li>` and `</li>`.
