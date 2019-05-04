<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweb.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 1:22
 */

if( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) ) die( 'Stop!!!' );

$admin_id = $admin_info['userid'];
 
 

require_once NV_ROOTDIR . '/includes/forum/model/node.php';
require_once NV_ROOTDIR . '/includes/forum/model/users.php';
require_once NV_ROOTDIR . '/includes/forum/model/permission.php';
require_once NV_ROOTDIR . '/includes/forum/model/moderator.php'; 
require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.function.php';
 
$array_status = array( 
    '0' => $lang_module['disabled'], '1' => $lang_module['enable'] 
);

$array_permissions_value = array('unset', 'content_allow', 'reset', 'deny');

$array_forum_type = array( 
    '1' => $lang_module['node_group'], '2' => $lang_module['node_forum'], '3' => $lang_module['node_link'], '4' => $lang_module['node_page']
);
 
$forum_node = getdbCache( $module_name, 'SELECT * FROM ' . NV_FORUM_GLOBALTABLE . '_node WHERE status =1 ORDER BY sort ASC', 'node', 'node_id' ); 
 
 
 
define( 'NV_IS_FILE_ADMIN', true ); 