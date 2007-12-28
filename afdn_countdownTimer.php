<?php
/*
Plugin Name: Countdown Timer
Plugin URI: http://www.andrewferguson.net/wordpress-plugins/countdown-timer/
Plugin Description: Add template tags and widget to count down the years, months, weeks, days, hours, and minutes to a particular event
Version: 1.97
Author: Andrew Ferguson
Author URI: http://www.andrewferguson.net

Countdown Timer - Add template tags and widget to count down the years, months, weeks, days, hours, and minutes to a particular event
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

		$afdnOptions = array(	"deleteOneTimeEvents" 	=> $_POST['deleteOneTimeEvents'],					//Should One Time Events be deleted after the happen (boolean)
								"checkUpdate" 			=> $_POST['checkUpdate'],									//Should the plugin check for updates (boolean)
								"timeOffset"			=> $_POST['timeOffset'],									//What is the time format
								"enableTheLoop"		 	=> $_POST['enableTheLoop'],								//Should the timer be allowed within the loop (boolean)
								"displayFormatPrefix" 	=> $_POST['displayFormatPrefix'],					
								"displayFormatSuffix" 	=> $_POST['displayFormatSuffix'],
								"displayStyle" 			=> $_POST['displayStyle'],
								"showYear" 				=> $_POST['showYear'],
								"showMonth" 			=> $_POST['showMonth'],
								"showWeek" 				=> $_POST['showWeek'],
								"showDay" 				=> $_POST['showDay'],
								"showHour" 				=> $_POST['showHour'],
								"showMinute" 			=> $_POST['showMinute'],
								"showSecond" 			=> $_POST['showSecond'],
								"stripZero" 			=> $_POST['stripZero'],
								); //Create the array to store the countdown options

		update_option("afdn_countdowntracker", $results); //Update the WPDB for the data
		update_option("afdn_countdownOptions", $afdnOptions);//Update the WPDB for the options
		
		echo '<div id="message" class="updated fade"><p>'. __('Options updated successfully.', 'afdn_countdownTimer') .'</p></div>';					//Report to the user that the data has been updated successfully
	}

	$dates = get_option("afdn_countdowntracker"); //Get the events from the WPDB to make sure a fresh copy is being used
	$getOptions = get_option("afdn_countdownOptions");//Get the options from the WPDB to make sure a fresh copy is being used

	/*If the user wants, cycle through the array to find out if they have already occured, if so: set them to NULL*/
	if($getOptions["deleteOneTimeEvents"] && (count($dates["oneTime"][0])!=0) ){
		foreach($dates["oneTime"] as $key => $value){
			if(($value["date"]<=time())&&($value["timeSince"]=="")){
			$dates["oneTime"][$key]["text"]=NULL;
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
  
  				<p><?php _e("Alternatively, you can use the Countdown Timer Widget if you have widgets enabled (or have the ability to enable widgets).", 'afdn_countdownTimer'); ?></p>
				<p><?php _e("If you want to individually manage countdown timers, such as in posts or on pages, you can use the following code:", 'afdn_countdownTimer'); ?></p>
				<p>
					<code><?php _e("Time until my birthday:", 'afdn_countdownTimer'); ?><br />
					&lt;!--afdn_countdownTimer_single("<em>ENTER_DATE_HERE</em>")--&gt;</code>
				</p>
				<p><?php _e("Where <em>\"ENTER_DATE_HERE\"</em> uses <a href='http://us2.php.net/strtotime'>PHP's strtotime function</a> and will parse about any English textual datetime description. If you do this, be sure to enable the \"Enable CountdownTimer within The Loop\" option below.", 'afdn_countdownTimer'); ?></p>
  
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
				<p><?php _e("Countdown timer uses <a href='http://us2.php.net/strtotime'>PHP's strtotime function</a> and will parse about any English textual datetime description.", 'afdn_countdownTimer'); ?></p>
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
						if($dates != ""){
							for($i=0; $i < $oneTimeEvent_entriesCount+1; $i++){
								if($dates["oneTime"][$i]["text"]!=''){ //If the text is NULL, skip over it?>
								<tr id="oneTimeEvent_table<?php echo $oneTimeEvent_count; ?>">
								<td><a href="javascript:void(0);" onclick="javascript:clearField('oneTimeEvent','<?php echo $oneTimeEvent_count; ?>');">X</a></td>
								<td><input type="text" size="35" name="oneTimeEvent_date<?php echo $oneTimeEvent_count; ?>" value="<?php if($dates["oneTime"][$i]["date"] != "")echo gmdate("D, d M Y H:i:s", $dates["oneTime"][$i]["date"] + (get_option('gmt_offset') * 3600))." ".(get_option('gmt_offset')>="0"?"+":NULL).(get_option('gmt_offset')=="0"?"00":NULL).(get_option('gmt_offset')*100); ?>" /></td>
								<td><input type="text" size="25" name="oneTimeEvent_text<?php echo $oneTimeEvent_count; ?>" value="<?php echo htmlspecialchars(stripslashes($dates["oneTime"][$i]["text"])); ?>" /></td>
								<td><input type="text" size="25" name="oneTimeEvent_link<?php echo $oneTimeEvent_count; ?>" value="<?php echo $dates["oneTime"][$i]["link"]; ?>" /></td>
								<td><input type="checkbox" name="oneTimeEvent_timeSince<?php echo $oneTimeEvent_count; ?>" value="1" <?php print($dates["oneTime"][$i]["timeSince"]==1?"checked='checked'":NULL)?>/></td>
								</tr>
								<?php
								$oneTimeEvent_count++;
								 }
						
							@next($dates["oneTime"]);

							}

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
					<p><?php _e('Enable JavaScript countdown?', 'afdn_countdownTimer'); ?> <input name="enableJS" type="radio" value="1" <?php print($getOptions["enableJS"]==1?"checked='checked'":NULL)?> /><?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="enableJS" type="radio" value="0" <?php print($getOptions["enableJS"]==0?"checked='checked'":NULL)?>/><?php _e('No', 'afdn_countdownTimer'); ?></p>
			</fieldset>

			<!-- Include within The Loop/One-off Countdowns -->
			<fieldset class="options">
				<legend><strong><?php _e('Include in The Loop/One-off Timers', 'afdn_countdownTimer'); ?></strong></legend>
				<p><?php _e('To include Countdown Timer and/or One-off Timer within a post or page, simply enable The Loop function below and then insert', 'afdn_countdownTimer'); ?>:
				<code>&lt;!--afdn_countdownTimer--&gt;</code>
				<?php _e('where you want the countdown to be inserted', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('You can also insert a one-off timer within a post or page by using the following code:', 'afdn_countdownTimer'); ?>
				<code>&lt;!--afdn_countdownTimer_single("<em>ENTER_DATE_HERE</em>")--&gt;</code>
				</p>
				<p><?php _e('Enable CountdownTimer within The Loop?', 'afdn_countdownTimer'); ?>
				<input name="enableTheLoop" type="radio" value="1" <?php print($getOptions["enableTheLoop"]==1?"checked='checked'":NULL)?> /><?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="enableTheLoop" type="radio" value="0" <?php print($getOptions["enableTheLoop"]==0?"checked='checked'":NULL)?>/><?php _e('No', 'afdn_countdownTimer'); ?></p>

			</fieldset>
			<!-- Countdown Time Display -->
			<fieldset class="options">
				<legend><strong><?php _e('Countdown Time Display', 'afdn_countdownTimer'); ?></strong></legend>
				<p><?php _e('This settings controls what units of time are displayed.', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Years?', 'afdn_countdownTimer'); ?> <input name="showYear" type = "radio" value = "1" <?php print($getOptions["showYear"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showYear" type = "radio" value = "0" <?php print($getOptions["showYear"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Months?', 'afdn_countdownTimer'); ?> <input name="showMonth" type = "radio" value = "1" <?php print($getOptions["showMonth"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showMonth" type = "radio" value = "0" <?php print($getOptions["showMonth"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Weeks?', 'afdn_countdownTimer'); ?> <input name="showWeek" type = "radio" value = "1" <?php print($getOptions["showWeek"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showWeek" type = "radio" value = "0" <?php print($getOptions["showWeek"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Days?', 'afdn_countdownTimer'); ?> <input name="showDay" type = "radio" value = "1" <?php print($getOptions["showDay"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showDay" type = "radio" value = "0" <?php print($getOptions["showDay"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Hours?', 'afdn_countdownTimer'); ?> <input name="showHour" type = "radio" value = "1" <?php print($getOptions["showHour"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showHour" type = "radio" value = "0" <?php print($getOptions["showHour"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Minutes?', 'afdn_countdownTimer'); ?> <input name="showMinute" type = "radio" value = "1" <?php print($getOptions["showMinute"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showMinute" type = "radio" value = "0" <?php print($getOptions["showMinute"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Seconds?', 'afdn_countdownTimer'); ?> <input name="showSecond" type = "radio" value = "1" <?php print($getOptions["showSecond"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showSecond" type = "radio" value = "0" <?php print($getOptions["showSecond"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Strip non-significant zeros?', 'afdn_countdownTimer'); ?> <input name="stripZero" type = "radio" value = "1" <?php print($getOptions["stripZero"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="stripZero" type = "radio" value = "0" <?php print($getOptions["stripZero"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></p>
			</fieldset>	
				

			<!-- onHover Time Display -->
			<fieldset class="options">
				<legend><strong><?php _e('onHover Time Format', 'afdn_countdownTimer'); ?></strong></legend>
				<p><?php _e("If you set 'onHover Time Format', hovering over the time left will show the user what the date of the event is. onHover Time Format uses", 'afdn_countdownTimer'); ?> <a href="http://us2.php.net/date" target="_blank">PHP's Date() function</a>.</p>
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
				<p><?php _e('This setting allows you to customize how each event is styled and wrapped.', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('<strong>Display Style</strong> sets the HTML Style attribute for each timer.', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('<strong>Display Format Prefix/Suffix</strong> sets any leading or trailing HTML (or text).', 'afdn_countdownTimer'); ?></p>
				<p><?php _e('Examples/Defaults', 'afdn_countdownTimer'); ?>:</p>
				<ul>
					<li><em><?php _e('Style', 'afdn_countdownTimer'); ?>:</em> <code>cursor:pointer; border-bottom:1px black dashed</code></li>
					<li><em><?php _e('Prefix', 'afdn_countdownTimer'); ?>:</em> <code>&lt;li&gt;</code></li>
					<li><em><?php _e('Suffix', 'afdn_countdownTimer'); ?>:</em> <code>&lt;/li&gt;</code></li>
				</ul>
				
				<p><?php _e('Display Style', 'afdn_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($getOptions["displayStyle"])); ?>" name="displayStyle" /></p>
				<p><?php _e('Display Format Prefix', 'afdn_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($getOptions["displayFormatPrefix"])); ?>" name="displayFormatPrefix" /></p>
				<p><?php _e('Display Format Suffix', 'afdn_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($getOptions["displayFormatSuffix"])); ?>" name="displayFormatSuffix" /></p>
			</fieldset>

			<div class="submit"><input type="submit" name="info_update" value="<?php
				_e('Update Events', 'afdn_countdownTimer')
			 ?>&raquo;" /></div>
		</form>
	</div> <?
}

function afdn_countdownTimer_loop($theContent){		
	global $getOptions;
																					//Filter function for including the countdown with The Loop
	if(preg_match("<!--afdn_countdownTimer(\([0-9]+\))-->", $theContent)){																//If the string is found within the loop, replace it
		$theContent = preg_replace("/<!--afdn_countdownTimer(\(([0-9]+)\))?-->/e", "afdn_countdownTimer('return', $2)", $theContent);	//The actual replacement of the string with the timer
	}
	elseif(preg_match("<!--afdn_countdownTimer-->", $theContent)){																		//If the string is found within the loop, replace it
		$theContent = preg_replace("/<!--afdn_countdownTimer-->/e", "afdn_countdownTimer('return', -1)", $theContent);				//The actual replacement of the string with the timer
	}
	
	if(preg_match("<!--afdn_countdownTimer_single\((.*?)\)-->", $theContent)){
		$theContent = preg_replace("/<!--afdn_countdownTimer_single\(('|\")(.*?)('|\")\)-->/e", "fergcorp_countdownTimer_format('', strtotime('$2'), ".( date('Z') - (get_settings('gmt_offset') * 3600) ).", 'true', '', '".$getOptions['timeOffset']."', '', '', '')", $theContent);
	}
	
	return $theContent;																													//Return theContent
}

function afdn_countdownTimer_optionsPage(){																		//Action function for adding the configuration panel to the Management Page
	if(function_exists('add_management_page')){
			add_management_page('Countdown Timer', 'Countdown Timer', 10, basename(__FILE__), 'afdn_countdownTimer_myOptionsSubpanel');
	}
}


/*This function is called from your page to output the actual data*/
function afdn_countdownTimer($output = "echo", $eventLimit = -1){ //'echo' will print the results, 'return' will just return them

	$dates = get_option("afdn_countdowntracker");//Get our text, times, and settings from the database
	$getOptions = get_option("afdn_countdownOptions");//Get the options from the WPDB
	
	if(count($dates["oneTime"][0])!=0){
		foreach($dates["oneTime"] as $key => $value){
			if(($value["date"]<=time())&&($value["timeSince"]=="")){
			$dates["oneTime"][$key]["text"]=NULL;
			}
		}
	}

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
	if($eventLimit != -1)	//If the eventLimit is set
		$eventCount = $eventLimit;

	global $fergcorp_countdownTimer_noEventsPresent;
	$fergcorp_countdownTimer_noEventsPresent = TRUE;
	//This is the part that does the actual outputting. If you want to preface data, this an excellent spot to do it in.
	for($i = 0; $i < $eventCount; $i++){
		if($output == "echo")
			echo fergcorp_countdownTimer_format(stripslashes($thisDate[$i]["text"]), $thisDate[$i]["date"], 0, $thisDate[$i]["timeSince"], $thisDate[$i]["link"], $getOptions["timeOffset"], $getOptions["displayFormatPrefix"], $getOptions["displayFormatSuffix"], $getOptions["displayStyle"]);
		elseif($output == "return"){
			$toReturn .= fergcorp_countdownTimer_format(stripslashes($thisDate[$i]["text"]), $thisDate[$i]["date"], 0, $thisDate[$i]["timeSince"], $thisDate[$i]["link"], $getOptions["timeOffset"], $getOptions["displayFormatPrefix"], $getOptions["displayFormatSuffix"], $getOptions["displayStyle"]);
		}
		if(($thisDate[$i]["text"]==NULL) && (isset($thisDate[$i]))){
			$eventCount++;
		}
	}
	if($output == "return")
			return $toReturn;
	
	if($fergcorp_countdownTimer_noEventsPresent == TRUE){
		if($output == "echo"){
			echo $getOptions["displayFormatPrefix"].__('No dates present', 'afdn_countdownTimer').$getOptions["displayFormatSuffix"];
		}
		elseif($output == "return"){
			$toReturn .= $getOptions["displayFormatPrefix"].__('No dates present', 'afdn_countdownTimer').$getOptions["displayFormatSuffix"];
		}
	}
}
/*PLUGIN-WIDE FUNCTIONS*/

/*fergcorp_countdownTimer_format takes four variables and returns a single strong for the output of the plugin
$text is a string with just the text associated with a given date, for example "My 20th Birthday!" HTML formatting is allowed, just be sure to close your tags
$time is an integer formated in UNIX time.
$offset is actually an illconceived idea that shouldn't have been implemented here. Let it be zero and you won't have any problems.
$timeSince is a single integer representitive of a boolean value. 1 = True; 0 = False. This really should be passed along as a boolean value, so it's on the to do list to fix. In any event, if this value is set to "True", after an event has passed, the text will count up from the time the even happened. If it is set to "False, it will not count and the event will not be displayed.

Note: As of version 2.0, I'm using following the way that WordPress handles time zones issues and am using gmdate. At some point, this will actually revert back to using date and then setting the date with the date_timezone_set() functionality introducted in PHP5.

Simple enough?
*/

function fergcorp_countdownTimer_format($text, $time, $offset, $timeSince=0, $link=NULL, $timeFormat = "j M Y, G:i:s", $displayFormatPrefix = "<li>", $displayFormatSuffix = "</li>", $displayStyle = "cursor:pointer; border-bottom:1px black dashed"){
	global $fergcorp_countdownTimer_noEventsPresent, $getOptions;
	$time_left = $time - time() + $offset;
	if(($time_left < 0)&&($timeSince==1)){
		$fergcorp_countdownTimer_noEventsPresent = FALSE;
		if($text)
			$content = $displayFormatPrefix.($link==""?$text.":":"<a href=\"$link\"><strong>".$text.":</strong></a>")."<br />\n";
		if($timeFormat == "")
			$content .= fergcorp_countdownTimer_fuzzyDate((time() + $offset), $time, $time)." ago".$displayFormatSuffix;
		else
			$content .= "<abbr title = \"".gmdate($timeFormat, $time + (get_option('gmt_offset') * 3600))."\" style=\"". $displayStyle ."\">".fergcorp_countdownTimer_fuzzyDate((time() + $offset), $time, $time)." ago</abbr>".$displayFormatSuffix;
		//echo $content;
		return $content;
	}
	elseif($time_left > 0){
		$fergcorp_countdownTimer_noEventsPresent = FALSE;
		if($text)
			$content = $displayFormatPrefix.($link==""?$text.":":"<a href=\"$link\"><strong>".$text.":</strong></a>")."<br />\n";
		if($timeFormat == "")
			$content .= fergcorp_countdownTimer_fuzzyDate($time, (time() + $offset), $time).$displayFormatSuffix;
		else
			$content .= "<abbr title = \"".gmdate($timeFormat, $time + (get_option('gmt_offset') * 3600))."\" style=\"". $displayStyle ."\">in ".fergcorp_countdownTimer_fuzzyDate($time, (time() + $offset), $time)."</abbr>".$displayFormatSuffix;
			//echo $content;
			return $content;
	}
	else{
		return NULL;
	}
}


//It's amazing how hard counting can really be.
function fergcorp_countdownTimer_fuzzyDate($targetTime, $nowTime, $realTargetTime){
	global $getOptions;
	global $fergcorp_countdownTimer_nonceTracker;
	if(!isset($fergcorp_countdownTimer_nonceTracker))
		$fergcorp_countdownTimer_nonceTracker = array();
	
	$rollover = 0;
	$s = '';
	$sigNumHit = false;

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
	if($getOptions['showYear']){
		if($sigNumHit || !$getOptions['stripZero'] || $resultantYear){
			$s = $resultantYear.' '.($resultantYear==1?__("year", "afdn_countdownTimer"):__("years", "afdn_countdownTimer")).', ';
			$sigNumHit = true;
		}
	}
	else{
		$rollover = $resultantYear*31536000;
	}

	//Month	
	if($getOptions['showMonth']){
		if($sigNumHit || !$getOptions['stripZero'] || $resultantMonth){
			$s = $s.($resultantMonth + intval($rollover/2592000)).' '.($resultantMonth==1?__("month", "afdn_countdownTimer"):__("months", "afdn_countdownTimer")).', ';
			$rollover = $rollover - intval($rollover/2592000)*2592000;
			$sigNumHit = true;
		}
	}
	else{
		$rollover = $rollover + $resultantMonth*2592000;
	}
	
	//Week (weeks are counted differently becuase we can just take 7 days and call it a week...so we do that)
	if($getOptions['showWeek']){
		if($sigNumHit || !$getOptions['stripZero'] || intval( ($resultantDay + intval($rollover/86400) )/7)){
			$s = $s.intval( ($resultantDay + intval($rollover/86400) )/7 ).' '.((intval( ($resultantDay + intval($rollover/86400) )/7))==1?__("week", "afdn_countdownTimer"):__("weeks", "afdn_countdownTimer")).', ';
			$rollover = $rollover - intval($rollover/86400)*86400;
			$resultantDay = $resultantDay - intval( ($resultantDay + intval($rollover/86400) )/7 )*7;
			$sigNumHit = true;
		}
	}

	//Day
	if($getOptions['showDay']){
		if($sigNumHit || !$getOptions['stripZero'] || $resultantDay){
			$s = $s.($resultantDay + intval($rollover/86400)).' '.($resultantDay==1?__("day", "afdn_countdownTimer"):__("days", "afdn_countdownTimer")).', ';
			$rollover = $rollover - intval($rollover/86400)*86400;
			$sigNumHit = true;
		}
	}
	else{
		$rollover = $rollover + $resultantDay*86400;
	}
	
	//Hour
	if($getOptions['showHour']){
		if($sigNumHit || !$getOptions['stripZero'] || $resultantHour){
			$s = $s.($resultantHour + intval($rollover/3600)).' '.($resultantHour==1?__("hour", "afdn_countdownTimer"):__("hours", "afdn_countdownTimer")).', ';
			$rollover = $rollover - intval($rollover/3600)*3600;
			$sigNumHit = true;
		}
	}
	else{
		$rollover = $rollover + $resultantHour*3600;
	}
	
	//Minute
	if($getOptions['showMinute']){
		if($sigNumHit || !$getOptions['stripZero'] || $resultantMinute){
			$s = $s.($resultantMinute + intval($rollover/60)).' '.($resultantMinute==1?__("minute", "afdn_countdownTimer"):__("minutes", "afdn_countdownTimer")).', ';
			$rollover = $rollover - intval($rollover/60)*60;
			$sigNumHit = true;
		}
	}
	else{
		$rollover = $rollover + $resultantMinute*60;
	}
	
	//Second
	if($getOptions['showSecond']){
		$s = $s.($resultantSecond + $rollover).' '.($resultantSecond==1?__("second", "afdn_countdownTimer"):__("seconds", "afdn_countdownTimer")).', ';
	}

	$nonceTracker = md5(rand());
	
	$fergcorp_countdownTimer_nonceTracker[count($fergcorp_countdownTimer_nonceTracker)] = array("id"			=> $nonceTracker,
																								"targetDate"	=> $realTargetTime,
																								);
	
	return "<span id = '$nonceTracker'>".rtrim($s,", ")."</span>"; //...and return the result (a string)
}

add_action('admin_menu', 'afdn_countdownTimer_optionsPage');	//Add Action for adding the options page to admin panel

add_action('activate_'.plugin_basename(__FILE__), 'afdn_countdownTimer_install');

function afdn_countdownTimer_install(){
	$version = get_option("fergcorp_countdownTimer_version");
	
	update_option("widget_fergcorp_countdown", array("title"=>"Countdown Timer", "count"=>"-1"));
		
	$getOptions = get_option("afdn_countdownOptions");
	
	if($version == NULL){ //Version < 1.8
			
		if($getOptions == NULL){ //No Previous Install (i.e. first time user)			
			$afdnOptions = array(	"deleteOneTimeEvents" => "0",					//Should One Time Events be deleted after the happen (boolean)
									"checkUpdate" => "1",									//Should the plugin check for updates (boolean)
									"timeOffset" => "F jS, Y, g:i a",									//What is the time format
									"enableTheLoop" => "0",								//Should the timer be allowed within the loop (boolean)
									"displayFormatPrefix" => "<li>",					
									"displayFormatSuffix" => "</li>",
									"displayStyle" => "cursor:pointer; border-bottom:1px black dashed",
									"showYear" => "1",
									"showMonth" => "1",
									"showWeek" => "0",
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
									"displayStyle" => "cursor:pointer; border-bottom:1px black dashed",
									"showYear" => "1",
									"showMonth" => "1",
									"showWeek" => "0",
									"showDay" => "1",
									"showHour" => "1",
									"showMinute" => "1",
									"showSecond" => "0",
									); //Create the array to store the countdown options
			
		}
	}
	elseif($version < 1.9){
				$afdnOptions = array(	"deleteOneTimeEvents" => $getOptions['deleteOneTimeEvents'],					//Should One Time Events be deleted after the happen (boolean)
									"checkUpdate" => $getOptions['checkUpdate'],									//Should the plugin check for updates (boolean)
									"timeOffset" => $getOptions['timeOffset'],									//What is the time format
									"enableTheLoop" => $getOptions['enableTheLoop'],								//Should the timer be allowed within the loop (boolean)
									"displayFormatPrefix" => $getOptions['displayFormatPrefix'],					
									"displayFormatSuffix" => $getOptions['displayFormatSuffix'],
									"displayStyle" => "cursor:pointer; border-bottom:1px black dashed",
									"showYear" => $getOptions['showYear'],
									"showMonth" => $getOptions['showMonth'],
									"showWeek" => "0",
									"showDay" => $getOptions['showDay'],
									"showHour" => $getOptions['showHour'],
									"showMinute" => $getOptions['showMinute'],
									"showSecond" => $getOptions['showSecond'],
									);
	}
	
	update_option("afdn_countdownOptions", $afdnOptions);//Update the WPDB for the options*/
	update_option("fergcorp_countdownTimer_version", "1.9");
}

$getOptions = get_option("afdn_countdownOptions");	//Get the options from the WPDB (this is actually pretty sloppy on my part and should be fixed)

if($getOptions["enableTheLoop"]){								//If the timer is to be allowed in The Loop, run this
	add_filter('the_content', 'afdn_countdownTimer_loop', 1);
}

//$getOption("enableJS")

if(1) {
    add_action('wp_footer', 'afdn_countdownTimer_js');
}

function afdn_countdownTimer_js(){
	global $fergcorp_countdownTimer_nonceTracker;
	//ksort(\$fergcorp_countdownTimer_nonceTracker);
	echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	echo "<!--\n";
	
	echo "var fergcorp_countdownTimer_js_language = new Array();\n";
	echo "fergcorp_countdownTimer_js_language['year'] = '".addslashes(__('year', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['years'] = '".addslashes(__('years', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['month'] = '".addslashes(__('month', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['months'] = '".addslashes(__('months', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['week'] = '".addslashes(__('week', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['weeks'] = '".addslashes(__('weeks', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['day'] = '".addslashes(__('day', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['days'] = '".addslashes(__('days', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['hour'] = '".addslashes(__('hour', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['hours'] = '".addslashes(__('hours', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['minute'] = '".addslashes(__('minute', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['minutes'] = '".addslashes(__('minutes', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['second'] = '".addslashes(__('second', 'afdn_countdownTimer'))."';\n";
	echo "fergcorp_countdownTimer_js_language['seconds'] = '".addslashes(__('seconds', 'afdn_countdownTimer'))."';\n";
	
	echo "var fergcorp_countdownTimer_js_events = new Array();\n";
	for($i=0; $i < count($fergcorp_countdownTimer_nonceTracker); $i++){
			echo "fergcorp_countdownTimer_js_events[$i] = new Array()\n";
			echo "fergcorp_countdownTimer_js_events[$i]['id'] 		= \"".$fergcorp_countdownTimer_nonceTracker[$i]['id']."\";\n";
			echo "fergcorp_countdownTimer_js_events[$i]['targetDate'] 	= \"".$fergcorp_countdownTimer_nonceTracker[$i]['targetDate']."\";\n";
	
	}
	echo "//-->\n";
	echo "</script>\n";
	/*echo '<script language="JavaScript" type="text/javascript" src="wp-content/plugins/countdown-timer/afdn_countdownTimer.php?showJavaScript"></script>';*/
	echo '<script language="JavaScript" type="text/javascript" src="wp-content/plugins/countdown-timer/fergcorp_countdownTimer_java.php"></script>';
}



// Put functions into one big function we'll call at the plugins_loaded
// action. This ensures that all required plugin functions are defined.

if(!function_exists('widget_fergcorp_countdown_init')){

	function widget_fergcorp_countdown_init() {
	
		// Check for the required plugin functions. This will prevent fatal
		// errors occurring when you deactivate the dynamic-sidebar plugin.
		if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
			return;
			
		// This saves options and prints the widget's config form.
		function widget_fergcorp_countdown_control() {
			$options = $newoptions = get_option('widget_fergcorp_countdown');
			if ( $_POST['countdown-submit'] ) {
				$newoptions['title'] = strip_tags(stripslashes($_POST['countdown-title']));
				$newoptions['count'] = (int) $_POST['countdown-count'];
			}
			if ( $options != $newoptions ) {
				$options = $newoptions;
				update_option('widget_fergcorp_countdown', $options);
			}
		?>
					<div style="text-align:left">
					<label for="countdown-title" style="line-height:35px;display:block;"><?php _e('Widget title:', 'afdn_countdownTimer'); ?> <input type="text" id="countdown-title" name="countdown-title" value="<?php echo wp_specialchars($options['title'], true); ?>" /></label>
					<label for="countdown-count" style="line-height:35px;display:block;"><?php _e('Maximum # of events to show:', 'afdn_countdownTimer'); ?> <input type="text" id="countdown-count" name="countdown-count" value="<?php echo $options['count']; ?>" size="5"/></label>
					<input type="hidden" name="countdown-submit" id="countdown-submit" value="1" />
					<small><strong><?php _e('Notes:', 'widget_fergcorp_countdown'); ?></strong> <?php _e("Set 'Maximum # of events' to '-1' if you want no limit.", 'afdn_countdownTimer'); ?></small>
					</div>
		<?php
		}
			
			
	
		// This is the function that outputs our little Google search form.
		function widget_fergcorp_countdown($args) {
		
			$options = get_option('widget_fergcorp_countdown');
			
			// $args is an array of strings that help widgets to conform to
			// the active theme: before_widget, before_title, after_widget,
			// and after_title are the array keys. Default tags: li and h2.
			extract($args);
			
			$title = $options['title'];
	
			// These lines generate our output. Widgets can be very complex
			// but as you can see here, they can also be very, very simple.
			echo $before_widget . $before_title . $title . $after_title;
	
			?>
				<ul>
					<?php afdn_countdownTimer("echo", $options['count']); ?>
				</ul>
			<?php
			echo $after_widget;
		}
	
		// This registers our widget so it appears with the other available
		// widgets and can be dragged and dropped into any active sidebars.
		register_sidebar_widget(array('Countdown Widget', 'widgets'), 'widget_fergcorp_countdown');
		register_widget_control(array('Countdown Widget', 'widgets'), 'widget_fergcorp_countdown_control');
	
	}
	
	// Run our code later in case this loads prior to any required plugins.
	add_action('widgets_init', 'widget_fergcorp_countdown_init');
}
?>