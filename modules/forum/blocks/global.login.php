<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/25/2010 18:6
 */

if ( ! defined( 'NV_SYSTEM' ) ) die( 'Stop!!!' );

global $client_info, $global_config, $module_name, $module_info, $user_info, $lang_global, $openid_servers, $lang_module;

if ( $module_name == 'users' ) return "";

$content = "";

$groups_list = nv_groups_list_pub();

if ( $global_config['allowuserlogin'] and $module_name != "users" )
{
	$mod_file = $site_mods[$module]['module_file'];
	$groups_list = nv_groups_list_pub();

	if ( file_exists( NV_ROOTDIR . "/themes/" . $global_config['module_theme'] . "/modules/" . $mod_file . "/block.login.tpl" ) )
	{
		$block_theme = $global_config['module_theme'];
	}
	elseif ( file_exists( NV_ROOTDIR . "/themes/" . $global_config['site_theme'] . "/modules/" . $mod_file . "/block.login.tpl" ) )
	{
		$block_theme = $global_config['site_theme'];
	}
	else
	{
		$block_theme = "default";
	}

	$xtpl = new XTemplate( "block.login.tpl", NV_ROOTDIR . "/themes/" . $block_theme . "/modules/" . $mod_file );

	if ( defined( 'NV_IS_USER' ) )
	{
		$avata = "";
		if ( ! empty( $user_info['photo'] ) )
		{
			$array_img = array();
			$array_img = explode( "[f]", $user_info['photo'] );

			if ( $array_img[0] != "" and file_exists( NV_ROOTDIR . '/' . NV_UPLOADS_DIR . '/users/' . $array_img[0] ) )
			{
				$avata = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/users/' . $array_img[0];

			}
			else
			{
				$avata = NV_BASE_SITEURL . "themes/" . $global_config['module_theme'] . "/images/no_avatar.jpg";
			}
		}
		else
		{
			$avata = NV_BASE_SITEURL . "themes/" . $global_config['module_theme'] . "/images/no_avatar.jpg";
		}
		$xtpl->assign( 'AVATA', $avata );
		$xtpl->assign( 'LANG', $lang_global );
		$xtpl->assign( 'USER', $user_info );
		if ( ! defined( 'NV_IS_ADMIN' ) )
		{
			$xtpl->assign( 'LOGOUT_ADMIN', "" . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=logout" );
			$xtpl->parse( 'signed.admin' );
		}
		$xtpl->assign( 'CHANGE_PASS', "" . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=changepass" );
		$xtpl->assign( 'CHANGE_INFO', "" . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users" );
		$xtpl->assign( 'RE_GROUPS', "" . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=regroups" );
		$xtpl->assign( 'PAGE_USER', NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module . "&amp;" . NV_OP_VARIABLE . "=members/".$user_info['username']."-".$user_info['userid'] );

		if ( ! empty( $groups_list ) && $global_config['allowuserpublic'] == 1 )
		{

			$in_group = "<a title='" . $lang_global['in_groups'] . "' href='" . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=regroups'>" . $lang_global['in_groups'] . "</a>";
			$xtpl->assign( 'in_group', $in_group );
		}

		$xtpl->parse( 'signed' );
		$content = $xtpl->text( 'signed' );
	}
	else
	{
		$xtpl->assign( 'REDIRECT', nv_base64_encode( $client_info['selfurl'] ) );
		$xtpl->assign( 'USER_LOGIN', "" . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=login" );
		$xtpl->assign( 'USER_REGISTER', "" . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=register" );
		$xtpl->assign( 'USER_LOSTPASS', "" . NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=lostpass" );

		$xtpl->assign( 'NICK_MAXLENGTH', NV_UNICKMAX );
		$xtpl->assign( 'PASS_MAXLENGTH', NV_UPASSMAX );
		$xtpl->assign( 'LANG', $lang_global );

		if ( in_array( $global_config['gfx_chk'], array(
			2,
			4,
			5,
			7 ) ) )
		{
			$xtpl->assign( 'N_CAPTCHA', $lang_global['securitycode'] );
			$xtpl->assign( 'CAPTCHA_REFRESH', $lang_global['captcharefresh'] );
			$xtpl->assign( 'GFX_WIDTH', NV_GFX_WIDTH );
			$xtpl->assign( 'GFX_HEIGHT', NV_GFX_HEIGHT );
			$xtpl->assign( 'CAPTCHA_REFR_SRC', NV_BASE_SITEURL . "images/refresh.png" );
			$xtpl->assign( 'SRC_CAPTCHA', NV_BASE_SITEURL . "index.php?scaptcha=captcha" );
			$xtpl->assign( 'GFX_MAXLENGTH', NV_GFX_NUM );
			$xtpl->parse( 'main.captcha' );
		}

		if ( defined( 'NV_OPENID_ALLOWED' ) )
		{
			$xtpl->assign( 'OPENID_IMG_SRC', NV_BASE_SITEURL . "themes/" . $block_theme . "/images/users/openid_small.gif" );
			$xtpl->assign( 'OPENID_IMG_WIDTH', 24 );
			$xtpl->assign( 'OPENID_IMG_HEIGHT', 24 );

			$assigns = array();
			foreach ( $openid_servers as $server => $value )
			{
				$assigns['href'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=login&amp;server=" . $server . "&amp;nv_redirect=" . nv_base64_encode( $client_info['selfurl'] );
				$assigns['title'] = ucfirst( $server );
				$assigns['img_src'] = NV_BASE_SITEURL . "themes/" . $block_theme . "/images/users/" . $server . ".gif";
				$assigns['img_width'] = $assigns['img_height'] = 16;

				$xtpl->assign( 'OPENID', $assigns );
				$xtpl->parse( 'main.openid.server' );
			}
			$xtpl->parse( 'main.openid' );
		}

		$xtpl->parse( 'main' );
		$content = $xtpl->text( 'main' );
	}
}