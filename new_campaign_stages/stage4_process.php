<?php

global $wpdb;

$pfm = $_POST['filtermethod'];

if(empty($pfm))
{
	echo '<h3>You did not select a filter method</h3>';
}
else
{
	if($pfm == 'manual' || $pfm == 'mixed')// mixed and manual requires more data from form
	{
	
	$filtercolumn =	$_POST['optedfiltercolumn'];
	@$defaultpostcategory = $_POST['defaultpostcategory'];
	$defaultphase =	get_option('csv2post_defaultphase');

	$sqlQuery = "UPDATE " .
	$wpdb->prefix . "spdfi_campaigns SET defaultcat = '$defaultpostcategory',filtercolumn = '$filtercolumn',filtermethod = '$pfm',stage = '4',allowupdate = '$defaultphase' WHERE id = '$camid'";
	$wpdb->query($sqlQuery);
	
	$stage4complete = true;

}// end if filter method selected

?>