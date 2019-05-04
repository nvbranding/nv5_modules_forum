<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'GlobalBlockStatistics' ) )
{
	function nv_block_config_statistics( $module, $data_block, $lang_block )
	{
        global $site_mods;
        $html = '<tr>';
        $html .= '	<td>Cập nhật lại sau x phút </td>';
        $html .= '	<td><input type="text" name="config_timereset" class="form-control w100" size="5" value="' . $data_block['timereset'] . '"/></td>';
        $html .= '</tr>';
        return $html;
    }
 
    function nv_block_config_statistics_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['timereset'] = $nv_Request->get_int('config_timereset', 'post', 0);
        return $return;
    }
	
	
	function GlobalBlockStatistics( $block_config )
	{
		global $module_info, $nv_Cache, $module_name, $site_mods, $forum_node, $db_slave, $client_info;
		$module = $block_config['module'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];
		
		$timereset = ( $block_config['timereset'] ) ? $block_config['timereset'] : 5;
		
		$timereset = $timereset * 60;
		
		$cache_file = NV_LANG_DATA . '_statistics_' . NV_CACHE_PREFIX . '.cache';
		
		$path_file = NV_ROOTDIR . '/' . NV_CACHEDIR . '/' . $module . '/' . $cache_file;
 
		if( file_exists( $path_file ) && ( NV_CURRENTTIME - $timereset ) > filemtime( $path_file ) )
		{
			nv_deletefile( $path_file );
		}
 
		
		// if( ( $cache = $nv_Cache->getItem( $module, $cache_file ) ) != false )
		// {
			// $content = unserialize( $cache );
		// }
		// else
		// {

			if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $mod_file . '/ModuleBlockTopx.tpl' ) )
			{
				$block_theme = $module_info['template'];
			}
			else
			{
				$block_theme = 'default';
			}
			$xtpl = new XTemplate( "GlobalBlockStatistics.tpl", NV_ROOTDIR . "/themes/" . $block_theme . "/modules/" . $mod_file );
			$xtpl->assign( 'IP', $client_info['ip'] );

			$total_post = $db_slave->query( 'SELECT COUNT(*) FROM ' . NV_FORUM_GLOBALTABLE . '_post WHERE message_state=\'visible\'' )->fetchColumn();
			$xtpl->assign( 'COUNT_POST', number_format( $total_post ) );

			$total_thread = $db_slave->query( 'SELECT COUNT(*) FROM ' . NV_FORUM_GLOBALTABLE . '_thread WHERE discussion_state=\'visible\'' )->fetchColumn();
			$xtpl->assign( 'COUNT_THREAD', number_format( $total_thread ) );

			$total_category = count( $forum_node );
			$xtpl->assign( 'COUNT_CATEGORY', number_format( $total_category ) );

			$total_users = $db_slave->query( 'SELECT COUNT(*) FROM ' . NV_USERS_GLOBALTABLE . ' WHERE active=1' )->fetchColumn();
			$xtpl->assign( 'COUNT_USERS', number_format( $total_users ) );

			$current_month = date( 'dm', NV_CURRENTTIME );
			$usersBirthday = array();
			$sql = "SELECT username FROM " . NV_USERS_GLOBALTABLE . " WHERE active=1 AND DATE_FORMAT(FROM_UNIXTIME(birthday),'%d%m') = " . $current_month;
			
			$result = $db_slave->query( $sql );
			while( list( $username ) = $result->fetch( 3 ) )
			{
				$usersBirthday[] = $username;
			}
			$result->closeCursor();

			if( !empty( $usersBirthday ) )
			{
				$xtpl->assign( 'USERNAME_BIRTHDAY', implode( ', ', $usersBirthday ) );
				$xtpl->parse( 'main.birthday' );
			}

			$newUsers = array();
			$sql = "SELECT username FROM " . NV_USERS_GLOBALTABLE . " WHERE active=1 ORDER BY regdate DESC LIMIT 0,10";
			$result = $db_slave->query( $sql );
			while( list( $new_username ) = $result->fetch( 3 ) )
			{
				$newUsers[] = $new_username;
			}
			$result->closeCursor();

			if( $newUsers )
			{
				$xtpl->assign( 'NEW_USERNAME', implode( ', ', $newUsers ) );
				$xtpl->parse( 'main.new_users' );
			}

			$mostUsers = array();
			$sql = "SELECT COUNT(*) total, p.userid, u.username FROM " . NV_FORUM_GLOBALTABLE . "_thread_user_post p LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON (p.userid = u.userid) GROUP BY p.userid ORDER BY total DESC LIMIT 0,10";
			$result = $db_slave->query( $sql );
			while( list( $_total, $_userid, $_username ) = $result->fetch( 3 ) )
			{
				$mostUsers[] = $_username;
			}
			$result->closeCursor();

			if( $mostUsers )
			{
				$xtpl->assign( 'MOST_USERNAME', implode( ', ', $mostUsers ) );
				$xtpl->parse( 'main.most_users' );
			}

			$sql = "SELECT c_type, c_count FROM " . NV_COUNTER_GLOBALTABLE . " WHERE (c_type='day' AND c_val='" . date( 'd', NV_CURRENTTIME ) . "') OR (c_type='month' AND c_val='" . date( 'M', NV_CURRENTTIME ) . "') OR (c_type='total' AND c_val='hits')";
			$result = $db_slave->query( $sql );
			while( list( $c_type, $c_count ) = $result->fetch( 3 ) )
			{
				if( $c_type == 'day' )
				{
					$xtpl->assign( 'COUNT_DAY', number_format( $c_count ) );
				}
				elseif( $c_type == 'month' )
				{
					$xtpl->assign( 'COUNT_MONTH', number_format( $c_count ) );
				}
				elseif( $c_type == 'total' )
				{
					$xtpl->assign( 'COUNT_ALL', number_format( $c_count ) );
				}
			}
			$result->closeCursor();

			$sql = 'SELECT userid, username FROM ' . NV_SESSIONS_GLOBALTABLE . ' WHERE onl_time >= ' . ( NV_CURRENTTIME - NV_ONLINE_UPD_TIME );
			$result = $db_slave->query( $sql );
			$count_online = $users = $bots = $guests = 0;
			$listUsersOnline = array();
			while( $row = $result->fetch() )
			{
				++$count_online;

				if( $row['userid'] )
				{
					$listUsersOnline[] = $row['username'];

					++$users;
				}
				elseif( preg_match( '/^bot\:/', $row['username'] ) )
				{
					++$bots;
				}
				else
				{
					++$guests;
				}
			}
			$result->closeCursor();
			if( $listUsersOnline )
			{
				$xtpl->assign( 'USERS_ONLINE', implode( ', ', $listUsersOnline ) );
				$xtpl->parse( 'main.users_online' );
			}

			$xtpl->assign( 'COUNT_ONLINE_USERS', number_format( $users ) );
			$xtpl->assign( 'COUNT_ONLINE', number_format( $count_online ) );
			$xtpl->parse( 'main' );

			$content = $xtpl->text( 'main' );
			// $cache = serialize( $content );
			// $nv_Cache->setItem( $module, $cache_file, $cache );
		// }

		return $content;
	}
}
if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods, $module_name;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		$content = GlobalBlockStatistics( $block_config );
	}
}
