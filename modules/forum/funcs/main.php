<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweb.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 22:18
 */

if( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

 
$generalPermissions = $forumData = array();

if( ! defined( 'NV_IS_USER' ) )
{
	/* Nhom khach truy cap => 5 */
	$permission_combination_id = 5;
	$result = $db->query( 'SELECT cache_value FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id =' . intval( $permission_combination_id ) );
	$generalPermissions = $result->fetchColumn();
	$result->closeCursor();

	$generalPermissions = unserializePermissions( $generalPermissions );
}
else
{
	/* Nhom thanh vien truy cap => 4 */
	$permission_combination_id = $user_info['permission_combination_id'];
	$generalPermissions = unserializePermissions( $user_info['global_permission_cache'] );
}
 

$cache_file = NV_CACHE_PREFIX . '.nodePermissions' . $permission_combination_id . '.' . NV_LANG_DATA . '.cache';
if( ( $cache = $nv_Cache->getItem( $module_name, $cache_file ) ) != false )
{
	$dataPermissions = unserialize( $cache );
}
else
{
	$dataPermissions = array();
	$result = $db_slave->query('SELECT content_id, cache_value FROM ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content WHERE permission_combination_id = '. intval( $permission_combination_id ) .' AND content_type = \'node\'');
	while( $rows = $result->fetch() )	
	{
		$dataPermissions[$rows['content_id']] = unserializePermissions($rows['cache_value']);
	}
	$cache = serialize($dataPermissions);
	$nv_Cache->setItem($module_name, $cache_file, $cache);
	
}

$categoryList = array();
$forum_id_array = array();
 
foreach( $forum_node as $_node_id => $node )
{
	if( $node['node_type_id'] == 'category' && $node['status'] == '1' && hasContentPermission( $dataPermissions[$_node_id], 'view') )
	{
		$GetNodeidInParent = GetNodeidInParent( $_node_id );
		foreach( $GetNodeidInParent as $_sub_node_id )
		{
			if( $forum_node[$_sub_node_id]['node_type_id'] == 'forum' && $forum_node[$_sub_node_id]['status'] == '1' && hasContentPermission( $dataPermissions[$_sub_node_id], 'view') )
			{
				$forum_id_array[] = $_sub_node_id;
			}
			
		}
		$categoryList[] = $_node_id;
	}
}

if( ! empty( $forum_id_array ) )
{
	$result = $db->query( 'SELECT forum.*, NULL AS forum_read_date
				FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
				JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
				WHERE node.status=1 AND forum.node_id IN (' . implode( ',', $forum_id_array ) . ')' );
 
	while( $rows = $result->fetch() )
	{
		$forumData[$rows['node_id']] = $rows;
	}
	$result->closeCursor();
}
unset( $forum_id_array );

updateSessionActivity( $global_userid, 'Index', 'valid', array( 'node_alias' => '' ) );

$contents = ThemeForumMain( $forumData, $categoryList );

$page_title = $module_info['custom_title'];

$key_words = $module_info['keywords'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
