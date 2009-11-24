<h2>New Campaign Stage 1 - Main Settings</h2>

<form enctype="multipart/form-data"  method="post" action="<?php $_SERVER['PHP_SELF']; ?>" name="new_campaign1">

    <div id="poststuff" class="metabox-holder">
        <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                    <h3 class='hndle'><span>Enter Campaign Name:</span></h3>
                    <div class="inside">
                    	<input type="text" name="campaignname" id="campaignname" />
                    </div>  
                </div>
            </div>
        </div>
    </div>
    
    <div id="poststuff" class="metabox-holder">
        <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                    <h3 class='hndle'><span>Select Processing Method </span></h3>
                    <div class="inside"><label>
                        <input type="radio" name="paideditiononly" value="1" id="paideditiononly" disabled="disabled" />
                        Full</label>
                        <br />
                        
                        <label>
                        <input type="radio" name="processrate" value="2" id="ProcessRate_1" />
                        Staggered</label>
                        <br />
                        
                        <input type="radio" name="paideditiononly" value="3" id="paideditiononly" disabled="disabled" />
Scheduled
                        </label>
                  <input type="text" name="paideditiononly" id="paideditiononly" size="4" disabled="disabled" />
                    </div>  
                </div>
            </div>
        </div>
    </div>
    
    <div id="poststuff" class="metabox-holder">
        <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                    <h3 class='hndle'><span>Select CSV File</span></h3>
                    <div class="inside">    
					<?php
                          while(false != ($csvfiles = readdir($csvfiles_diropen)))
                          {
                            if(($csvfiles != ".") and ($csvfiles != ".."))
                            {
                              $fileChunks = explode(".", $csvfiles);
                              if($fileChunks[1] == $csv_extension) //interested in second chunk only
                              { ?>
                                <label><input type="radio" name="csvfilename" value="<?php echo $csvfiles;?>" /><?php echo $csvfiles;?></label><br />
                              <?php
                              }
                            }
                          }
                          closedir($csvfiles_diropen); 
                    ?>
                    </div>  
                </div>
            </div>
        </div>
    </div>
    
    <div id="poststuff" class="metabox-holder">
        <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                    <h3 class='hndle'><span>Select Post Content Layout</span></h3>
                    <div class="inside">    
					<?php
                    // list existing custom post layouts from database
                    $res1 = $wpdb->get_results("SELECT * FROM " .$wpdb->prefix . "spdfi_layouts");
                    
                    foreach($res1 as $x)
                    {   
                        ?>
                        <label><input type="radio" name="layoutstyle" value="<?php echo $x->id;?>" /><?php echo $x->name;?></label><br />
                        <?php
                    }
                    ?>               
                    </div>  
                </div>
            </div>
        </div>
	</div>
        
    <br />

    <input name="stage" type="hidden" value="1" />
    <input name="page" type="hidden" value="new_campaign" />
    <input type="hidden" name="MAX_FILE_SIZE" value="90000000" />
    <input name="campaignsubmit" class="button-primary" type="submit" value="Next Step" />
</form>
