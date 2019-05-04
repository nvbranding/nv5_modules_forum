<!-- BEGIN: error -->
<div id="forum-content"> 
	<div class="titleBar">
		<h1>{ERROR}</h1>						
	</div> 
</div> 
<!-- END: error -->

 
<!-- BEGIN: edituser -->
<div id="forum-content"> 
	<div class="titleBar">
 
		<h1>Cập nhật điều hành viên: {MOD_CONTENT.username}</h1>			
				
	</div> 
	<form action="{ACTION}" id="form-moderators" class="xenForm formOverlay AutoValidator" data-redirect="yes" method="post">
	
		<fieldset>

			<dl class="ctrlUnit">
				<dt>Type of Moderator:</dt>
				<dd>

					Forum - diễn đàn con

				</dd>
			</dl>

		</fieldset>

		<fieldset>

			<dl class="ctrlUnit">
				<dt></dt>
				<dd>
					<ul>
						<li>
							<label for="ctrl_is_staff_1">
								<input type="checkbox" name="is_staff" value="1" id="ctrl_is_staff_1" {IS_STAFF}> Display user as staff</label>
							<p class="hint">If selected, this user will be listed publicly as a staff member.</p>
						</li>
					</ul>
				</dd>
			</dl>

			<dl class="ctrlUnit">
				<dt>Add Moderator to User Groups: <dfn><br><label><input type="checkbox" class="CheckAll" data-target="#addUserGroups"> Select All</label></dfn></dt>
				<dd>
					<ul class="checkboxColumns" id="addUserGroups">
						<!-- BEGIN: group -->
						<li>
							<label for="ctrl_extra_user_group_ids_{GROUP.group_id}">
								<input type="checkbox" name="extra_user_group_ids[]" value="{GROUP.group_id}" id="ctrl_extra_user_group_ids_{GROUP.group_id}" {GROUP.checked}> {GROUP.title}
							</label>
						</li>
						<!-- END: group --> 
					</ul>
				</dd>
			</dl>

		</fieldset>

		<div id="piGroups">

			<label class="secondaryContent">
				<input type="checkbox" class="CheckAll" data-target="#piGroups"> Select All</label>
			<!-- BEGIN: interface_group -->
			<fieldset id="ifgi-{IFGI}">

				<dl class="ctrlUnit">
					<dt>{TITLE}: <dfn><br><label><input type="checkbox" class="CheckAll" data-target="#ifgi-{IFGI}"> Select All</label></dfn></dt>
					<dd>
						<ul class="checkboxColumns">
							<!-- BEGIN: permission -->	
							<li>
								<label for="ctrl_general_moderator_permissionsgeneral{PERMISSION.permission_id}">
									<input type="checkbox" name="{PERMISSION.name}[{PERMISSION.permission_group_id}][{PERMISSION.permission_id}]" value="1" id="ctrl_general_moderator_permissionsgeneral{PERMISSION.permission_id}" {PERMISSION.checked}> {PERMISSION.title}
								</label>
							</li>
							<!-- END: permission -->
  
						</ul>
					</dd>
				</dl>

			</fieldset>
			<!-- END: interface_group -->
			
		</div>

		<dl class="ctrlUnit submitUnit"><dt></dt>
			<dd>
					<button class="btn btn-primary" type="submit" id="submitform"><i class="fa fa-spinner fa-lg fa-spin" style="display:none"></i> Cập nhật điều hành viên </button>
			
			</dd>
		</dl>
		<br>
		<input type="hidden" name="userid" value="{MOD_CONTENT.userid}">
		<input type="hidden" name="username" value="{MOD_CONTENT.username}">
		<input type="hidden" name="is_super_moderator" value="{MOD_CONTENT.is_super_moderator}">
		<input type="hidden" name="content_type" value="{MOD_CONTENT.content_type}">
		<input type="hidden" name="content_id" value="{MOD_CONTENT.content_id}">
		<input type="hidden" name="save" value="1">
		<input type="hidden" name="token" value="{TOKEN}">
	</form>
</div>
<script type="text/javascript">
$('.CheckAll').on('click', function() {
	var target = $(this).attr('data-target');
	if( $(this).is(':checked') )
	{
		$(target).find('input[type="checkbox"]').prop('checked', true);	
	}else{
		$(target).find('input[type="checkbox"]').prop('checked', false);	
	
	}	
});
$('ul.checkboxColumns').each(function(){
	var numberNotChecked = $(this).find('input:checkbox:not(":checked")').length;
	if( numberNotChecked == 0 )
	{
		$(this).parent().parent().parent().find('.CheckAll').prop('checked', true);
	}
})

$('#form-moderators').submit(function(e) {
	var form = $(this);
	$.ajax({
		type: form.attr('method'),
        url: form.attr('action'),
        data: form.serialize(),
		dataType: 'json',
		beforeSend: function() {
			$('#submitform').prop('disabled', true);
			$('#submitform i').show();
		},	
		complete: function() {
			$('#submitform').prop('disabled', false);
			$('#submitform i').show();
		},
		success: function(json) {
			if( json['redirect'] )
			{
				window.location.href =json['redirect']; 
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "rn" + xhr.statusText + "rn" + xhr.responseText);
			$('#submitform').prop('disabled', false);
			$('#submitform i').show();
		}
	});
	e.preventDefault();
});

</script>
<!-- END: edituser -->
  