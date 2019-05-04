<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU(dlinhvan@gmail.com)
 * @Copyright (C) 2013 webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 27-04-2013 08:20
 */
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

 
require_once NV_ROOTDIR . '/includes/forum/model/node.php';
require_once NV_ROOTDIR . '/includes/forum/model/users.php';
require_once NV_ROOTDIR . '/includes/forum/model/permission.php';
require_once NV_ROOTDIR . '/includes/forum/model/moderator.php';
	
if( in_array( ACTION_METHOD, array( 'add', 'edit', 'delete' ) ) )
{
	require_once NV_ROOTDIR . '/modules/' . $module_file . '/admin/moderators/moderators_' . ACTION_METHOD . '.php';
}


//$admin_info['is_moderator'] == 1
 
 
$result = $db->query( '
	SELECT moderator_content.*, user.username
	FROM ' . NV_FORUM_GLOBALTABLE . '_moderator_content AS moderator_content
	INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator_content.userid)		
	WHERE 1=1
	ORDER BY user.username' );
 
$moderator_content = array();
while( $rows = $result->fetch() )
{
	$moderator_content[] = $rows;
}
$result->closeCursor();
 

$result = $db->query( '
	SELECT moderator.*, user.username, user.photo 
	FROM ' . NV_FORUM_GLOBALTABLE . '_moderator AS moderator
	INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = moderator.userid)
	WHERE 1=1
	ORDER BY user.username' );
 
$normal_mod = array();
$super_mod = array();
while( $row = $result->fetch() )
{ 
	if( $row['is_super_moderator'] == 0 )
	{
		$normal_mod[] = $row;
	}elseif( $row['is_super_moderator'] == 1 )
	{
		$super_mod[] = $row;
	}
}
$result->closeCursor();

$xtpl = new XTemplate( 'moderators.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/moderators' );
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

$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=add' );
$xtpl->assign( 'JSON_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=json' );
$xtpl->assign( 'ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=add' );

if( !empty( $super_mod ) )
{
	foreach( $super_mod as $mod )
	{
		$mod['token'] = md5( $mod['userid'] . session_id() . $global_config['sitekey'] );
		$mod['delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=delete&super=1&userid='.$mod['userid']; 
		$mod['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=edit&super=1&userid='.$mod['userid']; 
		if ( file_exists( NV_ROOTDIR . '/' . $mod['photo'] ) and ! empty( $mod['photo'] ) )
        {
            $mod['photo'] = NV_BASE_SITEURL . $mod['photo'];
        }
        else
        {
            $mod['photo'] = NV_BASE_SITEURL . 'themes/' . $global_config['site_theme'] . '/images/users/no_avatar.png';
        }
		$xtpl->assign( 'MOD', $mod );
		$xtpl->parse( 'main.super_mod.loop' );
	}
	$xtpl->parse( 'main.super_mod' );
}
if( !empty( $moderator_content ) )
{
	foreach( $moderator_content as $mod )
	{
		
		$mod['node'] = $forum_node[$mod['content_id']];
		
		$mod['token'] = md5( $mod['content_id'] . session_id() . $global_config['sitekey'] );
		$mod['delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=delete&userid='. $mod['userid'] .'&mod='.$mod['moderator_id']; 
		$mod['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=edit&userid='. $mod['userid'] .'&mod='.$mod['moderator_id']; 
		
		$xtpl->assign( 'MOD', $mod );
		$xtpl->parse( 'main.normal_mod.loop' );
	}
	$xtpl->parse( 'main.normal_mod' );
}
 
$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
