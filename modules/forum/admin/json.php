<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );
 
if( ACTION_METHOD == 'getUsername' )
{
	$json = array();
	$data = array();
	
	$username = $nv_Request->get_title( 'username', 'get', '' );
 
	if( ! empty( $username ) )
	{
		$db->sqlreset()
			->select( 'userid, username' )
			->from( NV_USERS_GLOBALTABLE )
			->where( 'username LIKE :username' )
			->limit( '10' );

		$sth = $db->prepare( $db->sql() );
		$sth->bindValue( ':username', '%' . $username . '%' );
		$sth->execute();
		
		while( $rows = $sth->fetch() )
		{
			$json[] = $rows;
		}
	}
 
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';
}
 
trigger_error("No Action Method");