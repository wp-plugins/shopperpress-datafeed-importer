<?php
// get current option values
$debugmode = get_option('spdfi_debugmode');
$processingtrigger = get_option('spdfi_processingtrigger');
$tagslength = get_option('spdfi_tagslength');
$numerictags = get_option('spdfi_numerictags');
$publisherid = get_option('spdfi_publisherid');
$categoryparent = get_option('spdfi_defaultcatparent');
$maxstagtime = get_option('spdfi_maxstagtime');
$defaultposttype = get_option('spdfi_defaultposttype');
$defaultping = get_option('spdfi_defaultping');
$commentstatus = get_option('spdfi_defaultcomment');
$defaultphase =	get_option('spdfi_defaultphase');
$tooltips = get_option('spdfi_tooltipsonoff');
$spdfi_maxexecutiontime = get_option('spdfi_maxexecutiontime');

function spdfi_checkboxstatus1($v)
{	// echo opposite value to current value
	if($v == 0){echo 1;}else{echo 1;}
}

function spdfi_checkboxstatus2($v)
{	// echo checked or don't
	if($v == 0){}else{echo 'checked';}
}

// array for displaying list of usernames
$usernames = array(
    'orderby'          => 'display_name',
    'order'            => 'ASC',
    'multi'            => 0,
    'show'             => 'display_name',
    'echo'             => 1,
    'name'             => 'spdfi_publisherid');

?>


<div class="wrap">
    <h2>ShopperPress DataFeed Importer Settings <?php if(get_option('spdfi_demomode') == 1){echo 'Disabled In Demo Mode';}?></h2>
 
    <form method="post" action="options.php" class="form-table">
    
    <?php wp_nonce_field('update-options'); ?>

    <div id="poststuff" class="meta-box-sortables" style="position: relative; margin-top:10px;">
        <div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br /></div>
             <h3>ShopperPress Related Settings</h3>
                <div class="inside">
                    <p><a href="#" title="Use this to apply a global product quantity to all imported products. This setting will not apply if you pair a csv file column to the quantity custom field on your csv files profile. If you leave your csv file profile for quantity unpaired then this setting will take effect.">Product Quantity</a>:
<input type="text" name="spdfi_itemqty" value="<?php echo $spdfi_itemqty; ?>" size="3" maxlength="3" <?php if(get_option('spdfi_itemqty') == 1){echo 'disabled="disabled"';}?> /> 
Enter NA for unlimited products - This setting will be cancelled out if your csv profile has a pairing for the qauntity
</p>
          </div>
            </div>   
               
        <div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br /></div>
             <h3>Plugin Setup Options</h3>
                <div class="inside">
                    <p>
                        <a href="#" title="Activating debugging mode will make wordpress display all errors and write processing information to text file when running campaigns">Debugging Mode</a>:
                        <input name="spdfi_debugmode" type="checkbox" value="<?php spdfi_checkboxstatus1($debugmode); ?>" 
                        <?php spdfi_checkboxstatus2($debugmode); ?> />- Will cause the display of errors from other plugins!
                    </p>
                    
                    <p>
                    	<a href="#" title="Cannot be undone using this interface! It will disable functions in CSV 2 POST for using the plugin on a public demo, perfect for affiliates.">Activate Demo Mode</a>:
                    	<input name="spdfi_demomode" type="checkbox" value="<?php spdfi_checkboxstatus1(get_option('spdfi_demomode')); ?>" <?php spdfi_checkboxstatus2(get_option('spdfi_demomode')); ?>  />
                    </p>
                    
                    <p>
                    	<a href="#" title="This indicates the number of custom fields you need to use in your campaign on stage 4. You will need to use this for themes like ShopperPress or ClassiPress">Stage 4 Column Menus</a>:
                    	<input type="text" name="spdfi_stage4fieldscolumns" value="<?php echo get_option('spdfi_stage4fieldscolumns'); ?>" size="2" maxlength="2" />
                    </p>
                    <p>
                        <a href="#" title="To apply different character encoding to your post content and title during importing please select your requirement here">Character Encoding</a>:
                        <select name="spdfi_characterencoding" size="1">
                            <option value="default" <?php if(get_option('spdfi_characterencoding') == 'none'){echo 'selected="selected"';}?>>None</option>
                            <option value="utf8" <?php if(get_option('spdfi_characterencoding') == 'utf8'){echo 'selected="selected"';}?>>UTF-8</option>
                        </select>
                    </p>                
                </div>
            </div>    
            
        <div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br />
            </div>
             <h3>New Post Options</h3>
                <div class="inside">
                    <p><a href="#" title="Staggered processing only. This number is the amount of records that will be imported to make posts for every page visit on your blog.">Posts Per Hit</a>:
                    <input type="text" name="spdfi_postsperhit_global" value="<?php echo get_option('spdfi_postsperhit_global'); ?>" size="3" maxlength="3"  /> 
                    </p>
                    <p><a href="#" title="User account to be applied to new posts as the publisher">Default Publisher</a>:
                    <?php wp_dropdown_users( $usernames );?>
                    </p>
                    <p><a href="#" title="If you use category creation on stage 5 this setting will assign the parent on stage 5 as a child to the selected category here so that all categories and all posts come under this category. This setting is hardly ever needed.">Default Category Parent</a>:
                    <select name="spdfi_defaultcatparent" size="1">
                    <option value="NA">No Parent Required</option>
                    <?php get_categories_fordropdownmenu_spdfi();?>
                    </select>
                    </p>
                    <p><a href="#" title="Make import data blog posts or standard pages.">Default Post Type</a>:
                    <select name="spdfi_defaultposttype" size="1" >
                    <option value="post" <?php if($defaultposttype == 'post'){echo 'selected="selected"';}?>>Post</option>
                    <option value="page" <?php if($defaultposttype == 'page'){echo 'selected="selected"';}?>>Page</option>
                    </select>
                    </p>
                    <p><a href="#" title="Will allow or disallow pinging on newly created posts or pages">Default Ping Status</a>:
                      <select name="spdfi_defaultping" size="1">
                    <option value="1" <?php if($defaultping == '1'){echo 'selected="selected"';}?>>On</option>
                    <option value="0" <?php if($defaultping == '0'){echo 'selected="selected"';}?>>Off</option>
                    </select>
                    </p>
                    <p>
                        <a href="#" title="Switch commenting on or off for new posts">Default Comments Status</a>:
                        <select name="spdfi_defaultcomment" size="1">
                        <option value="open" <?php if($commentstatus == 'open'){echo 'selected="selected"';}?>>Open</option>
                        <option value="closed" <?php if($commentstatus == 'closed'){echo 'selected="selected"';}?>>Closed</option>
                        </select>
                    </p>                        
                    <p>
                        <a href="#" title="If using randomised publishing date on stage 3. The dates applied will be between the start and end date set here.">Random Date Range</a><br />
                        <?php spdfi_datepicker_nonejavascript(); ?>           
                    </p>           
                 </div>
            </div>
            
        <div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br />
            </div>
             <h3>Processing Configuration Options</h3>
                <div class="inside">
                    <p>
                    	<a href="#" title="If your server is very sensitive you will need to enforce  a maximum processing time however 30 seconds is normally enough for importing and will not effect most servers.">Maximum Execution Time In None Full Modes</a>:
                    	<input type="text" name="spdfi_maxstagtime" value="<?php echo $maxstagtime; ?>" size="8" maxlength="8" />seconds
                    </p>
                    <p>
                    	<a href="#" title="Will delay processing events and force the plugin to only import data once the timer has ended. The number entered here is in seconds">Processing Event Delay</a>:
                    	<input type="text" name="spdfi_processingdelay" value="<?php echo get_option('spdfi_processingdelay'); ?>" size="8" maxlength="8" />seconds
                    </p>
                    <p>
                        <a href="#" title="This is a special function built into Wordpress. The options are points in which plugins can action events while Wordpress loads. Different triggers here may have different effects depending on your setup">Processing Trigger</a>:
                        <select name="spdfi_processingtrigger" size="1" >
                            <option value="get_footer" <?php if($processingtrigger == 'get_footer'){echo 'selected="selected"';}?>>get_footer</option>
                            <option value="get_header" <?php if($processingtrigger == 'get_header'){echo 'selected="selected"';}?>>get_header</option>
                            <option value="wp_footer" <?php if($processingtrigger == 'wp_footer'){echo 'selected="selected"';}?>>wp_footer</option>
                            <option value="wp_head" <?php if($processingtrigger == 'wp_head'){echo 'selected="selected"';}?>>wp_head</option>
                            <option value="init" <?php if($processingtrigger == 'init'){echo 'selected="selected"';}?>>init</option>
                            <option value="send_headers" <?php if($processingtrigger == 'send_headers'){echo 'selected="selected"';}?>>send_headers</option>
                            <option value="shutdown" <?php if($processingtrigger == 'shutdown'){echo 'selected="selected"';}?>>shutdown</option>
                            <option value="wp" <?php if($processingtrigger == 'wp'){echo 'selected="selected"';}?>>wp</option>
                        </select>
                    </p>
                    <p>
                        <a href="#" title="If set to manual you will need to start the update phase yourself using the campaign manager. If you set it to automatic then updating will proceed once the campaign has imported all data. Updating will loop forever constantly applying the csv file data to existing posts.">Default Update State</a>:
                        <select name="spdfi_defaultphase" size="1" >
                        <option value="0" <?php if($defaultphase == '0'){echo 'selected="selected"';}?>>Manual Update Activation</option>
                        <option value="1" <?php if($defaultphase == '1'){echo 'selected="selected"';}?>>Auto Update Activation</option>
                        </select>
                    </p>  
                </div>
            </div>


        <div class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br />
            </div>
             <h3>SEO Options</h3>
                <div class="inside">
                    <p><a href="#" title="This is the total length of your tags string for each post. 50 will proceed 5-8 tags usually. This is ideal to keep your theme looking well laid out.">Post TAGs Maximum Length</a>:
                    <input type="text" name="spdfi_tagslength" value="<?php echo $tagslength; ?>" size="3" maxlength="3" /> 
                    characters</p>
                    <p><a href="#" title="You can stop numbers being used as tags by ticking this box.">Allow Numeric Tags</a>:
                    <input name="spdfi_numerictags" type="checkbox" value="<?php spdfi_checkboxstatus1($numerictags); ?>" <?php spdfi_checkboxstatus2($numerictags); ?> /> 
                    </p>
                    <textarea name="spdfi_exclusions" cols="100" rows="10"><?php echo get_option('spdfi_exclusions');?></textarea>                    
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
    
    </div>

    <input type="hidden" name="action" value="update" />    
    <input type="hidden" name="page_options" value="
    spdfi_debugmode,
    spdfi_processingtrigger,
    spdfi_postsperhit_global,
    spdfi_publisherid,
    spdfi_maxstagtime,
    spdfi_tagslength,
    spdfi_numerictags,
    spdfi_defaultcatparent,
	spdfi_defaultposttype,
    spdfi_defaultping,
    spdfi_defaultcomment,
	spdfi_defaultphase,
    spdfi_tooltipsonoff,
  	spdfi_processingdelay,
	spdfi_exclusions,
    spdfi_demomode,
	spdfi_stage4fieldscolumns,
    spdfi_randomdate_monthstart,
    spdfi_randomdate_daystart,
    spdfi_randomdate_yearstart,
    spdfi_randomdate_monthend,
    spdfi_randomdate_dayend,
    spdfi_randomdate_yearend,
	spdfi_characterencoding,
    spdfi_itemqty
    " />
    
    <p class="submit"><input type="submit" class="button-primary" value="Save Changes" <?php if(get_option('spdfi_demomode') == 1){echo 'disabled="disabled"';}?>/></p>
    
    </form>
</div>