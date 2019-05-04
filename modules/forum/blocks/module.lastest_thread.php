<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */

if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

// $sql='
	// SELECT thread.*,
	// last_post_user.gender AS last_post_gender,
	// last_post_user.photo AS last_post_photo,
	// IF(last_post_user.username IS NULL, thread.last_post_username, last_post_user.username) AS last_post_username,
	// node.title AS node_title,
	// node.alias,
	// forum.*,
	// forum.last_post_id AS forum_last_post_id,
	// forum.last_post_date AS forum_last_post_date,
	// forum.last_post_user_id AS forum_last_post_user_id,
	// forum.last_post_username AS forum_last_post_username,
	// forum.last_thread_title AS forum_last_thread_title,
	// thread.last_post_id,
	// thread.last_post_date,
	// thread.last_post_user_id,
	// thread.last_post_username,
	// NULL AS thread_read_date,
	// permission.cache_value AS node_permission_cache
	// FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
	// FORCE INDEX (last_post_date)
	// LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS last_post_user ON (last_post_user.userid = thread.last_post_user_id)
	// LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = thread.node_id)
	// LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum AS forum ON (forum.node_id = thread.node_id)
	// LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission ON (permission.permission_combination_id = '. intval( $permission_combination_id ) .'
	// AND permission.content_type = \'node\'
	// AND permission.content_id = thread.node_id)
	// WHERE (thread.discussion_type <> \'redirect\')
	// AND (thread.discussion_state IN (\'visible\'))
	// AND (thread.last_post_date > '. intval( $last_post_date ) .')
	// AND (forum.find_new = 1)
	// ORDER BY thread.last_post_date DESC LIMIT 10';

	// $result = $db->query($sql);

	// while( $rows = $result->fetch() )
	// {
	// $get_latest_thread[] = $rows;
	// }