<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweb.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 22:18
 */

if( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

// $result = $db->query('SELECT attachment.*,
// data.*
// FROM '. NV_FORUM_GLOBALTABLE .'_attachment AS attachment
// INNER JOIN '. NV_FORUM_GLOBALTABLE .'_attachment_data AS data ON
// (data.data_id = attachment.data_id)');
// while( $row = $result->fetch() )
// {
// $db->query('UPDATE '. NV_FORUM_GLOBALTABLE .'_attachment SET md5filename='. $db->quote( nv_md5safe( $row['data_id'] . $row['filename'] ) ) .' WHERE attachment_id=' . intval( $row['attachment_id'] ) );

// }
// die('ok');

if( ! defined( 'NV_IS_USER' ) )
{
	/* Nhom khach truy cap => 5 */
	$permission_combination_id = 5;
	$result = $db_slave->query( 'SELECT cache_value FROM ' . NV_FORUM_GLOBALTABLE . '_permission_combination WHERE permission_combination_id =' . intval( $permission_combination_id ) );
	$generalPermissions = $result->fetchColumn();
	$result->closeCursor();

	$generalPermissions = unserializePermissions( $generalPermissions );
}
else
{
	/* Nhom thanh vien truy cap => 4 */
	$permission_combination_id = $user_info['permission_combination_id'];
	$generalPermissions = $user_info['permissions'];
}

if( ACTION_METHOD == 'delete' )
{
	$json = array();
	$attachment_id = $nv_Request->get_int( 'attachment_id', 'post', 0 );
	$attachment = $db->query( 'SELECT attachment.*,
				data.filename, data.file_size, data.file_hash, data.file_path, data.width, data.height, data.thumbnail_width, data.thumbnail_height
			FROM ' . NV_FORUM_GLOBALTABLE . '_attachment AS attachment
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_attachment_data AS data ON
				(data.data_id = attachment.data_id)
			WHERE attachment.attachment_id = ' . intval( $attachment_id ) )->fetch();
	if( ! empty( $attachment ) )
	{
		$db->query( 'DELETE FROM ' . NV_FORUM_GLOBALTABLE . '_attachment WHERE attachment_id = ' . intval( $attachment_id ) );
		$db->query( '
			UPDATE ' . NV_FORUM_GLOBALTABLE . '_attachment_data
			SET attach_count = IF(attach_count > 0, attach_count - 1, 0)
			WHERE data_id = ' . intval( $attachment_id ) );
	}
	$json['success'] = 'ok';
	header( 'Content-Type: application/json' );
	include NV_ROOTDIR . '/includes/header.php';
	echo json_encode( $json );
	include NV_ROOTDIR . '/includes/footer.php';

}
else
{
	ignore_user_abort( true );

	$count_op = sizeof( $array_op );

	$tempHash = '';
	$attachmentData = array();
	if( $count_op == 2 && isset( $array_op[1] ) && preg_match( '/^[a-z0-9-]+$/', $array_op[1] ) )
	{
		$md5filename = $array_op[1];
		$checked = explode( '-', $md5filename );
		$is_tempHash = false;
		$attachment_id = 0;
		if( count( $checked ) == 3 && $checked[0] == 'h' )
		{
			$tempHash = $checked[1];
			$attachment_id = $checked[2];
			$md5filename = $tempHash;
			$is_tempHash = true;
		}

		$attachmentData = ModelAttachment_getAttachmentBymd5Filename( $md5filename, $is_tempHash, $attachment_id );
		if( ! $attachmentData )
		{
			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( ThemeErrorNotFoundAttachment() );
			include NV_ROOTDIR . '/includes/footer.php';

		}

		if( ! ModelAttachment_canViewAttachment( $attachmentData, $tempHash ) )
		{
			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( ThemeResponseNoPermission() );
			include NV_ROOTDIR . '/includes/footer.php';
		}

	}
	else
	{

		Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true ) );
		die();

	}

	$folder = floor( $attachmentData['data_id'] / 1000 );

	$filepath = NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/attachments/' . $folder . '/' . $attachmentData['data_id'] . '-' . $attachmentData['file_hash'] . '.data';
	

	if( ! file_exists( $filepath ) )
	{
		include NV_ROOTDIR . '/includes/header.php';
		echo nv_site_theme( ThemeErrorNotFoundAttachment() );
		include NV_ROOTDIR . '/includes/footer.php';
	}
	$db->exec( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_attachment SET view_count = view_count + 1 WHERE attachment_id = ' . intval( $attachmentData['attachment_id'] ) );
	$db->exec( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_attachment_view SET attachment_id = ' . intval( $attachmentData['attachment_id'] ) );
	
	
	function getClientInfo($key)
    {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            return $_ENV[$key];
        } elseif (@getenv($key)) {
            return @getenv($key);
        } elseif (function_exists('apache_getenv') && apache_getenv($key, true)) {
            return apache_getenv($key, true);
        }
        return '';
    }
	
	$file_mime = nv_get_mime_type( $filepath );
	$resume = 1;
	$seek_start = 0;
	$seek_end = -1;
	$max_speed =0;
	$mtime = 0;
	$data_section = false;
	$file_size = intval(sprintf('%u', $attachmentData['file_size'] ) );
	
	$disable_functions = (ini_get('disable_functions') != '' and ini_get('disable_functions') != false) ? array_map('trim', preg_split("/[\s,]+/", ini_get('disable_functions'))) : array();
	if (extension_loaded('suhosin')) {
		$disable_functions = array_merge($disable_functions, array_map('trim', preg_split("/[\s,]+/", ini_get('suhosin.executor.func.blacklist'))));
	}
	
	if( ( $http_range = getClientInfo( 'HTTP_RANGE' ) ) != '' )
	{
		$seek_range = substr( $http_range, 6 );

		$range = explode( '-', $seek_range );

		if( ! empty( $range[0] ) )
		{
			$seek_start = intval( $range[0] );
		}

		if( isset( $range[1] ) and ! empty( $range[1] ) )
		{
			$seek_end = intval( $range[1] );
		}

		if( ! $resume )
		{
			$seek_start = 0;
		}
		else
		{
			$data_section = true;
		}
	}

	if( @ob_get_length() )
	{
		@ob_end_clean();
	}
	$old_status = ignore_user_abort( true );

	if( function_exists( 'set_time_limit' ) and ! in_array( 'set_time_limit', $disable_functions ) )
	{
		set_time_limit( 0 );
	}

	if( $seek_start > ( $file_size - 1 ) )
	{
		$seek_start = 0;
	}
	
	if(  $attachmentData['width'] > 0 &&  $attachmentData['height'] > 0 )
	{
		header( 'Pragma: public' );
		header( 'Expires: 0' );
		header( 'Cache-Control:' );
		header( 'Cache-Control: public' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: ' . $file_mime . '' );
		header( 'Content-Disposition: inline;filename=' . $attachmentData['filename'] );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . $file_size );
		@readfile( $filepath ) or die( 'File not found.' );
		exit();
	}
 
	$res = fopen( $filepath, 'rb' );

	if( ! $res )
	{
		die( 'File error' );
	}

	if( $seek_start )
	{
		fseek( $res, $seek_start );
	}
	if( $seek_end < $seek_start )
	{
		$seek_end = $file_size - 1;
	}
	
	 
	header( 'Pragma: public' );
	header( 'Expires: 0' );
	header( 'Cache-Control:' );
	header( 'Cache-Control: public' );
	header( 'Content-Description: File Transfer' );
	header( 'Content-Type: ' . $file_mime );
	if( strstr( getClientInfo( 'HTTP_USER_AGENT' ), 'MSIE' ) != false )
	{
		header( 'Content-Disposition: attachment; filename="' . urlencode( $attachmentData['filename'] ) . '";' );
	}
	else
	{
		header( 'Content-Disposition: attachment; filename="' . $attachmentData['filename'] . '";' );
	}
	header( 'Last-Modified: ' . date( 'D, d M Y H:i:s \G\M\T', $mtime ) );
	
	if( $data_section and $resume )
	{
		header( 'HTTP/1.1 206 Partial Content' );
		header( 'Status: 206 Partial Content' );
		header( 'Accept-Ranges: bytes' );
		header( 'Content-Range: bytes ' . $seek_start . '-' . $seek_end . '/' . $file_size );
		header( 'Content-Length: ' . ( $seek_end - $seek_start + 1 ) );
	}
	else
	{
		header( 'Content-Length: ' . $file_size );
	}

	if( function_exists( 'usleep' ) and ! in_array( 'usleep', $disable_functions ) and ( $speed = $max_speed ) > 0 )
	{
		$sleep_time = ( 8 / $speed ) * 1e6;
	}
	else
	{
		$sleep_time = 0;
	}

	while( ! ( connection_aborted() or connection_status() == 1 ) and ! feof( $res ) )
	{
		print ( fread( $res, 1024 * 8 ) );
		flush();
		if( $sleep_time > 0 )
		{
			usleep( $sleep_time );
		}
	}
	fclose( $res );

	ignore_user_abort( $old_status );
	if( function_exists( 'set_time_limit' ) and ! in_array( 'set_time_limit', $disable_functions ) )
	{
		set_time_limit( ini_get( 'max_execution_time' ) );
	}
	exit();
  
}
die('Nothing to download!');