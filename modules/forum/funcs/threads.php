<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweb.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 22:18
 */

if( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

$dataContent = $threadData = $postData = $forumData = $generalPermissions = $nodePermissions = $tagData = $attachmentData = $attachCountPost = $generatePage = array();

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
 

if( sizeof( $array_op ) == 2 || ( isset( $array_op[2] ) and substr( $array_op[2], 0, 5 ) == 'page-' ) )
{
	$array_page = explode( '-', $array_op[1] );
	$thread_id = intval( end( $array_page ) );
	$number = strlen( $thread_id ) + 1;
	$alias_url = substr( $array_op[1], 0, -$number );

	$page = 1;
	if( isset( $array_op[2] ) )
	{
		$page = intval( substr( $array_op[2], 5 ) );
	}

	$array_image = array( 'forum.jpg', 'facebook_share.jpg' );
	shuffle( $array_image );
	$meta_property['og:image'] = NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $array_image[0];
	$meta_property['fb:admins'] = '100001351285520';
	$meta_property['fb:app_id'] = '257460811289387';

	if( ! defined( 'NV_IS_USER' ) )
	{
		/////
		$threadData = $db->query( 'SELECT thread.* ,
			   user.gender,
			   user.photo,
			   NULL AS thread_read_date,
			   0 AS thread_reply_banned,
			   0 AS thread_is_watched,
			   \'\' AS draft_message,
			   NULL AS draft_extra
		FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
		LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = thread.userid)
		WHERE thread.thread_id = ' . intval( $thread_id ) )->fetch();

		if( ! $threadData )
		{
			$contents = ThemeErrorNotFoundThread();
			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';
		}

		/* Tạo link chuẩn */
		$thread_alias = strtolower( change_alias( $threadData['title'] ) );
		if( $thread_alias != $alias_url )
		{
			Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . $thread_alias . '-' . $threadData['thread_id'] . $global_config['rewrite_exturl'], true ) );
			die();
		}
		$threadData['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . $thread_alias . '-' . $threadData['thread_id'];
	
		/////
		$forumData = $db->query( 'SELECT node.*, forum.*, permission.cache_value AS node_permission_cache, NULL AS forum_read_date
			FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission ON (permission.permission_combination_id = ' . intval( $permission_combination_id ) . '
				AND permission.content_type = \'node\'
				AND permission.content_id = forum.node_id)
			WHERE node.node_id = ' . intval( $threadData['node_id'] ) )->fetch();
		if (! $forumData )
		{
			$contents = ThemeErrorNotFoundForum();
			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';
		} 
		
		/////
		$nodePermissions = array();
		if( isset( $forumData['node_permission_cache'] ) )
		{

			if( is_string( $forumData['node_permission_cache'] ) )
			{
				$nodePermissions = unserializePermissions( $forumData['node_permission_cache'] );
			}

			if( is_array( $forumData['node_permission_cache'] ) )
			{
				$nodePermissions = $forumData['node_permission_cache'];
			}
			unset( $forumData['node_permission_cache'] );
		}

		// kiem tra quyen xem dien dan
		if (! ModelForum_canViewForum( $forumData, $errorLangKey, $nodePermissions))
		{	
			$contents = ThemeErrorOrNoPermission($errorLangKey);
			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';
		}

		$forumData['hasNew'] = ( isset( $forumData['forum_read_date'] ) && $forumData['forum_read_date'] < $forumData['last_post_date'] );
		$forumData['prefixCache'] = ( ! empty( $forumData['prefix_cache'] ) ? safeUnserialize( $forumData['prefix_cache'] ) : array() );
		
		if (!ModelThread_canViewThread($threadData, $forumData, $errorPhraseKey, $nodePermissions ))
		{
			$contents = ThemeErrorOrNoPermission($errorLangKey);
			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';
		}

		$threadData = ModelThread_prepareThread($threadData, $forumData, $nodePermissions);
 
		if( ModelThread_isRedirect( $threadData ) )
		{
			$redirect = ModelThreadRedirect_getThreadRedirectById( $threadData['thread_id'] );
			if( ! $redirect )
			{

				$contents = ThemeResponseNoPermission();
			}
			else
			{
				$contents = ThemeResponseRedirect( $redirect );

			}
			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';

		}

		$result = $db->query( '
			SELECT post.* ,
				   user.*,
				   IF(user.username IS NULL, post.username, user.username) AS username,
				   user_profile.*,
				   user_privacy.*,
				   0 AS like_date
			FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post
			LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = post.userid)
			LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON (user_profile.userid = post.userid)
			LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_privacy AS user_privacy ON (user_privacy.userid = post.userid)
			WHERE post.thread_id = ' . $threadData['thread_id'] . '
				AND (post.position >= 0
				AND post.position < 20)
				AND (post.message_state IN (\'visible\'))
			ORDER BY post.position ASC,
					 post.post_date ASC' );
		while( $rows = $result->fetch() )
		{
			$postData[$rows['post_id']] = $rows;
		}
		$result->closeCursor();

	}
	else
	{
		
		/* số ngày hiển thị chủ đề mới nhất > 30 ngày */
		$thread_read_date = NV_CURRENTTIME - 30 * 86400;
		
		$threadData = $db->query( '
			SELECT thread.*,
				   user.gender,
				   IF(thread_read.thread_read_date > ' . intval( $thread_read_date ) . ', thread_read.thread_read_date, ' . intval( $thread_read_date ) . ') AS thread_read_date,
				   IF(reply_ban.userid IS NULL, 0, IF(reply_ban.expiry_date IS NULL
													   OR reply_ban.expiry_date > ' . NV_CURRENTTIME . ', 1, 0)) AS thread_reply_banned,
				   IF(thread_watch.userid IS NULL, 0, IF(thread_watch.email_subscribe, \'watch_email\', \'watch_no_email\')) AS thread_is_watched,
				   draft.message AS draft_message,
				   draft.extra_data AS draft_extra
			FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
			LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = thread.userid)
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_read AS thread_read ON (thread_read.thread_id = thread.thread_id
														AND thread_read.userid = ' . intval( $global_userid ) . ')
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_reply_ban AS reply_ban ON (reply_ban.thread_id = thread.thread_id
														   AND reply_ban.userid = ' . intval( $global_userid ) . ')
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_watch AS thread_watch ON (thread_watch.thread_id = thread.thread_id
														  AND thread_watch.userid = ' . intval( $global_userid ) . ')
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_draft AS draft ON (draft.draft_key = CONCAT(\'thread-\', thread.thread_id)
											AND draft.userid = ' . intval( $global_userid ) . ')
			WHERE thread.thread_id = ' . intval( $thread_id ) )->fetch();

		if( ! $threadData )
		{
			$contents = ThemeErrorNotFoundThread();
			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';
		}

		/* Tạo link chuẩn */
		$thread_alias = strtolower( change_alias( $threadData['title'] ) );
		if( $thread_alias != $alias_url )
		{
			Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . $thread_alias . '-' . $threadData['thread_id'] . $global_config['rewrite_exturl'], true ) );
			die();
		}
		$threadData['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . $thread_alias . '-' . $threadData['thread_id'];
		
		$forumData = $db->query( 'SELECT node.*, forum.*,
				permission.cache_value AS node_permission_cache,
					IF(forum_read.forum_read_date > ' . intval( $thread_read_date ) . ', forum_read.forum_read_date, ' . intval( $thread_read_date ) . ') AS forum_read_date
			FROM ' . NV_FORUM_GLOBALTABLE . '_forum AS forum
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON (node.node_id = forum.node_id)
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
					ON (permission.permission_combination_id = ' . intval( $permission_combination_id ) . '
						AND permission.content_type = \'node\'
						AND permission.content_id = forum.node_id)
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum_read AS forum_read ON
						(forum_read.node_id = forum.node_id
						AND forum_read.userid = ' . intval( $global_userid ) . ')
			WHERE node.node_id = ' . intval( $threadData['node_id'] ) )->fetch();

		/////
		$nodePermissions = array();
		if( isset( $forumData['node_permission_cache'] ) )
		{

			if( is_string( $forumData['node_permission_cache'] ) )
			{
				$nodePermissions = unserializePermissions( $forumData['node_permission_cache'] );
			}

			if( is_array( $forumData['node_permission_cache'] ) )
			{
				$nodePermissions = $forumData['node_permission_cache'];
			}
			unset( $forumData['node_permission_cache'] );
		}

		// kiem tra quyen xem dien dan
		if( ! ModelForum_canViewForum( $forumData, $errorLangKey, $nodePermissions ) )
		{
			$contents = ThemeErrorOrNoPermission( $errorLangKey );

			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';

		}

		$forumData['hasNew'] = ( isset( $forumData['forum_read_date'] ) && $forumData['forum_read_date'] < $forumData['last_post_date'] );
		$forumData['prefixCache'] = ( ! empty( $forumData['prefix_cache'] ) ? safeUnserialize( $forumData['prefix_cache'] ) : array() );
		
		if (!ModelThread_canViewThread($threadData, $forumData, $errorPhraseKey, $nodePermissions ))
		{
			$contents = ThemeErrorOrNoPermission($errorLangKey);
			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';
		}

		$threadData = ModelThread_prepareThread($threadData, $forumData, $nodePermissions);
		
		if( ModelThread_isRedirect( $threadData ) )
		{
			$redirect = ModelThreadRedirect_getThreadRedirectById( $threadData['thread_id'] );
			if( ! $redirect )
			{

				$contents = ThemeResponseNoPermission();
			}
			else
			{
				$contents = ThemeResponseRedirect( $redirect );

			}
			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';

		}

		$result = $db->query( 'SELECT post.* 
				,
					user.*, IF(user.username IS NULL, post.username, user.username) AS username,
					user_profile.*,
					user_privacy.*,
					session_activity.view_date AS last_view_date,
					liked_content.like_date
			FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post			
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON
						(user.userid = post.userid)
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON
						(user_profile.userid = post.userid)
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_privacy AS user_privacy ON
						(user_privacy.userid = post.userid)
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_session_activity AS session_activity ON
						(post.userid > 0 AND session_activity.userid = post.userid)
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_liked_content AS liked_content
						ON (liked_content.content_type = \'post\'
							AND liked_content.content_id = post.post_id
							AND liked_content.like_user_id = ' . intval( $global_userid ) . ')
			WHERE post.thread_id = ' . intval( $threadData['thread_id'] ) . '
				 AND (post.position >= ' . intval( ( $per_page_post * $page ) - $per_page_post ) . ' AND post.position < ' . intval( $per_page_post * $page ) . ')
				AND (post.message_state IN (\'visible\') OR (post.message_state = \'moderated\' AND post.userid = ' . intval( $global_userid ) . '))
			ORDER BY post.position ASC, post.post_date ASC' );

		while( $rows = $result->fetch() )
		{
			$postData[$rows['post_id']] = $rows;
		}
		$result->closeCursor();

	}

	if( $threadData['tags'] )
	{
		$threadData['tags'] = unserialize( $threadData['tags'] );

		foreach( $threadData['tags'] as $tag_id => $tag )
		{
			$tagData[] = $tag;
		}
	}

	$postData = ModelPost_getAndMergeAttachmentsIntoPosts( $postData );

	$inlineModOptions = array();
	$maxPostDate = 0;
	$firstUnreadPostId = 0;

	$deletedPosts = 0;
	$moderatedPosts = 0;

	$pagePosition = 0;

	foreach( $postData as &$post )
	{
		$post['position_on_page'] = ++$pagePosition;

		$postModOptions = ModelPost_addInlineModOptionToPost( $post, $threadData, $forumData, $nodePermissions  );
		$inlineModOptions += $postModOptions;

		$post = ModelPost_preparePost( $post, $threadData, $forumData, $nodePermissions );

		if( $post['post_date'] > $maxPostDate )
		{
			$maxPostDate = $post['post_date'];
		}

		if( $post['isDeleted'] )
		{
			$deletedPosts++;
		}
		if( $post['isModerated'] )
		{
			$moderatedPosts++;
		}

		if( ! $firstUnreadPostId && $post['isNew'] )
		{
			$firstUnreadPostId = $post['post_id'];
		}
	}

	if( $firstUnreadPostId )
	{
	 
		$unreadLink = $client_info['selfurl'] . '#post-' . $firstUnreadPostId;
	}
	elseif( $threadData['isNew'] )
	{
		$unreadLink = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=threads-unread';
	}
	else
	{
		$unreadLink = '';
	}

	$attachmentHash = null;
	if( ! empty( $threadData['draft_extra'] ) )
	{
		$draftExtra = safeUnserialize( $threadData['draft_extra'] );
		if( ! empty( $draftExtra['attachment_hash'] ) )
		{
			$attachmentHash = $draftExtra['attachment_hash'];
		}
	}

	$attachmentParams = ModelForum_getAttachmentParams( $forumData, array( 'thread_id' => $threadData['thread_id'] ), $nodePermissions,  null, $attachmentHash );

	if( $threadData['discussion_type'] == 'poll' )
	{
 
		$poll = ModelPoll_getPollByContent( 'thread', $thread_id );
		if( $poll )
		{
			$poll = ModelPoll_preparePoll( $poll, ModelThread_canVoteOnPoll( $poll, $threadData, $forumData ) );
			$poll['canEdit'] = ModelThread_canEditPoll( $poll, $threadData, $forumData );
		}
	}
	else
	{
		$poll = false;
	}

	ModelThread_markThreadRead( $threadData, $forumData, $maxPostDate );
	ModelThread_logThreadView( $thread_id );
	
	
	$page_title = $threadData['title'];
	$description = $threadData['title'];
	$key_words = '';
 
	$generatePage = GeneratePagePost( $page_title, $threadData['link'],( $threadData['reply_count'] + 1 ), $per_page_post, $page );
		
	
	$dataContent = ModelThread_exportContent( $forumData, $threadData, $postData, $page, $nodePermissions, array(
		'deletedPosts' => $deletedPosts,
		'moderatedPosts' => $moderatedPosts,
		'inlineModOptions' => $inlineModOptions,
		'firstPost' => reset( $postData ),
		'lastPost' => end( $postData ),
		'unreadLink' => $unreadLink,
		'generatePage' => $generatePage,
		'poll' => $poll,
		'attachmentParams' => $attachmentParams,
		'attachmentConstraints' => ModelAttachment_getAttachmentConstraints(),
		'showPostedNotice' => $nv_Request->get_int( 'posted', 'post,get', 0 ),
	) );
	
 

}

$parent_id = $threadData['node_id'];
while( $parent_id > 0 )
{
	$array_cat_i = $forum_node[$parent_id];
	$array_mod_title[] = array(
		'catid' => $parent_id,
		'title' => $array_cat_i['title'],
		'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $array_cat_i['alias'] );
	$parent_id = $array_cat_i['parent_id'];
}
sort( $array_mod_title, SORT_NUMERIC );

$contents = ThemeForumViewThreads( $dataContent );

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
