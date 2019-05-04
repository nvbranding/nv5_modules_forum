<?php

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}
function ModelThreadRedirect_getThreadRedirectById( $threadId )
{
	global $db_slave;

	return $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread_redirect WHERE thread_id = ' . intval( $threadId ) )->fetch();

}

function ModelThreadRedirect_getExpiredThreadRedirects( $expiredDate )
{
	global $db_slave;

	$result = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread_redirect WHERE expiry_date > 0 AND expiry_date < ' . $expiredDate );

	$data = array();

	while( $row = $result->fetch() )
	{
		$data[$row['thread_id']] = $row;

	}
	$result->closeCursor();

	return $data;
}

function ModelThreadRedirect_getThreadRedirectsByKey( $redirectKey, $likeMatch = false )
{
	global $db_slave;
	if( $likeMatch )
	{
		$result = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread_redirect WHERE redirect_key LIKE ' . $db_slave->quote( $redirectKey ) );

		$data = array();

		while( $row = $result->fetch() )
		{
			$data[$row['thread_id']] = $row;

		}
		$result->closeCursor();

		return $data;

	}
	else
	{
		$result = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_thread_redirect WHERE redirect_key = ' . $db_slave->quote( $redirectKey ) );

		$data = array();

		while( $row = $result->fetch() )
		{
			$data[$row['thread_id']] = $row;

		}
		$result->closeCursor();

		return $data;
	}
}

function ModelThreadRedirect_createRedirectThread( $targetUrl, array $newThread, $redirectKey = '', $expiryDate = 0 )
{
	global $nv_Request;
	unset( $newThread['thread_id'], $newThread['tags'] );

	if( empty( $newThread['discussion_state'] ) )
	{
		$newThread['discussion_state'] = 'visible';
	}
	else
		if( $newThread['discussion_state'] != 'visible' )
		{
			return false;
		}

	$newThread['discussion_type'] = 'redirect';
	$newThread['first_post_id'] = 0; // remove any potential preview

	//PDO::beginTransaction();

	// $threadDw = XenForo_DataWriter::create('XenForo_DataWriter_Discussion_Thread', XenForo_DataWriter::ERROR_SILENT);
	// $threadDw->setOption(XenForo_DataWriter_Discussion::OPTION_REQUIRE_INSERT_FIRST_MESSAGE, false);
	// $threadDw->bulkSet($newThread, array('ignoreInvalidFields' => true));
	// if (!$threadDw->save())
	// {
	// PDO::rollback();
	// return false;
	// }

	$newThreadId = $nv_Request->get_int( 'thread_id', 'post,get', 0 );

	ModelThreadRedirect_insertThreadRedirect( $newThreadId, $targetUrl, $redirectKey, $expiryDate );

	//PDO::commit();

	return $newThreadId;
}

function ModelThreadRedirect_insertThreadRedirect( $threadId, $targetUrl, $redirectKey = '', $expiryDate = 0 )
{
	global $db;

	$expiryDate = ( $expiryDate > 4294967295 ? 0 : $expiryDate );

	$db->query( '
	INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_thread_redirect SET 
		thread_id=' . intval( $threadId ) . ',
		target_url=' . $db->quote( $targetUrl ) . ',
		redirect_key=' . $db->quote( $redirectKey ) );

}

function ModelThreadRedirect_deleteThreadRedirects( array $threadIds )
{
	global $db;
	if( ! $threadIds )
	{
		return;
	}
	PDO::beginTransaction( $db );

	$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_thread_redirect WHERE thread_id IN (' . implode( ',', $threadIds ) . ')' );

	foreach( $threadIds as $threadId )
	{
		//$db->delete();
	}

}

function ModelThreadRedirect_updateThreadRedirect( $threadId, array $update )
{
	global $db;

	$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_thread_redirect SET 
	target_url=' . $db->quote( $update['targetUrl'] ) . ',
	redirect_key=' . $db->quote( $update['redirectKey'] ) . '
	WHERE thread_id = ' . $db->quote( $threadId ) );
}
