<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU(dlinhvan@gmail.com)
 * @Copyright (C) 2013 webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 27-04-2013 08:20
 */
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

// $string='a:4:{s:11:"profilePost";a:8:{s:7:"editAny";s:1:"1";s:9:"deleteAny";s:1:"1";s:13:"hardDeleteAny";s:1:"1";s:4:"warn";s:1:"1";s:11:"viewDeleted";s:1:"1";s:13:"viewModerated";s:1:"1";s:8:"undelete";s:1:"1";s:16:"approveUnapprove";s:1:"1";}s:12:"conversation";a:2:{s:11:"editAnyPost";s:1:"1";s:12:"alwaysInvite";s:1:"1";}s:5:"forum";a:15:{s:18:"stickUnstickThread";i:1;s:16:"lockUnlockThread";i:1;s:15:"manageAnyThread";i:1;s:15:"deleteAnyThread";i:1;s:19:"hardDeleteAnyThread";i:1;s:14:"threadReplyBan";i:1;s:11:"editAnyPost";i:1;s:13:"deleteAnyPost";i:1;s:17:"hardDeleteAnyPost";i:1;s:4:"warn";i:1;s:12:"manageAnyTag";i:1;s:11:"viewDeleted";i:1;s:13:"viewModerated";i:1;s:8:"undelete";i:1;s:16:"approveUnapprove";i:1;}s:7:"general";a:7:{s:7:"viewIps";s:1:"1";s:17:"bypassUserPrivacy";s:1:"1";s:9:"cleanSpam";s:1:"1";s:11:"viewWarning";s:1:"1";s:4:"warn";s:1:"1";s:13:"manageWarning";s:1:"1";s:16:"editBasicProfile";s:1:"1";}}';
// $string = unserialize($string);
// print_r($string);
// die();

$error = '';
$data = array();
$userData = array();
$node_id = $nv_Request->get_int( 'node_id', 'get', 0 );
$type = $nv_Request->get_title( 'type', 'get', '' );

$type = ! empty( $type ) ? $type : 'super';
$moderator_id = 0;
$data['is_super_moderator'] = 0;
$data['extra_user_group_ids'] = array();
$data['general_moderator_permissions'] = array();
$data['moderator_permissions'] = array();
$data['username'] = $nv_Request->get_title( 'username', 'post', '' );
$data['type_mod'] = $nv_Request->get_title( 'type_mod', 'post', $type );
$data['node_id'] = $nv_Request->get_int( 'node_id', 'post', $node_id );
$data['content_type'] = ( $data['node_id'] > 0 ) ? 'node' : '';
$data['is_staff'] = 1;
$adduser = $nv_Request->get_int( 'adduser', 'post', 0 );

$xtpl = new XTemplate( 'moderators_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/moderators' );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'TEMPLATE', $global_config['site_theme'] );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'TYPE', $type );
$xtpl->assign( 'TOKEN', md5( session_id() . $global_config['sitekey'] ) );
if( $data['type_mod'] == 'super' )
{
	$data['is_super'] = 1;
}
else
{
	$data['is_super'] = 0;
}
$xtpl->assign( 'MODERATORS', $data );
$xtpl->assign( 'ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=add' );
$xtpl->assign( 'JSON_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=json' );
$xtpl->assign( 'SUPER_MOD', ( $data['type_mod'] == 'super' ) ? 'checked="checked"' : '' );
$xtpl->assign( 'FORUM_MOD', ( $data['type_mod'] == 'node' ) ? 'checked="checked"' : '' );
$xtpl->assign( 'NODE_DISABLED', ( $data['type_mod'] == 'super' ) ? 'disabled="disabled"' : '' );
$xtpl->assign( 'CLASS_DISABLED', ( $data['type_mod'] == 'super' ) ? 'disabled' : '' );

if( $adduser == 0 )
{

	foreach( $forum_node as $_node_id => $val )
	{

		$xtitle_i = '';
		if( $val['lev'] > 0 )
		{
			for( $i = 1; $i <= $val['lev']; $i++ )
			{
				$xtitle_i .= '&nbsp;&nbsp;&nbsp;&nbsp;';
			}
		}
		$xtitle_i .= $val['title'];
		$xtpl->assign( 'NODE', array(
			'key' => $val['node_id'],
			'name' => $xtitle_i,
			'selected' => ( $val['node_id'] == $data['node_id'] ) ? ' selected="selected"' : '' ) );
		$xtpl->parse( 'main.node' );
	}

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}
elseif( $adduser == 1 )
{

	if( $data['node_id'] == 0 && $data['type_mod'] == 'node' )
	{
		header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=add' );
	}
	
 
					
	$stmt = $db->prepare( '
		SELECT user.*,user_profile.*,user_option.*, user_privacy.* FROM ' . NV_USERS_GLOBALTABLE . ' user
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON
			(user_profile.userid = user.userid)
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_option AS user_option ON
			(user_option.userid = user.userid)
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_privacy AS user_privacy ON
			(user_privacy.userid = user.userid)
		WHERE user.username=:username' );
	$stmt->bindParam( ':username', $data['username'], PDO::PARAM_STR );
	$stmt->execute();
	$userData = $stmt->fetch();

	if( empty( $userData ) )
	{
		$error = 'Lỗi không tồn tại user này';

		$xtpl->assign( 'ERROR', $error );

		$xtpl->parse( 'error' );

		$contents = $xtpl->text( 'error' );

		include NV_ROOTDIR . '/includes/header.php';
		echo nv_admin_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}

}
elseif( $adduser == 2 )
{

	$data['userid'] = $nv_Request->get_int( 'userid', 'post', 0 );
	$data['is_super_moderator'] = $nv_Request->get_int( 'is_super_moderator', 'post', 0 );
	$data['content_id'] = $nv_Request->get_int( 'content_id', 'post', 0 );
	$data['content_type'] = $nv_Request->get_title( 'content_type', 'post', '' );

	$data['is_staff'] = $nv_Request->get_int( 'is_staff', 'post', 0 );
	$data['extra_user_group_ids'] = $nv_Request->get_typed_array( 'extra_user_group_ids', 'post', 'int', array() );
	$data['general_moderator_permissions'] = $nv_Request->get_typed_array( 'general_moderator_permissions', 'post', array(), array() );

	$data['moderator_permissions'] = $nv_Request->get_typed_array( 'moderator_permissions', 'post', array(), array() );

	if( $data['content_type'] )
	{
		$moderatorId = insertOrUpdateContentModerator( 
			$data['userid'], $data['content_type'], $data['content_id'], $data['moderator_permissions'], 
			array( 'general_moderator_permissions' => $data['general_moderator_permissions'], 'extra_user_group_ids' => $data['extra_user_group_ids'], 'is_staff' => $data['is_staff'], 'is_insert' => true ) );
	}
	else
	{
		$userId = insertOrUpdateGeneralModerator( $data['userid'], $data['general_moderator_permissions'], $data['is_super_moderator'], array(
			'super_moderator_permissions' => $data['moderator_permissions'],
			'extra_user_group_ids' => $data['extra_user_group_ids'],
			'is_staff' => $data['is_staff'], 'is_insert' => true ) );

		$moderatorId = "supermod_{$userId}";
	}
	

	$json = array();
	if( $data['is_super_moderator'] == 0 )
	{
		$json['redirect'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators#_' . $moderator_id;
	}
	if( $data['is_super_moderator'] == 1 )
	{
		$json['redirect'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators#_supermod_' . $data['userid'];

	}

	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';

}

$xtpl->assign( 'USER', $userData );
$xtpl->assign( 'DATA', $data );

$result = $db->query( 'SELECT moderator_content.*, user.username			
			FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content AS moderator_content
			INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator_content.userid)		
			WHERE (moderator_content.content_type = \'node\') 
				AND (moderator_content.content_id = ' . intval( $data['node_id'] ) . ') 
				AND (moderator_content.userid = ' . intval( $userData['userid'] ) . ')
			ORDER BY user.username' );
 
$moderator_content = $result->fetch();

$result->closeCursor();

/* SELECT permission_entry  */
$result = $db->query( '
	SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry 
	WHERE user_group_id = 0 
	AND userid = ' . intval( $userData['userid'] ) );

 
$permission_entry = array();
while( $rows = $result->fetch() )
{
	$permission_entry[$rows['permission_group_id']][$rows['permission_id']] = $rows;
}
$result->closeCursor();

/* SELECT permission_entry_content  */
$result = $db->query( '
		SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content 
		WHERE content_type=\'node\' 
		AND content_id = ' . intval( $data['node_id'] ) . ' 
		AND user_group_id = 0 
		AND userid = ' . intval( $userData['userid'] ) );
 
$permission_entry_content = array();
while( $rows = $result->fetch() )
{
	$permission_entry_content[$rows['permission_group_id']][$rows['permission_id']] = $rows;
}
$result->closeCursor();



$groups_list = nv_groups_list();

$getAllPermissions = getAllPermissions();

$generalInterfaceGroups = array();

$getAllPermissionInterfaceGroups = getAllPermissionInterfaceGroups();
foreach( $getAllPermissionInterfaceGroups as $generalInterfaceGroupId )
{
	foreach( $getAllPermissions as $permission )
	{
		if( $generalInterfaceGroupId['interface_group_id'] == $permission['interface_group_id'] )
		{
			$permission['title'] = $lang_module[$permission['permission_group_id'] . '_' . $permission['permission_id']];
			
			$generalInterfaceGroups[$generalInterfaceGroupId['interface_group_id']][] = $permission;
		}

	}

}

/* SELECT moderator  */
$stmt = $db->prepare( '
	SELECT moderator.*, user.username
	FROM ' . NV_FORUM_GLOBALTABLE . '_moderator AS moderator
	INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator.userid)
	WHERE moderator.userid = ' . intval( $userData['userid'] ) );

$stmt->execute();

$moderator = $stmt->fetch();

$stmt->closeCursor();

$data['extra_user_group_ids'] = ! empty( $moderator['extra_user_group_ids'] ) ? explode( ',', $moderator['extra_user_group_ids'] ) : array();

foreach( $groups_list as $group_id => $title )
{
	if( $group_id != '6' )
	{
		$xtpl->assign( 'GROUP', array(
			'group_id' => $group_id,
			'title' => $title,
			'checked' => in_array( $group_id, $data['extra_user_group_ids'] ) ? 'checked="checked"' : '' ) );
		$xtpl->parse( 'adduser.group' );
	}

}
$moderator_permissions = array();
if( $data['is_super'] == '1' )
{
	$moderator = $db->query( '
		SELECT moderator.*, user.username
		FROM ' . NV_FORUM_GLOBALTABLE . '_moderator AS moderator
		INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator.userid)
		WHERE moderator.userid = ' . intval( $userData['userid'] ) )->fetch();
	$moderator_permissions = ! empty( $moderator['moderator_permissions'] ) ? unserialize( $moderator['moderator_permissions'] ) : array();
}

$moderator_permissions_array = array(
	'generalModeratorPermissions',
	'profilePostModeratorPermissions',
	'conversationModeratorPermissions',
	'forumModeratorPermissions' );
$getAllPermissions = getAllPermissions();
$generalInterfaceGroups = array();
foreach( $moderator_permissions_array as $interface_group_id )
{
	foreach( $getAllPermissions as $permission )
	{
		if( $interface_group_id == $permission['interface_group_id'] && in_array( $interface_group_id, $moderator_permissions_array ) )
		{
			$permission['title'] = $lang_module[$permission['permission_group_id'] . '_' . $permission['permission_id']];
			$generalInterfaceGroups[$interface_group_id][$permission['permission_id']] = $permission;
		}

	}

}
 
foreach( $generalInterfaceGroups as $interface_group_id => $permissions )
{
	$xtpl->assign( 'IFGI', $interface_group_id );
	$xtpl->assign( 'TITLE', $lang_module[$interface_group_id] );
	foreach( $permissions as $permission_id => $permission )
	{

		$permission['checked'] = '';
	
		if( isset( $permission_entry[$permission['permission_group_id']][$permission['permission_id']] ) )
		{
			$permission['checked'] = 'checked="checked"';
		}
		if( isset( $permission_entry_content[$permission['permission_group_id']][$permission['permission_id']] ) )
		{
			$permission['checked'] = 'checked="checked"';
		}

		if( $permission['permission_group_id'] == 'forum' )
		{
			$permission['name'] = 'moderator_permissions';
		}
		else
		{
			$permission['name'] = 'general_moderator_permissions';
		}

		$xtpl->assign( 'PERMISSION', $permission );
		$xtpl->parse( 'adduser.interface_group.permission' );
	}
	$xtpl->parse( 'adduser.interface_group' );
}
 
$xtpl->assign( 'IS_STAFF', ( $userData['is_staff'] == 1 ) ? 'checked="checked"' : '' );

$xtpl->parse( 'adduser' );
$contents = $xtpl->text( 'adduser' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
