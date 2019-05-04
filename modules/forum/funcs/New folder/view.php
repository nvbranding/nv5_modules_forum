<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweB.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 4, 2013 22:18
 */

if ( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

$user_obj = new Users();
$ip_obj = new Ipcountry();
$sceditor = new Sceditor();
$post_obj = new Post();

$error = array();
$succe = array();
$info = array();

// ghi nhan thanh vien
forum_online( $catid, $thread_id, 0 );

if ( $nv_Request->isset_request( 'action', 'post' ) )
{

	$action = $nv_Request->get_string( 'action', 'post', '' );

	if ( $action == "update_post" )
	{

		$catid = $nv_Request->get_int( 'catid', 'post', 0 );
		$array['post_id'] = $nv_Request->get_int( 'post_id', 'post', 0 );
		$array = $post_obj->get_post( $array['post_id'] );

		$checkss_post_id = $nv_Request->get_title( 'checkss_post_id', 'post', '' );
		
		$checkss_post = md5( $userid . session_id() . $catid . $action . $global_config['sitekey'] . $array['post_id'] );

		$time_edit_user = $arr_config['time_edit_user'] * 86400 + $array['post_date'];
		$admin_array = explode( ',', $nv_cat[$catid]['admins'] );

		if ( ( $checkss_post == $checkss_post_id and ( $time_edit_user - NV_CURRENTTIME ) > 0 ) || in_array( $userid, $admin_array ) )
		{
			$array['old_text'] = $array['message'];
			$array['message'] = $nv_Request->get_string( 'messsage_post', 'post', '' );

			$check = $post_obj->update_message_post( $array );
			if ( $check )
			{
				$array['action'] = 'addnew';
				$array['content_type'] = 'edit';
				$array['content_id'] = $array['post_id'];
				$array['ip'] = $client_info['ip'];
				$ip_obj->insert_ip( $array );

				$array['last_edit_user_id'] = $userid;
				$post_obj->last_post_id( $array );

				$array['content_type'] = 'post'; //moderated | deleted
				$array['edit_user_id'] = $userid; //moderated | deleted
				$post_obj->insert_post_history( $array );
				// $test7 = var_export( $_REQUEST, true );
				// file_put_contents( NV_ROOTDIR . '/logs/file7.log', "" . $array['message'] . " \r\n", FILE_APPEND );

				$succe['message'] = $sceditor->BBCodeToHTML( $array['message'], true );

				$time1 = nv_date( 'd-m-Y', NV_CURRENTTIME );
				$time2 = nv_date( 'h:i:s A', NV_CURRENTTIME );

				$succe['time'] = '<span class="postcontent lastedited"> Sửa lần cuối bởi ' . $user_info['username'] . '; ' . $time1 . ' lúc <span class="time">' . $time2 . '</span>. </span>';
			}
			else
			{
				$succe['message'] = $sceditor->BBCodeToHTML( $array['message'], true );
			}

		}
		else
		{
			$error[] = $lang_module['error_edit_permision'];
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
	elseif ( $action == "post_reply" )
	{
		$catid = $nv_Request->get_int( 'catid', 'post', 0 );
		$thread_id = $nv_Request->get_int( 'thread_id', 'post', 0 );
		$check_permistion = md5( $userid . session_id() . $action . $thread_id );

		$checkss = $nv_Request->get_title( 'checkss', 'post', '' );

		if ( $check_permistion == $checkss )
		{
			$array['message'] = $nv_Request->get_string( 'message', 'post', 0 );
			$array['message1'] = $sceditor->remove( $array['message'] );

			if ( ! empty( $array['message1'] ) )
			{
				$array = $thread_obj->get_thread( $thread_id );
				if ( ! empty( $array ) )
				{
					$array['last_post_user_id'] = $userid;
					$array['last_post_username'] = $array['username'] = $user_info['username'];

					$array['post_date'] = $array['last_edit_date'] = $array['last_post_date'] = NV_CURRENTTIME;

					//update ip
					$array['action'] = 'addnew';
					$array['content_type'] = 'reply';
					$array['content_id'] = $array['first_post_id'];
					$array['ip'] = $client_info['ip'];
					$array['ip_id'] = $ip_obj->insert_ip( $array );

					// insert post reply
					$position = $db->query( "SELECT MAX(position) FROM " . NV_PREFIXLANG . "_" . $module_data . "_post WHERE thread_id=" . $thread_id . " LIMIT 1" )->fetchColumn();
					$array['position'] = $position + 1;
					$array['message'] = $nv_Request->get_string( 'message', 'post', 0 );
					$array['message_state'] = 'visible'; //moderated | deleted
					$array['attach_count'] = 0;
					$array['likes'] = 0;
					$array['like_users'] = '';
					$array['warning_id'] = 0;
					$array['warning_message'] = '';
					$array['edit_count'] = 0;
					$array['last_edit_user_id'] = 0;
					$array['post_id'] = $post_obj->insert_post( $array );

					$check_exist = $user_obj->get_users_statistic( $array['user_id'] );
					if ( $check_exist['total'] > 0 )
					{
						$message_count = $check_exist['message_count'] + 1;
						$user_obj->update_users_statistic( array( 'message_count' => $message_count ), $array['user_id'] );
					}
					else
					{
						// insert_users_statistic
						$user_obj->insert_users_statistic( array(
							'is_staff' => 0,
							'message_count' => 1,
							'like_count' => 0,
							'userid' => $array['user_id'] ) );
					}
					//update lastest forum
					$array['last_thread_title'] = $array['title'];
					$array['last_thread_id'] = $array['thread_id'];
					$array['last_prefix_id'] = $array['prefix_id'];
					$array['last_post_id'] = $array['post_id'];
					$array['last_post_page'] = $post_obj->all_post_page( $array['thread_id'], $per_page );

					$cat_obj->cat_last_thread( $array, false );

					$thread_obj->update_thread_last_post( $array );

					$thread_obj->update_thread_user_post( $array );
					$page = $post_obj->all_post_page( $array['thread_id'], $per_page );

					if ( $page > 1 ) $link = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $array['title'] ) . "/" . $array['thread_id'] . "/page-" . $page, true );
					else  $link = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $array['title'] ) . "/" . $array['thread_id'], true );

					if ( $array['user_id'] != $userid )
					{
						unset( $sql );
						$sql = "SELECT  userid, username, full_name, email, sendmail, token
						FROM  " . NV_PREFIXLANG . "_" . $module_data . "_thread_watch A
						CROSS JOIN " . NV_USERS_GLOBALTABLE . " B
						ON A.user_id = B.userid
						WHERE user_id = " . $array['user_id'] . " AND thread_id=" . $thread_id . "
						LIMIT 0 , 1";
					
						list( $userid_post, $username_post, $full_name_post, $email_post, $sendmail, $token ) = $db->query( $sql )->fetch( 3 );

						if ( $sendmail == 1 and ! empty( $email_post ) )
						{
							$data_notice['fullname'] = ! empty( $full_name ) ? $full_name : $username_post;

							$data_notice['link_comment'] = NV_MY_DOMAIN . $link;
							$data_notice['link_thread'] = NV_MY_DOMAIN . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $array['title'] ) . "/" . $array['thread_id'], true );
							$data_notice['link_un_watch'] = NV_MY_DOMAIN . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=unwatch&amp;token=".$token;
							$data_notice['title'] = $array['title'];
							$lang_module['notice_title_link'] = sprintf ( $lang_module['notice_title_link'], $user_info['username'], '<a href="'.$data_notice['link_thread'].'" target="_blank">'.$data_notice['title'].'</a>');
							$lang_module['notice_email_noreply'] = sprintf( $lang_module['notice_email_noreply'], $global_config['site_url'], $global_config['site_url'] );
							$lang_module['notice_email_title'] = sprintf( $lang_module['notice_email_title'], $user_info['username'] );
							
							$email_contents = call_user_func( "notice_email", $data_notice );
							nv_sendmail( array( $global_config['site_name'], $global_config['site_email'] ), $email_post, $lang_module['notice_email_title'], $email_contents );
						}
					}
					$succe['hash'] = "#post" . $array['post_id'];
					$succe['message'] = $lang_module['susses_answer'];
					$succe['link'] = $link;
					$succe['page'] = $page;
					$forum->del_file_cache( 'cat' );
					$forum->del_file_cache( 'lastest_post' );
				}
				else
				{
					$error[] = $lang_module['error_notfound'];
				}

			}
			else
			{
				$error[] = $lang_module['error_content'];
			}
		}
		else
		{
			$error[] = $lang_module['error_create_post'];
		}

		echo show_json( $info, $error, $succe );
		exit();
	}
	elseif ( $action == "load_message" )
	{
		$catid = $nv_Request->get_int( 'catid', 'post', 0 );
		$post_id = $nv_Request->get_int( 'post_id', 'post', 0 );
		$checkss = $nv_Request->get_title( 'checkss', 'post', '' );
		$checkss_post = md5( $userid . session_id() . $catid . $action. $global_config['sitekey'] . $post_id );

		$array = $post_obj->get_post( $post_id );

		// quyền thao tác công cụ
		$time_edit_user = $arr_config['time_edit_user'] * 86400 + $array['post_date'];
		$admin_array = explode( ',', $nv_cat[$catid]['admins'] );

		if ( ( $checkss_post == $checkss and ( $time_edit_user - NV_CURRENTTIME ) > 0 ) || in_array( $userid, $admin_array ) )
		{
			
			$action = 'update_post';
			$array['checkss'] = md5( $array['user_id'] . session_id() . $catid . $action. $global_config['sitekey'] . $array['post_id'] );
			$array['edit_post'] = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=post/update/" . $array['post_id'], true );

			$xtpl = new XTemplate( "quick_reply.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
			$xtpl->assign( 'LANG', $lang_module );
			$xtpl->assign( 'MODULE_FILE', $module_file );
			$xtpl->assign( 'TEMPLATE', $module_info['template'] );
			$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
			$xtpl->assign( 'DATA', $array );
			$xtpl->assign( 'catid', $catid );
			$xtpl->parse( 'main' );
			$contents = $xtpl->text( 'main' );
			$info['quick_reply'] = $contents;

		}
		else
		{
			$info['error'] = $lang_module['error_edit_permision'];
		}
		echo str_replace( '\n', '', json_encode( $info ) );
		exit();
	}
	elseif ( $action == "del_message" )
	{
		$array['thread_id'] = $nv_Request->get_int( 'thread_id', 'post', 0 );
		$array['post_id'] = $nv_Request->get_int( 'post_id', 'post', 0 );
		$catid = $nv_Request->get_int( 'catid', 'post', 0 );
		$checkss = $nv_Request->get_title( 'checkss', 'post', '' );
		$checkss_post = md5( $userid . session_id() . $global_config['sitekey'] . $array['post_id'] );

		// quyền thao tác công cụ
		$admin_array = explode( ',', $nv_cat[$catid]['admins'] );

		if ( $checkss_post == $checkss || in_array( $userid, $admin_array ) )
		{
			$sql = "SELECT A.thread_id, A.title, A.prefix_id, B.post_id, B.user_id, B.username, B.post_date, B.position, B.likes 
			FROM " . NV_PREFIXLANG . "_" . $module_data . "_thread A 
			CROSS JOIN " . NV_PREFIXLANG . "_" . $module_data . "_post B 
			ON A.thread_id = B.thread_id WHERE A.catid = " . $catid . " 
			AND A.thread_id=" . $array['thread_id'] . " 
			AND  B.post_id=" . $array['post_id'] . " 
			ORDER BY A.post_date, B.post_date DESC LIMIT 0 , 1";
			list( $thread_id, $title, $prefix_id, $post_id, $user_id, $username, $post_date, $position, $likes ) = $db->query( $sql )->fetch( 3 );

			if ( $position == 0 )
			{
				$error[] = $lang_module['error_del_permision'];
			}
			else
			{

				$db->query( "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_post WHERE post_id = " . $post_id );
				$db->query( "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_liked_content WHERE content_id = " . $post_id );
 
				$last_page = $post_obj->all_post_page( $thread_id, $per_page );

				$db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_cat SET
					replycount=replycount - 1,
					last_post_date=" . intval( $post_date ) . ",
					last_post_username=" . $db->quote( $username ) . ",
					last_post_user_id=" . intval( $user_id ) . ",
					last_post_id=" . intval( $post_id ) . ",
					last_post_page=" . intval( $last_page ) . ",
					last_thread_title=" . $db->quote( $title ) . ",
					last_thread_id=" . intval( $thread_id ) . ",
					last_prefix_id=" . intval( $prefix_id ) . "
					WHERE catid=" . $catid );

				$db->query( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_users_statistic A, " . NV_PREFIXLANG . "_" . $module_data . "_thread B, " . NV_PREFIXLANG . "_" . $module_data . "_thread_user_post C 
				SET A.like_count = IF(A.like_count > " . $likes . ", A.like_count - " . $likes . ", 0) , 
				A.message_count = IF(A.message_count > 1, A.message_count - 1, 0),
				B.reply_count = IF(B.reply_count > 1, B.reply_count - 1, 0),
				C.post_count = IF(C.post_count > 1, C.post_count - 1, 0)
				WHERE B.thread_id = " . $thread_id . "
				AND A.userid = " . $user_id . "
				AND C.thread_id = " . $thread_id . "
				AND C.user_id = " . $user_id . "" );

				$forum->del_file_cache( 'cat' );
				$forum->del_file_cache( 'lastest_post' );

				if ( ! empty( $last_page ) ) $link = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $title ) . "/" . $thread_id . "/page-" . $last_page, true ) . "#post" . $array['post_id'] . "";
				else  $link = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $title ) . "/" . $thread_id, true ) . "#post" . $array['post_id'] . "";
				$succe['link'] = $link;
				$succe['page'] = $last_page;

				$succe['message'] = $lang_module['susses_del'];
			}

		}
		else
		{
			$error[] = $lang_module['error_del'];
		}

		echo show_json( $info, $error, $succe );
		exit();

	}
	elseif ( $action == "like_post" )
	{
		$array['post_id'] = $nv_Request->get_int( 'post_id', 'post', 0 );
		$array['user_id'] = $nv_Request->get_int( 'user_id', 'post', 0 );
		$array['username'] = $nv_Request->get_title( 'username', 'post', '' );
		$checkss = $nv_Request->get_title( 'checkss', 'post', '' );
		$checkss_like = md5( session_id() . $array['post_id'] . $global_config['sitekey'] );

		if ( $checkss_like == $checkss )
		{
			if ( ! empty( $user_info ) )
			{
				$admin_forum = $forum->admin_forum();
				$succe['total'] = 0;
				$array['content_type'] = 'post';
				$array['content_id'] = $array['post_id'];
				$array['like_user_id'] = $userid;
				$array['content_user_id'] = $array['user_id'];
				$_total = $post_obj->get_post_like( $array );

				if ( $_total == 0 )
				{
					$check_exist = $user_obj->get_users_statistic( $array['content_user_id'] );

					$like_count = $check_exist['like_count'] + 1;
					$user_obj->update_users_statistic( array( 'like_count' => $like_count ), $array['content_user_id'] );
					$post_obj->update_post_likes( $array['content_id'], '+' );

					$succe['like_exist'] = 0;

					$like_id = $post_obj->insert_post_like( $array );
					if ( $like_id > 0 )
					{

						$succe['like_ok'] = 1;
						$likes = $post_obj->get_post_likes( $array['content_id'], array( 'page' => 0, 'paper_page' => 3 ) );

						$xtpl = new XTemplate( "like_content.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
						$xtpl->assign( 'LANG', $lang_module );
						$xtpl->assign( 'MODULE_FILE', $module_file );
						$xtpl->assign( 'TEMPLATE', $module_info['template'] );
						$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );

						$n = 1;
						if ( ! empty( $likes ) )
						{
							foreach ( $likes as $val )
							{
								( ( $n < count( $likes ) ) ? $xtpl->parse( 'main.like.looplike.comma' ) : '' );
								$userpage_like = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=members/" . $val['username'] . "-" . $val['userid'];
								$xtpl->assign( 'username_like', $val['username'] );
								$xtpl->assign( 'userpage_like', $userpage_like );
								$xtpl->parse( 'main.like.looplike' );
								++$n;
							}
						}
						$total_count = $post_obj->countLikesForContentUser( $array['content_id'] );
						if ( $total_count > $n )
						{
							$xtpl->assign( 'all_like', $total_count - $n );
							$xtpl->parse( 'main.like.all_like' );
						}

						$xtpl->parse( 'main.like' );

						$succe['total'] = $total_count;
						$succe['content_id'] = $array['content_id'];
						$xtpl->parse( 'main' );
						$succe['content'] = $xtpl->text( 'main' );

					}
					else
					{
						$succe['like_ok'] = 0;
						$succe['err'] = $lang_module['error_like'];
					}
				}
				else
				{
					$succe['like_exist'] = 1;
					$like_id = $post_obj->delete_post_like( $array );
					if ( $like_id > 0 )
					{
						$check_exist = $user_obj->get_users_statistic( $array['content_user_id'] );

						$like_count = $check_exist['like_count'] - 1;
						$user_obj->update_users_statistic( array( 'like_count' => $like_count ), $array['content_user_id'] );
						$post_obj->update_post_likes( $array['content_id'], '-' );

						$succe['like_ok'] = 1;
						$likes = $post_obj->get_post_likes( $array['content_id'], array( 'page' => 0, 'paper_page' => 3 ) );

						$xtpl = new XTemplate( "like_content.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
						$xtpl->assign( 'LANG', $lang_module );
						$xtpl->assign( 'MODULE_FILE', $module_file );
						$xtpl->assign( 'TEMPLATE', $module_info['template'] );
						$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );

						$n = 1;
						if ( ! empty( $likes ) )
						{
							foreach ( $likes as $val )
							{
								( ( $n < count( $likes ) ) ? $xtpl->parse( 'main.like.looplike.comma' ) : '' );
								$userpage_like = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=members/" . $val['username'] . "-" . $val['userid'];
								$xtpl->assign( 'username_like', $val['username'] );
								$xtpl->assign( 'userpage_like', $userpage_like );
								$xtpl->parse( 'main.like.looplike' );
								++$n;
							}
						}
						$total_count = $post_obj->countLikesForContentUser( $array['content_id'] );
						if ( $total_count > $n )
						{
							$xtpl->assign( 'all_like', $total_count - $n );
							$xtpl->parse( 'main.like.all_like' );
						}

						$xtpl->parse( 'main.like' );

						$succe['total'] = $total_count;
						$succe['content_id'] = $array['content_id'];
						$xtpl->parse( 'main' );
						$succe['content'] = $xtpl->text( 'main' );

					}
					else
					{
						$succe['like_ok'] = 0;
					}
				}
			}
			else
			{
				$error[] = $lang_module['error_login'];
			}
		}
		else
		{
			$error[] = $lang_module['error_permision'];
		}

		echo show_json( $info, $error, $succe );
		exit();
	}
	elseif ( $action == "load_content" )
	{
		$post_id = $nv_Request->get_int( 'post_id', 'post', 0 );

		$array = $post_obj->get_post( $post_id );

		$array['user_photo'] = $db->query( "SELECT  photo FROM " . NV_USERS_GLOBALTABLE . " WHERE userid = '" . $array['user_id'] . "'" )->fetchColumn();

		$xtpl = new XTemplate( "load_content.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'MODULE_FILE', $module_file );
		$xtpl->assign( 'TEMPLATE', $module_info['template'] );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );

		$array['post_time'] = date( 'i:s', $array['post_date'] );
		$array['post_date'] = date( 'd-m-Y', $array['post_date'] );

		$array['message'] = nv_clean60( strip_tags( $sceditor->BBCodeToHTML( $array['message'] ) ), 220 );

		if ( ! empty( $array['user_photo'] ) )
		{
			$array_img = array();
			$array_img = explode( "[f]", $array['user_photo'] );
			if ( $array_img[0] != "" and file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/' . $array_img[0] ) )
			{
				$array['user_photo'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/users/' . $array_img[0];

			}
			else
			{
				$array['user_photo'] = NV_BASE_SITEURL . "themes/" . $global_config['module_theme'] . "/images/no_avatar.jpg";
			}
		}
		else
		{
			$array['user_photo'] = NV_BASE_SITEURL . "themes/" . $global_config['module_theme'] . "/images/no_avatar.jpg";
		}

		$xtpl->assign( 'DATA', $array );

		$xtpl->parse( 'main' );
		$contents = $xtpl->text( 'main' );
		$succe['message'] = $contents;
		
		echo show_json( $info, $error, $succe );
		exit();
	}

}

unset( $matches );
if ( isset( $array_op[0] ) and isset( $array_op[1] ) || ( isset( $array_op[2] ) and substr( $array_op[2], 0, 5 ) == "page-" ) )
{

	$query = $db->query( "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_thread WHERE thread_id = " . $thread_id . "" );
	$data = $query->fetch();

	if ( $data['thread_id'] > 0 )
	{
		$page_title = $data['title'];
		$key_words = $data['title'];
		$description = $data['title'];

		$parentid = $data['catid'];
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

		// cap nhat luot xem chu de
		$time_set = $nv_Request->get_int( $module_data . '_' . $op . '_' . $data['thread_id'], 'session' );
		if ( empty( $time_set ) )
		{
			$nv_Request->set_Session( $module_data . '_' . $op . '_' . $data['thread_id'], NV_CURRENTTIME );
			$thread_obj->update_thread_view_count( $data['thread_id'] );
		}

		// lay thong tin bai tra loi
		$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_post WHERE thread_id=" . $thread_id . " ORDER BY position ASC LIMIT " . ( $page - 1 ) * $arr_config['paper_post'] . "," . $arr_config['paper_post'];
		$result = $db->query( $sql );
		$all_page = $db->query( "SELECT COUNT( * ) FROM " . NV_PREFIXLANG . "_" . $module_data . "_post WHERE thread_id=" . $thread_id )->fetchColumn();

		$array_normal = array();
		$list_user_id = array();
		$list_post_id = array();
		while ( $item = $result->fetch() )
		{
			$list_user_id[] = $item['user_id'];
			$list_user_id[] = $item['last_edit_user_id'];
			$list_post_id[] = $item['post_id'];
			$array_normal[] = $item;
		}

		// lay thong tin like
		unset( $item, $sql, $re );
		$sql = "SELECT  A.content_id, A.like_user_id FROM " . NV_PREFIXLANG . "_" . $module_data . "_post B 
		LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_liked_content A
		ON A.content_id=B.post_id WHERE B.post_id IN (" . implode( ',', $list_post_id ) . ") 
		AND A.like_user_id = " . $userid . "";
		$re = $db->query( $sql );
		$array_liked = array();
		while ( $item = $re->fetch() )
		{
			$array_liked[$item['content_id']] = $item;
		}

		unset( $item, $sql, $re );

		$sql = "SELECT post_id, COUNT(content_id) like_count 
		FROM " . NV_PREFIXLANG . "_" . $module_data . "_post B
		LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_liked_content A 
		ON content_id = post_id WHERE post_id IN (" . implode( ',', $list_post_id ) . ") GROUP BY post_id";
		$re = $db->query( $sql );
		$array_total = array();
		while ( $item = $re->fetch() )
		{
			$array_total[$item['post_id']] = $item['like_count'];
		}

		unset( $sql, $re, $item );

		$array_total_content = array();
		$sql = "SELECT content_id,  COUNT( * ) total  FROM " . NV_PREFIXLANG . "_" . $module_data . "_liked_content WHERE content_id IN ( " . implode( ',', $list_post_id ) . " ) GROUP BY content_id";
		$re = $db->query( $sql );
		while ( $item = $re->fetch() )
		{
			$array_total_content[$item['content_id']] = $item;
		}

		unset( $sql, $re, $item );

		$user_filter = array_filter( array_unique( $list_user_id ) );

		$array_user = $user_obj->get_user( $user_filter );

		$base_url = nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $data['title'] ) . "/" . $data['thread_id'], false );

		$generate_page = nv_alias_page( $page_title, $base_url, $all_page, $arr_config['paper_post'], $page );

		$xtpl = new XTemplate( "view.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
		$xtpl->assign( 'LANG', $lang_module );
		$xtpl->assign( 'MODULE_FILE', $module_file );
		$xtpl->assign( 'TEMPLATE', $module_info['template'] );
		$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
		$xtpl->assign( 'linksite', NV_BASE_SITEURL . "themes/" . $module_info['template'] );
		$xtpl->assign( 'ACTION', nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=view", true ) );
		$xtpl->assign( 'QUICK_MOD', nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=quick", true ) );
		$xtpl->assign( 'CAT', $nv_cat[$data['catid']] );
		$xtpl->assign( 'catid', $data['catid'] );
		$xtpl->assign( 'page', $page );

		$data['post_date_other'] = $data['post_date'];
		$data['post_time'] = date( 'i:s', $data['post_date'] );
		$data['post_date'] = date( 'd/m/Y', $data['post_date'] );

		$xtpl->assign( 'DATA', $data );
		
		$action = 'post_reply';
		$xtpl->assign( 'CHECKSS', md5( $userid . session_id() . $action . $data['thread_id'] ) );

		$admin_array = explode( ',', $nv_cat[$data['catid']]['admins'] );

		if ( ! empty( $array_normal ) )
		{
			$num = 1;
			foreach ( $array_normal as $loop )
			{

				$loop['like_count'] = $array_total[$loop['post_id']];
				$loop['num'] = $num;

				$sceditor = new Sceditor();

				$loop['message'] = $sceditor->BBCodeToHTML( $loop['message'], true );

				if ( date( 'd', $loop['post_date'] ) == date( 'd', NV_CURRENTTIME ) )
				{
					$xtpl->parse( 'main.loop_page.loop.new_post' );
				}

				$loop['post_date'] = ! empty( $loop['post_date'] ) ? nv_date( 'd-m-Y, h:i:s A', $loop['post_date'] ) : "";
				
				// kiểm tra tính toàn vẹn của dữ liệu
				$action = 'load_message';
				$loop['checkss'] = md5( $loop['user_id'] . session_id() . $data['catid'] . $action. $global_config['sitekey'] . $loop['post_id'] );
				
				$loop['checkss_like'] = md5( session_id() . $loop['post_id'] . $global_config['sitekey'] );
				$loop['checkss_admin'] = md5( $userid . session_id() . $global_config['sitekey'] . $loop['post_id'] );
				$xtpl->assign( 'loop', $loop );

				if ( $loop['edit_count'] > 0 )
				{
					$last_edit_user = $array_user[$loop['last_edit_user_id']]['username'];
					$last_edit_date1 = nv_date( 'd-m-Y', $loop['last_edit_date'] );
					$last_edit_date2 = nv_date( 'h:i:s A', $loop['last_edit_date'] );
					$xtpl->assign( 'last_edit_date1', $last_edit_date1 );
					$xtpl->assign( 'last_edit_date2', $last_edit_date2 );
					$xtpl->assign( 'last_edit_user', $last_edit_user );

					$xtpl->parse( 'main.loop_page.loop.edit' );
				}

				if ( isset( $array_user[$loop['user_id']] ) )
				{
					$user = $array_user[$loop['user_id']];

					$check = true;
				}
				else
				{
					$user = array(
						'userid' => 0,
						'username' => 'guest',
						'full_name' => 'guest',
						'photo' => '',
						'regdate' => 0,
						'like' => 0,
						'like_count' => 0,
						'total_post' => 0 );
					$check = false;
				}

				if ( ! empty( $user['photo'] ) )
				{
					$array_img = array();
					$array_img = explode( "[f]", $user['photo'] );
					if ( $array_img[0] != "" and file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/' . $array_img[0] ) )
					{
						$user['photo'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/users/' . $array_img[0];

					}
					else
					{
						$user['photo'] = NV_BASE_SITEURL . "themes/" . $global_config['module_theme'] . "/images/no_avatar.jpg";
					}
				}
				else
				{
					$user['photo'] = NV_BASE_SITEURL . "themes/" . $global_config['module_theme'] . "/images/no_avatar.jpg";
				}
				$user['user_page'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=members/" . $user['username'] . "-" . $user['userid'];

				$user['regdate'] = isset( $user['regdate'] ) ? nv_date( 'd-m-Y', $user['regdate'] ) : '';

				$xtpl->assign( 'USER', $user );
				
				
				// thong ke like
				if ( ! empty( $loop['likes'] ) )
				{
					$likes = $post_obj->get_post_likes( $loop['post_id'], array( 'page' => 0, 'paper_page' => 3 ) );
					$n = 1;
					if ( ! empty( $likes ) )
					{
						foreach ( $likes as $val )
						{
							( ( $n < $loop['likes'] ) ? $xtpl->parse( 'main.loop_page.loop.like.looplike.comma' ) : '' );
							$userpage_like = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=members/" . $val['username'] . "-" . $val['userid'];
							$xtpl->assign( 'username_like', $val['username'] );
							$xtpl->assign( 'userpage_like', $userpage_like );
							$xtpl->parse( 'main.loop_page.loop.like.looplike' );
							++$n;
						}
					}

					if ( $array_total_content[$loop['post_id']]['total'] > $n )
					{
						$xtpl->assign( 'all_like', $array_total_content[$loop['post_id']]['total'] - $n );
						$xtpl->parse( 'main.loop_page.loop.like.all_like' );
					}

					$xtpl->parse( 'main.loop_page.loop.like' );
				}

				if ( isset( $array_liked[$loop['post_id']] ) )
				{
					$xtpl->assign( 'like', 'hidden_elem' );
					$xtpl->assign( 'unlike', '' );
				}
				elseif ( ! isset( $array_liked[$loop['post_id']] ) or ! defined( 'NV_IS_USER' ) )
				{

					$xtpl->assign( 'like', '' );
					$xtpl->assign( 'unlike', 'hidden_elem' );
				}

				// quyền thao tác công cụ
				$time_edit_user = $arr_config['time_edit_user'] * 86400 + $loop['post_date'];

				// sửa bài viết
				if ( isset( $user['userid'] ) )
				{

					if ( ( $userid == $user['userid'] and ( $time_edit_user - NV_CURRENTTIME ) > 0 ) || in_array( $userid, $admin_array ) )
					{
						$xtpl->parse( 'main.loop_page.loop.user_edit' );
					}

				}

				// xóa bài viết
				if ( in_array( $userid, $admin_array ) )
				{
					$xtpl->parse( 'main.loop_page.loop.user_del' );

				}

				// trả lời bài viết
				if ( defined( 'NV_IS_USER' ) )
				{
					$xtpl->parse( 'main.loop_page.loop.reply' );
				}
				else
				{
					$xtpl->parse( 'main.loop_page.loop.nonereply' );
				}
 
				$xtpl->parse( 'main.loop_page.loop' );
				++$num;
			}
			
			// button reply
			if ( defined( 'NV_IS_USER' ) )
			{
				$xtpl->parse( 'main.loop_page.show_editor1' );
				$xtpl->parse( 'main.loop_page.show_editor2' );
			}
			else
			{
				$xtpl->parse( 'main.loop_page.none_user1' );
				$xtpl->parse( 'main.loop_page.none_user2' );
			}
			
			if ( ! empty( $generate_page ) )
			{
				$xtpl->assign( 'GENERATE_PAGE', $generate_page );
				$xtpl->parse( 'main.loop_page.generate_page0' );
				$xtpl->parse( 'main.loop_page.generate_page1' );
			}

			$xtpl->parse( 'main.loop_page' );

		}
		
 
		
		// chu de cung chuyen muc
		if ( $arr_config['type_thread'] == 'thread_new' )
		{
			$order_by = " WHERE catid=" . $data['catid'] . " AND post_date > " . $data['post_date_other'] . " ORDER BY post_date DESC ";
		}
		elseif ( $arr_config['type_thread'] == 'thread_old' )
		{
			$order_by = " WHERE catid=" . $data['catid'] . " AND post_date < " . $data['post_date_other'] . " ORDER BY post_date ASC ";
		}
		elseif ( $arr_config['type_thread'] == 'thread_random' )
		{
			$order_by = " WHERE catid=" . $data['catid'] . " ORDER BY RAND() ";
		}
		unset( $sql, $result );
		$sql = "SELECT thread_id, catid, title, post_date FROM " . NV_PREFIXLANG . "_" . $module_data . "_thread " . $order_by . " LIMIT  " . ( $page - 1 ) * $per_page . "," . $arr_config['paper_thread'];
		$result = $db->query( $sql );
		
		$i = 1;
		while ( $other = $result->fetch() )
		{
			$other['link'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $forum->lower_alias( $other['title'] ) . "/" . $other['thread_id'];
			$xtpl->assign( 'LOOP', $other );
			$xtpl->parse( 'main.other.loop' );
			++$i;
		}
		if( $i > 1) $xtpl->parse( 'main.other' );
		
		
		
		// quyen tra loi thanh vien
		if ( defined( 'NV_IS_USER' ) )
		{
			$xtpl->assign( 'checkss_quickmod', md5( $userid . session_id() . $data['thread_id'] . $global_config['sitekey'] ) );
			$xtpl->parse( 'main.user_reply' );
		} 
		
		
		// hien thi quickmod cho admin
		if ( in_array( $userid, $admin_array ) )
		{
			$xtpl->parse( 'main.quickmod' );
		}
		
		// quick forum
		foreach( $nv_cat as $_catid => $cat )
		{ 
			$cat['selected'] = ( $data['catid'] == $_catid ) ? 'selected="selected"': '';
			$xtpl->assign( 'QCAT', $cat);
			$xtpl->parse( 'main.qcat' );
		}
		
		
		$xtpl->parse( 'main' );
		$contents = $xtpl->text( 'main' );
	}
	else
	{
		$contents = "<h3 style=\"text-align:center;padding-top:20px\">Chủ đề bạn vừa chọn không tồn tại hoặc đã bị xóa
		<meta http-equiv=\"refresh\" content=\"3;URL=" . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) . "\" />
		</h3>";
	}
	
	$forum->clear();
	$thread_obj->clear();
	$cat_obj->clear();

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
}
else
{
	Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
	exit();
}