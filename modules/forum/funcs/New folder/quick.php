<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweB.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 22:18
 */

if( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

$error = array();
$succe = array();
$info = array();

$catid = $nv_Request->get_int( 'catid', 'post', 0 );
$thread_id = $nv_Request->get_int( 'thread_id', 'post', 0 );
$action = $nv_Request->get_title( 'action', 'post', '' );
$checkss_quickmod = $nv_Request->get_title( 'checkss_quickmod', 'post', '' );
$checkss = md5( $user_info['userid'] . session_id() . $thread_id . $global_config['sitekey'] );
if( $checkss == $checkss_quickmod )
{
	$post_obj = new Post();
	if( $action == 'lock' || $action == 'open' )
	{
		$discussion_open = 1;
		if( $action == 'lock' )
		{
			$discussion_open = 0;
		}
		elseif( $action == 'open' )
		{
			$discussion_open = 1;
		}
		$sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_thread SET
		discussion_open=" . intval( $discussion_open ) . "
		WHERE thread_id=" . $thread_id . " ";
		if( $db->query( $sql ) )
		{
			if( $action == 'lock' ) $succe['message'] = "Chủ đề đã được khóa";
			elseif( $action == 'open' ) $succe['message'] = "Chủ đề đã được mở";
		}

	}
	elseif( $action == 'delete_thread' )
	{

		$post_count = $db->query( "SELECT SUM(post_count) FROM  " . NV_PREFIXLANG . "_" . $module_data . "_thread_user_post WHERE thread_id=" . $thread_id )->fetchColumn();
		
		
		$sql_l = "SELECT A.thread_id, A.title, A.prefix_id, B.post_id, B.user_id, B.username, B.post_date, B.position
		FROM  " . NV_PREFIXLANG . "_" . $module_data . "_thread A
		LEFT JOIN  " . NV_PREFIXLANG . "_" . $module_data . "_post B 
		ON A.thread_id = B.thread_id
		WHERE A.thread_id = ( 
		SELECT MAX( thread_id ) 
		FROM  " . NV_PREFIXLANG . "_" . $module_data . "_thread 
		WHERE catid =".$catid." AND thread_id != ".$thread_id.")  
		ORDER BY B.position DESC 
		LIMIT 0 , 1";
		
				
		list( $new_threadid, $title, $prefix_id, $post_id,$user_id, $username,$post_date,$position ) = $db->query(  $sql_l )->fetch( 3 );
		
		
		// dang code
		$sql = "SELECT SUM( likes ) total_like , COUNT(post_id) total_post, user_id 
		FROM " . NV_PREFIXLANG . "_" . $module_data . "_post 
		WHERE thread_id=" . $thread_id." 
		GROUP BY user_id ";
		
		$result = $db->query( $sql );
		
		
		unset($_sql);
		$_sql="UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_users_statistic\n";
		$_sql.=" SET like_count = CASE \n";
		$list_user_id = array();
		$_like_count = $_message_count = "";
		while ( $item = $result->fetch() )
		{
			// $db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_users_statistic
				// SET like_count = IF(like_count > ".$item['total_like'].", like_count - ".$item['total_like'].", 0) , 
				// message_count = IF(message_count > ".$item['total_post'].", message_count - ".$item['total_post'].", 0),
				// WHERE userid = " . $item['user_id'] . "" );
			$list_user_id[] = $item['user_id'];
			$_like_count.=" WHEN userid = ".$item['user_id']." THEN IF( like_count > ".$item['total_like'].", like_count - ".$item['total_like'].", 0) ";
			$_message_count.=" WHEN userid = ".$item['user_id']." THEN IF( message_count > ".$item['total_post'].", message_count - ".$item['total_post'].", 0) \n";
			
			
		}
		$_sql.="".$_like_count."\n";
		$_sql.=" END\n";
		$_sql.=",message_count = CASE\n";
		$_sql.="".$_message_count."";
		$_sql.=" END\n";
		$_sql.=" WHERE userid  IN (" . implode(',', $list_user_id ) . ")\n";
		//var_dump($_sql);die();
		$db->query($_sql);
		
		$db->query( "DELETE FROM A , B, C 
		USING " . NV_PREFIXLANG . "_" . $module_data . "_thread A, " . NV_PREFIXLANG . "_" . $module_data . "_thread_user_post B , " . NV_PREFIXLANG . "_" . $module_data . "_post C
		WHERE A.thread_id = ".$thread_id."  
		AND B.thread_id = ".$thread_id."
		AND C.thread_id = ".$thread_id."" );
		
		
		
		$last_page = $post_obj->all_post_page( $new_threadid, $per_page );
		
		$db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_cat SET
				replycount=replycount - " . intval( $post_count ) . ",
				threadcount=threadcount - 1,
				last_post_date=" . intval( $post_date ) . ",
				last_post_username=" . $db->quote( $username ) . ",
				last_post_user_id=" . intval( $user_id ) . ",
				last_post_id=" . intval( $post_id ) . ",
				last_post_page=" . intval( $last_page ) . ",
				last_thread_title=" . $db->quote( $title ) . ",
				last_thread_id=" . intval( $new_threadid ) . ",
				last_prefix_id=" . intval( $prefix_id ) . "
			WHERE catid=" . $catid );
		
		
				
		$forum->del_file_cache( 'cat' );
		$forum->del_file_cache( 'lastest_post' );
		$forum->del_file_cache( 'lastest_post_topx' );
		$succe['link'] = $nv_cat[$catid]['link'];
		$succe['message'] = "Chủ đề đã xoá";

	}
	elseif( $action == 'move' )
	{

		$xtpl = new XTemplate( "quick_mod_move.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'MODULE_FILE', $module_file );
		$xtpl->assign( 'TEMPLATE', $module_info['template'] );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'linksite', NV_BASE_SITEURL . "themes/" . $module_info['template'] );
		$xtpl->assign( 'QUICK_MOD', nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=quick", true ) );
		$xtpl->assign( 'catid', $catid );
		$xtpl->assign( 'thread_id', $thread_id );
		$xtpl->assign( 'checkss_quickmod', $checkss_quickmod );

		foreach( $cat_obj->cat as $cat_id => $cat )
		{

			$xtitle_i = "";
			if( $cat['lev'] > 0 )
			{
				$xtitle_i .= "&nbsp;|";
				for( $i = 1; $i <= $cat['lev']; ++$i )
				{
					$xtitle_i .= "---";
				}
				$xtitle_i .= ">&nbsp;";
			}
			$xtitle_i .= $cat['title'];
			$cat['title'] = $xtitle_i;
			if( $cat['lev'] == 0 || $catid == $cat_id )
			{
				$cat['disabled'] = "disabled";
			}
			else
			{
				$cat['disabled'] = "";
			}
			$xtpl->assign( 'CAT', $cat );
			$xtpl->parse( 'main.cat' );
		}

		$xtpl->parse( 'main' );
		$succe['content'] = $xtpl->text( 'main' );
	}
	elseif( $action == 'move_thread' )
	{
		$newcatid = $nv_Request->get_int( 'newcatid', 'post', 0 );
		
		$db->query( $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_thread SET catid=" . $newcatid . " WHERE thread_id=" . $thread_id . "" );
		
		$sql_r = "SELECT A.thread_id, A.title, A.prefix_id, B.post_id, B.user_id, B.username, B.post_date
		FROM  " . NV_PREFIXLANG . "_" . $module_data . "_thread AS a
		LEFT JOIN  " . NV_PREFIXLANG . "_" . $module_data . "_post AS b ON A.thread_id = B.thread_id
		WHERE A.thread_id = ( 
		SELECT MAX( thread_id ) 
		FROM  " . NV_PREFIXLANG . "_" . $module_data . "_thread 
		WHERE catid =".$newcatid." ) 
		ORDER BY B.position DESC 
		LIMIT 0 , 1";
		
		list( $new_threadid, $title, $prefix_id, $post_id, $user_id, $username, $post_date ) = $db->query( $sql_r )->fetch( 3 );
		$last_page = $post_obj->all_post_page( $new_threadid, $per_page );
		
		$post_count = $db->query( "SELECT SUM(post_count) FROM  " . NV_PREFIXLANG . "_" . $module_data . "_thread_user_post WHERE thread_id=" . $thread_id )->fetchColumn();

		
		$sql2 = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_cat SET
			replycount=replycount + " . intval( $post_count ) . ",
			threadcount=threadcount + 1,
			last_post_date=" . intval( $post_date ) . ",
			last_post_username=" . $db->quote( $username ) . ",
			last_post_user_id=" . intval( $user_id ) . ",
			last_post_id=" . intval( $post_id ) . ",
			last_post_page=" . intval( $last_page ) . ",
			last_thread_title=" . $db->quote( $title ) . ",
			last_thread_id=" . intval( $new_threadid ) . ",
			last_prefix_id=" . intval( $prefix_id ) . "
			WHERE catid=" . $newcatid . " ";
		$db->query( $sql2 );
		//file_put_contents( NV_ROOTDIR . '/logs/sql2.log', "" . $sql_r . " \r\n", FILE_APPEND );

		unset( $last_page, $new_threadid, $title, $prefix_id, $post_id, $user_id, $username, $post_date );

		
		$sql_t = "SELECT A.thread_id, A.title, A.prefix_id, B.post_id, B.user_id, B.username, B.post_date
		FROM  " . NV_PREFIXLANG . "_" . $module_data . "_thread AS a
		LEFT JOIN  " . NV_PREFIXLANG . "_" . $module_data . "_post AS b ON A.thread_id = B.thread_id
		WHERE A.thread_id = ( 
		SELECT MAX( thread_id ) 
		FROM  " . NV_PREFIXLANG . "_" . $module_data . "_thread 
		WHERE catid =".$catid." ) 
		ORDER BY B.position DESC 
		LIMIT 0 , 1";
		
		list( $new_threadid, $title, $prefix_id, $post_id, $user_id, $username, $post_date ) = $db->query( $sql_t )->fetch( 3 );
		
		$last_page = $post_obj->all_post_page( $thread_id, $per_page );
		
		$sql1 = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_cat SET
			replycount=replycount - " . intval( $post_count ) . ",
			threadcount=threadcount - 1,
			last_post_date=" . intval( $post_date ) . ",
			last_post_username=" . $db->quote( $username ) . ",
			last_post_user_id=" . intval( $user_id ) . ",
			last_post_id=" . intval( $post_id ) . ",
			last_post_page=" . intval( $last_page ) . ",
			last_thread_title=" . $db->quote( $title ) . ",
			last_thread_id=" . intval( $new_threadid ) . ",
			last_prefix_id=" . intval( $prefix_id ) . "
		WHERE catid=" . $catid . " ";
		$db->query( $sql1 );

		$forum->del_file_cache( 'cat' );
		$forum->del_file_cache( 'lastest_post' );
		$forum->del_file_cache( 'lastest_post_topx' );
		unset ($sql_t, $sql1, $sql2, $sql_r);
		$succe['link'] = $nv_cat[$newcatid]['link'];
		$succe['message'] = "Di chuyển chủ đề thành công";
	}

}
else
{
	$error[] = "Lỗi: bạn không có quyền thực hiện thao tác này";
}

if( empty( $error ) )
{
	$info['data'] = array('message' => 'success', 'item' => $succe);
}
else
{
	$info['data'] = array('message' => 'unsuccess', 'item' => $error);
}
$forum->clear();
$thread_obj->clear();
$cat_obj->clear();
$post_obj->clear();

unset($post_obj, $nv_cat, $attach_obj, $forum, $thread_obj, $cat_obj, $arr_config, $array_cat_admin, $check_admin, $xtpl );


include NV_ROOTDIR . '/includes/header.php';
echo json_encode( $info );
include NV_ROOTDIR . '/includes/footer.php';