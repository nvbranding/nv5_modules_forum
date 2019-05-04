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
          <li><a rel='building' class="#primaryContent" href="#account/alerts">Thông báo của bạn</a></li>
          <li><a rel='building' class="#primaryContent" href="#account/news-feed">Bảng tin</a></li>
          <li><a rel='building' class="#primaryContent" href="#account/likes">Bạn đã được 'Thích'</a></li>
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
          <li><a rel='building'href="#" class="LogOut primaryContent">Thoát</a></li>
        </ul>
      </li>
    </ul>
  </div>
  <div class="mainContentBlock section sectionMain insideSidebar">
<div id="users">
	<div class="page-header">
		<h3>{LANG.editinfo_pagetitle}</h3>
	</div>
			<ul class="list-tab top-option clearfix">
				<li class="ui-tabs-selected"><a href="{URL_HREF}/editinfo">{LANG.editinfo}</a></li>
				<li><a href="{URL_HREF}/changepass">{LANG.changepass_title}</a></li>
				<li><a href="{URL_HREF}/editinfo/changequestion">{LANG.question2}</a></li>
				<!-- BEGIN: allowopenid --><li><a href="{URL_OPENID}">{LANG.openid_administrator}</a></li><!-- END: allowopenid -->
				<!-- BEGIN: regroups --><li><a href="{URL_HREF}/regroups">{LANG.in_group}</a></li><!-- END: regroups -->
				<!-- BEGIN: logout --><li><a href="{URL_HREF}/logout">{LANG.logout_title}</a></li><!-- END: logout -->
			</ul>
	<form action="{EDITINFO_FORM}" method="post" class="form-inline box-border content-box clearfix bgray" enctype="multipart/form-data">
		<div class="box-border content-box clearfix m-bottom edit-info bwhite">
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.account}
                    </label>
                </dt>
                <dd class="fl">
                    <!-- BEGIN: username_change -->
                    <input class="form-control" type="text" name="username" value="{DATA.username}" id="nv_username_iavim" maxlength="{NICK_MAXLENGTH}" />
                    <!-- END: username_change -->
                    <!-- BEGIN: username_no_change -->
                    <strong>{DATA.username}</strong>
                    <!-- END: username_no_change -->
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.email}
                    </label>
                </dt>
                <dd class="fl">
                    <!-- BEGIN: email_change -->
                    <input class="form-control" type="text" name="email" value="{DATA.email}" id="nv_email_iavim" maxlength="100" />
                    <!-- END: email_change -->
                    <!-- BEGIN: email_no_change -->
                    <strong>{DATA.email}</strong>
                    <!-- END: email_no_change -->
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.name}
                    </label>
                </dt>
                <dd class="fl">
                    <input class="form-control" type="text" name="full_name" value="{DATA.full_name}" maxlength="255" />
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.gender}
                    </label>
                </dt>
                <dd class="fl">
                    <select name="gender" class="form-control">
                        <!-- BEGIN: gender_option -->
                        <option value="{GENDER.value}"{GENDER.selected}>{GENDER.title}</option>
                        <!-- END: gender_option -->
                    </select>
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.avata} (80x80)
                    </label>
                </dt>
                <dd class="fl">
                    <input  type="file" name="avatar" />
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.birthday}
                    </label>
                </dt>
                <dd class="fl">
                    <input class="form-control" type="text" name="birthday" id="birthday" value="{DATA.birthday}" style="width: 150px;text-align:left" maxlength="10" readonly="readonly" type= "text" />
                    <img src="{NV_BASE_SITEURL}images/calendar.jpg" style="cursor: pointer; vertical-align: middle;" onclick="popCalendar.show(this, 'birthday', 'dd.mm.yyyy', true);" alt="" height="17" />
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.website}
                    </label>
                </dt>
                <dd class="fl">
                    <input class="form-control" type="text" name="website" value="{DATA.website}" maxlength="255" />
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.address}
                    </label>
                </dt>
                <dd class="fl">
                    <input class="form-control" type="text" name="address" value="{DATA.address}" maxlength="255" />
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.yahoo}
                    </label>
                </dt>
                <dd class="fl">
                    <input class="form-control" type="text" name="yim" value="{DATA.yim}" maxlength="100" />
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.phone}
                    </label>
                </dt>
                <dd class="fl">
                    <input class="form-control" type="text" name="telephone" value="{DATA.telephone}" maxlength="100" />
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.fax}
                    </label>
                </dt>
                <dd class="fl">
                    <input class="form-control" type="text" name="fax" value="{DATA.fax}" maxlength="100" />
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.mobile}
                    </label>
                </dt>
                <dd class="fl">
                    <input class="form-control" type="text" name="mobile" value="{DATA.mobile}" maxlength="100" />
                </dd>
            </dl>
            <dl class="clearfix">
                <dt class="fl">
                    <label>
                        {LANG.showmail}
                    </label>
                </dt>
                <dd class="fl">
                    <select name="view_mail" class="form-control">
                        <option value="0">{LANG.no}</option>
                        <option value="1"{DATA.view_mail}>{LANG.yes}</option>
                    </select>
                </dd>
            </dl>
		</div>	
        <div class="text-center" style="margin-top: 10px;">
			<input type="hidden" name="checkss"  value="{DATA.checkss}" />
			<input name="submit" type="submit" class="button2" value="{LANG.editinfo_confirm}" />
        </div>
    </form>
</div>
</div>
  </div>

<!-- END: main -->