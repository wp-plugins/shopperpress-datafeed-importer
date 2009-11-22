<h2>ShopperPress DataFeed Importer <?php if(get_option('spdfi_demomode') == 1){echo ' (Demo Edition)';}?></h2>

<?php if(get_option('spdfi_demomode') == 1){?>
	<div id="poststuff" class="metabox-holder">
		<div id="post-body">
			<div id="post-body-content">
				<div class="postbox">
                
					<h3 class='hndle'><span>Demo Edition Information - Please Read</span></h3>
                    <p>Some functions have been disabled or removed for security. There are functions in CSV 2 POST that can easily effect a server if abused.
                    The plugin also operates slightly different in order to help avoid users conflicting with each other.</p>
                    <ul>
                    	<li>Errors? Most errors are caused by data or csv file configuration/formatting and can easily be&nbsp;fixed, please email info@spdfi.com.</li>
                      <li>Posts To Wide?&nbsp;- Don't copy multiple special column values to  WYSIWYG editor or use tables in your posts that are wider than the theme.</li>
                      <li>No Title? - There is a blank box for creating a title above the WYSIWYG editor, you can place the special column values there too.</li>
                    </ul>
                </div>                    
			</div>
    	</div>
    </div>   
<?php } ?>

	<div id="poststuff" class="metabox-holder">
		<div id="post-body">
			<div id="post-body-content">
			  <div class="postbox">
                
				<h3 class='hndle'><span>Quick Start Instructions</span></h3>
                  <ol>
                      <li>Firstly configure the plugins <a href="admin.php?page=settings_plus">Settings</a> exactly as you need them for your long term campaigns.</li>
                      <li><a href="admin.php?page=uploader_plus">Upload</a> a 2MB or smaller csv file&nbsp;or upload a larger file by ftp to the csv files directory in your &quot;wp-content&quot; folder.</li>
                      <li>Then go to the <a href="admin.php?page=layouts_plus">Layouts</a> page, select your csv file from the list and then click on submit&nbsp;to begin creating your post layout.</li>
                      <li>Go to the <a href="admin.php?page=new_campaign_plus">New Campaign </a>page&nbsp;and complete the 5 Stages to create your campaign.</li>
                      <li>Once your campaign is running you can monitor it later on the <a href="admin.php?page=manage_campaigns_plus">Manage Campaigns</a> page.</li>
                      <li>If your  just getting to know the plugin you can undo anything created on the <a href="admin.php?page=tools_plus">Tools</a> page so you can start fresh again.</li>
                      <li>Should your post style and layout not be as you need, you can re-open your Custom Post Layout on <a href="admin.php?page=layouts_plus">Layouts</a> page and edit it.</li>
                </ol>
                </div>                    
			</div>
    	</div>
    </div>   
    
	<div id="poststuff" class="metabox-holder">
		<div id="post-body">
			<div id="post-body-content">
				<div class="postbox">
                
					<h3 class='hndle'><span>General Plugin Status</span></h3>
                    <p>CSV Files Folder: <?php echo spdfi_doesexist_csvfilesfolder(); ?></p>
                    <p>Auto Line Ending (MAC fix): <?php  echo spdfi_autolineendings_status(); ?></p>
                    <p>Server Safe Mode: <?php  echo spdfi_checksafemodestatus(); ?></p>
                                            
                </div>                    
			</div>
    	</div>
    </div>   

	<div id="poststuff" class="metabox-holder">
		<div id="post-body">
			<div id="post-body-content">
				<div class="postbox">
                
					<h3 class='hndle'><span>Recommended Services</span></h3>
					<a href='https://www.e-junkie.com/ecom/gb.php?cl=29717&c=ib&aff=85223'><img src='http://i627.photobucket.com/albums/tt355/classipress/aff/classipress_468x60_02.jpg' border='0' width='468' height='60' alt='Premium WordPress Theme' /></a>
                        
               </div>                    
			</div>
    	</div>
    </div>