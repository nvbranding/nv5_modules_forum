<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweb.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 22:18
 */

if ( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];

unset( $matches );
if ( isset( $array_op[0] ) and preg_match( "/^([a-zA-Z0-9\-\_]+)\-([\d]+)$/", $array_op[0], $matches ) and isset( $nv_cat[$matches[2]] ) and $nv_cat[$matches[2]]['alias'] == $matches[0] || ( isset( $array_op[1] ) and substr( $array_op[1], 0, 6 ) == "trang-" ) )
{
	$catid = (int)$matches[2];
	
	$viewcat = $nv_cat[$catid]['viewcat'];
	
	$page_title = ( ! empty( $nv_cat[$catid]['titlesite'] ) ) ? $nv_cat[$catid]['titlesite'] : $nv_cat[$catid]['title'];
	$key_words = $nv_cat[$catid]['keywords'];
	$description = $nv_cat[$catid]['description'];

	if ( $nv_cat[$catid]['parentid'] == 0 || ! empty( $nv_cat[$catid]['subcatid'] ) )
	{
		$xtpl = new XTemplate( "main.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'TEMPLATE', $module_info['template'] );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'CAT', $nv_cat[$catid] );

		$setsub = explode( ',', $nv_cat[$catid]['subcatid'] );
		if ( ! empty( $setsub ) )
		{
			foreach ( $setsub as $subcat )
			{
				if ( isset( $nv_cat[$subcat] ) )
				{
					$item = $nv_cat[$subcat];
					$item['thumbnail'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/' . $item['thumbnail'];
					if ( ! empty( $item['last_thread_id'] ) )
					{
						$item['last_thread_title1'] = nv_clean60( $item['last_thread_title'], 100 );
						$item['last_post_date'] = ! empty( $item['last_post_date'] ) ? nv_date( 'd-m-Y h:i:s A', $item['last_post_date'] ) : "";
						if ( ! empty( $item['last_post_page'] ) ) $item['last_link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $item['title'] ) . "/" . $item['last_thread_id'] . "/page-" . $item['last_post_page'], true );
						else  $item['last_link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $item['title'] ) . "/" . $item['last_thread_id'], true );

						$xtpl->assign( 'SUB', $item );
						$xtpl->parse( 'main.item.subcat.lastpost' );
					}
					$xtpl->assign( 'SUB', $item );
					$xtpl->parse( 'main.item.subcat' );
				}
			}
			$xtpl->parse( 'main.item' );
		}

	}
	else
	{

		$array_sticky = array();
		$list_threadid = array();

		$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_thread WHERE catid=" . $catid . " AND sticky=1 ORDER BY last_post_date DESC LIMIT 0, 10";
		$result = $db->query( $sql );
		while ( $item = $result->fetch() )
		{
			$array_sticky[] = $item;
			$list_threadid[] = $item['thread_id'];
		}

		if ( ! empty( $list_threadid ) ) $where = " AND thread_id NOT IN (" . implode( ',', $list_threadid ) . ") ";
		else  $where = "";

		unset( $sql, $result, $item );
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM " . NV_PREFIXLANG . "_" . $module_data . "_thread WHERE catid=" . $catid . " " . $where . " ORDER BY last_post_date DESC LIMIT " . ( $page - 1 ) * $per_thread . "," . $per_thread;
		$result = $db->query( $sql );
		$result_all = $db->query( "SELECT FOUND_ROWS()" );
		$all_page = $result_all->fetchColumn();
		$array_normal = array();
		while ( $item = $result->fetch() )
		{
			$array_normal[] = $item;
		}
		$base_url = $nv_cat[$catid]['link'];
		$generate_page = nv_alias_page( $page_title, $base_url, $all_page, $per_thread, $page );

		$xtpl = new XTemplate( "sub.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'TEMPLATE', $module_info['template'] );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'CAT', $nv_cat[$catid] );

		if ( ! empty( $array_sticky ) )
		{
			foreach ( $array_sticky as $loop )
			{

				$loop['link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $loop['title'] ) . "/" . $loop['thread_id'], true );

				$loop['title1'] = nv_clean60( $loop['title'], 100 );
				$loop['last_post_date'] = ! empty( $loop['last_post_date'] ) ? nv_date( 'd-m-Y, h:i:s A', $loop['last_post_date'] ) : "";

				if ( ! empty( $loop['last_post_page'] ) ) $loop['last_link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $loop['title'] ) . "/" . $loop['thread_id'] . "/page-" . $loop['last_post_page'], true );
				else  $loop['last_link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $loop['title'] ) . "/" . $loop['thread_id'], true );

				$xtpl->assign( 'loop', $loop );
				$xtpl->parse( 'main.sticky.loop_sticky' );
			}
			$xtpl->parse( 'main.sticky' );
		}

		unset( $loop );
		if ( ! empty( $array_normal ) )
		{
			foreach ( $array_normal as $loop )
			{
				$loop['link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $loop['title'] ) . "-" . $loop['thread_id'], true );
				$loop['title1'] = nv_clean60( $loop['title'], 100 );
				$loop['last_post_date'] = ! empty( $loop['last_post_date'] ) ? nv_date( 'd-m-Y, h:i:s A', $loop['last_post_date'] ) : "";

				if ( ! empty( $loop['last_post_page'] ) ) $loop['last_link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $loop['title'] ) . "/" . $loop['thread_id'] . "/page-" . $loop['last_post_page'], true );
				else  $loop['last_link'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $loop['title'] ) . "/" . $loop['thread_id'], true );

				$xtpl->assign( 'loop', $loop );
				$xtpl->parse( 'main.normal.loop_normal' );
			}
			$xtpl->parse( 'main.normal' );
			unset( $array_normal );
		}

	}

	if ( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	$forum->clear();
	$thread_obj->clear();
	$cat_obj->clear();
	unset( $item, $block, $nv_cat, $array_sticky, $list_threadid, $setsub, $forum, $thread_obj, $cat_obj, $arr_config, $array_cat_admin, $check_admin, $xtpl );
	//echo nv_convertfromBytes(memory_get_usage() - $start_memory);

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}
else
{
	//die('sai');
	Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
	exit();
}