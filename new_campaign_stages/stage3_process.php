<?php

if(!isset($_POST['poststatus']))
{
	echo '<h3>You did not select a post status</h3>';
}
elseif(isset($_POST['poststatus']))
{
	$status = $_POST['poststatus'];
	
	# UPDATE CAMPAIGN STAGE COUNTER
	$sqlQuery = "UPDATE " .
	$wpdb->prefix . "spdfi_campaigns SET stage = '4', poststatus = '$status' WHERE id = '$camid'";
	$wpdb->query($sqlQuery);
	$stage3complete = true;
}
?>