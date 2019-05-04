<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES., JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES ., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Dec 3, 2010  11:23:15 AM
 */

if( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

function ThemeForumMain( $forumData, $categoryList )
{
	global $forum_node, $generalPermissions, $global_config, $lang_global, $userid, $user_info, $module_upload, $site_mods, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeForumMain.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post' );
 
	foreach( $categoryList as $category_id )
	{
		if( isset( $forum_node[$category_id] ) )
		{
			$node = $forum_node[$category_id];
			$xtpl->assign( 'NODE', $node );
 
			if( ! empty( $node['subcatid'] ) )
			{
				$subcatid = explode( ',', $node['subcatid'] );
				foreach( $subcatid as $sub_node_id )
				{
					if( isset( $forum_node[$sub_node_id] ) )
					{

						$sub_node = $forum_node[$sub_node_id];

						$sub_node['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $sub_node['alias'];

						if( isset( $forumData[$sub_node_id] ) )
						{
 
							$data_forum = $forumData[$sub_node['node_id']];
							$data_forum['last_post_date'] = str_replace( '|', 'tháng', nv_date( 'd | m Y, H:i', $data_forum['last_post_date'] ) );
							$data_forum['last_post_link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $data_forum['last_post_id'] . '/view';

							$xtpl->assign( 'DATA', $data_forum );

							if( $data_forum['last_post_id'] > 0 )
							{
								$xtpl->parse( 'main.node.subnode.latest_post' );
							}
							else
							{
								$xtpl->parse( 'main.node.subnode.no_latest_post' );
							}

						}

						$xtpl->assign( 'SUBNODE', $sub_node );
						$xtpl->parse( 'main.node.subnode' );
					}
				}

			}
			$xtpl->parse( 'main.node' );
		}	
	}
 

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeForumViewNode( $dataContent )
{
	global $forum_node, $generalPermissions, $nodePermissions, $global_userid, $user_info, $module_upload, $lang_global, $global_config, $site_mods, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeForumViewNode.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post' );
 
	if( $dataContent['stickyThreads'] )
	{
		foreach( $dataContent['stickyThreads'] as $_thread_id => $thread )
		{
			$thread_alias = strtolower( change_alias( $thread['title'] ) );
			$thread['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . $thread_alias . '-' . $thread['thread_id'] . $global_config['rewrite_exturl'], true );
			$thread['last_post_link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $thread['last_post_id'] . '/view';
			$thread['post_date'] = nv_date( 'd/m/Y H:i', $thread['post_date'] );
			$thread['last_post_date'] = nv_date( 'd/m/Y H:i', $thread['last_post_date'] );
			$thread['last_post_username'] = $thread['last_post_username'];


			if( ! empty( $thread['photo'] ) && file_exists( NV_ROOTDIR . '/' . $thread['photo'] ) )
			{
				$thread['photo'] = NV_BASE_SITEURL . $thread['photo'];
			}
			elseif( is_file( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png' ) )
			{
				$thread['photo'] = NV_BASE_SITEURL . 'themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png';
			}
			else
			{
				$thread['photo'] = NV_BASE_SITEURL . 'themes/default/images/users/no_avatar.png';
			}

			$xtpl->assign( 'THREAD', $thread );
			$xtpl->parse( 'main.thread_sticky.loop' );	
		}
		$xtpl->parse( 'main.thread_sticky' );	
	}
	
	if( $dataContent['threads'] )
	{
		foreach( $dataContent['threads'] as $_thread_id => $thread )
		{
			$thread_alias = strtolower( change_alias( $thread['title'] ) );
			$thread['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . $thread_alias . '-' . $thread['thread_id'] . $global_config['rewrite_exturl'], true );
			$thread['last_post_link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $thread['last_post_id'] . '/view';
			$thread['post_date'] = nv_date( 'd/m/Y H:i', $thread['post_date'] );
			$thread['last_post_date'] = nv_date( 'd/m/Y H:i', $thread['last_post_date'] );
			$thread['last_post_username'] = $thread['last_post_username'];


			if( ! empty( $thread['photo'] ) && file_exists( NV_ROOTDIR . '/' . $thread['photo'] ) )
			{
				$thread['photo'] = NV_BASE_SITEURL . $thread['photo'];
			}
			elseif( is_file( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png' ) )
			{
				$thread['photo'] = NV_BASE_SITEURL . 'themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png';
			}
			else
			{
				$thread['photo'] = NV_BASE_SITEURL . 'themes/default/images/users/no_avatar.png';
			}

			$xtpl->assign( 'THREAD', $thread );
			$xtpl->parse( 'main.thread_normal.loop' );	
		}
		$xtpl->parse( 'main.thread_normal' );	
	}
	if( ! empty( $dataContent['generatePage'] ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $dataContent['generatePage'] );
		$xtpl->parse( 'main.generate_page_top' );
		$xtpl->parse( 'main.generate_page_bottom' );
	}
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeForumContent( $dataContent )
{
	global $lang_global, $forum_node, $nodePermissions, $node_id, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeForumContent.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'ACTION', $client_info['selfurl'] );
 	$xtpl->assign( 'NODE', $dataContent['forum'] );
 	 
	$xtpl->assign( 'ATTACHMENT_HASH', md5( uniqid( '', true ) ) );
	$xtpl->assign( 'TOKEN', md5( session_id() . $global_config['sitekey'] . $dataContent['forum']['node_id'] ) );
	$xtpl->assign( 'EXTENSIONS', implode(',', array_map('add_quotes_ext', $dataContent['attachmentConstraints']['extensions'])) );
	$xtpl->assign( 'MAX_SIZE',  $dataContent['attachmentConstraints']['size']);
	$xtpl->assign( 'UPLOAD_LIMIT',  $dataContent['attachmentConstraints']['limit']);
	
	$message = '';
	if( $dataContent['draft'] )
	{
		
		$xtpl->assign( 'TITLE', $dataContent['title'] );
		$message = $dataContent['draft']['message'];
		$message = htmlspecialchars( nv_editor_br2nl( $message ) );
	}

	$message = getForumEditor( 'message', $message, $width = '100%', $height = '300px' );
	
	$xtpl->assign( 'MESSAGE',  $message );
	// if( $dataContent['thread']['discussion_open'] )
	// {
		// $discussion_open = 1;
		// if( isset( $dataContent['extra']['discussion_open'] ) ) 
		// {
			// $discussion_open = 1;		
		// }else{
			
			// $discussion_open = 0;	
		// }
	// }else{
		// $discussion_open = 0;	
	// }
	
	$discussion_open = 1;
	
	$xtpl->assign( 'WatchStateChecked', ( $dataContent['watchState'] == 'watch_email' ) ? 'checked="checked"' : 'disabled="disabled"' );
	$xtpl->assign( 'WatchThreadChecked', ( $dataContent['watchState'] == 'watch_email' ) ? 'checked="checked"' : '' );
	$xtpl->assign( 'ThreadDiscussionChecked', ( $discussion_open ) ? 'checked="checked"' : '' );
	$xtpl->assign( 'ThreadStickyChecked', ( isset( $dataContent['extra']['sticky'] ) ) ? 'checked="checked"' : '' );
	$xtpl->assign( 'WatchStateClass', ( $dataContent['watchState'] != 'watch_email' ) ? 'class="pdisabled"' : '' );
 
	if( $dataContent['attachmentParams'])
	{
		$xtpl->parse( 'main.CanUploadAttachment1' );
		$xtpl->parse( 'main.CanUploadAttachment2' );
		$xtpl->parse( 'main.CanUploadAttachment3' );
	}
 
	if( ! empty($dataContent['tags'] ) )
	{
		foreach( $dataContent['tags'] as $tag )
		{
			$xtpl->assign( 'TAG', $tag );
			$xtpl->parse( 'main.tag' );
		}

	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeForumViewThreads( $dataContent )
{
	global $lang_global, $generalPermissions, $nodePermissions, $global_userid, $forum_node, $global_config, $userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeForumViewThreads.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'SITE_URL', $client_info['selfurl'] );
	$xtpl->assign( 'ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=quickreply' );
 
	$threadData = $dataContent['thread'];
	$forumData = $dataContent['forum'];
	$postData = $dataContent['posts'];
 
	$forum_node[$threadData['node_id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $forum_node[$threadData['node_id']]['alias'];

	$xtpl->assign( 'NODE', $forum_node[$threadData['node_id']] );

	$threadData['data_time'] = $threadData['post_date'];
	$threadData['post_date'] = nv_date( 'd/m/Y H:i', $threadData['post_date'] );
	$xtpl->assign( 'THREAD', $threadData );
	$count = 1;
	foreach( $postData as $_post_id => $post )
	{

		if( stripos( $post['message'], '&#91;/ATTACH&#93;' ) !== false )
		{
			if( preg_match_all( '#&\#91;ATTACH(=[^&\#93;]*)?&\#93;(?P<id>\d+)(\D.*)?&\#91;/ATTACH&\#93;#i', $post['message'], $matches ) )
			{
				$new_attachment = array();

				foreach( $matches['id'] as $key => $attachId )
				{
					if( isset( $post['attachments'][$attachId] ) )
					{
						
						
						$_attach = $post['attachments'][$attachId];

						if( stripos( $matches[1][$key], 'full' ) !== false )
						{
							$replace = '<a rel="image" href="' . $_attach['contentLink'] . '" data-image-count="' . $_attach['attachment_id'] . '"><img src="' . $_attach['contentLink'] . '" class="attach"  /></a>';
						}
						else
						{
							$replace = '<img src="' . $_attach['thumbnailUrl'] . '" class="attach" data-image-count="' . $_attach['attachment_id'] . '" />';
						}

						$new_attachment[] = array(
							'post_id' => $post['post_id'],
							'attachment_id' => $attachId,
							'search' => $matches[0][$key],
							'replace' => $replace );

						unset( $post['attachments'][$attachId] );
					}
				}
				foreach( $new_attachment as $value )
				{
					$post['message'] = str_replace( $value['search'], $value['replace'], $post['message'] );

				}

			}
			unset( $matches );
		}
		$post['message'] = preg_replace( '@<p class="closequote">(.*?)</p>@siu', '$1', $post['message'] );
		$post['message'] = preg_replace( "#&\#91;quote=&quot;(.*?), post: (.*?), member:(.*?)&\#93;(.*?)&\#91;/quote&\#93;#is", '<div class="bbCodeBlock bbCodeQuote" data-author="$1"><aside><div class="attribution type">$1 đã viết:<a href="#post-$2" class="AttributionLink">↑</a></div><blockquote class="quoteContainer"><div class="quote">$4</div><div class="quoteExpand">Xem chi tiết...</div></blockquote></aside></div>', $post['message'] );

		

		if( ! empty( $post['photo'] ) && file_exists( NV_ROOTDIR . '/' . $post['photo'] ) )
		{
			$post['photo'] = NV_BASE_SITEURL . $post['photo'];
		}
		elseif( is_file( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png' ) )
		{
			$post['photo'] = NV_BASE_SITEURL . 'themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png';
		}
		else
		{
			$post['photo'] = NV_BASE_SITEURL . 'themes/default/images/users/no_avatar.png';
		}

		$post['last_time'] = ( $post['last_edit_date'] ) ? $post['last_edit_date'] : 0;
		$post['last_edit_date'] = ( $post['last_edit_date'] ) ? nv_date( 'd/m/Y H:i', $post['last_edit_date'] ) : 0;
		$post['post_date'] = nv_date( 'd/m/Y H:i', $post['post_date'] );

		
		$post['token'] = md5( session_id() . $global_config['sitekey'] . $post['post_id'] );
		$post['post_edit_inline'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/edit-inline';
		$post['user_edit'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/edit';
		$post['user_delete'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/delete';
		$post['user_ip'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/ip';
		$post['user_report'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/report';
		$post['user_warn'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/warn';
		$post['user_quote'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/quote';
		$post['user_like'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/like';
		$post['user_location_quote'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . strtolower( change_alias( $threadData['title'] ) ) . '-' . $post['thread_id'] . '/reply&quote=' . $post['post_id'];

		$xtpl->assign( 'POST', $post );
		
		if( $post['likes'] > 0)
		{
 
			if( $post['likeUsers'] )
			{
				$countLike = sizeof( $post['likeUsers'] );
				$curentUser = 0;
				foreach( $post['likeUsers'] as $key => $like )
				{
					if( $like['userid'] != $global_userid )
					{
						$xtpl->assign( 'LIKE', $like );
						$xtpl->parse( 'main.post.likeUsers.member_like' );
					}else{
						++$curentUser;
						$xtpl->parse( 'main.post.likeUsers.you_like' );
					}
				}
				
				if( $countLike > 1 && $curentUser)
				{
					$xtpl->parse( 'main.post.likeUsers.and' );
				}
				
				$xtpl->parse( 'main.post.likeUsers' );
			}
 	
		}
		
		
		$userGroup = nv_user_groups( $post['user_group_id'] );

		if( $userGroup )
		{
			$xtpl->assign( 'GROUP', $userGroup[$post['user_group_id']] );
			$xtpl->parse( 'main.post.getgroup' );
		}

		if( $post['last_edit_date'] > 0 )
		{
			if( $post['userid'] == $post['last_edit_user_id'] )
			{
				$xtpl->parse( 'main.post.editDate.byUser' );
			}
			else
			{
				$xtpl->parse( 'main.post.editDate.byModerator' );
			}

			$xtpl->parse( 'main.post.editDate' );
		}

		if( isset( $post['attachments'] ) && $post['attachments'] )
		{

			foreach( $post['attachments'] as $attachment_id => $attachment )
			{
				$attachment['file_size'] = nv_convertfromBytes( $attachment['file_size'] );

				$xtpl->assign( 'ATTACHMENT', $attachment );

				if( $attachment['thumbnailUrl'] and $dataContent['canViewAttachments'] )
				{
					$xtpl->parse( 'main.post.attachment.loop.viewimage' );

				}
				elseif( $attachment['thumbnailUrl'] )
				{
					$xtpl->parse( 'main.post.attachment.loop.clickimage' );

				}
				else
				{

					$xtpl->parse( 'main.post.attachment.loop.clickfile' );
				}

				$xtpl->parse( 'main.post.attachment.loop' );
			}
			$xtpl->parse( 'main.post.attachment' );
		}

		if( $post['first_name'] && $post['last_name'] )
		{
			$xtpl->assign( 'fullName', nv_show_name_user( $post['first_name'], $post['last_name'], $post['username'] ) );
			$xtpl->parse( 'main.post.fullName' );
		}

		if( $post['isOnline'] )
		{
			$xtpl->parse( 'main.post.isOnline' );
		}
		if( $post['is_staff'] )
		{
			$xtpl->parse( 'main.post.isStaff' );
		}
		if( $post['canEdit'] )
		{
			$xtpl->parse( 'main.post.canEdit' );
		}
		if( $post['canDelete'] )
		{
			$xtpl->parse( 'main.post.canDelete' );
		}
		if( $post['canReport'] )
		{
			$xtpl->parse( 'main.post.canReport' );
		}
		if( $post['canViewHistory'] )
		{
			$xtpl->parse( 'main.post.canViewHistory' );
		}
		if( $post['canWarn'] )
		{
			$xtpl->parse( 'main.post.canWarn' );
		}

		if( $post['canLike'] )
		{
			$xtpl->parse( 'main.post.canLike' );
		}

		if( $post['isNew'] )
		{
			$xtpl->parse( 'main.post.isNew' );
		}

		if( $dataContent['canViewIps'] )
		{
			$xtpl->parse( 'main.post.canViewIps' );
		}

		if( $dataContent['canReply'] && defined( 'NV_IS_USER' ) )
		{
			$xtpl->parse( 'main.post.canReply' );
		}

		// cau hinh message
		$guestShowSignatures = 1; // 0 là tắt hiển thị chữ ký

		if( $guestShowSignatures )
		{

			$xtpl->parse( 'main.post.signature' );
		}
		
		if( $count == 1 )
		{
			$xtpl->parse( 'main.post.googleAds' );
		}
		
		$xtpl->parse( 'main.post' );
		++$count;
	}
	
	
	if( ! empty( $dataContent['generatePage'] ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $dataContent['generatePage'] );
		$xtpl->parse( 'main.generate_page_top' );
		$xtpl->parse( 'main.generate_page_bottom' );
	}
 
	if( ! defined( 'NV_IS_USER' ) )
	{
		$redirect = $client_info['selfurl'];

		$xtpl->assign( 'USER_LOGIN', NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt( $redirect ) );
		$xtpl->parse( 'main.guestLogin' );

	}
	else
	{
		$xtpl->parse( 'main.usertLogin' );
	}

	if( defined( 'NV_IS_USER' ) && $dataContent['canQuickReply'] )
	{
		$message = getForumEditor( 'message', '', $width = '100%', $height = '150px' );

		$xtpl->assign( 'MESSAGE', $message );
		$xtpl->assign( 'TOKEN', md5( session_id() . $global_config['sitekey'] . $threadData['node_id'] ) );
		$xtpl->assign( 'NODE_ID', $threadData['node_id'] );
		$xtpl->assign( 'ATTACHMENT_HASH', ForumRandomString() );
		$xtpl->parse( 'main.canQuickReply' );
	}
	else
	{

		$xtpl->parse( 'main.noQuickReply' );
	}

	// thread tool
	$xtpl->assign( 'TOKEN', md5( session_id() . $global_config['sitekey'] . $threadData['thread_id'] ) );
	$xtpl->assign( 'URL_ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=content' );
 	
	if( $dataContent['canEditThread'] )
	{
		
		$xtpl->parse( 'main.canEditThread' );
		
	}elseif( $dataContent['canEditTitle'] )
	{
		$xtpl->parse( 'main.canEditTitle' );
	}
	if( $dataContent['canDeleteThread'] )
	{	
		$xtpl->parse( 'main.canDeleteThread' );
	}
 	if( $dataContent['canMoveThread'] )
	{	
		$xtpl->parse( 'main.canMoveThread' );
	}
  	if( $dataContent['canStickUnstickThread'] )
	{	
		$xtpl->parse( 'main.canStickUnstickThread' );
	}
  	if( $dataContent['canLockUnlockThread'] )
	{	
		$xtpl->parse( 'main.canLockUnlockThread' );
	}
   	if( $dataContent['canWatchThread'] )
	{	
		$xtpl->parse( 'main.canWatchThread' );
	}
 
	unset( $dataContent['thread'], $dataContent['forum'], $dataContent['posts'] );
 

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeGetLikePost( $dataContent )
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeGetLikePost.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	
	
	$likeUsers = $dataContent['post']['likeUsers'];
 
	if( $likeUsers )
	{
		$countLike = sizeof( $likeUsers );
		$curentUser = 0;
		foreach( $likeUsers as $key => $like )
		{
			if( $like['userid'] != $global_userid )
			{
				$xtpl->assign( 'LIKE', $like );
				$xtpl->parse( 'main.likeUsers.member_like' );
			}else{
				++$curentUser;
				$xtpl->parse( 'main.likeUsers.you_like' );
			}
		}
				
		if( $countLike > 1 && $curentUser)
		{
			$xtpl->parse( 'main.likeUsers.and' );
		}
				
		$xtpl->parse( 'main.likeUsers' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeDeletePostForm( $postData, $threadData, $forumData, $nodePermissions, $generalPermissions )
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeDeletePostForm.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'ACTION', $client_info['selfurl'] );
	$xtpl->assign( 'DELETE_BY', $user_info['username'] );

	$postData['token'] = md5( session_id() . $global_config['sitekey'] . $postData['post_id'] );

	$xtpl->assign( 'POST', $postData );
	if( $postData['position'] == 0 )
	{
		$xtpl->parse( 'main.position' );

	}
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeEditPostForm( $dataContent )
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeEditPostForm.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );

	if( $dataContent['canSilentEdit'] )
	{
		$postData = $dataContent['post'];
		$threadData = $dataContent['thread'];
		$forumData = $dataContent['forum'];
		$xtpl->assign( 'ACTION', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $postData['post_id'] . '/save-inline', true ) );

		$postData['message'] = htmlspecialchars( nv_editor_br2nl( $postData['message'] ) );

		$postData['message'] = getForumEditor( 'message', $postData['message'], $width = '100%', $height = '300px' );
		$postData['token'] = md5( session_id() . $global_config['sitekey'] . $postData['post_id'] );
		$xtpl->assign( 'POST', $postData );

		$xtpl->assign( 'USER', $user_info );
		$xtpl->parse( 'main.canSilentEdit' );

	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeDeleteThreadForm( $dataContent )
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeDeleteThreadForm.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'ACTION', nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=content', true ) );

	$threadData = $dataContent['thread'];
	
	$threadData['token'] = md5( session_id() . $global_config['sitekey'] . $threadData['thread_id'] );
	
	$xtpl->assign( 'THREAD', $threadData );
 
	if( $dataContent['canHardDelete'] )
	{
 
		$xtpl->parse( 'main.canHardDelete' );

	}else{
		$xtpl->parse( 'main.canSoftDelete' );
		
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeUpdatePostContent( $dataContent, $post_id )
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeUpdatePostContent.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'ACTION', $client_info['selfurl'] );

	$threadData = $dataContent['thread'];
	$forumdData = $dataContent['forum'];
	$postsData = $dataContent['posts'];

	$postsData = $postsData[$post_id];

	if( stripos( $postsData['message'], '&#91;/ATTACH&#93;' ) !== false )
	{
		if( preg_match_all( '#&\#91;ATTACH(=[^&\#93;]*)?&\#93;(?P<id>\d+)(\D.*)?&\#91;/ATTACH&\#93;#i', $postsData['message'], $matches ) )
		{
			$new_attachment = array();

			foreach( $matches['id'] as $key => $attachId )
			{
				if( isset( $postsData['attachments'][$attachId] ) )
				{
					$_attach = $postsData['attachments'][$attachId];

					if( stripos( $matches[1][$key], 'full' ) !== false )
					{
						$replace = '<a rel="image" href="' . $_attach['contentLink'] . '" data-image-count="' . $_attach['attachment_id'] . '"><img src="' . $_attach['contentLink'] . '" class="attach"  /></a>';
					}
					else
					{
						$replace = '<img src="' . $_attach['thumbnailUrl'] . '" class="attach" data-image-count="' . $_attach['attachment_id'] . '" />';
					}

					$new_attachment[] = array(
						'post_id' => $postsData['post_id'],
						'attachment_id' => $attachId,
						'search' => $matches[0][$key],
						'replace' => $replace );

					unset( $postsData['attachments'][$attachId] );

				}
 	
			}
			foreach( $new_attachment as $value )
			{
				$postsData['message'] = str_replace( $value['search'], $value['replace'], $postsData['message'] );

			}

		}
		unset( $matches );
	}
	$postsData['message'] = preg_replace( '@<p class="closequote">(.*?)</p>@siu', '$1', $postsData['message'] );
	$postsData['message'] = preg_replace( "#&\#91;quote=&quot;(.*?), post: (.*?), member:(.*?)&\#93;(.*?)&\#91;/quote&\#93;#is", '<div class="bbCodeBlock bbCodeQuote" data-author="$1"><aside><div class="attribution type">$1 đã viết:<a href="#post-$2" class="AttributionLink">↑</a></div><blockquote class="quoteContainer"><div class="quote">$4</div><div class="quoteExpand">Xem chi tiết...</div></blockquote></aside></div>', $postsData['message'] );

	if( ! empty( $postsData['photo'] ) && file_exists( NV_ROOTDIR . '/' . $postsData['photo'] ) )
	{
		$postsData['photo'] = NV_BASE_SITEURL . $postsData['photo'];
	}
	elseif( is_file( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png' ) )
	{
		$postsData['photo'] = NV_BASE_SITEURL . 'themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png';
	}
	else
	{
		$postsData['photo'] = NV_BASE_SITEURL . 'themes/default/images/users/no_avatar.png';
	}

	$postsData['last_time'] = ( $postsData['last_edit_date'] ) ? $postsData['last_edit_date'] : 0;
	$postsData['last_edit_date'] = ( $postsData['last_edit_date'] ) ? nv_date( 'd/m/Y H:i', $postsData['last_edit_date'] ) : 0;

	$postsData['post_date'] = nv_date( 'd/m/Y H:i', $postsData['post_date'] );
	$postsData['staff'] = ( $postsData['is_staff'] ) ? 'staff' : '';

	$postsData['message'] = preg_replace( '@<p class="closequote">(.*?)</p>@siu', '$1', $postsData['message'] );
	$postsData['message'] = preg_replace( "#\[quote=&quot;(.*?), post: (.*?), member:(.*?)\](.*?)\[/quote\]#is", '<div class="bbCodeBlock bbCodeQuote" data-author="$1"><aside><div class="attribution type">$1 đã viết:<a href="#post-$2" class="AttributionLink">↑</a></div><blockquote class="quoteContainer"><div class="quote">$4</div><div class="quoteExpand">Xem chi tiết...</div></blockquote></aside></div>', $postsData['message'] );

	$postsData['token'] = md5( session_id() . $global_config['sitekey'] . $postsData['post_id'] );
	$postsData['post_edit_inline'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $postsData['post_id'] . '/edit-inline';
	$postsData['user_edit'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $postsData['post_id'] . '/edit';
	$postsData['user_delete'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $postsData['post_id'] . '/delete';
	$postsData['user_ip'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $postsData['post_id'] . '/ip';
	$postsData['user_report'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $postsData['post_id'] . '/report';
	$postsData['user_warn'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $postsData['post_id'] . '/warn';
	$postsData['user_quote'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $postsData['post_id'] . '/quote';
	$postsData['user_like'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $postsData['post_id'] . '/like';
	$postsData['user_location_quote'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . strtolower( change_alias( $threadData['title'] ) ) . '-' . $postsData['thread_id'] . '/reply&quote=' . $postsData['post_id'];

	$xtpl->assign( 'POST', $postsData );

	$userGroup = nv_user_groups( $postsData['user_group_id'] );

	if( $userGroup )
	{
		$xtpl->assign( 'GROUP', $userGroup[$postsData['user_group_id']] );
		$xtpl->parse( 'main.getgroup' );
	}

	if( $postsData['last_edit_date'] )
	{
		if( $postsData['userid'] == $postsData['last_edit_user_id'] )
		{
			$xtpl->parse( 'main.editDate.byUser' );
		}
		else
		{
			$xtpl->parse( 'main.editDate.byModerator' );
		}

		$xtpl->parse( 'main.editDate' );
	}

	if( isset( $postsData['attachments'] ) )
	{

		foreach( $postsData['attachments'] as $attachment_id => $attachment )
		{
			$attachment['file_size'] = nv_convertfromBytes( $attachment['file_size'] );

			$xtpl->assign( 'ATTACHMENT', $attachment );

			if( $attachment['thumbnailUrl'] and $dataContent['canViewAttachments'] )
			{
				$xtpl->parse( 'main.attachment.loop.viewimage' );

			}
			elseif( $attachment['thumbnailUrl'] )
			{
				$xtpl->parse( 'main.attachment.loop.clickimage' );

			}
			else
			{

				$xtpl->parse( 'main.attachment.loop.clickfile' );
			}

			$xtpl->parse( 'main.attachment.loop' );
		}
		$xtpl->parse( 'main.attachment' );
	}

	if( $postsData['first_name'] && $postsData['last_name'] )
	{
		$xtpl->assign( 'fullName', nv_show_name_user( $postsData['first_name'], $postsData['last_name'], $postsData['username'] ) );
		$xtpl->parse( 'main.fullName' );
	}

	if( $postsData['isOnline'] )
	{
		$xtpl->parse( 'main.isOnline' );
	}
	if( $postsData['is_staff'] )
	{
		$xtpl->parse( 'main.isStaff' );
	}
	if( $postsData['canEdit'] )
	{
		$xtpl->parse( 'main.canEdit' );
	}
	if( $postsData['canDelete'] )
	{
		$xtpl->parse( 'main.canDelete' );
	}
	if( $postsData['canReport'] )
	{
		$xtpl->parse( 'main.canReport' );
	}
	if( $postsData['canViewHistory'] )
	{
		$xtpl->parse( 'main.canViewHistory' );
	}
	if( $postsData['canWarn'] )
	{
		$xtpl->parse( 'main.canWarn' );
	}

	if( $postsData['canLike'] )
	{
		$xtpl->parse( 'main.canLike' );
	}

	if( $postsData['isNew'] )
	{
		$xtpl->parse( 'main.isNew' );
	}

	if( $dataContent['canViewIps'] )
	{
		$xtpl->parse( 'main.canViewIps' );
	}

	if( $dataContent['canReply'] )
	{
		$xtpl->parse( 'main.canReply' );
	}

	// cau hinh message
	$guestShowSignatures = 1; // 0 là tắt hiển thị chữ ký

	if( $guestShowSignatures )
	{
		$xtpl->parse( 'main.signature' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeReplyPostContent( $dataContent )
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeReplyPostContent.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'ACTION', $client_info['selfurl'] );

	$threadData = $dataContent['thread'];
	$forumdData = $dataContent['forum'];
	$postsData = $dataContent['posts'];

	$lastPost = $dataContent['lastPost'];

	if( stripos( $lastPost['message'], '&#91;/ATTACH&#93;' ) !== false )
	{
		if( preg_match_all( '#&\#91;ATTACH(=[^&\#93;]*)?&\#93;(?P<id>\d+)(\D.*)?&\#91;/ATTACH&\#93;#i', $lastPost['message'], $matches ) )
		{
			$new_attachment = array();

			foreach( $matches['id'] as $key => $attachId )
			{
				if( isset( $lastPost['attachments'][$attachId] ) )
				{
 
					$_attach = $lastPost['attachments'][$attachId];

					if( stripos( $matches[1][$key], 'full' ) !== false )
					{
						$replace = '<a rel="image" href="' . $_attach['contentLink'] . '" data-image-count="' . $_attach['attachment_id'] . '"><img src="' . $_attach['contentLink'] . '" class="attach"  /></a>';
					}
					else
					{
						$replace = '<img src="' . $_attach['thumbnailUrl'] . '" class="attach" data-image-count="' . $_attach['attachment_id'] . '" />';
					}

					$new_attachment[] = array(
						'post_id' => $lastPost['post_id'],
						'attachment_id' => $attachId,
						'search' => $matches[0][$key],
						'replace' => $replace );

					unset( $lastPost['attachments'][$attachId] );
				}
			}
			foreach( $new_attachment as $value )
			{
				$lastPost['message'] = str_replace( $value['search'], $value['replace'], $lastPost['message'] );

			}

		}
		unset( $matches );
	}
	//$lastPost['message'] = preg_replace( '@<p class="closequote">(.*?)</p>@siu', '$1', $lastPost['message'] );
	$lastPost['message'] = preg_replace( "#&\#91;quote=&quot;(.*?), post: (.*?), member:(.*?)&\#93;(.*?)&\#91;/quote&\#93;#is", '<div class="bbCodeBlock bbCodeQuote" data-author="$1"><aside><div class="attribution type">$1 đã viết:<a href="#post-$2" class="AttributionLink">↑</a></div><blockquote class="quoteContainer"><div class="quote">$4</div><div class="quoteExpand">Xem chi tiết...</div></blockquote></aside></div>', $lastPost['message'] );
	$lastPost['message'] = preg_replace( "#<p><br></p>#", '', $lastPost['message'] );

	if( ! empty( $lastPost['photo'] ) && file_exists( NV_ROOTDIR . '/' . $lastPost['photo'] ) )
	{
		$lastPost['photo'] = NV_BASE_SITEURL . $lastPost['photo'];
	}
	elseif( is_file( NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png' ) )
	{
		$lastPost['photo'] = NV_BASE_SITEURL . 'themes/' . $global_config['module_theme'] . '/images/users/no_avatar.png';
	}
	else
	{
		$lastPost['photo'] = NV_BASE_SITEURL . 'themes/default/images/users/no_avatar.png';
	}

	$lastPost['post_date'] = nv_date( 'd/m/Y H:i', $lastPost['post_date'] );
	$lastPost['staff'] = ( $lastPost['is_staff'] ) ? 'staff' : '';
 
	$lastPost['token'] = md5( session_id() . $global_config['sitekey'] . $lastPost['post_id'] );
	$lastPost['post_edit_inline'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $lastPost['post_id'] . '/edit-inline';
	$lastPost['user_edit'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $lastPost['post_id'] . '/edit';
	$lastPost['user_delete'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $lastPost['post_id'] . '/delete';
	$lastPost['user_ip'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $lastPost['post_id'] . '/ip';
	$lastPost['user_report'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $lastPost['post_id'] . '/report';
	$lastPost['user_warn'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $lastPost['post_id'] . '/warn';
	$lastPost['user_quote'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $lastPost['post_id'] . '/quote';
	$lastPost['user_like'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $lastPost['post_id'] . '/like';
	$lastPost['user_location_quote'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . strtolower( change_alias( $threadData['title'] ) ) . '-' . $lastPost['thread_id'] . '/reply&quote=' . $lastPost['post_id'];

	$xtpl->assign( 'POST', $lastPost );

	$userGroup = nv_user_groups( $lastPost['user_group_id'] );

	if( $userGroup )
	{
		$xtpl->assign( 'GROUP', $userGroup[$lastPost['user_group_id']] );
		$xtpl->parse( 'main.getgroup' );
	}

	if( isset( $lastPost['attachments'] ) )
	{

		foreach( $lastPost['attachments'] as $attachment_id => $attachment )
		{
			$attachment['file_size'] = nv_convertfromBytes( $attachment['file_size'] );

			$xtpl->assign( 'ATTACHMENT', $attachment );

			if( $attachment['thumbnailUrl'] and $dataContent['canViewAttachments'] )
			{
				$xtpl->parse( 'main.attachment.loop.viewimage' );

			}
			elseif( $attachment['thumbnailUrl'] )
			{
				$xtpl->parse( 'main.attachment.loop.clickimage' );

			}
			else
			{

				$xtpl->parse( 'main.attachment.loop.clickfile' );
			}

			$xtpl->parse( 'main.attachment.loop' );
		}
		$xtpl->parse( 'main.attachment' );
	}

	if( $lastPost['first_name'] && $lastPost['last_name'] )
	{
		$xtpl->assign( 'fullName', nv_show_name_user( $lastPost['first_name'], $lastPost['last_name'], $lastPost['username'] ) );
		$xtpl->parse( 'main.fullName' );
	}

	if( $lastPost['isOnline'] )
	{
		$xtpl->parse( 'main.isOnline' );
	}
	if( $lastPost['is_staff'] )
	{
		$xtpl->parse( 'main.isStaff' );
	}
	if( $lastPost['canEdit'] )
	{
		$xtpl->parse( 'main.canEdit' );
	}
	if( $lastPost['canDelete'] )
	{
		$xtpl->parse( 'main.canDelete' );
	}
	if( $lastPost['canReport'] )
	{
		$xtpl->parse( 'main.canReport' );
	}
	if( $lastPost['canViewHistory'] )
	{
		$xtpl->parse( 'main.canViewHistory' );
	}
	if( $lastPost['canWarn'] )
	{
		$xtpl->parse( 'main.canWarn' );
	}

	if( $lastPost['canLike'] )
	{
		$xtpl->parse( 'main.canLike' );
	}

	if( $lastPost['isNew'] )
	{
		$xtpl->parse( 'main.isNew' );
	}

	if( $dataContent['canViewIps'] )
	{
		$xtpl->parse( 'main.canViewIps' );
	}

	if( $dataContent['canReply'] )
	{
		$xtpl->parse( 'main.canReply' );
	}

	// cau hinh message
	$guestShowSignatures = 1; // 0 là tắt hiển thị chữ ký

	if( $guestShowSignatures )
	{
		$xtpl->parse( 'main.signature' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}
 
function ThemeEditThreadForm( $dataContent )
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeEditThreadForm.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'USER', $user_info );
	$xtpl->assign( 'ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=content' );

	$threadData = $dataContent['thread'];
	$threadData['token'] = md5( session_id() . $global_config['sitekey'] . $threadData['thread_id'] );
	$xtpl->assign( 'THREAD', $threadData );
	
	//$forumdData = $dataContent['forum'];
	
	if( $dataContent['canLockUnlockThread'] )
	{
		$xtpl->assign( 'OPEN_CHECKED', ( $threadData['discussion_open'] ) ? 'checked="checked"' : '' );
		$xtpl->parse( 'main.canLockUnlockThread' );
	}
	if( $dataContent['canStickUnstickThread'] )
	{
		$xtpl->assign( 'STICKY_CHECKED', ( $threadData['sticky'] ) ? 'checked="checked"' : '' );
		$xtpl->parse( 'main.canStickUnstickThread' );
	}
	
	
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}
 
function ThemeEditThreadTitleForm( $dataContent )
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeEditThreadTitleForm.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'USER', $user_info );
	$xtpl->assign( 'ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=content' );

	$threadData = $dataContent['thread'];
	$threadData['token'] = md5( session_id() . $global_config['sitekey'] . $threadData['thread_id'] );
	$xtpl->assign( 'THREAD', $threadData );
	
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}
 
function ThemeResponseNoPermission()
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeResponseNoPermission.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeResponseRedirect()
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeResponseRedirect.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeErrorNotFoundForum()
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeErrorNotFoundForum.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeErrorNotFoundThread()
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeErrorNotFoundThread.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeErrorNotFoundPost()
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeErrorNotFoundPost.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeErrorNotFoundAttachment()
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeErrorNotFoundAttachment.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeErrorOrNoPermission( $errorLangkey )
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeErrorOrNoPermission.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );

	$xtpl->assign( 'ERROR', ( isset( $lang_module[$errorLangkey] ) ) ? $lang_module[$errorLangkey] : $errorLangkey );

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeErrorPermission( $permissionName = '' )
{
	global $lang_global, $forum_node, $global_config, $userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeErrorPermission.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';

}

function ThemeErrorRequest()
{
	global $lang_global, $forum_node, $global_config, $userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeErrorRequest.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';

}

function ThemeForumLogin()
{
	global $lang_global, $forum_node, $global_config, $userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeForumLogin.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';

}
