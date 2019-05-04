<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2015 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 09 Nov 2015 06:03:42 GMT
 */

if( ! defined( 'NV_ADMIN' ) ) die( 'Stop!!!' );


$allow_func = array(
	'main',
	'alias',
	'config',
	'node',
	'json',
	'node-permissions',
	'moderators' );
 
$sub_node['node&action=insert&node_type_id=category'] = $lang_module['node_create_category'];
$sub_node['node&action=insert&node_type_id=forum'] = $lang_module['node_create_forum'];
$sub_node['node&action=insert&node_type_id=linkforum'] = $lang_module['node_create_linkforum'];
$sub_node['node&action=insert&node_type_id=page'] = $lang_module['node_create_page'];

$submenu['node'] = array( 'title' => $lang_module['node'], 'submenu' => $sub_node );


$sub_moderators['moderators&action=add'] = $lang_module['moderators_add'];
$submenu['moderators'] = array( 'title' => $lang_module['moderators'], 'submenu' => $sub_moderators );



$submenu['config'] = $lang_module['config'];
 



	