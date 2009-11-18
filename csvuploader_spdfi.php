<h1> ShopperPress DataFeed Importer (Free Edition)</h1>
<h2> CSV File Uploader</h2>
<p>Upload your csv files here first before creating a new campaign.</p>

<?php
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
			
			if(!conf && !fields)
			{
				messagebox_spdfi('errorSmall', 'File Not Uploaded - Either you attempted to upload a none .csv file or an error occured!');
			}
			else
			{
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
else 
{
	?>
		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="upload" />
			<div id="poststuff">
				<div id="datafeed-upload" class="postbox">
					<h3 class='hndle'><span>Upload CSV File</span></h3>
					<div class="inside" style="padding:20px;">
					  <span class="inside" style="padding:20px;">
					  <input type="file" name="file" size="40" /><?php $filelimit = ini_get( "upload_max_filesize"); echo $filelimit.'B file size limit,upload by ftp if larger 2MB.'; ?>
					  </span>
					  <p class="submit"><input class="button-primary" type="submit" value="Submit" /></p>
					</div>
				</div>
			</div>
		</form>
	
        <p><strong>CSV Files Folder Status:</strong> <?php echo spdfi_doesexist_csvfilesfolder(); ?></p>

<?php        
	return;
}
?>