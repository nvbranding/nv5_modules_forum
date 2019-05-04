<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog  http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 21 Jan 2015 14:00:59 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

$json = array();

/* kiem tra dang nhap thanh vien */
// if( ! defined( 'NV_IS_USER' ) )
// {
	// gltJsonResponse( array( 'code' => 200, 'message' => $lang_module['error_login'] ) );

// }

// Khong cho phep cache
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );

// Cross domain
// header("Access-Control-Allow-Origin: *");


// array(5) {
  // ["name"]=>
  // string(50) "12438954_930229283698772_8891950920324966538_n.jpg"
  // ["type"]=>
  // string(10) "image/jpeg"
  // ["tmp_name"]=>
  // string(24) "D:\Aaweb\tmp\php2F2C.tmp"
  // ["error"]=>
  // int(0)
  // ["size"]=>
  // int(52749)
// }

// Kiem tra phien lam viec
$attachment_hash = $nv_Request->get_title( 'attachment_hash', 'post', '' );
$token = $nv_Request->get_title( 'token', 'post', '' );
$thread_id = $nv_Request->get_int( 'thread_id', 'post', '' );
$node_id = $nv_Request->get_int( 'node_id', 'post', '' );
$fileName = $nv_Request->get_title( 'name', 'post', '' );
$chunk = $nv_Request->get_int( 'chunk', 'post', 0 );
$chunks = $nv_Request->get_int( 'chunks', 'post', 0 );
$cleanupTargetDir = true; // Remove old files
if( $token != md5( session_id() . $global_config['sitekey'] . $thread_id  . $node_id ) )
{
	getOutputJson( array( 'error' => $lang_module['uploadErrorSess'] ) );
}

// Tang thoi luong phien lam viec
if( $sys_info['allowed_set_time_limit'] )
{
	set_time_limit( 5 * 3600 );
}
 
 
$max_data_id = $db->query('SELECT Max(data_id) FROM ' . NV_FORUM_GLOBALTABLE . '_attachment_data')->fetchColumn();
$folder = floor( $max_data_id / 1000 );
$path_file = $module_upload . '/attachments/' . $folder;
$path_thumb = $module_upload . '/attach_thumb/' . $folder;

ForumMakeDir( $path_file );
ForumMakeDir( $path_thumb );

$path_file = NV_ROOTDIR . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/attachments/' . $folder;
$path_thumb = NV_ROOTDIR . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/attach_thumb/' . $folder;

 
$fileExt = nv_getextension( $fileName );
$fileName = change_alias( substr( $fileName, 0, -( strlen( $fileExt ) + 1 ) ) ) . '.' . $fileExt;
$fileName = nv_string_to_filename( $fileName );


$file_hash = md5_file( $_FILES["Filedata"]["tmp_name"] );

$new_name = ( $max_data_id + 1 ) . '-' . $file_hash . '.' . $fileExt;
 
$filePath = $path_file . '/' . $fileName;
 
$filenewPath = $path_file . '/' . $new_name;
$filethumbPath = $path_file . '/' . ( $max_data_id + 1 ) . '-' . $file_hash . '.' . $fileExt;
 
$file_size = $_FILES["Filedata"]['size'];
// Open temp file
if( ! $out = @fopen( "{$filePath}.part", $chunks ? "ab" : "wb" ) )
{
	getOutputJson( array( 'error' => 'Failed to open output stream.' ) );
}
 
if( ! empty( $_FILES ) )
{
	if( $_FILES["Filedata"]["error"] || ! is_uploaded_file( $_FILES["Filedata"]["tmp_name"] ) )
	{
		getOutputJson( array( 'error' => 'Failed to move uploaded file.' ) );
	}

	// Read binary input stream and append it to temp file
	if( ! $in = @fopen( $_FILES["Filedata"]["tmp_name"], "rb" ) )
	{
		getOutputJson( array( 'error' => 'Failed to open input stream.' ) );
	}
}
else
{
	if( ! $in = @fopen( "php://input", "rb" ) )
	{
		getOutputJson( array( 'error' => 'Failed to open input stream.' ) );
	}
}

while( $buff = fread( $in, 4096 ) )
{
	fwrite( $out, $buff );
}

@fclose( $out );
@fclose( $in );

 
clearstatcache();
 
// Check if file has been uploaded
if( ! $chunks || $chunk == $chunks - 1 )
{
	// Strip the temp .part suffix off
	$check = @rename( "{$filePath}.part", $filenewPath );

	if( empty( $check ) )
	{
		getOutputJson( array( 'error' => $lang_module['uploadErrorRenameFile'] ) );

	}
}
 
$userid = !empty( $user_info ) ? $user_info['userid'] : 0;

$thumbnail_width = 100; 

$thumbnail_height = 100; 

$thumb_url = '';
$image_url = '';
 
$is_image = false;
list( $width, $height, $type, $attr ) = getimagesize( $filenewPath );
if( $width > 0 && $height > 0 )
{
	$is_image = true;
	$thumb_url = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/attach_thumb/' . $folder . '/' . ForumCteateThumb( $filenewPath, $path_thumb, 100, 100, 100, false );	
}

$db->query('INSERT INTO '. NV_FORUM_GLOBALTABLE .'_attachment_data SET 
	userid='. intval( $userid ) .',
	upload_date='. NV_CURRENTTIME .', 
	filename='. $db->quote( $fileName ) .',
	file_size='. $db->quote( $file_size ) .',
	file_hash='. $db->quote( $file_hash ) .',
	file_path=\'\',
	width='. $db->quote( $width ) .',
	height='. $db->quote( $height ) .',
	thumbnail_width='. $db->quote( $thumbnail_width ) .',
	thumbnail_height='. $db->quote( $thumbnail_height ) .',
	attach_count=0' ); 

$data_id = $db->lastInsertId();

$db->query('INSERT INTO '. NV_FORUM_GLOBALTABLE .'_attachment SET 
	data_id='. intval( $data_id ) .',
	content_type=\'\',
	content_id=0,
	attach_date='. NV_CURRENTTIME .',
	temp_hash='. $db->quote( $attachment_hash ) .',
	unassociated=1,
	view_count=0');
		
getOutputJson( array( 'data' => array(
	'data_id' => $data_id,
	'file_hash' => $file_hash,
	'basename' => $fileName,
	'is_image' => $is_image,
	'thumb_url' => $thumb_url,
	'image_url' => $image_url,
) ) );
