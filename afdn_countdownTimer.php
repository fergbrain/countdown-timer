<?php
/*
Plugin Name: Countdown Timer
Plugin URI: http://www.andrewferguson.net/wordpress-plugins/#countdown
Plugin Description: Add template tags to count down the years, days, hours, and minutes to a particular event or recurring date
Version: 1.7
Author: Andrew Ferguson
Author URI: http://www.andrewferguson.net

Countdown Timer - Add template tags to count down the years, days, hours, and minutes to a particular event or recurring date
Copyright (c) 2005-2007 Andrew Ferguson

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/

function afdn_countdownTimer_myOptionsSubpanel(){
$pluginVersion = "1.7";
$updateURL = "http://dev.wp-plugins.org/file/countdown-timer/trunk/version.inc?format=txt";


	if (isset($_POST['info_update']))																		//If the user has submitted the form, do the following
	{
		/*Begin One Time Events*/
		$oneTimeEvent_count = $_POST['oneTimeEvent_count']; 												//Figure out how many fields there are
		$j=0;																								//Keep track of how many actual fields are filled, versus how many were sent (there could be empty fields which need to be removed)
		for($i=0; $i<$oneTimeEvent_count; $i++){
			if($_POST["oneTimeEvent_text$i"]=="" || $_POST["oneTimeEvent_date$i"]==""){						//If the text or date field is empty, ignore the entry
			}
			else{																							//If not, add it to an array so the data can be updated
				$results["oneTime"][$j] = array(	"date" => strtotime($_POST["oneTimeEvent_date$i"]),		//Date of the event converted to UNIX time
													"text" => $_POST["oneTimeEvent_text$i"],				//Text associated with the event (i.e. event label)
													"timeSince" => $_POST["oneTimeEvent_timeSince$i"],		//After the event has occured, should "Time Since" be displayed? Boolean value (0 0 for no or 1 for yes)
													"link" => $_POST["oneTimeEvent_link$i"],				//Where should the text link to (this can be null)
												); 															//For every field, create an array. Then stick that array into the master array
				$j++;
			}
		}
		/*End One Time Events*/

		/*Begin Recurring Events*/
		$recurringEvent_count = $_POST['recurringEvent_count']; 											//Figure out how many fields there are
		$j=0;
		for($i=0; $i<$oneTimeEvent_count; $i++){
			if($_POST["recurringEvent_text$i"]=="" || $_POST["recurringEvent_date$i"]==""){
			}
			else{
				$results["recurring"][$j] = array(	"date" => $_POST["recurringEvent_date$i"],
													"text" => $_POST["recurringEvent_text$i"],
													"timeSince" => $_POST["recurringEvent_timeSince$i"],
													"nextOccurance" => strtorecurringtime($_POST["recurringEvent_date$i"]),		//Figure out when the next occurance of this event is
													); 														//For every field, create an array. Then stick that array into the master array
				$j++;
			}
		}
		/*End Recurring Events*/

		/*Begin sorting events by time*/
		for($x=0; $x<$oneTimeEvent_count; $x++){
			for($z=0; $z<$oneTimeEvent_count-1; $z++){
				if(($results["oneTime"][$z+1]["date"] < $results["oneTime"][$z]["date"]) && (array_key_exists($z+1, $results["oneTime"]))){
					$temp = $results["oneTime"][$z];
					$results["oneTime"][$z] = $results["oneTime"][$z+1];
					$results["oneTime"][$z+1] = $temp;
				}
			}
		}
		/*End sorting events by time*/

		$afdnOptions = array(	"deleteOneTimeEvents" => $_POST['deleteOneTimeEvents'],					//Should One Time Events be deleted after the happen (boolean)
								"checkUpdate" => $_POST['checkUpdate'],									//Should the plugin check for updates (boolean)
								"timeOffset" => $_POST['timeOffset'],									//What is the time format
								"enableTheLoop" => $_POST['enableTheLoop'],								//Should the timer be allowed within the loop (boolean)
								"displayFormatPrefix" => $_POST['displayFormatPrefix'],					
								"displayFormatSuffix" => $_POST['displayFormatSuffix'],
								); //Create the array to store the countdown options

		update_option("afdn_countdowntracker", $results); //Update the WPDB for the data
		update_option("afdn_countdownOptions", $afdnOptions);//Update the WPDB for the options
		
		echo '<div id="message" class="updated fade"><p>Options updated successfully.</p></div>';					//Report to the user that the data has been updated successfully
	}

	$dates = get_option("afdn_countdowntracker"); //Get the events from the WPDB to make sure a fresh copy is being used
	$getOptions = get_option("afdn_countdownOptions");//Get the options from the WPDB to make sure a fresh copy is being used

	/*If the user wants, cycle through the array to find out if they have already occured, if so: set them to NULL*/
	if($getOptions["deleteOneTimeEvents"]){
		foreach($dates as $key => $value){
			if(($value["date"]<=time())&&($value["timeSince"]=="")){
			$dates[$key]["date"]=0;
			}
		}
	}
	?>

	<div class=wrap>
		<script language="javascript">
		//Not used, yet
		function clearField(eventType, fieldNum){
			var agree=confirm('Are you sure you wish to delete '+document.getElementsByName(eventType+'_text'+fieldNum).item(0).value+'?');
			if(agree){
				var inputID = eventType + '_table' + fieldNum;
				document.getElementById(inputID).style.display = 'none';
				document.getElementsByName(eventType+'_date'+fieldNum).item(0).value = '';
				document.getElementsByName(eventType+'_text'+fieldNum).item(0).value = '';
				document.getElementsByName(eventType+'_link'+fieldNum).item(0).value = '';
				document.getElementsByName(eventType+'_timeSince'+fieldNum).item(0).value = '';
				}
			else
				return false;
		}
		</script>
		<form method="post" name="afdn_countdownTimer">
			<h2>Countdown Timer</h2>

			<!-- Notes and pleas-->
			<fieldset name="notes" class="options">
				<legend><strong>Notes</strong></legend>
				<p>You've made it this far, you're almost there. To insert the Countdown Timer into your sidebar, you'll probably want to use code similar to:</p>
				<p><code>&lt;li id='countdown'&gt;&lt;h2&gt;Countdown:&lt;/h2&gt;<br />
  &lt;ul&gt;<br />
  &lt;?php afdn_countdownTimer(); ?&gt;<br />
  &lt;/ul&gt;<br />
  &lt;/li&gt;</code>		</p>
				<p>Hopefully if you <em>really</em> like my plugins (and/or me) you might consider making a donation. I've been spending more and more time writing and supporting plugins. I’m a college student  and really only do this programming thing on the side for the love of it.<br />

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
</form></p>
			</fieldset>
			
			<!-- Options for the plugin management -->			
			<fieldset name="management" class="options">
				<legend><strong>Management</strong></legend>
					Check for updates? <input name="checkUpdate" type="radio" value="1" <?php print($getOptions["checkUpdate"]==1?"checked":NULL)?> />Yes :: <input name="checkUpdate" type="radio" value="0" <?php print($getOptions["checkUpdate"]==0?"checked":NULL)?>/>No
					<?php if($getOptions["checkUpdate"]==1){
						echo "<br /><br />";
						$currentVersion = file_get_contents($updateURL);
						if($currentVersion == $pluginVersion){
						  echo "You have the latest version.";
						}
						elseif($currentVersion > $pluginVersion){
						  echo "You have version <strong>$pluginVersion</strong>, the current version is <strong>$currentVersion</strong>.<br />";
						  echo "Download the latest version at <a href=\"http://dev.wp-plugins.org/file/countdown-timer/trunk/afdn_countdownTimer.php\">http://dev.wp-plugins.org/file/countdown-timer/trunk/afdn_countdownTimer.php</a>";
						}
						elseif($currentVersion < $pluginVersion){
							echo "Beta version, eh?";
						}

					}
						?>
			</fieldset>

			<!-- Include within The Loop -->
			<fieldset name="inPost" class="options">
				<legend><strong>Include in The Loop</strong>
				<p>To include CountdownTimer within a post or page, simple enable The Loop function below and then insert
				<code>&lt;!--afdn_countdownTimer--&gt;</code>
				where you want the countdown to be inserted</p>
				<p>Enable CountdownTimer within The Loop?
				<input name="enableTheLoop" type="radio" value="1" <?php print($getOptions["enableTheLoop"]==1?"checked":NULL)?> />Yes :: <input name="enableTheLoop" type="radio" value="0" <?php print($getOptions["enableTheLoop"]==0?"checked":NULL)?>/>No</p>

			</fieldset>

			<!-- Time Display -->
			<fieldset name="onHoverTimeFormat " class="options">
				<legend><strong>onHover Time Format </strong></legend>
				<p>If you set "onHover Time Format", hovering over the time left will show the user what the date of the event is; or in the case of a recurring event, when the next occurance is. onHover Time Format uses <a href-"http://us2.php.net/date" target="_blank">PHP's Date() function</a>.</p>
				<p>Examples:</p>
				<ul>
					<li>"<em>j M Y, G:i:s</em>" goes to "<strong>17 Mar 2006, 14:50:00</strong>"</li>
					<li>"<em>F jS, Y, g:i a</em>" goes to "<strong>March 17th, 2006, 2:50 pm</strong>"</li>
				</ul>
				<p>onHover Time Format <input type="text" value="<?php print($getOptions["timeOffset"]); ?>" name="timeOffset" /></p>
			</fieldset>
			
			<!-- Display Format -->
			<fieldset name="displayFormatOptions" class="options">
				<legend><strong>Display Format Options</strong></legend>
				<p>This setting allows you to customize how each event is wrapped.</p>
				<p>Examples:</p>
				<ul>
					<li><em>Prefix:</em> <code>&lt;li&gt;</code><br />
						<em>Suffix:</em> <code>&lt;/li&gt;</code></li>
				</ul>
				
				<p>Display Format Prefix <input type="text" value="<?php print($getOptions["displayFormatPrefix"]); ?>" name="displayFormatPrefix" /></p>
				<p>Display Format Suffix <input type="text" value="<?php print($getOptions["displayFormatSuffix"]); ?>" name="displayFormatSuffix" /></p>
			</fieldset>

			<!-- One Time Events -->
			<fieldset name="ote" class="options">
				<legend><b>One Time Events</b></legend>
				<p>Countdown timer uses <a href="http://us2.php.net/strtotime">PHP's strtodate function</a> and will parse about any English textual datetime description.</p>
				<p>Examples of some (but not all) valid dates:
			<ul>
						<li>now</li>
						<li>31 january 1986</li>
						<li>+1 day</li>
						<li>next thursday</li>
						<li>last monday</li>
			</ul>

				</p>
				<table>
				<tr>
					<td><?php _e('Delete'); ?></td>
					<td><?php _e('Event Date'); ?></td>
					<td><?php _e('Event Title'); ?></td>
					<td><?php _e('Link'); ?></td>
					<td><?php _e('Display "Time since"'); ?></td>
				</tr>
					<?php
						//global $count;
						$oneTimeEvent_count = 0;
						$oneTimeEvent_entriesCount = count($dates["oneTime"]);
						for($i=0; $i < $oneTimeEvent_entriesCount+1; $i++){
							if($dates["oneTime"][$i]["date"]!=''){ //If the time is NULL, skip over it?>
							<tr id="oneTimeEvent_table<?php echo $oneTimeEvent_count; ?>">
							<td><a href="javascript:void(0);" onClick="javascript:clearField('oneTimeEvent','<?php echo $oneTimeEvent_count; ?>');">X</a></td>
							<td><input type="text" size="35" name="oneTimeEvent_date<?php echo $oneTimeEvent_count; ?>" value="<?php if($dates["oneTime"][$i]["date"] != "")echo date("r", $dates["oneTime"][$i]["date"]); ?>" /></td>
							<td><input type="text" size="25" name="oneTimeEvent_text<?php echo $oneTimeEvent_count; ?>" value="<?php echo htmlentities(stripslashes($dates["oneTime"][$i]["text"])); ?>" /></td>
							<td><input type="text" size="25" name="oneTimeEvent_link<?php echo $oneTimeEvent_count; ?>" value="<?php echo $dates["oneTime"][$i]["link"]; ?>" /></td>
							<td><input type="checkbox" name="oneTimeEvent_timeSince<?php echo $oneTimeEvent_count; ?>" value="1" <?php print($dates["oneTime"][$i]["timeSince"]==1?"checked":NULL)?>/></td>
							</tr>
							<?php
							$oneTimeEvent_count++;
							 }

						@next($dates["oneTime"]);
						}
							?><tr>
							<td></td>
							<td><input type="text" size="35" name="oneTimeEvent_date<?php echo $oneTimeEvent_count; ?>" /></td>
							<td><input type="text" size="25" name="oneTimeEvent_text<?php echo $oneTimeEvent_count; ?>" /></td>
							<td><input type="text" size="25" name="oneTimeEvent_link<?php echo $oneTimeEvent_count; ?>" /></td>
							<td><input type="checkbox" name="oneTimeEvent_timeSince<?php echo $oneTimeEvent_count; ?>" /></td>
							</tr>
							<?php
						echo '<input type="hidden" name="oneTimeEvent_count" value="'.($oneTimeEvent_count+1).'" />';
						?>
				</table>


			<p>Automatically delete '<?php _e('One Time Events') ?>' after they have occured? <input name="deleteOneTimeEvents" type="radio" value="1" <?php print($getOptions["deleteOneTimeEvents"]==1?"checked":NULL)?> />Yes :: <input name="deleteOneTimeEvents" type="radio" value="0" <?php print($getOptions["deleteOneTimeEvents"]==0?"checked":NULL)?>/>No</p>

			</fieldset>

			<!-- Recurring Events -->
			<fieldset name="recurring" class="options">
				<legend><b>Recurring Events</b></legend>
				<p>Recurring events are going to take some time to work out because there is no PHP function that can handle it natively.
					So I'm going to have to build a function from scratch and that will take some time. In the meantime, enter dates in
					the format <strong>hh:mm TZ mm/dd</strong> and the very basic parsing I have done already should be able to figure
					out what is going on. If you have any tips, ideas, or suggestions, please please please let me know. The function specs are posted in the comments so that you can take a crack at developing your own expressions, if you so desire.</p>
				<table>
				<tr>
					<td><?php _e('Event Date'); ?></td>
					<td><?php _e('Event Title'); ?></td>
					<td><?php _e('Link'); ?></td>
					<td><?php _e('Display "Time since"'); ?></td>
					<td><?php _e('Next Occurance'); ?></td>
				</tr>
					<?php
						$recurringEvent_count = 0;
						$recurringEvent_entriesCount = count($dates["recurring"]);
						for($i=0; $i < $recurringEvent_entriesCount+1; $i++){
							if($dates["recurring"][$i]["date"]!=''){ //If the time is NULL, skip over it?>
							<tr>
							<td><input type="text" size="35" name="recurringEvent_date<?php echo $recurringEvent_count; ?>" value="<?php echo $dates["recurring"][$i]["date"]; ?>" /></td>
							<td><input type="text" size="25" name="recurringEvent_text<?php echo $recurringEvent_count; ?>" value="<?php echo htmlentities(stripslashes($dates["recurring"][$i]["text"])); ?>" /></td>
							<td><input type="text" size="25" name="recurringeEvent_link<?php echo $recurringEvent_count; ?>" value="<?php echo $dates["recurring"][$i]["link"]; ?>" /></td>
							<td><input type="checkbox" name="recurringEvent_timeSince<?php echo $recurringEvent_count; ?>" value="1" <?php print($dates["recurring"][$i]["timeSince"]==1?"checked":NULL)?>/></td>
							<td><?php echo date("r", $dates["recurring"][$i]["nextOccurance"]); ?></td>
							</tr>
							<?php
							$recurringEvent_count++;
							 }

						@next($dates["recurring"]);
						}
							?><tr>
							<td><input type="text" size="35" name="recurringEvent_date<?php echo $recurringEvent_count; ?>"  /></td>
							<td><input type="text" size="25" name="recurringEvent_text<?php echo $recurringEvent_count; ?>" /></td>
							<td><input type="text" size="25" name="recurringeEvent_link<?php echo $recurringEvent_count; ?>" /></td>
							<td><input type="checkbox" name="recurringEvent_timeSince<?php echo $recurringEvent_count; ?>" value="1" <?php print($dates["recurring"][$i]["timeSince"]==1?"checked":NULL)?>/></td>
							<td></td>
							</tr>
							<?php
						echo '<input type="hidden" name="recurringEvent_count" value="'.($recurringEvent_count+1).'" />';
						?>
				</table>
			</fieldset>
			<div class="submit"><input type="submit" name="info_update" value="<?php
				_e('Update Events', 'Localization name')
			 ?>&raquo;" /></div>
		</form>
	</div> <?
}

function afdn_countdownTimer_loop($theContent){																							//Filter function for including the countdown with The Loop
	if(preg_match("<!--afdn_countdownTimer(\([0-9]+\))-->", $theContent)){																//If the string is found within the loop, replace it
		$theContent = preg_replace("/<!--afdn_countdownTimer(\(([0-9]+)\))?-->/e", "afdn_countdownTimer('return', $2)", $theContent);	//The actual replacement of the string with the timer
	}
	elseif(preg_match("<!--afdn_countdownTimer-->", $theContent)){																		//If the string is found within the loop, replace it
		$theContent = preg_replace("/<!--afdn_countdownTimer-->/e", "afdn_countdownTimer('return', 0)", $theContent);	//The actual replacement of the string with the timer
	}
	return $theContent;																													//Return theContent
}

function afdn_countdownTimer_optionsPage(){																		//Action function for adding the configuration panel to the Management Page
	if(function_exists('add_management_page')){
			add_management_page('Countdown Timer', 'Countdown Timer', 10, basename(__FILE__), 'afdn_countdownTimer_myOptionsSubpanel');
	}
}


/*This function is called from your page to output the actual data*/
function afdn_countdownTimer($output = "echo", $eventLimit = 0){ //'echo' will print the results, 'return' will just return them

	$dates = get_option("afdn_countdowntracker");//Get our text, times, and settings from the database
	$getOptions = get_option("afdn_countdownOptions");//Get the options from the WPDB

	//There are two sets of arrays, 'onetime' and 'recurring', which need to be combined these next lines do that...
	$numOneTimeDates = count($dates["oneTime"]);
	$numRecurringDates = count($dates["recurring"]);

	//Putting the 'onetime' events into a new array
	for($i = 0; $i < $numOneTimeDates; $i++){
		$thisDate[$i] = array(	"text" => $dates["oneTime"][$i]["text"],
								"date" => $dates["oneTime"][$i]["date"],
								"timeSince" => $dates["oneTime"][$i]["timeSince"],
								"link" => $dates["oneTime"][$i]["link"],
								);
	}

	//Putting the 'recurring' events into the array
	for($i = 0; $i < $numRecurringDates; $i++){
		$thisDate[$i+$numOneTimeDates] = array(	"text" => $dates["recurring"][$i]["text"],
												"date" => $dates["recurring"][$i]["nextOccurance"],
												"timeSince" => $dates["recurring"][$i]["timeSince"],
												"link" => $dates["recurring"][$i]["link"],
										);
	}
	/*Now that all the events are in the same array, we need to sort them by date. This is actually the same code used above for the admin page.
	At some point, I plan to make this into a function; but for, this will do...

	And what it does is this:
	The number of elements in the array are counted. Then for array is gone through x^(x-1) times. This allows for all posible date permuations to be sorted out and ordered correctly.
	Genious, yes? */
	$eventCount = count($thisDate);
	for($x=0; $x<$eventCount; $x++){
		for($z=0; $z<$eventCount-1; $z++){
			if(($thisDate[$z+1]["date"] < $thisDate[$z]["date"]) && (array_key_exists($z+1, $thisDate))){
				$temp = $thisDate[$z];
				$thisDate[$z] = $thisDate[$z+1];
				$thisDate[$z+1] = $temp;
			}
		}
	}
	if($eventLimit != 0)	//If the eventLimit is set
		$eventCount = $eventLimit;

	global $fergcorp_countdownTimer_noEventsPresent;
	$fergcorp_countdownTimer_noEventsPresent = TRUE;
	//This is the part that does the actual outputting. If you want to preface data, this an excellent spot to do it in.
	for($i = 0; $i < $eventCount; $i++){
		if($output == "echo")
			echo cdt_format(stripslashes($thisDate[$i]["text"]), $thisDate[$i]["date"], (date("Z") - (get_settings('gmt_offset') * 3600)), $thisDate[$i]["timeSince"], $thisDate[$i]["link"], $getOptions["timeOffset"], $getOptions["displayFormatPrefix"], $getOptions["displayFormatSuffix"]);
		elseif($output == "return"){
			$toReturn .= cdt_format(stripslashes($thisDate[$i]["text"]), $thisDate[$i]["date"], (date("Z") - (get_settings('gmt_offset') * 3600)), $thisDate[$i]["timeSince"], $thisDate[$i]["link"], $getOptions["timeOffset"], $getOptions["displayFormatPrefix"], $getOptions["displayFormatSuffix"]);
		}
	}
	if($output == "return")
			return $toReturn;
			
	if($fergcorp_countdownTimer_noEventsPresent = TRUE){
		echo "<li>No dates present</li>";
	}
}
/*PLUGIN-WIDE FUNCTIONS*/

/*cdt_format takes four variables and returns a single strong for the output of the plugin
$text is a string with just the text associated with a given date, for example "My 20th Birthday!" HTML formatting is allowed, just be sure to close your tags
$time is an integer formated in UNIX time.
$offset is a signed integer (i.e. it has both positive and negitive values) and represents the sum of many timezone offsets to make sure that the correct time is displayed, no matter what timezone you are in, your server is in, or your blog is in.
$timeSince is a single integer representitive of a boolean value. 1 = True; 0 = False. This really should be passed along as a boolean value, so it's on the to do list to fix. In any event, if this value is set to "True", after an event has passed, the text will count up from the time the even happened. If it is set to "False, it will not count and the event will not be displayed.

Simple enough?
*/

function cdt_format($text, $time, $offset, $timeSince=0, $link=NULL, $timeFormat = "j M Y, G:i:s", $displayFormatPrefix = "<li>", $displayFormatSuffix = "</li>"){
	$time_left = $time - time() + $offset;
	if(($time_left < 0)&&($timeSince==1)){
		$fergcorp_countdownTimer_noEventsPresent = FALSE;
		$content = $displayFormatPrefix.($link==""?$text.":":"<a href=\"$link\"><strong>".$text.":</strong></a>")."<br />\n";
		if($timeFormat == "")
			$content .= cdt_hms($time_left)." ago".$displayFormatSuffix;
		else
			$content .= "<abbr title = \"".date($timeFormat, $time)."\" style=\"cursor:pointer; border-bottom:1px black dashed\">".cdt_hms($time_left)." ago</abbr>".$displayFormatSuffix;
		return $content;
		//return NULL;
	}
	elseif($time_left > 0){
		$fergcorp_countdownTimer_noEventsPresent = FALSE;
		$content = $displayFormatPrefix.($link==""?$text.":":"<a href=\"$link\"><strong>".$text.":</strong></a>")."<br />\n";
		if($timeFormat == "")
			$content .= cdt_hms($time_left).$displayFormatSuffix;
		else
			$content .= "<abbr title = \"".date($timeFormat, $time)."\" style=\"cursor:pointer; border-bottom:1px black dashed\">in ".cdt_hms($time_left)."</abbr>".$displayFormatSuffix;
		return $content;
	}
	else{
		return NULL;
	}
}

/*$cdt_hms takes a two variable integers and returns a single string
$s is an integer formated in UNIX time and is set to the event date (i.e. usually sometime in the future)
$min is another integer masquerading as a boolean. If set to "True" (i.e. "1"), the minutes until an event will be displayed. Otherwise, they will not.
*/
function cdt_hms($s, $min=1){
	$years=intval($s/31536000); //How many years?
	$months=intval(($s-$years*31536000)/2592000);
	$days=intval(($s-($years*31536000)-($months*2592000))/86400); //How many days?
	$hours=intval(($s-($years*31536000)-($months*2592000)-($days*86400))/3600); //How many hours?
	$minutes=intval(($s-($years*31536000)-($months*2592000)-($days*86400)-($hours*3600))/60); //How many minutes?
	if ($years) //If there are any years, display them
		$r=$r.abs($years).' '.__("year").($years==1?NULL:"s").', '; //Absolute values (ABS function) are used to be compatible with counting up from events
	if($months) //If there are any years, display them
		$r=$r.abs($months).' '.__("month").($months==1?NULL:"s").', ';
	if ($days) //If there are any days, display them
		$r=$r.abs($days).' '.__("day").($days==1?NULL:"s").', ';
	if ($hours) //If there are any hours, display them
		$r=$r.abs($hours).' '.__("hour").($hours==1?NULL:"s").', ';
	if($min) //If we want minutes, display them
		$r=$r.abs($minutes).' '.__("minute").($minutes==1?NULL:"s");
	return $r; //...and return the result (a string)
}

function time_difference($start, $end, $offset){

}

/* I created this function to handle dates that repeat every year. The idea is that I can design a whole bunch of conditions and then pass a string of text to this function
and have it return UNIX time stamp*/
function strtorecurringtime($string){
	$newString = date("r", strtotime($string));
	if(strtotime($newString) < (time()-15768000)) //15768000 is 6 months. This avoids resetting for the new date too soon.
		$newString = date("r", strtotime($string."/".(date("Y")+1)));

	return strtotime($newString);
}

add_action('admin_menu', 'afdn_countdownTimer_optionsPage');	//Add Action for adding the options page to admin panel

$getOptions = get_option("afdn_countdownOptions");	//Get the options from the WPDB (this is actually pretty sloppy on my part and should be fixed)

if($getOptions["enableTheLoop"]){								//If the timer is to be allowed in The Loop, run this
	add_filter('the_content', 'afdn_countdownTimer_loop', 1);
}
?>