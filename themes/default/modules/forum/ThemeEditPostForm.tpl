<!-- BEGIN: main -->
<div id="EditInline" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" style="font-size:16px;">Chỉnh sửa bởi {USER.username}</h4>
			</div>
			
			<form id="FormEditInline" action="{ACTION}" method="post">
				<div class="modal-body">	
					<div class="message"> {POST.message} </div>
					<input type="hidden" name="post_id" value="{POST.post_id}">
					<input type="hidden" name="token" value="{POST.token}">	
					<input type="hidden" name="action" value="save-inline">	
				</div>
				<!-- BEGIN: canSilentEdit -->
				<div class="secondaryContent">
					<dl class="ctrlUnit ">
						<dt></dt>
						<dd>
							<ul>
								<li><label><input type="checkbox" name="silent" value="1" id="ctrl_silent" class="Disabler"> Sửa ẩn</label>
									<p class="explain">Nếu lựa chọn sẽ không cập nhật lần sửa cuối cùng.</p>
									<ul id="ctrl_silent_Disabler" class="disabled">
										<li><label><input type="checkbox" name="clear_edit" value="1" disabled="disabled" class="disabled"> Xóa thời gian cập nhật gần nhất</label>
											<p class="explain">Nếu lựa chọn lần sửa cuối sẽ bị xóa</p>
										</li>
									</ul>
								</li>
							</ul>
						</dd>
					</dl>
					<div class="actionAlert">
						<dl class="ctrlUnit">
							<dt></dt>
							<dd>
								<ul>
									<li>
										<label><input type="checkbox" name="send_author_alert" value="1" class="Disabler" id="ctrl_send_author_alert"> Thông báo cho tác giả về hành động này. Lý do:</label>
										<ul id="ctrl_send_author_alert_Disabler" class="disabled">
											<li><input type="text" name="author_alert_reason" class="textCtrl disabled" placeholder="Lý do sửa" disabled="disabled"></li>
										</ul>
										<p class="hint">Chú ý, tác giả sẽ thấy cảnh báo này ngay cả khi không xem tin nhắn này</p>
									</li>
								</ul>
							</dd>
						</dl>
					</div>

				</div>
				<!-- END: canSilentEdit -->
				<div class="modal-footer">
					<div class="submit" style="text-align:center">
						<button id="SubmitEditInline" type="button" class="btn btn-primary"> <i class="fa fa-spinner fa-lg fa-spin" style="display:none"> </i> Cập nhật</button>
						<a href="#" class="btn btn-primary">  Thêm lựa chọn </a>
					</div>
			    </div>
			</form>
		</div>
	</div>
</div>	
<script type="text/javascript">
$('input[name="silent"]').on('click', function(){
	if( $(this).is(':checked') )
	{
		$('input[name="clear_edit"]').prop('disabled', false).removeClass('disabled');
	}else{
		$('input[name="clear_edit"]').prop('disabled', true).addClass('disabled');
	}
})
$('input[name="send_author_alert"]').on('click', function(){
	if( $(this).is(':checked') )
	{
		$('input[name="author_alert_reason"]').prop('disabled', false).removeClass('disabled');
	}else{
		$('input[name="author_alert_reason"]').prop('disabled', true).addClass('disabled');
	}
})
</script>	
<!-- END: main -->