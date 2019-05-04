<!-- BEGIN: main -->

<div class="titleBar">
  <h1>Chi tiết cá nhân</h1>
</div>

<!-- main template -->

<div class="container">
  <div class="navigationSideBar ToggleTriggerAnchor">
    <h4 class="heading ToggleTrigger" data-target="> ul"data-toggle-if-pointer="yes">Tài khoản của bạn <span></span></h4>
    <ul data-toggle-class="menuVisible">
      <li class="section">
        <ul>
          <li><a rel='building'class="#primaryContent" href="#account/alerts">Thông báo của bạn</a></li>
          <li><a rel='building'class="#primaryContent" href="#account/news-feed">Bảng tin</a></li>
          <li><a rel='building'class="#primaryContent" href="#account/likes">Bạn đã được 'Thích'</a></li>
          <li><a rel='building' class="#primaryContent" href="#watched/threads">Chủ đề đang theo dõi</a></li>
          <li><a rel='building' class="#primaryContent" href="#watched/forums">Diễn đàn đang theo dõi</a></li>
        </ul>
      </li>
      <li class="section">
        <h4 class="subHeading">Hộp thư</h4>
        <ul>
          <li><a rel='building' class="#primaryContent" href="#conversations/">Xem tin nhắn</a></li>
          <li><a rel='building' class="#primaryContent" href="#conversations/add">Gửi tin nhắn</a></li>
        </ul>
      </li>
       
      <li class="section">
        <h4 class="subHeading">Thiết lập</h4>
        <ul>
          <li><a rel='building' class="secondaryContent" href="#account/personal-details">Chi tiết cá nhân</a></li>
          <li><a rel='building' class="primaryContent" href="#account/signature">Chữ ký</a></li>
          <li><a rel='building' class="primaryContent" href="#account/contact-details">Chi tiết liên hệ</a></li>
          <li><a rel='building' class="primaryContent" href="#account/privacy">Quyền riêng tư</a></li>
          <li><a rel='building' class="primaryContent" href="#account/preferences">Tùy chọn</a></li>
          <li><a rel='building' class="primaryContent" href="#account/alert-preferences">Thiết lập thông báo</a></li>
          <li><a rel='building' class="primaryContent" href="#account/following">Thành viên bạn theo đuôi</a></li>
          <li><a rel='building' class="primaryContent" href="#account/ignored">Danh sách đen</a></li>
          <li><a rel='building' class="primaryContent" href="#account/security">Mật khẩu</a></li>
        </ul>
      </li>
      <li class="section">
        <ul>
          <li><a rel='building' href="#" class="LogOut primaryContent">Thoát</a></li>
        </ul>
      </li>
    </ul>
  </div>
  <div class="mainContentBlock section sectionMain insideSidebar">
		<div id="users">
    <div class="page-header">
		<h3>{LANG.user_info}</h3>
	</div>
	<ul class="list-tab top-option clearfix">
		<li><a href="{URL_HREF}/editinfo">{LANG.editinfo}</a></li>
		<li><a href="{URL_HREF}/changepass">{LANG.changepass_title}</a></li>
		<li><a href="{URL_HREF}/editinfo/changequestion">{LANG.question2}</a></li>
		<!-- BEGIN: allowopenid --><li><a href="{URL_OPENID}">{LANG.openid_administrator}</a></li><!-- END: allowopenid -->
		<!-- BEGIN: regroups --><li><a href="{URL_HREF}regroups">{LANG.in_group}</a></li><!-- END: regroups -->		
		<!-- BEGIN: logout --><li><a href="{URL_HREF}/logout">{LANG.logout_title}</a></li><!-- END: logout -->
	</ul> 		           
    <div class="table box-border-shadow">
		<div class="content-box h-info">
			<div class="left fl">
				<img src="{SRC_IMG}" alt="" class="s-border" />
			</div>
			<div class="fl">
				{LANG.account2}: <strong>{USER.username}</strong> ({USER.email})<br />
				{USER.current_mode}<br />
				{LANG.current_login}: {USER.current_login}<br />
				{LANG.ip}: {USER.current_ip}
			</div>	
			<div class="clear"></div>
		</div>
        <!-- BEGIN: change_login_note -->
        <p>
            <strong>&raquo; {USER.change_name_info}</strong>
        </p>
        <!-- END: change_login_note -->
        <!-- BEGIN: pass_empty_note -->
        <p>
            <strong>&raquo; {USER.pass_empty_note}</strong>
        </p>
        <!-- END: pass_empty_note -->
        <!-- BEGIN: question_empty_note -->
        <p>
            <strong>&raquo; {USER.question_empty_note}</strong>
        </p>
        <!-- END: question_empty_note -->
            <dl class="clearfix">
        	   <dt class="fl">{LANG.name}:</dt>
               <dd class="fl">{USER.full_name}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.birthday}:</dt>
               <dd class="fl">{USER.birthday}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.gender}:</dt>
               <dd class="fl">{USER.gender}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.address}:</dt>
               <dd class="fl">{USER.location}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.website}:</dt>
               <dd class="fl">{USER.website}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.yahoo}:</dt>
               <dd class="fl">{USER.yim}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.phone}:</dt>
               <dd class="fl">{USER.telephone}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.fax}:</dt>
               <dd class="fl">{USER.fax}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.mobile}:</dt>
               <dd class="fl">{USER.mobile}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.showmail}:</dt>
               <dd class="fl">{USER.view_mail}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.regdate}:</dt>
               <dd class="fl">{USER.regdate}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.st_login2}:</dt>
               <dd class="fl">{USER.st_login}</dd>
            </dl>
            <dl class="clearfix">
        	   <dt class="fl">{LANG.last_login}:</dt>
               <dd class="fl">{USER.last_login}</dd>
            </dl>
    </div>
</div>
  </div>
</div>

<!-- END: main -->