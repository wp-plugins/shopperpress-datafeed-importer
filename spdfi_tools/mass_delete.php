<?php
# MASS DELETE
if(!empty($_POST['masspostbycatdelete_opentool']) || !empty($_POST['massdelete_deleteselectedposts']) || !empty($_POST['massdelete_deleteanyposts']) || isset($_GET['tool']) && $_GET['tool'] == 'masspostdeletebycat')
{
	# PROCESS THIS TOOLS FORM SUBMISSION
	if(!empty($_POST['massdelete_deleteselectedposts']))
	{
		// post delete requested - get delete criteria and process
		if(!isset($_POST['deletelimit'])){$deletelimit = 500;}else{$deletelimit = $_POST['deletelimit'];}
		
		foreach ( $_POST as $key => $postpart ) 
		{				
			if(is_numeric($postpart))
			{
				$string = "'showposts=-1&posts_per_page=".$deletelimit."&cat=".$postpart."');";
				
				 $myposts = query_posts($string);
				 
				foreach($myposts as $post)
				{
				
					setup_postdata($post);
					
					wp_delete_post( $post->ID );
				}
			}
		}
		
		wp_reset_query();
	}	
	?>
      
    <h4>Delete Posts By Category</h4>
    <p>Use to delete posts in a single category, enter a number around 500 at a time to avoid errors.</p>
    <form method="post" name="mpd2" action="<?php echo $_SERVER['PHP_SELF'];?>?page=tools_plus&amp;tool=masspostdelete" >
        <?php
        // get all categories and list them with check
        $categories = get_categories();
        
        $counter = 0;
        
        echo '<table>';
        
        foreach ($categories as $cat) 
        {
            echo '<tr><td><input type="checkbox" name="cats_' . $counter . '" value="' . $cat->cat_ID . '" id="CheckboxGroup1_' . $counter . '" /></td><td>' . $cat->cat_name . '</td></tr>';	
            $counter++;
        }
        
        echo '</table>';
        ?>
        <label>Post Number:<input name="deletelimit" type="text" size="4" maxlength="4" /></label>
        <input name="massdelete_deleteselectedposts" class="button-primary" type="submit" value="Delete By Category" />
    </form>
    
<?php 
}
?>