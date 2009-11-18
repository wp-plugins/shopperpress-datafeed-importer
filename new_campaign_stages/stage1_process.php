<?php
# PROCESS SUBMISSION AND EITHER FORWARD TO NEXT STAGE OR DISPLAY ERRORS
if(empty($_POST['campaignname']) || !is_string($_POST['campaignname']))
{
	echo '<h2>Invalid campaign name please try again!</h2>';
}
elseif(!empty($_POST['campaignname']) || is_string($_POST['campaignname']))
{
	if(empty($_POST['layoutstyle']))
	{
		echo '<h2>Please select a post layout template in part (c)</h2>';
	}
	elseif(!empty($_POST['layoutstyle']))
	{
		# ENSURE ALL REQUIRED OPTIONS SELECTED AND COMPLETE
		if(empty($_POST['processrate']))
		{
			echo '<h2>Sorry no process rate selected</h2>';
		}
		else
		{
			# ALL REQUIRED DETAILS CAPTURED SO NOW PROCESS
			$camname = $_POST['campaignname'];
			$process = $_POST['processrate'];
			$csvfilename = $_POST['csvfilename'];
			$layoutstyle = $_POST['layoutstyle'];
						
			// get csv file directory
			$target_path = spdfi_getcsvfilesdir();

			# CALL UPLOAD PROCESS FUNCTION PASSING REQUIRED VALUES ONLY

			$csvfiledirectory = $target_path . $csvfilename;
			
			# LINK LOCATION - FULL PROCESSING 						
			$fileexists = file_exists($csvfiledirectory);
			
			if($fileexists == false)
			{
				echo 'CSV file not found';
			}
			elseif(!isAllowedExtension_spdfi($csvfiledirectory))
			{
				echo '<h2>Sorry a slight problem! Only CSV files are allowed please try again</h2>';
			}
			else
			{	
				// file exists - store name for displaying as "Last Used Filename"
				update_option('spdfi_lastfilename',$csvfilename);
		
				// check if delimiter is being forced and not automatic using PEAR
				if(!empty($_POST['forcedelimiter']))
				{
					$delimiter = $_POST['forcedelimiter'];	
				}
				else
				{
					$delimiter = 0;	// set default and on stage 2 if default is set, the automatic delimiter will be entered
				}
				
				// get posts per hit ratio from options
				$ratio = get_option('spdfi_postsperhit');

				$sqlQuery = "INSERT INTO " .
				$wpdb->prefix . "spdfi_campaigns(camname,camfile,process,stage,layoutfile,delimiter,ratio)
				VALUES('$camname', '$csvfilename','$process','2','$layoutstyle','$delimiter','$ratio')";
				$stage1complete = spdfi_queryresult($wpdb->query($sqlQuery));
				$camid = $wpdb->insert_id;
			}
		}// check all required posts populated
	}
}// campaign name not empty and is string
?>