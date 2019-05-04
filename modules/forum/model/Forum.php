<?php

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

function  ModelForum_prepareForumJoinOptions(array $fetchOptions)
{
	global $db_slave;
		$selectFields = '';
		$joinTables = '';

		if (!empty($fetchOptions['permissionCombinationId']))
		{
			$selectFields .= ',
				permission.cache_value AS node_permission_cache';
			$joinTables .= '
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
					ON (permission.permission_combination_id = ' . $db_slave->quote($fetchOptions['permissionCombinationId']) . '
						AND permission.content_type = \'node\'
						AND permission.content_id = forum.node_id)';
		}

		if (isset($fetchOptions['readUserId']))
		{
			if (!empty($fetchOptions['readUserId']))
			{
				$readMarkingDataLifetime = 30;// cau hinh
				$autoReadDate = NV_CURRENTTIME - ( $readMarkingDataLifetime * 86400 );

				$selectFields .= ",
					IF(forum_read.forum_read_date > $autoReadDate, forum_read.forum_read_date, $autoReadDate) AS forum_read_date";
				$joinTables .= '
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_forum_read AS forum_read ON
						(forum_read.node_id = forum.node_id
						AND forum_read.userid = ' . $db->quote($fetchOptions['readUserId']) . ')';
			}
			else
			{
				$selectFields .= ',
					NULL AS forum_read_date';
			}
		}

		if (isset($fetchOptions['watchUserId']))
		{
			if (!empty($fetchOptions['watchUserId']))
			{
				$selectFields .= ',
					IF(forum_watch.userid IS NULL, 0, 1) AS forum_is_watched';
				$joinTables .= '
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .' AS forum_watch
						ON (forum_watch.node_id = forum.node_id
						AND forum_watch.userid = ' . $db_slave->quote($fetchOptions['watchUserId']) . ')';
			}
			else
			{
				$selectFields .= ',
					0 AS forum_is_watched';
			}
		}

		if (isset($fetchOptions['threadId']))
		{
			$joinTables .= '
				INNER JOIN '. NV_FORUM_GLOBALTABLE .'_thread AS thread ON
					(thread.node_id = forum.node_id)';
		}

		return array(
			'selectFields' => $selectFields,
			'joinTables'   => $joinTables
		);
	}

function ModelForum_getForumById($id, array $fetchOptions = array())
{
	global $global_userid, $db_slave;
	
	$joinOptions = ModelForum_prepareForumJoinOptions($fetchOptions);

	return $db_slave->query('
			SELECT node.*, forum.*
				' . $joinOptions['selectFields'] . '
			FROM '. NV_FORUM_GLOBALTABLE .'_forum AS forum
			INNER JOIN '. NV_FORUM_GLOBALTABLE .'_node AS node ON (node.node_id = forum.node_id)
			' . $joinOptions['joinTables'] . '
			WHERE node.node_id = '. intval( $id ) )->fetch();
}


// Forum Model
function ModelForum_canStickUnstickThreadInForum( array $forum, &$errorLangKey = '', array $nodePermissions )
{
	global $global_userid;

	if( ! $global_userid )
	{
		return false;
	}
	return hasContentPermission( $nodePermissions, 'stickUnstickThread' );
}
function  ModelForum_canWatchForum(array $forum, &$errorLangKey = '', array $nodePermissions = null )
	{
		global $global_userid;
		return ($global_userid ? true : false);
	}
function ModelForum_canPostThreadInForum( array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	global $global_userid, $lang_module;
	if( empty( $forum['allow_posting'] ) )
	{
		$errorLangKey = $lang_module['you_may_not_perform_this_action_because_forum_does_not_allow_posting'];
		return false;
	}

	return hasContentPermission( $nodePermissions, 'postThread' );
}

function ModelForum_getUserForumReadDate( $userId, $forumId )
{
	global $db_slave;
	if( ! $userId )
	{
		return null;
	}

	$readDate = $db_slave->query( ' SELECT forum_read_date FROM ' . NV_FORUM_GLOBALTABLE . '_forum_read WHERE userid = ' . intval( $userId ) . ' AND node_id = ' . intval( $forumId ) )->fetchColumn();

	$readMarkingDataLifetime = 30; //option

	$autoReadDate = NV_CURRENTTIME - ( $readMarkingDataLifetime * 86400 );

	return max( $readDate, $autoReadDate );
}

function ModelForum_getUnreadThreadCountInForum( $forumId, $userId, $forumReadDate = 0, $ignored = false )
{
	global $db_slave;

	if( ! $userId )
	{
		return false;
	}

	if( $ignored && is_string( $ignored ) )
	{
		$ignored = safeUnserialize( $ignored );
		$ignored = array_keys( $ignored );
	}

	return $db_slave->query( '
			SELECT COUNT(*)
			FROM ' . NV_FORUM_GLOBALTABLE . '_thread AS thread
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_thread_read AS thread_read ON
				(thread_read.thread_id = thread.thread_id AND thread_read.userid = ' . intval( $userId ) . ')
			WHERE thread.node_id = ' . intval( $forumId ) . '
				AND thread.last_post_date > ' . intval( $forumReadDate ) . '
				AND (thread_read.thread_id IS NULL OR thread.last_post_date > thread_read.thread_read_date)
				' . ( $ignored ? 'AND thread.userid NOT IN (' . $db_slave->quote( $ignored ) . ')' : '' ) . '
				AND thread.discussion_state = \'visible\'
				AND thread.discussion_type <> \'redirect\'' )->fetchColumn();
}

function ModelForum_markForumRead( array $forum, $readDate )
{
	global $db_slave, $global_userid;

	if( ! $global_userid )
	{
		return false;
	}

	if( ! array_key_exists( 'forum_read_date', $forum ) )
	{
		$forum['forum_read_date'] = ModelForum_getUserForumReadDate( $global_userid, $forum['node_id'] );
	}

	if( $readDate <= $forum['forum_read_date'] )
	{
		return false;
	}

	$db_slave->query( '
			INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_forum_read
				(userid, node_id, forum_read_date)
			VALUES
				(' . intval( $global_userid ) . ', ' . intval( $forum['node_id'] ) . ', ' . intval( $readDate ) . ')
			ON DUPLICATE KEY UPDATE forum_read_date = VALUES(forum_read_date)' );

	return true;
}

function ModelForum_markForumReadIfNeeded( array $forum, $ignored = false )
{
	global $global_userid;

	if( ! $global_userid )
	{
		return false;
	}

	if( ! array_key_exists( 'forum_read_date', $forum ) )
	{
		$forum['forum_read_date'] = ModelForum_getUserForumReadDate( $global_userid, $forum['node_id'] );
	}

	$unreadThreadCount = ModelForum_getUnreadThreadCountInForum( $forum['node_id'], $global_userid, $forum['forum_read_date'], $ignored );

	if( ! $unreadThreadCount )
	{
		return ModelForum_markForumRead( $forum, NV_CURRENTTIME );
	}
	else
	{
		return false;
	}
}

function ModelForum_canPostPollInForum( array $forum, &$errorLangKey = '', array $nodePermissions )
{
	return $forum['allow_poll'];
}

function ModelForum_canViewForum( array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	return hasContentPermission( $nodePermissions, 'view' );
}

 
function ModelForum_canViewForumContent( array $forum, &$errorLangKey = '', array $nodePermissions = null )
{

	return hasContentPermission( $nodePermissions, 'viewContent' );
}
function ModelForum_getAttachmentParams( array $forum, array $contentData, array $nodePermissions = null, $tempHash = null )
{
	if( ModelForum_canUploadAndManageAttachment( $forum, $null, $nodePermissions ) )
	{
		$existing = is_string( $tempHash ) && strlen( $tempHash ) == 32;
		$output = array(
			'hash' => $existing ? $tempHash : md5( uniqid( '', true ) ),
			'content_type' => 'post',
			'content_data' => $contentData );
		if( $existing )
		{
			$output['attachments'] = ModelAttachment_prepareAttachments( ModelForum_getAttachmentsByTempHash( $tempHash ) );
		}

		return $output;
	}
	else
	{
		return false;
	}
}
function ModelForum_canUploadAndManageAttachment( array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	global $global_userid;
	if( ! $global_userid )
	{
		return false;
	}

	return hasContentPermission( $nodePermissions, 'uploadAttachment' );
}
function getAttachmentParams( array $forum, array $contentData, array $nodePermissions = null, $tempHash = null )
{
	if( ModelForum_canUploadAndManageAttachment( $forum, $null, $nodePermissions ) )
	{
		$existing = is_string( $tempHash ) && strlen( $tempHash ) == 32;
		$output = array(
			'hash' => $existing ? $tempHash : md5( uniqid( '', true ) ),
			'content_type' => 'post',
			'content_data' => $contentData );
		if( $existing )
		{
			$output['attachments'] = ModelAttachment_prepareAttachments( ModelAttachment_getAttachmentsByTempHash( $tempHash ) );
		}

		return $output;
	}
	else
	{
		return false;
	}
}
function ModelForum_canLockUnlockThreadInForum(array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	global $global_userid;
 
	if (!$global_userid)
	{
		return false;
	}

	return hasContentPermission($nodePermissions, 'lockUnlockThread');
}
function ModelForum_prepareForum(array $forum)
{
	$forum['hasNew'] = (isset($forum['forum_read_date']) && $forum['forum_read_date'] < $forum['last_post_date']);
	$forum['prefixCache'] = (!empty($forum['prefix_cache']) ? safeUnserialize($forum['prefix_cache']) : array());

	return $forum;
} 
function ModelForum_getForumCounters($forumId)
{
	global $db_slave;
	return $db_slave->query('
		SELECT
			COUNT(*) AS discussion_count,
			COUNT(*) + SUM(reply_count) AS message_count
		FROM '. NV_FORUM_GLOBALTABLE .'_thread
		node_id = '. intval( $forumId ) .'
			AND discussion_state = \'visible\'
			AND discussion_type <> \'redirect\'')->fetch();
}

 
 

// Forum Model
