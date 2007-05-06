<?php
/*
Plugin Name: Countdown Timer
Plugin URI: http://www.andrewferguson.net/wordpress-plugins/countdown-timer/
Plugin Description: Add template tags to count down the years, days, hours, and minutes to a particular event
Version: 1.8 Beta
Author: Andrew Ferguson
Author URI: http://www.andrewferguson.net

Countdown Timer - Add template tags to count down the years, days, hours, and minutes to a particular event
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

	load_plugin_textdomain('afdn_countdownTimer', 'wp-content/plugins');

	if (isset($_POST['info_update']))																		//If the user has submitted the form, do the following
	{
		/*Begin One Time Events*/
		$oneTimeEvent_count = $_POST['oneTimeEvent_count']; 												//Figure out how many fields there are
		$j=0;																								//Keep track of how many actual fields are filled, versus how many were sent (there could be empty fields which need to be removed)
		for($i=0; $i<$oneTimeEvent_count; $i++){
			if($_POST["oneTimeEvent_text$i"]=="" || $_POST["oneTimeEvent_date$i"]==""){						//If the text or date field is empty, ignore the entry
			}
			else{																								//If not, add it to an array so the data can be updated
				$results["oneTime"][$j] = array(	"date" => strtotime($_POST["oneTimeEvent_date$i"]),		//Date of the event converted to UNIX time
													"text" => $_POST["oneTimeEvent_text$i"],				//Text associated with the event (i.e. event label)
													"timeSince" => $_POST["oneTimeEvent_timeSince$i"],		//After the event has occured, should "Time Since" be displayed? Boolean value (0 0 for no or 1 for yes)
													"link" => $_POST["oneTimeEvent_link$i"],				//Where should the text link to (this can be null)
												); 															//For every field, create an array. Then stick that array into the master array
				$j++;
			}
		}
		/*End One Time Events*/
		
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
								"showYear" => $_POST['showYear'],
								"showMonth" => $_POST['showMonth'],
								"showDay" => $_POST['showDay'],
								"showHour" => $_POST['showHour'],
								"showMinute" => $_POST['showMinute'],
								"showSecond" => $_POST['showSecond'],
								); //Create the array to store the countdown options

		update_option("afdn_countdowntracker", $results); //Update the WPDB for the data
		update_option("afdn_countdownOptions", $afdnOptions);//Update the WPDB for the options
		
		echo '<div id="message" class="updated fade"><p>'. __('Options updated successfully.', 'afdn_countdownTimer') .'</p></div>';					//Report to the user that the data has been updated successfully
	}

	$dates = get_option("afdn_countdowntracker"); //Get the events from the WPDB to make sure a fresh copy is being used
	$getOptions = get_option("afdn_countdownOptions");//Get the options from the WPDB to make sure a fresh copy is being used

	/*If the user wants, cycle through the array to find out if they have already occured, if so: set them to NULL*/
	if($getOptions["deleteOneTimeEvents"] && (count($dates["oneTime"][0])!=0) ){
		foreach($dates as $key => $value){
			if(($value["date"]<=time())&&($value["timeSince"]=="")){
			$dates[$key]["date"]=0;
			}
		}
	}
	?>

	<div class="wrap">
		<script language="javascript" type="text/javascript">

		function clearField(eventType, fieldNum){ //For deleting events without reloading
			var agree=confirm('<?php _e('Are you sure you wish to delete', 'afdn_countdownTimer'); ?> '+document.getElementsByName(eventType+'_text'+fieldNum).item(0).value+'?');
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
		
		function showHideContent(id, show){ //For hiding sections
			var elem = document.getElementById(id);
			if (elem){
				if (show){
					elem.style.display = 'block';
					elem.style.visibility = 'visible';
				} 
				else{
					elem.style.display = 'none';
					elem.style.visibility = 'hidden';
				}
			}
		}        

		
		
		
		</script>
			<h2>Countdown Timer</h2>

			<!-- Notes and pleas-->
			<fieldset class="options">
				<legend><strong><?php _e('Notes', 'afdn_countdownTimer'); ?></strong></legend>
				<p><?php _e("You've made it this far, you're almost there. To insert the Countdown Timer into your sidebar, you'll probably want to use code similar to", 'afdn_countdownTimer'); ?>:</p>
				<p><code>&lt;li id='countdown'&gt;&lt;h2&gt;Countdown:&lt;/h2&gt;<br />
  &lt;ul&gt;<br />
  &lt;?php afdn_countdownTimer(); ?&gt;<br />
  &lt;/ul&gt;<br />
  &lt;/li&gt;</code>		</p>
  
  				<p><?php _e("Alternatively, you can use the <a href='http://www.andrewferguson.net/wordpress-plugins/countdown-timer/'>Countdown Timer Widget</a> if you have widget enabled (or have the ability to enable widgets).", 'afdn_countdownTimer'); ?></p>
  
				<p><?php _e("Hopefully if you <em>really</em> like my plugins (and/or me) you might consider making a donation. I've been spending more and more time writing and supporting plugins. I'm a college student  and really only do this programming thing on the side for the love of it.", 'afdn_countdownTimer'); ?></p>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick" />
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" />
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
" />
</form>
			</fieldset>
			<form method="post" name="afdn_countdownTimer" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
			
			<!-- One Time Events -->
			<fieldset class="options">
				<legend><b><?php _e('One Time Events', 'afdn_countdownTimer'); ?></b></legend>
				<p><?php _e("Countdown timer uses <a href='http://us2.php.net/strtotime'>PHP's strtodate function</a> and will parse about any English textual datetime description.", 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Examples of some (but not all) valid dates', 'afdn_countdownTimer'); ?>:</p>
			<ul>
						<li>now</li>
						<li>31 january 1986</li>
						<li>+1 day</li>
						<li>next thursday</li>
						<li>last monday</li>
			</ul>
				<table>
				<tr>
					<td><strong><?php _e('Delete'); ?></strong></td>
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
							<td><a href="javascript:void(0);" onclick="javascript:clearField('oneTimeEvent','<?php echo $oneTimeEvent_count; ?>');">X</a></td>
							<td><input type="text" size="35" name="oneTimeEvent_date<?php echo $oneTimeEvent_count; ?>" value="<?php if($dates["oneTime"][$i]["date"] != "")echo date("r", $dates["oneTime"][$i]["date"]); ?>" /></td>
							<td><input type="text" size="25" name="oneTimeEvent_text<?php echo $oneTimeEvent_count; ?>" value="<?php echo htmlspecialchars(stripslashes($dates["oneTime"][$i]["text"])); ?>" /></td>
							<td><input type="text" size="25" name="oneTimeEvent_link<?php echo $oneTimeEvent_count; ?>" value="<?php echo $dates["oneTime"][$i]["link"]; ?>" /></td>
							<td><input type="checkbox" name="oneTimeEvent_timeSince<?php echo $oneTimeEvent_count; ?>" value="1" <?php print($dates["oneTime"][$i]["timeSince"]==1?"checked='checked'":NULL)?>/></td>
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
				</table>
				
					<?php echo '<input type="hidden" name="oneTimeEvent_count" value="'.($oneTimeEvent_count+1).'" />'; ?>


			<p><?php _e("Automatically delete 'One Time Events' after they have occured?"); ?> <input name="deleteOneTimeEvents" type="radio" value="1" <?php print($getOptions["deleteOneTimeEvents"]==1?"checked='checked'":NULL)?> /><?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="deleteOneTimeEvents" type="radio" value="0" <?php print($getOptions["deleteOneTimeEvents"]==0?"checked='checked'":NULL)?>/><?php _e('No', 'afdn_countdownTimer'); ?></p>

			</fieldset>
			
			
			<!-- Options for the plugin management -->			
			<fieldset class="options">
				<legend><strong><?php _e('Management', 'afdn_countdownTimer'); ?></strong></legend>
					<?php _e('Check for updates?', 'afdn_countdownTimer'); ?> <input name="checkUpdate" type="radio" value="1" <?php print($getOptions["checkUpdate"]==1?"checked='checked'":NULL)?> /><?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="checkUpdate" type="radio" value="0" <?php print($getOptions["checkUpdate"]==0?"checked='checked'":NULL)?>/><?php _e('No', 'afdn_countdownTimer'); ?>
					<?php if($getOptions["checkUpdate"]==1){
						echo "<br /><br />";
						echo "<iframe src='http://fergcorp.com/wordpress/version.php?plugin=afdn_countdownTimer&amp;version=". get_option("fergcorp_countdownTimer_version") ."' width='100%' height='90' scrolling='auto' frameborder='0'></iframe>";
						
					}
						?>
			</fieldset>

			<!-- Include within The Loop -->
			<fieldset class="options">
				<legend><strong><?php _e('Include in The Loop', 'afdn_countdownTimer'); ?></strong></legend>
				<p><?php _e('To include CountdownTimer within a post or page, simple enable The Loop function below and then insert', 'afdn_countdownTimer'); ?>:
				<code>&lt;!--afdn_countdownTimer--&gt;</code>
				<?php _e('where you want the countdown to be inserted', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Enable CountdownTimer within The Loop?', 'afdn_countdownTimer'); ?>
				<input name="enableTheLoop" type="radio" value="1" <?php print($getOptions["enableTheLoop"]==1?"checked='checked'":NULL)?> /><?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="enableTheLoop" type="radio" value="0" <?php print($getOptions["enableTheLoop"]==0?"checked='checked'":NULL)?>/><?php _e('No', 'afdn_countdownTimer'); ?></p>

			</fieldset>
			<!-- Countdown Time Display -->
			<fieldset class="options">
				<legend><strong><?php _e('Countdown Time Display', 'afdn_countdownTimer'); ?></strong></legend>
				<p><?php _e('This settings controls what unites of time are displayed.', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Years?', 'afdn_countdownTimer'); ?> <input name="showYear" type = "radio" value = "1" <?php print($getOptions["showYear"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showYear" type = "radio" value = "0" <?php print($getOptions["showYear"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Months?', 'afdn_countdownTimer'); ?> <input name="showMonth" type = "radio" value = "1" <?php print($getOptions["showMonth"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showMonth" type = "radio" value = "0" <?php print($getOptions["showMonth"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Days?', 'afdn_countdownTimer'); ?> <input name="showDay" type = "radio" value = "1" <?php print($getOptions["showDay"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showDay" type = "radio" value = "0" <?php print($getOptions["showDay"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Hours?', 'afdn_countdownTimer'); ?> <input name="showHour" type = "radio" value = "1" <?php print($getOptions["showHour"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showHour" type = "radio" value = "0" <?php print($getOptions["showHour"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Minutes?', 'afdn_countdownTimer'); ?> <input name="showMinute" type = "radio" value = "1" <?php print($getOptions["showMinute"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showMinute" type = "radio" value = "0" <?php print($getOptions["showMinute"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Seconds?', 'afdn_countdownTimer'); ?> <input name="showSecond" type = "radio" value = "1" <?php print($getOptions["showSecond"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showSecond" type = "radio" value = "0" <?php print($getOptions["showSecond"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
			</fieldset>	
				

			<!-- onHover Time Display -->
			<fieldset class="options">
				<legend><strong><?php _e('onHover Time Format', 'afdn_countdownTimer'); ?></strong></legend>
				<p><?php _e("If you set 'onHover Time Format', hovering over the time left will show the user what the date of the event is; or in the case of a recurring event, when the next occurance is. onHover Time Format uses", 'afdn_countdownTimer'); ?> <a href="http://us2.php.net/date" target="_blank">PHP's Date() function</a>.</p>
				<p><?php _e('Examples', 'afdn_countdownTimer'); ?>:</p>
				<ul>
					<li>"<em>j M Y, G:i:s</em>" <?php _e('goes to', 'afdn_countdownTimer'); ?> "<strong>17 Mar 2008, 14:50:00</strong>"</li>
					<li>"<em>F jS, Y, g:i a</em>" <?php _e('goes to', 'afdn_countdownTimer'); ?> "<strong>March 17th, 2008, 2:50 pm</strong>"</li>
				</ul>
				<p><?php _e('onHover Time Format', 'afdn_countdownTimer'); ?> <input type="text" value="<?php print($getOptions["timeOffset"]); ?>" name="timeOffset" /></p>
			</fieldset>
			
			<!-- Display Format -->
			<fieldset class="options">
				<legend><strong><?php _e('Display Format Options', 'afdn_countdownTimer'); ?></strong></legend>
				<p><?php _e('This setting allows you to customize how each event is wrapped.', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Examples', 'afdn_countdownTimer'); ?>:</p>
				<ul>
					<li><em><?php _e('Prefix', 'afdn_countdownTimer'); ?>:</em> <code>&lt;li&gt;</code></li>
					<li><em><?php _e('Suffix', 'afdn_countdownTimer'); ?>:</em> <code>&lt;/li&gt;</code></li>
				</ul>
				
				<p><?php _e('Display Format Prefix', 'afdn_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($getOptions["displayFormatPrefix"])); ?>" name="displayFormatPrefix" /></p>
				<p><?php _e('Display Format Suffix', 'afdn_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($getOptions["displayFormatSuffix"])); ?>" name="displayFormatSuffix" /></p>
			</fieldset>

			<div class="submit"><input type="submit" name="info_update" value="<?php
				_e('Update Events', 'afdn_countdownTimer')
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
			echo fergcorp_countdownTimer_format(stripslashes($thisDate[$i]["text"]), $thisDate[$i]["date"], (date("Z") - (get_settings('gmt_offset') * 3600)), $thisDate[$i]["timeSince"], $thisDate[$i]["link"], $getOptions["timeOffset"], $getOptions["displayFormatPrefix"], $getOptions["displayFormatSuffix"]);
		elseif($output == "return"){
			$toReturn .= fergcorp_countdownTimer_format(stripslashes($thisDate[$i]["text"]), $thisDate[$i]["date"], (date("Z") - (get_settings('gmt_offset') * 3600)), $thisDate[$i]["timeSince"], $thisDate[$i]["link"], $getOptions["timeOffset"], $getOptions["displayFormatPrefix"], $getOptions["displayFormatSuffix"]);
		}
	}
	if($output == "return")
			return $toReturn;
	
	if($fergcorp_countdownTimer_noEventsPresent == TRUE){
		echo $getOptions["displayFormatPrefix"].__('No dates present', 'afdn_countdownTimer').$getOptions["displayFormatSuffix"];
	}
}
/*PLUGIN-WIDE FUNCTIONS*/

/*fergcorp_countdownTimer_format takes four variables and returns a single strong for the output of the plugin
$text is a string with just the text associated with a given date, for example "My 20th Birthday!" HTML formatting is allowed, just be sure to close your tags
$time is an integer formated in UNIX time.
$offset is a signed integer (i.e. it has both positive and negitive values) and represents the sum of many timezone offsets to make sure that the correct time is displayed, no matter what timezone you are in, your server is in, or your blog is in.
$timeSince is a single integer representitive of a boolean value. 1 = True; 0 = False. This really should be passed along as a boolean value, so it's on the to do list to fix. In any event, if this value is set to "True", after an event has passed, the text will count up from the time the even happened. If it is set to "False, it will not count and the event will not be displayed.

Simple enough?
*/

function fergcorp_countdownTimer_format($text, $time, $offset, $timeSince=0, $link=NULL, $timeFormat = "j M Y, G:i:s", $displayFormatPrefix = "<li>", $displayFormatSuffix = "</li>"){
	global $fergcorp_countdownTimer_noEventsPresent, $getOptions;
	$time_left = $time - time() + $offset;
	if(($time_left < 0)&&($timeSince==1)){
		$fergcorp_countdownTimer_noEventsPresent = FALSE;
		$content = $displayFormatPrefix.($link==""?$text.":":"<a href=\"$link\"><strong>".$text.":</strong></a>")."<br />\n";
		if($timeFormat == "")
			$content .= fergcorp_countdownTimer_fuzzyDate((time() + $offset), $time)." ago".$displayFormatSuffix;
		else
			$content .= "<abbr title = \"".date($timeFormat, $time)."\" style=\"cursor:pointer; border-bottom:1px black dashed\">".fergcorp_countdownTimer_fuzzyDate((time() + $offset), $time)." ago</abbr>".$displayFormatSuffix;
		return $content;
	}
	elseif($time_left > 0){
		$fergcorp_countdownTimer_noEventsPresent = FALSE;
		$content = $displayFormatPrefix.($link==""?$text.":":"<a href=\"$link\"><strong>".$text.":</strong></a>")."<br />\n";
		if($timeFormat == "")
			$content .= fergcorp_countdownTimer_fuzzyDate($time, (time() + $offset)).$displayFormatSuffix;
		else
			$content .= "<abbr title = \"".date($timeFormat, $time)."\" style=\"cursor:pointer; border-bottom:1px black dashed\">in ".fergcorp_countdownTimer_fuzzyDate($time, (time() + $offset))."</abbr>".$displayFormatSuffix;
		return $content;
	}
	else{
		return NULL;
	}
}

function fergcorp_countdownTimer_fuzzyDate($targetTime, $nowTime){
	global $getOptions;
	
	$rollover = 0;
	$s = '';

	$nowYear = date("Y", $nowTime);
	$nowMonth = date("m", $nowTime);
	$nowDay = date("d", $nowTime);
	$nowHour = date("H", $nowTime);
	$nowMinute = date("i", $nowTime);
	$nowSecond = date("s", $nowTime);
	
	$targetYear = date("Y", $targetTime);
	$targetMonth = date("m", $targetTime);
	$targetDay = date("d", $targetTime);
	$targetHour = date("H", $targetTime);
	$targetMinute = date("i", $targetTime);
	$targetSecond = date("s", $targetTime);
	
	$resultantYear = $targetYear - $nowYear;
	$resultantMonth = $targetMonth - $nowMonth;
	$resultantDay = $targetDay - $nowDay;
	$resultantHour = $targetHour - $nowHour;
	$resultantMinute = $targetMinute - $nowMinute;
	$resultantSecond = $targetSecond - $nowSecond;
	
	
	if($resultantSecond < 0){
		$resultantMinute--;
		$resultantSecond = 60 + $resultantSecond;
	}
	
	if($resultantMinute < 0){
		$resultantHour--;
		$resultantMinute = 60 + $resultantMinute;
	}
	
	if($resultantHour < 0){
		$resultantDay--;
		$resultantHour = 24 + $resultantHour;
	}
	
	if($resultantDay < 0){
		$resultantMonth--;
		$resultantDay = $resultantDay + date("t", $targetMonth);
	}
	
	if($resultantMonth < 0){
		$resultantYear--;
		$resultantMonth = $resultantMonth + 12;
	}
	
	load_plugin_textdomain('afdn_countdownTimer', 'wp-content/plugins');

	//Year
	if($getOptions['showYear'] && ($resultantYear)){
		$s = $resultantYear.' '.($resultantYear==1?__("year", "afdn_countdownTimer"):__("years", "afdn_countdownTimer")).', ';;
	}
	else{
		$rollover = $resultantYear*31536000;
	}

	//Month	
	if($getOptions['showMonth']){
		$s = $s.($resultantMonth + intval($rollover/2592000)).' '.($resultantMonth==1?__("month", "afdn_countdownTimer"):__("months", "afdn_countdownTimer")).', ';
		$rollover = $rollover - intval($rollover/2592000)*2592000;
	}
	else{
		$rollover = $rollover + $resultantMonth*2592000;
	}

	//Day
	if($getOptions['showDay']){
		$s = $s.($resultantDay + intval($rollover/86400)).' '.($resultantDay==1?__("day", "afdn_countdownTimer"):__("days", "afdn_countdownTimer")).', ';
		$rollover = $rollover - intval($rollover/86400)*86400;
	}
	else{
		$rollover = $rollover + $resultantDay*86400;
	}
	
	//Hour
	if($getOptions['showHour']){
		$s = $s.($resultantHour + intval($rollover/3600)).' '.($resultantHour==1?__("hour", "afdn_countdownTimer"):__("hours", "afdn_countdownTimer")).', ';
		$rollover = $rollover - intval($rollover/3600)*3600;
	}
	else{
		$rollover = $rollover + $resultantHour*3600;
	}
	
	//Minute
	if($getOptions['showMinute']){
		$s = $s.($resultantMinute + intval($rollover/60)).' '.($resultantMinute==1?__("minute", "afdn_countdownTimer"):__("minutes", "afdn_countdownTimer")).', ';
		$rollover = $rollover - intval($rollover/60)*60;
	}
	else{
		$rollover = $rollover + $resultantMinute*60;
	}
	
	//Second
	if($getOptions['showSecond']){
		$s = $s.($resultantSecond + intval($rollover/86400)).' '.($resultantSecond==1?__("second", "afdn_countdownTimer"):__("seconds", "afdn_countdownTimer")).', ';
	}
	
	return rtrim($s,", "); //...and return the result (a string)
}

add_action('admin_menu', 'afdn_countdownTimer_optionsPage');	//Add Action for adding the options page to admin panel

add_action('activate_afdn_countdownTimer.php', 'afdn_countdownTimer_install');

function afdn_countdownTimer_install(){
	$version = get_option("fergcorp_countdownTimer_version");
	
	if($version == NULL){ //Version < 1.8
			
		$getOptions = get_option("afdn_countdownOptions");
		
		if($getOptions == NULL){ //No Previous Install (i.e. first time user)			
			$afdnOptions = array(	"deleteOneTimeEvents" => "0",					//Should One Time Events be deleted after the happen (boolean)
									"checkUpdate" => "1",									//Should the plugin check for updates (boolean)
									"timeOffset" => "F jS, Y, g:i a",									//What is the time format
									"enableTheLoop" => "0",								//Should the timer be allowed within the loop (boolean)
									"displayFormatPrefix" => "<li>",					
									"displayFormatSuffix" => "</li>",
									"showYear" => "1",
									"showMonth" => "1",
									"showDay" => "1",
									"showHour" => "1",
									"showMinute" => "1",
									"showSecond" => "0",
									); //Create the array to store the countdown options
			
			
		}
		else{ //Previously installed, but < version 1.8			
			$afdnOptions = array(	"deleteOneTimeEvents" => $getOptions['deleteOneTimeEvents'],					//Should One Time Events be deleted after the happen (boolean)
									"checkUpdate" => $getOptions['checkUpdate'],									//Should the plugin check for updates (boolean)
									"timeOffset" => $getOptions['timeOffset'],									//What is the time format
									"enableTheLoop" => $getOptions['enableTheLoop'],								//Should the timer be allowed within the loop (boolean)
									"displayFormatPrefix" => $getOptions['displayFormatPrefix'],					
									"displayFormatSuffix" => $getOptions['displayFormatSuffix'],
									"showYear" => "1",
									"showMonth" => "1",
									"showDay" => "1",
									"showHour" => "1",
									"showMinute" => "1",
									"showSecond" => "0",
									); //Create the array to store the countdown options
			
		}
		update_option("afdn_countdownOptions", $afdnOptions);//Update the WPDB for the options*/
		update_option("fergcorp_countdownTimer_version", "1.8");
	}

}

$getOptions = get_option("afdn_countdownOptions");	//Get the options from the WPDB (this is actually pretty sloppy on my part and should be fixed)

if($getOptions["enableTheLoop"]){								//If the timer is to be allowed in The Loop, run this
	add_filter('the_content', 'afdn_countdownTimer_loop', 1);
}
?>