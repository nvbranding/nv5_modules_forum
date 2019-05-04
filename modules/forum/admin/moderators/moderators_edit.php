<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU(dlinhvan@gmail.com)
 * @Copyright (C) 2013 webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 27-04-2013 08:20
 */
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$username = $nv_Request->get_title( 'user', 'get', '' );
$mod = $nv_Request->get_int( 'mod', 'get', 0 );
$super = $nv_Request->get_int( 'super', 'get', 0 );
$userid = $nv_Request->get_int( 'userid', 'get', 0 );

$groups_list = nv_groups_list();

// if( $super == 0 )
// {

// }
// elseif( $super == 1 )
// {
// $stmt = $db->prepare( '
// SELECT moderator.*, user.username, user.is_staff
// FROM ' . NV_FORUM_GLOBALTABLE . '_moderator AS moderator
// INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator.userid)
// WHERE moderator.userid = ' . intval( $userid ) );
// }

/* SELECT moderator_content  */
$result = $db->query( '
		SELECT moderator_content.*, user.username 				
		FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content AS moderator_content
		INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator_content.userid)			
		WHERE (moderator_content.moderator_id = ' . intval( $mod ) . ')
		ORDER BY user.username' );
 
$moderator_content = $result->fetch();
$result->closeCursor();

$moderator_permissions_content = ! empty( $moderator_content['moderator_permissions'] ) ? unserialize( $moderator_content['moderator_permissions'] ) : array();
//var_dump($moderator_permissions_content);

/* SELECT moderator  */
$result = $db->query( '
	SELECT moderator.*, user.username
	FROM ' . NV_FORUM_GLOBALTABLE . '_moderator AS moderator
	INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator.userid)
	WHERE moderator.userid = ' . intval( $moderator_content['userid'] ) );
 
$moderator = $result->fetch();

$result->closeCursor();


$moderator_permissions = ! empty( $moderator['moderator_permissions'] ) ? unserialize( $moderator['moderator_permissions'] ) : array();

$user_data = $db->query( 'SELECT user.* FROM ' . NV_USERS_GLOBALTABLE . ' AS user WHERE user.userid = ' . intval( $moderator_content['userid'] ) )->fetch();

if( $nv_Request->get_int( 'save', 'post', 0 ) == 1 )
{
	$json = array();

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
			array( 'general_moderator_permissions' => $data['general_moderator_permissions'], 'extra_user_group_ids' => $data['extra_user_group_ids'], 'is_staff' => $data['is_staff'], 'is_update' => true ) );
	}
	else
	{
		$userId = insertOrUpdateGeneralModerator( $data['userid'], $data['general_moderator_permissions'], $data['is_super_moderator'], array(
			'super_moderator_permissions' => $data['moderator_permissions'],
			'extra_user_group_ids' => $data['extra_user_group_ids'],
			'is_staff' => $data['is_staff'] ) );

		$moderatorId = "supermod_{$userId}";
	}

	if( $data['is_super_moderator'] == 0 )
	{
		$json['redirect'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators#_' . $moderatorId;
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

$xtpl = new XTemplate( 'moderators_edit.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/moderators' );
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
 
$xtpl->assign( 'TOKEN', md5( session_id() . $global_config['sitekey'] ) );

if( empty( $moderator_content ) )
{
	$error = 'Lỗi không tồn tại người điều hành này';

	$xtpl->assign( 'ERROR', $error );

	$xtpl->parse( 'error' );

	$contents = $xtpl->text( 'error' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
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
		if( isset( $moderator_permissions_content[$permission['permission_group_id']][$permission_id] ) )
		{
			$permission['checked'] = 'checked="checked"';
		}
		if( isset( $moderator_permissions[$permission['permission_group_id']][$permission_id] ) )
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
		$xtpl->parse( 'edituser.interface_group.permission' );
	}
	$xtpl->parse( 'edituser.interface_group' );
}

$data['extra_user_group_ids'] = ! empty( $moderator['extra_user_group_ids'] ) ? explode( ',', $moderator['extra_user_group_ids'] ) : array();

foreach( $groups_list as $group_id => $title )
{
	if( $group_id != '6' )
	{
		$xtpl->assign( 'GROUP', array(
			'group_id' => $group_id,
			'title' => $title,
			'checked' => in_array( $group_id, $data['extra_user_group_ids'] ) ? 'checked="checked"' : '' ) );
		$xtpl->parse( 'edituser.group' );
	}
}

$xtpl->assign( 'MOD_CONTENT', $moderator_content );
$xtpl->assign( 'IS_STAFF', ( $user_data['is_staff'] == 1 ) ? 'checked="checked"' : '' );

if( $super == 0 )
{
	$xtpl->assign( 'ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=edit&user=' . $moderator_content['username'] . '&mod=' . $mod );
}
elseif( $super == 1 )
{

	$xtpl->assign( 'ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=edit&super=1&userid=' . $moderator_content['userid'] );
}

$xtpl->parse( 'edituser' );
$contents = $xtpl->text( 'edituser' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
