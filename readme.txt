=== Countdown Timer ===
Contributors: fergbrain
Donate link: http://www.andrewferguson.net/2007/03/08/general-note/
Tags: countdown, timer, count, date, event, widget, countup, age, fun, time, international, i18n
Requires at least: 2.6
Tested up to: 2.7
Stable tag: 2.3

This plugin allows you to setup a series of dates to count to or from in terms of years, months, weeks, days, hours, minutes, and/or seconds.

== Description ==

Countdown Timer allows you to setup one or more dates to count down to or away from.

Events can be inserted into the sidebar, either manually or as a widget, or within posts and pages.

Version 2.3 includes all the great features of past versions plus the following updates:

* Made meta boxes into WP-based functions with AJAX support
* Renamed $dates to $fergcorp_countdownTimer_dates and made it global
* Reversed order of afdn_countdownTimer parameters. See documentation for usage
* Updated meta boxes to work in WP 2.7
* Removed the option to disable enableTheLoop (i.e. always enabled now)
* Added shortcodes. See documentation for usage
* Updated some of the text so that links are not part of the translation. Not that this has been an issue, but it assures that links aren't tampered with in language translations
* Updated the widget to use the latest WP functions
* Widget now has a description
* Internal versioning is now done automatically
* Fixed a bug where "No Dates Present" would not display, even though there were no dates present
* Fixed a bug where an empty array would cause plugin to crash
* Fixed a problem that caused the timer to only display "in " if "strip zeros" is enabled
* Updated a couple function checks to check for the functions that we're actually using
* Updated the plugins_dir function call to properly reference the countdown-timer directory (this fixes issues with IIS and Windows)
* Added a helper function for afdn_countdownTimer so that users can use fergcorp_countdownTimer instead
* Fixed a potential bug (aka The Furton Fix) for systems running Windows where PHP may barf and give a warning:
Warning: date() [function.date]: Windows does not support dates prior to midnight (00:00:00), January 1, 1970 in afdn_countdownTimer.php on line 612
* Various bug and security fixes
* Paypal link doesn't use a form anymore
* Added a test to ensure cal_days_in_month function exists. If not, use a drop in replacement.


Special thanks to:

* Mattias Tengblad (Swedish translation)
* Joan Piqu&eacute; (Spanish translation)
* Sascha Grams (German translation)
* Serge (French translation)
* [Caio Oliveira](http://www.caiooliveira.com.br/) (Portuguese [Brasil] translation)
* [Atamert &Ouml;l&ccedil;gen](http://www.muhuk.com) (Turkish translation)
* [singha](http://singha.cz) (Czech translation)
* Kobe Van Looveren (Dutch translation)
* [Qiang](http://richile.cn/) (Chinese translation)
* macryba (Polish translation)
* [Mick](http://www.gaspriz.it) (Italian translation)
* [Adem Omerovi&#263;](http://www.tehnopedija.net) (Bosnian translation)
* [masnapos](http://www.masnapos.eu) (Hungarian translation)
* [Anders Ruen](http://www.gullungen.com) (Norwegian translation)


If you'd like to translate Countdown Timer in to your language, please visit: [http://fergcorp.com/project/phPo/phPo.php?poFileName=afdn_countdownTimer.po](http://fergcorp.com/project/phPo/phPo.php?poFileName=afdn_countdownTimer.po)
Note: translator_name is for your name and translator_url is for the address to your website.

== Installation ==

Delete any previous version of Countdown Timer and associated files.

Download and install the timer into your plugins directory.

Activate the timer and add the widget or add the following code into your sidebar.php file:

`<li id='countdown'><h2>Countdown:</h2>
<ul>
<?php function_exists('fergcorp_countdownTimer')?fergcorp_countdownTimer():NULL; ?>
</ul>
</li>`

If you want to insert the Countdown Timer into a page or post, you can use the following shortcodes to return all or a limited number of Countdown Timers, respectively:
[fergcorp_cdt]
[fergcorp_cdt max=##]

Where ## is maximum number of results to be displayed - ordered by date

If you want to insert individual countdown timers, such as in posts or on pages, you can use the following shortcode:

Time until my birthday:
[fergcorp_cdt_single date="ENTER_DATE_HERE"]

Where "ENTER_DATE_HERE" uses PHP's strtotime function and will parse about any English textual datetime description.

== Frequently Asked Questions ==

= Your program is broken! The count down is of by XX days! =

Well, not quite. As it turns out, determining the number of months between two dates is harder than one might think. As you know, all months don't have the same number of days. Thus, some months have 31 days, others have 30 days, and then there's February. It's pretty trivial to figure out the number of complete months between two days (if complete months exist).

However, how many months exist between January 15 and February 20? There are 36 days, which is obviously more than the number of days in any given month we have, so the timer should display 1 month and how many days? Six days (30 days/month)? Five days (31 days/month)? Eight days (28 days/month since the date does end in February)?

I happened to mention my problem to a friend who said that the US military decided that there were 30 days in every month and to prorate the the addition day (or less day(s)) for all the months that have more (or less) than 30 days.

= Wait, so how /do/ you count months? =

Using the above example of January 15 to February 20, there would be one month and five days. February 15 to March 20 would also be one month and five days. Why? January 15 to February 15 is one month. February 15 to February 20 is 5 days. Put them together and you get one month and five days.


= Where I am supposed to set the count down time? =

Log into your WordPress Dashboard. Expand the Tools menu, click on Countdown Timer. Scroll down to One Time Events. In the dates field, type the date you want to count down to. Fill in the event title field with what text you want displayed. Click Update Events.

= How do I limit the number of countdown timers displayed? =

If you're using the widget, there is an option to set the maximum number of timers shown. If you are not using the widget, replace

`fergcorp_countdownTimer()`

with

`fergcorp_countdownTimer(##)`

where ## is the maximum number of events you wish to be displayed.

Events are automatically sorted by date of occurrence.

= How do I use the language files? =

You'll need to modify your `wp-config.php` file. Open it up and look for the line: `define ('WPLANG', '');`

You'll want to modify it to say: `define ('WPLANG', 'de_DE');`

Of course, you'll replace de_DE with the language extension that you want to use, unless of course you actually did want the German language translation.

= There's a foreign (non-english) word that's wrong, what do I do? =

There are two ways to fix this. First, you can always contact me via email, blog comment, support forum, etc and let me know about the error. I don't usually issue bug fix updates just for language errors, but it will make it into the next update cycle.

Second, if you're handy with poEdit or something of the like, you can make the changes yourself and email me the .po and .mo files (although I really only need the .po file).

= How come there are long periods of time when you don't respond or update the plugin? =

I'm in college and I do this for fun. That means school work must come first.


== Screenshots ==
1. Administration Interface
2. Example on Blog