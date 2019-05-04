<!-- BEGIN: main -->
<div id="forum-content"> 
	<div class="titleBar">
		<div class="topCtrl">
			<a href="{ADD_NEW}" class="btn btn-success btn-xs" accesskey="a">+ Thêm điều hành viên</a>
		</div>
		<h1>Danh sách điều hành viên</h1>			
	</div>  
	<!-- BEGIN: super_mod -->
	<form action="" class="xenForm formOverlay section" method="post">
		
		<h2 class="subHeading">
			Điều hành viên cao cấp
		</h2>

		<ol class="FilterList Scrollable">
			<!-- BEGIN: loop -->
			<li class="listItem primaryContent" id="_supermod_{MOD.userid}">
				<a href="{MOD.delete}" data-token="{MOD.token}" class="delete OverlayTrigger secondaryContent" title="Delete..."><span>Delete...</span></a>
				<a href="{MOD.edit}" class="secondaryContent">User Info</a>
				<h4> 
					<img src="{MOD.photo}" alt="" class="listAvatar">
					<a href="{MOD.edit}">
						<em>{MOD.username}</em>
					</a>
				</h4>
			</li>
			<!-- END: loop -->
		</ol>
	
		<!-- <p class="sectionFooter">Showing <span class="FilterListCount">1</span> of 1 items</p> -->

	</form>
	<!-- END: super_mod -->	
	<!-- BEGIN: normal_mod -->
	<form action="" class="xenForm formOverlay section" method="post">

	<h2 class="subHeading">
		Ban quản trị nội dung
	</h2>

	<ol class="FilterList Scrollable">
		<!-- BEGIN: loop -->
		<li>
			<h3 class="textHeading"><a href="{MOD.edit}" class="concealed">{MOD.username}</a></h3>
			<ol>

				<li class="listItem primaryContent" id="_{MOD.moderator_id}">
					<a href="{MOD.delete}" data-token="{MOD.token}" id="delete-{MOD.moderator_id}" class="delete OverlayTrigger secondaryContent" title="Delete..."><span>Delete...</span></a>

					<h4>
						<a href="{MOD.edit}">
						<em>{MOD.node.node_type_id} - {MOD.node.title}</em>
					</a></h4>
				</li>

			</ol>
		</li>
		<!-- END: loop -->
	</ol>

	<!-- <p class="sectionFooter">Showing <span class="FilterListCount">1</span> of 1 items</p> -->
	
 </form>
<!-- END: normal_mod -->
<script type="text/javascript">
 
$(document).ready(function(){  
  
	var hashValue = location.hash;

	hashValue = hashValue.replace(/^#/, '');  
 
	$('#'+ hashValue).addClass('last');
}); 
$('.delete').on('click', function( e ) {
	var obj = $(this);
	var action = $(this).attr('href');
	var token = $(this).attr('data-token');
	if( ! obj.hasClass('disabled') )
	{
		if ( confirm( 'Bạn có chắc muốn xóa điều hành viên này ?' ) ) 
		{
			$.ajax({
				url: action + '&nocache=' + new Date().getTime(),
				type: 'post',
				dataType: 'json',
				data: 'token=' + token,
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
					alert(thrownError + "rn" + xhr.statusText + "rn" + xhr.responseText);
				}
			});
		}
	}
	
	e.preventDefault();
});
 
</script>
<!-- END: main -->
 

