<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweB.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 22:18
 */

if( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

$json = array();

if( ! defined( 'NV_IS_USER' ) )
{
	/* Nhom khach truy cap => 5 */
	$permission_combination_id = 5;
	$result = $db_slave->query( 'SELECT cache_value FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id =' . intval( $permission_combination_id ) );
	$generalPermissions = $result->fetchColumn();
	$result->closeCursor();

	$generalPermissions = unserializePermissions( $generalPermissions );
}
else
{
	/* Nhom thanh vien truy cap => 4 */
	$permission_combination_id = $user_info['permission_combination_id'];
	$generalPermissions = $user_info['permissions'];
}

$data['node_id'] = $nv_Request->get_int( 'node_id', 'post', 0 );
$data['thread_id'] = $nv_Request->get_int( 'thread_id', 'post', 0 );
$data['watch_thread_email'] = $nv_Request->get_int( 'watch_thread_email', 'post', 0 );
$data['watch_thread_state'] = $nv_Request->get_int( 'watch_thread_state', 'post', 0 );
$data['watch_thread'] = $nv_Request->get_int( 'watch_thread', 'post', 0 );
$data['discussion_open'] = $nv_Request->get_int( 'discussion_open', 'post', 0 );
$data['last_date'] = $nv_Request->get_int( 'last_date', 'post', 0 );
$data['sticky'] = $nv_Request->get_int( 'sticky', 'post', 0 );
$data['token'] = nv_substr( $nv_Request->get_title( 'token', 'post', '', '' ), 0, 250 );
$data['attachment_hash'] = $nv_Request->get_string( 'attachment_hash', 'post', '' );
$data['message'] = $nv_Request->get_editor( 'message', 'post', NV_ALLOWED_HTML_TAGS );

$data['discussion_state'] = 'visible'; //moderated | deleted
$data['message_state'] = 'visible'; //moderated | deleted

$check_message = strip_tags( $data['message'], '<img>' );

if( empty( $check_message ) || nv_strlen( $data['message'] ) < 10 )
{

	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( array( 'error' => 'Tin nhắn phải từ 10 kí tự trở lên' ) );
	include NV_ROOTDIR . '/includes/footer.php';
}

$thread_read_date = NV_CURRENTTIME - 30 * 86400; // số ngày hiển thị chủ đề mới nhất > 30 ngày

$threadData = $db_slave->query( '
		SELECT thread.* ,
		IF(thread_read.thread_read_date > ' . intval( $thread_read_date ) . ', thread_read.thread_read_date, ' . intval( $thread_read_date ) . ') AS thread_read_date,
		IF(reply_ban.userid IS NULL, 0, IF(reply_ban.expiry_date IS NULL
												   OR reply_ban.expiry_date > ' . NV_CURRENTTIME . ', 1, 0)) AS thread_reply_banned
		FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_read AS thread_read ON (thread_read.thread_id = thread.thread_id
													AND thread_read.userid = ' . intval( $user_info['userid'] ) . ')
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_reply_ban AS reply_ban ON (reply_ban.thread_id = thread.thread_id
													   AND reply_ban.userid = ' . intval( $user_info['userid'] ) . ')
		WHERE thread.thread_id = ' . intval( $data['thread_id'] ) )->fetch();

$forum_read_date = NV_CURRENTTIME - 30 * 86400; // số ngày hiển thị chủ đề mới nhất > 30 ngày

$forumData = $db_slave->query( '
			SELECT node.*, forum.*,
				permission.cache_value AS node_permission_cache,
					IF(forum_read.forum_read_date > ' . intval( $forum_read_date ) . ', forum_read.forum_read_date, ' . intval( $forum_read_date ) . ') AS forum_read_date
			FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
					ON (permission.permission_combination_id = ' . intval( $user_info['permission_combination_id'] ) . '
						AND permission.content_type = \'node\'
						AND permission.content_id = forum.node_id)
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum_read AS forum_read ON
						(forum_read.node_id = forum.node_id
						AND forum_read.userid = ' . intval( $user_info['userid'] ) . ')
			WHERE node.node_id = ' . intval( $threadData['node_id'] ) )->fetch();

$nodePermissions = getNodePermissions( $threadData['node_id'], $permission_combination_id );

if( ! ModelThread_canReplyToThread( $threadData, $forumData, $errorLangKey, $nodePermissions ) )
{
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( array( 'error' => 'Bạn không có quyền tham gia thảo luận tại chủ đề này' ) );
	include NV_ROOTDIR . '/includes/footer.php';

}

// if( $capcha )
// {
// header( 'Content-Type: application/json' );
// include NV_ROOTDIR . '/includes/header.php';
// echo json_encode( array( 'Bạn không có quyền tham gia thảo luận tại chủ đề này' ) );
// include NV_ROOTDIR . '/includes/footer.php';

// }

$fetch_thread = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread WHERE thread_id = ' . $data['thread_id'] )->fetch();

$data['message'] = convertPostAttachment( $data['message'] );

$data['message'] = spamMessageCheck( $data['message'], $nodePermissions );

$data['message_state'] = ModelPost_getPostInsertMessageState( $threadData, $forumData, $nodePermissions );

$like_users = 'a:0:{}';
$position = $fetch_thread['reply_count'] + 1;
$sth = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_post SET 
				message=:message, 
				thread_id=' . intval( $data['thread_id'] ) . ', 
				userid=' . intval( $user_info['userid'] ) . ', 
				username=:username, 
				post_date=' . intval( NV_CURRENTTIME ) . ', 
				message_state=:message_state, 
				position= ' . intval( $position ) . ', 
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
$result_attachment = $db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_attachment SET
			content_type = \'post\',
			content_id = ' . intval( $data['post_id'] ) . ',
			temp_hash = \'\',
			unassociated = 0
			WHERE temp_hash = ' . $db->quote( $data['attachment_hash'] ) );
if( $result_attachment->rowCount() )
{
	$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET attach_count = 1  WHERE post_id=' . intval( $data['post_id'] ) );
}

$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET message_count = message_count + 1 WHERE userid = ' . intval( $user_info['userid'] ) );

$db->query( 'INSERT INTO  ' . NV_FORUM_GLOBALTABLE . '_thread_user_post
					(thread_id, userid, post_count)
				VALUES
					(' . intval( $data['thread_id'] ) . ', ' . intval( $user_info['userid'] ) . ', 1)
				ON DUPLICATE KEY UPDATE post_count = post_count + VALUES(post_count)' );

$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_thread SET 
		last_post_date = ' . NV_CURRENTTIME . ', 
		last_post_id = ' . intval( $data['post_id'] ) . ', 
		last_post_user_id = ' . intval( $user_info['userid'] ) . ', 
		last_post_username = ' . $db->quote( $user_info['username'] ) . ',
		reply_count = ' . ( $fetch_thread['reply_count'] + 1 ) . ' 
		WHERE thread_id = ' . intval( $data['thread_id'] ) );

$forum = $db_slave->query( '
				SELECT node.*, forum.*			
				FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
				INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)		
				WHERE node.node_id = ' . intval( $data['node_id'] ) )->fetch();

$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_forum SET 
			message_count = ' . intval( $forum['message_count'] + 1 ) . ', 
			last_post_date = ' . NV_CURRENTTIME . ',  
			last_post_id = ' . intval( $data['post_id'] ) . ', 
			last_post_user_id = ' . intval( $user_info['userid'] ) . ', 
			last_post_username = ' . $db->quote( $user_info['username'] ) . ' 
		WHERE node_id= ' . intval( $data['node_id'] ) );

// Tao thong bao cho nguoi dang ky nhan thong bao va email

$autoReadDate = NV_CURRENTTIME - 30 * 86400;

$result = $db_slave->query( 'SELECT user.*,
				thread_watch.email_subscribe,
				permission.cache_value AS node_permission_cache,
				GREATEST(COALESCE(thread_read.thread_read_date, 0), COALESCE(forum_read.forum_read_date, 0), ' . $autoReadDate . ') AS thread_read_date
			FROM ' . NV_FORUM_GLOBALTABLE . '_thread_watch AS thread_watch
			INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON
				(user.userid = thread_watch.userid AND user.user_state = \'valid\' AND user.is_banned = 0)
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
				ON (permission.permission_combination_id = user.permission_combination_id
					AND permission.content_type = \'node\'
					AND permission.content_id = ' . intval( $data['node_id'] ) . ' )
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_read AS thread_read
				ON (thread_read.thread_id = thread_watch.thread_id AND thread_read.userid = user.userid)
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum_read AS forum_read
				ON (forum_read.node_id = ' . intval( $data['node_id'] ) . ' AND forum_read.userid = user.userid)
			WHERE thread_watch.thread_id = ' . intval( $data['thread_id'] ) );

while( $item = $result->fetch() )
{
	//INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_mail_queue tao tien trinh tu dong gui email

	$db->query( 'INSERT INTO ' . NV_USERS_GLOBALTABLE . '_alert (alerted_userid, userid, username, content_type, content_id, action, extra_data, event_date) VALUES (' . intval( $item['userid'] ) . ', ' . intval( $user_info['userid'] ) . ', ' . $db->quote( $user_info['username'] ) . ', \'post\', ' . intval( $data['post_id'] ) . ', \'insert\', \'\', ' . NV_CURRENTTIME . ')' );

	$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET alerts_unread = alerts_unread + 1 WHERE userid = ' . intval( $item['userid'] ) . ' AND alerts_unread < 65535' );

}
$result->closeCursor();
// Tao thong bao cho nguoi dang ky nhan thong bao va email

//////////////
$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_draft WHERE draft_key = \'thread-' . intval( $data['thread_id'] ) . '\' AND userid = ' . intval( $user_info['userid'] ) );

$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_thread_read
				(userid, thread_id, thread_read_date)
			VALUES
				(' . intval( $user_info['userid'] ) . ', ' . intval( $data['thread_id'] ) . ', ' . NV_CURRENTTIME . ')
			ON DUPLICATE KEY UPDATE thread_read_date = VALUES(thread_read_date)' );

$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_forum_read
				(userid, node_id, forum_read_date)
			VALUES
				(' . intval( $user_info['userid'] ) . ', ' . intval( $data['node_id'] ) . ', ' . NV_CURRENTTIME . ')
			ON DUPLICATE KEY UPDATE forum_read_date = VALUES(forum_read_date)' );

if( $data['watch_thread_state'] )
{
	if( $data['watch_thread'] )
	{
		$watchState = ( $data['watch_thread_email'] ? 'watch_email' : 'watch_no_email' );
	}
	else
	{
		$watchState = '';
	}

	$threadWatch = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread_watch WHERE userid = ' . intval( $user_info['userid'] ) . ' AND thread_id = ' . intval( $data['thread_id'] ) )->fetch();

	if( $watchState == 'watch_email' || $watchState == 'watch_no_email' )
	{
		$email_subscribe = ( $watchState == 'watch_email' ? 1 : 0 );

		if( ! $threadWatch )
		{

			$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_thread_watch (userid, thread_id, email_subscribe) VALUES (' . intval( $user_info['userid'] ) . ', ' . intval( $data['thread_id'] ) . ', ' . intval( $email_subscribe ) . ')' );

		}
		else
		{

			$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_thread_watch  SET email_subscribe = ' . intval( $email_subscribe ) . ' WHERE userid = ' . intval( $threadWatch['userid'] ) . ' AND thread_id = ' . intval( $threadWatch['thread_id'] ) );
		}
	}
	elseif( $watchState == '' )
	{
		$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread_watch WHERE userid = ' . intval( $threadWatch['userid'] ) . ' AND thread_id = ' . intval( $threadWatch['thread_id'] ) );
	}

}
else
{
	$threadWatch = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread_watch WHERE userid = ' . intval( $user_info['userid'] ) . ' AND thread_id = ' . intval( $data['thread_id'] ) )->fetch();
	if( ! $threadWatch )
	{

		$watchState = $user_info['default_watch_state'];

		$threadWatch = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread_watch WHERE userid = ' . intval( $user_info['userid'] ) . ' AND thread_id = ' . intval( $data['thread_id'] ) )->fetch();

		if( $watchState == 'watch_email' || $watchState == 'watch_no_email' )
		{
			$email_subscribe = ( $watchState == 'watch_email' ? 1 : 0 );

			if( ! $threadWatch )
			{

				$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_thread_watch (userid, thread_id, email_subscribe) VALUES (' . intval( $user_info['userid'] ) . ', ' . intval( $data['thread_id'] ) . ', ' . intval( $email_subscribe ) . ')' );

			}
			else
			{

				$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_thread_watch  SET email_subscribe = ' . intval( $email_subscribe ) . ' WHERE userid = ' . intval( $threadWatch['userid'] ) . ' AND thread_id = ' . intval( $threadWatch['thread_id'] ) );
			}
		}
		elseif( $watchState == '' )
		{
			$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread_watch WHERE userid = ' . intval( $threadWatch['userid'] ) . ' AND thread_id = ' . intval( $threadWatch['thread_id'] ) );
		}
	}

}

$threadUpdateData = array();

if( ! empty( $data['discussion_open'] ) && ModelThread_canLockUnlockThread( $threadData, $forumData, $null, $nodePermissions ) )
{
	if( $threadData['discussion_open'] != $data['discussion_open'] )
	{
		$threadUpdateData['discussion_open'] = $data['discussion_open'];
	}
}

// discussion sticky state - moderator permission required
if( ! empty( $data['sticky'] ) && ModelForum_canStickUnstickThreadInForum( $forumData, $null, $nodePermissions ) )
{
	if( $threadData['sticky'] != $data['sticky'] )
	{
		$threadUpdateData['sticky'] = $data['sticky'];
	}
}

if( $threadUpdateData )
{
	$db->query( 'UPDATE FORM ' . NV_FORUM_GLOBALTABLE . '_thread SET discussion_open=' . $threadUpdateData['discussion_open'] . ', sticky=' . $threadUpdateData['sticky'] . ' WHERE thread_id=' . intval( $threadData['thread_id'] ) );
}
$canViewPost = ModelPost_canViewPost( $data, $threadData, $forumData, $null, $nodePermissions );

// this is a standard redirect
$noRedirect = 1;
if( ! $noRedirect || ! $data['last_date'] || ! $canViewPost )
{
	ModelThread_markThreadRead( $threadData, $forumData, NV_CURRENTTIME );

	if( ! $canViewPost )
	{
		$pages = floor( $threadData['reply_count'] + 1 ) / $per_page_thread + 1;
		//$return = buildPublicLink('threads', $threadData, array('page' => $pages, 'posted' => 1));
	}
	else
	{
		//$return =  buildPublicLink('posts', $post);
	}

	updateSessionActivity( $global_userid, 'AddReply', 'valid', array( 'thread_id' => $data['thread_id'] ) );

}
else
{
	// load a selection of posts that are newer than the last post viewed

	$dataContent = ModelThread_getNewPosts( $threadData, $forumData, $nodePermissions, $data['last_date'], 3 );

	updateSessionActivity( $global_userid, 'AddReply', 'valid', array( 'thread_id' => $data['thread_id'] ) );

	$json['template'] = ThemeReplyPostContent( $dataContent );

}

header( 'Content-Type: application/json' );
include NV_ROOTDIR . '/includes/header.php';
echo json_encode( $json );
include NV_ROOTDIR . '/includes/footer.php';
