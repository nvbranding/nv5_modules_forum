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
 
$page_title = $lang_module['node_create_forum'];

if( $nv_Request->get_int( 'save', 'post' ) == 1 )
{
	$json = array();

	$data['node_id'] = $nv_Request->get_int( 'node_id', 'post', 0 );
	$data['parentid_old'] = $nv_Request->get_int( 'parentid_old', 'post', 0 );
	$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'post', 0 );
	$data['weight'] = $nv_Request->get_int( 'weight', 'post', 0 );
	$data['old_weight'] = $nv_Request->get_int( 'old_weight', 'post', 0 );
	$data['status'] = $nv_Request->get_int( 'status', 'post', 0 );
	$data['title'] = nv_substr( $nv_Request->get_title( 'title', 'post', '', '' ), 0, 250 );
	$data['alias'] = nv_substr( $nv_Request->get_title( 'alias', 'post', '', '' ), 0, 250 );
	$data['alias'] = ! empty( $data['alias'] ) ? strtolower( change_alias( $data['alias'] ) ) : strtolower( change_alias( $data['title'] ) );

	$data['description'] = $nv_Request->get_editor( 'description', '', NV_ALLOWED_HTML_TAGS );
	$data['password'] = nv_substr( $nv_Request->get_title( 'password', 'post', '', '' ), 0, 250 );
	$data['image'] = nv_substr( $nv_Request->get_title( 'image', 'post', '', '' ), 0, 250 );
	$data['node_type_id'] = nv_substr( $nv_Request->get_title( 'node_type_id', 'post', '', '' ), 0, 10 );

	$data['allow_posting'] = $nv_Request->get_int( 'allow_posting', 'post', 0 );
	$data['allow_poll'] = $nv_Request->get_int( 'allow_poll', 'post', 0 );
	$data['moderate_threads'] = $nv_Request->get_int( 'moderate_threads', 'post', 0 );
	$data['moderate_replies'] = $nv_Request->get_int( 'moderate_replies', 'post', 0 );
	$data['count_messages'] = $nv_Request->get_int( 'count_messages', 'post', 0 );
	$data['find_new'] = $nv_Request->get_int( 'find_new', 'post', 0 );
	$data['default_prefix_id'] = $nv_Request->get_int( 'default_prefix_id', 'post', 0 );
	$data['allowed_watch_notifications'] = nv_substr( $nv_Request->get_title( 'allowed_watch_notifications', 'post', '', '' ), 0, 6 );
	$data['default_sort_order'] = nv_substr( $nv_Request->get_title( 'default_sort_order', 'post', '', '' ), 0, 20 );
	$data['default_sort_direction'] = nv_substr( $nv_Request->get_title( 'default_sort_direction', 'post', '', '' ), 0, 4 );
	$data['list_date_limit_days'] = $nv_Request->get_int( 'list_date_limit_days', 'post', 0 );

	if( empty( $data['title'] ) )
	{
		$json['error']['title'] = $lang_module['node_error_forum_title'];
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

	if( ! isset( $json['error'] ) )
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

					$sth = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_forum SET 
						node_id = ' . intval( $data['node_id'] ) . ',
						moderate_threads = ' . intval( $data['moderate_threads'] ) . ',
						moderate_replies = ' . intval( $data['moderate_replies'] ) . ',
						allow_posting = ' . intval( $data['allow_posting'] ) . ',
						allow_poll = ' . intval( $data['allow_poll'] ) . ',
						count_messages = ' . intval( $data['count_messages'] ) . ',
						find_new = ' . intval( $data['find_new'] ) . ',
						default_prefix_id = ' . intval( $data['default_prefix_id'] ) . ',
						require_prefix = ' . intval( $data['require_prefix'] ) . ',
						list_date_limit_days = ' . intval( $data['list_date_limit_days'] ) . ',
						default_sort_order =:default_sort_order,
						default_sort_direction =:default_sort_direction,
						allowed_watch_notifications=:allowed_watch_notifications' );

					$sth->bindParam( ':default_sort_order', $data['default_sort_order'], PDO::PARAM_STR );
					$sth->bindParam( ':default_sort_direction', $data['default_sort_direction'], PDO::PARAM_STR );
					$sth->bindParam( ':allowed_watch_notifications', $data['allowed_watch_notifications'], PDO::PARAM_STR );
					$sth->execute();
					$sth->closeCursor();
 
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
				//var_dump($e);die('ok');
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
					$sth = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_forum SET 
						node_id = ' . intval( $data['node_id'] ) . ',
						moderate_threads = ' . intval( $data['moderate_threads'] ) . ',
						moderate_replies = ' . intval( $data['moderate_replies'] ) . ',
						allow_posting = ' . intval( $data['allow_posting'] ) . ',
						allow_poll = ' . intval( $data['allow_poll'] ) . ',
						count_messages = ' . intval( $data['count_messages'] ) . ',
						find_new = ' . intval( $data['find_new'] ) . ',
						default_prefix_id = ' . intval( $data['default_prefix_id'] ) . ',
						require_prefix = ' . intval( $data['require_prefix'] ) . ',
						list_date_limit_days = ' . intval( $data['list_date_limit_days'] ) . ',
						default_sort_order =:default_sort_order,
						default_sort_direction =:default_sort_direction,
						allowed_watch_notifications=:allowed_watch_notifications 
						WHERE node_id=' . intval( $data['node_id'] ) );

					$sth->bindParam( ':default_sort_order', $data['default_sort_order'], PDO::PARAM_STR );
					$sth->bindParam( ':default_sort_direction', $data['default_sort_direction'], PDO::PARAM_STR );
					$sth->bindParam( ':allowed_watch_notifications', $data['allowed_watch_notifications'], PDO::PARAM_STR );
					$sth->execute();
					$sth->closeCursor();
					
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
					
					//rebuildPermissionCache();	
					
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
				//var_dump($e);die('ok');
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

$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'post', 0 );
$data['token'] = $nv_Request->get_string( 'token', 'post', '' );

if( defined( 'NV_EDITOR' ) )
{
	require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}
$data = array(
	'node_id' => 0,
	'parent_id' => $data['parent_id'],
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
	'allow_posting' => 1,
	'allow_poll' => 1,
	'moderate_threads' => 0,
	'moderate_replies' => 0,
	'count_messages' => 1,
	'find_new' => 1,
	'default_prefix_id' => 0,
	'allowed_watch_notifications' => 'all',
	'default_sort_order' => 'last_post_date',
	'default_sort_direction' => 'desc',
	'list_date_limit_days' => 0,
	);

$error = array();

$data['node_id'] = $nv_Request->get_int( 'node_id', 'get,post', 0 );
$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'get,post', 0 );
if( $data['node_id'] > 0 )
{
 
	$data = $db->query( 'SELECT node.*, forum.*			
			FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)		
			WHERE node.node_id=' . $data['node_id'] )->fetch();

	$caption = $lang_module['node_edit_forum'];
}
else
{
	$caption = $lang_module['node_create_forum'];
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

$data['find_new'] = ( $data['find_new'] == 1 )  ? 'checked="checked"': '';
$data['count_messages'] = ( $data['count_messages'] == 1 )  ? 'checked="checked"': '';
$data['moderate_replies'] = ( $data['moderate_replies'] == 1 )  ? 'checked="checked"': '';
$data['moderate_threads'] = ( $data['moderate_threads'] == 1 )  ? 'checked="checked"': '';
$data['allow_poll'] = ( $data['allow_poll'] == 1 )  ? 'checked="checked"': '';
$data['allow_posting'] = ( $data['allow_posting'] == 1 )  ? 'checked="checked"': '';


$xtpl = new XTemplate( 'node_insert_forum.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file . '/node' );
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
		'selected' => ( $val['node_id'] == $data['parent_id'] ) ? ' selected="selected"' : '' ) );
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

$allowed_watch_notifications = array(
	'all' => ' Tin nhắn mới',
	'thread' => ' Chủ đề mới',
	'none' => 'Không được phép' );
foreach( $allowed_watch_notifications as $key => $name )
{
	$xtpl->assign( 'ALLOWERWATCH', array(
		'key' => $key,
		'name' => $name,
		'checked' => ( $key == $data['allowed_watch_notifications'] ) ? 'checked="checked"' : '' ) );
	$xtpl->parse( 'main.allowed_watch' );
}

$list_date_limit_days = array(
	'0' => 'None',
	'7' => '7 Ngày',
	'14' => '14 Ngày',
	'30' => '20 Ngày',
	'60' => '2 Tháng',
	'90' => '3 Tháng',
	'182' => '6 Tháng',
	'365' => '1 Năm' );
foreach( $list_date_limit_days as $key => $name )
{
	$xtpl->assign( 'LIMIT_DAYS', array(
		'key' => $key,
		'name' => $name,
		'selected' => ( $key == $data['list_date_limit_days'] ) ? 'selected="selected"' : '' ) );
	$xtpl->parse( 'main.limit_days' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
