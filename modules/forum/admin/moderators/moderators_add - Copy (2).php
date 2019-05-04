<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU(dlinhvan@gmail.com)
 * @Copyright (C) 2013 webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 27-04-2013 08:20
 */
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$error = '';
$data = array();
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

	$stmt = $db->prepare( 'SELECT userid, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE username=:username' );
	$stmt->bindParam( ':username', $data['username'], PDO::PARAM_STR );
	$stmt->execute();
	$userinfo = $stmt->fetch();

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

	$data['userid'] = $nv_Request->get_int( 'userid', 'post', 0 );
	$data['is_super_moderator'] = $nv_Request->get_int( 'is_super_moderator', 'post', 0 );
	$data['content_id'] = $nv_Request->get_int( 'content_id', 'post', 0 );
	$data['content_type'] = $nv_Request->get_title( 'content_type', 'post', '' );

	$data['is_staff'] = $nv_Request->get_int( 'is_staff', 'post', 0 );
	$data['extra_user_group_ids'] = $nv_Request->get_typed_array( 'extra_user_group_ids', 'post', 'int', array() );
	$data['general_moderator_permissions'] = $nv_Request->get_typed_array( 'general_moderator_permissions', 'post', array(), array() );

	$data['moderator_permissions'] = $nv_Request->get_typed_array( 'moderator_permissions', 'post', array(), array() );

	/* moderator_content */
	$result = $db->query( '
		SELECT moderator_content.*, user.username
		FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content AS moderator_content
		INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator_content.userid)
		WHERE (moderator_content.content_type = \'node\') 
			AND (moderator_content.content_id = ' . intval( $data['content_id'] ) . ') 
			AND (moderator_content.userid =' . intval( $data['userid'] ) . ')
			ORDER BY user.username' );

	if( ! $result->rowCount() )
	{

		$moderator_permissions = serialize( $data['moderator_permissions'] );

		//$db->query('INSERT INTO '. NV_FORUM_GLOBALTABLE .'_moderator_content (content_type, content_id, userid, moderator_permissions) VALUES ( \'node\', '. intval( $data['content_id'] ) .', '. intval( $data['userid'] ) .', '. $db->quote( $moderator_permissions ) .')');

	}
	/* moderator_content */

	/* moderator */
	$result = $db->query( '
		SELECT moderator.*, user.username
			FROM ' . NV_FORUM_GLOBALTABLE . '_moderator AS moderator
			INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator.userid)
			WHERE moderator.userid = ' . intval( $data['userid'] ) );

	if( ! $result->rowCount() )
	{

		$general_moderator_permissions = serialize( $data['general_moderator_permissions'] );

		$extra_user_group_ids = ! empty( $data['extra_user_group_ids'] ) ? implode( ',', $data['extra_user_group_ids'] ) : '';

		//$db->query('INSERT INTO '. NV_FORUM_GLOBALTABLE .'_moderator (userid, is_super_moderator, moderator_permissions, extra_user_group_ids) VALUES ( '. intval( $data['userid'] ) .', '. intval( $data['is_super_moderator'] ) .', '. $db->quote( $general_moderator_permissions ) .', '. $db->quote( $extra_user_group_ids ) .')');

	}
	/* moderator */

	/* users */
	$user_data = $db->query( '
		SELECT user.* ,
			   user_profile.*,
			   user_option.*,
			   user_privacy.*
		FROM ' . NV_USERS_GLOBALTABLE . ' AS USER
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON (user_profile.userid = USER.userid)
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_option AS user_option ON (user_option.userid = USER.userid)
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_privacy AS user_privacy ON (user_privacy.userid = USER.userid)
		WHERE USER.userid = ' . intval( $data['userid'] ) )->fetch();

	if( ! empty( $data['extra_user_group_ids'] ) )
	{
		$extra_user_group_ids = implode( ',', $data['extra_user_group_ids'] );

		$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET secondary_group_ids = ' . $db->quote( $extra_user_group_ids ) . '  WHERE userid = ' . intval( $data['userid'] ) );

		$db->query( 'DELETE FROM ' . NV_USERS_GLOBALTABLE . '_group_relation WHERE userid = ' . intval( $data['userid'] ) );

		foreach( $data['extra_user_group_ids'] as $group_id )
		{
			$is_primary = ( $group_id == $user_data['user_group_id'] ) ? 1 : 0;
			$group_insert_array[] = '(' . intval( $data['userid'] ) . ', ' . intval( $group_id ) . ', ' . intval( $is_primary ) . ')';

		}

		$db->query( '
			INSERT INTO ' . NV_USERS_GLOBALTABLE . '_group_relation (userid, user_group_id, is_primary)
				VALUES ' . implode( ', ', $group_insert_array ) . ' 
			ON DUPLICATE KEY 
			UPDATE is_primary = VALUES(is_primary)' );

	}

	$permission_combination = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id = ' . intval( $user_data['permission_combination_id'] ) )->fetch();

	$ischeck_permission_entry = $db->query( 'SELECT 1
				FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry
				WHERE userid = ' . intval( $user_data['userid'] ) . '
					AND permission_value <> \'unset\'
			 LIMIT 1' )->fetchColumn();

	$ischeck_permission_entry_content = $db->query( 'SELECT 1
				FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content
				WHERE userid = ' . intval( $user_data['userid'] ) . '
					AND permission_value <> \'unset\'
			 LIMIT 1' )->fetchColumn();

	$user_group_list = array_merge( $data['extra_user_group_ids'], array( $user_data['user_group_id'] ) );

	sort( $user_group_list ); // sort ASC

	$permission_combination_id = $db->query( 'SELECT permission_combination_id
				FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination
				WHERE userid = 0 AND user_group_list = ' . $db->quote( implode( ',', $user_group_list ) ) );
	if( empty( $permission_combination_id ) )
	{
		$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_combination (userid, user_group_list, cache_value) VALUES (' . intval( $user_data['userid'] ) . ', ' . $db->quote( implode( ',', $user_group_list ) ) . ', \'\')' );

		if( $permission_combination_id = $db->lastInsertId() )
		{

			foreach( $user_group_list as $group_id )
			{
				$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_combination_user_group (user_group_id, permission_combination_id) VALUES (' . $db->quote( $group_id ) . ', ' . intval( $permission_combination_id ) . ')' );
			}
			if( empty( $ischeck_permission_entry ) )
			{
				$result = $db->query( '
					SELECT entry.*, permission.permission_type
					FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry AS entry
					INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_permission AS permission ON
						(permission.permission_id = entry.permission_id
						AND permission.permission_group_id = entry.permission_group_id)
					WHERE entry.permission_value <> \'unset\'' );

				$permission_entry = array();
				while( $row = $result->fetch() )
				{
					$row['permission_value'] = ( $row['permission_value'] == 'use_int' ) ? $row['permission_value_int'] : $row['permission_value'];
					$permission_entry[$row['permission_group_id']][$row['permission_id']] = $row['permission_value'];
				}
				$result->closeCursor();

				if( ! empty( $permission_entry ) )
				{
					$finalCache = canonicalizePermissionCache( $permission_entry );

					$finalCache = serialize( $finalCache );

					$stmt = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_permission_combination SET cache_value=:cache_value WHERE permission_combination_id =' . intval( $permission_combination_id ) );
					$stmt->bindParam( 'cache_value', $finalCache, PDO::PARAM_STR );
					$stmt->execute();
					$stmt->closeCursor();

					$result = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_node ORDER BY parent_id, sort ASC' );

					while( $node = $result->fetch() )
					{

						if( $node['node_type_id'] == 'category' )
						{
							$cache_value = array( 'view' => 1 );

						}
						elseif( $node['node_type_id'] == 'forum' )
						{
							$cache_value = array_merge( $permission_entry['forum'], array( 'view' => 1 ) );
						}
						$cache_value = canonicalizePermissionCache( $cache_value );

						$cache_value = serialize( $cache_value );

						$node_insert_permission[] = '(' . intval( $permission_combination_id ) . ', \'node\', ' . intval( $node['node_id'] ) . ', ' . $db->quote( $cache_value ) . ')';
					}
					$result->closeCursor();

					$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content (
						permission_combination_id, 
						content_type, 
						content_id, 
						cache_value) VALUES ' . implode( ', ', $node_insert_permission ) . ' ON DUPLICATE KEY UPDATE cache_value = VALUES(cache_value)' );

				}

			}

			$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET permission_combination_id = ' . intval( $permission_combination_id ) . ' WHERE userid = ' . intval( $user_data['userid'] ) );
			$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id = ' . intval( $permission_combination['permission_combination_id'] ) );
			$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination_user_group WHERE permission_combination_id = ' . intval( $permission_combination['permission_combination_id'] ) );
			$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content WHERE permission_combination_id = ' . intval( $permission_combination['permission_combination_id'] ) );

		}
	}
	else
	{
		$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET permission_combination_id = ' . intval( $permission_combination_id ) . ' WHERE userid = ' . intval( $user_data['userid'] ) );
	}

	$group_id = $db->query( '
		SELECT group_id
		FROM ' . NV_GROUPS_GLOBALTABLE . '
		WHERE group_id IN ( ' . implode( ',', array_map( 'add_quotes', $user_group_list ) ) . ')
		ORDER BY display_style_priority DESC
		LIMIT 1' );

	$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET display_style_group_id = ' . intval( $group_id ) . ' WHERE userid=' . intval( $user_data['userid'] ) );

	/* users */

	/* insert permission_entry */
	if( ! empty( $data['general_moderator_permissions'] ) )
	{

		foreach( $data['general_moderator_permissions'] as $permission_group_id => $permission )
		{
			foreach( $permission as $permission_id => $value )
			{
				if( $value == '1' )
				{
					$db->query( '
						INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_entry SET 
							user_group_id=0, 
							userid=' . intval( $user_data['userid'] ) . ', 
							permission_group_id=' . $db->quote( $permission_group_id ) . ', 
							permission_id=' . $db->quote( $permission_id ) . ', 
							permission_value=\'allow\', 
							permission_value_int=0' );
				}

			}
		}
	}
	/* insert permission_entry */

	/////////////////////////
	$user_next = $db->query( '
		SELECT user.* 
		FROM ' . NV_USERS_GLOBALTABLE . ' AS user 
		WHERE user.userid = ' . intval( $user_data['userid'] ) )->fetch();

	$permission_combination = $db->query( '
		SELECT *
		FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination
		WHERE permission_combination_id = ' . intval( $user_next['permission_combination_id'] ) )->fetch();

	$ischeck_permission_entry = $db->query( '
		SELECT 1 FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry 
		WHERE userid = ' . intval( $user_data['userid'] ) . '  
			AND permission_value <> \'unset\'
		LIMIT 1' )->fetchColumn();

	$permission_combination_id = $db->query( 'SELECT permission_combination_id
				FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination
				WHERE userid = ' . intval( $user_data['userid'] ) . ' 
					AND user_group_list = ' . $db->quote( implode( $user_group_list ) ) );
	if( empty( $permission_combination_id ) )
	{
		$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_combination (userid, user_group_list, cache_value) VALUES (' . intval( $user_data['userid'] ) . ', ' . $db->quote( implode( ',', $user_group_list ) ) . ', \'\')' );

		if( $permission_combination_id = $db->lastInsertId() )
		{

			foreach( $user_group_list as $group_id )
			{
				$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_combination_user_group (user_group_id, permission_combination_id) VALUES (' . $db->quote( $group_id ) . ', ' . intval( $permission_combination_id ) . ')' );
			}

			$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET permission_combination_id = ' . intval( $permission_combination_id ) . ' WHERE userid = ' . intval( $user_data['userid'] ) );

			$result = $db->query( '
					SELECT entry.*, permission.permission_type
					FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry AS entry
					INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_permission AS permission ON
						(permission.permission_id = entry.permission_id
						AND permission.permission_group_id = entry.permission_group_id)
					WHERE entry.permission_value <> \'unset\'' );

			$permission_entry = array();
			while( $row = $result->fetch() )
			{
				$row['permission_value'] = ( $row['permission_value'] == 'use_int' ) ? $row['permission_value_int'] : $row['permission_value'];
				$permission_entry[$row['permission_group_id']][$row['permission_id']] = $row['permission_value'];
			}
			$result->closeCursor();

			if( ! empty( $permission_entry ) )
			{
				$finalCache = canonicalizePermissionCache( $permission_entry );

				$finalCache = serialize( $finalCache );

				$stmt = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_permission_combination SET cache_value=:cache_value WHERE permission_combination_id =' . intval( $permission_combination_id ) );
				$stmt->bindParam( 'cache_value', $finalCache, PDO::PARAM_STR );
				$stmt->execute();
				$stmt->closeCursor();

				$result = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_node ORDER BY parent_id, sort ASC' );

				while( $node = $result->fetch() )
				{

					if( $node['node_type_id'] == 'category' )
					{
						$cache_value = array( 'view' => 1 );

					}
					elseif( $node['node_type_id'] == 'forum' )
					{
						$cache_value = array_merge( $permission_entry['forum'], array( 'view' => 1 ) );
					}
					$cache_value = canonicalizePermissionCache( $cache_value );

					$cache_value = serialize( $cache_value );

					$node_insert_permission[] = '(' . intval( $permission_combination_id ) . ', \'node\', ' . intval( $node['node_id'] ) . ', ' . $db->quote( $cache_value ) . ')';
				}
				$result->closeCursor();

				$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content (
						permission_combination_id, 
						content_type, 
						content_id, 
						cache_value) VALUES ' . implode( ', ', $node_insert_permission ) . ' ON DUPLICATE KEY UPDATE cache_value = VALUES(cache_value)' );

			}

		}
	}
	//////////////////

	//////////////////
	$user_data = $db->query( '
		SELECT user.* ,
			   user_profile.*,
			   user_option.*,
			   user_privacy.*
		FROM ' . NV_USERS_GLOBALTABLE . ' AS USER
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON (user_profile.userid = USER.userid)
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_option AS user_option ON (user_option.userid = USER.userid)
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_privacy AS user_privacy ON (user_privacy.userid = USER.userid)
		WHERE USER.userid = ' . intval( $data['userid'] ) )->fetch();

	$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET is_moderator = 1, is_staff = ' . intval( $data['is_staff'] ) . ' WHERE userid=' . $user_data['userid'] );

	if( ! empty( $data['general_moderator_permissions'] ) )
	{
		$result = $db->query( 'SELECT *
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content
			WHERE content_type = \'node\' AND content_id = ' . intval( $data['content_id'] ) . '
				AND user_group_id = \'0\' AND userid = ' . $user_data['userid'] );
		if( ! $result->rowCount() )
		{
			foreach( $data['general_moderator_permissions']['forum'] as $permission )
			{
				foreach( $permission as $permission_id => $value )
				{
					if( $value == '1' )
					{
						$db->query( '
							INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content (user_group_id, userid, content_type, content_id, permission_group_id, permission_id, permission_value, permission_value_int) 
							VALUES (
								\'0\', 
								' . intval( $user_data['userid'] ) . ', 
								\'node\', ' . intval( $data['content_id'] ) . ', 
								\'forum\', ' . $db->quote( $permission_id ) . ', 
								\'content_allow\', \'0\')' );

					}

				}

			}

		}

	}
	/////////////////////////////

	$user_next = $db->query( 'SELECT user.* FROM ' . NV_USERS_GLOBALTABLE . ' AS user WHERE user.userid = ' . $data['userid'] )->fetch();

	$permission_combination = $db->query( 'SELECT *
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination
			WHERE permission_combination_id = ' . intval( $user_next['permission_combination_id'] ) )->fetch();

	$ischeck_permission_entry = $db->query( 'SELECT 1
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry
			WHERE userid = ' . intval( $user_next['userid'] ) . '
				AND permission_value <> \'unset\'
			LIMIT 1' );

	$permission_combination_id = $db->query( 'SELECT permission_combination_id
				FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination
				WHERE userid = ' . intval( $user_next['userid'] ) . ' AND user_group_list = ' . $db->quote( implode( ',', $user_group_list ) ) )->fetch();

	$permission_combination_byuser = $db->query( 'SELECT *
				FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination
				WHERE userid = ' . intval( $user_next['userid'] ) )->fetch();

	$result = $db->query( 'SELECT entry.*, permission.permission_type
				FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry AS entry
				INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_permission AS permission ON
					(permission.permission_id = entry.permission_id
					AND permission.permission_group_id = entry.permission_group_id)' );

	$permission_entry = array();
	while( $row = $result->fetch() )
	{
		$row['permission_value'] = ( $row['permission_value'] == 'use_int' ) ? $row['permission_value_int'] : $row['permission_value'];
		$permission_entry[$row['permission_group_id']][$row['permission_id']] = $row['permission_value'];
	}
	$result->closeCursor();

	if( ! empty( $permission_entry ) )
	{
		$finalCache = canonicalizePermissionCache( $permission_entry );

		$finalCache = serialize( $finalCache );

		$stmt = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_permission_combination SET cache_value=:cache_value WHERE permission_combination_id =' . intval( $permission_combination_id ) );
		$stmt->bindParam( 'cache_value', $finalCache, PDO::PARAM_STR );
		$stmt->execute();
		$stmt->closeCursor();

		$node_array = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_node ORDER BY parent_id, sort ASC' )->fetch();

		$result = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_node ORDER BY parent_id, sort ASC' );

		while( $node = $result->fetch() )
		{

			if( $node['node_type_id'] == 'category' )
			{
				$cache_value = array( 'view' => 1 );

			}
			elseif( $node['node_type_id'] == 'forum' )
			{
				$cache_value = array_merge( $permission_entry['forum'], array( 'view' => 1 ) );
			}
			$cache_value = canonicalizePermissionCache( $cache_value );

			$cache_value = serialize( $cache_value );

			$node_insert_permission[] = '(' . intval( $permission_combination_id ) . ', \'node\', ' . intval( $node['node_id'] ) . ', ' . $db->quote( $cache_value ) . ')';
		}
		$result->closeCursor();

		$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content (
						permission_combination_id, 
						content_type, 
						content_id, 
						cache_value) VALUES ' . implode( ', ', $node_insert_permission ) . ' ON DUPLICATE KEY UPDATE cache_value = VALUES(cache_value)' );

	}

	die( 'ok' );

	/* SELECT permission_entry  */
	$stmt = $db->prepare( '
		SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry 
		WHERE user_group_id = 0 
		AND userid = ' . intval( $data['userid'] ) );

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
		AND userid = ' . intval( $data['userid'] ) );

	$stmt->execute();
	$permission_entry_content = array();
	while( $rows = $stmt->fetch() )
	{
		$permission_entry_content[$rows['permission_group_id']][$rows['permission_id']] = $rows;
	}
	$stmt->closeCursor();

	/* BEGIN update is_staff users*/

	$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET is_staff=' . intval( $data['is_staff'] ) . ' WHERE userid=' . intval( $data['userid'] ) );
	$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET is_moderator=1 WHERE userid=' . intval( $data['userid'] ) );

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
						$stmt->closeCursor();
					}
					else
					{
						$permission_entry[$permission_group_id][$permission_id];
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
		$moderator_array = serialize( $moderator_array );
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

			$db->query( 'DELETE FROM ' . NV_USERS_GLOBALTABLE . '_group_relation WHERE userid=' . intval( $data['userid'] ) );

			foreach( $data['extra_user_group_ids'] as $group_id )
			{
				$is_primary = ( $group_id == $data['user_group_id'] ) ? 1 : 0;
				$group_insert_array[] = '(' . intval( $data['userid'] ) . ', ' . intval( $group_id ) . ', ' . intval( $is_primary ) . ')';

			}

			$db->query( '
				INSERT INTO ' . NV_USERS_GLOBALTABLE . '_group_relation (userid, user_group_id, is_primary)
					VALUES ' . implode( ', ', $group_insert_array ) . ' 
					ON DUPLICATE KEY 
						UPDATE is_primary = VALUES(is_primary)' );

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

			$db->query( 'DELETE FROM ' . NV_USERS_GLOBALTABLE . '_group_relation WHERE userid=' . intval( $data['userid'] ) );

			foreach( $data['extra_user_group_ids'] as $group_id )
			{
				$is_primary = ( $group_id == $data['user_group_id'] ) ? 1 : 0;
				$group_insert_array[] = '(' . intval( $data['userid'] ) . ', ' . intval( $group_id ) . ', ' . intval( $is_primary ) . ')';

			}

			$db->query( '
				INSERT INTO ' . NV_USERS_GLOBALTABLE . '_group_relation (userid, user_group_id, is_primary)
					VALUES ' . implode( ', ', $group_insert_array ) . ' 
					ON DUPLICATE KEY 
						UPDATE is_primary = VALUES(is_primary)' );

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

$xtpl->assign( 'USER', $userinfo );
$xtpl->assign( 'DATA', $data );

$stmt = $db->prepare( 'SELECT moderator_content.*, user.username			
			FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content AS moderator_content
			INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator_content.userid)		
			WHERE (moderator_content.content_type = \'node\') 
				AND (moderator_content.content_id = ' . intval( $data['node_id'] ) . ') 
				AND (moderator_content.userid = ' . intval( $userinfo['userid'] ) . ')
			ORDER BY user.username' );
$stmt->execute();

$moderator_content = $stmt->fetch();

$stmt->closeCursor();

/* SELECT permission_entry  */
$stmt = $db->prepare( '
	SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry 
	WHERE user_group_id = 0 
	AND userid = ' . intval( $userinfo['userid'] ) );

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
		AND content_id = ' . intval( $data['node_id'] ) . ' 
		AND user_group_id = 0 
		AND userid = ' . intval( $userinfo['userid'] ) );
$stmt->execute();
$permission_entry_content = array();
while( $rows = $stmt->fetch() )
{
	$permission_entry_content[$rows['permission_group_id']][$rows['permission_id']] = $rows;
}
$stmt->closeCursor();

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
			$permission['checked'] = isset( $permission_entry[$permission['permission_group_id']][$permission['permission_id']] ) ? 'checked="checked"' : '';

			$generalInterfaceGroups[$generalInterfaceGroupId['interface_group_id']][] = $permission;
		}

	}

}

/* SELECT moderator  */
$stmt = $db->prepare( '
	SELECT moderator.*, user.username
	FROM ' . NV_FORUM_GLOBALTABLE . '_moderator AS moderator
	INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator.userid)
	WHERE moderator.userid = ' . intval( $userinfo['userid'] ) );

$stmt->execute();

$moderator = $stmt->fetch();

$stmt->closeCursor();

$data['extra_user_group_ids'] = ! empty( $moderator['extra_user_group_ids'] ) ? explode( ',', $moderator['extra_user_group_ids'] ) : array();

foreach( $groups_list as $group_id => $title )
{

	$xtpl->assign( 'GROUP', array(
		'group_id' => $group_id,
		'title' => $title,
		'checked' => in_array( $group_id, $data['extra_user_group_ids'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'adduser.group' );
}

foreach( $generalInterfaceGroups['generalModeratorPermissions'] as $value )
{
	$xtpl->assign( 'GENERRAL', $value );

	$xtpl->parse( 'adduser.general' );
}
foreach( $generalInterfaceGroups['profilePostModeratorPermissions'] as $value )
{
	$xtpl->assign( 'PROFILEPOST', $value );

	$xtpl->parse( 'adduser.profilePost' );
}

foreach( $generalInterfaceGroups['conversationModeratorPermissions'] as $value )
{
	$xtpl->assign( 'CONVERSATION', $value );

	$xtpl->parse( 'adduser.conversation' );
}

foreach( $generalInterfaceGroups['forumModeratorPermissions'] as $value )
{
	if( $data['is_super'] == 0 )
	{
		$moderator_permissions = isset( $permission_entry_content[$value['permission_group_id']][$value['permission_id']] ) ? $value['permission_id'] : '';
	}
	elseif( $data['is_super'] == 1 )
	{
		$moderator_permissions = isset( $permission_entry[$value['permission_group_id']][$value['permission_id']] ) ? $value['permission_id'] : '';
	}

	$value['checked'] = ( $moderator_permissions == $value['permission_id'] ) ? 'checked="checked"' : '';

	$xtpl->assign( 'FORUM', $value );

	$xtpl->parse( 'adduser.forum' );
}

$xtpl->assign( 'IS_STAFF', ( $data['is_staff'] == 1 ) ? 'checked="checked"' : '' );

$xtpl->parse( 'adduser' );
$contents = $xtpl->text( 'adduser' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
