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

$page_title = $lang_module['node'];

$data['parent_id'] = $nv_Request->get_int( 'parent_id', 'post', 0 );
$data['token'] = $nv_Request->get_string( 'token', 'post', '' );
$data['node_type_id'] = $nv_Request->get_int( 'node_type_id', 'post,get', 0 );

if( !in_array( $data['node_type_id'], array( 1, 2, 3, 4 ) ) )
{
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&action=add' );
	die();
}



if( $data['node_type_id'] == 1 ) //category
{
	
}	
elseif( $data['node_type_id'] == 2 ) //forum
{
	
}
elseif( $data['node_type_id'] == 3 ) //forum_link
{
	
}
elseif( $data['node_type_id'] == 4 ) //page
{
	
}
  
