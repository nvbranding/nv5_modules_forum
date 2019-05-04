<!-- BEGIN: main -->
<div id="DeletePostForm" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" style="font-size:16px;">Xóa bài viết bởi {DELETE_BY}</h4>
			</div>
			<form id="FormDeletePost" action="{ACTION}" method="post">
				<div class="modal-body">	
					
					<label>Lý do xóa:</label><input type="text" name="reason" value="" class="form-control">
					<!-- BEGIN: position -->
					<p style="padding:10px 0;font-size:14px;font-style:italic;font-weight:bold;">Chú ý: Đây là bài ​​viết đầu tiên trong chủ đề. Xóa nó sẽ xóa toàn bộ chủ đề.</p>
					<!-- END: position -->
					<input type="hidden" name="post_id" value="{POST.post_id}">
					<input type="hidden" name="token" value="{POST.token}">	
					<input type="hidden" name="action" value="update">	
				</div>
				<div class="modal-footer">
					<div class="submit" style="text-align:center">
						<button id="SubmitDeletePost" type="button" class="btn btn-primary"> <i class="fa fa-spinner fa-lg fa-spin" style="display:none"> </i> Xóa tin nhắn</button>
						  
					</div>
			    </div>
			</form>
		</div>
	</div>
</div>		
<!-- END: main -->