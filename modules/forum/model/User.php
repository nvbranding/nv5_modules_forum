<?php

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$defaultAdminGroupId = 1;
$defaultModeratorGroupId = 2;
$defaultModuleGroupId = 3;
$defaultRegisteredGroupId = 4;
$defaultNewRegisteredGroupId = 7;
$defaultGuestGroupId = 5;
$guestPermissionCombinationId = 5;

function ModelUser_canBypassUserPrivacy( &$errorLangKey = '' )
{
	global $user_info, $global_userid;
	if( hasPermission( $user_info['permissions'], 'general', 'bypassUserPrivacy' ) )
	{
		return true;
	}

	return false;
}

function ModelUser_canViewUserOnlineStatus( array $user, &$errorLangKey = '' )
{
	global $user_info, $global_userid;
	if( ! $user['userid'] || ! $user['last_login'] )
	{
		return false;
	}
	elseif( $user['visible'] )
	{
		return true;
	}

	if( $user['userid'] == $global_userid )
	{
		// can always view own
		return true;
	}

	return ModelUser_canBypassUserPrivacy( $errorLangKey );
}

function ModelUser_canReportContent( &$errorLangKey = '' )
{
	global $user_info, $global_userid, $lang_module;
	if( ! $global_userid || ! hasPermission( $user_info['permissions'], 'general', 'report' ) )
	{
		$errorLangKey = $lang_module['you_may_not_report_this_content'];
		return false;
	}

	return true;
}

function ModelUser_prepareUser( array $user )
{
	global $defaultGuestGroupId;
	if( empty( $user['user_group_id'] ) )
	{
		$user['display_style_group_id'] = $defaultGuestGroupId;
	}

	$user['customFields'] = ( ! empty( $user['custom_fields'] ) ? @unserialize( $user['custom_fields'] ) : array() );
	$user['externalAuth'] = ( ! empty( $user['external_auth'] ) ? @unserialize( $user['external_auth'] ) : array() );

	// "trusted" user check - used to determine if no follow is enabled
	$user['isTrusted'] = ( ! empty( $user['userid'] ) && ( ! empty( $user['is_admin'] ) || ! empty( $user['is_moderator'] ) ) );

	return $user;
}

function ModelUser_couldBeSpammer( array $user, &$errorKey = '' )
{
	global $global_userid, $user_info;
	// self
	if( $user['userid'] == $global_userid )
	{
		$errorKey = 'sorry_dave';
		return false;
	}

	// staff
	if( $user['is_admin'] || $user['is_moderator'] )
	{
		$errorKey = 'spam_cleaner_no_admins_or_mods';
		return false;
	}

	$criteria = $spamUserCriteria = array(
		'message_count' => 30,
		'register_date' => 30,
		'like_count' => 5 ); // cau hinh

	if( $criteria['message_count'] && $user['message_count'] > $criteria['message_count'] )
	{
		$errorKey = array( 'spam_cleaner_too_many_messages', 'message_count' => $criteria['message_count'] );
		return false;
	}

	if( $criteria['register_date'] && $user['regdate'] < ( NV_CURRENTTIME - $criteria['register_date'] * 86400 ) )
	{
		$errorKey = array( 'spam_cleaner_registered_too_long', 'register_days' => $criteria['register_date'] );
		return false;
	}

	if( $criteria['like_count'] && $user['like_count'] > $criteria['like_count'] )
	{
		$errorKey = array( 'spam_cleaner_too_many_likes', 'like_count' => $criteria['like_count'] );
		return false;
	}

	return true;
}

function ModelUser_canViewIps( &$errorLangKey = '' )
{
	global $global_userid, $user_info;
	return ( $global_userid && hasPermission( $user_info['permissions'], 'general', 'viewIps' ) );
}

function ModelUser_canViewWarnings( &$errorLangKey = '' )
{
	global $global_userid, $user_info, $generalPermissions;

	if( ! $global_userid || ! hasPermission( $generalPermissions, 'general', 'viewWarning' ) )
	{
		return false;
	}

	return true;
}
