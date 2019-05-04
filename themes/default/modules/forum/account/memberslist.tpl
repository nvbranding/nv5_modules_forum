<!-- BEGIN: main -->
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
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th><a href="{username}">{LANG.account}</a></th>
				<th style="witdh:50px"><a href="{gender}">{LANG.gender}</a></th>
				<th style="witdh:20%;">{LANG.yahoo}</th>
				<th style="witdh:100px"><a href="{regdate}">{LANG.regdate}</a></th>
			</tr>
		</thead>
		<!-- BEGIN: list -->
		<tbody>
			<tr>
		   <td>
			<a href="{USER.link}">
				{USER.username} <!-- BEGIN: fullname -->&nbsp;( {USER.full_name} ) <!-- END: fullname -->
			</a>
		   </td>
		   <td>{USER.gender}</td>
		   <td>
				<!-- BEGIN: yahoo -->
				<a href="ymsgr:sendim?{USER.yim}">
					<img border="0" src="http://opi.yahoo.com/online?u={USER.yim}&amp;m=g&amp;t=2" alt="{USER.yim}" />
				</a>
				<!-- END: yahoo -->
				<!-- BEGIN: nullyahoo -->
				N/A
				<!-- END: nullyahoo -->
		   </td>
		   <td class="fl">{USER.regdate}</td>
		   </tr>
		<!-- END: list -->
		<tbody>
		<!-- BEGIN: generate_page -->
			<tfoot>
				<tr>
					<td colspan="4">{GENERATE_PAGE}</td>
				</tr>
			</tfoot>
		<!-- END: generate_page -->
	</table>
</div>    
</div>
  </div>  
<!-- END: main -->