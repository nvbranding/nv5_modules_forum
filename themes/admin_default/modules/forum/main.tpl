<!-- BEGIN: main -->

<!-- BEGIN: error -->

<div style="width: 780px;" class="quote">
  <blockquote class="error">
    <p> <span>{ERROR}</span> </p>
  </blockquote>
</div>
<div class="clear"></div>
<!-- END: error --> 

<!-- BEGIN: content -->
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<caption>
	  Danh sách chủ đề
	  </caption>
	  <thead>
	    <tr>
	      <th style="width:20px;text-align:center">ID</th>
	      <th style="width:20px;"> {LANG.sort} </th>
	      <th> {LANG.title} </th>
	      <th class="w100"> Thành viên </th>
	      <th class="w100"> {LANG.time_order} </th>
	      <!-- <th style="width:90px;" class="text-center"> {LANG.status} </th> -->
	      <th style="width:130px;white-space:nowrap;text-align:center"> {LANG.feature} </th>
	    </tr>
	  </thead>
	  <tbody>
	  
	<!-- BEGIN: loop -->
	  <tr>
	    <td style="width:20px;" class="text-center"><input type="checkbox" class="ck" value="{thread_id}" /></td>
	    <td style="width:20px;" class="text-center"> <strong>{loop.sort}</strong> </td>
	    <td style="width:200px;"><a class="title" href="{loop.link}" target="_blank"> {loop.title}</a> </td>
	    <td style="width:100px;"> {loop.username} </td>
	    <td style="width:100px;"> {loop.post_date} </td>
	    <!-- <td style="width:90px;" class="text-center">
			<select class="form-control" id="status_{loop.thread_id}" name="status" onchange="forum_change_status('{loop.thread_id}')">
				<option value="1">Kích hoạt</option>
				<option value="2">Ẩn chủ đề</option>
				<option value="3">Khóa chủ đề</option>
			</select>
		</td> -->
	    <td style="width:100px;white-space:nowrap;text-align:center">
			<span class="add_icon"><a href="{loop.link}">Xem chủ đề</a></span> 
			<!-- BEGIN: admin --> 
			<span class="edit_icon"><a href="{loop.edit_post}">{GLANG.edit}</a></span> &nbsp;&nbsp;
			<span class="delete_icon"><a href="javascript:void(0);"  onclick="forum_del_thread( {loop.thread_id}, {loop.catid}, '{loop.checkmod}' );">{GLANG.delete}</a></span> 
			<!-- END: admin -->
		</td>
	  </tr>
	<!-- END: loop -->
	  
	<tbody> 
	  
	  <tr class="footer">
	    <td colspan="4">
		<span>
				<a name="checkall" id="checkall" href="javascript:void(0);"><strong>Chọn hết</strong></a>
				&nbsp;&nbsp;-&nbsp;&nbsp; <a name="uncheckall" id="uncheckall" href="javascript:void(0);"><strong>Bỏ chọn</strong></a>&nbsp;&nbsp;
		</span>
			-
		<span class="delete_icon">
			<a class="delete" href="{URL_DEL}"><strong>Xóa</strong></a>
		</span>
		</td>
	    <td colspan="5"> <!-- BEGIN: generate_page -->{GENERATE_PAGE}<!-- END: generate_page --> </td>
	  </tr>
	</table>
</div>
<div id="posting_msg"></div>
<script type='text/javascript'>
    $(function(){
        $('#checkall').click(function(){
            $('input:checkbox').each(function(){
                $(this).attr('checked', 'checked');
            });
        });
        $('#uncheckall').click(function(){
            $('input:checkbox').each(function(){
                $(this).removeAttr('checked');
            });
        });
        $('.delete').click(function(){
			event.preventDefault();
            if (confirm("Bạn có chắc chắn muốn xóa các chủ đề này ?")) {
                var listall = [];
                $('input.ck:checked').each(function(){
                    listall.push($(this).val());
                });
                if (listall.length < 1) {
                    alert("Bạn cần chọn ít nhất một function dể xóa");
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    url: '{URL_DEL}',
                    data: 'action=delete_thread_list&listall=' + listall,
                    success: function(res){
						var obj = $.parseJSON(res);
						var message = obj.data.message;
						var items = obj.data.item;
	
						if (message == 'success') {
							 alert(items['message']);
							location.reload();	
						} else if ( message == 'unsuccess') {
							var a = "";
							$.each(items, function (i, item) {
								a += '' + item + '<br />';
							});
							$('#posting_msg').html('<div class="success" style="display: none;"><strong>' + a + ' </strong><span class="close" onclick="close();"><img src="' + nv_siteroot + 'themes/forum/images/close.png" alt="" class="close" /></span></div>').show();
							$('.success').fadeIn('slow');
							$(".close").click(function () {
								$('#posting_msg').hide();
							});
						}
                    }
                });
            }
        });
		
    });
</script>
<!-- END: content --> 

<!-- END: main -->