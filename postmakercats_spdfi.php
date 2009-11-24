<?php

// used in post-maker.php to create categories and get category ID's for post filtering
$catidarray = array();

if(isset($_SESSION['spdfi_category1'])){$categoryname1 = htmlentities2( $_SESSION['spdfi_category1'] );};

// PARENT - this is the first category to be created or ID retrieved, it may have a parent set in options but mostly will be the parent for processing
if( $cam->filtermethod == 'NA')
{
	$catid = $cam->defaultcat;
	$catidarray['0'] = $catid;
}
else
{
	if( $cam->filtermethod== 'automated' && $cam->filtercolumn != 999)
	{ 
		// if category name does not exist create it
		$catid = get_cat_ID( $categoryname1 );// returns false if no match found else true
		
		// make new category if false returned
		if($catid == false)
		{
			if($catparent != 'NA')// if = 1 all new categories have the set parent from options page
			{
				if($catid == false){$catid = wp_create_category($categoryname1, $catparent);}	
			}
			else
			{
				if($catid == false){$catid = wp_create_category($categoryname1);}					
			}
		}
	}
	elseif( $cam->filtermethod== 'mixed' && $cam->filtercolumn != 999)
	{
		# FIRSTLY CHECK IF USER HAS SET THE VALUE MANUALLY AND MATCHED IT TO A CATEGORY OTHERWISE CREATE THE CATEGORY
		$val = $categoryname1;// unique value from csv column
		
		$catid = $wpdb->get_var("SELECT catid FROM " .$wpdb->prefix . "spdfi_categories WHERE camid = '$cam->id' AND uniquevalue = '$val'");							
	
		if($catid = false)
		{
			# NO MATCHING CATEGORY FOUND SO CREATE ONE
			if($catparent != 'NA')
			{
				if($catid == false){$catid = wp_create_category($categoryname1, $catparent);}	
			}
			else
			{
				if($catid == false){$catid = wp_create_category($categoryname1);}	
			}
		}
	}
	elseif( $cam->filtermethod== 'manual' && $cam->filtercolumn != 999)
	{
		# ONLY INSERT POSTS TO CATEGORYS WHERE VAL HAS BEEN PAIRD TO A CATEGORY ELSE IT ENDS UP IN THE WORDPRESS DEFAULT CATEGORY
		$val = $categoryname1;// unique value from csv column
	
		$catid = $wpdb->get_var("SELECT catid FROM " .$wpdb->prefix . "spdfi_categories WHERE camid = '$cam->id' AND uniquevalue = '$val'");
		
	}//  $cam->filtermethodmay = NA however this requires nothing to be done and posts will end up in default category

	if(isset($catid)){$catidarray['0'] = $catid;}
}

?>