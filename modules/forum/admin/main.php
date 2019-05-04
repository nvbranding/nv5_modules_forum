<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES., JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES ., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Dec 3, 2010  11:33:22 AM 
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

// $result = $db->query('SELECT COUNT(*) total, thread_id FROM nv4_forum_post WHERE position > 0 AND message_state=\'visible\' GROUP BY thread_id'); 

// while( $rows = $result->fetch() )
// {
	// $query='UPDATE nv4_forum_thread SET reply_count= '. intval( $rows['total'] ) .' WHERE thread_id=' . intval( $rows['thread_id'] ) . ';';
	// echo $query . "<br>";
// }

// $result = $db->query('SELECT COUNT(*) total, f.node_id 
// FROM nv4_forum_post p 
// LEFT JOIN nv4_forum_thread t ON( p.thread_id = t.thread_id )
// LEFT JOIN nv4_forum_forum f ON( f.node_id = t.node_id )
// WHERE p.message_state=\'visible\' AND t.discussion_state = \'visible\' AND t.discussion_open =1
// GROUP BY f.node_id');
// while( $rows = $result->fetch() )
// {
	// $query='UPDATE nv4_forum_forum SET message_count= '. intval( $rows['total'] ) .' WHERE node_id=' . intval( $rows['node_id'] ) . ';';
	// echo $query . "<br>";
// }


// $result = $db->query('SELECT COUNT(*) total, node_id FROM nv4_forum_thread WHERE discussion_state = \'visible\' AND discussion_open =1 GROUP BY node_id'); 

// while( $rows = $result->fetch() )
// {
	// $query='UPDATE nv4_forum_forum SET discussion_count= '. intval( $rows['total'] ) .' WHERE node_id=' . intval( $rows['node_id'] ) . ';';
	// echo $query . "<br>";
// }



// die();
// if( nv_sendmail( 'dlinhvan@gmail.com', 'sividuc1@gmail.com', 'Tiêu đề', 'Nội dung tin nhắn' ) )
// {
	// die('ok');
// }
 
 
// $nvModelPermission = new Forum\Model\Permission();
 
// $nvModelPermission->getAllPermissions();

 
 
/* $error = array();
$succe = array();
$info = array();

if( $nv_Request->isset_request( 'action', 'post' ) )
{
	$thread_id = $nv_Request->get_string( 'thread_id', 'post', '' );
	$catid = $nv_Request->get_string( 'catid', 'post', '' );
	$action = $nv_Request->get_string( 'action', 'post', '' );
	if( $action == 'delete_thread' )
	{
		$checkmod = $nv_Request->get_string( 'checkmod', 'post', '' );
		$checkess = md5( $thread_id . $global_config['sitekey'] . $catid. session_id() );
		if( $checkmod == $checkess )
		{	
			$admin_cat = explode(',', $nv_cat[$catid]['admins']);
			if( in_array( $admin_info['userid'], $admin_cat ) )
			{
				$thread_obj->del_thread( $thread_id, $catid );
				$forum->del_file_cache( 'cat' );
				$forum->del_file_cache( 'lastest_post' );
				$forum->del_file_cache( 'lastest_post_topx' );
				$forum->log_forum( NV_LANG_DATA, "Xóa", "Chủ đề-" . $thread_id, $admin_info['userid'] );
				$succe['message'] = "Chủ đề đã xoá";
			}else
			{
				$error[] = "Bạn không có quyền xóa chủ đề này";
			}
		}
		else
		{
				$error[] = "Phát hiện có gian lận: Chủ đề-" . $thread_id;
				$forum->log_forum( NV_LANG_DATA, "Xóa", "Gian lận: Chủ đề-" . $thread_id, $admin_info['userid'] );
		}
	}
	elseif( $action == 'delete_thread_list' )
	{
		$listall = $nv_Request->get_string( 'listall', 'post,get' );
		$array_thread_id = explode( ',', $listall );
		$listthread = array();
		$list_error = array();
		
		$a = 1;
		foreach( $array_thread_id as $order )
		{
			$thread = explode( "_", $order );
			$checkess = md5( $thread[0] . $global_config['sitekey'] . $thread[1] .session_id() );
			$thread_id = $thread[0];
			$catid = $thread[1];
			$checkmod = $thread[2];
			if( $checkmod == $checkess )
			{
				$admin_cat = explode(',', $nv_cat[$catid]['admins']);
				if( in_array( $admin_info['userid'], $admin_cat ) )
				{
					$thread_obj->del_thread( $thread_id, $catid );
					$listthread[] = $thread_id;
					++$a;
				}else
				{
					$error[] = "Bạn không có quyền xóa chủ đề này";
					$list_error[] = $thread_id;
				}
			}
			else
			{
				$list_error[] = $thread_id;
			}
		}
		
		if( $a > 1 )
		{
			$forum->del_file_cache( 'cat' );
			$forum->del_file_cache( 'lastest_post' );
			$forum->del_file_cache( 'lastest_post_topx' );

			$forum->log_forum( NV_LANG_DATA, "Xóa", "Chủ đề-" . implode( ',', $listthread ), $admin_info['userid'] );
			$succe['message'] = "Các chủ đề đã xoá: " . implode( ',', $listthread ) . " ";
		}
		else
		{
			$forum->log_forum( NV_LANG_DATA, "Xóa", "Gian lận: Chủ đề-" . implode( ',', $list_error ), $admin_info['userid'] );
			$error[] = "Phát hiện có gian lận: Chủ đề-" . implode( ',', $list_error );
		}
	}
	if( empty( $error ) )
	{
		$info['data'] = array( 'message' => 'success', 'item' => $succe );
	}
	else
	{
		$info['data'] = array( 'message' => 'unsuccess', 'item' => $error );
	}
	echo json_encode( $info );
	$forum->clear();
	$thread_obj->clear();
	$cat_obj->clear();
	 
	unset( $array, $nv_cat, $array_user, $generate_page, $forum, $thread_obj, $cat_obj, $arr_config, $admin_info, $array_cat_admin, $check_admin, $xtpl );
		
	exit();
}

$page = $nv_Request->get_int( 'page', 'get', 0 );
$per_page = 50;

$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_thread ORDER BY post_date DESC LIMIT " . $page . "," . $per_page;
$result = $db->query( $sql );
$result_all = $db->query( "SELECT FOUND_ROWS()" );
$numf = $result_all->fetchColumn();
$all_page = ( $numf ) ? $numf : 1;

$array = array();
$i = 0;
$a = 0;
while( $row = $result->fetch() )
{
	++$i;

	if( ! empty( $row['last_post_page'] ) ) $row['link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $row['title'] ) . "/" . $row['thread_id'] . "/page-" . $row['last_post_page'], true ) . "#post" . $row['last_post_id'] . "";
	else  $row['link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $row['title'] ) . "/" . $row['thread_id'], true ) . "#post" . $row['last_post_id'] . "";
	$row['edit_post'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=post/update/" . $row['last_post_id'], true );

	$array[$row['thread_id']] = array( //
		'thread_id' => $row['thread_id'], //
		'catid' => $row['catid'],
		'title' => $row['title'],
		'link' => $row['link'],
		'edit_post' => $row['edit_post'],
		'username' => $row['username'], //
		'post_date' => nv_date( 'd.n.Y,  H:i', $row['post_date'] ), //
		'admin' => $nv_cat[$row['catid']]['admins'], //
		'class' => ( $a % 2 == 0 ) ? "" : " class=\"second\"",
		'sort' => $i );
	++$a;
}

$base_url = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op;

$generate_page = nv_generate_page( $base_url, $all_page, $per_page, $page );

$xtpl = new XTemplate( "main.tpl", NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'URL_DEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=main" );

if( ! empty( $array ) )
{
	foreach( $array as $loop )
	{
		$loop['checkmod'] = md5( $loop['thread_id'] . $global_config['sitekey'] . $loop['catid']. session_id() );
		
		$xtpl->assign( 'loop', $loop );

		$ad = explode( ',', $loop['admin'] );

		if( in_array( $admin_info['admin_id'], $ad ) )
		{
			$xtpl->parse( 'main.content.loop.admin' );
		}
		$xtpl->assign( 'thread_id', $loop['thread_id'] . "_".$loop['catid']."_" . md5( $loop['thread_id'] . $global_config['sitekey'] . $loop['catid']. session_id() ) );

		$xtpl->parse( 'main.content.loop' );
	}

	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.content.generate_page' );
	}
	$xtpl->parse( 'main.content' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' ); */

// $forum->clear();
// $thread_obj->clear();
// $cat_obj->clear();
// $post_obj->clear();

// unset( $array, $nv_cat, $array_user, $generate_page, $forum, $thread_obj, $post_obj, $cat_obj, $arr_config, $admin_info, $array_cat_admin, $check_admin, $xtpl );
	

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';