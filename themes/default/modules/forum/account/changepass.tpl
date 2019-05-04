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
		<h3>{LANG.change_pass}</h3>
	</div>
	<ul class="list-tab top-option clearfix">
		<li><a href="{URL_HREF}/editinfo">{LANG.editinfo}</a></li>
		<li class="ui-tabs-selected"><a href="{URL_HREF}/changepass">{LANG.changepass_title}</a></li>
		<li><a href="{URL_HREF}/editinfo/changequestion">{LANG.question2}</a></li>
		<!-- BEGIN: allowopenid --><li><a href="{URL_OPENID}">{LANG.openid_administrator}</a></li><!-- END: allowopenid -->
		<!-- BEGIN: regroups --><li><a href="{URL_HREF}/regroups">{LANG.in_group}</a></li><!-- END: regroups -->
		<!-- BEGIN: logout --><li><a href="{URL_HREF}/logout">{LANG.logout_title}</a></li><!-- END: logout -->
	</ul>
	<form id="changePassForm" action="{USER_CHANGEPASS}" method="post" class="form-inline box-border-shadow content-box clearfix">
		<p>{DATA.change_info}</p>
        <!-- BEGIN: passEmpty -->
        <div class="clearfix rows">
            <label>
                {LANG.pass_old}
            </label>
            <input type="password" id="nv_password_iavim" name="nv_password" value="{DATA.nv_password}" class="form-control required password input" maxlength="{PASS_MAXLENGTH}" />
        </div>
        <!-- END: passEmpty -->
        <div class="clearfix rows">
            <label>
                {LANG.pass_new}
            </label>
            <input type="password" id="new_password_iavim" name="new_password" value="{DATA.new_password}" class="form-control required password input" maxlength="{PASS_MAXLENGTH}" />
        </div>
        <div class="clearfix rows">
            <label>
                {LANG.pass_new_re}
            </label>
            <input type="password" id="re_password_iavim" name="re_password" value="{DATA.re_password}" class="form-control required password input" maxlength="{PASS_MAXLENGTH}" />
        </div>
		<div class="clearfix rows">
			<label>
                &nbsp;
            </label>
			<input type="hidden" name="checkss" value="{DATA.checkss}" />
			<input type="submit" value="{LANG.change_pass}" class="sumbit" />
		</div>
        
    </form>	
</div>
</div>
</div>

<!-- END: main -->