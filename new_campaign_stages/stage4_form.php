<h2>New Campaign Stage 4 - Category Creation &amp; Filtering</h2>

<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>" name="new_campaign3">

    <div id="poststuff" class="metabox-holder">
        <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                
                    <h3 class='hndle'><span>Select Filter Method</span></h3>
                    <div class="inside">
    
                        <p><label><input type="radio" name="paideditiononly" value="paideditiononly" disabled="disabled"  />Manual</label></p>
                        <p><label><input type="radio" name="paideditiononly" value="paideditiononly" disabled="disabled" />Automated</label></p>
                        <p><label><input type="radio" name="paideditiononly" value="paideditiononly" disabled="disabled" />Mixed</label></p>
                        <p><label><input type="radio" name="filtermethod" value="NA" checked="checked" />Not Required: <select name="defaultpostcategory" size="1"><?php get_categories_fordropdownmenu_spdfi();?></select></label></p>
                  
                     </div>                    
                </div>
            </div>
        </div>
        
        <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                
                    <h3 class='hndle'><span>Main/Parent Filter Column</span></h3>
                    <div class="inside">
    
                        <?php
                           $handle = fopen("$csvfiledirectory", "r");
                        
                            $stop = 0;
                        
                           while (($data = fgetcsv($handle, 5000, $delimiter)) !== FALSE && $stop != 1)// Gets CSV rows
                            {	 
                                $stop++;// used to limit row parsing to just 1
                    
                                $i = 0; ?>
                                <select name="optedfiltercolumn" size="1">
                                <option value="999">Not Required</option>
                                <?php
                                while(isset($data[$i]))
                                {
                                    $data[$i] = rtrim($data[$i]);
                                    
                                    ?><option value="<?php echo $i; ?>"><?php echo $data[$i];?></option><?php
                                    
                                    $i++; // $i will equal number of columns - use to process submission
                                }?></select><?php
                            }
                            
                            fclose($handle);
                            ?>              
                     </div>                    
                </div>
            </div>
        </div>
      
          <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                
                    <h3 class='hndle'><span>Child/Sub Category Creation and Filtering (Parent/Child)</span></h3>
                    <div class="inside">
                      <select name="paideditiononly" size="1" disabled="disabled">
                          <option value="999">Not Required</option>
                      </select>
                    </div>                    
                </div>
            </div>
        </div>              
                        
          <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                
                    <h3 class='hndle'><span>Child of Child Category Creation and Filtering (Parent/Child/Child)</span></h3>
                    <div class="inside">
                                <select name="paideditiononly" size="1" disabled="disabled">
                                <option value="999">Not Required</option>
								</select>    
                     </div>                    
                </div>
            </div>
        </div>                         
    
    
        <p><input name="categoryfiltervalues" class="button-primary" type="submit" value="Submit" /></p>
    
        
    <input name="csvfile_columntotal" type="hidden" value="<?php echo $csvfile_columntotal; ?>" />
    <input name="delimiter" type="hidden" value="<?php echo $delimiter; ?>" />
    <input name="stage" type="hidden" value="4" />
    <input name="page" type="hidden" value="new_campaign" />
    <input name="csvfiledirectory" type="hidden" value="<?php echo $csvfiledirectory; ?>" />
    <input name="camid" type="hidden" value="<?php echo $camid; ?>" />
    <input name="layoutstyle" type="hidden" value="<?php echo $layoutstyle; ?>" />

</form>