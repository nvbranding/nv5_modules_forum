<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweb.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 22:18
 */

if ( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

unset( $matches );
if ( isset( $array_op[0] ) and preg_match( "/^([a-zA-Z0-9\-\_]+)\-([\d]+)$/", $array_op[0], $matches ) and isset( $nv_cat[$matches[2]] ) and $nv_cat[$matches[2]]['alias'] == $matches[0] || ( isset( $array_op[1] ) and substr( $array_op[1], 0, 5 ) == "page-" ) )
{
	
	$catid = (int)$matches[2];
	
	$viewcat = $nv_cat[$catid]['viewcat'];
	
	$page_title = ( ! empty( $nv_cat[$catid]['titlesite'] ) ) ? $nv_cat[$catid]['titlesite'] : $nv_cat[$catid]['title'];
	$key_words = $nv_cat[$catid]['keywords'];
	$description = $nv_cat[$catid]['description'];
	
	
	$result_all = $db->query( "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_thread WHERE catid=".$catid."" );
	$all_page = $result_all->fetchColumn();
	$_users = array();
	$user_array = array();
	$thread_array = array();
	$array_catpage = array();
	if( $all_page > 0 )
	{
		$order_by = ( $arr_config['indexfile'] == "viewcat_list_new" ) ? "ORDER BY last_post_date DESC" : "ORDER BY last_post_date ASC";
		$sql = "SELECT thread_id, catid, title, reply_count, view_count, user_id, username, post_date, sticky, discussion_state, discussion_open, discussion_type, first_post_id, first_post_likes, last_post_date, last_post_id, last_post_page, last_post_user_id, last_post_username, prefix_id FROM " . NV_PREFIXLANG . "_" . $module_data . "_thread  WHERE catid=".$catid." " . $order_by . " LIMIT  " . ( $page - 1 ) * $per_page . "," . $per_page;
		$result = $db->query( $sql );

		while ( $item = $result->fetch() )
		{
			if ( $item['last_post_page'] > 1 ) $item['last_link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $item['title'] ) . "/" . $item['thread_id'] . "/page-" . $item['last_post_page'], true );
			else  $item['last_link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $item['title'] ) . "/" . $item['thread_id'], true );
			$item['link'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $item['title'] ) . "/" . $item['thread_id'];

			$array_catpage[$item['thread_id']] = $item;
			$user_array[] = $item['user_id'];
			$user_array[] = $item['last_post_user_id'];
			$thread_array[] = $item['thread_id'];

		}
		$sql4 = "SELECT thread_id, COUNT(*) as all_page FROM " . NV_PREFIXLANG . "_" . $module_data . "_post WHERE thread_id IN (" . implode( ',', $thread_array ) . ") GROUP BY thread_id";
		$result = $db->query( $sql4 );
		while ( $item1 = $result->fetch() )
		{
			$array_catpage[$item1['thread_id']]['all_page'] = $item1['all_page'];
		}

		$user_array = array_unique( $user_array );

		$sql2 = "SELECT userid, username, full_name, photo FROM " . NV_USERS_GLOBALTABLE . " WHERE userid IN (" . implode( ',', $user_array ) . ")";
		$result2 = $db->query( $sql2 );
		
		while ( $loop = $result2->fetch() )
		{
			$_users[$loop['userid']] = $loop;
		}
	}
	
	$base_url = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name;

	$generate_page = nv_alias_page( $page_title, $base_url, $all_page, $per_page, $page );
	$contents = call_user_func( $viewcat, $array_catpage, $catid, $_users, $generate_page );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}
else
{
	Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
	exit();
}