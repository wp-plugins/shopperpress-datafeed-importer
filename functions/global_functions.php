<?php
function determine_delimiter_wtg($filename)
{
	 $fileChunks = explode(".", $filename);
     return get_option($fileChunks[0]); 
}

function spdfi_getcsvfilesdir()
{
	return WP_CONTENT_DIR . '/spdfifiles/';
}

function spdfi_tooltip($tooltip)
{
	$tooltip_setting = get_option('spdfi_tooltipsonoff');
	if($tooltip_setting == 1)
	{
		echo '<a href="#" class="tt">?<span class="tooltip"><span class="top"></span><span class="middle">' . $tooltip . '</span><span class="bottom"></span></span></a>';	
	}
}

function spdfi_queryresult($q)
{
	if(!$q){ return false; }else{ return true; }
}

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
			<span class="problemred">ERROR - Folder is not present and cannot be created or is just not writeable! You may need to create the directory manually. Example: http://www.domainname.com/wp-content/uploads/spdfifiles</span>
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
	$title = truncate_string_spdfi(seo_simple_strip_tags_spdfi($str), $length);
	if (strlen($str) > strlen($title)) 
	{$title .= "...";}
	return $title;
}
/* Example:	<title>WebTechGlobal: <?php echo create_meta_title($pagedesc, $met_tit_len);?></title> */

//STRIP HTML, TRUNCATE, CREATE DESCRIPTION
function create_meta_description_spdfi($str, $length)
{
	$meta_description = truncate_string_spdfi(seo_simple_strip_tags_spdfi($str), $length);
	if (strlen($str) > strlen($meta_description)) {$meta_description .= "...";}
	return $meta_description;
}
/* Example:	<meta name="description" content="<?php echo create_meta_description($pagedesc, $met_des_len);?>" /> */


//STRIP HTML,TRUNCATE,CREATE KEYWORDS
function create_meta_keywords_spdfi($str, $length) 
{
	return 'paid,edition,only';
}

// not only remove specified words but removes numeric only values if $tagsnumeric is set to 1 and not 0
function create_tags_spdfi($str, $length, $tagsnumeric) 
{
	return 'paid,edition,only';
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
// splitS word, that is longer than $cols and is outside
// HTML tags, by the string $cut. Lines with whitespace in them are ok, only
// single words over $cols length are split. (&shy; = safe-hyphen)
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
?>