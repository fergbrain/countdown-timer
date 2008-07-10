<?php
/*
Plugin Name: Countdown Timer
Plugin URI: http://www.andrewferguson.net/wordpress-plugins/countdown-timer/
Plugin Description: Add template tags and widget to count down or up to the years, months, weeks, days, hours, minutes, and/or seconds to a particular event.
Version: 2.2.3
Author: Andrew Ferguson
Author URI: http://www.andrewferguson.net

Countdown Timer - Add template tags and widget to count down the years, months, weeks, days, hours, and minutes to a particular event
Copyright (c) 2005-2008 Andrew Ferguson

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
		
	$currentLocale = get_locale();
	if(!empty($currentLocale)) {
		$moFile = dirname(__FILE__) . "/afdn_countdownTimer-" . $currentLocale . ".mo";
		if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('afdn_countdownTimer', $moFile);
	}

	/**
	 * Displays the option page
	 *
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	 */
	function afdn_countdownTimer_myOptionsSubpanel(){

		if (isset($_POST['afdn_countdownTimer_update']))														//If the user has submitted the form, do the following
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

			$afdnOptions = array(	"deleteOneTimeEvents" 	=> $_POST['deleteOneTimeEvents'],
									"checkUpdate" 			=> $_POST['checkUpdate'],
									"timeOffset"			=> $_POST['timeOffset'],
									"enableTheLoop"		 	=> $_POST['enableTheLoop'],
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
									"enableJS"				=> $_POST['enableJS'],
									"timeSinceTime"			=> (int)$_POST['timeSinceTime'],
									"titleSuffix"			=> $_POST['titleSuffix'],									
									); //Create the array to store the countdown options

			update_option("afdn_countdowntracker", $results); //Update the WPDB for the data
			update_option("afdn_countdownOptions", $afdnOptions);//Update the WPDB for the options

			echo '<div id="message" class="updated fade"><p>'. __('Options/dates updated successfully.', 'afdn_countdownTimer') .'</p></div>';					//Report to the user that the data has been updated successfully
		}

		$dates = get_option("afdn_countdowntracker"); //Get the events from the WPDB to make sure a fresh copy is being used
		$fergcorp_countdownTimer_getOptions = get_option("afdn_countdownOptions");//Get the options from the WPDB to make sure a fresh copy is being used

		/*If the user wants, cycle through the array to find out if they have already occured, if so: set them to NULL*/
		if($fergcorp_countdownTimer_getOptions["deleteOneTimeEvents"] && (count($dates["oneTime"][0])!=0) ){
			foreach($dates["oneTime"] as $key => $value){
				if(($value["date"]<=time())&&($value["timeSince"]=="")){
				$dates["oneTime"][$key]["text"]=NULL;
				}
			}
		}
		?>

			<script type="text/javascript">

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
			
            <div id="wpbody">

			<div class="wrap" id="afdn_countdownTimer_div">

				<h2>Countdown Timer</h2>
            
				<div id="poststuff">        
                    
						<div class="stuffbox">
							<h3><?php _e('Resources:','afdn_countdownTimer'); ?></h3>
							<div class="inside">
								<table width="90%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><ul><li><a href="http://andrewferguson.net/wordpress-plugins/countdown-timer/" target="_blank"><?php _e('Plugin Homepage','afdn_countdownTimer'); ?></a></li></ul></td>
                                    <td><ul><li><a href="http://wordpress.org/tags/countdown-timer" target="_blank"><?php _e('Support Forum','afdn_countdownTimer'); ?></a></li></ul></td>
                                    <td><ul><li><a href="http://www.amazon.com/gp/registry/registry.html?ie=UTF8&type=wishlist&id=E7Q6VO0I8XI4" target="_blank"><?php _e('Amazon Wishlist','afdn_countdownTimer'); ?></a></li></ul></td>
                                    <td><ul><li><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                       <input type="hidden" name="cmd" value="_s-xclick" />
                                       <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" />
                                       <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
                                       <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
                                       " />
                                </form></li></ul></td>
                                	<?php if(__('translator_name','afdn_countdownTimer')!='translator_name') {?>
	                                    <td><ul><li><?php _e('Translated by', 'afdn_countdownTimer'); ?> 
                                        	<?php if(__('translator_url','afdn_countdownTimer')!='translator_url') {?>
                                            	<a href="<?php _e('translator_url','afdn_countdownTimer'); ?>"><?php _e('translator_name','afdn_countdownTimer'); ?></a>
											<?php } else { ?>
                                            	<?php _e('translator_name','afdn_countdownTimer'); ?>
                                             <?php } ?>
										</li></ul></td>
                                    <?php } ?>
                                  </tr>
                                </table>

								<p><?php _e("I've been spending more and more time writing and supporting plugins. I'm a college student and really only do this programming thing on the side for the love of it.", 'afdn_countdownTimer'); ?></p>
							</div>
						</div>
   
                        <form method="post" name="afdn_countdownTimer" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

                            <!-- Installation -->
						<div id="fergcorp_countdownTimer_installation" class="postbox closed" >
							<h3><?php _e('Installation Notes', 'afdn_countdownTimer') ?></h3>
								<div class="inside">
										<p><?php _e("You've made it this far, you're almost there. To insert the Countdown Timer into your sidebar, you can use the Countdown Timer Widget if you have widgets enabled (or have the ability to enable widgets).", 'afdn_countdownTimer'); ?></p>
										<p><?php _e("Alternatively, you can also use this code", 'afdn_countdownTimer'); ?>:</p>
										<p>
											<code>&lt;li id='countdown'&gt;&lt;h2&gt;Countdown:&lt;/h2&gt;<br />
												&lt;ul&gt;<br />
												&lt;?php afdn_countdownTimer(); ?&gt;<br />
												&lt;/ul&gt;<br />
												&lt;/li&gt;
											</code>
										</p>
											<p><?php _e("If you want to individually manage countdown timers, such as in posts or on pages, you can use the following code:", 'afdn_countdownTimer'); ?></p>
											<p>
											<code><?php _e("Time until my birthday:", 'afdn_countdownTimer'); ?><br />
													&lt;!--afdn_countdownTimer_single("<em>ENTER_DATE_HERE</em>")--&gt;
											</code>
										</p>
										<p><?php _e("Where <em>\"ENTER_DATE_HERE\"</em> uses <a href='http://us2.php.net/strtotime' target='_blank'>PHP's strtotime function</a> and will parse about any English textual datetime description. If you do this, be sure to enable the \"Enable CountdownTimer within The Loop\" option below.", 'afdn_countdownTimer'); ?></p>
								</div>
						</div>

						<!-- Basic Options -->
						<div id="fergcorp_countdownTimer_basic_options" class="postbox">
							<h3><?php _e('One Time Events', 'afdn_countdownTimer') ?></h3>
							<div class="inside">
								<p><?php _e("Countdown timer uses <a href='http://us2.php.net/strtotime'>PHP's strtotime function</a> and will parse about any English textual datetime description.", 'afdn_countdownTimer'); ?></p>
										<p><?php _e('Examples of some (but not all) valid dates', 'afdn_countdownTimer'); ?>:</p>
										<ul>
													<li>now</li>
													<li>31 january 1986</li>
													<li>+1 day</li>
													<li>next thursday</li>
													<li>last monday</li>
										</ul>
										<table border="0" cellspacing="0" cellpadding="2">
										<tr align="center">
											<td><strong><?php _e('Delete', 'afdn_countdownTimer'); ?></strong></td>
											<td><?php _e('Event Date', 'afdn_countdownTimer'); ?></td>
											<td><?php _e('Event Title', 'afdn_countdownTimer'); ?></td>
											<td><?php _e('Link', 'afdn_countdownTimer'); ?></td>
											<td><?php _e('Display "Time since"', 'afdn_countdownTimer'); ?></td>
										</tr>
											<?php
												//global $count;
												$oneTimeEvent_count = 0;
												$oneTimeEvent_entriesCount = count($dates["oneTime"]);
												if($dates != ""){
													for($i=0; $i < $oneTimeEvent_entriesCount+1; $i++){
														if($dates["oneTime"][$i]["text"]!=''){ //If the text is NULL, skip over it?>
														<tr id="oneTimeEvent_table<?php echo $oneTimeEvent_count; ?>" align="center">
														<td><a href="javascript:void(0);" onclick="javascript:clearField('oneTimeEvent','<?php echo $oneTimeEvent_count; ?>');">X</a></td>
														<td><input type="text" size="30" name="oneTimeEvent_date<?php echo $oneTimeEvent_count; ?>" value="<?php if($dates["oneTime"][$i]["date"] != "")echo gmdate("D, d M Y H:i:s", $dates["oneTime"][$i]["date"] + (get_option('gmt_offset') * 3600))." ".(get_option('gmt_offset')>="0"?"+":NULL).(get_option('gmt_offset')=="0"?"00":NULL).(get_option('gmt_offset')*100); ?>" /></td>
														<td><input type="text" size="20" name="oneTimeEvent_text<?php echo $oneTimeEvent_count; ?>" value="<?php echo htmlspecialchars(stripslashes($dates["oneTime"][$i]["text"])); ?>" /></td>
														<td><input type="text" size="15" name="oneTimeEvent_link<?php echo $oneTimeEvent_count; ?>" value="<?php echo $dates["oneTime"][$i]["link"]; ?>" /></td>
														<td><input type="checkbox" name="oneTimeEvent_timeSince<?php echo $oneTimeEvent_count; ?>" value="1" <?php print($dates["oneTime"][$i]["timeSince"]==1?"checked='checked'":NULL)?>/></td>
														</tr>
														<?php
														$oneTimeEvent_count++;
														 }
														@next($dates["oneTime"]);
														}
													}
													?><tr align="center">
													<td></td>
													<td><input type="text" size="30" name="oneTimeEvent_date<?php echo $oneTimeEvent_count; ?>" /></td>
													<td><input type="text" size="20" name="oneTimeEvent_text<?php echo $oneTimeEvent_count; ?>" /></td>
													<td><input type="text" size="15" name="oneTimeEvent_link<?php echo $oneTimeEvent_count; ?>" /></td>
													<td><input type="checkbox" name="oneTimeEvent_timeSince<?php echo $oneTimeEvent_count; ?>" /></td>
													</tr>
										</table>
											<?php echo '<input type="hidden" name="oneTimeEvent_count" value="'.($oneTimeEvent_count+1).'" />'; ?>

										<p><?php _e("Automatically delete 'One Time Events' after they have occured?", 'afdn_countdownTimer'); ?> <input name="deleteOneTimeEvents" type="radio" value="1" <?php print($fergcorp_countdownTimer_getOptions["deleteOneTimeEvents"]==1?"checked='checked'":NULL)?> /><?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="deleteOneTimeEvents" type="radio" value="0" <?php print($fergcorp_countdownTimer_getOptions["deleteOneTimeEvents"]==0?"checked='checked'":NULL)?>/><?php _e('No', 'afdn_countdownTimer'); ?></p>
							</div>
						</div>

						<!-- Management -->
						<div id="fergcorp_countdownTimer_management" class="postbox closed">
							<h3><?php _e('Management', 'afdn_countdownTimer') ?></h3>
							<div class="inside">
								<p><?php _e('To include Countdown Timer(s) and/or One-off Timer(s) within a post or page, simply enable The Loop function below and then insert', 'afdn_countdownTimer'); ?>:</p>
								<code>&lt;!--afdn_countdownTimer--&gt;</code>
								<p><?php _e('where you want the countdown to be inserted', 'afdn_countdownTimer'); ?></p>
								<p><?php _e('You can also insert a one-off timer within a post or page by using the following code:', 'afdn_countdownTimer'); ?></p>
								<code>&lt;!--afdn_countdownTimer_single("<em>ENTER_DATE_HERE</em>")--&gt;</code>
								<ul>
									<li><?php _e('Enable JavaScript countdown:', 'afdn_countdownTimer'); ?> <input name="enableJS" type="radio" value="1" <?php print($fergcorp_countdownTimer_getOptions["enableJS"]==1?"checked='checked'":NULL)?> /><?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="enableJS" type="radio" value="0" <?php print($fergcorp_countdownTimer_getOptions["enableJS"]==0?"checked='checked'":NULL)?>/><?php _e('No', 'afdn_countdownTimer'); ?></li>
									<li><?php _e('Enable CountdownTimer within The Loop:', 'afdn_countdownTimer'); ?> <input name="enableTheLoop" type="radio" value="1" <?php print($fergcorp_countdownTimer_getOptions["enableTheLoop"]==1?"checked='checked'":NULL)?> /><?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="enableTheLoop" type="radio" value="0" <?php print($fergcorp_countdownTimer_getOptions["enableTheLoop"]==0?"checked='checked'":NULL)?>/><?php _e('No', 'afdn_countdownTimer'); ?></li>
								</ul>
							</div>	
						</div>


						<!-- Display Options -->
						<div id="fergcorp_countdownTimer_display_options" class="postbox closed">
                        	<h3><?php _e('Countdown Time Display', 'afdn_countdownTimer') ?></h3>
							<div class="inside">
								<p><?php _e('This setting controls what units of time are displayed.', 'afdn_countdownTimer'); ?></p>
								<ul>
									<li><?php _e('Years:', 'afdn_countdownTimer'); ?> <input name="showYear" type = "radio" value = "1" <?php print($fergcorp_countdownTimer_getOptions["showYear"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showYear" type = "radio" value = "0" <?php print($fergcorp_countdownTimer_getOptions["showYear"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></li>
									<li><?php _e('Months:', 'afdn_countdownTimer'); ?> <input name="showMonth" type = "radio" value = "1" <?php print($fergcorp_countdownTimer_getOptions["showMonth"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showMonth" type = "radio" value = "0" <?php print($fergcorp_countdownTimer_getOptions["showMonth"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></li>
									<li><?php _e('Weeks:', 'afdn_countdownTimer'); ?> <input name="showWeek" type = "radio" value = "1" <?php print($fergcorp_countdownTimer_getOptions["showWeek"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showWeek" type = "radio" value = "0" <?php print($fergcorp_countdownTimer_getOptions["showWeek"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></li>
									<li><?php _e('Days:', 'afdn_countdownTimer'); ?> <input name="showDay" type = "radio" value = "1" <?php print($fergcorp_countdownTimer_getOptions["showDay"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showDay" type = "radio" value = "0" <?php print($fergcorp_countdownTimer_getOptions["showDay"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></li>
									<li><?php _e('Hours:', 'afdn_countdownTimer'); ?> <input name="showHour" type = "radio" value = "1" <?php print($fergcorp_countdownTimer_getOptions["showHour"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showHour" type = "radio" value = "0" <?php print($fergcorp_countdownTimer_getOptions["showHour"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></li>
									<li><?php _e('Minutes:', 'afdn_countdownTimer'); ?> <input name="showMinute" type = "radio" value = "1" <?php print($fergcorp_countdownTimer_getOptions["showMinute"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showMinute" type = "radio" value = "0" <?php print($fergcorp_countdownTimer_getOptions["showMinute"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></li>
									<li><?php _e('Seconds:', 'afdn_countdownTimer'); ?> <input name="showSecond" type = "radio" value = "1" <?php print($fergcorp_countdownTimer_getOptions["showSecond"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="showSecond" type = "radio" value = "0" <?php print($fergcorp_countdownTimer_getOptions["showSecond"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></li>
									<li><?php _e('Strip non-significant zeros:', 'afdn_countdownTimer'); ?> <input name="stripZero" type = "radio" value = "1" <?php print($fergcorp_countdownTimer_getOptions["stripZero"]==1?"checked='checked'":NULL) ?> /> <?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="stripZero" type = "radio" value = "0" <?php print($fergcorp_countdownTimer_getOptions["stripZero"]==0?"checked='checked'":NULL) ?> /> <?php _e('No', 'afdn_countdownTimer'); ?></li>
								</ul>
                                <p><?php _e("How long the timer remain visable if \"Display 'Time Since'\" is ticked:", 'afdn_countdownTimer'); ?><br />
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php _e("Seconds: ", 'afdn_countdownTimer'); ?><input type="text" value="<?php print(htmlspecialchars(stripslashes($fergcorp_countdownTimer_getOptions["timeSinceTime"]))); ?>" name="timeSinceTime" size="10" /> <?php _e("(0 = infinite; 86400 seconds = 1 day; 604800 seconds = 1 week)", "afdn_countdownTimer"); ?></p>
							</div>
						</div>


						<!-- Time Format -->
						<div id="fergcorp_countdownTimer_onHover_time_format" class="postbox closed">
							<h3><?php _e('onHover Time Format', 'afdn_countdownTimer') ?></h3>
							<div class="inside">
								<p><?php _e("If you set 'onHover Time Format', hovering over the time left will show the user what the date of the event is. onHover Time Format uses", 'afdn_countdownTimer'); ?> <a href="http://us2.php.net/date" target="_blank">PHP's Date() function</a>.</p>
								<p><?php _e('Examples', 'afdn_countdownTimer'); ?>:</p>
								<ul>
									<li>"<em>j M Y, G:i:s</em>" <?php _e('goes to', 'afdn_countdownTimer'); ?> "<strong>17 Mar 2008, 14:50:00</strong>"</li>
									<li>"<em>F jS, Y, g:i a</em>" <?php _e('goes to', 'afdn_countdownTimer'); ?> "<strong>March 17th, 2008, 2:50 pm</strong>"</li>
								</ul>
								<p><?php _e('onHover Time Format', 'afdn_countdownTimer'); ?> <input type="text" value="<?php print(htmlspecialchars(stripslashes($fergcorp_countdownTimer_getOptions["timeOffset"]))); ?>" name="timeOffset" /></p>
							</div>
						</div>

						<!-- Includes -->
						<div id="fergcorp_countdownTimer_display_format_options" class="postbox closed">
                           	<h3><?php _e('Display Format Options', 'afdn_countdownTimer') ?></h3>
							<div class="inside">
								<p><?php _e('This setting allows you to customize how each event is styled and wrapped.', 'afdn_countdownTimer'); ?></p>
								<p><?php _e('<strong>Title Suffix</strong> sets the content that appears immediately after title and before the timer.', 'afdn_countdownTimer'); ?></p>
								<p><?php _e('<strong>Display Style</strong> sets the HTML Style attribute for each timer using CSS.', 'afdn_countdownTimer'); ?></p>
								<p><?php _e('<strong>Display Format Prefix/Suffix</strong> sets any leading or trailing HTML (or text).', 'afdn_countdownTimer'); ?></p>
								<p><?php _e('Examples/Defaults', 'afdn_countdownTimer'); ?>:</p>
								<ul>
                                       <li><em><?php _e('Title Suffix', 'afdn_countdownTimer'); ?>:</em> <code>:&lt;br /&gt;</code></li>
                                       <li><em><?php _e('Display Style', 'afdn_countdownTimer'); ?>:</em> <code>cursor:pointer; border-bottom:1px black dashed</code></li>
                                       <li><em><?php _e('Display Format Prefix', 'afdn_countdownTimer'); ?>:</em> <code>&lt;li&gt;</code></li>
                                       <li><em><?php _e('Display Format Suffix', 'afdn_countdownTimer'); ?>:</em> <code>&lt;/li&gt;</code></li>
                                   </ul>
                                   <p><?php _e('Title Suffix', 'afdn_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($fergcorp_countdownTimer_getOptions["titleSuffix"])); ?>" name="titleSuffix" /></p>
                                   <p><?php _e('Display Style', 'afdn_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($fergcorp_countdownTimer_getOptions["displayStyle"])); ?>" name="displayStyle" /></p>
                                   <p><?php _e('Display Format Prefix', 'afdn_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($fergcorp_countdownTimer_getOptions["displayFormatPrefix"])); ?>" name="displayFormatPrefix" /></p>
                                   <p><?php _e('Display Format Suffix', 'afdn_countdownTimer'); ?> <input type="text" value="<?php echo htmlspecialchars(stripslashes($fergcorp_countdownTimer_getOptions["displayFormatSuffix"])); ?>" name="displayFormatSuffix" /></p>
							</div>
						</div>

						<!-- Example Display -->
						<div id="fergcorp_countdownTimer_example_display" class="postbox">
                        	<h3><?php _e('Example Display', 'afdn_countdownTimer') ?></h3>
							<div class="inside">
								<ul>
									<?php afdn_countdownTimer(); ?>
                                    <?php afdn_countdownTimer_js(); ?>
								</ul>
							</div>
						</div>

						<div>
							<p class="submit">
								<input type="submit" name="afdn_countdownTimer_update" value="<?php _e('Update Events', 'afdn_countdownTimer'); ?>&raquo;" />
							</p>
						</div>
						</form>
				</div>
        

			</div>
            </div>
            <script type="text/javascript">add_postbox_toggles('fergcorp_countdownTimer');</script>
	<?php

	}

	/**
	 * Returns/echos the formated output for the countdown
	 *
	 * @param $output string If set to 'echo', will echo the results with no return; If set to 'return', will return the results with no echo.
	 * @param $eventLimit int The maximum number of events to echo or return, sorted by date
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	 * @return string If set, will return the formated output ready for display
	*/
	function afdn_countdownTimer($output = "echo", $eventLimit = -1){ //'echo' will print the results, 'return' will just return them
		global $fergcorp_countdownTimer_getOptions;
		
		$dates = get_option("afdn_countdowntracker");//Get our text, times, and settings from the database
		$fergcorp_countdownTimer_getOptions = get_option("afdn_countdownOptions");//Get the options from the WPDB

		//Remove events that shouldn't be displayed because the time elapsed and the Time Since option isn't ticked
		if($dates!=''){
			if(count($dates["oneTime"][0])!=0){
				foreach($dates["oneTime"] as $key => $value){
					if(($value["date"]<=time())&&($value["timeSince"]=="")){
					$dates["oneTime"][$key]["text"]=NULL;
					}
				}
			}
			else{
				return NULL; //because there are no dates at all!
			}
		}
		else{
			return NULL; //because there are no dates at all!
		}

		/*Now that all the events are in the same array, we need to sort them by date. This is actually the same code used above for the admin page.
		At some point, I plan to make this into a function; but for, this will do...

		And what it does is this:
		The number of elements in the array are counted. Then for array is gone through x^(x-1) times. This allows for all posible date permuations to be sorted out and ordered correctly.
		Genious, yes? No*/
		$eventCount = count($dates["oneTime"]);
		for($x=0; $x<$eventCount; $x++){
			for($z=0; $z<$eventCount-1; $z++){
				if(($dates["oneTime"][$z+1]["date"] < $dates["oneTime"][$z]["date"]) && (array_key_exists($z+1, $dates["oneTime"]))){
					$temp = $dates["oneTime"][$z];
					$dates["oneTime"][$z] = $dates["oneTime"][$z+1];
					$dates["oneTime"][$z+1] = $temp;
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
				echo fergcorp_countdownTimer_format(stripslashes($dates["oneTime"][$i]["text"]), $dates["oneTime"][$i]["date"], 0, $dates["oneTime"][$i]["timeSince"], $fergcorp_countdownTimer_getOptions["timeSinceTime"], $dates["oneTime"][$i]["link"], $fergcorp_countdownTimer_getOptions["timeOffset"], $fergcorp_countdownTimer_getOptions["displayFormatPrefix"], $fergcorp_countdownTimer_getOptions["displayFormatSuffix"], $fergcorp_countdownTimer_getOptions["displayStyle"]);
			elseif($output == "return"){
				$toReturn .= fergcorp_countdownTimer_format(stripslashes($dates["oneTime"][$i]["text"]), $dates["oneTime"][$i]["date"], 0, $dates["oneTime"][$i]["timeSince"], $fergcorp_countdownTimer_getOptions["timeSinceTime"], $dates["oneTime"][$i]["link"], $fergcorp_countdownTimer_getOptions["timeOffset"], $fergcorp_countdownTimer_getOptions["displayFormatPrefix"], $fergcorp_countdownTimer_getOptions["displayFormatSuffix"], $fergcorp_countdownTimer_getOptions["displayStyle"]);
			}
			if(($dates["oneTime"][$i]["text"]==NULL) && (isset($dates["oneTime"][$i]))){
				$eventCount++;
			}
		}
		if($output == "return")
				return $toReturn;

		if($fergcorp_countdownTimer_noEventsPresent == TRUE){
			if($output == "echo"){
				echo $fergcorp_countdownTimer_getOptions["displayFormatPrefix"].__('No dates present', 'afdn_countdownTimer').$fergcorp_countdownTimer_getOptions["displayFormatSuffix"];
			}
			elseif($output == "return"){
				$toReturn .= $fergcorp_countdownTimer_getOptions["displayFormatPrefix"].__('No dates present', 'afdn_countdownTimer').$fergcorp_countdownTimer_getOptions["displayFormatSuffix"];
			}
		}
	}

	/**
	 * Returns an individual countdown element
	 *
	 * @param $text string Text associated with the countdown event
	 * @param $time int Unix time of the event
	 * @param $offset int Server offset of the time
	 * @param $timeSince int If the event should be displayed if it has already passed
	 * @param $timeSinceTime int If $timeSince is set, how long should it be displayed for in seconds
	 * @param $link string Link associated with the countdown event
	 * @param $timeFormat string Forming of the onHover time display
	 * @param $displayFormatPrefix string HTML tags to prefix the event element
	 * @param $displayFormatSuffix string HTML tags to suffix the event element
	 * @param $displayStyle string CSS styles to apply to the event element
	 * @since 2.1
	 * @access private
	 * @author Andrew Ferguson
	 * @return string The content of the post with the appropriate dates inserted (if any)
	*/
	
	function fergcorp_countdownTimer_format($text, $time, $offset, $timeSince=0, $timeSinceTime=0, $link=NULL, $timeFormat = "j M Y, G:i:s", $displayFormatPrefix = "<li>", $displayFormatSuffix = "</li>", $displayStyle = "cursor:pointer; border-bottom:1px black dashed"){
		global $fergcorp_countdownTimer_noEventsPresent, $fergcorp_countdownTimer_getOptions, $fergcorp_countdownTimer_nonceTracker;
		if(!isset($fergcorp_countdownTimer_nonceTracker)){
			$fergcorp_countdownTimer_nonceTracker = array();
		}
		$time_left = $time - time() + $offset;
		$content = "";
		if(($time_left < 0)&&($timeSince==1)&&((($time_left + $timeSinceTime) > 0)||($timeSinceTime == 0))){
			$fergcorp_countdownTimer_noEventsPresent = FALSE;
			$nonceTracker = "x".md5(rand()); //XHTML prevents IDs from starting with a number, so append a 'x' on the front just to make sure it dosn't
			$fergcorp_countdownTimer_nonceTracker[count($fergcorp_countdownTimer_nonceTracker)] = array("id"			=> $nonceTracker,
																										"targetDate"	=> $time,
																										);
			if($text)
				$content = $displayFormatPrefix.($link==""?$text:"<a href=\"$link\"><strong>".$text."</strong></a>").$fergcorp_countdownTimer_getOptions["titleSuffix"]."\n";
			if($timeFormat == "")
				$content .= "<span id = '$nonceTracker'>".sprintf(__("%s ago", 'afdn_countdownTimer'), fergcorp_countdownTimer_fuzzyDate((time() + $offset), $time, $time))."</span>".$displayFormatSuffix;
			else
				$content .= "<abbr title = \"".gmdate($timeFormat, $time + (get_option('gmt_offset') * 3600))."\" style=\"". $displayStyle ."\"><span id = '$nonceTracker'>".sprintf(__("%s ago", 'afdn_countdownTimer'), fergcorp_countdownTimer_fuzzyDate((time() + $offset), $time, $time))."</span></abbr>".$displayFormatSuffix;
			return $content;
		}
		elseif($time_left > 0){
			$fergcorp_countdownTimer_noEventsPresent = FALSE;
			$nonceTracker = "x".md5(rand()); //XHTML prevents IDs from starting with a number, so append a 'x' on the front just to make sure it dosn't
			$fergcorp_countdownTimer_nonceTracker[count($fergcorp_countdownTimer_nonceTracker)] = array("id"			=> $nonceTracker,
																										"targetDate"	=> $time,
																										);
			if($text)
				$content = $displayFormatPrefix.($link==""?$text:"<a href=\"$link\"><strong>".$text."</strong></a>").$fergcorp_countdownTimer_getOptions["titleSuffix"]."\n";
			if($timeFormat == "")
				$content .= "<span id = '$nonceTracker'>".sprintf(__("in %s", 'afdn_countdownTimer'), fergcorp_countdownTimer_fuzzyDate($time, (time() + $offset), $time))."</span>".$displayFormatSuffix;
			else
				$content .= "<abbr title = \"".gmdate($timeFormat, $time + (get_option('gmt_offset') * 3600))."\" style=\"". $displayStyle ."\"><span id = '$nonceTracker'>".sprintf(__("in %s", 'afdn_countdownTimer'), fergcorp_countdownTimer_fuzzyDate($time, (time() + $offset), $time))."</span></abbr>".$displayFormatSuffix;
				return $content;
		}
		else{
			return NULL;
		}
	}

	/**
	 * Returns the numerical part of a single countdown element
	 *
	 * @param $targetTime
	 * @param $nowTime
	 * @param $realTargetTime
	 * @since 2.1
	 * @access private
	 * @author Andrew Ferguson
	 * @return string The content of the post with the appropriate dates inserted (if any)
	*/
	function fergcorp_countdownTimer_fuzzyDate($targetTime, $nowTime, $realTargetTime){
		global $fergcorp_countdownTimer_getOptions;

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
			$numToMonth = array("December", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
			$resultantDay = $resultantDay + date("t", strtotime($numToMonth[(int)$nowMonth]));
		}

		if($resultantMonth < 0){
			$resultantYear--;
			$resultantMonth = $resultantMonth + 12;
		}

		//Year
		if($fergcorp_countdownTimer_getOptions['showYear']){
			if($sigNumHit || !$fergcorp_countdownTimer_getOptions['stripZero'] || $resultantYear){
				if($resultantYear==1){
					$s = sprintf(__("%d year, ", "afdn_countdownTimer"), $resultantYear)." ";
				} else{
					$s = sprintf(__("%d years, ", "afdn_countdownTimer"), $resultantYear)." ";
				}
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $resultantYear*31536000;
		}

		//Month
		if($fergcorp_countdownTimer_getOptions['showMonth']){
			if($sigNumHit || !$fergcorp_countdownTimer_getOptions['stripZero'] || $resultantMonth){
				$resultantMonth = intval($resultantMonth + ($rollover/2628000));
				if($resultantMonth==1){
					$s .= sprintf(__("%d month, ", "afdn_countdownTimer"), $resultantMonth)." ";
				} else{
					$s .= sprintf(__("%d months, ", "afdn_countdownTimer"), $resultantMonth)." ";
				}
				$rollover = $rollover - intval($rollover/2628000)*2628000; //(12/31536000)
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $resultantMonth*2592000;
		}

		//Week (weeks are counted differently becuase we can just take 7 days and call it a week...so we do that)
		if($fergcorp_countdownTimer_getOptions['showWeek']){
			if($sigNumHit || !$fergcorp_countdownTimer_getOptions['stripZero'] || intval( ($resultantDay + intval($rollover/86400) )/7)){
				$resultantWeek = $resultantWeek + intval($rollover/86400)/7;
				if((intval( ($resultantDay + intval($rollover/86400) )/7))==1){
					$s .= sprintf(__("%d week, ", "afdn_countdownTimer"), (intval( ($resultantDay + intval($rollover/86400) )/7)))." ";
				} else{
					$s .= sprintf(__("%d weeks, ", "afdn_countdownTimer"), (intval( ($resultantDay + intval($rollover/86400) )/7)))." ";
				}
				$rollover = $rollover - intval($rollover/86400)*86400;
				$resultantDay = $resultantDay - intval( ($resultantDay + intval($rollover/86400) )/7 )*7;
				$sigNumHit = true;
			}
		}

		//Day
		if($fergcorp_countdownTimer_getOptions['showDay']){
			if($sigNumHit || !$fergcorp_countdownTimer_getOptions['stripZero'] || $resultantDay){
				$resultantDay = $resultantDay + intval($rollover/86400);
				if($resultantDay==1){
					$s .= sprintf(__("%d day, ", "afdn_countdownTimer"), $resultantDay)." ";
				} else{
					$s .= sprintf(__("%d days, ", "afdn_countdownTimer"), $resultantDay)." ";
				}
				$rollover = $rollover - intval($rollover/86400)*86400;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $resultantDay*86400;
		}

		//Hour
		if($fergcorp_countdownTimer_getOptions['showHour']){
			if($sigNumHit || !$fergcorp_countdownTimer_getOptions['stripZero'] || $resultantHour){
				$resultantHour = $resultantHour + intval($rollover/3600);
				if($resultantHour==1){
					$s .= sprintf(__("%d hour, ", "afdn_countdownTimer"), $resultantHour)." ";
				} else{
					$s .= sprintf(__("%d hours, ", "afdn_countdownTimer"), $resultantHour)." ";
				}
				$rollover = $rollover - intval($rollover/3600)*3600;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $resultantHour*3600;
		}

		//Minute
		if($fergcorp_countdownTimer_getOptions['showMinute']){
			if($sigNumHit || !$fergcorp_countdownTimer_getOptions['stripZero'] || $resultantMinute){
				$resultantMinute = $resultantMinute + intval($rollover/60);
				if($resultantMinute==1){
					$s .= sprintf(__("%d minute, ", "afdn_countdownTimer"), $resultantMinute)." ";
				} else{
					$s .= sprintf(__("%d minutes, ", "afdn_countdownTimer"), $resultantMinute)." ";
				}
				$rollover = $rollover - intval($rollover/60)*60;
				$sigNumHit = true;
			}
		}
		else{
			$rollover = $rollover + $resultantMinute*60;
		}

		//Second
		if($fergcorp_countdownTimer_getOptions['showSecond']){
			$resultantSecond = $resultantSecond + $rollover;
			if($resultantSecond==1){
				$s .= sprintf(__("%d second, ", "afdn_countdownTimer"), $resultantSecond)." ";
			} else{
				$s .= sprintf(__("%d seconds, ", "afdn_countdownTimer"), $resultantSecond)." ";
			}
		}
		
		return preg_replace("/(,? *)$/is", "", $s);

	}


	/**
	 * Returns the content of the post with dates inserted (if any)
	 *
	 * @param $theContent string The content of the post
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	 * @return string The content of the post with the appropriate dates inserted (if any)
	*/
	function afdn_countdownTimer_loop($theContent){
		global $fergcorp_countdownTimer_getOptions;
																						//Filter function for including the countdown with The Loop
		if(preg_match("<!--afdn_countdownTimer(\([0-9]+\))-->", $theContent)){																//If the string is found within the loop, replace it
			$theContent = preg_replace("/<!--afdn_countdownTimer(\(([0-9]+)\))?-->/e", "afdn_countdownTimer('return', $2)", $theContent);	//The actual replacement of the string with the timer
		}
		elseif(preg_match("<!--afdn_countdownTimer-->", $theContent)){																		//If the string is found within the loop, replace it
			$theContent = preg_replace("/<!--afdn_countdownTimer-->/e", "afdn_countdownTimer('return', -1)", $theContent);				//The actual replacement of the string with the timer
		}

		if(preg_match("<!--afdn_countdownTimer_single\((.*?)\)-->", $theContent)){
			$theContent = preg_replace("/<!--afdn_countdownTimer_single\(('|\")(.*?)('|\")\)-->/e", "fergcorp_countdownTimer_format('', strtotime('$2'), ".( date('Z') - (get_settings('gmt_offset') * 3600) ).", 'true', '0', '', '".$fergcorp_countdownTimer_getOptions['timeOffset']."', '', '', '')", $theContent);
		}

		return $theContent;																													//Return theContent
	}

	/**
	 * Creates a PHP-based one-off time for use outside the loop
	 *
	 * @param $date string Any string parsable by PHP's strtotime function
	 * @since 2.2
	 * @access public
	 * @author Andrew Ferguson
	*/
	function fergcorp_countdownTimer_single($date){
		global $fergcorp_countdownTimer_getOptions;
		return fergcorp_countdownTimer_format('', strtotime($date), ( date('Z') - (get_settings('gmt_offset') * 3600) ), 'true', '0', '', $fergcorp_countdownTimer_getOptions['timeOffset'], '', '', '');
	
	}


	/**
	 * Sets the defaults for the timer
	 *
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	*/
	function afdn_countdownTimer_install(){
		$theOptions = get_option("afdn_countdownOptions");

		if(get_option("widget_fergcorp_countdown") == NULL){	//Create default details for the widget if needed
			update_option("widget_fergcorp_countdown", array("title"=>"Countdown Timer", "count"=>"-1"));
		}

		$afdnOptions = array(	"deleteOneTimeEvents"	=> "0",
								"checkUpdate"			=> "1",
								"timeOffset"			=> "F jS, Y, g:i a",
								"enableTheLoop"			=> "0",
								"displayFormatPrefix"	=> "<li>",
								"displayFormatSuffix"	=> "</li>",
								"displayStyle"			=> "cursor:pointer; border-bottom:1px black dashed",
								"showYear"				=> "1",
								"showMonth"				=> "1",
								"showWeek"				=> "0",
								"showDay"				=> "1",
								"showHour"				=> "1",
								"showMinute"			=> "1",
								"showSecond"			=> "0",
								"stripZero"				=> "1",
								"enableJS"				=> "1",
								"timeSinceTime"			=> "0",
								"titleSuffix"			=> ":<br />",
							);

		//Check to see what options exists and add the ones that don't, keeping the values for the ones that do
		foreach($afdnOptions as $key => $value){
			if(array_key_exists($key, $theOptions)){
				$newOptionsArray["$key"] = $theOptions["$key"];
			}
			else{
				$newOptionsArray["$key"] = $value;
			}
		}

		update_option("afdn_countdownOptions", $newOptionsArray); //Update the WPDB for the options
		update_option("fergcorp_countdownTimer_version", "2.2.3");
	}


	if(!function_exists('widget_fergcorp_countdown_init')){

		/**
		 * Initialize the widget
		 *
		 * @since 2.1
		 * @access public
		 * @author Andrew Ferguson
		*/
		function widget_fergcorp_countdown_init() {

			// Check for the required plugin functions. This will prevent fatal
			// errors occurring when you deactivate the dynamic-sidebar plugin.
			if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
				return;

			/**
			 * Saves options and prints the widget's config form.
			 *
			 * @since 2.1
			 * @access private
			 * @author Andrew Ferguson
			*/
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

			/**
			 * Outputs the widget version of the countdown timer
			 *
			 * @since 2.1
			 * @access private
			 * @author Andrew Ferguson
			*/
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


	/**
	 * Echos the JavaScript for the timer
	 *
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	*/
	function afdn_countdownTimer_js(){
		global $fergcorp_countdownTimer_nonceTracker;
		global $fergcorp_countdownTimer_getOptions;

		echo "<script type=\"text/javascript\">\n";
		echo "<!--\n";

		//Pass on what units of time should be used
		echo "var getOptions = new Array();\n";
		echo "getOptions['showYear'] = ".$fergcorp_countdownTimer_getOptions['showYear'].";\n";
		echo "getOptions['showMonth'] = ".$fergcorp_countdownTimer_getOptions['showMonth'].";\n";
		echo "getOptions['showWeek'] = ".$fergcorp_countdownTimer_getOptions['showWeek'].";\n";
		echo "getOptions['showDay'] = ".$fergcorp_countdownTimer_getOptions['showDay'].";\n";
		echo "getOptions['showHour'] = ".$fergcorp_countdownTimer_getOptions['showHour'].";\n";
		echo "getOptions['showMinute'] = ".$fergcorp_countdownTimer_getOptions['showMinute'].";\n";
		echo "getOptions['showSecond'] = ".$fergcorp_countdownTimer_getOptions['showSecond'].";\n";
		echo "getOptions['stripZero'] = ".$fergcorp_countdownTimer_getOptions['stripZero'].";\n";

		//Pass on language variables
		echo "var fergcorp_countdownTimer_js_language = new Array();\n";
		echo "fergcorp_countdownTimer_js_language['year'] = '".addslashes(__('%d year, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['years'] = '".addslashes(__('%d years, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['month'] = '".addslashes(__('%d month, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['months'] = '".addslashes(__('%d months, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['week'] = '".addslashes(__('%d week, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['weeks'] = '".addslashes(__('%d weeks, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['day'] = '".addslashes(__('%d day, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['days'] = '".addslashes(__('%d days, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['hour'] = '".addslashes(__('%d hour, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['hours'] = '".addslashes(__('%d hours, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['minute'] = '".addslashes(__('%d minute, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['minutes'] = '".addslashes(__('%d minutes, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['second'] = '".addslashes(__('%d second, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['seconds'] = '".addslashes(__('%d seconds, ', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['ago'] = '".addslashes(__('%s ago', 'afdn_countdownTimer'))."';\n";
		echo "fergcorp_countdownTimer_js_language['in'] = '".addslashes(__('in %s', 'afdn_countdownTimer'))."';\n";

		//Pass on details about each timer
		echo "var fergcorp_countdownTimer_js_events = new Array();\n";
		for($i=0; $i < count($fergcorp_countdownTimer_nonceTracker); $i++){
				echo "fergcorp_countdownTimer_js_events[$i] = new Array()\n";
				echo "fergcorp_countdownTimer_js_events[$i]['id'] 		= \"".$fergcorp_countdownTimer_nonceTracker[$i]['id']."\";\n";
				echo "fergcorp_countdownTimer_js_events[$i]['targetDate'] 	= \"".$fergcorp_countdownTimer_nonceTracker[$i]['targetDate']."\";\n";

		}
		echo "fergcorp_countdownTimer_js();\n";
		echo "//-->\n";
		echo "</script>\n";
	}

	/**
	 * Adds the management page in the admin menu
	 *
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	 */
	function afdn_countdownTimer_optionsPage(){																		//Action function for adding the configuration panel to the Management Page
		if(function_exists('add_management_page')){
				$fergcorp_countdownTimer_add_management_page = add_management_page('Countdown Timer', 'Countdown Timer', 10, basename(__FILE__), 'afdn_countdownTimer_myOptionsSubpanel');
				add_action( "admin_print_scripts-$fergcorp_countdownTimer_add_management_page", 'fergcorp_countdownTimer_LoadUserScripts' );
				add_action( "admin_print_scripts-$fergcorp_countdownTimer_add_management_page", 'fergcorp_countdownTimer_LoadAdminScripts' );
		}
	}

	add_action('admin_menu', 'afdn_countdownTimer_optionsPage');	//Add Action for adding the options page to admin panel
	add_action('admin_menu', 'afdn_countdownTimer_optionsPage');
	register_activation_hook( __FILE__, 'afdn_countdownTimer_install');

	$fergcorp_countdownTimer_getOptions = get_option("afdn_countdownOptions");	//Get the options from the WPDB (this is actually pretty sloppy on my part and should be fixed)

	if($fergcorp_countdownTimer_getOptions["enableTheLoop"]){								//If the timer is to be allowed in The Loop, run this
		add_filter('the_content', 'afdn_countdownTimer_loop', 1);
	}

	if($fergcorp_countdownTimer_getOptions["enableJS"]) {
		add_action('wp_footer', 'afdn_countdownTimer_js');
	}

	add_action('wp_head', 'fergcorp_countdownTimer_LoadUserScripts', 1); //Priority needs to be set to 1 so that the scripts can be enqueued before the scripts are printed, since both actions are hooked into the wp_head action.

	/**
	 * Loads the appropriate scripts when in the admin page
	 *
	 * @since 2.2
	 * @access private
	 * @author Andrew Ferguson
	 */
	function fergcorp_countdownTimer_LoadAdminScripts() {
	    wp_enqueue_script('postbox'); //These appear to be new functions in WP 2.5
		//wp_enqueue_script('jquery');
		//wp_enqueue_script('post');
	}
	
	/**
	 * Loads the appropriate scripts
	 *
	 * @since 2.2
	 * @access private
	 * @author Andrew Ferguson
	 */
	function fergcorp_countdownTimer_LoadUserScripts() {
		$fergcorp_countdownTimer_getOptions = get_option("afdn_countdownOptions");
		if($fergcorp_countdownTimer_getOptions["enableJS"]) {
			wp_enqueue_script('fergcorp_countdowntimer', str_replace(ABSPATH, '', "/".dirname(__FILE__)).'/fergcorp_countdownTimer_java.js', FALSE, '2.2.3');
			wp_enqueue_script('webkit_sprintf', str_replace(ABSPATH, '', "/".dirname(__FILE__)).'/webtoolkit.sprintf.js', FALSE);
		}
	}