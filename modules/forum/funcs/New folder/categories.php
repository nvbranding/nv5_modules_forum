<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweb.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 22:18
 */

if ( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

$page_title = $module_info['custom_title'];

$key_words = $module_info['keywords'];
 
$data =  array();
$permission_value =  array();
if ( !defined('NV_IS_USER') )
{	
	$result = $db->query('SELECT cache_value FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id =11');
	$permission_value = $result->fetchColumn();
	$result->closeCursor();
	
	if( empty( $permission_value ) )
	{
		// khach khong co quyen truy cap
		// co the tao giao dien dang nhap kem theo thong bao khach khong co quyen xem 
		// hoac chuyen thang den trang dang nhap
		Header('Location: ' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users', true));
		die();
	} 
	
	$permission_value = unserialize( $permission_value );
	
	$sql='SELECT thread.*
				,
				last_post_user.gender AS last_post_gender,
				last_post_user.photo AS last_post_photo_date,
				IF(last_post_user.username IS NULL, thread.last_post_username, last_post_user.username) AS last_post_username,
				node.title AS node_title, node.alias AS node_alias,
				forum.*,
				forum.last_post_id AS forum_last_post_id,
				forum.last_post_date AS forum_last_post_date,
				forum.last_post_user_id AS forum_last_post_user_id,
				forum.last_post_username AS forum_last_post_username,
				forum.last_thread_title AS forum_last_thread_title,
				thread.last_post_id,
				thread.last_post_date,
				thread.last_post_user_id,
				thread.last_post_username,
				NULL AS thread_read_date,
			permission.cache_value AS node_permission_cache
			FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread FORCE INDEX (last_post_date)		
				LEFT JOIN ' . NV_USERS_GLOBALTABLE .' AS last_post_user ON
					(last_post_user.userid = thread.last_post_user_id)
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON
					(node.node_id = thread.node_id)
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum AS forum ON
					(forum.node_id = thread.node_id)
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
				ON (permission.permission_combination_id = 11
					AND permission.content_type = \'node\'
					AND permission.content_id = thread.node_id)
			WHERE (thread.discussion_type <> \'redirect\') AND (thread.discussion_state IN (\'visible\')) AND (thread.last_post_date > 1449349369) AND (forum.find_new = 1)
			ORDER BY thread.last_post_date DESC
		 LIMIT 10';
	$result = $db->query($sql);
	
	while( $rows = $result->fetch() )
	{
		$data[] = $rows; 
	}


	$sql='SELECT content_id, cache_value
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content
			WHERE permission_combination_id = 11
			AND content_type = \'node\'';
			
	$sql='SELECT forum.*, NULL AS forum_read_date
			FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
			JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
			WHERE forum.node_id IN (2, 3, 4)';
	
		
			
}	

 

$contents = call_user_func( 'ThemeForumMain', $data, $permission_value );

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';