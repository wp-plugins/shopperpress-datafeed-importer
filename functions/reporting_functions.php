<?php
//use to display any text message with any style
function messagebox_spdfi($style, $message){echo '<div class="'.$style.'">'.$message.'</div>';}

function error_spdfi($line,$file,$my_error)
{
	global $wpdb;

	$wpdb->query("SELECT id FROM " .$wpdb->prefix . "spdfi_reports ORDER BY id DESC LIMIT 10");
	$c = $wpdb->num_rows;
	
	if($c >= 1)
	{
		// do not insert in order to avoid too many duplicate errors
	}
	else
	{
		$sqlQuery = "INSERT INTO " . $wpdb->prefix . "spdfi_reports (line,file,my_error,date) VALUES ('$line','$file','$my_error',NOW())";
		$wpdb->query($sqlQuery);
		
		emailerrorreport_spdfi($line,$file,$my_error);
	}
}

function smallerrorreport_spdfi()
{
	global $wpdb;
	$r = $wpdb->get_results("SELECT line,file,my_error,date FROM " .$wpdb->prefix . "spdfi_reports ORDER BY id DESC LIMIT 5");
	
	if( !$r )
	{
		echo '<h4>No Error Events Logged</h4>';
	}
	else
	{
		echo '<ul>';
		foreach($res2 as $y)
		{
			echo '<li>' . $y->my_error . '</li>';
		}
		echo '</ul>';
	}
}

function mediumerrorreport_spdfi()
{
	global $wpdb;
	$sqlQuery = "INSERT INTO " . $wpdb->prefix . "spdfi_reports (line,file) VALUES ('$line','$file')";
	$wpdb->query($sqlQuery);
}

function fullerrorreport_spdfi()
{
	global $wpdb;
	$sqlQuery = "INSERT INTO " . $wpdb->prefix . "spdfi_reports (line,file) VALUES ('$line','$file')";
	$wpdb->query($sqlQuery);
}

function emailerrorreport_spdfi($line,$file,$my_error)
{	
	$email = $my_error;
	if(get_option('spdfi_debugmode') == 1)// email error@webtechglobal.co.uk
	{
		wp_mail('error@webtechglobal.co.uk', 'spdfi Error Report', $email);
	}
}

?>