<?php
/*
	Plugin Name: ShopperPress DataFeed Importer
	Version: 0.1
	Plugin URI: http://www.webtechglobal.co.uk
	Description: Import csv files download from datafeed services into your ShopperPress themed blog!
	Author: Ryan Bayne
	Author URI: http://www.webtechglobal.co.uk
*/
global $wpdb;

// set error handler function
include('functions/reporting_functions.php');

// fix for mac users
ini_set('auto_detect_line_endings', 1);

// include PEAR csv function files
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){ini_set('include_path',rtrim(ini_get('include_path'),';').';'.dirname(__FILE__).'/pear/');} 
else{ini_set('include_path',rtrim(ini_get('include_path'),':').':'.dirname(__FILE__).'/pear/');}
require_once 'File/CSV.php';

// plugin activation and installation hook then functions
function init_campaigndata_tables_wtg_spdfispdfi () 
{
	include('functions/config_functions.php');
	spdfi_databaseinstallation(0);
	spdfi_optionsinstallation(0);
}
register_activation_hook(__FILE__,'init_campaigndata_tables_wtg_spdfispdfi');

# CAMPAIGN PROCESSING TRIGGERS
function wtg_spdfi_processcheck()
{ 
	global $wpdb;

	# TRIGGER CSV 2 POST PROCESSING
	$_SESSION['page'] = $_SERVER['PHP_SELF']; // set current page into session and use on next page load to prevent processing if page not differrent

	$count = $wpdb->get_var("SELECT COUNT(*) FROM " .$wpdb->prefix . "spdfi_campaigns WHERE stage = '100' AND process != '3'");

	if( $count > 0 )// a campaign is ongoing
	{
		require_once('functions/global_functions.php');
		require( 'functions/postmaker_functions.php' );
		require('postmaker_spdfi.php');
	}
	else // check campaigns controlled by cron scheduling
	{
		$count = $wpdb->get_var("SELECT COUNT(*) FROM " .$wpdb->prefix . "spdfi_campaigns WHERE stage = '100' AND process = '3'");
		
		if( $count > 0 )
		{
			// a cron scheduled campaign is ongoing				
			function spdfi_cronscheduledcampaign()// if scheduled post is due then  this function is called 
			{
				require_once( 'global_functions.php' );
				require( 'functions/postmaker_functions.php' );
				require('postmaker_spdfi.php');
			}
			add_action('cronschedulledprocessing','spdfi_cronscheduledcampaign');// checks if scheduled posts are due
		}
		else
		{	
			update_option('spdfi_currentprocess',0);// do nothing no campaigns ongoing
		}
	}
}

// check if any ongoing processing is happening before triggering further processing
$t = get_option('spdfi_lastprocessingtime');
$c = get_option('spdfi_currentprocess');
$t = $t + 5;// add ten seconds to make extended time

if($c == 0 && $t < time())// if the extended time
{
	// set the current time for preventing further processing in none FULL modes within the time limit.
	update_option('spdfi_lastprocessingtime',time());// indicate processing ongoing
	update_option('spdfi_currentprocess',1);// indicate processing ongoing
	add_action(get_option('spdfi_processingtrigger'), 'wtg_spdfi_processcheck');// trigger processing
}
update_option('spdfi_currentprocess',0);// set back to zero to allow processing here on


// import css file for plugin only
function spdfi_plugincss() 
{
	// NEW METHOD
	$url = WP_CONTENT_URL . '/plugins/shopperpress-dfi-freeedition/style.css';

    echo '<link rel="stylesheet" type="text/css" href="' . $url . '" />';

	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'jquery-color' );
	wp_print_scripts('editor');
	if (function_exists('add_thickbox')) add_thickbox();
	wp_print_scripts('media-upload');
	if (function_exists('wp_tiny_mce')) wp_tiny_mce();
	wp_admin_css();
	wp_enqueue_script('utils');
	do_action("admin_print_styles-post-php");
	do_action('admin_print_styles');
}

add_action('admin_head', 'spdfi_plugincss');

add_action('admin_menu', 'wtg_spdfispdfi_add_pages');

function wtg_spdfispdfi_add_pages() 
{
	if(get_option('spdfi_demomode') == 1){$i = 0;}else{$i = 8;}
	
	add_menu_page('S.P. DFI Free', 'S.P. DFI Free', $i, __FILE__, 'wtg_spdfispdfi_toplevel_page');
    add_submenu_page(__FILE__, 'New Campaign', 'New Campaign', $i, 'new_campaign_spdfi', 'wtg_spdfispdfi_sublevel_page1');
    add_submenu_page(__FILE__, 'Manage Campaigns', 'Manage Campaigns', $i, 'manage_campaigns_spdfi', 'wtg_spdfispdfi_sublevel_page2');
    add_submenu_page(__FILE__, 'Disclaimer', 'Disclaimer', $i, 'disclaimer_spdfi', 'wtg_spdfispdfi_sublevel_page3');
    add_submenu_page(__FILE__, 'Settings', 'Settings', $i, 'settings_spdfi', 'wtg_spdfispdfi_sublevel_page4');
    add_submenu_page(__FILE__, 'Tools', 'Tools', $i, 'tools_spdfi', 'wtg_spdfispdfi_sublevel_page5');
    add_submenu_page(__FILE__, 'Layouts (WYSIWYG)', 'Layouts (WYSIWYG)', $i, 'layouts_spdfi', 'wtg_spdfispdfi_sublevel_page6');
    add_submenu_page(__FILE__, 'CSV Uploader', 'CSV Uploader', $i, 'uploader_spdfi', 'wtg_spdfispdfi_sublevel_page7');
}

function wtg_spdfispdfi_toplevel_page() 
{
	include_once('functions/global_functions.php');
    require('main_page.php');
}

function wtg_spdfispdfi_sublevel_page1() 
{
	include_once('functions/global_functions.php');
	require('newcampaign_spdfi.php');
}
function wtg_spdfispdfi_sublevel_page2() 
{
	include_once('functions/global_functions.php');
	require('editcampaign_spdfi.php');
}
function wtg_spdfispdfi_sublevel_page3() 
{
	include_once('functions/global_functions.php');
	require('disclaimer_spdfi.php');
}
function wtg_spdfispdfi_sublevel_page4() 
{
	include_once('functions/global_functions.php');
	require('settings_spdfi.php');
}
function wtg_spdfispdfi_sublevel_page5() 
{
	include_once('functions/global_functions.php');
	require('tools_spdfi.php');
}
function wtg_spdfispdfi_sublevel_page6() 
{
	include_once('functions/global_functions.php');
	require('layouts_spdfi.php');
}
function wtg_spdfispdfi_sublevel_page7() 
{
	include_once('functions/global_functions.php');
	require('csvuploader_spdfi.php');
}
?>