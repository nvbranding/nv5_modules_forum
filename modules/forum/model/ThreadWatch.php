<?php

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

function ModelThreadWatch_getUserThreadWatchByThreadId( $userId, $threadId )
{
	global $db_slave;

	return $db_slave->query( '
			SELECT *
			FROM ' . NV_FORUM_GLOBALTABLE . '_thread_watch
			WHERE userid = ' . intval( $userId ) . '
				AND thread_id = ' . intval( $threadId ) )->fetch();

}

function ModelThreadWatch_getThreadWatchStateForVisitor( $threadId = false, $useDefaultIfNotWatching = true )
{
	global $global_userid, $user_info;
	if( ! $global_userid )
	{
		return '';
	}

	if( $threadId )
	{
		$threadWatch = ModelThreadWatch_getUserThreadWatchByThreadId( $global_userid, $threadId );
	}
	else
	{
		$threadWatch = false;
	}

	if( $threadWatch )
	{
		return ( $threadWatch['email_subscribe'] ? 'watch_email' : 'watch_no_email' );
	}
	elseif( $useDefaultIfNotWatching )
	{
		return $user_info['default_watch_state'];
	}
	else
	{
		return '';
	}
}
