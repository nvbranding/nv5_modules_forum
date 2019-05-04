<!-- BEGIN: main -->
<div id="PopupFormThread" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" style="font-size:16px;">Chỉnh sửa chủ đề: <span>{THREAD.title}</span></h4>
			</div>
			
			<form id="FormEditThreadTitle" action="{ACTION}" method="post">
				<div class="modal-body">	
					<input type="hidden" name="thread_id" value="{THREAD.thread_id}">
					<input type="hidden" name="token" value="{THREAD.token}">	
					<input type="hidden" name="action" value="EditThread">	
					<input type="hidden" name="save" value="1">	
					
					<dl class="ctrlUnit">
						<dt><label for="title_thread_edit">Tiêu đề:</label></dt>
						<dd><input type="text" name="title" value="{THREAD.title}" class="form-control" id="title_thread_edit" maxlength="250"></dd>
					</dl>
					<dl class="ctrlUnit ">
						<dt><label>Trạng thái:</label></dt>
						<dd>
							<ul>
								<li>
									<label for="ctrl_discussion_open">
									<input type="checkbox" name="discussion_open" value="1" id="ctrl_discussion_open" {OPEN_CHECKED}> Mở</label>
  
									<p class="hint">Mọi người có thể trả lời tại chủ đề này</p>
								</li>
								<li>
									<label for="ctrl_sticky"><input type="checkbox" name="sticky" value="1" id="ctrl_sticky" {STICKY_CHECKED}> Chủ đề nhấn mạnh</label>
									<input type="hidden" name="sticky" value="1">
									<p class="hint">Chủ đề sẽ xuất hiện ở phía trên cùng của trang đầu tiên tại danh sách các chủ đề trong diễn đàn</p>
								</li>
							</ul>
						</dd>
					</dl>
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer">
					<div class="submit" style="text-align:center">
						<button id="SubmitEditThreadTitle" type="button" class="btn btn-primary"> <i class="fa fa-spinner fa-lg fa-spin" style="display:none"> </i> Lưu lại </button>
						<button id="SubmitCancel" type="button" data-dismiss="modal" class="btn btn-primary"> Hủy </button>		 
					</div>
			    </div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$('#title_thread_edit').on('keydown, keyup', function(){
	var title = $(this).val();		 
	$('#PopupFormThread h4 span').text(title);		
});
$('#SubmitEditThreadTitle').on('click', function(e){

	
	var title = $('#title_thread_edit').val();	
	if( title.length >= 20 )
	{
		var form = $('#FormEditThreadTitle');
		var dataContent = {
			title : title,
			discussion_open : ( $('#FormEditThreadTitle input[name="discussion_open"]').prop('checked') ) ? 1 : 0,
			sticky : ( $('#FormEditThreadTitle input[name="sticky"]').prop('checked') ) ? 1 : 0,
			token : $('#FormEditThreadTitle input[name="token"]').val(),
			thread_id : $('#FormEditThreadTitle input[name="thread_id"]').val(),
			save : $('#FormEditThreadTitle input[name="save"]').val(),
			action : $('#FormEditThreadTitle input[name="action"]').val(),
			
		};
		$.ajax({
			type: form.attr('method'),
			url: form.attr('action'),
			data: dataContent,
			dataType: 'json',
			beforeSend: function() {
				$('#FormEditThreadTitle input[type="button"]').prop('disabled', true); 
				$('#SubmitEditThreadTitle i').show(); 
			},
			complete: function() {
				$('#FormEditThreadTitle input[type="button"]').prop('disabled', false); 
				$('#SubmitEditThreadTitle i').hide(); 

			},
			success: function(json) {
			 
				if (json['link']) {
					window.location.href = json['link'];
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				$('#FormEditThreadTitle input[type="button"]').prop('disabled', false); 
				$('#SubmitEditThreadTitle i').hide (); 

			}
			
		});	
	}else{
		alert('Lỗi: Tên chủ đề quá ngắn');
	}
 
	e.preventDefault();
})
 
</script>		
<!-- END: main -->