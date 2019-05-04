<?php

/**
 * Gets a general moderator records based on user ID.
 *
 * @param integer $userId
 *
 * @return array|false
 */
function getGeneralModeratorByUserId( $userId )
{
	global $db;
	
	return $db->query('
		SELECT moderator.*, user.username
		FROM '. NV_FORUM_GLOBALTABLE .'_moderator AS moderator
		INNER JOIN '. NV_USERS_GLOBALTABLE .' AS user ON (user.userid = moderator.userid)
		WHERE moderator.userid=' . intval( $userId ) )->fetch(); 
}

/**
 * Gets all general moderators, potentially limited by super moderator status.
 *
 * @param boolean|null $isSuperModerator If not null, limits to super or non-super mods only
 *
 * @return array Format: [user id] => info
 */
function getAllGeneralModerators( $isSuperModerator = null )
{
	global $db;
	if( $isSuperModerator === null )
	{
		$moderatorClause = '1=1';
	}
	elseif( $isSuperModerator )
		{
			$moderatorClause = 'moderator.is_super_moderator = 1';
		}
		else
		{
			$moderatorClause = 'moderator.is_super_moderator = 0';
		}
	$result = $db->query('
			SELECT moderator.*, user.username,
				user.avatar_date, user.gravatar
			FROM '. NV_FORUM_GLOBALTABLE .'_moderator AS moderator
			INNER JOIN '. NV_USERS_GLOBALTABLE .' AS user ON (user.userid = moderator.userid)
			WHERE ' . $moderatorClause . '
			ORDER BY user.username');
	$moderator = array();
	while( $rows = $result->fetch() )
	{
		$moderator[$rows['userid']] = $rows;  
	}
 
}

/**
 * Gets a matching content moderator.
 *
 * @param array $conditions
 * @param array $fetchOptions
 *
 * @return array|false
 */
function getContentModerator( array $conditions, array $fetchOptions = array() )
{
	$moderators = getContentModerators( $conditions, $fetchOptions );
	return reset( $moderators );
}

/**
 * Gets all matching content moderators.
 *
 * @param array $conditions
 * @param array $fetchOptions
 *
 * @return array Format: [moderator id] => info
 */
function getContentModerators( array $conditions = array(), array $fetchOptions = array() )
{ 	
	global $db;
	$whereConditions = prepareContentModeratorConditions( $conditions, $fetchOptions );
	//$limitOptions = prepareLimitFetchOptions( $fetchOptions );
	$sqlClauses = prepareContentModeratorFetchOptions( $fetchOptions );
	
	$db->sqlreset()
		->select('moderator_content.*, user.username ' . $sqlClauses['selectFields'] )
		->from( NV_FORUM_GLOBALTABLE .'_moderator_content AS moderator_content' )
		->join( 'INNER JOIN ' . NV_USERS_GLOBALTABLE .' AS user ON (user.userid = moderator_content.userid) ' . $sqlClauses['joinTables'] );
		
	if( $whereConditions )
	{
		$db->where( $whereConditions );
	}
	if( isset( $sqlClauses['orderClause'] ) )
	{
		$db->order( $sqlClauses['orderClause'] );
	} 
	if( isset( $fetchOptions['limit'] ) )
	{
		$db->limit( $fetchOptions['limit'] );
	}
	if( isset( $fetchOptions['offset'] ) )
	{
		$db->offset( $fetchOptions['offset'] );
	}
 
	$result= $db->query( $db->sql() );
 
	$moderator_content = array();
	while( $rows = $result->fetch() )
	{
		$moderator_content[$rows['moderator_id']] = $rows;  
	}
	return $moderator_content;
}

/**
 * Prepares the set of content moderator conditions.
 *
 * @param array $conditions
 * @param array $fetchOptions
 *
 * @return string SQL clause value for conditions
 */
function prepareContentModeratorConditions( array $conditions, array &$fetchOptions )
{
	global $db;
	
	$sqlConditions = array();
 

	if( isset( $conditions['moderator_id'] ) )
	{
		$sqlConditions[] = 'moderator_content.moderator_id = ' . $db->quote( $conditions['moderator_id'] );
	}

	if( ! empty( $conditions['content'] ) )
	{
		if( is_array( $conditions['content'] ) )
		{
			$sqlConditions[] = 'moderator_content.content_type = ' . $db->quote( $conditions['content'][0] );
			$sqlConditions[] = 'moderator_content.content_id = ' . $db->quote( $conditions['content'][1] );
		}
		else
		{
			$sqlConditions[] = 'moderator_content.content_type = ' . $db->quote( $conditions['content'] );
		}
	}

	if( isset( $conditions['userid'] ) )
	{
		$sqlConditions[] = 'moderator_content.userid = ' . $db->quote( $conditions['userid'] );
	}

	return getConditionsForClause( $sqlConditions );
}

/**
 * Prepares the content moderator fetch options into select fields, joins, and ordering.
 *
 * @param array $fetchOptions
 *
 * @return array Keys: selectFields, joinTables, orderClause
 */
function prepareContentModeratorFetchOptions( array $fetchOptions )
{
	$selectFields = '';
	$joinTables = '';
	$orderBy = '';

	if( isset( $fetchOptions['order'] ) )
	{
		switch( $fetchOptions['order'] )
		{
			case 'username':
				$orderBy = 'user.username';
				break;
		}
	}
	else
	{
		$orderBy = 'user.username';
	}

	return array(
		'selectFields' => $selectFields,
		'joinTables' => $joinTables,
		'orderClause' => ( $orderBy ? $orderBy : '' ) );
}

/**
 * Gets a content moderator by its unique ID.
 *
 * @param integer $id
 *
 * @return array|false
 */
function getContentModeratorById( $id )
{
	return getContentModerator( array( 'moderator_id' => $id ) );
}

/**
 * Gets a content moderator by the unique combination of content and user ID.
 *
 * @param string $contentType
 * @param integer $contentId
 * @param integer $userId
 *
 * @return array|false
 */
function getContentModeratorByContentAndUserId( $contentType, $contentId, $userId )
{
	return getContentModerator( array( 'content' => array( $contentType, $contentId ), 'userid' => $userId ) );
}

/**
 * Gets all content moderator info for a specified user ID
 *
 * @param integer $userId
 *
 * @return array Format: [moderator id] => info
 */
function getContentModeratorsByUserId( $userId )
{
	return getContentModerators( array( 'userid' => $userId ), array( 'order' => false ) );
}

/**
 * Inserts or updates the necessary content moderator record.
 *
 * @param integer $userId
 * @param string $contentType
 * @param integer $contentId
 * @param array $modPerms List of moderator permissions to apply to this content
 * @param array $extra Extra info. Includes general_moderator_permissions and extra_user_group_ids
 *
 * @return integer Moderator ID
 */
function insertOrUpdateContentModerator( $userId, $contentType, $contentId, array $modPerms, array $extra = array() )
{
	global $db, $admin_info;
	

	$contentModerator = getContentModeratorByContentAndUserId( $contentType, $contentId, $userId );

	$moderator_id = 0;
	if( !empty( $contentModerator ) )
	{	
		$db->query('UPDATE '. NV_FORUM_GLOBALTABLE .'_moderator_content SET moderator_permissions = '. $db->quote( serialize( $modPerms ) ).' WHERE moderator_id = ' . intval( $contentModerator['moderator_id'] ) );
	}
	else
	{
		$db->query('INSERT INTO '. NV_FORUM_GLOBALTABLE .'_moderator_content (content_type, content_id, userid, moderator_permissions) VALUES ('. $db->quote( $contentType ) .', '. intval( $contentId ) .', '. intval( $userId ) .', ' . $db->quote( serialize( $modPerms ) ) . ' )');
		$moderator_id = $db->lastInsertId ();
	}
 
	$moderator = $db->query('
			SELECT moderator.*, user.username
			FROM '. NV_FORUM_GLOBALTABLE .'_moderator AS moderator
			INNER JOIN '. NV_USERS_GLOBALTABLE .' AS user ON (user.userid = moderator.userid)
			WHERE moderator.userid = '. intval( $userId ) )->fetch();
	
	if( empty( $moderator ) )
	{	
 
		$result = $db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_moderator (userid, is_super_moderator, moderator_permissions, extra_user_group_ids) VALUES ( '. intval( $userId ) .', 0, '. $db->quote( serialize( $extra['general_moderator_permissions'] ) ) .', '. $db->quote( implode( ',', $extra['extra_user_group_ids'] ) ) . ' )');
		if( $result->rowCount() )
		{
			$is_moderator = 1;
		}else
		{
			$is_moderator = 0;
		}
	}else
	{
 
		$db->query( 'UPDATE '. NV_FORUM_GLOBALTABLE .'_moderator SET moderator_permissions=' . $db->quote( serialize( $extra['general_moderator_permissions'] ) ) . ', extra_user_group_ids= ' . $db->quote( implode( ',', $extra['extra_user_group_ids'] ) ) .' WHERE userid = '. intval( $userId ) );				
		$is_moderator = 1;
	}
	
	$user = getUserById( $userId, array( 'join'=> FETCH_USER_FULL ) );

	
	$extra_user_group_ids = implode( ',', $extra['extra_user_group_ids'] );
	if( isset( $extra['extra_user_group_ids'] ) &&  $extra_user_group_ids != $user['secondary_group_ids'])
	{	
		
		$result = $db->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET secondary_group_ids = ' . $db->quote( $extra_user_group_ids ) . ' WHERE userid = ' . intval( $userId ) );
		if( $result->rowCount() )
		{
			userChangeLog( $userId, $admin_info['userid'], 'secondary_group_ids', $user['secondary_group_ids'], $extra_user_group_ids );
		}
		
		$db->query( 'DELETE FROM ' . NV_USERS_GLOBALTABLE . '_group_relation WHERE userid = ' . intval( $userId ) );
		
		$group_insert_array = array();
		if( !empty( $extra['extra_user_group_ids'] ) )
		{
			foreach( $extra['extra_user_group_ids'] as $group_id )
			{
				$is_primary = ( $group_id == $user['user_group_id'] ) ? 1 : 0;
				$group_insert_array[] = '(' . intval( $userId ) . ', ' . intval( $group_id ) . ', ' . intval( $is_primary ) . ')';

			}
			$db->query( '
			INSERT INTO ' . NV_USERS_GLOBALTABLE . '_group_relation (userid, user_group_id, is_primary)
				VALUES ' . implode( ', ', $group_insert_array ) . ' 
			ON DUPLICATE KEY 
			UPDATE is_primary = VALUES(is_primary)' );		
		}
		
		$user['secondary_group_ids'] = $extra_user_group_ids;
	
		updateUserPermissionCombination( $user , true );
		
	} 
	 
	$existingPermissions = !empty( $moderator  ) ? $moderator['moderator_permissions']: array();
	_updatePermissionsModerator( $extra['general_moderator_permissions'], $existingPermissions, $userId );
		
	$existingPermissions = ( $contentModerator ) ? $contentModerator['moderator_permissions']: array();
	
	_updatePermissionsModeratorContent( $modPerms, $existingPermissions, array( 'content_type'=> $contentType, 'content_id'=> $contentId, 'userid'=> $userId  ) );
	 
 
	if( isset( $extra['is_staff'] ) || $is_moderator != $user['is_moderator'] )
	{		
		$conditions = array();
		if( $user['is_staff'] != $extra['is_staff'])
		{
			$conditions['is_staff'] = array('old'=> $user['is_staff'], 'new'=> $extra['is_staff']);
		}
		if( $is_moderator != $user['is_moderator'])
		{
			$conditions['is_moderator'] = array('old'=> $user['is_moderator'], 'new'=> $is_moderator);
		}
		if( !empty( $conditions ) )
		{
			$implode = array();
			foreach( $conditions as $key => $value )
			{
				$implode[]= $key . '=' . $db->quote( $value['new'] );
			}		
			$db->query('UPDATE ' . NV_USERS_GLOBALTABLE . ' SET '. implode(',', $implode ) .' WHERE userid=' . intval( $userId ) );
			
			foreach( $conditions as $key => $value )
			{
				userChangeLog( $user['userid'], $admin_info['userid'], $key, $value['old'], $value['new'] );		
			}
			
		}
			
	}

	
	return !empty( $moderator_id ) ? $moderator_id : $contentModerator['moderator_id'];
}

/**
 * Inserts or updates the necessary general moderator record.
 *
 * @param integer $userId
 * @param array $modPerms General moderator permissions. Does not include content-specific super mod perms.
 * @param boolean|null $isSuperModerator If non-null, the new super moderator setting
 * @param array $extra Extra data, including extra_user_group_ids and super_moderator_permissions
 *
 * @return integer Moderator ID
 */
function insertOrUpdateGeneralModerator( $userId, array $modPerms, $isSuperModerator = null, array $extra = array() )
{
	$moderator = getGeneralModeratorByUserId( $userId );

	// $dw = XenForo_DataWriter::create( 'XenForo_DataWriter_Moderator' );
	if( empty( $moderator ) )
	{
		//$dw->setExistingData( $moderator, true );
	}
	else
	{
		//$dw->set( 'userid', $userId );
	}

	if( $isSuperModerator !== null )
	{
		//$dw->set( 'is_super_moderator', $isSuperModerator );
	}

	if( isset( $extra['extra_user_group_ids'] ) )
	{
		//$dw->set( 'extra_user_group_ids', $extra['extra_user_group_ids'] );
	}

	if( isset( $extra['is_staff'] ) )
	{
		//$dw->setOption( XenForo_DataWriter_Moderator::OPTION_SET_IS_STAFF, $extra['is_staff'] );
	}

	if( isset( $extra['super_moderator_permissions'] ) )
	{
		$modPerms =  mergeModeratorPermissions( $modPerms, $extra['super_moderator_permissions'] );
	}

	// $dw->set( 'moderator_permissions', $modPerms );
	// $dw->save();

	return $userId;
}




/**
 * Merges 2 sets of "grouped" moderator permissions.
 *
 * @param array $modPerms Existing permissions like [group][permission] => info
 * @param array $merge Merging permissions like [group][permission] => info
 *
 * @return array Merged set
 */
function mergeModeratorPermissions( array $modPerms, array $merge )
{
	foreach( $merge as $generalGroupId => $generalGroup )
	{
		foreach( $generalGroup as $generalId => $general )
		{
			$modPerms[$generalGroupId][$generalId] = $general;
		}
	}

	return $modPerms;
}

/**
 * Merges only general moderator permissions into a set of grouped permissions.
 * The initial set may contain more than general moderator permissions.
 *
 * @param array $modPerms Existing permissions like [group][permission] => info
 * @param array $merge Merging permissions like [group][permission] => info
 *
 * @return array Merged set
 */
function mergeGeneralModeratorPermissions( array $modPerms, array $merge )
{
	$generalModeratorPermissions = getGeneralModeratorPermissions();

	foreach( $merge as $generalGroupId => $generalGroup )
	{
		foreach( $generalGroup as $generalId => $general )
		{
			if( isset( $generalModeratorPermissions[$generalGroupId][$generalId] ) )
			{
				$modPerms[$generalGroupId][$generalId] = $general;
			}
		}
	}

	return $modPerms;
}

/**
 * Merges a set of permission differences for setting/updating permission entries.
 *
 * @param array|string $newPermissions Set of new permissions (ie, new effective value). May be serialized.
 * @param array|string $existingPermissions Set of old permissions (ie, old effective value). May be serialized.
 * @param string $allowValue If a permission is to be allowed, the name of the allow state (allow or content_allow).
 *
 * @return array New effective permissions, with non-matching old values returned to "unset" state
 */
function getModeratorPermissionsForUpdate( $newPermissions, $existingPermissions, $allowValue = 'allow' )
{
	$finalPermissions = array();

	if( is_string( $newPermissions ) )
	{
		 
		$newPermissions = safeUnserialize( $newPermissions );
	}
	elseif( ! is_array( $newPermissions ) )
	{
		$newPermissions = array();
	}

	foreach( $newPermissions as $permissionGroupId => $permissionGroup )
	{
		foreach( $permissionGroup as $permissionId => $value )
		{
			$finalPermissions[$permissionGroupId][$permissionId] = $allowValue;
		}
	}

	if( is_string( $existingPermissions ) )
	{
		$existingPermissions = safeUnserialize( $existingPermissions );
	}
	elseif( ! is_array( $existingPermissions ) )
	{
		$existingPermissions = array();
	}

	foreach( $existingPermissions as $permissionGroupId => $permissionGroup )
	{
		foreach( $permissionGroup as $permissionId => $value )
		{
			if( ! isset( $finalPermissions[$permissionGroupId][$permissionId] ) )
			{
				$finalPermissions[$permissionGroupId][$permissionId] = 'unset';
			}
		}
	}

	return $finalPermissions;
}

/**
 * Gets the permission interface group IDs that apply to all general moderators.
 *
 * @return array
 */
function getGeneralModeratorInterfaceGroupIds()
{
	return array(
		'generalModeratorPermissions',
		'profilePostModeratorPermissions',
		'conversationModeratorPermissions' );
}

/**
 * Gets the permission interface group IDs that apply to the moderator in question.
 * If a content moderator, only includes general and that content's groups;
 * if a super moderator, includes all matching groups;
 * otherwise, includes only the general groups.
 *
 * @param array $moderator
 *
 * @return array List of interface group IDs
 */
function getModeratorInterfaceGroupIds( array $moderator )
{
	$interfaceGroupIds = getGeneralModeratorInterfaceGroupIds();

	if( ! empty( $moderator['content_type'] ) )
	{
		$handler = getContentModeratorHandlers( $moderator['content_type'] );
		$interfaceGroupIds = array_merge( $interfaceGroupIds, getModeratorInterfaceGroupIds() );
	}
	else
		if( ! empty( $moderator['is_super_moderator'] ) )
		{
			foreach( getContentModeratorHandlers() as $handler )
			{
				$interfaceGroupIds = array_merge( $interfaceGroupIds, $handler->getModeratorInterfaceGroupIds() );
			}
		}

	return $interfaceGroupIds;
}

/**
 * Gets all general moderator permissions.
 *
 * @return array Format: [group id][permission id] => permission info
 */
function getGeneralModeratorPermissions()
{
	return getModeratorPermissions( getGeneralModeratorInterfaceGroupIds() );
}

/**
 * Gets moderator permissions from the specified interface groups.
 *
 * @param array $interfaceGroupIds
 *
 * @return array Format: [group id][permission id] => permission info
 */
function getModeratorPermissions( array $interfaceGroupIds )
{
	//$permissions = _getLocalCacheData( 'permissions' );
	if( $permissions === false )
	{
		$permissions = getAllPermissions();
		//$this->setLocalCacheData( 'permissions', $permissions );
	}

	$validPermissions = array();
	foreach( $permissions as $permission )
	{
		if( $permission['permission_type'] != 'flag' )
		{
			continue;
		}

		if( in_array( $permission['interface_group_id'], $interfaceGroupIds ) )
		{
			$validPermissions[$permission['permission_group_id']][$permission['permission_id']] = $permission;
		}
	}

	return $validPermissions;
}

/**
 * Gets the necessary moderator permissions and interface groups for the UI,
 *
 * @param array $interfaceGroupIds List of interface groups to pull permissions from
 * @param array $existingPermissions Existing permissions ([group id][permission id]), for selected values
 *
 * @return array List of interface groups, with "permissions" key (flat array)
 */
function getModeratorPermissionsForInterface( array $interfaceGroupIds, array $existingPermissions = array() )
{
 
	$interfaceGroups = getAllPermissionInterfaceGroups();
	foreach( $interfaceGroups as $interfaceGroupId => &$interfaceGroup )
	{
		if( ! in_array( $interfaceGroupId, $interfaceGroupIds ) )
		{
			unset( $interfaceGroups[$interfaceGroupId] );
		}
		else
		{
			$interfaceGroup = preparePermissionInterfaceGroup( $interfaceGroup );
		}
	}

	foreach( getModeratorPermissions( $interfaceGroupIds ) as $groupId => $group )
	{
		foreach( $group as $permissionId => $permission )
		{
			if( isset( $interfaceGroups[$permission['interface_group_id']] ) )
			{
				$permission = $permissionModel->preparePermission( $permission );
				$interfaceGroups[$permission['interface_group_id']]['permissions'][] = array(
					'label' => $permission['title'],
					'name' => "[$permission[permission_group_id]][$permission[permission_id]]",
					'selected' => ! empty( $existingPermissions[$permission['permission_group_id']][$permission['permission_id']] ) );
			}
		}
	}

	return $interfaceGroups;
}

/**
 * Gets the list of possible extra user groups in "option" format.
 *
 * @param string|array $extraGroupIds List of existing extra group IDs; may be serialized.
 *
 * @return array List of user group options (keys: label, value, selected)
 */
function getExtraUserGroupOptions( $extraGroupIds )
{
	return $this->getModelFromCache( 'XenForo_Model_UserGroup' )->getUserGroupOptions( $extraGroupIds );
}

/**
 * Gets all content moderator handler objects, or one for the specified content type.
 *
 * @param string|array|null $limitContentType If specified, gets handler for specified type(s) only
 *
 * @return XenForo_ModeratorHandler_Abstract|array|false
 */
function getContentModeratorHandlers( $limitContentType = null )
{
	$contentTypes = _getLocalCacheData( 'moderatorHandlerPairs' );
	if( $contentTypes === false )
	{
		$contentTypes = getContentTypesWithField( 'moderator_handler_class' );
		setLocalCacheData( 'moderatorHandlerPairs', $contentTypes );
	}

	if( is_string( $limitContentType ) )
	{
		if( isset( $contentTypes[$limitContentType] ) )
		{
			$class = $contentTypes[$limitContentType];
			if( ! class_exists( $class ) )
			{
				return false;
			}

			$class = XenForo_Application::resolveDynamicClass( $class );
			return new $class();
		}
		else
		{
			return false;
		}
	}
	else
		if( is_array( $limitContentType ) )
		{
			$handlers = array();
			foreach( $contentTypes as $contentType => $handlerClass )
			{
				if( in_array( $contentType, $limitContentType ) )
				{
					if( ! class_exists( $handlerClass ) )
					{
						continue;
					}

					$handlerClass = XenForo_Application::resolveDynamicClass( $handlerClass );
					$handlers[$contentType] = new $handlerClass();
				}
			}
		}
		else
		{
			$handlers = array();
			foreach( $contentTypes as $contentType => $handlerClass )
			{
				if( ! class_exists( $handlerClass ) )
				{
					continue;
				}

				$handlerClass = XenForo_Application::resolveDynamicClass( $handlerClass );
				$handlers[$contentType] = new $handlerClass();
			}
		}

		return $handlers;
}

/**
 * Goes through a list of content moderators and fetches the content titles for all of them.
 * Items that are not returned by the handler will not have a "title" key.
 *
 * @param array $moderators
 *
 * @return array Moderators with "title" key where given
 */
function addContentTitlesToModerators( array $moderators )
{
	$types = array();
	foreach( $moderators as $key => $moderator )
	{
		if( ! $moderator['content_type'] )
		{
			continue;
		}

		$types[$moderator['content_type']][$key] = $moderator['content_id'];
	}

	if( $types )
	{
		$handlers = getContentModeratorHandlers( array_keys( $types ) );
		foreach( $handlers as $contentType => $handler )
		{
			$titles = getContentTitles( $types[$contentType] );
			foreach( $titles as $key => $title )
			{
				$moderators[$key]['title'] = $title;
			}
		}
	}

	return $moderators;
}

/**
 * Fetches an array containing $value for the value of each permission.
 * Useful for automatically populating super moderator records with a full permission set.
 *
 * @param mixed Value for every permission
 *
 * @return array $permissionSet[$groupId][$permId] = $value;
 */
function getFullPermissionSet( $value = true )
{
	$permissionSet = array();

	foreach( getModeratorPermissions( getModeratorInterfaceGroupIds( array( 'is_super_moderator' => true ) ) ) as $groupId => $group )
	{
		foreach( $group as $permId => $perm )
		{
			$permissionSet[$groupId][$permId] = $value;
		}
	}

	return $permissionSet;
}

/**
 * Returns the total number of members who are moderators
 * Note: distinct on userid
 *
 * @return integer
 */
function countModerators()
{
	global $db;
	return $db->query( 'SELECT COUNT(*) FROM '. NV_FORUM_GLOBALTABLE .'_moderator' )->fetcColumn();
}
