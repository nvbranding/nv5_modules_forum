<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES., JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES ., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Dec 3, 2010  11:12:21 AM 
 */

if ( ! defined( 'NV_SYSTEM' ) ) die( 'Stop!!!' );

$registry = array(
	'mod_data' => $module_data,
	'mod_name' => $module_name,
	'mod_file' => $module_file,
	'mod_lang' => $lang_module,
	'lang_data' => NV_LANG_DATA,
);

define( 'NV_FORUM_GLOBALTABLE', $db_config['prefix'] . '_' . $module_data );
define( 'ACTION_METHOD', $nv_Request->get_string( 'action', 'get,post', '' ) );
define( 'LOAD_PATH', NV_ROOTDIR . '/modules/' . $module_file . '/library' );
 
require_once NV_ROOTDIR . '/modules/' . $module_file . '/Autoloader.php';
 
$forum_global = new forum_global( $registry );
 
$my_head = "<script type=\"text/javascript\">
//<![CDATA[
template='" . $module_info['template'] . "',site_theme='" . $global_config['site_theme'] . "';
//]]>
</script>";

$userid = ( isset( $user_info['userid'] ) ) ? $user_info['userid'] : 0;
  

// $forum = new Forum();
// $thread_obj = new Thread();
// $cat_obj = new Cat();

// $forum->callJqueryPlugin( 'bootstrap' );
// $nv_cat = $cat_obj->cat;
// $arr_config = $forum->setting;


$per_page = $arr_config['paper_page'];
$per_thread = $arr_config['paper_thread'];
$catid = 0;
$parentid = 0;
$thread_id = 0;
$page = 1;

unset( $matches );

$check_op = isset( $array_op[0] ) ? $array_op[0] : "";

$fil_op = array(
	'members',
	'account',
	'post',
	'openid',
	'find-new',
	'unwatch',
	'rss' );

if ( isset( $array_op[0] ) and ! in_array( $check_op, $fil_op ) and preg_match( "/^([a-zA-Z0-9\-\_]+)\-([\d]+)$/", $array_op[0], $matches ) and isset( $nv_cat[$matches[2]] ) and $nv_cat[$matches[2]]['alias'] == $matches[0] || ( isset( $array_op[1] ) and substr( $array_op[1], 0, 5 ) == "page-" ) )
{
	$op = 'sub';

	if ( isset( $array_op[1] ) and substr( $array_op[1], 0, 5 ) == "page-" )
	{
		$page = intval( substr( $array_op[1], 5 ) );
	}

	$catid = $matches[2];
	$parentid = $catid;

}
elseif ( isset( $array_op[0] ) and ! in_array( $check_op, $fil_op ) and isset( $array_op[1] ) || ( isset( $array_op[2] ) and substr( $array_op[2], 0, 5 ) == "page-" ) )
{

	$op = 'view';
	if ( isset( $array_op[2] ) and substr( $array_op[2], 0, 5 ) == "page-" )
	{
		$page = intval( substr( $array_op[2], 5 ) );
	}

	$thread_id = intval( $array_op[1] );
	$thread_alias = $array_op[0];

}else
{
	if ( isset( $array_op[0] ) and substr( $array_op[0], 0, 5 ) == "page-" )
	{
		$page = intval( substr( $array_op[0], 5 ) );
	}
}

while ( $parentid > 0 )
{
	$array_cat_i = $nv_cat[$parentid];
	$array_mod_title[] = array(
		'catid' => $parentid,
		'title' => $array_cat_i['title'],
		'link' => $array_cat_i['link'] );
	$parentid = $array_cat_i['parentid'];
}

sort( $array_mod_title, SORT_NUMERIC );

function convert_time( $time )
{
	$ago = NV_CURRENTTIME - $time;
	if ( $ago < 60 )
	{
		return 'Vài giây trước';
	}
	elseif ( $ago >= 60 and $ago < 3600 )
	{
		return floor( $ago / 60 ) . ' phút trước';
	}
	elseif ( date( 'd', $time ) == date( 'd', NV_CURRENTTIME ) )
	{
		return 'Hôm nay lúc ' . date( 'H:i', $time );
	}
	elseif ( ( date( 'd', NV_CURRENTTIME ) - date( 'd', $time ) ) == 1 )
	{
		return 'Hôm qua lúc ' . date( 'H:i', $time );
	}
	else
	{
		return date( 'd-m-Y', $time ) . ' lúc ' . nv_date( 'i:s', $time );
	}

}


function forums_alias_page( $title, $base_url, $num_items, $per_page, $on_page, $add_prevnext_text = true )
{
	global $lang_global;

	$total_pages = ceil( $num_items / $per_page );
	
	if( $total_pages < 2 ) return '';

	$title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'];
	$page_string = '<span>...</span>';
	if( $total_pages > 10 )
	{
		$init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;
	
		// for( $i = 2; $i <= $init_page_max; ++$i )
		// {
			// $page_string .= ( $i == $on_page ) ? "" : "<a title=\"" . $title . " " . $i . "\" href=\"" . $base_url . "/page-" . $i . "\">" . $i . "</a>";
			
			// if( $i < $init_page_max ) $page_string .= " ";
		// }
	
		if( $total_pages > 3 )
		{
			if( $on_page > 1 && $on_page < $total_pages )
			{
				$page_string .= ( $on_page > 5 ) ? " ... " : " ";
				$init_page_min = ( $on_page > 4 ) ? $on_page : 5;
				$init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;
			
				for( $i = $init_page_min - 1; $i < $init_page_max + 2; ++$i )
				{
					$page_string .= ( $i == $on_page ) ? "" : "<a title=\"" . $title . " " . $i . "\" href=\"" . $base_url . "/page-" . $i . "\">" . $i . "</a>";
				
					if( $i < $init_page_max + 1 )
					{
						$page_string .= " ";
					}
				}
			
				$page_string .= ( $on_page < $total_pages - 4 ) ? " ... " : " ";
			}
			else
			{
				$page_string .= " ... ";
			}

			for( $i = $total_pages - 2; $i < $total_pages + 1; ++$i )
			{
				$page_string .= ( $i == $on_page ) ? "<strong>" . $i . "</strong>" : "<a title=\"" . $title . " " . $i . "\" href=\"" . $base_url . "/page-" . $i . "\">" . $i . "</a>";
				
				if( $i < $total_pages )
				{
					$page_string .= " ";
				}
			}
		}
	}
	else
	{
		$tem = $total_pages - 2;
		for( $i = $tem; $i < $total_pages + 1; ++$i )
		{
			if ($i > 0) 
			$page_string .= ( $i == $on_page ) ? "" : "<a title=\"" . $title . " " . $i . "\" href=\"" . $base_url . "/page-" . $i . "\">" . $i . "</a>";
		
			if( $i < $total_pages )
			{
				$page_string .= " ";
			}
		}
	}

	if( $add_prevnext_text )
	{
		if( $on_page > 1 )
		{
			$page_string = "&nbsp;&nbsp;<a title=\"" . $title . " " . ( $on_page - 1 ) . "\" href=\"" . $base_url . "/page-" . ( $on_page - 1 ) . "\">" . $lang_global['pageprev'] . "</a>&nbsp;&nbsp;" . $page_string;
		}
	
		if( $on_page < $total_pages )
		{
			$page_string .= "&nbsp;&nbsp;|  <a title=\"" . $title . " " . ( $on_page + 1 ) . "\"  href=\"" . $base_url . "/page-" . ( $on_page + 1 ) . "\">" . $lang_global['pagenext'] . "</a>";
		}
	}
	
	return $page_string;
}

/**
 * forum_online()
 * 
 * @return void
 */
function forum_online ( $catid, $thread_id, $memberid )
{
    global $db, $client_info, $module_name, $user_info, $module_data;
    $userid = 0;
    $username = "guest";
    if ( isset( $user_info['userid'] ) and $user_info['userid'] > 0 )
    {
        $userid = $user_info['userid'];
        $username = $user_info['username'];
    }
    elseif ( $client_info['is_bot'] )
    {
        $username = 'bot:' . $client_info['bot_info']['name'];
    }
    $query = "REPLACE INTO " . NV_PREFIXLANG . "_" . $module_data . "_online VALUES (
    " . $db->quote( $client_info['session_id'] ) . ", 
    " . $thread_id . ", 
    " . $catid . ", 
    " . $memberid . ", 
    " . $userid . ", 
    " . $db->quote( $username ) . ", 
    " . NV_CURRENTTIME . "
    )";
    $db->query( $query );
	
}


function show_json( $info, $error, $succe ){

	if ( empty( $error ) )
	{
		$info['data'] = array( 'message' => 'success', 'item' => $succe );
	}
	else
	{
		$info['data'] = array( 'message' => 'unsuccess', 'item' => $error );
	}
	return json_encode( $info );
}


define( 'NV_IS_MOD_FORUM', true );