<?php
require_once('fergcorp_countdownTimer.php');


/**
 * Countdown Timer Tests
 */

class CountdownTimerTest extends WP_UnitTestCase {
    public $plugin_slug = 'countdown_timer';
	private $plugin;
	
	//TOTEST:
	/* Input validation? Only test valid inputs...don't test for invalid?
	 * Test date manipulation
	 * Test Defaults
	 * Test blanks (e.g. database corruption)
	 * 
	*/
	  
    public function setUp() {
        parent::setUp();
		
		$GLOBALS['fergcorp_countdownTimer_init'] = new Fergcorp_Countdown_Timer();
        $this->plugin = $GLOBALS['fergcorp_countdownTimer_init'];
		$this->plugin->install();
		
		$this->plugin->__construct();
    }
	
	public function testTrue(){
		$this->assertTrue(true);
	}
	
	public function test_construct(){
		$pluginData = get_plugin_data("fergcorp_countdownTimer.php");
		//global $fergcorp_countdownTimer_init;
		
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
		'version' => $pluginData["Version"]
		);
		
		//First we need to create a ReflectionClass object
		//passing in the class name as a variable
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer");

		$props = $reflection_class->getProperties(ReflectionProperty::IS_PRIVATE);
		
		foreach ($props as $prop) {
			$prop->setAccessible(true);
			//echo $prop->getName() . ": " . $prop->getValue($this->plugin) ."\n";
			if(key_exists($prop->getName(), $defaultInstallValues)){
				$this->assertEquals($defaultInstallValues[$prop->getName()], $prop->getValue($this->plugin), "Failed to match: ".$prop->getName());
			}
		}
		
		$this->assertTrue( (bool) has_action("admin_init", array( &$this->plugin, 'install' )));
		$this->assertTrue( (bool) has_action("admin_init", array( &$this->plugin, 'loadSettings' )));
		
		$this->assertTrue(wp_script_is("webkit_sprintf", "registered"));
		$this->assertTrue(wp_script_is("fergcorp_countdowntimer", "registered"));
		
		$this->assertTrue( (bool) has_action('wp_footer', array ( &$this->plugin, 'json' ) ) );
		

		$this->assertFalse( (bool) has_filter('the_excerpt', 'do_shortcode') );
		
		
		$this->assertTrue( (bool) has_action('wp_head', array( &$this->plugin, 'print_countdown_scripts' ), 1) );
		
		$this->assertTrue( (bool) has_action('admin_init', array( &$this->plugin, 'register_settings' ) ) );			//Initialized the options
		$this->assertTrue( (bool) has_action('admin_menu', array( &$this->plugin, 'register_settings_page' ) ) );	//Add Action for adding the options page to admin panel

		$this->assertArrayHasKey("fergcorp_cdt_single", $GLOBALS["shortcode_tags"]);  
		$this->assertArrayHasKey("fergcorp_cdt", $GLOBALS["shortcode_tags"]);
		
		//@TODO: Need to test this
		//$plugin = plugin_basename("countdown-timer/fergcorp_countdownTimer.php");
		//var_dump($plugin);
		//var_dump($GLOBALS["wp_filter"]);
		//$this->assertTrue( (bool) has_filter("plugin_action_links_$plugin", array( &$this->plugin, 'settings_link' ) ) );
		
	}
	
	public function test_loadSettings(){
		
		$defaultInstallValues = array(
		'deleteOneTimeEvents',
		'timeFormat',
		'showYear',
		'showMonth',
		'showWeek',
		'showDay',
		'showHour',
		'showMinute',
		'showSecond',
		'stripZero',
		'enableJS',
		'timeSinceTime',
		'titleSuffix',
		'enableShortcodeExcerpt',
		'version');
		
		//First we need to create a ReflectionClass object
		//passing in the class name as a variable
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer");
		
		//Clear the values, since they're initiated in the setup
		$props   = $reflection_class->getProperties(ReflectionProperty::IS_PRIVATE);
		foreach ($props as $prop) {
			$prop->setAccessible(true);
			if(key_exists($prop->getName(), $defaultInstallValues)){
				$prop->setValue($this->plugin, NULL);
			}
		}

		//Reload the settings
		$this->plugin->loadSettings();
		
		$loaded_settings = array();
		
		//Test to make sure they were loaded correctly
		$props   = $reflection_class->getProperties(ReflectionProperty::IS_PRIVATE);
		foreach ($props as $prop) {
			$prop->setAccessible(true);
			$loaded_settings[$prop->getName()] = $prop->getValue($this->plugin);
		}

		foreach($defaultInstallValues as $value){
			$this->assertEquals(get_option("fergcorp_countdownTimer_{$value}"), $loaded_settings[$value], "Failed to match $value: " . get_option("fergcorp_countdownTimer_{$value}") . " = " . $loaded_settings[$value]);
		}
		
		$this->assertEquals(get_option("fergcorp_countdownTimer_oneTimeEvent"), $loaded_settings["eventList"]);
		
	}

	public function test_print_admin_script(){
		//@TODO
		//$this->plugin->print_admin_script();
		//$this->assertTrue(wp_script_is("postbox", "enqueued"));
	}

	public function test_print_countdown_scripts(){
		//@TODO
	}
	
	public function test_register_settings_page(){
		//@TODO
		//has_action( 'admin_print_scripts-' . $settings_page, array( &$this, 'print_countdown_scripts' ) );
	}
	
	public function test_settings_page(){
		//@TODO:
		//$this->expectOutputRegex("(.*)");
		//$this->plugin->settings_page();
	}
	
	public function test_display_options_meta_box(){
		//@TODO
	}
	
	public function test_events_meta_box(){
		//@TODO
	}
	
	public function test_installation_meta_box(){
		//@TODO
	}
	
	public function test_management_meta_box(){
		//@TODO
	}
	
	public function test_onHover_time_format_meta_box(){
		//@TODO
	}
	
	public function test_display_format_options_meta_box(){
		//@TODO
	}
	
	public function test_example_display_meta_box(){
		//@TODO
	}
	
	public function test_singleTimer(){
		//@TODO
	}
	
	public function test_showTimer(){
		//@TODO
	}
	
	public function test_formatEvent(){
		//@TODO
	}
	
	public function test_fuzzyDate(){
		//@TODO
	}
	
	public function test_shortcode_showTimer(){
		//@TODO
	}
	
	public function test_shortcode_singleTimer(){
		//@TODO
	}
	
	public function test_register_settings(){
		//@TODO
	}
	
	public function test_sanitize(){
		//@TODO
	}
	
	public function test_json(){
		//@TODO
	}
	
	public function test_install(){
		//@TODO
	}
	
	public function test_install_option(){
		$prefix = "test";
		$option = "option";
		$default = "default";
		
		$this->assertTrue($this->plugin->install_option($prefix, $option, $default));
		$this->assertFalse($this->plugin->install_option($prefix, $option, $default));
		$this->assertEquals(get_option($prefix.$option), $default);
	}
	
	public function test_build_input(){
		$this->expectOutputRegex("/^ <input(.*?)type=\"checkbox\" name=\"fergcorp_countdownTimer_oneTimeEvent\[1\]\[timeSince\]\" value=\"1\"(.*?)checked=\'checked\' \/>$/");	
		echo $this->plugin->build_input(array(
												"type" => "checkbox",
												"name" => "fergcorp_countdownTimer_oneTimeEvent[1][timeSince]",
												"value" => 1,
												), 
											checked("1", "1", false)
										);
	}
	
	public function test_build_yes_no_1(){
		$this->expectOutputRegex("/^ <input(.*?)name=\"name\"(.*?)checked=\'checked\' \/>(.*?)<input(.*?)name=\"name\"(.*?) \/>No$/");
		echo $this->plugin->build_yes_no("name", "1");
	}
	
	public function test_build_yes_no_0(){
		$this->expectOutputRegex("/^ <input(.*?)name=\"name\"(.*?) \/>(.*?)<input(.*?)name=\"name\"(.*?)checked=\'checked\' \/>No$/");
		echo $this->plugin->build_yes_no("name", "0");
	}
	
	public function test_fergcorp_countdown_timer_register_widgets(){
		fergcorp_countdown_timer_register_widgets();
	}
	
	public function test_fergcorp_countdownTimer(){
		
		fergcorp_countdownTimer();
	}
	
	public function tearDown(){
		 parent::tearDown();
		 unset($this->plugin);
		
	}
}

class Countdown_Timer_Event_Test extends WP_UnitTestCase {
    public $plugin_slug = 'countdown_timer';
	private $plugin;
	
	//TOTEST:
	/* Input validation? Only test valid inputs...don't test for invalid?
	 * Test date manipulation
	 * Test Defaults
	 * Test blanks (e.g. database corruption)
	 * 
	*/
	  
    public function setUp() {
        parent::setUp();
		
		$date = "31 January 2015"; //1422662400
		
		$GLOBALS['fergcorp_countdown_timer_event'] = new Fergcorp_Countdown_Timer_Event( strtotime( $date ), "myTitle", "http://google.com",  true);
        $this->plugin = $GLOBALS['fergcorp_countdown_timer_event'];
		
		//$this->plugin->__construct();
    }
	
	public function testTrue(){
		$this->assertTrue(true);
	}
	
	public function test_construct(){
		$reflection_class = new ReflectionClass("Fergcorp_Countdown_Timer_Event");
		
		$values = array("title" => "myTitle", 
						"time" => "1422662400",
						"url" => "http://google.com",
						"timeSince" => "1",
						
						);
		
		
		$props = $reflection_class->getProperties(ReflectionProperty::IS_PRIVATE);
		
		foreach ($props as $prop) {
			$prop->setAccessible(true);
			echo $prop->getName() . ": " . $prop->getValue($this->plugin) ."\n";
			if(key_exists($prop->getName(), $values)){
				$this->assertEquals($values[$prop->getName()], $prop->getValue($this->plugin), "Failed to match: ".$prop->getName());
			}
		}
		
	}
}