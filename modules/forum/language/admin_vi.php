<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 tdweb.vn All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate  10, 5, 2013 13:10
 */

 
if ( ! defined( 'NV_MAINFILE' ) )
{
    die( 'Stop!!!' );
}

$lang_translator['author'] = 'VINADES.,JSC (contact@vinades.vn)';
$lang_translator['createdate'] = '04/03/2010, 15:22';
$lang_translator['copyright'] = '@Copyright (C) 2010 VINADES.,JSC. All rights reserved';
$lang_translator['info'] = '';
$lang_translator['langtype'] = 'lang_module';

$lang_module['main'] = 'main';
$lang_module['config'] = 'Cấu hình';
$lang_module['save'] = 'Lưu lại';
$lang_module['alias'] = 'Liên kết tĩnh';
$lang_module['enable'] = 'Kích hoạt';
$lang_module['disabled'] = 'Không kích hoạt';
$lang_module['cancel'] = 'Hủy';
$lang_module['add_new'] = 'Thêm mới';
$lang_module['action'] = 'Thao tác';

$lang_module['node'] = 'Quản lý danh mục';
$lang_module['node_list']='Danh sách chuyên mục';
$lang_module['node_add']='Thêm mục mới';
$lang_module['node_edit']='Sửa chuyên mục';
$lang_module['node_title']='Tiêu đề';
$lang_module['node_alias']='Liên kết tĩnh';
$lang_module['node_password']='Mật khẩu chuyên mục';
$lang_module['node_image']='Biểu tượng';
$lang_module['node_type']='Nhóm chuyên mục';
$lang_module['node_group']='Nhóm chuyên mục';
$lang_module['node_forum']='Chuyên mục';
$lang_module['node_link']='Chuyên mục liên kết';
$lang_module['node_page']='Trang liên kết';
$lang_module['node_rules_link']='Liên kết đến nội quy chuyên mục';
$lang_module['node_rules']='Nội quy chuyên mục';
$lang_module['node_show_status']='Trạng thái kích hoạt';
$lang_module['node_status']='Trạng thái';
$lang_module['node_description']='Mô tả';
$lang_module['node_sub_sl']='Là mục chính';
$lang_module['node_post']='Bài viết';
$lang_module['node_thread']='Chủ đề';

 
$lang_module['node_create_linkforum']='Tạo liên kết diễn đàn mới';
$lang_module['node_create_page']='Tạo trang mới';
$lang_module['node_search']='Nhập từ khóa tìm kiếm';
$lang_module['node_error_title']='Cảnh báo: Tên chủ đề không được để trống';
$lang_module['node_error_warning']='Cảnh báo: Hãy kiểm tra các trường thông báo lỗi';
$lang_module['node_error_save']='Cảnh báo: Không cập nhật được nội dung. Tên chủ đề có thể bị trùng';
$lang_module['node_error_security']='Cảnh báo: Lỗi bảo mật thao tác của bạn đã bị dừng lại';
$lang_module['node_delete_success']='Xóa chủ đề thành công';
 

$lang_module['node_create_category']='Tạo danh mục mới'; 
$lang_module['node_edit_category']='Sửa danh mục';
$lang_module['node_category_title']='Tiêu đề';
$lang_module['node_category_description']='Mô tả';
$lang_module['node_category_weight']='Thứ tự hiển thị';
$lang_module['node_category_status']='Trạng thái hiển thị';
$lang_module['node_error_category_title']='Tên danh mục chưa nhập';
 
 
$lang_module['node_create_forum']='Tạo diễn đàn mới'; 
$lang_module['node_edit_forum']='Sửa diễn đàn';
$lang_module['node_forum_title']='Tiêu đề';
$lang_module['node_forum_alias']='Liên kết tĩnh';
$lang_module['node_forum_description']='Mô tả';
$lang_module['node_forum_password']='Mật khẩu diễn đàn';
$lang_module['node_forum_weight']='Thứ tự hiển thị';
$lang_module['node_forum_status']='Trạng thái hiển thị';
$lang_module['node_error_forum_title']='Tên diễn đàn chưa nhập';


/* moderators */
$lang_module['moderators']='Điều hành viên';
$lang_module['moderators_add']='Thêm điều hành viên';





$lang_module['general_view']='View Forum';
$lang_module['general_viewNode']='View Node';
$lang_module['general_viewIps']='View IP addresses';
$lang_module['general_viewMemberList']='View Member List';
$lang_module['general_bypassUserPrivacy']='Bypass user privacy';
$lang_module['general_cleanSpam']='Use the spam cleaner';
$lang_module['general_viewWarning']='View warning details';
$lang_module['general_warn']=' Give users warnings directly';
$lang_module['general_manageWarning']='Delete all warnings';
$lang_module['general_editBasicProfile']='Edit basic user profiles';

$lang_module['profilePost_editAny']='Edit profile posts by anyone';
$lang_module['profilePost_deleteAny']='Delete profile posts by anyone';
$lang_module['profilePost_hardDeleteAny']='Hard-delete profile posts by anyone';
$lang_module['profilePost_warn']='Give warnings on profile posts';
$lang_module['profilePost_viewDeleted']='View deleted profile posts';
$lang_module['profilePost_viewModerated']='View moderated profile post';
$lang_module['profilePost_undelete']='Undelete profile posts';
$lang_module['profilePost_approveUnapprove']='Approve / unapprove profile posts';

$lang_module['conversation_editAnyPost']='Edit post by anyone';
$lang_module['conversation_alwaysInvite']='Always invite participants to conversations';

$lang_module['forum_stickUnstickThread']='Stick / unstick thread';
$lang_module['forum_lockUnlockThread']='Lock / unlock threads';
$lang_module['forum_deleteAnyThread']='Delete thread by anyone';
$lang_module['forum_hardDeleteAnyThread']='Hard-delete thread by anyone';
$lang_module['forum_threadReplyBan']='Ban users from replying to a thread';
$lang_module['forum_editAnyPost']='Edit post by anyone';
$lang_module['forum_deleteAnyPost']='Delete post by anyone';
$lang_module['forum_hardDeleteAnyPost']='Hard-delete post by anyone';
$lang_module['forum_manageAnyThread']='Manage (move, merge, etc.) thread by anyone';
$lang_module['forum_warn']='Give warnings on posts';
$lang_module['forum_viewDeleted']='View deleted threads / posts';
$lang_module['forum_viewModerated']='View moderated threads / posts';
$lang_module['forum_undelete']='Undelete threads / posts';
$lang_module['forum_approveUnapprove']='Approve / unapprove threads / posts';
$lang_module['forum_manageAnyTag']='Manage tags by anyone';





	
	
 


$lang_module['forumPermissions_viewOthers']='View threads by others	';
$lang_module['forumPermissions_viewContent']='View thread content';
$lang_module['forumPermissions_like']='Like posts';
$lang_module['forumPermissions_postThread']='Post new thread';
$lang_module['forumPermissions_postReply']='Post replies';
$lang_module['forumPermissions_editOwnPost']='Edit post by self';
$lang_module['forumPermissions_deleteOwnPost']='Delete post by self';
$lang_module['forumPermissions_editOwnPostTimeLimit']='Time limit on editing/deleting own posts (minutes)';
$lang_module['forumPermissions_editOwnThreadTitle']='Edit thread title by self (requires edit own post)';
$lang_module['forumPermissions_deleteOwnThread']='Delete thread by self';
$lang_module['forumPermissions_viewAttachment']='View attachments to posts';
$lang_module['forumPermissions_uploadAttachment']='Upload attachments to posts';
$lang_module['forumPermissions_tagOwnThread']='Tag thread by self';
$lang_module['forumPermissions_tagAnyThread']='Tag thread by anyone';
$lang_module['forumPermissions_manageOthersTagsOwnThread']='Manage tags by others in own thread';
$lang_module['forumPermissions_votePoll']='Vote on polls';
$lang_module['forumPermissions_keepOutLink'] = 'Keep out link';
$lang_module['forumPermissions_keepOutTextLink'] = 'Keep out text link';
$lang_module['forumModeratorPermissions_stickUnstickThread']='Stick / unstick thread';
$lang_module['forumModeratorPermissions_lockUnlockThread']='Lock / unlock threads';
$lang_module['forumModeratorPermissions_manageAnyThread']='Manage (move, merge, etc.) thread by anyone';
$lang_module['forumModeratorPermissions_deleteAnyThread']='Delete thread by anyone';
$lang_module['forumModeratorPermissions_hardDeleteAnyThread']='Hard-delete thread by anyone';
$lang_module['forumModeratorPermissions_threadReplyBan']='Ban users from replying to a thread';
$lang_module['forumModeratorPermissions_editAnyPost']='Edit post by anyone';
$lang_module['forumModeratorPermissions_deleteAnyPost']='Delete post by anyone';
$lang_module['forumModeratorPermissions_hardDeleteAnyPost']='Hard-delete post by anyone';
$lang_module['forumModeratorPermissions_warn']='Give warnings on posts';
$lang_module['forumModeratorPermissions_manageAnyTag']='Manage tags by anyone';
$lang_module['forumModeratorPermissions_viewDeleted']='View deleted threads / posts';
$lang_module['forumModeratorPermissions_viewModerated']='View moderated threads / posts';
$lang_module['forumModeratorPermissions_undelete']='Undelete threads / posts';
$lang_module['forumModeratorPermissions_approveUnapprove']='Approve / unapprove threads / posts';
 

$lang_module['generalModeratorPermissions']='General Moderator Permissions';
$lang_module['forumPermissions']='Forum Permissions';
$lang_module['forumModeratorPermissions']='Forum Moderator Permissions';
$lang_module['conversationModeratorPermissions']='Conversation Moderator Permissions';
$lang_module['profilePostModeratorPermissions']='Profile Post Moderator Permissions';





$lang_module['node_category']='Danh Mục';
$lang_module['node_forum']='Diễn Đàn';
$lang_module['node_linkforum']='Diễn Đàn Liên Kết';
$lang_module['node_page']=' Trang';
 

 
 