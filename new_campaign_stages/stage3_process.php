<?php
if(!isset($_POST['poststatus']))
{
	echo '<h3>You did not select a post status</h3>';
}
elseif(isset($_POST['poststatus']))
{
	$status = $_POST['poststatus'];

	if(isset($_POST['randomdate']) && $_POST['randomdate'] == 1)
	{
		$randomdate = $_POST['randomdate'];
	}
	else
	{
		$randomdate = 0;
	}
	
	global $wpdb;
	
	# UPDATE CAMPAIGN STAGE COUNTER
	$sqlQuery = "UPDATE " .
	$wpdb->prefix . "spdfi_campaigns SET stage = '4', poststatus = '$status',randomdate = '$randomdate' WHERE id = '$camid'";
	$wpdb->query($sqlQuery);
	$stage3complete = true;
}
?>