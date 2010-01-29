<?php
global $wpdb;

if(get_option('spdfi_debugmode') == 1)
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


# STAGE 4 CUSTOM FIELDS - DISPLAY IF STAGE 3 IS COMPLETE OR STAGE 4 FORM ALREADY SUBMITTED
if((isset($_POST['stage']) && $_POST['stage'] == 4) || (isset($stage3complete) && $stage3complete == true))
{ 
	// echos errors if stage to stage variables are not set
	require( 'new_campaign_stages/stage_varto_stage.php' );

	if(!empty($_POST['customfieldssubmit']))
	{		
		require( 'new_campaign_stages/stage4_process.php' );
	}
	
	if(!isset($stage4complete) || $stage4complete != true)
	{
		require( 'new_campaign_stages/stage4_form.php' );
	}
}


# STAGE 5 CATEGORY FILTERING - DISPLAY IF STAGE 4 IS COMPLETE OR STAGE 5 FORM ALREADY SUBMITTED - STAGE 5 IS CATEGORY COLUMN SELECTION
if((isset($_POST['stage']) && $_POST['stage'] == 5) || (isset($stage4complete) && $stage4complete == true))
{ 
	// echos errors if stage to stage variables are not set
	require( 'new_campaign_stages/stage_varto_stage.php' );

	if(!empty($_POST['categoryfiltervalues']))// checks form submission
	{
		require( 'new_campaign_stages/stage5_process.php' );
	}

	if(!isset($stage5complete) || $stage5complete != true)
	{
		require( 'new_campaign_stages/stage5_form.php' );
	}
}

# STAGE 6 - DISPLAY IF STAGE 5 IS COMPLETE OR STAGE 5 FORM ALREADY SUBMITTED
if((isset($_POST['stage']) && $_POST['stage'] == 6) || (isset($stage5complete) && $stage5complete == true))
{ 
	// echos errors if stage to stage variables are not set
	require( 'new_campaign_stages/stage_varto_stage.php' );
	
	// if in demo mode pause all current campaigns
	if(get_option('spdfi_demomode') == 1)
	{
		$sqlQuery = "UPDATE " .
		$wpdb->prefix . "spdfi_campaigns SET stage = '200' WHERE stage = '100'";
		$wpdb->query($sqlQuery);
	}
    
	// update campaign to complete but in paused mode
	$sqlQuery = "UPDATE " .
	$wpdb->prefix . "spdfi_campaigns SET filtercolumn = '$filtercolumn', stage = '100' WHERE id = '$camid'";
	$wpdb->query($sqlQuery);

	echo '<h2>New Campaign Stage 6 - Campaign Complete!</h2>';
	messagebox_spdfi('successSmall', 'Success - Your campaign has been created, please go to Campaign Management and click start to begin.');
	
	if($demomode = get_option('spdfi_demomode') == 1)
	{ 
		echo '<p>Demo Mode: In demo mode any existing campaigns are paused automatically to allow the new campaign to run straight away.';
	}    
	
	if(get_option('spdfi_debugmode') == 1)
	{?>
		<div id="poststuff" class="metabox-holder">
			<div id="post-body">
				<div id="post-body-content">
					<div class="postbox">
						<h3 class='hndle'><span>Final Campaign Settings - Switch Off Debugging To Remove This Box</span></h3>
						<div class="inside">  
						  
							<ul>
							  <li><strong>Default Category: <?php echo $defaultpostcategory; ?>                        </strong></li>
							  <li><strong>Filter Column 1 (Parent): <?php if($filtercolumn == 999){echo'Not Used';}else{echo $filtercolumn;} ?>                        </strong></li>
							  <li><strong>Filter Column 2 (Child of Parent): <?php if($filtercolumn2 == 999){echo'Not Used';}else{echo $filtercolumn2;} ?>                        </strong></li>
							  <li><strong>Filter Column 3 (Child of Child): <?php if($filtercolumn3 == 999){echo'Not Used';}else{echo $filtercolumn3;} ?>                        </strong></li>
							  <li><strong>Filter Method: <?php echo $pfm; ?>                        </strong></li>
                              <li>Randomdate: <?php if(!empty($_POST['randomdate'])){echo $_POST['randomdate'];}else{echo 'Random Date Not Selected';} ?>                        </li>
                              <li>Post Status: <?php echo $_POST['poststatus']; ?>                        </li>
                              <li>CSV file column total: <?php echo $csvfile_columntotal; ?>                    </li>
                              <li>Delimiter: <?php echo $delimiter; ?>                        </li>
                              <li>CSV File Directory: <?php echo $csvfiledirectory; ?>                        </li>
                              <li>Campaign ID: <?php echo $camid; ?>                        </li>
                              <li>Selected Layout: <?php echo $layoutfile; ?></li>
						  </ul>
							
						</div>  
					</div>
				</div>
			</div>
		</div>
	<?php
	}	
}

$debugmode = get_option('spdfi_debugmode');
if( $debugmode == 1 ){ $wpdb->hide_errors(); }
?>