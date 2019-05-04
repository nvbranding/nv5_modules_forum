<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU(dlinhvan@gmail.com)
 * @Copyright (C) 2013 webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 27-04-2013 08:20
 */
if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$token = $nv_Request->get_title( 'token', 'get', '' );
$username = $nv_Request->get_title( 'user', 'get', '' );
$super = $nv_Request->get_int( 'super', 'get', 0 );
$userid = $nv_Request->get_int( 'userid', 'get', 0 );
$moderator_id = $nv_Request->get_int( 'mod', 'get', 0 );


$contentModerators = getContentModeratorById( $moderator_id );
if( !empty( $contentModerators ) )
{
	$result = $db->query('DELETE FROM '. NV_FORUM_GLOBALTABLE .'_moderator_content WHERE moderator_id = '. intval( $contentModerator['moderator_id'] ) ); 
	if( $result->rowCount() )
	{
		insertOrUpdateContentModerator( $contentModerators['userid'], $contentModerators['content_type'], $contentModerators['content_id'], $contentModerators['moderator_permissions'], array( 'general_moderator_permissions' => array(), 'extra_user_group_ids' => array(), 'is_staff' => 0, 'is_delete' => true ) );
	}
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

$Moderators = getGeneralModeratorByUserId( $Moderators['userid'] );
if( !empty( $Moderators ) )
{
	$result = $db->query('DELETE FROM '. NV_FORUM_GLOBALTABLE .'_moderator WHERE userid = '. intval( $Moderators['userid'] ) ); 
	if( $result->rowCount() )
	{
		insertOrUpdateGeneralModerator( $data['userid'], $data['general_moderator_permissions'], $data['is_super_moderator'], array(
			'super_moderator_permissions' => array(),
			'extra_user_group_ids' => array(),
			'is_staff' => 0 ) );
	}
}
 
$db->query('UPDATE ' . NV_USERS_GLOBALTABLE . ' SET is_staff=0, is_moderator=0 WHERE userid=' . intval( $userid ) );
 
 
$json = array();
$json['redirect'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=moderators';
header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';