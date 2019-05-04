<!-- BEGIN: main -->
<div id="module_show_list">
	{CAT_LIST}
</div>
<br />

<div id="edit">
<!-- BEGIN: error -->
    <div class="quote" style="width:780px;">
    <blockquote class="error"><span>{ERROR}</span></blockquote>
    </div>
    <div class="clear"></div>
<!-- END: error -->
<!-- BEGIN: content -->
    <form class="form-inline" action="{NV_BASE_ADMINURL}index.php" method="post">
    <input type="hidden" name ="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
    <input type="hidden" name ="{NV_OP_VARIABLE}" value="{OP}" />
    <input type="hidden" name ="catid" value="{DATA.catid}" />
    <input type="hidden" name ="parentid_old" value="{DATA.parentid}" />
    <input name="savecat" type="hidden" value="1" />
    <table class="table table-striped table-bordered table-hover">
		<caption>{caption}</caption>  
		<tbody>
			<tr>
				<td align="right"><strong>{LANG.cat_name}: </strong></td>
				<td><input class="form-control" style="width: 600px" name="title" type="text" value="{DATA.title}" maxlength="255" id="idtitle"/></td>
			</tr>
			<tr>
				<td valign="top" align="right"><strong>{LANG.alias}: </strong></td>
				<td>
					<input class="form-control" style="width: 550px" name="alias" type="text" value="{DATA.alias}" maxlength="255" id="idalias"/>
					<img src="{NV_BASE_SITEURL}images/refresh.png" width="16" style="cursor: pointer; vertical-align: middle;" onclick="get_alias('cat', {DATA.catid});" alt="" height="16" />
				</td>
			</tr>
			<tr>
				<td align="right"><strong>Title Site: </strong></td>
				<td><input class="form-control" style="width: 600px" name="titlesite" type="text" value="{DATA.titlesite}" maxlength="255"/></td>
			</tr>
			<tr>
				<td align="right"><strong>{LANG.cat_sub}: </strong></td>
				<td>
				<select class="form-control" name="parentid">
					<!-- BEGIN: cat_listsub -->
						<option value="{cat_listsub.value}" {cat_listsub.selected}>{cat_listsub.title}</option>
					<!-- END: cat_listsub -->
				</select>
				</td>
			</tr>
			<tr>
				<td align="right"><strong>{LANG.keywords}: </strong></td>
				<td><input class="form-control" style="width: 600px" name="keywords" type="text" value="{DATA.keywords}" maxlength="255" /></td>
			</tr>
			<tr>
				<td valign="top" align="right"><br /><strong>{LANG.description} </strong></td>
				<td><textarea style="width: 600px" name="description" cols="100" rows="5">{DATA.description}</textarea>
				</td>
			</tr>
			<tr>
				<td align="right"  width="180px"><strong>{LANG.img} : </strong></td>
				<td>
					<input class="form-control" style="width:380px" type="text" name="homeimg" id="homeimg" value="{DATA.image}"/>
					<input type="button" value="Browse server" name="selectimg"/>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right"><br /><strong>{LANG.is_email_admin}</strong></td>
				<td>
					<input type="checkbox" name="is_email"  value="1"  {email_checked} />
				</td>
			</tr>
			<tr>
				<td valign="top" align="right"><strong>{LANG.user_admin}</strong>(<span style="color:red">*</span>)</td>
				<td>
					<!-- BEGIN: add_district -->
	                   <strong> <input id = "check_all[]" type="checkbox" value="yes" onclick="nv_checkAll(this.form, 'idcheck[]', 'check_all[]',this.checked);" />Chọn/ Bỏ chọn</strong><br/>							
						<!-- BEGIN: quantri -->
						<span style="width: 25%;float: left; margin-top: 5px;">
							<input name="admins[{AD.userid}]" value="{AD.userid}" type="checkbox"{AD.checked} id ="idcheck[]"/> {AD.username}
						</span>							
						<!-- END: quantri -->
						 <!-- <input type="hidden" name="add_ward" value="1" /> -->							
	               <!-- END: add_district -->
				</td>
			</tr>
		<tr>
			<td valign="top" align="right"><br /><strong>{GLANG.who_view} </strong></td>
			<td>
				<div class="message_body">
					<select class="form-control" name="who_view" id="who_view" onchange="nv_sh('who_view','groups_list')" style="width: 250px;">
						<!-- BEGIN: who_views -->
							<option value="{who_views.value}" {who_views.selected}>{who_views.title}</option>
						<!-- END: who_views -->
					</select>
					<br />
					<div id="groups_list" style="{hidediv}">
						<strong>{GLANG.groups_view}:</strong>
						<table style="margin-bottom:8px; width:250px;">
							<col valign="top" width="150px" />
								<tr>
									<td>
										<!-- BEGIN: groups_views -->
										<p><input name="groups_view[]" type="checkbox" value="{groups_views.value}" {groups_views.checked} />{groups_views.title}</p>
										<!-- END: groups_views -->
									</td>
								</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
		</tbody>
    </table>
    <br /><center><input class="btn btn-primary" name="submit1" type="submit" value="{LANG.save}" /></center>
</form>
</div>
<!-- BEGIN: getalias -->
<script type="text/javascript">
$("#idtitle").change(function () {
    get_alias( "cat", 0 );
});
</script>
<!-- END: getalias -->
<script type="text/javascript">
	//<![CDATA[
	$("input[name=selectimg]").click(function() {
		var area = "homeimg";
		var path = "{UPLOADS_DIR_USER}";
		var currentpath = "{UPLOAD_CURRENT}";
		var type = "image";
		nv_open_browse(script_name + "?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", "850", "420", "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
		return false;
	});
	//]]>
</script>
<!-- END: content -->
<!-- END: main -->