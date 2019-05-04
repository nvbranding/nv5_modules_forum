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

// change Avatar
$error = array();
$succe = array();
$info = array();
$userid = ( isset( $user_info['userid'] ) ) ? $user_info['userid'] : 0;
$user_obj = new Users();
if ( $nv_Request->isset_request( 'action', 'post' ) )
{
	$action = $nv_Request->get_title( 'action', 'post', '' );
	$userid = $nv_Request->get_int( 'userid', 'post', 0 );
	$checkss = $nv_Request->get_title( 'checkss', 'post', '' );
	$checkss_user = md5( session_id() . $userid . $global_config['sitekey'] );

	if ( $action == "update_avatar" )
	{
		if ( $checkss == $checkss_user )
		{
			if( $nv_Request->get_int( 'fdelete', 'post', 0 ) == 1 )
			{
				list( $userid, $photo ) = $db->query( "SELECT userid, photo  FROM " . NV_USERS_GLOBALTABLE . " WHERE userid = '" . $userid . "' LIMIT 0,1 " )->fetch( 3 );
				if ( $userid > 0 )
				{
					$sql="UPDATE ".NV_USERS_GLOBALTABLE." SET photo = '' WHERE userid=".$userid."";
					$query = $db->query( $sql );
					$_photo = explode('[f]', $photo);
					
					foreach( $_photo as $file )
					{
						if( ! empty( $file ) and is_file( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/' . $file ) )
						{
							@nv_deletefile( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/' . $file );
						}
					}
					$succe['medium_avatar'] = 
					$succe['medium_avatar'] = 
					$succe['large_avatar'] = NV_BASE_SITEURL . "themes/" . $global_config['module_theme'] . "/images/no_avatar.jpg";
		
				}

				$succe['message'] ="delete";
			}else
			{
				$succe['message'] ="ok";
			}
			
			
		}
	}
	elseif ( $action == "upload_avatar" )
	{
		if ( $checkss == $checkss_user )
		{

			$array_image = array();
			$folder = floor( $userid / 1000 );
			$array_image[] = 'l/' . $folder;
			$array_image[] = 'm/' . $folder;
			$array_image[] = 's/' . $folder;
			foreach ( $array_image as $tem )
			{
				$p = explode( '/', $tem );
				if ( ! file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/' . $tem ) )
				{
					if ( ! file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/' . $p[0] ) )
					{
						nv_mkdir( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users', $p[0] );
						if ( ! file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $p[0] . '/' . $p[1] ) )
						{
							nv_mkdir( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/' . $p[0], $p[1] );
						}
					}
					else
					{
						if ( ! file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/' . $p[0] . '/' . $p[1] ) )
						{
							nv_mkdir( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/' . $p[0], $p[1] );
						}
					}
				}

			}

			require_once ( NV_ROOTDIR . "/includes/class/upload.class.php" );
			require_once ( NV_ROOTDIR . "/includes/class/image.class.php" );

			$upload = new upload( array( 'images' ), array(
				'php',
				'php3',
				'php4',
				'php5',
				'phtml',
				'inc' ), array(), $global_config['nv_max_size'], 0, 0 );

			$upload_info = $upload->save_file( $_FILES['avatar'], NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/l/' . $folder, false );

			if ( empty( $upload_info['error'] ) )
			{
				$imgfolder = NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/l/' . $folder;
				$createImage = new image( $imgfolder . '/' . $upload_info['basename'], 0, 0 );
				$createImage->resizeXY( 0, 192 );
				$createImage->save( $imgfolder, $upload_info['basename'] );
				$createImage = new image( $imgfolder . '/' . $upload_info['basename'], 0, 0 );
				$createImage->resizeXY( 0, 192 );
				$createImage->save( $imgfolder, $upload_info['basename'] );
				$new_file = NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/l/' . $folder . '/' . $userid . '.' . $upload_info['ext'];
				rename( $upload_info['name'], $new_file );

				$upload_info['name'] = $new_file;

				$succe['userid'] = $userid;
				$succe['large_avatar'] = str_replace( NV_ROOTDIR, '', $new_file );
				$upload_info['basename'] = $userid . '.' . $upload_info['ext'];

				$basename = basename( $upload_info['name'] );

				$image = new image( $upload_info['name'], 0, 0 );
				$image->resizeXY( 96, 96 );
				$image->save( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/m/' . $folder, $basename );
				$thumb_m = $image->create_Image_info;
				$thumb_m_name = str_replace( NV_ROOTDIR, '', $thumb_m['src'] );

				$succe['medium_avatar'] = $thumb_m_name;

				$images = new image( $upload_info['name'], 0, 0 );
				$images->resizeXY( 48, 48 );
				$images->save( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/s/' . $folder, $basename );
				$thumb_s = $image->create_Image_info;
				$thumb_s_name = str_replace( NV_ROOTDIR, '', $thumb_s['src'] );

				$succe['small_avatar'] = $thumb_s_name;
				$user_obj->update_user_photo(array(
						'userid'=>$userid,
						'photo'=>str_replace( '/'.NV_UPLOADS_DIR . '/users/', '', $succe['large_avatar'].'[f]'.$succe['medium_avatar'].'[f]'.$succe['small_avatar'] )
				) );	
			}
			else
			{
				$error[] = $upload_info['error'];
			}
		}

	}
	elseif ( $action == "change_avatar" )
	{
		if ( $checkss == $checkss_user )
		{
			list( $userid, $photo ) = $db->query( "SELECT userid, photo  FROM " . NV_USERS_GLOBALTABLE . " WHERE userid = '" . $userid . "' LIMIT 0,1 " )->fetch( 3 );
			if ( $userid > 0 )
			{
			
				if( ! empty( $photo ) )
				{
					$array_img = array();
					$array_img = explode( "[f]", $photo );
					
					if( $array_img[0] != "" and file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/' . $array_img[0] ) )
					{
						$photo =  NV_BASE_SITEURL . NV_UPLOADS_DIR . '/users/' . $array_img[0];
						
					}else
					{
						$photo = NV_BASE_SITEURL . "themes/" . $global_config['module_theme'] . "/images/no_avatar.jpg";
					}
				}
				else
				{
					$photo = NV_BASE_SITEURL . "themes/" . $global_config['module_theme'] . "/images/no_avatar.jpg";
				}
				$xtpl = new XTemplate( "change_avatar.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file . "/account" );
				$xtpl->assign( 'LANG', $lang_module );
				$xtpl->assign( 'MODULE_FILE', $module_file );
				$xtpl->assign( 'TEMPLATE', $module_info['template'] );
				$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
				$xtpl->assign( 'url_upload', NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=account" );
				$xtpl->assign( 'userid', $userid );
				$xtpl->assign( 'photo', $photo );
				$xtpl->assign( 'checkss', $checkss );

				$xtpl->parse( 'main' );
				$succe['message'] = $xtpl->text( 'main' );

			}
			else
			{
				$error[] = "Có lỗi xảy ra không tìm thấy thành viên này";
			}

		}
		else
		{
			$error[] = "Có lỗi xảy ra phát hiện có gia lận";
		}

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

$getop = isset( $array_op[1] ) ? $array_op[1] : "";
$getsub_op = isset( $array_op[2] ) ? $array_op[2] : "";



if ( ! empty( $array_op ) )
{

	if ( $getop == 'editinfo' )
	{
		if ( ! defined( 'NV_IS_USER' ) or ! $global_config['allowuserlogin'] )
		{
			Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
			die();
		}

		$sql = "SELECT * FROM " . NV_USERS_GLOBALTABLE . " WHERE userid=" . $user_info['userid'];
		$query = $db->query( $sql );
		$row = $query->fetch();

		$array_data = array();
		$array_data['checkss'] = md5( $client_info['session_id'] . $global_config['sitekey'] );
		$checkss = $nv_Request->get_title( 'checkss', 'post', '' );

		//Thay doi cau hoi - cau tra loi du phong
		if ( $getsub_op == 'changequestion' )
		{
			$get_step_op = isset( $array_op[3] ) ? $array_op[3] : "";

			$oldpassword = $row['password'];
			$oldquestion = $row['question'];
			$oldanswer = $row['answer'];

			$page_title = $mod_title = $lang_module['change_question_pagetitle'];
			$key_words = $module_info['keywords'];

			$array_data['your_question'] = $oldquestion;
			$array_data['answer'] = $oldanswer;
			$array_data['nv_password'] = $nv_Request->get_title( 'nv_password', 'post', '' );
			$array_data['send'] = $nv_Request->get_bool( 'send', 'post', false );

			$get_step_op = 1;
			$error = "";

			if ( empty( $oldpassword ) )
			{
				$get_step_op = 2;
			}
			else
			{
				if ( $checkss == $array_data['checkss'] )
				{
					if ( $crypt->validate( $array_data['nv_password'], $oldpassword ) or $array_data['nv_password'] == md5( $oldpassword ) )
					{
						$get_step_op = 2;

						if ( ! isset( $array_data['nv_password']
						{
							31}
						) )
						{
							$array_data['nv_password'] = md5( $crypt->hash( $array_data['nv_password'] ) );
						}
					}
					else
					{
						$get_step_op = 1;
						$error = $lang_global['incorrect_password'];
					}
				}
			}

			if ( $get_step_op == 2 )
			{
				if ( $array_data['send'] )
				{
					$array_data['your_question'] = nv_substr( $nv_Request->get_title( 'your_question', 'post', '', 1 ), 0, 255);
					$array_data['answer'] = nv_substr( $nv_Request->get_title( 'answer', 'post', '', 1 ), 0, 255);

					if ( empty( $array_data['your_question'] ) )
					{
						$error = $lang_module['your_question_empty'];
					}
					elseif ( empty( $array_data['answer'] ) )
					{
						$error = $lang_module['answer_empty'];
					}
					else
					{
						$sql = "UPDATE " . NV_USERS_GLOBALTABLE . " 
						SET question=" . $db->quote( $array_data['your_question'] ) . ", 
						answer=" . $db->quote( $array_data['answer'] ) . " 
						WHERE userid=" . $user_info['userid'];
						$db->query( $sql );

						$contents = $user_obj->user_info_exit( $lang_module['change_question_ok'] );
						$contents .= "<meta http-equiv=\"refresh\" content=\"2;url=" . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name, true ) . "\" />";

						include NV_ROOTDIR . '/includes/header.php';
						echo nv_site_theme( $contents );
						include NV_ROOTDIR . '/includes/footer.php';
						exit();
					}
				}
			}

			$array_data['step'] = $get_step_op;
			$array_data['info'] = empty( $error ) ? $lang_module['changequestion_step' . $array_data['step']] : "<span style=\"color:#fb490b;\">" . $error . "</span>";

			if ( $get_step_op == 2 )
			{
				$array_data['questions'] = array();
				$array_data['questions'][] = $lang_module['select_question'];
				$sql = "SELECT title FROM " . NV_USERS_GLOBALTABLE . "_question  WHERE lang='" . NV_LANG_DATA . "' ORDER BY weight ASC";
				$result = $db->query( $sql );
				while ( $row = $result->fetch() )
				{
					$array_data['questions'][$row['title']] = $row['title'];
				}
			}

			$contents = $user_obj->user_changequestion( $array_data );

			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';
			exit();
		}

		//Thay doi thong tin khac
		$page_title = $mod_title = $lang_module['editinfo_pagetitle'];
		$key_words = $module_info['keywords'];

		$array_data['username'] = $row['username'];
		$array_data['email'] = $row['email'];
		$array_data['photo'] = $row['photo'];

		$array_data['allowmailchange'] = $global_config['allowmailchange'];
		$array_data['allowloginchange'] = ( $global_config['allowloginchange'] or ( ! empty( $row['last_openid'] ) and empty( $user_info['last_login'] ) and empty( $user_info['last_agent'] ) and empty( $user_info['last_ip'] ) and empty( $user_info['last_openid'] ) ) ) ? 1 : 0;

		if ( $checkss == $array_data['checkss'] )
		{
			$error = array();
			$array_data['full_name'] = nv_substr( $nv_Request->get_title( 'full_name', 'post', '', 1 ), 0, 255);
			$array_data['gender'] = nv_substr( $nv_Request->get_title( 'gender', 'post', '', 1 ), 0, 1);
			$array_data['birthday'] = nv_substr( $nv_Request->get_title( 'birthday', 'post', '', 0 ), 0, 10);
			$array_data['website'] = nv_substr( $nv_Request->get_title( 'website', 'post', '', 0 ), 0, 255);
			$array_data['address'] = nv_substr( $nv_Request->get_title( 'address', 'post', '', 1 ), 0, 255);
			$array_data['yim'] = nv_substr( $nv_Request->get_title( 'yim', 'post', '', 1 ), 0, 100);
			$array_data['telephone'] = nv_substr( $nv_Request->get_title( 'telephone', 'post', '', 1 ), 0, 100);
			$array_data['fax'] = nv_substr( $nv_Request->get_title( 'fax', 'post', '', 1 ), 0, 100);
			$array_data['mobile'] = nv_substr( $nv_Request->get_title( 'mobile', 'post', '', 1 ), 0, 100);
			$array_data['view_mail'] = $nv_Request->get_int( 'view_mail', 'post', 0 );

			if ( $array_data['allowloginchange'] )
			{
				$array_data['username'] = nv_substr( $nv_Request->get_title( 'username', 'post', '', 1 ), 0, NV_UNICKMAX);
				if ( $user_obj->nv_check_username_change( $array_data['username'] ) != "" )
				{
					$array_data['username'] = $row['username'];
					$error[] = $lang_module['account'];
				}
			}

			if ( empty( $array_data['full_name'] ) )
			{
				$array_data['full_name'] = $row['full_name'];
				$error[] = $lang_module['name'];
				if ( empty( $array_data['full_name'] ) )
				{
					$array_data['full_name'] = $row['username'];
				}
			}

			if ( $array_data['gender'] != "M" and $array_data['gender'] != "F" ) $array_data['gender'] = "";

			if ( preg_match( "/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/", $array_data['birthday'], $m ) )
			{
				$array_data['birthday'] = mktime( 0, 0, 0, $m[2], $m[1], $m[3] );
			}
			else
			{
				$array_data['birthday'] = 0;
			}

			if ( ! empty( $array_data['yim'] ) and ! preg_match( "/^([a-zA-Z0-9\_\.]+)$/", $array_data['yim'] ) )
			{
				$array_data['yim'] = $row['yim'];
				$error[] = $lang_module['yahoo'];
			}

			if ( $array_data['gender'] == "N" ) $array_data['gender'] = "";

			if ( ! empty( $array_data['website'] ) )
			{
				if ( ! preg_match( "#^(http|https|ftp|gopher)\:\/\/#", $array_data['website'] ) )
				{
					$array_data['website'] = "http://" . $array_data['website'];
				}
				if ( ! nv_is_url( $array_data['website'] ) )
				{
					$array_data['website'] = $row['website'];
					$error[] = $lang_module['website'];
				}
			}

			if ( $array_data['view_mail'] != 1 ) $array_data['view_mail'] = 0;

			if ( $array_data['allowmailchange'] )
			{
				$email_new = nv_substr( $nv_Request->get_title( 'email', 'post', '', 1 ), 0, 100);
				if ( $email_new != $row['email'] )
				{
					$checknum = nv_genpass( 10 );
					$checknum = md5( $checknum . $email_new );
					$md5_username = md5( $array_data['username'] );

					$sql = "DELETE FROM " . NV_USERS_GLOBALTABLE . "_reg WHERE md5username=" . $db->quote( $md5_username );
					$db->query( $sql );
					$error_email_change = $user_obj->nv_check_email_change( $email_new );
					if ( ! empty( $error_email_change ) )
					{
						$error[] = $error_email_change;
					}
					else
					{
						$sql = "INSERT INTO " . NV_USERS_GLOBALTABLE . "_reg VALUES (
						NULL, 
						'CHANGE_EMAIL_USERID_" . $user_info['userid'] . "', 
						" . $db->quote( $md5_username ) . ", 
						'', 
						" . $db->quote( $email_new ) . ", 
						'', 
						" . NV_CURRENTTIME . ", 
						'', 
						'', 
						" . $db->quote( $checknum ) . ")";
						$userid_check = $db->insert_id( $sql );

						if ( $userid_check > 0 )
						{
							$subject = $lang_module['email_active'];
							$message = sprintf( $lang_module['email_active_info'], $array_data['full_name'], $array_data['username'], NV_MY_DOMAIN . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=active&userid=" . $userid_check . "&checknum=" . $checknum, nv_date( "H:i d/m/Y", NV_CURRENTTIME + 86400 ), $global_config['site_name'] );
							$message .= "<br /><br />------------------------------------------------<br /><br />";
							if ( NV_LANG_DATA == 'vi' ) $message .= nv_EncString( $message );
							$send = nv_sendmail( $global_config['site_email'], $email_new, $subject, $message );
							if ( $send )
							{
								$error[] = $lang_module['email_active_mes'];
							}
							else
							{
								$error[] = $lang_module['email_active_error_mail'];
							}
						}
					}
				}
			}

			$sql = "UPDATE " . NV_USERS_GLOBALTABLE . " SET 
			username=" . $db->quote( $array_data['username'] ) . ", 
			md5username=" . $db->quote( md5( $array_data['username'] ) ) . ", 
			email=" . $db->quote( $array_data['email'] ) . ", 
			full_name=" . $db->quote( $array_data['full_name'] ) . ", 
			gender=" . $db->quote( $array_data['gender'] ) . ", 
			birthday=" . $db->quote( $array_data['birthday'] ) . ", 
			website=" . $db->quote( $array_data['website'] ) . ", 
			location=" . $db->quote( $array_data['address'] ) . ", 
			yim=" . $db->quote( $array_data['yim'] ) . ", 
			telephone=" . $db->quote( $array_data['telephone'] ) . ", 
			fax=" . $db->quote( $array_data['fax'] ) . ", 
			mobile=" . $db->quote( $array_data['mobile'] ) . ", 
			view_mail=" . $db->quote( $array_data['view_mail'] ) . " 
			WHERE userid=" . $user_info['userid'];
			$db->query( $sql );

			if ( isset( $_FILES['avatar'] ) and is_uploaded_file( $_FILES['avatar']['tmp_name'] ) )
			{
				@require_once ( NV_ROOTDIR . "/includes/class/upload.class.php" );

				$upload = new upload( array( 'images' ), $global_config['forbid_extensions'], $global_config['forbid_mimes'], NV_UPLOAD_MAX_FILESIZE, NV_MAX_WIDTH, NV_MAX_HEIGHT );
				$upload_info = $upload->save_file( $_FILES['avatar'], NV_UPLOADS_REAL_DIR . '/' . $module_name, false );

				@unlink( $_FILES['avatar']['tmp_name'] );

				if ( empty( $upload_info['error'] ) )
				{
					@chmod( $upload_info['name'], 0644 );

					if ( ! empty( $array_data['photo'] ) and is_file( NV_ROOTDIR . '/' . $array_data['photo'] ) )
					{
						@nv_deletefile( NV_ROOTDIR . '/' . $array_data['photo'] );
					}

					$image = $upload_info['name'];
					$basename = $upload_info['basename'];

					$imginfo = nv_is_image( $image );

					$basename = preg_replace( '/(.*)(\.[a-zA-Z]+)$/', '\1_' . $user_info['userid'] . '-' . 80 . '-' . 80 . '\2', $basename );

					$_image = new image( $image, 80, 80 );
					$_image->resizeXY( 80, 80 );
					$_image->save( NV_UPLOADS_REAL_DIR . '/' . $module_name, $basename );
					if ( file_exists( NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $basename ) )
					{
						$file_name = NV_UPLOADS_REAL_DIR . '/' . $module_name . '/' . $basename;
						//@chmod($file_name, 0644);
						$file_name = str_replace( NV_ROOTDIR . "/", "", $file_name );
						@nv_deletefile( $upload_info['name'] );
					}

					$sql = "UPDATE " . NV_USERS_GLOBALTABLE . " SET photo=" . $db->quote( $file_name ) . " WHERE userid=" . $user_info['userid'];
					$db->query( $sql );
				}
				else
				{
					$error[] = $lang_module['avata'];
				}
			}

			$info = $lang_module['editinfo_ok'];
			$sec = 3;
			if ( ! empty( $error ) )
			{
				$error = implode( "<br />", $error );
				$info = $info . ", " . sprintf( $lang_module['editinfo_error'], "<span style=\"color:#fb490b;\">" . $error . "</span>" );
				$sec = 5;
			}

			$contents = $user_obj->user_info_exit( $info );
			$contents .= "<meta http-equiv=\"refresh\" content=\"" . $sec . ";url=" . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name, true ) . "\" />";

			include NV_ROOTDIR . '/includes/header.php';
			echo nv_site_theme( $contents );
			include NV_ROOTDIR . '/includes/footer.php';
			exit();
		}
		else
		{
			$array_data['full_name'] = $row['full_name'];
			$array_data['gender'] = $row['gender'];
			$array_data['birthday'] = ! empty( $row['birthday'] ) ? date( "d.m.Y", $row['birthday'] ) : "";
			$array_data['website'] = $row['website'];
			$array_data['address'] = $row['location'];
			$array_data['yim'] = $row['yim'];
			$array_data['telephone'] = $row['telephone'];
			$array_data['fax'] = $row['fax'];
			$array_data['mobile'] = $row['mobile'];
			$array_data['view_mail'] = intval( $row['view_mail'] );
		}

		$array_data['view_mail'] = $array_data['view_mail'] ? " selected=\"selected\"" : "";

		$array_data['gender_array'] = array();
		$array_data['gender_array']['N'] = array(
			'value' => 'N',
			'title' => 'N/A',
			'selected' => '' );
		$array_data['gender_array']['M'] = array(
			'value' => 'M',
			'title' => $lang_module['male'],
			'selected' => ( $array_data['gender'] == 'M' ? " selected=\"selected\"" : "" ) );
		$array_data['gender_array']['F'] = array(
			'value' => 'F',
			'title' => $lang_module['female'],
			'selected' => ( $array_data['gender'] == 'F' ? " selected=\"selected\"" : "" ) );

		$contents = $user_obj->user_info( $array_data );

	}
	elseif ( $getop == 'changepass' )
	{
		$sql = "SELECT password FROM " . NV_USERS_GLOBALTABLE . " WHERE userid=" . $user_info['userid'];
		$query = $db->query( $sql );
		$oldpassword = $query->fetchColumn();

		$page_title = $mod_title = $lang_module['change_pass'];
		$key_words = $module_info['keywords'];

		$array_data = array();
		$array_data['pass_empty'] = empty( $oldpassword ) ? true : false;
		$array_data['change_info'] = $lang_module['change_info'];
		$array_data['checkss'] = md5( session_id() . $global_config['sitekey'] );

		$array_data['nv_password'] = $nv_Request->get_title( 'nv_password', 'post', '' );
		$array_data['new_password'] = $nv_Request->get_title( 'new_password', 'post', '' );
		$array_data['re_password'] = $nv_Request->get_title( 're_password', 'post', '' );
		$checkss = $nv_Request->get_title( 'checkss', 'post', '' );

		if ( $checkss == $array_data['checkss'] )
		{
			$error = "";

			if ( ! empty( $oldpassword ) and ! $crypt->validate( $array_data['nv_password'], $oldpassword ) )
			{
				$error = $lang_global['incorrect_password'];
				$error = str_replace( $lang_global['password'], $lang_module['pass_old'], $error );
			}
			elseif ( ( $check_new_password = nv_check_valid_pass( $array_data['new_password'], NV_UPASSMAX, NV_UPASSMIN ) ) != "" )
			{
				$error = $check_new_password;
			}
			elseif ( $array_data['new_password'] != $array_data['re_password'] )
			{
				$error = sprintf( $lang_global['passwordsincorrect'], $array_data['new_password'], $array_data['re_password'] );
				$error = str_replace( $lang_global['password'], $lang_module['pass_new'], $error );
			}
			else
			{
				$new_password = $crypt->hash( $array_data['new_password'] );

				$sql = "UPDATE " . NV_USERS_GLOBALTABLE . " SET password=" . $db->quote( $new_password ) . " WHERE userid=" . $user_info['userid'];
				$db->query( $sql );

				$contents = $user_obj->user_info_exit( $lang_module['change_pass_ok'] );
				$contents .= "<meta http-equiv=\"refresh\" content=\"5;url=" . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name, true ) . "\" />";

				include NV_ROOTDIR . '/includes/header.php';
				echo nv_site_theme( $contents );
				include NV_ROOTDIR . '/includes/footer.php';
				exit();
			}

			$array_data['change_info'] = "<span style=\"color:#fb490b;\">" . $error . "</span>";
		}

		$contents = $user_obj->user_changepass( $array_data );

	}
	elseif ( $getop == 'openid' )
	{
		if ( $nv_Request->isset_request( 'del', 'get' ) )
		{
			$openid_del = $nv_Request->get_typed_array( 'openid_del', 'post', 'string', '' );
			if ( ! empty( $openid_del ) )
			{
				foreach ( $openid_del as $opid )
				{
					if ( ! empty( $opid ) and ( empty( $user_info['current_openid'] ) or ( ! empty( $user_info['current_openid'] ) and $user_info['current_openid'] != $opid ) ) )
					{
						$sql = "DELETE FROM " . NV_USERS_GLOBALTABLE . "_openid WHERE opid=" . $db->quote( $opid );
						$db->query( $sql );
					}
				}
			}
			Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=openid", true ) );
			die();
		}

		if ( $nv_Request->isset_request( 'server', 'get' ) )
		{
			$server = $nv_Request->get_string( 'server', 'get', '' );
			if ( ! empty( $server ) and isset( $openid_servers[$server] ) )
			{
				include_once ( NV_ROOTDIR . "/includes/class/openid.class.php" );
				$openid_class = new LightOpenID();

				if ( $nv_Request->isset_request( 'openid_mode', 'get' ) )
				{
					$openid_mode = $nv_Request->get_string( 'openid_mode', 'get', '' );

					if ( $openid_mode == "cancel" )
					{
						$nv_Request->set_Session( 'openid_error', 1 );
						header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=openid", true ) );
						die();
					}
					elseif ( ! $openid_class->validate() )
					{
						$nv_Request->set_Session( 'openid_error', 2 );
						header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=openid", true ) );
						die();
					}
					else
					{
						$openid = $openid_class->identity;
						$attribs = $openid_class->getAttributes();
						$email = ( isset( $attribs['contact/email'] ) and nv_check_valid_email( $attribs['contact/email'] ) == "" ) ? $attribs['contact/email'] : "";
						if ( empty( $openid ) or empty( $email ) )
						{
							$nv_Request->set_Session( 'openid_error', 3 );
							header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=openid", true ) );
							die();
						}

						$opid = $crypt->hash( $openid );

						$query = "SELECT COUNT(*) AS count FROM " . NV_USERS_GLOBALTABLE . "_openid WHERE opid=" . $db->quote( $opid );
						$result = $db->query( $query );
						$count = $result->fetchColumn();

						if ( $count )
						{
							$nv_Request->set_Session( 'openid_error', 4 );
							header( "Location: " . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=openid" );
							die();
						}

						$query = "SELECT COUNT(*) AS count FROM " . NV_USERS_GLOBALTABLE . " WHERE userid!=" . $user_info['userid'] . " AND email=" . $db->quote( $email );
						$result = $db->query( $query );
						$count = $result->fetchColumn();

						if ( $count )
						{
							$nv_Request->set_Session( 'openid_error', 5 );
							header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=openid", true ) );
							die();
						}

						if ( $global_config['allowuserreg'] == 2 or $global_config['allowuserreg'] == 3 )
						{
							$query = "SELECT COUNT(*) AS count FROM " . NV_USERS_GLOBALTABLE . "_reg WHERE email=" . $db->quote( $email );
							if ( $global_config['allowuserreg'] == 2 )
							{
								$query .= " AND regdate>" . ( NV_CURRENTTIME - 86400 );
							}
							$result = $db->query( $query );
							$count = $result->fetchColumn();

							if ( $count )
							{
								$nv_Request->set_Session( 'openid_error', 6 );
								header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=openid", true ) );
								die();
							}
						}

						$sql = "INSERT INTO " . NV_USERS_GLOBALTABLE . "_openid VALUES (" . $user_info['userid'] . ", " . $db->quote( $openid ) . ", " . $db->quote( $opid ) . ", " . $db->quote( $email ) . ")";
						$db->query( $sql );

						nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['openid_add'], $user_info['username'] . " | " . $client_info['ip'] . " | " . $opid, 0 );

						header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=openid", true ) );
						die();
					}
				}
				else
				{
					$openid_class->identity = $openid_servers[$server]['identity'];
					$openid_class->required = array_values( $openid_servers[$server]['required'] );
					header( 'Location: ' . $openid_class->authUrl() );
					die();
				}
			}
		}

		$data = array();
		$data['openid_list'] = array();
		$sql = "SELECT * FROM " . NV_USERS_GLOBALTABLE . "_openid WHERE userid=" . $user_info['userid'];
		$query = $db->query( $sql );
		while ( $row = $query->fetch() )
		{
			$server = parse_url( $row['openid'] );

			$data['openid_list'][] = array( //
				'opid' => $row['opid'], //
				'openid' => $row['openid'], //
				'server' => $server['host'], //
				'email' => $row['email'], //
				'disabled' => ( ( ! empty( $user_info['current_openid'] ) and $user_info['current_openid'] == $row['opid'] ) ? " disabled=\"disabled\"" : "" ) ); //

		}

		$error = $nv_Request->get_int( 'openid_error', 'session', 0 );
		$nv_Request->unset_request( 'openid_error', 'session' );

		switch ( $error )
		{
			case 1:
				$data['info'] = "<div style=\"color:#fb490b;\">" . $lang_module['canceled_authentication'] . "</div>";
				break;

			case 2:
				$data['info'] = "<div style=\"color:#fb490b;\">" . $lang_module['not_logged_in'] . "</div>";
				break;

			case 3:
				$data['info'] = "<div style=\"color:#fb490b;\">" . $lang_module['logged_in_failed'] . "</div>";
				break;

			case 4:
				$data['info'] = "<div style=\"color:#fb490b;\">" . $lang_module['openid_is_exists'] . "</div>";
				break;

			case 5:
			case 6:
				$data['info'] = "<div style=\"color:#fb490b;\">" . $lang_module['email_is_exists'] . "</div>";
				break;

			default:
				$data['info'] = $lang_module['openid_add_new'];
		}

		$contents = $user_obj->user_openid_administrator( $data );

	}
}
else
{
	if ( ! defined( 'NV_IS_USER' ) )
	{
		$redirect = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=account";
		Header( "Location: " . NV_BASE_SITEURL . "index.php?" . NV_NAME_VARIABLE . "=users&" . NV_OP_VARIABLE . "=login&nv_redirect=" . nv_base64_encode( $redirect ) );
		die();
	}
	else
	{
		$contents = $user_obj->user_welcome();
	}
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';