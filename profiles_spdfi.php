<div class="wrap">
  <h2>ShopperPress DataFeed Importer CSV File Profile</h2>
  
    <div id="poststuff" class="meta-box-sortables" style="position: relative; margin-top:10px;">
        
    <?php
    global $wpdb;
    
    $wysiwyg_finished = true;
        
    // prepare opendir for listing layout files
    $php_extension = 'php';
    $csv_extension = 'csv';
    
    $csvfiles_dir = spdfi_getcsvfilesdir();
    $csvfiles_diropen = opendir($csvfiles_dir);
    	
	// function used for special column function submission and to determine the first set state
    function spdfi_decidestate($v){  if( $v == 'NA' ){ return 'OFF'; }elseif( $v != 'NA' ){ return 'ON'; } }

    function process_wysiwyg()// processes a submission - update or new insert
    {
        global $wpdb;
    
        $post_content_original = $_POST['content'];
        $post_title_original   = $_POST['post_title'];
        $filename = $_POST['csvfilename'];
        $post_content = str_replace('<br>', '<br />', $post_content_original);
        $post_content = str_replace('<hr>', '<hr />', $post_content);
        $post_content = $wpdb->escape($post_content);
        
		// first save the delimiter
		$csvprofile = spdfi_getcsvprofile( $filename );
		$csvprofile['format'] = array(
			'delimiter' => $_POST['delimiter'],
		);
		update_option( spdfi_csvfilesprofilename($filename), $csvprofile );
		
        $file = spdfi_getcsvfilesdir() . $filename;
        $handle = fopen("$file", "r");
                
        $num = 0;
    
        //build post_parts_layoutfile_spdfiplus() function 
        $cpl_function1 = '
        function post_parts_layoutfile_spdfi($i){?>
            <select name="posttypes<?php echo $i; ?>" size="1">
                <!--default options do not edit -->
                <option value="exclude" selected="selected">EXCLUDE</option><!-- csv columns not used -->
                <option value="customurlcloaking2">Cloak URL 2nd</option><!-- If default post date not wanted use data -->
                <!-- default options do not edit -->
        ';
        
        $post_title = $post_title_original;
            
        $stop_rows = 0;
        
        while (($record = fgetcsv($handle, 5000, $csvprofile['format']['delimiter'])) !== FALSE && $stop_rows != 1)// get first csv row
        {
            $stop_rows++;// used to limit row parsing to just 1
            foreach($record as $postpart) 
            {
                $special_val = '%-' . $postpart . '-%';// value to locate
                $new_session = '@$_SESSION["' . $postpart . '"]';// new value (session variable)
                $new_val = "'.".$new_session.".'";
                $cpl_function1 .= '<option value="' . $postpart . '">' . $postpart . '</option>';
                $post_content = str_replace($special_val, $new_val, $post_content);
                $post_title = str_replace($special_val, $new_val, $post_title);
            }	
        }//end while rows
    
    
        $cpl_function1 .= '</select><?php } ';
        
        // build function post_content_layoutfile_spdfiplus() from cpl
        $cpl_function2 = 'function post_content_layoutfile_spdfi(){$';
        $cpl_function2 .= "post = '";
        $cpl_function2 .= $post_content;// content from WYSIWYG
        $cpl_function2 .= "'; return $";
        $cpl_function2 .= 'post;}';
        
        // build function manipulate_values_layoutfile_spdfiplus()
        $cpl_function3 = 'function manipulate_values_layoutfile_spdfi($post){';
        $cpl_function3 .= ' return $';
        $cpl_function3 .= 'post;}';
        
        $cpl_function4 = '
            function postparttitle_layoutfile_spdfi()
            {
                @$';
        $cpl_function4 .= '_SESSION["posttitle"] =  ';
        $cpl_function4 .= "'". $post_title . "'";
        $cpl_function4 .= ';}';	
        
        $code = $cpl_function1 . $cpl_function2 . $cpl_function3 . $cpl_function4;// puts all cpl parts together
        
        // prepare for database entry
        $code = mysql_real_escape_string( $code );
        $post_content = mysql_real_escape_string( $post_content );
        $post_title = mysql_real_escape_string( $post_title );
        $post_content_original = mysql_real_escape_string( $post_content_original );
        $post_title_original = mysql_real_escape_string( $post_title_original );
    
		if($_POST['action'] == 'process_wysiwygupdate')
        {
            $sqlQuery = "UPDATE " .	$wpdb->prefix . "spdfi_layouts 
            SET 
            code = '$code',
            wysiwyg_content = '$post_content_original',
            wysiwyg_title = '$post_title_original'
            WHERE csvfile = '$filename'
			AND name = '$filename'";
            
            $r = $wpdb->query( $sqlQuery );
            if( !$r )
            {
				echo '<div id="message" class="updated fade"><p>CSV Profile Not Saved - Possibly because there were no changes made!</p></div>';
            }
            else
            {			
				echo '<div id="message" class="updated fade"><p>Success - Your Custom Post Layout has been updated and it will also take effect on running campaigns!</p><p>Please remember to complete your entire CSV Profile by pairing your columns to the correct custom fields and any special functions you wish to use!</div>';
            }
            $wysiwyg_finished = true;
        }
        elseif($_POST['action'] == 'process_columnupdate')
        {
			$csvprofile = csv2post_getcsvprofile( $filename );

			// create array of special column values
			$csvprofile['columns'] = array(
				'price_column' => $_POST['price_column'],
				'old_price_column' => $_POST['old_price_column'],
				'image_column' => $_POST['image_column'],
				'images_column' => $_POST['images_column'],
				'thumbnail_column' => $_POST['thumbnail_column'],
				'qty_column' => $_POST['qty_column'],
				'customlist1_column' => $_POST['customlist1_column'],
				'customlist2_column' => $_POST['customlist2_column'],
				'shipping_column' => $_POST['shipping_column'],
				'featured_column' => $_POST['featured_column'],
				'excerpt_column' => $_POST['excerpt_column'],
				'tags_column' => $_POST['tags_column'],
				'uniqueid_column' => $_POST['uniqueid_column'],
				'urlcloaking_column' => $_POST['urlcloaking_column'],
				'permalink_column' => $_POST['permalinkc_olumn'],
				'dates_column' => $_POST['dates_column']
			);
						
			// the state is a boolean switch which will be used to switch the special function on or off per campaign on stage 2
			$csvprofile['states'] = array(
				'price_state' => spdfi_decidestate($_POST['price_column']),
				'old_price_state' => spdfi_decidestate($_POST['old_price_column']),
				'image_state' => spdfi_decidestate($_POST['image_column']),
				'images_state' => spdfi_decidestate($_POST['images_column']),
				'thumbnail_state' => spdfi_decidestate($_POST['thumbnail_column']),
				'qty_state' => spdfi_decidestate($_POST['qty_column']),
				'customlist1_state' => spdfi_decidestate($_POST['customlist1_column']),
				'customlist2_state' => spdfi_decidestate($_POST['customlist2_column']),
				'shipping_state' => spdfi_decidestate($_POST['shipping_column']),
				'featured_state' => spdfi_decidestate($_POST['featured_column']),
				'excerpt_state' => spdfi_decidestate($_POST['excerpt_column']),
				'tags_state' => spdfi_decidestate($_POST['tags_column']),
				'uniqueid_state' => spdfi_decidestate($_POST['uniqueid_column']),
				'urlcloaking_state' => spdfi_decidestate($_POST['urlcloaking_column']),
				'permalink_state' => spdfi_decidestate($_POST['permalink_column']),
				'dates_state' => spdfi_decidestate($_POST['dates_column'])
			);
			
			update_option( spdfi_csvfilesprofilename($filename), $csvprofile );
		}
    }// end of function  		
    
 
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
    {
        ini_set('include_path',rtrim(ini_get('include_path'),';').';'.dirname(__FILE__).'/pear/');
    } 
    else 
    {
        ini_set('include_path',rtrim(ini_get('include_path'),':').':'.dirname(__FILE__).'/pear/');
    }
    
    require_once 'File/CSV.php';// PEAR csv file handling
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
        $_POST = stripslashes_deep($_POST);
		
		$filename = $_POST['csvfilename'];
        $cpl_id = $_POST['cpl_id'];
		$post_title   = $_POST['post_title'];
        $post_content = $_POST['content'];		
		
        $csvprofile = spdfi_getcsvprofile( $filename );
		$delimiter = $csvprofile['format']['delimiter']; 

        if($_POST['action'] == 'opencsvfile') 
        {
            $full_filename = spdfi_getcsvfilesdir() . $filename;
    
            if($_POST['submitbutton'] == 'Delete')
            {			
                if(!$filename)
                {
					echo '<div id="message" class="updated fade"><p>Sorry could not delete, filename not submitted</p></div>';
                }
                else
                {
                    $user_count = $wpdb->get_var("SELECT COUNT(*) FROM " .$wpdb->prefix . "spdfi_campaigns WHERE camfile = '$filename'");
                    
                    if($user_count >= 1)
                    {
						echo '<div id="message" class="updated fade"><p>Cannot delete, currently in use! Please delete the campaign using it then try again.</p></div>';
                    }
                    else
                    {		
                        $delete =  @unlink($full_filename);
                        
                        if(!$delete)
                        {
							echo '<div id="message" class="updated fade"><p>Sorry there was a problem deleting your CSV file</p></div>';
                        }
                        elseif($delete)
                        {
							echo '<div id="message" class="updated fade"><p>CSV file deleted successfully: ' . $filename . '</p></div>';
                        }
                    }
                }
                $wysiwyg_finished = true;
            }
            else
            {
                $csvfilehandle = fopen("$full_filename", "r");
    
                $wysiwyg_finished = false;
            }
        }
        elseif($_POST['action'] == 'process_insert')// insert new record
        {		
            $update_or_insert = 'insert';
            
            list($notice, $message) = process_wysiwyg($update_or_insert);
    
            $wysiwyg_finished = true;
        }
        elseif($_POST['action'] == 'process_wysiwygupdate')// update existing record
        {                         
            $update_or_insert = 'wysiwygupdate';
            
            list($notice, $message) = process_wysiwyg($update_or_insert);
            
            $wysiwyg_finished = true;
        }
        elseif($_POST['action'] == 'process_columnupdate')// update existing record
        {                          
            $update_or_insert = 'columnupdate';
            
            list($notice, $message) = process_wysiwyg($update_or_insert);
            
            $wysiwyg_finished = true;
        }
        elseif($_POST['action'] == 'opencustompostlayout')// open exisitng record for editing
        {
            $cpl_id = $_POST['custompostlayout_id'];
            
            if($_POST['submitbutton'] == 'Delete')
            {
                // ensure layout not in use before deleting
                $res1 = $wpdb->get_row("SELECT * FROM " .$wpdb->prefix . "spdfi_layouts WHERE id = '$cpl_id'");
                $filename = $res1->csvfile;
    
				$csvprofile = spdfi_getcsvprofile( $filename );
				$delimiter = $csvprofile['format']['delimiter'];
    
                $user_count = $wpdb->get_var("SELECT COUNT(*) FROM " .$wpdb->prefix . "spdfi_campaigns WHERE camfile = '$usedfilename'");
                
                if($user_count >= 1)
                {
					echo '<div id="message" class="updated fade"><p>Cannot delete, currently in use! Please delete the campaign on the campaign management page then try again.</p></div>';
                }
                else
                {
					// delete wp option holding array of special column information for this file
					$optionname = 'csvprofile_' . $filename;
					delete_option( $optionname );
                    $res3 = $wpdb->get_row("DELETE FROM " .$wpdb->prefix . "spdfi_layouts WHERE id = '$cpl_id'");
					echo '<div id="message" class="updated fade"><p>Custom Post Layout deleted successfully</p></div>';
                }
                
                $wysiwyg_finished = true;		
            }
            else
            {
                $res1 = $wpdb->get_row("SELECT * FROM " .$wpdb->prefix . "spdfi_layouts WHERE id = '$cpl_id'");
                $post_title = $res1->wysiwyg_title;
                $post_content = $res1->wysiwyg_content;
                $filename = $res1->csvfile;
                $inuse = $res1->inuse;
                $spdfi_cpl_name = $res1->name;
                
				$csvprofile = spdfi_getcsvprofile( $filename );
				$delimiter = $csvprofile['format']['delimiter'];
                
                $full_filename = spdfi_getcsvfilesdir() . $filename;
                
                # OPEN CSV FILE
                $csvfilehandle = fopen("$full_filename", "r");
    
                $wysiwyg_finished = false;
            }
        }
    } 
        
    if($wysiwyg_finished == true)
    {
    ?>

         <form method="post">
            <input type="hidden" name="action" value="opencsvfile" />
                <div id="poststuff" class="metabox-holder">
                    <div id="datafeed-upload" class="postbox">
                        <h3 class='hndle'><span>Create New CSV File Profile</span></h3>
                        <div class="inside" style="padding:20px;">
                        <p>This box will list any csv files that have not yet had a profile completed. Select a new csv file and submit it to begin creating its profile</p>
                        <br />
                            <?php
                            $i = 0;
							while(false != ($csvfiles = readdir($csvfiles_diropen)))
							{
								if(($csvfiles != ".") and ($csvfiles != ".."))
								{
									$fileChunks = explode(".", $csvfiles);
									
									// check if profile already edited - if so do not display - causes only new csv files without profiles to be listed here
									$results = $wpdb->get_row("SELECT * FROM " .$wpdb->prefix . "spdfi_layouts WHERE csvfile = '$csvfiles' AND wysiwyg_content = 'TBC' AND wysiwyg_title = 'TBC' ");
									if( !empty ( $results ) )
									{
										if($fileChunks[1] == $csv_extension) //interested in second chunk only
										{
											++$i;
											$csvprofile = spdfi_getcsvprofile( $csvfiles );
											$delimiter = $csvprofile['format']['delimiter'];
											?>
											<label>
											<input type="radio" name="csvfilename" value="<?php echo $csvfiles;?>" <?php if(empty($delimiter)){echo 'disabled="disabled"';}?> /><?php echo $csvfiles; ?> 
											<?php if(empty($delimiter)){echo 'Enter Delimiter On CSV Uploader Page To Use';}?>
											</label>
											<br /><?php
										}
									}
								}
							}
                              
                            if( $i == 0 ){ echo '<h4>Either You Have No CSV Files Uploaded Or They All Have A Complete Profile Which You Can Edit Below</h4>';}
                            
                            closedir($csvfiles_diropen); 
                            ?>
                            <p class="submit"><input class="button-primary" type="submit" name="submitbutton" value="Submit" /></p>
                        </div>
                    </div>
                </div>
          </form>
            
            <form method="post">
                <input type="hidden" name="action" value="opencustompostlayout" />
                <div id="poststuff" class="metabox-holder">
                    <div id="datafeed-upload" class="postbox">
                        <h3 class='hndle'><span>Edit Profiles</span></h3>
                        <div class="inside" style="padding:20px;">
                            <?php
                                // list existing custom post layouts from database
                                $res1 = $wpdb->get_results("SELECT * FROM " .$wpdb->prefix . "spdfi_layouts WHERE type = '0' AND wysiwyg_content != 'TBC' AND wysiwyg_title != 'TBC'");
                                
                                if(! $res1 )
                                { 
                                    echo '<h4>You Have Not Created Any CSV File Profiles Yet</h4>';
                                }
                                else
                                {
                                    foreach($res1 as $x)
                                    {   
                                        ?>
                                        <label><input type="radio" name="custompostlayout_id" value="<?php echo $x->id;?>" /><?php echo $x->name;?></label><br />
                                        <?php
                                    }
                                }
                            ?>
                            <p class="submit"><input class="button-primary" type="submit" name="submitbutton" value="Submit" /></p>
                        </div>
                    </div>
                </div>
            </form>
                    
        <?php        
        return;
    }
    elseif($wysiwyg_finished == false){?>
    
    <form method="post">
    
        <input type="hidden" name="action" value="process_wysiwygupdate" />
    
            <?php
				// get delimiter for current csv file			
				$csvprofile = spdfi_getcsvprofile( $filename );
				$delimiter = $csvprofile['format']['delimiter'];

				if($_POST['action'] == 'opencustompostlayout')// if cpl is in use display warning
				{
					if(isset($inuse) && $inuse == 1)
					{
    					?><h3>WARNING: <?php echo $filename; ?> is currently in use by an active campaign!</h3><?php
					}
				}
			?>
            
            <h2>Custom Post Layout for <?php echo $filename; ?></h2>                                         
			<p>Please begin by creating your Custom Post Layout using the Wordpress WYSIWYG editor which you can find by clicking below. You will find a save button for the editor
            just below the editor itself. You can use the same column of data multiple times especially for ShopperPress picture requirements.</p>

		<div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br />
            </div>
             <h3>Custom Post Layout &amp; Delimiter</h3>
                          
                <div class="inside">
                            
                <h4><a href="#" title="Special column title values, copy and paste these into the WYSIWYG editor (including title) as they represent where your data will appear in posts">Column Name Tokens</a></h4>
           
					<?php		
                    $stop_rows = 0;
                    
                    while (($data = fgetcsv($csvfilehandle, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                           
                               $i = 0;
                         
                                while(isset($data[$i]))
                                {
                                    $data[$i] = rtrim($data[$i]);
                                    echo '<b>%-' . $data[$i] . '-%</b><br /><br />';
                                    $i++; // $i will equal number of columns - use to process submission
                                }
                                
                                $csvfile_columntotal = $i;
                    
                    }//end while rows
                    
                    fclose($csvfilehandle);	
                    ?>        
                             
                    <h4><a href="#" title="If the column title tokens above are not listed properly (all on one line) then please change your delimiter to the correct value here then save before doing anything else.">CSV File Delimiter</a></h4>
                    <input type="text" name="delimiter" size="1" maxlength="1" value="<?php echo $delimiter; ?>" id="delimiter" />
                                        
                     <div id="titlediv">
                        <div id="titlewrap">
                        	<h4><a href="#" title="You can enter special values listed above into this title">Post Title</a></h4>
                          <input type="text" name="post_title" size="30" value="<?php if(isset($post_title)){echo attribute_escape($post_title);} ?>" id="title" />
                       </div>
                    </div>     
                    
                    
                     <div id="postdivrich" class="postarea">
                            <?php		
                            if(isset($post_content))
                            {
                                the_editor($post_content, 'content'); 
                            }
                            else
                            {
                                the_editor('', 'content'); 
                            }
                            ?>
                        <div id="post-status-info">
                            <span id="wp-word-count" class="alignleft"></span>
                        </div>
                    </div> 
                       
                    <input name="csvfilename" type="hidden" value="<?php echo $filename;?>" />
                    <input name="cpl_id" type="hidden" value="<?php if(isset($cpl_id)){echo $cpl_id;}?>" />
                    <input class="button-primary" type="submit" value="Save Post Layout" />
  			</div>
        </div>    
    </form>
    
    
    <br />
    
    
    <!-- Special column values begin here and are saved on a seperate form form WYSIWYG editor because the editor always requires a change in order to update -->
    <h2>Special Functions &amp; Custom Field Pairing for <?php echo $filename; ?></h2>
    <p>Below you can pair your csv file columns to the correct custom fields and activate special functions in this plugin before importing data. Please remember to click save at the very bottom of the page.</p>
    <form method="post">
        <input type="hidden" name="action" value="<?php if($_POST['action'] == 'opencsvfile'){echo 'process_insert';}elseif($_POST['action'] == 'opencustompostlayout'){echo 'process_columnupdate';}?>" />
                   
        <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Price Column (price)</h3>
            <div class="inside">
                <p>Select the column that holds your current offer price.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="price_column" value="NA" <?php if( $csvprofile['columns']['price_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php			
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
					{	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i])){
                            $data[$i] = rtrim($data[$i]);?>
                            
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="price_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['price_column'] != 'NA' && $csvprofileoption['columns']['price_column'] == $i ){ echo 'checked="checked"'; } ?>  /></td>
                                </tr>
                                
						<?php $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
     
        <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Old Price Column (old_price)</h3>
            <div class="inside">
                <p>If your data has the old price for your products select that column here.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="old_price_column" value="NA" <?php if( $csvprofile['columns']['old_price_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="old_price_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['old_price_column'] != 'NA' && $csvprofileoption['columns']['old_price_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
            
        <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Main Image Column (image)</h3>
            <div class="inside">
                <p>Select the column that has url's for your main product image on posts.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="image_column" value="NA" <?php if( $csvprofile['columns']['image_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="image_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['image_column'] != 'NA' && $csvprofileoption['columns']['image_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
            
        <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Multiple Images Column (images)</h3>
            <div class="inside">
                <p>If you have url's prepared for the multiple image display select that column here.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="images_column" value="NA" <?php if( $csvprofile['columns']['images_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="images_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['images_column'] != 'NA' && $csvprofileoption['columns']['images_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
            
    	<!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Thumbnail Column (thumbnail)</h3>
            <div class="inside">
                <p>Select the column that has a url to an image suitable for your thumbnail.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="thumbnail_column" value="NA" <?php if( $csvprofile['columns']['thumbnail_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="thumbnail_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['thumbnail_column'] != 'NA' && $csvprofileoption['columns']['thumbnail_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
        
        <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Quantity Column (qty)</h3>
            <div class="inside">
                <p>Your data may not include stock quantity but if it does please select it here.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="qty_column" value="NA" <?php if( $csvprofile['columns']['qty_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="qty_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['qty_column'] != 'NA' && $csvprofileoption['columns']['qty_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->

        <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Custom List 1 Column (customlist1)</h3>
            <div class="inside">
                <p>If you have data prepared to be the first custom list select it here.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="customlist1_column" value="NA" <?php if( $csvprofile['columns']['customlist1_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="customlist1_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['customlist1_column'] != 'NA' && $csvprofileoption['columns']['customlist1_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
            
        <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Custom List 2 Column (customlist1)</h3>
            <div class="inside">
                <p>If you have data prepared to be the first custom list select it here.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="customlist2_column" value="NA" <?php if( $csvprofile['columns']['customlist2_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="customlist2_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['customlist2_column'] != 'NA' && $csvprofileoption['columns']['customlist2_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
            
        <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Shipping Rate Column (shipping)</h3>
            <div class="inside">
                <p>If you have a column of shipping prices for your products then select it here.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="shipping_column" value="NA" <?php if( $csvprofile['columns']['shipping_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="shipping_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['shipping_column'] != 'NA' && $csvprofileoption['columns']['shipping_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
            
        <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Featured Column (featured)</h3>
            <div class="inside">
                <p>If you have a featured column with image urls in it select it here. You can also select a standard images column to set all products as featured.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="featured_column" value="NA" <?php if( $csvprofile['columns']['featured_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="featured_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['featured_column'] != 'NA' && $csvprofileoption['columns']['featured_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
 
        <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Column For Generating Excerpts</h3>
            <div class="inside">
                <p>Originally intended for creating meta description just like the keywords generator however it is more applicable as an Excerpt when your them requires one. To use please select a column that holds the biggest block of text such as a description.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="excerpt_column" value="NA" <?php if( $csvprofile['columns']['excerpt_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="excerpt_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['excerpt_column'] != 'NA' && $csvprofileoption['columns']['excerpt_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->


        <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Column For Generating Tags</h3>
            <div class="inside">
                <p>Select a column of text if you would like CSV 2 POST to automatically apply tags to posts. There are Tag specific options in the Settings page also.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="tags_column" value="NA" <?php if( $csvprofile['columns']['tags_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="tags_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['tags_column'] != 'NA' && $csvprofileoption['columns']['tags_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
        
        
                    <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Unique ID Column</h3>
            <div class="inside">
                <p>Required if you want to use automatic updating when your csv file data changes but it will also help enforce duplicate post prevention. Please select the column that contains a unique product code or serial number etc.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="uniqueid_column" value="NA" <?php if( $csvprofile['columns']['uniqueid_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="uniqueid_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['uniqueid_column'] != 'NA' && $csvprofileoption['columns']['uniqueid_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
        
        
                    <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select URL Column For Cloaking</h3>
            <div class="inside">
                <p>CSV 2 POST has a cloaking function that will store the real url in a posts custom field and replace the url with a shortened one. Select the column that has url data that you need cloaked.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="urlcloaking_column" value="NA" <?php if( $csvprofile['columns']['urlcloaking_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="urlcloaking_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['urlcloaking_column'] != 'NA' && $csvprofileoption['columns']['urlcloaking_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
        
        
                    <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Column For Custom Post Name/Slug/URL/Permalink</h3>
            <div class="inside">
                <p>This is not used very often and in most cases you will not need to use it. You can use this if you have slugs from an old website which will help you maintain url structure when used. Select the column that holds your old permalinks.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="permalink_column" value="NA" <?php if( $csvprofile['columns']['permalink_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="permalink_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['permalink_column'] != 'NA' && $csvprofileoption['columns']['permalink_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
            </div>
        </div>    
        <!-- Special Item Finish -->
        
        
                    <!-- Special Item Start -->
        <div class="postbox closed">
        <div class="handlediv" title="Click to toggle"><br />
        </div>
         <h3>Select Column For Post Publish Dates</h3>
            <div class="inside">
                <p>If you have a column of dates for your data records you can use them for custom post publish dates. Select your column of dates below.</p>
                <table width="649">
                    <tr>
                        <td width="163">Not Applicable</td>
                        <td width="15"></td>
                        <td><input type="radio" name="dates_column" value="NA" <?php if( $csvprofile['columns']['dates_column'] == 'NA' ){ echo 'checked="checked"'; } ?> /></td>
                    </tr>
                    <?php
                    $i = 0;
                    $stop_rows = 0;
                    $handle2 = fopen("$full_filename", "r");
                    while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
                    {	 
                        $stop_rows++;// used to limit row parsing to just 1
                                                  
                        while(isset($data[$i]))
                        {
                            $data[$i] = rtrim($data[$i]);
                                ?>
                                <tr>
                                    <td width="163"><?php echo $data[$i]; ?></td>
                                    <td width="15"></td>
                                    <td><input type="radio" name="dates_column" value="<?php echo $i;?>"<?php if( $csvprofileoption['columns']['dates_column'] != 'NA' && $csvprofileoption['columns']['dates_column'] == $i ){ echo 'checked="checked"'; } ?> /></td>
                                </tr><?php							
                                $i++; // $i will equal number of columns - use to process submission
                        }
                    
                    }//end while rows
                    ?>
                </table>
                
            </div>
        </div>    
        <!-- Special Item Finish -->
                               
        <input name="csvfilename" type="hidden" value="<?php echo $filename;?>" />
        <input name="cpl_id" type="hidden" value="<?php if(isset($cpl_id)){echo $cpl_id;}?>" />
        <input class="button-primary" type="submit" value="Save Profile" />
    </form>
    </div>
    <?php
    }
    ?>
                    
                
  <script type="text/javascript">
            // <![CDATA[
            jQuery('.postbox div.handlediv').click( function() { jQuery(jQuery(this).parent().get(0)).toggleClass('closed'); } );
            jQuery('.postbox h3').click( function() { jQuery(jQuery(this).parent().get(0)).toggleClass('closed'); } );
            jQuery('.postbox.close-me').each(function(){
            jQuery(this).addClass("closed");
            });
            //-->
        </script>
    
  </div><!-- end of poststuff div id -->

</div>
    
   
