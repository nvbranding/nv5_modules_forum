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

if( $nv_Request->get_int( 'save', 'post', 0 ) == 1 )
{
	$content_type = 'node';
	$permissions = $nv_Request->get_typed_array( 'permissions', 'post', array() );
	$node_id = $nv_Request->get_int( 'node_id', 'post', 0 );
	$group_id = $nv_Request->get_int( 'group_id', 'post', 0 );
 
	require_once NV_ROOTDIR . '/includes/forum/model/node.php';
	require_once NV_ROOTDIR . '/includes/forum/model/users.php';
	require_once NV_ROOTDIR . '/includes/forum/model/permission.php';
	
	updateContentPermissionsForUserCollection( $permissions, $content_type, $node_id, $group_id, 0 );
 
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node-permissions&node_id=' . $data['node_id'] );
	die();

}

$xtpl = new XTemplate( 'node_permissions_group.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/node' );
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

$xtpl->assign( 'DATA', $data );
$xtpl->assign( 'NODE_TITLE', $forum_node[$data['node_id']]['title'] );
$xtpl->assign( 'GROUP_TITLE', $groups_list[$data['group_id']] );
$xtpl->assign( 'ACTION', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=node-permissions&action=group&node_id=" . $data['node_id'] . "&group_id=" . $data['group_id'] );

$nodeData = $db->query( 'SELECT node.* FROM ' . NV_FORUM_GLOBALTABLE . '_node AS node WHERE node.node_id = ' . intval( $data['node_id'] ) )->fetch();

$groupData = $db->query( 'SELECT * FROM ' . NV_GROUPS_GLOBALTABLE . ' WHERE group_id = ' . intval( $data['group_id'] ) )->fetch();

$result = $db->query( '
	SELECT permission.*,
		entry_content.permission_value, entry_content.permission_value_int,
		COALESCE(entry_content.permission_value, \'unset\') AS value,
		COALESCE(entry_content.permission_value_int, 0) AS value_int
	FROM ' . NV_FORUM_GLOBALTABLE . '_permission AS permission
	LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content AS entry_content ON (entry_content.permission_id = permission.permission_id
		AND entry_content.permission_group_id = permission.permission_group_id
		AND entry_content.content_type = \'node\'
		AND entry_content.content_id = ' . intval( $nodeData['node_id'] ) . '
		AND entry_content.user_group_id = ' . intval( $groupData['group_id'] ) . '
		AND entry_content.userid = \'0\')
	WHERE permission.permission_group_id IN (\'category\', \'forum\', \'linkForum\', \'page\')
	ORDER BY permission.display_order' );

$nodePermissions = array();
while( $pme = $result->fetch() )
{
	$nodePermissions[$pme['interface_group_id']][$pme['permission_id']] = $pme;
}
$result->closeCursor();

$result = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_interface_group ORDER BY display_order' );
$permission_interface_group_key = array();
while( $pig = $result->fetch() )
{
	$permission_interface_group_key[] = $pig['interface_group_id'];
}
$result->closeCursor();
 
$array_permissionChoices = array(
	'unset' => 'unset',
	'content_allow' => 'content_allow',
	'reset' => 'reset',
	'deny' => 'deny' );

$nodePermissions = sortArrayByArray( $nodePermissions, $permission_interface_group_key );

if( ! empty( $nodePermissions ) )
{
	foreach( $nodePermissions as $interface_group_id => $permissions )
	{
		$xtpl->assign( 'interfaceGroup', array( 'key' => $interface_group_id, 'name' => $lang_module[$interface_group_id] ) );

		foreach( $permissions as $permission )
		{
 
			//echo "$lang_module['". $interface_group_id ."_". $permission['permission_id'] ."']='';<br>";
 			
			$permission['name'] = $lang_module[$interface_group_id . '_' . $permission['permission_id']];

			$xtpl->assign( 'PERMISSION', $permission );

			if( $permission['permission_type'] == 'integer' )
			{

				$xtpl->assign( 'UNSET_CHECKED', ( $permission['value_int'] == 0 ) ? 'checked="checked"' : '' );
				$xtpl->assign( 'UNLIMITED_CHECKED', ( $permission['value_int'] == '-1' ) ? 'checked="checked"' : '' );
				$xtpl->assign( 'LIMITED_CHECKED', ( $permission['value_int'] > 0 || ( ! $array_permissionChoices['reset'] && $permission['value_int'] == 0 ) ) ? 'checked="checked"' : '' );
				$xtpl->assign( 'DISABLED', ( $permission['value_int'] <= 0 ) ? 'disabled="disabled"' : '' );
				$xtpl->assign( 'DISABLED_CLASS', ( $permission['value_int'] <= 0 ) ? 'disabled' : '' );
				$xtpl->assign( 'VALUE', ( $permission['value_int'] > 0 ) ? $permission['value_int'] : 0 );

				$xtpl->parse( 'main.permission_interface_group.permission_group.integer' );
			}
			else
			{
				foreach( $array_permissionChoices as $value => $permissionChoices )
				{
					$xtpl->assign( 'permissionChoices', array( 'value' => $value, 'checked' => ( $permission['value'] == $value ) ? 'checked="checked"' : '' ) );
					$xtpl->parse( 'main.permission_interface_group.permission_group.permissionChoices' );
				}
			}

			$xtpl->parse( 'main.permission_interface_group.permission_group' );
		}

		$xtpl->parse( 'main.permission_interface_group' );
	}

}
 
$generalPermissions = $db->query( '
	SELECT permission.*,
		entry_content.permission_value, entry_content.permission_value_int,
		COALESCE(entry_content.permission_value, \'unset\') AS value,
		COALESCE(entry_content.permission_value_int, 0) AS value_int
	FROM ' . NV_FORUM_GLOBALTABLE . '_permission AS permission
	LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content AS entry_content ON (entry_content.permission_id = permission.permission_id
		AND entry_content.permission_group_id = permission.permission_group_id
		AND entry_content.content_type = \'node\'
		AND entry_content.content_id = ' . intval( $nodeData['node_id'] ) . '
		AND entry_content.user_group_id = ' . intval( $groupData['group_id'] ) . '
		AND entry_content.userid = \'0\')
	WHERE permission.permission_group_id = \'general\'
		AND permission.permission_id = \'viewNode\'' )->fetch();
		
foreach( $array_permissionChoices as $key => $value )
{	
	$xtpl->assign( 'permissionChoices', array( 'value' => $value, 'checked' => ( $generalPermissions['value'] == $value ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.viewNode' );
	
}
 
$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
