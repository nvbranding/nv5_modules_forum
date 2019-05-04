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

	$user_obj = new Users();
	$members_obj = new Members();
	$ip_obj = new Ipcountry();
						
	$getop = isset( $array_op[1] ) ? $array_op[1] : "";
	$getsub_op = isset( $array_op[2] ) ? $array_op[2] : "";

	$array_page = explode( "-", $getop );
	$profile_user_id = intval( end( $array_page ) );
	$number = strlen( $profile_user_id ) + 1;
	$profile_username = substr( $array_op[1], 0, -$number );

	$error = $succe = $info = array();
	
	if ( $nv_Request->isset_request( 'action', 'post' ) )
	{
		
		$action = $nv_Request->get_string( 'action', 'post', '' );
		$checkss = $nv_Request->get_title( 'checkss', 'post', '' );
		if ( $action == "info" )
		{
			$thread_id = $nv_Request->get_int( 'threadid', 'post', 0 );
			$userid = $nv_Request->get_int( 'userid', 'post', 0 );
			$checkss = $nv_Request->get_title( 'checkss', 'post', '' );
			$_checkss = md5( session_id() . $thread_id . $global_config['sitekey'] );
			if ( $checkss == $_checkss and $thread_id > 0 and $userid > 0 )
			{

				$sql2 = "SELECT COUNT(a.user_id) post,  b.userid, b.username, b.photo, b.regdate, b.last_login
				FROM " . NV_PREFIXLANG . "_" . $module_data . "_post a
				CROSS JOIN " . NV_USERS_GLOBALTABLE . " b 
				ON b.userid = a.user_id
				WHERE b.userid = " . $userid . " ";

				$result2 = $db->query( $sql2 );
				$data = $result2->fetch();
				$data['user_page'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=members/" . $data['username'] . "-" . $data['userid'], true );

				$content_user = $db->query( "SELECT COUNT(content_user_id) content_user
				FROM " . NV_PREFIXLANG . "_" . $module_data . "_liked_content 
				WHERE content_user_id = " . $userid . "" )->fetch();

				$data = array_merge( $data, $content_user );
				unset( $content_user );

				if ( ! empty( $data['photo'] ) )
				{
					$array_img = array();
					$array_img = explode( "[f]", $data['photo'] );

					if ( $array_img[0] != "" and file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/' . $array_img[0] ) )
					{
						$data['photo'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/users/' . $array_img[0];

					}
					else
					{
						$data['photo'] = NV_BASE_SITEURL . "themes/" . $global_config['module_theme'] . "/images/no_avatar.jpg";
					}
				}
				else
				{
					$data['photo'] = NV_BASE_SITEURL . "themes/" . $global_config['module_theme'] . "/images/no_avatar.jpg";
				}

				$data['regdate'] = date( 'd/m/Y', $data['regdate'] );
				$data['last_time'] = date( 'd/m/Y', $data['last_login'] );
				$data['last_hour'] = date( 'i:s', $data['last_login'] );
				$data['last_login'] = convert_time( $data['last_login'] );

				//$data['last_login'] = date( 'd/m/Y', $data['last_login'] );
				$xtpl = new XTemplate( "load_content.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
				$xtpl->assign( 'LANG', $lang_module );
				$xtpl->assign( 'MODULE_FILE', $module_file );
				$xtpl->assign( 'TEMPLATE', $module_info['template'] );
				$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
				$xtpl->assign( 'DATA', $data );

				$xtpl->parse( 'info_user' );
				$contents = $xtpl->text( 'info_user' );

				$succe['message'] = $contents;
			}
			else
			{
				$error[] = "Có lỗi xảy ra";
			}

		}
		elseif ( $action == "insert_profile" )
		{
			
			$message = $nv_Request->get_string( 'message', 'post', '' );
			$profile_user_id = $nv_Request->get_int( 'profile_user_id', 'post', 0 );
			$newcheckss = md5( $user_info['userid'] .session_id() . $profile_user_id. $global_config['sitekey'] );
			if( $checkss == $newcheckss )
			{
				$profile_post_id = $members_obj->insert_profile_post( array(
					'profile_user_id' => $profile_user_id,
					'user_id' => $user_info['userid'],
					'username' => $user_info['username'],
					'post_date' => NV_CURRENTTIME,
					'message' => $message,
					'ip_id' => 0,
					'message_state' => 'visible',
					'attach_count' => 0,
					'likes' => 0,
					'like_users' => '',
					'comment_count' => 0,
					'first_comment_date' => 0,
					'last_comment_date' => 0,
					'latest_comment_ids' => 0,
					'warning_id' => 0,
					'warning_message' => '' ) );

				if ( $profile_post_id > 0 )
				{
					// profile post log ip
					$ip_id = $ip_obj->insert_ip( array(
						'ip' => $client_info['ip'],
						'user_id' => $user_info['userid'],
						'content_id' => $profile_post_id,
						'content_type' => 'profile_post_comment',
						'action' => 'insert',
						'log_date' => NV_CURRENTTIME ) );

					$members_obj->update_profile_post_ip( array( 
						'profile_post_id' => $profile_post_id, 
						'ip_id' => $ip_id ) );
					$contents = call_user_func( "insert_comment", array(
							'profile_post_id'=> $profile_post_id,
							'profile_user_id'=> $profile_user_id,
							'user_id'=> $user_info['userid'],
							'username'=> $user_info['username'],
							'photo'=> $user_info['photo'],
							'message'=> $message,
							'post_date'=> NV_CURRENTTIME,
							'last_hour'=> date( 'i:s', NV_CURRENTTIME ),
							'last_time'=> convert_time( NV_CURRENTTIME )
						) );	
					$succe['message'] = $contents;
				}
			}else $error[] = 'Lỗi bảo mật không thể đăng status vào thời điểm này';
		}
		elseif ( $action == "insert_comment" )
		{	
			$profile_user_id = $nv_Request->get_int( 'profile_user_id', 'post', 0 );

			$newcheckss = md5( $user_info['userid'] .session_id() . $profile_user_id. $global_config['sitekey'] );
			if( $checkss == $newcheckss )
			{
				// get info from post
				$message = $nv_Request->get_string( 'message', 'post', '' );
				$profile_post_id = $nv_Request->get_int( 'profile_post_id', 'post', 0 );
				
				// profile post log ip
				$ip_id = $ip_obj->insert_ip( array(
					'ip' => $client_info['ip'],
					'user_id' => $user_info['userid'],
					'content_id' => $profile_post_id,
					'content_type' => 'profile_post_comment',
					'action' => 'insert',
					'log_date' => NV_CURRENTTIME ) );
				
				// insert comement profile level 2
				$profile_post_comment_id = $members_obj->insert_comment( array(
					'profile_post_id' => $profile_post_id,
					'profile_user_id' => $profile_user_id,
					'user_id' => $user_info['userid'],
					'username' => $user_info['username'],
					'comment_date' => NV_CURRENTTIME,
					'message' => $message,
					'ip_id' => $ip_id ) );

				if ( $profile_post_comment_id > 0 )
				{
					
					$array_comment = $members_obj->profile_comment_count( $profile_post_id );
					
					$comment_count = count( $array_comment );
					$first_comment_date = empty( $comment_count ) ? NV_CURRENTTIME : 0;
					$last_date = end( $array_comment );
					$profile_comment_id  = array();
					foreach($array_comment as $post_comment)
					{
						$profile_comment_id[] = $post_comment['profile_post_comment_id'];
					}
					$latest_comment_ids = implode( ',', $profile_comment_id );
					
					// update profile comment count
					$members_obj->update_profile_comment_count( array(
						'profile_post_id' => $profile_post_id,
						'comment_count' => 'comment_count + 1',
						'first_comment_date' => $first_comment_date,
						'last_comment_date' => $last_date['comment_date'],
						'latest_comment_ids' => $latest_comment_ids ) );
					
					
					$contents = call_user_func( "insert_sub_comment", array( 0 => array(
						'profile_post_id' => $profile_post_id,
						'profile_post_comment_id' => $profile_post_comment_id,
						'user_id' => $user_info['userid'],
						'username' => $user_info['username'],
						'photo' => $user_info['photo'],
						'message' => $message,
						'comment_date' => NV_CURRENTTIME ) ) );	
					$succe['message'] = $contents;
				}
			}else $error[] = 'Lỗi bảo mật không thể đăng bình luận vào thời điểm này';
		
		}
		elseif ( $action == "DeleteProfilePost" )
		{
			$profile_user_id = $nv_Request->get_int( 'profile_user_id', 'post', 0 );

			$newcheckss = md5( $user_info['userid'] .session_id() . $profile_user_id. $global_config['sitekey'] );
			if( $checkss == $newcheckss )
			{
				$hard_delete = $nv_Request->get_int( 'hard_delete', 'post', 0 );
				$profile_post_id = $nv_Request->get_int( 'profile_post_id', 'post', 0 );
				$message_state = $hard_delete ? 'permanently': 'deleted';
				$members_obj->delete_profile_post( array(
					'profile_post_id' => $profile_post_id,
					'message_state' => $message_state
				) );
			
				if( $message_state == 'permanently' )
				{
					$members_obj->delete_profile_comment( $profile_post_id );
				}
			}else $error[] = 'Lỗi bảo mật không thể xóa bài đăng vào thời điểm này';
		
		}
		elseif ( $action == "DeleteComment" )
		{
			$profile_user_id = $nv_Request->get_int( 'profile_user_id', 'post', 0 );

			$newcheckss = md5( $user_info['userid'] .session_id() . $profile_user_id. $global_config['sitekey'] );
			if( $checkss == $newcheckss )
			{
				$profile_post_id = $nv_Request->get_int( 'profile_post_id', 'post', 0 );
				$profile_post_comment_id = $nv_Request->get_int( 'profile_post_comment_id', 'post', 0 );
			
			
				$members_obj->delete_post_comment( array(
					'profile_post_comment_id'=>$profile_post_comment_id,
					'user_id'=>$user_info['userid']
				) );
			
		
				$array_comment = $members_obj->profile_comment_count( $profile_post_id );
				$comment_count = count( $array_comment );
				$last_date = end( $array_comment );
				$first_comment_date = empty( $comment_count ) && !empty( $last_date ) ? NV_CURRENTTIME : 0;
				$profile_post_comment_id  = array();
				foreach($array_comment as $post_comment)
				{
					$profile_post_comment_id[] = $post_comment['profile_post_comment_id'];
				}
				
				$latest_comment_ids = implode( ',', $profile_post_comment_id );
					
					// update profile comment count
				$members_obj->update_profile_comment_count( array(
					'profile_post_id' => $profile_post_id,
					'comment_count' => 'IF( comment_count > 0 , comment_count - 1, comment_count )',
					'first_comment_date' => $first_comment_date,
					'last_comment_date' => empty( $last_date['last_comment_date'] ) ? 0 : $last_date['last_comment_date'],
					'latest_comment_ids' => $latest_comment_ids ) );
			}else $error[] = 'Lỗi bảo mật không thể đăng bình luận vào thời điểm này';
		
		}
		elseif ( $action == "loadPreComment" )
		{
			$profile_user_id = $nv_Request->get_int( 'profile_user_id', 'post', 0 );

			$newcheckss = md5( $user_info['userid'] .session_id() . $profile_user_id. $global_config['sitekey'] );
			if( $checkss == $newcheckss )
			{
				$profile_post_id = $nv_Request->get_int( 'profile_post_id', 'post', 0 );
				$PreTime = $nv_Request->get_int( 'PreTime', 'post', 0 );
				
				$sql2="SELECT A.*, B.photo FROM  " . NV_PREFIXLANG . "_" . $module_data . "_comment A
				LEFT JOIN " . NV_USERS_GLOBALTABLE . " B
				ON A.user_id = B.userid
				WHERE profile_post_id=".$profile_post_id." AND comment_date < ".$PreTime." 
				ORDER BY comment_date ASC";
				$_result = $db->query( $sql2 );
				$profile_array = array();
				while ( $rows = $_result->fetch() )
				{
					$profile_array[] = $rows;
				}

				$contents = call_user_func( "insert_sub_comment", $profile_array );	
				$succe['message'] = $contents;
			}else $error[] = 'Lỗi bảo mật không thể tải bình luận vào thời điểm này';
		
		}
		elseif ( $action == "RecentActivity" )
		{
			$profile_user_id = $nv_Request->get_int( 'profile_user_id', 'post', 0 );

			$newcheckss = md5( $user_info['userid'] .session_id() . $profile_user_id. $global_config['sitekey'] );
			if( $checkss == $newcheckss )
			{
				// thong tin status comment
				$sql="SELECT A.*, B.photo
				FROM  " . NV_PREFIXLANG . "_" . $module_data . "_profile_post A
				LEFT JOIN " . NV_USERS_GLOBALTABLE . " B
				ON A.profile_user_id = B.userid
				WHERE A.profile_user_id =".$profile_user_id."
				AND A.message_state ='visible'
				ORDER BY A.post_date DESC LIMIT 0, 20";
				$re_sult = $db->query( $sql );
				$profile_array = array();
				$key = 0;
				while ( $item = $re_sult->fetch() )
				{
					
					$profile_array[$key] = $item;
					if( !empty( $item['latest_comment_ids'] ) )
					{
						$sql2="SELECT A.*, B.photo FROM  " . NV_PREFIXLANG . "_" . $module_data . "_comment A
						LEFT JOIN " . NV_USERS_GLOBALTABLE . " B
						ON A.user_id = B.userid
						WHERE profile_post_comment_id IN (".$item['latest_comment_ids'].")
						ORDER BY comment_date ASC";
						$_result = $db->query( $sql2 );
						while ( $rows = $_result->fetch() )
						{
							$profile_array[$key]['comment'][] = $rows;
						}
					}	
					++$key;
				}
				$contents = call_user_func( "RecentActivity", $profile_array );	
				$succe['message'] = $contents;
			}else $error[] = 'Lỗi bảo mật không thể tải bình luận vào thời điểm này';
		
		}
		elseif ( $action == "EditProfilePost" )
		{
			$profile_user_id = $nv_Request->get_int( 'profile_user_id', 'post', 0 );

			$newcheckss = md5( $user_info['userid'] .session_id() . $profile_user_id. $global_config['sitekey'] );
			if( $checkss == $newcheckss )
			{
				$profile_post_id = $nv_Request->get_int( 'profile_post_id', 'post', 0 );
				
				$profile_post = $members_obj->get_profile_post( $profile_post_id );
				$contents = call_user_func( "EditProfilePost", array(
					'profile_post_id' => $profile_post_id,
					'profile_user_id' => $profile_user_id,
					'checkss' => $checkss,
					'message' => $profile_post['message'],
					'username' => $user_info['username'] ) );	
				$succe['message'] = $contents;
			}else $error[] = 'Lỗi bảo mật không thể tải bình luận vào thời điểm này';
		
		}
		elseif ( $action == "UpdateProfilePost" )
		{
			$profile_user_id = $nv_Request->get_int( 'profile_user_id', 'post', 0 );

			$newcheckss = md5( $user_info['userid'] .session_id() . $profile_user_id. $global_config['sitekey'] );
			if( $checkss == $newcheckss )
			{
				$profile_post_id = $nv_Request->get_int( 'profile_post_id', 'post', 0 );
				$message = $nv_Request->get_string( 'message', 'post', '' );
				$members_obj->update_profile_post( array(
					'profile_post_id' => $profile_post_id,
					'message' => $message 
				) );
				//$succe['message'] = $contents;
			}else $error[] = 'Lỗi bảo mật không thể tải bình luận vào thời điểm này';
		
		}

		if ( empty( $error ) )
		{
			$info['data'] = array( 'message' => 'success', 'item' => $succe );
		}
		else
		{
			$info['data'] = array( 'message' => 'unsuccess', 'item' => $error );
		}
		echo json_encode( $info );
		exit();
	}
if ( ! empty( $array_op ) )
{


	$position_online1 = array();
	$position_online2 = array();
	$position_online3 = array();

	$sql = "SELECT A.userid, A.username, A.photo, A.regdate, A.last_login, B.like_count, B.message_count
				FROM " . NV_USERS_GLOBALTABLE . " A
				LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_users_statistic B 
				ON A.userid = B.userid
				WHERE A.userid = " . $profile_user_id . " AND A.username='" . $profile_username . "' ";

	$result = $db->query( $sql );
	$array_data = $result->fetch();

	if ( ! empty( $array_data ) )
	{
		// ghi nhan thanh vien
		forum_online( 0, 0, $profile_user_id );

		$is_online = $db->query( "SELECT thread_id, catid, memberid, full_name, onl_time
			FROM " . NV_PREFIXLANG . "_" . $module_data . "_online
			WHERE uid = " . $profile_user_id . "" )->fetch();
		if ( ! empty( $is_online ) )
		{

			if ( $is_online['memberid'] > 0 )
			{
				$position_online1 = $db->query( "SELECT userid, username
				FROM " . NV_USERS_GLOBALTABLE . "
				WHERE userid = " . $is_online['memberid'] . "" )->fetch();
			}
			elseif ( $is_online['thread_id'] > 0 )
			{
				$position_online2 = $db->query( "SELECT thread_id, catid, title
				FROM " . NV_PREFIXLANG . "_" . $module_data . "_thread
				WHERE userid = " . $is_online['memberid'] . "" )->fetch();

			}
			elseif ( $is_online['catid'] > 0 )
			{
				$position_online3 = $db->query( "SELECT thread_id, catid, title
				FROM " . NV_PREFIXLANG . "_" . $module_data . "_thread
				WHERE userid = " . $is_online['memberid'] . "" )->fetch();

			}
			else
			{
				$is_online = array(
					'catid' => 0,
					'thread_id' => 0,
					'memberid' => 0,
					'full_name' => '',
					'onl_time' => 0 );
			}

		}

		// kiem tra tu cach thanh vien
		//if ( $forum->check_admin();
		//$profile_user_id
		$father_hood = "Thành viên";
		$fatherhood = $user_obj->fatherhood_user( array( $profile_user_id ) );
		if ( ! empty( $fatherhood ) )
		{
			if ( $fatherhood[$profile_user_id]['lev'] == 1 )
			{
				$father_hood = "Quản trị Tối cao";
			}
			elseif ( $fatherhood[$profile_user_id]['lev'] == 2 )
			{
				$father_hood = "Điều hành viên";
			}
			elseif ( $fatherhood[$profile_user_id]['lev'] == 3 )
			{
				$father_hood = "Quản trị website";

				$admins = $db->query( "SELECT admins FROM " . NV_PREFIXLANG . "_modules WHERE title = '" . $module_name . "' LIMIT 0,1 " )->fetchColumn();
				if ( ! empty( $admins ) )
				{
					$_admins = explode( ',', $admins );

					if ( $fatherhood[$profile_user_id]['lev'] == 3 && in_array( $profile_user_id, $_admins ) )
					{
						$father_hood = "Quản trị diễn đàn";
					}
				}

			}
		}
		else
		{
			$fatherhood[$profile_user_id]['lev'] = 4;
			if ( $array_data['post'] <= $arr_config['verify_post'] )
			{
				$father_hood = "Thành viên mới";
			}
			elseif ( $array_data['post'] > 3 && $array_data['post'] < 100 )
			{
				$father_hood = "Thành viên quen thuộc";
			}
			elseif ( $array_data['post'] > 100 && $array_data['post'] < 500 )
			{
				$father_hood = "Thành viên gắn bó với vietbrokers.vn";
			}
			elseif ( $array_data['post'] > 500 )
			{
				$father_hood = "Thành viên tâm huyết với vietbrokers.vn";
			}
		}
		
		
		$profile_page = 1;
		if( !empty( $getsub_op ) and substr( $getsub_op, 0, 5 ) == "page-" )
		{
			$profile_page = intval( substr( $getsub_op, 5 ) );
			if( $profile_page == 1 )
			{
				Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name."/members/".$array_data['username']."-".$array_data['userid'], true ) );
				exit();
			}
		}
		
		
		
		// thong tin status comment
		$sql="SELECT A.*, B.photo
		FROM  " . NV_PREFIXLANG . "_" . $module_data . "_profile_post A
		LEFT JOIN " . NV_USERS_GLOBALTABLE . " B
		ON A.profile_user_id = B.userid
		WHERE A.profile_user_id =".$profile_user_id."
		AND A.message_state ='visible'
		ORDER BY A.post_date DESC LIMIT " . ( $profile_page - 1 ) * $arr_config['profile_perpage'] . "," . $arr_config['profile_perpage'];
		$re_sult = $db->query( $sql );
		$profile_array = array();
		$key = 0;
		while ( $item = $re_sult->fetch() )
		{
			
			$profile_array[$key] = $item;
			if( !empty( $item['latest_comment_ids'] ) )
			{
				$sql2="SELECT A.*, B.photo FROM  " . NV_PREFIXLANG . "_" . $module_data . "_comment A
				LEFT JOIN " . NV_USERS_GLOBALTABLE . " B
				ON A.user_id = B.userid
				WHERE profile_post_comment_id IN (".$item['latest_comment_ids'].")
				ORDER BY comment_date ASC";
				$_result = $db->query( $sql2 );
				while ( $rows = $_result->fetch() )
				{
					$profile_array[$key]['comment'][] = $rows;
				}
				
			}
			
			++$key;
		}
		
		$profile_all_page = $db->query( "SELECT SQL_NO_CACHE COUNT( * ) FROM " . NV_PREFIXLANG . "_" . $module_data . "_profile_post WHERE profile_user_id =".$profile_user_id )->fetchColumn();

		$base_url = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name."/members/".$array_data['username']."-".$array_data['userid'];
		$generate_page = nv_alias_page( $page_title, $base_url, $profile_all_page, $arr_config['profile_perpage'], $profile_page );
		
	}
	else
	{
		Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
		exit();
	}
	$contents = $user_obj->member_page( $array_data, $profile_array, $generate_page, $userid, $is_online, $fatherhood[$profile_user_id], $father_hood, $position_online1, $position_online2, $position_online3 );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
	exit();

}else
{
	Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
	exit();
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';