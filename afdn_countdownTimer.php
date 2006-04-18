<?php
/*
Plugin Name: Countdown Timer
Plugin URI: http://www.andrewferguson.net/wordpress-plugins/
Plugin Description: Add template tages to coutn down the years, days, hours, and minutes to a particular event
Version: 0.95
Author: Andrew Ferguson
Author URI: http://www.andrewferguson.net

Countdown - Adds template tags to count down to a specified date

This code is licensed under the MIT License.
http://www.opensource.org/licenses/mit-license.php
Copyright (c) 2005 Andrew Ferguson

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the
Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software,
and to permit persons to whom the Software is furnished to
do so, subject to the following conditions:

The above copyright notice and this permission notice shall
be included in all copies or substantial portions of the
Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY
KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
$pluginName = "countdown";
$pluginVersion = "0.95";

function afdn_countdownTimer_myOptionsSubpanel(){

	
	if (isset($_POST['info_update'])) //If the user has submitted the form, do the following
	{
		$numPosts = $_POST['number']; //Figure out how many fields there are
		for($i=0; $i<$numPosts; $i++){
			if($_POST["date$i"] != ""){
				$results[$i] = array(	"date" => strtotime($_POST["date$i"]), 
										"text" => $_POST["text$i"],
										"timeSince" => $_POST["timeSince$i"]); //For every field, create an array. Then stick that array into the master array
			}
		}

		/*Begin sorting events by time*/
		for($x=0; $x<$numPosts; $x++){
			for($z=0; $z<$numPosts-1; $z++){
				if(($results[$z+1]["date"] < $results[$z]["date"]) && (array_key_exists($z+1, $results))){
					$temp = $results[$z];
					$results[$z] = $results[$z+1];
					$results[$z+1] = $temp;
				}
			}	
		}
		/*End sorting events by time*/	
		
		$afdnOptions = array(	"deleteOneTimeEvents" => $_POST['deleteOneTimeEvents'],
								"checkUpdate" => $_POST['checkUpdate']); //Create the array to store the countdown options
		
		update_option("afdn_countdowntracker", serialize($results)); //Update the WPDB
		update_option("afdn_countdownOptions", serialize($afdnOptions));//Update the WPDB
	}
	
	$dates = get_option("afdn_countdowntracker"); //Get the events from the WPDB to make sure a fresh copy is being used
	$getOptions = get_option("afdn_countdownOptions");//Get the options from the WPDB
	
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
		<form method="post">
			<h2>Countdown Timer</h2>
			<fieldset name="management">
				<legend><?php _e('Management'); ?></legend>
					Check for updates? <input name="checkUpdate" type="radio" value="1" <?php print($getOptions["checkUpdate"]==1?"checked":NULL)?> />Yes :: <input name="checkUpdate" type="radio" value="0" <?php print($getOptions["checkUpdate"]==0?"checked":NULL)?>/>No		
					<?php if($getOptions["checkUpdate"]==1){
						echo "<br /><br />";
						include_once("http://andrewferguson.net/wp-content/dl/wp/version.php?pn=$pluginName&pv=$pluginVersion");
						}
						?>
			</fieldset>
			
			
			<fieldset name="ote">
				<legend><?php _e('One Time Events'); ?></legend>
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
					<td><?php _e('Event Date'); ?></td>
					<td><?php _e('Event Title'); ?></td>
					<td><?php _e('Display "Time since"'); ?></td>
				</tr>
					<?php 
						$count=0;
						$entriesCount = count($dates);
						for($i=0; $i < $entriesCount+1; $i++){ 
							if($dates[$i]["date"]!=''){ //If the time is NULL, skip over it?>
							<tr>
							<td><input type="text" size="35" name="date<?php echo $count; ?>" value="<?php if($dates[$i]["date"]!='')echo date("r", $dates[$i]["date"]); ?>" /></td>
							<td><input type="text" size="50" name="text<?php echo $count; ?>" value="<?php echo $dates[$i]["text"]; ?>" /></td>
							<td><input type="checkbox" name="timeSince<?php echo $count; ?>" value="1" <?php print($dates[$i]["timeSince"]==1?"checked":NULL)?>/></td>
							</tr>
							<?php
							$count++;
							 }
						
						@next($dates);
						} 						
							?><tr>
							<td><input type="text" size="35" name="date<?php echo $count; ?>" value="<?php if($dates[$i][0]!='')echo date("r", $dates[$i][0]); ?>" /></td>
							<td><input type="text" size="50" name="text<?php echo $count; ?>" value="<?php echo $dates[$i][1]; ?>" /></td>
							<td><input type="checkbox" name="timeSince<?php echo $count; ?>" value="1"/></td>
							</tr>
							<?php
						echo '<input type="hidden" name="number" value="'.($entriesCount+1).'" />';
						?>
				</table>
			</fieldset>
							Automatically delete '<?php _e('One Time Events') ?>' after they have occured? <input name="deleteOneTimeEvents" type="radio" value="1" <?php print($getOptions["deleteOneTimeEvents"]==1?"checked":NULL)?> />Yes :: <input name="deleteOneTimeEvents" type="radio" value="0" <?php print($getOptions["deleteOneTimeEvents"]==0?"checked":NULL)?>/>No
			<fieldset name="reoccuring">
				<legend><?php _e('Reoccuring'); ?></legend>
				<table>
				<tr>
					<td><?php _e('Reoccuring Date'); ?></td>
					<td><?php _e('Reoccuring Title'); ?></td>
				</tr>
					<?php 
						$count=0;
						$entriesCount = count($dates);
						for($i=0; $i < $entriesCount+1; $i++){ 
							if($dates[$i]["date"]!=''){ //If the time is NULL, skip over it?>
							<tr>
							<td><input type="text" size="35" name="reoccuringDate<?php echo $count; ?>" value="" /></td>
							<td><input type="text" size="50" name="reoccuringText<?php echo $count; ?>" value="" /></td>
							</tr>
							<?php
							$count++;
							 }
						
						@next($dates);
						} 						
							?><tr>
							<td><input type="text" size="35" name="reoccuringDate<?php echo $count; ?>" value="" /></td>
							<td><input type="text" size="50" name="reoccuringText<?php echo $count; ?>" value="" /></td>
							</tr>
							<?php
						echo '<input type="hidden" name="number" value="'.($entriesCount+1).'" />';
						?>
				</table>
			</fieldset>
			<div class="submit"><input type="submit" name="info_update" value="<?php
				_e('Update Events', 'Localization name')
			 ?>&raquo;" /></div>
		</form>
	</div> <?
}



function afdn_countdownTimer_optionsPage(){
	if(function_exists('add_options_page')){
			add_options_page('Countdown Timer', 'Countdown Timer', 10, basename(__FILE__), 'afdn_countdownTimer_myOptionsSubpanel');
	}
}



function countdown_timer(){


	function cdt_format($text, $time, $offset, $timeSince=0){
		$time_left = $time - time() + $offset;
		if(($time_left < 0)&&($timeSince==1)){
			$content = "<li><b>Time since ".$text.":</b><br />\n";
			$content .= cdt_hms($time_left)." ago</li>";
			return $content;
			//return NULL;
		}
		elseif($time_left > 0){
		$content = "<li><b>".$text.":</b><br />\n";
		$content .= "in ".cdt_hms($time_left)."</li>";
		return $content;
		}
		else{
			return NULL;
		}
	}
	
	function cdt_hms($s, $min=1){
		$years=intval($s/31556736);
		$days=intval(($s-$years*31556736)/86400);
		$hours=intval(($s-($years*31556736)-($days*86400))/3600);
		$minutes=intval(($s-($years*31556736)-($days*86400)-($hours*3600))/60);
		$secs=$s%60;
		if ($years)
			$r=$r.abs($years).' '.__("years").', ';
		if ($days)
			$r=$r.abs($days).' '.__("days").', ';
		if ($hours)
			$r=$r.abs($hours).' '.__("hours").', ';
		if($min)
			$r=$r.abs($minutes).' '.__("minutes");
		return $r;
	}
	


	$dates = get_option("afdn_countdowntracker");
	$numDates = count($dates);
	
	for($i = 0; $i < $numDates; $i++){
		echo cdt_format($dates[$i]["text"], $dates[$i]["date"], (date("Z") - (get_settings('gmt_offset') * 3600)), $dates[$i]["timeSince"]);
		@next($dates);
	}
}

add_action('admin_menu', 'afdn_countdownTimer_optionsPage');
?>