<?php

$_nodeTypes = array(
	'category'=>'category',
	'forum'=>'forum',
	'linkforum'=>'linkforum',
	'page'=>'page' );


function _adjustBasePermissionAllows( array $basePermissions )
{
	foreach( $basePermissions as $group => $p )
	{
		foreach( $p as $id => $value )
		{
			if( $value === 'content_allow' )
			{
				$basePermissions[$group][$id] = 'allow';
			}
		}
	}

	return $basePermissions;
}

function nodeTree()
{
	global $db, $nodeTree;

	$result = $db->query( 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_node ORDER BY parent_id, sort ASC' );
	$nodeTree = array();
	while( $node = $result->fetch() )
	{
		$nodeTree[$node['parent_id']][$node['node_id']] = $node;
	}
	$result->closeCursor();
	return $nodeTree;
}

function _getUserNodeEntries( $nodeId, $userId )
{
	global $_nodePermissionEntries;
	if( empty( $_nodePermissionEntries ) ) 
	$_nodePermissionEntries = getAllContentPermissionEntriesByTypeGrouped( 'node' );
	$rawUserEntries = $_nodePermissionEntries['users'];
	if( $userId && isset( $rawUserEntries[$userId], $rawUserEntries[$userId][$nodeId] ) )
	{
		return $rawUserEntries[$userId][$nodeId];
	}
	else
	{
		return array();
	}
}

function _getUserGroupNodeEntries( $nodeId, array $userGroupIds )
{
	global $_nodePermissionEntries;
	if( empty( $_nodePermissionEntries ) ) 
	$_nodePermissionEntries = getAllContentPermissionEntriesByTypeGrouped( 'node' );
	$rawUgEntries = $_nodePermissionEntries['userGroups'];
	$groupEntries = array();
	foreach( $userGroupIds as $userGroupId )
	{
		if( isset( $rawUgEntries[$userGroupId], $rawUgEntries[$userGroupId][$nodeId] ) )
		{
			$groupEntries[$userGroupId] = $rawUgEntries[$userGroupId][$nodeId];
		}
	}

	return $groupEntries;
}

function _getNodeWideEntries( $nodeId )
{
	global $_nodePermissionEntries;
	
	if( empty( $_nodePermissionEntries ) ) 
	$_nodePermissionEntries = getAllContentPermissionEntriesByTypeGrouped( 'node' );
	
	if( isset( $_nodePermissionEntries['system'][$nodeId] ) )
	{
		return $_nodePermissionEntries['system'][$nodeId];
	}
	else
	{
		return array();
	}
}

function _buildNodeTreePermissions( $userId, array $userGroupIds, array $basePermissions, array $permissionsGrouped, $parentId = 0 )
{
	global $_nodeTypes, $nodeTree;

	if( ! isset( $basePermissions['general']['viewNode'] ) )
	{
		if( isset( $GLOBALS['globalPerms']['general']['viewNode'] ) )
		{
			$basePermissions['general']['viewNode'] = $GLOBALS['globalPerms']['general']['viewNode'];
		}
		else
		{
			$basePermissions['general']['viewNode'] = 'unset';
		}
	}

	$basePermissions = _adjustBasePermissionAllows( $basePermissions );

	$finalPermissions = array();
	if( empty( $nodeTree ) ) $nodeTree = nodeTree();
	
	if( isset( $nodeTree[$parentId] ) )
	{
		foreach( $nodeTree[$parentId] as $node )
		{
			if( ! isset( $_nodeTypes[$node['node_type_id']] ) )
			{
				continue;
			}

			$nodeType = $_nodeTypes[$node['node_type_id']];
			$nodeId = $node['node_id'];

			$groupEntries = _getUserGroupNodeEntries( $nodeId, $userGroupIds );
			$userEntries = _getUserNodeEntries( $nodeId, $userId );
			$nodeWideEntries = _getNodeWideEntries( $nodeId );

			$nodePermissions = buildPermissionCacheForCombination( $permissionsGrouped, $nodeWideEntries, $groupEntries, $userEntries, $basePermissions, $passPermissions );

			if( ! isset( $nodePermissions['general']['viewNode'] ) )
			{
				$nodePermissions['general']['viewNode'] = 'unset';
			}

			if( $nodeType )
			{
				$nodePermissions[$nodeType]['view'] = $nodePermissions['general']['viewNode'];

				$finalNodePermissions = canonicalizePermissionCache( $nodePermissions[$nodeType] );

				if( isset( $finalNodePermissions['view'] ) && ! $finalNodePermissions['view'] )
				{
					// forcable deny viewing perms to children if this isn't viewable
					$passPermissions['general']['viewNode'] = 'deny';
				}
			}
			else
			{
				$finalNodePermissions = array();
			}

			$finalPermissions[$nodeId] = $finalNodePermissions;

			$finalPermissions += _buildNodeTreePermissions( $userId, $userGroupIds, $passPermissions, $permissionsGrouped, $nodeId );
		}
	}
	

	return $finalPermissions;
}

function rebuildContentPermissions( array $userGroupIds, $userId, array $permissionsGrouped, array & $globalPerms )
{
	$finalPermissions = _buildNodeTreePermissions( $userId, $userGroupIds, $globalPerms, $permissionsGrouped );

	$GLOBALS['globalPerms'] = $globalPerms;

	return $finalPermissions;
}
