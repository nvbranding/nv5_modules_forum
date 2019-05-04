<?php

/**
 * @Project NUKEVIET 5.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 nvbranding.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  Wed, 15 Jun 2016 01:20:15 GMT
 */
 
$module_version = array( 
    'name' => 'name', 
    'modfuncs' => 'main, content, post, viewnode, threads', 
    'submenu' => 'view', 
	'is_sysmod' => 1, 
    'virtual' => 0, 
    'version' => '5.0.00', 
    'date' => 'Wed, 15 Jun 2016 01:20:15 GMT', 
    'author' => 'DANGDINHTU-NVBRANDING (hoang.nt@nvbranding.vn)', 
    'note' => '', 
	'uploads_dir' => array( 
        $module_name,
		$module_name . '/node',
		$module_name . '/attachments',
		$module_name . '/attach_thumb'
	) 
);