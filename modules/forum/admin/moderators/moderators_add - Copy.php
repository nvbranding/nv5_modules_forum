<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU(dlinhvan@gmail.com)
 * @Copyright (C) 2013 webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 27-04-2013 08:20
 */
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$node_id = $nv_Request->get_int( 'node_id', 'get', 0 );
$type = $nv_Request->get_title( 'type', 'get', '' );
$type = ! empty( $type ) ? $type : 'super';

$moderators['username'] = $nv_Request->get_title( 'username', 'post', '' );
$moderators['type_mod'] = $nv_Request->get_title( 'type_mod', 'post', $type );
$moderators['node_id'] = $nv_Request->get_int( 'node_id', 'post', $node_id );

$data = array();
$data['extra_user_group_ids'] = array();
$data['general_moderator_permissions'] = array();
$data['moderator_permissions'] = array();

$error = '';
$userinfo = array();

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
if( $moderators['type_mod'] == 'super' )
{
	$moderators['is_super'] = 1;
}
else
{
	$moderators['is_super'] = 0;
}
$xtpl->assign( 'MODERATORS', $moderators );
$xtpl->assign( 'ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=add' );
$xtpl->assign( 'JSON_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=json' );
$xtpl->assign( 'SUPER_MOD', ( $moderators['type_mod'] == 'super' ) ? 'checked="checked"' : '' );
$xtpl->assign( 'FORUM_MOD', ( $moderators['type_mod'] == 'node' ) ? 'checked="checked"' : '' );
$xtpl->assign( 'NODE_DISABLED', ( $moderators['type_mod'] == 'super' ) ? 'disabled="disabled"' : '' );
$xtpl->assign( 'CLASS_DISABLED', ( $moderators['type_mod'] == 'super' ) ? 'disabled' : '' );

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
			'selected' => ( $val['node_id'] == $moderators['node_id'] ) ? ' selected="selected"' : '' ) );
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

	$stmt = $db->prepare( 'SELECT userid, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE username=:username' );
	$stmt->bindParam( ':username', $moderators['username'], PDO::PARAM_STR );
	$stmt->execute();
	$userinfo = $stmt->fetch();
	$xtpl->assign( 'USER', $userinfo );
	if( empty( $userinfo ) )
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

	$data['user_id'] = $nv_Request->get_int( 'user_id', 'post', 0 );
	$data['is_super_moderator'] = $nv_Request->get_int( 'is_super_moderator', 'post', 0 );
	$data['content_id'] = $nv_Request->get_int( 'content_id', 'post', 0 );
	$data['content_type'] = $nv_Request->get_title( 'content_type', 'post', '' );

	$data['is_staff'] = $nv_Request->get_int( 'is_staff', 'post', 0 );
	$data['extra_user_group_ids'] = $nv_Request->get_typed_array( 'extra_user_group_ids', 'post', 'int', array() );
	$data['general_moderator_permissions'] = $nv_Request->get_typed_array( 'general_moderator_permissions', 'post', array(), array() );

	$data['moderator_permissions'] = $nv_Request->get_typed_array( 'moderator_permissions', 'post', array() );

	/* SELECT permission_entry  */
	$stmt = $db->prepare( '
		SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry 
		WHERE user_group_id = 0 
		AND user_id = ' . intval( $data['user_id'] ) );

	$stmt->execute();
	$permission_entry = array();
	while( $rows = $stmt->fetch() )
	{ 
		$permission_entry[$rows['permission_group_id']][$rows['permission_id']] = $rows;
	}
	$stmt->closeCursor();
 
	/* SELECT permission_entry_content  */
	$stmt = $db->prepare( '
		SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content 
		WHERE content_type=\'node\' 
		AND content_id = ' . intval( $data['content_id'] ) . ' 
		AND user_group_id = 0 
		AND user_id = ' . intval( $data['user_id'] ) );

	$stmt->execute();
	$permission_entry_content = array();
	while( $rows = $stmt->fetch() )
	{
		$permission_entry_content[$rows['permission_id']] = $rows;
	}
	$stmt->closeCursor();

	/* BEGIN update is_staff users*/

	//$db->exec( 'UPDATE '. NV_USERS_GLOBALTABLE .' SET is_staff=' . intval( $is_staff ) . ' WHERE userid=' . intval( $data['user_id'] ) );

	/* END update is_staff users*/

	/* BEGIN Add permission_entry */
	$moderator_array = array();
	$moderator_content_array = array();
	if( ! empty( $data['general_moderator_permissions'] ) )
	{
		foreach( $data['general_moderator_permissions'] as $permission_group_id => $permissions )
		{
			foreach( $permissions as $permission_id => $ischeck )
			{
				if( $ischeck == 1 && ! isset( $permission_entry[$permission_group_id][$permission_id] ) )
				{
 
					$permission_value = 'allow';
					$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_entry SET 
						user_group_id= 0, 
						user_id=' . intval( $data['user_id'] ) . ', 
						permission_group_id=:permission_group_id, 
						permission_id=:permission_id, 
						permission_value=:permission_value, 
						permission_value_int=0' );
					$stmt->bindParam( ':permission_group_id', $permission_group_id, PDO::PARAM_STR );
					$stmt->bindParam( ':permission_id', $permission_id, PDO::PARAM_STR );
					$stmt->bindParam( ':permission_value', $permission_value, PDO::PARAM_STR );
					$stmt->execute();
					$stmt->closeCursor();
					$moderator_array[$permission_group_id][] = array( $permission_id => $ischeck );

				}
				else
				{
					unset( $permission_entry[$permission_group_id][$permission_id] );
				}

			}

		}
	}

	if( ! empty( $permission_entry ) )
	{
		foreach( $permission_entry as $permission_group_id => $pms )
		{
			foreach( $pms as $permission_id => $pms )
			{
				$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry WHERE permission_entry_id=' . intval( $pms['permission_entry_id'] ) );
		
			}
		}
	}

	/* END Add permission_entry */

	/* BEGIN Add permission_entry_content */
	foreach( $data['moderator_permissions'] as $permission_group_id => $permissions )
	{
		foreach( $permissions as $permission_id => $ischeck )
		{
			if( $ischeck == 1 && ! isset( $permission_entry_content[$permission_id] ) )
			{

				if( ! isset( $permission_entry_content[$permission_id] ) && $permission_group_id == 'forum' )
				{
					// insert permission_entry_content
					$permission_value = 'content_allow';
					$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content SET 
						user_group_id= 0, 
						user_id=' . intval( $data['user_id'] ) . ', 
						content_id=' . intval( $data['content_id'] ) . ', 
						content_type=:content_type, 
						permission_group_id=:permission_group_id, 
						permission_id=:permission_id, 
						permission_value=:permission_value, 
						permission_value_int=0' );
					$stmt->bindParam( ':content_type', $data['content_type'], PDO::PARAM_STR );
					$stmt->bindParam( ':permission_group_id', $permission_group_id, PDO::PARAM_STR );
					$stmt->bindParam( ':permission_id', $permission_id, PDO::PARAM_STR );
					$stmt->bindParam( ':permission_value', $permission_value, PDO::PARAM_STR );
					$stmt->execute();
					$stmt->closeCursor();

					$moderator_content_array[$permission_group_id][] = array( $permission_id => $ischeck );
				}

			}
			else
			{
				unset( $permission_entry_content[$permission_id] );
			}
		}
	}

	if( ! empty( $permission_entry_content ) )
	{
		foreach( $permission_entry_content as $permission_id => $pms )
		{
			$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content WHERE permission_entry_id=' . intval( $pms['permission_entry_id'] ) );
		}
	}
	/* END Add permission_entry_content */
 
	/* BEGIN Add Moderator to User Groups */
	$stmt = $db->prepare( 'SELECT moderator.*, user.username
			FROM ' . NV_FORUM_GLOBALTABLE . '_moderator AS moderator
			INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator.user_id)
			WHERE moderator.user_id = ' . intval( $data['user_id'] ) );
	$stmt->execute();

	$moderator = $stmt->fetch();

	$stmt->closeCursor();

	$moderator_permissions = serialize( $moderator_array );
	$extra_user_group_ids = ! empty( $data['extra_user_group_ids'] ) ? implode( ',', $data['extra_user_group_ids'] ) : '';

	if( ! empty( $moderator_array ) && empty( $moderator ) )
	{

		$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_moderator SET 
			user_id=' . intval( $data['user_id'] ) . ', 
			is_super_moderator=' . intval( $data['is_super_moderator'] ) . ', 
			moderator_permissions=:moderator_permissions, 
			extra_user_group_ids=:extra_user_group_ids' );
		$stmt->bindParam( ':moderator_permissions', $moderator_permissions, PDO::PARAM_STR, strlen( $moderator_permissions ) );
		$stmt->bindParam( ':extra_user_group_ids', $extra_user_group_ids, PDO::PARAM_STR, strlen( $data['extra_user_group_ids'] ) );
		$stmt->execute();
		$stmt->closeCursor();

		if( ! empty( $data['extra_user_group_ids'] ) )
		{
			$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET in_groups=:in_groups WHERE userid=' . intval( $data['user_id'] ) );
			$stmt->bindParam( ':in_groups', $extra_user_group_ids, PDO::PARAM_STR, strlen( $data['extra_user_group_ids'] ) );
			$stmt->execute();
			$stmt->closeCursor();
		}

	}
	elseif( ! empty( $moderator ) )
	{
		$stmt = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_moderator SET 
			moderator_permissions=:moderator_permissions, 
			extra_user_group_ids=:extra_user_group_ids WHERE user_id=' . $data['user_id'] );
		$stmt->bindParam( ':moderator_permissions', $moderator_permissions, PDO::PARAM_STR, strlen( $moderator_permissions ) );
		$stmt->bindParam( ':extra_user_group_ids', $extra_user_group_ids, PDO::PARAM_STR, strlen( $extra_user_group_ids ) );
		$stmt->execute();
		$stmt->closeCursor();

		if( ! empty( $extra_user_group_ids ) )
		{
			$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET in_groups=:in_groups WHERE userid=' . intval( $data['user_id'] ) );
			$stmt->bindParam( ':in_groups', $extra_user_group_ids, PDO::PARAM_STR, strlen( $extra_user_group_ids ) );
			$stmt->execute();
			$stmt->closeCursor();
		}

	}
	/* END Add Moderator to User Groups */

	/* BEGIN Add  moderator content */
	$stmt = $db->prepare( 'SELECT moderator_content.*, user.username			
			FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content AS moderator_content
			INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator_content.user_id)		
			WHERE (moderator_content.content_type = \'node\') 
				AND (moderator_content.content_id = ' . intval( $data['content_id'] ) . ') 
				AND (moderator_content.user_id = ' . intval( $data['user_id'] ) . ')
			ORDER BY user.username' );
	$stmt->execute();

	$moderator_content = $stmt->fetch();

	$stmt->closeCursor();

	if( ! empty( $moderator_content_array ) && empty( $moderator_content ) )
	{
		$moderator_permissions = serialize( $moderator_content_array );
		$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_moderator_content SET 
			content_type=\'node\', 
			content_id=' . intval( $data['content_id'] ) . ', 
			user_id=' . intval( $data['user_id'] ) . ', 
			moderator_permissions=:moderator_permissions' );
		$stmt->bindParam( ':moderator_permissions', $moderator_permissions, PDO::PARAM_STR, strlen( $moderator_permissions ) );
		$stmt->execute();
		$stmt->closeCursor();
	}
	elseif( ! empty( $moderator_content ) )
	{
		$data['moderator_permissions'] = serialize( $moderator_content_array );
		$stmt = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_moderator_content SET moderator_permissions=:moderator_permissions WHERE moderator_id=' . $moderator_content['moderator_id'] );
		$stmt->bindParam( ':moderator_permissions', $data['moderator_permissions'], PDO::PARAM_STR, strlen( $data['moderator_permissions'] ) );
		$stmt->execute();
		$stmt->closeCursor();
	}
	/* END Add moderator content */
	$json = array();
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';
	
	
}

$xtpl->assign( 'DATA', $data );

$groups_list = nv_groups_list();

foreach( $groups_list as $group_id => $title )
{

	$xtpl->assign( 'GROUP', array(
		'group_id' => $group_id,
		'title' => $title,
		'checked' => in_array( $group_id, $data['extra_user_group_ids'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'adduser.group' );
}

$getAllPermissions = getAllPermissions();

$generalInterfaceGroups = array();

$generalInterfaceGroupIds = getGeneralModeratorInterfaceGroupIds();
foreach( $generalInterfaceGroupIds as $generalInterfaceGroupId )
{
	foreach( $getAllPermissions as $permission )
	{
		if( $generalInterfaceGroupId == $permission['interface_group_id'] )
		{
			$permission['title'] = $lang_module[$permission['permission_id']];
			$permission['checked'] = isset( $data['general_moderator_permissions'][$permission['permission_group_id']][$permission['permission_id']] ) ? 'checked="checked"' : '';

			$generalInterfaceGroups[$generalInterfaceGroupId][] = $permission;
		}

	}

} 
foreach( $generalInterfaceGroups['generalModeratorPermissions'] as $value )
{
	$xtpl->assign( 'ModeratorPermissions', $value );

	$xtpl->parse( 'adduser.general_moderator_permissions' );
}
foreach( $generalInterfaceGroups['generalModeratorPermissions'] as $value )
{
	$xtpl->assign( 'ModeratorPermissions', $value );

	$xtpl->parse( 'adduser.general_moderator_permissions' );
}

$xtpl->parse( 'adduser' );
$contents = $xtpl->text( 'adduser' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
