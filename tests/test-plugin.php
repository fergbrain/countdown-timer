<?php
/**
 * Class PluginTest
 *
 * @package Countdown_Timer
 */

/**
 * Sample test case.
 */
class PluginTest extends WP_UnitTestCase {


	/**
	* @group init
	*/
	// Check that that activation doesn't break
	function test_plugin_activated() {
		$this->assertTrue( is_plugin_active( PLUGIN_PATH ) );
	}

	public function test_fergcorp_countdownTimer(){

		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
		$this->plugin = $GLOBALS['fergcorp_countdownTimer_init'];
		$this->pluginData = get_plugin_data("fergcorp_countdownTimer.php");
		$this->plugin->install();

		$defaultInstallValues = array(
										'deleteOneTimeEvents' => '0',
										'timeFormat' => 'F jS, Y, g:i a',
										'showYear' => '1',
										'showMonth' => '1',
										'showWeek' => '0',
										'showDay' => '1',
										'showHour' => '1',
										'showMinute' => '1',
										'showSecond' => '0',
										'stripZero' => '1',
										'enableJS' => '1',
										'timeSinceTime' => '0',
										'titleSuffix' => ':<br />',
										'enableShortcodeExcerpt' => '0',
										'oneTimeEvent' => '0',
										'version' => $this->pluginData["Version"]
									);
		foreach($defaultInstallValues as $key => $value){
			$this->assertEquals($value, get_option("fergcorp_countdownTimer_".$key));
		}


		$event_object_array = array();
		for($i = -3; $i < 3; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
		}
		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		$this->plugin->loadSettings();
		$this->expectOutputRegex("/^<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example\.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hour,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n$/is");
		fergcorp_countdownTimer();
	}


}