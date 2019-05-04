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
	      <th style="width:20px;"> {LANG.sort} </th>
	      <th> {LANG.title} </th>
	      <th class="w100"> Thành viên </th>
	      <th class="w100"> {LANG.time_order} </th>
	      <th style="width:90px;" class="text-center"> {LANG.status} </th>
	      <th style="width:130px;white-space:nowrap;text-align:center"> {LANG.feature} </th>
	    </tr>
	  </thead>
	  <tbody>
	  
	<!-- BEGIN: loop -->
	  <tr>
	    <td style="width:20px;" class="text-center"> <strong>{loop.sort}</strong> </td>
	    <td style="width:200px;"><a href="{loop.link}"> {loop.title}</a> </td>
	    <td style="width:100px;"> {loop.username} </td>
	    <td style="width:100px;"> {loop.post_date} </td>
	    <td style="width:90px;" class="text-center">
			<select class="form-control" id="status_{loop.thread_id}" name="status" onchange="forum_change_status('{loop.thread_id}')">
				<option value="1">Kích hoạt</option>
				<option value="2">Ẩn chủ đề</option>
				<option value="3">Khóa chủ đề</option>
			</select>
		</td>
	    <td style="width:100px;white-space:nowrap;text-align:center">
			<span class="add_icon"><a href="{loop.link}">Xem chủ đề</a></span> 
			<!-- BEGIN: admin --> 
			<span class="edit_icon"><a href="{loop.edit_post}">{GLANG.edit}</a></span> &nbsp;&nbsp;
			<span class="delete_icon"><a href="javascript:void(0);" onclick="forum_del_thread({loop.thread_id});">{GLANG.delete}</a></span> 
			<!-- END: admin -->
		</td>
	  </tr>
	<!-- END: loop -->
	  
	<tbody> 
	  <!-- BEGIN: generate_page -->
	  <tr class="footer">
	    <td colspan="8"> {GENERATE_PAGE} </td>
	  </tr>
	  <!-- END: generate_page -->
	</table>
</div>
<!-- END: content --> 

<!-- END: main -->