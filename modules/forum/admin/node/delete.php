<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$data['node_type_id'] = nv_substr( $nv_Request->get_title( 'node_type_id', 'get,post', '', '' ), 0, 10 );
$data['token'] = nv_substr( $nv_Request->get_title( 'token', 'get,post', '', '' ), 0, 50 );
$data['node_id'] = $nv_Request->get_int( 'node_id', 'get,post', '', 0 );
 
if( ! empty( $data['node_id'] ) && $data['token'] == md5( $nv_Request->session_id . $global_config['sitekey'] . $data['node_id'] ) )
{

	function DeleteNodeById( $node_id, $node_type_id )
	{
		global $db, $forum_node, $module_name;
		
		if( $node_type_id == 'forum' )
		{
			$node = $db->query( 'SELECT node.*, forum.*			
			FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)		
			WHERE node.node_id =' . intval( $node_id ) )->fetch();
		}else
		{
			
			$node = $db->query( 'SELECT node.*
				FROM ' . NV_FORUM_GLOBALTABLE . '_node AS node
				WHERE node.node_id =' . intval( $node_id ) )->fetch();
		}
		
		
		
		$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_node WHERE node_id=' . intval( $node['node_id'] ) );
		
		
		if( $node_type_id == 'forum' )
		{
			$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_forum WHERE node_id=' . intval( $node['node_id'] ) );
			$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_forum_prefix WHERE node_id = ' . intval( $node['node_id'] ) );
			$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_forum_watch WHERE node_id = ' . intval( $node['node_id'] ) );
 
		}
		$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content WHERE content_type = \'node\' AND content_id = ' . intval( $node['node_id'] ) ); 
		$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content WHERE content_type = \'node\' AND content_id = ' . intval( $node['node_id'] ) ); 
		
		
		
		$result = $db->query( 'SELECT moderator_content.*, users.username					
					FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content AS moderator_content
					INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS users ON (users.userid = moderator_content.userid)			
					WHERE moderator_content.content_type = \'node\' AND moderator_content.content_id = ' . intval( $node['node_id'] ) . '
					ORDER BY users.username' );
		$users_array = array();
		while( $rows = $result->fetch() )
		{
			$users_array[] = $rows['userid'];
			$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content WHERE moderator_id=' . intval( $rows['moderator_id'] ) );
		}
		$result->closeCursor();

		if( ! empty( $users_array ) )
		{
			foreach( $users_array as $userid )
			{
				$result = $db->query( 'SELECT *
				FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content
				WHERE content_type = \'node\' AND content_id = ' . intval( $node['node_id'] ) . ' 
					AND user_group_id = 0 AND userid =' . intval( $userid ) );

				while( $rows = $result->fetch() )
				{
					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content WHERE permission_entry_id = ' . intval( $rows['permission_entry_id'] ) );
				}
				$result->closeCursor();

				$user = $db->query( 'SELECT * FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid = ' . intval( $userid ) );

				$combination = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id = ' . intval( $user['permission_combination_id'] ) );

				rebuildPermissionCombinationById( $combination );

				$result = $db->query( 'SELECT moderator_content.*, user.username		
					FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content AS moderator_content
					INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator_content.userid)
					WHERE moderator_content.userid =' . intval( $userid ) );
				while( $rows = $result->fetch() )
				{
					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content WHERE moderator_id = ' . intval( $rows['moderator_id'] ) );
				}
				$result->closeCursor();

				if( isset( $user['secondary_group_ids'] ) && $user['secondary_group_ids'] != '' )
				{
					$userGroups = explode( ',', $user['secondary_group_ids'] );
				}
				else
				{
					$userGroups = array();
				}
				$userGroups[] = $user['user_group_id'];

				$permission_combination_id = $db->query( 'SELECT permission_combination_id FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE userid = 0 AND user_group_list = ' . $db->quote( implode( ',', $userGroups ) ) )->fetchColumn();

				$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET permission_combination_id = ' . intval( $permission_combination_id ) . ' WHERE userid = ' . intval( $userid ) );

				$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id = ' . intval( $user['permission_combination_id'] ) );
				$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination_user_group WHERE permission_combination_id = ' . intval( $user['permission_combination_id'] ) );
				$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content WHERE permission_combination_id = ' . intval( $user['permission_combination_id'] ) );

				$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET is_moderator = 0 WHERE user_id = ' . intval( $userid ) );
				$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET secondary_group_ids = \'\' WHERE userid = ' . intval( $userid ) );
				$db->query( 'DELETE FROM ' . NV_USERS_GLOBALTABLE . '_group_relation WHERE userid = ' . intval( $userid ) );

				$db->query( 'INSERT INTO ' . NV_USERS_GLOBALTABLE . '_group_relation
							(userid, user_group_id, is_primary)
						VALUES
							(' . intval( $userid ) . ', 4, 1)
						ON DUPLICATE KEY UPDATE
							is_primary = VALUES(is_primary)' );
			}
		}
		 

	}
	
	DeleteNodeById( $data['node_id'], $data['node_type_id'] ); 
	
	$subcatid = explode( ',', $forum_node[$data['node_id']]['subcatid'] );
	foreach( $subcatid as $sub_node_id )
	{
		$node_type_id = $forum_node[$sub_node_id]['node_type_id'];
		 
		DeleteNodeById( $sub_node_id, $node_type_id );
	}
 
	deleteCache( 'node', $module_name );
	
	forum_fix_node_sort();
	
	$json['redirect'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node';
	
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';
	
}
