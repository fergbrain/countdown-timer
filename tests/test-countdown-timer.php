<?php
/**
 * Class Test_Countdown_Timer
 *
 * @package Countdown_Timer
 */

/**
 * Sample test case.
 */
class Test_Countdown_Timer extends WP_UnitTestCase {


	/**
	* @group init
	*/
	// Check that that activation doesn't break
	function test_plugin_activated() {
		$this->assertTrue( is_plugin_active( PLUGIN_PATH ) );
	}
	
	public function setUp() {
		parent::setUp();
		update_option('timezone_string', $this->timezone_string);
		$this->pluginData = get_plugin_data("fergcorp_countdownTimer.php");
		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
		$this->plugin = $GLOBALS['fergcorp_countdownTimer_init'];
		$this->plugin->install();
		$this->plugin->__construct();

	}
	
	public function tearDown(){
		 parent::tearDown();
		 unset($this->plugin);
	}

	public function test_fergcorp_countdownTimer(){

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
	
	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::singleTimer
	 */
	public function test_singleTimer(){
		$date = "+31 days";
		$calcDate = gmdate("F jS, Y, g:i a", strtotime($date)+(3600*(get_option('gmt_offset'))));
		$this->assertRegExp("/<abbr title = \"$calcDate\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit\">1 month,<\/span> <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">0 days,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span><\/abbr>/is", $this->plugin->singleTimer($date));
	}
	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::showTimer
	 */
	public function test_showTimer_noEvents(){
		$this->assertRegExp("/^<li>No dates present<\/li>$/is", $this->plugin->showTimer(-1, FALSE));
	}
	
	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::showTimer
	 */
	public function test_showTimer_all(){
		$event_object_array = array();
		for($i = -3; $i < 3; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
		}
		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		$this->plugin->loadSettings();
		$this->assertRegExp("/<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hour,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>/is", $this->plugin->showTimer(-1, FALSE));
	}
	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::showTimer
	 */
	public function test_showTimer_3(){
		$event_object_array = array();
		for($i = -3; $i < 3; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
		}
		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		$this->plugin->loadSettings();
		$this->expectOutputRegex("/<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>/is");
		print $this->plugin->showTimer(3, FALSE);
	}
	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::showTimer
	 */
	public function test_showTimer_1(){
		$event_object_array = array();
		for($i = -3; $i < 3; $i++){
			$time = time()+($i*3210);
			array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
		}
		update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
		$this->plugin->loadSettings();
		$this->expectOutputRegex("/<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>/is");
		print $this->plugin->showTimer(1, FALSE);
	}
	/**
	 * @group guest
	 * @covers Fergcorp_Countdown_Timer::formatEvent
	 */
	public function test_formatEvent(){
		$this->assertRegExp("/^<span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/www\.example\.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Tomorrow<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">1 day,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span><\/abbr>$/is", $this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "+1day" ), "Tomorrow", "http://www.example.com") , TRUE ));
		$this->assertRegExp("/^<span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/www\.example\.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Yesterday<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">1 day,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span> ago<\/abbr>$/is", $this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "-1day" ), "Yesterday", "http://www.example.com" ) , TRUE ));
		$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">1 day,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span><\/abbr><\/li>\r\n$/is", $this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "+1 day" ) ) , FALSE ));
		$this->assertNull($this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "yesterday" ) ) , FALSE ));
		update_option("fergcorp_countdownTimer_timeFormat", "");
		update_option("fergcorp_countdownTimer_timeSinceTime", -99999);
		$this->plugin->loadSettings();
		$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">1 day,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span><\/abbr><\/li>\r\n$/is", $this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "+1day" ), "", "", 1 ) , FALSE ));
		$this->assertRegExp("/^<span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/www\.example\.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Yesterday<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">1 day,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span> ago<\/abbr>$/is", $this->plugin->formatEvent( new Fergcorp_Countdown_Timer_Event( strtotime( "-1day" ), "Yesterday", "http://www.example.com", 1 ) , TRUE ));
	}
	
	
	function check_this($time, $unitList, $s, $now){
					$nowObj = new DateTime;
					date_timestamp_set($nowObj, $now);
					$diff = date_diff($time, $nowObj);
					$secondsDiff = $time->getTimestamp() - $now;
					$Year =		$s[0].intval($secondsDiff/31536000)." ".$s[1]."s?,".$s[14]." ";
					$Month =	$s[2].intval($secondsDiff/2628000)." ".$s[3]."s?,".$s[14]." ";
					$Week =		$s[4].intval($secondsDiff/2628000)." ".$s[5]."s?,".$s[14]." ";
					$Day = 		$s[6].intval($secondsDiff/86400)." ".$s[7]."s?,".$s[14]." ";
					$Hour =		$s[8].intval($secondsDiff/3600)." ".$s[9]."s?,".$s[14]." ";
					$Minute =	$s[10].intval($secondsDiff/60)." ".	$s[11]."s?,".$s[14]." ";
					$Second = 	$s[12].$secondsDiff." ".		$s[13]."(s)?,".$s[14]." ";
					$secondFudge = $diff->s;	//"(".($diff->s-1)."|".$diff->s."|".($diff->s+1).")"; //Test takes too long!
					if(get_option("fergcorp_countdownTimer_showYear")){
						$Year =		$s[0].$diff->y." ".$s[1]."(s)?,".$s[14]." ";
						$Month =	$s[2].$diff->m." ".$s[3]."s?,".$s[14]." ";
						$Day = 		$s[6].$diff->d." ".$s[7]."s?,".$s[14]." ";
						$Hour =		$s[8].$diff->h." ".$s[9]."s?,".$s[14]." ";
						$Minute =	$s[10].$diff->i." ".$s[11]."s?,".$s[14]." ";
						$Second = 	$s[12].$secondFudge." ".$s[13]."s?,".$s[14]." ";
					}
					elseif(get_option("fergcorp_countdownTimer_showMonth")){
						$secondsDiff = $secondsDiff - ($diff->m*2628000);
						$Day = 		$s[6].$diff->d." ".$s[7]."s?,".$s[14]." ";
						$Hour =		$s[8].$diff->h." ".$s[9]."s?,".$s[14]." ";
						$Minute =	$s[10].$diff->i." ".$s[11]."s?,".$s[14]." ";
						$Second = 	$s[12].$secondFudge." ".$s[13]."s?,".$s[14]." ";
					}
					elseif(get_option("fergcorp_countdownTimer_showWeek")){
						$Week = 	$s[4].intval($diff->days/7)." ".$s[5]."s?,".$s[14]." ";
						$Day = 		$s[6].($diff->d%7)." ".$s[7]."s?,".$s[14]." ";
						$Hour =		$s[8].$diff->h." ".$s[9]."s?,".$s[14]." ";
						$Minute =	$s[10].$diff->i." ".$s[11]."s?,".$s[14]." ";
						$Second = 	$s[12].$secondFudge." ".$s[13]."s?,".$s[14]." ";
					}
					elseif(get_option("fergcorp_countdownTimer_showDay")){
						$secondsDiff = $secondsDiff - ($diff->d*86400);
						$Hour =		$s[8].$diff->h." ".$s[9]."s?,".$s[14]." ";
						$Minute =	$s[10].$diff->i." ".$s[11]."s?,".$s[14]." ";
						$Second = 	$s[12].$secondFudge." ".$s[13]."s?,".$s[14]." ";
					}
					elseif(get_option("fergcorp_countdownTimer_showHour")){
						$secondsDiff = $secondsDiff - ($diff->h*3600);
						$Minute =	$s[10].$diff->i." ".	$s[11]."s?,".$s[14]." ";
						$Second = 	$s[12].$secondFudge." ".		$s[13]."s?,".$s[14]." ";
					}
					elseif(get_option("fergcorp_countdownTimer_showMinute")){
						$secondsDiff = $secondsDiff - ($diff->s*60);
						$Second = 	$s[12].$secondFudge." ".		$s[13]."s?,".$s[14]." ";
					}
					foreach($unitList as $unit){
						if(get_option("fergcorp_countdownTimer_show{$unit}")){
							$unitValue[$unit] .= ${$unit};
						}
					}
					$testOfUnits = "";
					foreach($unitList as $unit){
						if(get_option("fergcorp_countdownTimer_show{$unit}")){
							$testOfUnits .= ${$unit};
						}
					}
					//print $testOfUnits."\n\n";
					return $testOfUnits;
				}
	
	/**
		 * @group guest
		 * @group long
		 * @covers Fergcorp_Countdown_Timer::fuzzyDate
		 */
		public function test_fuzzyDate(){
			$now = time();
					$s = array(		"<span class=\"fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit\">",
									"year",
									"<span class=\"fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit\">",
									"month",
									"<span class=\"fergcorp_countdownTimer_week fergcorp_countdownTimer_timeUnit\">",
									"week",
									"<span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">",
									"day",
									"<span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">",
									"hour",
									"<span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">",
									"minute",
									"<span class=\"fergcorp_countdownTimer_second fergcorp_countdownTimer_timeUnit\">",
									"second",
									"<\/span>",
			);
			//Setup massive database of events!!!!
			$unitList = array(	"Year",
								"Month",
								//"Week",
								"Day",
								"Hour",
								"Minute",
								"Second",
			);
			$unitList_reverse = array_reverse($unitList);
			foreach($unitList as $unit){
					update_option("fergcorp_countdownTimer_show{$unit}", 0);
				}
			for($u=count($unitList)-1; $u >= 0; $u--){
				update_option("fergcorp_countdownTimer_show{$unitList[$u]}", 1);
				$this->plugin->loadSettings();
				$time = new DateTime("+11 years, 9 months, 7 days, 5 hours, 3 minutes, 1 second");
				//Years
				for($i = 10; $i > 2; $i--){
					$time->sub(new DateInterval("P1Y"));
					$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now), "Date attempted is: " . date("r", $time->getTimestamp()));
				}
				for($i = 1; $i < 12; $i++){
					$time->sub(new DateInterval("P1M"));
					$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now), "Time: ".var_export($time->getTimestamp(), true)."\n unitList: ".var_export($unitList, true)." \n s: ".var_export($s, true)." \n now: $now");
				}
				for($i = 1; $i < date("t", $time->getTimestamp()); $i++){
					$time->sub(new DateInterval("P1D"));
					$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));
				}
				for($i = 0; $i < 25; $i++){
					$time->sub(new DateInterval("PT1H"));
					$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));
				}
				for($i = 0; $i < 59; $i++){
					$time->sub(new DateInterval("PT1M"));
					$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));
				}
				for($i = 0; $i < 59; $i++){
					$time->sub(new DateInterval("PT1S"));
					$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));
				}
				}
			//some special cases
			$time = new DateTime("+11 years, 9 months, 7 days, 5 hours, 3 minutes, 1 second");
			$now = time();
			//No month, with year, and every thing else
			update_option("fergcorp_countdownTimer_showMonth", 0);
			$this->plugin->loadSettings();
			//print $this->plugin->fuzzyDate($time->getTimestamp(), $now);
			$this->assertRegExp("/^<span class=\"fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit\">11 year(s)?,<\/span> <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">283 days?,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">5 hours?,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">3 minutes?,<\/span> <span class=\"fergcorp_countdownTimer_second fergcorp_countdownTimer_timeUnit\">(1|2|3) seconds?<\/span>/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));
			//Weeks
					$unitList = array(	"Year",
								"Month",
								"Week",
								"Day",
								"Hour",
								"Minute",
								"Second",
			);
			update_option("fergcorp_countdownTimer_showYear", 0);
			update_option("fergcorp_countdownTimer_showMonth", 0);
			update_option("fergcorp_countdownTimer_showWeek", 1);
			$this->plugin->loadSettings();
			//print $this->plugin->fuzzyDate($time->getTimestamp(), $now);
			$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));
			//$time = new DateTime("+11 years, 9 months, 7 days, 5 hours, 3 minutes, 1 second");
			//$now = time();
			//Nothing selected
			update_option("fergcorp_countdownTimer_showYear", 0);
			update_option("fergcorp_countdownTimer_showMonth", 0);
			update_option("fergcorp_countdownTimer_showWeek", 0);
			update_option("fergcorp_countdownTimer_showDay", 0);
			update_option("fergcorp_countdownTimer_showHour", 0);
			update_option("fergcorp_countdownTimer_showMinute", 0);
			update_option("fergcorp_countdownTimer_showSecond", 0);
			$this->plugin->loadSettings();
			//print $this->plugin->fuzzyDate($time->getTimestamp(), $now);
			$this->assertRegExp("/^".preg_replace("/,<\\\\\/span> $/", "<\/span>", $this->check_this($time, $unitList, $s, $now))."/is", $this->plugin->fuzzyDate($time->getTimestamp(), $now));
		}
		/**
		 * @group guest
		 * @group shortcode
		 * @covers Fergcorp_Countdown_Timer::shortcode_showTimer
		 */
		public function test_shortcode_showTimer(){
			$event_object_array = array();
			for($i = -3; $i < 3; $i++){
				$time = time()+($i*3210);
				array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", ($i%2) ) );
			}
			update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
			$this->plugin->loadSettings();
			$atts = array("max" => "-1", "bar"=>"not");
			$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hour,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n$/is", $this->plugin->shortcode_showTimer($atts));
			$atts = array("max" => "1", "bar"=>"not");
			$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n$/is", $this->plugin->shortcode_showTimer($atts));
			$atts = array("max" => "3", "bar"=>"not");
			$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n$/is", $this->plugin->shortcode_showTimer($atts));
			$atts = NULL;
			$this->assertRegExp("/^<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'><span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span> ago<\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n<li class = 'fergcorp_countdownTimer_event_li'><span class = 'fergcorp_countdownTimer_event_title'><a href=\"http:\/\/example.com\" class = 'fergcorp_countdownTimer_event_linkTitle'>Date is set to (.*?)<\/a><\/span>:<br \/>\n<abbr title = \"(.*?)\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">[0-9] hour,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">[0-9]{2} minutes<\/span><\/abbr><\/li>\r\n$/is", $this->plugin->shortcode_showTimer($atts));
		}
		/**
		 * @group guest
		 * @group shortcode
		 * @covers Fergcorp_Countdown_Timer::shortcode_singleTimer
		 */
		public function test_shortcode_singleTimer(){
			$date = "+2 months";
			$calcDate = gmdate("F jS, Y, g:i a", strtotime($date)+(3600*(get_option('gmt_offset'))));
			$atts = array("date" => $date);
			$this->assertRegExp("/^<abbr title = \"$calcDate\" id = 'x[0-9a-z]{32}' class = 'fergcorp_countdownTimer_event_time'>in <span class=\"fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit\">2 months,<\/span> <span class=\"fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit\">0 days,<\/span> <span class=\"fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit\">0 hours,<\/span> <span class=\"fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit\">0 minutes<\/span><\/abbr>$/is", $this->plugin->shortcode_singleTimer($atts));
		}
		/**
		 * @group guest
		 * @group display
		 * @group script
		 * @covers Fergcorp_Countdown_Timer::json
		 */
		public function test_json(){
			$event_object_array = array();
			for($i = -3; $i < 3; $i++){
				$time = time()+($i*3210);
				array_push($event_object_array, new Fergcorp_Countdown_Timer_Event($time, "Date is set to " . date('Y-m-d H:i:s', $time), "http://example.com", $i%2));
			}
			update_option("fergcorp_countdownTimer_oneTimeEvent", $event_object_array);
			$this->plugin->loadSettings();
			$this->plugin->showtimer(-1, FALSE);
			$this->plugin->json();
			$this->assertRegExp("/^var fergcorp_countdown_timer_js_lang = {\"year\":\"%d year,\",\"years\":\"%d years,\",\"month\":\"%d month,\",\"months\":\"%d months,\",\"week\":\"%d week,\",\"weeks\":\"%d weeks,\",\"day\":\"%d day,\",\"days\":\"%d days,\",\"hour\":\"%d hour,\",\"hours\":\"%d hours,\",\"minute\":\"%d minute,\",\"minutes\":\"%d minutes,\",\"second\":\"%d second,\",\"seconds\":\"%d seconds,\",\"agotime\":\"%s ago\",\"intime\":\"in %s\"};\nvar fergcorp_countdown_timer_jsEvents = {\"x[0-9a-z]{32}\":\"[0-9]{10}\",\"x[0-9a-z]{32}\":\"[0-9]{10}\",\"x[0-9a-z]{32}\":\"[0-9]{10}\",\"x[0-9a-z]{32}\":\"[0-9]{10}\"};\nvar fergcorp_countdown_timer_options = {\"showYear\":\"1\",\"showMonth\":\"1\",\"showWeek\":\"0\",\"showDay\":\"1\",\"showHour\":\"1\",\"showMinute\":\"1\",\"showSecond\":\"0\",\"stripZero\":\"1\"};$/is", $GLOBALS["wp_scripts"]->print_scripts_l10n("fergcorp_countdowntimer", FALSE));
		}

}
       /**
        * @group TZ
	    * @group admin
        */
class Test_Countdown_Timer_TZ extends Test_Countdown_Timer{
	public $timezone_string;
    public function setUp() {
    	$this->timezone_string = 'America/Denver';
        parent::setUp();
    }
}
       /**
        * @group GMT
	    * @group admin
        */
class Test_Countdown_Timer_GMT extends Test_Countdown_Timer{
	public $timezone_string;
    public function setUp() {
    	$this->timezone_string = 'UTC-2';
        parent::setUp();
    }
}