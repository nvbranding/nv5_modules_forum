<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweb.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 5, 2013 13:10
 */

if( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

if( ! defined( 'NV_IS_USER' ) )
{

	$redirect = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=content/' . $node_alias, true );
	Header( 'Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt( $redirect ) );
	die();
}
 
$nodePermissions = array();
if( ACTION_METHOD == 'CreateThread' )
{
	$data['node_id'] = $nv_Request->get_int( 'node_id', 'post', $node_id );

	$data['token'] = nv_substr( $nv_Request->get_title( 'token', 'post', '', '' ), 0, 250 );

	$data['title'] = nv_substr( $nv_Request->get_title( 'title', 'post', '', '' ), 0, 250 );

	$data['message'] = $nv_Request->get_editor( 'message', '', NV_ALLOWED_HTML_TAGS );

	$data['message'] = convertPostAttachment( $data['message'] );

	$data['attachment_hash'] = $nv_Request->get_string( 'attachment_hash', 'post', '' );

	$data['tags'] = $nv_Request->get_string( 'tags', 'post', '' );
	$data['tags'] = array_map( 'trim', explode( ',', $data['tags'] ) );
	$data['tags'] = array_unique( array_filter( $data['tags'] ) );

	$data['watch_thread'] = $nv_Request->get_int( 'watch_thread', 'post', 0 );
	$data['watch_thread_email'] = $nv_Request->get_int( 'watch_thread_email', 'post', 0 );
	$data['watch_thread_state'] = $nv_Request->get_int( 'watch_thread_state', 'post', 0 );
	$data['discussion_open'] = $nv_Request->get_int( 'discussion_open', 'post', 0 );
	$data['sticky'] = $nv_Request->get_int( 'sticky', 'post', 0 );
	$data['userid'] = $data['last_post_user_id'] = $user_info['userid'];
	$data['sticky'] = $nv_Request->get_int( 'sticky', 'post', 0 );
	$data['prefix_id'] = $nv_Request->get_int( 'prefix_id', 'post', 0 );
	$data['discussion_state'] = 'visible'; //moderated | deleted
	$data['message_state'] = 'visible'; //moderated | deleted
	$data['discussion_type'] = '';

	$sth = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_thread SET
		userid = ' . intval( $user_info['userid'] ) . ', 
		username=:username, 
		title=:title, 
		prefix_id=' . intval( $data['prefix_id'] ) . ', 
		node_id=' . intval( $data['node_id'] ) . ', 
		discussion_state=:discussion_state, 
		discussion_open=' . intval( $data['discussion_open'] ) . ', 
		sticky=' . intval( $data['sticky'] ) . ', 
		post_date=' . intval( NV_CURRENTTIME ) . ', 
		last_post_date=' . intval( NV_CURRENTTIME ) . ', 
		last_post_user_id=' . intval( $user_info['userid'] ) . ', 
		last_post_username=:last_post_username, 
		reply_count=0, 
		view_count=0, 
		discussion_type=:discussion_type, 
		first_post_id=0,  
		last_post_id=0,  
		tags=\'\', 
		first_post_likes=0' );
	$sth->bindParam( ':username', $user_info['username'], PDO::PARAM_STR );
	$sth->bindParam( ':title', $data['title'], PDO::PARAM_STR );
	$sth->bindParam( ':discussion_state', $data['discussion_state'], PDO::PARAM_STR );
	$sth->bindParam( ':last_post_username', $user_info['username'], PDO::PARAM_STR );
	$sth->bindParam( ':discussion_type', $data['discussion_type'] );
	//$sth->bindParam( ':tags', $tags, PDO::PARAM_STR, strlen( $tags ) );
	$sth->execute();
	$sth->closeCursor();
	$data['thread_id'] = $db->lastInsertId();
	if( $data['thread_id'] > 0 )
	{
		/* insert post */
		$like_users = 'a:0:{}';

		$sth = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_post SET 
			message=:message, 
			thread_id=' . intval( $data['thread_id'] ) . ', 
			userid=' . intval( $user_info['userid'] ) . ', 
			username=:username, 
			post_date=' . intval( NV_CURRENTTIME ) . ', 
			message_state=:message_state, 
			position=0, 
			last_edit_user_id=0, 
			ip_id=0, 
			attach_count=0, 
			likes=0,
			like_users=:like_users, 
			warning_id=0,
			warning_message=\'\', 
			last_edit_date=0, 
			edit_count=0' );

		$sth->bindParam( ':message', $data['message'], PDO::PARAM_STR, strlen( $data['message'] ) );
		$sth->bindParam( ':username', $user_info['username'], PDO::PARAM_STR );
		$sth->bindParam( ':message_state', $data['message_state'], PDO::PARAM_STR );
		$sth->bindParam( ':like_users', $like_users, PDO::PARAM_STR );
		$sth->execute();
		$sth->closeCursor();
		$data['post_id'] = $db->lastInsertId();

		/* insert ip */
		$data['ip_id'] = forumInsertLogs( $global_userid, $data['post_id'], 'post', 'insert' );

		/* update ip to post */
		$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET ip_id = ' . intval( $data['ip_id'] ) . ' WHERE post_id = ' . intval( $data['post_id'] ) );

		/* update attachment */
		$result = $db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_attachment SET
		content_type = \'post\',
		content_id = ' . intval( $data['post_id'] ) . ',
		temp_hash = \'\',
		unassociated = 0
		WHERE temp_hash = ' . $db->quote( $data['attachment_hash'] ) );
		if( $result->rowCount() )
		{
			$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET attach_count = 1 WHERE post_id = ' . intval( $data['post_id'] ) );
		}

		$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET message_count = message_count + 1 WHERE userid = ' . intval( $user_info['userid'] ) );

		 
		$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_thread_user_post
					(thread_id, userid, post_count)
				VALUES
					(' . intval( $data['thread_id'] ) . ', ' . intval( $user_info['userid'] ) . ', 1)
				ON DUPLICATE KEY UPDATE post_count = post_count + VALUES(post_count)' );

		$thread_array = $db->query( 'SELECT thread.* FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread WHERE thread.thread_id = ' . intval( $data['thread_id'] ) )->fetch();

		$thread_node_array = $db->query( 'SELECT thread.*, node.title AS node_title, node.alias as node_alias
				FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON
					(node.node_id = thread.node_id)
				WHERE thread.thread_id = ' . intval( $data['thread_id'] ) )->fetch();

		$user_data = $db->query( 'SELECT user.* FROM ' . NV_USERS_GLOBALTABLE . '  AS user WHERE user.userid = ' . $user_info['userid'] )->fetch();

		// $db->query( 'SELECT user.*,
		// user_option.*,
		// user_profile.*,
		// forum_watch.notify_on,
		// forum_watch.send_alert,
		// forum_watch.send_email,
		// permission.cache_value AS nodePermissions,
		// GREATEST(COALESCE(thread_read.thread_read_date, 0), COALESCE(forum_read.forum_read_date, 0), ' . NV_CURRENTTIME . ') AS read_date
		// FROM ' . NV_FORUM_GLOBALTABLE . '_forum_watch AS forum_watch
		// INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON
		// (user.userid = forum_watch.userid AND user.user_state = 'valid' AND user.is_banned = 0 AND user.last_login >= ' . NV_CURRENTTIME . ')
		// INNER JOIN ' . NV_USERS_GLOBALTABLE . '_option AS user_option ON
		// (user_option.userid = user.userid)
		// INNER JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON
		// (user_profile.userid = user.userid)
		// LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
		// ON (permission.permission_combination_id = user.permission_combination_id
		// AND permission.content_type = 'node'
		// AND permission.content_id = forum_watch.node_id)
		// LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum_read AS forum_read
		// ON (forum_read.node_id = forum_watch.node_id AND forum_read.userid = user.userid)
		// LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_read AS thread_read
		// ON (thread_read.thread_id = ' . intval( $data['thread_id'] ) . ' AND thread_read.userid = user.userid)
		// WHERE forum_watch.node_id = ' . intval( $data['node_id'] ) . '
		// AND forum_watch.notify_on IN ('thread', 'message')
		// AND (forum_watch.send_alert <> 0 OR forum_watch.send_email <> 0)' );

		$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_thread SET first_post_id = ' . intval( $data['post_id'] ) . ', last_post_id = ' . intval( $data['post_id'] ) . ' WHERE thread_id = ' . intval( $data['thread_id'] ) );

		$node_forum = $db->query( 'SELECT node.*, forum.*		
			FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)		
			WHERE node.node_id = ' . intval( $data['node_id'] ) )->fetch();

		$discussion_count = $node_forum['discussion_count'] + 1;
		$message_count = $node_forum['message_count'] + 1;

		$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_forum SET 
			discussion_count = ' . intval( $discussion_count ) . ', 
			message_count = ' . intval( $message_count ) . ',
			last_post_date = ' . NV_CURRENTTIME . ', 
			last_post_id = ' . intval( $data['post_id'] ) . ', 
			last_post_user_id = ' . intval( $user_info['userid'] ) . ', 
			last_post_username = ' . $db->quote( $user_info['username'] ) . ', 
			last_thread_title = ' . $db->quote( $data['title'] ) . ' 
			WHERE node_id = ' . intval( $data['node_id'] ) );

		$array_tags_id = array();
		if( ! empty( $data['tags'] ) )
		{
			foreach( $data['tags'] as $tag )
			{

				$check_exist_tag = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_tag WHERE tag = ' . $db->quote( $tag ) )->fetch();

				if( empty( $check_exist_tag ) )
				{
					$tag_url = change_alias( $tag ) . '-' . $data['thread_id'];

					$check_exist_url = $db->query( 'SELECT *
						FROM ' . NV_FORUM_GLOBALTABLE . '_tag
						WHERE tag_url = ' . $db->quote( $tag_url ) . '
							OR (tag_url LIKE ' . $db->quote( $tag_url ) . ' AND tag_url REGEXP \'^' . $tag_url . '-[0-9]+$\')
						ORDER BY tag_id DESC
						LIMIT 1' )->fetch();

					
					$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_tag (tag, tag_url, use_count, last_use_date, permanent) VALUES 
					(' . $db->quote( $tag ) . ', ' . $db->quote( $tag_url ) . ', 0, 0, 0)' );

					$array_tags_id[] = $db->lastInsertId();

				}
				else
				{
					$array_tags_id[] = $check_exist_tag['tag_id'];
				}

			}
		}

		$array_thread = $db->query( 'SELECT thread.* FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread WHERE thread.thread_id = ' . intval( $data['thread_id'] ) )->fetch();

		if( ! empty( $array_thread ) && ! empty( $array_tags_id ) )
		{
			foreach( $array_tags_id as $tag_id )
			{
				$db->query( '
					INSERT IGNORE INTO ' . NV_FORUM_GLOBALTABLE . '_tag_content
						(content_type, content_id, tag_id, add_user_id, add_date, content_date, visible)
					VALUES
						(\'thread\', ' . intval( $array_thread['thread_id'] ) . ', ' . intval( $tag_id ) . ', ' . intval( $array_thread['userid'] ) . ', ' . NV_CURRENTTIME . ', ' . intval( $array_thread['post_date'] ) . ', 1)' );

				$db->query( '
					UPDATE ' . NV_FORUM_GLOBALTABLE . '_tag
					SET use_count = use_count + 1, last_use_date = ' . NV_CURRENTTIME . '
					WHERE tag_id = ' . intval( $tag_id ) );
			}

			$result = $db->query( 'SELECT tag_content.*, tag.*
					FROM ' . NV_FORUM_GLOBALTABLE . '_tag_content AS tag_content
					INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_tag AS tag ON (tag.tag_id = tag_content.tag_id)
					WHERE tag_content.content_type = \'thread\'
						AND tag_content.content_id = ' . intval( $array_thread['thread_id'] ) . '
					ORDER BY tag.tag' );
			$array_tags = array();
			while( $rows = $result->fetch() )
			{
				$array_tags[$rows['tag_id']] = array( 'tag' => $rows['tag'], 'tag_url' => $rows['tag_url'] );
			}
			if( ! empty( $array_tags ) )
			{
				$tags_content = serialize( $array_tags );

				$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_thread SET tags = ' . $db->quote( $tags_content ) . ' WHERE thread_id = ' . intval( $array_thread['thread_id'] ) );
			}

			// $result = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_tag WHERE tag IN ( ' . implode( ',', array_map( 'add_quotes', $data['tags'] ) ) . ')' );
			// $array_tags_old = array();
			// while( $rows = $result->fetch() )
			// {
			// $array_tags_old[$rows['tag_id']] = array( 'tag'=> $rows['tag'], 'tag_url'=> $rows['tag_url'] );
			// }

			// $db->query('
			// SELECT node.*, forum.*
			// FROM '. NV_FORUM_GLOBALTABLE .'_forum AS forum
			// INNER JOIN '. NV_FORUM_GLOBALTABLE .'_node AS node ON (node.node_id = forum.node_id)
			// WHERE node.node_id = '. intval( $data['node_id'] ) )->fetch();

			// $db->query('
			// SELECT post.*
			// FROM '. NV_FORUM_GLOBALTABLE .'_post AS post
			// WHERE post.post_id = '. intval( $data['post_id'] ) );

			$draft_key = $forum_node[$data['node_id']]['node_type_id'] . '-' . $data['node_id'];

			$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_draft WHERE draft_key = ' . $db->quote( $draft_key ) . ' AND userid = ' . intval( $user_info['userid'] ) );

			$thread_watch = $db->query( '
				SELECT *
				FROM ' . NV_FORUM_GLOBALTABLE . '_thread_watch
				WHERE userid = ' . intval( $user_info['userid'] ) . '
					AND thread_id = ' . intval( $array_thread['thread_id'] ) )->fetch();
			if( empty( $thread_watch ) )
			{
				$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_thread_watch (userid, thread_id, email_subscribe) VALUES (' . intval( $user_info['userid'] ) . ', ' . intval( $array_thread['thread_id'] ) . ', ' . intval( $data['watch_thread_email'] ) . ')' );
			}

			$thread_read_date = $db->query( 'SELECT thread_read_date
					FROM ' . NV_FORUM_GLOBALTABLE . '_thread_read
					WHERE userid = ' . intval( $user_info['userid'] ) . '
						AND thread_id = ' . intval( $array_thread['thread_id'] ) )->fetchColumn();

			$thread_read_date = ! empty( $thread_read_date ) ? $thread_read_date : NV_CURRENTTIME;

			$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_thread_read
						(userid, thread_id, thread_read_date)
					VALUES
						(' . intval( $user_info['userid'] ) . ', ' . intval( $array_thread['thread_id'] ) . ', ' . intval( $thread_read_date ) . ')
					ON DUPLICATE KEY UPDATE thread_read_date = VALUES(thread_read_date)' );

			$forum_read_date = $db->query( 'SELECT forum_read_date
					FROM ' . NV_FORUM_GLOBALTABLE . '_forum_read
					WHERE userid = ' . intval( $user_info['userid'] ) . '
						AND node_id = ' . intval( $data['node_id'] ) )->fetchColumn();

			$forum_read_date = ! empty( $forum_read_date ) ? $forum_read_date : NV_CURRENTTIME;

			$count_thread_read = $db->query( 'SELECT COUNT(*)
					FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_read AS thread_read ON
						(thread_read.thread_id = thread.thread_id AND thread_read.userid = ' . intval( $user_info['userid'] ) . ')
					WHERE thread.node_id =  ' . intval( $data['node_id'] ) . '
						AND thread.last_post_date > ' . NV_CURRENTTIME . '
						AND (thread_read.thread_id IS NULL OR thread.last_post_date > thread_read.thread_read_date)	
						AND thread.discussion_state = \'visible\'
						AND thread.discussion_type <> \'redirect\'' )->fetchColumn();

			$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_forum_read
						(userid, node_id, forum_read_date)
					VALUES
						(' . intval( $user_info['userid'] ) . ', ' . intval( $data['node_id'] ) . ', ' . intval( $forum_read_date ) . ')
					ON DUPLICATE KEY UPDATE forum_read_date = VALUES(forum_read_date)' );

		}
		$json['success'] = 'Tạo chủ đề thành công';

		$thread_alias = strtolower( change_alias( $data['title'] ) );

		$json['redirect'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . $thread_alias . '-' . $data['thread_id'] . $global_config['rewrite_exturl'], true );

	}
	else
	{

		$json['error'] = 'Lỗi không tạo được chủ đề';

	}
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';
}
elseif( ACTION_METHOD == 'SaveDraft' )
{
	$json = array();
	$data['node_id'] = $nv_Request->get_int( 'node_id', 'post', $node_id );

	$data['token'] = nv_substr( $nv_Request->get_title( 'token', 'post', '', '' ), 0, 250 );

	$data['title'] = nv_substr( $nv_Request->get_title( 'title', 'post', '', '' ), 0, 250 );

	$data['message'] = $nv_Request->get_editor( 'message', '', NV_ALLOWED_HTML_TAGS );

	$data['tags'] = $nv_Request->get_string( 'tags', 'post', '' );
	$data['tags'] = array_map( 'trim', explode( ',', $data['tags'] ) );
	$data['tags'] = array_unique( array_filter( $data['tags'] ) );

	$data['watch_thread'] = $nv_Request->get_int( 'watch_thread', 'post', 0 );
	$data['watch_thread_email'] = $nv_Request->get_int( 'watch_thread_email', 'post', 0 );
	$data['watch_thread_state'] = $nv_Request->get_int( 'watch_thread_state', 'post', 0 );
	$data['discussion_open'] = $nv_Request->get_int( 'discussion_open', 'post', 0 );
	$data['sticky'] = $nv_Request->get_int( 'sticky', 'post', 0 );
	$data['userid'] = $data['last_post_user_id'] = $user_info['userid'];

	$data['prefix_id'] = $nv_Request->get_int( 'prefix_id', 'post', 0 );
	$data['discussion_state'] = 'visible'; //moderated | deleted
	$data['message_state'] = 'visible'; //moderated | deleted
	$data['discussion_type'] = '';

	$draft_key = $forum_node[$data['node_id']]['node_type_id'] . '-' . $data['node_id'];

	$check_draft = $db->query( 'SELECT COUNT(*) FROM ' . NV_FORUM_GLOBALTABLE . '_draft WHERE draft_key = ' . $db->quote( $draft_key ) . ' AND userid = ' . intval( $user_info['userid'] ) )->fetchColumn();

	try
	{
		$extra_data = serialize( array(
			'title' => $data['title'],
			'prefix_id' => $data['prefix_id'],
			'tags' => $data['tags'],
			'watch_thread_state' => $data['watch_thread_state'],
			'watch_thread' => $data['watch_thread'],
			'watch_thread_email' => $data['watch_thread_email'],
			'discussion_open' => $data['discussion_open'],
			'sticky' => $data['sticky'] ) );

		if( empty( $check_draft ) )
		{
			$stmt = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_draft SET 
			userid =' . intval( $user_info['userid'] ) . ',
			last_update =' . NV_CURRENTTIME . ',
			draft_key =:draft_key,
			message =:message,
			extra_data=:extra_data' );

			$stmt->bindParam( ':draft_key', $draft_key, PDO::PARAM_STR );
			$stmt->bindParam( ':message', $data['message'], PDO::PARAM_STR, strlen( $data['message'] ) );
			$stmt->bindParam( ':extra_data', $extra_data, PDO::PARAM_STR, strlen( $extra_data ) );
			$stmt->execute();
		}
		else
		{
			$stmt = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_draft SET 
			last_update =' . NV_CURRENTTIME . ',
			message =:message,
			extra_data=:extra_data 
			WHERE draft_key=:draft_key AND userid =' . intval( $user_info['userid'] ) );

			$stmt->bindParam( ':draft_key', $draft_key, PDO::PARAM_STR );
			$stmt->bindParam( ':message', $data['message'], PDO::PARAM_STR, strlen( $data['message'] ) );
			$stmt->bindParam( ':extra_data', $extra_data, PDO::PARAM_STR, strlen( $extra_data ) );
			$stmt->execute();
		}

		$json['success'] = 'Lưu chủ đề thành công';
	}
	catch ( PDOException $e )
	{
		$json['error'] = 'Lỗi không lưu được chủ đề';
		//var_dump($e);die('ok');
	}

	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';
}
elseif( ACTION_METHOD == 'GetTag' )
{
	$json = array();
	$tag = $nv_Request->get_title( 'tag', 'get', '' );

	if( ! empty( $tag ) )
	{
		$db->sqlreset()->select( '*' )->from( NV_FORUM_GLOBALTABLE . '_tag' )->where( 'tag LIKE :tag AND (use_count > 0 OR permanent = 1)' )->order( 'tag ASC' )->limit( '10' );

		$sth = $db->prepare( $db->sql() );
		$sth->bindValue( ':tag', '' . $tag . '%' );
		$sth->execute();

		while( $rows = $sth->fetch() )
		{
			$json[] = $rows;
		}
	}

	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';
}
else
{

	$count_op = sizeof( $array_op );
	if( $count_op == 3 && in_array( $array_op[2], array( 'create-thread' ) ) )
	{
		$array_page = explode( '-', $array_op[1] );
		$node_id = intval( end( $array_page ) );

		$parent_id = $node_id;
		while( $parent_id > 0 )
		{
			$array_cat_i = $forum_node[$parent_id];
			$array_cat_i['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $array_cat_i['alias'];
			$array_mod_title[] = array(
				'catid' => $parent_id,
				'title' => $array_cat_i['title'],
				'link' => $array_cat_i['link'] );
			$parent_id = $array_cat_i['parent_id'];
		}
		sort( $array_mod_title, SORT_NUMERIC );

		$result = $db->query( 'SELECT node.*, forum.*,
				permission.cache_value AS nodePermissions
			FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
					ON (permission.permission_combination_id = ' . intval( $user_info['permission_combination_id'] ) . '
						AND permission.content_type = \'node\'
						AND permission.content_id = forum.node_id)
			WHERE node.node_id = ' . intval( $node_id ) );

		$forumData = $result->fetch();

		$result->closeCursor();

		$nodePermissions = unserializePermissions( $forumData['nodepermissions'] );

		$global_permission_cache = ! empty( $user_info['global_permission_cache'] ) ? unserializePermissions( $user_info['global_permission_cache'] ) : '';

		if( ! hasContentPermission( $nodePermissions, 'postThread' ) )
		{
			ThemeErrorPermission( 'postThread' );
		}
		$draft_key = $forum_node[$node_id]['node_type_id'] . '-' . $node_id;

		$draft = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_draft WHERE draft_key = ' . $db->quote( $draft_key ) . ' AND userid = ' . intval( $user_info['userid'] ) )->fetch();

		if( ! empty( $draft ) )
		{
			$extra_data = unserializePermissions( $draft['extra_data'] );
			$data['message'] = $draft['message'];
			$data['title'] = $extra_data['title'];
			$data['tags'] = $extra_data['tags'];
			$data['watch_thread'] = $extra_data['watch_thread'];
			$data['watch_thread_email'] = $extra_data['watch_thread_email'];
			$data['watch_thread_state'] = $extra_data['watch_thread_state'];
			$data['discussion_open'] = $extra_data['discussion_open'];
			$data['sticky'] = $extra_data['sticky'];
		}
		else
		{
			$data['title'] = '';
			$data['message'] = '';
			$data['tags'] = '';
			$data['watch_thread'] = 1;
			$data['watch_thread_email'] = 0;
			$data['discussion_open'] = 1;
			$data['watch_thread_state'] = 1;
			$data['sticky'] = 0;
		}

		$data['node_id'] = $node_id;
		$data['token'] = md5( $nv_Request->session_id . $global_config['sitekey'] . $data['node_id'] . $user_info['userid'] );
		$data['watch_thread_checked'] = ( $data['watch_thread'] == 1 ) ? 'checked="checked"' : '';
		$data['watch_thread_email_checked'] = ( $data['watch_thread'] == 1 && $data['watch_thread_email'] == 1 ) ? 'checked="checked"' : '';
		$data['watch_thread_email_disabled'] = ( $data['watch_thread'] == 1 ) ? '' : 'disabled="disabled"';
		$data['discussion_open_checked'] = ( $data['discussion_open'] == 1 ) ? 'checked="checked"' : '';
		$data['sticky_checked'] = ( $data['sticky'] == 1 ) ? 'checked="checked"' : '';
		$data['message'] = getForumEditor( 'message', $data['message'], $width = '100%', $height = '300px' );

		updateSessionActivity( $global_userid, 'CreateThread', 'valid', array( 'node_id' => $node_id ) );

		$contents = ThemeForumContent( $data );

		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';

	}
	else
	{

		updateSessionActivity( $global_userid, 'CreateThread', 'error', array( 'node_id' => $node_id ) );

		$contents = ThemeErrorPermission();
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}

}

Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true ) );
die();
