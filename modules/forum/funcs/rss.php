<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  7, 21, 2013 5:20
 */

if( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

$channel = array();
$items = array();

$channel['title'] = $module_info['custom_title'];
$channel['link'] = NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
$channel['description'] = ! empty( $module_info['description'] ) ? $module_info['description'] : $global_config['site_description'];

$node_id = 0;
if( isset( $array_op[1] ) )
{
	$alias_cat_url = $array_op[1];
	foreach( $forum_node as $node_id_i => $array_cat_i )
	{
		if( $alias_cat_url == $array_cat_i['alias'] )
		{
			$node_id = $node_id_i;
			break;
		}
	}
}

if( ! empty( $node_id ) )
{
	$channel['title'] = $module_info['custom_title'] . ' - ' . $forum_node[$node_id]['title'];
	$channel['link'] = NV_MY_DOMAIN . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $alias_cat_url;
	$channel['description'] = $forum_node[$node_id]['description'];
	
	$sql='SELECT thread.*
					,
					user.*, IF(user.username IS NULL, thread.username, user.username) AS username,
					post.message, post.attach_count,
					NULL AS thread_read_date,
					0 AS thread_is_watched,
					0 AS user_post_count
				FROM '. NV_FORUM_GLOBALTABLE .'_thread AS thread 				
					LEFT JOIN '. NV_USERS_GLOBALTABLE .' AS user ON
						(user.userid = thread.userid)
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_post AS post ON
						(post.post_id = thread.first_post_id)
				WHERE (thread.node_id = '. intval( $node_id ) .') AND (thread.discussion_state IN (\'visible\'))
				ORDER BY thread.last_post_date DESC
			 LIMIT 20';
	
}
else
{
	$sql='SELECT thread.*
					,
					user.*, IF(user.username IS NULL, thread.username, user.username) AS username,
					post.message, post.attach_count,
					NULL AS thread_read_date,
					0 AS thread_is_watched,
					0 AS user_post_count
				FROM '. NV_FORUM_GLOBALTABLE .'_thread AS thread 				
					LEFT JOIN '. NV_USERS_GLOBALTABLE .' AS user ON
						(user.userid = thread.userid)
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_post AS post ON
						(post.post_id = thread.first_post_id)
				WHERE (thread.discussion_state IN (\'visible\'))
				ORDER BY thread.last_post_date DESC
			 LIMIT 20';
}

if( $module_info['rss'] )
{
	$result = $db->query( $sql );
	while( $rows = $result->fetch( ) )
	{
 
		$items[] = array(
			'title' => $rows['title'],
			'link' => NV_MY_DOMAIN . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . strtolower( change_alias( $rows['title'] ) ) . '-' . $rows['thread_id'], true ), 
			'guid' => $module_name . '_' . $rows['thread_id'],  
			'description' => '',  
			'pubdate' => $rows['last_post_date'], 
			'author' => $rows['username']  
		);
	}
}
 
nv_rss_generate( $channel, $items );
die();