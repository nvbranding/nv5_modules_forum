<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */

if ( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if ( ! nv_function_exists( 'nv_verify_post' ) )
{
	function nv_verify_post( $block_config )
	{
		global $op, $global_config, $nodePermissions, $node_permission_cache, $module_info, $node_alias, $node_id, $client_info, $user_info, $lang_module, $module_file, $module_name, $site_mods, $db;
		
	
		
			$module = $block_config['module'];
			$mod_data = $site_mods[$module]['module_data'];
			$mod_file = $site_mods[$module]['module_file'];
 
			if ( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $mod_file . '/blocks/block_verify_post.tpl' ) )
			{
				$block_theme = $module_info['template'];
			}
			else
			{
				$block_theme = 'default';
			}
			$xtpl = new XTemplate( 'block_verify_post.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file . '/blocks' );
			$xtpl->assign( 'LANG', $lang_module );
			$xtpl->assign( 'MODULE_FILE', $mod_file );
			$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
			$xtpl->assign( 'TEMPLATE', $module_info['template'] );
			if ( ! defined('NV_IS_USER') )
			{
				$nv_redirect = nv_redirect_encrypt( $client_info['selfurl'] );
                
				$link_register =  NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=register&nv_redirect=' . $nv_redirect;
            
				$xtpl->assign( 'LINK_REGISTER', $link_register );
			
				$xtpl->parse( 'main.register' );
				
			}else
			{ 
				 
				if( $node_id > 0 && $op !='content' )
				{
					$xtpl->assign( 'LINK_POST', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '='. $module .'&' . NV_OP_VARIABLE . '=content/'. $node_alias .'-'. $node_id . '/create-thread' . $global_config['rewrite_exturl'], true ) );
					$xtpl->parse( 'main.verify_post' );
				}
				
 	
			}
			
			$xtpl->parse( 'main' );
			return $xtpl->text( 'main' );
		 
	}
}

if ( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name;
	$module = $block_config['module'];
	if ( isset( $site_mods[$module] ) )
	{
		$content = nv_verify_post( $block_config );
	}
}