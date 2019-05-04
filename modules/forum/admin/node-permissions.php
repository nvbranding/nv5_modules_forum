<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU(dlinhvan@gmail.com)
 * @Copyright (C) 2013 webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 27-04-2013 08:20
 */
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );
 
$groups_list = nv_groups_list(); 
 
$data['node_id'] = $nv_Request->get_int( 'node_id', 'get', 0 ); 
$data['group_id'] = $nv_Request->get_int( 'group_id', 'get', 0 ); 
 
if( empty( $data['node_id'] ) )
{
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node' );
	die();
}

if( in_array( ACTION_METHOD, array( 'group', 'user' )  )  )
{
	require_once NV_ROOTDIR . '/modules/' . $module_file . '/admin/node/node_permissions_' . ACTION_METHOD . '.php';
} 

$revoke = $db->query( 'SELECT COUNT( * ) FROM '. NV_FORUM_GLOBALTABLE .'_permission_entry_content WHERE content_id=' . $data['node_id'] . ' AND permission_id=\'viewNode\' AND content_type=\'node\'')->fetchColumn();
 
if( ACTION_METHOD == 'node-revoke' )
{
	$data['revoke'] = $nv_Request->get_int( 'revoke', 'post', 0 ); 
	$data['content_type'] = 'node';
	$data['permission_group_id'] = 'general';
	$data['permission_id'] = 'viewNode';
	$data['permission_value'] = 'reset'; //unset | reset | content_allow | deny | use_int
	$data['permission_value_int'] = 0; 
	if( $data['revoke'] == 1 && intval( $revoke ) == 0 )
	{
		$stmt = $db->prepare( 'INSERT INTO '. NV_FORUM_GLOBALTABLE .'_permission_entry_content SET 
			content_type=:content_type,
			content_id = ' . intval( $data['node_id'] ) . ', 
			user_group_id=0, 
			userid=0, 
			permission_group_id=:permission_group_id, 
			permission_id=:permission_id, 
			permission_value=:permission_value, 
			permission_value_int=' . intval( $data['permission_value_int'] ) );
		$stmt->bindParam( ':content_type', $data['content_type'], PDO::PARAM_STR );
		$stmt->bindParam( ':permission_group_id', $data['permission_group_id'], PDO::PARAM_STR );
		$stmt->bindParam( ':permission_id', $data['permission_id'], PDO::PARAM_STR );
		$stmt->bindParam( ':permission_value', $data['permission_value'], PDO::PARAM_STR );
		$stmt->execute();
	}else{
		
		$stmt = $db->prepare( 'DELETE FROM '. NV_FORUM_GLOBALTABLE .'_permission_entry_content WHERE 
		content_id=' . intval( $data['node_id'] ) . ' 
		AND permission_group_id=:permission_group_id 
		AND permission_id=:permission_id
		AND content_type=:content_type' );
		$stmt->bindParam( ':content_type', $data['content_type'], PDO::PARAM_STR );
		$stmt->bindParam( ':permission_group_id', $data['permission_group_id'], PDO::PARAM_STR );
		$stmt->bindParam( ':permission_id', $data['permission_id'], PDO::PARAM_STR );
		$stmt->execute();
	}
	
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&node_id=' . $data['node_id'] );
	die();	
} 


$data['revoke'] = ( $revoke > 0 ) ? 'checked="checked"': '';

$xtpl = new XTemplate( 'node_permissions.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/node' );
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
$xtpl->assign( 'DATA', $data );
$xtpl->assign( 'NODE_TITLE', $forum_node[$data['node_id']]['title'] );
$xtpl->assign( 'JSON_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=json' );
$xtpl->assign( 'ACTION_USER', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node-permissions&action=user&node_id=' . $data['node_id']);
$xtpl->assign( 'ACTION_REVOKE', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node-permissions&action=node-revoke&node_id=' . $data['node_id'] );


$stmt = $db->prepare('SELECT DISTINCT entry.content_id, entry.user_group_id, entry.userid
	FROM '. NV_FORUM_GLOBALTABLE .'_permission_entry_content entry
	INNER JOIN '. NV_FORUM_GLOBALTABLE .'_permission permission ON (permission.permission_group_id = entry.permission_group_id AND permission.permission_id = entry.permission_id)
	LEFT JOIN '. NV_USERS_GLOBALTABLE .' USER ON (USER.userid = entry.userid AND entry.userid > 0)
	LEFT JOIN '. NV_GROUPS_GLOBALTABLE .' user_group ON (user_group.group_id = entry.user_group_id AND entry.user_group_id > 0)
	WHERE entry.content_type = \'node\' AND ( USER.userid IS NOT NULL OR user_group.group_id IS NOT NULL OR (entry.userid = 0 AND entry.user_group_id = 0) )');
$stmt->execute();
$array_node_permissions = array();
$array_node_permissions_groups = array();
$array_node_permissions_users = array();
while( $rows = $stmt->fetch() )
{
	$array_node_permissions[] = $rows;
	$array_node_permissions_groups[] = $rows['user_group_id'];
	$array_node_permissions_users[] = $rows['userid'];
}
$stmt->closeCursor();
 
 
if( !empty( $groups_list ) )
{
	foreach( $groups_list as $group_id => $title )
	{
		
		$group_link = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=groups&id=' . $group_id;
		
		$permissions_link = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node-permissions&action=group&node_id=' . $data['node_id'] . '&group_id=' . $group_id;
		$hasPermissions = in_array( $group_id, $array_node_permissions_groups ) ? 'class="hasPermissions"' : '';
		$xtpl->assign( 'GROUP', array(  'group_id'=> $group_id, 'title'=> $title, 'group_link'=> $group_link, 'permissions_link'=> $permissions_link, 'class'=> $hasPermissions  ) );
		$xtpl->parse( 'main.group' );
	}
}

$stmt = $db->prepare('SELECT USER.*
  FROM '. NV_FORUM_GLOBALTABLE .'_permission_entry_content permission_entry_content
  INNER JOIN '.  NV_USERS_GLOBALTABLE .' USER ON (USER.userid = permission_entry_content.userid)
  INNER JOIN '. NV_FORUM_GLOBALTABLE .'_permission permission ON (permission.permission_group_id = permission_entry_content.permission_group_id AND permission.permission_id = permission_entry_content.permission_id) WHERE permission_entry_content.content_type =\'node\'
  AND permission_entry_content.content_id = '. intval( $data['node_id'] ) .'
  AND permission_entry_content.user_group_id = 0
  AND permission_entry_content.userid > 0
GROUP BY permission_entry_content.userid
ORDER BY USER.username');
$stmt->execute();
if( $stmt->rowCount() )
{
	while( $user = $stmt->fetch() )
	{
		$user['link']= NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&userid=' . $user['userid'];	
		$user['permissions_link'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node-permissions&action=user&node_id=' . $data['node_id'] . '&userid=' . $user['userid'];
		$user['class'] = in_array( $group_id, $array_node_permissions_groups ) ? 'class="hasPermissions"' : '';
		if ( file_exists( NV_ROOTDIR . '/' . $user['photo'] ) and ! empty( $user['photo'] ) )
        {
            $user['avatar'] = NV_BASE_SITEURL . $user['photo'];
        }
        else
        {
            $user['avatar'] = NV_BASE_SITEURL . 'themes/' . $global_config['site_theme'] . '/images/users/no_avatar.png';
        }
		$xtpl->assign( 'USER', $user );
		$xtpl->parse( 'main.user.loop' );
	}
	$xtpl->parse( 'main.user' );
}

$stmt->closeCursor();
 
$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
