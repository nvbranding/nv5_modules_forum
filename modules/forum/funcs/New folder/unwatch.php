<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweB.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 22:18
 */

if ( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];

$token = $nv_Request->get_title( 'token', 'get', '' );
if ( preg_match( '/^[a-zA-Z0-9]+$/', $token ) )
{

	$sql = "SELECT B.user_id, B.thread_id, A.title, B.sendmail, B.token 
			FROM " . NV_PREFIXLANG . "_" . $module_data . "_thread A
			LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_thread_watch B
			ON A.thread_id = B.thread_id
			WHERE B.token = '" . $token . "'";

	list( $user_id, $thread_id, $title, $sendmail, $token ) = $db->query( $sql )->fetch( 3 );
	
	if ( $thread_id > 0 && $user_id > 0 && $sendmail == 1)
	{
		$sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_thread_watch
			SET sendmail = 0, token =  '' 
			WHERE thread_id = " . $thread_id . " AND user_id = " . $user_id . "";

		if ( $db->query( $sql ) )
		{
			$url = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $title ) . "/" . $thread_id;
			$contents = call_user_func( "unwatch_redirect", $url, $title );

			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';
			exit();

		}
		else
		{
			Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
			exit();
		}
	}
	else
	{
		Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
		exit();
	}
}
else
{

	Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
	exit();
}