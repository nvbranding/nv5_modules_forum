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
 

$data['node_type_id'] = nv_substr( $nv_Request->get_title( 'node_type_id', 'get,post', '', '' ), 0, 10 );
 
if( !in_array( $data['node_type_id'], array( 'category', 'forum', 'linkforum', 'page' ) ) )
{
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=add' );
	die();
}
 

require_once NV_ROOTDIR . '/modules/' . $module_file . '/admin/node/insert_' . $data['node_type_id']  . '.php';

