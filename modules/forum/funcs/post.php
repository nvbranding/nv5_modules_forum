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

if (preg_match('/^[0-9]{10,}$/', $nv_Request->get_string('nocache', 'post', '')) and $client_info['is_myreferer'] === 1) {
    define('FORUM_IS_AJAX', true);
}

$postData = $db->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $post_id ) )->fetch();

if( ! $postData )
{
	if( defined( 'FORUM_IS_AJAX' ) ) getOutputJson(array('error'=> $lang_module['error_post_not_found']));
	 
	$contents = ThemeErrorNotFoundPost();
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

$threadData = $db->query( 'SELECT thread.* ,0 AS thread_reply_banned FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread WHERE thread.thread_id = ' . $postData['thread_id'] )->fetch();
if( ! $threadData )
{
	if( defined( 'FORUM_IS_AJAX' ) ) getOutputJson(array('error'=> $lang_module['error_thread_not_found']));
	
	$contents = ThemeErrorNotFoundThread();
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

$forumData = $db->query( '
		SELECT node.*, forum.*
			,
			permission.cache_value AS node_permission_cache
		FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
		INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)	
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
				ON (permission.permission_combination_id = ' . $permission_combination_id . '
					AND permission.content_type = \'node\'
					AND permission.content_id = forum.node_id)
		WHERE node.node_id = ' . intval( $threadData['node_id'] ) )->fetch();

if( ! $forumData )
{
	if( defined( 'FORUM_IS_AJAX' ) ) getOutputJson(array('error'=> $lang_module['error_forum_not_found']));
	$contents = ThemeErrorNotFoundForum();
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}
$nodePermissions = array();
if( isset( $forumData['node_permission_cache'] ) )
{
	$nodePermissions = unserializePermissions( $forumData['node_permission_cache'] );
	unset( $forumData['node_permission_cache'] );
}

if( ! ModelForum_canViewForum( $forumData, $errorLangKey, $nodePermissions ) )
{
	if( defined( 'FORUM_IS_AJAX' ) ) getOutputJson(array('error'=> $errorLangKey));
	$contents = ThemeErrorOrNoPermission( $errorLangKey );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

if( ! ModelThread_canViewThread( $threadData, $forumData, $errorPhraseKey, $nodePermissions ) )
{
	if( defined( 'FORUM_IS_AJAX' ) ) getOutputJson(array('error'=> $errorLangKey));
	$contents = ThemeErrorOrNoPermission( $errorLangKey );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

$threadData = ModelThread_prepareThread( $threadData, $forumData, $nodePermissions );

if( ! ModelPost_canViewPost( $postData, $threadData, $forumData, $errorLangKey, $nodePermissions ) )
{
	if( defined( 'FORUM_IS_AJAX' ) ) getOutputJson(array('error'=> $errorLangKey));
	$contents = ThemeErrorOrNoPermission( $errorLangKey );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

$postData = ModelPost_preparePost( $postData, $threadData, $forumData, $nodePermissions );

if( $ActionMethod == 'view' )
{

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
 
	getOutputJson( array('error'=> 'Tính năng này đang code lát úp lên ạ :D') );

}
elseif( $ActionMethod == 'warn' )
{

	getOutputJson( array( 'error' => 'Chức năng này đang được xây dựng' ) );

}
elseif( $ActionMethod == 'report' )
{

	getOutputJson( array( 'error' => 'Chức năng này đang được xây dựng' ) );

}
elseif( $ActionMethod == 'ip' )
{

	getOutputJson( array( 'error' => 'Chức năng này đang được xây dựng' ) );

}
elseif( $ActionMethod == 'edit-inline' )
{

	$dataContent = array(
		'post' => $postData,
		'thread' => $threadData,
		'forum' => $forumData,
		'canSilentEdit' => ModelPost_canControlSilentEdit( $postData, $threadData, $forumData, $null, $nodePermissions ) );

	$json['template'] = ThemeEditPostForm( $dataContent );
	getOutputJson( $json );

}
elseif( $ActionMethod == 'save-inline' )
{ 
		
	$data['silent'] = $nv_Request->get_int( 'silent', 'post', 0);
	$data['clear_edit'] =  $nv_Request->get_int( 'clear_edit', 'post', 0);
	$data['send_author_alert'] =  $nv_Request->get_int( 'send_author_alert', 'post', 0);
	$data['author_alert_reason'] = nv_substr( $nv_Request->get_title( 'author_alert_reason', 'post', '', '' ), 0, 250 );
	$data['token'] = nv_substr( $nv_Request->get_title( 'token', 'post', '', '' ), 0, 250 );
	if( $data['token'] == md5( session_id() . $global_config['sitekey'] . $post_id ) )
	{
		$data['message'] = $nv_Request->get_editor( 'message', 'post', NV_ALLOWED_HTML_TAGS );
		$data['message'] = convertPostAttachment( $data['message'] );
		$data['message'] = spamMessageCheck( $data['message'], $nodePermissions );
		
		
		$edit_count = $postData['edit_count'] + 1;
 
		$set = '';
		if (ModelPost_canControlSilentEdit($postData, $threadData, $forumData, $null, $nodePermissions))
		{
			
			if ( $data['silent'] && $data['clear_edit'])
			{
				$set.=', last_edit_date=0, last_edit_user_id=0';
			}
			elseif ( ! $data['silent'] )
			{
				$set.=', last_edit_date=' . NV_CURRENTTIME;
				$set.=', last_edit_user_id=' . intval( $global_userid );
				
			}
 
		}else{
			
			$set.=', last_edit_date= ' . NV_CURRENTTIME;
			$set.=', last_edit_user_id=' . intval( $global_userid );
		}
		
		$sth = $db->prepare( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET message = :message, edit_count = ' . intval( $edit_count ) . ' '. $set .' WHERE post_id = ' . intval( $postData['post_id'] ) );
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
			
			$threadLink = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . strtolower( change_alias( $threadData['title'] ) ) . '-' . $threadData['thread_id'] . $global_config['rewrite_exturl'], true );
			if ($data['send_author_alert'])
			{
		 
				$resion = array(
					'title'=> $threadData['title'],
					'postLink'=> nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $postData['post_id'] . '/view', true ),
					'threadLink'=> $threadLink,
					'reason'=> $data['author_alert_reason']
					
				);
				
				$db->query( 'INSERT INTO ' . NV_USERS_GLOBALTABLE . '_alert (alerted_userid, userid, username, content_type, content_id, action, extra_data, event_date) 
				VALUES ('. intval( $postData['userid'] ) .', 0, \'\', \'user\', '. intval( $postData['post_id'] ) .', \'post_edit\', '. $db->quote( serialize( $resion ) ) .', '. NV_CURRENTTIME .')');
		 
				$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET alerts_unread = alerts_unread + 1 WHERE userid = '. intval( $postData['userid'] ) .' AND alerts_unread < 65535');
	 
			}
			
			$ipAddress = (string) convertIpStringToBinary($client_info['ip']);
			$db->query( 'INSERT INTO '. NV_FORUM_GLOBALTABLE .'_moderator_log 
			(userid, content_type, content_id, content_userid, content_username, content_title, content_url, discussion_content_type, discussion_content_id, action, action_params, ip_address, log_date) 
			VALUES ('.intval( $global_userid ).', \'post\', '. intval( $postData['post_id'] ) .', '. intval( $postData['userid'] ) .', '. $db->quote( $postData['username'] ) .', 
			'. $db->quote( $threadData['title'] ) .', 
			'. $db->quote( $threadLink ) .', 
			\'thread\', '. intval( $threadData['thread_id'] ) .', 
			\'edit\', \'[]\', 
			'. $db->quote( $ipAddress ) .', 
			'. NV_CURRENTTIME .')');
		
			
			$forumData = $db->query( '
					SELECT node.*, forum.*
						,
						permission.cache_value AS node_permission_cache
					FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
					INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)	
						LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
							ON (permission.permission_combination_id = ' . $permission_combination_id . '
								AND permission.content_type = \'node\'
								AND permission.content_id = forum.node_id)
					WHERE node.node_id = ' . intval( $threadData['node_id'] ) )->fetch();

			if( ! $forumData )
			{
				getOutputJson(array('error'=> 'Lỗi: diễn đàn này không tồn tại hoặc đã bị xóa'));
			}
			$nodePermissions = array();
			if( isset( $forumData['node_permission_cache'] ) )
			{
				$nodePermissions = unserializePermissions( $forumData['node_permission_cache'] );
				unset( $forumData['node_permission_cache'] );
			}

			if( ! ModelForum_canViewForum( $forumData, $errorLangKey, $nodePermissions ) )
			{
	 
				getOutputJson(array('error'=> $errorLangKey));
			}

			if( ! ModelThread_canViewThread( $threadData, $forumData, $errorPhraseKey, $nodePermissions ) )
			{
				getOutputJson(array('error'=> $errorLangKey));
			}

			$threadData = ModelThread_prepareThread( $threadData, $forumData, $nodePermissions );

			if( ! ModelPost_canViewPost( $postData, $threadData, $forumData, $errorLangKey, $nodePermissions ) )
			{
				getOutputJson(array('error'=> $errorLangKey));
			}
			
			$page =  $nv_Request->get_int('page', 'post', 0);

			$result = $db->query('
				SELECT post.*
					,
					user.*, IF(user.username IS NULL, post.username, user.username) AS username,
					user_profile.*,
					user_privacy.*,
					deletion_log.delete_date, deletion_log.delete_reason,
					deletion_log.delete_user_id, deletion_log.delete_username,
					session_activity.view_date AS last_view_date,
					liked_content.like_date
				FROM '. NV_FORUM_GLOBALTABLE .'_post AS post
					LEFT JOIN '. NV_USERS_GLOBALTABLE .' AS user ON
						(user.userid = post.userid)
					LEFT JOIN '. NV_USERS_GLOBALTABLE .'_profile AS user_profile ON
						(user_profile.userid = post.userid)
					LEFT JOIN '. NV_USERS_GLOBALTABLE .'_privacy AS user_privacy ON
						(user_privacy.userid = post.userid)
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_deletion_log AS deletion_log ON
						(deletion_log.content_type = \'post\' AND deletion_log.content_id = post.post_id)
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_session_activity AS session_activity ON
						(post.userid > 0 AND session_activity.userid = post.userid)
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_liked_content AS liked_content
						ON (liked_content.content_type = \'post\'
							AND liked_content.content_id = post.post_id
							AND liked_content.like_user_id = '. $postData['userid'] .')
				WHERE post.post_id IN (' . $postData['post_id'] . ') ');
			
			$posts = array();
			while($rows = $result->fetch() )
			{
				$posts[$rows['post_id']] = $rows;
			}
			$result->closeCursor();
			
 			$posts = ModelPost_getAndMergeAttachmentsIntoPosts( $posts );
 
			$inlineModOptions = array();
			$maxPostDate = 0;
 
			foreach ($posts AS $key => &$post)
			{
				// only allow posts from the specified thread to be loaded (permissions reasons)
				if ($post['thread_id'] != $threadData['thread_id']
					|| !ModelPost_canViewPost($post, $threadData, $forumData, $null, $nodePermissions)
				)
				{
					unset($posts[$key]);
					continue;
				}

				$post = ModelPost_preparePost($post, $threadData, $forumData, $nodePermissions);
			}

			if (empty($posts))
			{
 
				getOutputJson(array('error'=> 'không có bài phù hợp với tiêu chuẩn quy định đã được tìm thấy'));
			}

			$dataContent = ModelThread_exportContent($forumData, $threadData, $posts, $page, $nodePermissions ) ;
			$json['post_id'] = $post_id;
			$json['template'] = ThemeUpdatePostContent( $dataContent, $post_id);
			
		}
	}
	else
	{
		$json['error'] = 'Lỗi bảo mật: Không thể cập nhật tin nhắn !';
	}

	getOutputJson( $json );
}
elseif( $ActionMethod == 'quote' )
{
	 
	//$json['quote'] = '[QUOTE=&quot;'. $postData['username'] .', post: '. $postData['post_id'] .', member: '. $postData['userid'] .'&quot;]'. $postData['message'] .'[/QUOTE]';
	//$postData['message'] = preg_replace( '@<p class="closequote">(.*?)</p>@siu', '$1', $postData['message'] );
	//$postData['message'] = preg_replace( "#\[quote=(.*?)\](.*?)\[/quote\]#is", '', $postData['message'] );
	$postData['message'] = preg_replace('@&\#91;QUOTE[^>]*?&#93;.*?&\#91;/QUOTE&\#93;@siu', '', $postData['message']);
	$postData['message'] = preg_replace( "#<p><br></p>#", '', $postData['message'] );
	$json['quote'] = '&#91;QUOTE=&quot;' . $postData['username'] . ', post: ' . $postData['post_id'] . ', member: ' . $postData['userid'] . '&quot;&#93;' . $postData['message'] . '&#91;/QUOTE&#93;';
 
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';
}
elseif( $ActionMethod == 'like' )
{
	if (!ModelLike_canLikePost($postData, $threadData, $forumData, $errorLangKey, $nodePermissions))
	{
		getOutputJson(array('error'=> $errorLangKey));
	}
 
	$existingLike = ModelLike_getContentLikeByLikeUser('post', $post_id, $global_userid);
 

	if ($existingLike)
	{
		$latestUsers = ModelLike_unlikeContent($existingLike);
		$postCurentData = $result = $db->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $post_id ) )->fetch();
	 
		$likes = $postCurentData['likes'] - 1;
		$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET likes = ' . $likes . ', like_users = ' . $db->quote( serialize( $latestUsers ) ) . ' WHERE post_id = ' . intval( $post_id ) );
		
	}
	else
	{
		$latestUsers = ModelLike_likeContent('post', $post_id, $postData['userid']);
		
		$postCurentData = $db->query( 'SELECT post.* FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post WHERE post.post_id = ' . intval( $post_id ) )->fetch();
		$likes = $postCurentData['likes'] + 1;
		$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_post SET likes = ' . $likes . ', like_users = ' . $db->quote( serialize( $latestUsers ) ) . ' WHERE post_id = ' . intval( $post_id ) );
			
		
	}
	

	$liked = ($existingLike ? false : true);
	
	
	$noRedirect = 1;
	
	if ($noRedirect && $latestUsers !== false)
	{
		$postData['likeUsers'] = $latestUsers;
		$postData['likes'] += ($liked ? 1 : -1);
		$postData['like_date'] = ($liked ? NV_CURRENTTIME : 0);

		$dataContent = array(
			'post' => $postData,
			'thread' => $threadData,
			'forum' => $forumData,
			'liked' => $liked,
		);

		$json['liked'] = $liked;
		$json['post_id'] = $postData['post_id'];
		$json['template'] = ThemeGetLikePost( $dataContent );
		getOutputJson( $json );
	}
	else
	{
		//
	}
	
	
}
die( 'NOT FOUND !' );
