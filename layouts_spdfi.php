<h2>Layout &amp; Styling</h2>

<?php
	
global $wpdb;

$wysiwyg_finished = true;
	
// prepare opendir for listing layout files
$php_extension = 'php';
$csv_extension = 'csv';

$csvfiles_dir = spdfi_getcsvfilesdir();
$csvfiles_diropen = opendir($csvfiles_dir);

function teeny_mce_buttons($array) 
{
	return array_merge(array('forecolor', 'charmap'), $array);
}

function process_wysiwyg()// processes a submission - update or new insert
{
	global $wpdb;

	$post_content_original = $_POST['content'];
	$post_title_original   = $_POST['post_title'];
	$filename = $_POST['csv_on_wysiwyg'];
	$post_content = str_replace('<br>', '<br />', $post_content_original);
	$post_content = str_replace('<hr>', '<hr />', $post_content);
	$post_content = $wpdb->escape($post_content);
	
	$file = spdfi_getcsvfilesdir() . $filename;
	$handle = fopen("$file", "r");
	
	$num = 0;

	//build post_parts_layoutfile_spdfiplus() function 
	$cpl_function1 = '
	function post_parts_layoutfile_spdfi($i){?>
		<select name="posttypes<?php echo $i; ?>" size="1">
			<!--default options do not edit -->
			<option value="exclude" selected="selected">EXCLUDE</option><!-- csv columns not used -->
			<option value="post_url">Post Custom URL (Special)</option><!-- pre-made or custom data for post_name/url -->
			<option value="publishdate">Set Date (Special)</option><!-- If default post date not wanted use data -->
			<option value="uniquecode_spdfi">Unique Code (Special)</option><!-- If default post date not wanted use data -->
			<option value="customurlcloaking2">Cloak URL 2nd</option><!-- If default post date not wanted use data -->
			<!-- default options do not edit -->
	';
	
	$post_title = $post_title_original;

	$delimiter = determine_delimiter_wtg($filename);

	while (($record = fgetcsv($handle, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
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

	if($_POST['action'] == 'process_insert')
	{
		$name = mysql_real_escape_string( $_POST['cpl_name'] );
	
		$sqlQuery = "INSERT INTO " . $wpdb->prefix . "spdfi_layouts (name,code,inuse,type,csvfile,wysiwyg_content,wysiwyg_title) VALUES ('$name','$code',0,0,'$filename','$post_content_original','$post_title_original')";
		$r = $wpdb->query( $sqlQuery );
		if( !$r )
		{
			error_spdfiplus(__LINE__,__FILE__,'Custom Post Layout could not be saved! Name: '. $name .'');
			messagebox_spdfi('errorSmall', 'Failed - Your Custom Post Layout could not be saved!');
		}
		else
		{
			messagebox_spdfi('successSmall', 'Success - Custom Post Layout created you can <a href="admin.php?page=new_campaign_spdfi">Create New Campaign</a>');
		}
		$wysiwyg_finished = true;
	}
	elseif($_POST['action'] == 'process_update')
	{
		$cpl_id = $_POST['cpl_id'];
		
		$sqlQuery = "UPDATE " .	$wpdb->prefix . "spdfi_layouts 
		SET 
		code = '$code',
		wysiwyg_content = '$post_content_original',
		wysiwyg_title = '$post_title_original'
		WHERE id = '$cpl_id'";
		
		$r = $wpdb->query( $sqlQuery );
		if( !$r )
		{
			error_spdfiplus(__LINE__,__FILE__,'Custom Post Layout could not be updated! ID: '. $cpl_id .'');
			messagebox_spdfi('errorSmall', 'Failed - Your Custom Post Layout could not be saved!');
		}
		else
		{			
			messagebox_spdfi('successSmall', 'Success - Your Custom Post Layout has been updated and it will also take effect on running campaigns!');
		}
		$wysiwyg_finished = true;
	}
}  		



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
		
	if($_POST['action'] == 'opencsvfile') 
	{
		$filename = $_POST['csvfilename'];
		
		$delimiter = determine_delimiter_wtg($filename);
		
		$full_filename = spdfi_getcsvfilesdir() . $filename;

		if($_POST['submitbutton'] == 'Delete')
		{			
			if(!$filename)
			{
				messagebox_spdfi('errorSmall', 'Sorry could not delete, filename not submitted');
			}
			else
			{
				$user_count = $wpdb->get_var("SELECT COUNT(*) FROM " .$wpdb->prefix . "spdfi_campaigns WHERE camfile = '$filename'");
				
				if($user_count >= 1)
				{
					messagebox_spdfi('warningSmall', 'Cannot delete, currently in use! Please delete the campaign using it then try again.');
				}
				else
				{
					$delete =  @unlink($full_filename);
					
					if(!$delete)
					{
						messagebox_spdfi('warningSmall', 'Sorry there was a problem deleting your CSV file!');
					}
					elseif($delete)
					{
						$mes = 'CSV file deleted successfully: ' . $filename;
						messagebox_spdfi('successSmall', $mes);
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
		$post_title   = $_POST['post_title'];
		$post_content = $_POST['content'];
		$filename = $_POST['csv_on_wysiwyg'];
		
		$delimiter = determine_delimiter_wtg($filename);

		$update_or_insert = 'insert';
		
		list($notice, $message) = process_wysiwyg($update_or_insert);

		$wysiwyg_finished = true;
	}
	elseif($_POST['action'] == 'process_update')// update existing record
	{
		$post_title   = $_POST['post_title'];
		$post_content = $_POST['content'];
		$filename = $_POST['csv_on_wysiwyg'];
		
		$delimiter = determine_delimiter_wtg($filename);

		$cpl_id = $_POST['cpl_id'];
		
		$update_or_insert = 'update';
		
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
			$usedfilename = $res1->csvfile;

			$delimiter = determine_delimiter_wtg($usedfilename);

			$user_count = $wpdb->get_var("SELECT COUNT(*) FROM " .$wpdb->prefix . "spdfi_campaigns WHERE camfile = '$usedfilename'");
			
			if($user_count >= 1)
			{
				messagebox_spdfi('warningSmall', 'Cannot delete, currently in use! Please delete the campaign on the campaign management page then try again.');
			}
			else
			{
				$res3 = $wpdb->get_row("DELETE FROM " .$wpdb->prefix . "spdfi_layouts WHERE id = '$cpl_id'");
				messagebox_spdfi('successSmall', 'Custom Post Layout deleted successfully');
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
			$cpl_name = $res1->name;
			
			$delimiter = determine_delimiter_wtg($filename);
			
			$full_filename = spdfi_getcsvfilesdir() . $filename;
			
			# OPEN CSV FILE
			$csvfilehandle = fopen("$full_filename", "r");
	
			$wysiwyg_finished = false;
		}
	}
	elseif($_POST['action'] == 'submitphpcustompostlayout')// php markup dump and make new record
	{
		$name = mysql_real_escape_string( $_POST['cpl_name'] );
		$code = mysql_real_escape_string( $_POST['phpcode'] );
	
		global $wpdb;
		$sqlQuery = "INSERT INTO " . $wpdb->prefix . "spdfi_layouts (name,code,inuse,type) VALUES ('$name','$code',0,1)";
		$wpdb->query($sqlQuery);
		
		messagebox_spdfi('successSmall', 'Success - Your PHP dump has been saved as a new Custom Post Layout');

		$wysiwyg_finished = true;
	}
} 
	
if($wysiwyg_finished == true)
{
?>

	<div class="wrap">
		
		<form method="post">
			<input type="hidden" name="action" value="opencsvfile" />
			<div id="poststuff" class="metabox-holder">
				<div id="datafeed-upload" class="postbox">
					<h3 class='hndle'><span>Select CSV file and create layout in WYSIWYG editor</span></h3>
					<div class="inside" style="padding:20px;">
                        <?php
						
						$i = 0;
						
						while(false != ($csvfiles = readdir($csvfiles_diropen)))
						{
							$i++;
							
							if(($csvfiles != ".") and ($csvfiles != ".."))
							{
								$fileChunks = explode(".", $csvfiles);
								
								if($fileChunks[1] == $csv_extension) //interested in second chunk only
								{ 	?>
									<label><input type="radio" name="csvfilename" value="<?php echo $csvfiles;?>" /><?php echo $csvfiles;?></label><br />
									<?php
								}
							}
						}
												
						if( $i == 2 ){ echo '<h4>No CSV Files Found</h4>';
			
			}
						
                        closedir($csvfiles_diropen); 
                        ?>
            			<p class="submit"><input class="button-primary" type="submit" name="submitbutton" value="Submit" />	<input class="button-primary" type="submit" name="submitbutton" value="Delete" /></p>
					</div>
				</div>
			</div>
		</form>
        
		<form method="post">
			<input type="hidden" name="action" value="opencustompostlayout" />
			<div id="poststuff" class="metabox-holder">
				<div id="datafeed-upload" class="postbox">
					<h3 class='hndle'><span>Select existing layout and edit in WYSIWYG editor</span></h3>
					<div class="inside" style="padding:20px;">
                        <?php
							// list existing custom post layouts from database
							$res1 = $wpdb->get_results("SELECT * FROM " .$wpdb->prefix . "spdfi_layouts WHERE type = '0'");
							
							if(! $res1 )
							{ 
								echo '<h4>No Layouts Found</h4>';
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
            			<p class="submit"><input class="button-primary" type="submit" name="submitbutton" value="Submit" /><input class="button-primary" type="submit" name="submitbutton" value="Delete" /></p>
                    </div>
				</div>
			</div>
		</form>     
        		
	</div>
	
	<?php        
	return;
}
elseif($wysiwyg_finished == false)
{
?>

<div class="wrap">

<form method="post">

	<input type="hidden" name="action" value="<?php if($_POST['action'] == 'opencsvfile'){echo 'process_insert';}elseif($_POST['action'] == 'opencustompostlayout'){echo 'process_update';}?>" />


	<div id="poststuff" class="metabox-holder">
	
		<div id="post-body">
			<div id="post-body-content">
					
				<div class="postbox">
                
					<h3 class='hndle'><span>Custom Post Layout Name</span></h3>

					<div class="inside">
                          <input type="text" name="cpl_name" size="30" value="<?php if(isset($cpl_name)){echo $cpl_name;} ?>" id="title" />
                    </div>                    
						
				</div>
                
                
				<div class="postbox">
                
					<h3 class='hndle'><span>Column Titles - Please Use Required Format ( %-example-% ) </span></h3>
                    
					<div class="inside">
                    
                    <p></p>
	
							<table style="width:55%;margin-bottom:15px;">
								<?php
								$stop_rows = 0;
								
								while (($data = fgetcsv($csvfilehandle, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
								{	 
									$stop_rows++;// used to limit row parsing to just 1
									   
										   $i = 0;
									 
											while(isset($data[$i]))
											{
												$data[$i] = rtrim($data[$i]);
												echo '<tr><td>%-' . $data[$i] . '-%</td></tr>';
												$i++; // $i will equal number of columns - use to process submission
											}
											
											$csvfile_columntotal = $i;
								
								}//end while rows
								
								fclose($csvfilehandle);		
								?>      
							</table>
						
					 </div> 
				</div>
                
				<?php 
				if($_POST['action'] == 'opencustompostlayout')// if cpl is in use display warning
                {
                    if(isset($inuse) && $inuse == 1)
                    {
						messagebox_spdfi('warningSmall', 'WARNING - This Post Custom Layout is currently being used by a campaign');
                    }
                }
                ?>
                           
                <div id="titlediv">
					<div id="titlewrap">
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
						<br class="clear" />
					</div>
				
				</div>
			
			</div>
		</div>
	</div>
	
	<br class="clear" />
   
    <input name="csv_on_wysiwyg" type="hidden" value="<?php echo $filename;?>" />
    <input name="cpl_id" type="hidden" value="<?php if(isset($cpl_id)){echo $cpl_id;}?>" />
	<p class="submit"><input class="button-primary" type="submit" value="Submit Layout" /></p>

</form>
</div>
<?php
}
?>

