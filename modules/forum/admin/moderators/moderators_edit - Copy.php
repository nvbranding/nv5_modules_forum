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
$stmt = $db->prepare( '
		SELECT moderator_content.*, user.username 				
		FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content AS moderator_content
		INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator_content.userid)			
		WHERE (moderator_content.moderator_id = ' . intval( $mod ) . ')
		ORDER BY user.username' ); 

$stmt->execute();
$moderator_content = $stmt->fetch();
$stmt->closeCursor();

$moderator_permissions_content = !empty( $moderator_content['moderator_permissions'] ) ? unserialize( $moderator_content['moderator_permissions'] ) : array();
 //var_dump($moderator_permissions_content);

/* SELECT moderator  */
$stmt = $db->prepare( '
	SELECT moderator.*, user.username
	FROM ' . NV_FORUM_GLOBALTABLE . '_moderator AS moderator
	INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator.userid)
	WHERE moderator.userid = ' . intval( $moderator_content['userid'] ) );

$stmt->execute();

$moderator = $stmt->fetch();

$stmt->closeCursor();
$moderator_permissions = !empty( $moderator['moderator_permissions'] ) ? unserialize( $moderator['moderator_permissions'] ) : array();
 
$user_data = $db->query('SELECT user.* FROM '. NV_USERS_GLOBALTABLE .' AS user WHERE user.userid = ' . intval( $moderator_content['userid'] ) )->fetch();
 
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

	$data['moderator_permissions'] = $nv_Request->get_typed_array( 'moderator_permissions', 'post', array() );
 
	/* BEGIN update is_staff users*/

	$db->query( 'UPDATE '. NV_USERS_GLOBALTABLE .' SET is_staff=' . intval( $data['is_staff'] ) . ' WHERE userid=' . intval( $data['userid'] ) );

	/* END update is_staff users*/
	
	

	
	/* BEGIN Add is_super_moderator = 1 */
	$moderator_array = array();
	$moderator_content_array = array();
	if( $data['is_super_moderator'] == 1 )
	{
		if( ! empty( $data['general_moderator_permissions'] ) )
		{
			foreach( $data['general_moderator_permissions'] as $permission_group_id => $permissions )
			{
				foreach( $permissions as $permission_id => $ischeck )
				{
					if( $ischeck == 1 )
					{
						$moderator_array[$permission_group_id][$permission_id] = $ischeck;

					}
					if( $ischeck == 1 && ! isset( $permission_entry[$permission_group_id][$permission_id] ) )
					{

						$permission_value = 'allow';
						$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_entry SET 
							user_group_id= 0, 
							userid=' . intval( $data['userid'] ) . ', 
							permission_group_id=:permission_group_id, 
							permission_id=:permission_id, 
							permission_value=:permission_value, 
							permission_value_int=0' );
						$stmt->bindParam( ':permission_group_id', $permission_group_id, PDO::PARAM_STR );
						$stmt->bindParam( ':permission_id', $permission_id, PDO::PARAM_STR );
						$stmt->bindParam( ':permission_value', $permission_value, PDO::PARAM_STR );
						$stmt->execute();
						$stmt->closeCursor();

					}
					else
					{
						unset( $permission_entry[$permission_group_id][$permission_id] );
					}

				}

			}
		}
		if( ! empty( $data['moderator_permissions'] ) )
		{
			foreach( $data['moderator_permissions'] as $permission_group_id => $permissions )
			{
				foreach( $permissions as $permission_id => $ischeck )
				{

					if( $ischeck == 1 && $permission_group_id == 'forum' )
					{
						$moderator_array[$permission_group_id][$permission_id] = $ischeck;
					}

					if( $ischeck == 1 && $permission_group_id == 'forum' && ! isset( $permission_entry[$permission_group_id][$permission_id] ) )
					{
						$permission_value = 'allow';
						$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_entry SET 
								user_group_id= 0, 
								userid=' . intval( $data['userid'] ) . ', 
								permission_group_id=:permission_group_id, 
								permission_id=:permission_id, 
								permission_value=:permission_value, 
								permission_value_int=0' );
						$stmt->bindParam( ':permission_group_id', $permission_group_id, PDO::PARAM_STR );
						$stmt->bindParam( ':permission_id', $permission_id, PDO::PARAM_STR );
						$stmt->bindParam( ':permission_value', $permission_value, PDO::PARAM_STR );
						$stmt->execute();
						$stmt->closeCursor();
 
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
			foreach( $permission_entry as $permission_group_id => $_pms )
			{
				foreach( $_pms as $permission_id => $pms )
				{
					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry WHERE permission_entry_id=' . intval( $pms['permission_entry_id'] ) );

				}
			}
		}
	}
	/* END Add is_super_moderator = 1 */

	/* BEGIN Add is_super_moderator = 0 */
	if( $data['is_super_moderator'] == 0 )
	{
		if( ! empty( $data['general_moderator_permissions'] ) )
		{
			foreach( $data['general_moderator_permissions'] as $permission_group_id => $permissions )
			{
				foreach( $permissions as $permission_id => $ischeck )
				{
					if( $ischeck == 1 )
					{
						$moderator_array[$permission_group_id][$permission_id] = $ischeck ;

					}
					if( $ischeck == 1 && ! isset( $permission_entry[$permission_group_id][$permission_id] ) )
					{

						$permission_value = 'allow';
						$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_entry SET 
							user_group_id= 0, 
							userid=' . intval( $data['userid'] ) . ', 
							permission_group_id=:permission_group_id, 
							permission_id=:permission_id, 
							permission_value=:permission_value, 
							permission_value_int=0' );
						$stmt->bindParam( ':permission_group_id', $permission_group_id, PDO::PARAM_STR );
						$stmt->bindParam( ':permission_id', $permission_id, PDO::PARAM_STR );
						$stmt->bindParam( ':permission_value', $permission_value, PDO::PARAM_STR );
						$stmt->execute();
						$stmt->closeCursor();

					}
					else
					{
						unset( $permission_entry[$permission_group_id][$permission_id] );
					}

				}

			}
		}
		
		if( ! empty( $data['moderator_permissions'] ) )
		{
			foreach( $data['moderator_permissions'] as $permission_group_id => $permissions )
			{
				foreach( $permissions as $permission_id => $ischeck )
				{
					if( $ischeck == 1 && $permission_group_id == 'forum' )
					{
						$moderator_content_array[$permission_group_id][$permission_id] = $ischeck;

					}

					if( $ischeck == 1 && $permission_group_id == 'forum' && ! isset( $permission_entry_content[$permission_group_id][$permission_id] ) )
					{
 
						$permission_value = 'content_allow';
						$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content SET 
								user_group_id= 0, 
								userid=' . intval( $data['userid'] ) . ', 
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

					}
					else
					{
						unset( $permission_entry_content[$permission_group_id][$permission_id] );
					}
				}
			}
		}
		if( ! empty( $permission_entry_content ) )
		{
			foreach( $permission_entry_content as $permission_group_id => $_pms )
			{
				foreach( $_pms as $permission_id => $pms )
				{
					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content WHERE permission_entry_id=' . intval( $pms['permission_entry_id'] ) );
				}
			}
		}

	}
	/* END Add is_super_moderator = 0 */

	/* BEGIN Add Moderator to User Groups */
	$stmt = $db->prepare( 'SELECT moderator.*, user.username
			FROM ' . NV_FORUM_GLOBALTABLE . '_moderator AS moderator
			INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator.userid)
			WHERE moderator.userid = ' . intval( $data['userid'] ) );
	$stmt->execute();

	$moderator = $stmt->fetch();

	$stmt->closeCursor();

 
	
	$extra_user_group_ids = ! empty( $data['extra_user_group_ids'] ) ? implode( ',', $data['extra_user_group_ids'] ) : '';

	if( ! empty( $moderator_array ) && empty( $moderator ) )
	{

		$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_moderator SET 
			userid=' . intval( $data['userid'] ) . ', 
			is_super_moderator=' . intval( $data['is_super_moderator'] ) . ', 
			moderator_permissions=:moderator_permissions, 
			extra_user_group_ids=:extra_user_group_ids' );
		$stmt->bindParam( ':moderator_permissions', $moderator_array, PDO::PARAM_STR, strlen( $moderator_permissions ) );
		$stmt->bindParam( ':extra_user_group_ids', $extra_user_group_ids, PDO::PARAM_STR, strlen( $extra_user_group_ids ) );
		$stmt->execute();
		$stmt->closeCursor();

		if( ! empty( $data['extra_user_group_ids'] ) )
		{
			$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET secondary_group_ids=:secondary_group_ids WHERE userid=' . intval( $data['userid'] ) );
			$stmt->bindParam( ':secondary_group_ids', $extra_user_group_ids, PDO::PARAM_STR, strlen( $extra_user_group_ids ) );
			$stmt->execute();
			$stmt->closeCursor();
			
			$db->query('DELETE FROM ' . NV_USERS_GLOBALTABLE . '_group_relation WHERE userid=' . intval( $data['userid'] ) );
 
			foreach( $data['extra_user_group_ids'] as $group_id )
			{
				$is_primary = ( $group_id == $data['user_group_id'] ) ? 1 : 0;
				$group_insert_array[] = '('. intval( $data['userid'] ) .', '. intval( $group_id ) .', '. intval( $is_primary ) .')';
					
			}
			
			$db->query('
				INSERT INTO ' . NV_USERS_GLOBALTABLE . '_group_relation (userid, user_group_id, is_primary)
					VALUES ' . implode( ', ', $group_insert_array ) . ' 
					ON DUPLICATE KEY 
						UPDATE is_primary = VALUES(is_primary)');
		}

	}
	elseif( ! empty( $moderator ) )
	{
		$moderator_array = serialize( $moderator_array );
		$stmt = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_moderator SET 
			moderator_permissions=:moderator_permissions, 
			extra_user_group_ids=:extra_user_group_ids WHERE userid=' . $data['userid'] );
		$stmt->bindParam( ':moderator_permissions', $moderator_array, PDO::PARAM_STR, strlen( $moderator_permissions ) );
		$stmt->bindParam( ':extra_user_group_ids', $extra_user_group_ids, PDO::PARAM_STR, strlen( $extra_user_group_ids ) );
		$stmt->execute();
		$stmt->closeCursor();

		if( ! empty( $extra_user_group_ids ) )
		{
			$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET secondary_group_ids=:secondary_group_ids WHERE userid=' . intval( $data['userid'] ) );
			$stmt->bindParam( ':secondary_group_ids', $extra_user_group_ids, PDO::PARAM_STR, strlen( $extra_user_group_ids ) );
			$stmt->execute();
			$stmt->closeCursor();
			
			$db->query('DELETE FROM ' . NV_USERS_GLOBALTABLE . '_group_relation WHERE userid=' . intval( $data['userid'] ) );
 
			foreach( $data['extra_user_group_ids'] as $group_id )
			{
				$is_primary = ( $group_id == $data['user_group_id'] ) ? 1 : 0;
				$group_insert_array[] = '('. intval( $data['userid'] ) .', '. intval( $group_id ) .', '. intval( $is_primary ) .')';
					
			}
			
			$db->query('
				INSERT INTO ' . NV_USERS_GLOBALTABLE . '_group_relation (userid, user_group_id, is_primary)
					VALUES ' . implode( ', ', $group_insert_array ) . ' 
					ON DUPLICATE KEY 
						UPDATE is_primary = VALUES(is_primary)');
		}

	}
	/* END Add Moderator to User Groups */
 
	/* BEGIN Add  moderator content */
	$stmt = $db->prepare( 'SELECT moderator_content.*, user.username			
			FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content AS moderator_content
			INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator_content.userid)		
			WHERE (moderator_content.content_type = \'node\') 
				AND (moderator_content.content_id = ' . intval( $data['content_id'] ) . ') 
				AND (moderator_content.userid = ' . intval( $data['userid'] ) . ')
			ORDER BY user.username' );
	$stmt->execute();

	$moderator_content = $stmt->fetch();

	$stmt->closeCursor();
	$moderator_id = isset( $moderator_content['moderator_id'] ) ? $moderator_content['moderator_id'] : 0;
	if( ! empty( $moderator_content_array ) && empty( $moderator_content ) )
	{
 

		$moderator_content_array = serialize( $moderator_content_array );
		$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_moderator_content SET 
			content_type=\'node\', 
			content_id=' . intval( $data['content_id'] ) . ', 
			userid=' . intval( $data['userid'] ) . ', 
			moderator_permissions=:moderator_permissions' );
		$stmt->bindParam( ':moderator_permissions', $moderator_content_array, PDO::PARAM_STR, strlen( $moderator_permissions ) );
		$stmt->execute();
		$stmt->closeCursor();
		$moderator_id = $db->lastInsertId();
	}
	elseif( ! empty( $moderator_content ) )
	{
		$moderator_content_array = serialize( $moderator_content_array ); 
		$stmt = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_moderator_content SET moderator_permissions=:moderator_permissions WHERE moderator_id=' . $moderator_content['moderator_id'] );
		$stmt->bindParam( ':moderator_permissions', $moderator_content_array, PDO::PARAM_STR, strlen( $moderator_permissions ) );
		$stmt->execute();
		$stmt->closeCursor();
	}
	/* END Add moderator content */
	$json = array();
	if( $super == 0 )
	{
		$json['redirect'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators#_' . $moderator_id;
	}
	elseif( $super == 1 )
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
$xtpl->assign( 'TYPE', $type );
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


$moderator_permissions_array = array('generalModeratorPermissions', 'profilePostModeratorPermissions', 'conversationModeratorPermissions', 'forumModeratorPermissions');
$getAllPermissions = getAllPermissions();
$generalInterfaceGroups = array();
foreach( $moderator_permissions_array as $interface_group_id  )
{ 
	foreach( $getAllPermissions as $permission )
	{
		if( $interface_group_id == $permission['interface_group_id'] && in_array( $interface_group_id, $moderator_permissions_array ))
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
		$xtpl->assign( 'PERMISSION', $permission );
		$xtpl->parse( 'edituser.interface_group.permission' );
	}
	$xtpl->parse( 'edituser.interface_group' );
}
 
$data['extra_user_group_ids'] = ! empty( $moderator['extra_user_group_ids'] ) ? explode( ',', $moderator['extra_user_group_ids'] ) : array();

foreach( $groups_list as $group_id => $title )
{

	$xtpl->assign( 'GROUP', array(
		'group_id' => $group_id,
		'title' => $title,
		'checked' => in_array( $group_id, $data['extra_user_group_ids'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'edituser.group' );
}
 
$xtpl->assign( 'MOD_CONTENT', $moderator_content );
$xtpl->assign( 'IS_STAFF', ( $user_data['is_staff'] == 1) ? 'checked="checked"' : '' );

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
