<?php
class PluginTest extends WP_UnitTestCase {
    
    /**
    * @group init
    */
    // Check that that activation doesn't break
    function test_plugin_activated() {
        $this->assertTrue( is_plugin_active( PLUGIN_PATH ) );
    }
}