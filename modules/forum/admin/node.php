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

$page_title = $lang_module['node'];

function forum_fix_node_sort( $parent_id = 0, $sort = 0, $lev = 0 )
{
	global $db, $module_data;

	$sql = 'SELECT node_id, parent_id FROM ' . NV_FORUM_GLOBALTABLE . '_node WHERE parent_id=' . $parent_id . ' ORDER BY weight ASC';
	$result = $db->query( $sql );
	$array_cat_sort = array();
	while( $row = $result->fetch() )
	{
		$array_cat_sort[] = $row['node_id'];
	}
	$result->closeCursor();
	$weight = 0;
	if( $parent_id > 0 )
	{
		++$lev;
	}
	else
	{
		$lev = 0;
	}
	foreach( $array_cat_sort as $node_id_i )
	{
		++$sort;
		++$weight;
		$sql = 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_node SET weight=' . $weight . ', sort=' . $sort . ', lev=' . $lev . ' WHERE node_id=' . intval( $node_id_i );
		$db->query( $sql );
		$sort = forum_fix_node_sort( $node_id_i, $sort, $lev );
	}
	$numsubcat = $weight;
	if( $parent_id > 0 )
	{
		$sql = 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_node SET numsubcat=' . $numsubcat;
		if( $numsubcat == 0 )
		{
			$sql .= ",subcatid=''";
		}
		else
		{
			$sql .= ",subcatid='" . implode( ',', $array_cat_sort ) . "'";
		}
		$sql .= ' WHERE node_id=' . intval( $parent_id );
		$db->query( $sql );
	}
	return $sort;
}

function forum_fix_node_weight( $node_id, $parent_id, $new_weight )
{
	global $db;
	$sql = 'SELECT node_id FROM ' . NV_FORUM_GLOBALTABLE . '_node WHERE node_id <> ' . $node_id . ' AND parent_id=' . $parent_id . ' ORDER BY weight ASC';
	$result = $db->query( $sql );
	$weight = 0;
	while( $row = $result->fetch() )
	{
		++$weight;
		if( $weight == $new_weight ) ++$weight;
		$sql = 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_node SET weight=' . $weight . ' WHERE node_id=' . intval( $row['node_id'] );
		$db->query( $sql );
	}

	$sql = 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_node SET weight=' . $new_weight . ' WHERE node_id=' . $node_id;
	$db->query( $sql );
}

if( ACTION_METHOD && file_exists( NV_ROOTDIR . '/modules/' . $module_file . '/admin/node/' . ACTION_METHOD . '.php' ) )
{
	require_once NV_ROOTDIR . '/modules/' . $module_file . '/admin/node/' . ACTION_METHOD . '.php';
}

$xtpl = new XTemplate( 'node.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/node' );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'THEME', $global_config['site_theme'] );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'TOKEN', md5( $nv_Request->session_id . $global_config['sitekey'] ) );
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=node&action=add" );

$result = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_node ORDER BY sort ASC' );
while( $item = $result->fetch() )
{

	$item['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $item['node_id'] );

	$item['link'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node&parent_id=' . $item['node_id'];
	$item['edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node&action=insert&node_type_id=' . $item['node_type_id'] . '&node_id=' . $item['node_id'] . '&token=' . $item['token'];
	$item['delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node&action=delete&node_type_id=' . $item['node_type_id'] . '&node_id=' . $item['node_id'] . '&token=' . $item['token'];
	$item['create_sibling'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node&action=add&node_type_id=' . $item['node_type_id'] . '&parent_id=' . $item['parent_id'];
	$item['create_child'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node&action=add&node_type_id=' . $item['node_type_id'] . '&parent_id=' . $item['node_id'];
	$item['permissions'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node-permissions&node_id=' . $item['node_id'];
	$item['moderators'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators&action=add&type=node&node_id=' . $item['node_id'];

	$item['numsubcat'] = $item['numsubcat'] > 0 ? ' <span style="color:#FF0101;">(' . $item['numsubcat'] . ')</span>' : '';

	$item['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/' . $item['image'];

	// $item['status'] = $array_status[$item['status']];

	$xtpl->assign( 'LOOP', $item );

	$xtpl->parse( 'main.loop' );
}
$result->closeCursor();

// $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=node';
// $generate_page = nv_generate_page( $base_url, $num_items, $per_page, $page );
// if( ! empty( $generate_page ) )
// {
// $xtpl->assign( 'GENERATE_PAGE', $generate_page );
// $xtpl->parse( 'main.generate_page' );
// }

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
