<?php
// simply returns the passed filename with prepended profile name tag
function spdfi_csvfilesprofilename($filename)
{
   	return 'spdfiprofile_' . $filename;
}

// will return the profiles entire array from wordpress options table
function spdfi_getcsvprofile( $filename )
{
   	$profileoptionname = spdfi_csvfilesprofilename($filename);
	return get_option( $profileoptionname );			
}

// function initiates a newly uploaded csv files profile in the wordpress options table
function spdfi_createcsvprofile( $filename )
{
    global $wpdb;

	// create the initial wysiwyg editor entry so that only updates are required afterwards
    $sqlQuery = "INSERT INTO " . $wpdb->prefix . "spdfi_layouts (name,code,inuse,type,csvfile,wysiwyg_content,wysiwyg_title) VALUES ('$filename','TBC',0,0,'$filename','TBC','TBC')";
    $wpdb->query( $sqlQuery );
	
	// create profile option for the csv file
	$optionname = spdfi_csvfilesprofilename($filename);
			
	$specialfunctions['columns'] = array(
		'price_column' => 'NA',
		'old_price_column' => 'NA',
		'image_column' => 'NA',
		'images_column' => 'NA',
		'thumbnail_column' => 'NA',
		'qty_column' => 'NA',
		'customlist1_column' => 'NA',
		'customlist2_column' => 'NA',
		'shipping_column' => 'NA',
		'featured_column' => 'NA',
		'excerpts_column' => 'NA',
		'tags_column' => 'NA',
		'uniqueid_column' => 'NA',
		'urlcloaking_column' => 'NA',
		'permalink_column' => 'NA',
		'dates_column' => 'NA'
	);
				
	// the state is a boolean switch which will be used to switch the special function on or off per campaign on stage 2
	$specialfunctions['states'] = array(
		'price_state' => 'OFF',
		'old_price_state' => 'OFF',
		'image_state' => 'OFF',
		'images_state' => 'OFF',
		'thumbnail_state' => 'OFF',
		'qty_state' => 'OFF',
		'customlist1_state' => 'OFF',
		'customlist2_state' => 'OFF',
		'shipping_state' => 'OFF',
		'featured_state' => 'OFF',
		'excerpt_state' => 'OFF',
		'tags_state' => 'OFF',
		'uniqueid_state' => 'OFF',
		'urlcloaking_state' => 'OFF',
		'permalink_state' => 'OFF',
		'dates_state' => 'OFF'
	);
	
	// csv file specific format information
	$specialfunctions['format'] = array(
		'delimiter' => ',',
		'columns_pear' => '1',
		'quote_pear' => '"'
	);
	
	add_option( $optionname, $specialfunctions );	
}

function spdfi_getcsvfilesdir(){	return WP_CONTENT_DIR . '/spdfifiles/'; }

function spdfi_queryresult($q){ if(!$q){ return false; }else{ return true; }}

// check and deal with safe mode status
function spdfi_checksafemodestatus()
{
	$server_safemode = ini_get('safe_mode');
	if ($server_safemode == 1) 
	{
		return '<span class="okgreen">Server Safe Mode Is On</span>';
	} 
	else 
	{
		return '<span class="okgreen">Server Safe Mode Is Off</span>';
	}
}

// create csv files storage folder
function spdfi_doesexist_csvfilesfolder()
{				
	$filename = spdfi_getcsvfilesdir();
	
	if (file_exists($filename)) 
	{
		if (is_writable($filename)) 
		{
			return '<span class="okgreen">OK - Folder is present and writeable</span>';
		} 
		else
		{
			return '<span class="okgreen">ERROR - Folder is present but not writeable! This may be a permissions issues.</span>';
		}
	} 
	else 
	{
		$outcome = @mkdir($filename, 0777);
		
		if(!$outcome)
		{	
			return '
			<span class="problemred">ERROR - Folder is not present and cannot be created or is just not writeable! You may need to create the directory manually. Example: http://www.domainname.com/wp-content/spdfifiles</span>
			';
		}
		else
		{
			return '<span class="okgreen">OK - Folder has just been created for you.</span>';
		}
	}
}

function spdfi_autolineendings_status()
{
	$status = ini_get('auto_detect_line_endings');
	
	if($status == 1)
	{
		return '<span class="okgreen">Currently ON</span>';
	}
	elseif($status == 0)
	{
		return '<span class="problemred">Currently OFF! May cause problems uploading csv files from a MAC.</span>';
	}
}


# USED TO CHECK ALLOWED FILE EXTENSIONS
function isAllowedExtension_spdfi($fileName)
{
	$allowedExtensions = array("csv", "CSV");
	return in_array(end(explode(".", $fileName)), $allowedExtensions);
}
	
//STRIP HTML, TRUNCATE, CREATE TITLE
function create_meta_title_spdfi($str, $length) 
{
	$title = truncate_string_wtg_spdfiplus(seo_simple_strip_tags_wtg_spdfiplus($str), $length);
	if (strlen($str) > strlen($title)) 
	{$title .= "...";}
	return $title;
}
/* Example:	<title>WebTechGlobal: <?php echo create_meta_title($pagedesc, $met_tit_len);?></title> */

//STRIP HTML, TRUNCATE, CREATE DESCRIPTION
function createexcerpt_spdfi($str, $length)
{
	$meta_description = truncate_string_wtg_spdfiplus(seo_simple_strip_tags_wtg_spdfiplus($str), $length);
	if (strlen($str) > strlen($meta_description)) {$meta_description .= "...";}
	return $meta_description;
}
/* Example:	<meta name="description" content="<?php echo create_meta_description($pagedesc, $met_des_len);?>" /> */

// not only remove specified words but removes numeric only values if $tagsnumeric is set to 1 and not 0
function createtags_spdfi($str, $length, $tagsnumeric) 
{
	$exclude = array(get_option('spdfi_exclusions'));
	$splitstr = @explode(" ", truncate_string_wtg_spdfiplus(seo_simple_strip_tags_wtg_spdfiplus(str_replace(array(",",".")," ", $str)), $length));
	$new_splitstr = array();
	foreach ($splitstr as $spstr) 
	{
		if($tagsnumeric == 1)
		{	// numeric only values will be removed
			if (strlen($spstr) > 2 && !(in_array(strtolower($spstr), $new_splitstr)) && !(in_array(strtolower($spstr), $exclude)) && !is_numeric($spstr)) 
			{$new_splitstr[] = strtolower($spstr);}
		}
		elseif($tagsnumeric == 0)
		{	// numeric only values will be included
			if (strlen($spstr) > 2 && !(in_array(strtolower($spstr), $new_splitstr)) && !(in_array(strtolower($spstr), $exclude))) 
			{$new_splitstr[] = strtolower($spstr);}
		}
	}
	return @implode(", ", $new_splitstr);
}

//STRIP HTML TAGS - CALLED WITHIN THE OTHER FUNCTIONS
function seo_simple_strip_tags_spdfi($str)
{
	$untagged = "";
	$skippingtag = false;
	for ($i = 0; $i < strlen($str); $i++) 
	{
		if (!$skippingtag) 
		{
			if ($str[$i] == "<") 
			{
				$skippingtag = true;
			} 
			else
			{
				if ($str[$i]==" " || $str[$i]=="\n" || $str[$i]=="\r" || $str[$i]=="\t") 
				{
					$untagged .= " ";
				}
				else
				{
					$untagged .= $str[$i];
				}
			}
		}
		else
		{
			if ($str[$i] == ">") 
			{
				$untagged .= " ";
				$skippingtag = false;
			}		
		}
	}	
	$untagged = preg_replace("/[\n\r\t\s ]+/i", " ", $untagged); // remove multiple spaces, returns, tabs, etc.
	if (substr($untagged,-1) == ' ') { $untagged = substr($untagged,0,strlen($untagged)-1); } // remove space from end of string
	if (substr($untagged,0,1) == ' ') { $untagged = substr($untagged,1,strlen($untagged)-1); } // remove space from start of string
	if (substr($untagged,0,12) == 'DESCRIPTION ') { $untagged = substr($untagged,12,strlen($untagged)-1); } // remove 'DESCRIPTION ' from start of string
	return $untagged;
}


//SPLIT WORDS (\W) BY DELIMITERS, ucfirst THEN RECOMBINE WITH DELIMITERS
function ucfirst_title_spdfi($string) 
{
	$temp = preg_split('/(\W)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE );
	foreach ($temp as $key=>$word) 
	{
		$temp[$key] = ucfirst(strtolower($word));
	}
	$new_string = join ('', $temp);
	// Do the Search and Replacements on the $new_string.
	$search = array (' And ',' Or ',' But ',' At ',' In ',' On ',' To ',' From ',' Is ',' A ',' An ',' Am ',' For ',' Of ',' The ',"'S", 'Ac/');
	$replace = array (' and ',' or ',' but ',' at ',' in ',' on ',' to ',' from ',' is ',' a ',' an ',' am ',' for ',' of ',' the ',"'s", 'AC/');
	$new_string = str_replace($search, $replace, $new_string);
	// Several Special Replacements ('s, McPherson, McBain, etc.) on the $new_string.
	$new_string = preg_replace("/Mc([a-z]{3,})/e", "\"Mc\".ucfirst(\"$1\")", $new_string);
	// Another Strange Replacement (example: "Pure-Breed Dogs: the Breeds and Standards") on the $new_string.
	$new_string = preg_replace("/([:;])\s+([a-zA-Z]+)/e", "\"$1\".\" \".ucfirst(\"$2\")", $new_string);
	// If this is a very low string ( > 60 char) then do some more replacements.
	if (strlen($new_string > 60)) 
	{
		$search = array (" With "," That ");
		$replace = array (" with "," that ");
		$new_string = str_replace($search, $replace, $new_string);
	}
	return ($new_string);
}

//WORD WRAP, EXCLUDES HTML IN COUNT, SET MAX COLUMNS/CHARACTERS
function wordwrap_excluding_html_spdfi($str, $cols = 30, $cut = "&shy;")
{
	$len = strlen($str);
	$tag = 0;
	for ($i = 0; $i < $len; $i++) 
	{
		$chr = $str[$i];
		if ($chr == '<') 
		{$tag++;} 
		elseif($chr == '>')
		{$tag--;}
		elseif((!$tag) && ($chr==" " || $chr=="\n" || $chr=="\r" || $chr=="\t"))
		{$wordlen = 0;}
		elseif(!$tag)
		{$wordlen++;}
		if ((!$tag) && ($wordlen) && (!($wordlen % $cols))) 
		{$chr .= $cut;}
		$result .= $chr;
	}
	return $result;
}

//TRUNCATE STRING TO LENGTH, EXCLUDING HTML IN LENGTH COUNT BUT KEEPS THE HTML
function truncate_string_excluding_html_spdfi($str, $len = 150)
{
	$wordlen = 0; // Total text length.
	$resultlen = 0; // Total length of HTML and text.
	$len_exceeded = false;
	$cnt = 0;
	$splitstr = array (); // String split by HTML tags including delimiter.
	$open_tags = array(); // Assoc. Array for Simple HTML Tags
	$str = str_replace(array("\n","\r","\t"), array (" "," "," "), $str); // Replace returns/tabs with spaces
	$splitstr = preg_split('/(<[^>]*>)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE );
	//var_dump($splitstr);
	if (count($splitstr) > 0 && strlen($str) > $len) 
	{
		while ($wordlen <= $len && $cnt <= 200 &&!$len_exceeded) 
		{
			$part = $splitstr[$cnt];
			if (preg_match('/^<[A-Za-z]{1,}/', $part)) 
			{$open_tags[strtolower(substr($match[0],1))]++;} 
			else if(preg_match('/^<\/[A-Za-z]{1,}/', $part))
			{$open_tags[strtolower(substr($match[0],2))]--;}
			else if(strlen($part) > $len-$wordlen)
			{ // truncate remaining length
				$tmpsplit = explode("\n", wordwrap($part, $len-$wordlen));
				$part = $tmpsplit[0]; // Define the truncated part.
				$len_exceeded = true;
				$wordlen += strlen($part);
			}else{$wordlen += strlen($part);}
			$result .= $part; // Add the part to the $result
			$resultlen = strlen($result);
			$cnt++;
		}
		
		// Close the open HTML Tags (Simple Tags Only!). This excludes IMG and LI tags.
		foreach ($open_tags as $key=>$value) 
		{
			if ($value > 0 && $key!= "" && $key!= null && $key!= "img" && $key!= "li") 
			{for ($i=0; $i<$value; $i++) { $result .= "</".$key.">"; }}
		}//end foreach
	}
	else
	{
		$result = $str;
	}//end if count
	return $result;
}

//TRUNCATE STRING TO SPECIFIED LENGTH - USED IN OTHER FUNCTIONS
function truncate_string_spdfi($string, $length = 70)
{
	if (strlen($string) > $length) 
	{
		$split = preg_split("/\n/", wordwrap($string, $length));
		return ($split[0]);
	}
	return ($string);
}

function webtechglobal_replacespaces_spdfi($v)
{
	return(str_replace(array(' ','  ','   ','     ','      ','       ','        ','         ',), '-', $v));
	return $v;
}

function webtechglobal_replacespecial_spdfi($v)
{
	return(str_replace(array('&reg;','and'), '', $v));
	return $v;
}

function webtechglobal_clean_desc_spdfi($text)
{
	$code_entities_match = array('  ','--','&quot;',"'",'"');
	$code_entities_replace = array(' ','-','','','');
	$text = str_replace($code_entities_match, $code_entities_replace, $text);
	return $text;
}

function webtechglobal_clean_title_spdfi($text)
{
	$code_entities_match = array('  ','--','&quot;',"'");
	$code_entities_replace = array(' ','-','','');
	$text = str_replace($code_entities_match, $code_entities_replace, $text);
	return $text;
}

function webtechglobal_clean_url_spdfi($text)
{
	$text=strtolower($text);
	$code_entities_match = array(' ','  ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=');
	$code_entities_replace = array('-','-','-','','','','','','','','','','','','','','','','','','','','','','','','');
	$url = str_replace($code_entities_match, $code_entities_replace, $text);
	return $url;
}

function webtechglobal_clean_keywords_spdfi($text)
{
	$text=strtolower($text);
	$code_entities_match = array('--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",'.','/','*','+','~','`','=');
	$code_entities_replace = array('-','','','','','','','','','','','','','','','','','','','','','','','','');
	$text = str_replace($code_entities_match, $code_entities_replace, $text);
	return $text;
}

function get_categories_fordropdownmenu_spdfi()
{		
	get_categories('hide_empty=0show_option_all=&title_li=');
	$test = get_categories('hide_empty=0&echo=0&show_option_none=&style=none&title_li=');
	foreach($test as $category) 
	{   
		if($category->term_id == get_option('spdfi_defaultcatparent')){$selected = 'selected="selected"';}else{$selected="";}
		?>
   	 	<option value="<?php echo $category->term_id; ?>" <?php echo $selected; ?>><?php echo $category->cat_name;?></option>
		<?php
	}      
} 

function spdfi_opt($possible,$actual){	if($possible == $actual){return 'selected="selected"';}}

function spdfi_datepicker_nonejavascript()
{
	$monthstart = get_option('spdfi_randomdate_monthstart');
    $daystart = get_option('spdfi_randomdate_daystart');
    $yearstart = get_option('spdfi_randomdate_yearstart');
    $monthend = get_option('spdfi_randomdate_monthend');
    $dayend = get_option('spdfi_randomdate_dayend');
    $yearend = get_option('spdfi_randomdate_yearend');
	?>
    
	<strong>Start Date: </strong>    
    
    <select name="spdfi_randomdate_monthstart">
        <option <?php echo spdfi_opt('01',$monthstart)?> value="01">January</option>
        <option <?php echo spdfi_opt('02',$monthstart)?> value="02">Febuary</option>
        <option <?php echo spdfi_opt('03',$monthstart)?> value="03">March</option>
        <option <?php echo spdfi_opt('04',$monthstart)?> value="04">April</option>
        <option <?php echo spdfi_opt('05',$monthstart)?> value="05">May</option>
        <option <?php echo spdfi_opt('06',$monthstart)?> value="06">June</option>
        <option <?php echo spdfi_opt('07',$monthstart)?> value="07">July</option>
        <option <?php echo spdfi_opt('08',$monthstart)?> value="08">August</option>
        <option <?php echo spdfi_opt('09',$monthstart)?> value="09">September</option>
        <option <?php echo spdfi_opt('10',$monthstart)?> value="10">October</option>
        <option <?php echo spdfi_opt('11',$monthstart)?> value="11">November</option>
        <option <?php echo spdfi_opt('12',$monthstart)?> value="12">December</option>
    </select>
    
    <select name="spdfi_randomdate_daystart">
    	<?php
			$counter = 1;
			while($counter < 32)
			{
				$code = '<option ';
				$code .= spdfi_opt($counter,$daystart);
				$code .= ' value="' . $counter .'">' . $counter .'</option>';
				echo $code;
				$counter++;
			}
		?>
    </select>
    
    <select name="spdfi_randomdate_yearstart">
    	<?php
			$counter = 1990;
			while($counter < 2021)
			{
				$code = '<option ';
				$code .= spdfi_opt($counter,$yearstart);
				$code .= ' value="' . $counter .'">' . $counter .'</option>';
				echo $code;
				$counter++;
			}
		?>
    </select>

	<br />

	<strong>End Date:</strong>

    <select name="spdfi_randomdate_monthend">
        <option <?php spdfi_opt('01',$monthend)?> value="01">January</option>
        <option <?php spdfi_opt('02',$monthend)?> value="02">Febuary</option>
        <option <?php spdfi_opt('03',$monthend)?> value="03">March</option>
        <option <?php spdfi_opt('04',$monthend)?> value="04">April</option>
        <option <?php spdfi_opt('05',$monthend)?> value="05">May</option>
        <option <?php spdfi_opt('05',$monthend)?> value="06">June</option>
        <option <?php spdfi_opt('07',$monthend)?> value="07">July</option>
        <option <?php spdfi_opt('08',$monthend)?> value="08">August</option>
        <option <?php spdfi_opt('09',$monthend)?> value="09">September</option>
        <option <?php spdfi_opt('10',$monthend)?> value="10">October</option>
        <option <?php spdfi_opt('11',$monthend)?> value="11">November</option>
        <option <?php spdfi_opt('12',$monthend)?> value="12">December</option>
    </select>
    
    <select name="spdfi_randomdate_dayend">
    	<?php
			$counter = 1;
			while($counter < 32)
			{
				$code = '<option ';
				$code .= spdfi_opt($counter,$dayend);
				$code .= ' value="' . $counter .'">' . $counter .'</option>';
				echo $code;
				$counter++;
			}
		?>
    </select>
    
    <select name="spdfi_randomdate_yearend">
    	<?php
			$counter = 1990;
			while($counter < 2021)
			{
				$code = '<option ';
				$code .= spdfi_opt($counter,$yearend);
				$code .= ' value="' . $counter .'">' . $counter .'</option>';
				echo $code;
				$counter++;
			}
		?>
    </select>

<?php 
}
?>