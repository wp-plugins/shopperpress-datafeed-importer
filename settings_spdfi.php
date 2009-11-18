<h1> ShopperPress DataFeed Importer (Free Edition)</h1>

<?php
// get current option values
$debugmode = get_option('spdfi_debugmode');
$processingtrigger = get_option('spdfi_processingtrigger');
$postsperhit = get_option('spdfi_postsperhit');
$publisherid = get_option('spdfi_publisherid');
$maxstagtime = get_option('spdfi_maxstagtime');
$localhostinstalled = get_option('spdfi_localhostinstalled');
$defaultposttype = get_option('spdfi_defaultposttype');
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
    <h2> Settings</h2>

    <form method="post" action="options.php">
    
    <?php wp_nonce_field('update-options'); ?>
    
	<div id="poststuff" class="metabox-holder">
	
		<div id="post-body">
			<div id="post-body-content">
				<div class="postbox">
                
					<h3 class='hndle'><span>Plugin Setup</span></h3>
					<div class="inside">
                    
					<?php if(get_option('spdfi_demomode') == 0){ ?>
            			<p>
                            Debugging Mode:  
                            <input name="spdfi_debugmode" type="checkbox" value="<?php spdfi_checkboxstatus1($debugmode); ?>" 
                            <?php spdfi_checkboxstatus2($debugmode); ?> />  
                        </p>
                  	<?php } ?>
                    
                        <p>
                            Processing Trigger: 
                            <select name="spdfi_processingtrigger" size="1" <?php if(get_option('spdfi_demomode') == 1){echo 'disabled="disabled"';}?>>
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
                        
                     </div>   
				</div>
            </div>
        </div>
    </div>
        
	<div id="poststuff" class="metabox-holder">
	
		<div id="post-body">
			<div id="post-body-content">
					
				<div class="postbox">
                
					<h3 class='hndle'><span>Post Settings</span> (Paid Edition Only)</h3>

					<div class="inside">

           			  <p>Posts Per Hit: <input type="text" name="spdfi_postsperhit" value="<?php echo $postsperhit; ?>" size="3" maxlength="3" <?php if(get_option('spdfi_demomode') == 1){echo 'disabled="disabled"';}?> /></p>

                        <p>Default Publisher ID: <?php wp_dropdown_users( $usernames );?></p>
                      
                      
           			  <p>Product Quantity: <input type="text" name="spdfi_itemqty" value="5" size="3" maxlength="3" disabled="disabled" />
           			  </p>


                        <p>Default Post Type: 
                        	<select name="paideditiononly" size="1" disabled="disabled">
                            	<option value="post" <?php if($defaultposttype == 'post'){echo 'selected="selected"';}?>>Post</option>
                            	<option value="page" <?php if($defaultposttype == 'page'){echo 'selected="selected"';}?>>Page</option>
                            </select>
                        </p>
                      
                        <p>Default Ping: 
                        	<select name="paideditiononly" size="1" disabled="disabled">
                            	<option value="1" <?php if($defaultping == '1'){echo 'selected="selected"';}?>>On</option>
                            	<option value="0" <?php if($defaultping == '0'){echo 'selected="selected"';}?>>Off</option>
                            </select>
                        </p>
                             
                        <p>Default Comments: 
                        	<select name="paideditiononly" size="1" disabled="disabled">
                            	<option value="open" <?php if($commentstatus == 'open'){echo 'selected="selected"';}?>>Open</option>
                            	<option value="closed" <?php if($commentstatus == 'closed'){echo 'selected="selected"';}?>>Closed</option>
                            </select>
                        </p>                             
                                             
                     </div>   
				</div>
            </div>
        </div>
    </div>

	<div id="poststuff" class="metabox-holder">
	
		<div id="post-body">
			<div id="post-body-content">
					
				<div class="postbox">
                
					<h3 class='hndle'><span>Processing Configuration</span></h3>

					<div class="inside">

            			<p>Maximum Execution Time In None Full Modes: 
            			  <input type="text" name="spdfi_maxstagtime" value="<?php echo $maxstagtime; ?>" size="8" maxlength="8" <?php if(get_option('spdfi_demomode') == 1){echo 'disabled="disabled"';}?>/> 
           			  </p>
                      
                    <p>Default Update State: 
                        <select name="spdfi_defaultphase" size="1">
                            <option value="0" <?php if($defaultphase == '0'){echo 'selected="selected"';}?>>Manual Update Activation</option>
                            <option value="1" <?php if($defaultphase == '1'){echo 'selected="selected"';}?>>Auto Update Activation</option>
                        </select>
                    </p>                           
                      
                     </div>   

 
				</div>
            </div>
        </div>
    </div>

	<div id="poststuff" class="metabox-holder">
	
		<div id="post-body">
			<div id="post-body-content">
					
				<div class="postbox">
                
					<h3 class='hndle'><span>SEO</span> (Paid Edition Only)</h3>

					<div class="inside">

            			<p>Allow Automatic Keyword Creation:
            			  <input name="paideditiononly" type="checkbox" value="0"  disabled="disabled" />
            			</p>
					  <p>Allow Automatic Excerpt Creation: 
					    <input name="paideditiononly" type="checkbox" value="0" disabled="disabled" /> 
					  </p>
					  <p>Allow Automatic Tag Creation: <input name="paideditiononly" type="checkbox" value="0" disabled="disabled" /> 
					  </p>
                      <p>Post TAG's Maximum Length: <input type="text" name="paideditiononly" value="20" size="3" maxlength="3" disabled="disabled" /> 
                      </p>
                      <p>Allow Numeric Tags: <input name="paideditiononly" type="checkbox" value="0" disabled="disabled" /> 
                      </p>
                     </div>   
                                      
				</div>
            </div>
        </div>
    </div>  


    <input type="hidden" name="action" value="update" />
    
    <input type="hidden" name="page_options" value="
    spdfi_debugmode,
    spdfi_processingtrigger,
    spdfi_publisherid,
	spdfi_defaultposttype,
	spdfi_defaultphase,
    " />
    
    <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
    
    </form>
</div>