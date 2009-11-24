<?php
# STAGE 2 SUBMISSION MADE - PROCESS POST VARIABLES, THEY SHOULD BE POPULATED AND VALIDATED BEFORE THIS SUBMISSION
$camid = $_POST['camid'];
$csvfiledirectory = $_POST['csvfiledirectory'];
$csvfile_columntotal = $_POST['csvfile_columntotal'];
$delimiter = $_POST['delimiter'];
$layoutstyle = $_POST['layoutstyle'];

// optional submission data - set column to 999 to indicate user opted not to use it
if(isset($_POST['price_col'])){$price_col = $_POST['price_col'];}else{$price_col = 999;}
if(isset($_POST['oldprice_col'])){$oldprice_col = $_POST['oldprice_col'];}else{$oldprice_col = 999;}
if(isset($_POST['image_col'])){$image_col = $_POST['image_col'];}else{$image_col = 999;}
if(isset($_POST['images_col'])){$images_col = $_POST['images_col'];}else{$images_col = 999;}
if(isset($_POST['thumbnail_col'])){$thumbnail_col = $_POST['thumbnail_col'];}else{$thumbnail_col = 999;}
if(isset($_POST['shipping_col'])){$shipping_col = $_POST['shipping_col'];}else{$shipping_col = 999;}
if(isset($_POST['featured_col'])){$featured_col = $_POST['featured_col'];}else{$featured_col = 999;}
if(isset($_POST['excerpt_col'])){$excerpt_col = $_POST['excerpt_col'];}else{$excerpt_col = 999;}
if(isset($_POST['keywords_col'])){$keywords_col = $_POST['keywords_col'];}else{$keywords_col = 999;}
if(isset($_POST['tags_col'])){$tags_col = $_POST['tags_col'];}else{$tags_col = 999;}
if(isset($_POST['uniquecolumn_col'])){$uniquecolumn_col = $_POST['uniquecolumn_col'];}else{$uniquecolumn_col = 999;}


# ENTER CSV FILE COLUMN TOTAL TO MAIN CAMPAIGN TABLE FOR VALIDATION LATER
$sqlQuery = "UPDATE " .	$wpdb->prefix . "spdfi_campaigns SET 
price_col = '$price_col',
oldprice_col = '$oldprice_col',
image_col = '$image_col',
images_col = '$images_col',
thumbnail_col = '$thumbnail_col',
shipping_col = '$shipping_col',
featured_col = '$featured_col',
excerpt_col = '$excerpt_col',
keywords_col = '$keywords_col',
tags_col = '$tags_col',
uniquecolumn = '$uniquecolumn_col'
WHERE id = '$camid'";

$wpdb->query($sqlQuery);
		
$i = 0;	

function spdfi_countcsvrows($handle,$delimiter)
{
	// count total number of rows
	$rows_total = 1;
	while (($data = fgetcsv($handle, 5000, $delimiter)) !== FALSE)
	{
		$rows_total++;// used to limit row parsing to just 1
	}//end while rows
	
	return $rows_total;// will return data rows total not
}

// put every single column into relationship table and assume a match by entering the column title has postpart
// used for the WYSIWYG Editor support
$handle = fopen("$csvfiledirectory", "r");

$stop_rows = 0;

while (($data = fgetcsv($handle, 5000, $delimiter)) !== FALSE && $stop_rows != 1)// get first csv row
{	 
	$stop_rows++;// used to limit row parsing to just 1
		   
	while(isset($data[$i]))
	{
		$data[$i] = rtrim($data[$i]);
		
		# THIS POST IS A COLUMN TO POST RELATIONSHIP
		$sqlQuery = "INSERT INTO " .
		$wpdb->prefix . "spdfi_relationships(camid, csvcolumnid, postpart) VALUES ('$camid', '$i','$data[$i]')";
		$wpdb->query($sqlQuery);
		
		$i++; // $i will equal number of columns - use to process submission
	}
}//end while rows

$rows_total = spdfi_countcsvrows($handle,$delimiter);		

fclose($handle);

$stage2complete = true;

# UPDATE CAMPAIGN STAGE COUNTER
$sqlQuery = "UPDATE " .	$wpdb->prefix . "spdfi_campaigns SET stage = '3',csvrows = '$rows_total' WHERE id = '$camid'";	
$wpdb->query($sqlQuery);

?>