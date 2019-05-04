<?php

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

// Post Model
function ModelPost_isDeleted( array $post )
{
	if( ! isset( $post['message_state'] ) )
	{
		trigger_error( 'Message state not available in post.' );
	}

	return ( $post['message_state'] == 'deleted' );
}

function ModelPost_isModerated( array $post )
{
	if( ! isset( $post['message_state'] ) )
	{
		trigger_error( 'Message state not available in post.' );
	}

	return ( $post['message_state'] == 'moderated' );
}

function ModelPost_canViewPostAndContainer(array $post, array $thread, array $forum, &$errorLangKey = '',array $nodePermissions = null)
{
	global $global_userid, $user_info;
	
	if (!ModelThread_canViewThreadAndContainer($thread, $forum, $errorLangKey, $nodePermissions))
	{
		return false;
	}

	return ModelPost_canViewPost($post, $thread, $forum, $errorLangKey, $nodePermissions) ;
}
function ModelPost_canViewAttachmentOnPost(array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	return ModelThread_canViewAttachmentsInThread($thread, $forum, $errorLangKey, $nodePermissions);
}

function ModelPost_canViewPost( array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid, $user_info;

	if( ! hasContentPermission( $nodePermissions, 'view' ) )
	{
		return false;
	}

	if( ModelPost_isModerated( $post ) )
	{
		if( ! ModelThread_canViewModeratedPosts( $thread, $forum, $errorLangKey, $nodePermissions ) )
		{
			if( ! $global_userid || $global_userid != $post['userid'] )
			{
				return false;
			}
		}
	}
	elseif( ModelPost_isDeleted( $post ) )
	{
		if( ! ModelThread_canViewDeletedPosts( $thread, $forum, $errorLangKey, $nodePermissions ) )
		{
			return false;
		}
	}

	return true;
}

function ModelPost_getPermissionBasedPostFetchOptions( array $thread, array $forum, array $nodePermissions )
{
	global $global_userid;
	if( hasContentPermission( $nodePermissions, 'viewModerated' ) )
	{
		$viewModerated = true;
	}
	else
		if( $global_userid )
		{
			$viewModerated = $global_userid;
		}
		else
		{
			$viewModerated = false;
		}

		return array( 'deleted' => hasContentPermission( $nodePermissions, 'viewDeleted' ), 'moderated' => $viewModerated );
}

function ModelPost_getNextPostInThread( $thread_id, $postDate )
{
	global $db_slave;

	return $db_slave->query( '
		SELECT *
			FROM ' . NV_FORUM_GLOBALTABLE . '_post
			WHERE thread_id = ' . intval( $thread_id ) . '
				AND post_date > ' . intval( $postDate ) . '
				AND (message_state IN (\'visible\',\'deleted\',\'moderated\'))			
			ORDER BY post_date
		LIMIT 1' )->fetch();
}

function ModelPost_getAndMergeAttachmentsIntoPosts( array $posts )
{
	global $db_slave;
	$postIds = array();

	foreach( $posts as $postId => $post )
	{
		if( $post['attach_count'] )
		{
			$postIds[] = $postId;
		}
	}

	if( $postIds )
	{

		$attachments = ModelAttachment_getAttachmentsByContentIds( 'post', $postIds );

		foreach( $attachments as $attachment )
		{
			$posts[$attachment['content_id']]['attachments'][$attachment['attachment_id']] = ModelAttachment_prepareAttachment( $attachment );
		}
	}

	return $posts;
}

function ModelPost_canEditPost( array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $user_info, $global_userid, $lang_module;

	if( ! $global_userid )
	{
		return false;
	}

	if( ! $thread['discussion_open'] && ! ModelThread_canLockUnlockThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
	{
		$errorLangKey = $lang_module['you_may_not_perform_this_action_because_discussion_is_closed'];
		return false;
	}

	if( hasContentPermission( $nodePermissions, 'editAnyPost' ) )
	{
		return true;
	}

	if( $post['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'editOwnPost' ) )
	{
		$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );

		if( $editLimit != -1 && ( ! $editLimit || $post['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
		{
			$errorLangKey = sprintf( $lang_module['message_edit_time_limit_expired'], $editLimit );
			return false;
		}

		if( empty( $forum['allow_posting'] ) )
		{
			$errorLangKey =  $lang_module['you_may_not_perform_this_action_because_forum_does_not_allow_posting'];
			return false;
		}

		return true;
	}

	return false;
}

function ModelPost_canViewPostHistory( array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $user_info, $global_userid;

	if( ! $global_userid )
	{
		return false;
	}
	$editHistory = 1; // cau hinh cho phep sua lich su
	if( ! $editHistory )
	{
		return false;
	}

	if( hasContentPermission( $nodePermissions, 'editAnyPost' ) )
	{
		return true;
	}

	return false;
}

function ModelPost_canDeletePost( array $post, array $thread, array $forum, $deleteType = 'soft', &$errorLangKey = '', array $nodePermissions )
{
	global $user_info, $global_userid, $lang_module;

	if( ! $global_userid )
	{
		return false;
	}

	if( $deleteType != 'soft' && ! hasContentPermission( $nodePermissions, 'hardDeleteAnyPost' ) )
	{
		// fail immediately on hard delete without permission
		return false;
	}

	if( ! $thread['discussion_open'] && ! ModelThread_canLockUnlockThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
	{
		$errorLangKey = $lang_module['you_may_not_perform_this_action_because_discussion_is_closed'];
		return false;
	}

	if( $post['post_id'] == $thread['first_post_id'] )
	{
		// would delete thread, so use that permission
		return ModelThread_canDeleteThread( $thread, $forum, $deleteType, $errorLangKey, $nodePermissions );
	}
	elseif( hasContentPermission( $nodePermissions, 'deleteAnyPost' ) )
	{
		return true;
	}
	elseif( $post['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'deleteOwnPost' ) )
	{
		$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );

		if( $editLimit != -1 && ( ! $editLimit || $post['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
		{
			$errorLangKey = sprintf( $lang_module['message_edit_time_limit_expired'], $editLimit );
			return false;
		}

		if( empty( $forum['allow_posting'] ) )
		{
			$errorLangKey = $lang_module['you_may_not_perform_this_action_because_forum_does_not_allow_posting'];
			return false;
		}

		return true;
	}

	return false;
}
function ModelPost_canUndeletePost( array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null)
{
	global $user_info, $global_userid;

	return ( $global_userid && hasContentPermission( $nodePermissions, 'undelete' ) );
}
function ModelPost_canReportPost( array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	return ModelUser_canReportContent( $errorLangKey );
}

function ModelPost_canWarnPost( array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $user_info, $global_userid;
	if( $post['warning_id'] || empty( $post['userid'] ) )
	{
		return false;
	}

	if( ! empty( $post['is_admin'] ) || ! empty( $post['is_moderator'] ) )
	{
		return false;
	}

	return ( $global_userid && hasContentPermission( $nodePermissions, 'warn' ) );
}
function ModelPost_canApproveUnapprovePost( array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	global $user_info, $global_userid;

	return ( $global_userid && hasContentPermission( $nodePermissions, 'approveUnapprove' ) );
}
function ModelPost_canMovePost( array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null)
{
	global $user_info, $global_userid;
	return ( $global_userid && hasContentPermission( $nodePermissions, 'manageAnyThread' ) );
}
function ModelPost_canCopyPost( array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null)
{
	global $user_info, $global_userid;
	return ( $global_userid && hasContentPermission( $nodePermissions, 'manageAnyThread' ) );
}
function ModelPost_canMergePost( array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null)
{
	global $user_info, $global_userid;
	return ( $global_userid && hasContentPermission( $nodePermissions, 'manageAnyThread' ) );
}
function ModelPost_canControlSilentEdit(array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	global $user_info, $global_userid;
		 
	return ( $global_userid && hasContentPermission($nodePermissions, 'editAnyPost'));
}
function ModelPost_keepOutLinkPost( array $nodePermissions = null)
{
	global $user_info, $global_userid;

	return ( $global_userid && hasContentPermission( $nodePermissions, 'keepOutLink' ) );
}
function ModelPost_keepOutTextLinkPost( array $nodePermissions = null)
{
	global $user_info, $global_userid;

	return ( $global_userid && hasContentPermission( $nodePermissions, 'keepOutTextLink' ) );
}

function ModelPost_addInlineModOptionToPost( array &$post, array $thread, array $forum, array $nodePermissions = null )
{
	global $user_info, $global_userid;

	$postModOptions = array();
	$canInlineMod = ( $global_userid && ( hasContentPermission( $nodePermissions, 'deleteAnyPost' ) || hasContentPermission( $nodePermissions, 'undelete' ) || hasContentPermission( $nodePermissions, 'approveUnapprove' ) || hasContentPermission( $nodePermissions, 'manageAnyThread' ) ) );

	if( $canInlineMod )
	{
		if( ModelPost_canDeletePost( $post, $thread, $forum, 'soft', $null, $nodePermissions ) )
		{
			$postModOptions['delete'] = true;
		}
		if( ModelPost_canUndeletePost( $post, $thread, $forum, $null, $nodePermissions ) )
		{
			$postModOptions['undelete'] = true;
		}
		if( ModelPost_canApproveUnapprovePost( $post, $thread, $forum, $null, $nodePermissions ) )
		{
			$postModOptions['approve'] = true;
			$postModOptions['unapprove'] = true;
		}
		if( ModelPost_canMovePost( $post, $thread, $forum, $null, $nodePermissions ) )
		{
			$postModOptions['move'] = true;
		}
		if( ModelPost_canCopyPost( $post, $thread, $forum, $null, $nodePermissions ) )
		{
			$postModOptions['copy'] = true;
		}
		if( ModelPost_canMergePost( $post, $thread, $forum, $null, $nodePermissions ) )
		{
			$postModOptions['merge'] = true;
		}
	}

	$post['canInlineMod'] = ( count( $postModOptions ) > 0 );

	return $postModOptions;
}
function ModelPost_preparePost( array $post, array $thread, array $forum, array $nodePermissions )
{
	global $user_info, $global_userid, $generalPermissions;
 
	if( ! isset( $post['canInlineMod'] ) )
	{
		ModelPost_addInlineModOptionToPost( $post, $thread, $forum, $nodePermissions );
	}

	$post['canEdit'] = ModelPost_canEditPost( $post, $thread, $forum, $null, $nodePermissions );
	$post['canViewHistory'] = ModelPost_canViewPostHistory( $post, $thread, $forum, $null, $nodePermissions );
	$post['canDelete'] = ModelPost_canDeletePost( $post, $thread, $forum, 'soft', $null, $nodePermissions );
	$post['canLike'] = ModelLike_canLikePost( $post, $thread, $forum, $null, $nodePermissions );
	$post['canReport'] = ModelPost_canReportPost( $post, $thread, $forum, $null, $nodePermissions );
	$post['canWarn'] = ModelPost_canWarnPost( $post, $thread, $forum, $null, $nodePermissions );
	$post['isFirst'] = ( $post['post_id'] == $thread['first_post_id'] );
	$post['isDeleted'] = ModelPost_isDeleted( $post );
	$post['isModerated'] = ModelPost_isModerated( $post );

	if( isset( $thread['thread_read_date'] ) || isset( $forum['forum_read_date'] ) )
	{
		$readOptions = array( 0 );
		if( isset( $thread['thread_read_date'] ) )
		{
			$readOptions[] = $thread['thread_read_date'];
		}
		if( isset( $forum['forum_read_date'] ) )
		{
			$readOptions[] = $forum['forum_read_date'];
		}

		$post['isNew'] = ( max( $readOptions ) < $post['post_date'] );
	}
	else
	{
		$post['isNew'] = false;
	}

	$post['isOnline'] = null;
	if( array_key_exists( 'last_view_date', $post ) && ModelUser_canViewUserOnlineStatus( $post, $null ) )
	{
		$onlineStatusTimeout = 10; // cau hinh thoi gian online

		$onlineCutOff = NV_CURRENTTIME - $onlineStatusTimeout * 60;
		$post['isOnline'] = ( $post['userid'] == $global_userid || $post['last_view_date'] > $onlineCutOff );

	}

	if( array_key_exists( 'user_group_id', $post ) )
	{

		$post = ModelUser_prepareUser( $post );
		$post['canCleanSpam'] = ( ! empty( $post['user_group_id'] ) && hasPermission( $generalPermissions, 'general', 'cleanSpam' ) && ModelUser_couldBeSpammer( $post ) );
	}

	if( ! empty( $post['delete_date'] ) )
	{
		$post['deleteInfo'] = array(
			'userid' => $post['delete_user_id'],
			'username' => $post['delete_username'],
			'date' => $post['delete_date'],
			'reason' => $post['delete_reason'],
			);
	}

	if( $post['likes'] )
	{
		$post['likeUsers'] = unserialize( $post['like_users'] );
	}

	return $post;
}

function ModelPost_getPostInsertMessageState( array $thread, array $forum, array $nodePermissions )
{
	global $user_info, $global_userid, $generalPermissions;

	if( $global_userid && hasContentPermission( $nodePermissions, 'approveUnapprove' ) )
	{
		return 'visible';
	}
	elseif( hasPermission( $generalPermissions, 'general', 'followModerationRules' ) )
	{
		if( empty( $thread['thread_id'] ) )
		{
			// new thread
			return ( empty( $forum['moderate_threads'] ) ? 'visible' : 'moderated' );
		}
		else
		{
			// reply
			return ( empty( $forum['moderate_replies'] ) ? 'visible' : 'moderated' );
		}
	}
	else
	{
		return 'moderated';
	}
}

function ModelPost_getNewestPostsInThreadAfterDate( $threadId, $postDate, $limit )
{
	global $db_slave, $global_userid;

	$data = array();

	$result = $db_slave->query( '
		SELECT post.*
			,
			user.*, IF(user.username IS NULL, post.username, user.username) AS username,
			user_profile.*,
			user_privacy.*,
			deletion_log.delete_date, deletion_log.delete_reason,
			deletion_log.delete_user_id, deletion_log.delete_username,
			session_activity.view_date AS last_view_date,
			liked_content.like_date
		FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post
			LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON
				(user.userid = post.userid)
			LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON
				(user_profile.userid = post.userid)
			LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_privacy AS user_privacy ON
				(user_privacy.userid = post.userid)
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_deletion_log AS deletion_log ON
				(deletion_log.content_type = \'post\' AND deletion_log.content_id = post.post_id)
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_session_activity AS session_activity ON
				(post.userid > 0 AND session_activity.userid = post.userid)
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_liked_content AS liked_content
				ON (liked_content.content_type = \'post\'
					AND liked_content.content_id = post.post_id
					AND liked_content.like_user_id = ' . intval( $global_userid ) . ')
		WHERE post.thread_id = ' . intval( $threadId ) . '
			AND post.post_date > ' . intval( $postDate ) . '
			AND (post.message_state IN (\'visible\',\'deleted\',\'moderated\'))
		ORDER BY post.post_date DESC
	 LIMIT ' . intval( $limit ) );

	while( $row = $result->fetch() )
	{
		$data[$row['post_id']] = $row;
	}
	$result->closeCursor();
	return $data;
}



// post Model
