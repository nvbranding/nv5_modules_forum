<!-- BEGIN: main -->
<script>
function move_thread(thread_id, catid)
{	
	var url = $('#quick_mod_move').attr('action');
    var action = $('#action').val();
	var newcatid = $('#newcatid').val();
	var checkss_quickmod = $('#checkss_quickmod').val();

     $.ajax({
		 type: "POST",
		 url: url,
         data: 'action=' + action + '&checkss_quickmod=' + checkss_quickmod + '&catid=' + catid + '&newcatid=' + newcatid + '&thread_id=' + thread_id + '&nocache=' + new Date().getTime(),
		 success: function (res){
			 var obj = $.parseJSON(res);
             var message = obj.data.message;
             var items = obj.data.item;

             if ( message == 'success' ) 
			 {
				 alert(items['message']);
				 window.location.href = items['link'];
			 } else if ( message == 'unsuccess' ) 
			 {
				 alert(items['message']);
			 }
		 }
	 });
	
	return false;
}
</script>

<div class="quick_mod_move">
	<span class="close" onclick="close();"><img src="{linksite}/images/close.png" alt="" class="close" /></span>
	<form class="form-inline" id="quick_mod_move" onSubmit="return move_thread('{thread_id}', '{catid}');" method="post" action="{QUICK_MOD}" style="width: 100%;">
	  <input type="hidden" name="checkss_quickmod" id="checkss_quickmod" value="{checkss_quickmod}" />
	  <input type="hidden" name="action" id="action" value="move_thread" />
	  <span>Chọn diễn đàn cần di chuển tới :</span>
		<select class="form-control" name="newcatid" id="newcatid">
		  <!-- BEGIN: cat -->
			<option value="{CAT.catid}" {CAT.selected} {CAT.disabled}>{CAT.title} </option>
		<!-- END: cat -->
		</select>
	  <div style="padding-top:6px;text-align: center;">
		<input id="submit" type="submit" class="post_button" value="Di chuyển" />
		<span id="quick_mod_move_msg" class="hidden"> <img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/progress.gif" alt="Đang gửi yêu cầu - Xin đợi">&nbsp;<strong>Đang gửi yêu cầu - Xin đợi</strong> </span>
	 </div>
	</form>
</div>
<!-- END: main -->