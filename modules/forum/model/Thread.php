<?php

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}
// Thread Model
function ModelThread_isRedirect( array $thread )
{
	return ( $thread['discussion_type'] == 'redirect' );
}

function ModelThread_isDeleted( array $thread )
{
	return ( $thread['discussion_state'] == 'deleted' );
}

function ModelThread_isModerated( array $thread )
{
	return ( $thread['discussion_state'] == 'moderated' );
}

function ModelThread_isVisible( array $thread )
{
	return ( $thread['discussion_state'] == 'visible' );
}

function ModelThread_isNew( array $thread, array $forum )
{
	if( isset( $thread['thread_read_date'] ) || isset( $forum['forum_read_date'] ) )
	{
		if( ModelThread_isRedirect( $thread ) || ModelThread_isDeleted( $thread ) )
		{
			return false;
		}
		else
		{
			return ( ModelThread_getMaxThreadReadDate( $thread, $forum ) < $thread['last_post_date'] );
		}
	}

	return false;
}
 
function ModelThread_canViewThreadAndContainer(array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
 
	if (!ModelForum_canViewForum($forum, $errorLangKey, $nodePermissions ))
	{
		return false;
	}

	return ModelThread_canViewThread($thread, $forum, $errorLangKey, $nodePermissions);
}
	
function ModelThread_canReplyToThread( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $lang_module, $user_info, $global_userid, $db_slave;
 
	if( ModelThread_isRedirect( $thread ) || ModelThread_isDeleted( $thread ) )
	{
		return false;
	}

	if( ! $thread['discussion_open'] && ! ModelThread_canLockUnlockThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
	{
		$errorLangKey = $lang_module['you_may_not_perform_this_action_because_discussion_is_closed'];
		return false;
	}

	if( empty( $forum['allow_posting'] ) )
	{
		$errorLangKey = $lang_module['you_may_not_perform_this_action_because_forum_does_not_allow_posting'];
		return false;
	}

	if( ! hasContentPermission( $nodePermissions, 'postReply' ) )
	{
		return false;
	}

	if( $global_userid )
	{
		if( ! isset( $thread['thread_reply_banned'] ) )
		{
			$result = $db_slave->query( '
					SELECT expiry_date
					FROM ' . NV_FORUM_GLOBALTABLE . '_thread_reply_ban
					WHERE thread_id = ' . intval( $thread['thread_id'] ) . '
						AND userid = ' . intval( $global_userid ) )->fetch();
			$thread['thread_reply_banned'] = ( $result && ( $result['expiry_date'] === null || $result['expiry_date'] > NV_CURRENTTIME ) );
		}
		if( $thread['thread_reply_banned'] )
		{
			return false;
		}
	}

	return true;
}

function ModelThread_canViewDeletedPosts( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{

	return ( hasContentPermission( $nodePermissions, 'viewDeleted' ) );
}

function ModelThread_canViewModeratedPosts( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	return ( hasContentPermission( $nodePermissions, 'viewModerated' ) );
}

function ModelThread_canLockUnlockThread( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid, $user_info;

	return ( $global_userid && hasContentPermission( $nodePermissions, 'lockUnlockThread' ) );
}

function ModelThread_canDeleteThread( array $thread, array $forum, $deleteType = 'soft', &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid, $user_info, $lang_module;
	if( ! $global_userid )
	{
		return false;
	}

	if( $deleteType != 'soft' && ! hasContentPermission( $nodePermissions, 'hardDeleteAnyThread' ) )
	{
		// fail immediately on hard delete without permission
		return false;
	}

	if( ! $thread['discussion_open'] && ! ModelThread_canLockUnlockThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
	{
		$errorLangKey = $lang_module['you_may_not_perform_this_action_because_discussion_is_closed'];
		return false;
	}

	if( hasContentPermission( $nodePermissions, 'deleteAnyThread' ) )
	{
		return true;
	}
	else
		if( $thread['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'deleteOwnThread' ) )
		{
			$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );

			if( $editLimit != -1 && ( ! $editLimit || $thread['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
			{
 
				$errorLangKey = sprintf( $lang_module['message_edit_time_limit_expired'], $editLimit );
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

function ModelThread_canQuickReply( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $user_info, $global_userid;

	if( ! ModelThread_canReplyToThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
	{
		return false;
	}

	if( ! $global_userid )
	{
		return false;
	}
	else
	{
		return true;
	}
}

function ModelThread_getUserThreadReadDate( $userId, $thread_id )
{
	global $db_slave;
	if( ! $userId )
	{
		return null;
	}

	$readDate = $db_slave->query( '
		SELECT thread_read_date
		FROM ' . NV_FORUM_GLOBALTABLE . '_thread_read
		WHERE userid = ' . intval( $userId ) . ' 
			AND thread_id = ' . intval( $thread_id ) )->fetchColumn();

	$readMarkingDataLifetime = 30; // option

	$autoReadDate = NV_CURRENTTIME - ( $readMarkingDataLifetime * 86400 );
	return max( $readDate, $autoReadDate );
}

function ModelThread_getMaxThreadReadDate( array $thread, array $forum )
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

	return max( $readOptions );
}

function ModelThread_markThreadRead( array $thread, array $forum, $readDate )
{
	global $db_slave, $global_userid;

	if( ! $global_userid )
	{
		return false;
	}

	if( ! array_key_exists( 'thread_read_date', $thread ) )
	{
		$thread['thread_read_date'] = ModelThread_getUserThreadReadDate( $global_userid, $thread['thread_id'] );
	}

	if( $readDate <= ModelThread_getMaxThreadReadDate( $thread, $forum ) )
	{
		return false;
	}

	$db_slave->query( '
			INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_thread_read
				(userid, thread_id, thread_read_date)
			VALUES
				(' . intval( $global_userid ) . ', ' . intval( $thread['thread_id'] ) . ', ' . intval( $readDate ) . ')
			ON DUPLICATE KEY UPDATE thread_read_date = VALUES(thread_read_date)' );

	if( $readDate < $thread['last_post_date'] )
	{
		// we haven't finished reading this thread - forum won't be read
		return false;
	}

	ModelForum_markForumReadIfNeeded( $forum );

	return true;
}

function ModelThread_getNewPosts( array $thread, array $forum, array $nodePermissions, $lastDate, $limit = 3 )
{
	global $user_info, $db_slave, $global_userid, $per_page_post;

	$limit = $limit + 1;
	$posts = ModelPost_getNewestPostsInThreadAfterDate( $thread['thread_id'], $lastDate, $limit );

	// We fetched one more post than needed, if more than $limit posts were returned,
	// we can show the 'there are more posts' notice
	if( count( $posts ) > $limit )
	{
		$postPermissionOptions = ModelPost_getPermissionBasedPostFetchOptions( $thread, $forum, $nodePermissions );
		$firstUnshownPost = ModelPost_getNextPostInThread( $thread['thread_id'], $lastDate, $postPermissionOptions );

		// remove the extra post
		array_pop( $posts );
	}
	else
	{
		$firstUnshownPost = false;
	}

	// put the posts into oldest-first order
	$posts = array_reverse( $posts, true );

	$posts = ModelPost_getAndMergeAttachmentsIntoPosts( $posts );

	foreach( $posts as &$post )
	{
		$post = ModelPost_preparePost( $post, $thread, $forum, $nodePermissions );
	}

	// mark thread as read if we're showing the remaining posts in it or they've been read
	if( $global_userid )
	{
		if( ! $firstUnshownPost || $firstUnshownPost['post_date'] <= $thread['thread_read_date'] )
		{
			ModelThread_markThreadRead( $thread, $forum, NV_CURRENTTIME );
		}
	}

	$pages = floor( $thread['reply_count'] + 1 ) / $per_page_post + 1;

	return ModelThread_exportContent( $forum, $thread, $posts, $pages, $nodePermissions, array(
		'firstUnshownPost' => $firstUnshownPost,
		'lastPost' => end( $posts ),
	) );
}

function ModelThread_canEditThread( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid;
	return ( $global_userid && hasContentPermission( $nodePermissions, 'manageAnyThread' ) );
}

function ModelThread_canEditTags( array $thread = null, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid, $lang_module;

	$enableTagging = 1; // cau hinh

	if( ! $enableTagging )
	{
		return false;
	}

	if( $thread )
	{
		if( ! $thread['discussion_open'] && ! ModelThread_canLockUnlockThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
		{
			$errorLangKey = $lang_module['you_may_not_perform_this_action_because_discussion_is_closed'];
			return false;
		}
	}

	// if no thread, assume the thread will be owned by this person
	if( ! $thread || $thread['userid'] == $global_userid )
	{
		if( hasContentPermission( $nodePermissions, 'tagOwnThread' ) )
		{
			return true;
		}
	}

	if( hasContentPermission( $nodePermissions, 'tagAnyThread' ) || hasContentPermission( $nodePermissions, 'manageAnyTag' ) )
	{
		return true;
	}

	return false;
}
function ModelThread_canReplyBanUserFromThread( array $user = null, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid, $lang_module;

	if( $user )
	{
		if( $user['is_staff'] )
		{
			$errorLangKey = $lang_module['staff_members_cannot_be_reply_banned'];
			return false;
		}
	}

	if( ! $global_userid )
	{
		return false;
	}

	return hasContentPermission( $nodePermissions, 'threadReplyBan' );
}
function ModelThread_canEditThreadTitle( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid, $lang_module;

	if( ! $global_userid )
	{
		return false;
	}

	if( ! $thread['discussion_open'] && ! ModelThread_canLockUnlockThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
	{
		$errorLangKey = $lang_module['you_may_not_perform_this_action_because_discussion_is_closed'];
		return false;
	}

	if( hasContentPermission( $nodePermissions, 'manageAnyThread' ) )
	{
		return true;
	}

	if( $thread['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'editOwnPost' ) )
	{
		$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );

		if( $editLimit != -1 && ( ! $editLimit || $thread['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
		{
			$errorLangKey = sprintf( $lang_module['message_edit_time_limit_expired'], $editLimit );
			return false;
		}

		if( empty( $forum['allow_posting'] ) )
		{
			$errorLangKey = $lang_module['you_may_not_perform_this_action_because_forum_does_not_allow_posting'];
			return false;
		}

		return hasContentPermission( $nodePermissions, 'editOwnThreadTitle' );
	}

	return false;
}

function ModelThread_canAddPoll( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid, $lang_module;
	if( $thread['discussion_type'] !== '' )
	{
		return false;
	}

	if( ! ModelForum_canPostPollInForum( $forum, $null, $nodePermissions ) )
	{
		return false;
	}

	if( ! $global_userid )
	{
		return false;
	}

	if( ! $thread['discussion_open'] && ! ModelThread_canLockUnlockThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
	{
		$errorLangKey = $lang_module['you_may_not_perform_this_action_because_discussion_is_closed'];
		return false;
	}

	if( hasContentPermission( $nodePermissions, 'manageAnyThread' ) )
	{
		return true;
	}

	if( $thread['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'editOwnPost' ) )
	{
		$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );

		if( $editLimit != -1 && ( ! $editLimit || $thread['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
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

function ModelThread_canMoveThread( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid;
	return ( $global_userid && hasContentPermission( $nodePermissions, 'manageAnyThread' ) );
}
function ModelThread_canStickUnstickThread( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid;
	return ( $global_userid && hasContentPermission( $nodePermissions, 'stickUnstickThread' ) );
}
function ModelThread_canWatchThread( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid;
	return ( $global_userid ? true : false );
}
function ModelThread_canViewIps( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid;

	return ModelUser_canViewIps( $errorLangKey );
}
function ModelThread_canUndeleteThread( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	global $global_userid;
	return ( $global_userid && hasContentPermission( $nodePermissions, 'undelete' ) );
}
function ModelThread_canApproveUnapproveThread( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	global $global_userid;
	return ( $global_userid && hasContentPermission( $nodePermissions, 'approveUnapprove' ) );
}
function ModelThread_canMergeThread( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	global $global_userid;
	return ( $global_userid && hasContentPermission( $nodePermissions, 'manageAnyThread' ) );
}
function ModelThread_canViewAttachmentsInThread( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{

	return hasContentPermission( $nodePermissions, 'viewAttachment' );
}

function ModelThread_canViewThread( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	global $global_userid, $user_info, $lang_module;

	if( isset( $thread['thread_user_id'] ) )
	{
		$thread['userid'] = $thread['thread_user_id'];
	}

	if( ! hasContentPermission( $nodePermissions, 'view' ) )
	{
		return false;
	}
	if( ! hasContentPermission( $nodePermissions, 'viewOthers' ) && ( $global_userid != $thread['userid'] || ! $global_userid ) )
	{
		return false;
	}
	if( ! hasContentPermission( $nodePermissions, 'viewContent' ) )
	{
		// TODO: specific error message?
		return false;
	}

	if( ModelThread_isModerated( $thread ) )
	{
		if( ! hasContentPermission( $nodePermissions, 'viewModerated' ) )
		{
			if( ! $global_userid || $global_userid != $thread['userid'] )
			{
				$errorLangKey = $lang_module['requested_thread_not_found'];
				return false;
			}
		}
	}
	else
		if( ModelThread_isDeleted( $thread ) )
		{
			if( ! hasContentPermission( $nodePermissions, 'viewDeleted' ) )
			{
				$errorLangKey = $lang_module['requested_thread_not_found'];
				return false;
			}
		}

	return true;
}

function ModelThread_canViewThreadModeratorLog( array $thread, array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid;
	if( ! $global_userid )
	{
		return false;
	}

	return ( hasContentPermission( $nodePermissions, 'manageAnyThread' ) || hasContentPermission( $nodePermissions, 'editAnyPost' ) || hasContentPermission( $nodePermissions, 'deleteAnyPost' ) || hasContentPermission( $nodePermissions, 'deleteAnyThread' ) || hasContentPermission( $nodePermissions, 'hardDeleteAnyPost' ) || hasContentPermission( $nodePermissions, 'hardDeleteAnyThread' ) );
}

function ModelThread_getPermissionBasedThreadFetchConditions( array $forum, array $nodePermissions = null )
{
	global $global_userid, $user_info;
	if( hasContentPermission( $nodePermissions, 'viewModerated' ) )
	{
		$viewModerated = true;
	}
	elseif( $global_userid )
	{
		$viewModerated = $global_userid;
	}
	else
	{
		$viewModerated = false;
	}

	$conditions = array( 'deleted' => hasContentPermission( $nodePermissions, 'viewDeleted' ), 'moderated' => $viewModerated );

	if( ! hasContentPermission( $nodePermissions, 'viewOthers' ) )
	{
		$conditions['userid'] = $global_userid ? $global_userid : -1;
	}

	return $conditions;
}

function ModelThread_getThreadWatchStateFromThread( array $thread, $useDefaultIfNotWatching = true )
{
	global $global_userid, $user_info;
	if( ! empty( $thread['thread_is_watched'] ) )
	{
		return $thread['thread_is_watched'];
	}
	elseif( $useDefaultIfNotWatching )
	{
		return ( isset( $user_info['default_watch_state'] ) ) ? $user_info['default_watch_state'] : '';
	}
	else
	{
		return '';
	}
}
function ModelThread_canAlterThreadState( array $thread, array $forum, $state, &$errorLangKey = '', array $nodePermissions = null  )
{
	if( $state == $thread['discussion_state'] )
	{
		// not attempting to change, so allow
		return true;
	}

	switch( $state )
	{
		case 'visible':
			{
				if( ModelThread_isModerated( $thread ) && ! ModelThread_canApproveUnapproveThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
				{
					return false;
				}

				if( ModelThread_isDeleted( $thread ) && ! ModelThread_canUndeleteThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
				{
					return false;
				}

				break;
			}

		case 'moderated':
			{
				if( ModelThread_isVisible( $thread ) && ! ModelThread_canApproveUnapproveThread( $thread, $forum, $errorLangKey, $nodePermissions ) )
				{
					return false;
				}

				if( ModelThread_isDeleted( $thread ) && ( ! ModelThread_canUndeleteThread( $thread, $forum, $errorLangKey, $nodePermissions ) || ! ModelThread_canApproveUnapproveThread( $thread, $forum, $errorLangKey, $nodePermissions ) ) )
				{
					return false;
				}

				break;
			}

		case 'deleted':
			{
				if( ! ModelThread_canDeleteThread( $thread, $forum, 'soft', $errorLangKey, $nodePermissions ) )
				{
					return false;
				}

				break;
			}

		default:
			{
				return false;
			}
	}

	return true;
}

function ModelThread_exportContent( array $forum, array $thread, array $posts, $page = 1, $nodePermissions, array $viewParams = array() )
{
	global $per_page_post;

	$page = max( 1, $page );

	return array(
		'thread' => $thread,
		'forum' => $forum,
		'posts' => $posts,
		'ignoredNames' => '',
		'page' => $page,
		'postsPerPage' => $per_page_post,
		'totalPosts' => $thread['reply_count'] + 1,
		'postsRemaining' => max( 0, $thread['reply_count'] + 1 - ( $page * $per_page_post ) ),
		'canReply' => ModelThread_canReplyToThread( $thread, $forum, $null, $nodePermissions ),
		'canQuickReply' => ModelThread_canQuickReply( $thread, $forum, $null, $nodePermissions ),
		'canEditThread' => ModelThread_canEditThread( $thread, $forum, $null, $nodePermissions ),
		'canEditTags' => ModelThread_canEditTags( $thread, $forum, $null, $nodePermissions ),
		'canReplyBan' => ModelThread_canReplyBanUserFromThread( null, $thread, $forum, $null, $nodePermissions ),
		'canEditTitle' => ModelThread_canEditThreadTitle( $thread, $forum, $null, $nodePermissions ),
		'canAddPoll' => ModelThread_canAddPoll( $thread, $forum, $null, $nodePermissions ),
		'canDeleteThread' => ModelThread_canDeleteThread( $thread, $forum, 'soft', $null, $nodePermissions ),
		'canMoveThread' => ModelThread_canMoveThread( $thread, $forum, $null, $nodePermissions ),
		'canStickUnstickThread' => ModelThread_canStickUnstickThread( $thread, $forum, $null, $nodePermissions ),
		'canLockUnlockThread' => ModelThread_canLockUnlockThread( $thread, $forum, $null, $nodePermissions ),
		'canWatchThread' => ModelThread_canWatchThread( $thread, $forum, $null, $nodePermissions ),
		'canViewIps' => ModelThread_canViewIps( $thread, $forum, $null, $nodePermissions ),
		'canViewAttachments' => ModelThread_canViewAttachmentsInThread( $thread, $forum, $null, $nodePermissions ),
		'canViewModeratorLog' => ModelThread_canViewThreadModeratorLog( $thread, $forum, $null, $nodePermissions ),
		'canViewWarnings' => ModelUser_canViewWarnings(),
		'watchState' => ModelThread_getThreadWatchStateFromThread( $thread ),
		) + $viewParams;
}

function ModelThread_addInlineModOptionToThread( array &$thread, array $forum, array $nodePermissions = null )
{
	global $global_userid, $user_info;
	$modOptions = array();
	$canInlineMod = ( $global_userid && ( hasContentPermission( $nodePermissions, 'deleteAnyThread' ) || hasContentPermission( $nodePermissions, 'undelete' ) || hasContentPermission( $nodePermissions, 'approveUnapprove' ) || hasContentPermission( $nodePermissions, 'lockUnlockThread' ) || hasContentPermission( $nodePermissions, 'stickUnstickThread' ) || hasContentPermission( $nodePermissions, 'manageAnyThread' ) ) );

	if( $canInlineMod )
	{
		if( ModelThread_canDeleteThread( $thread, $forum, 'soft', $null, $nodePermissions ) )
		{
			$modOptions['delete'] = true;
		}
		if( ModelThread_canUndeleteThread( $thread, $forum, $null, $nodePermissions ) )
		{
			$modOptions['undelete'] = true;
		}
		if( ModelThread_canApproveUnapproveThread( $thread, $forum, $null, $nodePermissions ) )
		{
			$modOptions['approve'] = true;
			$modOptions['unapprove'] = true;
		}
		if( ModelThread_canLockUnlockThread( $thread, $forum, $null, $nodePermissions ) )
		{
			$modOptions['lock'] = true;
			$modOptions['unlock'] = true;
		}
		if( ModelThread_canStickUnstickThread( $thread, $forum, $null, $nodePermissions ) )
		{
			$modOptions['stick'] = true;
			$modOptions['unstick'] = true;
		}
		if( ModelThread_canMoveThread( $thread, $forum, $null, $nodePermissions ) )
		{
			$modOptions['move'] = true;
		}
		if( ModelThread_canMergeThread( $thread, $forum, $null, $nodePermissions ) )
		{
			$modOptions['merge'] = true;
		}
		if( ModelThread_canEditThread( $thread, $forum, $null, $nodePermissions ) )
		{
			$modOptions['edit'] = true;
		}
	}

	$thread['canInlineMod'] = ( count( $modOptions ) > 0 );

	return $modOptions;
}
function ModelThread_hasPreview( array $thread, array $forum, array $nodePermissions = null )
{
	$discussionPreviewLength = 200;
	return ( $thread['first_post_id'] && $discussionPreviewLength && ModelThread_isRedirect( $thread ) == false && hasContentPermission( $nodePermissions, 'viewContent' ) );
}

function _getLastPageNumbers( $replyCount, $perPage = null, $maxLinks = null )
{
	global $per_page_post;
	$lastPageLinks = 3; // cau hinh
	if( $perPage === null )
	{
		$perPage = $per_page_post;
	}

	if( $maxLinks === null )
	{
		$maxLinks = $lastPageLinks;
	}

	$pageCount = ceil( ( $replyCount + 1 ) / $perPage );

	$startPage = max( 2, $pageCount - ( $maxLinks - 1 ) );

	$pages = array();
	for( $i = $startPage; $i <= $pageCount; $i++ )
	{
		$pages[] = $i;
	}

	return $pages;
}

function ModelThread_getLastPageNumbers( $replyCount )
{
	global $per_page_post;
	$perPage = $per_page_post; // cau hinh

	if( ( $replyCount + 1 ) > $perPage )
	{
		return _getLastPageNumbers( $replyCount, $perPage );
	}
	else
	{
		return false;
	}
}
function ModelThread_prepareThread( array $thread, array $forum, array $nodePermissions = null )
{
	global $global_userid, $user_info;

	if( isset( $thread['node_title'] ) )
	{
		$thread['forum'] = array(
			'node_id' => $thread['node_id'],
			'title' => $thread['node_title'],
			'node_name' => isset( $thread['node_name'] ) ? $thread['node_name'] : null );
	}

	if( $thread['view_count'] <= $thread['reply_count'] )
	{
		$thread['view_count'] = $thread['reply_count'] + 1;
	}

	if( ! empty( $thread['delete_date'] ) )
	{
		$thread['deleteInfo'] = array(
			'userid' => $thread['delete_user_id'],
			'username' => $thread['delete_username'],
			'date' => $thread['delete_date'],
			'reason' => $thread['delete_reason'],
			);
	}

	if( ! isset( $thread['canInlineMod'] ) )
	{
		ModelThread_addInlineModOptionToThread( $thread, $forum, $nodePermissions );
	}

	$thread['canEditThread'] = ModelThread_canEditThread( $thread, $forum, $null, $nodePermissions );

	$thread['isNew'] = ModelThread_isNew( $thread, $forum );
	if( $thread['isNew'] )
	{
		$readDate = ModelThread_getMaxThreadReadDate( $thread, $forum );
		$readMarkingDataLifetime = 30; // cau hinh
		$thread['haveReadData'] = ( $readDate > NV_CURRENTTIME - ( $readMarkingDataLifetime * 86400 ) );
	}
	else
	{
		$thread['haveReadData'] = false;
	}

	$thread['hasPreview'] = ModelThread_hasPreview( $thread, $forum, $nodePermissions );
	$thread['canViewContent'] = ModelForum_canViewForumContent( $forum, $null, $nodePermissions );

	$thread['isRedirect'] = ModelThread_isRedirect( $thread );
	$thread['isDeleted'] = ModelThread_isDeleted( $thread );
	$thread['isModerated'] = ModelThread_isModerated( $thread );

	$thread['title'] = $thread['title'];
	$thread['titleCensored'] = true;

	$thread['lastPageNumbers'] = ModelThread_getLastPageNumbers( $thread['reply_count'] );

	$thread['lastPostInfo'] = array(
		'post_date' => $thread['last_post_date'],
		'post_id' => $thread['last_post_id'],
		'userid' => $thread['last_post_user_id'],
		'username' => $thread['last_post_username'],
		'isIgnoring' => '' );
	if( isset( $thread['last_post_gender'] ) )
	{
		$thread['lastPostInfo']['gender'] = $thread['last_post_gender'];
		$thread['lastPostInfo']['photo'] = $thread['last_post_photo'];
	}

	if( array_key_exists( 'user_group_id', $thread ) )
	{
		$thread = ModelUser_prepareUser( $thread );
	}

	$thread['tagsList'] = $thread['tags'] ? @unserialize( $thread['tags'] ) : array();

	return $thread;
}

function ModelThread_logThreadView( $threadId )
{
	global $db;

	$enableInsertDelayed = 'DELAYED';

	$db->query( 'INSERT ' . $enableInsertDelayed . ' INTO ' . NV_FORUM_GLOBALTABLE . '_thread_view(thread_id) VALUES (' . intval( $threadId ) . ') ' );

}
function ModelThread_canEditPoll( array $poll, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null )
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

	if( hasContentPermission( $nodePermissions, 'manageAnyThread' ) )
	{
		return true;
	}

	if( $thread['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'editOwnPost' ) )
	{
		$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );

		if( $editLimit != -1 && ( ! $editLimit || $thread['post_date'] < NV_CURRENTTIME - 60 * $editLimit ) )
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
function ModelThread_canVoteOnPoll( array $poll, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	global $user_info, $global_userid, $lang_module;
	if( ! $thread['discussion_open'] )
	{
		$errorLangKey = $lang_module['you_may_not_perform_this_action_because_discussion_is_closed'];
		return false;
	}

	if( empty( $forum['allow_posting'] ) )
	{
		$errorLangKey = $lang_module['you_may_not_perform_this_action_because_forum_does_not_allow_posting'];
		return false;
	}

	return ( $global_userid && hasContentPermission( $nodePermissions, 'votePoll' ) );
}

function ModelThread_countThreadsInForum( $forumId, array $conditions = array() )
{
	$conditions['forum_id'] = $forumId;
	return ModelThread_countThreads( $conditions );
}
function ModelThread_prepareThreadConditions( array $conditions, array &$fetchOptions )
{
	global $db_slave;

	$sqlConditions = array();

	if( ! empty( $conditions['thread_id_gt'] ) )
	{
		$sqlConditions[] = 'thread.thread_id > ' . $db_slave->quote( $conditions['thread_id_gt'] );
	}

	if( ! empty( $conditions['title'] ) )
	{
		if( is_array( $conditions['title'] ) )
		{
			$sqlConditions[] = 'thread.title LIKE ' . $db_slave->dblikeescape( $conditions['title'][0], $conditions['title'][1] );
		}
		else
		{
			$sqlConditions[] = 'thread.title LIKE ' . $db_slave->dblikeescape( $conditions['title'], 'lr' );
		}
	}

	if( ! empty( $conditions['forum_id'] ) && empty( $conditions['node_id'] ) )
	{
		$conditions['node_id'] = $conditions['forum_id'];
	}

	if( ! empty( $conditions['node_id'] ) )
	{
		if( is_array( $conditions['node_id'] ) )
		{
			$sqlConditions[] = 'thread.node_id IN (' . $db_slave->quote( $conditions['node_id'] ) . ')';
		}
		else
		{
			$sqlConditions[] = 'thread.node_id = ' . $db_slave->quote( $conditions['node_id'] );
		}
	}

	if( ! empty( $conditions['discussion_type'] ) )
	{
		if( is_array( $conditions['discussion_type'] ) )
		{
			$sqlConditions[] = 'thread.discussion_type IN (' . $db_slave->quote( $conditions['discussion_type'] ) . ')';
		}
		else
		{
			$sqlConditions[] = 'thread.discussion_type = ' . $db_slave->quote( $conditions['discussion_type'] );
		}
	}

	if( ! empty( $conditions['not_discussion_type'] ) )
	{
		if( is_array( $conditions['not_discussion_type'] ) )
		{
			$sqlConditions[] = 'thread.discussion_type NOT IN (' . $db_slave->quote( $conditions['not_discussion_type'] ) . ')';
		}
		else
		{
			$sqlConditions[] = 'thread.discussion_type <> ' . $db_slave->quote( $conditions['not_discussion_type'] );
		}
	}

	if( ! empty( $conditions['prefix_id'] ) )
	{
		if( is_array( $conditions['prefix_id'] ) )
		{
			if( in_array( -1, $conditions['prefix_id'] ) )
			{
				$conditions['prefix_id'][] = 0;
			}
			$sqlConditions[] = 'thread.prefix_id IN (' . $db_slave->quote( $conditions['prefix_id'] ) . ')';
		}
		else
			if( $conditions['prefix_id'] == -1 )
			{
				$sqlConditions[] = 'thread.prefix_id = 0';
			}
			else
			{
				$sqlConditions[] = 'thread.prefix_id = ' . $db_slave->quote( $conditions['prefix_id'] );
			}
	}

	if( isset( $conditions['sticky'] ) )
	{
		$sqlConditions[] = 'thread.sticky = ' . ( $conditions['sticky'] ? 1 : 0 );
	}

	if( isset( $conditions['discussion_open'] ) )
	{
		$sqlConditions[] = 'thread.discussion_open = ' . ( $conditions['discussion_open'] ? 1 : 0 );
	}

	if( ! empty( $conditions['discussion_state'] ) )
	{
		if( is_array( $conditions['discussion_state'] ) )
		{
			$sqlConditions[] = 'thread.discussion_state IN (' . $db_slave->quote( $conditions['discussion_state'] ) . ')';
		}
		else
		{
			$sqlConditions[] = 'thread.discussion_state = ' . $db_slave->quote( $conditions['discussion_state'] );
		}
	}

	if( isset( $conditions['deleted'] ) || isset( $conditions['moderated'] ) )
	{
		$sqlConditions[] = prepareStateLimitFromConditions( $conditions, 'thread', 'discussion_state' );
	}

	if( ! empty( $conditions['last_post_date'] ) && is_array( $conditions['last_post_date'] ) )
	{
		$sqlConditions[] = getCutOffCondition( "thread.last_post_date", $conditions['last_post_date'] );
	}

	if( ! empty( $conditions['post_date'] ) && is_array( $conditions['post_date'] ) )
	{
		$sqlConditions[] = getCutOffCondition( "thread.post_date", $conditions['post_date'] );
	}

	if( ! empty( $conditions['reply_count'] ) && is_array( $conditions['reply_count'] ) )
	{
		$sqlConditions[] = getCutOffCondition( "thread.reply_count", $conditions['reply_count'] );
	}

	if( ! empty( $conditions['first_post_likes'] ) && is_array( $conditions['first_post_likes'] ) )
	{
		$sqlConditions[] = getCutOffCondition( "thread.first_post_likes", $conditions['first_post_likes'] );
	}

	if( ! empty( $conditions['view_count'] ) && is_array( $conditions['view_count'] ) )
	{
		$sqlConditions[] = getCutOffCondition( "thread.view_count", $conditions['view_count'] );
	}

	// fetch threads only from forums with find_new = 1
	if( ! empty( $conditions['find_new'] ) && isset( $fetchOptions['join'] ) && $fetchOptions['join'] & self::FETCH_FORUM_OPTIONS )
	{
		$sqlConditions[] = 'forum.find_new = 1';
	}

	// thread starter
	if( isset( $conditions['userid'] ) )
	{
		if( is_array( $conditions['userid'] ) )
		{
			$sqlConditions[] = 'thread.userid IN (' . $db_slave->quote( $conditions['userid'] ) . ')';
		}
		else
		{
			$sqlConditions[] = 'thread.userid = ' . $db_slave->quote( $conditions['userid'] );
		}
	}

	// watch limit
	if( ! empty( $conditions['watch_only'] ) )
	{
		$parts = array();
		if( ! empty( $fetchOptions['forumWatchUserId'] ) )
		{
			$parts[] = 'forum_watch.node_id IS NOT NULL';
		}
		if( ! empty( $fetchOptions['watchUserId'] ) )
		{
			$parts[] = 'thread_watch.thread_id IS NOT NULL';
		}
		if( ! $parts )
		{
			$sqlConditions[] = '0'; // no watch info - return nothing
		}
		else
		{
			$sqlConditions[] = '(' . implode( ' OR ', $parts ) . ')';
		}
	}

	return getConditionsForClause( $sqlConditions );
}
function ModelThread_prepareThreadFetchOptions( array $fetchOptions )
{
	global $db_slave, $global_userid, $user_info, $modelThreadConst;
	$selectFields = '';
	$joinTables = '';
	$orderBy = '';

	if( ! empty( $fetchOptions['order'] ) )
	{
		$orderBySecondary = '';

		switch( $fetchOptions['order'] )
		{
			case 'title':
			case 'post_date':
			case 'view_count':
				$orderBy = 'thread.' . $fetchOptions['order'];
				break;

			case 'reply_count':
			case 'first_post_likes':
				$orderBy = 'thread.' . $fetchOptions['order'];
				$orderBySecondary = ', thread.last_post_date DESC';
				break;

			case 'last_post_date':
			default:
				$orderBy = 'thread.last_post_date';
		}
		if( ! isset( $fetchOptions['orderDirection'] ) || $fetchOptions['orderDirection'] == 'desc' )
		{
			$orderBy .= ' DESC';
		}
		else
		{
			$orderBy .= ' ASC';
		}

		$orderBy .= $orderBySecondary;
	}

	if( ! empty( $fetchOptions['join'] ) )
	{
		if( $fetchOptions['join'] & $modelThreadConst['user'] )
		{
			$selectFields .= ',
					user.*, IF(user.username IS NULL, thread.username, user.username) AS username';
			$joinTables .= '
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON
						(user.userid = thread.userid)';
		}
		else
			if( $fetchOptions['join'] & $modelThreadConst['avatar'] )
			{
				$selectFields .= ',
					user.gender, user.avatar_date, user.gravatar';
				$joinTables .= '
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON
						(user.userid = thread.userid)';
			}

		if( $fetchOptions['join'] & $modelThreadConst['last_post_avatar'] )
		{
			$selectFields .= ',
					last_post_user.gender AS last_post_gender,
					last_post_user.photo AS last_post_photo,
					IF(last_post_user.username IS NULL, thread.last_post_username, last_post_user.username) AS last_post_username';
			$joinTables .= '
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS last_post_user ON
						(last_post_user.userid = thread.last_post_user_id)';
		}

		if( $fetchOptions['join'] & $modelThreadConst['forum'] )
		{
			$selectFields .= ',
					node.title AS node_title, node.description';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON
						(node.node_id = thread.node_id)';
		}

		if( $fetchOptions['join'] & $modelThreadConst['forum_option'] )
		{
			$selectFields .= ',
					forum.*,
					forum.last_post_id AS forum_last_post_id,
					forum.last_post_date AS forum_last_post_date,
					forum.last_post_user_id AS forum_last_post_user_id,
					forum.last_post_username AS forum_last_post_username,
					forum.last_thread_title AS forum_last_thread_title,
					thread.last_post_id,
					thread.last_post_date,
					thread.last_post_user_id,
					thread.last_post_username';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum AS forum ON
						(forum.node_id = thread.node_id)';
		}

		if( $fetchOptions['join'] & $modelThreadConst['firstpost'] )
		{
			$selectFields .= ',
					post.message, post.attach_count';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_post AS post ON
						(post.post_id = thread.first_post_id)';
		}

		if( $fetchOptions['join'] & $modelThreadConst['deletion_log'] )
		{
			$selectFields .= ',
					deletion_log.delete_date, deletion_log.delete_reason,
					deletion_log.delete_user_id, deletion_log.delete_username';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_deletion_log AS deletion_log ON
						(deletion_log.content_type = \'thread\' AND deletion_log.content_id = thread.thread_id)';
		}
	}

	if( isset( $fetchOptions['readUserId'] ) )
	{
		if( ! empty( $fetchOptions['readUserId'] ) )
		{
			$readMarkingDataLifetime = 30; // cau hinh
			$autoReadDate = NV_CURRENTTIME - $readMarkingDataLifetime * 86400;

			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_read AS thread_read ON
						(thread_read.thread_id = thread.thread_id
						AND thread_read.userid = ' . $db_slave->quote( $fetchOptions['readUserId'] ) . ')';

			$joinForumRead = ( ! empty( $fetchOptions['includeForumReadDate'] ) || ( ! empty( $fetchOptions['join'] ) && $fetchOptions['join'] & $modelThreadConst['forum'] ) );
			if( $joinForumRead )
			{
				$joinTables .= '
						LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum_read AS forum_read ON
							(forum_read.node_id = thread.node_id
							AND forum_read.userid = ' . $db_slave->quote( $fetchOptions['readUserId'] ) . ')';

				$selectFields .= ",
						GREATEST(COALESCE(thread_read.thread_read_date, 0), COALESCE(forum_read.forum_read_date, 0), $autoReadDate) AS thread_read_date";
			}
			else
			{
				$selectFields .= ",
						IF(thread_read.thread_read_date > $autoReadDate, thread_read.thread_read_date, $autoReadDate) AS thread_read_date";
			}
		}
		else
		{
			$selectFields .= ',
					NULL AS thread_read_date';
		}
	}

	if( isset( $fetchOptions['replyBanUserId'] ) )
	{
		if( ! empty( $fetchOptions['replyBanUserId'] ) )
		{
			$selectFields .= ',
					IF(reply_ban.userid IS NULL, 0,
						IF(reply_ban.expiry_date IS NULL OR reply_ban.expiry_date > ' . $db_slave->quote( NV_CURRENTTIME ) . ', 1, 0)) AS thread_reply_banned';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_reply_ban AS reply_ban
						ON (reply_ban.thread_id = thread.thread_id
						AND reply_ban.userid = ' . $db_slave->quote( $fetchOptions['replyBanUserId'] ) . ')';
		}
		else
		{
			$selectFields .= ',
					0 AS thread_reply_banned';
		}
	}

	if( isset( $fetchOptions['watchUserId'] ) )
	{
		if( ! empty( $fetchOptions['watchUserId'] ) )
		{
			$selectFields .= ',
					IF(thread_watch.userid IS NULL, 0,
						IF(thread_watch.email_subscribe, \'watch_email\', \'watch_no_email\')) AS thread_is_watched';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_watch AS thread_watch
						ON (thread_watch.thread_id = thread.thread_id
						AND thread_watch.userid = ' . $db_slave->quote( $fetchOptions['watchUserId'] ) . ')';
		}
		else
		{
			$selectFields .= ',
					0 AS thread_is_watched';
		}
	}

	if( isset( $fetchOptions['forumWatchUserId'] ) )
	{
		if( ! empty( $fetchOptions['forumWatchUserId'] ) )
		{
			$selectFields .= ',
					IF(forum_watch.userid IS NULL, 0, 1) AS forum_is_watched';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_forum_watch AS forum_watch
						ON (forum_watch.node_id = thread.node_id
						AND forum_watch.userid = ' . $db_slave->quote( $fetchOptions['forumWatchUserId'] ) . ')';
		}
		else
		{
			$selectFields .= ',
					0 AS forum_is_watched';
		}
	}

	if( isset( $fetchOptions['draftUserId'] ) )
	{
		if( ! empty( $fetchOptions['draftUserId'] ) )
		{
			$selectFields .= ',
					draft.message AS draft_message, draft.extra_data AS draft_extra';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_draft AS draft
						ON (draft.draft_key = CONCAT(\'thread-\', thread.thread_id)
						AND draft.userid = ' . $db_slave->quote( $fetchOptions['draftUserId'] ) . ')';
		}
		else
		{
			$selectFields .= ',
					\'\' AS draft_message, NULL AS draft_extra';
		}
	}

	if( isset( $fetchOptions['postCountUserId'] ) )
	{
		if( ! empty( $fetchOptions['postCountUserId'] ) )
		{
			$selectFields .= ',
					thread_user_post.post_count AS user_post_count';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_user_post AS thread_user_post
						ON (thread_user_post.thread_id = thread.thread_id
						AND thread_user_post.userid = ' . $db_slave->quote( $fetchOptions['postCountUserId'] ) . ')';
		}
		else
		{
			$selectFields .= ',
					0 AS user_post_count';
		}
	}

	if( ! empty( $fetchOptions['permissionCombinationId'] ) )
	{
		$selectFields .= ',
				permission.cache_value AS node_permission_cache';
		$joinTables .= '
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
					ON (permission.permission_combination_id = ' . $db_slave->quote( $fetchOptions['permissionCombinationId'] ) . '
						AND permission.content_type = \'node\'
						AND permission.content_id = thread.node_id)';
	}

	return array(
		'selectFields' => $selectFields,
		'joinTables' => $joinTables,
		'orderClause' => ( $orderBy ? "ORDER BY $orderBy" : '' ) );
}


function ModelThread_getThreadById($threadId, array $fetchOptions = array())
{
	global $db_slave;
	
	$joinOptions = ModelThread_prepareThreadFetchOptions($fetchOptions);

	return $db_slave->query('
			SELECT thread.*
				' . $joinOptions['selectFields'] . '
			FROM '. NV_FORUM_GLOBALTABLE .'_thread AS thread
			' . $joinOptions['joinTables'] . '
			WHERE thread.thread_id = '. intval( $threadId ) )->fetch();
 
}

function ModelThread_countThreads( array $conditions )
{
	global $db_slave;
	$fetchOptions = array();
	$whereConditions = ModelThread_prepareThreadConditions( $conditions, $fetchOptions );
	$sqlClauses = ModelThread_prepareThreadFetchOptions( $fetchOptions );

	return $db_slave->query( '
			SELECT COUNT(*)
			FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
			' . $sqlClauses['joinTables'] . '
			WHERE ' . $whereConditions . '
		' )->fetchColumn();
}
function ModelThread_getThreads( array $conditions, array $fetchOptions = array() )
{
	global $db_slave;
	$whereConditions = ModelThread_prepareThreadConditions( $conditions, $fetchOptions );

	$sqlClauses = ModelThread_prepareThreadFetchOptions( $fetchOptions );
	$limitOptions = prepareLimitFetchOptions( $fetchOptions );

	$forceIndex = ( ! empty( $fetchOptions['forceThreadIndex'] ) ? 'FORCE INDEX (' . $fetchOptions['forceThreadIndex'] . ')' : '' );
	
	$data = array();
	
	$limit = '';
	if( $limitOptions )
	{
		$limit = ' LIMIT ' . intval( $limitOptions['offset'] ) . ', ' . intval( $limitOptions['limit'] );
	}
 
	$result = $db_slave->query('SELECT thread.*
					' . $sqlClauses['selectFields'] . '
				FROM '. NV_FORUM_GLOBALTABLE .'_thread AS thread ' . $forceIndex . '
				' . $sqlClauses['joinTables'] . '
				WHERE ' . $whereConditions . '
				' . $sqlClauses['orderClause'] . $limit);
	
	while( $rows = $result->fetch() )
	{
		
		$data[$rows['thread_id']] = $rows;
	}
	$result->closeCursor();	
	
	return $data;
 
}
function ModelThread_getThreadsInForum( $forumId, array $conditions = array(), array $fetchOptions = array() )
{
	$conditions['forum_id'] = $forumId;
	return ModelThread_getThreads( $conditions, $fetchOptions );
}
function ModelThread_getStickyThreadsInForum($forumId, array $conditions = array(), array $fetchOptions = array())
{
	$conditions['forum_id'] = $forumId;
	$conditions['sticky'] = 1;
	return ModelThread_getThreads($conditions, $fetchOptions);
}

function ModelThread_sendModeratorActionAlert( $action, array $thread, $reason = '', array $extra = array(), $alertUserId = null, $slient = 0 )
{
	global $db, $global_config, $global_userid, $user_info, $module_name;
	$link = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . strtolower( change_alias( $thread['title'] ) ) . '-' . $thread['thread_id'] . $global_config['rewrite_exturl'], true );

	$extra = array_merge( array(
		'title' => $thread['title'],
		'link' => $link,
		'reason' => $reason ), $extra );

	if( $alertUserId === null )
	{
		$alertUserId = $thread['userid'];
	}

	if( ! $alertUserId )
	{
		return false;
	}
	
	if( $slient )
	{
		$deleteUserid = $user_info['userid'];
		$deleteUsername = $user_info['username'];
	}else{
		
		$deleteUserid = 0;
		$deleteUsername = '';
	}
	// thong bao toi tai khoan bi xoa chu de
	$db->query('INSERT INTO '. NV_USERS_GLOBALTABLE .'_alert (alerted_userid, userid, username, content_type, content_id, action, extra_data, event_date) VALUES ('. intval( $alertUserId ) .', '. intval( $deleteUserid ) .', '. $db->quote( $deleteUsername ) .', \'user\', '. intval( $alertUserId ) .', \'thread_delete\', '. $db->quote( serialize( $extra ) ) .', '. NV_CURRENTTIME .')');

	return true;
}


function ModelThread_deleteThread($thread, $deleteType, array $options = array())
{
	global $db, $db_slave, $global_userid, $user_info ;
	
 
	$return = false;
	if ($deleteType == 'hard')
	{
		$fetchresult = $db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread WHERE thread_id='. intval( $thread['thread_id'] ) );
		if( $fetchresult->rowCount() )
		{
			$return = true;
		}
		$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread_watch WHERE thread_id='. intval( $thread['thread_id'] ) );
		$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread_user_post WHERE thread_id='. intval( $thread['thread_id'] ) );
		$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread_reply_ban WHERE thread_id='. intval( $thread['thread_id'] ) );
		
		$forum = $db->query('SELECT node.*, forum.*				
				FROM '. NV_FORUM_GLOBALTABLE .'_forum AS forum
				INNER JOIN '. NV_FORUM_GLOBALTABLE .'_node AS node ON (node.node_id = forum.node_id)		
				WHERE node.node_id = ' . intval( $thread['node_id'] ) )->fetch();
			

		if( $thread['discussion_state'] == 'visible' )
		{
			$forum['discussion_count'] =  $forum['discussion_count'] - 1;
			$forum['message_count'] = $forum['message_count'] - $thread['reply_count'] - 1;

			$lastPost = ModelThread_getLastUpdatedThreadInForum( $thread['node_id'] );
			if( $lastPost )
			{
				$data['last_post_id'] = $lastPost['last_post_id'];
				$data['last_post_date'] = $lastPost['last_post_date'];
				$data['last_post_user_id'] = $lastPost['last_post_user_id'];
				$data['last_post_username'] = $lastPost['last_post_username'];
				$data['last_thread_title'] = $lastPost['title'];
					 
			}else{
				$data['last_post_id'] = 0;
				$data['last_post_date'] = 0;
				$data['last_post_user_id'] = 0;
				$data['last_post_username'] = 0;
				$data['last_thread_title'] = '';
			}
	 
			$db->query('UPDATE '. NV_FORUM_GLOBALTABLE .'_forum SET 
				discussion_count = '. intval( $forum['discussion_count'] ) .', 
				message_count = '. intval( $forum['message_count'] ) .', 
				last_post_id = '. intval( $data['last_post_id'] ) .', 
				last_post_date = '. intval( $data['last_post_date'] ) .', 
				last_post_user_id = '. intval( $data['last_post_user_id'] ) .', 
				last_post_username = '. $db->quote( $data['last_post_username'] ) .', 
				last_thread_title = '. $db->quote( $data['last_thread_title'] ) .' 
			WHERE node_id = ' . intval( $thread['node_id'] ) );
		}   
		
		$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_deletion_log WHERE (content_type = \'thread\' AND content_id IN ('. intval( $thread['thread_id'] ) .'))');
		  
		$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_moderation_queue WHERE (content_type = \'thread\' AND content_id IN ('. intval( $thread['thread_id'] ) .'))');
		 
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
							$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_attachment_data
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

			$result = $db_slave->query( 'SELECT content_user_id, COUNT(*) as like_count FROM ' . NV_FORUM_GLOBALTABLE . '_liked_content WHERE content_type = \'post\' AND content_id IN (' . implode( ',', $post_array_key ) . ') GROUP BY content_user_id' );
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
		
		///////////
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
		
		$return = true;
	}
	else
	{
 
		$fetchresult = $db->query('UPDATE '. NV_FORUM_GLOBALTABLE .'_thread SET discussion_state = \'deleted\' WHERE thread_id = '. intval( $thread['thread_id'] ) );
		
		if( $fetchresult->rowCount() )
		{
 
			$forum = $db->query('SELECT node.*, forum.*				
				FROM '. NV_FORUM_GLOBALTABLE .'_forum AS forum
				INNER JOIN '. NV_FORUM_GLOBALTABLE .'_node AS node ON (node.node_id = forum.node_id)		
				WHERE node.node_id = ' . intval( $thread['node_id'] ) )->fetch();
			

			if( $thread['discussion_state'] == 'visible' )
			{
				$forum['discussion_count'] =  $forum['discussion_count'] - 1;
				$forum['message_count'] = $forum['message_count'] - $thread['reply_count'] - 1;

				$lastPost = ModelThread_getLastUpdatedThreadInForum( $thread['node_id'] );
				if( $lastPost )
				{
					$data['last_post_id'] = $lastPost['last_post_id'];
					$data['last_post_date'] = $lastPost['last_post_date'];
					$data['last_post_user_id'] = $lastPost['last_post_user_id'];
					$data['last_post_username'] = $lastPost['last_post_username'];
					$data['last_thread_title'] = $lastPost['title'];
					 
				}else{
					$data['last_post_id'] = 0;
					$data['last_post_date'] = 0;
					$data['last_post_user_id'] = 0;
					$data['last_post_username'] = 0;
					$data['last_thread_title'] = '';
				}
	 
				$db->query('UPDATE '. NV_FORUM_GLOBALTABLE .'_forum SET 
					discussion_count = '. intval( $forum['discussion_count'] ) .', 
					message_count = '. intval( $forum['message_count'] ) .', 
					last_post_id = '. intval( $data['last_post_id'] ) .', 
					last_post_date = '. intval( $data['last_post_date'] ) .', 
					last_post_user_id = '. intval( $data['last_post_user_id'] ) .', 
					last_post_username = '. $db->quote( $data['last_post_username'] ) .', 
					last_thread_title = '. $db->quote( $data['last_thread_title'] ) .' 
				WHERE node_id = ' . intval( $thread['node_id'] ) );
			}
		
			$db->query('INSERT IGNORE INTO '. NV_FORUM_GLOBALTABLE .'_deletion_log
							(content_type, content_id, delete_date, delete_user_id, delete_username, delete_reason)
						VALUES
							(\'thread\', '. intval( $thread['thread_id'] ) .', '. NV_CURRENTTIME .', '. intval( $user_info['userid'] ) .', '. $db->quote( $user_info['username'] ) .', '. $db->quote( $options['reason'] ) .')');

			// $lastest_forum = $db->query('SELECT node.*, forum.*						
						// FROM '. NV_FORUM_GLOBALTABLE .'_forum AS forum
						// INNER JOIN '. NV_FORUM_GLOBALTABLE .'_node AS node ON (node.node_id = forum.node_id)
						// WHERE node.node_id = '. intval( $thread['node_id'] ) )->fetch();

			$result = $db_slave->query( '
				SELECT post_id, thread_id, userid, message_state, likes, post_date 
				FROM ' . NV_FORUM_GLOBALTABLE . '_post 
				WHERE thread_id = ' . intval( $thread['thread_id'] ) . ' 
				ORDER BY position ASC, post_date ASC' );
			$post_array = array();
			while( $rows = $result->fetch() )
			{
				$post_array[$rows['post_id']] = $rows;
			}
			$result->closeCursor();

			if( $post_array )
			{
				$users = array();
				$users_likes = array();
				foreach( $post_array as $post_id => $message )
				{
					if( $message['message_state'] == 'visible' && $message['userid'] )
					{
						if( isset( $users[$message['userid']] ) )
						{
							$users[$message['userid']]++;
							$users_likes[$message['userid']] = $users_likes[$message['userid']] + $message['likes'];	
						}
						else
						{
							$users[$message['userid']] = 1;
							$users_likes[$message['userid']] = $message['likes'];
						}
					}
					 
				}
				
				foreach( $users as $userId => $modify )
				{
					$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET message_count = IF(message_count > ' . intval( $modify ) . ', message_count - ' . intval( $modify ) . ', 0) WHERE userid = ' . intval( $userId ) );
					
					$like_count = $users_likes[$userId];
					
					$db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . '
						SET like_count = IF(like_count > '. intval( $like_count ) .', like_count - '. intval( $like_count ) .', 0)
						WHERE userid = '. intval( $userId ) );
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
				$result = $db_slave->query( '
					SELECT tag_id,
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
			
			$return = true;
			//SELECT * FROM '. NV_FORUM_GLOBALTABLE .'_thread_redirect WHERE redirect_key LIKE 'thread-36-%'				
		}
	}

	return $return;
}

function ModelThread_getLastUpdatedThreadInForum($forumId, array $fetchOptions = array())
{
	global $db_slave;
	
	$stateLimit = prepareStateLimitFromConditions($fetchOptions, '', 'discussion_state');

	return $db_slave->query('
			SELECT *
			FROM '. NV_FORUM_GLOBALTABLE .'_thread
			WHERE node_id = '. intval( $forumId ) .'
				AND discussion_type <> \'redirect\'
				AND (' . $stateLimit . ')
			ORDER BY last_post_date DESC LIMIT 1')->fetch();
}
// Thread Model
