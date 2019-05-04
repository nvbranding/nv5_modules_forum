<!-- BEGIN: main -->
<div id="forum-content"> 
	<div class="titleBar">
		<div class="topCtrl">
			<form action="{ACTION_USER}" method="post">
				<span class="spansearch">
					<input type="search" name="username" class="textCtrl AcSingle" size="20" results="10" placeholder="User Name" autocomplete="off">
					<i class="fa fa-times"></i>
				</span>
				<input type="hidden" value="1" name="add">
				<input type="submit" value="Cấp quyền" class="btn btn-primary btn-sm">
			</form>
		</div>
		<h1>Thiết lập quyền mục: {NODE_TITLE}</h1>	
	</div>
	<form action="{ACTION_REVOKE}" method="post" class="xenForm formOverlay" style="margin-bottom: 10px;">
		<dl class="ctrlUnit fullWidth"> <dt></dt>
			<dd>
				<ul>
					<li>
						<label for="ctrl_revoke_1">
							<input type="checkbox" name="revoke" value="1" id="ctrl_revoke_1" {DATA.revoke} > Mục riêng tư</label>
						<p class="hint">Nếu lựa chọn, thành viên sẽ chỉ có thể xem mục này nếu họ được cấp quyền truy cập.</p>
					</li>
				</ul>
			</dd>
		</dl>
		
		<dl class="ctrlUnit submitUnit"><dt></dt>
			<dd>
				<button class="btn btn-primary" type="submit" id="submitform"><i class="fa fa-spinner fa-lg fa-spin" style="display:none"></i> Cập nhật</button>
						
			</dd>
		</dl>
		<div class="clear"></div>
	</form>
	
	<form action="{ACTION_GROUP}" name="user-groups" class="section">
		<h2 class="subHeading">Nhóm thành viên</h2>
		<ol class="FilterList Scrollable">
			<!-- BEGIN: group  -->
			<li class="listItem primaryContent" id="_user_group_{GROUP.group_id}"> 
				<a href="{GROUP.group_link}" class="secondaryContent">Thông tin nhóm</a>
				<h4 {GROUP.class}>
					<a href="{GROUP.permissions_link}">
						<em>{GROUP.title}</em>
					</a>
				</h4> 
			</li>
			<!-- END: group  -->
 
		</ol>
	</form>
	<!-- BEGIN: user -->
	<form action="#users" name="users" class="section">
		<h2 class="subHeading">Users</h2>
		<ol class="FilterList Scrollable">	
			<!-- BEGIN: loop -->
			<li class="listItem primaryContent hasPermissions" id="_user_{USER.userid}">
				<a href="{USER.link}" class="secondaryContent">Thông tin thành viên</a>
				<h4>
					<img src="{USER.avatar}" alt="" class="listAvatar">
					<a href="{USER.permissions_link}">
						<em>{USER.username}</em>
					</a>
				</h4>
			</li>
		<!-- END: loop -->			
		</ol>
	</form>
	<!-- END: user -->
</div> 
<script type="text/javascript">
$('#submitform').on('click', function(){
	$('#submitform').prop('disabled', true);
	$('#submitform i').show();
});
$('input[name=\'username\']').on('keydown, keyup', function(){
	if( $(this).val() != '')
	{
		$('.spansearch i').show();
	}else{
		$('.spansearch i').hide();
	}	
});
$('.spansearch i').on('click', function(){
	$(this).hide();
	$('input[name=\'username\']').val('');
});
$('input[name=\'username\']').autofill({
	'source': function(request, response) {
		if( $('input[name=\'username\']').val().length > 2 )
		{	 
			$.ajax({
				url: '{JSON_URL}&action=getUsername&username=' +  encodeURIComponent(request) + '&nocache=' + new Date().getTime(),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['username'],
							value: item['userid']
						}
					}));
				}
			});
		}
	},
	'select': function(item) {
		$('input[name=\'username\']').val( item['label'] );
		
	}
});

</script>
<!-- END: main -->
 

