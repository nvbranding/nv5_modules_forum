<!-- BEGIN: main -->

<li id="profile-post-{DATA.profile_post_id}" class="primaryContent messageSimple" data-author="{DATA.profile_post_id}">
    <a href="{DATA.user_page}" class="avatar Av{DATA.user_id}s" >
        <img src="{DATA.photo}" width="48" height="48" alt="{DATA.username}">
    </a>
    <div class="messageInfo">
        <div class="messageContent">
            <a href="{DATA.user_page}" class="username poster">{DATA.username}</a>
            <article>
                <blockquote class="ugc baseHtml">{DATA.message}</blockquote>
            </article>
        </div>
        <div class="messageMeta">
            <div class="privateControls">
                <!-- <input type="checkbox" name="profilePosts[]" value="{DATA.profile_post_id}" class="InlineModCheck item" data-target="#profile-post-{DATA.profile_post_id}"> -->
                <a href="#" title="Permalink" class="item muted"><abbr class="DateTime" data-time="{DATA.post_date}"  data-timestring="{DATA.last_hour}" title="{DATA.last_time}">{DATA.last_time}</abbr></a>
                <a href="javascript:void(0);" Onclick="EditProfilePost( '{DATA.profile_post_id}' );" class="OverlayTrigger item control edit"><span></span>Sửa</a>
				<a href="javascript:void(0);" Onclick="DeleteProfilePost( '{DATA.profile_post_id}' );" class="item OverlayTrigger control delete"><span></span>Xóa</a>
				<!-- <a href="#" class="item control deleteSpam OverlayTrigger"><span></span>Spam</a> -->
                <!-- <a href="#" class="item control ip OverlayTrigger"><span></span>IP</a> -->
                <!-- <a href="#" class="item control warn"><span></span>Cảnh báo</a> -->
                <!-- <a href="#" class="OverlayTrigger item control report"><span></span>Báo cáo vi phạm</a> -->
            </div>
            <div class="publicControls">
                <!-- <a href="#" class="LikeLink item control like" data-container="#likes-wp-{DATA.profile_post_id}"><span></span><span class="LikeLabel">Thích</span></a> -->
                <a href="javascript:void(0);" onclick="open_comment('{DATA.profile_post_id}')" class="CommentPoster item control postComment"><span></span>Bình luận</a>
            </div>
        </div>
		<ol class="messageResponse">
			<li id="likes-wp-{DATA.profile_post_id}"> </li>
			<li id="commentSubmit-{DATA.profile_post_id}" class="comment secondaryContent" style="display:none">
				<a href="{DATA.user_page}" class="avatar Av{DATA.user_id}s">
					<img src="{DATA.photo}" width="48" height="48" alt="{DATA.username}">
				</a>
				<div class="elements"><textarea name="message{DATA.profile_post_id}" id="message{DATA.profile_post_id}" rows="2" class="textCtrl Elastic"></textarea>
					<div class="submit">
						<input onclick="ProfileComment( '{DATA.profile_user_id}', '{DATA.profile_post_id}' )" type="submit" class="button primary" value="Đăng bình luận">
					</div>
				</div>
			</li>
		</ol>
    </div>
</li>
<!-- END: main -->

<!-- BEGIN: subcomment -->

<!-- BEGIN: loop -->
<li class="comment secondaryContent ">
	<a href="" class="avatar Av{DATA.user_id}s"><img src="{DATA.photo}" width="48" height="48" alt="{DATA.username}"></a>
	<div class="commentInfo">
		<div class="commentContent">
			<a href="{DATA.user_page}" class="username poster">{DATA.username}</a>
			<article><blockquote>{DATA.message}</blockquote></article>
		</div>
		<div class="commentControls">
			<abbr class="DateTime muted" title="{DATA.comment_date}">{DATA.comment_date}</abbr>
			<a href="javascript:void(0);"  class="OverlayTrigger item control delete" onclick="DeleteComment('{DATA.profile_post_id}', '{DATA.profile_post_comment_id}')"><span></span>Xóa</a>
		</div>
	</div>
</li>
<!-- END: loop -->
<!-- END: subcomment --> 

<!-- BEGIN: EditProfilePost --> 
<form action="" id="UpdateProfilePost" method="post" class="form-inline xenForm formOverlay">
<h2 class="heading h1">Sửa tin nhắn hồ sơ bởi {DATA.username}</h2>
	<dl class="ctrlUnit">
		<dt><label for="ctrl_message">Nội dung:</label></dt>
		<dd><textarea name="message" id="ctrl_message" class="textCtrl Elastic" rows="2" style="overflow-y: hidden; height: 38px;">{DATA.message}</textarea></dd>
	</dl>
	<dl class="ctrlUnit submitUnit">
		<dt></dt>
		<dd>
			<input Onclick="UpdateProfilePost( '{DATA.profile_post_id}' );" type="submit" value="Lưu thay đổi" accesskey="s" class="button primary">
			<a href="javascript:void(0);" Onclick="DeleteProfilePost( '{DATA.profile_post_id}' );" class="button OverlayTrigger">Xóa bài viết...</a> 
			<input type="reset" onclick="jQuery.colorbox.close();" class="button OverlayCloser" value="Hủy bỏ">
		</dd>
	</dl>
</form>
<!-- END: EditProfilePost --> 

<!-- BEGIN: RecentActivity --> 
<div class="newsFeed">
  <ol class="eventList">
    <!-- BEGIN: loop -->
	<li id="item_{DATA.profile_post_id}" class="event primaryContent NewsFeedItem" data-author="{DATA.username}"> <a href="{DATA.page_user}" class="avatar Av{DATA.profile_user_id}s icon"><span class="img s" style="background-image: url('{DATA.photo}')"></span></a>
      <div class="content">
        <h3 class="description"> <a href="{DATA.page_user}" class="username primaryText">{DATA.username}</a> <em>{DATA.message}</em> <a href="#">»</a> </h3>
        <abbr class="DateTime" title="{DATA.post_time}">{DATA.post_time}</abbr> </div>
    </li>
	<!-- END: loop -->
  </ol>
  <div class="NewsFeedEnd">
    <div class="sectionFooter"> <a href="#" class="NewsFeedLoader" data-oldestitemid="{Pretime}">Hiển thị mục cũ hơn</a> </div>
  </div>
</div>
<!-- END: RecentActivity -->