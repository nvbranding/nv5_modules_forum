<?php

$_permissionPriority = array(
	'deny' => 1,
	'content_allow' => 2,
	'reset' => 3,
	'allow' => 4,
	'unset' => 5,
	'use_int' => 6 );

/**
 * Get all permissions (ungrouped), in their relative display order.
 * Proper display order cannot be gained unless the permissions are
 * grouped into their interface groups.
 *
 * @return array Format: [] => permission info
 */
function getAllPermissions()
{
	global $db_slave;

	$result = $db_slave->query( '
			SELECT *,
				default_value AS value, default_value_int AS value_int
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission 
			ORDER BY display_order
		' );
	$permission = array();
	while( $rows = $result->fetch() )
	{
		$permission[] = $rows;
	}
	$result->closeCursor();
	return $permission;

}

/**
 * Gets the named permission based on it's group and ID. Both the group
 * and the permission ID are required for unique identification.
 *
 * @param string $permissionGroupId
 * @param string $permissionId
 *
 * @return array|false
 */
function getPermissionByGroupAndId( $permissionGroupId, $permissionId )
{
	global $db_slave;

	return $db_slave->query( ' SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission WHERE permission_group_id = ' . intval( $permissionGroupId ) . ' AND permission_id = ' . intval( $permissionId ) )->fetch();
}

/**
 * Gets the default permission data.
 *
 * @return array
 */
function getDefaultPermission()
{
	return array(
		'permission_id' => '',
		'permission_group_id' => '',
		'permission_type' => 'flag',
		'interface_group_id' => '',
		'depend_permisssion_id' => '',
		'display_order' => 1,
		'default_value' => 'unset',
		'default_value_int' => 0 );
}

/**
 * Prepares a set of permissions that were grouped for display.
 *
 * @param array $permissions Format: [group id][] => permission info
 *
 * @return array Prepared array
 */
function preparePermissionsGrouped( array $permissions )
{
	foreach( $permissions as $groupId => $group )
	{
		foreach( $group as $permissionId => $permission )
		{
			$permissions[$groupId][$permissionId] = preparePermission( $permission );
		}
	}

	return $permissions;
}

/**
 * Prepares an ungrouped list of permissions for display.
 *
 * @param array $permissions Format: [] => permission info
 *
 * @return array
 */
function preparePermissions( array $permissions )
{
	foreach( $permissions as &$permission )
	{
		$permission = preparePermission( $permission );
	}

	return $permissions;
}

/**
 * Prepares a permission for display.
 *
 * @param array $permission
 *
 * @return array
 */
function preparePermission( array $permission )
{
	global $lang_module;
	//$permission['permission_group_id'], $permission['permission_id']
	$permission['title'] = $lang_module;
	// $permission['permission_group_id']
	$permission['groupTitle'] = $lang_module;

	return $permission;
}

/**
 * Perpares a list of permission groups for display.
 *
 * @param array $permissionGroups Format: [] => permission group info
 *
 * @return array
 */
function preparePermissionGroups( array $permissionGroups )
{
	foreach( $permissionGroups as &$group )
	{
		$group = preparePermissionGroup( $group );
	}

	return $permissionGroups;
}

/**
 * Prepares a permission group for display.
 *
 * @param array $permissionGroup
 *
 * @return array
 */
function preparePermissionGroup( array $permissionGroup )
{
	global $lang_module;

	$permissionGroup['title'] = $lang_module['permissionGroup_' . $permissionGroup['permission_group_id']];

	return $permissionGroup;
}

/**
 * Perpares a list of permission interface groups for display.
 *
 * @param array $interfaceGroups Format: [] => interface group info
 *
 * @return array
 */
function preparePermissionInterfaceGroups( array $interfaceGroups )
{
	foreach( $interfaceGroups as &$group )
	{
		$group = preparePermissionInterfaceGroup( $group );
	}

	return $interfaceGroups;
}

/**
 * Prepares a permission interface group for display.
 *
 * @param array $interfaceGroup
 *
 * @return array
 */
function preparePermissionInterfaceGroup( array $interfaceGroup )
{
	global $lang_module;
	$interfaceGroup['title'] = $lang_module['interfaceGroup_' . $interfaceGroup['interface_group_id']];

	return $interfaceGroup;
}

/**
 * Gets all permission grouped based on their internal permission groups.
 * This does not return based on interface groups.
 *
 * @return array Format: [permission group id][permission id] => permission info
 */
function getAllPermissionsGrouped()
{
	$groupedPermissions = array();
	foreach( getAllPermissions() as $permission )
	{
		$groupedPermissions[$permission['permission_group_id']][$permission['permission_id']] = $permission;
	}

	return $groupedPermissions;
}

/**
 * Internal function to sanitize the user and user group values for
 * use in a query against permission entries. Only one of the user group
 * and user ID may be specified; if both are specified, the user ID takes
 * precedence. If neither are specified, this relates to system-wide permissions.
 *
 * @param integer $userGroupId Modified by reference
 * @param integer $userId Modified by reference
 */
function _sanitizeUserIdAndUserGroupForQuery( &$userGroupId, &$userId )
{
	if( $userId ) // user perms
	{
		$userGroupId = 0;
		$userId = intval( $userId );
	}
	else
		if( $userGroupId ) // group perms
		{
			$userGroupId = intval( $userGroupId );
			$userId = 0;
		}
		else // system-wide perms
		{
			$userGroupId = 0;
			$userId = 0;
		}
}

/**
 * Gets all permissions in their relative display order, with the correct/effective
 * value for the specified user group or user.
 *
 * @param integer $userGroupId
 * @param integer $userId
 *
 * @return array Format: [] => permission info, permission_value/permission_value_int from entry,
 * 			value/value_int for effective value
 */
function getAllPermissionsWithValues( $userGroupId = 0, $userId = 0 )
{
	global $db_slave;

	//_sanitizeUserIdAndUserGroupForQuery( $userGroupId, $userId );

	try
	{

		$result = $db_slave->query( '
			SELECT permission.*,
				entry.permission_value, entry.permission_value_int,
				COALESCE(entry.permission_value, \'unset\') AS value,
				COALESCE(entry.permission_value_int, 0) AS value_int
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission AS permission
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_entry AS entry ON
				(entry.permission_id = permission.permission_id
				AND entry.permission_group_id = permission.permission_group_id
				AND entry.user_group_id = ' . intval( $userGroupId ) . '
				AND entry.userid = ' . intval( $userId ) . ')
			ORDER BY permission.display_order
			' );
		$permission = array();
		while( $rows = $result->fetch() )
		{
			$permission[] = $rows;
		}
		$result->closeCursor();
		return $permission;
	}
	catch ( PDOException $e )
	{
		trigger_error( $e->getMessage() );
	}

}

/**
 * Gets content permissions from the specified groups in their relative display order, with the
 * correct/effective value for the specified user group or user.
 *
 * @param string $contentTypeId
 * @param integer $contentId
 * @param mixed|array If array, only pulls permissions from the specified groups; otherwise, all
 * @param integer $userGroupId
 * @param integer $userId
 *
 * @return array Format: [] => permission info, permission_value/permission_value_int from entry,
 * 			value/value_int for effective value
 */
function getContentPermissionsWithValues( $contentTypeId, $contentId, $permissionGroups, $userGroupId = 0, $userId = 0 )
{
	global $db_slave;
	//_sanitizeUserIdAndUserGroupForQuery( $userGroupId, $userId );

	if( is_string( $permissionGroups ) )
	{
		$permissionGroups = array( $permissionGroups );
	}

	if( is_array( $permissionGroups ) )
	{
		if( empty( $permissionGroups ) )
		{
			return array();
		}
		else
		{
			$groupLimit = 'permission.permission_group_id IN (' . $db_slave->quote( $permissionGroups ) . ')';
		}
	}
	else
	{
		$groupLimit = '1=1';
	}

	try
	{

		$result = $db_slave->query( '
				SELECT permission.*,
				entry_content.permission_value, entry_content.permission_value_int,
				COALESCE(entry_content.permission_value, \'unset\') AS value,
				COALESCE(entry_content.permission_value_int, 0) AS value_int
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission AS permission
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content AS entry_content ON
				(entry_content.permission_id = permission.permission_id
				AND entry_content.permission_group_id = permission.permission_group_id
				AND entry_content.content_type = ' . $db_slave->quote( $contentTypeId ) . '
				AND entry_content.content_id = ' . intval( $contentId ) . '
				AND entry_content.user_group_id = ' . intval( $userGroupId ) . '
				AND entry_content.userid = ' . intval( $userId ) . ')
			WHERE ' . $groupLimit . '
			ORDER BY permission.display_order' );
		$permission = array();
		while( $rows = $result->fetch() )
		{
			$permission[] = $rows;
		}
		$result->closeCursor();
		return $permission;
	}
	catch ( PDOException $e )
	{
		trigger_error( $e->getMessage() );
	}

}

/**
 * Gets the view node permission attached to a specific node. This permission is a bit
 * weird since it doesn't fit in the expected groups, so it has to be handled specially.
 *
 * @param integer $nodeId
 * @param integer $userGroupId
 * @param integer $userId
 *
 * @return array
 */
function getViewNodeContentPermission( $nodeId, $userGroupId, $userId )
{
	global $db_slave;
	try
	{

		$result = $db_slave->query( '
			SELECT permission.*,
				entry_content.permission_value, entry_content.permission_value_int,
				COALESCE(entry_content.permission_value, \'unset\') AS value,
				COALESCE(entry_content.permission_value_int, 0) AS value_int
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission AS permission
			LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content AS entry_content ON
				(entry_content.permission_id = permission.permission_id
				AND entry_content.permission_group_id = permission.permission_group_id
				AND entry_content.content_type = \'node\'
				AND entry_content.content_id = ' . intval( $nodeId ) . '
				AND entry_content.user_group_id = ' . intval( $userGroupId ) . '
				AND entry_content.userid = ' . intval( $userId ) . ')
			WHERE permission.permission_group_id = \'general\'
				AND permission.permission_id = \'viewNode\'' );
		$permission = array();
		while( $rows = $result->fetch() )
		{
			$permission[] = $rows;
		}
		$result->closeCursor();
		return $permission;
	}
	catch ( PDOException $e )
	{
		trigger_error( $e->getMessage() );
	}

}

/**
 * Gets all permission interface groups in order.
 *
 * @return array Format: [interface group id] => interface group info
 */
function getAllPermissionInterfaceGroups()
{
	global $db_slave;
	$array = array();
	$result = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_interface_group ORDER BY display_order' );
	while( $rows = $result->fetch() )
	{
		$array[$rows['interface_group_id']] = $rows;
	}
	return $array;
}

/**
 * Gets permission interface groups names in their display order.
 *
 * @return array Format: [interface group id] => name
 */
function getPermissionInterfaceGroupNames()
{
	$groups = preparePermissionInterfaceGroups( getAllPermissionInterfaceGroups() );

	$output = array();
	foreach( $groups as $group )
	{
		$output[$group['interface_group_id']] = $group['title'];
	}

	return $output;
}

/**
 * Gets all permissions, grouped into the interface groups, with values
 * for the permissions specified for a particular group or user.
 *
 * @param integer $userGroupId
 * @param integer $userId
 *
 * @return array Format: [interface group id] => interface group info + key:permissions => [] => permission info with effective value
 */
function getUserCollectionPermissionsForInterface( $userGroupId = 0, $userId = 0 )
{
	$permissions = preparePermissions( getAllPermissionsWithValues( $userGroupId, $userId ) );
	$interfaceGroups = preparePermissionInterfaceGroups( getAllPermissionInterfaceGroups() );

	return getInterfaceGroupedPermissions( $permissions, $interfaceGroups );
}

/**
 * Gets all content permissions, grouped into the interface groups, with values
 * for the permissions specified for a particular group or user.
 *
 * @param string $contentTypeId
 * @param integer $contentId
 * @param mixed|string|array $permissionGroups If array, only those permission groups; if string, only that group; otherwise, all
 * @param integer $userGroupId
 * @param integer $userId
 *
 * @return array Format: [interface group id] => interface group info + key:permissions => [] => permission info with effective value
 */
function getUserCollectionContentPermissionsForInterface( $contentTypeId, $contentId, $permissionGroups, $userGroupId = 0, $userId = 0 )
{
	$permissions = getContentPermissionsWithValues( $contentTypeId, $contentId, $permissionGroups, $userGroupId, $userId );
	$interfaceGroups = preparePermissionInterfaceGroups( getAllPermissionInterfaceGroups() );

	return getInterfaceGroupedPermissions( $permissions, $interfaceGroups );
}

/**
 * Gets all permissions, grouped into the interface groups, with values
 * for the permissions coming from the default values.
 *
 * @return array Format: [interface group id] => interface group info + key:permissions => [] => permission info with effective value
 */
function getDefaultPermissionsForInterface()
{
	$permissions = preparePermissions( getAllPermissions() );
	$interfaceGroups = preparePermissionInterfaceGroups( getAllPermissionInterfaceGroups() );

	return getInterfaceGroupedPermissions( $permissions, $interfaceGroups );
}

/**
 * Groups a list of permissions based on the interface group they belong to.
 *
 * @param array $permissions
 * @param array $interfaceGroups
 *
 * @return array Format: [interface group id] => interface group info + key:permissions => [] => permission info with effective value
 */
function getInterfaceGroupedPermissions( array $permissions, array $interfaceGroups )
{
	$permissionsGrouped = array();
	foreach( $permissions as $permission )
	{
		$permissionsGrouped[$permission['interface_group_id']][] = $permission;
	}

	foreach( $interfaceGroups as $groupKey => &$group )
	{
		if( ! isset( $permissionsGrouped[$group['interface_group_id']] ) )
		{
			unset( $interfaceGroups[$groupKey] );
		}
		else
		{
			$group['permissions'] = $permissionsGrouped[$group['interface_group_id']];
		}
	}

	return $interfaceGroups;
}

/**
 * Gets all content permissions, grouped into the permission groups and then
 * interface groups, with values  for the permissions specified for a
 * particular group or user.
 *
 * @param string $contentTypeId
 * @param integer $contentId
 * @param mixed|string|array $permissionGroups If array, only those permission groups; if string, only that group; otherwise, all
 * @param integer $userGroupId
 * @param integer $userId
 *
 * @return array Format: [permission group id][interface group id] => interface group info, with key permissions => permissions in interface group
 */
function getUserCollectionContentPermissionsForGroupedInterface( $contentTypeId, $contentId, $permissionGroups, $userGroupId = 0, $userId = 0 )
{
	$permissions = getContentPermissionsWithValues( $contentTypeId, $contentId, $permissionGroups, $userGroupId, $userId );
	$permissions = preparePermissions( $permissions );

	$interfaceGroups = preparePermissionInterfaceGroups( getAllPermissionInterfaceGroups() );

	return getPermissionAndInterfaceGroupedPermissions( $permissions, $interfaceGroups );
}

/**
 * Gets permissions grouped by their permission group and then their interface group.
 * This is needed when a system requires all permissions in one or more permission
 * groups for display, but keeping the permissions together based on permission group.
 *
 * @param array $permissions
 * @param array $interfaceGroups
 *
 * @return array Format: [permission group id][interface group id] => interface group info, with key permissions => permissions in interface group
 */
function getPermissionAndInterfaceGroupedPermissions( array $permissions, array $interfaceGroups )
{
	$permissionsGrouped = array();
	$permissionGroups = array();
	foreach( $permissions as $permission )
	{
		$permissionsGrouped[$permission['permission_group_id']][$permission['interface_group_id']][] = $permission;
		$permissionGroups[] = $permission['permission_group_id'];
	}

	$outputGroups = array();
	foreach( $permissionGroups as $permissionGroupId )
	{
		foreach( $interfaceGroups as $interfaceGroupId => $interfaceGroup )
		{
			if( isset( $permissionsGrouped[$permissionGroupId][$interfaceGroupId] ) )
			{
				$interfaceGroup['permissions'] = $permissionsGrouped[$permissionGroupId][$interfaceGroupId];
				$outputGroups[$permissionGroupId][$interfaceGroupId] = $interfaceGroup;
			}
		}
	}

	return $outputGroups;
}

/**
 * Gets all permission groups ordered by their ID.
 *
 * @return array Format: [] => permission group info
 */
function getAllPermissionGroups()
{
	global $db_slave;
	$result = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_group ORDER BY permission_group_id' );
	$permission_group = array();
	while( $rows = $result->fetch() )
	{
		$permission_group[] = $rows;
	}
	$result->closeCursor();
	return $permission_group;

}

/**
 * Gets all permission group names ordered by their ID.
 *
 * @return array Format: [group id] => name
 */
function getPermissionGroupNames()
{
	$groups = preparePermissionGroups( getAllPermissionGroups() );

	$output = array();
	foreach( $groups as $group )
	{
		$output[$group['permission_group_id']] = $group['title'];
	}

	return $output;
}

/**
 * Gets the specified permission group.
 *
 * @param string $permissionGroupId
 *
 * @return array|false
 */
function getPermissionGroupById( $permissionGroupId )
{
	global $db_slave;

	return $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_group WHERE permission_group_id =' . intval( $permissionGroupId ) )->fetch();
}

/**
 * Gets the named permission groups.
 *
 * @param array $groupIds
 *
 * @return array Format: [section id] => info
 */
function getPermissionGroupsByIds( array $groupIds )
{
	global $db_slave;
	if( ! $groupIds )
	{
		return array();
	}
	$result = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_group WHERE permission_group_id IN (' . $db_slave->quote( $groupIds ) . ')' );
	$permission = array();
	while( $rows = $result->fetch() )
	{
		$permission[] = $rows;
	}
	$result->closeCursor();
	return $permission;

}

/**
 * Gets the default permission group data.
 *
 * @return array
 */
function getDefaultPermissionGroup()
{
	return array( 'permission_group_id' => '' );
}

/**
 * Gets the specified permission interface group.
 *
 * @param string $interfaceGroupId
 *
 * @return array|false
 */
function getPermissionInterfaceGroupById( $interfaceGroupId )
{
	global $db_slave;

	return $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_interface_group WHERE interface_group_id =' . intval( $interfaceGroupId ) )->fetch();

}

/**
 * Gets the named permission interface groups.
 *
 * @param array $groupIds
 *
 * @return array Format: [section id] => info
 */
function getPermissionInterfaceGroupsByIds( array $groupIds )
{
	global $db_slave;
	if( ! $groupIds )
	{
		return array();
	}
	$result = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_interface_group WHERE interface_group_id IN (' . $db_slave->quote( $groupIds ) . ')' );
	$permission = array();
	while( $rows = $result->fetch() )
	{
		$permission[] = $rows;
	}
	$result->closeCursor();
	return $permission;

}

/**
 * Gets the default permission interface group data.
 *
 * @return array
 */
function getDefaultPermissionInterfaceGroup()
{
	return array( 'interface_group_id' => '', 'display_order' => 1 );
}

/**
 * Gets a permission entry (for a user or group) by its entry ID
 *
 * @param integer $id
 *
 * @return array|false Permission entry info
 */
function getPermissionEntryById( $id )
{
	global $db_slave;

	return $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry WHERE permission_entry_id =' . intval( $id ) )->fetch();

}

/**
 * Gets a content permission entry (for a user or group) by its entry ID
 *
 * @param integer $id
 *
 * @return array|false Permission entry info
 */
function getContentPermissionEntryById( $id )
{
	global $db_slave;

	return $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content WHERE permission_entry_id =' . intval( $id ) )->fetch();

}

/**
 * Gets all permission entries in an undefined order, grouped by the "level"
 * of the permission. This is generally only needed for internal cache rebuilds.
 *
 * Note that entries with a value of "unset" will not be returned by this.
 *
 * @return array Format: ['users'][user id][group][permission] => permission value;
 * 		['userGroups'][user group id][group][permission] => permission value;
 * 		['system'][group][permission] => permission value
 */
function getAllPermissionEntriesGrouped()
{
	global $db_slave;
	$entries = array(
		'users' => array(),
		'userGroups' => array(),
		'system' => array() );

	$entryResult = $db_slave->query( '
			SELECT entry.*, permission.permission_type
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry AS entry
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_permission AS permission ON
				(permission.permission_id = entry.permission_id
				AND permission.permission_group_id = entry.permission_group_id)
			WHERE entry.permission_value <> \'unset\'
		' );
	while( $entry = $entryResult->fetch() )
	{
		$value = ( $entry['permission_type'] == 'flag' ? $entry['permission_value'] : $entry['permission_value_int'] );
		$pgId = $entry['permission_group_id'];
		$pId = $entry['permission_id'];

		if( $entry['userid'] )
		{
			$entries['users'][$entry['userid']][$pgId][$pId] = $value;
		}
		else
			if( $entry['user_group_id'] )
			{
				$entries['userGroups'][$entry['user_group_id']][$pgId][$pId] = $value;
			}
			else
			{
				$entries['system'][$pgId][$pId] = $value;
			}
	}

	return $entries;
}

/**
 * Gets all global-level permission entries for a user collection,
 * grouped into their respective permission (not interface) groups.
 *
 * @param integer $userGroupId
 * @param integer $userId
 *
 * @return array Format: [permission_group_id][permission_id] => permission_info
 */
function getAllGlobalPermissionEntriesForUserCollectionGrouped( $userGroupId = 0, $userId = 0 )
{
	global $db_slave;
	//_sanitizeUserIdAndUserGroupForQuery( $userGroupId, $userId );

	$result = $db_slave->query( '
			SELECT *
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry
			WHERE user_group_id = ' . intval( $userGroupId ) . ' AND userid = ' . intval( $userId ) );
	$permissions = array();
	while( $permission = $result->fetch() )
	{
		$permissions[$permission['permission_group_id']][$permission['permission_id']] = $permission;
	}

	return $permissions;
}

/**
 * Gets all content-level permission entries for a user collection,
 * grouped into their respective permission (not interface) groups.
 *
 * @param string $contentTypeId
 * @param integer $contentId
 * @param integer $userGroupId
 * @param integer $userId
 *
 * @return array Format: [permission_group_id][permission_id] => permission_info
 */
function getAllContentPermissionEntriesForUserCollectionGrouped( $contentTypeId, $contentId, $userGroupId = 0, $userId = 0 )
{
	global $db_slave;
	//_sanitizeUserIdAndUserGroupForQuery( $userGroupId, $userId );

	$result = $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content WHERE content_type = ' . $db_slave->quote( $contentTypeId ) . ' AND content_id = ' . intval( $contentId ) . ' AND user_group_id = ' . intval( $userGroupId ) . ' AND userid = ' . intval( $userId ) );

	$permissions = array();
	while( $permission = $result->fetch() )
	{
		$permissions[$permission['permission_group_id']][$permission['permission_id']] = $permission;
	}

	return $permissions;
}

/**
 * Gets all content permission entries for a type in an undefined order, grouped by the
 * "level" of the permission. This is generally only needed for internal cache rebuilds.
 *
 * Note that entries with a value of "unset" will not be returned by this.
 *
 * @return array Format: ['users'][user id][content id][group][permission] => permission value;
 * 		['userGroups'][user group id][content id][group][permission] => permission value;
 * 		['system'][content id][group][permission] => permission value
 */
function getAllContentPermissionEntriesByTypeGrouped( $permissionType )
{
	global $db_slave;
	$entries = array(
		'users' => array(),
		'userGroups' => array(),
		'system' => array() );

	$entryResult = $db_slave->query( '
			SELECT entry_content.*, permission.permission_type
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content AS entry_content
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_permission AS permission ON
				(permission.permission_id = entry_content.permission_id
				AND permission.permission_group_id = entry_content.permission_group_id)
			WHERE entry_content.content_type = ' . $db_slave->quote( $permissionType ) . ' 
				AND entry_content.permission_value <> \'unset\' ' );
	while( $entry = $entryResult->fetch() )
	{
		$value = ( $entry['permission_type'] == 'flag' ? $entry['permission_value'] : $entry['permission_value_int'] );
		$pgId = $entry['permission_group_id'];
		$pId = $entry['permission_id'];
		$cId = $entry['content_id'];

		if( $entry['userid'] )
		{
			$entries['users'][$entry['userid']][$cId][$pgId][$pId] = $value;
		}
		elseif( $entry['user_group_id'] )
		{
			$entries['userGroups'][$entry['user_group_id']][$cId][$pgId][$pId] = $value;
		}
		else
		{
			$entries['system'][$cId][$pgId][$pId] = $value;
		}
	}

	return $entries;
}

/**
 * Returns true if a user has specific permissions set.
 *
 * @param integer $userId
 *
 * @return boolean
 */
function permissionsForUserExist( $userId )
{
	global $db_slave;
	if( ! $userId )
	{
		return false;
	}

	if( $db_slave->query( 'SELECT 1 FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry WHERE userid=' . intval( $userId ) . ' AND permission_value <> \'unset\' LIMIT 1' )->fetchColumn() )
	{
		return true;
	}
	elseif( $db_slave->query( 'SELECT 1 FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content WHERE userid = ' . intval( $userId ) . ' AND permission_value <> \'unset\' LIMIT 1' )->fetchColumn() )
	{
		return true;
	}

	return false;
}

/**
 * Gets information about all permission combinations. Note that this function
 * does not return the cached permission data!
 *
 * @return array Format: [] => permission combo info (id, user, user group list)
 */
function getAllPermissionCombinations()
{
	global $db_slave;
	$result = $db_slave->query( 'SELECT permission_combination_id, userid, user_group_list FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination ORDER BY permission_combination_id' );
	$combination = array();
	while( $rows = $result->fetch() )
	{
		$combination[] = $rows;
	}
	$result->closeCursor();
	return $combination;

}

/**
 * Gets the specified permission combination, including permission cache.
 *
 * @param integer $combinationId
 *
 * @return false|array Permission combination if, it it exists
 */
function getPermissionCombinationById( $combinationId )
{
	global $db_slave;
	if( ! $combinationId )
	{
		return false;
	}
	return $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id = ' . intval( $combinationId ) )->fetch();

}

/**
 * Gets the permission combination that applies to a user. Returns false if
 * no user ID is specified.
 *
 * @param integer $userId
 *
 * @return false|array Permission combo info
 */
function getPermissionCombinationByUserId( $userId )
{
	global $db_slave;
	if( ! $userId )
	{
		return false;
	}
	return $db_slave->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE userid = ' . intval( $userId ) )->fetch();

}

/**
 * Gets all permission combinations that involve the specified user group.
 *
 * @param integer $userGroupId
 *
 * @return array Format: [permission_combination_id] => permission combination info
 */
function getPermissionCombinationsByUserGroupId( $userGroupId )
{
	global $db_slave;

	$result = $db_slave->query( '
			SELECT combination.permission_combination_id, combination.userid, combination.user_group_list
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination_user_group AS combination_user_group
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_combination AS combination ON
				(combination.permission_combination_id = combination_user_group.permission_combination_id)
			WHERE combination_user_group.user_group_id = ' . intval( $userGroupId ) . '
		' );
	$combination = array();
	while( $rows = $result->fetch() )
	{
		$combination[$rows['permission_combination_id']] = $rows;
	}
	$result->closeCursor();
	return $combination;
}

/**
 * Updates the provded set of global permissions for a user collection
 * (user group, user, system-wide).
 *
 * @param array $newPermissions Permissions to update, format: [permission_group_id][permission_id] => value
 * @param integer $userGroupId
 * @param integer $userId
 *
 * @return boolean
 */
function updateGlobalPermissionsForUserCollection( array $newPermissions, $userGroupId = 0, $userId = 0 )
{
	$existingEntries = getAllGlobalPermissionEntriesForUserCollectionGrouped( $userGroupId, $userId );
	$dwData = array( 'user_group_id' => $userGroupId, 'userid' => $userId );

	return _updatePermissionsForUserCollection( $newPermissions, $existingEntries, $userGroupId, $userId, array( NV_FORUM_GLOBALTABLE . '_permission_entry' ), $dwData );
}

/**
 * Updates the provded set of global permissions for a user collection
 * (user group, user, system-wide).
 *
 * @param array $newPermissions Permissions to update, format: [permission_group_id][permission_id] => value
 * @param string $contentTypeId
 * @param integer $contentId
 * @param integer $userGroupId
 * @param integer $userId
 *
 * @return boolean
 */
function updateContentPermissionsForUserCollection( array $newPermissions, $contentTypeId, $contentId, $userGroupId = 0, $userId = 0 )
{
	$existingEntries = getAllContentPermissionEntriesForUserCollectionGrouped( $contentTypeId, $contentId, $userGroupId, $userId );

	$dwData = array(
		'user_group_id' => $userGroupId,
		'userid' => $userId,
		'content_type' => $contentTypeId,
		'content_id' => $contentId );

	return _updatePermissionsForUserCollection( $newPermissions, $existingEntries, $userGroupId, $userId, array( NV_FORUM_GLOBALTABLE . '_permission_entry_content' ), $dwData );
}

function _updatePermissionsModerator( $newPermissions, $existingPermissions, $userId )
{
	$finalPermissions = getModeratorPermissionsForUpdate( $newPermissions, $existingPermissions );
	updateGlobalPermissionsForUserCollection( $finalPermissions, 0, $userId );
}
function _updatePermissionsModeratorContent( $newPermissions, $existingPermissions, $data )
{

	$finalPermissions = getModeratorPermissionsForUpdate( $newPermissions, $existingPermissions, 'content_allow' );
	updateContentPermissionsForUserCollection( $finalPermissions, $data['content_type'], $data['content_id'], 0, $data['userid'] );
}
/**
 * Internal handler to update global or content permissions for the specified user collection.
 *
 * @param array $newPermissions Permissions to update, format: [permission_group_id][permission_id] => value
 * @param array $existingEntries Existing permission entries for this collection
 * @param integer $userGroupId
 * @param integer $userId
 * @param string $dwName Name of the data writer to use to insert/update data
 * @param array $bulkData Bulk data to give to the datawriter
 *
 * @return boolean
 */
function _updatePermissionsForUserCollection( array $newPermissions, array $existingEntries, $userGroupId, $userId, $tables = array(), array $bulkData )
{
	global $db_slave, $db;

	$existingPermissions = getAllPermissionsGrouped();

	// let's lock all the tables involved so we get a consistent cache rebuild
	//$tables = array( NV_FORUM_GLOBALTABLE .'_permission_entry_content' );
	foreach( $tables as $table )
	{
		$db_slave->query( "SELECT 1 FROM $table GROUP BY 1 FOR UPDATE" );
	}

	foreach( $newPermissions as $groupId => $groupPermissions )
	{
		if( ! is_array( $groupPermissions ) || ! isset( $existingPermissions[$groupId] ) )
		{
			continue;
		}

		foreach( $groupPermissions as $permissionId => $permissionValue )
		{
			if( ! isset( $existingPermissions[$groupId][$permissionId] ) )
			{
				continue;
			}

			if( isset( $existingEntries[$groupId][$permissionId] ) )
			{
				if( $existingPermissions[$groupId][$permissionId]['permission_type'] == 'integer' )
				{
					if( intval( $permissionValue ) == 0 )
					{

						$db->query( 'DELETE FROM ' . $table . ' WHERE permission_entry_id=' . intval( $existingEntries[$groupId][$permissionId]['permission_entry_id'] ) );

						continue;

					}
					else
					{

						$db->query( 'UPDATE ' . $table . ' SET permission_value_int=' . intval( $permissionValue ) . ' WHERE permission_entry_id=' . intval( $existingEntries[$groupId][$permissionId]['permission_entry_id'] ) );
					}

				}
				else
				{
					if( $permissionValue == 'unset' )
					{
						if( isset( $existingEntries[$groupId][$permissionId] ) )
						{
							$db->query( 'DELETE FROM ' . $table . ' WHERE permission_entry_id=' . intval( $existingEntries[$groupId][$permissionId]['permission_entry_id'] ) );

						}
						continue;
					}
					else
					{
						$db->query( 'UPDATE ' . $table . ' SET permission_value=' . $db->quote( $permissionValue ) . ' WHERE permission_entry_id=' . intval( $existingEntries[$groupId][$permissionId]['permission_entry_id'] ) );
					}

				}

			}
			else
			{
				if( $permissionValue != 'unset' && ! empty( $permissionValue ) )
				{

					if( is_numeric( $permissionValue ) )
					{
						$value_int = $permissionValue;
						$permissionValue = 'use_int';
					}
					else
					{
						$value_int = 0;
					}

					if( isset( $bulkData['content_type'] ) && isset( $bulkData['content_id'] ) )
					{
						$stmt = $db->prepare( 'INSERT INTO ' . $table . ' SET 
							content_type=:content_type,
							content_id = ' . intval( $bulkData['content_id'] ) . ', 
							user_group_id=' . intval( $bulkData['user_group_id'] ) . ', 
							userid=' . intval( $bulkData['userid'] ) . ', 
							permission_group_id=:permission_group_id, 
							permission_id=:permission_id, 
							permission_value=:permission_value, 
							permission_value_int=' . intval( $value_int ) );

						$stmt->bindParam( ':content_type', $bulkData['content_type'], PDO::PARAM_STR );
						$stmt->bindParam( ':permission_group_id', $groupId, PDO::PARAM_STR );
						$stmt->bindParam( ':permission_id', $permissionId, PDO::PARAM_STR );
						$stmt->bindParam( ':permission_value', $permissionValue, PDO::PARAM_STR );
						$stmt->execute();
						$stmt->closeCursor();
					}
					else
					{
						$stmt = $db->prepare( 'INSERT INTO ' . $table . ' SET 
							user_group_id=' . intval( $bulkData['user_group_id'] ) . ', 
							userid=' . intval( $bulkData['userid'] ) . ', 
							permission_group_id=:permission_group_id, 
							permission_id=:permission_id, 
							permission_value=:permission_value, 
							permission_value_int=' . intval( $value_int ) );

						$stmt->bindParam( ':permission_group_id', $groupId, PDO::PARAM_STR );
						$stmt->bindParam( ':permission_id', $permissionId, PDO::PARAM_STR );
						$stmt->bindParam( ':permission_value', $permissionValue, PDO::PARAM_STR );
						$stmt->execute();
						$stmt->closeCursor();
					}

				}

			}

		}
	}

	if( $userId )
	{
		updateUserPermissionCombination( $userId, false );
		rebuildPermissionCacheForUserId( $userId );
	}
	elseif( $userGroupId )
	{
		$combinations = getPermissionCombinationsByUserGroupId( $userGroupId );

		if( count( $combinations ) >= 10 )
		{

			trigger_error( 'too many combinations, may timeout', E_USER_ERROR );
		}
		else
		{
			rebuildPermissionCacheForUserGroup( $combinations, $userGroupId );
		}
	}
	else
	{
		trigger_error( 'No permission', E_USER_NOTICE );
	}

	return true;
}

/**
 * Prepares an array of user groups into the list that is used in permission
 * combination lookups (comma delimited, ascending order).
 *
 * @param array $userGroupIds List of user group IDs
 *
 * @return string Comma delimited, sorted string of user group IDs
 */
function _prepareCombinationUserGroupList( array $userGroupIds )
{
	$userGroupIds = array_unique( $userGroupIds );
	sort( $userGroupIds, SORT_NUMERIC );

	return implode( ',', $userGroupIds );
}

/**
 * Gets a permission combination ID based on a specific user role (user ID if there are specific
 * permissions and a list of user group ID).
 *
 * @param integer $userId
 * @param array $userGroupIds
 *
 * @return integer|false Combination ID or false
 */
function getPermissionCombinationIdByUserRole( $userId, array $userGroupIds )
{
	global $db_slave;

	$userGroupList = _prepareCombinationUserGroupList( $userGroupIds );

	return $db_slave->query( 'SELECT permission_combination_id FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE userid = ' . intval( $userId ) . ' AND user_group_list = ' . $db_slave->quote( $userGroupList ) )->fetchColumn();

}

/**
 * Updates a user's permission combination based on the current state in the database.
 *
 * @param integer|array $userId Integer user ID or array of user info
 * @param boolean $buildOnCreate If true, the permission cache for a combination will be built if it's created
 * @param boolean $checkForUserPerms If false, doesn't look for user perms. Mostly an optimization
 *
 * @return false|integer Combination ID for the user if possible
 */
function updateUserPermissionCombination( $userId, $buildOnCreate = true, $checkForUserPerms = true )
{
	global $db_slave;

	if( is_array( $userId ) )
	{
		$user = $userId;
		if( ! isset( $user['userid'] ) )
		{
			return false;
		}
		$userId = $user['userid'];
	}
	else
	{
		$user = getUserById( $userId );
		if( ! $user )
		{
			return false;
		}
	}

	$originalCombination = getPermissionCombinationById( $user['permission_combination_id'] );

	$combinationId = findOrCreatePermissionCombinationFromUser( $user, $buildOnCreate, $checkForUserPerms );

	if( $combinationId != $user['permission_combination_id'] )
	{

		$db_slave->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET permission_combination_id=' . intval( $combinationId ) . ' WHERE userid=' . intval( $userId ) );
		// if changing combinations and the old combination used this userid, delete it
		if( $originalCombination && $originalCombination['userid'] == $userId )
		{
			deletePermissionCombination( $originalCombination['permission_combination_id'] );
		}
	}

	return $combinationId;
}

/**
 * Updates the permission combinations for a bunch of users.
 *
 * @param array $userIds
 * @param boolean $buildOnCreate
 */
function updateUserPermissionCombinations( array $userIds, $buildOnCreate = true )
{
	global $db_slave;
	$users = getUsersByIds( $userIds );
	if( ! $users )
	{
		return;
	}

	foreach( $users as $user )
	{
		$combinationId = findOrCreatePermissionCombinationFromUser( $user, $buildOnCreate );
		if( $combinationId != $user['permission_combination_id'] )
		{

			$db_slave->query( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET permission_combination_id=' . intval( $permission_combination_id ) . ' WHERE userid=' . $user['userid'] );

			// if changing combinations and the old combination used this userid, delete it
			$originalCombination = getPermissionCombinationById( $user['permission_combination_id'] );
			if( $originalCombination && $originalCombination['userid'] == $user['userid'] )
			{
				deletePermissionCombination( $user['permission_combination_id'] );
			}
		}
	}
}

/**
 * Deletes the sepcified permission combination.
 *
 * @param integer $combinationId
 */
function deletePermissionCombination( $combinationId )
{
	global $db_slave;

	$db_slave->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id = ' . $db_slave->quote( $combinationId ) );
	$db_slave->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination_user_group WHERE permission_combination_id = ' . $db_slave->quote( $combinationId ) );
	$db_slave->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content WHERE permission_combination_id = ' . $db_slave->quote( $combinationId ) );
}

/**
 * Deletes permissions combinations that aren't associated with users.
 *
 * @return array List of combination IDs that were deleted
 */
function deleteUnusedPermissionCombinations()
{
	global $db_slave;
	$combinationIds = $db_slave->query( '
			SELECT p.permission_combination_id
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination AS p
			LEFT JOIN (SELECT DISTINCT u.permission_combination_id FROM ' . NV_USERS_GLOBALTABLE . ' AS u) AS up
				ON (p.permission_combination_id = up.permission_combination_id)
			WHERE up.permission_combination_id IS NULL
				AND p.user_group_list <> \'1\'
				AND p.permission_combination_id <> 1
		' )->fetchColumn();
	if( $combinationIds )
	{

		$db_slave->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id IN (' . $db_slave->quote( $combinationIds ) . ')' );
		$db_slave->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination_user_group WHERE permission_combination_id IN (' . $db_slave->quote( $combinationIds ) . ')' );
		$db_slave->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content WHERE permission_combination_id IN (' . $db_slave->quote( $combinationIds ) . ')' );

	}

	return $combinationIds;
}

/**
 * Finds an existing permission combination or creates a new one from a user info array.
 *
 * @param array $user User info
 * @param boolean $buildOnCreate Build the permission combo cache if it must be created
 * @param boolean $checkForUserPerms If false, assumes there are no user perms (optimization)
 *
 * @return integer Permission combination ID
 */
function findOrCreatePermissionCombinationFromUser( array $user, $buildOnCreate = true, $checkForUserPerms = true )
{
	$userId = $user['userid'];
	if( $checkForUserPerms )
	{
		$userIdForPermissions = ( permissionsForUserExist( $userId ) ? $userId : 0 );
	}
	else
	{
		$userIdForPermissions = 0;
	}

	if( isset( $user['secondary_group_ids'] ) && $user['secondary_group_ids'] != '' )
	{
		$userGroups = explode( ',', $user['secondary_group_ids'] );
	}
	else
	{
		$userGroups = array();
	}

	$userGroups[] = $user['user_group_id'];

	return findOrCreatePermissionCombination( $userIdForPermissions, $userGroups, $buildOnCreate );
}

/**
 * Finds or creates a permission combination using the specified combination parameters.
 * The user ID should only be provided if permissions exist for that user.
 *
 * @param integer $userId User ID, if there are user-specific permissions
 * @param array $userGroupIds List of user group IDs
 * @param boolean $buildOnCreate Build permission combo cache if created
 *
 * @return integer Permission combination ID
 */
function findOrCreatePermissionCombination( $userId, array $userGroupIds, $buildOnCreate = true )
{
	global $db, $db_slave;

	$permissionCombinationId = getPermissionCombinationIdByUserRole( $userId, $userGroupIds );
	if( $permissionCombinationId )
	{
		return $permissionCombinationId;
	}

	$userGroupList = _prepareCombinationUserGroupList( $userGroupIds );

	$combination = array(
		'userid' => $userId,
		'user_group_list' => $userGroupList,
		'cache_value' => '' );

	$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_combination (userid, user_group_list, cache_value) VALUES( ' . intval( $combination['userid'] ) . ', ' . $db_slave->quote( $combination['user_group_list'] ) . ', \'\')' );

	$combination['permission_combination_id'] = $db->lastInsertId();

	foreach( explode( ',', $userGroupList ) as $userGroupId )
	{
		$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_combination_user_group (user_group_id, permission_combination_id) VALUES( ' . intval( $userGroupId ) . ', ' . intval( $combination['permission_combination_id'] ) . ')' );
	}

	if( $buildOnCreate )
	{
		$entries = getAllPermissionEntriesGrouped();
		$permissionsGrouped = getAllPermissionsGrouped();
		rebuildPermissionCombination( $combination, $permissionsGrouped, $entries );
	}

	return $combination['permission_combination_id'];
}

/**
 * Rebuilds the permission cache for the specified user ID. A combination with
 * this user ID must exist for a rebuild to be triggered.
 *
 * @param integer $userId
 *
 * @return boolean True on success (false if no cache needs to be updated)
 */
function rebuildPermissionCacheForUserId( $userId )
{
	$combination = getPermissionCombinationByUserId( $userId );
	if( ! $combination )
	{
		return false;
	}

	$entries = getAllPermissionEntriesGrouped();
	$permissionsGrouped = getAllPermissionsGrouped();

	rebuildPermissionCombination( $combination, $permissionsGrouped, $entries );

	return true;
}

/**
 * Rebuilds all permission cache data for combinations that involve the specified
 * user group.
 *
 * @param integer $userGroupId
 *
 * @return boolean True on success
 */
function rebuildPermissionCacheForUserGroup( $combinations, $userGroupId )
{

	if( ! $combinations )
	{
		return false;
	}

	$entries = getAllPermissionEntriesGrouped();
	$permissionsGrouped = getAllPermissionsGrouped();

	foreach( $combinations as $combination )
	{
		rebuildPermissionCombination( $combination, $permissionsGrouped, $entries );
	}

	return true;
}

/**
 * Rebuilds all permission cache entries.
 *
 * @param integer $maxExecution Limit execution time
 * @param integer $startCombinationId If specified, starts the rebuild at the specified combination ID
 *
 * @return boolean|integer True when totally complete; the next combination ID to start with otherwise
 */
function rebuildPermissionCache( $maxExecution = 0, $startCombinationId = 0 )
{
	$entries = getAllPermissionEntriesGrouped();
	$permissionsGrouped = getAllPermissionsGrouped();
	$combinations = getAllPermissionCombinations();

	$startTime = microtime( true );
	$restartCombinationId = false;

	foreach( $combinations as $combination )
	{
		if( $combination['permission_combination_id'] < $startCombinationId )
		{
			continue;
		}

		rebuildPermissionCombination( $combination, $permissionsGrouped, $entries );

		if( $maxExecution && ( microtime( true ) - $startTime ) > $maxExecution )
		{
			$restartCombinationId = $combination['permission_combination_id'] + 1; // next one
			break;
		}
	}

	return ( $restartCombinationId ? $restartCombinationId : true );
}

/**
 * Rebuilds the specific permission combination.
 *
 * @param integer $combinationId
 *
 * @return array|bool False if combination is not found, global permissions otherwise
 */
function rebuildPermissionCombinationById( $combinationId )
{
	$combination = getPermissionCombinationById( $combinationId );
	if( ! $combination )
	{
		return false;
	}

	$entries = getAllPermissionEntriesGrouped();
	$permissionsGrouped = getAllPermissionsGrouped();

	return rebuildPermissionCombination( $combination, $permissionsGrouped, $entries );
}

/**
 * Rebuilds the specified permission combination and updates the cache.
 *
 * @param array $combination Permission combination info
 * @param array $permissionsGrouped List of valid permissions, grouped
 * @param array $entries List of permission entries, with keys system/users/userGroups
 *
 * @return array Permission cache for this combination.
 */
function rebuildPermissionCombination( array $combination, array $permissionsGrouped, array $entries )
{
	global $db_slave;
	$userGroupIds = array_map( 'trim', explode( ',', $combination['user_group_list'] ) );

	$userId = $combination['userid'];

	$groupEntries = array();
	foreach( $userGroupIds as $userGroupId )
	{
		if( isset( $entries['userGroups'][$userGroupId] ) )
		{
			$groupEntries[$userGroupId] = $entries['userGroups'][$userGroupId];
		}
	}

	if( $userId && isset( $entries['users'][$userId] ) )
	{
		$userEntries = $entries['users'][$userId];
	}
	else
	{
		$userEntries = array();
	}

	$permCache = buildPermissionCacheForCombination( $permissionsGrouped, $entries['system'], $groupEntries, $userEntries );

	$finalCache = canonicalizePermissionCache( $permCache );

	$db_slave->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_permission_combination SET cache_value = ' . $db_slave->quote( serialize( $finalCache ) ) . ' WHERE permission_combination_id = ' . intval( $combination['permission_combination_id'] ) );

	rebuildContentPermissionCombination( $combination, $permissionsGrouped, $permCache );

	return $permCache;
}

/**
 * Rebuilds the content permission cache for the specified combination. This
 * function will rebuild permissions for all types of content and all pieces
 * of content for that type.
 *
 * @param array $combination Array of combination information
 * @param array $permissionsGrouped List of permissions, grouped
 * @param array $permCache Global permission cache for this combination, with values of unset, etc. May be modified by ref.
 */
function rebuildContentPermissionCombination( array $combination, array $permissionsGrouped, array & $permCache )
{
	global $db_slave;

	$userGroups = explode( ',', $combination['user_group_list'] );

	$contentTypeId = 'node';

	$cacheEntries = rebuildContentPermissions( $userGroups, $combination['userid'], $permissionsGrouped, $permCache );

	if( ! is_array( $cacheEntries ) )
	{
		return; 
	}

	$rows = array();
	$rowLength = 0;

	foreach( $cacheEntries as $contentId => $entry )
	{
		$row = '
		(' . $db_slave->quote( $combination['permission_combination_id'] ) . ', ' . $db_slave->quote( $contentTypeId ) . ', ' . $db_slave->quote( $contentId ) . ', ' . $db_slave->quote( serialize( $entry ) ) . ')';

		$rows[] = $row;
		$rowLength += strlen( $row );

		if( $rowLength > 500000 )
		{
			$db_slave->query( '
			INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content 
			(permission_combination_id, content_type, content_id, cache_value) VALUES 
			' . implode( ', ', $rows ) . ' 
			ON DUPLICATE KEY UPDATE cache_value = VALUES(cache_value)' );
			$rows = array();
			$rowLength = 0;
		}
	}

	if( $rows )
	{
		$db_slave->query( ' INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content 
		(permission_combination_id, content_type, content_id, cache_value) VALUES 
		' . implode( ', ', $rows ) . ' 
		ON DUPLICATE KEY 
		UPDATE cache_value = VALUES(cache_value)' );
	}

}

/**
 * Builds the permission cache for a given combination (via user groups and user ID).
 *
 * @param array $permissions List of valid permissions, grouped
 * @param array $systemEntries List of system-wide permission entries
 * @param array $goupEntries List of user group permission entries; an array of arrays
 * @param array $userEntries List of user-specific permission entries (if any)
 * @param array $basePermissions Base set of permissions to use as a starting point
 * @param array $preDependencyCache Outputs the permissions before dependency checks - useful for hierarchies
 *
 * @return array Permission cache details
 */
function buildPermissionCacheForCombination( array $permissionsGrouped, array $systemEntries, array $groupEntries, array $userEntries, array $basePermissions = array(), &$preDependencyCache = null )
{
	$entrySets = $groupEntries;
	if( $systemEntries )
	{
		$entrySets[] = $systemEntries;
	}
	if( $userEntries )
	{
		$entrySets[] = $userEntries;
	}

	$cache = array();
	foreach( $permissionsGrouped as $groupId => $permissions )
	{
		foreach( $permissions as $permissionId => $permission )
		{
			$permissionType = $permission['permission_type'];

			if( isset( $basePermissions[$groupId], $basePermissions[$groupId][$permissionId] ) )
			{
				$permissionValue = $basePermissions[$groupId][$permissionId];
			}
			else
			{
				$permissionValue = ( $permissionType == 'integer' ? 0 : 'unset' );
			}

			foreach( $entrySets as $entries )
			{
				$permissionValue = _getPermissionPriorityValueFromList( $permissionValue, $entries, $permissionType, $groupId, $permissionId, $permission['depend_permission_id'] );
			}

			$cache[$groupId][$permissionId] = $permissionValue;
		}
	}

	$preDependencyCache = $cache;

	// second pass to catch dependent permissions that shouldn't be more than their parent
	foreach( $permissionsGrouped as $groupId => $permissions )
	{
		foreach( $permissions as $permissionId => $permission )
		{
			if( $permission['depend_permission_id'] && isset( $cache[$groupId][$permission['depend_permission_id']] ) )
			{
				$parentValue = $cache[$groupId][$permission['depend_permission_id']];

				if( $parentValue == 'deny' || $parentValue == 'reset' )
				{
					$cache[$groupId][$permissionId] = ( $permission['permission_type'] == 'integer' ? 0 : 'deny' );
				}
			}
		}
	}

	return $cache;
}

/**
 * Canonicalizes permission cache data into integers or true/false values from
 * a version with deny/allow/unset/etc values. This is the actual representation
 * to be used externally.
 *
 * @param array $cache Permission cache info with allow/unset/deny/etc values
 *
 * @return array Permission cache with true/false values
 */
function canonicalizePermissionCache( array $cache )
{
	$newCache = array();
	foreach( $cache as $cacheKey => $value )
	{
		if( is_array( $value ) )
		{
			$newCache[$cacheKey] = canonicalizePermissionCache( $value );
		}
		else
		{
			if( is_numeric( $value ) )
			{
				$newCache[$cacheKey] = intval( $value );
			}
			else
			{
				$newCache[$cacheKey] = ( $value == 'allow' || $value == 'content_allow' );
			}

		}
	}

	return $newCache;
}

function getFinalPermissionValue( array $values, $permissionType )
{
	if( $permissionType == 'integer' )
	{
		$final = 0;
	}
	else
	{
		$final = 'unset';
	}

	foreach( $values as $value )
	{
		$final = getMergedPermissionPriorityValue( $final, $value, $permissionType );
	}

	return $final;
}

/**
 * Gets the value of a permission using the priority list. For flag permissions,
 * higher priority (lower numbers) will take priority over the already existing values.
 * For integers, -1 (unlimited) is highest priority; otherwise, higher numbers are better.
 *
 * @param string $existingValue Existing permission value (strings like unset, allow, deny, etc)
 * @param array $permissionEntries List of permission entries to look through. First key is group, second is permission ID.
 * @param string $permissionType Type of permission (integer or flag)
 * @param string $permissionGroupId Permission Group ID to check
 * @param string $permissionId Permission ID to check
 * @param string $dependPermissionId The permission this one depends on; if this permission is not active, this permission is ignored
 *
 * @return string New priority value
 */
function _getPermissionPriorityValueFromList( $existingValue, array $permissionEntries, $permissionType, $permissionGroupId, $permissionId, $dependPermissionId )
{
	$newValue = null;

	/*if ($dependPermissionId)
	{
	if (isset($permissionEntries[$permissionGroupId][$dependPermissionId]))
	{
	$dependValue = $permissionEntries[$permissionGroupId][$dependPermissionId];
	}
	else
	{
	$dependValue = 'unset';
	}

	if ($dependValue != 'allow' && $dependValue != 'content_allow')
	{
	$newValue = ($permissionType == 'integer' ? 0 : $dependValue);
	}
	}*/

	if( $newValue === null )
	{
		if( isset( $permissionEntries[$permissionGroupId][$permissionId] ) )
		{
			$newValue = $permissionEntries[$permissionGroupId][$permissionId];
		}
		else
		{
			$newValue = ( $permissionType == 'integer' ? 0 : 'unset' );
		}
	}

	return _getMergedPermissionPriorityValue( $existingValue, $newValue, $permissionType );
}

/**
 * Gets the merged the permission priority value.
 *
 * @param string|int $existingValue Existing value for the permission (int, or unset/allow/etc)
 * @param string|int $newValue New value for the permission (int, unset/allow/etc)
 * @param string $permissionType "integer" or "flag"
 *
 * @return string|int Effective value for the permission, using the priority list
 */
function _getMergedPermissionPriorityValue( $existingValue, $newValue, $permissionType )
{
	global $db, $_permissionPriority;
	if( $permissionType == 'integer' )
	{
		if( strval( $existingValue ) === '-1' )
		{
			return $existingValue;
		}
		else
			if( strval( $newValue ) === '-1' || $newValue > $existingValue )
			{
				return intval( $newValue );
			}
	}
	else
		if( $_permissionPriority[$newValue] < $_permissionPriority[$existingValue] )
		{
			return $newValue;
		}

	return $existingValue;
}

/**
 * Gets all users that have global, custom user permissions.
 *
 * @return array [user id] => info
 */
function getUsersWithGlobalUserPermissions()
{
	global $db_slave;

	try
	{
		$result = $db_slave->query( '
			SELECT user.*
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry AS permission_entry
			INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON
				(user.userid = permission_entry.userid)
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_permission AS permission ON
				(permission.permission_group_id = permission_entry.permission_group_id
				AND permission.permission_id = permission_entry.permission_id)
			WHERE permission_entry.user_group_id = 0
				AND permission_entry.userid > 0
			GROUP BY permission_entry.userid
			ORDER BY user.username' );
		$user = array();
		while( $rows = $result->fetch() )
		{
			$user[$rows['userid']] = $rows;
		}
		$result->closeCursor();
		return $user;
	}
	catch ( PDOException $e )
	{
		trigger_error( $e->getMessage() );
	}
}

function getUsersWithContentUserPermissions( $contentType, $contentId )
{
	global $db_slave;

	try
	{
		$result = $db_slave->query( '
			SELECT user.*
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content AS permission_entry_content
			INNER JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON
				(user.userid = permission_entry_content.userid)
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_permission AS permission ON
				(permission.permission_group_id = permission_entry_content.permission_group_id
				AND permission.permission_id = permission_entry_content.permission_id)
			WHERE permission_entry_content.content_type =' . $db_slave->quote( $contentType ) . '
				AND permission_entry_content.content_id = ' . $db_slave->quote( $contentId ) . '
				AND permission_entry_content.user_group_id = 0
				AND permission_entry_content.userid > 0
			GROUP BY permission_entry_content.userid
			ORDER BY user.username' );

		$user = array();
		while( $rows = $result->fetch() )
		{
			$user[$rows['userid']] = $rows;
		}
		$result->closeCursor();
		return $user;
	}
	catch ( PDOException $e )
	{
		trigger_error( $e->getMessage() );
	}
}

function getUserCombinationsWithContentPermissions( $contentType, $contentId = null )
{
	global $db_slave;

	try
	{
		$result = $db_slave->query( '
			SELECT DISTINCT entry.content_id, entry.group_id, entry.userid
			FROM ' . NV_FORUM_GLOBALTABLE . '_permission_entry_content AS entry
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_permission AS permission ON
				(permission.permission_group_id = entry.permission_group_id
				AND permission.permission_id = entry.permission_id)
			LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON (user.userid = entry.userid AND entry.userid > 0)
			LEFT JOIN ' . NV_GROUPS_GLOBALTABLE . ' AS user_group ON (user_group.group_id = entry.group_id AND entry.user_group_id > 0)
			WHERE entry.content_type = ' . $db_slave->quote( $contentType ) . '
				AND (
					user.userid IS NOT NULL
					OR user_group.group_id IS NOT NULL
					OR (entry.userid = 0 AND entry.user_group_id = 0)
				)' . ( $contentId !== null ? ' AND entry.content_id = ' . $db_slave->quote( $contentId ) : '' ) );

		$stmt->bindParam( ':content_type', $contentType, PDO::PARAM_STR );
		$stmt->execute();
		$entry_content = array();
		while( $rows = $stmt->fetch() )
		{
			$entry_content[] = $rows;
		}
		$result->closeCursor();
		return $entry_content;
	}
	catch ( PDOException $e )
	{
		trigger_error( $e->getMessage() );
	}

}
