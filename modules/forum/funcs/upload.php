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
if( ! defined( 'NV_IS_USER' ) )
{
	getOutputJson(array( 'error' =>$lang_module['error_login'] ));

}

// Khong cho phep cache
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );

// Cross domain
// header("Access-Control-Allow-Origin: *");

$allowed_file_ext = array('zip', 'txt', 'pdf', 'png', 'jpg', 'jpeg', 'jpe', 'gif');
$allowed_file_type = array('application/x-rar','application/zip','image/jpg','image/jpeg','image/jpe','image/gif','image/png','text/plain','application/pdf');


 

// Kiem tra phien lam viec
$attachment_hash = $nv_Request->get_title( 'attachment_hash', 'post', '' );
$token = $nv_Request->get_title( 'token', 'post', '' );
$node_id = $nv_Request->get_int( 'node_id', 'post', '' );
$fileName = $nv_Request->get_title( 'name', 'post', '' );
$chunk = $nv_Request->get_int( 'chunk', 'post', 0 );
$chunks = $nv_Request->get_int( 'chunks', 'post', 0 );
$cleanupTargetDir = true; // Remove old files

$fileExt = nv_getextension( $fileName );
$fileOldName = nv_string_to_filename( $fileName );

if( $token != md5( session_id() . $global_config['sitekey'] . $node_id ) )
{

	getOutputJson(array( 'error' => $lang_module['uploadErrorSess'] ));
}

if( !in_array( $fileExt, $allowed_file_ext ) )
{
	
	getOutputJson( array( 'error' => 'Tệp tin tải lên phải có đuôi' . implode( ', ', $allowed_file_ext ) ) );
}

$file_type = nv_get_mime_type( $_FILES["Filedata"]["tmp_name"] );

if( !in_array( $file_type, $allowed_file_type ) )
{
	
	getOutputJson( array( 'error' => 'Kiểu tệp tin này không được phép tải lên' ) );
}
 
// Tang thoi luong phien lam viec
if( $sys_info['allowed_set_time_limit'] )
{
	set_time_limit( 5 * 3600 );
}

$result = $db->query( "SHOW TABLE STATUS WHERE NAME='" . NV_FORUM_GLOBALTABLE . "_attachment_data'" );
$item = $result->fetch();
$result->closeCursor();

$max_data_id = intval( $item['auto_increment'] );
$folder = floor( $max_data_id / 1000 );
$path_file = $module_upload . '/attachments/' . $folder;
$path_thumb = $module_upload . '/attach_thumb/' . $folder;

ForumMakeDir( $path_file );
ForumMakeDir( $path_thumb );
 
$fileTemp = NV_ROOTDIR . '/' . NV_TEMP_DIR;
$filePath = $fileTemp . '/' . $attachment_hash . '.' . $fileExt;

$file_size = $_FILES["Filedata"]['size'];

// Open temp file
if( ! $out = @fopen( "{$fileTemp}.part", $chunks ? "ab" : "wb" ) )
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
	$check = @rename( "{$fileTemp}.part", $filePath );

	if( empty( $check ) )
	{

		getOutputJson( array( 'error' => $lang_module['uploadErrorRenameFile'] ) );

	}
}

$path_file = NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/attachments/' . $folder;
$path_thumb = NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $module_upload . '/attach_thumb/' . $folder;

$file_hash = md5_file( $filePath );

$thumbName = $max_data_id . '-' . $file_hash . '.' . $fileExt;
$fileName = $max_data_id . '-' . $file_hash . '.data';

$thumb_url = '';
$image_url = '';

$is_image = false;
list( $width, $height, $type, $attr ) = getimagesize( $filePath );
if( $width > 0 && $height > 0 )
{
	$is_image = true;
	$thumb_url = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/attach_thumb/' . $folder . '/' . ForumCteateThumb( $filePath, $path_thumb, $thumbName, 100, 100, 100, false );
}

if( rename( $filePath, $path_file . '/' . $fileName ) )
{

	if( $width > 0 )
	{
		$thumbnail_width = 100;

		$thumbnail_height = 100;
	}
	else
	{
		$thumbnail_width = 0;

		$thumbnail_height = 0;
	}

	$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_attachment_data SET 
		userid=' . intval( $global_userid ) . ',
		upload_date=' . NV_CURRENTTIME . ', 
		filename=' . $db->quote( $fileOldName ) . ',
		file_size=' . $db->quote( $file_size ) . ',
		file_hash=' . $db->quote( $file_hash ) . ',
		file_path=\'\',
		width=' . $db->quote( $width ) . ',
		height=' . $db->quote( $height ) . ',
		thumbnail_width=' . $db->quote( $thumbnail_width ) . ',
		thumbnail_height=' . $db->quote( $thumbnail_height ) . ',
		attach_count=0' );

	$data_id = $db->lastInsertId();

	$db->query( 'INSERT INTO ' . NV_FORUM_GLOBALTABLE . '_attachment SET 
		data_id=' . intval( $data_id ) . ',
		content_type=\'\',
		content_id=0,
		attach_date=' . NV_CURRENTTIME . ',
		md5filename=' . $db->quote( nv_md5safe( $data_id . $fileOldName ) ) . ',
		temp_hash=' . $db->quote( $attachment_hash ) . ',
		unassociated=1,
		view_count=0' );
	$attachment_id = $db->lastInsertId();

	if( $data_id > 0 && $attachment_id > 0 )
	{

		$db->query( 'UPDATE ' . NV_FORUM_GLOBALTABLE . '_attachment_data
			SET attach_count = attach_count + 1
			WHERE data_id = ' . intval( $data_id ) );

		$alias_name = str_replace( '.', '-', $fileOldName );
		$image_url = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=attachments/h-' . $attachment_hash . '-' . $attachment_id, true );
 
		getOutputJson( array( 'data' => array(
				'attachment_id' => $attachment_id,
				'file_hash' => $file_hash,
				'basename' => $fileOldName,
				'is_image' => $is_image,
				'thumb_url' => $thumb_url,
				'image_url' => $image_url,
				'token' => md5( session_id() . $global_config['sitekey'] . $attachment_id ),

		) ) );

	}
	else
	{

		getOutputJson( array( 'error' => $lang_module['uploadErrorInsert'] ) );

	}
}
else
{

	unlink( $filePath );
	unlink( str_replace( '//', '/', NV_ROOTDIR . '/' . $thumb_url ) );

	getOutputJson( array( 'error' => $lang_module['uploadErrorRenameFile'] ) );

}
