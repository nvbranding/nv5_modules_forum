<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 28/8/2010, 23:11
 */


if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_node";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_forum";

$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_ip";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_post";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_post_history";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_config";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_prefix";  
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_admins";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_smilie";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_warning";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_watch";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_user_post";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_attachment";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_attachment_data";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_liked_content";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_online";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_users_statistic";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_comment";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_profile_post";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator_content";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator_log";
 sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_entry";
 
$sql_create_module = $sql_drop_module;


$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_node (
	node_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	parent_id mediumint(8) unsigned NOT NULL DEFAULT '0',
	title varchar(250) DEFAULT NULL COMMENT 'tên chuyên mục',
	alias varchar(250) DEFAULT NULL COMMENT 'liên kết tĩnh',
	description text NOT NULL COMMENT 'mô tả chuyên mục',
	image varchar(250) NOT NULL DEFAULT '' COMMENT 'Hình ảnh chuyên mục',
	password varchar(50) NOT NULL DEFAULT '' COMMENT 'mật khẩu chuyên mục',
	weight smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Thứ tự chuyên mục',
	sort mediumint(8) NOT NULL DEFAULT '0',
	lev smallint(4) NOT NULL DEFAULT '0',
	node_type_id varchar(20) NOT NULL DEFAULT '' COMMENT 'category=1, forum=2, forum_link=3, page=4',
	numsubcat int(11) NOT NULL DEFAULT '0' COMMENT 'Đếm số chuyên mục con',
	subcatid varchar(250) NOT NULL DEFAULT '' COMMENT 'ID của chuyên mục con',
	status tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Trạng thái chuyên mục',
	date_added int(11) unsigned NOT NULL default '0' COMMENT 'Ngày tạo chuyên mục',
	date_modified int(11) unsigned NOT NULL default '0' COMMENT 'Ngày cập nhật chuyên mục',
	PRIMARY KEY (node_id),
	UNIQUE KEY alias (alias),
	KEY parent_id (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";


$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_forum (
	node_id int(10) unsigned NOT NULL,
	discussion_count int(10) unsigned NOT NULL DEFAULT '0',
	message_count int(10) unsigned NOT NULL DEFAULT '0',
	last_post_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Most recent post_id',
	last_post_date int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Date of most recent post',
	last_post_user_id int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'User_id of user posting most recently',
	last_post_username varchar(50) NOT NULL DEFAULT '' COMMENT 'Username of most recently-posting user',
	last_thread_title varchar(150) NOT NULL DEFAULT '' COMMENT 'Title of thread most recent post is in',
	moderate_threads tinyint(3) unsigned NOT NULL DEFAULT '0',
	moderate_replies tinyint(3) unsigned NOT NULL DEFAULT '0',
	allow_posting tinyint(3) unsigned NOT NULL DEFAULT '1',
	allow_poll tinyint(3) unsigned NOT NULL DEFAULT '1',
	count_messages tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'If not set, messages posted (directly) within this forum will not contribute to user message totals.',
	find_new tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Include posts from this forum when running /find-new/threads',
	prefix_cache mediumblob NOT NULL COMMENT 'Serialized data from xf_forum_prefix, [group_id][prefix_id] => prefix_id',
	default_prefix_id int(10) unsigned NOT NULL DEFAULT '0',
	default_sort_order varchar(25) NOT NULL DEFAULT 'last_post_date',
	default_sort_direction varchar(5) NOT NULL DEFAULT 'desc',
	list_date_limit_days smallint(5) unsigned NOT NULL DEFAULT '0',
	require_prefix tinyint(3) unsigned NOT NULL DEFAULT '0',
	allowed_watch_notifications varchar(10) NOT NULL DEFAULT 'all',
	PRIMARY KEY (node_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_forum_prefix (
	node_id int(10) unsigned NOT NULL,
	prefix_id int(10) unsigned NOT NULL,
	PRIMARY KEY (node_id,prefix_id), 
	KEY prefix_id (prefix_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_forum_read (
	forum_read_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	user_id int(10) unsigned NOT NULL,
	node_id int(10) unsigned NOT NULL,
	forum_read_date int(10) unsigned NOT NULL,
	PRIMARY KEY (forum_read_id),
	UNIQUE KEY user_id_node_id (user_id,node_id),
	KEY node_id (node_id),
	KEY forum_read_date (forum_read_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_forum_watch (
	user_id int(10) unsigned NOT NULL,
	node_id int(10) unsigned NOT NULL,
	notify_on enum('','thread','message') NOT NULL,
	send_alert tinyint(3) unsigned NOT NULL,
	send_email tinyint(3) unsigned NOT NULL,
	PRIMARY KEY (user_id,node_id), 
	KEY node_id_notify_on (node_id,notify_on)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_link_forum (
  node_id int(10) unsigned NOT NULL,
  link_url varchar(150) NOT NULL,
  redirect_count int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (node_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";

 
$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator (
  user_id int(10) UNSIGNED NOT NULL,
  is_super_moderator tinyint(3) UNSIGNED NOT NULL,
  moderator_permissions mediumblob NOT NULL,
  extra_user_group_ids varbinary(255) NOT NULL,
  PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
 

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator_content (
  moderator_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  content_type varbinary(25) NOT NULL,
  content_id int(10) UNSIGNED NOT NULL,
  user_id int(10) UNSIGNED NOT NULL,
  moderator_permissions mediumblob NOT NULL,
  PRIMARY KEY (moderator_id),
  UNIQUE KEY content_user_id (content_type,content_id,user_id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
 

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator_log (
  moderator_log_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  log_date int(10) UNSIGNED NOT NULL,
  user_id int(10) UNSIGNED NOT NULL,
  ip_address varbinary(16) NOT NULL DEFAULT '',
  content_type varbinary(25) NOT NULL,
  content_id int(10) UNSIGNED NOT NULL,
  content_user_id int(10) UNSIGNED NOT NULL,
  content_username varchar(50) NOT NULL,
  content_title varchar(150) NOT NULL,
  content_url text NOT NULL,
  discussion_content_type varchar(25) NOT NULL,
  discussion_content_id int(10) UNSIGNED NOT NULL,
  action varchar(25) NOT NULL,
  action_params mediumblob NOT NULL,
  PRIMARY KEY (moderator_log_id),
  KEY log_date (log_date),
  KEY content_type_id (content_type,content_id),
  KEY discussion_content_type_id (discussion_content_type,discussion_content_id),
  KEY user_id_log_date (user_id,log_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_entry (
  permission_entry_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_group_id int(10) UNSIGNED NOT NULL,
  user_id int(10) UNSIGNED NOT NULL,
  permission_group_id varbinary(25) NOT NULL,
  permission_id varbinary(25) NOT NULL,
  permission_value enum('unset','allow','deny','use_int') NOT NULL,
  permission_value_int int(11) NOT NULL,
  PRIMARY KEY (permission_entry_id),
  UNIQUE KEY unique_permission (user_group_id,user_id,permission_group_id,permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci"; 

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_combination_user_group (
	user_group_id int(10) UNSIGNED NOT NULL,
	permission_combination_id int(10) UNSIGNED NOT NULL,
	PRIMARY KEY (user_group_id,permission_combination_id),
	KEY permission_combination_id (permission_combination_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
 
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_permission_combination_user_group (user_group_id, permission_combination_id) VALUES
(1, 1),
(1, 6),
(2, 1),
(2, 6),
(3, 4),
(3, 6),
(4, 2),
(4, 3),
(4, 4),
(4, 6),
(5, 3),
(5, 4),
(5, 6)";

$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_permission_entry (permission_entry_id, user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int) VALUES
(11, 2, 0, 0x617661746172, 0x616c6c6f776564, 'allow', 0),
(12, 2, 0, 0x617661746172, 0x6d617846696c6553697a65, 'use_int', 51200),
(13, 2, 0, 0x636f6e766572736174696f6e, 0x7374617274, 'allow', 0),
(14, 2, 0, 0x636f6e766572736174696f6e, 0x72656365697665, 'allow', 0),
(15, 2, 0, 0x636f6e766572736174696f6e, 0x6d6178526563697069656e7473, 'use_int', 5),
(16, 2, 0, 0x636f6e766572736174696f6e, 0x656469744f776e506f7374, 'allow', 0),
(17, 2, 0, 0x636f6e766572736174696f6e, 0x656469744f776e506f737454696d654c696d6974, 'use_int', 5),
(18, 2, 0, 0x666f72756d, 0x64656c6574654f776e506f7374, 'allow', 0),
(19, 2, 0, 0x666f72756d, 0x656469744f776e506f7374, 'allow', 0),
(20, 2, 0, 0x666f72756d, 0x656469744f776e5468726561645469746c65, 'allow', 0),
(21, 2, 0, 0x666f72756d, 0x656469744f776e506f737454696d654c696d6974, 'use_int', -1),
(22, 2, 0, 0x666f72756d, 0x706f73745265706c79, 'allow', 0),
(23, 2, 0, 0x666f72756d, 0x706f7374546872656164, 'allow', 0),
(24, 2, 0, 0x666f72756d, 0x75706c6f61644174746163686d656e74, 'allow', 0),
(25, 2, 0, 0x666f72756d, 0x766965774174746163686d656e74, 'allow', 0),
(26, 2, 0, 0x666f72756d, 0x76696577436f6e74656e74, 'allow', 0),
(27, 2, 0, 0x666f72756d, 0x766965774f7468657273, 'allow', 0),
(28, 2, 0, 0x666f72756d, 0x766f7465506f6c6c, 'allow', 0),
(29, 2, 0, 0x666f72756d, 0x6c696b65, 'allow', 0),
(30, 2, 0, 0x67656e6572616c, 0x6564697450726f66696c65, 'allow', 0),
(31, 2, 0, 0x67656e6572616c, 0x656469745369676e6174757265, 'allow', 0),
(32, 2, 0, 0x67656e6572616c, 0x666f6c6c6f774d6f6465726174696f6e52756c6573, 'allow', 0),
(33, 2, 0, 0x67656e6572616c, 0x736561726368, 'allow', 0),
(34, 2, 0, 0x67656e6572616c, 0x76696577, 'allow', 0),
(35, 2, 0, 0x67656e6572616c, 0x766965774e6f6465, 'allow', 0),
(36, 2, 0, 0x67656e6572616c, 0x7669657750726f66696c65, 'allow', 0),
(37, 2, 0, 0x67656e6572616c, 0x766965774d656d6265724c697374, 'allow', 0),
(38, 2, 0, 0x67656e6572616c, 0x7265706f7274, 'allow', 0),
(39, 2, 0, 0x67656e6572616c, 0x6d61785461676765645573657273, 'use_int', 5),
(40, 2, 0, 0x7369676e6174757265, 0x626173696354657874, 'allow', 0),
(41, 2, 0, 0x7369676e6174757265, 0x657874656e64656454657874, 'allow', 0),
(42, 2, 0, 0x7369676e6174757265, 0x616c69676e, 'allow', 0),
(43, 2, 0, 0x7369676e6174757265, 0x6c697374, 'allow', 0),
(44, 2, 0, 0x7369676e6174757265, 0x696d616765, 'allow', 0),
(45, 2, 0, 0x7369676e6174757265, 0x6c696e6b, 'allow', 0),
(46, 2, 0, 0x7369676e6174757265, 0x6d65646961, 'allow', 0),
(47, 2, 0, 0x7369676e6174757265, 0x626c6f636b, 'allow', 0),
(48, 2, 0, 0x7369676e6174757265, 0x6d61785072696e7461626c65, 'use_int', -1),
(49, 2, 0, 0x7369676e6174757265, 0x6d61784c696e6573, 'use_int', -1),
(50, 2, 0, 0x7369676e6174757265, 0x6d61784c696e6b73, 'use_int', -1),
(51, 2, 0, 0x7369676e6174757265, 0x6d6178496d61676573, 'use_int', -1),
(52, 2, 0, 0x7369676e6174757265, 0x6d6178536d696c696573, 'use_int', -1),
(53, 2, 0, 0x7369676e6174757265, 0x6d61785465787453697a65, 'use_int', -1),
(54, 2, 0, 0x70726f66696c65506f7374, 0x64656c6574654f776e, 'allow', 0),
(55, 2, 0, 0x70726f66696c65506f7374, 0x656469744f776e, 'allow', 0),
(56, 2, 0, 0x70726f66696c65506f7374, 0x6d616e6167654f776e, 'allow', 0),
(57, 2, 0, 0x70726f66696c65506f7374, 0x706f7374, 'allow', 0),
(58, 2, 0, 0x70726f66696c65506f7374, 0x636f6d6d656e74, 'allow', 0),
(59, 2, 0, 0x70726f66696c65506f7374, 0x76696577, 'allow', 0),
(60, 2, 0, 0x70726f66696c65506f7374, 0x6c696b65, 'allow', 0),
(61, 3, 0, 0x617661746172, 0x616c6c6f776564, 'allow', 0),
(62, 3, 0, 0x617661746172, 0x6d617846696c6553697a65, 'use_int', -1),
(63, 3, 0, 0x636f6e766572736174696f6e, 0x6d6178526563697069656e7473, 'use_int', -1),
(64, 3, 0, 0x636f6e766572736174696f6e, 0x65646974416e79506f7374, 'allow', 0),
(65, 3, 0, 0x636f6e766572736174696f6e, 0x616c77617973496e76697465, 'allow', 0),
(66, 3, 0, 0x636f6e766572736174696f6e, 0x75706c6f61644174746163686d656e74, 'allow', 0),
(67, 3, 0, 0x666f72756d, 0x6d61785461676765645573657273, 'use_int', -1),
(68, 3, 0, 0x666f72756d, 0x64656c6574654f776e546872656164, 'allow', 0),
(69, 3, 0, 0x666f72756d, 0x656469744f776e506f737454696d654c696d6974, 'use_int', -1),
(70, 3, 0, 0x67656e6572616c, 0x627970617373466c6f6f64436865636b, 'allow', 0),
(71, 3, 0, 0x67656e6572616c, 0x65646974437573746f6d5469746c65, 'allow', 0),
(72, 4, 0, 0x617661746172, 0x6d617846696c6553697a65, 'use_int', -1),
(73, 4, 0, 0x636f6e766572736174696f6e, 0x6d6178526563697069656e7473, 'use_int', -1),
(74, 4, 0, 0x636f6e766572736174696f6e, 0x75706c6f61644174746163686d656e74, 'allow', 0),
(75, 4, 0, 0x666f72756d, 0x6d61785461676765645573657273, 'use_int', -1),
(76, 4, 0, 0x666f72756d, 0x656469744f776e506f737454696d654c696d6974, 'use_int', -1),
(77, 4, 0, 0x67656e6572616c, 0x627970617373466c6f6f64436865636b, 'allow', 0),
(78, 4, 0, 0x67656e6572616c, 0x65646974437573746f6d5469746c65, 'allow', 0);";
 
 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission (
  permission_id varbinary(25) NOT NULL,
  permission_group_id varbinary(25) NOT NULL,
  permission_type enum('flag','integer') NOT NULL,
  interface_group_id varbinary(50) NOT NULL,
  depend_permission_id varbinary(25) NOT NULL,
  display_order int(10) UNSIGNED NOT NULL,
  default_value enum('allow','deny','unset') NOT NULL,
  default_value_int int(11) NOT NULL,
  addon_id varbinary(25) NOT NULL DEFAULT '',
  PRIMARY KEY (permission_id,permission_group_id),
  KEY display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
 

$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_permission (permission_id, permission_group_id, permission_type, interface_group_id, depend_permission_id, display_order, default_value, default_value_int, addon_id) VALUES
(0x616c69676e, 0x7369676e6174757265, 'flag', 0x7369676e61747572655065726d697373696f6e73, '', 30, 'unset', 0, 0x58656e466f726f),
(0x616c6c6f776564, 0x617661746172, 'flag', 0x6176617461725065726d697373696f6e73, '', 1, 'unset', 0, 0x58656e466f726f),
(0x616c77617973496e76697465, 0x636f6e766572736174696f6e, 'flag', 0x636f6e766572736174696f6e4d6f64657261746f725065726d697373696f6e73, '', 20, 'unset', 0, 0x58656e466f726f),
(0x617070726f7665556e617070726f7665, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 330, 'unset', 0, 0x58656e466f726f),
(0x617070726f7665556e617070726f7665, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73744d6f64657261746f725065726d697373696f6e73, '', 130, 'unset', 0, 0x58656e466f726f),
(0x626173696354657874, 0x7369676e6174757265, 'flag', 0x7369676e61747572655065726d697373696f6e73, '', 10, 'unset', 0, 0x58656e466f726f),
(0x626c6f636b, 0x7369676e6174757265, 'flag', 0x7369676e61747572655065726d697373696f6e73, '', 70, 'unset', 0, 0x58656e466f726f),
(0x627970617373466c6f6f64436865636b, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 10000, 'unset', 0, 0x58656e466f726f),
(0x6279706173735370616d436865636b, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 11000, 'unset', 0, 0x58656e466f726f),
(0x6279706173735573657250726976616379, 0x67656e6572616c, 'flag', 0x67656e6572616c4d6f64657261746f725065726d697373696f6e73, '', 50, 'unset', 0, 0x58656e466f726f),
(0x627970617373557365725461674c696d6974, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 106, 'unset', 0, 0x58656e466f726f),
(0x636c65616e5370616d, 0x67656e6572616c, 'flag', 0x67656e6572616c4d6f64657261746f725065726d697373696f6e73, '', 100, 'unset', 0, 0x58656e466f726f),
(0x636f6d6d656e74, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73745065726d697373696f6e73, '', 32, 'unset', 0, 0x58656e466f726f),
(0x637265617465546167, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 105, 'unset', 0, 0x58656e466f726f),
(0x64656c657465416e79, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73744d6f64657261746f725065726d697373696f6e73, '', 20, 'unset', 0, 0x58656e466f726f),
(0x64656c657465416e79506f7374, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 210, 'unset', 0, 0x58656e466f726f),
(0x64656c657465416e79546872656164, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 50, 'unset', 0, 0x58656e466f726f),
(0x64656c6574654f776e, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73745065726d697373696f6e73, '', 35, 'unset', 0, 0x58656e466f726f),
(0x64656c6574654f776e506f7374, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 20, 'unset', 0, 0x58656e466f726f),
(0x64656c6574654f776e546872656164, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 28, 'unset', 0, 0x58656e466f726f),
(0x65646974416e79, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73744d6f64657261746f725065726d697373696f6e73, '', 10, 'unset', 0, 0x58656e466f726f),
(0x65646974416e79506f7374, 0x636f6e766572736174696f6e, 'flag', 0x636f6e766572736174696f6e4d6f64657261746f725065726d697373696f6e73, '', 10, 'unset', 0, 0x58656e466f726f),
(0x65646974416e79506f7374, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 200, 'unset', 0, 0x58656e466f726f),
(0x65646974426173696350726f66696c65, 0x67656e6572616c, 'flag', 0x67656e6572616c4d6f64657261746f725065726d697373696f6e73, '', 130, 'unset', 0, 0x58656e466f726f),
(0x65646974437573746f6d5469746c65, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 500, 'unset', 0, 0x58656e466f726f),
(0x656469744f776e, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73745065726d697373696f6e73, '', 40, 'unset', 0, 0x58656e466f726f),
(0x656469744f776e506f7374, 0x636f6e766572736174696f6e, 'flag', 0x636f6e766572736174696f6e5065726d697373696f6e73, '', 40, 'unset', 0, 0x58656e466f726f),
(0x656469744f776e506f7374, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 20, 'unset', 0, 0x58656e466f726f),
(0x656469744f776e506f737454696d654c696d6974, 0x636f6e766572736174696f6e, 'integer', 0x636f6e766572736174696f6e5065726d697373696f6e73, 0x656469744f776e506f7374, 50, 'unset', 2, 0x58656e466f726f),
(0x656469744f776e506f737454696d654c696d6974, 0x666f72756d, 'integer', 0x666f72756d5065726d697373696f6e73, '', 21, 'unset', 0, 0x58656e466f726f),
(0x656469744f776e5468726561645469746c65, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 25, 'unset', 0, 0x58656e466f726f),
(0x6564697450726f66696c65, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 490, 'unset', 0, 0x58656e466f726f),
(0x656469745369676e6174757265, 0x67656e6572616c, 'flag', 0x7369676e61747572655065726d697373696f6e73, '', 1, 'unset', 0, 0x58656e466f726f),
(0x657874656e64656454657874, 0x7369676e6174757265, 'flag', 0x7369676e61747572655065726d697373696f6e73, '', 20, 'unset', 0, 0x58656e466f726f),
(0x666f6c6c6f774d6f6465726174696f6e52756c6573, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 9000, 'unset', 0, 0x58656e466f726f),
(0x6861726444656c657465416e79, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73744d6f64657261746f725065726d697373696f6e73, '', 25, 'unset', 0, 0x58656e466f726f),
(0x6861726444656c657465416e79506f7374, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 215, 'unset', 0, 0x58656e466f726f),
(0x6861726444656c657465416e79546872656164, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 55, 'unset', 0, 0x58656e466f726f),
(0x696d616765, 0x7369676e6174757265, 'flag', 0x7369676e61747572655065726d697373696f6e73, '', 50, 'unset', 0, 0x58656e466f726f),
(0x6c696b65, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 5, 'unset', 0, 0x58656e466f726f),
(0x6c696b65, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73745065726d697373696f6e73, '', 15, 'unset', 0, 0x58656e466f726f),
(0x6c696e6b, 0x7369676e6174757265, 'flag', 0x7369676e61747572655065726d697373696f6e73, '', 50, 'unset', 0, 0x58656e466f726f),
(0x6c697374, 0x7369676e6174757265, 'flag', 0x7369676e61747572655065726d697373696f6e73, '', 40, 'unset', 0, 0x58656e466f726f),
(0x6c6f636b556e6c6f636b546872656164, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 20, 'unset', 0, 0x58656e466f726f),
(0x6d616e616765416e79546167, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 225, 'unset', 0, 0x58656e466f726f),
(0x6d616e616765416e79546872656164, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 30, 'unset', 0, 0x58656e466f726f),
(0x6d616e6167654f7468657273546167734f776e546872656164, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 42, 'unset', 0, 0x58656e466f726f),
(0x6d616e6167654f776e, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73745065726d697373696f6e73, '', 20, 'unset', 0, 0x58656e466f726f),
(0x6d616e6167655761726e696e67, 0x67656e6572616c, 'flag', 0x67656e6572616c4d6f64657261746f725065726d697373696f6e73, '', 125, 'unset', 0, 0x58656e466f726f),
(0x6d617846696c6553697a65, 0x617661746172, 'integer', 0x6176617461725065726d697373696f6e73, 0x616c6c6f776564, 2, 'unset', 0, 0x58656e466f726f),
(0x6d6178496d61676573, 0x7369676e6174757265, 'integer', 0x7369676e61747572655065726d697373696f6e73, '', 110, 'unset', 0, 0x58656e466f726f),
(0x6d61784c696e6573, 0x7369676e6174757265, 'integer', 0x7369676e61747572655065726d697373696f6e73, '', 105, 'unset', 0, 0x58656e466f726f),
(0x6d61784c696e6b73, 0x7369676e6174757265, 'integer', 0x7369676e61747572655065726d697373696f6e73, '', 105, 'unset', 0, 0x58656e466f726f),
(0x6d61785072696e7461626c65, 0x7369676e6174757265, 'integer', 0x7369676e61747572655065726d697373696f6e73, '', 100, 'unset', 0, 0x58656e466f726f),
(0x6d6178526563697069656e7473, 0x636f6e766572736174696f6e, 'integer', 0x636f6e766572736174696f6e5065726d697373696f6e73, 0x7374617274, 100, 'unset', 0, 0x58656e466f726f),
(0x6d6178536d696c696573, 0x7369676e6174757265, 'integer', 0x7369676e61747572655065726d697373696f6e73, '', 120, 'unset', 0, 0x58656e466f726f),
(0x6d61785461676765645573657273, 0x67656e6572616c, 'integer', 0x67656e6572616c5065726d697373696f6e73, '', 100, 'unset', 0, 0x58656e466f726f),
(0x6d61785465787453697a65, 0x7369676e6174757265, 'integer', 0x7369676e61747572655065726d697373696f6e73, '', 130, 'unset', 0, 0x58656e466f726f),
(0x6d65646961, 0x7369676e6174757265, 'flag', 0x7369676e61747572655065726d697373696f6e73, '', 60, 'unset', 0, 0x58656e466f726f),
(0x706f7374, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73745065726d697373696f6e73, '', 30, 'unset', 0, 0x58656e466f726f),
(0x706f73745265706c79, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 10, 'allow', 0, 0x58656e466f726f),
(0x706f7374546872656164, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 9, 'unset', 0, 0x58656e466f726f),
(0x72656365697665, 0x636f6e766572736174696f6e, 'flag', 0x636f6e766572736174696f6e5065726d697373696f6e73, '', 12, 'unset', 0, 0x58656e466f726f),
(0x7265706f7274, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 12000, 'unset', 0, 0x58656e466f726f),
(0x72657175697265546661, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 510, 'unset', 0, 0x58656e466f726f),
(0x736561726368, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 90, 'unset', 0, 0x58656e466f726f),
(0x7374617274, 0x636f6e766572736174696f6e, 'flag', 0x636f6e766572736174696f6e5065726d697373696f6e73, '', 10, 'unset', 0, 0x58656e466f726f),
(0x737469636b556e737469636b546872656164, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 10, 'unset', 0, 0x58656e466f726f),
(0x746167416e79546872656164, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 41, 'unset', 0, 0x58656e466f726f),
(0x7461674f776e546872656164, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 40, 'unset', 0, 0x58656e466f726f),
(0x7468726561645265706c7942616e, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 60, 'unset', 0, 0x58656e466f726f),
(0x756e64656c657465, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 320, 'unset', 0, 0x58656e466f726f),
(0x756e64656c657465, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73744d6f64657261746f725065726d697373696f6e73, '', 120, 'unset', 0, 0x58656e466f726f),
(0x75706c6f61644174746163686d656e74, 0x636f6e766572736174696f6e, 'flag', 0x636f6e766572736174696f6e5065726d697373696f6e73, '', 30, 'unset', 0, 0x58656e466f726f),
(0x75706c6f61644174746163686d656e74, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, 0x766965774174746163686d656e74, 31, 'unset', 0, 0x58656e466f726f),
(0x76696577, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 1, 'allow', 0, 0x58656e466f726f),
(0x76696577, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73745065726d697373696f6e73, '', 10, 'unset', 0, 0x58656e466f726f),
(0x766965774174746163686d656e74, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 30, 'unset', 0, 0x58656e466f726f),
(0x76696577436f6e74656e74, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 2, 'unset', 0, 0x58656e466f726f),
(0x7669657744656c65746564, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 300, 'unset', 0, 0x58656e466f726f),
(0x7669657744656c65746564, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73744d6f64657261746f725065726d697373696f6e73, '', 100, 'unset', 0, 0x58656e466f726f),
(0x76696577497073, 0x67656e6572616c, 'flag', 0x67656e6572616c4d6f64657261746f725065726d697373696f6e73, '', 10, 'unset', 0, 0x58656e466f726f),
(0x766965774d656d6265724c697374, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 45, 'unset', 0, 0x58656e466f726f),
(0x766965774d6f64657261746564, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 310, 'unset', 0, 0x58656e466f726f),
(0x766965774d6f64657261746564, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73744d6f64657261746f725065726d697373696f6e73, '', 110, 'unset', 0, 0x58656e466f726f),
(0x766965774e6f6465, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, 0x76696577, 2, 'unset', 0, 0x58656e466f726f),
(0x766965774f7468657273, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 1, 'unset', 0, 0x58656e466f726f),
(0x7669657750726f66696c65, 0x67656e6572616c, 'flag', 0x67656e6572616c5065726d697373696f6e73, '', 50, 'unset', 0, 0x58656e466f726f),
(0x766965775761726e696e67, 0x67656e6572616c, 'flag', 0x67656e6572616c4d6f64657261746f725065726d697373696f6e73, '', 110, 'unset', 0, 0x58656e466f726f),
(0x766f7465506f6c6c, 0x666f72756d, 'flag', 0x666f72756d5065726d697373696f6e73, '', 100, 'unset', 0, 0x58656e466f726f),
(0x7761726e, 0x666f72756d, 'flag', 0x666f72756d4d6f64657261746f725065726d697373696f6e73, '', 220, 'unset', 0, 0x58656e466f726f),
(0x7761726e, 0x67656e6572616c, 'flag', 0x67656e6572616c4d6f64657261746f725065726d697373696f6e73, '', 120, 'unset', 0, 0x58656e466f726f),
(0x7761726e, 0x70726f66696c65506f7374, 'flag', 0x70726f66696c65506f73744d6f64657261746f725065726d697373696f6e73, '', 30, 'unset', 0, 0x58656e466f726f);";

 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 

// $sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_node (
	// node_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	// parent_id mediumint(8) unsigned NOT NULL DEFAULT '0',
	// title varchar(250) DEFAULT NULL COMMENT 'tên chuyên mục',
	// alias varchar(250) DEFAULT NULL COMMENT 'liên kết tĩnh',
	// description text NOT NULL COMMENT 'mô tả chuyên mục',
	// rules text NOT NULL COMMENT 'Nội quy chuyên mục',
	// rules_link varchar(250) DEFAULT NULL COMMENT 'Đường dẫn nội quy chuyên mục',
	// link varchar(250) DEFAULT NULL COMMENT 'Đường dẫn chuyên mục liên kết',
	// image varchar(250) NOT NULL DEFAULT '' COMMENT 'Hình ảnh chuyên mục',
	// password varchar(50) NOT NULL DEFAULT '' COMMENT 'mật khẩu chuyên mục',
	// weight smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Thứ tự chuyên mục',
	// sort mediumint(8) NOT NULL DEFAULT '0',
	// lev smallint(4) NOT NULL DEFAULT '0',
	// type smallint(4) NOT NULL DEFAULT '0' COMMENT 'node=1, forum=2, forum_link=3, page=4',
	// numsubcat int(11) NOT NULL DEFAULT '0' COMMENT 'Đếm số chuyên mục con',
	// subcatid varchar(250) NOT NULL DEFAULT '' COMMENT 'ID của chuyên mục con',
	// replycount mediumint(8) unsigned NOT NULL default '0' COMMENT 'Số bài bình luận',
	// threadcount mediumint(8) unsigned NOT NULL default '0' COMMENT 'Số chủ đề được tạo',
	// status tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Trạng thái chuyên mục',
	// keywords text NOT NULL COMMENT 'Từ khóa chuyên mục',
	// last_post_date int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Thời gian bài viết mới nhất',
	// last_post_username varchar(250) NOT NULL DEFAULT '' COMMENT 'tài khoản đăng mới nhất',
	// last_post_user_id int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'userid tài khoản đăng mới nhất',
	// last_post_id int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'bài đăng mới nhất',
	// last_post_page int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Trang cuối',
	// last_thread_title varchar(250) NOT NULL DEFAULT '' COMMENT 'tên chủ đề mới nhất',
	// last_thread_id int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'id chủ đề mới nhất',
	// last_prefix_id varchar(25) NOT NULL DEFAULT '' COMMENT 'tiếp đầu tố chủ đề',
	// date_added int(11) unsigned NOT NULL default '0' COMMENT 'Ngày tạo chuyên mục',
	// date_modified int(11) unsigned NOT NULL default '0' COMMENT 'Ngày cập nhật chuyên mục',
	// PRIMARY KEY (node_id),
	// UNIQUE KEY alias (alias),
	// KEY parent_id (parent_id)
// ) ENGINE=InnoDB";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread (
	thread_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	node_id int(10) unsigned NOT NULL,
	title varchar(150) NOT NULL,
	reply_count int(10) unsigned NOT NULL DEFAULT '0',
	view_count int(10) unsigned NOT NULL DEFAULT '0',
	user_id int(10) unsigned NOT NULL,
	username varchar(50) NOT NULL,
	post_date int(10) unsigned NOT NULL,
	sticky tinyint(3) unsigned NOT NULL DEFAULT '0',
	discussion_state enum('visible','moderated','deleted') NOT NULL DEFAULT 'visible',
	discussion_open tinyint(3) unsigned NOT NULL DEFAULT '1',
	discussion_type varchar(25) NOT NULL DEFAULT '',
	first_post_id int(10) unsigned NOT NULL,
	first_post_likes int(10) unsigned NOT NULL DEFAULT '0',
	last_post_date int(10) unsigned NOT NULL,
	last_post_id int(10) unsigned NOT NULL,
	last_post_page int(11) unsigned NOT NULL DEFAULT '0',
	last_post_user_id int(10) unsigned NOT NULL,
	last_post_username varchar(50) NOT NULL,
	prefix_id int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (thread_id),
	KEY node_id_last_post_date (node_id,last_post_date),
	KEY node_id_sticky_last_post_date (node_id,sticky,last_post_date),
	KEY last_post_date (last_post_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread_user_post (
	thread_id int(10) unsigned NOT NULL DEFAULT '0',
	user_id int(10) unsigned NOT NULL DEFAULT '0',
	post_count int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (thread_id,user_id),
	KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread_watch (
	user_id int(10) unsigned NOT NULL,
	thread_id int(10) unsigned NOT NULL,
	sendmail tinyint(3) unsigned NOT NULL DEFAULT '0',
	token varchar( 50 ) NOT NULL DEFAULT '',
	PRIMARY KEY (user_id,thread_id),
	KEY thread_id_sendmail (thread_id,sendmail)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_post (
	post_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	thread_id int(10) unsigned NOT NULL,
	user_id int(10) unsigned NOT NULL,
	username varchar(50) NOT NULL,
	post_date int(10) unsigned NOT NULL,
	message mediumtext NOT NULL,
	ip_id int(10) unsigned NOT NULL DEFAULT '0',
	message_state enum('visible','moderated','deleted') NOT NULL DEFAULT 'visible',
	attach_count smallint(5) unsigned NOT NULL DEFAULT '0',
	position int(10) unsigned NOT NULL,
	likes int(10) unsigned NOT NULL DEFAULT '0',
	like_users blob NOT NULL,
	warning_id int(10) unsigned NOT NULL DEFAULT '0',
	warning_message varchar(250) NOT NULL DEFAULT '',
	last_edit_date int(10) unsigned NOT NULL DEFAULT '0',
	last_edit_user_id int(10) unsigned NOT NULL DEFAULT '0',
	edit_count int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (post_id),
	KEY thread_id_post_date (thread_id,post_date),
	KEY thread_id_position (thread_id,position),
	KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_post_history (
	edit_history_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	content_type varbinary(25) NOT NULL,
	content_id int(10) unsigned NOT NULL,
	edit_user_id int(10) unsigned NOT NULL,
	edit_date int(10) unsigned NOT NULL,
	old_text mediumtext NOT NULL,
	PRIMARY KEY (edit_history_id),
	KEY content_type (content_type,content_id,edit_date),
	KEY edit_date (edit_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_profile_post (
	profile_post_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	profile_user_id int(10) unsigned NOT NULL,
	user_id int(10) unsigned NOT NULL,
	username varchar(50) NOT NULL,
	post_date int(10) unsigned NOT NULL,
	message mediumtext NOT NULL,
	ip_id int(10) unsigned NOT NULL DEFAULT '0',
	message_state enum('visible','moderated','deleted') NOT NULL DEFAULT 'visible',
	attach_count smallint(5) unsigned NOT NULL DEFAULT '0',
	likes int(10) unsigned NOT NULL DEFAULT '0',
	like_users blob NOT NULL,
	comment_count int(10) unsigned NOT NULL DEFAULT '0',
	first_comment_date int(10) unsigned NOT NULL DEFAULT '0',
	last_comment_date int(10) unsigned NOT NULL DEFAULT '0',
	latest_comment_ids varbinary(100) NOT NULL DEFAULT '',
	warning_id int(10) unsigned NOT NULL DEFAULT '0',
	warning_message varchar(250) NOT NULL DEFAULT '',
	PRIMARY KEY (profile_post_id),
	KEY profile_user_id_post_date (profile_user_id,post_date),
	KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
 
/* create column in users*/
$sql_create_module[] = "CREATE TABLE ". NV_USERS_GLOBALTABLE ."_privacy (
  user_id int(10) UNSIGNED NOT NULL,
  allow_view_profile enum('everyone','members','followed','none') NOT NULL DEFAULT 'everyone',
  allow_post_profile enum('everyone','members','followed','none') NOT NULL DEFAULT 'everyone',
  allow_send_personal_conversation enum('everyone','members','followed','none') NOT NULL DEFAULT 'everyone',
  allow_view_identities enum('everyone','members','followed','none') NOT NULL DEFAULT 'everyone',
  allow_receive_news_feed enum('everyone','members','followed','none') NOT NULL DEFAULT 'everyone',
  PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
 
$sql_create_module[] = "CREATE TABLE " . NV_USERS_GLOBALTABLE . "_option (
  user_id int(10) UNSIGNED NOT NULL,
  show_dob_year tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Show date of month year (thus: age)',
  show_dob_date tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Show date of birth day and month',
  content_show_signature tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Show user''s signatures with content',
  receive_admin_email tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  email_on_conversation tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Receive an email upon receiving a conversation message',
  is_discouraged tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'If non-zero, this user will be subjected to annoying random system failures.',
  default_watch_state enum('','watch_no_email','watch_email') NOT NULL DEFAULT '',
  alert_optout text NOT NULL COMMENT 'Comma-separated list of alerts from which the user has opted out. Example: ''post_like,user_trophy''',
  enable_rte tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  enable_flash_uploader tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";


$sql_create_module[] = "ALTER TABLE ". NV_USERS_GLOBALTABLE ." ADD is_moderator tinyint(1) UNSIGNED NOT NULL DEFAULT '0', ADD is_admin tinyint(1) UNSIGNED NOT NULL DEFAULT '0', ADD is_staff tinyint(1) UNSIGNED NOT NULL DEFAULT '0';";

$result = $db->query('SELECT u.userid, a.admin_id FROM '. NV_AUTHORS_GLOBALTABLE .' a RIGHT JOIN ' . NV_USERS_GLOBALTABLE . ' u ON (a.admin_id = u.userid)' );
 
while( $item = $result->fetch() )
{
	if( empty( $item['admin_id'] ) )
	{
		$watch_email = 'watch_email';
	}else
	{
		$watch_email = '';
		
		$sql_create_module[] = "UPDATE ". NV_USERS_GLOBALTABLE ." SET is_admin= 1 WHERE userid=" . intval( $item['userid'] );

	}	

	$sql_create_module[] = "INSERT INTO  ". NV_USERS_GLOBALTABLE ."_privacy (userid, allow_view_profile, allow_post_profile, allow_send_personal_conversation, allow_view_identities, allow_receive_news_feed) VALUES
	(". intval( $item['userid'] ) .", 'everyone', 'members', 'members', 'everyone', 'everyone');"
	
	$sql_create_module[] = "INSERT INTO ". NV_USERS_GLOBALTABLE ."_option (userid, show_dob_year, show_dob_date, content_show_signature, receive_admin_email, email_on_conversation, is_discouraged, default_watch_state, alert_optout, enable_rte, enable_flash_uploader) VALUES 
	(". intval( $item['userid'] ) .", 1, 1, 1, 1, 1, 0, '". $watch_email ."', '', 1, 1)";	

}
 
 
// $sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_comment (
	// profile_post_comment_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	// profile_post_id int(10) unsigned NOT NULL,
	// user_id int(10) unsigned NOT NULL,
	// username varchar(50) NOT NULL,
	// comment_date int(10) unsigned NOT NULL,
	// message mediumtext NOT NULL,
	// ip_id int(10) unsigned NOT NULL DEFAULT '0',
	// PRIMARY KEY (profile_post_comment_id),
	// KEY profile_post_id_comment_date (profile_post_id,comment_date)
// ) ENGINE=InnoDB";




// $sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_prefix (
	// prefixid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	// title varchar(50) NOT NULL,
	// PRIMARY KEY (prefixid)
// ) ENGINE=InnoDB";


/* 
  
/* $sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_online (
	session_id varchar(50) DEFAULT NULL,
	thread_id int(10) unsigned NOT NULL,
	catid int(10) unsigned NOT NULL,
	memberid int(10) unsigned NOT NULL,
	uid mediumint(8) unsigned NOT NULL DEFAULT '0',
	full_name varchar(100) NOT NULL,
	onl_time int(11) unsigned NOT NULL DEFAULT '0',
	UNIQUE KEY session_id (session_id),
	KEY onl_time (onl_time)
) ENGINE=MEMORY";
 */
 

/* 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_ip (
	ip_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	user_id int(10) unsigned NOT NULL,
	content_type varbinary(25) NOT NULL,
	content_id int(10) unsigned NOT NULL,
	action varbinary(25) NOT NULL DEFAULT '',
	ip varchar(15) NOT NULL,
	log_date int(10) unsigned NOT NULL,
	PRIMARY KEY (ip_id),
	KEY user_id_log_date (user_id,log_date),
	KEY ip_log_date (ip,log_date),
	KEY content_type_content_id (content_type,content_id)
) ENGINE=InnoDB";
 */


/* 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_admins (
	userid int(11) NOT NULL default '0',
	catid int(11) NOT NULL default '0',
	admin tinyint(4) NOT NULL default '0',
	add_content tinyint(4) NOT NULL default '0',
	pub_content tinyint(4) NOT NULL default '0',
	edit_content tinyint(4) NOT NULL default '0',
	del_content tinyint(4) NOT NULL default '0',
	comment tinyint(4) NOT NULL default '0',
	UNIQUE KEY userid (userid,catid)
) ENGINE=InnoDB";

 */
 /* 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_smilie (
	smilie_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	title varchar(50) NOT NULL,
	smilie_text text NOT NULL,
	image_url varchar(200) NOT NULL,
	sprite_mode tinyint(3) unsigned NOT NULL DEFAULT '0',
	sprite_params text NOT NULL,
	PRIMARY KEY (smilie_id)
) ENGINE=InnoDB"; */
/* 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_attachment_data (
	data_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	user_id int(10) unsigned NOT NULL,
	upload_date int(10) unsigned NOT NULL,
	filename varchar(100) NOT NULL,
	file_size int(10) unsigned NOT NULL,
	ext varchar(25) NOT NULL DEFAULT '',
	file_hash varchar(32) NOT NULL,
	width int(10) unsigned NOT NULL DEFAULT '0',
	height int(10) unsigned NOT NULL DEFAULT '0',
	thumbnail_width int(10) unsigned NOT NULL DEFAULT '0',
	thumbnail_height int(10) unsigned NOT NULL DEFAULT '0',
	attach_count int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (data_id),
	KEY user_id_upload_date (user_id,upload_date),
	KEY attach_count (attach_count)
) ENGINE=InnoDB";
 */
/* 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_attachment (
	attachment_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	data_id int(10) unsigned NOT NULL,
	content_type varbinary(25) NOT NULL,
	content_id int(10) unsigned NOT NULL,
	attach_date int(10) unsigned NOT NULL,
	temp_hash varchar(32) NOT NULL DEFAULT '',
	unassociated tinyint(3) unsigned NOT NULL,
	view_count int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (attachment_id),
	KEY content_type_id_date (content_type,content_id,attach_date),
	KEY temp_hash_attach_date (temp_hash,attach_date),
	KEY unassociated_attach_date (unassociated,attach_date)
) ENGINE=InnoDB";
 */
/* 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_warning (
	warning_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	content_type varbinary(25) NOT NULL,
	content_id int(10) unsigned NOT NULL,
	content_title varchar(250) NOT NULL,
	user_id int(10) unsigned NOT NULL,
	warning_date int(10) unsigned NOT NULL,
	warning_user_id int(10) unsigned NOT NULL,
	warning_definition_id int(10) unsigned NOT NULL,
	title varchar(250) NOT NULL,
	notes text NOT NULL,
	points smallint(5) unsigned NOT NULL,
	expiry_date int(10) unsigned NOT NULL,
	is_expired tinyint(3) unsigned NOT NULL,
	extra_user_group_ids varbinary(250) NOT NULL,
	PRIMARY KEY (warning_id),
	KEY content_type_id (content_type,content_id),
	KEY user_id_date (user_id,warning_date),
	KEY expiry (expiry_date)
) ENGINE=InnoDB"; */

/* 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_liked_content (
	like_id int(10) unsigned NOT NULL AUTO_INCREMENT,
	content_type varbinary(25) NOT NULL,
	content_id int(10) unsigned NOT NULL,
	like_user_id int(10) unsigned NOT NULL,
	like_date int(10) unsigned NOT NULL,
	content_user_id int(10) unsigned NOT NULL,
	PRIMARY KEY (like_id),
	UNIQUE KEY content_type_id_like_user_id (content_type,content_id,like_user_id),
	KEY like_user_content_type_id (like_user_id,content_type,content_id),
	KEY content_user_id_like_date (content_user_id,like_date)
) ENGINE=InnoDB"; */

/* 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_users_statistic (
	userid int(10) unsigned NOT NULL AUTO_INCREMENT,
	is_staff tinyint(3) unsigned NOT NULL,
	like_count int(10) unsigned NOT NULL DEFAULT '0',
	message_count int(10) unsigned NOT NULL DEFAULT '0', 
	PRIMARY KEY (userid),
	KEY message_count (message_count),
	KEY like_count (like_count)
) ENGINE=InnoDB"; */

/* $sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $module_data . "_config (
	config_name varchar(30) NOT NULL,
	config_value varchar(250) NOT NULL,
	UNIQUE KEY config_name (config_name)
)ENGINE=InnoDB"; */
/* 
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_config VALUES
('upload_logo', 'images/logo.png'),
('addlogo', '1'),
('indexfile', 'viewcat_main'),
('type_thread', 'thread_new'),
('paper_post', '10'),
('paper_page', '10'),
('paper_thread', '10'),
('verify_post', '3'),
('time_edit_user', '3'),
('other_link', '5'),
('show_smile', '1'),
('thumb_width', '500'),
('thumb_height', '380'),
('img_template_width', '760'),
('is_cus', '1'),
('is_admin', '1'),
('profile_perpage', '20'),
('maxupload', '2097152');
"; */