<?php

function ForumCheckPermission( $permissionName  )
{
	if( ! defined( 'NV_IS_USER' ) )
	{
		$contents = ThemeForumLogin( ); 
	}else
	{
		$contents = ThemeErrorPermission( $permissionName ='');
	}
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}

function ForumUserLadder( )
{
	global $module_name;
	
	return getdbCache( $module_name, 'SELECT * FROM ' . NV_USERS_GLOBALTABLE . '_title_ladder ORDER BY minimum_level DESC', 'users', 'minimum_level' ); 
}
 /* tính điểm thưởng chưa làm */
function ForumCheckTrophies( $user )
{
	$ForumUserLadder = ForumUserLadder( );
	foreach( $ForumUserLadder as $key => $value ) 
	if( $point >= $key ) return $value['title'];	 
    return;
 
}
 