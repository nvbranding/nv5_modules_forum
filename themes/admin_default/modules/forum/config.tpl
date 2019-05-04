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
<div id="users">
    <form class="form-inline" action="{FORM_ACTION}" method="post">
     <table class="table table-striped table-bordered table-hover">
			<thead>
			<th colspan="2">
				<strong>CẤU HÌNH TRANG CHỦ DIỄN ĐÀN</strong>
			</th>
			</thead>
			<tbody>
			<tr>
				<td>{LANG.setting_indexfile}</td>
				<td>
					<select class="form-control" name="indexfile">
						<!-- BEGIN: indexfile -->
						<option value="{INDEXFILE.key}"{INDEXFILE.selected}>{INDEXFILE.title}</option>
						<!-- END: indexfile -->
					</select>
				</td>
			</tr>
                <tr>
                    <td width="380">Số chủ đề trên cùng một trang</td>
                    <td>
                        <input class="form-control" name="paper_page" type="text" value="{DATA.paper_page}" style="width:150px"/>
                    </td>
                </tr>
		</tbody>
		
		 </table>
		 
		
		<table class="table table-striped table-bordered table-hover">
			<thead>
			<th colspan="2">
				<strong>CẤU HÌNH XEM CHỦ ĐỀ</strong>
			</th>
			</thead>
			<tbody>
                <tr>
                    <td width="380">Kiểu hiển thị chủ đề liên quan</td>
                    <td>
                         <select class="form-control" name="type_thread">
							<!-- BEGIN: type_thread -->
							<option value="{TYPE.key}" {TYPE.selected}> {TYPE.title} </option>
							<!-- END: type_thread -->
						 </select>
                    </td>
                </tr>
                <tr>
                    <td width="380">Số bài trả lời trên một trang</td>
                    <td>
                        <input class="form-control" name="paper_post" type="text" value="{DATA.paper_post}" style="width:150px"/>
                    </td>
                </tr>
                <tr>
                    <td width="380">Số chủ đề liên quan trên một trang</td>
                    <td>
                         <input class="form-control" name="paper_thread" value="{DATA.paper_thread}" type="text" style="width:150px" />
                    </td>
                </tr>
            </tbody>
		 	
		 </table>
		 <table class="table table-striped table-bordered table-hover">
			<thead>
			<th colspan="2">
				<strong>CẤU HÌNH ĐĂNG BÀI THÀNH VIÊN</strong>
			</th>
			</thead>
			
			<tbody>
                <tr>
                    <td width="380">Giới hạn bài đăng cần chờ duyệt</td>
                    <td>
                         <input class="form-control" name="verify_post" value="{DATA.verify_post}" type="text" style="width:150px" />
                    </td>
                </tr>
            </tbody>
		 </table>
		 <table class="table table-striped table-bordered table-hover">
			<thead>
			<th colspan="2">
				<strong>CẤU HÌNH TRANG CÁ NHÂN THÀNH VIÊN</strong>
			</th>
			</thead>
			
			<tbody>
                <tr>
                    <td width="380">Số bình luận trên một trang</td>
                    <td>
                         <input class="form-control" name="profile_perpage" value="{DATA.profile_perpage}" type="text" style="width:150px" />
                    </td>
                </tr>
            </tbody>
		 </table>
		

		
		 <table class="table table-striped table-bordered table-hover">
            <tbody>
                <tr>
                    <td width="380">Đóng dấu ảnh</td>
                    <td>
                        <input name="addlogo" value="1" type="checkbox"{DATA.addlogo} />
                    </td>
                </tr>
                <tr>
                    <td width="380">Hiển thị biểu tượng vui</td>
                    <td>
                        <input name="show_smile" value="1" type="checkbox" {DATA.show_smile} />
                    </td>
                </tr>
                <tr>
                    <td width="380">Đường dẫn logo đóng dấu</td>
                    <td>
                        <input class="form-control" name="upload_logo" id="upload_logo" value="{upload_logo}" type="text" style="width:200px"/>
						<input style="width:100px;" value="Chọn ảnh" name="selectimg" type="button" />
					</td>
                </tr>
                <tr>
                    <td width="380">Số liên kết chủ đề</td>
                    <td>
                         <input class="form-control" name="other_link" value="{DATA.other_link}" type="text" />
                    </td>
                </tr>
                <tr>
                    <td width="380">Thời hạn được phép sửa bài viết</td>
                    <td>
                        <input class="form-control" name="time_edit_user" type="text" value="{DATA.time_edit_user}" style="width:60px"/> ngày
                    </td>
                </tr>
                <tr>
                    <td width="380">Kích thước ảnh thumb</td>
                    <td>
                        <input class="form-control" name="thumb_width" type="text" value="{DATA.thumb_width}"  style="width:60px"/> X
                        <input class="form-control" name="thumb_height" type="text" value="{DATA.thumb_height}"  style="width:60px"/>
                    </td>
                </tr>
                <tr>
                    <td width="380">Kích thước ảnh lớn</td>
                    <td>
                        <input class="form-control" name="img_template_width" type="text" value="{DATA.img_template_width}"  style="width:60px"/>
                    </td>
                </tr>
			<tr>
				<td><strong>{LANG.nv_max_size1}:</strong></td>
				<td>
					<select class="form-control" name="maxupload">
						<!-- BEGIN: size1 -->
						<option value="{SIZE1.key}"{SIZE1.selected}>{SIZE1.title}</option>
						<!-- END: size1 -->
					</select>
					({LANG.sys_max_size}: {SYS_MAX_SIZE})
				</td>
			</tr>
		</tbody>
            
      </table>
      <div style="textarea-align:center;padding-top:15px">
            <input class="btn btn-primary" type="submit" name="submit" value="{LANG.config_confirm}" />
        </div>
    </form>
</div>
<script type="text/javascript">
//<![CDATA[
$("input[name=selectimg]").click(function(){
	var area = "upload_logo";
	var type= "image";
	var path= "{PATH}";
	var currentpath= "{CURRENTPATH}";
	nv_open_browse("{NV_BASE_ADMINURL}index.php?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", "850", "420","resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
	return false;
});
//]]>
</script>
<!-- END: main -->