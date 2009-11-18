<?php
// no sessions are used
// check submitted values, some stages only require certain values but all values need to be pass from stage to stage

if(isset($stage1complete) && $stage1complete == true)
{
	$arrived_stage = 2;
}
elseif(isset($stage2complete) && $stage2complete == true)
{
	$arrived_stage = 3;
	if(empty($csvfile_columntotal)){ echo "Error: CSV column total was not passed to Stage " . $arrived_stage . "!"; }
	if(empty($delimiter)){ echo "Error 4: Data delimiter was not passed to Stage " . $arrived_stage . "!"; }
}
elseif(isset($stage3complete) && $stage3complete == true)
{
	$arrived_stage = 4;
	if(empty($csvfile_columntotal)){ echo "Error: CSV column total was not passed to Stage " . $arrived_stage . "!"; }
	if(empty($delimiter)){ echo "Error 4: Data delimiter was not passed to Stage " . $arrived_stage . "!"; }
}
elseif(isset($stage4complete) && $stage4complete == true)
{
	$arrived_stage = 5;
}

// check all variables that should exist and print errors
if(empty($camid)){ echo "Error 1: Campaign ID was not passed to Stage " . $arrived_stage . "!"; }
if(empty($csvfiledirectory)){ echo "Error 2: CSV file directory was not passed to Stage " . $arrived_stage . "!"; }
if(empty($layoutstyle)){ echo "Error 3: Layout file option was not passed to Stage " . $arrived_stage . "!"; }


?>