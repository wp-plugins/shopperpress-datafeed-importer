<?php
/* Same in both paid or free edition */

function spdfi_optionsinstallation($state)
{
	// demo mode setting - manually edited by WebTechGlobal for demo blogs only, please ignore
	add_option('spdfi_demomode',0);// 0 = off 1 = on

	if($state == 1)// delete options before attempted re-adding them
	{
		delete_option('spdfi_debugmode');// 0 = off 1 = on
		delete_option('spdfi_processingtrigger','shutdown');
		delete_option('spdfi_tagslength');// usually 50-150
		delete_option('spdfi_postsperhit');// usually 5-20
		delete_option('spdfi_publisherid');// any number
		delete_option('spdfi_defaultcatparent');// any number
		delete_option('spdfi_autokeywords');// 1 = on 0 = off
		delete_option('spdfi_autodescription');// 1 = on 0 = off
		delete_option('spdfi_autotags');// 1 = on 0 = off
		delete_option('spdfi_maxstagtime');// usually 20-50
		delete_option('spdfi_lastfilename');
		delete_option('spdfi_currentprocess');// indicates processing is ongoing or not	
		delete_option('spdfi_defaultposttype');// post or page
		delete_option('spdfi_defaultping');// post ping on or off
		delete_option('spdfi_defaultcomment');// allow comments or not setting
		delete_option('spdfi_defaultphase');// 0 = auto update start not allow, manual start only and 1 = auto start allowed
		delete_option('spdfi_tooltipsonoff');// 0 = off and 1 = on
		delete_option('spdfi_demomode');// 0 = off and 1 = on
		delete_option('spdfi_maxexecutiontime');
		delete_option('spdfi_lastprocessingtime');
		delete_option('spdfi_itemqty');
		delete_option('spdfi_processingdelay');
	}
	
	add_option('spdfi_debugmode',0);// 0 = off 1 = on
	add_option('spdfi_processingtrigger','shutdown');
	add_option('spdfi_tagslength',50);// usually 50-150
	add_option('spdfi_postsperhit',1);// usually 5-20
	add_option('spdfi_publisherid',1);// any number
	add_option('spdfi_defaultcatparent',1);// any number
	add_option('spdfi_autokeywords',1);// 1 = on 0 = off
	add_option('spdfi_autodescription',1);// 1 = on 0 = off
	add_option('spdfi_autotags',1);// 1 = on 0 = off
	add_option('spdfi_maxstagtime',50);// usually 20-50
	add_option('spdfi_lastfilename','None Submitted Yet');
	add_option('spdfi_currentprocess',0);// indicates processing is ongoing or not
	add_option('spdfi_defaultposttype','post');// post or page
	add_option('spdfi_defaultping', 1);// post ping on or off
	add_option('spdfi_defaultcomment', 'open');// post ping on or off
	add_option('spdfi_defaultphase', 1);// 0 = initial import and 1 is update phase
	add_option('spdfi_tooltipsonoff', 0);// 0 = off and 1 = on
	add_option('spdfi_maxexecutiontime', 20);// the number is in seconds
	add_option('spdfi_lastprocessingtime', time());// used to prevent processing happening too soon
	add_option('spdfi_itemqty', 5);// default item quantity
	add_option('spdfi_processingdelay',5);
	
	echo '<h3>Options and Settings Installed Successfully<h3>';
}

function spdfi_databaseinstallation($state)
{
	global $wpdb;

	if($state == 1)// delete existing tables before re-adding them
	{
		$result = false;
		
		$table_name = $wpdb->prefix . "spdfi_relationships";
		$sql = "DROP TABLE ". $table_name;
		$result = $wpdb->query($sql);
		
		$table_name = $wpdb->prefix . "spdfi_customfields";
		$sql = "DROP TABLE ". $table_name;
		$result = $wpdb->query($sql);
		
		$table_name = $wpdb->prefix . "spdfi_categories";
		$sql = "DROP TABLE ". $table_name;
		$result = $wpdb->query($sql);

		$table_name = $wpdb->prefix . "spdfi_campaigns";
		$sql = "DROP TABLE ". $table_name;
		$result = $wpdb->query($sql);

		$table_name = $wpdb->prefix . "spdfi_posthistory";
		$sql = "DROP TABLE ". $table_name;
		$result = $wpdb->query($sql);

		$table_name = $wpdb->prefix . "spdfi_layouts";
		$sql = "DROP TABLE ". $table_name;
		$result = $wpdb->query($sql);

		$table_name = $wpdb->prefix . "spdfi_reports";
		$sql = "DROP TABLE ". $table_name;
		$result = $wpdb->query($sql);
	}
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	# TABLE 1
	$table_name = $wpdb->prefix . "spdfi_relationships";
	$table1 = "CREATE TABLE `" . $table_name . "` (
		`id` int(10) unsigned NOT NULL auto_increment,
		`camid` int(10) unsigned NOT NULL COMMENT 'Campaign ID',
		`csvcolumnid` int(10) unsigned NOT NULL COMMENT 'Incremented number assigned to columns of CSV file in order they are in the file',
		`postpart` varchar(50) NOT NULL COMMENT 'Part CSV column assigned to in order to fulfill post data requirements',
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=380 DEFAULT CHARSET=utf8 COMMENT='Links between CSV file columns and post parts';";		
		
	# TABLE 2
	$table_name = $wpdb->prefix . "spdfi_customfields";
	$table2 = "CREATE TABLE `" . $table_name . "` (
		`id` int(10) unsigned NOT NULL auto_increment,
		`camid` int(10) unsigned NOT NULL,
		`identifier` varchar(30) NOT NULL,
		`value` varchar(500) NOT NULL,
		`type` int(10) unsigned NOT NULL COMMENT '0 = custom global value 1 = column marriage and possible unique value per post',
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=169 DEFAULT CHARSET=utf8 COMMENT='custom field data for campaigns';";

	# TABLE 3
	$table_name = $wpdb->prefix . "spdfi_categories";
	$table3 = "CREATE TABLE `" . $table_name . "` (
		`id` int(10) unsigned NOT NULL auto_increment,
		`camid` int(10) unsigned NOT NULL,
		`catcolumn` int(10) unsigned NOT NULL COMMENT 'csv column id for the column used to decide categorie sorting',
		`catid` int(10) unsigned NOT NULL COMMENT 'id of wp category',
		`uniquevalue` varchar(50) NOT NULL COMMENT 'unique value from the choosing column that determines this post goes in this category',
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COMMENT='Data used to sort new posts into correct category';";
	
	# TABLE 4
	$table_name = $wpdb->prefix . "spdfi_campaigns";
	$table4 = "CREATE TABLE `" . $table_name . "` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `camname` varchar(50) NOT NULL,
  `camfile` varchar(500) NOT NULL COMMENT 'Filename without extension (directory is scripted)',
  `process` int(10) unsigned NOT NULL COMMENT '1 = Full and 2 = Staggered',
  `ratio` int(10) unsigned NOT NULL default '1' COMMENT 'If Staggered processing selected this is the per visitor row to process',
  `stage` int(10) unsigned NOT NULL COMMENT '100 = Ready, 200 = Paused, 300 = FINISHED',
  `csvcolumns` int(10) unsigned default NULL COMMENT 'Number of columns in CSV file',
  `poststatus` varchar(45) default NULL COMMENT 'published,pending,draft',
  `filtercolumn` int(10) unsigned default '999' COMMENT 'CSV file column ID for the choosen categories filter',
  `tagscolumn` int(10) unsigned default '999' COMMENT 'Column ID assigned for making tags with.',
  `location` varchar(500) default NULL COMMENT 'CSV file location for FULL processing selection',
  `locationtype` int(10) unsigned default NULL COMMENT '1 = link and 2 = upload',
  `posts` int(10) unsigned default '0' COMMENT 'Total number of posts created',
  `layoutfile` varchar(100) default NULL COMMENT 'Layout and post content styling file selected for this campaign',
  `customfieldsmethod` varchar(50) default NULL COMMENT 'Used during post injection - auto, manual or mixed',
  `filtermethod` varchar(50) default NULL COMMENT 'Used during category filtering',
  `delimiter` varchar(3) default NULL,
  `type` int(10) unsigned default NULL COMMENT '1 = localhost to online 0 = standard post to self',
  `filtercolumn2` int(10) unsigned default '999' COMMENT 'stage 5 child category option',
  `filtercolumn3` int(10) unsigned default '999' COMMENT 'stage 5 child of child category option',
  `defaultcat` int(10) unsigned default NULL,
  `schedulednumber` int(10) unsigned default NULL COMMENT 'if processing = 3 (scheduled) then this number is the number of posts to be created per day',
  `csvrows` int(10) unsigned default NULL COMMENT 'number of rows in csv file',
  `allowupdate` int(10) unsigned default '0' COMMENT '1 = yes and 0 = no',
  `phase` int(10) unsigned default '1',
  `randomdate` int(10) unsigned default '0' COMMENT '1 = random date will be applied',
  `updatedposts` int(10) unsigned default '0' COMMENT 'Number of posts updated in this campaign since the campaign started',
  `processcounter` int(10) unsigned default '0' COMMENT 'records position and progress of processing on main processing or updating, is reset during phases',
  `droppedrows` int(10) unsigned default '0' COMMENT 'rows dropped during phase 1',
  `uniquecolumn` int(10) unsigned default '999',
  `primaryurlcloak` int(10) unsigned default '999' COMMENT 'Primary url cloak, select on stage 2',
  `price_col` int(10) unsigned default '999',
  `oldprice_col` int(10) unsigned default '999',
  `image_col` int(10) unsigned default '999',
  `thumbnail_col` int(10) unsigned default '999',
  `shipping_col` int(10) unsigned default '999',
  `featured_col` int(10) unsigned default '999',
  `excerpt_col` int(10) unsigned default '999',
  `keywords_col` int(10) unsigned default '999',
  `tags_col` int(10) unsigned default '999',
  `images_col` int(10) unsigned default '999',
  `customlist1_col` int(10) unsigned default '999',
  `customlist2_col` int(10) unsigned default '999',
  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=195 DEFAULT CHARSET=utf8;";




	# TABLE 5
	$table_name = $wpdb->prefix . "spdfi_posthistory";
	$table5 = "CREATE TABLE  `" . $table_name . "` (
		`id` int(10) unsigned NOT NULL auto_increment,
		`camid` int(10) unsigned NOT NULL,
		`postid` int(10) unsigned NOT NULL,
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='List of post ID''s created under each campaign';";

	# TABLE 6
	$table_name = $wpdb->prefix . "spdfi_layouts";
	$table6 = "CREATE TABLE  `" . $table_name . "` (
		`id` int(10) unsigned NOT NULL auto_increment,
		`name` varchar(45) default NULL,
		`csvfile` varchar(45) default NULL,
		`code` text COMMENT 'code dump',
		`inuse` int(10) unsigned default NULL COMMENT '0 = no and 1 = yes to being in use by a campaign',
		`wysiwyg_content` text COMMENT 'original worpdress wysiwyg editor content before creating post custom layout',
		`wysiwyg_title` text,
		`type` int(10) unsigned default NULL COMMENT '0 = WYSIWYG Edited 1 = PHP Dump and No WYSIWYG support',
 		`posttitle` varchar(500) default NULL COMMENT 'session code for post title',
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Custom post layouts are stored in database in this table';";


	# TABLE 7
	$table_name = $wpdb->prefix . "spdfi_reports";
	$table7 = "CREATE TABLE  `" . $table_name . "` (
		`id` int(10) unsigned NOT NULL auto_increment,
		`wp_error` varchar(500) default NULL,
		`php_error` varchar(500) default NULL,
		`my_error` varchar(500) default NULL,
		`file` varchar(100) default NULL,
		`line` int(10) unsigned default NULL,
		`query` varchar(5000) default NULL,
		`cam_id` int(10) unsigned default NULL COMMENT 'Campaign ID if it is campaign related, usually is',
		`date` datetime default '0000-00-00 00:00:00',
		`datadump` varchar(5000) default NULL COMMENT 'a dump of any data available that was being used during the time of the error',
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COMMENT='General campaign and plugin error or status reporting';";


	$result = $wpdb->query($table1);
	$result = $wpdb->query($table2);
	$result = $wpdb->query($table3);
	$result = $wpdb->query($table4);
	$result = $wpdb->query($table5);
	$result = $wpdb->query($table6);
	$result = $wpdb->query($table7);
	

	// execute update attempt for all tables
	dbDelta($table1);
	dbDelta($table2);
	dbDelta($table3);
	dbDelta($table4);
	dbDelta($table5);
	dbDelta($table6);
	dbDelta($table7);
	
		
	echo '<h3>CSV 2 POST Database Tables Installed Successfully.</h3>';
}


?>