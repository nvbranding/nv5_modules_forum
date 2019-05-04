<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'ModuleBlockTopx' ) )
{
	function nv_block_config_topx( $module, $data_block, $lang_block )
	{
		global $site_mods;
		$html = '';
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['numrow'] . '</td>';
		$html .= '<td><input type="text" name="config_numrow" size="5" value="' . $data_block['numrow'] . '" class="form-control"/></td>';
		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<td>' . $lang_block['numcut'] . '</td>';
		$html .= '<td><input type="text" name="config_numcut" size="5" value="' . $data_block['numcut'] . '" class="form-control"/></td>';
		$html .= '</tr>';

		return $html;
	}

	function nv_block_config_topx_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['numrow'] = $nv_Request->get_int( 'config_numrow', 'post', 0 );
		$return['config']['numcut'] = $nv_Request->get_int( 'config_numcut', 'post', 0 );
		return $return;
	}

	function ModuleBlockTopx( $block_config )
	{
		global $module_info, $global_config, $nv_Request, $lang_module, $module_file, $module_name, $site_mods, $db_slave;
		
		$module = $block_config['module'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];
		 
		 
		
		$LastestThread = array();
		$sql = "SELECT * FROM ". NV_FORUM_GLOBALTABLE ."_thread WHERE discussion_state = 'visible' AND node_id NOT IN ( 48 ) AND node_id NOT IN ( 34 ) ORDER BY last_post_date DESC LIMIT 0,10";
		$result = $db_slave->query( $sql );
		while( $row = $result->fetch( ) )
		{
			$LastestThread[] = $row;
		}
		$result->closeCursor();

		

		if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $mod_file . '/ModuleBlockTopx.tpl' ) )
		{
			$block_theme = $module_info['template'];
		}
		else
		{
			$block_theme = 'default';
		}
		$xtpl = new XTemplate( 'ModuleBlockTopx.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'MODULE_FILE', $mod_file );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'TEMPLATE', $module_info['template'] );
		$xtpl->assign( 'TOKEN', md5( session_id() . $global_config['sitekey'] ) );
		if( $LastestThread )
		{
			foreach( $LastestThread as $loop )
			{
				$loop['last_post_link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module . '&' . NV_OP_VARIABLE . '=post/' . $loop['last_post_id'] . '/view';

				$xtpl->assign( 'LOOP', $loop );
				$xtpl->parse( 'main.lastest_thread.loop' );
			}
			$xtpl->parse( 'main.lastest_thread' );
		} 
 
		$xtpl->parse( 'main' );
		return $xtpl->text( 'main' ); 
	}
}

if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		$content = ModuleBlockTopx( $block_config );
	}
}