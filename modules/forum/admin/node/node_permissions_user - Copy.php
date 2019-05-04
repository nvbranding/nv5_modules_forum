<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU(dlinhvan@gmail.com)
 * @Copyright (C) 2013 webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 27-04-2013 08:20
 */
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

// $data['node_id'] = $nv_Request->get_int( 'node_id', 'get', 0 );
// $data['group_id'] = $nv_Request->get_int( 'group_id', 'get', 0 );
$data['userid'] = $nv_Request->get_int( 'userid', 'get', 0 );
$data['username'] = $nv_Request->get_title( 'username', 'post', '' );

$where = '';
if( !empty( $data['userid'] ) )
{
	$where = 'userid=' . $data['userid'];
}
else{
	$where = 'username=:username';
}

$stmt = $db->prepare( 'SELECT userid, username FROM ' . NV_USERS_GLOBALTABLE . ' WHERE ' . $where );
$stmt->bindParam( ':username', $data['username'], PDO::PARAM_STR );
$stmt->execute();
$user = $stmt->fetch();

 
if( empty( $user ) )
{ 
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node-permissions' );
	die();
} 
elseif( !empty( $user ) && $nv_Request->get_int('add', 'post', 0 ) ) 
{ 
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node-permissions&action=user&userid=' . $user['userid']. '&node_id=' . $data['node_id'] );
	die();
}
 
if( $nv_Request->get_int( 'save', 'post' ) == 1 && $nv_Request->get_title( 'token', 'post', '' ) == md5( session_id() . $global_config['sitekey'] . $user['userid'] ) )
{
	$data['permission_value_int'] = 0;
	$data['content_type'] = 'node';
	$permissions_array = $nv_Request->get_typed_array( 'permissions', 'post', array() );

	$result = $db->query( 'SELECT *
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content
			WHERE content_type = \'node\' AND content_id = ' . $data['node_id'] . '
				AND user_group_id = 0 AND userid = ' . $user['userid'] );

	if( $result->rowCount() > 0 )
	{
		while( $rows = $result->fetch() )
		{
 
			$permission_value = isset( $permissions_array[$rows['permission_group_id']][$rows['permission_id']] ) ? (string)$permissions_array[$rows['permission_group_id']][$rows['permission_id']] : '0';
 
			if( $permission_value === 'unset' || $permission_value === '0' ) 
			{
	 
				$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content WHERE permission_entry_id=' . intval( $rows['permission_entry_id'] ) );

				unset( $permissions_array[$rows['permission_group_id']][$rows['permission_id']] );

			}
			elseif( ! empty( $permission_value ) && $permission_value != 'unset' )
			{
				try
				{
					if( is_numeric( $permission_value ) )
					{
						$permission_value_int = $permission_value;
						$permission_value = 'use_int';
					}else{
						$permission_value_int = 0;
					}
					$stmt = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content SET 
						permission_value=:permission_value, 
						permission_value_int=' . intval( $permission_value_int ) . ' 
						WHERE permission_entry_id=' . intval( $rows['permission_entry_id'] ) );

					$stmt->bindParam( ':permission_value', $permission_value, PDO::PARAM_STR );
					$stmt->execute();
				}
				catch ( PDOException $e )
				{
					trigger_error( $e->getMessage() );
				}

				unset( $permissions_array[$rows['permission_group_id']][$rows['permission_id']] );

			}

		}
	}

	if( ! empty( $permissions_array ) )
	{
		foreach( $permissions_array as $permission_group_id => $_permission )
		{
			foreach( $_permission as $permission_id => $permission_value )
			{

				if( $permission_value != 'unset' && ! empty( $permission_value ) )
				{

					if( is_numeric( $permission_value ) )
					{
						$permission_value_int = $permission_value;
						$permission_value = 'use_int';
					}else{
						$permission_value_int = 0;
					}
 
					$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content SET 
						content_type=:content_type,
						content_id = ' . intval( $data['node_id'] ) . ', 
						user_group_id=0, 
						userid= ' . $user['userid'] . ', 
						permission_group_id=:permission_group_id, 
						permission_id=:permission_id, 
						permission_value=:permission_value, 
						permission_value_int=' . intval( $permission_value_int ) );

					$stmt->bindParam( ':content_type', $data['content_type'], PDO::PARAM_STR );
					$stmt->bindParam( ':permission_group_id', $permission_group_id, PDO::PARAM_STR );
					$stmt->bindParam( ':permission_id', $permission_id, PDO::PARAM_STR );
					$stmt->bindParam( ':permission_value', $permission_value, PDO::PARAM_STR );
					$stmt->execute();
					$stmt->closeCursor();
				}

			}
		}

	}
 
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node-permissions&node_id=' . $data['node_id'] );
	die();
}
 
$xtpl = new XTemplate( 'node_permissions_user.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/node' );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'THEME', $global_config['site_theme'] );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'CAPTION', $caption );
$xtpl->assign( 'DATA', $data );
$xtpl->assign( 'TOKEN', md5( session_id() . $global_config['sitekey'] . $user['userid'] ) );
$xtpl->assign( 'NODE_TITLE', $forum_node[$data['node_id']]['title'] );
$xtpl->assign( 'USER_TITLE', $user['username'] );
$xtpl->assign( 'ACTION', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=node-permissions&action=user&node_id=" . $data['node_id'] . "&userid=" . $user['userid'] );

$stmt = $db->prepare( 'SELECT permission.*,
		entry_content.permission_value, entry_content.permission_value_int,
			COALESCE(entry_content.permission_value, \'unset\') AS value,
			COALESCE(entry_content.permission_value_int, 0) AS value_int
		FROM ' . NV_FORUM_GLOBALTABLE . '_permission AS permission
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content AS entry_content ON
			(entry_content.permission_id = permission.permission_id
			AND entry_content.permission_group_id = permission.permission_group_id
			AND entry_content.content_type = \'node\'
			AND entry_content.content_id = '. intval( $data['node_id'] ).'
			AND entry_content.user_group_id = 0
			AND entry_content.userid = '. $user['userid'] .')
		WHERE permission.permission_group_id IN (\'category\', \'forum\', \'linkForum\', \'page\')
		ORDER BY permission.display_order' );

$stmt->execute();
$node_permissions_group = array();

while( $row = $stmt->fetch() )
{
	$node_permissions_group[$row['permission_id']] = $row;
}
$stmt->closeCursor();

 
foreach( $array_permissions_value as $permission_value )
{
	$xtpl->assign( 'viewOthers', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['viewOthers']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.viewOthers' );
	
	$xtpl->assign( 'viewContent', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['viewContent']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.viewContent' );
	
	$xtpl->assign( 'like', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['like']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.like' );
 	
	$xtpl->assign( 'postThread', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['postThread']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.postThread' );
 
	$xtpl->assign( 'postReply', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['postReply']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.postReply' );
 
	$xtpl->assign( 'deleteOwnPost', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['deleteOwnPost']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.deleteOwnPost' );
 
	$xtpl->assign( 'editOwnPost', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['editOwnPost']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.editOwnPost' );
 
	$xtpl->assign( 'editOwnThreadTitle', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['editOwnThreadTitle']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.editOwnThreadTitle' );
 
	$xtpl->assign( 'deleteOwnThread', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['deleteOwnThread']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.deleteOwnThread' );
 
	$xtpl->assign( 'viewAttachment', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['viewAttachment']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.viewAttachment' );
 
	$xtpl->assign( 'uploadAttachment', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['uploadAttachment']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.uploadAttachment' );
	
	$xtpl->assign( 'votePoll', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['votePoll']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.votePoll' );
	
	$xtpl->assign( 'stickUnstickThread', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['stickUnstickThread']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.stickUnstickThread' );
 
	$xtpl->assign( 'lockUnlockThread', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['lockUnlockThread']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.lockUnlockThread' );
	
	$xtpl->assign( 'manageAnyThread', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['manageAnyThread']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.manageAnyThread' );
	
	$xtpl->assign( 'deleteAnyThread', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['deleteAnyThread']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.deleteAnyThread' );
	
	$xtpl->assign( 'hardDeleteAnyThread', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['hardDeleteAnyThread']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.hardDeleteAnyThread' );
	
	$xtpl->assign( 'threadReplyBan', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['threadReplyBan']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.threadReplyBan' );
	
	$xtpl->assign( 'editAnyPost', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['editAnyPost']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.editAnyPost' );
	
	$xtpl->assign( 'deleteAnyPost', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['deleteAnyPost']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.deleteAnyPost' );
 
	$xtpl->assign( 'hardDeleteAnyPost', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['hardDeleteAnyPost']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.hardDeleteAnyPost' );
 
	$xtpl->assign( 'warn', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['warn']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.warn' );
 
	$xtpl->assign( 'viewDeleted', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['viewDeleted']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.viewDeleted' );
 
	$xtpl->assign( 'viewModerated', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['viewModerated']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.viewModerated' );
 
	$xtpl->assign( 'undelete', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['undelete']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.undelete' );
 
	$xtpl->assign( 'approveUnapprove', array( 'key' => $permission_value, 'checked' => ( $permission_value == $node_permissions_group['approveUnapprove']['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.approveUnapprove' );
 
}
 
if( isset( $node_permissions_group['editOwnPostTimeLimit'] ) && $node_permissions_group['editOwnPostTimeLimit']['value_int'] != '0' )
{
	$xtpl->assign( 'editOwnPostTimeLimit_value', $node_permissions_group['editOwnPostTimeLimit']['value_int'] );
	$xtpl->assign( 'editOwnPostTimeLimit0', '' );
	$xtpl->assign( 'editOwnPostTimeLimit_unlimited', ( $node_permissions_group['editOwnPostTimeLimit']['value_int'] == '-1' ) ? 'checked="checked"' : '' );
	$xtpl->assign( 'editOwnPostTimeLimit1', ( $node_permissions_group['editOwnPostTimeLimit']['value_int'] > 0 ) ? 'checked="checked"' : '' );
	$xtpl->assign( 'spinDisabled', ( $node_permissions_group['editOwnPostTimeLimit']['value_int'] < 1 ) ? 'disabled="disabled"' : '' );

}
else{
	$xtpl->assign( 'editOwnPostTimeLimit_value', 0 );
	$xtpl->assign( 'editOwnPostTimeLimit0',  'checked="checked"' );// not set
	$xtpl->assign( 'editOwnPostTimeLimit_unlimited', '' ); // unlimited
	$xtpl->assign( 'editOwnPostTimeLimit1', '' );// set value
	$xtpl->assign( 'spinDisabled', 'disabled="disabled"' );

}

$stmt = $db->prepare( 'SELECT permission.*,
		entry_content.permission_value, entry_content.permission_value_int,
		COALESCE(entry_content.permission_value, \'unset\') AS value,
		COALESCE(entry_content.permission_value_int, 0) AS value_int
	FROM ' . NV_FORUM_GLOBALTABLE . '_permission AS permission
	LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content AS entry_content ON
		(entry_content.permission_id = permission.permission_id
		AND entry_content.permission_group_id = permission.permission_group_id
		AND entry_content.content_type = \'node\'
		AND entry_content.content_id = ' . intval( $data['node_id'] ) . ' 
		AND entry_content.user_group_id = 0 
		AND entry_content.userid = ' . intval( $user['userid'] ) . ')
	WHERE permission.permission_group_id = \'general\'
		AND permission.permission_id = \'viewNode\'' );

$stmt->execute();
$view_node = $stmt->fetch();
$stmt->closeCursor();
 
foreach( $array_permissions_value as $permission_value )
{
	$xtpl->assign( 'VIEWNOTE', array( 'key' => $permission_value, 'checked' => ( $permission_value == $view_node['value'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.view_node' );
}
 


$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
