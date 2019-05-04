<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES., JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES ., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Dec 3, 2010  11:12:21 AM 
 */

if( ! defined( 'NV_SYSTEM' ) ) die( 'Stop!!!' );

define( 'NV_IS_MOD_FORUM', true );

$global_userid = ( isset( $user_info['userid'] ) ) ? $user_info['userid'] : 0;

require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.function.php';

$forum_node = getdbCache( $module_name, 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_node ORDER BY sort ASC', 'node', 'node_id' );

$page = 1;
$per_page_thread = 20;
$per_page_post = 15;
$discussionsPerPage = 30;
$node_id = 0;
$parent_id = 0;
$node_alias = '';
if( ! empty( $array_op ) )
{
	$page = 1;
	foreach( $forum_node as $l )
	{
		if( $array_op[0] == $l['alias'] )
		{
			$node_id = $l['node_id'];
			$node_alias = $l['alias'];
			$op = 'viewnode';
			break;
		}
	}

}


$modelThreadConst = array( 
	'user'=> 1, 
	'forum'=> 2, 
	'firstpost'=> 4, 
	'avatar'=> 8, 
	'deletion_log'=> 10,
	'forum_option'=> 20,
	'last_post_avatar'=> 40 );
 
require_once NV_ROOTDIR . '/modules/forum/model/Forum.php';
require_once NV_ROOTDIR . '/modules/forum/model/Thread.php';
require_once NV_ROOTDIR . '/modules/forum/model/ThreadRedirect.php';
require_once NV_ROOTDIR . '/modules/forum/model/ThreadWatch.php';
require_once NV_ROOTDIR . '/modules/forum/model/User.php';
require_once NV_ROOTDIR . '/modules/forum/model/Like.php';
require_once NV_ROOTDIR . '/modules/forum/model/Attachment.php';
require_once NV_ROOTDIR . '/modules/forum/model/Post.php';
