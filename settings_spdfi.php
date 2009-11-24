<?php
// get current option values
$debugmode = get_option('spdfi_debugmode');
$processingtrigger = get_option('spdfi_processingtrigger');
$publisherid = get_option('csv2post_publisherid');
$categoryparent = get_option('csv2post_defaultcatparent');
$autodescription = get_option('spdfi_autodescription');
$spdfi_itemqty = get_option('spdfi_itemqty');


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
                                            
                     </div>   
				</div>
            </div>
        </div>
    </div>
        
	<div id="poststuff" class="metabox-holder">
	
		<div id="post-body">
			<div id="post-body-content">
					
				<div class="postbox">
                
					<h3 class='hndle'><span>Post Settings</span></h3>

					<div class="inside">

           			  <p>Posts Per Hit: <input type="text" name="spdfi_postsperhit" value="<?php echo $postsperhit; ?>" size="3" maxlength="3" disabled="disabled" /></p>

                        <p>Default Publisher ID: <?php wp_dropdown_users( $usernames );?></p>
                      
                  		<p>Default Category Parent: 
                  		  <select name="csv2post_defaultcatparent" size="1">
                          <option value="NA">No Parent Required</option>
                            <?php get_categories_fordropdownmenu_spdfi();?>
                            </select>
               		  </p>
                                            
           			  <p>Product Quantity: <input type="text" name="spdfi_itemqty" value="<?php echo $spdfi_itemqty; ?>" size="3" maxlength="3" disabled="disabled" /></p>


                        <p>Default Post Type: 
                        	<select name="spdfi_defaultposttype" size="1" disabled="disabled">
                            	<option value="post" <?php if($defaultposttype == 'post'){echo 'selected="selected"';}?>>Post</option>
                            </select>
                        </p>
                      
                        <p>Default Ping: 
                        	<select name="spdfi_defaultping" size="1" disabled="disabled">
                            	<option value="1" <?php if($defaultping == '1'){echo 'selected="selected"';}?>>On</option>
                            </select>
                        </p>
                             
                        <p>Default Comments: 
                        	<select name="spdfi_defaultcomment" size="1" disabled="disabled">
                            	<option value="open" <?php if($commentstatus == 'open'){echo 'selected="selected"';}?>>Open</option>
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
            			  <input type="text" name="spdfi_maxstagtime" value="<?php echo $maxstagtime; ?>" size="8" maxlength="8" disabled="disabled"/> 
           			  </p>
                      
            			<p>Processing Event Delay: 
            			  <input type="text" name="spdfi_processingdelay" value="<?php echo get_option('spdfi_processingdelay'); ?>" size="8" maxlength="8" disabled="disabled" /> 
           			  </p>
                       
                       
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
                                            
                    <p>Default Update State: 
                        <select name="spdfi_defaultphase" size="1" disabled="disabled">
                            <option value="0" <?php if($defaultphase == '0'){echo 'selected="selected"';}?>>Manual Update Activation</option>
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
                
					<h3 class='hndle'><span>SEO</span></h3>

					<div class="inside">

            			<p>Allow Automatic Keyword Creation: <input name="spdfi_autokeywords" type="checkbox" value="<?php spdfi_checkboxstatus1($autokeywords); ?>" <?php spdfi_checkboxstatus2($autokeywords); ?> disabled="disabled" /> 
           			  </p>
					  <p>Allow Automatic Excerpt Creation: 
					    <input name="spdfi_autodescription" type="checkbox" value="<?php spdfi_checkboxstatus1($autodescription); ?>" <?php spdfi_checkboxstatus2($autodescription); ?> disabled="disabled" /> 
					  </p>
					  <p>Allow Automatic Tag Creation: <input name="spdfi_autotags" type="checkbox" value="<?php spdfi_checkboxstatus1($autotags); ?>" <?php spdfi_checkboxstatus2($autotags); ?> disabled="disabled" /> 
					  </p>
                      <p>Post TAG's Maximum Length: <input type="text" name="spdfi_tagslength" value="<?php echo $tagslength; ?>" size="3" maxlength="3" disabled="disabled" /> 
                      </p>
                      <p>Allow Numeric Tags: <input name="spdfi_numerictags" type="checkbox" value="<?php spdfi_checkboxstatus1($numerictags); ?>" <?php spdfi_checkboxstatus2($numerictags); ?> disabled="disabled" /> 
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
    spdfi_autodescription,
    spdfi_itemqty,
    csv2post_publisherid,
    " />
    
    <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
    
    </form>
</div>