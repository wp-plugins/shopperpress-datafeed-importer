<h2> New Campaign Stage 2 - Relationships</h2>

<?php	
$conf = File_CSV::discoverFormat($csvfiledirectory);
$fields = File_CSV::read($csvfiledirectory, $conf);
$delimiter = $conf['sep'];

# OPEN CSV FILE
$handle = fopen("$csvfiledirectory", "r");

$stop_rows = 0;

while (($data = fgetcsv($handle, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
{	 
    $stop_rows++;// used to limit row parsing to just 1
	
	// count number of columns in csv file 
	$i = 0;
	while(isset($data[$i]))
	{
		$data[$i] = rtrim($data[$i]);
		$i++; 
	}
	
	$csvfile_columntotal = $i; 
   
    ?>
    
    <form method="post" action="<?php $_SERVER['PHP_SELF']; ?>" name="new_campaign2">
        <table width="1153">    
                
            <tr>
                <td width="142"><h3>Custom Field</h3></td><td width="33"></td><td width="962"><h3>CSV Column Assigned</h3></td>
            </tr>    
                
            <tr>
                <td>Price:</td>
                <td></td><td><select name="price_col" size="1"> 
                <option value="999">Exclude</option>     
                <?php           
                $handle1 = fopen("$csvfiledirectory", "r"); $stop = 0; $i = 0;
                while (($data = fgetcsv($handle1, 5000, $delimiter)) !== FALSE && $stop != 1)
                {	 
                    $stop++;
                    $i = 0; 
                    while(isset($data[$i]))
                    {
                        $data[$i] = rtrim($data[$i]);
                        ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                        $i++;
                    }
                }
                fclose($handle1);
                ?>
                </select></td>
            </tr>
              
            <tr>
                <td>Old Price:</td>
                <td></td><td><select name="oldprice_col" size="1">      
                <option value="999">Exclude</option>     
                <?php           
                $handle2 = fopen("$csvfiledirectory", "r"); $stop = 0; $i = 0;
                while (($data = fgetcsv($handle2, 5000, $delimiter)) !== FALSE && $stop != 1)
                {	 
                    $stop++;
                    $i = 0; 
                    while(isset($data[$i]))
                    {
                        $data[$i] = rtrim($data[$i]);
                        ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                        $i++;
                    }
                }
                fclose($handle2);
                ?>
                </select></td>
            </tr>     
              
            <tr>
                <td>Image:</td>
                <td></td><td><select name="image_col" size="1">      
                <option value="999">Exclude</option>     
                <?php           
                $handle3 = fopen("$csvfiledirectory", "r"); $stop = 0; $i = 0;
                while (($data = fgetcsv($handle3, 5000, $delimiter)) !== FALSE && $stop != 1)
                {	 
                    $stop++;
                    $i = 0; 
                    while(isset($data[$i]))
                    {
                        $data[$i] = rtrim($data[$i]);
                        ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                        $i++;
                    }
                }
                fclose($handle3);
                ?>
                </select></td>
            </tr>       
            
            <tr>
                <td>Images:</td><td></td><td><select name="images_col" size="1">      
                <option value="999">Exclude</option>     
                <?php           
                $handle4 = fopen("$csvfiledirectory", "r"); $stop = 0; $i = 0;
                while (($data = fgetcsv($handle4, 5000, $delimiter)) !== FALSE && $stop != 1)
                {	 
                    $stop++;
                    $i = 0; 
                    while(isset($data[$i]))
                    {
                        $data[$i] = rtrim($data[$i]);
                        ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                        $i++;
                    }
                }
                fclose($handle4);
                ?>
                </select></td>
            </tr>       
                        
            <tr>
                <td>Thumbnail:</td><td></td><td><select name="thumbnail_col" size="1">      
                <option value="999">Exclude</option>     
                <?php           
                $handle5 = fopen("$csvfiledirectory", "r"); $stop = 0; $i = 0;
                while (($data = fgetcsv($handle5, 5000, $delimiter)) !== FALSE && $stop != 1)
                {	 
                    $stop++;
                    $i = 0; 
                    while(isset($data[$i]))
                    {
                        $data[$i] = rtrim($data[$i]);
                        ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                        $i++;
                    }
                }
                fclose($handle5);
                ?>
                </select></td>
            </tr> 
            
            <tr>
                <td>Shipping:</td><td></td><td><select name="shipping_col" size="1">      
                <option value="999">Exclude</option>     
                <?php           
                $handle6 = fopen("$csvfiledirectory", "r"); $stop = 0; $i = 0;
                while (($data = fgetcsv($handle6, 5000, $delimiter)) !== FALSE && $stop != 1)
                {	 
                    $stop++;
                    $i = 0; 
                    while(isset($data[$i]))
                    {
                        $data[$i] = rtrim($data[$i]);
                        ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                        $i++;
                    }
                }
                fclose($handle6);
                ?>
                </select></td>
            </tr> 
            
            <tr>
                <td>Featured:</td><td></td><td><select name="featured_col" size="1">      
                <option value="999">Exclude</option>     
                <?php           
                $handle7 = fopen("$csvfiledirectory", "r"); $stop = 0; $i = 0;
                while (($data = fgetcsv($handle7, 5000, $delimiter)) !== FALSE && $stop != 1)
                {	 
                    $stop++;
                    $i = 0; 
                    while(isset($data[$i]))
                    {
                        $data[$i] = rtrim($data[$i]);
                        ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                        $i++;
                    }
                }
                fclose($handle7);
                ?>
                </select></td>
            </tr> 
            
            <tr>
                <td>Excerpt:</td><td></td><td><select name="excerpt_col" size="1">      
                <option value="999">Exclude</option>     
                <?php           
                $handle8 = fopen("$csvfiledirectory", "r"); $stop = 0; $i = 0;
                while (($data = fgetcsv($handle8, 5000, $delimiter)) !== FALSE && $stop != 1)
                {	 
                    $stop++;
                    $i = 0; 
                    while(isset($data[$i]))
                    {
                        $data[$i] = rtrim($data[$i]);
                        ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                        $i++;
                    }
                }
                fclose($handle8);
                ?>
                </select></td>
            </tr> 
            
            <tr>
                <td>Keywords:</td><td></td><td><select name="keywords_col" size="1">      
                <option value="999">Exclude</option>     
                <?php           
                $handle9 = fopen("$csvfiledirectory", "r"); $stop = 0; $i = 0;
                while (($data = fgetcsv($handle9, 5000, $delimiter)) !== FALSE && $stop != 1)
                {	 
                    $stop++;
                    $i = 0; 
                    while(isset($data[$i]))
                    {
                        $data[$i] = rtrim($data[$i]);
                        ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                        $i++;
                    }
                }
                fclose($handle9);
                ?>
                </select></td>
            </tr> 
            
            <tr>
                <td>Tags:</td><td></td><td><select name="tags_col" size="1">      
                <option value="999">Exclude</option>     
                <?php           
                $handle10 = fopen("$csvfiledirectory", "r"); $stop = 0; $i = 0;
                while (($data = fgetcsv($handle10, 5000, $delimiter)) !== FALSE && $stop != 1)
                {	 
                    $stop++;
                    $i = 0; 
                    while(isset($data[$i]))
                    {
                        $data[$i] = rtrim($data[$i]);
                        ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                        $i++;
                    }
                }
                fclose($handle10);
                ?>
                </select></td>
            </tr> 
            
            <tr>
                <td>Unique:</td><td></td><td><select name="uniquecolumn_col" size="1">      
                <option value="999">Exclude</option>     
                <?php           
                $handle11 = fopen("$csvfiledirectory", "r"); $stop = 0; $i = 0;
                while (($data = fgetcsv($handle11, 5000, $delimiter)) !== FALSE && $stop != 1)
                {	 
                    $stop++;
                    $i = 0; 
                    while(isset($data[$i]))
                    {
                        $data[$i] = rtrim($data[$i]);
                        ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                        $i++;
                    }
                }
                fclose($handle11);
                ?>
                </select></td>
            </tr> 

        <tr>
            <td>Custom List 1:</td>
            <td></td>
            <td><select name="customlist1" size="1">      
                <option value="999">Exclude</option>     
                <?php           
            $handle12 = fopen("$csvfiledirectory", "r"); 

            $stop = 0;
            $i = 0;
            while (($data = fgetcsv($handle12, 5000, $delimiter)) !== FALSE && $stop != 1)// Gets CSV rows
            {	 
                $stop++;// used to limit row parsing to just 1
            
                $i = 0; 
            
                while(isset($data[$i]))
                {
                    $data[$i] = rtrim($data[$i]);
                    
                    ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                
                    $i++; // $i will equal number of columns - use to process submission
                }
            }
            
            fclose($handle12);
            ?>
            </select></td>
        </tr>
                       
        <tr>
            <td>Custom List 2:</td>
            <td></td>
            <td><select name="customlist2" size="1">      
                <option value="999">Exclude</option>     
                <?php           
            $handle13 = fopen("$csvfiledirectory", "r");
            $stop = 0;
            $i = 0;
            while (($data = fgetcsv($handle13, 5000, $delimiter)) !== FALSE && $stop != 1)// Gets CSV rows
            {	 
                $stop++;// used to limit row parsing to just 1
            
                $i = 0; 
            
                while(isset($data[$i]))
                {
                    $data[$i] = rtrim($data[$i]);
                    
                    ?><option value="<?php echo $i; ?>"><?php echo $i . ' - ' . $data[$i]; ?></option><?php
                
                    $i++; // $i will equal number of columns - use to process submission
                }
            }
            
            fclose($handle13);
            ?>
            </select></td>
        </tr>
			
                            
            <!-- Values submitted in stage 1 are stored here for passing through all stages -->
            <input name="csvfile_columntotal" type="hidden" value="<?php echo $csvfile_columntotal; ?>" />
            <input name="delimiter" type="hidden" value="<?php echo $delimiter; ?>" />
            <input name="stage" type="hidden" value="2" />
            <input name="page" type="hidden" value="new_campaign" />
            <input name="csvfiledirectory" type="hidden" value="<?php echo $csvfiledirectory; ?>" />
            <input name="camid" type="hidden" value="<?php echo $camid; ?>" />
            <input name="layoutstyle" type="hidden" value="<?php echo $layoutstyle; ?>" />
            <input name="campaigntype" type="hidden" value="<?php echo $campaigntype; ?>" />
                
            <tr>
                <td colspan="21"><p><input name="matchsubmit" class="button-primary" type="submit" value="Submit" /> Submission May Take 2-3 Minutes To Process</p></td>
            </tr>
                
        </table>
</form>
    <p>
      <?php 

}//end while rows

fclose($handle);
?>
</p>