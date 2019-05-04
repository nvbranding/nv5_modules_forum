<!-- BEGIN: main -->
<div id="forum-content"> 
	<div class="titleBar">
		<div class="topCtrl">
			<a href="{ADD_NEW}" class="btn btn-success btn-xs" accesskey="a">+ Tạo mục mới</a>
		</div>
		<h1>Danh sách mục</h1>			
	</div>  
	<!-- BEGIN: catnav -->
	<div class="divbor1" style="margin-bottom: 10px">
		<!-- BEGIN: loop -->
		{CAT_NAV}
		<!-- END: loop -->
	</div>
	<!-- END: catnav -->
 
	<form action=" " class="xenForm formOverlay section" method="post">
	<h2 class="subHeading">
	<div class="FilterControls">
		<input type="search" name="filter" value="" placeholder="Filter items" results="10" class="textCtrl" id="ctrl_filter">
		
		<input type="button" name="clearfilter" value="Clear" title="Clear filter parameters" class="btn btn-success btn-xs">
	</div>
	Danh mục
	</h2>
	<ol class="FilterList">
		<!-- BEGIN: loop -->
		<li class="listItem primaryContent lev{LOOP.lev}" > 
			<a href="{LOOP.delete}" class="delete OverlayTrigger secondaryContent" title="Xóa..."><span>Xóa...</span></a>
			<div class="Popup dropdown">
				 
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Tạo mới <span class="caret"></span></a>
					<ul class="dropdown-menu">
					  <li><a href="{LOOP.create_sibling}">Mục ngang hàng </a></li>
					  <li><a href="{LOOP.create_child}">Mục con</a></li>
					</ul>	
				 
			</div> 
			<a href="{LOOP.moderators}" class="secondaryContent fixedOptionBox">Thêm điều hành</a> 
			<a href="{LOOP.permissions}" class="secondaryContent ">Quyền truy cập</a>
			<h4 class="node-status-{LOOP.status}">
				<a href="{LOOP.edit}">
					<em>{LOOP.title}</em>
					<dfn>{LOOP.node_type_id}</dfn>
				</a>
			</h4> 
		</li>
		<!-- END: loop -->
		 
	</ol>
	<!-- <p class="sectionFooter">Showing <span class="FilterListCount">5</span> of 5 items</p> -->
	<input type="hidden" name="token" value="{TOKEN}"> 
	</form> 
 

<script type="text/javascript">

$('#ctrl_filter').on('keydown, keyup', function(){
	
	var string = $('#ctrl_filter').val();
 
	if( string )
	{
		
		$('.FilterList li').hide();
		
		$('.FilterList li em').each(function(  ){
			var str = $(this).text();
			var res = str.match( new RegExp(string, 'gi') );
			if( res )
			{
				$(this).parent().parent().parent().show();	
			}
		});
		
	}else
	{
		$('.FilterList li').show();
	}

	
})

 
$('.formajax').on('change', function() {
 
	var action = $(this).attr('data-action');
	var token = $(this).attr('data-token');
	var node_id = $(this).attr('data-id');
	var new_vid = $(this).val();
	var id = $(this).attr('id');
	$.ajax({
		url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=node&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: 'action=' + action + '&node_id=' + node_id + '&new_vid=' + new_vid + '&token='+token,
		beforeSend: function() {
			$('#'+id ).prop('disabled', true);
			$('.alert').remove();
		},	
		complete: function() {
			$('#'+id ).prop('disabled', false);
		},
		success: function(json) {
			
			if ( json['error'] ) alert( json['error'] );	
			if ( json['new_vid'] == 0 || json['new_vid'] == 1){
				
				$('#id_favorite_'+node_id).val( json['new_vid'] );
				
			};	
			if ( json['link'] ) location.href = json['link'];
 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});


$('.delete').on('click', function( e ) {
	var obj = $(this);
	var action = $(this).attr('href');
	if( ! obj.hasClass('disabled') )
	{
		if ( confirm( 'Bạn có chắc muốn xóa chủ đề này ?' ) ) 
		{
			$.ajax({
				url: action + '&nocache=' + new Date().getTime(),
				type: 'post',
				dataType: 'json',
				data: '',
				beforeSend: function() {
					obj.addClass('disabled');
				},	
				complete: function() {
					obj.removeClass('disabled');
				},
				success: function(json) {
					
					if ( json['error'] ) alert( json['error'] );	
					 
					if ( json['redirect'] ) window.location.href = json['redirect'];
		 
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}
	
	e.preventDefault();
});

function delete_node(node_id, token) {
	if(confirm('{LANG.confirm}')) {
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=node&action=delete&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'node_id=' + node_id + '&token=' + token,
			beforeSend: function() {
				$('#button-delete i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
				$('#button-delete').prop('disabled', true);
			},	
			complete: function() {
				$('#button-delete i').replaceWith('<i class="fa fa-trash-o"></i>');
				$('#button-delete').prop('disabled', false);
			},
			success: function(json) {
				$('.alert').remove();

				if (json['error']) {
					$('#content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
				}
				
				if (json['success']) {
					$('#content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
					 $.each(json['id'], function(i, id) {
						$('#group_' + id ).remove();
					});
				}		
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

$('#button-delete').on('click', function() {
	if(confirm('{LANG.confirm}')) 
	{
		var listid = [];
		$("input[name=\"selected[]\"]:checked").each(function() {
			listid.push($(this).val());
		});
		if (listid.length < 1) {
			alert("{LANG.please_select_one}");
			return false;
		}
	 
		$.ajax({
			url: script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=node&action=delete&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'listid=' + listid + '&token={TOKEN}',
			beforeSend: function() {
				$('#button-delete i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
				$('#button-delete').prop('disabled', true);
			},	
			complete: function() {
				$('#button-delete i').replaceWith('<i class="fa fa-trash-o"></i>');
				$('#button-delete').prop('disabled', false);
			},
			success: function(json) {
				$('.alert').remove();
 
				if (json['error']) {
					$('#content').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
				}
				
				if (json['success']) {
					$('#content').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
					 $.each(json['id'], function(i, id) {
						$('#group_' + id ).remove();
					});
				}		
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}	
});

</script>
<!-- END: main -->
 

