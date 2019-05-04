
<!-- BEGIN: insert_1 -->
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
			<div class="pull-right">
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary" title="{LANG.save}"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-node" class="form-horizontal">
				<input type="hidden" name ="node_type_id" value="{DATA.node_type_id}" />
				<input type="hidden" name ="node_id" value="{DATA.node_id}" />
				<input type="hidden" name ="parentid_old" value="{DATA.parent_id}" />
				<input name="save" type="hidden" value="1" />
                    
				
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-parent">{LANG.node_type}</label>
					<div class="col-sm-20">
						<select class="form-control" name="type" id="forum_typex">
							<!-- BEGIN: type -->
							<option value="{TYPE.key}" {TYPE.selected}>{TYPE.name}</option>
							<!-- END: type -->
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-parent">{LANG.node_sub_sl}</label>
					<div class="col-sm-20">
						<select class="form-control" name="parent_id">
							<!-- BEGIN: node -->
							<option value="{node.key}" {node.selected}>{node.name}</option>
							<!-- END: node -->
						</select>
					</div>
				</div>
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-title">{LANG.node_title}</label>
					<div class="col-sm-20">
						<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.node_title}" id="input-title" class="form-control" />
						<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
					</div>
				</div>
				<div class="form-group">
                    <label class="col-sm-4 control-label" for="input-alias">{LANG.node_alias} <i class="fa fa-refresh fa-lg icon-pointer" onclick="get_alias( );">&nbsp;</i></label>
                    <div class="col-sm-20">
						<input class="form-control" name="alias" placeholder="{LANG.node_alias}"  type="text" value="{DATA.alias}" maxlength="255" id="input-alias"/>
							 
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-password">{LANG.node_password}</label>
					<div class="col-sm-20">
						<input type="text" name="password" value="{DATA.password}" placeholder="{LANG.node_password}" id="input-password" class="form-control" />
						<!-- BEGIN: error_password --><div class="text-danger">{error_password}</div><!-- END: error_password -->
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">{LANG.node_image}</label>
						<div class="col-sm-20">
							<input class="form-control" type="text" name="image" value="{DATA.image}" id="id_forum_image" style="display: inline-block;vertical-align: middle;min-width: 295px; max-width: 400px">
							&nbsp;
							<button type="button" class="btn btn-info" id="img_forum_image">
								<i class="fa fa-folder-open-o">&nbsp;</i> Browse server
							</button>
						</div>
				</div>
				<div class="form-group forum_link">
					<label class="col-sm-4 control-label">{LANG.node_link}</label>
					<div class="col-sm-20">
						<input class="form-control" type="text" name="link" value="{DATA.link}">
					</div>
				</div>				
				<div class="form-group rules_link">
					<label class="col-sm-4 control-label">{LANG.node_rules_link}</label>
					<div class="col-sm-20">
						<input class="form-control" type="text" name="rules_link" value="{DATA.rules_link}">
					</div>
				</div>	
                <div class="form-group forum_rules">
                     <label class="col-sm-4 control-label" for="input-rules">{LANG.node_rules} </label>
                     <div class="col-sm-20">
                          {DATA.rules}       
                     </div>
                </div>
                 <div class="form-group">
                     <label class="col-sm-4 control-label" for="input-description">{LANG.node_description} </label>
                     <div class="col-sm-20">
                           {DATA.description}   
                      </div>
                 </div>
 
	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-status">{LANG.node_show_status}</label>
					<div class="col-sm-20">
						<select name="status" id="input-status" class="form-control">
							<!-- BEGIN: status -->
							<option value="{STATUS.key}" {STATUS.selected}>{STATUS.name}</option>
							<!-- END: status -->
						</select>
					</div>
				</div>                    
				<div align="center">
					<input class="btn btn-primary" type="submit" value="{LANG.save}">
					<a class="btn btn-default" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>          
			</form>
		</div>
	</div>
</div>

<!-- END: insert_1 -->


<!-- BEGIN: main -->
<div id="photo-content">
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
			<div class="pull-right">
				<button type="submit" form="form-stock" data-toggle="tooltip" class="btn btn-primary" title="{LANG.save}"><i class="fa fa-save"></i></button> 
				<a href="{CANCEL}" data-toggle="tooltip" class="btn btn-default" title="{LANG.cancel}"><i class="fa fa-reply"></i></a>
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="" method="post"  enctype="multipart/form-data" id="form-node" class="form-horizontal">
				<input type="hidden" name ="node_id" value="{DATA.node_id}" />
				<input type="hidden" name ="parentid_old" value="{DATA.parent_id}" />
				<input name="save" type="hidden" value="1" />
                    
				
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-parent">{LANG.node_type}</label>
					<div class="col-sm-20">
						<select class="form-control" name="type" id="forum_typex">
							<!-- BEGIN: type -->
							<option value="{TYPE.key}" {TYPE.selected}>{TYPE.name}</option>
							<!-- END: type -->
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-parent">{LANG.node_sub_sl}</label>
					<div class="col-sm-20">
						<select class="form-control" name="parent_id">
							<!-- BEGIN: node -->
							<option value="{node.key}" {node.selected}>{node.name}</option>
							<!-- END: node -->
						</select>
					</div>
				</div>
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-title">{LANG.node_title}</label>
					<div class="col-sm-20">
						<input type="text" name="title" value="{DATA.title}" placeholder="{LANG.node_title}" id="input-title" class="form-control" />
						<!-- BEGIN: error_title --><div class="text-danger">{error_title}</div><!-- END: error_title -->
					</div>
				</div>
				<div class="form-group">
                    <label class="col-sm-4 control-label" for="input-alias">{LANG.node_alias} <i class="fa fa-refresh fa-lg icon-pointer" onclick="get_alias( );">&nbsp;</i></label>
                    <div class="col-sm-20">
						<input class="form-control" name="alias" placeholder="{LANG.node_alias}"  type="text" value="{DATA.alias}" maxlength="255" id="input-alias"/>
							 
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-password">{LANG.node_password}</label>
					<div class="col-sm-20">
						<input type="text" name="password" value="{DATA.password}" placeholder="{LANG.node_password}" id="input-password" class="form-control" />
						<!-- BEGIN: error_password --><div class="text-danger">{error_password}</div><!-- END: error_password -->
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">{LANG.node_image}</label>
						<div class="col-sm-20">
							<input class="form-control" type="text" name="image" value="{DATA.image}" id="id_forum_image" style="display: inline-block;vertical-align: middle;min-width: 295px; max-width: 400px">
							&nbsp;
							<button type="button" class="btn btn-info" id="img_forum_image">
								<i class="fa fa-folder-open-o">&nbsp;</i> Browse server
							</button>
						</div>
				</div>
				<div class="form-group forum_link">
					<label class="col-sm-4 control-label">{LANG.node_link}</label>
					<div class="col-sm-20">
						<input class="form-control" type="text" name="link" value="{DATA.link}">
					</div>
				</div>				
				<div class="form-group rules_link">
					<label class="col-sm-4 control-label">{LANG.node_rules_link}</label>
					<div class="col-sm-20">
						<input class="form-control" type="text" name="rules_link" value="{DATA.rules_link}">
					</div>
				</div>	
                <div class="form-group forum_rules">
                     <label class="col-sm-4 control-label" for="input-rules">{LANG.node_rules} </label>
                     <div class="col-sm-20">
                          {DATA.rules}       
                     </div>
                </div>
                 <div class="form-group">
                     <label class="col-sm-4 control-label" for="input-description">{LANG.node_description} </label>
                     <div class="col-sm-20">
                           {DATA.description}   
                      </div>
                 </div>
 
	 
				<div class="form-group">
					<label class="col-sm-4 control-label" for="input-status">{LANG.node_show_status}</label>
					<div class="col-sm-20">
						<select name="status" id="input-status" class="form-control">
							<!-- BEGIN: status -->
							<option value="{STATUS.key}" {STATUS.selected}>{STATUS.name}</option>
							<!-- END: status -->
						</select>
					</div>
				</div>                    
				<div align="center">
					<input class="btn btn-primary" type="submit" value="{LANG.save}">
					<a class="btn btn-default" href="{CANCEL}" title="{LANG.cancel}">{LANG.cancel}</a> 
				</div>          
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
    $('.forum_link').hide();
    $('#forum_typex').on("change", function() {
      if($(this).val() == 3 ){
         $('.forum_link').show();
          $('.rules_link').hide();
           $('.forum_rules').hide();
      }else{
         $('.forum_link').hide();
          $('.rules_link').show();
           $('.forum_rules').show();
      }
    });

    $("#img_forum_image").click(function() {
        var area = "id_forum_image";
        var path = "{PATH}";
        var currentpath = "{CURRENT_PATH}";
        var type = "image";
        nv_open_browse(script_name + "?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", 850, 420, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
        return false;
    });

<!-- BEGIN: getalias -->
$("#input-title").change(function() {
	get_alias('node', {DATA.node_id});
});
<!-- END: getalias -->
//]]>
</script>
<!-- END: main -->