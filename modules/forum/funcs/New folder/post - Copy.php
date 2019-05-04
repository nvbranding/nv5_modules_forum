<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweb.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 5, 2013 13:10
 */

if( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );
$ActionMethod = '';
$post_id = 0;
 
if( sizeof( $array_op ) > 2 )
{
	$post_id = intval( $array_op[1] );
	$ActionMethod = $array_op[2];
}

if( ! defined( 'NV_IS_USER' ) )
{
	/* Nhom khach truy cap => 6 */
	$permission_combination_id = 6;
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


if( $ActionMethod == 'view' )
{
	
	$postData = $db->query('SELECT post.* FROM '. NV_FORUM_GLOBALTABLE .'_post AS post WHERE post.post_id = '. intval( $post_id ) )->fetch();
 
	if ( ! $postData )
	{
		$contents = ThemeErrorNotFoundPost();
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php'; 
	}
	
	
	$threadData = $db->query('SELECT thread.* ,0 AS thread_reply_banned FROM '. NV_FORUM_GLOBALTABLE .'_thread AS thread WHERE thread.thread_id = '. $postData['thread_id'] )->fetch();
	if( ! $threadData )
	{
		$contents = ThemeErrorNotFoundThread();
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	
	$forumData = $db->query('
		SELECT node.*, forum.*
			,
			permission.cache_value AS node_permission_cache
		FROM '. NV_FORUM_GLOBALTABLE .'_forum AS forum
		INNER JOIN '. NV_FORUM_GLOBALTABLE .'_node AS node ON (node.node_id = forum.node_id)	
			LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_permission_cache_content AS permission
				ON (permission.permission_combination_id = '. $permission_combination_id .'
					AND permission.content_type = \'node\'
					AND permission.content_id = forum.node_id)
		WHERE node.node_id = '. intval( $threadData['node_id'] ) )->fetch();
	
	if (! $forumData )
	{
		$contents = ThemeErrorNotFoundForum();
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	} 
	$nodePermissions = array();
	if (isset($forumData['node_permission_cache']))
	{
		$nodePermissions = unserializePermissions ( $forumData['node_permission_cache'] );
		unset($forumData['node_permission_cache']);
	}
 
	if (! ModelForum_canViewForum( $forumData, $errorLangKey, $nodePermissions))
	{	
		$contents = ThemeErrorOrNoPermission($errorLangKey);
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	
	if (!ModelThread_canViewThread($threadData, $forumData, $errorPhraseKey, $nodePermissions ))
	{
		$contents = ThemeErrorOrNoPermission($errorLangKey);
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	$threadData = ModelThread_prepareThread($threadData, $forumData, $nodePermissions);

	if (!ModelPost_canViewPost($postData, $threadData, $forumData, $errorLangKey, $nodePermissions))
	{
		$contents = ThemeErrorOrNoPermission($errorLangKey);
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	$postData = ModelPost_preparePost($postData, $threadData, $forumData, $nodePermissions);
	// $viewParams = array(
		// 'post' => $postData,
		// 'thread' => $threadData,
		// 'forum' => $forumData 
		// 'canSilentEdit' => ModelPost_canControlSilentEdit($postData, $threadData, $forumData)
	// );

	$post_items = $threadData['reply_count'] + 1;
	$threadData['alias'] = strtolower( change_alias( $threadData['title'] ) );
	if( $post_items > $per_page_post )
	{

		$threadData['next_page'] = ceil( $post_items / $per_page_post );
		Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . $threadData['alias'] . '-' . $threadData['thread_id'] . '/page-' . $threadData['next_page'] . $global_config['rewrite_exturl'], true ) . '#post-' . $postData['post_id'] );
		die();
	}
	else
	{
		Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . $threadData['alias'] . '-' . $threadData['thread_id'] . $global_config['rewrite_exturl'], true ) . '#post-' . $postData['post_id'] );
		die();
	}
 

}
elseif( $ActionMethod == 'delete' )
{

	function canLockUnlockThread( array $nodePermissions = null )
	{
		global $global_userid;

		return ( $global_userid && hasContentPermission( $nodePermissions, 'lockUnlockThread' ) );
	}
	function canDeleteThread( array $thread, array $forum, $deleteType = 'soft', &$errorLangKey = '', array $nodePermissions = null )
	{
		global $global_userid;

		if( ! $global_userid )
		{
			return false;
		}

		if( $deleteType != 'soft' && ! hasContentPermission( $nodePermissions, 'hardDeleteAnyThread' ) )
		{
			// fail immediately on hard delete without permission
			return false;
		}

		if( ! $thread['discussion_open'] && ! canLockUnlockThread( $nodePermissions ) )
		{
			$errorLangKey = 'Bạn không có quyền thực hiện thao tác này vì chức năng thảo luận đã bị đóng';

			return false;
		}

		if( hasContentPermission( $nodePermissions, 'deleteAnyThread' ) )
		{
			return true;
		}
		elseif( $thread['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'deleteOwnThread' ) )
		{
			$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );

			if( $editLimit != -1 && ( ! $editLimit || $thread['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
			{
				$errorLangKey = array( 'message_edit_time_limit_expired', 'minutes' => $editLimit );
				return false;
			}

			if( empty( $forum['allow_posting'] ) )
			{
				return false;
			}

			return true;
		}

		return false;
	}
	function canDeletePost( array $post, array $thread, array $forum, $deleteType = 'soft', &$errorLangKey = '', array $nodePermissions = null )
	{
		global $global_userid;

		if( ! $global_userid )
		{
			return false;
		}

		if( $deleteType != 'soft' && ! hasContentPermission( $nodePermissions, 'hardDeleteAnyPost' ) )
		{
			// fail immediately on hard delete without permission
			return false;
		}

		if( ! $thread['discussion_open'] && ! canLockUnlockThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
		{
			$errorLangKey = 'Bạn không có quyền thực hiện thao tác này vì chức năng thảo luận đã bị đóng';
			return false;
		}

		if( $post['post_id'] == $thread['first_post_id'] )
		{
			// would delete thread, so use that permission
			return canDeleteThread( $thread, $forum, $deleteType, $errorLangKey, $nodePermissions );
		}
		else
			if( hasContentPermission( $nodePermissions, 'deleteAnyPost' ) )
			{
				return true;
			}
			else
				if( $post['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'deleteOwnPost' ) )
				{
					$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );

					if( $editLimit != -1 && ( ! $editLimit || $post['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
					{
						$errorLangKey = 'Đã hết thời gian sửa tin nhắn. Thời gian sửa là ' . $editLimit . ' phút';
						return false;
					}

					if( empty( $forum['allow_posting'] ) )
					{
						$errorLangKey = 'Bạn không có quyền thực hiện thao tác này vì diễn đàn không cho phép thảo luận';
						return false;
					}

					return true;
				}

		return false;
	}

	if( $nv_Request->get_title( 'token', 'post', '', '' ) == md5( session_id() . $global_config['sitekey'] . $post_id ) )
	{
		$hardDelete = $nv_Request->get_int( 'hard_delete', 'post', 0 );

		$deleteType = ( $hardDelete ) ? 'hard' : 'soft';

		$reason = $nv_Request->get_string( 'reason', 'post', '' );
		$send_author_alert = $nv_Request->get_int( 'send_author_alert', 'post', 0 );
		$author_alert_reason = $nv_Request->get_string( 'author_alert_reason', 'post', '' );

		$json['delete'] = true;

		if( empty( $global_userid ) )
		{
			getOutputJson( array( 'template' => ThemeForumLogin() ) );
		}
		$permission_combination_id = $user_info['permission_combination_id'];
		$generalPermissions = unserializePermissions( $user_info['global_permission_cache'] );

		$postData = $db_slave->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $post_id ) )->fetch();

		$threadData = $db_slave->query( 'SELECT thread.*, IF(reply_ban.userid IS NULL, 0, IF(reply_ban.expiry_date IS NULL OR reply_ban.expiry_date > ' . NV_CURRENTTIME . ', 1, 0)) AS thread_reply_banned
			FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_reply_ban AS reply_ban
				ON (reply_ban.thread_id = thread.thread_id
					AND reply_ban.userid = ' . intval( $global_userid ) . ')
			WHERE thread.thread_id = ' . intval( $postData['thread_id'] ) )->fetch();

		$forumData = $db_slave->query( '
			SELECT node.*, forum.*,
					permission.cache_value AS node_permission_cache
			FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
				ON (permission.permission_combination_id = ' . intval( $permission_combination_id ) . '
					AND permission.content_type = \'node\'
				AND permission.content_id = forum.node_id)
			WHERE node.node_id = ' . intval( $threadData['node_id'] ) )->fetch();

		$nodePermissions = array();

		if( ! empty( $forumData['node_permission_cache'] ) )
		{
			$nodePermissions = unserializePermissions( $forumData['node_permission_cache'] );
		}

		$canDeletePost = canDeletePost( $postData, $threadData, $forumData, $deleteType, $errorLangKey, $nodePermissions );

		if( ACTION_METHOD == 'update' )
		{

			$post = $db_slave->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $postData['post_id'] ) )->fetch();

			$thread = $db_slave->query( 'SELECT thread.* FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread WHERE thread.thread_id = ' . intval( $postData['thread_id'] ) )->fetch();

			if( $deleteType == 'soft' )
			{
				if( $post['position'] == 0 )
				{
					$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_thread SET discussion_state = \'deleted\' WHERE thread_id =' . intval( $thread['thread_id'] ) );

					$node_forum = $db_slave->query( 'SELECT node.*, forum.* FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
						INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)	
						WHERE node.node_id = ' . intval( $thread['node_id'] ) )->fetch();

					$last_thread = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread WHERE node_id = ' . intval( $thread['node_id'] ) . ' AND discussion_type <> \'redirect\' AND discussion_state IN (\'visible\') ORDER BY last_post_date DESC LIMIT 1' )->fetch();

					$message_count = ( $node_forum['message_count'] ) ? $node_forum['message_count'] - 1 : 0;
					$discussion_count = ( $node_forum['discussion_count'] ) ? $node_forum['discussion_count'] - 1 : 0;

					$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_forum SET discussion_count = ' . intval( $discussion_count ) . ', message_count = ' . intval( $message_count ) . ', last_post_id = ' . intval( $last_thread['last_post_id'] ) . ', last_post_date = ' . intval( $last_thread['last_post_date'] ) . ', last_post_user_id = ' . intval( $last_thread['last_post_user_id'] ) . ', last_post_username = ' . $db->quote( $last_thread['last_post_username'] ) . ', last_thread_title = ' . $db->quote( $last_thread['title'] ) . ' WHERE node_id = ' . intval( $thread['node_id'] ) );

					$db->query( 'INSERT IGNORE INTO ' . NV_FORUM_GLOBALTABLE . '_deletion_log
						(content_type, content_id, delete_date, delete_user_id, delete_username, delete_reason)
					VALUES
						(\'thread\', ' . intval( $thread['node_id'] ) . ', ' . NV_CURRENTTIME . ', ' . intval( $user_info['userid'] ) . ', ' . $db->quote( $user_info['username'] ) . ', ' . $db->quote( $reason ) . ')' );

					$result = $db_slave->query( '
						SELECT post_id, thread_id, userid, message_state, likes, post_date	
						FROM ' . NV_FORUM_GLOBALTABLE . '_post
						WHERE thread_id = ' . intval( $thread['thread_id'] ) . '
						ORDER BY position ASC, post_date ASC' );

					$result = $db_slave->query( 'SELECT post_id, thread_id, userid, message_state, likes, post_date FROM ' . NV_FORUM_GLOBALTABLE . '_post WHERE thread_id = ' . intval( $thread['thread_id'] ) . ' ORDER BY position ASC, post_date ASC' );
					$post_array = array();
					while( $rows = $result->fetch() )
					{
						$post_array[$rows['post_id']] = $rows;
					}
					$result->closeCursor();

					if( $post_array )
					{

						$users = array();
						foreach( $post_array as $post_id => $message )
						{
							if( $message['message_state'] == 'visible' && $message['userid'] )
							{
								if( isset( $users[$message['userid']] ) )
								{
									$users[$message['userid']]++;
								}
								else
								{
									$users[$message['userid']] = 1;
								}
							}

						}
						foreach( $users as $userId => $modify )
						{
							$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET message_count = IF(message_count > ' . intval( $modify ) . ', message_count - ' . intval( $modify ) . ', 0) WHERE userid = ' . intval( $userId ) );
						}

					}

					$result = $db_slave->query( 'SELECT tag_id, tag_content_id, visible
						FROM ' . NV_FORUM_GLOBALTABLE . '_tag_content
						WHERE content_type = \'thread\'
							AND content_id = ' . $thread['thread_id'] );
					$tags_array = array();
					while( $rows = $result->fetch() )
					{
						$tags_array[$rows['tag_id']] = $rows;
					}
					$result->closeCursor();
					if( $tags_array )
					{
						$tags_key = array_keys( $tags_array );
						$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_tag_content SET visible = \'0\' WHERE tag_content_id IN (' . implode( ',', $tags_key ) . ') ' );

						foreach( $tags_array as $_tag_id => $tag )
						{
							$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_tag SET use_count = 0, last_use_date = 0 WHERE tag_id = ' . $_tag_id );

						}
						///////////
						$result = $db_slave->query( 'SELECT tag_id,
							COUNT(IF(visible, 1, NULL)) AS use_count,
							COUNT(*) AS raw_use_count,
							MAX(IF(visible, add_date, 0)) AS last_use_date
						FROM ' . NV_FORUM_GLOBALTABLE . '_tag_content
						WHERE tag_id IN (' . implode( ',', $tags_key ) . ')
						GROUP BY tag_id' );

						$tag_content = array();
						while( $rows = $result->fetch() )
						{
							$tag_content[$rows['tag_id']] = $rows;

						}
						$result->closeCursor();

						$db_slave->query( 'SELECT tag_id, permanent
							FROM ' . NV_FORUM_GLOBALTABLE . '_tag
							WHERE tag_id IN (' . implode( ',', $tags_key ) . ')' );
						$tags_permanent = array();
						while( $rows = $result->fetch() )
						{
							$delete = false;

							if( isset( $tag_content[$rows['tag_id']] ) )
							{
								$result = $tag_content[$rows['tag_id']];
								if( ! $result['use_count'] && ! $result['raw_use_count'] )
								{
									// this shouldn't actually happen since there shouldn't be a row
									$delete = true;
								}
								else
								{
									$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_tag SET use_count = ' . intval( $result['use_count'] ) . ', last_use_date = ' . intval( $result['last_use_date'] ) . ' WHERE tag_id = ' . intval( $rows['tag_id'] ) );

								}
							}
							else
							{
								$delete = true;
							}

							if( $delete )
							{
								if( $rows['permanent'] )
								{
									$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_tag SET use_count = 0, last_use_date = 0 WHERE tag_id = ' . intval( $rows['tag_id'] ) );

								}
								else
								{
									$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_tag WHERE tag_id = ' . intval( $rows['tag_id'] ) );

								}
							}
						}
						$result->closeCursor();

					}

				}
				else
				{

					$position = ( $post['position'] ) ? $post['position'] - 1 : 0;
					$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET message_state = \'deleted\', position = ' . intval( $position ) . ' WHERE post_id = ' . intval( $post['post_id'] ) );

					$post_visible = $db_slave->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.thread_id = ' . intval( $thread['thread_id'] ) . ' AND post.message_state IN (\'visible\') ORDER BY post.post_date DESC LIMIT 1' )->fetch();

					$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post
								SET position = IF(position > 0, position - 1, 0)
								WHERE thread_id = ' . intval( $post['thread_id'] ) . '
									AND position >= ' . intval( $post['position'] ) . '
									AND post_id <> ' . intval( $post['post_id'] ) );

					$db->query( 'INSERT IGNORE INTO ' . NV_FORUM_GLOBALTABLE . '_deletion_log
						(content_type, content_id, delete_date, delete_user_id, delete_username, delete_reason)
					VALUES
						(\'post\', ' . intval( $post['post_id'] ) . ', ' . NV_CURRENTTIME . ', ' . intval( $user_info['userid'] ) . ', ' . $db->quote( $user_info['username'] ) . ', ' . $db->quote( $reason ) . ')' );

					$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . '
						SET message_count = IF(message_count > 0, message_count - 1, 0)
						WHERE userid = ' . intval( $post['userid'] ) );

					$result = $db_slave->query( 'SELECT * FROM ' . NV_USERS_GLOBALTABLE . '_alert WHERE content_type = \'post\' AND content_id = ' . intval( $post['post_id'] ) );

					while( $rows = $result->fetch() )
					{
						$db->query( 'DELETE FROM ' . NV_USERS_GLOBALTABLE . '_user_alert WHERE alert_id = ' . intval( $rows['alert_id'] ) );

						$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET alerts_unread = IF(alerts_unread > 0, alerts_unread - 1, 0) WHERE userid = ' . intval( $rows['alerted_user_id'] ) );
					}

					$post_count = $db_slave->query( 'SELECT post_count FROM ' . NV_FORUM_GLOBALTABLE . '_thread_user_post WHERE thread_id = ' . intval( $post['thread_id'] ) . ' AND userid = ' . intval( $post['userid'] ) )->fetchColumn();
					if( $post_count > 1 )
					{
						$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_thread_user_post SET post_count = post_count - 1 WHERE thread_id = ' . intval( $post['thread_id'] ) . ' AND userid = ' . intval( $post['userid'] ) );

					}
					else
					{
						$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread_user_post WHERE thread_id = ' . intval( $post['thread_id'] ) . ' AND userid = ' . intval( $post['userid'] ) );

					}

					$reply_count = ( $thread['reply_count'] ) ? $thread['reply_count'] - 1 : 0;
					$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_thread SET reply_count = ' . intval( $reply_count ) . ', last_post_id = ' . intval( $post_visible['post_id'] ) . ', last_post_date = ' . NV_CURRENTTIME . ' WHERE thread_id = ' . intval( $post['thread_id'] ) );

					$node_forum = $db_slave->query( 'SELECT node.*, forum.* FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
					INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)	
					WHERE node.node_id = ' . intval( $thread['node_id'] ) )->fetch();

					$message_count = ( $node_forum['message_count'] ) ? $node_forum['message_count'] - 1 : 0;

					$last_thread = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread WHERE node_id = ' . intval( $thread['node_id'] ) . ' AND discussion_type <> \'redirect\' AND (discussion_state IN (\'visible\')) ORDER BY last_post_date DESC LIMIT 1' )->fetch();

					$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_forum SET message_count = ' . intval( $message_count ) . ', last_post_id = ' . intval( $last_thread['last_post_id'] ) . ', last_post_date = ' . $last_thread['last_post_date'] . ' WHERE node_id = ' . intval( $thread['node_id'] ) );
				}

			}
			else
			{
				if( $post['position'] == 0 )
				{
					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_post WHERE post_id = ' . intval( $post['post_id'] ) );
					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread WHERE thread_id = ' . intval( $thread['thread_id'] ) );
					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_deletion_log WHERE content_type = \'post\' AND content_id=' . intval( $post['post_id'] ) );
					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_moderation_queue WHERE content_type = \'post\' AND content_id=' . intval( $post['post_id'] ) );
					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_edit_history WHERE content_type = \'post\' AND content_id=' . intval( $post['post_id'] ) );

					$node_forum = $db_slave->query( 'SELECT node.*, forum.* FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id) WHERE node.node_id = ' . intval( $thread['thread_id'] ) )->fetch();

					$message_count = ( $node_forum['message_count'] ) ? $node_forum['message_count'] - 1 : 0;

					$last_thread = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread WHERE node_id = ' . intval( $thread['node_id'] ) . ' AND discussion_type <> \'redirect\' AND (discussion_state IN (\'visible\')) ORDER BY last_post_date DESC LIMIT 1' )->fetch();

					$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_forum SET message_count = ' . intval( $message_count ) . ', last_post_id = ' . intval( $last_thread['last_post_id'] ) . ', last_post_date = ' . intval( $last_thread['last_post_date'] ) . ', last_post_user_id = ' . intval( $last_thread['last_post_user_id'] ) . ', last_post_username = ' . $db->quote( $last_thread['last_post_username'] ) . ' WHERE node_id = ' . intval( $thread['node_id'] ) );

					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_deletion_log WHERE content_type = \'thread\' AND content_id=' . intval( $post['post_id'] ) );

					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_moderation_queue WHERE content_type = \'thread\' AND content_id=' . intval( $post['post_id'] ) );

					$result = $db_slave->query( 'SELECT post_id, thread_id, userid, message_state, likes, post_date FROM ' . NV_FORUM_GLOBALTABLE . '_post WHERE thread_id = ' . intval( $thread['thread_id'] ) . ' ORDER BY position ASC, post_date ASC' );
					$post_array = array();
					while( $rows = $result->fetch() )
					{
						$post_array[$rows['post_id']] = $rows;
					}
					$result->closeCursor();

					if( $post_array )
					{
						$post_array_key = array_keys( $post_array );

						$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_post WHERE post_id IN (' . implode( ',', $post_array_key ) . ')' );

						$result = $db_slave->query( 'SELECT attachment_id, data_id FROM ' . NV_FORUM_GLOBALTABLE . '_attachment WHERE content_type = \'post\' AND content_id IN (' . implode( ',', $post_array_key ) . ')' );
						$_attachment = array();
						while( $rows = $result->fetch() )
						{
							$_attachment[$rows['attachment_id']] = $rows;
						}
						$result->closeCursor();

						if( $_attachment )
						{
							$_attachment_id = array_keys( $_attachment );
							$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_attachment WHERE attachment_id IN ( ' . implode( ',', $_attachment_id ) . ' )' );
							foreach( $_attachment as $id => $attach )
							{
								$dataCount = array();

								if( isset( $dataCount[$attach['data_id']] ) )
								{
									$dataCount[$attach['data_id']]++;
								}
								else
								{
									$dataCount[$attach['data_id']] = 1;
								}
								if( $dataCount )
								{
									foreach( $dataCount as $dataId => $delta )
									{
										$db->query( '
											UPDATE ' . NV_FORUM_GLOBALTABLE . '_attachment_data
											SET attach_count = IF(attach_count > ' . intval( $delta ) . ', attach_count - ' . intval( $delta ) . ', 0)
											WHERE data_id = ' . intval( $dataId ) );
									}
								}

								// $db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_attachment_view WHERE attachment_id=' . intval( $attach['attachment_id'] ) );

								// $attachment_data = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_attachment_data WHERE data_id=' . intval( $attach['data_id'] ) )->fetch();

								// $db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_attachment_data WHERE data_id=' . intval( $attach['data_id'] ) );

								// $folder = floor( $attach['data_id'] / 1000 );

								// nv_deletefile( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/attachments/' . $folder . '/' . $attach['data_id'] . '-' . $attachment_data['file_hash'] . '.data' );
								// if( $attachment_data['thumbnail_width'] ) nv_deletefile( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/attach_thumb/' . $folder . '/' . $attachment_data['filename'] );

							}

						}

						$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_deletion_log WHERE content_type = \'post\' AND content_id IN (' . implode( ',', $post_array_key ) . ')' );

						$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_moderation_queue WHERE content_type = \'post\' AND content_id IN (' . implode( ',', $post_array_key ) . ')' );

						$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_edit_history WHERE content_type = \'post\' AND content_id IN (' . implode( ',', $post_array_key ) . ')' );

						$result = $db_slave->query( 'SELECT content_user_id, COUNT(*) as like_count FROM ' . NV_FORUM_GLOBALTABLE . '_liked_content WHERE content_type = \'post\' AND content_id IN (' . implode( ',', $post_array_key ) . ' GROUP BY content_userid' );
						while( $rows = $result->fetch() )
						{
							$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET like_count = IF(like_count > ' . intval( $rows['like_count'] ) . ', like_count - ' . intval( $rows['like_count'] ) . ', 0) WHERE userid = ' . intval( $rows['content_user_id'] ) );
						}
						$result->closeCursor();

						$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_liked_content WHERE content_type = \'post\' AND content_id IN (' . implode( ',', $post_array_key ) . ')' );
						$users = array();
						foreach( $post_array as $post_id => $message )
						{
							if( $message['message_state'] == 'visible' && $message['userid'] )
							{
								if( isset( $users[$message['userid']] ) )
								{
									$users[$message['userid']]++;
								}
								else
								{
									$users[$message['userid']] = 1;
								}
							}

						}
						foreach( $users as $userId => $modify )
						{
							$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET message_count = IF(message_count > ' . intval( $modify ) . ', message_count - ' . intval( $modify ) . ', 0) WHERE userid = ' . intval( $userId ) );
						}

					}
					////////
					$result = $db_slave->query( 'SELECT tag_id, visible FROM ' . NV_FORUM_GLOBALTABLE . '_tag_content WHERE content_type = \'thread\' AND content_id = ' . intval( $thread['thread_id'] ) );
					$recalc = array();
					while( $rows = $result->fetch() )
					{
						if( $rows['visible'] )
						{
							$recalc[] = $rows['tag_id'];
						}

					}
					$result->closeCursor();

					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_tag_content WHERE content_type = \'thread\' AND content_id = ' . intval( $thread['thread_id'] ) );

					if( $recalc )
					{
						$result = $db_slave->query( 'SELECT tag_id,
							COUNT(IF(visible, 1, NULL)) AS use_count,
							COUNT(*) AS raw_use_count,
							MAX(IF(visible, add_date, 0)) AS last_use_date
						FROM ' . NV_FORUM_GLOBALTABLE . '_tag_content
						WHERE tag_id IN (' . implode( ',', $recalc ) . ')
						GROUP BY tag_id' );

						$tag_content = array();
						while( $rows = $result->fetch() )
						{
							$tag_content[$rows['tag_id']] = $rows;

						}
						$result->closeCursor();

						$db_slave->query( 'SELECT tag_id, permanent
							FROM ' . NV_FORUM_GLOBALTABLE . '_tag
							WHERE tag_id IN (' . implode( ',', $recalc ) . ')' );

						while( $rows = $result->fetch() )
						{
							$delete = false;

							if( isset( $tag_content[$rows['tag_id']] ) )
							{
								$result = $tag_content[$rows['tag_id']];
								if( ! $result['use_count'] && ! $result['raw_use_count'] )
								{
									// this shouldn't actually happen since there shouldn't be a row
									$delete = true;
								}
								else
								{
									$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_tag SET use_count = ' . intval( $result['use_count'] ) . ', last_use_date = ' . intval( $result['last_use_date'] ) . ' WHERE tag_id = ' . intval( $rows['tag_id'] ) );

								}
							}
							else
							{
								$delete = true;
							}

							if( $delete )
							{
								if( $rows['permanent'] )
								{
									$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_tag SET use_count = 0, last_use_date = 0 WHERE tag_id = ' . intval( $rows['tag_id'] ) );

								}
								else
								{
									$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_tag WHERE tag_id = ' . intval( $rows['tag_id'] ) );

								}
							}
						}
						$result->closeCursor();
					}

					////////
					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread_watch WHERE thread_id = ' . intval( $thread['thread_id'] ) );

					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread_user_post WHERE thread_id = ' . intval( $thread['thread_id'] ) );

					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread_reply_ban WHERE thread_id = ' . intval( $thread['thread_id'] ) );

					/* SELECT * FROM xf_thread_redirect WHERE redirect_key LIKE 'thread-12-%' */

					$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET message_count = IF(message_count > 0, message_count - 1, 0) WHERE userid = ' . intval( $post['userid'] ) );

					$db_slave->query( 'SELECT content_user_id, COUNT(*) FROM ' . NV_FORUM_GLOBALTABLE . '_liked_content WHERE content_type = \'post\' AND content_id IN (' . intval( $post['userid'] ) . ') GROUP BY content_user_id' );

					$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET like_count = IF(like_count > 2, like_count - 2, 0) WHERE userid = ' . intval( $post['post_id'] ) );

					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_liked_content WHERE content_type = \'post\' AND content_id IN (' . intval( $post['post_id'] ) . ')' );

					$result = $db_slave->query( 'SELECT * FROM ' . NV_USERS_GLOBALTABLE . '_alert WHERE (content_type = \'post\' AND content_id IN (' . intval( $post['post_id'] ) . ')' );

					while( $rows = $result->fetch() )
					{
						$db->query( 'DELETE FROM ' . NV_USERS_GLOBALTABLE . '_alert WHERE alert_id = ' . $rows['alert_id'] );

						$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET alerts_unread = IF(alerts_unread > 0, alerts_unread - 1, 0) WHERE userid = ' . $rows['alerted_userid'] );

					}

				}
				else
				{

					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_post WHERE post_id = ' . intval( $post['post_id'] ) );

					$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET position = IF(position > 0, position - 1, 0) WHERE thread_id = ' . intval( $thread['thread_id'] ) . ' AND position >= ' . $db->quote( $post['position'] ) . ' AND post_id <> ' . intval( $post['post_id'] ) );

					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_deletion_log WHERE content_type = \'post\' AND content_id=' . intval( $post['post_id'] ) );

					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_moderation_queue WHERE content_type = \'post\' AND content_id=' . intval( $post['post_id'] ) );

					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_edit_history WHERE content_type = \'post\' AND content_id=' . intval( $post['post_id'] ) );

					if( $post['attach_count'] )
					{
						$result = $db_slave->query( 'SELECT attachment_id, data_id FROM ' . NV_FORUM_GLOBALTABLE . '_attachment WHERE content_type = \'post\' AND content_id IN (' . implode( ',', $post_array_key ) . ')' );
						$_attachment = array();
						while( $rows = $result->fetch() )
						{
							$_attachment[$rows['attachment_id']] = $rows;
						}
						$result->closeCursor();

						if( $_attachment )
						{
							$_attachment_id = array_keys( $_attachment );
							$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_attachment WHERE attachment_id IN ( ' . implode( ',', $_attachment_id ) . ' )' );
							foreach( $_attachment as $id => $attach )
							{
								$dataCount = array();

								if( isset( $dataCount[$attach['data_id']] ) )
								{
									$dataCount[$attach['data_id']]++;
								}
								else
								{
									$dataCount[$attach['data_id']] = 1;
								}
								if( $dataCount )
								{
									foreach( $dataCount as $dataId => $delta )
									{
										$db->query( '
											UPDATE ' . NV_FORUM_GLOBALTABLE . '_attachment_data
											SET attach_count = IF(attach_count > ' . intval( $delta ) . ', attach_count - ' . intval( $delta ) . ', 0)
											WHERE data_id = ' . intval( $dataId ) );
									}
								}

								// $db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_attachment_view WHERE attachment_id=' . intval( $attach['attachment_id'] ) );

								// $attachment_data = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_attachment_data WHERE data_id=' . intval( $attach['data_id'] ) )->fetch();

								// $db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_attachment_data WHERE data_id=' . intval( $attach['data_id'] ) );

								// $folder = floor( $attach['data_id'] / 1000 );

								// nv_deletefile( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/attachments/' . $folder . '/' . $attach['data_id'] . '-' . $attachment_data['file_hash'] . '.data' );
								// if( $attachment_data['thumbnail_width'] ) nv_deletefile( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/attach_thumb/' . $folder . '/' . $attachment_data['filename'] );

							}

						}

					}

					$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET message_count = IF(message_count > 0, message_count - 1, 0) WHERE userid = ' . $post['userid'] );

					$result = $db_slave->query( 'SELECT * FROM ' . NV_USERS_GLOBALTABLE . '_alert WHERE content_type = \'post\' AND content_id = ' . intval( $post['post_id'] ) );

					while( $rows = $result->fetch() )
					{
						$db->query( 'DELETE FROM ' . NV_USERS_GLOBALTABLE . '_user_alert WHERE alert_id = ' . intval( $rows['alert_id'] ) );

						$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET alerts_unread = IF(alerts_unread > 0, alerts_unread - 1, 0) WHERE userid = ' . intval( $rows['alerted_user_id'] ) );
					}

					$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread_user_post WHERE thread_id = ' . intval( $thread['thread_id'] ) . ' AND userid = ' . intval( $post['userid'] ) );

					$post_visible = $db_slave->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.thread_id = ' . intval( $thread['thread_id'] ) . ' AND post.message_state IN (\'visible\') ORDER BY post.post_date DESC LIMIT 1' )->fetch();

					$reply_count = ( $thread['reply_count'] ) ? $thread['reply_count'] - 1 : 0;

					$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_thread SET last_post_id = ' . intval( $post_visible['post_id'] ) . ', last_post_date = ' . intval( $post_visible['post_date'] ) . ', last_post_user_id = ' . intval( $post_visible['userid'] ) . ', last_post_username = ' . $db->quote( $post_visible['username'] ) . ', reply_count = ' . intval( $reply_count ) . ' WHERE thread_id = ' . $thread['thread_id'] );

					$node_forum = $db_slave->query( 'SELECT node.*, forum.* FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
					INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)	
					WHERE node.node_id = ' . intval( $thread['node_id'] ) )->fetch();

					$message_count = ( $node_forum['message_count'] ) ? $node_forum['message_count'] - 1 : 0;

					$last_thread = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread WHERE node_id = ' . intval( $thread['node_id'] ) . ' AND discussion_type <> \'redirect\' AND (discussion_state IN (\'visible\')) ORDER BY last_post_date DESC LIMIT 1' )->fetch();

					$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_forum SET message_count = ' . intval( $message_count ) . ', last_post_id = ' . intval( $last_thread['last_post_id'] ) . ', last_post_date = ' . intval( $last_thread['last_post_date'] ) . ', last_post_user_id = ' . intval( $last_thread['last_post_user_id'] ) . ', last_post_username = ' . $db->quote( $last_thread['last_post_username'] ) . ' WHERE node_id = ' . intval( $thread['node_id'] ) );

				}

			}

			if( $send_author_alert )
			{

				$extra_data = array(
					'title' => $thread['title'],
					'link' => nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/view', true ),
					'threadLink' => nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . strtolower( change_alias( $threadData['title'] ) ) . '-' . $thread['thread_id'] . $global_config['rewrite_exturl'], true ),
					'reason' => $author_alert_reason );

				$db->query( 'INSERT INTO ' . NV_USERS_GLOBALTABLE . '_alert (alerted_user_id, userid, username, content_type, content_id, action, extra_data, event_date) VALUES (' . intval( $user_info['userid'] ) . ', 0, \'\', \'user\', ' . intval( $thread['thread_id'] ) . ', \'post_delete\', ' . $db->quote( serialize( $extra_data ) ) . ', ' . NV_CURRENTTIME . ')' );

				$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET alerts_unread = alerts_unread + 1 WHERE userid = ' . intval( $post['userid'] ) . ' AND alerts_unread < 65535' );

				$ipAddress = ( string )convertIpStringToBinary( $client_info['ip'] );
				$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_moderator_log (userid, content_type, content_id, content_user_id, content_username, content_title, content_url, discussion_content_type, discussion_content_id, action, action_params, ip_address, log_date) VALUES (' . $user_info['userid'] . ', \'post\', ' . intval( $post['post_id'] ) . ', ' . intval( $post['userid'] ) . ', ' . $db->quote( $post['username'] ) . ', ' . $db->quote( $thread['title'] ) . ', ' . $db->quote( $extra_data['link'] ) . ', \'thread\', ' . intval( $thread['thread_id'] ) . ', \'delete_soft\', ' . $db->quote( serialize( array( 'reason' => $author_alert_reason ) ) ) . ', ' . $db->quote( $ipAddress ) . ', ' . NV_CURRENTTIME . ')' );
			}

			if( $post_visible )
			{
				$json['redirect'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post_visible['post_id'] . '/view', true );

			}
			else
			{
				$json['redirect'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true );

			}
		}
		else
		{
			$json['template'] = ThemeDeletePostForm( $postData, $threadData, $forumData, $nodePermissions, $generalPermissions );

		}

	}
	else
	{
		$json['error'] = 'Lỗi bảo mật thao tác của bạn đã bị chặn lại';

	}
	getOutputJson($json);

}
elseif( $ActionMethod == 'warn' )
{
 
	getOutputJson(array('error'=> 'Chức năng này đang được xây dựng')); 
 
 
}
elseif( $ActionMethod == 'report' )
{
  
	getOutputJson(array('error'=> 'Chức năng này đang được xây dựng')); 
 
}
elseif( $ActionMethod == 'ip' )
{
  
	getOutputJson(array('error'=> 'Chức năng này đang được xây dựng')); 
 
}
elseif( $ActionMethod == 'edit-inline' )
{

	$json = array();
 
	/* Quyen xem noi dung trong toan bo module */
	if( ! hasContentPermission( $generalPermissions['general'], 'view' ) )
	{
		getOutputJson(array( 'error' => 'Bạn không có quyền truy cập toàn bộ diễn đàn' )); 
 
	}
	/* Quyen xem noi dung trong dien dan */
	elseif( ! hasContentPermission( $generalPermissions['general'], 'viewNode' ) )
	{

		getOutputJson(array( 'error' => 'Bạn không có quyền xem chuyên mục diễn đàn' )); 
 
	}
	/* Quyen xem noi dung chu de */
	elseif( ! hasContentPermission( $generalPermissions['forum'], 'viewContent' ) )
	{

		getOutputJson(array( 'error' => 'Bạn không có quyền xem chủ đề' )); 
 
	}

	$postData = $db_slave->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $post_id ) )->fetch();
	$threadData = $db_slave->query( 'SELECT thread.*, IF(reply_ban.userid IS NULL, 0, IF(reply_ban.expiry_date IS NULL OR reply_ban.expiry_date > ' . NV_CURRENTTIME . ', 1, 0)) AS thread_reply_banned
		FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_reply_ban AS reply_ban
			ON (reply_ban.thread_id = thread.thread_id
				AND reply_ban.userid = ' . intval( $global_userid ) . ')
		WHERE thread.thread_id = ' . intval( $postData['thread_id'] ) )->fetch();
	$forumData = $db_slave->query( '
		SELECT node.*, forum.*, permission.cache_value AS node_permission_cache
		FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
		INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
			ON (permission.permission_combination_id = ' . intval( $permission_combination_id ) . '
				AND permission.content_type = \'node\'
				AND permission.content_id = forum.node_id)
		WHERE node.node_id = ' . intval( $threadData['node_id'] ) )->fetch();
	if( ! empty( $forumData['node_permission_cache'] ) )
	{
		$nodePermissions = unserializePermissions( $forumData['node_permission_cache'] );
	}

	if( empty( $nodePermissions ) )
	{

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Diễn đàn này chưa được cấp quyền' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	$lockUnlockThread = isset( $nodePermissions['lockUnlockThread'] ) ? $nodePermissions['lockUnlockThread'] : false;
	if( ! $threadData['discussion_open'] && ! $lockUnlockThread )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không thể thực hiện thao tác này vì chủ đề này đã bị khóa' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	// su dung neu la moderator
	// if ( ! hasContentPermission( $nodePermissions , 'editAnyPost') )
	// {
	// header( 'Content-Type: application/json' );
	// include NV_ROOTDIR . '/includes/header.php';
	// echo json_encode( array('error'=> 'Bạn không có quyền sửa bài viết') );
	// include NV_ROOTDIR . '/includes/footer.php';
	// }

	if( $postData['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'editOwnPost' ) )
	{
		$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );
		if( $editLimit != -1 && ( ! $editLimit || $postData['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
		{

			header( 'Content-Type: application/json' );
			include NV_ROOTDIR . '/includes/header.php';
			echo json_encode( array( 'error' => 'Bạn đã hết thời gian sửa tin nhắn này sau ' . $editLimit . ' phút' ) );
			include NV_ROOTDIR . '/includes/footer.php';
		}

		if( empty( $forumData['allow_posting'] ) )
		{

			header( 'Content-Type: application/json' );
			include NV_ROOTDIR . '/includes/header.php';
			echo json_encode( array( 'error' => 'Bạn có thể không thực hiện thao tác này vì diễn đàn không cho phép gửi bài' ) );
			include NV_ROOTDIR . '/includes/footer.php';
		}
	}

	if( ACTION_METHOD == 'update' )
	{

		$data['token'] = nv_substr( $nv_Request->get_title( 'token', 'post', '', '' ), 0, 250 );
		if( $data['token'] == md5( session_id() . $global_config['sitekey'] . $post_id ) )
		{
			$data['message'] = $nv_Request->get_editor( 'message', 'post', NV_ALLOWED_HTML_TAGS );
			$data['message'] = convertPostAttachment( $data['message'] );
			$edit_count = $postData['edit_count'] + 1;
			$sth = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET message = :message, edit_count = ' . intval( $edit_count ) . ' WHERE post_id = ' . intval( $postData['post_id'] ) );
			$sth->bindParam( ':message', $data['message'], PDO::PARAM_STR, strlen( $data['message'] ) );
			$sth->execute();
			$affected_rows = $sth->rowCount();
			$sth->closeCursor();
			if( $affected_rows )
			{

				$sth = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_edit_history SET
					content_type=\'post\',
					content_id=' . intval( $postData['post_id'] ) . ', 
					edit_user_id=' . intval( $global_userid ) . ', 
					old_text=:old_text, 
					edit_date=' . NV_CURRENTTIME );
				$sth->bindParam( ':old_text', $postData['message'], PDO::PARAM_STR, strlen( $postData['message'] ) );
				$sth->execute();
				$edit_history_id = $db->lastInsertId();
				forumInsertLogs( $global_userid, $edit_history_id, 'edit_history', 'insert' );
				$attachmentData = array();
				if( $postData['attach_count'] > 0 )
				{
					$result = $db_slave->query( '
						SELECT attachment.*,
							data.filename, data.file_size, data.file_hash, data.file_path, data.width, data.height, data.thumbnail_width, data.thumbnail_height
						FROM ' . NV_FORUM_GLOBALTABLE . '_attachment AS attachment
						INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_attachment_data AS data ON
							(data.data_id = attachment.data_id)
						WHERE attachment.content_type = \'post\'
							AND attachment.content_id IN (' . $postData['post_id'] . ')
						ORDER BY attachment.content_id, attachment.attach_date' );
					while( $rows = $result->fetch() )
					{
						$alias_name = str_replace( '.', '-', $rows['filename'] );
						$folder = floor( $rows['data_id'] / 1000 );
						$fileExt = nv_getextension( $rows['filename'] );
						$thumb_name = $rows['data_id'] . '-' . $rows['file_hash'] . '.' . $fileExt;
						$rows['image_full'] = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=attachments/' . $alias_name . '-' . $rows['attachment_id'];
						$rows['image_thumb'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/attach_thumb/' . $folder . '/' . $thumb_name;
						$rows['is_image'] = ( $rows['width'] > 0 && $rows['width'] > 0 ) ? true : false;
						$attachmentData[$rows['content_id']][$rows['attachment_id']] = $rows;
					}

				}

				$json['success'] = 'Cập nhật thành công !';
				$postData['message'] = $data['message'];
				$postData['edit_count'] = $edit_count;
				$postData['last_edit_user_id'] = $global_userid;
				$postData['last_edit_date'] = NV_CURRENTTIME;
				$json['hastag'] = '#post-' . $postData['post_id'];
				$json['template'] = ThemeUpdatePostContent( $postData, $threadData, $forumData, $attachmentData, $nodePermissions, $generalPermissions );
			}
		}
		else
		{
			$json['error'] = 'Lỗi bảo mật: Không thể cập nhật tin nhắn !';
		}

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( $json );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	else
	{

		$json['template'] = ThemeEditPostForm( $postData, $threadData, $forumData );
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( $json );
		include NV_ROOTDIR . '/includes/footer.php';
	}

}
elseif( $ActionMethod == 'save-inline' )
{

	$json = array();
	$nodePermissions = array();
	$generalPermissions = array();
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
		$generalPermissions = unserializePermissions( $user_info['global_permission_cache'] );
	}

	/* Quyen xem noi dung trong toan bo module */
	if( ! hasContentPermission( $generalPermissions['general'], 'view' ) )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền truy cập toàn bộ diễn đàn' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	/* Quyen xem noi dung trong dien dan */
	elseif( ! hasContentPermission( $generalPermissions['general'], 'viewNode' ) )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền xem chuyên mục diễn đàn' ) );
		include NV_ROOTDIR . '/includes/footer.php';

	}
	/* Quyen xem noi dung chu de */
	elseif( ! hasContentPermission( $generalPermissions['forum'], 'viewContent' ) )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền xem chủ đề' ) );
		include NV_ROOTDIR . '/includes/footer.php';

	}

	$postData = $db_slave->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $post_id ) )->fetch();
	$threadData = $db_slave->query( 'SELECT thread.*, IF(reply_ban.userid IS NULL, 0, IF(reply_ban.expiry_date IS NULL OR reply_ban.expiry_date > ' . NV_CURRENTTIME . ', 1, 0)) AS thread_reply_banned
		FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_reply_ban AS reply_ban
			ON (reply_ban.thread_id = thread.thread_id
				AND reply_ban.userid = ' . intval( $global_userid ) . ')
		WHERE thread.thread_id = ' . intval( $postData['thread_id'] ) )->fetch();
	$forumData = $db_slave->query( '
		SELECT node.*, forum.*, permission.cache_value AS node_permission_cache
		FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
		INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
			ON (permission.permission_combination_id = ' . intval( $permission_combination_id ) . '
				AND permission.content_type = \'node\'
				AND permission.content_id = forum.node_id)
		WHERE node.node_id = ' . intval( $threadData['node_id'] ) )->fetch();
	if( ! empty( $forumData['node_permission_cache'] ) )
	{
		$nodePermissions = unserializePermissions( $forumData['node_permission_cache'] );
	}

	if( empty( $nodePermissions ) )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Diễn đàn này chưa được cấp quyền' ) );
		include NV_ROOTDIR . '/includes/footer.php';

	}

	$lockUnlockThread = isset( $nodePermissions['lockUnlockThread'] ) ? $nodePermissions['lockUnlockThread'] : false;
	if( ! $threadData['discussion_open'] && ! $lockUnlockThread )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không thể thực hiện thao tác này vì chủ đề này đã bị khóa' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	// su dung neu la moderator
	// if ( ! hasContentPermission( $nodePermissions , 'editAnyPost') )
	// {

	// header( 'Content-Type: application/json' );
	// include NV_ROOTDIR . '/includes/header.php';
	// echo json_encode( array( 'error' => 'Bạn không có quyền sửa bài viết' ) );
	// include NV_ROOTDIR . '/includes/footer.php';
	// }

	if( $postData['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'editOwnPost' ) )
	{
		$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );
		if( $editLimit != -1 && ( ! $editLimit || $postData['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
		{
			header( 'Content-Type: application/json' );
			include NV_ROOTDIR . '/includes/header.php';
			echo json_encode( array( 'error' => 'Bạn đã hết thời gian sửa tin nhắn này sau ' . $editLimit . ' phút' ) );
			include NV_ROOTDIR . '/includes/footer.php';
		}

		if( empty( $forumData['allow_posting'] ) )
		{
			header( 'Content-Type: application/json' );
			include NV_ROOTDIR . '/includes/header.php';
			echo json_encode( array( 'error' => 'Bạn có thể không thực hiện thao tác này vì diễn đàn không cho phép gửi bài' ) );
			include NV_ROOTDIR . '/includes/footer.php';
		}
	}

	if( ACTION_METHOD == 'update' )
	{

		$data['token'] = nv_substr( $nv_Request->get_title( 'token', 'post', '', '' ), 0, 250 );
		if( $data['token'] == md5( session_id() . $global_config['sitekey'] . $post_id ) )
		{
			$data['message'] = $nv_Request->get_editor( 'message', 'post', NV_ALLOWED_HTML_TAGS );
			$data['message'] = convertPostAttachment( $data['message'] );
			$edit_count = $postData['edit_count'] + 1;
			$sth = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET message = :message, edit_count = ' . intval( $edit_count ) . ' WHERE post_id = ' . intval( $postData['post_id'] ) );
			$sth->bindParam( ':message', $data['message'], PDO::PARAM_STR, strlen( $data['message'] ) );
			$sth->execute();
			$affected_rows = $sth->rowCount();
			$sth->closeCursor();
			if( $affected_rows )
			{

				$sth = $db->prepare( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_edit_history SET
					content_type=\'post\',
					content_id=' . intval( $postData['post_id'] ) . ', 
					edit_user_id=' . intval( $global_userid ) . ', 
					old_text=:old_text, 
					edit_date=' . NV_CURRENTTIME );
				$sth->bindParam( ':old_text', $postData['message'], PDO::PARAM_STR, strlen( $postData['message'] ) );
				$sth->execute();
				$edit_history_id = $db->lastInsertId();
				forumInsertLogs( $global_userid, $edit_history_id, 'edit_history', 'insert' );
				$attachmentData = array();
				if( $postData['attach_count'] > 0 )
				{
					$result = $db_slave->query( '
						SELECT attachment.*,
							data.filename, data.file_size, data.file_hash, data.file_path, data.width, data.height, data.thumbnail_width, data.thumbnail_height
						FROM ' . NV_FORUM_GLOBALTABLE . '_attachment AS attachment
						INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_attachment_data AS data ON
							(data.data_id = attachment.data_id)
						WHERE attachment.content_type = \'post\'
							AND attachment.content_id IN (' . $postData['post_id'] . ')
						ORDER BY attachment.content_id, attachment.attach_date' );
					while( $rows = $result->fetch() )
					{
						$alias_name = str_replace( '.', '-', $rows['filename'] );
						$folder = floor( $rows['data_id'] / 1000 );
						$fileExt = nv_getextension( $rows['filename'] );
						$thumb_name = $rows['data_id'] . '-' . $rows['file_hash'] . '.' . $fileExt;
						$rows['image_full'] = NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=attachments/' . $alias_name . '-' . $rows['attachment_id'];
						$rows['image_thumb'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/attach_thumb/' . $folder . '/' . $thumb_name;
						$rows['is_image'] = ( $rows['width'] > 0 && $rows['width'] > 0 ) ? true : false;
						$attachmentData[$rows['content_id']][$rows['attachment_id']] = $rows;
					}

				}

				$json['success'] = 'Cập nhật thành công !';
				$postData['message'] = $data['message'];
				$postData['edit_count'] = $edit_count;
				$postData['last_edit_user_id'] = $global_userid;
				$postData['last_edit_date'] = NV_CURRENTTIME;
				$json['hastag'] = '#post-' . $postData['post_id'];
				$json['template'] = ThemeUpdatePostContent( $postData, $threadData, $forumData, $attachmentData, $nodePermissions, $generalPermissions );
			}
		}
		else
		{
			$json['error'] = 'Lỗi bảo mật: Không thể cập nhật tin nhắn !';
		}

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( $json );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	else
	{

		$contents = ThemeEditPostFormFull( $postData, $threadData, $forumData );

		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( $contents );
		include NV_ROOTDIR . '/includes/footer.php';
	}

}
elseif( $ActionMethod == 'quote' )
{
	$json = array();
	$nodePermissions = array();
	$generalPermissions = array();
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
		$generalPermissions = unserializePermissions( $user_info['global_permission_cache'] );
	}

	/* Quyen xem noi dung trong toan bo module */
	if( ! hasContentPermission( $generalPermissions['general'], 'view' ) )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền truy cập toàn bộ diễn đàn' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	/* Quyen xem noi dung trong dien dan */
	elseif( ! hasContentPermission( $generalPermissions['general'], 'viewNode' ) )
	{

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền xem chuyên mục diễn đàn' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	/* Quyen xem noi dung chu de */
	elseif( ! hasContentPermission( $generalPermissions['forum'], 'viewContent' ) )
	{

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền xem chủ đề' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	$postData = $db_slave->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $post_id ) )->fetch();

	//$json['quote'] = '[QUOTE=&quot;'. $postData['username'] .', post: '. $postData['post_id'] .', member: '. $postData['userid'] .'&quot;]'. $postData['message'] .'[/QUOTE]';
	$postData['message'] = preg_replace( '@<p class="closequote">(.*?)</p>@siu', '$1', $postData['message'] );
	$postData['message'] = preg_replace( "#\[quote=(.*?)\](.*?)\[/quote\]#is", '', $postData['message'] );
	$json['quote'] = '<p class="closequote">[QUOTE=&quot;' . $postData['username'] . ', post: ' . $postData['post_id'] . ', member: ' . $postData['userid'] . '&quot;]</p><div class="closemessage">' . $postData['message'] . '</div><p class="closequote">[/QUOTE]</p><br>';

	$json['quote'] = trim( $json['quote'] );
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';
}
elseif( $ActionMethod == 'like' )
{
	$json = array();
	$nodePermissions = array();
	$generalPermissions = array();
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
		$generalPermissions = unserializePermissions( $user_info['global_permission_cache'] );
	}

	/* Quyen xem noi dung trong toan bo module */
	if( ! hasContentPermission( $generalPermissions['general'], 'view' ) )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền truy cập toàn bộ diễn đàn' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	/* Quyen xem noi dung trong dien dan */
	elseif( ! hasContentPermission( $generalPermissions['general'], 'viewNode' ) )
	{

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền xem chuyên mục diễn đàn' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	/* Quyen xem noi dung chu de */
	elseif( ! hasContentPermission( $generalPermissions['forum'], 'viewContent' ) )
	{

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền xem chủ đề' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	$json = array();
	$nodePermissions = array();
	$generalPermissions = array();
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
		$generalPermissions = unserializePermissions( $user_info['global_permission_cache'] );
	}

	/* Quyen xem noi dung trong toan bo module */
	if( ! hasContentPermission( $generalPermissions['general'], 'view' ) )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền truy cập toàn bộ diễn đàn' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	/* Quyen xem noi dung trong dien dan */
	elseif( ! hasContentPermission( $generalPermissions['general'], 'viewNode' ) )
	{

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền xem chuyên mục diễn đàn' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	/* Quyen xem noi dung chu de */
	elseif( ! hasContentPermission( $generalPermissions['forum'], 'viewContent' ) )
	{

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền xem chủ đề' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	$postData = $db_slave->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $post_id ) )->fetch();
	$threadData = $db_slave->query( 'SELECT thread.*, IF(reply_ban.userid IS NULL, 0, IF(reply_ban.expiry_date IS NULL OR reply_ban.expiry_date > ' . NV_CURRENTTIME . ', 1, 0)) AS thread_reply_banned
		FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_reply_ban AS reply_ban
			ON (reply_ban.thread_id = thread.thread_id
				AND reply_ban.userid = ' . intval( $global_userid ) . ')
		WHERE thread.thread_id = ' . intval( $postData['thread_id'] ) )->fetch();
	$forumData = $db_slave->query( '
		SELECT node.*, forum.*, permission.cache_value AS node_permission_cache
		FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
		INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
			ON (permission.permission_combination_id = ' . intval( $permission_combination_id ) . '
				AND permission.content_type = \'node\'
				AND permission.content_id = forum.node_id)
		WHERE node.node_id = ' . intval( $threadData['node_id'] ) )->fetch();
	if( ! empty( $forumData['node_permission_cache'] ) )
	{
		$nodePermissions = unserializePermissions( $forumData['node_permission_cache'] );
	}

	if( empty( $nodePermissions ) )
	{

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Diễn đàn này chưa được cấp quyền' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	$lockUnlockThread = isset( $nodePermissions['lockUnlockThread'] ) ? $nodePermissions['lockUnlockThread'] : false;
	if( ! $threadData['discussion_open'] && ! $lockUnlockThread )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không thể thực hiện thao tác này vì chủ đề này đã bị khóa' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	// su dung neu la moderator
	// if ( ! hasContentPermission( $nodePermissions , 'editAnyPost') )
	// {
	// header( 'Content-Type: application/json' );
	// include NV_ROOTDIR . '/includes/header.php';
	// echo json_encode( array('error'=> 'Bạn không có quyền sửa bài viết') );
	// include NV_ROOTDIR . '/includes/footer.php';
	// }

	if( $postData['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'editOwnPost' ) )
	{
		$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );
		if( $editLimit != -1 && ( ! $editLimit || $postData['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
		{

			header( 'Content-Type: application/json' );
			include NV_ROOTDIR . '/includes/header.php';
			echo json_encode( array( 'error' => 'Bạn đã hết thời gian sửa tin nhắn này sau ' . $editLimit . ' phút' ) );
			include NV_ROOTDIR . '/includes/footer.php';
		}

		if( empty( $forumData['allow_posting'] ) )
		{

			header( 'Content-Type: application/json' );
			include NV_ROOTDIR . '/includes/header.php';
			echo json_encode( array( 'error' => 'Bạn có thể không thực hiện thao tác này vì diễn đàn không cho phép gửi bài' ) );
			include NV_ROOTDIR . '/includes/footer.php';
		}
	}
	$json = array();
	$nodePermissions = array();
	$generalPermissions = array();
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
		$generalPermissions = unserializePermissions( $user_info['global_permission_cache'] );
	}

	/* Quyen xem noi dung trong toan bo module */
	if( ! hasContentPermission( $generalPermissions['general'], 'view' ) )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền truy cập toàn bộ diễn đàn' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	/* Quyen xem noi dung trong dien dan */
	elseif( ! hasContentPermission( $generalPermissions['general'], 'viewNode' ) )
	{

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền xem chuyên mục diễn đàn' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	/* Quyen xem noi dung chu de */
	elseif( ! hasContentPermission( $generalPermissions['forum'], 'viewContent' ) )
	{

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không có quyền xem chủ đề' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	$postData = $db_slave->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $post_id ) )->fetch();
	$threadData = $db_slave->query( 'SELECT thread.*, IF(reply_ban.userid IS NULL, 0, IF(reply_ban.expiry_date IS NULL OR reply_ban.expiry_date > ' . NV_CURRENTTIME . ', 1, 0)) AS thread_reply_banned
		FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_reply_ban AS reply_ban
			ON (reply_ban.thread_id = thread.thread_id
				AND reply_ban.userid = ' . intval( $global_userid ) . ')
		WHERE thread.thread_id = ' . intval( $postData['thread_id'] ) )->fetch();
	$forumData = $db_slave->query( '
		SELECT node.*, forum.*, permission.cache_value AS node_permission_cache
		FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
		INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
			ON (permission.permission_combination_id = ' . intval( $permission_combination_id ) . '
				AND permission.content_type = \'node\'
				AND permission.content_id = forum.node_id)
		WHERE node.node_id = ' . intval( $threadData['node_id'] ) )->fetch();
	if( ! empty( $forumData['node_permission_cache'] ) )
	{
		$nodePermissions = unserializePermissions( $forumData['node_permission_cache'] );
	}

	if( empty( $nodePermissions ) )
	{

		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Diễn đàn này chưa được cấp quyền' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	$lockUnlockThread = isset( $nodePermissions['lockUnlockThread'] ) ? $nodePermissions['lockUnlockThread'] : false;
	if( ! $threadData['discussion_open'] && ! $lockUnlockThread )
	{
		header( 'Content-Type: application/json' );
		include NV_ROOTDIR . '/includes/header.php';
		echo json_encode( array( 'error' => 'Bạn không thể thực hiện thao tác này vì chủ đề này đã bị khóa' ) );
		include NV_ROOTDIR . '/includes/footer.php';
	}

	// su dung neu la moderator
	// if ( ! hasContentPermission( $nodePermissions , 'editAnyPost') )
	// {
	// header( 'Content-Type: application/json' );
	// include NV_ROOTDIR . '/includes/header.php';
	// echo json_encode( array('error'=> 'Bạn không có quyền sửa bài viết') );
	// include NV_ROOTDIR . '/includes/footer.php';
	// }

	if( $postData['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'editOwnPost' ) )
	{
		$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );
		if( $editLimit != -1 && ( ! $editLimit || $postData['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
		{

			header( 'Content-Type: application/json' );
			include NV_ROOTDIR . '/includes/header.php';
			echo json_encode( array( 'error' => 'Bạn đã hết thời gian sửa tin nhắn này sau ' . $editLimit . ' phút' ) );
			include NV_ROOTDIR . '/includes/footer.php';
		}

		if( empty( $forumData['allow_posting'] ) )
		{

			header( 'Content-Type: application/json' );
			include NV_ROOTDIR . '/includes/header.php';
			echo json_encode( array( 'error' => 'Bạn có thể không thực hiện thao tác này vì diễn đàn không cho phép gửi bài' ) );
			include NV_ROOTDIR . '/includes/footer.php';
		}
	}

	$canLikePost = canLikePost( $postData, $threadData, $forumData, $nodePermissions, $errorLangKey );
	if( $canLikePost )
	{

		$like = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_liked_content WHERE content_type = \'post\' AND content_id = ' . intval( $postData['post_id'] ) . ' AND like_user_id = ' . intval( $global_userid ) )->fetch();

		if( $like )
		{
			$result = $db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_liked_content WHERE like_id = ' . intval( $like['like_id'] ) );

			if( ! $result->rowCount() )
			{
				$json['error'] = $lang_module['error_post_like_delete'];
			}
			$result->closeCursor();unset($result);

			if( $like['content_user_id'] )
			{
				$db->query( '
					UPDATE ' . NV_USERS_GLOBALTABLE . '
					SET like_count = IF(like_count > 1, like_count - 1, 0)
					WHERE userid=' . intval( $like['content_user_id'] ) );

				$result = $db->query( 'SELECT *
					FROM ' . NV_USERS_GLOBALTABLE . '_alert
					WHERE content_type = ' . $db->quote( $like['content_type'] ) . ' 
						AND content_id = ' . intval( $like['content_id'] ) . '
						AND userid = ' . intval( $like['like_user_id'] ) . '
						AND action = \'like\'' );
				while( $alert = $result->fetch() )
				{
					$db->query( 'DELETE FROM ' . NV_USERS_GLOBALTABLE . '_alert WHERE alert_id = ' . intval( $alert['alert_id'] ) );
				}
				$result->closeCursor();unset($result);

			}

			$result = $db->query( 'SELECT liked_content.*,
						user.*,
						user_profile.*,
						user_option.*
					FROM ' . NV_FORUM_GLOBALTABLE . '_liked_content AS liked_content
					INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON
						(user.userid = liked_content.like_user_id)
					INNER JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON
						(user_profile.userid = user.userid)
					INNER JOIN ' . NV_USERS_GLOBALTABLE . '_option AS user_option ON
						(user_option.userid = user.userid)
					WHERE liked_content.content_type = \'post\'
						AND liked_content.content_id = ' . intval( $like['content_id'] ) . '
					ORDER BY liked_content.like_date DESC
				 LIMIT 5' );
			$latestLikeUsers = array();
			while( $liked_content = $result->fetch() )
			{
				$latestLikeUsers = array( 'userid' => $liked_content['like_user_id'], 'username' => $liked_content['username'] );
			}
			$result->closeCursor();unset($result);
			
			$postCurentData = $result = $db->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $like['content_id'] ) )->fetch();
			$likes = $postCurentData['like'] - 1;
			$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET likes = ' . $likes . ', like_users = ' . $db->quote( serialize( $latestLikeUsers ) ) . ' WHERE post_id = ' . intval( $like['content_id'] ) );
			
			
			$json['post_id'] = $postData['post_id'];
			$json['template'] = ThemeGetLikePost( $latestLikeUsers, 0 );
			$json['islike'] = 'Like';
			
		}
		else
		{

			$result = $db->query( '
				INSERT IGNORE INTO ' . NV_FORUM_GLOBALTABLE . '_liked_content
					(content_type, content_id, content_user_id, like_user_id, like_date)
				VALUES
					(\'post\', 
					' . intval( $postData['post_id'] ) . ', 
					' . intval( $postData['userid'] ) . ', 
					' . intval( $global_userid ) . ', 
					' . NV_CURRENTTIME . ')');

			if( ! $result->rowCount() )
			{
				$json['error'] = $lang_module['error_post_like_insert'];
			}
			$result->closeCursor();unset($result);

			if( $postData['userid'] )
			{
				$contentUser = $db->query( 'SELECT USER.*, user_profile.*, user_option.*
					FROM ' . NV_USERS_GLOBALTABLE . ' AS USER
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON (user_profile.userid = USER.userid)
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_option AS user_option ON (user_option.userid = USER.userid) WHERE USER.userid = ' . intval( $postData['userid'] ) )->fetch();

				if( $contentUser )
				{
					$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET like_count = like_count + 1 WHERE userid = ' . intval( $postData['userid'] ) );
					if( $postData['userid'] != $global_userid )
					{
						$db->query( 'INSERT INTO ' . NV_USERS_GLOBALTABLE . '_alert (alerted_userid, userid, username, content_type, content_id, action, extra_data, event_date)
						  VALUES (' . intval( $postData['userid'] ) . ',
								  ' . intval( $global_userid ) . ',
								  ' . $db->quote( $user_info['username'] ) . ',
								  \'post\',
								  ' . intval( $postData['post_id'] ) . ',
								  \'like\',
								  \'\',
								  ' . NV_CURRENTTIME . ')' );

						$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET alerts_unread = alerts_unread + 1 WHERE userid = ' . intval( $postData['userid'] ) . ' AND alerts_unread < 65535' );

					}

				}
			}
 
			$result = $db->query( 'SELECT liked_content.*,
						user.*,
						user_profile.*,
						user_option.*
					FROM ' . NV_FORUM_GLOBALTABLE . '_liked_content AS liked_content
					INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON
						(user.userid = liked_content.like_user_id)
					INNER JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON
						(user_profile.userid = user.userid)
					INNER JOIN ' . NV_USERS_GLOBALTABLE . '_option AS user_option ON
						(user_option.userid = user.userid)
					WHERE liked_content.content_type = \'post\'
						AND liked_content.content_id = ' . intval( $postData['post_id'] ) . '
					ORDER BY liked_content.like_date DESC
				 LIMIT 5' );
			$latestLikeUsers = array();
			while( $liked_content = $result->fetch() )
			{
				$latestLikeUsers[] = array( 'userid' => $liked_content['like_user_id'], 'username' => $liked_content['username'] );
			}
			$result->closeCursor();unset($result);
			
			$postCurentData = $db->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $postData['post_id'] ) )->fetch();
			$likes = $postCurentData['like'] + 1;
			$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET likes = ' . $likes . ', like_users = ' . $db->quote( serialize( $latestLikeUsers ) ) . ' WHERE post_id = ' . intval( $postData['post_id'] ) );
			$json['post_id'] = $postData['post_id'];
			$json['template'] = ThemeGetLikePost( $latestLikeUsers, 1 );
			$json['latestLikeUsers'] = $latestLikeUsers;
			$json['islike'] = 'Unlike';
			
		}

	}
	elseif( $errorLangKey )
	{
		$json['error'] = $lang_module[$errorLangKey];
	}
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';
}
die( 'NOT FOUND !' );
