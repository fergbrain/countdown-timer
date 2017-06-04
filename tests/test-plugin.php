<?php
class PluginTest extends WP_UnitTestCase {
    
    /**
    * @group init
    */
    // Check that that activation doesn't break
    function test_plugin_activated() {
        $this->assertTrue( is_plugin_active( PLUGIN_PATH ) );
    }

	/**
	 * @group guest
	 * @group init
	 */
	public function test_fergcorp_countdownTimer(){

		$event_object_array = array();
		for($i = -3; $i < 3; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
		}

		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		$this->plugin->loadSettings();
		$this->expectOutputRegex("/^<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hour,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n$/is");
		fergcorp_countdownTimer();
	}
}