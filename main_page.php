<div class="wrap">
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
                    	<li>Errors? Most errors are caused by data or csv file configuration/formatting and can easily be&nbsp;fixed, please email info@webtechglobal.com.</li>
                      <li>Posts To Wide?&nbsp;- Don't copy multiple special column values to  WYSIWYG editor or use tables in your posts that are wider than the theme.</li>
                      <li>No Title? - There is a blank box for creating a title above the WYSIWYG editor, you can place the special column values there too.</li>
                    </ul>
                </div>                    
			</div>
    	</div>
    </div> 
    
<?php }   ?> 
 
 
    <div id="poststuff" class="meta-box-sortables" style="position: relative; margin-top:10px;">

        <div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br />
            </div>
             <h3>Click Statistics</h3>
                <div class="inside">
                    <?php 
                    global $post;
                    $myposts = get_posts();
                    $total_click_count = 0;
                    foreach($myposts as $post) :
                        setup_postdata($post);
                        $posts_click_count = get_post_meta($post->ID, 'spdfi_cloakedlinkclicks', true);
                        $total_click_count = $total_click_count + $posts_click_count;
                    endforeach; 
                    ?>
                    <p><a href="#" title="Total number of clicks. Click statistics will be gathered when you use URL cloaking on Stage 2 of the New Campaign process.">Total Clicks</a>: <?php echo $total_click_count;?>
                </div>
            </div>
            
            
        <div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br />
            </div>
             <h3>Support</h3>
                <div class="inside">
                    <ul>
                      <li><a href="https://secure.avangate.com/order/product.php?PRODS=2929632&amp;QTY=1&amp;AFFILIATE=8691" title="Visit ShopperPress Home Site" target="_blank">ShopperPress Home</a> - Go to the ShopperPress Theme home website. </li>
                      <li><a href="mailto:webmaster@webtechglobal.co.uk" title="Email for help on CSV 2 POST" target="_blank">Email</a>&nbsp;-  If you can't find answers on <a href="http://www.spdfi.com/?s=help" title="Get help on the CSV 2 POST website" target="_blank">www.spdfi.com</a> please email me.</li>
                      <li><a href="http://forum.webtechglobal.co.uk/viewforum.php?f=2&amp;sid=62639f7692667366357e6b79bff726b1" title="Go to the WTG forum for support" target="_blank">Forum</a>&nbsp;- Central forum for all WebTechGlobal services and products.</li>
                      <li><a href="http://www.csv2post.com/blog/free-edition/post2" title="Watch CSV 2 POST videos" target="_blank">Videos</a> - You will find various videos for CSV 2 POST, almost identical plugin.</li>
                      <li><a href="http://twitter.com/webtechglobal" title="Follow WebTechGlobal on Twitter" target="_blank">Twitter</a> - Get new version notifications for plugins through my tweets.</li>
                    </ul>                
                </div>
            </div>            
    
        <div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br />
            </div>
             <h3>Quick Start Instructions</h3>
                <div class="inside">
                      <ol>
                          <li>Firstly configure the plugins <strong>Settings</strong> exactly as you need them for your long term campaigns.</li>
                          <li><strong>Upload</strong> a 2MB or smaller csv file&nbsp;or upload a larger file by ftp to the csv files directory (wp-content/spdfifiles).</li>
                          <li>Then go to  <strong>CSV Profiles</strong> page, select your csv file from the list and fill out all form details to create a long term profile of your file.</li>
                          <li>Go to the <strong>New Campaign</strong> page&nbsp;and complete the 5 Stages to create a unique importing campaign based on your CSV Profile.</li>
                          <li>Once your campaign is running you can monitor it later on the <strong>Manage Campaigns</strong> page.</li>
                          <li>If your  just getting to know the plugin you can undo anything created on the <strong>Tools</strong> page so you can start fresh again.</li>
                    </ol>             
            </div>
            </div>     
            
        <div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br />
            </div>
             <h3>General Plugin Status</h3>
                <div class="inside">
                    <p>CSV Files Folder: <?php echo spdfi_doesexist_csvfilesfolder(); ?></p>
                    <p>Auto Line Ending (MAC fix): <?php  echo spdfi_autolineendings_status(); ?></p>
                    <p>Server Safe Mode: <?php  echo spdfi_checksafemodestatus(); ?></p>
                    <p>Last Execution Point: <?php echo get_option('spdfi_lastpointofexecution');?></p>
                </div>
            </div>
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
    
   