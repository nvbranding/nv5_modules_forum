<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES., JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES ., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Dec 3, 2010  11:23:15 AM
 */

if( ! defined( 'NV_IS_MOD_FORUM' ) ) die( 'Stop!!!' );

function ThemeForumMain( $forumData, $getNodeDataForListDisplay )
{
	global $forum_node, $generalPermissions, $global_config, $lang_global, $userid, $user_info, $module_upload, $site_mods, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeForumMain.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post' );

	if( ! empty( $forum_node ) && hasContentPermission( $generalPermissions['general'], 'viewNode' ) )
	{
		foreach( $forum_node as $_node_id => $node )
		{
			if( $node['lev'] == 0 && isset( $getNodeDataForListDisplay[$_node_id] ) && hasContentPermission( $getNodeDataForListDisplay[$_node_id], 'view' ) )
			{
				$xtpl->assign( 'NODE', $node );

				if( $node['node_type_id'] == 'category' )
				{
					$xtpl->parse( 'main.node.category' );
				}

				if( ! empty( $node['subcatid'] ) )
				{
					$subcatid = explode( ',', $node['subcatid'] );
					foreach( $subcatid as $sub_node_id )
					{
						if( isset( $forum_node[$sub_node_id] ) && isset( $getNodeDataForListDisplay[$sub_node_id] ) && hasContentPermission( $getNodeDataForListDisplay[$sub_node_id], 'view' ) )
						{

							$sub_node = $forum_node[$sub_node_id];

							$sub_node['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $sub_node['alias'];

							if( isset( $forumData[$sub_node['node_id']] ) )
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
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeForumViewNode( $forumData, $ThreadDataNoneSticky, $ThreadDataSticky, $generate_page )
{
	global $forum_node, $generalPermissions, $nodePermissions, $global_userid, $user_info, $module_upload, $lang_global, $global_config, $site_mods, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeForumViewNode.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post' );

	if( ! empty( $ThreadDataSticky ) )
	{
		foreach( $ThreadDataSticky as $thread )
		{
			$thread_alias = strtolower( change_alias( $thread['title'] ) );
			$thread['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . $thread_alias . '-' . $thread['thread_id'] . $global_config['rewrite_exturl'], true );
			$thread['last_post_link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $thread['last_post_id'] . '/view';
			//thêm bởi nhím
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

			$thread['post_date'] = nv_date( 'd/m/Y H:i', $thread['post_date'] );
			$thread['last_post_date'] = nv_date( 'd/m/Y H:i', $thread['last_post_date'] );
			$xtpl->assign( 'THREAD', $thread );
			$xtpl->parse( 'main.thread_sticky' );

		}

	}
	if( ! empty( $ThreadDataNoneSticky ) )
	{
		foreach( $ThreadDataNoneSticky as $thread )
		{
			$thread_alias = strtolower( change_alias( $thread['title'] ) );
			$thread['link'] = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . $thread_alias . '-' . $thread['thread_id'] . $global_config['rewrite_exturl'], true );
			$thread['last_post_link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $thread['last_post_id'] . '/view';
			$thread['post_date'] = nv_date( 'd/m/Y H:i', $thread['post_date'] );
			$thread['last_post_date'] = nv_date( 'd/m/Y H:i', $thread['last_post_date'] );
			$thread['last_post_username'] = $thread['last_post_username'];

			//thêm bởi nhím
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
			$xtpl->parse( 'main.thread_none_sticky' );

		}

	}

	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'MODULE_FILE', $module_file );
		$xtpl->parse( 'main.generate_page_top' );
		$xtpl->parse( 'main.generate_page_bottom' );
	}
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeForumContent( $data )
{
	global $lang_global, $forum_node, $nodePermissions, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeForumContent.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'ACTION', $client_info['selfurl'] );
	$xtpl->assign( 'NODE', $forum_node[$data['node_id']] );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'ATTACHMENT_HASH', ForumRandomString() );
	$xtpl->assign( 'TOKEN', md5( session_id() . $global_config['sitekey'] . $data['node_id'] ) );

	if( ! empty( $data['tags'] ) )
	{

		foreach( $data['tags'] as $tag )
		{
			$xtpl->assign( 'TAG', $tag );
			$xtpl->parse( 'main.tags' );
		}

	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeForumViewThreads( $dataContent   )
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

	// $node_permission_cache = unserializePermissions( $user_info['global_permission_cache'] );
	// if( hasContentPermission( $node_permission_cache, 'viewContent' ) )
	// {
		// ThemeErrorPermission( 'viewContent' );

	// }

	$threadData['data_time'] = $threadData['post_date'];
	$threadData['post_date'] = nv_date( 'd/m/Y H:i', $threadData['post_date'] );
	$xtpl->assign( 'THREAD', $threadData );

	$xtpl->assign( 'FORUM', $forumData );

	$forum_node[$threadData['node_id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $forum_node[$threadData['node_id']]['alias'];

	$xtpl->assign( 'NODE', $forum_node[$threadData['node_id']] );

	if( $tagData )
	{

		foreach( $tagData as $tag_id => $tag )
		{
			$xtpl->assign( 'TAG', $tag );
			$xtpl->parse( 'main.tag.looptag' );
		}
		$xtpl->parse( 'main.tag' );
	}

	if( ! empty( $postData ) )
	{
		foreach( $postData as $post )
		{

			if( stripos( $post['message'], '[/attach]' ) !== false )
			{
				if( preg_match_all( '#\[attach(=[^\]]*)?\](?P<id>\d+)(\D.*)?\[/attach\]#iU', $post['message'], $matches ) )
				{
					$new_attachment = array();

					foreach( $matches['id'] as $key => $attachId )
					{

						$_attach = isset( $attachmentData[$post['post_id']][$attachId] ) ? $attachmentData[$post['post_id']][$attachId] : array();
						if( $_attach )
						{
							if( stripos( $matches[1][$key], 'full' ) !== false )
							{
								$replace = '<a rel="image" href="' . $_attach['image_full'] . '" data-image-count="' . $_attach['attachment_id'] . '"><img src="' . $_attach['image_full'] . '" class="attach"  /></a>';
							}
							else
							{
								$replace = '<img src="' . $_attach['image_thumb'] . '" class="attach" data-image-count="' . $_attach['attachment_id'] . '" />';
							}

							$new_attachment[] = array(
								'post_id' => $post['post_id'],
								'attachment_id' => $attachId,
								$post['post_id'],
								'search' => $matches[0][$key],
								'replace' => $replace );
						}

					}
					if( ! empty( $new_attachment ) )
					{
						foreach( $new_attachment as $value )
						{
							$post['message'] = str_replace( $value['search'], $value['replace'], $post['message'] );

							unset( $attachmentData[$value['post_id']][$value['attachment_id']] );
						}
					}

				}
				unset( $matches );
			}

			$post['message'] = preg_replace( '@<p class="closequote">(.*?)</p>@siu', '$1', $post['message'] );
			$post['message'] = preg_replace( "#\[quote=&quot;(.*?), post: (.*?), member:(.*?)\](.*?)\[/quote\]#is", '<div class="bbCodeBlock bbCodeQuote" data-author="$1"><aside><div class="attribution type">$1 đã viết:<a href="#post-$2" class="AttributionLink">↑</a></div><blockquote class="quoteContainer"><div class="quote">$4</div><div class="quoteExpand">Xem toàn bộ trích dẫn...</div></blockquote></aside></div>', $post['message'] );

			$post['token'] = md5( session_id() . $global_config['sitekey'] . $post['post_id'] );
			$post['post_date'] = nv_date( 'd/m/Y H:i', $post['post_date'] );
			$post['post_edit_inline'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/edit-inline';
			$post['user_edit'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/edit';
			$post['user_delete'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/delete';
			$post['user_report'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/report';
			$post['user_quote'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/quote';
			$post['user_like'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=post/' . $post['post_id'] . '/like';
			$post['user_location_quote'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=threads/' . strtolower( change_alias( $threadData['title'] ) ) . '-' . $post['thread_id'] . '/reply&quote=' . $post['post_id'];

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

			$post['isOnline'] = null;
			if( array_key_exists( 'last_view_date', $post ) )
			{
				$onlineStatusTimeout = 15; // cau hinh
				$onlineCutOff = NV_CURRENTTIME - $onlineStatusTimeout * 60;
				$post['isOnline'] = ( $post['userid'] == $global_userid || $post['last_view_date'] > $onlineCutOff );

			}
			if( $post['isOnline'] )
			{
				$xtpl->parse( 'main.post.isOnline' );
			}

			$xtpl->assign( 'POST', $post );

			if( $post['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'editOwnPost' ) )
			{
				$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );

				if( ( $editLimit == '-1' || ( ! $editLimit && $post['post_date'] > NV_CURRENTTIME - 60 * $editLimit ) ) && ! empty( $forumData['allow_posting'] ) )
				{
					$xtpl->parse( 'main.post.user_edit' );
				}
			}
			if( $post['userid'] == $global_userid && hasContentPermission( $nodePermissions, 'deleteOwnPost' ) )
			{
				$editLimit = hasContentPermission( $nodePermissions, 'editOwnPostTimeLimit' );

				if( ( $editLimit == -1 || ( ! $editLimit && $post['post_date'] > NV_CURRENTTIME - 60 * $editLimit ) ) && ! empty( $forumData['allow_posting'] ) )
				{
					$xtpl->parse( 'main.post.user_delete' );
				}
			}

			if( hasContentPermission( $generalPermissions['general'], 'report' ) )
			{
				$xtpl->parse( 'main.post.user_report' );
			}

			if( isset( $attachmentData[$post['post_id']] ) && ! empty( $attachmentData[$post['post_id']] ) )
			{
				foreach( $attachmentData[$post['post_id']] as $_attachment_id => $attachment )
				{
					$attachment['file_size'] = nv_convertfromBytes( $attachment['file_size'] );
					$xtpl->assign( 'ATTACHMENT', $attachment );
					$xtpl->parse( 'main.post.attachment.loop' );
				}
				$xtpl->parse( 'main.post.attachment' );
			}
			$checkuser = 0;
			if( $post['likes'] > 0 )
			{
				$post['like_users'] = @unserialize( $post['like_users'] );
				$sizeof = sizeof( $post['like_users'] );

				foreach( $post['like_users'] as $user )
				{
					if( $user['userid'] != $global_userid )
					{
						$xtpl->assign( 'LIKEUSER', $user );
						$xtpl->parse( 'main.post.like_users.loop_user' );
					}
					else
					{

						++$checkuser;
					}

				}

				if( $checkuser == 1 && $sizeof == 1 )
				{
					$xtpl->parse( 'main.post.like_users.oneuser' );

				}
				elseif( $checkuser == 1 && $sizeof > 1 )
				{
					$xtpl->parse( 'main.post.like_users.manyuser' );
				}

				$xtpl->parse( 'main.post.like_users' );

			}

			if( $checkuser )
			{
				$xtpl->assign( 'LANGLIKE', 'Unlike' );
			}
			else
			{
				$xtpl->assign( 'LANGLIKE', 'Like' );
			}

			$canLikePost = ModelLike_canLikePost( $post, $threadData, $forumData, $null, $nodePermissions );

			if( $canLikePost )
			{
				$xtpl->parse( 'main.post.show_button_like' );
			}

			$xtpl->parse( 'main.post' );
		}

		if( $generatePage )
		{
			$xtpl->assign( 'GENERATE_PAGE', $generatePage );
			$xtpl->parse( 'main.generate_page1' );
			$xtpl->parse( 'main.generate_page2' );
		}

	}

	if( isset( $nodePermissions['postReply'] ) && $nodePermissions['postReply'] == true && ! defined( 'NV_IS_USER' ) )
	{
		$xtpl->parse( 'main.guest_post' );

	}
	elseif( ! defined( 'NV_IS_USER' ) )
	{
		$xtpl->parse( 'main.user_login' );

	}
	elseif( ! isset( $nodePermissions['postReply'] ) || ( $nodePermissions['postReply'] == false ) )
	{
		$xtpl->parse( 'main.user_no_permission' );
	}
	else
	{

		$message = getForumEditor( 'message', '', $width = '100%', $height = '150px' );

		$xtpl->assign( 'MESSAGE', $message );
		$xtpl->assign( 'TOKEN', md5( session_id() . $global_config['sitekey'] . $threadData['node_id'] ) );
		$xtpl->assign( 'NODE_ID', $threadData['node_id'] );
		$xtpl->assign( 'ATTACHMENT_HASH', ForumRandomString() );
		$xtpl->parse( 'main.user_post' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );

}

function ThemeGetLikePost( $dataContent, $islike )
{
	global $lang_global, $forum_node, $global_config, $userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeGetLikePost.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	if( $islike )
	{
		$sizeof = sizeof( $dataContent );
		if( $sizeof > 1 )
		{
			$xtpl->parse( 'like.and' );
		}
		if( $dataContent && $sizeof > 1 )
		{
			foreach( $dataContent as $loop )
			{
				$xtpl->assign( 'LOOP', $loop );
				$xtpl->parse( 'like.loop' );
			}

		}

		$xtpl->parse( 'like' );
		$contents = $xtpl->text( 'like' );

	}
	else
	{

		if( $dataContent )
		{
			foreach( $dataContent as $loop )
			{
				$xtpl->assign( 'LOOP', $loop );
				$xtpl->parse( 'unlike.loop' );
			}
			$xtpl->parse( 'unlike' );
			$contents = $xtpl->text( 'unlike' );
		}
		else
		{

			$contents = '';
		}

	}
	return $contents;

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

function ThemeEditPostForm( $postData, $threadData, $forumData )
{
	global $lang_global, $forum_node, $global_config, $global_userid, $user_info, $site_mods, $client_info, $module_name, $module_file, $lang_module, $module_config, $module_info;

	$xtpl = new XTemplate( 'ThemeEditPostForm.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $module_info['template'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'ACTION', $client_info['selfurl'] );

	$postData['message'] = getForumEditor( 'message', $postData['message'], $width = '100%', $height = '300px' );
	$postData['token'] = md5( session_id() . $global_config['sitekey'] . $postData['post_id'] );
	$xtpl->assign( 'POST', $postData );

	$xtpl->assign( 'USER', $user_info );

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function ThemeUpdatePostContent( $dataContent )
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

	$lastPost = $dataContent['lastPost'];

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

	$lastPost['message'] = preg_replace( '@<p class="closequote">(.*?)</p>@siu', '$1', $lastPost['message'] );
	$lastPost['message'] = preg_replace( "#\[quote=&quot;(.*?), post: (.*?), member:(.*?)\](.*?)\[/quote\]#is", '<div class="bbCodeBlock bbCodeQuote" data-author="$1"><aside><div class="attribution type">$1 đã viết:<a href="#post-$2" class="AttributionLink">↑</a></div><blockquote class="quoteContainer"><div class="quote">$4</div><div class="quoteExpand">Click to expand...</div></blockquote></aside></div>', $lastPost['message'] );

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
			
			if( $attachment['thumbnailUrl'] AND $dataContent['canViewAttachments'] )
			{
				$xtpl->parse( 'main.attachment.loop.viewimage' );	
				
			}elseif( $attachment['thumbnailUrl'] )
			{
				$xtpl->parse( 'main.attachment.loop.clickimage' );
				
			}else{
				
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

function ThemeResponseNoPermission( ) 
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
function ThemeResponseRedirect( ) 
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
function ThemeErrorNotFoundThread( ) 
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
