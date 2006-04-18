php

Plugin Name Countdown Timer
Plugin URI httpwww.andrewferguson.networdpress-plugins
Plugin Description Add template tages to coutn down the years, days, hours, and minutes to a particular event
Version 1.0
Author Andrew Ferguson
Author URI httpwww.andrewferguson.net

Countdown - Adds a template tag to count down to a specified date

This code is licensed under the MIT License.
httpwww.opensource.orglicensesmit-license.php
Copyright (c) 2005 Andrew Ferguson

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated
documentation files (the Software), to deal in the
Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish,
distribute, sublicense, andor sell copies of the Software,
and to permit persons to whom the Software is furnished to
do so, subject to the following conditions

The above copyright notice and this permission notice shall
be included in all copies or substantial portions of the
Software.

THE SOFTWARE IS PROVIDED AS IS, WITHOUT WARRANTY OF ANY
KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


function afdn_countdownTimer_myOptionsSubpanel(){
$pluginName = afdn_countdownTimer;
$pluginVersion = 1.0;

	
	if (isset($_POST['info_update'])) If the user has submitted the form, do the following
	{
		$oneTimeEvent_count = $_POST['oneTimeEvent_count']; Figure out how many fields there are
		for($i=0; $i$oneTimeEvent_count; $i++){
			if($_POST[oneTimeEvent_text$i]==  $_POST[oneTimeEvent_date$i]==){
			}
			else{
				$results[oneTime][$i] = array(	date = strtotime($_POST[oneTimeEvent_date$i]), 
											text = $_POST[oneTimeEvent_text$i],
											timeSince = $_POST[oneTimeEvent_timeSince$i],
											); For every field, create an array. Then stick that array into the master array
			}
		}

		$recurringEvent_count = $_POST['recurringEvent_count']; Figure out how many fields there are
		for($i=0; $i$oneTimeEvent_count; $i++){
			if($_POST[recurringEvent_text$i]==  $_POST[recurringEvent_date$i]==){
			}
			else{
			$results[recurring][$i] = array(	date = $_POST[recurringEvent_date$i], 
										text = $_POST[recurringEvent_text$i],
										timeSince = $_POST[recurringEvent_timeSince$i],			
										nextOccurance = strtorecurringtime($_POST[recurringEvent_date$i]),
										); For every field, create an array. Then stick that array into the master array
			}
		}

		Begin sorting events by time
		for($x=0; $x$oneTimeEvent_count; $x++){
			for($z=0; $z$oneTimeEvent_count-1; $z++){
				if(($results[oneTime][$z+1][date]  $results[oneTime][$z][date]) && (array_key_exists($z+1, $results[oneTime]))){
					$temp = $results[oneTime][$z];
					$results[oneTime][$z] = $results[oneTime][$z+1];
					$results[oneTime][$z+1] = $temp;
				}
			}	
		}
		End sorting events by time	
		
		$afdnOptions = array(	deleteOneTimeEvents = $_POST['deleteOneTimeEvents'],
								checkUpdate = $_POST['checkUpdate']); Create the array to store the countdown options
		
		update_option(afdn_countdowntracker, serialize($results)); Update the WPDB
		update_option(afdn_countdownOptions, serialize($afdnOptions));Update the WPDB
	}
	
	$dates = get_option(afdn_countdowntracker); Get the events from the WPDB to make sure a fresh copy is being used
	$getOptions = get_option(afdn_countdownOptions);Get the options from the WPDB
	
	If the user wants, cycle through the array to find out if they have already occured, if so set them to NULL
	if($getOptions[deleteOneTimeEvents]){
		foreach($dates as $key = $value){
			if(($value[date]=time())&&($value[timeSince]==)){
			$dates[$key][date]=0;
			}
		}
	}
	
	
	div class=wrap
		form method=post
			h2Countdown Timerh2
			fieldset name=management
				legendphp _e('Management'); legend
					Check for updates input name=checkUpdate type=radio value=1 php print($getOptions[checkUpdate]==1checkedNULL) Yes  input name=checkUpdate type=radio value=0 php print($getOptions[checkUpdate]==0checkedNULL)No		
					php if($getOptions[checkUpdate]==1){
						echo br br ;
						include_once(httpandrewferguson.netwp-contentdlwpversion.phppn=$pluginName&pv=$pluginVersion);
						}
						
			fieldset
			
			fieldset name=ote
				legendphp _e('One Time Events'); legend
				pCountdown timer uses a href=httpus2.php.netstrtotimePHP's strtodate functiona and will parse about any English textual datetime description.p
				pExamples of some (but not all) valid dates
					ul
						linowli
						li31 january 1986li
						li+1 dayli
						linext thursdayli
						lilast mondayli
					ul
				
				p
				table
				tr
					tdphp _e('Event Date'); td
					tdphp _e('Event Title'); td
					tdphp _e('Display Time since'); td
				tr
					php 
						global $count;
						$oneTimeEvent_count = 0;
						$oneTimeEvent_entriesCount = count($dates[oneTime]);
						for($i=0; $i  $oneTimeEvent_entriesCount+1; $i++){ 
							if($dates[oneTime][$i][date]!=''){ If the time is NULL, skip over it
							tdinput type=text size=35 name=oneTimeEvent_datephp echo $oneTimeEvent_count;  value=php if($dates[oneTime][$i][date] != )echo date(r, $dates[oneTime][$i][date]);  td
							tdinput type=text size=50 name=oneTimeEvent_textphp echo $oneTimeEvent_count;  value=php echo $dates[oneTime][$i][text];  td
							tdinput type=checkbox name=oneTimeEvent_timeSincephp echo $oneTimeEvent_count;  value=1 php print($dates[oneTime][$i][timeSince]==1checkedNULL)td
							tr
							php
							$oneTimeEvent_count++;
							 }
						
						@next($dates[oneTime]);
						} 						
							tr
							tdinput type=text size=35 name=oneTimeEvent_datephp echo $oneTimeEvent_count;  td
							tdinput type=text size=50 name=oneTimeEvent_textphp echo $oneTimeEvent_count;  td
							tdinput type=checkbox name=oneTimeEvent_timeSincephp echo $oneTimeEvent_count;  td
							tr
							php
						echo 'input type=hidden name=oneTimeEvent_count value='.($oneTimeEvent_count+1).' ';
						
				table
			
			
			pAutomatically delete 'php _e('One Time Events') ' after they have occured input name=deleteOneTimeEvents type=radio value=1 php print($getOptions[deleteOneTimeEvents]==1checkedNULL) Yes  input name=deleteOneTimeEvents type=radio value=0 php print($getOptions[deleteOneTimeEvents]==0checkedNULL)Nop
							
			fieldset
			
			fieldset name=recurring
				legendphp _e('Recurring Events'); legend
				pRecurring events are going to take some time to work out because there is no PHP function that can handle it natively. 
					So I'm going to have to build a function from scratch and that will take some time. In the meantime, enter dates in
					the format stronghhmm TZ mmddstrong and the very basic parsing I have done already should be able to figure
					out what is going on. If you have any tips, ideas, or suggestions, please please please let me know. I will post the
					function specs shortly so that you can take a crack at developing your own function, if you so desire.p
				table
				tr
					tdphp _e('Event Date'); td
					tdphp _e('Event Title'); td
					tdphp _e('Display Time since'); td
					tdphp _e('Next Occurance'); td
				tr
					php 
						$recurringEvent_count = 0;
						$recurringEvent_entriesCount = count($dates[recurring]);
						for($i=0; $i  $recurringEvent_entriesCount+1; $i++){ 
							if($dates[recurring][$i][date]!=''){ If the time is NULL, skip over it
							tr
							tdinput type=text size=35 name=recurringEvent_datephp echo $recurringEvent_count;  value=php echo $dates[recurring][$i][date];  td
							tdinput type=text size=50 name=recurringEvent_textphp echo $recurringEvent_count;  value=php echo $dates[recurring][$i][text];  td
							tdinput type=checkbox name=recurringEvent_timeSincephp echo $recurringEvent_count;  value=1 php print($dates[recurring][$i][timeSince]==1checkedNULL)td
							tdphp echo date(r, $dates[recurring][$i][nextOccurance]); td
							tr
							php
							$recurringEvent_count++;
							 }
						
						@next($dates[recurring]);
						} 						
							tr
							tdinput type=text size=35 name=recurringEvent_datephp echo $recurringEvent_count;   td
							tdinput type=text size=50 name=recurringEvent_textphp echo $recurringEvent_count;  td
							tdinput type=checkbox name=recurringEvent_timeSincephp echo $recurringEvent_count;  value=1 php print($dates[recurring][$i][timeSince]==1checkedNULL)td
							tdtd
							tr
							php
						echo 'input type=hidden name=recurringEvent_count value='.($recurringEvent_count+1).' ';
						
				table
			fieldset							
			div class=submitinput type=submit name=info_update value=php
				_e('Update Events', 'Localization name')
			 &raquo; div
		form
	div 
}



function afdn_countdownTimer_optionsPage(){
	if(function_exists('add_options_page')){
			add_options_page('Countdown Timer', 'Countdown Timer', 10, basename(__FILE__), 'afdn_countdownTimer_myOptionsSubpanel');
	}
}


This function is called from your page to output the actual data
function afdn_countdownTimer(){

	$dates = get_option(afdn_countdowntracker);Get our text, times, and settings from the database
	
	There are two sets of arrays, 'onetime' and 'recurring', which need to be combined these next lines do that...
	$numOneTimeDates = count($dates[oneTime]); 
	$numRecurringDates = count($dates[recurring]);
	
	Putting the 'onetime' events into a new array
	for($i = 0; $i  $numOneTimeDates; $i++){
		$thisDate[$i] = array(	text = $dates[oneTime][$i][text],
								date = $dates[oneTime][$i][date],
								timeSince = $dates[oneTime][$i][timeSince],
								);
	}
	
	Putting the 'recurring' events into the array
	for($i = 0; $i  $numRecurringDates; $i++){
		$thisDate[$i+$numOneTimeDates] = array(	text = $dates[recurring][$i][text],
												date = $dates[recurring][$i][nextOccurance],
												timeSince = $dates[recurring][$i][timeSince],
										);
	}
	Now that all the events are in the same array, we need to sort them by date. This is actually the same code used above for the admin page. 
	At some point, I plan to make this into a function; but for, this will do...
	
	And what it does is this
	The number of elements in the array are counted. Then for array is gone through x^(x-1) times. This allows for all posible date permuations to be sorted out and ordered correctly.
	Genious, yes 
	$eventCount = count($thisDate);
	for($x=0; $x$eventCount; $x++){
		for($z=0; $z$eventCount-1; $z++){
			if(($thisDate[$z+1][date]  $thisDate[$z][date]) && (array_key_exists($z+1, $thisDate))){
				$temp = $thisDate[$z];
				$thisDate[$z] = $thisDate[$z+1];
				$thisDate[$z+1] = $temp;
			}
		}	
	}
	
	This is the part that does the actual outputting. If you want to preface data, this an excellent spot to do it in.
	for($i = 0; $i  $eventCount; $i++){
		echo cdt_format($thisDate[$i][text], $thisDate[$i][date], (date(Z) - (get_settings('gmt_offset')  3600)), $thisDate[$i][timeSince]);
	}
}
PLUGIN-WIDE FUNCTIONS

cdt_format takes four variables and returns a single strong for the output of the plugin
$text is a string with just the text associated with a given date, for example My 20th Birthday! HTML formatting is allowed, just be sure to close your tags
$time is an integer formated in UNIX time.
$offset is a signed integer (i.e. it has both positive and negitive values) and represents the sum of many timezone offsets to make sure that the correct time is displayed, no matter 
		what timezone you are in, your server is in, or your blog is in.
$timeSince is a single integer representitive of a boolean value. 1 = True; 0 = False. This really should be passed along as a boolean value, so it's on the to do list to fix. In any event,
		if this value is set to True, after an event has passed, the text will count up from the time the even happened. If it is set to False, it will not count and the event will not be
		displayed.

Simple enough		



function cdt_format($text, $time, $offset, $timeSince=0){
	$time_left = $time - time() + $offset;
	if(($time_left  0)&&($timeSince==1)){
		$content = libTime since .$text.bbr n;
		$content .= cdt_hms($time_left). agoli;
		return $content;
		return NULL;
	}
	elseif($time_left  0){
	$content = lib.$text.bbr n;
	$content .= in .cdt_hms($time_left).li;
	return $content;
	}
	else{
		return NULL;
	}
}

$cdt_hms takes a two variable integers and returns a single string
$s is an integer formated in UNIX time and is set to the event date (i.e. usually sometime in the future)
$min is another integer masquerading as a boolean. If set to True (i.e. 1), the minutes until an event will be displayed. Otherwise, they will not.

function cdt_hms($s, $min=1){
	$years=intval($s31556736); How many years
	$days=intval(($s-$years31556736)86400); How many days
	$hours=intval(($s-($years31556736)-($days86400))3600); How many hours
	$minutes=intval(($s-($years31556736)-($days86400)-($hours3600))60); How many minutes
	if ($years) If there are any years, display them
		$r=$r.abs($years).' '.__(years).', '; Absolute values (ABS function) are used to be compatible with counting up from events
	if ($days) If there are any days, display them
		$r=$r.abs($days).' '.__(days).', ';
	if ($hours) If there are any hours, display them
		$r=$r.abs($hours).' '.__(hours).', ';
	if($min) If we want minutes, display them
		$r=$r.abs($minutes).' '.__(minutes);
	return $r; ...and return the result (a string)
}

 I created this function to handle dates that repeat every year. The idea is that I can design a whole bunch of conditions and then pass a string of text to this function
and have it return UNIX time stamp
function strtorecurringtime($string){
	$newString = date(r, strtotime($string));
	if(strtotime($newString)  time())
		$newString = date(r, strtotime($string..(date(Y)+1)));
	
	return strtotime($newString);
}

add_action('admin_menu', 'afdn_countdownTimer_optionsPage');
