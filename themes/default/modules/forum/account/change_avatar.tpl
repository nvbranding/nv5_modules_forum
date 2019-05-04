<!-- BEGIN: main -->
<form id="my_avatars" action="" method="post" enctype="multipart/form-data" class="form-inline AvatarEditor AutoInlineUploader formOverlay" onsubmit="return false">
    <h2 class="heading h1">Sửa Avatar <span id="images_loading"></span></h2>
	<div class="currentAvatar">
        <label id="currentAvatar" for="ctrl_avatar" class="avatar Av{userid} NoOverlay">
            <img src="{photo}" alt="Current Avatar" />
        </label>
    </div>
    <ul class="modifyControls">
        <li class="avatarOption">
            <div class="avatarCropper avatarLabel" style="width: 96px; height: 96px">
                <label for="ctrl_useGravatar_0" class="pane Av{userid} AvatarCropControl avatar NoOverlay" style="width: 96px; height: 96px">
                    <img src="{photo}" style="height: 96px" alt="Đổi Avatar" />
                </label>
            </div>
            <input type="radio" name="use_gravatar" value="0" class="Disabler radioOption" id="ctrl_useGravatar_0" checked="checked" />
            <div class="labelText" id="ctrl_useGravatar_0_Disabler">
                <label for="ctrl_useGravatar_0" id="ExistingCustom">Sử dụng avatar riêng<span class="explain faint">Kéo, thả ảnh để cắt kích thước sau đó ấn <span class="saveHint">Đồng ý</span> để xác nhận, hoặc tải lên avatar mới bên dưới đây.</span>
                </label>
                <label for="ctrl_avatar" class="ClickProxy" rel="#ctrl_useGravatar_0" data-allowDefault="1">Tải lên avatar riêng mới:</label>
                <input onchange="get_avatar_image();" type="file" name="avatar" id="avatar" class="textCtrl avatarUpload" id="ctrl_avatar" title="Hỗ trợ định dạng: JPEG, PNG, GIF" />
                <div class="explain faint">Chúng tôi khuyến nghị rằng bạn nên sử dụng ảnh với độ phân giải ít nhất là 200x200 pixels.</div>
            </div>
        </li>
        <li class="submitUnit saveDeleteControls">
            <label for="DeleteAvatar" class="deleteCtrl">
                <input type="checkbox" name="delete" value="1" id="DeleteAvatar" />Xóa avatar hiện tại?</label>
            <span class="buttons">				
          <input onclick="submit_ok()" type="submit" value="Đồng ý" class="button2" accesskey="s" id="ctrl_save" />				
          <input onclick="jQuery.colorbox.close(); return false;" type="submit" value="Đóng" class="button OverlayCloser overlayOnly" accesskey="d" id="ctrl_close"/>			
          </span>	
        </li>
    </ul>
    <!-- <input type="hidden" name="avatar_date" value="1396497424" /> -->
    <!-- <input type="hidden" name="avatar_crop_x" value="33" /> -->
    <!-- <input type="hidden" name="avatar_crop_y" value="0" /> -->
    <input type="hidden" name="userid" id="avatar_userid" value="{userid}" />
    <input type="hidden" name="checkss" id="avatar_checkss" value="{checkss}" />
    <input type="hidden" name="action" value="upload_avatar" />
</form>
<!-- END: main -->