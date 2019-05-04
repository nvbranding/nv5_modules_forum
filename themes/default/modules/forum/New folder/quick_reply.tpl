<!-- BEGIN: main -->
<script type="text/javascript">
$(function() {
	$("#messsage_post_id").sceditor({
		plugins: 'bbcode',
		width: '100%',
		style: "{NV_BASE_SITEURL}modules/{MODULE_FILE}/bbcode/minified/jquery.sceditor.default.min.css"							
	});
});
</script>
<form class="form-inline" id="thread_edit" onSubmit="return update_post('{DATA.post_id}');" method="post" action="" style="width: 100%;">
  <input type="hidden" name="checkss_post_id" value="{DATA.checkss}" />
  <input type="hidden" name="post_id" value="{DATA.post_id}" />
  <input type="hidden" name="catid" value="{catid}" />
  <input type="hidden" name="action" value="update_post" />
  <div class="clears"><textarea id="messsage_post_id" name="messsage_post_id" style="height: 200px; width: 100%; display: none;">{DATA.message}</textarea>
  </div>
  <div style="padding-top:6px">
    <input id="submit" type="submit" class="button2" value="Lưu lại" />
    <a href="javascript:void(0);" onClick="cancel_edit({DATA.post_id});" class="button2 nopadding">Hủy bỏ</a> 
	<a href="{DATA.edit_post}" class="button2 nopadding">Nâng cao</a> 
	<span id="posting_msg" class="hidden"> <img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/progress.gif" alt="Đang gửi trả lời nhanh - Xin đợi">&nbsp;<strong>Đang gửi trả - Xin đợi</strong> </span>
	</div>
</form>
<!-- END: main -->