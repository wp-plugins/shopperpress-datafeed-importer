<h2>New Campaign Stage 4 - Category Creation &amp; Filtering</h2>

<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>" name="new_campaign3">

    <div id="poststuff" class="metabox-holder">
        <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                
                    <h3 class='hndle'><span>Select Filter Method</span></h3>
                    <div class="inside">
    
                        <p><label><input type="radio" name="filtermethod" value="manual"  />Manual</label></p>
                        <p><label><input type="radio" name="filtermethod" value="automated" />Automated</label></p>
                        <p><label><input type="radio" name="filtermethod" value="mixed" />Mixed</label></p>
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
                        <?php
                           $handle = fopen("$csvfiledirectory", "r");
                        
                            $stop = 0;
                        
                           while (($data = fgetcsv($handle, 5000, $delimiter)) !== FALSE && $stop != 1)// Gets CSV rows
                            {	 
                                $stop++;// used to limit row parsing to just 1
                    
                                $i = 0; ?>
                                <select name="optedfiltercolumn2" size="1">
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
                
                    <h3 class='hndle'><span>Child of Child Category Creation and Filtering (Parent/Child/Child)</span></h3>
                    <div class="inside">
                        <?php
                           $handle = fopen("$csvfiledirectory", "r");
                        
                            $stop = 0;
                        
                           while (($data = fgetcsv($handle, 5000, $delimiter)) !== FALSE && $stop != 1)// Gets CSV rows
                            {	 
                                $stop++;// used to limit row parsing to just 1
                    
                                $i = 0; ?>
                                <select name="optedfiltercolumn3" size="1">
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
    
    
        <p><input name="categoryfiltervalues" class="button-primary" type="submit" value="Submit" /></p>
    
    
          <div id="post-body">
            <div id="post-body-content">
                <div class="postbox">
                
                    <h3 class='hndle'><span>Enter Manual or Mixed Filter Values Below</span></h3>
                    <div class="inside">
                         <table>
                            
                            <tr>
                                <td><b>Data Value</b> </td><td><b>Category</b></td><td></td>
                            </tr>
                            
                            <?php
                            $number = 16;
                            $count = 1;
                            while($count != $number)
                            {   # ECHO A SET OF FILTER FIELDS ?>
                                <tr>
                                    <td><?php echo $count; ?>: <input name="cat<?php echo $count; ?>a" type="text" value="" size="30" maxlength="50" />
                                    <select name="cat<?php echo $count; ?>b" size="1"><?php get_categories_fordropdownmenu_spdfi();?></select></td>
                                    <td></td>
                                    <td></td>
                                </tr><?php 
                                $count++;
                            }
                            ?>
                      </table>   
                     </div>                    
                </div>
            </div>
        </div>       
    </div>
    
    <input name="csvfile_columntotal" type="hidden" value="<?php echo $csvfile_columntotal; ?>" />
    <input name="delimiter" type="hidden" value="<?php echo $delimiter; ?>" />
    <input name="stage" type="hidden" value="4" />
    <input name="page" type="hidden" value="new_campaign" />
    <input name="csvfiledirectory" type="hidden" value="<?php echo $csvfiledirectory; ?>" />
    <input name="camid" type="hidden" value="<?php echo $camid; ?>" />
    <input name="layoutstyle" type="hidden" value="<?php echo $layoutstyle; ?>" />

</form>