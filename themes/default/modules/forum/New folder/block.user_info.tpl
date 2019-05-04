<!-- BEGIN: main -->
<ul class="visitorTabs">
	<li class="navTab2 Popup PopupClosed PopupContainerControl">                  
		<a href="#login/" class="navLink NoPopupGadget OverlayTrigger">Đăng nhập | Đăng ký</a>
                                 
	</li>
</ul>
<!-- END: main -->
<!-- BEGIN: signed -->

<script type="text/javascript">
function DropDown(el) {
    this.dd = el;
    this.initEvents();
}
DropDown.prototype = {
    initEvents: function () {
        var obj = this;

        obj.dd.on('click', function (event) {
            $(this).toggleClass('active');
            event.stopPropagation();
        });
    }
}
$(function () {

    var dd = new DropDown($('#dd'));

    $(document).click(function () {
        // all dropdowns
        $('.wrapper-dropdown-5').removeClass('active');
    });

});		
</script>
<ul class="visitorTabs">
	<li class="navTab2 account Popup PopupControl  PopupContainerControl PopupOpen" style="position: relative;">
		<div id="dd" class="wrapper-dropdown-5"><a class="user_info" href="{URL_HREF}"> {USER.username} </a>
			<ul class="dropdown">
				<li class="content">
					<div id="AccountMenu">
					<div class="primaryContent menuHeader">
					<a href="{PAGE_USER}" class="avatar NoOverlay Av{USER.userid}m plainImage" title="Xem hồ sơ của bạn"><span class="img m" style="background: url('{AVATA}') center"></span></a>
				
				<h3><a href="{PAGE_USER}" class="concealed" title="Xem hồ sơ của bạn">{USER.username}</a></h3>
				
				<div class="muted">Thành viên mới</div>
				
				<ul class="links">
					<li class="fl"><a href="{PAGE_USER}">Trang cá nhân của bạn</a></li>
				</ul>
			</div>
			<div class="clear"></div>
			<div class="menuColumns secondaryContent">
				<ul class="col1 blockLinksList">
				
					<li><a href="{CHANGE_INFO}">Chi tiết cá nhân</a></li>
					<li><a rel='building' href="#">Chữ ký</a></li>
					<li><a rel='building' href="#account/contact-details">Chi tiết liên hệ</a></li>
					<li><a rel='building' href="#account/privacy">Quyền riêng tư</a></li>
					<li><a rel='building' href="#account/preferences">Tùy chọn</a></li>
					<li><a rel='building' href="/">Thiết lập thông báo</a></li>
					<li><a rel='change_avatar' checkss="{checkss}" userid="{USER.userid}" href="#" class="OverlayTrigger">Đổi Avatar</a></li>
					<li><a href="{CHANGE_PASS}">Mật khẩu</a></li>
					<li><a href="{OPENID}">Quản lý Openid</a></li>
				
				</ul>
				<ul class="col2 blockLinksList">
				
					<li><a rel='building' href="#account/news-feed">Bảng tin</a></li>
					<li><a rel='building' href="#account/alerts">Thông báo của bạn</a></li>
					<li><a rel='building' href="#watched/threads">Chủ đề đang theo dõi</a></li>
					<li><a rel='building' href="#account/likes">Bạn đã được 'Thích'</a></li>
					<li><a rel='building' href="#search/member">Các chủ đề đã tham gia</a></li>
					<li><a rel='building' href="#account/following">Thành viên bạn theo đuôi</a></li>
					<li><a rel='building' href="#account/ignored">Danh sách đen</a></li>
				</ul>
			</div>
			<div class="menuColumns secondaryContent">
				<!-- BEGIN: admin -->
				<ul class="col2 blockLinksList">
					<li><a href="{LOGOUT_ADMIN}" class="LogOut OverlayTrigger">Thoát</a></li>
				</ul>
				<!-- END: admin -->
			</div>
			<div class="clear"></div>
				</div>
				</li>
			</ul>
			<div class="clear"></div>
		</div>
	</li>	
</ul>
<div class="clear"></div>
<script type="text/javascript">
    $(document).ready(function(){
		$("a[rel='change_avatar']").on('click', function(e) {
			e.preventDefault();
			var checkss = $(this).attr("checkss");
			var userid = $(this).attr("userid");
			$.ajax({
				type: "POST",
				url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=account&nocache=' + new Date().getTime(),
				data: 'action=change_avatar&userid=' + userid + '&checkss=' + checkss,
				success: function (res) {
					var obj = $.parseJSON(res);
					var message = obj.data.message;
					var items = obj.data.item;

					if (message == 'success') {
						$.colorbox({html:items['message'],top:'10%', speed:0, width:'50%'});
						
					}else if (message == 'unsuccess') {
						alert('Có lỗi xảy ra không lấy được thông tin thành viên này');
					}
				} 
			});
			return false;
		});
		
	});
</script>

<!-- END: signed -->