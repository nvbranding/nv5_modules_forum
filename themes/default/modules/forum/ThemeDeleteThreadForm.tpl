<!-- BEGIN: main -->
<div id="PopupFormThread" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" style="font-size:16px;">Xóa chủ đề: <span>{THREAD.title}</span></h4>
			</div>
			
			<form id="FormDeleteThread" action="{ACTION}" method="post" class="xenForm">
				<div class="modal-body">	
					<input type="hidden" name="thread_id" value="{THREAD.thread_id}">
					<input type="hidden" name="token" value="{THREAD.token}">	
					<input type="hidden" name="action" value="DeleteThread">	
					<input type="hidden" name="save" value="1">	
					<!-- BEGIN: canHardDelete -->
					<dl class="ctrlUnit">
						<dt>kiểu xóa:</dt>
						<dd>
							<ul>
								<li><label for="ctrl_soft_delete">
									<input type="radio" name="hard_delete" id="ctrl_soft_delete" value="0" class="Disabler" checked="checked"> Xóa khỏi danh sách công cộng</label>
									<ul id="ctrl_soft_delete_Disabler">
										<li><input type="text" name="reason" class="form-control" placeholder="Lý do..."></li>
									</ul>
									<p class="hint">Chủ đề vẫn có thể xem được bởi người điều hành và có thể được khôi phục lại vào một ngày sau đó.</p>
								</li>
								<li><label for="ctrl_hard_delete">
									<input type="radio" name="hard_delete" id="ctrl_hard_delete" value="1">Xóa vĩnh viễn</label>
									<p class="hint">Lựa chọn này sẽ xóa vĩnh viễn chủ đề và các mục liên quan</p></li>
							</ul>
						</dd>
					</dl>
					<dl class="ctrlUnit">
						<dt></dt>
						<dd><ul>
							<li>
								<label><input type="checkbox" name="send_starter_alert" value="1" class="Disabler" id="ctrl_send_starter_alert"> Thông báo cho tác giả về hành động này. Lý do:</label>
								<ul id="ctrl_send_starter_alert_Disabler" class="disabled">
									<li><input type="text" name="starter_alert_reason" class="form-control disabled" placeholder="Không bắt buộc" disabled=""></li>
								</ul>
								<p class="hint"> Tác giả chủ đề sẽ thấy cảnh báo này ngay cả khi không thể xem chủ đề này nữa </p>
							</li>
						</ul></dd>
					</dl>
					<!-- END: canHardDelete -->
					<!-- BEGIN: canSoftDelete -->
					<dl class="ctrlUnit">
						<dt><label for="ctrl_reason">Lý do xóa:</label></dt>
						<dd><input type="text" name="reason" id="ctrl_reason" class="form-control"></dd>
					</dl>
					<input type="hidden" name="hard_delete" value="0">
					<!-- END: canSoftDelete -->
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer" style="margin-top:10px">
					<div class="submit" style="text-align:center">
						<button id="SubmitDeleteThread" type="button" class="btn btn-primary"> <i class="fa fa-spinner fa-lg fa-spin" style="display:none"> </i> Xóa chủ đề </button>
						<button id="SubmitCancel" type="button" data-dismiss="modal" class="btn btn-primary"> Hủy </button>		 
					</div>
			    </div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

$('#ctrl_send_starter_alert').click(function(){
	if( $(this).prop('checked') )
	{
		$('input[name="starter_alert_reason"]').prop('disabled', false);
		
		
	}else{
		$('input[name="starter_alert_reason"]').prop('disabled', true);
		
	}
})
$('input[name="hard_delete"]').click(function(){
	 
	if( $(this).val() == 0 )
	{
		$('input[name="reason"]').prop('disabled', false);
		
		
	}else{
		$('input[name="reason"]').prop('disabled', true);	
	}
})

$('#SubmitDeleteThread').on('click', function(e){

	var form = $('#FormDeleteThread');
	$.ajax({
			type: form.attr('method'),
			url: form.attr('action'),
			//data:  $('#FormDeleteThread input[type=\'text\'],#FormDeleteThread input[type=\'hidden\'],#FormDeleteThread input[type=\'radio\']:checked,#FormDeleteThread input[type=\'checkbox\']:checked'),
			data: form.serializeArray(),
			dataType: 'json',
			beforeSend: function() {
				$('#FormDeleteThread input[type="button"]').prop('disabled', true); 
				$('#SubmitDeleteThread i').show(); 
			},
			complete: function() {
				$('#FormDeleteThread input[type="button"]').prop('disabled', false); 
				$('#SubmitDeleteThread i').hide(); 

			},
			success: function(json) {
			 
				if (json['link']) {
					window.location.href = json['link'];
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				$('#FormDeleteThread input[type="button"]').prop('disabled', false); 
				$('#SubmitDeleteThread i').hide (); 

			}
			
	});	
	
 
	e.preventDefault();
})
 
</script>		
<!-- END: main -->