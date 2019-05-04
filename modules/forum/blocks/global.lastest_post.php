<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nv_block_lastest_post' ) )
{
	function nv_block_config_lastest_post( $module, $data_block, $lang_block )
	{
		global $site_mods;
		$html = "";
		$html .= "<tr>";
		$html .= "<td>" . $lang_block['numrow'] . "</td>";
		$html .= "<td><input type=\"text\" name=\"config_numrow\" size=\"5\" value=\"" . $data_block['numrow'] . "\"/></td>";
		$html .= "</tr>";

		$html .= "<tr>";
		$html .= "<td>" . $lang_block['numcut'] . "</td>";
		$html .= "<td><input type=\"text\" name=\"config_numcut\" size=\"5\" value=\"" . $data_block['numcut'] . "\"/></td>";
		$html .= "</tr>";

		return $html;
	}

	function nv_block_config_lastest_post_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['numrow'] = $nv_Request->get_int( 'config_numrow', 'post', 0 );
		$return['config']['numcut'] = $nv_Request->get_int( 'config_numcut', 'post', 0 );
		return $return;
	}

	function nv_block_lastest_post( $block_config )
	{
		global $module_info, $module_name, $site_mods, $db;
		$module = $block_config['module'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];

		$lastest_post = array();
		$cache_file_lastest_post = NV_LANG_DATA . "_" . md5( $module ) . "_lastest_post_" . NV_CACHE_PREFIX . ".cache";

		if( ( $cache_lastest_post = $nv_Cache->getItem( $cache_file_lastest_post ) ) != false )
		{
			$lastest_post = unserialize( $cache_lastest_post );
		}
		else
		{
			$sql = "SELECT  thread_id, title, view_count, username , user_id ,  last_post_date, last_post_id, last_post_page, last_post_user_id, last_post_username, prefix_id 
				FROM  " . NV_PREFIXLANG . "_" . $mod_data . "_thread 
				ORDER BY  last_post_date DESC 
				LIMIT 0 , " . $block_config['numrow'];
			$query = $db->query( $sql );
			while( $row = $query->fetch() )
			{
				$lastest_post[] = $row;
			}
			$cache_lastest_post = serialize( $lastest_post );
			nv_set_cache( $cache_file_lastest_post, $cache_lastest_post );
		}

		$i = 1;
		if( ! empty( $lastest_post ) )
		{
			if( file_exists( NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $mod_file . "/block_lastest_post.tpl" ) )
			{
				$block_theme = $module_info['template'];
			}
			else
			{
				$block_theme = "default";
			}
			$xtpl = new XTemplate( "block_lastest_post.tpl", NV_ROOTDIR . "/themes/" . $block_theme . "/modules/" . $mod_file );
			foreach( $lastest_post as $item )
			{

				$item['title1'] = nv_clean60( $item['title'], $block_config['numcut'] );
				$item['last_post_date'] = ! empty( $item['last_post_date'] ) ? nv_date( 'd-m-Y, h:i:s A', $item['last_post_date'] ) : "";
				if(  $item['last_post_page'] > 1 ) $item['last_link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module . "&amp;" . NV_OP_VARIABLE . "=" . strtolower( change_alias( $item['title'] ) ) . "/" . $item['thread_id'] . "/page-" . $item['last_post_page'], true );
				else  $item['last_link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module . "&amp;" . NV_OP_VARIABLE . "=" . strtolower( change_alias( $item['title'] ) ) . "/" . $item['thread_id'], true );
				$xtpl->assign( 'loop', $item );
				$xtpl->parse( 'main.loop' );
				++$i;

			}

			$xtpl->parse( 'main' );
			return $xtpl->text( 'main' );
		}
	}
}
if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		$content = nv_block_lastest_post( $block_config );
	}
}