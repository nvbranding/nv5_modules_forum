<?php

if( ACTION_METHOD =='newthreads' ) 
{
	$json = array();
 
	$sql = "SELECT thread_id, title, last_post_id, last_post_username, last_post_user_id, last_post_date FROM ". NV_FORUM_GLOBALTABLE ."_thread WHERE discussion_state = 'visible' AND node_id NOT IN ( 34 ) ORDER BY post_date DESC LIMIT 0,10";
	$result = $db_slave->query( $sql );
	while( $row = $result->fetch( ) )
	{
		$json['data'][] = array(
			'thread_id'=> $row['thread_id'], 
			'title'=> $row['title'], 
			'link'=> nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $row['last_post_id'] . '/view', true),
			'last_post_username'=> $row['last_post_username'], 
			'last_post_user_id'=> $row['last_post_user_id'], 
			'last_post_date'=> date('d/m/Y H:i:s', $row['last_post_date']),  
		);
	}
	$result->closeCursor();
	 
	
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';
}
elseif( ACTION_METHOD =='mostviewed' ) 
{
	$json = array();
 
	$sql = "SELECT thread_id, title, last_post_id, last_post_username, last_post_user_id, last_post_date FROM ". NV_FORUM_GLOBALTABLE ."_thread WHERE discussion_state = 'visible' AND node_id NOT IN ( 34 ) ORDER BY view_count DESC LIMIT 0,10";
	$result = $db_slave->query( $sql );
	while( $row = $result->fetch( ) )
	{
		$json['data'][] = array(
			'thread_id'=> $row['thread_id'], 
			'title'=> $row['title'], 
			'link'=>  nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $row['last_post_id'] . '/view', true),
			'last_post_username'=> $row['last_post_username'], 
			'last_post_user_id'=> $row['last_post_user_id'], 
			'last_post_date'=> date('d/m/Y H:i:s', $row['last_post_date']), 
		);
	}
	$result->closeCursor();
	 
	
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php'; 
}
elseif( ACTION_METHOD =='mostpost' ) 
{
	$json = array();
 
	$sql = "SELECT thread_id, title, last_post_id, last_post_username, last_post_user_id, last_post_date FROM ". NV_FORUM_GLOBALTABLE ."_thread WHERE discussion_state = 'visible' AND node_id NOT IN ( 34 ) ORDER BY reply_count DESC LIMIT 0,10";
	$result = $db_slave->query( $sql );
	while( $row = $result->fetch( ) )
	{
		$json['data'][] = array(
			'thread_id'=> $row['thread_id'], 
			'title'=> $row['title'], 
			'link'=>  nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $row['last_post_id'] . '/view',true),
			'last_post_username'=> $row['last_post_username'], 
			'last_post_user_id'=> $row['last_post_user_id'], 
			'last_post_date'=> date('d/m/Y H:i:s', $row['last_post_date']), 
		);
	}
	$result->closeCursor();
	 
	
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php'; 
}
trigger_error('ERROR !');