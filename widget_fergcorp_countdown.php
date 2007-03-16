<?php
/*
Plugin Name: Countdown Timer Widget Add-on
Description: Allows user to embed the Countdown Timer as a widget (user will also need to activate regular Countdown Timer)
Author: Andrew Ferguson
Version: 0.1
Author URI: http://www.andrewferguson.net

Countdown Timer Widget Add-on - Allows user to embed the Countdown Timer as a widget
Copyright (c) 2006-2007 Andrew Ferguson

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

// Put functions into one big function we'll call at the plugins_loaded
// action. This ensures that all required plugin functions are defined.
function widget_fergcorp_countdown_init() {

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	// This is the function that outputs our little Google search form.
	function widget_fergcorp_countdown($args) {
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);
		
		$title = "Countdown";		

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;

		?>
			<ul>
			    <?php afdn_countdownTimer(); ?>
			</ul>
		<?php
		echo $after_widget;
	}

	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget(array('Countdown Widget', 'widgets'), 'widget_fergcorp_countdown');

}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_fergcorp_countdown_init');

?>
