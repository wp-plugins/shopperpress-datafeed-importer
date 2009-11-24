<?php					
if(!isset($_GET['viewitem']))// view item number is actually post ID
{
	$valid_entry = "no";
}
elseif(isset($_GET['viewitem']))
{	
	if(!is_numeric($_GET['viewitem']))
	{
		$valid_entry = "no";
	}
	elseif(is_numeric($_GET['viewitem']))
	{
		$valid_entry = "yes";
	}
}

if($valid_entry == "no")
{ 
	//if ID is false don't need to do anything further, leave hit page with defaults			
}
elseif($valid_entry == "yes")
{	
	// using passed campaign id locate the real url and forward url
	$postid = $_GET['viewitem'];
	
	$r = $wpdb->get_row("SELECT meta_value FROM " .$wpdb->prefix . "postmeta WHERE post_id = '$postid' AND meta_key = 'spdfi_cloakedurl'");
	
	header( "Location:" . $r->meta_value ); 
}
?>