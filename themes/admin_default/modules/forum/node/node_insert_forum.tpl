<!-- BEGIN: main -->
<div id="forum-content">
    <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_warning}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <br>
    </div>
    <!-- END: error_warning -->
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {CAPTION}</h3>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="{ACTION}" method="post" enctype="multipart/form-data" id="node-forum" class="form-horizontal">
				<input type="hidden" name ="node_type_id" value="{DATA.node_type_id}" />
				<input type="hidden" name ="node_id" value="{DATA.node_id}" />
				<input type="hidden" name ="parentid_old" value="{DATA.parent_id}" />
				<input name="save" type="hidden" value="1" />
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-first" data-toggle="tab">Thông tin chung</a> </li>         
					<li><a href="#tab-second" data-toggle="tab">Tùy chỉnh</a></li>           
					<!-- <li><a href="#tab-third" data-toggle="tab">Tiền tố chủ đề</a></li>	  -->
				</ul>
                <div id="form-insert" class="tab-content margintop">
					<div class="tab-pane active" id="tab-first">
						<div class="form-group required">
							<label class="col-sm-4 control-label" for="input-title">{LANG.node_forum_title}</label>
							<div class="col-sm-20">
								<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.node_forum_title}" id="input-title" class="form-control" />
								<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="input-alias">{LANG.node_forum_alias} <i class="fa fa-refresh fa-lg icon-pointer" onclick="get_alias( );">&nbsp;</i></label>
							<div class="col-sm-20">
								<input class="form-control" name="alias" placeholder="{LANG.node_forum_alias}" type="text" value="{DATA.alias}" maxlength="255" id="input-alias"/>
										 
							</div>
						</div>
						<div class="form-group">
							 <label class="col-sm-4 control-label" for="input-description">{LANG.node_forum_description} </label>
							 <div class="col-sm-20">
								   {DATA.description}   
							 </div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="input-password">{LANG.node_forum_password}</label>
							<div class="col-sm-20">
								<input type="text" name="password" value="{DATA.password}" placeholder="{LANG.node_forum_password}" id="input-password" class="form-control" />
							</div>
						</div>
						<div class="form-group">
								<label class="col-sm-4 control-label" for="input-parent">{LANG.node_sub_sl}</label>
								<div class="col-sm-20">
									<select class="form-control" name="parent_id">
										<option value="0"> Là nút chính </option>
										<!-- BEGIN: node -->
										<option value="{NODE.key}" {NODE.selected}>{NODE.name}</option>
										<!-- END: node -->
									</select>
								</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="input-title">{LANG.node_forum_weight}</label>
							<div class="col-sm-20">
								<input type="text" name="weight" value="{DATA.weight}" placeholder="{LANG.node_forum_weight}" id="input-weight" class="form-control" />
								<input type="hidden" name="old_weight" value="{DATA.weight}" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="input-status">{LANG.node_forum_status}</label>
							<div class="col-sm-20">
								<select name="status" id="input-status" class="form-control">
									<!-- BEGIN: status -->
									<option value="{STATUS.key}" {STATUS.selected}>{STATUS.name}</option>
									<!-- END: status -->
								</select>
							</div>
						</div>                    
					</div>  
					<div class="tab-pane" id="tab-second">
							<div class="form-group">
								<label class="col-sm-4 control-label"></label>
								<div class="col-sm-20">
									<label for="ctrl_allow_posting_1"><input type="checkbox" name="allow_posting" value="1" id="ctrl_allow_posting_1" checked="checked"> Cho phép tin nhắn mới được đăng trên diễn đàn này</label>
									<p class="hint">Nếu bị vô hiệu hóa, người dùng sẽ không thể đăng bài mới hoặc chỉnh sửa hoặc xóa các tin nhắn của mình. Người điều hành vẫn sẽ có thể quản lý các nội dung của diễn đàn này.</p>									
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"></label>
								<div class="col-sm-20">
									<label for="ctrl_allow_poll_1">
										<input type="checkbox" name="allow_poll" value="1" id="ctrl_allow_poll_1" checked="checked"> Cho phép tạo thăm dò trong diễn đàn này</label>
									<p class="hint">Nếu bị vô hiệu hóa, người dùng sẽ không được cung cấp tùy chọn để tạo ra một cuộc thăm dò khi đăng một chủ đề hoặc để thêm một sau đó. Nếu một sợi bằng một cuộc thăm dò được chuyển vào diễn đàn này, nó sẽ giữ cho các cuộc thăm dò.</p>
								
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"></label>
								<div class="col-sm-20">
									<label for="ctrl_moderate_threads_1"><input type="checkbox" name="moderate_threads" value="1" id="ctrl_moderate_threads_1">Điều hành chủ đề mới đăng tại diễn đàn này</label>
									<p class="hint">Nếu được kích hoạt, điều hành viên sẽ phải tự phê duyệt chủ đề được đăng tại diễn đàn này.</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"></label>
								<div class="col-sm-20">
									<label for="ctrl_moderate_replies_1"><input type="checkbox" name="moderate_replies" value="1" id="ctrl_moderate_replies_1" {DATA.moderate_replies}>Điều hành viên có thể trả lời các bài đăng tại diễn đàn này</label>
									<p class="hint">Nếu được kích hoạt, điều hành viên sẽ phải tự chấp nhận trả lời gửi đến chủ đề trong diễn đàn này.</p>									
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"></label>
								<div class="col-sm-20">
									 <label for="ctrl_count_messages_1"><input type="checkbox" name="count_messages" value="1" id="ctrl_count_messages_1" {DATA.count_messages}> Đếm bài viết đăng trên diễn đàn này trong tổng trị sử dụng</label>
									 <p class="hint">Nếu bị vô hiệu hóa, tin nhắn gửi (trực tiếp) trong diễn đàn này sẽ không đóng góp vào tổng số bài viết của người dùng đăng tải đếm.</p>
								</div>
							</div>
							<div class="form-group bottomline">
								<label class="col-sm-4 control-label"></label>
								<div class="col-sm-20">
									 <label for="ctrl_find_new_1"><input type="checkbox" name="find_new" value="1" id="ctrl_find_new_1" {DATA.find_new}> Bao gồm các bài từ diễn đàn này khi người dùng nhấp vào "Bài viết mới"</label>
									 <p class="hint">Nếu vô hiệu hóa, chủ đề được tạo sẽ không bao giờ xuất hiện trong danh sách / bài viết mới của diễn đàn này.</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Quyền xem thông báo tại diễn đàn này:</label>
								<div class="col-sm-20">
									 <!-- BEGIN: allowed_watch -->
									 <label for="ctrl_allowed_watch_notifications_{ALLOWERWATCH.key}" class="clearfix"><input type="radio" name="allowed_watch_notifications" value="{ALLOWERWATCH.key}" id="ctrl_allowed_watch_notifications_{ALLOWERWATCH.key}" {ALLOWERWATCH.checked}> {ALLOWERWATCH.name}</label><div class="clear"></div>
									 <!-- END: allowed_watch -->
									 <p class="explain">Bạn có thể giới hạn số lượng các thông báo có thể được kích hoạt bởi một người dùng xem một diễn đàn ở đây. Ví dụ, nếu bạn chọn "chủ đề mới", người dùng sẽ chỉ có thể lựa chọn giữa không thông báo hoặc thông báo khi một chủ đề mới được gửi. Điều này có thể được sử dụng để hạn chế lượt tải của hệ thống diễn đàn xem trong diễn đàn quá tải.</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Sắp xếp mặc định:</label>
								<div class="col-sm-20">
									<select name="default_sort_order" class="form-control" style="width:150px;display:inline-block;height:30px" id="ctrl_default_sort_order">
										<option value="last_post_date" selected="selected">Tin nhắn cuối</option>
										<option value="post_date">Ngày bắt đầu</option>
										<option value="title">Tiêu đề</option>
										<option value="reply_count">Lượt trả lời</option>
										<option value="view_count">Lượt xem</option>
									</select>
									<select name="default_sort_direction" class="form-control" style="width:150px;display:inline-block;height:30px" id="ctrl_default_sort_direction">
										<option value="desc" selected="selected">Giảm dần</option>
										<option value="asc">Tăng dần</option>
									</select>	
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Thread List Date Limit:</label>
								<div class="col-sm-20">
									<select name="list_date_limit_days" class="form-control" style="width:150px;display:inline-block;height:30px" id="ctrl_list_date_limit_days">
										<!-- BEGIN: limit_days -->
										<option value="{LIMIT_DAYS.key}" {LIMIT_DAYS.selected}>{LIMIT_DAYS.name}</option>
										<!-- END: limit_days -->
									</select>
								</div>
							</div>
					</div> 
					<div class="tab-pane" id="tab-third">
					<!-- tab-third -->
					</div> 		
				</div>                    
				<div class="form-group">
					<label class="col-sm-4"></label>
					<div class="col-sm-20">
						<button class="btn btn-primary" type="submit" id="submitform"><i class="fa fa-spinner fa-lg fa-spin" style="display:none"></i> {LANG.save} </button>
						
					</div>
				</div>       
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$('#submitform').on('click', function( e ){	
	CKEDITOR.instances.forum_description.updateElement(); 	
	var data = $('#node-forum').serialize();
	var action = $('#node-forum').attr('action');
	$.ajax({
		url: action + '&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$('#submitform').prop('disabled', true);
			$('#submitform i').show();
		},	
		complete: function() {
			$('#submitform').prop('disabled', false);
			$('#submitform i').hide();
		},
		success: function(json) {
			
			if ( json['error'] )
			{
				if ( json['error']['title'] ) alert( json['error']['title'] );
				if ( json['error']['db'] ) alert( json['error']['db'] );		
			} 
						
			if ( json['redirect'] ) location.href = json['redirect']; 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('#submitform').prop('disabled', false);
			$('#submitform i').hide();
		}
	});
	e.preventDefault();
	return false;
})
</script>
<!-- END: main -->