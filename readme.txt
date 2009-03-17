=== Countdown Timer ===
Contributors: fergbrain
Donate link: http://www.andrewferguson.net/2007/03/08/general-note/
Tags: countdown, timer, count, date, event, widget, countup, age, fun, time, international, i18n, countdown timer
Requires at least: 2.6
Tested up to: 2.7.1
Stable tag: 2.3.5

This plugin allows you to setup a series of dates to count to or from in terms of years, months, weeks, days, hours, minutes, and/or seconds.

== Description ==

Countdown Timer allows you to setup one or more dates to count down to or away from.

Events can be inserted into the sidebar, either manually or as a widget, or within posts and pages.

Version 2.3.5 includes all the great features of past versions plus the following updates:

* Updated calculation routine to ensure that dates are accurate when "Months" are not displayed.
* Updated languages and added Latvian, Romanian, Russian, Danish, Lithuanian, and Serbian.
* Updated readme.txt file

== Translations ==

= Using another language =

You'll need to modify your `wp-config.php` file. Open it up and look for the line: `define ('WPLANG', '');`

You'll want to modify it to say: `define ('WPLANG', 'de_DE');`

Of course, you'll replace de_DE with the language extension that you want to use, unless of course you actually did want the German language translation.

= Special thanks to: =

* Mattias Tengblad (Swedish translation): sv_SE
* Joan Piqu&eacute; (Spanish translation): es_ES
* Sascha Grams (German translation): de_DE
* Serge (French translation): fr_FR
* [Caio Oliveira](http://www.caiooliveira.com.br/) (Portuguese [Brazil] translation): pr_BR
* [Atamert &Ouml;l&ccedil;gen](http://www.muhuk.com) (Turkish translation): tr_TR
* [singha](http://singha.cz) (Czech translation): cs_CZ
* Kobe Van Looveren (Dutch translation): nl_NL
* [Qiang](http://richile.cn/) (Chinese translation): zh_CN
* macryba (Polish translation): pl_PL
* [Mick](http://www.gaspriz.it) (Italian translation): it_IT
* [Adem Omerovi&#263;](http://www.tehnopedija.net) (Bosnian translation): bs_BA
* [masnapos](http://www.masnapos.eu) (Hungarian translation): hu_HU
* [Anders Ruen](http://www.gullungen.com) (Norwegian translation): nb_NO
* [Jans Pavlovs](http://www.btserv.org) (Latvian translation): lv_LV
* [Dragos Nicolae of Free Software Downloads](http://www.FreeSoftwareWorkshop.com) (Romanian translation): ro_RO
* [Oleg Shalomanov](http://coolidea.ru/) (Russian translation): ru_RU
* Steen Manniche (Danish translation): da_DK
* Darius &#142;itkevicius (Lithuanian translation): lt_LT
* [Ljev Rjadcenko](http://www.rjadcenko.com) (Serbian [Cyrilic] translation): sr_RS
* [Robert Buj](http://www.softcatala.org) (Catalan translation): ca_CA


If you'd like to translate Countdown Timer in to your language, please visit: [http://fergcorp.com/project/phPo/phPo.php?poFileName=afdn_countdownTimer.po](http://fergcorp.com/project/phPo/phPo.php?poFileName=afdn_countdownTimer.po)
Note: translator\_name is for your name (i.e. John Doe) and translator\_url is for the address to your website (i.e. http://google.com).

== Installation ==

Delete any previous version of Countdown Timer and associated files.

Download and install the timer into your plugins directory.

Activate the timer and add the widget.

If you don't want to use the widget, you can also add the following code into your sidebar.php file:

`<li id='countdown'><h2>Countdown:</h2>
<ul>
<?php function_exists('fergcorp_countdownTimer')?fergcorp_countdownTimer():NULL; ?>
</ul>
</li>`

= Inserting countdown timers into a page or post =

If you want to insert the Countdown Timer into a page or post, you can use the following shortcodes to return all or a limited number of Countdown Timers, respectively:
[fergcorp\_cdt]
[fergcorp\_cdt max=##]

Where ## is maximum number of results to be displayed - ordered by date

If you want to insert individual countdown timers, such as in posts or on pages, you can use the following shortcode:

Time until my birthday:
[fergcorp_cdt_single date="ENTER\_DATE\_HERE"]

Where "ENTER_DATE_HERE" uses PHP's strtotime function and will parse about any English textual datetime description.

= Limiting the number of countdown timers displayed =

If you're using the widget, there is an option to set the maximum number of timers shown. If you are using the PHP code, replace

`fergcorp_countdownTimer()`

with

`fergcorp_countdownTimer(##)`

where ## is the maximum number of events you wish to be displayed.

Events are automatically sorted by date of occurrence.

== Frequently Asked Questions ==

= Your program is broken! The count down is off by XX days! =

Well, not quite. As it turns out, determining the number of months between two dates is harder than one might think. As you know, all months don't have the same number of days. Thus, some months have 31 days, others have 30 days, and then there's February. It's pretty trivial to figure out the number of complete months between two days (if complete months exist).

However, how many months exist between January 15 and February 20? There are 36 days, which is obviously more than the number of days in any given month we have, so the timer should display 1 month and how many days? Six days (30 days/month)? Five days (31 days/month)? Eight days (28 days/month since the date does end in February)?

I happened to mention my problem to a friend who said that the US military decided that there were 30 days in every month and to prorate the the addition day (or less day(s)) for all the months that have more (or less) than 30 days.

= Wait, so how /do/ you count months? =

Using the above example of January 15 to February 20, there would be one month and five days. February 15 to March 20 would also be one month and five days. Why? January 15 to February 15 is one month. February 15 to February 20 is 5 days. Put them together and you get one month and five days.


= Where I am supposed to set the count down time? =

Log into your WordPress Dashboard. Expand the Tools menu, click on Countdown Timer. Scroll down to One Time Events. In the dates field, type the date you want to count down to. Fill in the event title field with what text you want displayed. Click Update Events.

= There's a foreign (non-English) word that's wrong, what do I do? =

There are two ways to fix this. First, you can always contact me via email, blog comment, support forum, etc and let me know about the error. I don't usually issue bug fix updates just for language errors, but it will make it into the next update cycle.

Second, if you're handy with poEdit or something of the like, you can make the changes yourself and email me the .po and .mo files (although I really only need the .po file).

= How come there are long periods of time when you don't respond or update the plugin? =

I'm in college and I do this for fun. That means school work must come first.


== Screenshots ==
1. Administration Interface
2. Example on Blog