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

$page_title = $lang_module['node_create_category'];

if( $nv_Request->get_int( 'save', 'post' ) == 1 )
{
	$json = array();
	$data['node_id'] = $nv_Request->get_int( 'node_id', 'post', 0 );
	$data['parentid_old'] = $nv_Request->get_int( 'parentid_old', 'post', 0 );
	$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'post', 0 );
	$data['old_weight'] = $nv_Request->get_int( 'old_weight', 'post', 0 );
	$data['weight'] = $nv_Request->get_int( 'weight', 'post', 0 );
	$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );
	$data['title'] = nv_substr( $nv_Request->get_title( 'title', 'post', '', '' ), 0, 250 );
	$data['alias'] = nv_substr( $nv_Request->get_title( 'alias', 'post', '', '' ), 0, 250 );
	$data['alias'] = ! empty( $data['alias'] ) ? strtolower( change_alias( $data['alias'] ) ) : strtolower( change_alias( $data['title'] ) );
	$data['image'] = '';
	$data['description'] = $nv_Request->get_editor( 'description', '', NV_ALLOWED_HTML_TAGS );
	$data['password'] = nv_substr( $nv_Request->get_title( 'password', 'post', '', '' ), 0, 250 );
	$data['node_type_id'] = nv_substr( $nv_Request->get_title( 'node_type_id', 'post', '', '' ), 0, 10 );
 
	if( empty( $data['title'] ) )
	{
		$json['error']['title'] = $lang_module['node_error_title'];
	}

	$stmt = $db->prepare( 'SELECT COUNT(*) FROM ' . NV_FORUM_GLOBALTABLE . '_node WHERE node_id !=' . $data['node_id'] . ' AND alias= :alias' );
	$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
	$stmt->execute();
	$check_alias = $stmt->fetchColumn();

	if( $check_alias and $data['parent_id'] > 0 )
	{
		$parentid_alias = $db->query( 'SELECT  FROM ' . NV_FORUM_GLOBALTABLE . ' WHERE node_id=' . $data['parent_id'] )->fetchColumn();
		$data['alias'] = $parentid_alias . '-' . $data['alias'];
	}

	if( empty( $error ) )
	{
		if( $data['node_id'] == 0 )
		{
			try
			{

				$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_node SET 
						parent_id = ' . intval( $data['parent_id'] ) . ', 
						status=' . intval( $data['status'] ) . ', 
						weight = ' . intval( $data['weight'] ) . ', 
						date_added=' . intval( NV_CURRENTTIME ) . ',  
						date_modified=0, 
						sort = 0,
						lev = 0,
						numsubcat=0, 
						title =:title,
						alias =:alias,
						description =:description,
						image =:image,
						password=:password,
						node_type_id=:node_type_id' );

				$stmt->bindParam( ':title', $data['title'], PDO::PARAM_STR );
				$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
				$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR, strlen( $data['description'] ) );
				$stmt->bindParam( ':image', $data['image'], PDO::PARAM_STR );
				$stmt->bindParam( ':password', $data['password'], PDO::PARAM_STR );
				$stmt->bindParam( ':node_type_id', $data['node_type_id'], PDO::PARAM_STR );
				$stmt->execute();

				if( $data['node_id'] = $db->lastInsertId() )
				{

					forum_fix_node_sort();
 	
					rebuildPermissionCache();				
 
					nv_insert_logs( NV_LANG_DATA, $module_name, 'Add A node', 'node_id: ' . $data['node_id'], $admin_info['userid'] );
				}
				else
				{
					$json['error']['db'] = $lang_module['node_error_save'];

				}
				$stmt->closeCursor();
			}
			catch ( PDOException $e )
			{ 
				$json['error']['db'] = $lang_module['node_error_save'];
				var_dump($e);die('ok');
			}

		}
		else
		{
			try
			{

				$stmt = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_node SET 
					parent_id = ' . intval( $data['parent_id'] ) . ', 
					status=' . intval( $data['status'] ) . ', 
					weight=' . intval( $data['weight'] ) . ', 
					date_modified=' . intval( NV_CURRENTTIME ) . ',  
					title =:title,
					alias =:alias,
					description =:description,
					image =:image,
					password=:password 
					WHERE node_id=' . $data['node_id'] );

				$stmt->bindParam( ':title', $data['title'], PDO::PARAM_STR );
				$stmt->bindParam( ':alias', $data['alias'], PDO::PARAM_STR );
				$stmt->bindParam( ':description', $data['description'], PDO::PARAM_STR, strlen( $data['description'] ) );
				$stmt->bindParam( ':image', $data['image'], PDO::PARAM_STR );
				$stmt->bindParam( ':password', $data['password'], PDO::PARAM_STR );

				if( $stmt->execute() )
				{
					//rebuildPermissionCache();	
					
					if( $data['parent_id'] != $data['parentid_old'] )
					{
						$stmt = $db->prepare( 'SELECT max(weight) FROM ' . NV_FORUM_GLOBALTABLE . '_node WHERE parent_id= :parent_id ' );
						$stmt->bindParam( ':parent_id', $data['parent_id'], PDO::PARAM_INT );
						$stmt->execute();

						$weight = $stmt->fetchColumn();

						$weight = intval( $weight ) + 1;
						$sql = 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_node SET weight=' . $weight . ' WHERE node_id=' . intval( $data['node_id'] );
						$db->query( $sql );

						
					}
					
					if( $data['old_weight'] !=  $data['weight'] )
					{
						forum_fix_node_weight( $data['node_id'], $data['parent_id'], $data['weight'] );	
					}
					
					forum_fix_node_sort();
					
					nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit A node', 'node_id: ' . $data['node_id'], $admin_info['userid'] );


				}
				else
				{
					$json['error']['db'] = $lang_module['node_error_save'];

				}
				$stmt->closeCursor();
			}
			catch ( PDOException $e )
			{
				$json['error']['db'] = $lang_module['node_error_save'];
				var_dump($e);die('ok');
			}

		}

	}
	if( ! isset( $json['error'] ) )
	{
		deleteCache( 'node', $module_name );

		$json['redirect'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;
	}

	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';

}

//$data['node_type_id'] = nv_substr( $nv_Request->get_title( 'node_type_id', 'get,post', '', '' ), 0, 10 );
// category | forum | linkforum | page

$parent_id = $nv_Request->get_int( 'parent_id', 'post', 0 );
$data['token'] = $nv_Request->get_string( 'token', 'post', '' );
$data = array(
	'node_id' => 0,
	'parent_id' => $parent_id,
	'title' => '',
	'alias' => '',
	'description' => '',
	'password' => '',
	'image' => '',
	'weight' => 1,
	'sort' => 0,
	'lev' => 0,
	'node_type_id' => $data['node_type_id'],
	'numsubcat' => 0,
	'subcatid' => '',
	'status' => 1,
	'date_added' => NV_CURRENTTIME,
	'date_modified' => NV_CURRENTTIME,
	);

if( defined( 'NV_EDITOR' ) )
{
	require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}

$error = array();

$data['node_id'] = $nv_Request->get_int( 'node_id', 'get,post', 0 );
$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'get,post', 0 );
if( $data['node_id'] > 0 )
{
	$data = $db->query( 'SELECT *
		FROM ' . NV_FORUM_GLOBALTABLE . '_node  
		WHERE node_id=' . $data['node_id'] )->fetch();

	$caption = $lang_module['node_edit_category'];
}
else
{
	$caption = $lang_module['node_create_category'];
}

$data['description'] = htmlspecialchars( nv_editor_br2nl( $data['description'] ) );

if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )
{
	$data['description'] = nv_aleditor( 'description', '100%', '200px', $data['description'], '' );
}
else
{
	$data['description'] = '<textarea style="width: 100%" name="description" id="description" cols="20" rows="15">' . $data['description'] . '</textarea>';
}
$xtpl = new XTemplate( 'node_insert_category.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/node' );
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
$xtpl->assign( 'ACTION', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=node&action=insert" );

$xtpl->assign( 'PATH', NV_UPLOADS_DIR . '/' . $module_name );
$xtpl->assign( 'CURRENT_PATH', NV_UPLOADS_DIR . '/' . $module_name . '/node' );
$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );

if( isset( $error['warning'] ) )
{
	$xtpl->assign( 'error_warning', $error['warning'] );
	$xtpl->parse( 'main.error_warning' );
}

if( isset( $error['title'] ) )
{
	$xtpl->assign( 'error_title', $error['title'] );
	$xtpl->parse( 'main.error_title' );
}

$sql = 'SELECT node_id, title, lev FROM ' . NV_FORUM_GLOBALTABLE . '_node WHERE node_id !=' . $data['node_id'] . ' ORDER BY sort ASC';

$result = $db->query( $sql );

$array_cat_list = array();

$array_cat_list[0] = array( '0', $lang_module['node_sub_sl'] );

while( list( $catid_i, $title_i, $lev_i ) = $result->fetch( 3 ) )
{
	$xtitle_i = '';
	if( $lev_i > 0 )
	{
		$xtitle_i .= '&nbsp;';
		for( $i = 1; $i <= $lev_i; $i++ )
		{
			$xtitle_i .= '---';
		}
	}
	$xtitle_i .= $title_i;
	$array_cat_list[] = array( $catid_i, $xtitle_i );
}

foreach( $array_cat_list as $rows_i )
{
	$xtpl->assign( 'node', array(
		'key' => $rows_i[0],
		'name' => $rows_i[1],
		'selected' => ( $rows_i[0] == $data['parent_id'] ) ? ' selected="selected"' : '' ) );
	$xtpl->parse( 'main.node' );
}

foreach( $array_status as $key => $name )
{
	$xtpl->assign( 'STATUS', array(
		'key' => $key,
		'name' => $name,
		'selected' => ( $key == $data['status'] ) ? 'selected="selected"' : '' ) );
	$xtpl->parse( 'main.status' );
}

if( empty( $data['alias'] ) )
{
	$xtpl->parse( 'main.getalias' );
}
$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
