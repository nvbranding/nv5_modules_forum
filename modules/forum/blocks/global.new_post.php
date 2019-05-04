<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  7, 21, 2013 17:33
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nv_thao_luan_post' ) )
{

	function nv_thao_luan_post( $block_config )
	{
		global $module_info, $lang_module, $site_mods, $node_id, $node_alias;
		$mod_name = $block_config['module'];
		$mod_data = $site_mods[$mod_name]['module_data'];
		$mod_file = $site_mods[$mod_name]['module_file'];

		if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $mod_file . '/global_new_post.tpl' ) )
		{
			$block_theme = $module_info['template'];
		}
		else
		{
			$block_theme = 'default';
		}

		$xtpl = new XTemplate( 'global_new_post.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'TEMPLATE', $block_theme );
		$xtpl->assign( 'POST_LINK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $mod_name . '&amp;' . NV_OP_VARIABLE . '=post/' . $node_alias );
		
		$xtpl->parse( 'main' );
		return $xtpl->text( 'main' );

	}
}

if( defined( 'NV_SYSTEM' ) )
{
	$content = nv_thao_luan_post( $block_config );
}