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

if( $forum_node[$node_id]['node_type_id'] == 'category' )
{
	Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true ) . '#' . $forum_node[$node_id]['alias'] . '-' . $node_id );
	die();
}

$parent_id = $node_id;
while( $parent_id > 0 )
{
	$array_cat_i = $forum_node[$parent_id];
	$array_mod_title[] = array(
		'catid' => $parent_id,
		'title' => $array_cat_i['title'],
		'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $array_cat_i['alias'] );
	$parent_id = $array_cat_i['parent_id'];
}
sort( $array_mod_title, SORT_NUMERIC );

$forumData = $generalPermissions = $nodePermissions = $threadDataSticky = $threadDataNoneSticky = array();

if( ! defined( 'NV_IS_USER' ) )
{
	/* Nhom khach truy cap => 5*/
	$permission_combination_id = 5;
	$result = $db_slave->query( 'SELECT cache_value FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id =' . intval( $permission_combination_id ) );
	$generalPermissions = $result->fetchColumn();
	$result->closeCursor();

	$generalPermissions = unserializePermissions( $generalPermissions );

	$forumData = $db_slave->query( 'SELECT node.*, forum.*
				,
				permission.cache_value AS node_permission_cache,
					NULL AS forum_read_date,
					0 AS forum_is_watched
			FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
					ON (permission.permission_combination_id = ' . intval( $permission_combination_id ) . '
						AND permission.content_type = \'node\'
						AND permission.content_id = forum.node_id)
			WHERE node.node_id = ' . intval( $node_id ) )->fetch();
}
else
{
	/* Nhom thanh vien truy cap => 4 */
	$permission_combination_id = $user_info['permission_combination_id'];
	$generalPermissions = $user_info['permissions'];

	$readMarkingDataLifetime = 30; //cau hinh
	$autoReadDate = NV_CURRENTTIME - $readMarkingDataLifetime * 86400;

	$forumData = $db_slave->query( 'SELECT node.*, forum.*
				,
				permission.cache_value AS node_permission_cache,
					IF(forum_read.forum_read_date > ' . intval( $autoReadDate ) . ', forum_read.forum_read_date, ' . intval( $autoReadDate ) . ') AS forum_read_date,
					IF(forum_watch.userid IS NULL, 0, 1) AS forum_is_watched
			FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)		
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
					ON (permission.permission_combination_id = ' . intval( $permission_combination_id ) . '
						AND permission.content_type = \'node\'
						AND permission.content_id = forum.node_id)
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum_read AS forum_read ON
						(forum_read.node_id = forum.node_id
						AND forum_read.userid = ' . intval( $global_userid ) . ')
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum_watch AS forum_watch
						ON (forum_watch.node_id = forum.node_id
						AND forum_watch.userid = ' . intval( $global_userid ) . ')
			WHERE node.node_id = ' . intval( $node_id ) )->fetch();

}

if( ! $forumData )
{
	$contents = ThemeErrorNotFoundForum();
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

/////
$nodePermissions = array();
if( isset( $forumData['node_permission_cache'] ) )
{

	if( is_string( $forumData['node_permission_cache'] ) )
	{
		$nodePermissions = unserializePermissions( $forumData['node_permission_cache'] );
	}

	if( is_array( $forumData['node_permission_cache'] ) )
	{
		$nodePermissions = $forumData['node_permission_cache'];
	}
	unset( $forumData['node_permission_cache'] );
}

// kiem tra quyen xem dien dan
if( ! ModelForum_canViewForum( $forumData, $errorLangKey, $nodePermissions ) )
{
	$contents = ThemeErrorOrNoPermission( $errorLangKey );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

$threadsPerPage = $discussionsPerPage;

list( $defaultOrder, $defaultOrderDirection ) = array( $forumData['default_sort_order'], $forumData['default_sort_direction'] );

//$order = $nv_Request->get_int( 'order', 'get', $defaultOrder );
$order = 'last_post_date';
$orderDirection = $nv_Request->get_int( 'direction', 'get', $defaultOrderDirection );

$displayConditions = array();

$prefixId = $nv_Request->get_int( 'prefix_id', 'get', 0 );
if( $prefixId )
{
	$displayConditions['prefix_id'] = $prefixId;
}

$noDateLimit = $nv_Request->get_bool( 'no_date_limit', 'get', 0 );
$isDateLimited = ( $forumData['list_date_limit_days'] && $order == 'last_post_date' && ! $noDateLimit );
if( $isDateLimited )
{
	$displayConditions['last_post_date'] = array( '>=', NV_CURRENTTIME - 86400 * $forumData['list_date_limit_days'] );
}

function _getThreadSortFields( array $forum )
{
	return array(
		'title',
		'post_date',
		'reply_count',
		'view_count',
		'last_post_date' );
}
function getThreadFetchElements( array $forum, array $displayConditions )
{
	global $global_userid, $user_info, $nodePermissions, $modelThreadConst;
	$threadFetchConditions = $displayConditions + ModelThread_getPermissionBasedThreadFetchConditions( $forum, $nodePermissions );

	$getResponseType = 'html'; //rss

	if( $getResponseType != 'rss' )
	{
		$threadFetchConditions += array( 'sticky' => 0 );
	}

	$threadFetchOptions = array(
		'join' => $modelThreadConst['user'],
		'readUserId' => $global_userid,
		'watchUserId' => $global_userid,
		'postCountUserId' => $global_userid,
		);
	if( ! empty( $threadFetchConditions['deleted'] ) )
	{
		$threadFetchOptions['join'] |= $modelThreadConst['deletion_log'];
	}

	if( $getResponseType == 'rss' )
	{
		$threadFetchOptions['join'] |= $modelThreadConst['firstpost'];
	}

	return array( 'conditions' => $threadFetchConditions, 'options' => $threadFetchOptions );
}

$fetchElements = getThreadFetchElements( $forumData, $displayConditions );
$threadFetchConditions = $fetchElements['conditions'];
$threadFetchOptions = $fetchElements['options'] + array(
	'perPage' => $discussionsPerPage,
	'page' => $page,
	'order' => $order,
	'orderDirection' => $orderDirection );
unset( $fetchElements );

$totalThreads = $db_slave->query( '
			SELECT COUNT(*) FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
			WHERE (thread.node_id = ' . intval( $node_id ) . ')
				AND (thread.sticky = 0) 
				AND (thread.discussion_state IN (\'visible\',\'deleted\',\'moderated\'))' )->fetchColumn();

$totalThreads = ModelThread_countThreadsInForum( $node_id, $threadFetchConditions );

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $forum_node[$node_id]['alias'];

$generatePage = nv_alias_page( $page_title, $base_url, $totalThreads, $discussionsPerPage, $page );

$threads = ModelThread_getThreadsInForum( $node_id, $threadFetchConditions, $threadFetchOptions );

if( $page == 1 )
{
	$stickyThreadFetchOptions = $threadFetchOptions;
	unset( $stickyThreadFetchOptions['perPage'], $stickyThreadFetchOptions['page'] );

	$stickyThreadConditions = $threadFetchConditions;
	unset( $stickyThreadConditions['last_post_date'] );

	$stickyThreadFetchOptions['limit'] = 10;
	$stickyThreadFetchOptions['offset'] = 0;

	$stickyThreads = ModelThread_getStickyThreadsInForum( $node_id, $stickyThreadConditions, $stickyThreadFetchOptions );

}
else
{
	$stickyThreads = array();
}

// prepare all threads for the thread list
$inlineModOptions = array();

foreach( $threads as &$thread )
{
	$threadModOptions = ModelThread_addInlineModOptionToThread( $thread, $forumData, $nodePermissions );
	$inlineModOptions += $threadModOptions;

	$thread = ModelThread_prepareThread( $thread, $forumData, $nodePermissions );
}
foreach( $stickyThreads as &$thread )
{
	$threadModOptions = ModelThread_addInlineModOptionToThread( $thread, $forumData, $nodePermissions );
	$inlineModOptions += $threadModOptions;

	$thread = ModelThread_prepareThread( $thread, $forumData, $nodePermissions );
}
unset( $thread );
 
// if we've read everything on the first page of a normal sort order, probably need to mark as read
if( $global_userid && $page == 1 && ! $displayConditions && $order == 'last_post_date' && $orderDirection == 'desc' && $forumData['forum_read_date'] < $forumData['last_post_date'] )
{
	$hasNew = false;
	foreach( $threads as $thread )
	{
		if( $thread['isNew'] )
		{
			$hasNew = true;
			break;
		}
	}

	if( ! $hasNew )
	{
		// everything read, but forum not marked as read. Let's check.
		ModelForum_markForumReadIfNeeded( $forumData );
	}
}

// get the ordering params set for the header links
$orderParams = array();
foreach( _getThreadSortFields( $forumData ) as $field )
{
	$orderParams[$field] = $displayConditions;
	$orderParams[$field]['order'] = ( $field != $defaultOrder ? $field : false );
	if( $order == $field )
	{
		$orderParams[$field]['direction'] = ( $orderDirection == 'desc' ? 'asc' : 'desc' );
	}
}

$pageNavParams = $displayConditions;
$pageNavParams['order'] = ( $order != $defaultOrder ? $order : false );
$pageNavParams['direction'] = ( $orderDirection != $defaultOrderDirection ? $orderDirection : false );
if( $noDateLimit )
{
	$pageNavParams['no_date_limit'] = 1;
}
unset( $pageNavParams['last_post_date'] );

$threadEndOffset = ( $page - 1 ) * $threadsPerPage + count( $threads );
$showDateLimitDisabler = ( $isDateLimited && $threadEndOffset >= $totalThreads );

$dataContent = array(
	'forum' => $forumData,
	'generatePage' => $generatePage,
	'canPostThread' => ModelForum_canPostThreadInForum( $forumData, $null, $nodePermissions ),
	'canSearch' => true,
	'canWatchForum' => ModelForum_canWatchForum( $forumData, $null, $nodePermissions ),

	'inlineModOptions' => $inlineModOptions,
	'threads' => $threads,
	'stickyThreads' => $stickyThreads,

	'ignoredNames' => '',

	'order' => $order,
	'orderDirection' => $orderDirection,
	'orderParams' => $orderParams,
	'displayConditions' => $displayConditions,

	'pageNavParams' => $pageNavParams,
	'page' => $page,
	'threadStartOffset' => ( $page - 1 ) * $threadsPerPage + 1,
	'threadEndOffset' => $threadEndOffset,
	'threadsPerPage' => $threadsPerPage,
	'totalThreads' => $totalThreads,

	'showPostedNotice' => $nv_Request->get_int( 'posted', 'get', 0 ),
	'showDateLimitDisabler' => $showDateLimitDisabler );

/* cập nhật trạng thái trực tuyến theo khu vực */
if( $global_userid )
{
	updateSessionActivity( $global_userid, 'Forum', 'valid', array( 'node_id' => $node_id ) );
}

$contents = ThemeForumViewNode( $dataContent );

$page_title = $forum_node[$node_id]['title'];
$key_words = '';
$description = $forum_node[$node_id]['description'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
