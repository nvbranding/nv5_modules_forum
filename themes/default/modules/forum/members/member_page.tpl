<!-- BEGIN: main -->

<div class="profilePage" itemscope="itemscope" itemtype="http://data-vocabulary.org/Person">
  <div class="mast">
    <div class="avatarScaler"> <a class="OverlayTrigger" href="#"> <img src="{DATA.photo}" alt="{DATA.username}" itemprop="photo"> </a> </div>
    <div class="section infoBlock">
      <div class="secondaryContent pairsJustified">
        <dl>
          <dt>Hoạt động cuối:</dt>
          <dd><abbr class="DateTime" title="{DATA.last_time} lúc {DATA.last_hour}">{DATA.last_login}</abbr></dd>
        </dl>
        <dl>
          <dt>Tham gia ngày:</dt>
          <dd>{DATA.regdate}</dd>
        </dl>
        <dl>
          <dt>Bài viết:</dt>
          <dd>{DATA.message_count}</dd>
        </dl>
        <dl>
          <dt>Đã được thích:</dt>
          <dd>{DATA.like_count}</dd>
        </dl>
        <dl>
          <dt>Điểm thành tích:</dt>
          <dd><a href="#" class="OverlayTrigger">0</a></dd>
        </dl>
      </div>
    </div>
    <div class="followBlocks">
      <div class="section">
        <h3 class="subHeading textWithCount" title="{DATA.username} đang theo đuôi 83 thành viên."> <span class="text">Bạn đang theo đuôi</span> <a href="#" class="count OverlayTrigger">83</a> </h3>
        <div class="primaryContent avatarHeap">
			<img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/theodoi.png">
          <!-- <ol>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#488/488427.jpg?1394796963')">hungcuongcfo</span></a> </li>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#454/454399.jpg?1384090697')">meocaibang</span></a> </li>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#395/395454.jpg?1384090133')">FromCambodia</span></a> </li>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#413/413564.jpg?1384090347')">vtczone</span></a> </li>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#403/403083.jpg?1387553043')">suggar</span></a> </li>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#125/125340.jpg?1384088294')">Neu_boy</span></a> </li>
          </ol> -->
        </div>
        <div class="sectionFooter"><a href="#" class="OverlayTrigger">Xem tất cả</a></div>
      </div>
      <div class="section">
        <h3 class="subHeading textWithCount" title="49 thành viên đang theo đuôi {DATA.username}."> <span class="text">Người theo đuôi bạn</span> <a href="#" class="count OverlayTrigger">49</a> </h3>
        <div class="primaryContent avatarHeap">
			<img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/theodoi.png">
          <!-- <ol>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#495/495524.jpg?1390313731')">Jonny Nguyen</span></a> </li>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#491/491027.jpg?1384091015')">hongthatcong11</span></a> </li>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#498/498908.jpg?1396184081')">kimbang</span></a> </li>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#491/491987.jpg?1384091161')">orenburg</span></a> </li>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#490/490017.jpg?1391844428')">danhmat</span></a> </li>
            <li> <a href="#" class="avatar Tooltip" itemprop="contact" data-avatarhtml="true"><span class="img s" style="background-image: url('#493/493030.jpg?1384091161')">HOANG-MINH</span></a> </li>
          </ol> -->
        </div>
        <div class="sectionFooter"><a href="#" class="OverlayTrigger">Xem tất cả</a></div>
      </div>
    </div>
    <div class="section infoBlock sharePage">
      <div class="secondaryContent">
        <h3>Chia sẻ trang này</h3>
        <div class="tweet shareControl">
          <iframe id="twitter-widget-0" scrolling="no" frameborder="0" allowtransparency="true" src="http://platform.twitter.com/widgets/tweet_button.1395870373.html#_=1396433625267&amp;count=horizontal&amp;id=twitter-widget-0&amp;lang=en&amp;original_referer={DATA.page_user}&amp;size=m&amp;text={DATA.username}%20%7C%20Di%E1%BB%85n%20%C4%91%C3%A0n%20F319.com&amp;url=http%3A%2F%2Ff319.com%2Fmembers%2F{DATA.username}.498854%2F" class="twitter-share-button twitter-tweet-button twitter-count-horizontal" title="Twitter Tweet Button" data-twttr-rendered="true" style="width: 110px; height: 20px;"></iframe>
        </div>
        <div class="facebookLike shareControl">
          <fb:like href="{DATA.page_user}" layout="button_count" action="recommend" font="trebuchet ms" colorscheme="light" class=" fb_iframe_widget" fb-xfbml-state="rendered" fb-iframe-plugin-query="action=recommend&amp;app_id=&amp;color_scheme=light&amp;font=trebuchet%20ms&amp;href=http%3A%2F%2Ff319.com%2Fmembers%2F{DATA.username}.498854%2F&amp;layout=button_count&amp;locale=vi_VN&amp;sdk=joey"><span style="vertical-align: top; width: 0px; height: 0px;">
            <iframe name="f10d08b09" width="1000px" height="1000px" frameborder="0" allowtransparency="true" scrolling="no" title="fb:like Facebook Social Plugin" src="http://www.facebook.com/plugins/like.php?action=recommend&amp;app_id=&amp;channel=http%3A%2F%2Fstatic.ak.facebook.com%2Fconnect%2Fxd_arbiter%2FwTH8U0osOYl.js%3Fversion%3D40%23cb%3Df1a881616%26domain%3Df319.com%26origin%3Dhttp%253A%252F%252Ff319.com%252Ff2cf04a0%26relation%3Dparent.parent&amp;color_scheme=light&amp;font=trebuchet%20ms&amp;href=http%3A%2F%2Ff319.com%2Fmembers%2F{DATA.username}.498854%2F&amp;layout=button_count&amp;locale=vi_VN&amp;sdk=joey" style="border: none; visibility: visible; width: 0px; height: 0px;"></iframe>
            </span></fb:like>
        </div>
      </div>
    </div>
  </div>
  <div class="mainProfileColumn">
    <div class="section primaryUserBlock">
      <div class="mainText secondaryContent">
        <div class="followBlock">
          <ul>
            <li><a href="#" class="OverlayTrigger">Báo vi phạm</a></li>
          </ul>
        </div>
        <h1 itemprop="name" class="username">{DATA.username}</h1>
        <p class="userBlurb"> <span class="userTitle" itemprop="title">{father_hood}</span> </p>
       
	    
		<div class="userBanners"> 
		
		<!-- BEGIN: user --> 
		<em class="userBanner bannerRed " itemprop="title"><span class="before"></span><strong>Not Official</strong><span class="after"></span></em> 
		<!-- END: user -->
		<!-- BEGIN: admin --> 
		<em class="userBanner bannerStaff " itemprop="title"><span class="before"></span><strong>Thành viên ban quản trị</strong><span class="after"></span></em>
		<!-- END: admin -->
		
		</div>
        
		
		<!-- BEGIN: user_page1 --> 
		<dl class="pairsInline lastActivity">
          <dt>{DATA.username} được nhìn thấy lần cuối:</dt>
          <dd> Đang xem hồ sơ thành viên <em><a href="{user_page}">{title}</a></em>, <abbr class="DateTime muted"  title="{ONLINE.last_time} lúc {ONLINE.last_hour}">{ONLINE.last_login}</abbr> </dd>
        </dl>
		<!-- END: user_page1 --> 
		
		<!-- BEGIN: user_page2 --> 
		<dl class="pairsInline lastActivity">
          <dt>{DATA.username} được nhìn thấy lần cuối:</dt>
          <dd> Đang xem chủ đề <em><a href="{user_page}">{title}</a></em>, <abbr class="DateTime muted"  title="{ONLINE.last_time} lúc {ONLINE.last_hour}">{ONLINE.last_login}</abbr> </dd>
        </dl>
		<!-- END: user_page2 -->
		
		<!-- BEGIN: user_page3 --> 
		<dl class="pairsInline lastActivity">
          <dt>{DATA.username} được nhìn thấy lần cuối:</dt>
          <dd> Đang xem chuyên mục <em><a href="{user_page}">{title}</a></em>, <abbr class="DateTime muted"  title="{ONLINE.last_time} lúc {ONLINE.last_hour}">{ONLINE.last_login}</abbr> </dd>
        </dl>
		<!-- END: user_page3 --> 
		
		<!-- BEGIN: user_page4 --> 
		<dl class="pairsInline lastActivity">
          <dt>{DATA.username} được nhìn thấy lần cuối:</dt>
          <dd><abbr class="DateTime muted"  title="{ONLINE.last_time} lúc {ONLINE.last_hour}">{ONLINE.last_login}</abbr> </dd>
        </dl>
		<!-- END: user_page4 --> 
		
      </div>
      <!-- <div class="clear"></div> -->
    </div>
	
	<div id="tabs_member">
	<ul class="tabs mainTabs Tabs">
        <li><a href="#profilePosts" >Tin nhắn hồ sơ</a></li>
        <li><a href="#RecentActivity">Hoạt động gần đây</a></li>
        <li><a href="#postings">Các bài đăng</a></li>
        <li><a href="#info">Thông tin</a></li>
	</ul>
    <ul id="ProfilePanes">
      <li id="profilePosts" class="profileContent">
        <!-- BEGIN: form_comment --> 
		<form action="" Onsubmit="return ProfilePoster('{DATA.profile_user_id}', '{DATA.checkss}')" method="post" class="form-inline messageSimple profilePoster AutoValidator primaryContent" id="ProfilePoster">
          <a href="#" class="avatar"><img src="{DATA.photo}" width="48" height="48" alt="{DATA.username}"></a>
          <div class="messageInfo"><textarea name="message" id="message" class="textCtrl StatusEditor UserTagger Elastic" placeholder="Cập nhật trạng thái..." rows="3" cols="50" maxlength="140"></textarea>
            <div class="submitUnit"> 
				<span id="loading_comment"></span>
				<span id="statusEditorCounter" title="Số ký tự còn lại" class="statusEditorCounter">140</span>
              <input type="submit" class="button primary" value="Đăng" accesskey="s">
			</div>
          </div>
        </form>
		<!-- END: form_comment --> 
		
		<!-- BEGIN: listcomment -->
        <form action="#" id="ProfileComment" method="post" class="form-inline InlineModForm section">
          <ol class="messageSimpleList" id="ProfilePostList">
			<!-- BEGIN: loop -->
			<li id="profile-post-{PROFILE.profile_post_id}" class="primaryContent messageSimple" data-author="{PROFILE.username}">
				<a href="{PROFILE.user_page}" class="avatar Av{PROFILE.user_id}s">
					<img src="{PROFILE.photo}" width="48" height="48" alt="{PROFILE.username}">
				</a>
				<div class="messageInfo">
					<div class="messageContent">
						<a href="{PROFILE.user_page}" class="username poster">{PROFILE.username}</a>
						<article>
							<blockquote class="ugc baseHtml">{PROFILE.message}</blockquote>
						</article>
					</div>
					<div class="messageMeta">
						<div class="privateControls">
							
							<!-- <input type="checkbox" name="profilePosts[]" value="{PROFILE.profile_post_id}" class="InlineModCheck item" data-target="#profile-post-{PROFILE.profile_post_id}"> -->
							<a href="#" title="Permalink" class="item muted"><abbr class="DateTime"  title="{PROFILE.post_date}">{PROFILE.post_date}</abbr></a>
							<!-- BEGIN: UserEdit -->
							<a href="javascript:void(0);" Onclick="EditProfilePost( '{PROFILE.profile_post_id}' );" class="OverlayTrigger item control edit"><span></span>Sửa</a>
							<a href="javascript:void(0);" Onclick="DeleteProfilePost( '{PROFILE.profile_post_id}' );" class="item OverlayTrigger control delete"><span></span>Xóa</a>
							<!-- END: UserEdit -->
							<!-- <a href="#" class="item control deleteSpam OverlayTrigger"><span></span>Spam</a> -->
							<!-- <a href="#" class="item control ip OverlayTrigger"><span></span>IP</a> -->
							<!-- <a href="#" class="item control warn"><span></span>Cảnh báo</a> -->
							<!-- <a href="#" class="OverlayTrigger item control report"><span></span>Báo cáo vi phạm</a> -->
						
						</div>
						<div class="publicControls">
							<!-- <a href="#" class="LikeLink item control like" data-container="#likes-wp-{PROFILE.profile_post_id}"><span></span><span class="LikeLabel">Bỏ thích</span></a> -->
							<!-- BEGIN: CommentUser -->
							<a href="javascript:void(0);" onclick="open_comment('{PROFILE.profile_post_id}')" class="CommentPoster item control postComment"><span></span>Bình luận</a>
							<!-- END: CommentUser -->
						</div>
					</div>
					<ol class="messageResponse">
						<li id="likes-wp-{PROFILE.profile_post_id}"></li>
						
						
						<!-- BEGIN: ShowMore -->
						<li id="commentMore{PROFILE.profile_post_id}" class="commentMore secondaryContent">
							<a href="javascript:void(0);" onclick="loadPreComment('{PROFILE.profile_post_id}', '{PreTime}')" class="CommentLoader">Xem các bình luận trước đó...</a>
						</li>
						<!-- END: ShowMore -->
						
						<!-- BEGIN: loopsub -->
						<li class="comment secondaryContent ">
							<a href="{COMMENT.user_page}" class="avatar Av{COMMENT.user_id}s">
								<img src="{COMMENT.photo}" width="48" height="48" alt="{COMMENT.username}">
							</a>
							<div class="commentInfo">
								<div class="commentContent">
									<a href="{COMMENT.user_page}" class="username poster">{COMMENT.username}</a>
									<article>
										<blockquote>{COMMENT.message}</blockquote>
									</article>
								</div>
								<div class="commentControls">
									<abbr class="DateTime muted" title="{COMMENT.comment_time}">{COMMENT.comment_date}</abbr>
									<!-- BEGIN: UserEdit --><a href="javascript:void(0);"  class="OverlayTrigger item control delete" onclick="DeleteComment('{PROFILE.profile_post_id}','{COMMENT.profile_post_comment_id}')"><span></span>Xóa</a><!-- END: UserEdit -->
		
								</div>
							</div>
						</li>
						<!-- END: loopsub -->
						<li id="commentSubmit-{PROFILE.profile_post_id}" class="comment secondaryContent" style="display:none">
							<a href="{PROFILE.user_page}" class="avatar Av{PROFILE.user_id}s">
								<img src="{PROFILE.photo}" width="48" height="48" alt="{PROFILE.username}">
							</a>
							<div class="elements"><textarea id="message{PROFILE.profile_post_id}" id="message{PROFILE.profile_post_id}" rows="2" class="textCtrl Elastic"></textarea>
								<div class="submit">
									<span id="loading_comment{PROFILE.profile_post_id}"></span><input onclick="ProfileComment( '{DATA.profile_user_id}', '{PROFILE.profile_post_id}' )" type="submit" class="button primary" value="Đăng bình luận">
								</div>
							</div>
						</li>

					</ol>
				</div>
			</li>	
			<!-- END: loop -->		
		  </ol>
		  <!-- BEGIN: generate_page -->
		  <div class="generate_page">
            {GENERATE_PAGE}
          </div>
		  <!-- END: generate_page -->
			<input type="hidden" name="checkss" value="{DATA.checkss}">		  
        	<input type="hidden" name="profile_user_id" value="{DATA.profile_user_id}">		  
        </form>
		<!-- END: listcomment -->
		
		<!-- BEGIN: nocomment -->
        <form action="#" method="post" class="form-inline InlineModForm section">
          <ol class="messageSimpleList" id="ProfilePostList">
            <li id="NoProfilePosts">Hiện tại không có tin nhắn trong hồ sơ của {DATA.username}.</li>
          </ol>
          <div class="pageNavLinkGroup">
            <div class="linkGroup SelectionCountContainer"></div>
            <div class="linkGroup" style="display: none"><a href="javascript:" class="muted JsOnly DisplayIgnoredContent Tooltip">Show Ignored Content</a></div>
          </div>
        </form>
		<!-- END: nocomment -->
		
      </li>
      <li id="RecentActivity" class="profileContent"> <span class="JsOnly">Chức năng này đang được xây dựng...</span> </li>
      <li id="postings" class="profileContent"> <span class="JsOnly">Chức năng này đang được xây dựng...</span> </li>
      <li id="info" class="profileContent">
        <div class="section">
          <h3 class="textHeading">Tương tác</h3>
          <div class="primaryContent">
            <div class="pairsColumns contactInfo">
              <dl>
                <dt>Nội dung:</dt>
                <dd>
                  <ul>
                    <li><a href="#" rel="nofollow">Tìm tất cả nội dung bởi {DATA.username}</a></li>
                    <li><a href="#" rel="nofollow">Tìm tất cả chủ đề bởi {DATA.username}</a></li>
                  </ul>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </li>
    </ul>
	
  </div>
  </div>
</div><div class="clear"></div>
<script type="text/javascript">
$(function () {
    $('#message').keyup(function (e) {
        var maxLength = 140;
        var textlength = this.value.length;
        if (textlength >= maxLength) {
            this.value = this.value.substring(0, maxLength);
            e.preventDefault();
        } else {
            $('#statusEditorCounter').html((maxLength - textlength));
        }
    });
});
</script>
<!-- END: main -->