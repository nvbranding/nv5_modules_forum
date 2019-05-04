<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div style="width: 780px;" class="quote">
    <blockquote class="error">
        <p>
            <span>{ERROR}</span>
        </p>
    </blockquote>
</div>
<div class="clear"></div>
<!-- END: error -->
<form class="form-inline" action="{FORM_ACTION}" method="post">
    <table class="table table-striped table-bordered table-hover">
        <tbody>
            <tr>
                <td>
                    {LANG.cat_name}(<span style="color:red">*</span>)
                </td>
                <td>
                    <input class="txt" value="{DATA.title}" name="title" id="title" style="width:300px" maxlength="100" />
                </td>
            </tr>
            <tr>
                <td>
                    {LANG.user_admin}(<span style="color:red">*</span>)
                </td>
                <td>
                
                 <!-- BEGIN: add_district -->
	                   <strong> <input id = "check_all[]" type="checkbox" value="yes" onclick="nv_checkAll(this.form, 'idcheck[]', 'check_all[]',this.checked);" />Chon/ Bỏ chọn</strong><br/>							
						<!-- BEGIN: quantri -->
						<span style="width: 25%;float: left; margin-top: 5px;">
						<input name="admin[{AD.userid}]" value="{AD.userid}" type="checkbox"{AD.checked} id ="idcheck[]"/> {AD.username}
						
						</span>							
						<!-- END: quantri -->
						 <input type="hidden" name="add_ward" value="1" />							
	              <!-- END: add_district -->   
                </td>
            </tr>
        </tbody  class="second">
        <tbody>
            <tr>
                <td>
                    {LANG.hometext}
                </td>
                <td><textarea rows="2" cols="50" name="hometext" id="hometext">{DATA.hometext}</textarea>                   
                </td>
            </tr>
            <tr>
                <td>
                    {LANG.is_email_admin}
                </td>
                <td>
                 
                <input type="checkbox" name="is_email"  value="1"  {DATA.is_email} />
                                                 
                </td>
            </tr>
            <tr>
                <td>
                    {LANG.img}
                </td>
                <td>
                    <input class="form-control" title="{LANG.img}" type="text" name="img" id="img" value="{DATA.img}" style="width:280px" maxlength="255" />
                    <input type="button" value="Browse server" class="selectimg" />
                </td>
            </tr>
            <tr>
                <td>
                    {LANG.cat_parent}
                </td>
                <td>
                    <select class="form-control" name="parentid">
                        <!-- BEGIN: parentid -->
                        <option value="{LISTCATS.id}"{LISTCATS.selected}>{LISTCATS.name}</option>
                        <!-- END: parentid -->
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input class="btn btn-primary" type="submit" name="submit" value="{LANG.save}" />
                </td>
            </tr>
        </tbody>
    </table>
    <script type="text/javascript">
//<![CDATA[
$("input.selectimg").click(function() {
  var a = $(this).prev().attr("id");
  nv_open_browse(script_name + "?" + nv_name_variable + "=upload&popup=1&area=" + a + "&path={UPLOAD_CURRENT}&type=image&currentpath={UPLOAD_CURRENT}", "NVImg", "850", "420", "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
  return false
});
//]]>
</script>
</form>
<!-- END: main -->