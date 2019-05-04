<?php

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}
function ModelLike_getContentLikeByLikeUser( $contentType, $contentId, $userId )
{
	global $user_info, $global_userid, $db_slave, $lang_module;

	return $db_slave->query( '
			SELECT *
			FROM ' . NV_FORUM_GLOBALTABLE . '_liked_content
			WHERE content_type = ' . $db_slave->quote( $contentType ) . '
				AND content_id = ' . intval( $contentId ) . '
				AND like_user_id = ' . intval( $userId ) )->fetch();

}

function ModelLike_canLikePost( array $post, array $thread, array $forum, &$errorLangKey = '', array $nodePermissions = null )
{
	global $user_info, $global_userid, $lang_module;
	if( ! $global_userid )
	{
		return false;
	}

	if( $post['message_state'] != 'visible' )
	{
		return false;
	}

	if( $post['userid'] == $global_userid )
	{
		$errorLangKey = $lang_module['liking_own_content_cheating'];
		return false;
	}

	return hasContentPermission( $nodePermissions, 'like' );
}
 
function ModelLike_getLatestContentLikeUsers( $contentType, $contentId, $option )
{
	global $db_slave;
 
	$result = $db_slave->query( '
	SELECT liked_content.*,
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
		WHERE liked_content.content_type = ' . $db_slave->quote( $contentType ) . '
			AND liked_content.content_id = ' . intval( $contentId ) . '
			ORDER BY liked_content.like_date DESC LIMIT ' . intval( $option['offset'] ) . ', ' . intval( $option['limit'] ) );
	$latestLikeUsers = array();
	while( $liked_content = $result->fetch() )
	{
		$latestLikeUsers[] = array( 'userid' => $liked_content['like_user_id'], 'username' => $liked_content['username'] );
	}
	$result->closeCursor();
	unset( $result );

	return $latestLikeUsers;
}
function ModelLike_likeContent( $contentType, $contentId, $contentUserId, $likeUserId = null, $likeDate = null )
{
	global $user_info, $global_userid, $db_slave;

	if( $likeUserId === null )
	{
		$likeUserId = $global_userid;
	}
	if( ! $likeUserId )
	{
		return false;
	}

	if( $likeUserId != $global_userid )
	{
		$user = $db_slave->query( 'SELECT USER.*, user_profile.*, user_option.*
					FROM ' . NV_USERS_GLOBALTABLE . ' AS USER
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON (user_profile.userid = USER.userid)
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_option AS user_option ON (user_option.userid = USER.userid) WHERE USER.userid = ' . intval( $likeUserId ) )->fetch();

		if( ! $user )
		{
			return false;
		}
		$likeUsername = $user['username'];
	}
	else
	{
		$likeUsername = $user_info['username'];
	}

	if( $likeDate === null )
	{
		$likeDate = NV_CURRENTTIME;
	}

	$db_slave->beginTransaction( );

	$result = $db_slave->query( '
			INSERT IGNORE INTO ' . NV_FORUM_GLOBALTABLE . '_liked_content
				(content_type, content_id, content_user_id, like_user_id, like_date)
			VALUES
				(' . $db_slave->quote( $contentType ) . ', ' . intval( $contentId ) . ', ' . intval( $contentUserId ) . ', ' . intval( $likeUserId ) . ', ' . intval( $likeDate ) . ')' );

	if( ! $result->rowCount() )
	{
		$db_slave->commit( );
		return false;
	}

	if( $contentUserId )
	{
		$contentUser = $db_slave->query( 'SELECT USER.*, user_profile.*, user_option.*
					FROM ' . NV_USERS_GLOBALTABLE . ' AS USER
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON (user_profile.userid = USER.userid)
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_option AS user_option ON (user_option.userid = USER.userid) WHERE USER.userid = ' . intval( $contentUserId ) )->fetch();

		if( $contentUser )
		{
			$db_slave->query( ' UPDATE ' . NV_USERS_GLOBALTABLE . ' SET like_count = like_count + 1 WHERE userid = ' . intval( $contentUserId ) );

			$db_slave->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET alerts_unread = alerts_unread + 1 WHERE userid = ' . intval( $contentUserId ) . ' AND alerts_unread < 65535' );

		}
	}

	$latestLikeUsers = ModelLike_getLatestContentLikeUsers( $contentType, $contentId, array('offset'=> 0, 'limit'=> 5) );

	$db_slave->commit( );

	return $latestLikeUsers;
}
function  ModelLike_unlikeContent(array $like)
{
	global $db_slave;
	
	$db_slave->beginTransaction();

	$result = $db_slave->query('DELETE FROM '. NV_FORUM_GLOBALTABLE .'_liked_content WHERE like_id = '. intval( $like['like_id'] ) );
	 

	if (!$result->rowCount())
	{
		$db_slave->commit();
		return false;
	}

	if ($like['content_user_id'])
	{
		$db_slave->query('UPDATE '. NV_USERS_GLOBALTABLE .' SET like_count = IF(like_count > 1, like_count - 1, 0) WHERE userid = '. $like['content_user_id'] );
		
		$result = $db_slave->query( 'SELECT *
			FROM ' . NV_USERS_GLOBALTABLE . '_alert
			WHERE content_type = ' . $db_slave->quote( $like['content_type'] ) . ' 
				AND content_id = ' . intval( $like['content_id'] ) . '
				AND userid = ' . intval( $like['like_user_id'] ) . '
				AND action = \'like\'' );
		while( $alert = $result->fetch() )
		{
			$db_slave->query( 'DELETE FROM ' . NV_USERS_GLOBALTABLE . '_alert WHERE alert_id = ' . intval( $alert['alert_id'] ) );
		}
		$result->closeCursor();unset($result);
 
	}
 
	$latestLikeUsers = ModelLike_getLatestContentLikeUsers($like['content_type'], $like['content_id'], array('offset'=> 0, 'limit'=> 5));
 
	$db_slave->commit();

	return $latestLikeUsers;
}
// Like Model
