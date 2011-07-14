<?php
/*
Plugin Name: Display Time(zone)
Plugin URI: http://apps.tutorboy.com/display-timezone/
Description: Simple plug-in to display current time with timezone in the upper right of your admin screen on every page. It takes the values from the option Timezone, Date Format, Time Format and starts the clock.
Author: Midhun Devasia,kavitharmenon
Version: 1.0.3
Author URI: http://tutorboy.com
License: GPLv2

	Copyright 2010  Midhun Devasia  (email : midhun@tutorboy.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


    Contributer: Jacob Wright <http://jacwright.com/> whos wrote the javascript function for the dateformat.
*/


// This just echoes the current time according to the timezone set from the WordPress date settings.
$wp_v = $wp_version;

function tboy_getTimezone() {
	date_default_timezone_set(get_option('timezone_string'));
	$date = strtotime(date(get_option('date_format') . " " . get_option('time_format'))); 
	
	$jscript = "
	<script>
	var sec = 0, cd = new Date();
	tboy_setTimezone();
	tboy_getTimezone();
	setInterval(\"tboy_getTimezone()\", 1000 );
	
	function tboy_setTimezone(){
		cd.setYear(".date('Y', $date).");
		cd.setMonth((".date('m', $date)."-1));
		cd.setDate(".date('d', $date).");
		cd.setHours(".date('H', $date).");
		cd.setMinutes(".date('i', $date).");
		sec = ".date('s', $date).";
		cd.setSeconds(sec);
	}
	
	function tboy_getTimezone(){
		(sec == 60 )? sec = 1 : sec++;
		cd.setSeconds(sec);
		document.getElementById(\"tboy_displayTimezoneSpan\").innerHTML  = cd.format('". get_option('date_format') . " " .get_option('time_format')."'); 
	}
	
	</script>";
	
	echo "<p id='tboy_displayTimezone'>
		<b><span id=\"tboy_displayTimezoneSpan\">" . 
		date(get_option('date_format') . " " . 
		get_option('time_format')) . "</span></b>
		(" . date_default_timezone_get() .")</p> ";
		
	echo $jscript ;
}
// We need some CSS to position the paragraph
function tboy_displayTimezone_css() {
	GLOBAL $wp_v;
	// This makes sure that the posinioning is also good for right-to-left languages
	$dir1 = WP_PLUGIN_URL.'/display-timezone';
	$x = ( is_rtl() ) ? 'left' : 'right';
	$top_pos = (version_compare($wp_v, '3.2', '>='))?'top:0.8em':'top:4.5em';
	echo "
	<style type='text/css'>
	#tboy_displayTimezone {
		position: absolute;
		$top_pos;
		margin: 0;
		padding-left: 20px;
		$x: 215px;
		font-size: 11px;
		background-image: url({$dir1}/clock.png);
      	background-repeat: no-repeat;
	}
	</style>
	";
}

   
function tboy_getTimezone_script_css(){
	define(WPDTZ_URL, WP_PLUGIN_URL.'/display-timezone/');
	wp_register_script('tboy_getTimezone_script', WPDTZ_URL. 'js/Date.format.js');
	// enqueue the script
	wp_enqueue_script('tboy_getTimezone_script');
 
}
add_action('init', 'tboy_getTimezone_script_css');
add_action('admin_head', 'tboy_displayTimezone_css');
// Now we set that function up to execute when the admin_footer action is called
add_action('admin_footer', 'tboy_getTimezone');
?>
