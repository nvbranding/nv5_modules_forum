<!-- BEGIN: main -->
<div id="forum-content">
    <!-- BEGIN: error_warning -->
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {error_warning}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <br>
    </div>
    <!-- END: error_warning -->
    <div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-pencil"></i> {CAPTION}</h3>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="{ACTION}" method="post" enctype="multipart/form-data" id="node-forum" class="form-horizontal">
				<input type="hidden" name ="node_type_id" value="{DATA.node_type_id}" />
				<input type="hidden" name ="node_id" value="{DATA.node_id}" />
				<input type="hidden" name ="parentid_old" value="{DATA.parent_id}" />
				<input name="save" type="hidden" value="1" />
				
				<div class="form-group required">
					<label class="col-sm-6 control-label" for="input-title">{LANG.node_category_title}</label>
					<div class="col-sm-18">
						<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.node_category_title}" id="input-title" class="form-control" />
						<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
					</div>
				</div>
				<div class="form-group">
                     <label class="col-sm-6 control-label" for="input-description">{LANG.node_category_description} </label>
                     <div class="col-sm-18">
                           {DATA.description}   
                      </div>
                </div>
				<div class="form-group">
					<label class="col-sm-6 control-label" for="input-parent">{LANG.node_sub_sl}</label>
					<div class="col-sm-18">
						<select class="form-control" name="parent_id">
							<!-- BEGIN: node -->
							<option value="{node.key}" {node.selected}>{node.name}</option>
							<!-- END: node -->
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label" for="input-title">{LANG.node_category_weight}</label>
					<div class="col-sm-18">
						<input type="text" name="weight" value="{DATA.weight}" placeholder="{LANG.node_category_weight}" id="input-weight" class="form-control" />
						<input type="hidden" name="old_weight" value="{DATA.weight}" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label" for="input-status">{LANG.node_category_status}</label>
					<div class="col-sm-18">
						<select name="status" id="input-status" class="form-control">
							<!-- BEGIN: status -->
							<option value="{STATUS.key}" {STATUS.selected}>{STATUS.name}</option>
							<!-- END: status -->
						</select>
					</div>
				</div>                    
				<div align="center">
					<button class="btn btn-primary" type="submit" id="submitform"><i class="fa fa-spinner fa-lg fa-spin" style="display:none"></i> {LANG.save} </button>
					 
				</div>          
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$('#submitform').on('click', function( e ){	
	CKEDITOR.instances.forum_description.updateElement(); 	
	var data = $('#node-forum').serialize();
	var action = $('#node-forum').attr('action');
	$.ajax({
		url: action + '&nocache=' + new Date().getTime(),
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$('#submitform').prop('disabled', true);
			$('#submitform i').show();
		},	
		complete: function() {
			$('#submitform').prop('disabled', false);
			$('#submitform i').hide();
		},
		success: function(json) {
			
			if ( json['error'] )
			{
				if ( json['error']['title'] ) alert( json['error']['title'] );
				if ( json['error']['db'] ) alert( json['error']['db'] );		
			} 
						
			if ( json['redirect'] ) location.href = json['redirect']; 
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$('#submitform').prop('disabled', false);
			$('#submitform i').hide();
		}
	});
	return false;
})
</script>
<!-- END: main -->