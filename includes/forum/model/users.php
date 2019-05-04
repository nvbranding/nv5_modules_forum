<?php

const FETCH_USER_PROFILE = 0x01;
const FETCH_USER_OPTION = 0x02;
const FETCH_USER_PRIVACY = 0x04;
const FETCH_USER_PERMISSIONS = 0x08;
const FETCH_LAST_ACTIVITY = 0x10;
const FETCH_USER_FULL = 0x07;
const PERMANENT_BAN = 0;

$defaultGuestGroupId = 5;
$defaultRegisteredGroupId = 4;
$defaultAdminGroupId = 1;
$defaultModeratorGroupId = 2;
$guestPermissionCombinationId = 5;

function prepareUserFetchOptions( array $fetchOptions )
{
	$selectFields = '';
	$joinTables = '';

	if( ! empty( $fetchOptions['join'] ) )
	{
		if( $fetchOptions['join'] & FETCH_USER_PROFILE )
		{
			$selectFields .= ',
					user_profile.*';
			$joinTables .= '
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_profile AS user_profile ON
						(user_profile.userid = user.userid)';
		}

		// TODO: optimise the join on user_option with serialization to user or user_profile
		if( $fetchOptions['join'] & FETCH_USER_OPTION )
		{
			$selectFields .= ',
					user_option.*';
			$joinTables .= '
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_option AS user_option ON
						(user_option.userid = user.userid)';
		}

		if( $fetchOptions['join'] & FETCH_USER_PRIVACY )
		{
			$selectFields .= ',
					user_privacy.*';
			$joinTables .= '
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_privacy AS user_privacy ON
						(user_privacy.userid = user.userid)';
		}

		if( $fetchOptions['join'] & FETCH_USER_PERMISSIONS )
		{
			$selectFields .= ',
					permission_combination.cache_value AS global_permission_cache';
			$joinTables .= '
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_permission_combination AS permission_combination ON
						(permission_combination.permission_combination_id = user.permission_combination_id)';
		}

		if( $fetchOptions['join'] & FETCH_LAST_ACTIVITY )
		{
			$selectFields .= ',
					IF (session_activity.view_date IS NULL, user.last_activity, session_activity.view_date) AS effective_last_activity,
					session_activity.view_date, session_activity.action, session_activity.params, session_activity.ip';
			$joinTables .= '
					LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_session_activity AS session_activity ON
						(session_activity.userid = user.userid AND session_activity.unique_key = user.userid)';
		}
	}

	if( isset( $fetchOptions['followingUserId'] ) )
	{
		$fetchOptions['followingUserId'] = intval( $fetchOptions['followingUserId'] );
		if( $fetchOptions['followingUserId'] )
		{
			// note: quoting is skipped; intval'd above
			$selectFields .= ',
					IF(user_follow.userid IS NOT NULL, 1, 0) AS following_' . $fetchOptions['followingUserId'];
			$joinTables .= '
					LEFT JOIN ' . NV_USERS_GLOBALTABLE . '_follow AS user_follow ON
						(user_follow.userid = user.userid AND user_follow.follow_user_id = ' . $fetchOptions['followingUserId'] . ')';
		}
		else
		{
			$selectFields .= ',
					0 AS following_0';
		}
	}

	if( isset( $fetchOptions['nodeIdPermissions'] ) )
	{
		$fetchOptions['nodeIdPermissions'] = intval( $fetchOptions['nodeIdPermissions'] );
		$selectFields .= ',
				permission.cache_value AS node_permission_cache';
		$joinTables .= '
				LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
					ON (permission.permission_combination_id = user.permission_combination_id
						AND permission.content_type = \'node\'
						AND permission.content_id = ' . $fetchOptions['nodeIdPermissions'] . ')';
	}

	return array( 'selectFields' => $selectFields, 'joinTables' => $joinTables );
}

function getOrderByClause( array $choices, array $fetchOptions, $defaultOrderSql = '' )
{
	$orderSql = null;

	if( ! empty( $fetchOptions['order'] ) && isset( $choices[$fetchOptions['order']] ) )
	{
		$orderSql = $choices[$fetchOptions['order']];

		if( empty( $fetchOptions['direction'] ) )
		{
			$fetchOptions['direction'] = 'asc';
		}

		$dir = ( strtolower( $fetchOptions['direction'] ) == 'desc' ? 'DESC' : 'ASC' );
		$orderSqlOld = $orderSql;
		$orderSql = sprintf( $orderSql, $dir );
		if( $orderSql === $orderSqlOld )
		{
			$orderSql .= ' ' . $dir;
		}
	}

	if( ! $orderSql )
	{
		$orderSql = $defaultOrderSql;
	}
	return ( $orderSql ? 'ORDER BY ' . $orderSql : '' );
}

function prepareUserOrderOptions( array &$fetchOptions, $defaultOrderSql = '' )
{
	$choices = array(
		'username' => 'user.username',
		'register_date' => 'user.regdate',
		'message_count' => 'user.message_count',
		'trophy_points' => 'user.trophy_points',
		'like_count' => 'user.like_count',
		'last_activity' => 'user.last_activity' );
	return getOrderByClause( $choices, $fetchOptions, $defaultOrderSql );
}

function getUserById( $userId, array $fetchOptions = array() )
{
	global $db;
	if( empty( $userId ) )
	{
		return false;
	}
	$joinOptions = prepareUserFetchOptions( $fetchOptions );

	return $db->query( '
			SELECT user.*
				' . $joinOptions['selectFields'] . '
			FROM ' . NV_USERS_GLOBALTABLE . ' AS user
			' . $joinOptions['joinTables'] . '
			WHERE user.userid = ' . intval( $userId ) )->fetch();
}

function getUsersByIds( array $userIds, array $fetchOptions = array() )
{
	global $db;
	if( ! $userIds )
	{
		return array();
	}

	$orderClause = prepareUserOrderOptions( $fetchOptions, 'user.username' );

	$joinOptions = prepareUserFetchOptions( $fetchOptions );

	$result = $db->query( '
				SELECT user.*
					' . $joinOptions['selectFields'] . '
				FROM ' . NV_USERS_GLOBALTABLE . ' AS user
				' . $joinOptions['joinTables'] . '
				WHERE user.userid IN (' . $db->quote( $userIds ) . ')
				' . $orderClause );

	$users = array();
	while( $rows = $result->fetch() )
	{
		$users[] = $rows;
	}
	$result->closeCursor();
	return $users;

}
 