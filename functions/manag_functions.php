<?php
function spdfi_startpausecancelled($s,$c,$id)
{	// s = stage c = campaign name id = campaign id
	$SPClink = '<td><a href="';
	// First create link
	$SPClink .= $_SERVER['PHP_SELF'];
	if($s == 100)
	{
		$SPClink .= '?page=manage_campaigns_plus&id='.$id.'&action=pause';
	}
	elseif($s == 200)
	{
		$SPClink .= '?page=manage_campaigns_plus&id='.$id.'&action=start';
	}
	elseif($s == 300)
	{
		$SPClink .= '?page=manage_campaigns_plus&id='.$id.'&action=complete';
	}
	
	// Meta title
	$SPClink .= '" title="';
	if($s == 100)
	{
		$SPClink .= 'Click to pause '.$c;
	}
	elseif($s == 200)
	{
		$SPClink .= 'Click to start '.$c;
	}
	elseif($s == 300)
	{
		$SPClink .= 'Campaign '.$c.' is finished.';
	}
	$SPClink .= '">';
	
	// Link text
	if($s == 100)
	{
		$SPClink .= 'Pause';
	}
	elseif($s == 200)
	{
		$SPClink .= 'Start';
	}
	elseif($s == 300)
	{
		$SPClink .= 'Complete';
	}
	
	$SPClink .= '</a></td>';
	
	echo $SPClink;
}

# FUNCTION CREATES UNDO LINK FOR EACH CAMPAIGN
function spdfi_campaignundo($s,$c,$id)
{	// s = stage c = campaign name id = campaign id
	$UNDOlink = '<td><a href="';
	
	// First part of URL is PHP SELF
	$UNDOlink .= $_SERVER['PHP_SELF'];
	// Use campaign ID and action in rest of URL
	$UNDOlink .= '?page=manage_campaigns_plus&id='.$id.'&action=undo';
	// Start meta title
	$UNDOlink .= '" title="';
	// Meta link title
	$UNDOlink .= 'Click to undo posts created by '.$c;	
	$UNDOlink .= '">';
	// Link title/name
	$UNDOlink .= 'Undo';
	// End a href
	$UNDOlink .= '</a></td>';
	
	echo $UNDOlink;
}

# FUNCTION DELETES CAMPAIGN INSTANCE FROM CSV 2 POST DATABASE
function spdfi_campaigndelete($id)
{
	echo '<td><a href="' . $_SERVER['PHP_SELF'] . '?page=manage_campaigns_plus&id=' . $id . '&action=delete" title="Delete campaign '. $id .'">Delete</a></td>';
}

function spdfi_campaignview($name,$id)
{
	echo '<td><a href="' . $_SERVER['PHP_SELF'] . '?page=manage_campaigns_plus&id=' . $id . '&action=view" title="Manage campaign '. $name .'">Manage '. $name .' </a></td>';
}

function spdfi_pausecampaign($camid)
{
	global $wpdb;
	$sqlQuery = "UPDATE " .	$wpdb->prefix . "spdfi_campaigns SET stage = '200' WHERE id = '$camid'";
	$result = $wpdb->query($sqlQuery);
	if($result){return '<h3>Campaign Now Paused</h3>';}else{return '<h3>Campaign Failed To Stop!</h3>';}
}

function spdfi_startcampaign($camid)
{
	global $wpdb;
	$sqlQuery = "UPDATE " .	$wpdb->prefix . "spdfi_campaigns SET stage = '100' WHERE id = '$camid'";
	$result = $wpdb->query($sqlQuery);
	if($result){return '<h3>Campaign Now Running</h3>';}else{return '<h3>Campaign Failed To Start</h3>';}
}

function spdfi_deletecampaign($camid)
{
	global $wpdb;
	$result = $wpdb->query(" DELETE FROM " . $wpdb->prefix . "spdfi_campaigns WHERE id = '$camid'");
	if($result){return '<h3>Campaign Deleted</h3>';}else{return '<h3>Deleting Campaign Failed</h3>';}
}

function spdfi_camman_process($v)
{
	if($v == 1)
	{
		return 'Full (entire file)';
	}
	elseif($v == 2)
	{
		return 'Staggered (per blog hit)';
	}	
	elseif($v == 3)
	{
		return 'Scheduled (spread over 24 hours)';
	}
}

function spdfi_camman_stage($v)
{
	if($v < 100)
	{
		return 'Campaign Not Setup - Finished Creating At Stage ' . $v . '';
	}
	elseif($v == 100)
	{
		return 'Campaign Running!';
	}
	elseif($v == 200)
	{
		return 'Campaign Paused!';
	}
	elseif($v == 300)
	{
		return 'Campaign Finished!';
	}
	elseif($v == 400)
	{
		return 'Cancelled!';
	}
	elseif($v == 999)
	{
		return 'Not Used';
	}
}
?>