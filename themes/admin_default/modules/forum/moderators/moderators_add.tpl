<!-- BEGIN: error -->
<div id="forum-content"> 
	<div class="titleBar">
		<h1>{ERROR}</h1>						
	</div> 
</div> 
<!-- END: error -->

<!-- BEGIN: main -->
<div id="forum-content"> 
	<div class="titleBar">
		<h1>Tạo điều hành viên mới</h1>			
	</div>  
	<form action="{ACTION}" method="post" class="xenForm formOverlay">
		<!-- BEGIN: error -->
		<div class="error">{ERROR}</div>
		<!-- END: error -->
		<dl class="ctrlUnit">
			<dt><label for="ctrl_username">Moderator User Name:</label></dt>
			<dd>
				<span class="spansearch">
					<input type="search" name="username" value="{MODERATORS.username}" placeholder="User Name..." results="5" class="textCtrl AcSingle" id="ctrl_username" autocomplete="off">
					<i class="fa fa-times"></i>
				</span>
			</dd>
		</dl>

		<dl class="ctrlUnit">
			<dt>Type of Moderator:</dt>
			<dd>
				<ul>
					<li>
						<label for="ctrl_type__super">
							<input type="radio" name="type_mod" value="super" id="ctrl_type__super" {SUPER_MOD}> Super Moderator
						</label>
						<p class="hint">A super moderator can moderate the entire board.</p>
					</li>
					<li>
						<label for="ctrl_type_node">
							<input type="radio" name="type_mod" value="node" class="Disabler" id="ctrl_type_node" {FORUM_MOD}> Forum Moderator:
						</label>
							
						<ul id="ctrl_type_node_Disabler" class="disablerList {CLASS_DISABLED}">
							<li>
								<select name="node_id" class="textCtrl {CLASS_DISABLED}" id="ctrl_type_idnode" {NODE_DISABLED}>
									<option value="0" >  </option>	
									<!-- BEGIN: node -->
									<option value="{NODE.key}" {NODE.selected}>{NODE.name}</option>
									<!-- END: node -->
								</select>
							</li>
						</ul>
					</li>
				</ul>
			</dd>
		</dl>

		<dl class="ctrlUnit submitUnit"><dt></dt>
			<dd>
					<button class="btn btn-primary" type="submit" id="submitform"><i class="fa fa-spinner fa-lg fa-spin" style="display:none"></i> Thêm điều hành viên </button>
			
			</dd>
		</dl>

		<input type="hidden" name="adduser" value="1">
		<input type="hidden" name="token" value="{TOKEN}">
	</form>
	 
</div>
<script type="text/javascript">
$('#submitform').on('click', function(){
	$('#submitform').prop('disabled', true);
	$('#submitform i').show();
});
$('input[name="type_mod"]').on('click', function(){
	if( $(this).val() == 'node' )
	{
		$('#ctrl_type_idnode').prop('disabled', false).removeClass('disabled');
		$('#ctrl_type_node_Disabler').removeClass('disabled');
	}else{
		$('#ctrl_type_idnode').prop('disabled', true).addClass('disabled');
		$('#ctrl_type_node_Disabler').addClass('disabled');
 
	}

})
$('input[name="username"]').on('keydown, keyup', function(){
	if( $(this).val() != '')
	{
		$('.spansearch i').show();
	}else{
		$('.spansearch i').hide();
	}	
});
$('.spansearch i').on('click', function(){
	$(this).hide();
	$('input[name="username"]').val('');
});
$('input[name="username"]').autofill({
	'source': function(request, response) {
		if( $('input[name="username"').val().length > 2 )
		{	 
			$.ajax({
				url: '{JSON_URL}&action=getUsername&username=' +  encodeURIComponent(request) + '&nocache=' + new Date().getTime(),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['username'],
							value: item['userid']
						}
					}));
				}
			});
		}
	},
	'select': function(item) {
		$('input[name="username"]').val( item['label'] );
		
	}
});

</script> 
<!-- END: main -->
 
<!-- BEGIN: adduser -->
<div id="forum-content"> 
	<div class="titleBar">
 
		<h1>Cập nhật điều hành viên: {USER.username}</h1>			
				
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
								<label for="ctrl_{PERMISSION.name}{PERMISSION.permission_group_id}{PERMISSION.permission_id}">
									<input type="checkbox" name="{PERMISSION.name}[{PERMISSION.permission_group_id}][{PERMISSION.permission_id}]" value="1" id="ctrl_{PERMISSION.name}{PERMISSION.permission_group_id}{PERMISSION.permission_id}" {PERMISSION.checked}> {PERMISSION.title}
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
		<input type="hidden" name="userid" value="{USER.userid}">
		<input type="hidden" name="username" value="{USER.username}">
		<input type="hidden" name="is_super_moderator" value="{DATA.is_super}">
		<input type="hidden" name="content_type" value="{DATA.content_type}">
		<input type="hidden" name="content_id" value="{DATA.node_id}">
		<input type="hidden" name="adduser" value="2">
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
<!-- END: adduser -->
  