<?php

const FETCH_USER = 0x01;
const FETCH_FORUM = 0x02;
const FETCH_FIRSTPOST = 0x04;
const FETCH_AVATAR = 0x08;
const FETCH_DELETION_LOG = 0x10;
const FETCH_FORUM_OPTIONS = 0x20;
const FETCH_LAST_POST_AVATAR = 0x40;

function prepareThreadFetchOptions( array $fetchOptions )
{
	global $db;
	
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
		if( $fetchOptions['join'] & FETCH_USER )
		{
			$selectFields .= ',
					user.*, IF(user.username IS NULL, thread.username, user.username) AS username';
			$joinTables .= '
					LEFT JOIN '. NV_USERS_GLOBALTABLE .' AS user ON
						(user.userid = thread.userid)';
		}
		else
			if( $fetchOptions['join'] & FETCH_AVATAR )
			{
				$selectFields .= ',
					user.gender, user.avatar_date, user.gravatar';
				$joinTables .= '
					LEFT JOIN '. NV_USERS_GLOBALTABLE .' AS user ON
						(user.userid = thread.userid)';
			}

		if( $fetchOptions['join'] & FETCH_LAST_POST_AVATAR )
		{
			$selectFields .= ',
					last_post_user.gender AS last_post_gender,
					last_post_user.avatar_date AS last_post_avatar_date,
					last_post_user.gravatar AS last_post_gravatar,
					IF(last_post_user.username IS NULL, thread.last_post_username, last_post_user.username) AS last_post_username';
			$joinTables .= '
					LEFT JOIN '. NV_USERS_GLOBALTABLE .' AS last_post_user ON
						(last_post_user.userid = thread.last_post_userid)';
		}

		if( $fetchOptions['join'] & FETCH_FORUM )
		{
			$selectFields .= ',
					node.title AS node_title, node.node_name';
			$joinTables .= '
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_node AS node ON
						(node.node_id = thread.node_id)';
		}

		if( $fetchOptions['join'] & FETCH_FORUM_OPTIONS )
		{
			$selectFields .= ',
					forum.*,
					forum.last_post_id AS forum_last_post_id,
					forum.last_post_date AS forum_last_post_date,
					forum.last_post_userid AS forum_last_post_userid,
					forum.last_post_username AS forum_last_post_username,
					forum.last_thread_title AS forum_last_thread_title,
					thread.last_post_id,
					thread.last_post_date,
					thread.last_post_userid,
					thread.last_post_username';
			$joinTables .= '
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_forum AS forum ON
						(forum.node_id = thread.node_id)';
		}

		if( $fetchOptions['join'] & FETCH_FIRSTPOST )
		{
			$selectFields .= ',
					post.message, post.attach_count';
			$joinTables .= '
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_post AS post ON
						(post.post_id = thread.first_post_id)';
		}

		if( $fetchOptions['join'] & FETCH_DELETION_LOG )
		{
			$selectFields .= ',
					deletion_log.delete_date, deletion_log.delete_reason,
					deletion_log.delete_userid, deletion_log.delete_username';
			$joinTables .= '
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_deletion_log AS deletion_log ON
						(deletion_log.content_type = \'thread\' AND deletion_log.content_id = thread.thread_id)';
		}
	}

	if( isset( $fetchOptions['readUserId'] ) )
	{
		if( ! empty( $fetchOptions['readUserId'] ) )
		{
			$readMarkingDataLifetime = 30;
			$autoReadDate = NV_CURRENTTIME - $readMarkingDataLifetime * 86400;
			
			$joinTables .= '
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_thread_read AS thread_read ON
						(thread_read.thread_id = thread.thread_id
						AND thread_read.userid = ' . $db->quote( $fetchOptions['readUserId'] ) . ')';

			$joinForumRead = ( ! empty( $fetchOptions['includeForumReadDate'] ) || ( ! empty( $fetchOptions['join'] ) && $fetchOptions['join'] & FETCH_FORUM ) );
			if( $joinForumRead )
			{
				$joinTables .= '
						LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_forum_read AS forum_read ON
							(forum_read.node_id = thread.node_id
							AND forum_read.userid = ' . $db->quote( $fetchOptions['readUserId'] ) . ')';

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
						IF(reply_ban.expiry_date IS NULL OR reply_ban.expiry_date > ' . $db->quote( NV_CURRENTTIME ) . ', 1, 0)) AS thread_reply_banned';
			$joinTables .= '
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_thread_reply_ban AS reply_ban
						ON (reply_ban.thread_id = thread.thread_id
						AND reply_ban.userid = ' . $db->quote( $fetchOptions['replyBanUserId'] ) . ')';
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
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_thread_watch AS thread_watch
						ON (thread_watch.thread_id = thread.thread_id
						AND thread_watch.userid = ' . $db->quote( $fetchOptions['watchUserId'] ) . ')';
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
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_forum_watch AS forum_watch
						ON (forum_watch.node_id = thread.node_id
						AND forum_watch.userid = ' . $db->quote( $fetchOptions['forumWatchUserId'] ) . ')';
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
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_draft AS draft
						ON (draft.draft_key = CONCAT(\'thread-\', thread.thread_id)
						AND draft.userid = ' . $db->quote( $fetchOptions['draftUserId'] ) . ')';
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
					LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_thread_user_post AS thread_user_post
						ON (thread_user_post.thread_id = thread.thread_id
						AND thread_user_post.userid = ' . $db->quote( $fetchOptions['postCountUserId'] ) . ')';
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
				LEFT JOIN '. NV_FORUM_GLOBALTABLE .'_permission_cache_content AS permission
					ON (permission.permission_combination_id = ' . $db->quote( $fetchOptions['permissionCombinationId'] ) . '
						AND permission.content_type = \'node\'
						AND permission.content_id = thread.node_id)';
	}

	return array(
		'selectFields' => $selectFields,
		'joinTables' => $joinTables,
		'orderClause' => ( $orderBy ? "ORDER BY $orderBy" : '' ) );
}

/**
 * Prepares a collection of thread fetching related conditions into an SQL clause
 *
 * @param array $conditions List of conditions
 * @param array $fetchOptions Modifiable set of fetch options (may have joins pushed on to it)
 *
 * @return string SQL clause (at least 1=1)
 */
public function prepareThreadConditions( array $conditions, array & $fetchOptions )
{
	global $db;
	
	$sqlConditions = array();
	 

	if( ! empty( $conditions['thread_id_gt'] ) )
	{
		$sqlConditions[] = 'thread.thread_id > ' . $db->quote( $conditions['thread_id_gt'] );
	}

	if( ! empty( $conditions['title'] ) )
	{
		if( is_array( $conditions['title'] ) )
		{
			$sqlConditions[] = 'thread.title LIKE \'%' . $db->dblikeescape($conditions['title'][0], $conditions['title'][1]) . '%\'';
		}
		else
		{
			$sqlConditions[] = 'thread.title LIKE \'%' . $db->dblikeescape($conditions['title']) . '%\'';
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
			$sqlConditions[] = 'thread.node_id IN (' . $db->quote( $conditions['node_id'] ) . ')';
		}
		else
		{
			$sqlConditions[] = 'thread.node_id = ' . $db->quote( $conditions['node_id'] );
		}
	}

	if( ! empty( $conditions['discussion_type'] ) )
	{
		if( is_array( $conditions['discussion_type'] ) )
		{
			$sqlConditions[] = 'thread.discussion_type IN (' . $db->quote( $conditions['discussion_type'] ) . ')';
		}
		else
		{
			$sqlConditions[] = 'thread.discussion_type = ' . $db->quote( $conditions['discussion_type'] );
		}
	}

	if( ! empty( $conditions['not_discussion_type'] ) )
	{
		if( is_array( $conditions['not_discussion_type'] ) )
		{
			$sqlConditions[] = 'thread.discussion_type NOT IN (' . $db->quote( $conditions['not_discussion_type'] ) . ')';
		}
		else
		{
			$sqlConditions[] = 'thread.discussion_type <> ' . $db->quote( $conditions['not_discussion_type'] );
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
			$sqlConditions[] = 'thread.prefix_id IN (' . $db->quote( $conditions['prefix_id'] ) . ')';
		}
		else
			if( $conditions['prefix_id'] == -1 )
			{
				$sqlConditions[] = 'thread.prefix_id = 0';
			}
			else
			{
				$sqlConditions[] = 'thread.prefix_id = ' . $db->quote( $conditions['prefix_id'] );
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
			$sqlConditions[] = 'thread.discussion_state IN (' . $db->quote( $conditions['discussion_state'] ) . ')';
		}
		else
		{
			$sqlConditions[] = 'thread.discussion_state = ' . $db->quote( $conditions['discussion_state'] );
		}
	}

	if( isset( $conditions['deleted'] ) || isset( $conditions['moderated'] ) )
	{
		$sqlConditions[] = prepareStateLimitFromConditions( $conditions, 'thread', 'discussion_state' );
	}

	if( ! empty( $conditions['last_post_date'] ) && is_array( $conditions['last_post_date'] ) )
	{
		$sqlConditions[] = $this->getCutOffCondition( "thread.last_post_date", $conditions['last_post_date'] );
	}

	if( ! empty( $conditions['post_date'] ) && is_array( $conditions['post_date'] ) )
	{
		$sqlConditions[] = $this->getCutOffCondition( "thread.post_date", $conditions['post_date'] );
	}

	if( ! empty( $conditions['reply_count'] ) && is_array( $conditions['reply_count'] ) )
	{
		$sqlConditions[] = $this->getCutOffCondition( "thread.reply_count", $conditions['reply_count'] );
	}

	if( ! empty( $conditions['first_post_likes'] ) && is_array( $conditions['first_post_likes'] ) )
	{
		$sqlConditions[] = $this->getCutOffCondition( "thread.first_post_likes", $conditions['first_post_likes'] );
	}

	if( ! empty( $conditions['view_count'] ) && is_array( $conditions['view_count'] ) )
	{
		$sqlConditions[] = $this->getCutOffCondition( "thread.view_count", $conditions['view_count'] );
	}

	// fetch threads only from forums with find_new = 1
	if( ! empty( $conditions['find_new'] ) && isset( $fetchOptions['join'] ) && $fetchOptions['join'] & FETCH_FORUM_OPTIONS )
	{
		$sqlConditions[] = 'forum.find_new = 1';
	}

	// thread starter
	if( isset( $conditions['userid'] ) )
	{
		if( is_array( $conditions['userid'] ) )
		{
			$sqlConditions[] = 'thread.userid IN (' . $db->quote( $conditions['userid'] ) . ')';
		}
		else
		{
			$sqlConditions[] = 'thread.userid = ' . $db->quote( $conditions['userid'] );
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

	return $this->getConditionsForClause( $sqlConditions );
}

 

function getPermissionBasedThreadFetchConditions( array $forum, array $nodePermissions = null )
{
	global $db, $global_userid;

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

function getThreadsInForum($forumId, array $conditions = array(), array $fetchOptions = array())
{
	$conditions['forum_id'] = $forumId;
	return $this->getThreads($conditions, $fetchOptions);
}