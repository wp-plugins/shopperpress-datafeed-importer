<h2>CSV File Uploader</h2>
<p>Upload your csv files here first before creating a new campaign.</p>

<?php
global $wpdb;

if ($_SERVER['REQUEST_METHOD']=='POST') 
{
	$_POST = stripslashes_deep($_POST);
	
	$file = $_FILES['file'];
	
	if($_POST['action'] == 'upload') 
	{
		if ($file['error'] == 0) 
		{
			$filename = time() . '_' . $file['name'];
			
			$pathtofilename = spdfi_getcsvfilesdir() . $filename;
			move_uploaded_file($file['tmp_name'], $pathtofilename);
		
			if (file_exists(rtrim(ABSPATH, '/') . '/wp-content/spdfifiles/' . $options['file'])) 
			{
				@unlink(rtrim(ABSPATH, '/') . '/wp-content/spdfifiles/' . $options['file']);
			}
					
			$conf = File_CSV::discoverFormat($pathtofilename);
			$fields = File_CSV::read($pathtofilename, $conf);
			
			$fileChunks = explode(".", $filename);
     		$fileChunks[0];
	 
			if(!conf && !fields)
			{
				messagebox_spdfi('errorSmall', 'File Not Uploaded - Either you attempted to upload a none .csv file or an error occured!');
			}
			else
			{
				// create option using filename that will hold the delimiter - comma as default as its most common
				if(isset($_POST['manualdelimiter']))
				{
					add_option($fileChunks[0],$_POST['manualdelimiter']);// set submitted delimiter
				}
				else
				{
					add_option($fileChunks[0],',');// set default delimiter
				}
				
				messagebox_spdfi('successSmall', 'Success - CSV File Uploaded - <a href="admin.php?page=layouts_spdfi">Now Create Custom Post Layout</a>');
				echo '<p>Your csv file name is now ' . $filename .'.</p>';
				echo '<p>Your csv file will now be available in the Custom Post Layout editor and campaign creator.</p>';
			}
			
			if(!$conf)
			{
				messagebox_spdfi('errorSmall', 'Error - problem processing file!');
			}
			else
			{
				// update csv file delimiter if required
				if(isset($_POST['manualdelimiter']))
				{
					update_option($fileChunks[0],$_POST['manualdelimiter']);// set submitted delimiter
				}
				else
				{
					update_option($fileChunks[0],$conf['sep']);// set auto detected delimiter
				}
				
				echo '<h3>CSV Format Detection</h3>';
				echo '<p>I detected ' . $conf['fields'] . ' columns in your csv file.</p>';
				echo '<p>Your csv data seperator has been detected as ' . $conf['sep'] . '';
				echo '<p>The quote used in your csv file is ' . $conf['quote'] . '';
			}
			
			if(!$fields)
			{
				messagebox_spdfi('errorSmall', 'Error - problem reading your first row of titles and they will be required later!');
			}
			else
			{
				echo '<h3>CSV Column Titles Detected</h3>';
				foreach($fields as $title)
				{
					echo '<p>' . $title . '</p>';
				}
			}				
		}
	}
} 
?>

		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="upload" />
			<div id="poststuff">
				<div id="datafeed-upload" class="postbox">
					<h3 class='hndle'><span>Upload CSV File</span></h3>
					<div class="inside" style="padding:20px;">
					  <input type="file" name="file" size="40" /><?php $filelimit = ini_get( "upload_max_filesize"); echo $filelimit.'B file size limit,upload by ftp if larger 2MB.'; ?>
					  	<br />
                        <input name="manualdelimiter" type="text" size="1" maxlength="1" /> 
                        Please Enter File Delimiter If You Know It                      
					  <p class="submit"><input class="button-primary" type="submit" value="Submit" /></p>
					</div>
				</div>
			</div>
		</form>

    <form method="post" action="options.php" class="form-table">

            <?php wp_nonce_field('update-options'); ?>

			<div id="poststuff">
				<div id="datafeed-upload" class="postbox">
				  <h3 class='hndle'><span>Detected CSV Files</span></h3>
					  <span class="inside" style="padding:20px;">
						
                        <table width="682">
                        	<tr>
                            	<td width="263">File Name</td><td width="15"></td><td width="388"> Delimiter</td>
                            </tr>

						<?php
							$csv_extension = 'csv';
						
							$csvfiles_dir = spdfi_getcsvfilesdir();
							$csvfiles_diropen = opendir($csvfiles_dir);
							
							// create list of comma seperated filenames for wordpress option update process
							$csvfilenamelist_options = '';
							$i = 0;
							while(false != ($csvfiles = readdir($csvfiles_diropen)))
                            {
                                if(($csvfiles != ".") and ($csvfiles != ".."))
                                {
                                  $fileChunks = explode(".", $csvfiles);
                                  if($fileChunks[1] == $csv_extension) //interested in second chunk only
                                  { ?>  
                                        <tr>
                                            <td><?php echo $fileChunks[0];?></td><td></td><td>
											<?php
												// first attempt to set option for each file to ensure it exists, but with no value
												add_option($fileChunks[0],'');
												$file_del = get_option($fileChunks[0]);
												if(!empty($file_del))
												{?>
													<input name="<?php echo $fileChunks[0]; ?>" value="<?php echo get_option($fileChunks[0]); ?>" type="text" size="1" maxlength="1" /><?php
												}
												else
												{?>
													<input name="<?php echo $fileChunks[0]; ?>" value="" type="text" size="1" maxlength="1" /> Delimiter Not Set<?php
												} 
											?>
                                            </td>
                                        </tr>
                                        
                                  <?php
								  		if($i != 0)
										{
								  			$csvfilenamelist_options .= ',';
										}
										$csvfilenamelist_options .= $fileChunks[0];;
                                  		$i++;
								  }
                                }
                              }
                              closedir($csvfiles_diropen); 
                        ?>
                        </table>
                      
                      
              			<input type="hidden" name="page_options" value="<?php echo $csvfilenamelist_options; ?>" />
					
                        <input type="hidden" name="action" value="update" />

                        <p class="submit">
                            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                        </p>
                        		
                  </span>
                	</div>
				</div>
			</div>
		</form>	
	
        <p><strong>CSV Files Folder Status:</strong> <?php echo spdfi_doesexist_csvfilesfolder(); ?></p>
