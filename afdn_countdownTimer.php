<?php
/*
Plugin Name: Countdown Timer
Plugin URI: http://www.andrewferguson.net/wordpress-plugins/countdown-timer/
Plugin Description: Add template tags and widget to count down or up to the years, months, weeks, days, hours, minutes, and/or seconds to a particular event.
Version: 2.4 Alpha
Author: Andrew Ferguson
Author URI: http://www.andrewferguson.net

Countdown Timer - Add template tags and widget to count down the years, months, weeks, days, hours, and minutes to a particular event
Copyright (c) 2005-2009 Andrew Ferguson

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
		$moFile = dirname(__FILE__) . "/lang/afdn_countdownTimer-" . $currentLocale . ".mo";
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
									"serialDataFilename"	=> $_POST['serialDataFilename'],									
									); //Create the array to store the countdown options

			update_option("afdn_countdowntracker", $results); //Update the WPDB for the data
			
			//Leaving this out of 2.3 release
			//$file = fopen(dirname(__FILE__)."/".$_POST['serialDataFilename'], 'wb');
			//fwrite($file, serialize($results));
			//fclose($file);
			
			update_option("afdn_countdownOptions", $afdnOptions);//Update the WPDB for the options

			echo '<div id="message" class="updated fade"><p>'. __('Options/dates updated successfully.', 'afdn_countdownTimer') .'</p></div>';					//Report to the user that the data has been updated successfully
		}
		global $fergcorp_countdownTimer_dates, $fergcorp_countdownTimer_getOptions;
		$fergcorp_countdownTimer_dates = get_option("afdn_countdowntracker"); //Get the events from the WPDB to make sure a fresh copy is being used
		$fergcorp_countdownTimer_getOptions = get_option("afdn_countdownOptions");//Get the options from the WPDB to make sure a fresh copy is being used
		/*If the user wants, cycle through the array to find out if they have already occured, if so: set them to NULL*/
		$fergcorp_oneTimeDates=$fergcorp_countdownTimer_dates["oneTime"];
		if($fergcorp_countdownTimer_getOptions["deleteOneTimeEvents"] && (count($fergcorp_oneTimeDates[0])!=0) ){
			foreach($fergcorp_countdownTimer_dates["oneTime"] as $key => $value){
				if(($value["date"]<=time())&&($value["timeSince"]=="")){
				$fergcorp_countdownTimer_dates["oneTime"][$key]["text"]=NULL;
				}
			}
		}
		?>

			<script type="text/javascript">
			
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				
				// postboxes
				<?php
				global $wp_version;
				if(version_compare($wp_version,"2.7-alpha", "<")){
					echo "add_postbox_toggles('fergcorp-countdown-timer');"; //For WP2.6 and below
				}
				else{
					echo "postboxes.add_postbox_toggles('fergcorp-countdown-timer');"; //For WP2.7 and above
				}
				?>
			
			});

			
			
			
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

			<div class="wrap" id="afdn_countdownTimer_div">

				<h2>Countdown Timer</h2>
            
				<div id="poststuff">        
                    
				<?php
				
                		function fergcorp_countdownTimer_resources_meta_box(){
							?>
                            <table width="90%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td><ul><li><a href="http://andrewferguson.net/wordpress-plugins/countdown-timer/" target="_blank"><?php _e('Plugin Homepage','afdn_countdownTimer'); ?></a></li></ul></td>
                                    <td><ul><li><a href="http://wordpress.org/tags/countdown-timer" target="_blank"><?php _e('Support Forum','afdn_countdownTimer'); ?></a></li></ul></td>
                                    <td><ul><li><a href="http://www.amazon.com/gp/registry/registry.html?ie=UTF8&amp;type=wishlist&amp;id=E7Q6VO0I8XI4" target="_blank"><?php _e('Amazon Wishlist','afdn_countdownTimer'); ?></a></li></ul></td>
                                    <td><ul><li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=38923"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" alt="Donate"/></a></li></ul></td>
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
                            
                            
							<?php
						}
						add_meta_box("fergcorp_countdownTimer_resources", __('Resources'), "fergcorp_countdownTimer_resources_meta_box", "fergcorp-countdown-timer");
						
                        ?>
   
                        <form method="post" name="afdn_countdownTimer" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        
							<?php echo '<input type="hidden" name="fergcorp_countdownTimer_noncename" id="fergcorp_countdownTimer_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ) . '" />'; ?>
                            <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
                            <?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

                            <?php
							function fergcorp_countdownTimer_installation_meta_box(){
							?>
                            <p><?php printf(__("You've made it this far, you're almost there. To insert the Countdown Timer into your sidebar, you can use the <a %s>Countdown Timer Widget</a>.", 'afdn_countdownTimer'), "href='".admin_url('widgets.php')."'"); ?></p>
										<p><?php _e("Alternatively, you can also use this code in your sidebar.php file:", 'afdn_countdownTimer'); ?></p>
										<p>
											<code>&lt;li id='countdown'&gt;&lt;h2&gt;Countdown:&lt;/h2&gt;<br />
												&lt;ul&gt;<br />
												&lt;?php function_exists('fergcorp_countdownTimer')?fergcorp_countdownTimer():NULL; ?&gt;<br />
												&lt;/ul&gt;<br />
												&lt;/li&gt;
											</code>
										</p>
                                        
                                        <p><?php printf(__("If you want to insert the Countdown Timer into a page or post, you can use the following <abbr %s %s>shortcodes</abbr> to return all or a limited number of Countdown Timers, respectively:", 'afdn_countdownTimer'), "title='".__('A shortcode is a WordPress-specific code that lets you do nifty things with very little effort. Shortcodes can embed files or create objects that would normally require lots of complicated, ugly code in just one line. Shortcode = shortcut.', 'afdn_countdownTimer')."'", "style='cursor:pointer; border-bottom:1px black dashed'" ); ?></p>
                                   			<code>
													[fergcorp_cdt]<br /><br />
                                                    [fergcorp_cdt max=##]
											</code>
                                        <p><?php _e("Where <em>##</em> is maximum number of results to be displayed - ordered by date.", 'afdn_countdownTimer'); ?></p>   
										<p><?php _e("If you want to insert individual countdown timers, such as in posts or on pages, you can use the following shortcode:", 'afdn_countdownTimer'); ?></p>
										<p>
											<code><?php _e("Time until my birthday:", 'afdn_countdownTimer'); ?><br />
													[fergcorp_cdt_single date="<em>ENTER_DATE_HERE</em>"]
											</code>
										</p>
										<p><?php printf(__("Where <em>\"ENTER_DATE_HERE\"</em> uses <a %s>PHP's strtotime function</a> and will parse about any English textual datetime description.", 'afdn_countdownTimer'), "href='http://us2.php.net/strtotime' target='_blank'"); ?></p>                     
                            <?php		
							}
                        	add_meta_box('fergcorp_countdownTimer_installation', __('Installation Notes'), 'fergcorp_countdownTimer_installation_meta_box', 'fergcorp-countdown-timer', 'advanced', 'default');
										
							function fergcorp_countdownTimer_events_meta_box(){
							global $fergcorp_countdownTimer_dates, $oneTimeEvent_count, $oneTimeEvent_entriesCount;
							?>
								<p><?php printf(__("Countdown timer uses <a %s>PHP's strtotime function</a> and will parse about any English textual datetime description.", 'afdn_countdownTimer'), "href='http://us2.php.net/strtotime' target='_blank'"); ?></p>
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
												$oneTimeEvent_entriesCount = count($fergcorp_countdownTimer_dates["oneTime"]);
												if($fergcorp_countdownTimer_dates != ""){
													for($i=0; $i < $oneTimeEvent_entriesCount+1; $i++){
														if($fergcorp_countdownTimer_dates["oneTime"][$i]["text"]!=''){ //If the text is NULL, skip over it?>
														<tr id="oneTimeEvent_table<?php echo $oneTimeEvent_count; ?>" align="center">
														<td><a href="javascript:void(0);" onclick="javascript:clearField('oneTimeEvent','<?php echo $oneTimeEvent_count; ?>');">X</a></td>
														<td><input type="text" size="30" name="oneTimeEvent_date<?php echo $oneTimeEvent_count; ?>" value="<?php if($fergcorp_countdownTimer_dates["oneTime"][$i]["date"] != "")echo gmdate("D, d M Y H:i:s", $fergcorp_countdownTimer_dates["oneTime"][$i]["date"] + (get_option('gmt_offset') * 3600))." ".(get_option('gmt_offset')>="0"?"+":NULL).(get_option('gmt_offset')=="0"?"00":NULL).(get_option('gmt_offset')*100); ?>" /></td>
														<td><input type="text" size="20" name="oneTimeEvent_text<?php echo $oneTimeEvent_count; ?>" value="<?php echo htmlspecialchars(stripslashes($fergcorp_countdownTimer_dates["oneTime"][$i]["text"])); ?>" /></td>
														<td><input type="text" size="15" name="oneTimeEvent_link<?php echo $oneTimeEvent_count; ?>" value="<?php echo $fergcorp_countdownTimer_dates["oneTime"][$i]["link"]; ?>" /></td>

														<td><input type="checkbox" name="oneTimeEvent_timeSince<?php echo $oneTimeEvent_count; ?>" value="1" <?php print($fergcorp_countdownTimer_dates["oneTime"][$i]["timeSince"]==1?"checked='checked'":NULL)?>/></td>
														</tr>
														<?php
														$oneTimeEvent_count++;
														 }
														@next($fergcorp_countdownTimer_dates["oneTime"]);
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
                               <?php
                            }
						   	add_meta_box("fergcorp_countdownTimer_events", __('One Time Events'), "fergcorp_countdownTimer_events_meta_box", "fergcorp-countdown-timer");

							function fergcorp_countdownTimer_management_meta_box(){
								global $fergcorp_countdownTimer_getOptions;
								?>
								<ul>
									<li><?php _e('Enable JavaScript countdown:', 'afdn_countdownTimer'); ?> <input name="enableJS" type="radio" value="1" <?php print($fergcorp_countdownTimer_getOptions["enableJS"]==1?"checked='checked'":NULL)?> /><?php _e('Yes', 'afdn_countdownTimer'); ?> :: <input name="enableJS" type="radio" value="0" <?php print($fergcorp_countdownTimer_getOptions["enableJS"]==0?"checked='checked'":NULL)?>/><?php _e('No', 'afdn_countdownTimer'); ?></li>
								</ul>
                                <?php /*<p><?php //_e('Countdown Timer exports your events so they can be used by other applications, such as Facebook. The location of your file is:', 'afdn_countdownTimer'); ?></p>
								<ul>
                                	<li><input name="serialDataFilename" type="hidden" value="<?php print($fergcorp_countdownTimer_getOptions["serialDataFilename"]); ?>" size="50"/> <a href="<?php print(plugins_url(dirname(plugin_basename(__FILE__)) . "/" . $fergcorp_countdownTimer_getOptions["serialDataFilename"])); ?>" target="_blank"><?php //print(plugins_url(dirname(plugin_basename(__FILE__)) . "/". $fergcorp_countdownTimer_getOptions["serialDataFilename"])); ?></a></li>
		                        </ul>
								*/ ?>
								<?php
							}
							add_meta_box("fergcorp_countdownTimer_management", __('Management', 'afdn_countdownTimer'), "fergcorp_countdownTimer_management_meta_box", "fergcorp-countdown-timer");

							function fergcorp_countdownTimer_display_options_meta_box(){
								global $fergcorp_countdownTimer_getOptions;
								?>
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
								<?php
							}
							add_meta_box("fergcorp_countdownTimer_display_options", __('Countdown Time Display'), "fergcorp_countdownTimer_display_options_meta_box", "fergcorp-countdown-timer");
							
							function fergcorp_countdownTimer_onHover_time_format_meta_box(){
								global $fergcorp_countdownTimer_getOptions;
								?>
								<p><?php printf(__("If you set 'onHover Time Format', hovering over the time left will show the user what the date of the event is. onHover Time Format uses <a %s>PHP's Date() function</a>.", 'afdn_countdownTimer'), "href='http://us2.php.net/date' target='_blank'"); ?></p>
								<p><?php _e('Examples', 'afdn_countdownTimer'); ?>:</p>
								<ul>
									<li>"<em>j M Y, G:i:s</em>" <?php _e('goes to', 'afdn_countdownTimer'); ?> "<strong>17 Mar 2008, 14:50:00</strong>"</li>
									<li>"<em>F jS, Y, g:i a</em>" <?php _e('goes to', 'afdn_countdownTimer'); ?> "<strong>March 17th, 2008, 2:50 pm</strong>"</li>
								</ul>
								<p><?php _e('onHover Time Format', 'afdn_countdownTimer'); ?> <input type="text" value="<?php print(htmlspecialchars(stripslashes($fergcorp_countdownTimer_getOptions["timeOffset"]))); ?>" name="timeOffset" /></p>
								<?php
							}
							add_meta_box("fergcorp_countdownTimer_onHover_time_format", __('onHover Time Format'), "fergcorp_countdownTimer_onHover_time_format_meta_box", "fergcorp-countdown-timer");
							
							function fergcorp_countdownTimer_display_format_options_meta_box(){
								global $fergcorp_countdownTimer_getOptions;
								?>
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
								<?php
							}
							add_meta_box("fergcorp_countdownTimer_display_format_options", __('Display Format Options'), "fergcorp_countdownTimer_display_format_options_meta_box", "fergcorp-countdown-timer");

							function fergcorp_countdownTimer_example_display_meta_box(){
								echo "<ul>";
								fergcorp_countdownTimer();
								echo "</ul>";
                                afdn_countdownTimer_js();
							}
							add_meta_box("fergcorp_countdownTimer_example_display", __('Example Display'), "fergcorp_countdownTimer_example_display_meta_box", "fergcorp-countdown-timer");

							

							do_meta_boxes('fergcorp-countdown-timer','advanced',null);                           
							   
						?>

						<div>
							<p class="submit">
								<input type="submit" name="afdn_countdownTimer_update" value="<?php _e('Update Events', 'afdn_countdownTimer'); ?>&raquo;" />
							</p>
						</div>
						</form>
				</div>
       
            </div>
	<?php

	}
	/**
	 * afdn_countdownTimer helper function
	 *
	 * @param $eventLimit int The maximum number of events to echo or return, sorted by date
	 * @param $output string If set to 'echo', will echo the results with no return; If set to 'return', will return the results with no echo.
	 * @since 2.3
	 * @access public
	 * @author Andrew Ferguson
	 * @return string If set, will return the formated output ready for display
	*/
	function fergcorp_countdownTimer($eventLimit = -1, $output = "echo"){
		afdn_countdownTimer($eventLimit, $output);
	}

	/**
	 * Returns/echos the formated output for the countdown
	 *
	 * @param $eventLimit int The maximum number of events to echo or return, sorted by date
	 * @param $output string If set to 'echo', will echo the results with no return; If set to 'return', will return the results with no echo.
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	 * @return string If set, will return the formated output ready for display
	*/
	function afdn_countdownTimer($eventLimit = -1, $output = "echo"){ //'echo' will print the results, 'return' will just return them
		global $fergcorp_countdownTimer_getOptions, $fergcorp_countdownTimer_noEventsPresent;
		$fergcorp_countdownTimer_noEventsPresent = FALSE;
		
		$fergcorp_countdownTimer_dates = get_option("afdn_countdowntracker");//Get our text, times, and settings from the database
		$fergcorp_countdownTimer_getOptions = get_option("afdn_countdownOptions");//Get the options from the WPDB
		
		//Remove events that shouldn't be displayed because the time elapsed and the Time Since option isn't ticked
		if($fergcorp_countdownTimer_dates!=''){
			if(count($fergcorp_countdownTimer_dates["oneTime"][0])!=0){
				foreach($fergcorp_countdownTimer_dates["oneTime"] as $key => $value){
					if(($value["date"]<=time())&&($value["timeSince"]=="")){
					$fergcorp_countdownTimer_dates["oneTime"][$key]["text"]=NULL;
					}
				}
			}
			else{
				$fergcorp_countdownTimer_noEventsPresent = TRUE;//because there are no dates at all!
			}
		}
		else{
				$fergcorp_countdownTimer_noEventsPresent = TRUE;//because there are no dates at all!
		}

		/*Now that all the events are in the same array, we need to sort them by date. This is actually the same code used above for the admin page.
		At some point, I plan to make this into a function; but for, this will do...

		And what it does is this:
		The number of elements in the array are counted. Then for array is gone through x^(x-1) times. This allows for all posible date permuations to be sorted out and ordered correctly.
		Genious, yes? No*/
		$eventCount = count($fergcorp_countdownTimer_dates["oneTime"]);
		for($x=0; $x<$eventCount; $x++){
			for($z=0; $z<$eventCount-1; $z++){
				if(($fergcorp_countdownTimer_dates["oneTime"][$z+1]["date"] < $fergcorp_countdownTimer_dates["oneTime"][$z]["date"]) && (array_key_exists($z+1, $fergcorp_countdownTimer_dates["oneTime"]))){
					$temp = $fergcorp_countdownTimer_dates["oneTime"][$z];
					$fergcorp_countdownTimer_dates["oneTime"][$z] = $fergcorp_countdownTimer_dates["oneTime"][$z+1];
					$fergcorp_countdownTimer_dates["oneTime"][$z+1] = $temp;
				}
			}
		}
		if($eventLimit != -1)	//If the eventLimit is set
			$eventCount = $eventLimit;

		//This is the part that does the actual outputting. If you want to preface data, this an excellent spot to do it in.
		if($fergcorp_countdownTimer_noEventsPresent == FALSE){
			$fergcorp_countdownTimer_noEventsPresent = TRUE; //Reset the test
			for($i = 0; $i < $eventCount; $i++){
				if($output == "echo")
					echo fergcorp_countdownTimer_format(stripslashes($fergcorp_countdownTimer_dates["oneTime"][$i]["text"]), $fergcorp_countdownTimer_dates["oneTime"][$i]["date"], 0, $fergcorp_countdownTimer_dates["oneTime"][$i]["timeSince"], $fergcorp_countdownTimer_getOptions["timeSinceTime"], stripslashes($fergcorp_countdownTimer_dates["oneTime"][$i]["link"]), $fergcorp_countdownTimer_getOptions["timeOffset"]);
				elseif($output == "return"){
					$toReturn .= fergcorp_countdownTimer_format(stripslashes($fergcorp_countdownTimer_dates["oneTime"][$i]["text"]), $fergcorp_countdownTimer_dates["oneTime"][$i]["date"], 0, $fergcorp_countdownTimer_dates["oneTime"][$i]["timeSince"], $fergcorp_countdownTimer_getOptions["timeSinceTime"], stripslashes($fergcorp_countdownTimer_dates["oneTime"][$i]["link"]), stripslashes($fergcorp_countdownTimer_getOptions["timeOffset"]) );
				}
				if(($fergcorp_countdownTimer_dates["oneTime"][$i]["text"]==NULL) && (isset($fergcorp_countdownTimer_dates["oneTime"][$i]))){
					$eventCount++;
				}
			}
		}
		if($fergcorp_countdownTimer_noEventsPresent){
			if($output == "echo"){
				echo $fergcorp_countdownTimer_getOptions["displayFormatPrefix"].__('No dates present', 'afdn_countdownTimer').$fergcorp_countdownTimer_getOptions["displayFormatSuffix"];
			}
			elseif($output == "return"){
				$toReturn .= $fergcorp_countdownTimer_getOptions["displayFormatPrefix"].__('No dates present', 'afdn_countdownTimer').$fergcorp_countdownTimer_getOptions["displayFormatSuffix"];
			}
		}

		
		if($output == "return")
				return $toReturn;

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
	 * @since 2.1
	 * @access private
	 * @author Andrew Ferguson
	 * @return string The content of the post with the appropriate dates inserted (if any)
	*/
	
	function fergcorp_countdownTimer_format($eventText, $time, $offset, $timeSince=0, $timeSinceTime=0, $link=NULL, $timeFormat = "j M Y, G:i:s"){
		global $fergcorp_countdownTimer_noEventsPresent, $fergcorp_countdownTimer_getOptions, $fergcorp_countdownTimer_nonceTracker;
		if(!isset($fergcorp_countdownTimer_nonceTracker)){
			$fergcorp_countdownTimer_nonceTracker = array();
		}
		$time_left = $time - time() + $offset;
		$content = "<li class = 'fergcorp_countdownTimer_event_li'>";
		$nonceTracker = "x".md5($eventText.$time); //XHTML prevents IDs from starting with a number, so append a 'x' on the front just to make sure it dosn't, made this a predictable		
		$eventTitle = $displayFormatPrefix."<span class = 'fergcorp_countdownTimer_event_title'>".($link==""?$eventText:"<a href=\"$link\" class = 'fergcorp_countdownTimer_event_linkTitle'>".$eventText."</a>").'</span>'.$fergcorp_countdownTimer_getOptions["titleSuffix"]."\n";
		$timePrefix = "<abbr title = \"".gmdate($timeFormat, $time + (get_option('gmt_offset') * 3600))."\" id = '$nonceTracker' class = 'fergcorp_countdownTimer_event_time'>";
		
		if(($time_left < 0)&&($timeSince==1)&&((($time_left + $timeSinceTime) > 0)||($timeSinceTime == 0))){ //If the event has already passed and we still want to display the event
			$fergcorp_countdownTimer_noEventsPresent = FALSE; //Set to FALSE so we know there's an event to display
			$fergcorp_countdownTimer_nonceTracker[count($fergcorp_countdownTimer_nonceTracker)] = array("id"			=> $nonceTracker,
																										"targetDate"	=> $time,
																										);	//Don't want to actually keep track of it until now
			if($eventText){
				$content .= $eventTitle;
			}
			$content .= $timePrefix.sprintf(__("%s ago", 'afdn_countdownTimer'), fergcorp_countdownTimer_fuzzyDate((time() + $offset), $time, $time))."</abbr></li>";
			return $content;
		}
		elseif($time_left > 0){ //If the event has not yet happened yet
			$fergcorp_countdownTimer_noEventsPresent = FALSE; //Set to FALSE so we know there's an event to display
			$fergcorp_countdownTimer_nonceTracker[count($fergcorp_countdownTimer_nonceTracker)] = array("id"			=> $nonceTracker,
																										"targetDate"	=> $time,
																										);	//Don't want to actually keep track of it until now
			if($eventText){
				$content .= $eventTitle;
			}
			$content .= $timePrefix.sprintf(__("in %s", 'afdn_countdownTimer'), fergcorp_countdownTimer_fuzzyDate($time, (time() + $offset), $time))."</abbr></li>";
			return $content;
		}
		else{
			return NULL;
		}
	}
	
	if(!function_exists("cal_days_in_month")){
		/**
		 * Returns the number of days in a given month and year, taking into account leap years.
		 * The is a replacement function should cal_days_in_month not be availible
		 *
		 * @param $calendar int ignored
	 	 * @param $month int month (integers 1-12) 
		 * @param $year int year (any integer)
		 * @since 2.3
		 * @access private
		 * @author David Bindel (dbindel at austin dot rr dot com) (http://us.php.net/manual/en/function.cal-days-in-month.php#38666)
		 * @author ben at sparkyb dot net
		 * @return int The content of the post with the appropriate dates inserted (if any)
		*/
		function cal_days_in_month($calendar, $month, $year){
			// calculate number of days in a month 
			return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
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
			$resultantDay = $resultantDay + cal_days_in_month(CAL_GREGORIAN, $nowMonth, $nowYear); //Holy crap! When did they introduce this function and why haven't I heard about it??
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
			if($sigNumHit || !$fergcorp_countdownTimer_getOptions['stripZero'] || intval($resultantMonth + ($rollover/2628000)) ){
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
			
			//If we don't want to show months, let's just calculate the exact number of seconds left since all other units of time are fixed (i.e. months are not a fixed unit of time)
						
			$rollover = $rollover + $resultantMonth*2592000;
			
			$totalTime = $targetTime - $nowTime;
			
			//If we showed years, but not months, we need to account for those.
			if($fergcorp_countdownTimer_getOptions['showYear']){
				$totalTime = $totalTime - $resultantYear*31536000;
			}
			
			//Re calculate the resultant times
			$resultantWeek = intval( $totalTime/(86400*7) );
			 
			$resultantDay = intval( $totalTime/86400 );
			
			$resultantHour = intval( ($totalTime - $resultantDay*86400)/3600 );
			
			$resultantMinute = intval( ($totalTime - $resultantDay*86400 - $resultantHour*3600)/60 );
			
			$resultantSecond = intval( ($totalTime - $resultantDay*86400 - $resultantHour*3600 - $resultantMinute*60) );
			
			//and clear any rollover time
			$rollover = 0;
		}

		//Week (weeks are counted differently becuase we can just take 7 days and call it a week...so we do that)
		if($fergcorp_countdownTimer_getOptions['showWeek']){
			if($sigNumHit || !$fergcorp_countdownTimer_getOptions['stripZero'] || ( ($resultantDay + intval($rollover/86400) )/7)){
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
			if($sigNumHit || !$fergcorp_countdownTimer_getOptions['stripZero'] || ($resultantDay + intval($rollover/86400)) ){
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
			if($sigNumHit || !$fergcorp_countdownTimer_getOptions['stripZero'] || ($resultantHour + intval($rollover/3600)) ){
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
			if($sigNumHit || !$fergcorp_countdownTimer_getOptions['stripZero'] || ($resultantMinute + intval($rollover/60)) ){
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
		
		//Catch blank statements
		if($s==""){
			if($fergcorp_countdownTimer_getOptions['showSecond']){
				$s = sprintf(__("%d seconds, ", "afdn_countdownTimer"), "0");
			}
			elseif($fergcorp_countdownTimer_getOptions['showMinute']){
				$s = sprintf(__("%d minutes, ", "afdn_countdownTimer"), "0");
			}
			elseif($fergcorp_countdownTimer_getOptions['showHour']){
				$s = sprintf(__("%d hours, ", "afdn_countdownTimer"), "0");
			}	
			elseif($fergcorp_countdownTimer_getOptions['showDay']){
				$s = sprintf(__("%d days, ", "afdn_countdownTimer"), "0");
			}	
			elseif($fergcorp_countdownTimer_getOptions['showWeek']){
				$s = sprintf(__("%d weeks, ", "afdn_countdownTimer"), "0");
			}
			elseif($fergcorp_countdownTimer_getOptions['showMonth']){
				$s = sprintf(__("%d months, ", "afdn_countdownTimer"), "0");
			}
			else{
				$s = sprintf(__("%d years, ", "afdn_countdownTimer"), "0");
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
			$theContent = preg_replace("/<!--afdn_countdownTimer(\(([0-9]+)\))?-->/e", "afdn_countdownTimer($2, 'return')", $theContent);	//The actual replacement of the string with the timer
		}
		elseif(preg_match("<!--afdn_countdownTimer-->", $theContent)){																		//If the string is found within the loop, replace it
			$theContent = preg_replace("/<!--afdn_countdownTimer-->/e", "afdn_countdownTimer('-1', 'return')", $theContent);				//The actual replacement of the string with the timer
		}

		if(preg_match("<!--afdn_countdownTimer_single\((.*?)\)-->", $theContent)){
			$theContent = preg_replace("/<!--afdn_countdownTimer_single\(('|\")(.*?)('|\")\)-->/e", "fergcorp_countdownTimer_format('', strtotime('$2'), ".( date('Z') - (get_settings('gmt_offset') * 3600) ).", true, '0', '', '".$fergcorp_countdownTimer_getOptions['timeOffset']."')", $theContent);
		}

		return $theContent;																													//Return theContent
	}
	add_filter('the_content', 'afdn_countdownTimer_loop', 1);
	
	/**
	 * Processes shortcodes
	 *
	 * @param $atts array Attributes of the shortcode
	 * @since 2.3
	 * @access public
	 * @author Andrew Ferguson
	 * @return string countdown timer(s)
	*/	
	// [fergcorp_cdt max=##]
	function fergcorp_cdt_function($atts) {
		extract(shortcode_atts(array(
			'max' => '-1',
		), $atts));
	
		return afdn_countdownTimer($max, 'return');
	}
	add_shortcode('fergcorp_cdt', 'fergcorp_cdt_function');
	
	
	/**
	 * Processes shortcodes
	 *
	 * @param $atts array Attributes of the shortcode
	 * @since 2.3
	 * @access public
	 * @author Andrew Ferguson
	 * @return string countdown timer
	*/	
	// [fergcorp_cdt max=##]
	function fergcorp_cdt_single_function($atts) {
		extract(shortcode_atts(array(
			'date' => '-1',
		), $atts));
	
		return fergcorp_countdownTimer_format('', strtotime($date), ( date('Z') - (get_settings('gmt_offset') * 3600) ), true, '0', '', $fergcorp_countdownTimer_getOptions['timeOffset']);
	}
	add_shortcode('fergcorp_cdt_single', 'fergcorp_cdt_single_function');
	
	

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
		return fergcorp_countdownTimer_format('', strtotime($date), ( date('Z') - (get_settings('gmt_offset') * 3600) ), true, '0', '', $fergcorp_countdownTimer_getOptions['timeOffset']);
	
	}


	/**
	 * Sets the defaults for the timer
	 *
	 * @since 2.1
	 * @access public
	 * @author Andrew Ferguson
	*/
	function afdn_countdownTimer_install(){
		$plugin_data = get_plugin_data(__FILE__);
		$theOptions = get_option("afdn_countdownOptions");

		if(get_option("widget_fergcorp_countdown") == NULL){	//Create default details for the widget if needed
			update_option("widget_fergcorp_countdown", array("title"=>"Countdown Timer", "count"=>"-1"));
		}

		$afdnOptions = array(	"deleteOneTimeEvents"	=> "0",
								"checkUpdate"			=> "1",
								"timeOffset"			=> "F jS, Y, g:i a",
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
								"serialDataFilename"	=> "fergcorp_countdownTimer_serialData_".wp_generate_password(8,false).".txt",
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
		update_option("fergcorp_countdownTimer_version", $plugin_data["Version"]);
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
			if ( !function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control') )
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
				echo $before_widget . $before_title . "<span class = 'fergcorp_countdownTimer_widgetTitle' >". $title . "</span>" . $after_title;

				?>
					<ul>
						<?php afdn_countdownTimer($options['count'], "echo"); ?>
					</ul>
				<?php
				echo $after_widget;
			}

			// This registers our widget so it appears with the other available
			// widgets and can be dragged and dropped into any active sidebars.
			$widget_ops = array('description' => __('Adds the Countdown Timer', 'afdn_countdownTimer'));
			wp_register_sidebar_widget('fergcorp_countdownTimer', 'Countdown Timer', 'widget_fergcorp_countdown', $widget_ops);
			wp_register_widget_control('fergcorp_countdownTimer', 'Countdown Timer', 'widget_fergcorp_countdown_control');

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
	register_activation_hook( __FILE__, 'afdn_countdownTimer_install');

	$fergcorp_countdownTimer_getOptions = get_option("afdn_countdownOptions");	//Get the options from the WPDB (this is actually pretty sloppy on my part and should be fixed)
	
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
		//wp_enqueue_script('post'); //Don't call this...
		//wp_enqueue_script('dashboard'); //...or this. I've stripped out the required JS and place in at line 118
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
		$fergcorp_countdownTimer_getVersion = get_option("fergcorp_countdownTimer_version");
		if($fergcorp_countdownTimer_getOptions["enableJS"]) {
			wp_enqueue_script('fergcorp_countdowntimer', plugins_url(dirname(plugin_basename(__FILE__)) . "/js/". 'fergcorp_countdownTimer_java.js'), FALSE, $fergcorp_countdownTimer_getVersion);
			wp_enqueue_script('webkit_sprintf', plugins_url(dirname(plugin_basename(__FILE__)) . "/js/" . 'webtoolkit.sprintf.js'), FALSE, $fergcorp_countdownTimer_getVersion);
		}
	}