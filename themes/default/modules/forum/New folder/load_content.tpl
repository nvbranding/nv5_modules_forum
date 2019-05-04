<!-- BEGIN: main -->
<div class="previewTooltip">
	<a href="#" class="avatar"><img src="{DATA.user_photo}" style="width:50px"></a></a>
    <div class="text">
        <blockquote class="previewText">{DATA.message}</blockquote>
        <div class="posterDate muted">{DATA.username}, <span class="DateTime faint" title="{DATA.post_date} lúc {DATA.post_time}">{DATA.post_date}</span>
        </div>
    </div>
</div>
<!-- END: main -->

<!-- BEGIN: info_user -->
<div id="memberCard{DATA.userid}" class="memberCard">
  <div class="avatarCropper"> <a class="avatar NoOverlay" href="{DATA.user_page}"> <img id="member-image" src="{DATA.photo}" alt=""> </a> </div>
  <div class="userInfo">
		<h3 class="username"><a href="{DATA.user_page}" class="username NoOverlay">{DATA.username}</a></h3>
		
		<!-- <div class="userTitleBlurb">
			<h4 class="userTitle">Thành viên gắn bó với vietbrokers.vn</h4>
			<div class="userBlurb"><span class="muted">from</span> <a href="#" class="concealed" target="_blank" rel="nofollow">a</a></div>
		</div> -->
		
		<blockquote class="status"></blockquote>

		<div class="userLinks">
		
			<a href="{DATA.user_page}">Trang cá nhân</a>
			
				<a href="#">Gửi tin nhắn</a>
				<a href="#" class="FollowLink Tooltip">Theo đuôi</a>
				<a href="#" class="FollowLink">Thêm vào danh sách đen</a>
			
		
		</div>
		
		<dl class="userStats pairsInline">
		
			<dt>Là thành viên từ:</dt> <dd>{DATA.regdate}</dd>
			<dt>Bài viết:</dt> <dd><a href="#" class="concealed" rel="nofollow">{DATA.post}</a></dd>
			<dt>Đã được thích:</dt> <dd>{DATA.content_user}</dd>

			<!-- <dt>Điểm thành tích:</dt> <dd><a href="#" class="concealed OverlayTrigger">{DATA.like_user}</a></dd> -->

		</dl>
		<dl class="pairsInline lastActivity">
			<dt>{DATA.username} được nhìn thấy lần cuối:</dt>
			<dd>
				<abbr class="DateTime" title="{DATA.last_time} lúc {DATA.last_hour}">{DATA.last_login}</abbr>
			</dd>
		</dl>		
</div><a class="close OverlayCloser"></a> </div>
<!-- END: info_user -->