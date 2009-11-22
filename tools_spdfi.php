<style type="text/css">
<!--
.warning {
	color: #F00;
}
-->
</style>
<h2>Tools</h2>

<h3>WARNING</h3>
<h4 class="warning">The functions that can be actioned on this page are not reversable. Please take care when pressing any of the buttons below!</h4>

<?php 
include('spdfi_tools/mass_delete.php');

if(!empty($_POST['dbinstall_opentool']))
{
	include('functions/config_functions.php');
	spdfi_databaseinstallation(1);
}

if(!empty($_POST['optionsreset_opentool']))
{
	include('functions/config_functions.php');
	spdfi_optionsinstallation(1);
}

if(!empty($_POST['masspostdelete_opentool']))
{
	include('functions/tools_functions.php');
	spdfi_masspostdelete();
}

if(!empty($_POST['masstagdelete_opentool']))
{
	include('functions/tools_functions.php');
	spdfi_masstagdelete();
}

if(!empty($_POST['masspagedelete_opentool']))
{
	include('functions/tools_functions.php');
	spdfi_masspagedelete();
}

if(!empty($_POST['masscategorydelete_opentool']))
{
	include('functions/tools_functions.php');
	spdfi_masscategorydelete();
}

if(!isset($_GET['tool']))
{?>

	<div id="poststuff" class="metabox-holder">
	
		<div id="post-body">
			<div id="post-body-content">
					
				<div class="postbox">
                
					<h3 class='hndle'><span>Action Database Re-installation</span></h3>

					<div class="inside">
                        <p>This tool will delete all your CSV 2 POST PLUS campaign settings and records data, use with caution.</p>
                        <form method="post" name="tool2" action="<?php echo $_SERVER['PHP_SELF'];?>?page=tools_spdfi&amp;tool=databasereinstallation" >
                            <input name="dbinstall_opentool" class="button-primary" type="submit" value="Submit" />
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
                
					<h3 class='hndle'><span>Action Options/Settings Reset</span></h3>

					<div class="inside">
                        <p>This will reset all options and configuration settings to the installation state. </p>
                        <form method="post" name="tool2" action="<?php echo $_SERVER['PHP_SELF'];?>?page=tools_spdfi&amp;tool=optionsreset" >
                            <input name="optionsreset_opentool" class="button-primary" type="submit" value="Submit" />
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
                
					<h3 class='hndle'><span>Open Post Delete By Category</span></h3>

					<div class="inside">
                        <p>Delete all the posts from selected categories only.</p>
                        <form method="post" name="tool1" action="<?php echo $_SERVER['PHP_SELF'];?>?page=tools_spdfi&amp;tool=masspostdeletebycat" >
                            <input name="masspostbycatdelete_opentool" class="button-primary" type="submit" value="Submit" />
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
                
					<h3 class='hndle'><span>Action Mass Post Delete</span></h3>

					<div class="inside">
                        <p>Delete all posts from your blog.</p>
                        <form method="post" name="tool1" action="<?php echo $_SERVER['PHP_SELF'];?>?page=tools_spdfi&amp;tool=masspostdelete" >
                            <input name="masspostdelete_opentool" class="button-primary" type="submit" value="Submit" />
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
                
					<h3 class='hndle'><span>Action Mass Tag Delete</span></h3>

					<div class="inside">
                        <p>Delete all tags from your blog.</p>
                        <form method="post" name="tool1" action="<?php echo $_SERVER['PHP_SELF'];?>?page=tools_spdfi&amp;tool=masstagdelete" >
                            <input name="masstagdelete_opentool" class="button-primary" type="submit" value="Submit" />
                        </form>                    
                    </div>                    
						
				</div>
    	</div>
    </div>
    </div>

	<?php if(get_option('spdfi_demomode') == 0){?>
	<div id="poststuff" class="metabox-holder">
	
		<div id="post-body">
			<div id="post-body-content">
					
				<div class="postbox">
                
					<h3 class='hndle'><span>Action Mass Page Delete</span></h3>

					<div class="inside">
                        <p>Delete all pages from your blog.</p>
                        <form method="post" name="tool1" action="<?php echo $_SERVER['PHP_SELF'];?>?page=tools_spdfi&amp;tool=masspagedelete" >
                            <input name="masspagedelete_opentool" class="button-primary" type="submit" value="Submit" />
                        </form>                    
                    </div>                    
						
				</div>
    	</div>
    </div>
    </div>
    <?php }?>
    
	<div id="poststuff" class="metabox-holder">
	
		<div id="post-body">
			<div id="post-body-content">
					
				<div class="postbox">
                
					<h3 class='hndle'><span>Action Mass Category Delete</span></h3>

					<div class="inside">
                        <p>Delete all categories from your blog.</p>
                        <form method="post" name="tool1" action="<?php echo $_SERVER['PHP_SELF'];?>?page=tools_spdfi&amp;tool=masscategorydelete" >
                            <input name="masscategorydelete_opentool" class="button-primary" type="submit" value="Submit" />
                        </form>                    
                    </div>                    
						
				</div>
    	</div>
    </div>      
    </div>      
<?php 
}
else
{?>
	<br />
	<form method="post" name="viewtools" action="<?php echo $_SERVER['PHP_SELF'];?>?page=tools_spdfi" >
		<input name="viewtools_opentool" class="button-primary" type="submit" value="Back To Tools Page" />
	</form>
    
<?php
}
?>
