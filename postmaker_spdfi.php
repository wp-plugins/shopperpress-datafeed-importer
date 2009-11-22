<?php
global $wpdb;	

$debugmode = get_option('spdfi_debugmode');
if($debugmode == 1)
{
	$wpdb->show_errors();
	ini_set('display_errors',1);
	error_reporting(E_ALL);
}

// get wordpress taxonomy functions
require_once ABSPATH . '/wp-admin/includes/taxonomy.php';

// select a campaign and process
$cam = $wpdb->get_row("SELECT * FROM " .$wpdb->prefix . "spdfi_campaigns WHERE stage = '100'");

// set processing time limits and post create limit
$server_safemode = ini_get('safe_mode');
$wordpress_maxexecutiontime = ini_get( "max_execution_time");// use at end of script to reset to default
$wordpress_maxtimelimit = ini_get( "set_time_limit");// use at end of script to reset to default
if($cam->process == 1)// full processing
{
	if( $server_safemode == 0 )
	{
		 set_time_limit(99999);
		 ini_set('max_execution_time', 99999);
	}
	$postlimit_event = 999999;
}
elseif($cam->process == 2)// staggered processing
{
	if( $server_safemode == 0 )
	{		
		set_time_limit(get_option('spdfi_maxstagtime'));
		ini_set('max_execution_time', get_option('spdfi_maxstagtime'));
	}
	$postlimit_event = get_option('spdfi_postsperhit');
}
elseif($cam->process == 3)// cron scheduled processing
{
	if( $server_safemode == 0 )
	{		
		set_time_limit(get_option('spdfi_maxstagtime'));
		ini_set('max_execution_time', get_option('spdfi_maxstagtime'));
	}
	$postlimit_event =  1;
}
	
// include custom post layout php
spdfi_custompostlayout_postmaker($cam->layoutfile,$cam->camname,$cam->id);

// make directory to csv files
$csvfilepath = spdfi_getcsvfilesdir() . $cam->camfile;

$delimiter = determine_delimiter_wtg($cam->camfile);

// open csv file
$handle = fopen("$csvfilepath", "r");
		
if($handle == false)
{
	# FILE FAILED TO BE FOUND OR OPEN - ERROR REPORTING WILL BE PUT HERE EVENTUALLY
}
else
{	
	// initialise variables
	$rowsused_event = 0;// counts rows used to create or update posts, phase will determine what the score applies to in database counters
	$rowsdropped_event = 0;// added to rowsused_event to determine any difference between the main process counter
	$rowsprocessed_event = 0;// rows processed on this event - used to detect select limit and count when end of file is reached
	$custom_count = 0;
	$default_count = 0;

	// get wordpress stored options
	$autokeywords = get_option('spdfi_autokeywords');				
	$autodescription = get_option('spdfi_autodescription');
	$autotags = get_option('spdfi_autotags');
	$authorid = get_option('spdfi_publisherid');
	$catparent = get_option('spdfi_defaultcatparent');
	$posttype = get_option('spdfi_defaultposttype');
	$postping = get_option('spdfi_defaultping');
	$commentstatus = get_option('spdfi_defaultcomment');
	  		
	// begin processing csv file rows - stop when we have created or updated the limit number of posts/pages
	while (($csvrow = fgetcsv($handle, 9999, $delimiter)) !== FALSE && $rowsused_event != $postlimit_event)
	{  
		if( $cam->phase == 1)
		{
			// total rows created and dropped previously and rows created plus dropped on this event to determine position in file
			$passedrows_event = $cam->droppedrows + $rowsdropped_event + $rowsused_event + $cam->posts ;
		}
		elseif( $cam->phase == 2 )
		{
			// total rows created and dropped previously and rows created plus dropped on this event to determine position in file
			$passedrows_event = $cam->droppedrows + $rowsdropped_event + $rowsused_event + $cam->updatedposts ;
		}
		
		// avoid processing first row - also skip full processing on previously used rows by comparing against main process counter
		//if($rowsprocessed_event != 0 && $rowsprocessed_event >= $cam->processcounter)  OLD METHOD 
		if($rowsprocessed_event != 0 && $rowsprocessed_event >= $passedrows_event)  // NEW METHOD - Should be more accurate 
		{
			// each column is processed and its exact use in the campain decided, then applied
			$column_counter_getdata = 0;
			
			// Set manually selected columns data - start with special functions by looping through each column and counting
			foreach($csvrow as $data)
			{ 	
				$data = rtrim($data);	

				// put required data for category creation and filtering into session				
				if($cam->filtercolumn != 999)
				{ 
					if($column_counter_getdata == $cam->filtercolumn)
					{
						$_SESSION['spdfi_category1'] = $data;
					}
					elseif($column_counter_getdata == $cam->filtercolumn2)
					{
						$_SESSION['spdfi_category2'] = $data;
					}
					elseif($column_counter_getdata == $cam->filtercolumn3)
					{
						$_SESSION['spdfi_category3'] = $data;
					}
				}
				
				if($column_counter_getdata == $cam->keywords_col && $cam->keywords_col != 999 && $autokeywords != 999)
				{ 
					$metakeywords = create_meta_keywords_spdfi($data, 150);
				}
					
				if($column_counter_getdata == $cam->excerpt_col && $cam->excerpt_col != 999 && $autodescription != 999)
				{
					$metadescription = create_meta_description_spdfi($data, 150);
				}
		
				if($column_counter_getdata == $cam->tags_col && $cam->tags_col != 999 && $autotags != 999)
				{	
					$tagsnumeric = get_option('spdfi_numerictags');
					$tags = create_tags_spdfi($data, get_option('spdfi_tagslength'), $tagsnumeric);
				}
				
				if($column_counter_getdata == $cam->uniquecolumn && $cam->uniquecolumn != 999)
				{	
					$uniquecode_spdfi = $data;
				}

				if($column_counter_getdata == $cam->primaryurlcloak && $cam->primaryurlcloak != 999)
				{
					$_SESSION['customurlcloaking'] = $cam->primaryurlcloak;
				}

				// continue with none special functions
				if($column_counter_getdata == $cam->price_col && $cam->price_col != 999)
				{	
					$price_col = $data;
				}
				
				if($column_counter_getdata == $cam->oldprice_col && $cam->oldprice_col != 999)
				{	
					$oldprice_col = $data;
				}
								
				if($column_counter_getdata == $cam->image_col && $cam->image_col != 999)
				{	
					$image_col = $data;
				}
				
				if($column_counter_getdata == $cam->images_col && $cam->images_col != 999)
				{	
					$images_col = $data;
				}
				
				if($column_counter_getdata == $cam->thumbnail_col && $cam->thumbnail_col != 999)
				{	
					$thumbnail_col = $data;
				}
				
				if($column_counter_getdata == $cam->shipping_col && $cam->shipping_col != 999)
				{	
					$shipping_col = $data;
				}
				
				if($column_counter_getdata == $cam->featured_col && $cam->featured_col != 999)
				{	
					$featured_col = $data;
				}
				
				if($column_counter_getdata == $cam->excerpt_col && $cam->excerpt_col != 999)
				{	
					$excerpt_col = $data;
				}
				
				if($column_counter_getdata == $cam->keywords_col && $cam->keywords_col != 999)
				{	
					$keywords_col = $data;
				}
				
				if($column_counter_getdata == $cam->tags_col && $cam->tags_col != 999)
				{	
					$tags_col = $data;
				}
				
				if($column_counter_getdata == $cam->customlist1_col && $cam->customlist1_col != 999)
				{	
					$customlist1_col = $data;
				}
				
				if($column_counter_getdata == $cam->customlist2_col && $cam->customlist2_col != 999)
				{	
					$customlist2_col = $data;
				}
				
				if($column_counter_getdata == $cam->uniquecolumn && $cam->uniquecolumn != 999)
				{	
					$uniquecolumn_col = $data;
				}
								
				// find out what part of the post a column is to be applied to and set in session - this supports the WYSIWYG editor
				$postpart = spdfi_postpart_postmaker($column_counter_getdata,$cam->id);
				$code = '$_SESSION["'.$postpart.'"] = $data;';	eval($code);
								
				$column_counter_getdata++;
			}// end of foreach				
				
			// check for duplicates agains post name if customised - users can make the post url different from post title
			$custom_count = spdfi_duplicatecustomurl_postmaker($cam->type,$cam->phase);
			
			// puts title straight into $_SESSION['title'], will overwrite any default title column data
			postparttitle_layoutfile_spdfi();
			
			// use wordpress sanitization for post title
			$default_postname = spdfi_sanitizetitle_postmaker($cam->type);

			// check for duplicates against default post name made from post title
			$default_count = spdfi_duplicateposttitle_postmaker($cam->type,$cam->phase,$default_postname);

			// reset duplicat counters to zero if this is an update phase
			if( $default_count != 0 || $custom_count != 0 )
			{
				$rowsdropped_event++;			
			}
			elseif( $default_count == 0 && $custom_count == 0 )
			{				
				# GET REQUIRED POST CONTENT LAYOUT AND STYLING
				$post = post_content_layoutfile_spdfi();// function in layout file
				
				$post = manipulate_values_layoutfile_spdfi($post,$cam->id);// make final specific changes to data to suit specific needs
				
				// get cat ID for post to be put under
				require('postmakercats_spdfi.php');	

				# CREATE POST OBJECT AND INSERT TO DATABASE
				$my_post = array();//create array
				$my_post['post_title'] = $_SESSION['posttitle'];
				$my_post['post_content'] = $post; 
				$my_post['post_status'] = $cam->poststatus;
				$my_post['post_author'] = $authorid;
				$my_post['post_type'] = $posttype;
				$my_post['to_ping'] = $posttype;
				$my_post['comment_status'] = $commentstatus;							
				// if $catid is set then user wants categorising - use the populated array - else use default
				if(!empty($catid)){$my_post['post_category'] = $catidarray;}elseif(empty($catid)){$my_post['post_category'] = array(1);}
				if(!empty($excerpt_col) && $autodescription != 0){$my_post['post_excerpt'] = $excerpt_col;}else{/* PUT ERROR REPORTING HERE */}
				if(!empty($metakeywords) && $autokeywords != 0){$my_post['tags_input'] = $tags;}
				
				// apply random date if requested, if not apply data set date if requested else date will be default
				if($cam->randomdate == 1)
				{	
					$time = rand( strtotime("Jan 01 2005"), strtotime("Oct 31 2009") );
					$my_post['post_date'] = date("m-d-Y", $time);
				}
				elseif(!empty($_SESSION['publishdate'])){$my_post['post_date'] = $_SESSION['publishdate'];}
				
				// avoid entering new post if matching title values found
				if( $cam->type == 1)
				{
					# DO LOCAL HOST VERSION HERE
				}
				else
				{
					$duplicatecount = 0;
					$wpdb->query("SELECT post_title FROM " .$wpdb->prefix . "posts WHERE post_name = '$default_postname'");
					$duplicatecount = $wpdb->num_rows;					
				}
				
				if( $duplicatecount >= 1 && $cam->phase == 1)
				{
					$rowsdropped_event++;
				}
				elseif( $duplicatecount == 0 ||  $cam->phase == 2)
				{
					if( $cam->type == 1)
					{
						# DO LOCAL HOST VERSION HERE
					}
					else
					{				
						if($cam->allowupdate == 1 && $cam->phase == 2)// update phase
						{ 
							// unique code is used to find existing meta to then find posts id that the data belongs to in the blog
							if( !isset( $uniquecode_spdfi ) || empty( $uniquecode_spdfi ) )
							{ 
								error_spdfi(__LINE__,__FILE__,'Update cannot be perfermed, no unique value column provided! Cam Name: ' . $cam->camname .' Cam ID: '. $cam->id .'');
							}
							else
							{ 
								// find meta record with unique code and get id
								$r = $wpdb->get_row("SELECT post_id FROM " .$wpdb->prefix . "postmeta WHERE meta_value = '$uniquecode_spdfi'");
								
								// ads existing post ID to the new post array
								$my_post['ID'] = $r->post_id; 
								// update post
								$post_id = wp_update_post( $my_post ); 
								$rowsused_event++;							
							}
						}
						elseif($cam->allowupdate == 0 || $cam->phase == 1)// insert phase
						{ 
							$post_id = wp_insert_post( $my_post ); 
							$rowsused_event++;
						}
					
						// does user require a url to be cloaked within the content - PRIMARY cloak
						if(!empty($_SESSION['customurlcloaking']) && $_SESSION['customurlcloaking'] != 999)
						{
							$old_url_cloaking = $_SESSION['customurlcloaking'];
							$new_url_cloaking = bloginfo( 'url' ) . '?viewitem=' . $post_id;
							$post = str_replace($old_url, $new_url, $post);// replace data url with cloaking url
							
							$cloakupdate_post = array();
							$my_post['post_id'] = $post_id;// add post id for updating
							$my_post['post_content'] = $post;// will update post content with new edition with cloaking
							
							$post_id = wp_update_post( $cloakupdate_post ); 
						} 
						
						// has a 2nd cloaked url been selected on stage 2 - SECONDARY cloak
						if(!empty($_SESSION['customurlcloaking2']) && $_SESSION['customurlcloaking2'] != 999)
						{
							$old_url_cloaking2 = $_SESSION['customurlcloaking2'];
							$new_url_cloaking2 = bloginfo( 'url' ) . '?viewitem=' . $post_id;
							$post = str_replace($old_url, $new_url, $post);// replace data url with cloaking url
							
							$cloakupdate_post = array();
							$my_post['post_id'] = $post_id;// add post id for updating
							$my_post['post_content'] = $post;// will update post content with new edition with cloaking
							
							$post_id = wp_update_post( $cloakupdate_post ); 
						}
						
						// record new post id in spdfi_posthistory table
						if( isset( $post_id) ){ spdfi_recordnewid_postmaker($cam->id,$post_id); }
					}
					
					// enter custom fields - if no post id then theres nothing further that can be done
					if( isset( $post_id ) )
					{ 					
						if($cam->process == 2 && isset($handle2)){fclose($handle2);}// full file processing
			
						// add custom field for posts keywords
						spdfi_metakeywords_postmaker($cam->type,$post_id,$metakeywords);
											
						// enter manually selected custom fields - mainly ShopperPress custom fields
						if(!empty($_SESSION['customurlcloaking']) )
						{
							add_post_meta($post_id, 'spdfiplus_cloakedurl', $old_url_cloaking, true);
						}	
						
						if( !empty($_SESSION['customurlcloaking2']) )
						{
							add_post_meta($post_id, 'spdfiplus_cloakedurl2', $old_url_cloaking, true);
						}	

						if( !empty($price_col) )
						{
							add_post_meta($post_id, 'price', $price_col, true);
						}	

						if( !empty($oldprice_col) )
						{
							add_post_meta($post_id, 'oldprice', $oldprice_col, true);
						}	

						if( !empty($image_col) )
						{
							add_post_meta($post_id, 'image', $image_col, true);
						}	

						if( !empty($images_col) )
						{
							add_post_meta($post_id, 'images', $images_col, true);
						}	

						if( !empty($thumbnail_col) )
						{
							add_post_meta($post_id, 'thumbnail', $thumbnail_col, true);
						}	

						if( !empty($shipping_col) )
						{
							add_post_meta($post_id, 'shipping', $shipping_col, true);
						}	

						if( !empty($featured_col) )
						{
							add_post_meta($post_id, 'featured', $featured_col, true);
						}	

						if( !empty($excerpt_col) )
						{
							add_post_meta($post_id, 'excerpt', $excerpt_col, true);
						}	

						if( !empty($customlist1_col) )
						{
							add_post_meta($post_id, 'customlist1', $customlist1_col, true);
						}	

						if( !empty($customlist2_col) )
						{
							add_post_meta($post_id, 'customlist2', $customlist2_col, true);
						}	
			
						// add custom field for product quantity
						add_post_meta($post_id, 'qty', get_option('spdfi_itemqty'), true);
						
						// add custom field for unique identity code (sky, product number etc)
						spdfi_customfielduniquecode_postmaker($cam->type,$post_id);					
					}

					// unset all reused variables
					unset($post); unset($link); unset($img); unset($text);
					unset($_SESSION['title']); unset($buyurl); unset($publisher);
					unset($contact); unset($currency); unset($price);
					unset($advertiser); unset($imageurl);
					unset($category); unset($author); 
					unset($cat1);unset($cat2);unset($cat3);
					unset($catid);unset($catid2);unset($catid3);
					unset($_SESSION['spdfi_category1']);
					unset($_SESSION['spdfi_category2']);
					unset($_SESSION['spdfi_category3']);
				}// end of second duplication check
			}
		}// end if first row or not
		
		$rowsprocessed_event++;// used to indicate if first row (0) or not
	}// end fgetcsv while loop

	// update campaign counters
	spdfi_updatecounters_postmaker($cam->id,$rowsprocessed_event,$rowsused_event,$rowsdropped_event);					

	fclose($handle);// close csv file
	if($cam->process == 1 && isset($handle2)){fclose($handle2);}// full file processing
	
	$wpdb->flush();// clear sql cache

	update_option('spdfi_currentprocess',0);// acts as a switch to now allow another processing event to take place
}

$debugmode = get_option('spdfi_debugmode');
if($debugmode == 1){$wpdb->hide_errors();}

set_time_limit($wordpress_maxtimelimit);
ini_set('max_execution_time', $wordpress_maxexecutiontime);

?>