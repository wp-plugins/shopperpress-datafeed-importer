<?php


function spdfi_updatecounters_postmaker($id,$rowsprocessed_event,$rowsused_event,$rowsdropped_event)
{
	global $wpdb;
	
	$cam = $wpdb->get_row("SELECT updatedposts,posts,allowupdate,phase,csvrows,droppedrows FROM " .$wpdb->prefix . "spdfi_campaigns WHERE id = '$id'");
	
	if($cam->phase == 2)// update processing phase
	{
		$updatedposts = $rowsused_event + $cam->updatedposts ;// actual posts updated - not rows processed - used to determine number of rows actually used

		$sqlQuery = "UPDATE " .	$wpdb->prefix . "spdfi_campaigns SET updatedposts = '$updatedposts',droppedrows = '$rowsdropped_event'  WHERE id = '$id'";
		$wpdb->query($sqlQuery);
		
		$passedrows_event = $cam->droppedrows + $rowsdropped_event + $rowsused_event + $cam->updatedposts;

		if($passedrows_event >= $cam->csvrows && $cam->allowupdate == 1)// reset updated posts counter
		{
			$sqlQuery = "UPDATE " .	$wpdb->prefix . "spdfi_campaigns SET updatedposts = '0',droppedrows = '0'  WHERE id = '$id'";
			$wpdb->query($sqlQuery);
		}
	}
	elseif($cam->phase == 1)// normal processing phase
	{
		$totalposts = $cam->posts + $rowsused_event;

		$sqlQuery = "UPDATE " .	$wpdb->prefix . "spdfi_campaigns SET posts = '$totalposts',droppedrows = '$rowsdropped_event'   WHERE id = '$id'";
		$wpdb->query($sqlQuery);
					
		$passedrows_event = $cam->droppedrows + $rowsdropped_event + $rowsused_event + $cam->posts;
			
		if($passedrows_event >= $cam->csvrows && $cam->allowupdate == 1)// switches to update phase   NEW METHOD
		{ 
			$sqlQuery = "UPDATE " .	$wpdb->prefix . "spdfi_campaigns SET phase = '2',updatedposts = '0',droppedrows = '0' WHERE id = '$id'";
			$wpdb->query($sqlQuery);
		}
	}
}

function spdfi_custompostlayout_postmaker($camlayoutfile,$camcamname,$camid)
{
	global $wpdb;	
	$r = $wpdb->get_row("SELECT * FROM " .$wpdb->prefix . "spdfi_layouts WHERE id = '$camlayoutfile'");
	if( !$r ){ error_spdfiplus(__LINE__,__FILE__,'Custom Post Layout not found for a current campaign! Name: ' . $cam->camname .' ID: '. $cam->id .''); return; }
	return eval( $r->code);
}

function spdfi_postpart_postmaker($column_counter_getdata,$camid)
{
	global $wpdb;	
	return $wpdb->get_var("SELECT postpart FROM " .$wpdb->prefix . "spdfi_relationships WHERE csvcolumnid = '$column_counter_getdata' AND camid = '$camid'");
}

function spdfi_duplicatecustomurl_postmaker($type,$phase)
{
	if( $phase == 2 )
	{
		return 0; // no duplication check required for post update as the url will stay the same always anyway
	}
	else
	{
		if(!empty($_SESSION['post_url']))
		{	
			global $wpdb;
			$customurl = $_SESSION['post_url'];

			$wpdb->query("SELECT post_title FROM " .$wpdb->prefix . "posts WHERE post_name = '$customurl'");
			return $wpdb->num_rows;
		}
	}
}

function spdfi_sanitizetitle_postmaker($type)
{
	if( $type == 1)
	{
		return sanitize_title( $_SESSION['posttitle'] );// used to check for duplicates - now also used for actual post_name
	}
	else
	{
		return sanitize_title( $_SESSION['posttitle'] );// used to check for duplicates - now also used for actual post_name
	}
}

function spdfi_duplicateposttitle_postmaker($phase,$default_postname)
{			
	global $wpdb;	
	if( $phase == 2 )
	{
		return 0; // no duplication check required for post update as the url will stay the same always anyway
	}
	else
	{
		$count = 0;
		$wpdb->query("SELECT post_title FROM " .$wpdb->prefix . "posts WHERE post_name = '$default_postname'");
		return $wpdb->num_rows;				
	}
}

function spdfi_recordnewid_postmaker($id,$post_id)
{	
	global $wpdb;	
	$sqlQuery = "INSERT INTO " . $wpdb->prefix . "spdfi_posthistory(camid, postid) VALUES ('$id', '$post_id')";
	$wpdb->query($sqlQuery);
}

// add custom field for meta description
function spdfi_metadescription_postmaker($type, $metadescription, $post_id)
{		
	$autodescription = get_option('spdfi_autodescription');
	if($autodescription != 0)
	{
		if( $type == 1)
		{
			# DO LOCAL HOST VERSION HERE
		}
		else
		{
			add_post_meta($post_id, '_headspace_description', $metadescription, true);
		}
	}	
}
					
// add custom field for posts keywords
function spdfi_metakeywords_postmaker($type,$post_id,$metakeywords)
{	
	$autokeywords = get_option('spdfi_autokeywords');				
	if($autokeywords != 0)
	{
		if( $type == 1)
		{
			# DO LOCAL HOST VERSION HERE
		}
		else
		{
			add_post_meta($post_id, 'head_keywords', $metakeywords, true);
		}
	}
}		

function spdfi_customfielduniquecode_postmaker($type,$post_id)
{
	if( $type == 1 && !empty($_SESSION['uniquecode_spdfi']) )
	{
		# DO LOCAL HOST VERSION HERE
	}
	elseif( $type == 1 && !empty($_SESSION['uniquecode_spdfi']) )
	{
		add_post_meta($post_id, 'spdfiplus_uniquecode', $_SESSION['uniquecode_spdfi'], true);
	}
}

// insert custom fields with preset values			
function spdfi_customfieldsmanual_postmaker($id,$type,$post_id,$csvrow)
{
	global $wpdb;
	$res1 = $wpdb->get_results("SELECT identifier,value FROM " .$wpdb->prefix . "spdfi_customfields WHERE camid = '$id' AND type = '0'");
	foreach($res1 as $x)
	{   
		$key = $x->identifier;
		$value = $x->value;
		
		if( $cam->type== 1)
		{
			# DO LOCAL HOST VERSION HERE
		}
		else
		{
			add_post_meta($post_id, $key, $value, true);
		}
	}
					
	// insert custom fields that use column data for value
	$res2 = $wpdb->get_results("SELECT identifier,value FROM " .$wpdb->prefix . "spdfi_customfields WHERE camid = '$id' AND type = '1'");
	
	foreach($res2 as $y)
	{
		$v = $y->value;
		$k = $y->identifier;
	
		$column_counter = 0;
		
		foreach($csvrow as $data)
		{
			if($column_counter == $v)
			{ 
				if( $type == 1)
				{
					# DO LOCAL HOST VERSION HERE
				}
				else
				{
					add_post_meta($post_id, $k, $data, true);
				}
				
			}
			return $column_counter++;
		}
	}
}

function spdfi_customfieldsautomated_postmaker($csvfilepath,$type,$post_id,$csvrow)
{
	$stop2 = 0;
	
	$handle2 = fopen("$csvfilepath", "r");

	while (($data_cf = fgetcsv($handle2, 999999, ",")) !== FALSE && $stop2 != 1)// Gets CSV rows
	{	 
		$stop2++;// used to limit row parsing to just 1
		
	   $i = 0;
 
		while(isset($data_cf[$i]))
		{
			$data_cf[$i] = rtrim($data_cf[$i]);// gets column names - used for custom field key
			
			$b = 0;
			
			foreach($csvrow as $data)
			{  
				if($b == $i)// only take action when posts data matches correct column
				{
					if( $type == 1)
					{
						# DO LOCAL HOST VERSION HERE
					}
					else
					{
						add_post_meta($post_id, $data_cf[$i], $data, true);
					}
				}
				$b++;
			}
			$i++; // $i will equal number of columns - use to process submission
		}
	}
}
?>
