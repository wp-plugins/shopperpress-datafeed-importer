<?php

global $wpdb;

$debugmode = get_option('spdfi_debugmode');
if($debugmode == 1)
{
	$wpdb->show_errors();
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}

# GET OPTIONS
$auto_keywords = get_option('spdfi_autokeywords');
$auto_description = get_option('spdfi_autodescription');
$auto_tags = get_option('spdfi_autotags');

// passes post to variables from stage to stage, returning errors where required
require('new_campaign_stages/stage_postto_stage.php');

// prepare opendir for listing layout files
$php_extension = 'php';
$csv_extension = 'csv';


$csvfiles_dir = spdfi_getcsvfilesdir();
$csvfiles_diropen = opendir($csvfiles_dir);

# STAGE 1: SUBMISSION OR FIRST TIME VISIT FOR CAPTURING INITIAL CAMPAIGN SETTINGS
if(!isset($_POST['stage']) || $_POST['stage'] == 1)
{
	if(!empty($_POST['campaignsubmit']))// is stage 1 form submission made?
	{
		require( 'new_campaign_stages/stage1_process.php' );
	}

	if(!isset($stage1complete) || $stage1complete != true)
	{
		require( 'new_campaign_stages/stage1_form.php' );
	}
}

# STAGE 2: RELATIONSHIPS
if((isset($_POST['stage']) && $_POST['stage'] == 2) || (isset($stage1complete) && $stage1complete == true))
{ 
	// echos errors if stage to stage variables are not set
	require('new_campaign_stages/stage_varto_stage.php');

	if(!empty($_POST['matchsubmit']))
	{	
		require( 'new_campaign_stages/stage2_process.php' );
	}	

	if(!isset($stage2complete) || $stage2complete != true)
	{
		require( 'new_campaign_stages/stage2_form.php' );
	}
}

# STAGE 3 POST STATUS - DISPLAY IF STAGE 2 IS COMPLETE OR STAGE 3 FORM ALREADY SUBMITTED
if((isset($_POST['stage']) && $_POST['stage'] == 3) || (isset($stage2complete) && $stage2complete == true))
{ 
	// echos errors if stage to stage variables are not set
	require( 'new_campaign_stages/stage_varto_stage.php' );

	if(!empty($_POST['statussubmit']))
	{	
		require( 'new_campaign_stages/stage3_process.php' );
	}

	if(!isset($stage3complete) || $stage3complete != true)
	{
		require( 'new_campaign_stages/stage3_form.php' );
	}
}

# STAGE 5 CATEGORY FILTERING - DISPLAY IF STAGE 4 IS COMPLETE OR STAGE 5 FORM ALREADY SUBMITTED - STAGE 5 IS CATEGORY COLUMN SELECTION
if((isset($_POST['stage']) && $_POST['stage'] == 4) || (isset($stage3complete) && $stage3complete == true))
{ 
	// echos errors if stage to stage variables are not set
	require( 'new_campaign_stages/stage_varto_stage.php' );

	# PROCESS STAGE 5 FIRST SUBMISSION FOR CATEGORY COLUMN SELECTION
	if(!empty($_POST['categoryfiltervalues']))// checks form submission
	{
		require( 'new_campaign_stages/stage4_process.php' );
	}

	# ONLY DISPLAY STAGE 5 FORM IF NOT COMPLETE
	if(!isset($stage5complete) || $stage5complete != true)
	{
		require( 'new_campaign_stages/stage4_form.php' );
	}
}

# STAGE 6 - DISPLAY IF STAGE 5 IS COMPLETE OR STAGE 5 FORM ALREADY SUBMITTED
if((isset($_POST['stage']) && $_POST['stage'] == 5) || (isset($stage4complete) && $stage4complete == true))
{ 
	// echos errors if stage to stage variables are not set
	require( 'new_campaign_stages/stage_varto_stage.php' );

	# CAMPAIGN IS CREATED BUT DECIDE IF THIS CAMPAIGN CAN BE STARTED OR SET TO PAUSE AT THIS TIME	
	
	// if in demo mode pause all current campaigns
	if(get_option('spdfi_demomode') == 1)
	{
		$sqlQuery = "UPDATE " .
		$wpdb->prefix . "spdfi_campaigns SET stage = '200' WHERE stage = '100'";
		$wpdb->query($sqlQuery);
	}
    
	$count = $wpdb->get_var("SELECT COUNT(*) FROM " .$wpdb->prefix . "spdfi_campaigns WHERE stage = '100'");
	
	if( $count > 0 ){ $startstage = '200'; }else{ $startstage = '100'; }		
	
	$sqlQuery = "UPDATE " .
	$wpdb->prefix . "spdfi_campaigns SET filtercolumn = '$filtercolumn', stage = '$startstage' WHERE id = '$camid'";
	$wpdb->query($sqlQuery);

	echo '<h2>New Campaign Stage 5 - Campaign Complete!</h2>';
	messagebox_spdfi('successSmall', 'Success - Your campaign has been created and will begin running when your blog gets another page hit.');
	
	if($demomode = get_option('spdfi_demomode') == 1)
	{ 
		echo '<p>Demo Mode: In demo mode any existing campaigns are paused automatically to allow the new campaign to run straight away.';
	}                    
}

$debugmode = get_option('spdfi_debugmode');
if( $debugmode == 1 ){ $wpdb->hide_errors(); }
?>
