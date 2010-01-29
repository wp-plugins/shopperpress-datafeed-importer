<?php
/*
	Plugin Name: ShopperPress DFI Pro
	Version: 0.2
	Plugin URI: http://www.webtechglobal.co.uk/wordpress-services/wordpress-plugins/shopperpress-datafeed-importer
	Description: Pro csv import plugin for creating posts using datafeeds on blogs with ShopperPress themes!
	Author: Ryan Bayne
	Author URI: http://www.webtechglobal.co.uk/wordpress-services/wordpress-plugins/shopperpress-datafeed-importer
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
function init_campaigndata_tables_wtg_spdfiplus () 
{
	include('functions/config_functions.php');
	spdfi_databaseinstallation(0);
	spdfi_optionsinstallation(0);
}

// this function calls the function and post maker files only when required by scheduled campaigns		
function spdfi_cronscheduledcampaign() 
{
	spdfi_debug_write('Ran spdfi_cronscheduledcampaign function - Scheduled campaign processing begun!');
	require_once( 'global_functions.php' );
}
			
// this function is called when a campaing requires processing
function spdfi_processcheck()
{
	spdfi_debug_write(__LINE__,__FILE__,'Run spdfi_processcheck function - Start');
	global $wpdb;

	// force waiting period between processing events basedo on user settings
	$t = get_option('spdfi_lastprocessingtime');
	$t = $t + get_option('spdfi_processingdelay');// add seconds to to the old time
	
	// do not attempt processing if the page load was done on a New Campaign process Stage
	if(isset($_GET['page']) && $_GET['page'] == 'new_campaign_plus')
	{
		spdfi_debug_write(__LINE__,__FILE__,'Processing rejected due to being new campaign admin page triggering it');
	}
	else
	{
		if($t < time())// if old time (extended) is less than current time then allow processing
		{	
			// set the current time for preventing further processing in none FULL modes within the time limit.
			update_option('spdfi_lastprocessingtime',time());// indicate processing ongoing
	
			$_SESSION['lastpage'] = $_SERVER['PHP_SELF']; // set current page into session and use on next page load to prevent processing if page not differrent
		
			$count = $wpdb->get_var("SELECT COUNT(*) FROM " .$wpdb->prefix . "spdfi_campaigns WHERE stage = '100' AND process != '3'");
		
			if( $count > 0 )// a full or staggered campaign is ongoing
			{
				spdfi_debug_write(__LINE__,__FILE__,'Run spdfi_processcheck function - Full or Staggered campaign found');
				require_once('functions/global_functions.php');			}
			else // check scheduled campaigns - controlled by cron scheduling
			{
				spdfi_debug_write(__LINE__,__FILE__,'No Full or Staggered campaign found, now checking Scheduled');
				$count = $wpdb->get_var("SELECT COUNT(*) FROM " .$wpdb->prefix . "spdfi_campaigns WHERE stage = '100' AND process = '3'");
				
				if( $count > 0 )
				{
					add_action('cronschedulledprocessing','spdfi_cronscheduledcampaign');// checks if scheduled posts are due
				}
			}
		}
		else
		{
			spdfi_debug_write(__LINE__,__FILE__,'Timer has not yet expired since last processing event');
		}		
	}
}

// import css file for plugin only
function spdfi_plugincss() 
{
	// NEW METHOD
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

// add action for detecting cloaked url click
function spdfi_processcloakedurlclick() 
{
	require('cloakedurls_spdfi.php');// processes click and forwards user to destination
}

// plugin admin pages
function spdfi_add_pages() 
{
	if(get_option('spdfi_demomode') == 1){$i = 0;}else{$i = 8;}
	
	add_menu_page('spdfi PRO', 'spdfi PRO', $i, __FILE__, 'toplevel_spdfi');
    add_submenu_page(__FILE__, '1. CSV Uploader', '1. CSV Uploader', $i, 'uploader_spdfi', 'spdfi_sublevel_page7');	
    add_submenu_page(__FILE__, '2. CSV Profiles', '2. CSV Profiles', $i, 'csvprofiles_spdfi', 'spdfi_sublevel_page6');
    add_submenu_page(__FILE__, '3. New Campaign', '3. New Campaign', $i, 'newcampaign_spdfi', 'spdfi_sublevel_page1');
    add_submenu_page(__FILE__, 'Manage Campaigns', 'Manage Campaigns', $i, 'managecampaigns_spdfi', 'spdfi_sublevel_page2');
    add_submenu_page(__FILE__, 'Settings', 'Settings', $i, 'settings_spdfi', 'spdfi_sublevel_page4');
    add_submenu_page(__FILE__, 'Tools', 'Tools', $i, 'tools_spdfi', 'sublevel_page5_spdfi');

}

function toplevel_spdfi() 
{
	include_once('functions/global_functions.php');
    require('main_page.php');
}

function spdfi_sublevel_page1() 
{
	include_once('functions/global_functions.php');
	require('newcampaign_spdfi.php');
}
function spdfi_sublevel_page2() 
{
	include_once('functions/global_functions.php');
	require('editcampaign_spdfi.php');
}
function spdfi_sublevel_page4() 
{
	include_once('functions/global_functions.php');
	require('settings_spdfi.php');
}
function spdfi_sublevel_page5() 
{
	include_once('functions/global_functions.php');
	require('tools_spdfi.php');
}
function spdfi_sublevel_page6() 
{
	include_once('functions/global_functions.php');
	require('profiles_spdfi.php');
}
function spdfi_sublevel_page7() 
{
	include_once('functions/global_functions.php');
	require('csvuploader_spdfi.php');
}

// do hooks and actions
add_action('admin_menu', 'spdfi_add_pages',0);
register_activation_hook(__FILE__,'init_campaigndata_tables_wtg_spdfiplus');
add_action('status_header', 'spdfi_processcloakedurlclick');
add_action('admin_head', 'spdfi_plugincss');
add_action(get_option('spdfi_processingtrigger'), 'spdfi_processcheck');// trigger processing
?>