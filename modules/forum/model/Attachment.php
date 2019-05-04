<?php

if( ! defined( 'NV_MAINFILE' ) )
{
	die( 'Stop!!!' );
}

function Post_canViewAttachment( array $attachment )
{
	global $global_userid, $db_slave, $permission_combination_id;
	$post = $db_slave->query( '
		SELECT post.*
			,
				thread.*, thread.userid AS thread_user_id, thread.username AS thread_username,
				thread.post_date AS thread_post_date,
				post.userid, post.username, post.post_date,
				node.title AS node_title, node.description,
				user.*, IF(user.username IS NULL, post.username, user.username) AS username,
			permission.cache_value AS node_permission_cache
		FROM ' . NV_FORUM_GLOBALTABLE . '_post AS post
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_thread AS thread ON
				(thread.thread_id = post.thread_id)
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_node AS node ON
				(node.node_id = thread.node_id)
			LEFT JOIN ' . NV_USERS_GLOBALTABLE . ' AS user ON
				(user.userid = post.userid)
		LEFT JOIN ' . NV_FORUM_GLOBALTABLE . '_permission_cache_content AS permission
			ON (permission.permission_combination_id = ' . intval( $permission_combination_id ) . '
				AND permission.content_type = \'node\'
				AND permission.content_id = thread.node_id)
	WHERE post.post_id = ' . intval( $attachment['content_id'] ) )->fetch();
	if( ! $post )
	{
		return false;
	}
 
	$permissions = unserializePermissions( $post['node_permission_cache'] );

	$canViewPost = ModelPost_canViewPostAndContainer( $post, $post, $post, $null, $permissions );
	if( ! $canViewPost )
	{
		return false;
	}

	return ModelPost_canViewAttachmentOnPost( $post, $post, $post, $null, $permissions );
}

function ModelAttachment_canViewAttachment( array $attachment, $tempHash = '' )
{
	if( ! empty( $attachment['temp_hash'] ) && empty( $attachment['content_id'] ) )
	{

		return ( $tempHash === $attachment['temp_hash'] );
	}
	else
	{

		return Post_canViewAttachment( $attachment );
	}
}
function ModelAttachment_getAttachmentBymd5Filename( $md5filename, $is_tempHash )
{
	global $db_slave;
	
	if( $is_tempHash )
	{
		$where = 'attachment.temp_hash = ' . $db_slave->quote( $md5filename );		
	}else{
		$where = 'attachment.md5filename = ' . $db_slave->quote( $md5filename );	
	}
 
	return $db_slave->query( '
			SELECT attachment.*,
				data.*
			FROM ' . NV_FORUM_GLOBALTABLE . '_attachment AS attachment
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_attachment_data AS data ON
				(data.data_id = attachment.data_id)
			WHERE '. $where )->fetch();
}
function ModelAttachment_getAttachmentById( $attachmentId )
{
	global $db_slave;

	return $db_slave->query( '
			SELECT attachment.*,
				data.*
			FROM ' . NV_FORUM_GLOBALTABLE . '_attachment AS attachment
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_attachment_data AS data ON
				(data.data_id = attachment.data_id)
			WHERE attachment.attachment_id = ' . intval( $attachmentId ) )->fetch();
}

function ModelAttachment_getAttachmentsByContentIds( $contentType, array $contentIds )
{
	global $db_slave;

	$result = $db_slave->query( '
			SELECT attachment.*,
				data.*
			FROM ' . NV_FORUM_GLOBALTABLE . '_attachment AS attachment
			INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_attachment_data AS data ON
				(data.data_id = attachment.data_id)
			WHERE attachment.content_type = ' . $db_slave->quote( $contentType ) . '
				AND attachment.content_id IN (' . implode( ',', $contentIds ) . ')
			ORDER BY attachment.content_id, attachment.attach_date' );
	$data = array();

	while( $row = $result->fetch() )
	{
		$data[$row['attachment_id']] = $row;
	}
	return $data;

}
function ModelAttachment_prepareAttachment( array $attachment, $fetchContentLink = false )
{
	global $module_name, $module_upload;
	$alias_name = str_replace( '.', '-', $attachment['filename'] );
	$folder = floor( $attachment['data_id'] / 1000 );

	$attachment['extension'] = nv_getextension( $attachment['filename'] );

	if( $attachment['thumbnail_width'] )
	{
		$thumb_name = $attachment['data_id'] . '-' . $attachment['file_hash'] . '.' . $attachment['extension'];

		$attachment['thumbnailUrl'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/attach_thumb/' . $folder . '/' . $thumb_name;
	}
	else
	{
		$attachment['thumbnailUrl'] = '';
	}

	$attachment['contentLink'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=attachments/' . $attachment['md5filename'], true );
	$attachment['deleteUrl'] = 'attachments/delete';
	$attachment['viewUrl'] = 'attachments';

	return $attachment;
}

function ModelAttachment_prepareAttachments( array $attachments, $fetchContentLinks = false )
{
	foreach( $attachments as &$attachment )
	{
		$attachment = ModelAttachment_prepareAttachment( $attachment, $fetchContentLinks );
	}

	return $attachments;
}
function ModelAttachment_getAttachmentsByTempHash( $tempHash )
{
	global $db;
	if( strval( $tempHash ) === '' )
	{
		return array();
	}

	$result = $db->query( '
		SELECT attachment.*, data.filename, data.file_size, data.file_hash, data.file_path, data.width, data.height, data.thumbnail_width, data.thumbnail_height
		FROM ' . NV_FORUM_GLOBALTABLE . '_attachment AS attachment
		INNER JOIN ' . NV_FORUM_GLOBALTABLE . '_attachment_data AS data ON
			(data.data_id = attachment.data_id)
		WHERE attachment.temp_hash = ' . $db->quote( $tempHash ) . '
		ORDER BY attachment.attach_date' );
	$data = array();
	while( $rows = $result->fetch() )
	{
		$data[$rows['attachment_id']] = $rows;
	}
	$result->closeCursor();

}
function ModelAttachment_getAttachmentConstraints()
{

	return array(
		'extensions' => array(
			'rar',
			'zip',
			'txt',
			'pdf',
			'png',
			'jpg',
			'jpeg',
			'jpe',
			'gif' ),
		'size' => 1024 * 2,
		'limit' => 10,
		'width' => '',
		'height' => '',
		'count' => 10 );
}
 
