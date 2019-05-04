<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nv_block_counter' ) )
{
	function nv_block_counter( $block_config )
	{
		global $global_config, $module_name, $db, $thread_id, $catid, $lang_global, $site_mods, $module_info, $forum;
		
		$module = $block_config['module'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];

		if( file_exists( NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $mod_file . "/module.counter_user.tpl" ) )
		{
			$block_theme = $module_info['template'];
		}
		else
		{
			$block_theme = "default";
		}	
		$xtpl = new XTemplate( "module.counter_user.tpl", NV_ROOTDIR . "/themes/" . $block_theme . "/modules/" . $mod_file );

		$xtpl->assign( 'LANG', $lang_global );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'TEMPLATE', $block_theme );
		$xtpl->assign( 'current_time', nv_date( "H:i T l, d/m/Y", NV_CURRENTTIME ) );
		$xtpl->assign( 'IMG_PATH', NV_BASE_SITEURL . "themes/" . $block_theme . "/" );
		
		
		$forum = new Forum();
		// max visit
		/* $max_visitor = array('last_update'=>'','visit'=>0 );
		$cache_file_max_visitor = NV_LANG_DATA . "_" . md5( $module  ) . "_max_visitor_" . NV_CACHE_PREFIX . ".cache";
		if ( file_exists ( NV_ROOTDIR . "/" . NV_CACHEDIR . "/" . $cache_file_max_visitor ) )
		{
			if ( ( NV_CURRENTTIME - filemtime ( NV_ROOTDIR . "/" . NV_CACHEDIR . "/" . $cache_file_max_visitor ) ) > 3600 )
			{
				nv_deletefile( NV_ROOTDIR . "/" . NV_CACHEDIR . "/" . $cache_file_max_visitor, true );
			}
		}
		
		
		if ( ( $cache_max_visitor = $nv_Cache->getItem( $cache_file_max_visitor ) ) != false )
		{
			$max_visitor = unserialize( $cache_max_visitor );
			
		}
		else
		{
			$sql = "SELECT last_update , c_count FROM  " . NV_COUNTER_TABLE . " WHERE c_type = 'hour' ORDER BY c_count DESC, last_update DESC LIMIT 1";
			$query = $db->query( $sql );
			list( $last_update, $visit ) = $query->fetch( 3 );
			$max_visitor['last_update'] = $last_update;
			$max_visitor['visit'] = $visit;
			$cache_max_visitor = serialize( $max_visitor );
			nv_set_cache( $cache_file_max_visitor, $cache_max_visitor );
		}
		$xtpl->assign( 'last_update',  nv_date( 'd-m-Y, h:i:s A', $max_visitor['last_update']) );
		$xtpl->assign( 'visit', $max_visitor['visit'] ); */

		// het max visit
		
		// thong ke online
		unset($sql);
		$_user_array = array();
			$admin_forum = $forum->admin_forum();
			$sql = "SELECT uid, full_name FROM " . NV_SESSIONS_GLOBALTABLE . " WHERE onl_time >= " . ( NV_CURRENTTIME - NV_ONLINE_UPD_TIME );
			$query = $db->query( $sql );
			while( $row = $query->fetch() )
			{
				$lev = ( isset( $admin_forum[$row['uid']]['lev'] ) ) ? $admin_forum[$row['uid']]['lev'] : 4 ;
				if($lev == 1)
				{
					$row['lev'] = 'style="color:#CC0000;font-weight: bold;"';
				}elseif($lev == 2)
				{
					$row['lev'] = 'style="color:#006600;font-weight: bold;"';
				}elseif($lev == 3)
				{
					$row['lev'] = 'style="color:#FF6600;font-weight: bold;"';
				}elseif($lev == 4)
				{
					$row['lev'] = 'style="color:#105289"';
				}
				$_user_array[] = $row;
			}

		$count_online = $users = $bots = $guests = 0;
		if( !empty( $_user_array ) )
		{	
			$n=1;
			$num_member = count( $_user_array );
			foreach($_user_array as $row)
			{
				++$count_online;

				if( $row['uid'] != 0 )
				{
					++$users;
					
					( ( $n < $num_member ) ? $xtpl->parse( 'main.member.loop.comma' ) : '' );
					$xtpl->assign( 'loop', $row );
					$xtpl->parse( 'main.member.loop' );
					++$n;
					$xtpl->parse( 'main.member' );
					
				}
				else
				{
					if( preg_match( "/^bot\:/", $row['full_name'] ) )
					{
						++$bots;
					}
					else
					{
						++$guests;
					}
				}
			}
		}
		
		$xtpl->assign( 'COUNT_ONLINE', $count_online );
		$xtpl->assign( 'COUNT_USERS', $users );
		$xtpl->assign( 'COUNT_GUESTS', $guests );
		if(!empty($bots))
		{
			$xtpl->assign( 'COUNT_BOTS', $bots );
			$xtpl->parse( 'main.bots' );
		}

		// het thong ke online
		
		// thong ke online theo chuyen muc hoac chu de
		if( $thread_id > 0 )
		{
	
			$sql2="SELECT uid, full_name, COUNT(*) as online FROM " . NV_PREFIXLANG . "_" . $mod_data . "_online WHERE thread_id =".$thread_id." GROUP BY uid,full_name ORDER BY onl_time DESC LIMIT 0 , 15";
			$result2 = $db->query( $sql2 );
			$_users = array();
			$_guest = 0;
			$online = 0;
			while ( $loop = $result2->fetch() )
			{
				
				
				if( $loop['uid'] == 0 )
				++$_guest;
				else $_users[] = $loop;
				
				$online = $online + $loop['online'];
			}
			
			
			foreach( $_users as $user )
			{
				$user['user_page'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module . "&amp;" . NV_OP_VARIABLE . "=members/".$user['full_name']."-".$user['uid'];
				$xtpl->assign( 'USER', $user );
				$xtpl->parse( 'main.thread.user_loop' );				
			}
			$xtpl->assign( 'ONLINE', $online );
			$xtpl->assign( 'GUEST', $_guest );
			$xtpl->assign( 'USERS', count($_users) );
			$xtpl->parse( 'main.thread' );
		}
		
		
		// sinh nhat thanh vien
		unset($sql);
		$array_user = array();
		$cache_file_birthday = NV_LANG_DATA . "_" . md5( $module  ) . "_birthday_" . NV_CACHE_PREFIX . ".cache";
		if ( file_exists ( NV_ROOTDIR . "/" . NV_CACHEDIR . "/" . $cache_file_birthday ) )
		{
			$old_d = date('m', filemtime ( NV_ROOTDIR . "/" . NV_CACHEDIR . "/" . $cache_file_birthday ) );
			$new_d = date('m', NV_CURRENTTIME);
			if ( $new_d != $old_d )
			{
				nv_deletefile( NV_ROOTDIR . "/" . NV_CACHEDIR . "/" . $cache_file_birthday, true );
			}
		}
		
		if ( ( $cache_birthday = $nv_Cache->getItem( $cache_file_birthday ) ) != false )
		{
			$array_user = unserialize( $cache_birthday );
		}
		else
		{
			$admin_forum = $forum->admin_forum();
			$sql = "SELECT userid, username, birthday FROM " .NV_USERS_GLOBALTABLE." WHERE active=1 AND DATE_FORMAT(FROM_UNIXTIME(birthday),'%m') = DATE_FORMAT(NOW(),'%m')";
			$query = $db->query( $sql );
			while ( $row = $query->fetch() )
			{   
				$lev = ( isset( $admin_forum[$row['userid']]['lev'] ) ) ? $admin_forum[$row['userid']]['lev'] : 4 ;
				if($lev == 1)
				{
					$row['lev'] = 'style="color:#CC0000;font-weight: bold;"';
				}elseif($lev == 2)
				{
					$row['lev'] = 'style="color:#006600;font-weight: bold;"';
				}elseif($lev == 3)
				{
					$row['lev'] = 'style="color:#FF6600;font-weight: bold;"';
				}elseif($lev == 4)
				{
					$row['lev'] = 'style="color:#105289"';
				}
				$array_user[] = $row;
			}  
			$cache_birthday = serialize( $array_user );
			nv_set_cache( $cache_file_birthday, $cache_birthday );
		}
		
		$n = 1;
		if(!empty( $array_user ) )
		{
			$num_row = count( $array_user );
			foreach($array_user as $user)
			{
				
				$today = date('d-m', NV_CURRENTTIME);
				$birthday = date('d-m', $user['birthday']);
				if( $birthday == $today)
				{
					( ( $n < $num_row ) ? $xtpl->parse( 'main.birthday.loop.comma' ) : '' );
					$xtpl->assign( 'loop', $user );
					$xtpl->parse( 'main.birthday.loop' );
					++$n;
				}
			}
			if( $n > 1 )
			{
				$xtpl->parse( 'main.birthday' );
			}
		}
		// het sinh nhat thanh vien
		
		$xtpl->parse( 'main' );
		$content = $xtpl->text( 'main' );
		
		return $content;
		$forum->clear();
		unset ($forum);
	}
}

if( defined( 'NV_SYSTEM' ) )
{
	global $global_config, $site_mods, $module_name;
	$module = $block_config['module'];

	if( $global_config['online_upd'] )
	{
		$content = nv_block_counter( $block_config );
	}
}