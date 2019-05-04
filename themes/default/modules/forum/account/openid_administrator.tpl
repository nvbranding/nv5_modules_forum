<!-- BEGIN: main -->
<div class="titleBar">
  <h1>Chi tiết cá nhân</h1>
</div>
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
		<h3>{LANG.openid_administrator}</h3>
	</div>
			<ul class="list-tab top-option clearfix">
				<li><a href="{URL_HREF}/editinfo">{LANG.editinfo}</a></li>
				<li><a href="{URL_HREF}/changepass">{LANG.changepass_title}</a></li>
				<li><a href="{URL_HREF}/editinfo/changequestion">{LANG.question2}</a></li>
				<li class="ui-tabs-selected"><a href="{URL_HREF}/openid">{LANG.openid_administrator}</a></li>
				<!-- BEGIN: regroups --><li><a href="{URL_HREF}/regroups">{LANG.in_group}</a></li><!-- END: regroups -->
				<!-- BEGIN: logout --><li><a href="{URL_HREF}/logout">{LANG.logout_title}</a></li><!-- END: logout -->
			</ul>
	<div class="box-border-shadow">
		<div class="acenter">
			<img alt="{LANG.openid_administrator}" title="{LANG.openid_administrator}" src="{OPENID_IMG_SRC}" width="{OPENID_IMG_WIDTH}" height="{OPENID_IMG_HEIGHT}" />
		</div>
		<!-- BEGIN: openid_empty -->
		<form class="form-inline" id="openidForm" action="{FORM_ACTION}" method="post">
            <!-- BEGIN: openid_list -->
            <dl class="clearfix{OPENID_CLASS}">
                <dt class="fl">
                    <input name="openid_del[]" type="checkbox" value="{OPENID_LIST.opid}" style="padding-right:5px"{OPENID_LIST.disabled} />
                </dt>
                <dd class="fl">
                    <a href="javascript:void(0);" title="{OPENID_LIST.openid}">{OPENID_LIST.server}</a>
                </dd>
                <dd class="fr">
                    {OPENID_LIST.email}
                </dd>
            </dl>
            <!-- END: openid_list -->
            <input id="submit" type="submit" class="button" value="{LANG.openid_del}" />
		</form>
    <!-- END: openid_empty -->
		<div class="m-bottom acenter">
			<p>
				{DATA.info}
			</p>
			<!-- BEGIN: server -->
				<a href="{OPENID.href}"><img style="margin-left: 10px;margin-right:2px;vertical-align:middle;" alt="{OPENID.title}" title="{OPENID.title}" src="{OPENID.img_src}" width="{OPENID.img_width}" height="{OPENID.img_height}" /> {OPENID.title}</a>
			<!-- END: server -->
		</div>
	</div>
    
</div>
</div>
  </div>
<!-- END: main -->