<!-- BEGIN: main -->
<div id="forum-content"> 
	<div class="titleBar">
		<h1>Cấp quyền mục: {NODE_TITLE} - {USER_TITLE}</h1>	
	</div>
	<form action="{ACTION}" class="xenForm formOverlay PermissionChoices" method="post">
		
		<div style="display: none">

			<span class="PermissionTooltipOption" data-permissionstate="unset">The value of this permission will be inherited from a parent node or the global permissions, unless the default permissions have been revoked in another applicable permission set.</span>
			<span class="PermissionTooltipOption" data-permissionstate="allow content_allow">This permission is granted unless a "Never" value is found in another applicable permission set.</span>
			<span class="PermissionTooltipOption" data-permissionstate="reset">This permission is granted only if an "Allow" value is explicitly specified in another applicable permission set on this node.</span>
			<span class="PermissionTooltipOption" data-permissionstate="deny">This permission is denied in all circumstances. A "Never" cannot be overridden.</span>

		</div>

		<fieldset id="piGroups">

			<table class="permissions quickCheckAll">
				<tbody>
					<tr class="groupHeading">
						<th class="groupTitle secondaryContent"><span>Chọn nhanh tất cả</span></th>

						<th class="option unset">
							<label class="CheckAll" data-target="#piGroups input:radio[value=unset], #piGroups .integer input:radio[value=0]">Mặc định</label>
						</th>

						<th class="option content_allow">
							<label class="CheckAll" data-target="#piGroups input:radio[value=content_allow]">Cho phép</label>
						</th>

						<th class="option reset">
							<label class="CheckAll" data-target="#piGroups input:radio[value=reset]">Thu hồi</label>
						</th>

						<th class="option deny">
							<label class="CheckAll" data-target="#piGroups input:radio[value=deny]">Không bao giờ</label>
						</th>

						<th class="help secondaryContent">
							<a href="#" target="_blank">Permissions Help</a>
						</th>
					</tr>
				</tbody>
			</table>
 
			<table class="permissions">
				<tbody>
					<tr class="groupHeading">
						<th class="groupTitle">&nbsp;</th>
						<th class="option">Inherit</th>
						<th class="option">Allow</th>
						<th class="option">Revoke</th>
						<th class="option">Never</th>
						<th>&nbsp;</th>
					</tr>
					<tr class="permission">
						<th>View node:</th>
						<!-- BEGIN: viewNode -->
						<td class="option {permissionChoices.value}"><label>
							<input type="radio" name="permissions[general][viewNode]" value="{permissionChoices.value}" {permissionChoices.checked}>
							</label>
						</td>
						<!-- END: viewNode --> 
						<td class="description">&nbsp;</td>
					</tr>
				</tbody>
			</table>

			<!-- BEGIN: permission_interface_group -->
			<table class="permissions" id="pg_{interfaceGroup.key}">
				<tbody>
					<tr class="groupHeading">
						<th class="groupTitle">{interfaceGroup.name}</th>

						<th class="option"><a class="CheckAll" data-target="#pg_{interfaceGroup.key} input:radio[value='unset']">Inherit</a></th>

						<th class="option"><a class="CheckAll"  data-target="#pg_{interfaceGroup.key} input:radio[value='content_allow']">Allow</a></th>

						<th class="option"><a class="CheckAll" data-target="#pg_{interfaceGroup.key} input:radio[value='reset']">Revoke</a></th>

						<th class="option"><a class="CheckAll" data-target="#pg_{interfaceGroup.key} input:radio[value='deny']">Never</a></th>

						<th>&nbsp;</th>
					</tr>
					<!-- BEGIN: permission_group -->
					<tr class="permission">
						<th>{PERMISSION.name}:</th>
						
						<!-- BEGIN: permissionChoices -->
						<td class="option {permissionChoices.value}">
							<label>
								<input type="radio" name="permissions[{PERMISSION.permission_group_id}][{PERMISSION.permission_id}]" value="{permissionChoices.value}" {permissionChoices.checked}>
							</label>
						</td>
						<!-- END: permissionChoices -->
						<!-- BEGIN: integer -->						 
						<td colspan="5" class="{PERMISSION.permission_type}">
							<label><input class="resetChoice" type="radio" name="permissions[{PERMISSION.permission_group_id}][{PERMISSION.permission_id}]" value="0" {UNSET_CHECKED}> Inherit</label>
							<label><input type="radio" name="permissions[{PERMISSION.permission_group_id}][{PERMISSION.permission_id}]" value="-1" {UNLIMITED_CHECKED}> Unlimited</label>
							<input type="radio" name="permissions[{PERMISSION.permission_group_id}][{PERMISSION.permission_id}]" value="1" {LIMITED_CHECKED} class="Disabler" id="ctrl_forum_{PERMISSION.permission_id}">
							<span id="ctrl_forum_{PERMISSION.permission_id}_Disabler" class="{DISABLED_CLASS}" >
								<input type="text" name="permissions[{PERMISSION.permission_group_id}][{PERMISSION.permission_id}]" value="{VALUE}"  size="5" step="1" min="0" shiftStep="0" maxlength="6" class="textCtrl autoSize number SpinBox {DISABLED_CLASS}" id="ctrl_{interfaceGroup.key}{PERMISSION.permission_id}" autocomplete="off" {DISABLED}>
								<input type="button" class="button spinBoxButton up {DISABLED_CLASS}" value="+" {DISABLED}>
								<input type="button" class="button spinBoxButton down {DISABLED_CLASS}" value="-" {DISABLED}>
							</span>
						</td>
						<script type="text/javascript"> 
						$('.integer input[type="radio"]').on('click', function(){
								 
								if( $(this).val() == '1' )
								{  
					
									$('#ctrl_{interfaceGroup.key}{PERMISSION.permission_id}').prop('disabled', false).removeClass('disabled');
									$('#ctrl_forum_{PERMISSION.permission_id}_Disabler').removeClass('disabled');
									$('#ctrl_forum_{PERMISSION.permission_id}_Disabler input.spinBoxButton').prop('disabled', false).removeClass('disabled');
									 
								}else 
								{
									$('#ctrl_{interfaceGroup.key}{PERMISSION.permission_id}').prop('disabled', true).addClass('disabled');
									$('#ctrl_forum_{PERMISSION.permission_id}_Disabler').addClass('disabled');
									$('#ctrl_forum_{PERMISSION.permission_id}_Disabler input.spinBoxButton').prop('disabled', true).addClass('disabled');
								}
							})
						</script>
						<!-- END: integer -->
						
						<td class="description">&nbsp;</td>

					</tr>
					<!-- END: permission_group -->
 
				</tbody>
			</table>
			<!-- END: permission_interface_group -->
		
		</fieldset>

		

		<dl class="ctrlUnit submitUnit"><dt></dt>
			<dd>
				<input type="hidden" name="node_id" value="{NODE_ID}">
				<input type="hidden" name="userid" value="{USER_ID}">
				<input type="hidden" name="token" value="{TOKEN}">
				<input type="hidden" name="save" value="1">
				<button class="btn btn-primary" type="submit" id="submitform"><i class="fa fa-spinner fa-lg fa-spin" style="display:none"></i> Cập nhật quyền </button>
			</dd>
		</dl>
		<div class="clear"></div>		 
	</form>
</div>
<script type="text/javascript">
	$('.CheckAll').on('click', function() {
		var target = $(this).attr('data-target');
		$(target).prop('checked', true);
	});
	$('.quickCheckAll .unset .CheckAll').click(function() {
		var $container = $('#piGroups');
		$container.find('.integer input[type=text]').val(0);
		if (!$container.find('.integer .resetChoice').length)
		{
			$container.find('.integer input:radio[value=1]').prop('checked', true)
			
		}
	});
	$('input[type="text"].spinBoxButton').on('change, keyup, keydown', function(){

		var min = parseFloat($(this).attr('min'));
		var step = parseFloat($(this).attr('step'));
		var shiftStep = parseFloat($(this).attr('shiftStep'));	
		var new_val = $(this).val();
		if( new_val < min )
		{
			$(this).val( min );
		}else if( ( new_val > shiftStep && shiftStep > 0  ) )
		{
			$(this).val( shiftStep );	
		}
	})
	$('#submitform').on('click', function(){
		$('#submitform').prop('disabled', true);
		$('#submitform i').show();
	});
	$('.spinBoxButton').click(function(e){
		
		var id= $(this).parent().attr('id');
		var spinbox = $('#'+ id).find('input[type="text"]').attr('id');
		var step = parseFloat($('#'+ spinbox).attr('step'));
		var shiftStep = parseFloat($('#'+ spinbox).attr('shiftStep')); 
		var min = $('#'+ spinbox).attr('min');
		var oldval = $('#'+ spinbox).val();
		if( $(this).hasClass('up') )
		{
			var new_val = parseFloat( oldval ) + step;
			if( ( new_val <= shiftStep  && shiftStep > 0 ) || ( shiftStep == 0 ) )
			{
				$('#'+ spinbox).val( new_val );
			}
			
		}else if( $(this).hasClass('down') ){
			var new_val = parseFloat( oldval ) - step;
			if( new_val >= 0  )
			{
				$('#'+ spinbox).val( new_val );
			}
			
		}
		
	});
 
</script>
<!-- END: main -->
 

