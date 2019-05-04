<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweb.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 22:18
 */

if( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

$base_url_rewrite = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $forum_node[$node_id]['alias'];
if( $page > 1 )
{
	$base_url_rewrite .= '/page-' . $page;
}

$base_url_rewrite = nv_url_rewrite( $base_url_rewrite, true );
if( $_SERVER['REQUEST_URI'] != $base_url_rewrite and NV_MAIN_DOMAIN . $_SERVER['REQUEST_URI'] != $base_url_rewrite )
{
	Header( 'Location: ' . $base_url_rewrite );
	die();
}


$parent_id = $node_id;
    while ($parent_id > 0) {
        $array_cat_i = $forum_node[$parent_id];
        $array_mod_title[] = array(
            'catid' => $parent_id,
            'title' => $array_cat_i['title'],
            'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $array_cat_i['alias']
        );
        $parent_id = $array_cat_i['parent_id'];
    }
    sort($array_mod_title, SORT_NUMERIC);


$forumData = $generalPermissions = $nodePermissions = $threadDataSticky = $threadDataNoneSticky = array();

if( ! defined( 'NV_IS_USER' ) )
{
	/* Nhom khach truy cap => 6 */
	$permission_combination_id = 6;
	$result = $db_slave->query( 'SELECT cache_value FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id =' . intval( $permission_combination_id ) );
	$generalPermissions = $result->fetchColumn();
	$result->closeCursor();

	$generalPermissions = unserializePermissions( $generalPermissions );
}
else
{
	/* Nhom thanh vien truy cap => 4 */
	$permission_combination_id = $user_info['permission_combination_id'];
	$generalPermissions = $user_info['permissions'];
}

/* Quyen xem noi dung trong toan bo module */
if( ! hasContentPermission( $generalPermissions['general'], 'view' ) )
{
	ForumCheckPermission( 'view' );

}


// lay thong tin dien dan theo lua chon
 

function prepareForumJoinOptions( array $fetchOptions )
{
	global $db;
	$selectFields = '';
	$joinTables = '';

	if( ! empty( $fetchOptions['permissionCombinationId'] ) )
	{
		$selectFields .= ',
				permission.cache_value AS node_permission_cache';
		$joinTables .= '
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
					ON (permission.permission_combination_id = ' . $db->quote( $fetchOptions['permissionCombinationId'] ) . '
						AND permission.content_type = \'node\'
						AND permission.content_id = forum.node_id)';
	}

	if( isset( $fetchOptions['readUserId'] ) )
	{
		if( ! empty( $fetchOptions['readUserId'] ) )
		{
			/* sau nay cho vao cau hinh */
			$readMarkingDataLifetime = 30;
			$autoReadDate = NV_CURRENTTIME - $readMarkingDataLifetime * 86400;

			$selectFields .= ',
					IF(forum_read.forum_read_date > '. $autoReadDate .', forum_read.forum_read_date, '. $autoReadDate .') AS forum_read_date';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum_read AS forum_read ON
						(forum_read.node_id = forum.node_id
						AND forum_read.userid = ' . $db->quote( $fetchOptions['readUserId'] ) . ')';
		}
		else
		{
			$selectFields .= ',
					NULL AS forum_read_date';
		}
	}

	if( isset( $fetchOptions['watchUserId'] ) )
	{
		if( ! empty( $fetchOptions['watchUserId'] ) )
		{
			$selectFields .= ',
					IF(forum_watch.userid IS NULL, 0, 1) AS forum_is_watched';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum_watch AS forum_watch
						ON (forum_watch.node_id = forum.node_id
						AND forum_watch.userid = ' . $db->quote( $fetchOptions['watchUserId'] ) . ')';
		}
		else
		{
			$selectFields .= ',
					0 AS forum_is_watched';
		}
	}

	if( isset( $fetchOptions['threadId'] ) )
	{
		$joinTables .= '
				INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_thread AS thread ON
					(thread.node_id = forum.node_id)';
	}

	return array( 'selectFields' => $selectFields, 'joinTables' => $joinTables );
}
 
$fetchOptions = array(
	'readUserId'=> $global_userid,
	'watchUserId'=> $global_userid,
	'permissionCombinationId'=> $permission_combination_id,	
);

$joinOptions = prepareForumJoinOptions( $fetchOptions ); 
 
$forumData = $db->query( 'SELECT node.*, forum.*
	' . $joinOptions['selectFields'] . '			 
	FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
	INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)							
	' . $joinOptions['joinTables'] . '	
	WHERE node.node_id = ' . intval( $node_id ) )->fetch();

if( !empty( $forumData['node_permission_cache'] ) )
{
	$nodePermissions = unserializePermissions( $forumData['node_permission_cache'] );	
}
 
if( hasContentPermission( $nodePermissions, 'viewModerated' ) )
{
	$viewModerated = true;
}
elseif( $global_userid )
{
	$viewModerated = $global_userid;
}
else
{
	$viewModerated = false;
}

$conditions = array( 
	'deleted' => hasContentPermission( $nodePermissions, 'viewDeleted' ), 
	'moderated' => $viewModerated 
);

 
$db_slave->sqlreset()
	->select('COUNT(*)')
	->from( NV_FORUM_GLOBALTABLE . '_thread AS thread');
$where  ='';	

$where .= '(thread.node_id = ' . intval( $node_id ) . ') ';

$where .= 'AND (thread.sticky = 0) ';
 
if ( ( isset( $conditions['deleted'] ) || isset( $conditions['moderated'] ) ) && !empty( $global_userid ) )
{ 	
	$where .= 'AND ( thread.discussion_state IN (\'visible\') OR (thread.discussion_state = \'moderated\' AND thread.userid = ' . intval( $global_userid ) . ') ) ';
 
}else
{
	$where .= 'AND ( thread.discussion_state IN (\'visible\') ) ';
	
}
 
if( ! hasContentPermission( $nodePermissions, 'viewOthers' ) )
{ 
	$where .= 'AND thread.userid = ' . $db_slave->quote( $global_userid ) . ' ';	 
}

$db_slave->where( $where );
 
$num_items = $db_slave->query( $db_slave->sql() )->fetchColumn();
 
$select = '';
$select .='user.*, ';
if( !empty( $global_userid ) )
{
	$readMarkingDataLifetime = 30;
	$autoReadDate = NV_CURRENTTIME - $readMarkingDataLifetime * 86400;
	$select .='IF(user.username IS NULL, thread.username, user.username) AS username, ';		
	$select .='IF(thread_read.thread_read_date > '. $autoReadDate .', thread_read.thread_read_date, '. $autoReadDate .') AS thread_read_date, ';				
	$select .='IF(thread_watch.userid IS NULL, 0,';					
	$select .='IF(thread_watch.email_subscribe, \'watch_email\', \'watch_no_email\')) AS thread_is_watched, ';
	$select .='thread_user_post.post_count AS user_post_count';
}else
{
	$select .='NULL AS thread_read_date, ';
	$select .='0 AS thread_is_watched, ';
	$select .='0 AS user_post_count ';
}
 
$db_slave->select( 'thread.*, ' . $select )
		 ->where( $where )
		 ->order( 'thread.last_post_date DESC' )	 
		 ->limit( $per_page_thread )
		 ->offset( ($page - 1) * $per_page_thread );

$join = '';

$join .= 'LEFT JOIN '. NV_USERS_GLOBALTABLE .' AS user ON (user.userid = thread.userid) ';

if( !empty( $global_userid ) )
{ 
	$join .= 'LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_thread_read AS thread_read ON
						(thread_read.thread_id = thread.thread_id
						AND thread_read.userid = '. intval( $global_userid ) .') ';

	$join .= 'LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_thread_watch AS thread_watch
						ON (thread_watch.thread_id = thread.thread_id
						AND thread_watch.userid = '. intval( $global_userid ) .') ';
					
	$join .= 'LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_thread_user_post AS thread_user_post
						ON (thread_user_post.thread_id = thread.thread_id
						AND thread_user_post.userid = '. intval( $global_userid ) .') ';
} 
		 
$db_slave->join( $join );
 
 
$result = $db_slave->query( $db_slave->sql() );
 
$threadDataNoneSticky = array();
while( $rows = $result->fetch() )
{
	$threadDataNoneSticky[] = $rows;
}
$result->closeCursor();


///////////////////////////////////////////////////
$where  ='';	

$where .= '(thread.node_id = ' . intval( $node_id ) . ') ';

$where .= 'AND (thread.sticky = 1) ';
 
if ( ( isset( $conditions['deleted'] ) || isset( $conditions['moderated'] ) ) && !empty( $global_userid ) )
{ 	
	$where .= 'AND ( thread.discussion_state IN (\'visible\') OR (thread.discussion_state = \'moderated\' AND thread.userid = ' . intval( $global_userid ) . ') ) ';
 
}else
{
	$where .= 'AND ( thread.discussion_state IN (\'visible\') ) ';
	
}
 
if( ! hasContentPermission( $nodePermissions, 'viewOthers' ) )
{ 
	$where .= 'AND thread.userid = ' . $db_slave->quote( $global_userid ) . ' ';	 
}

$db_slave->where( $where );
 
$num_items = $db_slave->query( $db_slave->sql() )->fetchColumn();
 
$select = '';
$select .='user.*, ';
if( !empty( $global_userid ) )
{
	$readMarkingDataLifetime = 30;
	$autoReadDate = NV_CURRENTTIME - $readMarkingDataLifetime * 86400;
	$select .='IF(user.username IS NULL, thread.username, user.username) AS username, ';		
	$select .='IF(thread_read.thread_read_date > '. $autoReadDate .', thread_read.thread_read_date, '. $autoReadDate .') AS thread_read_date, ';				
	$select .='IF(thread_watch.userid IS NULL, 0,';					
	$select .='IF(thread_watch.email_subscribe, \'watch_email\', \'watch_no_email\')) AS thread_is_watched, ';
	$select .='thread_user_post.post_count AS user_post_count';
}else
{
	$select .='NULL AS thread_read_date, ';
	$select .='0 AS thread_is_watched, ';
	$select .='0 AS user_post_count ';
}
 
$db_slave->select( 'thread.*, ' . $select )
		 ->where( $where )
		 ->order( 'thread.last_post_date DESC' )	 
		 ->limit( $per_page_thread )
		 ->offset( ($page - 1) * $per_page_thread );

$join = '';

$join .= 'LEFT JOIN '. NV_USERS_GLOBALTABLE .' AS user ON (user.userid = thread.userid) ';

if( !empty( $global_userid ) )
{ 
	$join .= 'LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_thread_read AS thread_read ON
						(thread_read.thread_id = thread.thread_id
						AND thread_read.userid = '. intval( $global_userid ) .') ';

	$join .= 'LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_thread_watch AS thread_watch
						ON (thread_watch.thread_id = thread.thread_id
						AND thread_watch.userid = '. intval( $global_userid ) .') ';
					
	$join .= 'LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_thread_user_post AS thread_user_post
						ON (thread_user_post.thread_id = thread.thread_id
						AND thread_user_post.userid = '. intval( $global_userid ) .') ';
} 
		 
$db_slave->join( $join );
 
$result = $db_slave->query( $db_slave->sql() );
 
$ThreadDataSticky = array();
while( $rows = $result->fetch() )
{
	$ThreadDataSticky[] = $rows;
}
$result->closeCursor();
 
/* cập nhật trạng thái trực tuyến theo khu vực */
if( $global_userid )
{
	updateSessionActivity( $global_userid, 'Forum', 'valid', array( 'node_id' => $node_id ) );
}

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $forum_node[$node_id]['alias'];

$GeneratePage = nv_alias_page( $page_title, $base_url, $num_items, $per_page_thread, $page );

$contents = ThemeForumViewNode( $forumData, $threadDataNoneSticky, $ThreadDataSticky, $GeneratePage );

$page_title = $forum_node[$node_id]['title'];
$key_words = '';
$description = $forum_node[$node_id]['description'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
