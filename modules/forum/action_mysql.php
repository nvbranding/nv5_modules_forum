<?php

/**
 * @Project NUKEVIET 5.x
 * @Author NV Systems (hoangnt@nguyenvan.vn)
 * @Copyright (C) 2019 NV Branding. All rights reserved
 * @Createdate Wed, 3 Apr 2019 08:34:29 GMT
 */

if ( ! defined( 'NV_IS_FILE_MODULES' ) ) die( 'Stop!!!' );

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_attachment";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_attachment_data";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_attachment_view";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_deletion_log";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_draft";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_edit_history";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_prefix";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_read";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_watch";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_ip";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_liked_content";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_link";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderation_queue";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator_content";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator_log";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_node";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_cache_content";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_combination";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_combination_user_group";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_entry";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_entry_content";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_group";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_interface_group";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_poll";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_poll_response";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_poll_vote";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_post";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_post_history";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_session_activity";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_tag";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_tag_content";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_tag_result_cache";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_read";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_redirect";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_reply_ban";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_user_post";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_view";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_watch";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_watch";
$sql_create_module = $sql_drop_module;



$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_attachment (
  attachment_id int(10) unsigned NOT NULL,
  data_id int(10) unsigned NOT NULL,
  content_type varbinary(25) NOT NULL,
  content_id int(10) unsigned NOT NULL,
  attach_date int(10) unsigned NOT NULL,
  md5filename varchar(50) NOT NULL DEFAULT '',
  temp_hash varchar(32) NOT NULL DEFAULT '',
  unassociated tinyint(3) unsigned NOT NULL,
  view_count int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_attachment_data (
  data_id int(10) unsigned NOT NULL,
  userid int(10) unsigned NOT NULL,
  upload_date int(10) unsigned NOT NULL,
  filename varchar(100) NOT NULL,
  file_size int(10) unsigned NOT NULL,
  file_hash varchar(32) NOT NULL,
  file_path varchar(250) NOT NULL DEFAULT '',
  width int(10) unsigned NOT NULL DEFAULT '0',
  height int(10) unsigned NOT NULL DEFAULT '0',
  thumbnail_width int(10) unsigned NOT NULL DEFAULT '0',
  thumbnail_height int(10) unsigned NOT NULL DEFAULT '0',
  attach_count int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_attachment_view (
  attachment_id int(10) unsigned NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_deletion_log (
  content_type varbinary(25) NOT NULL,
  content_id int(11) NOT NULL,
  delete_date int(11) NOT NULL,
  delete_user_id int(11) NOT NULL,
  delete_username varchar(50) NOT NULL,
  delete_reason varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_draft (
  draft_id int(10) unsigned NOT NULL,
  draft_key varbinary(75) NOT NULL,
  userid int(10) unsigned NOT NULL,
  last_update int(10) unsigned NOT NULL,
  message mediumtext NOT NULL,
  extra_data mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_edit_history (
  edit_history_id int(10) unsigned NOT NULL,
  content_type varbinary(25) NOT NULL,
  content_id int(10) unsigned NOT NULL,
  edit_user_id int(10) unsigned NOT NULL,
  edit_date int(10) unsigned NOT NULL,
  old_text mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . " (
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
  allowed_watch_notifications varchar(10) NOT NULL DEFAULT 'all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_prefix (
  node_id int(10) unsigned NOT NULL,
  prefix_id int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_read (
  forum_read_id int(10) unsigned NOT NULL,
  userid int(10) unsigned NOT NULL,
  node_id int(10) unsigned NOT NULL,
  forum_read_date int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_watch (
  userid int(10) unsigned NOT NULL,
  node_id int(10) unsigned NOT NULL,
  notify_on enum('','thread','message') NOT NULL,
  send_alert tinyint(3) unsigned NOT NULL,
  send_email tinyint(3) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_ip (
  ip_id int(10) unsigned NOT NULL,
  userid int(10) unsigned NOT NULL,
  content_type varbinary(25) NOT NULL,
  content_id int(10) unsigned NOT NULL,
  action varbinary(25) NOT NULL DEFAULT '',
  ip varbinary(16) NOT NULL,
  log_date int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_liked_content (
  like_id int(10) unsigned NOT NULL,
  content_type varbinary(25) NOT NULL,
  content_id int(10) unsigned NOT NULL,
  like_user_id int(10) unsigned NOT NULL,
  like_date int(10) unsigned NOT NULL,
  content_user_id int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_link (
  node_id int(10) unsigned NOT NULL,
  link_url varchar(150) NOT NULL,
  redirect_count int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderation_queue (
  content_type varbinary(25) NOT NULL,
  content_id int(10) unsigned NOT NULL,
  content_date int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator (
  userid int(10) unsigned NOT NULL,
  is_super_moderator tinyint(3) unsigned NOT NULL,
  moderator_permissions mediumblob NOT NULL,
  extra_user_group_ids varbinary(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator_content (
  moderator_id int(10) unsigned NOT NULL,
  content_type varbinary(25) NOT NULL,
  content_id int(10) unsigned NOT NULL,
  userid int(10) unsigned NOT NULL,
  moderator_permissions mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_moderator_log (
  moderator_log_id int(10) unsigned NOT NULL,
  log_date int(10) unsigned NOT NULL,
  userid int(10) unsigned NOT NULL,
  ip_address varbinary(16) NOT NULL DEFAULT '',
  content_type varbinary(25) NOT NULL,
  content_id int(10) unsigned NOT NULL,
  content_userid int(10) unsigned NOT NULL,
  content_username varchar(50) NOT NULL,
  content_title varchar(150) NOT NULL,
  content_url text NOT NULL,
  discussion_content_type varchar(25) NOT NULL,
  discussion_content_id int(10) unsigned NOT NULL,
  action varchar(25) NOT NULL,
  action_params mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_node (
  node_id mediumint(8) unsigned NOT NULL,
  parent_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  title varchar(250) DEFAULT NULL COMMENT 'tên chuyên mục',
  alias varchar(250) DEFAULT NULL COMMENT 'liên kết tĩnh',
  description text NOT NULL COMMENT 'mô tả chuyên mục',
  image varchar(250) NOT NULL DEFAULT '' COMMENT 'Hình ảnh chuyên mục',
  password varchar(50) NOT NULL DEFAULT '' COMMENT 'mật khẩu chuyên mục',
  weight smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Thứ tự chuyên mục',
  sort mediumint(8) NOT NULL DEFAULT '0',
  lev smallint(4) NOT NULL DEFAULT '0',
  node_type_id varchar(20) NOT NULL DEFAULT '' COMMENT 'node=1, forum=2, linkforum=3, page=4',
  numsubcat int(11) NOT NULL DEFAULT '0' COMMENT 'Đếm số chuyên mục con',
  subcatid varchar(250) NOT NULL DEFAULT '' COMMENT 'ID của chuyên mục con',
  status tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Trạng thái chuyên mục',
  date_added int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ngày tạo chuyên mục',
  date_modified int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ngày cập nhật chuyên mục'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission (
  permission_id varbinary(25) NOT NULL,
  permission_group_id varbinary(25) NOT NULL,
  permission_type enum('flag','integer') NOT NULL,
  interface_group_id varbinary(50) NOT NULL,
  depend_permission_id varbinary(25) NOT NULL,
  display_order int(10) unsigned NOT NULL,
  default_value enum('allow','deny','unset') NOT NULL,
  default_value_int int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_cache_content (
  permission_combination_id int(10) unsigned NOT NULL,
  content_type varbinary(25) NOT NULL,
  content_id int(10) unsigned NOT NULL,
  cache_value mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_combination (
  permission_combination_id int(10) unsigned NOT NULL,
  userid int(10) unsigned NOT NULL,
  user_group_list mediumblob NOT NULL,
  cache_value mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_combination_user_group (
  user_group_id int(10) unsigned NOT NULL,
  permission_combination_id int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_entry (
  permission_entry_id int(10) unsigned NOT NULL,
  user_group_id int(10) unsigned NOT NULL,
  userid int(10) unsigned NOT NULL,
  permission_group_id varbinary(25) NOT NULL,
  permission_id varbinary(25) NOT NULL,
  permission_value enum('unset','allow','deny','use_int') NOT NULL,
  permission_value_int int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_entry_content (
  permission_entry_id int(10) unsigned NOT NULL,
  content_type varbinary(25) NOT NULL,
  content_id int(10) unsigned NOT NULL,
  user_group_id int(10) unsigned NOT NULL,
  userid int(10) unsigned NOT NULL,
  permission_group_id varbinary(25) NOT NULL,
  permission_id varbinary(25) NOT NULL,
  permission_value enum('unset','reset','content_allow','deny','use_int') NOT NULL,
  permission_value_int int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_group (
  permission_group_id varbinary(25) NOT NULL,
  addon_id varbinary(25) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_permission_interface_group (
  interface_group_id varbinary(50) NOT NULL,
  display_order int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_poll (
  poll_id int(10) unsigned NOT NULL,
  content_type varbinary(25) NOT NULL,
  content_id int(10) unsigned NOT NULL,
  question varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  responses mediumblob NOT NULL,
  voter_count int(10) unsigned NOT NULL DEFAULT '0',
  public_votes tinyint(3) unsigned NOT NULL DEFAULT '0',
  max_votes tinyint(3) unsigned NOT NULL DEFAULT '1',
  close_date int(10) unsigned NOT NULL DEFAULT '0',
  change_vote tinyint(3) unsigned NOT NULL DEFAULT '0',
  view_results_unvoted tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_poll_response (
  poll_response_id int(10) unsigned NOT NULL,
  poll_id int(10) unsigned NOT NULL,
  response varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  response_vote_count int(10) unsigned NOT NULL DEFAULT '0',
  voters mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_poll_vote (
  userid int(10) unsigned NOT NULL,
  poll_response_id int(10) unsigned NOT NULL,
  poll_id int(10) unsigned NOT NULL,
  vote_date int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_post (
  post_id int(10) unsigned NOT NULL,
  thread_id int(10) unsigned NOT NULL,
  userid int(10) unsigned NOT NULL,
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
  edit_count int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_post_history (
  edit_history_id int(10) unsigned NOT NULL,
  content_type varbinary(25) NOT NULL,
  content_id int(10) unsigned NOT NULL,
  edit_user_id int(10) unsigned NOT NULL,
  edit_date int(10) unsigned NOT NULL,
  old_text mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_session_activity (
  userid int(10) unsigned NOT NULL,
  unique_key varbinary(16) NOT NULL,
  ip varbinary(16) NOT NULL DEFAULT '',
  action varbinary(50) NOT NULL,
  view_state enum('valid','error') NOT NULL,
  params varbinary(100) NOT NULL,
  view_date int(10) unsigned NOT NULL,
  robot_key varbinary(25) NOT NULL DEFAULT ''
) ENGINE=MEMORY DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_tag (
  tag_id int(10) unsigned NOT NULL,
  tag varchar(100) NOT NULL,
  tag_url varchar(100) NOT NULL,
  use_count int(10) unsigned NOT NULL DEFAULT '0',
  last_use_date int(10) unsigned NOT NULL DEFAULT '0',
  permanent tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_tag_content (
  tag_content_id int(10) unsigned NOT NULL,
  content_type varbinary(25) NOT NULL,
  content_id int(11) NOT NULL,
  tag_id int(10) unsigned NOT NULL,
  add_user_id int(10) unsigned NOT NULL,
  add_date int(10) unsigned NOT NULL,
  visible tinyint(3) unsigned NOT NULL,
  content_date int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_tag_result_cache (
  result_cache_id int(11) NOT NULL,
  tag_id int(10) unsigned NOT NULL,
  user_id int(10) unsigned NOT NULL,
  cache_date int(10) unsigned NOT NULL,
  expiry_date int(10) unsigned NOT NULL,
  results mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread (
  thread_id int(10) unsigned NOT NULL,
  node_id int(10) unsigned NOT NULL,
  title varchar(150) NOT NULL,
  reply_count int(10) unsigned NOT NULL DEFAULT '0',
  view_count int(10) unsigned NOT NULL DEFAULT '0',
  userid int(10) unsigned NOT NULL,
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
  last_post_user_id int(10) unsigned NOT NULL,
  last_post_username varchar(50) NOT NULL,
  prefix_id int(10) unsigned NOT NULL DEFAULT '0',
  tags mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_read (
  thread_read_id int(10) unsigned NOT NULL,
  userid int(10) unsigned NOT NULL,
  thread_id int(10) unsigned NOT NULL,
  thread_read_date int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_redirect (
  thread_id int(10) unsigned NOT NULL,
  target_url text COLLATE utf8mb4_unicode_ci NOT NULL,
  redirect_key varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  expiry_date int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_reply_ban (
  thread_reply_ban_id int(10) unsigned NOT NULL,
  thread_id int(10) unsigned NOT NULL,
  userid int(10) unsigned NOT NULL,
  ban_date int(10) unsigned NOT NULL,
  expiry_date int(10) unsigned DEFAULT NULL,
  reason varchar(100) NOT NULL DEFAULT '',
  ban_user_id int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_user_post (
  thread_id int(10) unsigned NOT NULL DEFAULT '0',
  userid int(10) unsigned NOT NULL DEFAULT '0',
  post_count int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_view (
  thread_id int(10) unsigned NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8";

$sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $module_data . "_thread_watch (
  userid int(10) unsigned NOT NULL,
  thread_id int(10) unsigned NOT NULL,
  email_subscribe tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_attachment
  ADD PRIMARY KEY (attachment_id),
  ADD UNIQUE KEY md5filename (md5filename),
  ADD KEY content_type_id_date (content_type,content_id,attach_date),
  ADD KEY temp_hash_attach_date (temp_hash,attach_date),
  ADD KEY unassociated_attach_date (unassociated,attach_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_attachment_data
  ADD PRIMARY KEY (data_id),
  ADD KEY userid_upload_date (userid,upload_date),
  ADD KEY attach_count (attach_count),
  ADD KEY upload_date (upload_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_attachment_view
  ADD KEY attachment_id (attachment_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_deletion_log
  ADD PRIMARY KEY (content_type,content_id),
  ADD KEY delete_user_id_date (delete_user_id,delete_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_draft
  ADD PRIMARY KEY (draft_id),
  ADD UNIQUE KEY draft_key_user (draft_key,userid),
  ADD KEY last_update (last_update)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_edit_history
  ADD PRIMARY KEY (edit_history_id),
  ADD KEY content_type (content_type,content_id,edit_date),
  ADD KEY edit_date (edit_date),
  ADD KEY edit_user_id (edit_user_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "
  ADD PRIMARY KEY (node_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_prefix
  ADD PRIMARY KEY (node_id,prefix_id),
  ADD KEY prefix_id (prefix_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_read
  ADD PRIMARY KEY (forum_read_id),
  ADD UNIQUE KEY user_id_node_id (userid,node_id),
  ADD KEY node_id (node_id),
  ADD KEY forum_read_date (forum_read_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_watch
  ADD PRIMARY KEY (userid,node_id),
  ADD KEY node_id_notify_on (node_id,notify_on)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_ip
  ADD PRIMARY KEY (ip_id),
  ADD KEY user_id_log_date (userid,log_date),
  ADD KEY ip_log_date (ip,log_date),
  ADD KEY content_type_content_id (content_type,content_id),
  ADD KEY log_date (log_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_liked_content
  ADD PRIMARY KEY (like_id),
  ADD UNIQUE KEY content_type_id_like_user_id (content_type,content_id,like_user_id),
  ADD KEY like_user_content_type_id (like_user_id,content_type,content_id),
  ADD KEY content_user_id_like_date (content_user_id,like_date),
  ADD KEY like_date (like_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_link
  ADD PRIMARY KEY (node_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_moderation_queue
  ADD PRIMARY KEY (content_type,content_id),
  ADD KEY content_date (content_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_moderator
  ADD PRIMARY KEY (userid)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_moderator_content
  ADD PRIMARY KEY (moderator_id),
  ADD UNIQUE KEY content_user_id (content_type,content_id,userid),
  ADD KEY user_id (userid)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_moderator_log
  ADD PRIMARY KEY (moderator_log_id),
  ADD KEY log_date (log_date),
  ADD KEY content_type_id (content_type,content_id),
  ADD KEY discussion_content_type_id (discussion_content_type,discussion_content_id),
  ADD KEY user_id_log_date (userid,log_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_node
  ADD PRIMARY KEY (node_id),
  ADD UNIQUE KEY alias (alias),
  ADD KEY parent_id (parent_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission
  ADD PRIMARY KEY (permission_id,permission_group_id),
  ADD KEY display_order (display_order)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_cache_content
  ADD PRIMARY KEY (permission_combination_id,content_type,content_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_combination
  ADD PRIMARY KEY (permission_combination_id),
  ADD KEY user_id (userid)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_combination_user_group
  ADD PRIMARY KEY (user_group_id,permission_combination_id),
  ADD KEY permission_combination_id (permission_combination_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_entry
  ADD PRIMARY KEY (permission_entry_id),
  ADD UNIQUE KEY unique_permission (user_group_id,userid,permission_group_id,permission_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_entry_content
  ADD PRIMARY KEY (permission_entry_id),
  ADD UNIQUE KEY user_group_id_unique (user_group_id,userid,content_type,content_id,permission_group_id,permission_id),
  ADD KEY content_type_content_id (content_type,content_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_group
  ADD PRIMARY KEY (permission_group_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_interface_group
  ADD PRIMARY KEY (interface_group_id),
  ADD KEY display_order (display_order)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_poll
  ADD PRIMARY KEY (poll_id),
  ADD UNIQUE KEY content_type_content_id (content_type,content_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_poll_response
  ADD PRIMARY KEY (poll_response_id),
  ADD KEY poll_id_response_id (poll_id,poll_response_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_poll_vote
  ADD PRIMARY KEY (poll_response_id,userid),
  ADD KEY poll_id_user_id (poll_id,userid),
  ADD KEY userid (userid)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_post
  ADD PRIMARY KEY (post_id),
  ADD KEY thread_id_post_date (thread_id,post_date),
  ADD KEY thread_id_position (thread_id,position),
  ADD KEY user_id (userid)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_post_history
  ADD PRIMARY KEY (edit_history_id),
  ADD KEY content_type (content_type,content_id,edit_date),
  ADD KEY edit_date (edit_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_session_activity
  ADD PRIMARY KEY (userid,unique_key) USING BTREE,
  ADD KEY view_date (view_date) USING BTREE";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_tag
  ADD PRIMARY KEY (tag_id),
  ADD UNIQUE KEY tag (tag),
  ADD UNIQUE KEY tag_url (tag_url),
  ADD KEY use_count (use_count)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_tag_content
  ADD PRIMARY KEY (tag_content_id),
  ADD UNIQUE KEY content_type_id_tag (content_type,content_id,tag_id),
  ADD KEY tag_id_content_date (tag_id,content_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_tag_result_cache
  ADD PRIMARY KEY (result_cache_id),
  ADD UNIQUE KEY tag_id_user_id (tag_id,user_id),
  ADD KEY expiration_date (expiry_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread
  ADD PRIMARY KEY (thread_id),
  ADD KEY node_id_last_post_date (node_id,last_post_date),
  ADD KEY node_id_sticky_state_last_post (node_id,sticky,discussion_state,last_post_date),
  ADD KEY last_post_date (last_post_date),
  ADD KEY post_date (post_date),
  ADD KEY userid (userid)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread_read
  ADD PRIMARY KEY (thread_read_id),
  ADD UNIQUE KEY user_id_thread_id (userid,thread_id),
  ADD KEY thread_id (thread_id),
  ADD KEY thread_read_date (thread_read_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread_redirect
  ADD PRIMARY KEY (thread_id),
  ADD KEY redirect_key_expiry_date (redirect_key,expiry_date)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread_reply_ban
  ADD PRIMARY KEY (thread_reply_ban_id),
  ADD UNIQUE KEY thread_id (thread_id,userid),
  ADD KEY expiry_date (expiry_date),
  ADD KEY userid (userid)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread_user_post
  ADD PRIMARY KEY (thread_id,userid),
  ADD KEY user_id (userid)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread_view
  ADD KEY thread_id (thread_id)";

$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread_watch
  ADD PRIMARY KEY (userid,thread_id),
  ADD KEY thread_id_email_subscribe (thread_id,email_subscribe)";


$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_attachment
  MODIFY attachment_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_attachment_data
  MODIFY data_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_draft
  MODIFY draft_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_edit_history
  MODIFY edit_history_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_read
  MODIFY forum_read_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_ip
  MODIFY ip_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_liked_content
  MODIFY like_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_moderator_content
  MODIFY moderator_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_moderator_log
  MODIFY moderator_log_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_node
  MODIFY node_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_combination
  MODIFY permission_combination_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_entry
  MODIFY permission_entry_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_permission_entry_content
  MODIFY permission_entry_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_poll
  MODIFY poll_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_poll_response
  MODIFY poll_response_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_post
  MODIFY post_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_post_history
  MODIFY edit_history_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_tag
  MODIFY tag_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_tag_content
  MODIFY tag_content_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_tag_result_cache
  MODIFY result_cache_id int(11) NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread
  MODIFY thread_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread_read
  MODIFY thread_read_id int(10) unsigned NOT NULL AUTO_INCREMENT";
$sql_create_module[] = "ALTER TABLE " . $db_config['prefix'] . "_" . $module_data . "_thread_reply_ban
  MODIFY thread_reply_ban_id int(10) unsigned NOT NULL AUTO_INCREMENT";
  
  
  
  
  $sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_permission (permission_id, permission_group_id, permission_type, interface_group_id, depend_permission_id, display_order, default_value, default_value_int) VALUES
('align', 'signature', 'flag', 'signaturePermissions', '', 30, 'unset', 0),
('allowed', 'avatar', 'flag', 'avatarPermissions', '', 1, 'unset', 0),
('alwaysInvite', 'conversation', 'flag', 'conversationModeratorPermissions', '', 20, 'unset', 0),
('approveUnapprove', 'forum', 'flag', 'forumModeratorPermissions', '', 330, 'unset', 0),
('approveUnapprove', 'profilePost', 'flag', 'profilePostModeratorPermissions', '', 130, 'unset', 0),
('basicText', 'signature', 'flag', 'signaturePermissions', '', 10, 'unset', 0),
('block', 'signature', 'flag', 'signaturePermissions', '', 70, 'unset', 0),
('bypassFloodCheck', 'general', 'flag', 'generalPermissions', '', 10000, 'unset', 0),
('bypassSpamCheck', 'general', 'flag', 'generalPermissions', '', 11000, 'unset', 0),
('bypassUserPrivacy', 'general', 'flag', 'generalModeratorPermissions', '', 50, 'unset', 0),
('bypassUserTagLimit', 'general', 'flag', 'generalPermissions', '', 106, 'unset', 0),
('cleanSpam', 'general', 'flag', 'generalModeratorPermissions', '', 100, 'unset', 0),
('comment', 'profilePost', 'flag', 'profilePostPermissions', '', 32, 'unset', 0),
('createTag', 'general', 'flag', 'generalPermissions', '', 105, 'unset', 0),
('deleteAny', 'profilePost', 'flag', 'profilePostModeratorPermissions', '', 20, 'unset', 0),
('deleteAnyPost', 'forum', 'flag', 'forumModeratorPermissions', '', 210, 'unset', 0),
('deleteAnyThread', 'forum', 'flag', 'forumModeratorPermissions', '', 50, 'unset', 0),
('deleteOwn', 'profilePost', 'flag', 'profilePostPermissions', '', 35, 'unset', 0),
('deleteOwnPost', 'forum', 'flag', 'forumPermissions', '', 20, 'unset', 0),
('deleteOwnThread', 'forum', 'flag', 'forumPermissions', '', 28, 'unset', 0),
('editAny', 'profilePost', 'flag', 'profilePostModeratorPermissions', '', 10, 'unset', 0),
('editAnyPost', 'conversation', 'flag', 'conversationModeratorPermissions', '', 10, 'unset', 0),
('editAnyPost', 'forum', 'flag', 'forumModeratorPermissions', '', 200, 'unset', 0),
('editBasicProfile', 'general', 'flag', 'generalModeratorPermissions', '', 130, 'unset', 0),
('editCustomTitle', 'general', 'flag', 'generalPermissions', '', 500, 'unset', 0),
('editOwn', 'profilePost', 'flag', 'profilePostPermissions', '', 40, 'unset', 0),
('editOwnPost', 'conversation', 'flag', 'conversationPermissions', '', 40, 'unset', 0),
('editOwnPost', 'forum', 'flag', 'forumPermissions', '', 20, 'unset', 0),
('editOwnPostTimeLimit', 'conversation', 'integer', 'conversationPermissions', 'editOwnPost', 50, 'unset', 2),
('editOwnPostTimeLimit', 'forum', 'integer', 'forumPermissions', '', 21, 'unset', 0),
('editOwnThreadTitle', 'forum', 'flag', 'forumPermissions', '', 25, 'unset', 0),
('editProfile', 'general', 'flag', 'generalPermissions', '', 490, 'unset', 0),
('editSignature', 'general', 'flag', 'signaturePermissions', '', 1, 'unset', 0),
('extendedText', 'signature', 'flag', 'signaturePermissions', '', 20, 'unset', 0),
('followModerationRules', 'general', 'flag', 'generalPermissions', '', 9000, 'unset', 0),
('hardDeleteAny', 'profilePost', 'flag', 'profilePostModeratorPermissions', '', 25, 'unset', 0),
('hardDeleteAnyPost', 'forum', 'flag', 'forumModeratorPermissions', '', 215, 'unset', 0),
('hardDeleteAnyThread', 'forum', 'flag', 'forumModeratorPermissions', '', 55, 'unset', 0),
('image', 'signature', 'flag', 'signaturePermissions', '', 50, 'unset', 0),
('keepOutLink', 'forum', 'flag', 'forumPermissions', '', 250, 'unset', 0),
('keepOutTextLink', 'forum', 'flag', 'forumPermissions', '', 251, 'unset', 0),
('like', 'forum', 'flag', 'forumPermissions', '', 5, 'unset', 0),
('like', 'profilePost', 'flag', 'profilePostPermissions', '', 15, 'unset', 0),
('link', 'signature', 'flag', 'signaturePermissions', '', 50, 'unset', 0),
('list', 'signature', 'flag', 'signaturePermissions', '', 40, 'unset', 0),
('lockUnlockThread', 'forum', 'flag', 'forumModeratorPermissions', '', 20, 'unset', 0),
('manageAnyTag', 'forum', 'flag', 'forumModeratorPermissions', '', 225, 'unset', 0),
('manageAnyThread', 'forum', 'flag', 'forumModeratorPermissions', '', 30, 'unset', 0),
('manageOthersTagsOwnThread', 'forum', 'flag', 'forumPermissions', '', 42, 'unset', 0),
('manageOwn', 'profilePost', 'flag', 'profilePostPermissions', '', 20, 'unset', 0),
('manageWarning', 'general', 'flag', 'generalModeratorPermissions', '', 125, 'unset', 0),
('maxFileSize', 'avatar', 'integer', 'avatarPermissions', 'allowed', 2, 'unset', 0),
('maxImages', 'signature', 'integer', 'signaturePermissions', '', 110, 'unset', 0),
('maxLines', 'signature', 'integer', 'signaturePermissions', '', 105, 'unset', 0),
('maxLinks', 'signature', 'integer', 'signaturePermissions', '', 105, 'unset', 0),
('maxPrintable', 'signature', 'integer', 'signaturePermissions', '', 100, 'unset', 0),
('maxRecipients', 'conversation', 'integer', 'conversationPermissions', 'start', 100, 'unset', 0),
('maxSmilies', 'signature', 'integer', 'signaturePermissions', '', 120, 'unset', 0),
('maxTaggedUsers', 'general', 'integer', 'generalPermissions', '', 100, 'unset', 0),
('maxTextSize', 'signature', 'integer', 'signaturePermissions', '', 130, 'unset', 0),
('media', 'signature', 'flag', 'signaturePermissions', '', 60, 'unset', 0),
('post', 'profilePost', 'flag', 'profilePostPermissions', '', 30, 'unset', 0),
('postReply', 'forum', 'flag', 'forumPermissions', '', 10, 'allow', 0),
('postThread', 'forum', 'flag', 'forumPermissions', '', 9, 'unset', 0),
('receive', 'conversation', 'flag', 'conversationPermissions', '', 12, 'unset', 0),
('report', 'general', 'flag', 'generalPermissions', '', 12000, 'unset', 0),
('requireTfa', 'general', 'flag', 'generalPermissions', '', 510, 'unset', 0),
('search', 'general', 'flag', 'generalPermissions', '', 90, 'unset', 0),
('start', 'conversation', 'flag', 'conversationPermissions', '', 10, 'unset', 0),
('stickUnstickThread', 'forum', 'flag', 'forumModeratorPermissions', '', 10, 'unset', 0),
('tagAnyThread', 'forum', 'flag', 'forumPermissions', '', 41, 'unset', 0),
('tagOwnThread', 'forum', 'flag', 'forumPermissions', '', 40, 'unset', 0),
('threadReplyBan', 'forum', 'flag', 'forumModeratorPermissions', '', 60, 'unset', 0),
('undelete', 'forum', 'flag', 'forumModeratorPermissions', '', 320, 'unset', 0),
('undelete', 'profilePost', 'flag', 'profilePostModeratorPermissions', '', 120, 'unset', 0),
('uploadAttachment', 'conversation', 'flag', 'conversationPermissions', '', 30, 'unset', 0),
('uploadAttachment', 'forum', 'flag', 'forumPermissions', 'viewAttachment', 31, 'unset', 0),
('view', 'general', 'flag', 'generalPermissions', '', 1, 'allow', 0),
('view', 'profilePost', 'flag', 'profilePostPermissions', '', 10, 'unset', 0),
('viewAttachment', 'forum', 'flag', 'forumPermissions', '', 30, 'unset', 0),
('viewContent', 'forum', 'flag', 'forumPermissions', '', 2, 'unset', 0),
('viewDeleted', 'forum', 'flag', 'forumModeratorPermissions', '', 300, 'unset', 0),
('viewDeleted', 'profilePost', 'flag', 'profilePostModeratorPermissions', '', 100, 'unset', 0),
('viewIps', 'general', 'flag', 'generalModeratorPermissions', '', 10, 'unset', 0),
('viewMemberList', 'general', 'flag', 'generalPermissions', '', 45, 'unset', 0),
('viewModerated', 'forum', 'flag', 'forumModeratorPermissions', '', 310, 'unset', 0),
('viewModerated', 'profilePost', 'flag', 'profilePostModeratorPermissions', '', 110, 'unset', 0),
('viewNode', 'general', 'flag', 'generalPermissions', 'view', 2, 'unset', 0),
('viewOthers', 'forum', 'flag', 'forumPermissions', '', 1, 'unset', 0),
('viewProfile', 'general', 'flag', 'generalPermissions', '', 50, 'unset', 0),
('viewWarning', 'general', 'flag', 'generalModeratorPermissions', '', 110, 'unset', 0),
('votePoll', 'forum', 'flag', 'forumPermissions', '', 100, 'unset', 0),
('warn', 'forum', 'flag', 'forumModeratorPermissions', '', 220, 'unset', 0),
('warn', 'general', 'flag', 'generalModeratorPermissions', '', 120, 'unset', 0),
('warn', 'profilePost', 'flag', 'profilePostModeratorPermissions', '', 30, 'unset', 0)";


$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_permission_combination_user_group (user_group_id, permission_combination_id) VALUES
(5, 5),
(7, 7),
(1, 1),
(1, 2),
(1, 3),
(2, 2),
(2, 3),
(3, 3),
(4, 4),
(6, 6)";

$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_permission_entry (permission_entry_id, user_group_id, userid, permission_group_id, permission_id, permission_value, permission_value_int) VALUES
(1, 5, 0, 'forum', 'viewContent', 'allow', 0),
(2, 5, 0, 'forum', 'viewOthers', 'allow', 0),
(3, 5, 0, 'general', 'followModerationRules', 'allow', 0),
(4, 5, 0, 'general', 'editProfile', 'allow', 0),
(5, 5, 0, 'general', 'search', 'allow', 0),
(6, 5, 0, 'general', 'view', 'allow', 0),
(7, 5, 0, 'general', 'viewNode', 'allow', 0),
(8, 5, 0, 'general', 'viewProfile', 'allow', 0),
(9, 5, 0, 'general', 'viewMemberList', 'allow', 0),
(10, 5, 0, 'profilePost', 'view', 'allow', 0),
(63, 1, 0, 'avatar', 'allowed', 'allow', 0),
(66, 1, 0, 'conversation', 'editAnyPost', 'allow', 0),
(67, 1, 0, 'conversation', 'alwaysInvite', 'allow', 0),
(68, 1, 0, 'conversation', 'uploadAttachment', 'allow', 0),
(69, 1, 0, 'forum', 'maxTaggedUsers', 'use_int', -1),
(70, 1, 0, 'forum', 'deleteOwnThread', 'allow', 0),
(71, 1, 0, 'forum', 'manageOthersTagsOwnThread', 'allow', 0),
(73, 1, 0, 'general', 'bypassFloodCheck', 'allow', 0),
(74, 1, 0, 'general', 'editCustomTitle', 'allow', 0),
(75, 2, 0, 'avatar', 'allowed', 'allow', 0),
(76, 2, 0, 'avatar', 'maxFileSize', 'use_int', -1),
(77, 2, 0, 'conversation', 'maxRecipients', 'use_int', -1),
(78, 2, 0, 'conversation', 'editAnyPost', 'allow', 0),
(79, 2, 0, 'conversation', 'alwaysInvite', 'allow', 0),
(80, 2, 0, 'conversation', 'uploadAttachment', 'allow', 0),
(81, 2, 0, 'forum', 'maxTaggedUsers', 'use_int', -1),
(82, 2, 0, 'forum', 'deleteOwnThread', 'allow', 0),
(83, 2, 0, 'forum', 'manageOthersTagsOwnThread', 'allow', 0),
(84, 2, 0, 'forum', 'editOwnPostTimeLimit', 'use_int', -1),
(85, 2, 0, 'general', 'bypassFloodCheck', 'allow', 0),
(86, 2, 0, 'general', 'editCustomTitle', 'allow', 0),
(87, 3, 0, 'avatar', 'maxFileSize', 'use_int', -1),
(88, 3, 0, 'conversation', 'maxRecipients', 'use_int', -1),
(89, 3, 0, 'conversation', 'uploadAttachment', 'allow', 0),
(90, 3, 0, 'forum', 'maxTaggedUsers', 'use_int', -1),
(91, 3, 0, 'forum', 'editOwnPostTimeLimit', 'use_int', -1),
(92, 3, 0, 'general', 'bypassFloodCheck', 'allow', 0),
(93, 3, 0, 'general', 'editCustomTitle', 'allow', 0),
(1592, 4, 0, 'general', 'viewMemberList', 'allow', 0),
(1593, 4, 0, 'general', 'viewProfile', 'allow', 0),
(1594, 4, 0, 'general', 'search', 'allow', 0),
(1595, 4, 0, 'general', 'createTag', 'allow', 0),
(1596, 4, 0, 'general', 'bypassUserTagLimit', 'allow', 0),
(1597, 4, 0, 'general', 'editProfile', 'allow', 0),
(1598, 4, 0, 'general', 'editCustomTitle', 'allow', 0),
(1599, 4, 0, 'general', 'requireTfa', 'allow', 0),
(1600, 4, 0, 'general', 'followModerationRules', 'allow', 0),
(1601, 4, 0, 'general', 'bypassFloodCheck', 'allow', 0),
(1602, 4, 0, 'general', 'bypassSpamCheck', 'allow', 0),
(1605, 4, 0, 'forum', 'viewContent', 'allow', 0),
(1606, 4, 0, 'forum', 'like', 'allow', 0),
(1608, 4, 0, 'forum', 'postReply', 'allow', 0),
(1609, 4, 0, 'forum', 'editOwnPost', 'allow', 0),
(1610, 4, 0, 'forum', 'deleteOwnPost', 'allow', 0),
(1611, 4, 0, 'forum', 'editOwnThreadTitle', 'allow', 0),
(1612, 4, 0, 'forum', 'deleteOwnThread', 'allow', 0),
(1613, 4, 0, 'forum', 'viewAttachment', 'allow', 0),
(1614, 4, 0, 'forum', 'uploadAttachment', 'allow', 0),
(1615, 4, 0, 'forum', 'tagOwnThread', 'allow', 0),
(1616, 4, 0, 'forum', 'tagAnyThread', 'allow', 0),
(1617, 4, 0, 'forum', 'manageOthersTagsOwnThread', 'allow', 0),
(1618, 4, 0, 'forum', 'votePoll', 'allow', 0),
(1619, 4, 0, 'general', 'view', 'allow', 0),
(1620, 4, 0, 'general', 'viewNode', 'allow', 0),
(1621, 4, 0, 'forum', 'postThread', 'allow', 0),
(1625, 4, 0, 'general', 'report', 'allow', 0),
(1627, 1, 0, 'general', 'view', 'allow', 0),
(1628, 1, 0, 'general', 'viewNode', 'allow', 0),
(1629, 1, 0, 'general', 'viewMemberList', 'allow', 0),
(1630, 1, 0, 'general', 'viewProfile', 'allow', 0),
(1631, 1, 0, 'general', 'search', 'allow', 0),
(1632, 1, 0, 'general', 'createTag', 'allow', 0),
(1633, 1, 0, 'general', 'bypassUserTagLimit', 'allow', 0),
(1634, 1, 0, 'general', 'editProfile', 'allow', 0),
(1635, 1, 0, 'general', 'requireTfa', 'allow', 0),
(1636, 1, 0, 'general', 'followModerationRules', 'allow', 0),
(1637, 1, 0, 'general', 'bypassSpamCheck', 'allow', 0),
(1638, 1, 0, 'general', 'report', 'allow', 0),
(1639, 1, 0, 'general', 'viewIps', 'allow', 0),
(1640, 1, 0, 'general', 'bypassUserPrivacy', 'allow', 0),
(1641, 1, 0, 'general', 'cleanSpam', 'allow', 0),
(1642, 1, 0, 'general', 'viewWarning', 'allow', 0),
(1643, 1, 0, 'general', 'warn', 'allow', 0),
(1644, 1, 0, 'general', 'manageWarning', 'allow', 0),
(1645, 1, 0, 'general', 'editBasicProfile', 'allow', 0),
(1646, 1, 0, 'general', 'editSignature', 'allow', 0),
(1647, 1, 0, 'forum', 'viewOthers', 'allow', 0),
(1648, 1, 0, 'forum', 'viewContent', 'allow', 0),
(1649, 1, 0, 'forum', 'like', 'allow', 0),
(1650, 1, 0, 'forum', 'postThread', 'allow', 0),
(1651, 1, 0, 'forum', 'postReply', 'allow', 0),
(1652, 1, 0, 'forum', 'editOwnPost', 'allow', 0),
(1653, 1, 0, 'forum', 'deleteOwnPost', 'allow', 0),
(1654, 1, 0, 'forum', 'editOwnThreadTitle', 'allow', 0),
(1655, 1, 0, 'forum', 'viewAttachment', 'allow', 0),
(1656, 1, 0, 'forum', 'uploadAttachment', 'allow', 0),
(1657, 1, 0, 'forum', 'tagOwnThread', 'allow', 0),
(1658, 1, 0, 'forum', 'tagAnyThread', 'allow', 0),
(1659, 1, 0, 'forum', 'votePoll', 'allow', 0),
(1660, 1, 0, 'forum', 'stickUnstickThread', 'allow', 0),
(1661, 1, 0, 'forum', 'lockUnlockThread', 'allow', 0),
(1662, 1, 0, 'forum', 'manageAnyThread', 'allow', 0),
(1663, 1, 0, 'forum', 'deleteAnyThread', 'allow', 0),
(1664, 1, 0, 'forum', 'hardDeleteAnyThread', 'allow', 0),
(1665, 1, 0, 'forum', 'threadReplyBan', 'allow', 0),
(1666, 1, 0, 'forum', 'editAnyPost', 'allow', 0),
(1667, 1, 0, 'forum', 'deleteAnyPost', 'allow', 0),
(1668, 1, 0, 'forum', 'hardDeleteAnyPost', 'allow', 0),
(1669, 1, 0, 'forum', 'warn', 'allow', 0),
(1670, 1, 0, 'forum', 'manageAnyTag', 'allow', 0),
(1671, 1, 0, 'forum', 'viewDeleted', 'allow', 0),
(1672, 1, 0, 'forum', 'viewModerated', 'allow', 0),
(1673, 1, 0, 'forum', 'undelete', 'allow', 0),
(1674, 1, 0, 'forum', 'approveUnapprove', 'allow', 0),
(1675, 1, 0, 'conversation', 'start', 'allow', 0),
(1676, 1, 0, 'conversation', 'receive', 'allow', 0),
(1677, 1, 0, 'conversation', 'editOwnPost', 'allow', 0),
(1678, 1, 0, 'signature', 'basicText', 'allow', 0),
(1679, 1, 0, 'signature', 'extendedText', 'allow', 0),
(1680, 1, 0, 'signature', 'align', 'allow', 0),
(1681, 1, 0, 'signature', 'list', 'allow', 0),
(1682, 1, 0, 'signature', 'image', 'allow', 0),
(1683, 1, 0, 'signature', 'link', 'allow', 0),
(1684, 1, 0, 'signature', 'media', 'allow', 0),
(1685, 1, 0, 'signature', 'block', 'allow', 0),
(1686, 1, 0, 'signature', 'maxPrintable', 'use_int', -1),
(1687, 1, 0, 'signature', 'maxLines', 'use_int', -1),
(1688, 1, 0, 'signature', 'maxLinks', 'use_int', -1),
(1689, 1, 0, 'signature', 'maxImages', 'use_int', -1),
(1690, 1, 0, 'signature', 'maxSmilies', 'use_int', -1),
(1691, 1, 0, 'signature', 'maxTextSize', 'use_int', -1),
(1692, 1, 0, 'profilePost', 'view', 'allow', 0),
(1693, 1, 0, 'profilePost', 'like', 'allow', 0),
(1694, 1, 0, 'profilePost', 'manageOwn', 'allow', 0),
(1695, 1, 0, 'profilePost', 'post', 'allow', 0),
(1696, 1, 0, 'profilePost', 'comment', 'allow', 0),
(1697, 1, 0, 'profilePost', 'deleteOwn', 'allow', 0),
(1698, 1, 0, 'profilePost', 'editOwn', 'allow', 0),
(1699, 1, 0, 'profilePost', 'editAny', 'allow', 0),
(1700, 1, 0, 'profilePost', 'deleteAny', 'allow', 0),
(1701, 1, 0, 'profilePost', 'hardDeleteAny', 'allow', 0),
(1702, 1, 0, 'profilePost', 'warn', 'allow', 0),
(1703, 1, 0, 'profilePost', 'viewDeleted', 'allow', 0),
(1704, 1, 0, 'profilePost', 'viewModerated', 'allow', 0),
(1705, 1, 0, 'profilePost', 'undelete', 'allow', 0),
(1706, 1, 0, 'profilePost', 'approveUnapprove', 'allow', 0),
(1708, 5, 0, 'forum', 'approveUnapprove', 'deny', 0),
(1784, 0, 0, 'general', 'viewIps', 'allow', 1),
(1785, 0, 0, 'general', 'cleanSpam', 'allow', 1),
(1786, 0, 0, 'general', 'viewWarning', 'allow', 1),
(1787, 0, 0, 'general', 'warn', 'allow', 1),
(1788, 0, 0, 'general', 'manageWarning', 'allow', 1),
(1789, 0, 0, 'general', 'editBasicProfile', 'allow', 1),
(1790, 0, 0, 'profilePost', 'deleteAny', 'allow', 1),
(1791, 0, 0, 'profilePost', 'warn', 'allow', 1),
(1792, 0, 0, 'profilePost', 'viewDeleted', 'allow', 1),
(1793, 0, 0, 'profilePost', 'viewModerated', 'allow', 1),
(1794, 0, 0, 'profilePost', 'undelete', 'allow', 1),
(1795, 0, 0, 'profilePost', 'approveUnapprove', 'allow', 1),
(1796, 0, 0, 'conversation', 'editAnyPost', 'allow', 1),
(1797, 0, 0, 'conversation', 'alwaysInvite', 'allow', 1),
(1798, 0, 0, 'general', 'bypassUserPrivacy', 'allow', 0),
(1799, 0, 0, 'profilePost', 'editAny', 'allow', 0),
(1800, 0, 0, 'profilePost', 'hardDeleteAny', 'allow', 0),
(1988, 0, 2, 'general', 'view', 'allow', 0),
(1989, 0, 2, 'general', 'viewNode', 'allow', 0),
(1990, 0, 2, 'general', 'viewMemberList', 'allow', 0),
(1991, 0, 2, 'general', 'viewProfile', 'allow', 0),
(1992, 0, 2, 'general', 'search', 'allow', 0),
(1993, 0, 2, 'general', 'createTag', 'allow', 0),
(1994, 0, 2, 'general', 'bypassUserTagLimit', 'allow', 0),
(1995, 0, 2, 'general', 'editProfile', 'allow', 0),
(1996, 0, 2, 'general', 'editCustomTitle', 'allow', 0),
(1997, 0, 2, 'general', 'requireTfa', 'allow', 0),
(1998, 0, 2, 'general', 'followModerationRules', 'allow', 0),
(1999, 0, 2, 'general', 'bypassFloodCheck', 'allow', 0),
(2000, 0, 2, 'general', 'bypassSpamCheck', 'allow', 0),
(2001, 0, 2, 'general', 'report', 'allow', 0),
(2002, 0, 2, 'general', 'viewIps', 'allow', 0),
(2003, 0, 2, 'general', 'bypassUserPrivacy', 'allow', 0),
(2004, 0, 2, 'general', 'cleanSpam', 'allow', 0),
(2005, 0, 2, 'general', 'viewWarning', 'allow', 0),
(2006, 0, 2, 'general', 'warn', 'allow', 0),
(2007, 0, 2, 'general', 'manageWarning', 'allow', 0),
(2008, 0, 2, 'general', 'editBasicProfile', 'allow', 0),
(2009, 0, 2, 'general', 'editSignature', 'allow', 0),
(2010, 0, 2, 'forum', 'viewOthers', 'allow', 0),
(2011, 0, 2, 'forum', 'viewContent', 'allow', 0),
(2012, 0, 2, 'forum', 'like', 'allow', 0),
(2013, 0, 2, 'forum', 'postThread', 'allow', 0),
(2014, 0, 2, 'forum', 'postReply', 'allow', 0),
(2015, 0, 2, 'forum', 'editOwnPost', 'allow', 0),
(2016, 0, 2, 'forum', 'deleteOwnPost', 'allow', 0),
(2017, 0, 2, 'forum', 'editOwnThreadTitle', 'allow', 0),
(2018, 0, 2, 'forum', 'deleteOwnThread', 'allow', 0),
(2019, 0, 2, 'forum', 'viewAttachment', 'allow', 0),
(2020, 0, 2, 'forum', 'uploadAttachment', 'allow', 0),
(2021, 0, 2, 'forum', 'tagOwnThread', 'allow', 0),
(2022, 0, 2, 'forum', 'tagAnyThread', 'allow', 0),
(2023, 0, 2, 'forum', 'manageOthersTagsOwnThread', 'allow', 0),
(2024, 0, 2, 'forum', 'votePoll', 'allow', 0),
(2025, 0, 2, 'forum', 'stickUnstickThread', 'allow', 0),
(2026, 0, 2, 'forum', 'lockUnlockThread', 'allow', 0),
(2027, 0, 2, 'forum', 'manageAnyThread', 'allow', 0),
(2028, 0, 2, 'forum', 'deleteAnyThread', 'allow', 0),
(2029, 0, 2, 'forum', 'hardDeleteAnyThread', 'allow', 0),
(2030, 0, 2, 'forum', 'threadReplyBan', 'allow', 0),
(2031, 0, 2, 'forum', 'editAnyPost', 'allow', 0),
(2032, 0, 2, 'forum', 'deleteAnyPost', 'allow', 0),
(2033, 0, 2, 'forum', 'hardDeleteAnyPost', 'allow', 0),
(2034, 0, 2, 'forum', 'warn', 'allow', 0),
(2035, 0, 2, 'forum', 'manageAnyTag', 'allow', 0),
(2036, 0, 2, 'forum', 'viewDeleted', 'allow', 0),
(2037, 0, 2, 'forum', 'viewModerated', 'allow', 0),
(2038, 0, 2, 'forum', 'undelete', 'allow', 0),
(2039, 0, 2, 'forum', 'approveUnapprove', 'allow', 0),
(2040, 0, 2, 'avatar', 'allowed', 'allow', 0),
(2041, 0, 2, 'conversation', 'start', 'allow', 0),
(2042, 0, 2, 'conversation', 'receive', 'allow', 0),
(2043, 0, 2, 'conversation', 'uploadAttachment', 'allow', 0),
(2044, 0, 2, 'conversation', 'editOwnPost', 'allow', 0),
(2045, 0, 2, 'conversation', 'editAnyPost', 'allow', 0),
(2046, 0, 2, 'conversation', 'alwaysInvite', 'allow', 0),
(2047, 0, 2, 'signature', 'basicText', 'allow', 0),
(2048, 0, 2, 'signature', 'extendedText', 'allow', 0),
(2049, 0, 2, 'signature', 'align', 'allow', 0),
(2050, 0, 2, 'signature', 'list', 'allow', 0),
(2051, 0, 2, 'signature', 'image', 'allow', 0),
(2052, 0, 2, 'signature', 'link', 'allow', 0),
(2053, 0, 2, 'signature', 'media', 'allow', 0),
(2054, 0, 2, 'signature', 'block', 'allow', 0),
(2055, 0, 2, 'profilePost', 'view', 'allow', 0),
(2056, 0, 2, 'profilePost', 'like', 'allow', 0),
(2057, 0, 2, 'profilePost', 'manageOwn', 'allow', 0),
(2058, 0, 2, 'profilePost', 'post', 'allow', 0),
(2059, 0, 2, 'profilePost', 'comment', 'allow', 0),
(2060, 0, 2, 'profilePost', 'deleteOwn', 'allow', 0),
(2061, 0, 2, 'profilePost', 'editOwn', 'allow', 0),
(2062, 0, 2, 'profilePost', 'editAny', 'allow', 0),
(2063, 0, 2, 'profilePost', 'deleteAny', 'allow', 0),
(2064, 0, 2, 'profilePost', 'hardDeleteAny', 'allow', 0),
(2065, 0, 2, 'profilePost', 'warn', 'allow', 0),
(2066, 0, 2, 'profilePost', 'viewDeleted', 'allow', 0),
(2067, 0, 2, 'profilePost', 'viewModerated', 'allow', 0),
(2068, 0, 2, 'profilePost', 'undelete', 'allow', 0),
(2069, 0, 2, 'profilePost', 'approveUnapprove', 'allow', 0),
(2071, 4, 0, 'forum', 'viewOthers', 'allow', 0),
(2079, 4, 0, 'general', 'editSignature', 'allow', 0),
(2101, 4, 0, 'signature', 'basicText', 'allow', 0),
(2102, 4, 0, 'signature', 'extendedText', 'allow', 0),
(2103, 4, 0, 'signature', 'align', 'allow', 0),
(2104, 4, 0, 'signature', 'list', 'allow', 0),
(2105, 4, 0, 'signature', 'image', 'allow', 0),
(2106, 4, 0, 'signature', 'link', 'allow', 0),
(2107, 4, 0, 'signature', 'media', 'allow', 0),
(2108, 4, 0, 'signature', 'block', 'allow', 0),
(2109, 4, 0, 'profilePost', 'view', 'allow', 0),
(2110, 4, 0, 'profilePost', 'like', 'allow', 0),
(2111, 4, 0, 'profilePost', 'manageOwn', 'allow', 0),
(2112, 4, 0, 'profilePost', 'post', 'allow', 0),
(2113, 4, 0, 'profilePost', 'comment', 'allow', 0),
(2114, 4, 0, 'profilePost', 'deleteOwn', 'allow', 0),
(2115, 4, 0, 'profilePost', 'editOwn', 'allow', 0),
(2172, 4, 0, 'signature', 'maxPrintable', 'use_int', -1),
(2173, 4, 0, 'signature', 'maxLines', 'use_int', -1),
(2174, 4, 0, 'signature', 'maxLinks', 'use_int', -1),
(2175, 4, 0, 'signature', 'maxImages', 'use_int', -1),
(2176, 4, 0, 'signature', 'maxSmilies', 'use_int', -1),
(2177, 4, 0, 'signature', 'maxTextSize', 'use_int', -1),
(2178, 4, 0, 'general', 'maxTaggedUsers', 'use_int', -1),
(2179, 4, 0, 'forum', 'editOwnPostTimeLimit', 'use_int', 10080),
(2180, 4, 0, 'avatar', 'maxFileSize', 'use_int', -1),
(2181, 4, 0, 'conversation', 'editOwnPostTimeLimit', 'use_int', -1),
(2182, 4, 0, 'conversation', 'maxRecipients', 'use_int', -1),
(2183, 0, 1, 'general', 'view', 'allow', 0),
(2184, 0, 1, 'general', 'viewNode', 'allow', 0),
(2185, 0, 1, 'general', 'viewMemberList', 'allow', 0),
(2186, 0, 1, 'general', 'viewProfile', 'allow', 0),
(2187, 0, 1, 'general', 'search', 'allow', 0),
(2188, 0, 1, 'general', 'createTag', 'allow', 0),
(2189, 0, 1, 'general', 'bypassUserTagLimit', 'allow', 0),
(2190, 0, 1, 'general', 'editProfile', 'allow', 0),
(2191, 0, 1, 'general', 'editCustomTitle', 'allow', 0),
(2192, 0, 1, 'general', 'requireTfa', 'allow', 0),
(2193, 0, 1, 'general', 'followModerationRules', 'allow', 0),
(2194, 0, 1, 'general', 'bypassFloodCheck', 'allow', 0),
(2195, 0, 1, 'general', 'bypassSpamCheck', 'allow', 0),
(2196, 0, 1, 'general', 'report', 'allow', 0),
(2197, 0, 1, 'general', 'viewIps', 'allow', 0),
(2198, 0, 1, 'general', 'bypassUserPrivacy', 'allow', 0),
(2199, 0, 1, 'general', 'cleanSpam', 'allow', 0),
(2200, 0, 1, 'general', 'viewWarning', 'allow', 0),
(2201, 0, 1, 'general', 'warn', 'allow', 0),
(2202, 0, 1, 'general', 'manageWarning', 'allow', 0),
(2203, 0, 1, 'general', 'editBasicProfile', 'allow', 0),
(2204, 0, 1, 'general', 'editSignature', 'allow', 0),
(2205, 0, 1, 'forum', 'viewOthers', 'allow', 0),
(2206, 0, 1, 'forum', 'viewContent', 'allow', 0),
(2207, 0, 1, 'forum', 'like', 'allow', 0),
(2208, 0, 1, 'forum', 'postThread', 'allow', 0),
(2209, 0, 1, 'forum', 'postReply', 'allow', 0),
(2210, 0, 1, 'forum', 'editOwnPost', 'allow', 0),
(2211, 0, 1, 'forum', 'deleteOwnPost', 'allow', 0),
(2212, 0, 1, 'forum', 'editOwnThreadTitle', 'allow', 0),
(2213, 0, 1, 'forum', 'deleteOwnThread', 'allow', 0),
(2214, 0, 1, 'forum', 'viewAttachment', 'allow', 0),
(2215, 0, 1, 'forum', 'uploadAttachment', 'allow', 0),
(2216, 0, 1, 'forum', 'tagOwnThread', 'allow', 0),
(2217, 0, 1, 'forum', 'tagAnyThread', 'allow', 0),
(2218, 0, 1, 'forum', 'manageOthersTagsOwnThread', 'allow', 0),
(2219, 0, 1, 'forum', 'votePoll', 'allow', 0),
(2220, 0, 1, 'forum', 'stickUnstickThread', 'allow', 0),
(2221, 0, 1, 'forum', 'lockUnlockThread', 'allow', 0),
(2222, 0, 1, 'forum', 'manageAnyThread', 'allow', 0),
(2223, 0, 1, 'forum', 'deleteAnyThread', 'allow', 0),
(2224, 0, 1, 'forum', 'hardDeleteAnyThread', 'allow', 0),
(2225, 0, 1, 'forum', 'threadReplyBan', 'allow', 0),
(2226, 0, 1, 'forum', 'editAnyPost', 'allow', 0),
(2227, 0, 1, 'forum', 'deleteAnyPost', 'allow', 0),
(2228, 0, 1, 'forum', 'hardDeleteAnyPost', 'allow', 0),
(2229, 0, 1, 'forum', 'warn', 'allow', 0),
(2230, 0, 1, 'forum', 'manageAnyTag', 'allow', 0),
(2231, 0, 1, 'forum', 'viewDeleted', 'allow', 0),
(2232, 0, 1, 'forum', 'viewModerated', 'allow', 0),
(2233, 0, 1, 'forum', 'undelete', 'allow', 0),
(2234, 0, 1, 'forum', 'approveUnapprove', 'allow', 0),
(2235, 0, 1, 'avatar', 'allowed', 'allow', 0),
(2236, 0, 1, 'conversation', 'start', 'allow', 0),
(2237, 0, 1, 'conversation', 'receive', 'allow', 0),
(2238, 0, 1, 'conversation', 'uploadAttachment', 'allow', 0),
(2239, 0, 1, 'conversation', 'editOwnPost', 'allow', 0),
(2240, 0, 1, 'conversation', 'editAnyPost', 'allow', 0),
(2241, 0, 1, 'conversation', 'alwaysInvite', 'allow', 0),
(2242, 0, 1, 'signature', 'basicText', 'allow', 0),
(2243, 0, 1, 'signature', 'extendedText', 'allow', 0),
(2244, 0, 1, 'signature', 'align', 'allow', 0),
(2245, 0, 1, 'signature', 'list', 'allow', 0),
(2246, 0, 1, 'signature', 'image', 'allow', 0),
(2247, 0, 1, 'signature', 'link', 'allow', 0),
(2248, 0, 1, 'signature', 'media', 'allow', 0),
(2249, 0, 1, 'signature', 'block', 'allow', 0),
(2250, 0, 1, 'profilePost', 'view', 'allow', 0),
(2251, 0, 1, 'profilePost', 'like', 'allow', 0),
(2252, 0, 1, 'profilePost', 'manageOwn', 'allow', 0),
(2253, 0, 1, 'profilePost', 'post', 'allow', 0),
(2254, 0, 1, 'profilePost', 'comment', 'allow', 0),
(2255, 0, 1, 'profilePost', 'deleteOwn', 'allow', 0),
(2256, 0, 1, 'profilePost', 'editOwn', 'allow', 0),
(2257, 0, 1, 'profilePost', 'editAny', 'allow', 0),
(2258, 0, 1, 'profilePost', 'deleteAny', 'allow', 0),
(2259, 0, 1, 'profilePost', 'hardDeleteAny', 'allow', 0),
(2260, 0, 1, 'profilePost', 'warn', 'allow', 0),
(2261, 0, 1, 'profilePost', 'viewDeleted', 'allow', 0),
(2262, 0, 1, 'profilePost', 'viewModerated', 'allow', 0),
(2263, 0, 1, 'profilePost', 'undelete', 'allow', 0),
(2264, 0, 1, 'profilePost', 'approveUnapprove', 'allow', 0),
(2266, 0, 1, 'general', 'maxTaggedUsers', 'use_int', -1),
(2267, 0, 1, 'avatar', 'maxFileSize', 'use_int', -1),
(2268, 0, 1, 'conversation', 'editOwnPostTimeLimit', 'use_int', -1),
(2269, 0, 1, 'conversation', 'maxRecipients', 'use_int', -1),
(2270, 0, 1, 'signature', 'maxPrintable', 'use_int', -1),
(2271, 0, 1, 'signature', 'maxLines', 'use_int', -1),
(2272, 0, 1, 'signature', 'maxLinks', 'use_int', -1),
(2273, 0, 1, 'signature', 'maxImages', 'use_int', -1),
(2274, 0, 1, 'signature', 'maxSmilies', 'use_int', -1),
(2275, 0, 1, 'signature', 'maxTextSize', 'use_int', -1),
(2276, 1, 0, 'forum', 'keepOutLink', 'allow', 0),
(2277, 1, 0, 'forum', 'keepOutTextLink', 'allow', 0),
(2278, 2, 0, 'general', 'view', 'allow', 0),
(2279, 2, 0, 'general', 'viewNode', 'allow', 0),
(2280, 2, 0, 'general', 'viewMemberList', 'allow', 0),
(2281, 2, 0, 'general', 'viewProfile', 'allow', 0),
(2282, 2, 0, 'general', 'search', 'allow', 0),
(2283, 2, 0, 'general', 'maxTaggedUsers', 'use_int', -1),
(2284, 2, 0, 'general', 'createTag', 'allow', 0),
(2285, 2, 0, 'general', 'bypassUserTagLimit', 'allow', 0),
(2286, 2, 0, 'general', 'editProfile', 'allow', 0),
(2287, 2, 0, 'general', 'requireTfa', 'allow', 0),
(2288, 2, 0, 'general', 'followModerationRules', 'allow', 0),
(2289, 2, 0, 'general', 'bypassSpamCheck', 'allow', 0),
(2290, 2, 0, 'general', 'report', 'allow', 0),
(2291, 2, 0, 'general', 'viewIps', 'allow', 0),
(2292, 2, 0, 'general', 'bypassUserPrivacy', 'allow', 0),
(2293, 2, 0, 'general', 'cleanSpam', 'allow', 0),
(2294, 2, 0, 'general', 'viewWarning', 'allow', 0),
(2295, 2, 0, 'general', 'warn', 'allow', 0),
(2296, 2, 0, 'general', 'manageWarning', 'allow', 0),
(2297, 2, 0, 'general', 'editBasicProfile', 'allow', 0),
(2298, 2, 0, 'general', 'editSignature', 'allow', 0),
(2299, 2, 0, 'forum', 'viewOthers', 'allow', 0),
(2300, 2, 0, 'forum', 'viewContent', 'allow', 0),
(2301, 2, 0, 'forum', 'like', 'allow', 0),
(2302, 2, 0, 'forum', 'postThread', 'allow', 0),
(2303, 2, 0, 'forum', 'postReply', 'allow', 0),
(2304, 2, 0, 'forum', 'editOwnPost', 'allow', 0),
(2305, 2, 0, 'forum', 'deleteOwnPost', 'allow', 0),
(2306, 2, 0, 'forum', 'editOwnThreadTitle', 'allow', 0),
(2307, 2, 0, 'forum', 'viewAttachment', 'allow', 0),
(2308, 2, 0, 'forum', 'uploadAttachment', 'allow', 0),
(2309, 2, 0, 'forum', 'tagOwnThread', 'allow', 0),
(2310, 2, 0, 'forum', 'tagAnyThread', 'allow', 0),
(2311, 2, 0, 'forum', 'votePoll', 'allow', 0),
(2312, 2, 0, 'forum', 'keepOutLink', 'allow', 0),
(2313, 2, 0, 'forum', 'keepOutTextLink', 'allow', 0),
(2314, 2, 0, 'forum', 'stickUnstickThread', 'allow', 0),
(2315, 2, 0, 'forum', 'lockUnlockThread', 'allow', 0),
(2316, 2, 0, 'forum', 'manageAnyThread', 'allow', 0),
(2317, 2, 0, 'forum', 'deleteAnyThread', 'allow', 0),
(2318, 2, 0, 'forum', 'hardDeleteAnyThread', 'allow', 0),
(2319, 2, 0, 'forum', 'threadReplyBan', 'allow', 0),
(2320, 2, 0, 'forum', 'editAnyPost', 'allow', 0),
(2321, 2, 0, 'forum', 'deleteAnyPost', 'allow', 0),
(2322, 2, 0, 'forum', 'hardDeleteAnyPost', 'allow', 0),
(2323, 2, 0, 'forum', 'warn', 'allow', 0),
(2324, 2, 0, 'forum', 'manageAnyTag', 'allow', 0),
(2325, 2, 0, 'forum', 'viewDeleted', 'allow', 0),
(2326, 2, 0, 'forum', 'viewModerated', 'allow', 0),
(2327, 2, 0, 'forum', 'undelete', 'allow', 0),
(2328, 2, 0, 'forum', 'approveUnapprove', 'allow', 0),
(2329, 2, 0, 'conversation', 'start', 'allow', 0),
(2330, 2, 0, 'conversation', 'receive', 'allow', 0),
(2331, 2, 0, 'conversation', 'editOwnPost', 'allow', 0),
(2332, 2, 0, 'conversation', 'editOwnPostTimeLimit', 'use_int', -1),
(2333, 2, 0, 'signature', 'basicText', 'allow', 0),
(2334, 2, 0, 'signature', 'extendedText', 'allow', 0),
(2335, 2, 0, 'signature', 'align', 'allow', 0),
(2336, 2, 0, 'signature', 'list', 'allow', 0),
(2337, 2, 0, 'signature', 'link', 'allow', 0),
(2338, 2, 0, 'signature', 'image', 'allow', 0),
(2339, 2, 0, 'signature', 'media', 'allow', 0),
(2340, 2, 0, 'signature', 'block', 'allow', 0),
(2341, 2, 0, 'signature', 'maxPrintable', 'use_int', -1),
(2342, 2, 0, 'signature', 'maxLines', 'use_int', -1),
(2343, 2, 0, 'signature', 'maxLinks', 'use_int', -1),
(2344, 2, 0, 'signature', 'maxImages', 'use_int', -1),
(2345, 2, 0, 'signature', 'maxSmilies', 'use_int', -1),
(2346, 2, 0, 'signature', 'maxTextSize', 'use_int', -1),
(2347, 2, 0, 'profilePost', 'view', 'allow', 0),
(2348, 2, 0, 'profilePost', 'like', 'allow', 0),
(2349, 2, 0, 'profilePost', 'manageOwn', 'allow', 0),
(2350, 2, 0, 'profilePost', 'post', 'allow', 0),
(2351, 2, 0, 'profilePost', 'comment', 'allow', 0),
(2352, 2, 0, 'profilePost', 'deleteOwn', 'allow', 0),
(2353, 2, 0, 'profilePost', 'editOwn', 'allow', 0),
(2354, 2, 0, 'profilePost', 'editAny', 'allow', 0),
(2355, 2, 0, 'profilePost', 'deleteAny', 'allow', 0),
(2356, 2, 0, 'profilePost', 'hardDeleteAny', 'allow', 0),
(2357, 2, 0, 'profilePost', 'warn', 'allow', 0),
(2358, 2, 0, 'profilePost', 'viewDeleted', 'allow', 0),
(2359, 2, 0, 'profilePost', 'viewModerated', 'allow', 0),
(2360, 2, 0, 'profilePost', 'undelete', 'allow', 0),
(2361, 2, 0, 'profilePost', 'approveUnapprove', 'allow', 0),
(2362, 4, 0, 'avatar', 'allowed', 'allow', 0),
(2363, 4, 0, 'forum', 'keepOutLink', 'deny', 0),
(2364, 4, 0, 'forum', 'keepOutTextLink', 'allow', 0),
(2365, 0, 44, 'general', 'view', 'allow', 0),
(2366, 0, 44, 'general', 'viewNode', 'allow', 0),
(2367, 0, 44, 'general', 'viewMemberList', 'allow', 0),
(2368, 0, 44, 'general', 'viewProfile', 'allow', 0),
(2369, 0, 44, 'general', 'search', 'allow', 0),
(2370, 0, 44, 'general', 'createTag', 'allow', 0),
(2371, 0, 44, 'general', 'bypassUserTagLimit', 'allow', 0),
(2372, 0, 44, 'general', 'editProfile', 'allow', 0),
(2373, 0, 44, 'general', 'editCustomTitle', 'allow', 0),
(2374, 0, 44, 'general', 'requireTfa', 'allow', 0),
(2375, 0, 44, 'general', 'followModerationRules', 'allow', 0),
(2376, 0, 44, 'general', 'bypassFloodCheck', 'allow', 0),
(2377, 0, 44, 'general', 'bypassSpamCheck', 'allow', 0),
(2378, 0, 44, 'general', 'report', 'allow', 0),
(2379, 0, 44, 'forum', 'stickUnstickThread', 'allow', 0),
(2380, 0, 44, 'forum', 'lockUnlockThread', 'allow', 0),
(2381, 0, 44, 'forum', 'manageAnyThread', 'allow', 0),
(2382, 0, 44, 'forum', 'deleteAnyThread', 'allow', 0),
(2383, 0, 44, 'forum', 'hardDeleteAnyThread', 'allow', 0),
(2384, 0, 44, 'forum', 'threadReplyBan', 'allow', 0),
(2385, 0, 44, 'forum', 'editAnyPost', 'allow', 0),
(2386, 0, 44, 'forum', 'deleteAnyPost', 'allow', 0),
(2387, 0, 44, 'forum', 'hardDeleteAnyPost', 'allow', 0),
(2388, 0, 44, 'forum', 'warn', 'allow', 0),
(2389, 0, 44, 'forum', 'manageAnyTag', 'allow', 0),
(2390, 0, 44, 'forum', 'viewDeleted', 'allow', 0),
(2391, 0, 44, 'forum', 'viewModerated', 'allow', 0),
(2392, 0, 44, 'forum', 'undelete', 'allow', 0),
(2393, 0, 44, 'forum', 'approveUnapprove', 'allow', 0),
(2394, 0, 44, 'avatar', 'allowed', 'allow', 0),
(2395, 0, 515, 'general', 'view', 'allow', 0),
(2396, 0, 515, 'general', 'viewNode', 'allow', 0),
(2397, 0, 515, 'general', 'viewMemberList', 'allow', 0),
(2398, 0, 515, 'general', 'viewProfile', 'allow', 0),
(2399, 0, 515, 'general', 'search', 'allow', 0)";

$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_permission_group (permission_group_id, addon_id) VALUES
('avatar', 'XenForo'),
('conversation', 'XenForo'),
('forum', 'XenForo'),
('general', 'XenForo'),
('profilePost', 'XenForo'),
('signature', 'XenForo')";

$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_permission_interface_group (interface_group_id, display_order) VALUES
('generalPermissions', 1),
('generalModeratorPermissions', 2),
('forumPermissions', 3),
('forumModeratorPermissions', 4),
('avatarPermissions', 5),
('conversationPermissions', 6),
('conversationModeratorPermissions', 7),
('signaturePermissions', 8),
('profilePostPermissions', 9),
('profilePostModeratorPermissions', 10)";

$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_permission_combination (permission_combination_id, userid, user_group_list, cache_value) VALUES
(1, 0, '1', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:1;s:11:\"maxFileSize\";i:0;}s:7:\"general\";a:23:{s:4:\"view\";b:1;s:13:\"editSignature\";b:1;s:8:\"viewNode\";b:1;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:1;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:1;s:6:\"search\";b:1;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:0;s:9:\"createTag\";b:1;s:18:\"bypassUserTagLimit\";b:1;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:1;s:15:\"editCustomTitle\";b:1;s:10:\"requireTfa\";b:1;s:21:\"followModerationRules\";b:1;s:16:\"bypassFloodCheck\";b:1;s:15:\"bypassSpamCheck\";b:1;s:6:\"report\";b:1;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:1;s:11:\"viewContent\";b:1;s:4:\"like\";b:1;s:10:\"postThread\";b:1;s:18:\"stickUnstickThread\";b:1;s:9:\"postReply\";b:1;s:11:\"editOwnPost\";b:1;s:13:\"lockUnlockThread\";b:1;s:16:\"lockUnlockThread\";b:1;s:20:\"editOwnPostTimeLimit\";i:0;s:18:\"editOwnThreadTitle\";b:1;s:15:\"deleteOwnThread\";b:1;s:15:\"manageAnyThread\";b:1;s:14:\"viewAttachment\";b:1;s:16:\"uploadAttachment\";b:1;s:12:\"tagOwnThread\";b:1;s:12:\"tagAnyThread\";b:1;s:25:\"manageOthersTagsOwnThread\";b:1;s:15:\"deleteAnyThread\";b:1;s:19:\"hardDeleteAnyThread\";b:1;s:14:\"threadReplyBan\";b:1;s:8:\"votePoll\";b:1;s:11:\"editAnyPost\";b:1;s:13:\"deleteAnyPost\";b:1;s:17:\"hardDeleteAnyPost\";b:1;s:4:\"warn\";b:1;s:12:\"manageAnyTag\";b:1;s:11:\"keepOutLink\";b:1;s:15:\"keepOutTextLink\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:1;s:7:\"receive\";b:1;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:1;s:11:\"editOwnPost\";b:1;s:20:\"editOwnPostTimeLimit\";i:0;s:13:\"maxRecipients\";i:0;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:1;s:12:\"extendedText\";b:1;s:5:\"align\";b:1;s:4:\"list\";b:1;s:4:\"link\";b:1;s:5:\"image\";b:1;s:5:\"media\";b:1;s:5:\"block\";b:1;s:12:\"maxPrintable\";i:-1;s:8:\"maxLines\";i:-1;s:8:\"maxLinks\";i:-1;s:9:\"maxImages\";i:-1;s:10:\"maxSmilies\";i:-1;s:11:\"maxTextSize\";i:-1;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:1;s:7:\"editAny\";b:1;s:4:\"like\";b:1;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:1;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:1;s:7:\"comment\";b:1;s:9:\"deleteOwn\";b:1;s:7:\"editOwn\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}'),
(2, 0, '2', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:1;s:11:\"maxFileSize\";i:-1;}s:7:\"general\";a:23:{s:4:\"view\";b:1;s:13:\"editSignature\";b:1;s:8:\"viewNode\";b:1;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:1;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:1;s:6:\"search\";b:1;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:-1;s:9:\"createTag\";b:1;s:18:\"bypassUserTagLimit\";b:1;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:1;s:15:\"editCustomTitle\";b:1;s:10:\"requireTfa\";b:1;s:21:\"followModerationRules\";b:1;s:16:\"bypassFloodCheck\";b:1;s:15:\"bypassSpamCheck\";b:1;s:6:\"report\";b:1;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:1;s:11:\"viewContent\";b:1;s:4:\"like\";b:1;s:10:\"postThread\";b:1;s:18:\"stickUnstickThread\";b:1;s:9:\"postReply\";b:1;s:11:\"editOwnPost\";b:1;s:13:\"lockUnlockThread\";b:1;s:16:\"lockUnlockThread\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:18:\"editOwnThreadTitle\";b:1;s:15:\"deleteOwnThread\";b:1;s:15:\"manageAnyThread\";b:1;s:14:\"viewAttachment\";b:1;s:16:\"uploadAttachment\";b:1;s:12:\"tagOwnThread\";b:1;s:12:\"tagAnyThread\";b:1;s:25:\"manageOthersTagsOwnThread\";b:1;s:15:\"deleteAnyThread\";b:1;s:19:\"hardDeleteAnyThread\";b:1;s:14:\"threadReplyBan\";b:1;s:8:\"votePoll\";b:1;s:11:\"editAnyPost\";b:1;s:13:\"deleteAnyPost\";b:1;s:17:\"hardDeleteAnyPost\";b:1;s:4:\"warn\";b:1;s:12:\"manageAnyTag\";b:1;s:11:\"keepOutLink\";b:1;s:15:\"keepOutTextLink\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:1;s:7:\"receive\";b:1;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:1;s:11:\"editOwnPost\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:13:\"maxRecipients\";i:-1;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:1;s:12:\"extendedText\";b:1;s:5:\"align\";b:1;s:4:\"list\";b:1;s:4:\"link\";b:1;s:5:\"image\";b:1;s:5:\"media\";b:1;s:5:\"block\";b:1;s:12:\"maxPrintable\";i:-1;s:8:\"maxLines\";i:-1;s:8:\"maxLinks\";i:-1;s:9:\"maxImages\";i:-1;s:10:\"maxSmilies\";i:-1;s:11:\"maxTextSize\";i:-1;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:1;s:7:\"editAny\";b:1;s:4:\"like\";b:1;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:1;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:1;s:7:\"comment\";b:1;s:9:\"deleteOwn\";b:1;s:7:\"editOwn\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}'),
(3, 0, '3', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:0;s:11:\"maxFileSize\";i:-1;}s:7:\"general\";a:23:{s:4:\"view\";b:0;s:13:\"editSignature\";b:0;s:8:\"viewNode\";b:0;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:0;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:0;s:6:\"search\";b:0;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:0;s:9:\"createTag\";b:0;s:18:\"bypassUserTagLimit\";b:0;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:0;s:15:\"editCustomTitle\";b:1;s:10:\"requireTfa\";b:0;s:21:\"followModerationRules\";b:0;s:16:\"bypassFloodCheck\";b:1;s:15:\"bypassSpamCheck\";b:0;s:6:\"report\";b:0;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:0;s:11:\"viewContent\";b:0;s:4:\"like\";b:0;s:10:\"postThread\";b:0;s:18:\"stickUnstickThread\";b:0;s:9:\"postReply\";b:0;s:11:\"editOwnPost\";b:0;s:13:\"lockUnlockThread\";b:0;s:16:\"lockUnlockThread\";b:0;s:20:\"editOwnPostTimeLimit\";i:-1;s:18:\"editOwnThreadTitle\";b:0;s:15:\"deleteOwnThread\";b:0;s:15:\"manageAnyThread\";b:0;s:14:\"viewAttachment\";b:0;s:16:\"uploadAttachment\";b:0;s:12:\"tagOwnThread\";b:0;s:12:\"tagAnyThread\";b:0;s:25:\"manageOthersTagsOwnThread\";b:0;s:15:\"deleteAnyThread\";b:0;s:19:\"hardDeleteAnyThread\";b:0;s:14:\"threadReplyBan\";b:0;s:8:\"votePoll\";b:0;s:11:\"editAnyPost\";b:0;s:13:\"deleteAnyPost\";b:0;s:17:\"hardDeleteAnyPost\";b:0;s:4:\"warn\";b:0;s:12:\"manageAnyTag\";b:0;s:11:\"keepOutLink\";b:0;s:15:\"keepOutTextLink\";b:0;s:11:\"viewDeleted\";b:0;s:13:\"viewModerated\";b:0;s:8:\"undelete\";b:0;s:16:\"approveUnapprove\";b:0;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:0;s:7:\"receive\";b:0;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:1;s:11:\"editOwnPost\";b:0;s:20:\"editOwnPostTimeLimit\";i:0;s:13:\"maxRecipients\";i:-1;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:0;s:12:\"extendedText\";b:0;s:5:\"align\";b:0;s:4:\"list\";b:0;s:4:\"link\";b:0;s:5:\"image\";b:0;s:5:\"media\";b:0;s:5:\"block\";b:0;s:12:\"maxPrintable\";i:0;s:8:\"maxLines\";i:0;s:8:\"maxLinks\";i:0;s:9:\"maxImages\";i:0;s:10:\"maxSmilies\";i:0;s:11:\"maxTextSize\";i:0;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:0;s:7:\"editAny\";b:1;s:4:\"like\";b:0;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:0;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:0;s:7:\"comment\";b:0;s:9:\"deleteOwn\";b:0;s:7:\"editOwn\";b:0;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}'),
(4, 0, '4', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:1;s:11:\"maxFileSize\";i:-1;}s:7:\"general\";a:23:{s:4:\"view\";b:1;s:13:\"editSignature\";b:1;s:8:\"viewNode\";b:1;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:1;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:1;s:6:\"search\";b:1;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:-1;s:9:\"createTag\";b:1;s:18:\"bypassUserTagLimit\";b:1;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:1;s:15:\"editCustomTitle\";b:1;s:10:\"requireTfa\";b:1;s:21:\"followModerationRules\";b:1;s:16:\"bypassFloodCheck\";b:1;s:15:\"bypassSpamCheck\";b:1;s:6:\"report\";b:1;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:1;s:11:\"viewContent\";b:1;s:4:\"like\";b:1;s:10:\"postThread\";b:1;s:18:\"stickUnstickThread\";b:0;s:9:\"postReply\";b:1;s:11:\"editOwnPost\";b:1;s:13:\"lockUnlockThread\";b:1;s:16:\"lockUnlockThread\";b:0;s:20:\"editOwnPostTimeLimit\";i:10080;s:18:\"editOwnThreadTitle\";b:1;s:15:\"deleteOwnThread\";b:1;s:15:\"manageAnyThread\";b:0;s:14:\"viewAttachment\";b:1;s:16:\"uploadAttachment\";b:1;s:12:\"tagOwnThread\";b:1;s:12:\"tagAnyThread\";b:1;s:25:\"manageOthersTagsOwnThread\";b:1;s:15:\"deleteAnyThread\";b:0;s:19:\"hardDeleteAnyThread\";b:0;s:14:\"threadReplyBan\";b:0;s:8:\"votePoll\";b:1;s:11:\"editAnyPost\";b:0;s:13:\"deleteAnyPost\";b:0;s:17:\"hardDeleteAnyPost\";b:0;s:4:\"warn\";b:0;s:12:\"manageAnyTag\";b:0;s:11:\"keepOutLink\";b:0;s:15:\"keepOutTextLink\";b:1;s:11:\"viewDeleted\";b:0;s:13:\"viewModerated\";b:0;s:8:\"undelete\";b:0;s:16:\"approveUnapprove\";b:0;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:0;s:7:\"receive\";b:0;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:0;s:11:\"editOwnPost\";b:0;s:20:\"editOwnPostTimeLimit\";i:-1;s:13:\"maxRecipients\";i:-1;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:1;s:12:\"extendedText\";b:1;s:5:\"align\";b:1;s:4:\"list\";b:1;s:4:\"link\";b:1;s:5:\"image\";b:1;s:5:\"media\";b:1;s:5:\"block\";b:1;s:12:\"maxPrintable\";i:-1;s:8:\"maxLines\";i:-1;s:8:\"maxLinks\";i:-1;s:9:\"maxImages\";i:-1;s:10:\"maxSmilies\";i:-1;s:11:\"maxTextSize\";i:-1;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:1;s:7:\"editAny\";b:1;s:4:\"like\";b:1;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:1;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:1;s:7:\"comment\";b:1;s:9:\"deleteOwn\";b:1;s:7:\"editOwn\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}'),
(5, 0, '5', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:0;s:11:\"maxFileSize\";i:0;}s:7:\"general\";a:23:{s:4:\"view\";b:1;s:13:\"editSignature\";b:0;s:8:\"viewNode\";b:1;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:1;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:1;s:6:\"search\";b:1;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:0;s:9:\"createTag\";b:0;s:18:\"bypassUserTagLimit\";b:0;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:1;s:15:\"editCustomTitle\";b:0;s:10:\"requireTfa\";b:0;s:21:\"followModerationRules\";b:1;s:16:\"bypassFloodCheck\";b:0;s:15:\"bypassSpamCheck\";b:0;s:6:\"report\";b:0;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:1;s:11:\"viewContent\";b:1;s:4:\"like\";b:0;s:10:\"postThread\";b:0;s:18:\"stickUnstickThread\";b:0;s:9:\"postReply\";b:0;s:11:\"editOwnPost\";b:0;s:13:\"lockUnlockThread\";b:0;s:16:\"lockUnlockThread\";b:0;s:20:\"editOwnPostTimeLimit\";i:0;s:18:\"editOwnThreadTitle\";b:0;s:15:\"deleteOwnThread\";b:0;s:15:\"manageAnyThread\";b:0;s:14:\"viewAttachment\";b:0;s:16:\"uploadAttachment\";b:0;s:12:\"tagOwnThread\";b:0;s:12:\"tagAnyThread\";b:0;s:25:\"manageOthersTagsOwnThread\";b:0;s:15:\"deleteAnyThread\";b:0;s:19:\"hardDeleteAnyThread\";b:0;s:14:\"threadReplyBan\";b:0;s:8:\"votePoll\";b:0;s:11:\"editAnyPost\";b:0;s:13:\"deleteAnyPost\";b:0;s:17:\"hardDeleteAnyPost\";b:0;s:4:\"warn\";b:0;s:12:\"manageAnyTag\";b:0;s:11:\"keepOutLink\";b:0;s:15:\"keepOutTextLink\";b:0;s:11:\"viewDeleted\";b:0;s:13:\"viewModerated\";b:0;s:8:\"undelete\";b:0;s:16:\"approveUnapprove\";b:0;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:0;s:7:\"receive\";b:0;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:0;s:11:\"editOwnPost\";b:0;s:20:\"editOwnPostTimeLimit\";i:0;s:13:\"maxRecipients\";i:0;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:0;s:12:\"extendedText\";b:0;s:5:\"align\";b:0;s:4:\"list\";b:0;s:4:\"link\";b:0;s:5:\"image\";b:0;s:5:\"media\";b:0;s:5:\"block\";b:0;s:12:\"maxPrintable\";i:0;s:8:\"maxLines\";i:0;s:8:\"maxLinks\";i:0;s:9:\"maxImages\";i:0;s:10:\"maxSmilies\";i:0;s:11:\"maxTextSize\";i:0;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:1;s:7:\"editAny\";b:1;s:4:\"like\";b:0;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:0;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:0;s:7:\"comment\";b:0;s:9:\"deleteOwn\";b:0;s:7:\"editOwn\";b:0;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}'),
(6, 0, '1,2,3', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:1;s:11:\"maxFileSize\";i:-1;}s:7:\"general\";a:23:{s:4:\"view\";b:1;s:13:\"editSignature\";b:1;s:8:\"viewNode\";b:1;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:1;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:1;s:6:\"search\";b:1;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:-1;s:9:\"createTag\";b:1;s:18:\"bypassUserTagLimit\";b:1;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:1;s:15:\"editCustomTitle\";b:1;s:10:\"requireTfa\";b:1;s:21:\"followModerationRules\";b:1;s:16:\"bypassFloodCheck\";b:1;s:15:\"bypassSpamCheck\";b:1;s:6:\"report\";b:1;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:1;s:11:\"viewContent\";b:1;s:4:\"like\";b:1;s:10:\"postThread\";b:1;s:18:\"stickUnstickThread\";b:1;s:9:\"postReply\";b:1;s:11:\"editOwnPost\";b:1;s:13:\"lockUnlockThread\";b:1;s:16:\"lockUnlockThread\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:18:\"editOwnThreadTitle\";b:1;s:15:\"deleteOwnThread\";b:1;s:15:\"manageAnyThread\";b:1;s:14:\"viewAttachment\";b:1;s:16:\"uploadAttachment\";b:1;s:12:\"tagOwnThread\";b:1;s:12:\"tagAnyThread\";b:1;s:25:\"manageOthersTagsOwnThread\";b:1;s:15:\"deleteAnyThread\";b:1;s:19:\"hardDeleteAnyThread\";b:1;s:14:\"threadReplyBan\";b:1;s:8:\"votePoll\";b:1;s:11:\"editAnyPost\";b:1;s:13:\"deleteAnyPost\";b:1;s:17:\"hardDeleteAnyPost\";b:1;s:4:\"warn\";b:1;s:12:\"manageAnyTag\";b:1;s:11:\"keepOutLink\";b:1;s:15:\"keepOutTextLink\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:1;s:7:\"receive\";b:1;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:1;s:11:\"editOwnPost\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:13:\"maxRecipients\";i:-1;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:1;s:12:\"extendedText\";b:1;s:5:\"align\";b:1;s:4:\"list\";b:1;s:4:\"link\";b:1;s:5:\"image\";b:1;s:5:\"media\";b:1;s:5:\"block\";b:1;s:12:\"maxPrintable\";i:-1;s:8:\"maxLines\";i:-1;s:8:\"maxLinks\";i:-1;s:9:\"maxImages\";i:-1;s:10:\"maxSmilies\";i:-1;s:11:\"maxTextSize\";i:-1;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:1;s:7:\"editAny\";b:1;s:4:\"like\";b:1;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:1;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:1;s:7:\"comment\";b:1;s:9:\"deleteOwn\";b:1;s:7:\"editOwn\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}'),
(7, 0, '1,2', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:1;s:11:\"maxFileSize\";i:-1;}s:7:\"general\";a:23:{s:4:\"view\";b:1;s:13:\"editSignature\";b:1;s:8:\"viewNode\";b:1;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:1;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:1;s:6:\"search\";b:1;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:-1;s:9:\"createTag\";b:1;s:18:\"bypassUserTagLimit\";b:1;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:1;s:15:\"editCustomTitle\";b:1;s:10:\"requireTfa\";b:1;s:21:\"followModerationRules\";b:1;s:16:\"bypassFloodCheck\";b:1;s:15:\"bypassSpamCheck\";b:1;s:6:\"report\";b:1;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:1;s:11:\"viewContent\";b:1;s:4:\"like\";b:1;s:10:\"postThread\";b:1;s:18:\"stickUnstickThread\";b:1;s:9:\"postReply\";b:1;s:11:\"editOwnPost\";b:1;s:13:\"lockUnlockThread\";b:1;s:16:\"lockUnlockThread\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:18:\"editOwnThreadTitle\";b:1;s:15:\"deleteOwnThread\";b:1;s:15:\"manageAnyThread\";b:1;s:14:\"viewAttachment\";b:1;s:16:\"uploadAttachment\";b:1;s:12:\"tagOwnThread\";b:1;s:12:\"tagAnyThread\";b:1;s:25:\"manageOthersTagsOwnThread\";b:1;s:15:\"deleteAnyThread\";b:1;s:19:\"hardDeleteAnyThread\";b:1;s:14:\"threadReplyBan\";b:1;s:8:\"votePoll\";b:1;s:11:\"editAnyPost\";b:1;s:13:\"deleteAnyPost\";b:1;s:17:\"hardDeleteAnyPost\";b:1;s:4:\"warn\";b:1;s:12:\"manageAnyTag\";b:1;s:11:\"keepOutLink\";b:1;s:15:\"keepOutTextLink\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:1;s:7:\"receive\";b:1;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:1;s:11:\"editOwnPost\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:13:\"maxRecipients\";i:-1;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:1;s:12:\"extendedText\";b:1;s:5:\"align\";b:1;s:4:\"list\";b:1;s:4:\"link\";b:1;s:5:\"image\";b:1;s:5:\"media\";b:1;s:5:\"block\";b:1;s:12:\"maxPrintable\";i:-1;s:8:\"maxLines\";i:-1;s:8:\"maxLinks\";i:-1;s:9:\"maxImages\";i:-1;s:10:\"maxSmilies\";i:-1;s:11:\"maxTextSize\";i:-1;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:1;s:7:\"editAny\";b:1;s:4:\"like\";b:1;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:1;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:1;s:7:\"comment\";b:1;s:9:\"deleteOwn\";b:1;s:7:\"editOwn\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}'),
(8, 0, '2,3', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:1;s:11:\"maxFileSize\";i:-1;}s:7:\"general\";a:23:{s:4:\"view\";b:1;s:13:\"editSignature\";b:1;s:8:\"viewNode\";b:1;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:1;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:1;s:6:\"search\";b:1;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:-1;s:9:\"createTag\";b:1;s:18:\"bypassUserTagLimit\";b:1;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:1;s:15:\"editCustomTitle\";b:1;s:10:\"requireTfa\";b:1;s:21:\"followModerationRules\";b:1;s:16:\"bypassFloodCheck\";b:1;s:15:\"bypassSpamCheck\";b:1;s:6:\"report\";b:1;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:1;s:11:\"viewContent\";b:1;s:4:\"like\";b:1;s:10:\"postThread\";b:1;s:18:\"stickUnstickThread\";b:1;s:9:\"postReply\";b:1;s:11:\"editOwnPost\";b:1;s:13:\"lockUnlockThread\";b:1;s:16:\"lockUnlockThread\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:18:\"editOwnThreadTitle\";b:1;s:15:\"deleteOwnThread\";b:1;s:15:\"manageAnyThread\";b:1;s:14:\"viewAttachment\";b:1;s:16:\"uploadAttachment\";b:1;s:12:\"tagOwnThread\";b:1;s:12:\"tagAnyThread\";b:1;s:25:\"manageOthersTagsOwnThread\";b:1;s:15:\"deleteAnyThread\";b:1;s:19:\"hardDeleteAnyThread\";b:1;s:14:\"threadReplyBan\";b:1;s:8:\"votePoll\";b:1;s:11:\"editAnyPost\";b:1;s:13:\"deleteAnyPost\";b:1;s:17:\"hardDeleteAnyPost\";b:1;s:4:\"warn\";b:1;s:12:\"manageAnyTag\";b:1;s:11:\"keepOutLink\";b:1;s:15:\"keepOutTextLink\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:1;s:7:\"receive\";b:1;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:1;s:11:\"editOwnPost\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:13:\"maxRecipients\";i:-1;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:1;s:12:\"extendedText\";b:1;s:5:\"align\";b:1;s:4:\"list\";b:1;s:4:\"link\";b:1;s:5:\"image\";b:1;s:5:\"media\";b:1;s:5:\"block\";b:1;s:12:\"maxPrintable\";i:-1;s:8:\"maxLines\";i:-1;s:8:\"maxLinks\";i:-1;s:9:\"maxImages\";i:-1;s:10:\"maxSmilies\";i:-1;s:11:\"maxTextSize\";i:-1;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:1;s:7:\"editAny\";b:1;s:4:\"like\";b:1;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:1;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:1;s:7:\"comment\";b:1;s:9:\"deleteOwn\";b:1;s:7:\"editOwn\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}'),
(9, 0, '1,3', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:1;s:11:\"maxFileSize\";i:-1;}s:7:\"general\";a:23:{s:4:\"view\";b:1;s:13:\"editSignature\";b:1;s:8:\"viewNode\";b:1;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:1;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:1;s:6:\"search\";b:1;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:0;s:9:\"createTag\";b:1;s:18:\"bypassUserTagLimit\";b:1;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:1;s:15:\"editCustomTitle\";b:1;s:10:\"requireTfa\";b:1;s:21:\"followModerationRules\";b:1;s:16:\"bypassFloodCheck\";b:1;s:15:\"bypassSpamCheck\";b:1;s:6:\"report\";b:1;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:1;s:11:\"viewContent\";b:1;s:4:\"like\";b:1;s:10:\"postThread\";b:1;s:18:\"stickUnstickThread\";b:1;s:9:\"postReply\";b:1;s:11:\"editOwnPost\";b:1;s:13:\"lockUnlockThread\";b:1;s:16:\"lockUnlockThread\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:18:\"editOwnThreadTitle\";b:1;s:15:\"deleteOwnThread\";b:1;s:15:\"manageAnyThread\";b:1;s:14:\"viewAttachment\";b:1;s:16:\"uploadAttachment\";b:1;s:12:\"tagOwnThread\";b:1;s:12:\"tagAnyThread\";b:1;s:25:\"manageOthersTagsOwnThread\";b:1;s:15:\"deleteAnyThread\";b:1;s:19:\"hardDeleteAnyThread\";b:1;s:14:\"threadReplyBan\";b:1;s:8:\"votePoll\";b:1;s:11:\"editAnyPost\";b:1;s:13:\"deleteAnyPost\";b:1;s:17:\"hardDeleteAnyPost\";b:1;s:4:\"warn\";b:1;s:12:\"manageAnyTag\";b:1;s:11:\"keepOutLink\";b:1;s:15:\"keepOutTextLink\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:1;s:7:\"receive\";b:1;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:1;s:11:\"editOwnPost\";b:1;s:20:\"editOwnPostTimeLimit\";i:0;s:13:\"maxRecipients\";i:-1;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:1;s:12:\"extendedText\";b:1;s:5:\"align\";b:1;s:4:\"list\";b:1;s:4:\"link\";b:1;s:5:\"image\";b:1;s:5:\"media\";b:1;s:5:\"block\";b:1;s:12:\"maxPrintable\";i:-1;s:8:\"maxLines\";i:-1;s:8:\"maxLinks\";i:-1;s:9:\"maxImages\";i:-1;s:10:\"maxSmilies\";i:-1;s:11:\"maxTextSize\";i:-1;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:1;s:7:\"editAny\";b:1;s:4:\"like\";b:1;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:1;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:1;s:7:\"comment\";b:1;s:9:\"deleteOwn\";b:1;s:7:\"editOwn\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}'),
(10, 0, '1,2,3,4', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:1;s:11:\"maxFileSize\";i:-1;}s:7:\"general\";a:23:{s:4:\"view\";b:1;s:13:\"editSignature\";b:1;s:8:\"viewNode\";b:1;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:1;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:1;s:6:\"search\";b:1;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:-1;s:9:\"createTag\";b:1;s:18:\"bypassUserTagLimit\";b:1;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:1;s:15:\"editCustomTitle\";b:1;s:10:\"requireTfa\";b:1;s:21:\"followModerationRules\";b:1;s:16:\"bypassFloodCheck\";b:1;s:15:\"bypassSpamCheck\";b:1;s:6:\"report\";b:1;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:1;s:11:\"viewContent\";b:1;s:4:\"like\";b:1;s:10:\"postThread\";b:1;s:18:\"stickUnstickThread\";b:1;s:9:\"postReply\";b:1;s:11:\"editOwnPost\";b:1;s:13:\"lockUnlockThread\";b:1;s:16:\"lockUnlockThread\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:18:\"editOwnThreadTitle\";b:1;s:15:\"deleteOwnThread\";b:1;s:15:\"manageAnyThread\";b:1;s:14:\"viewAttachment\";b:1;s:16:\"uploadAttachment\";b:1;s:12:\"tagOwnThread\";b:1;s:12:\"tagAnyThread\";b:1;s:25:\"manageOthersTagsOwnThread\";b:1;s:15:\"deleteAnyThread\";b:1;s:19:\"hardDeleteAnyThread\";b:1;s:14:\"threadReplyBan\";b:1;s:8:\"votePoll\";b:1;s:11:\"editAnyPost\";b:1;s:13:\"deleteAnyPost\";b:1;s:17:\"hardDeleteAnyPost\";b:1;s:4:\"warn\";b:1;s:12:\"manageAnyTag\";b:1;s:11:\"keepOutLink\";b:0;s:15:\"keepOutTextLink\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:1;s:7:\"receive\";b:1;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:1;s:11:\"editOwnPost\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:13:\"maxRecipients\";i:-1;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:1;s:12:\"extendedText\";b:1;s:5:\"align\";b:1;s:4:\"list\";b:1;s:4:\"link\";b:1;s:5:\"image\";b:1;s:5:\"media\";b:1;s:5:\"block\";b:1;s:12:\"maxPrintable\";i:-1;s:8:\"maxLines\";i:-1;s:8:\"maxLinks\";i:-1;s:9:\"maxImages\";i:-1;s:10:\"maxSmilies\";i:-1;s:11:\"maxTextSize\";i:-1;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:1;s:7:\"editAny\";b:1;s:4:\"like\";b:1;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:1;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:1;s:7:\"comment\";b:1;s:9:\"deleteOwn\";b:1;s:7:\"editOwn\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}'),
(11, 0, '1,4', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:1;s:11:\"maxFileSize\";i:-1;}s:7:\"general\";a:23:{s:4:\"view\";b:1;s:13:\"editSignature\";b:1;s:8:\"viewNode\";b:1;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:1;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:1;s:6:\"search\";b:1;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:-1;s:9:\"createTag\";b:1;s:18:\"bypassUserTagLimit\";b:1;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:1;s:15:\"editCustomTitle\";b:1;s:10:\"requireTfa\";b:1;s:21:\"followModerationRules\";b:1;s:16:\"bypassFloodCheck\";b:1;s:15:\"bypassSpamCheck\";b:1;s:6:\"report\";b:1;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:1;s:11:\"viewContent\";b:1;s:4:\"like\";b:1;s:10:\"postThread\";b:1;s:18:\"stickUnstickThread\";b:1;s:9:\"postReply\";b:1;s:11:\"editOwnPost\";b:1;s:13:\"lockUnlockThread\";b:1;s:16:\"lockUnlockThread\";b:1;s:20:\"editOwnPostTimeLimit\";i:10080;s:18:\"editOwnThreadTitle\";b:1;s:15:\"deleteOwnThread\";b:1;s:15:\"manageAnyThread\";b:1;s:14:\"viewAttachment\";b:1;s:16:\"uploadAttachment\";b:1;s:12:\"tagOwnThread\";b:1;s:12:\"tagAnyThread\";b:1;s:25:\"manageOthersTagsOwnThread\";b:1;s:15:\"deleteAnyThread\";b:1;s:19:\"hardDeleteAnyThread\";b:1;s:14:\"threadReplyBan\";b:1;s:8:\"votePoll\";b:1;s:11:\"editAnyPost\";b:1;s:13:\"deleteAnyPost\";b:1;s:17:\"hardDeleteAnyPost\";b:1;s:4:\"warn\";b:1;s:12:\"manageAnyTag\";b:1;s:11:\"keepOutLink\";b:0;s:15:\"keepOutTextLink\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:1;s:7:\"receive\";b:1;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:1;s:11:\"editOwnPost\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:13:\"maxRecipients\";i:-1;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:1;s:12:\"extendedText\";b:1;s:5:\"align\";b:1;s:4:\"list\";b:1;s:4:\"link\";b:1;s:5:\"image\";b:1;s:5:\"media\";b:1;s:5:\"block\";b:1;s:12:\"maxPrintable\";i:-1;s:8:\"maxLines\";i:-1;s:8:\"maxLinks\";i:-1;s:9:\"maxImages\";i:-1;s:10:\"maxSmilies\";i:-1;s:11:\"maxTextSize\";i:-1;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:1;s:7:\"editAny\";b:1;s:4:\"like\";b:1;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:1;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:1;s:7:\"comment\";b:1;s:9:\"deleteOwn\";b:1;s:7:\"editOwn\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}'),
(12, 0, '2,4', 'a:6:{s:6:\"avatar\";a:2:{s:7:\"allowed\";b:1;s:11:\"maxFileSize\";i:-1;}s:7:\"general\";a:23:{s:4:\"view\";b:1;s:13:\"editSignature\";b:1;s:8:\"viewNode\";b:1;s:7:\"viewIps\";b:1;s:14:\"viewMemberList\";b:1;s:17:\"bypassUserPrivacy\";b:1;s:11:\"viewProfile\";b:1;s:6:\"search\";b:1;s:9:\"cleanSpam\";b:1;s:14:\"maxTaggedUsers\";i:-1;s:9:\"createTag\";b:1;s:18:\"bypassUserTagLimit\";b:1;s:11:\"viewWarning\";b:1;s:4:\"warn\";b:1;s:13:\"manageWarning\";b:1;s:16:\"editBasicProfile\";b:1;s:11:\"editProfile\";b:1;s:15:\"editCustomTitle\";b:1;s:10:\"requireTfa\";b:1;s:21:\"followModerationRules\";b:1;s:16:\"bypassFloodCheck\";b:1;s:15:\"bypassSpamCheck\";b:1;s:6:\"report\";b:1;}s:5:\"forum\";a:33:{s:10:\"viewOthers\";b:1;s:11:\"viewContent\";b:1;s:4:\"like\";b:1;s:10:\"postThread\";b:1;s:18:\"stickUnstickThread\";b:1;s:9:\"postReply\";b:1;s:11:\"editOwnPost\";b:1;s:13:\"lockUnlockThread\";b:1;s:16:\"lockUnlockThread\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:18:\"editOwnThreadTitle\";b:1;s:15:\"deleteOwnThread\";b:1;s:15:\"manageAnyThread\";b:1;s:14:\"viewAttachment\";b:1;s:16:\"uploadAttachment\";b:1;s:12:\"tagOwnThread\";b:1;s:12:\"tagAnyThread\";b:1;s:25:\"manageOthersTagsOwnThread\";b:1;s:15:\"deleteAnyThread\";b:1;s:19:\"hardDeleteAnyThread\";b:1;s:14:\"threadReplyBan\";b:1;s:8:\"votePoll\";b:1;s:11:\"editAnyPost\";b:1;s:13:\"deleteAnyPost\";b:1;s:17:\"hardDeleteAnyPost\";b:1;s:4:\"warn\";b:1;s:12:\"manageAnyTag\";b:1;s:11:\"keepOutLink\";b:0;s:15:\"keepOutTextLink\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}s:12:\"conversation\";a:8:{s:11:\"editAnyPost\";b:1;s:5:\"start\";b:1;s:7:\"receive\";b:1;s:12:\"alwaysInvite\";b:1;s:16:\"uploadAttachment\";b:1;s:11:\"editOwnPost\";b:1;s:20:\"editOwnPostTimeLimit\";i:-1;s:13:\"maxRecipients\";i:-1;}s:9:\"signature\";a:14:{s:9:\"basicText\";b:1;s:12:\"extendedText\";b:1;s:5:\"align\";b:1;s:4:\"list\";b:1;s:4:\"link\";b:1;s:5:\"image\";b:1;s:5:\"media\";b:1;s:5:\"block\";b:1;s:12:\"maxPrintable\";i:-1;s:8:\"maxLines\";i:-1;s:8:\"maxLinks\";i:-1;s:9:\"maxImages\";i:-1;s:10:\"maxSmilies\";i:-1;s:11:\"maxTextSize\";i:-1;}s:11:\"profilePost\";a:15:{s:4:\"view\";b:1;s:7:\"editAny\";b:1;s:4:\"like\";b:1;s:9:\"deleteAny\";b:1;s:9:\"manageOwn\";b:1;s:13:\"hardDeleteAny\";b:1;s:4:\"warn\";b:1;s:4:\"post\";b:1;s:7:\"comment\";b:1;s:9:\"deleteOwn\";b:1;s:7:\"editOwn\";b:1;s:11:\"viewDeleted\";b:1;s:13:\"viewModerated\";b:1;s:8:\"undelete\";b:1;s:16:\"approveUnapprove\";b:1;}}')";


$data = array(
    'per_page' => 15,
    'per_topic' => 15,
    'groups_post' => 4,
    'groups_reply' => 4,
    'editor' => 'ckeditor',
    'notifysystem' => 0,
    'firebase' => 0,
    'email' => '',
    'mailserver' => 'system',
    'mailconfig' => '',
    'groups_admin' => 1,
    'notify_new_topic' => 1,
    'captcha' => 0,
    'homeview' => 'viewforum',
    'homedata' => 1,
    'queue' => 0
);

foreach ($data as $config_name => $config_value) {
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', " . $db->quote($module_name) . ", " . $db->quote($config_name) . ", " . $db->quote($config_value) . ")";
}
