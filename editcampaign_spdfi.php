<h2> Pro Campaign Management</h2>
<p>Here you can manage your campaigns including pause, start or undo them.</p>

<?php

global $wpdb;

require('functions/manag_functions.php');
if(isset($_GET['settings']) && isset($_GET['id']))
{
	if($_GET['settings'] == 'updatesave')
	{
		$camid = $_GET['id'];
		
		$allowupdate = $_POST['allowupdate'];

		$sqlQuery = "UPDATE " .	$wpdb->prefix . "spdfi_campaigns SET allowupdate = '$allowupdate' WHERE id = '$camid'";

		$r = $wpdb->query( $sqlQuery );
		if( !$r )
		{
			//error_spdfi(__LINE__,__FILE__,'Custom Post Layout could not be saved! Name: '. $name .'');
			messagebox_spdfi('infoSmall', 'You did not change any settings and so nothing was saved!');
		}
		else
		{
			messagebox_spdfi('successSmall', 'Success - Your Campaign Update settings were saved!');
		}
	}
}

if(isset($_GET['action']) && isset($_GET['id']))
{
	$camid = $_GET['id'];

	# PROCESS ACTION
	if($_GET['action'] == 'pause')
	{
		echo spdfi_pausecampaign($camid);
	}
	elseif($_GET['action'] == 'start')
	{
		echo spdfi_startcampaign($camid);		
	}
	elseif($_GET['action'] == 'delete')
	{
		echo spdfi_deletecampaign($camid);
	}	
	elseif($_GET['action'] == 'view')
	{ 
		// get all information relating to selected campaign
		$r = $wpdb->get_row("SELECT * FROM " .$wpdb->prefix . "spdfi_campaigns WHERE id = '$camid'");
		if($r)
		{
			// dispay retreived information
			echo '<h3>Campaign Management for ' . $r->camname . '</h3>';
			
			?>

            <div id="poststuff" class="metabox-holder">
                <div id="post-body">
                    <div id="post-body-content">
                        <div class="postbox">
                        
                            <h3 class='hndle'><span>Campaign Idenfification</span></h3>
                            <div class="inside">
                                <p><strong>ID:</strong> <?php echo $r->id;?></p>
                                <p><strong>Name:</strong> <?php echo $r->camname;?></p>
                                <p><strong>File:</strong> <?php echo $r->camfile;?></p>
                                <p><strong>CSV Columns:</strong> <?php echo $r->csvcolumns;?></p>
                                <p><strong>CSV Rows:</strong> <?php echo $r->csvrows;?></p>
                            </div>   
                                              
                        </div>
                    </div>
                </div>
            </div>
            
              <div id="poststuff" class="metabox-holder">
                <div id="post-body">
                    <div id="post-body-content">
                        <div class="postbox">
                        
                            <h3 class='hndle'><span>Campaign Status &amp; Statistics</span></h3>
                            <div class="inside">
                                <p><strong>Status:</strong> <?php echo spdfi_camman_stage($r->stage);?></p>
                                <p><strong>Posts Created:</strong> <?php echo $r->posts;?></p>
                                <p><strong>Rows Dropped:</strong> <?php echo $r->droppedrows;?></p>
                                <p><strong>Updated Posts:</strong> <?php echo $r->updatedposts;?></p>
                                <p></p>
                            </div>   
                                              
                        </div>
                    </div>
                </div>
            </div>    
                          <div id="poststuff" class="metabox-holder">
                <div id="post-body">
                    <div id="post-body-content">
                        <div class="postbox">
                        
                            <h3 class='hndle'><span>Campaign Updating</span></h3>
                            <div class="inside">
                                 
   								 <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=manage_campaigns_spdfi&id=<?php echo $camid; ?>&action=view&settings=updatesave">
                                 Update Activation:
                                    <select name="allowupdate" size="1">
                                        <option value="0" <?php if($r->allowupdate == '0'){echo 'selected="selected"';}?>>Disallow Updates</option>
                                        <option value="1" <?php if($r->allowupdate == '1'){echo 'selected="selected"';}?>>Allow Updates</option>
                                    </select>
                                    <br />
                                    <br />
                                    <input type="submit" class="button-primary" value="Save Update Settings" />
                                </form>
                                
                                <?php
									if($r->phase == '0')
									{
										echo '<p>Campaign Updating is not currently ongoing, this campaign may be on its initial import stage</p>';
									}
									elseif($r->phase == '1')
									{
										echo '<p>Campaign Updating is currently being performed on your blog</p>';
									}
								?>
                                <p><strong>Updated Posts:</strong> <?php echo $r->updatedposts;?></p>
                            </div>   
                                              
                        </div>
                    </div>
                </div>
            </div>     
                        
            <div id="poststuff" class="metabox-holder">
                <div id="post-body">
                    <div id="post-body-content">
                        <div class="postbox">
                        
                            <h3 class='hndle'><span>Campaign Settings</span></h3>
                            <div class="inside">
                                <p><strong>Process:</strong> <?php echo spdfi_camman_process($r->process);?></p>
                                <p><strong>Category Column (Primary):</strong> <?php echo $r->filtercolumn;?></p>
                                <p><strong>Category Column (Sub 1):</strong> <?php echo $r->filtercolumn2;?></p>
                                <p><strong>Category Column (Sub 2):</strong> <?php echo $r->filtercolumn3;?></p>
                                <p><strong>Layout File:</strong> <?php echo $r->layoutfile;?></p>
                                <p><strong>Custom Field Method:</strong> <?php echo $r->customfieldsmethod;?></p>
                                <p><strong>Keyword Column:</strong> <?php echo $r->keywordcolumn;?></p>
                                <p><strong>Description Column:</strong> <?php echo $r->descriptioncolumn;?></p>
                                <p><strong>Tags Column:</strong> <?php echo $r->tagscolumn;?></p>
                                <p><strong>Delimiter:</strong> <?php echo $r->delimiter;?></p>
                                <p><strong>Hits/Posts Ratio:</strong> <?php echo $r->ratio;?></p>
                                <p><strong>Post Status:</strong> <?php echo $r->poststatus;?></p>
                                <p><strong>Unique Column:</strong> <?php echo $r->uniquecolumn;?></p>
                                <p><strong>URL Cloaked Column:</strong> <?php echo $r->primaryurlcloak;?></p>
                                <p><strong>Random Date Option:</strong> <?php echo $r->randomdate;?></p>
                                <p><strong>Posts Per Hour (Scheduled Only):</strong> <?php echo $r->scheduledvalue;?></p>
                            </div>   
                                              
                        </div>
                    </div>
                </div>
            </div>
			
			<?php 
		}
		else
		{
			messagebox_shdfi('errorSmall', 'Failed To Retrieve Campaign Details!');
		}	
	}
}
else
{
	$c = $wpdb->get_results("SELECT id,camname,stage FROM " .$wpdb->prefix . "spdfi_campaigns");
	
	?>

	<div id="poststuff" class="metabox-holder">
	
		<div id="post-body">
			<div id="post-body-content">
				<div class="postbox">
                
					<h3 class='hndle'><span>Current Campaigns</span></h3>
					<div class="inside">
                    <form>
                        <table width="667">
                            <tr>
                            <td width="56" height="23"><strong>ID</strong></td>
                            <td width="167"><strong>Campaign Name</strong></td>
                            <td width="164"></td>
                            <td width="94"></td>
                            <td width="101"></td>
                            <td width="104"></td>
                            </tr>
                            
                        <?php
                        foreach ($c as $v)
                        {?>
                    
                            <tr>
                                <td><?php echo $v->id; ?></td>
                                <td><?php echo $v->camname; ?></td>
                                <?php spdfi_campaignview($v->camname,$v->id); ?>
                                <?php spdfi_startpausecancelled($v->stage,$v->camname,$v->id); ?>
                                <?php spdfi_campaigndelete($v->id); ?>
                            </tr>
                            
                       <?php }?>  
                          
                        </table>
                    </form>
                     </div>   
                                      
				</div>
            </div>
        </div>
    </div>
    

	<div id="poststuff" class="metabox-holder">
		<div id="post-body">
			<div id="post-body-content">
				<div class="postbox">
                
					<h3 class='hndle'><span>Active Campaigns Report Coming Soon!</span></h3>
					<div class="inside">
					  <ul>
					    <li>
				        Last campaign that ran.</li>
					    <li>Number of posts created on the hit or schedule.</li>
					    <li>Last 10 post id's created&nbsp;by the last run campaign.</li>
					    <li>Speed of processing</li>
					    <li>Number of duplicates found from csv file on last processing.</li>
				      </ul>
					</div>   
                                      
				</div>
            </div>
        </div>
    </div>
    
<?php
}
?>
