<!-- BEGIN: main -->

<div class="titleBar">
  <h1>Tạo chủ đề mới</h1>
  <p id="pageDescription" class="muted baseHtml">Diễn đàn tìm hiểu kiến thức và trao đổi thông tin về thị trường Chứng khoán, Kinh tế Việt Nam và Thế giới.</p>
</div>
<form class="form-inline vbform block" id="postform"  action="{ACTION}" name="postform" method="post" enctype="multipart/form-data">
  <div class="blockbody formcontrols"> 
    <!-- BEGIN: error -->
    <div style="font-weight: bold;margin-bottom: 10px; color: #f00;"> {error} </div>
    <!-- END: error -->
    
    <div class="section1">
      <div class="blockrow">
        <label for="subject" class="full_title">{LANG.title}: <span style="color:red">(*)</span></label>
        <!-- BEGIN: edit_title -->
        <input type="text" class="form-control textbox" value="{DATA.title}" id="title" name="title" title="Tiêu đề phải lớn hơn 5 ký tự" maxlength="250" minlength="5"  required/>
        <!-- END: edit_title --> 
        
        <!-- BEGIN: disable_title -->
        <h3>{DATA.title}</h3>
        <!-- END: disable_title --> 
      </div>
    </div>
    <div class="section1">
      <div class="blockrow">
        <label for="subject" class="full_title">{LANG.cat}: <span style="color:red">(*)</span></label>
        <select name="catid" id="catid" class="form-control select-form" title="Bạn chưa chọn chuyên mục" required>
          <option value="">{LANG.cat0} </option>
          <!-- BEGIN: cat -->
          <option value="{CAT.catid}" {CAT.selected} {CAT.disabled}>{CAT.title} </option>
          <!-- END: cat -->
        </select>
      </div>
    </div>
    <div class="section1">
      <div class="blockrow" style="position:relative">
        <label for="subject" class="full_title">{LANG.ndquestion}: <span style="color:red">(*)</span></label>
        <style>
	.sceditor-button-vimeo div { background: url('/modules/dien-dan/bbcode/minified/themes/icon/vimeo.png'); }
	</style>
        <script type="text/javascript">
		$(function() {
		
			$('#file_upload').uploadifive({
				'auto'             : true,
				'buttonText': "{LANG.upload}",
				'width': '120',
				'formData': {
					'lang' : '{lang}',
					'sess' : '{sess}',
					'token': '{token}',
					'mod': '{mod}',
					'read_file': '{read_file}',
					'userid': '{userid}',
					'post_id': '{DATA.post_id}'
				},
				'queueID': 'queue',
				'uploadScript': '{uploadifive}',
				'onUploadComplete' : function(file, data) { 
					var obj = $.parseJSON( data );
					var message = obj.data.message;
					var items = obj.data.item;	
					if(message == 'success')
					{
						var a="";
						a+="<li id=\"attachment"+items['attachment_id']+"\" rel=\""+items['checkss']+"\" class=\"AttachedFile  secondaryContent AttachedImage\" style=\"opacity: 1;\">";
						a+="	<div class=\"Thumbnail\"> <a id=\"data-href"+items['attachment_id']+"\" href=\""+items['imgfile']+"\" data=\""+items['new_name']+"\" target=\"_blank\" class=\"_not_LbTrigger\">";
						a+="	<img id=\"data-src"+items['attachment_id']+"\" src=\""+items['thumb_name']+"\" alt=\""+items['filename']+"\" class=\"_not_LbImage\"></a> </div>";
						a+="	<div class=\"AttachmentText\" style=\"\">";
						a+="	  <div class=\"Filename\"><a href=\""+items['imgfile']+"\" target=\"_blank\">"+items['filename']+"</a></div>";
						a+="	  <div class=\"labels\">Thêm vào:</div>";
						a+="	  <div class=\"controls\">";
						a+="		<input type=\"hidden\" value=\""+items['attachment_id']+"\" name=\"attachment_id[]\">";
						a+="		<input type=\"button\" onclick=\"insertimg( "+items['attachment_id']+", "+items['thumbnail_height']+", 'small' )\" value=\"Ảnh nhỏ\" class=\"button smallButton AttachmentInserter\">";
						a+="		<input type=\"button\" onclick=\"insertimg( "+items['attachment_id']+", "+items['img_template_width']+", 'full' )\"  value=\"Ảnh gốc\" class=\"button smallButton AttachmentInserter\">";
						a+="		<input type=\"button\" onclick=\"removeimg( "+items['attachment_id']+", "+items['data_id']+" )\"  value=\"Xóa\" class=\"button smallButton AttachmentDeleter\" style=\"display: block;\">";
						a+="	  </div>";
						a+="	</div>";
						a+="</li>";
						$('#AttachmentList').append(a);
						$('#queue').empty().fadeIn('slow');
					}
					if(message == 'unsuccess')
					{
						var a="";
						$.each(items, function (i, item) {
							a+=''+item+'<br />';
						});

						$('#qr_posting_msg').html('<div class="success" style="display: none;"><strong>' + a + ' </strong><span class="close" onclick="close();"><img src="' + nv_siteroot + 'themes/{TEMPLATE}/images/close.png" alt="" class="close" /></span></div>').show();
						$('.success').fadeIn('slow');
						$(".close").click(function () {
							$('#qr_posting_msg').hide();
						});	
						setTimeout(function() {$('#qr_posting_msg').hide();}, 3000);

					}	
				}
			});
		});
		</script>
        <div class="textarea"><textarea class="forum_bbcode" id="message" name="message" style="height:300px;width:100%;" title="Nội dung bài viết phải lớn hơn 10 ký tự" minlength="10" required>{DATA.message}</textarea>
          <div id="show_error"></div>
          <div class="blockrow"> <span for="subject" class="full_title">{LANG.watch_thread}: </span>
            <input onchange="if( $(this).is(':checked') ){$('#sendmail').removeAttr('disabled') }else{ $('#sendmail').attr('disabled', 'disabled') }" type="checkbox" id="watch_thread" name="watch_thread" {DATA.watch_thread} value="1" >
          </div>
          <div class="blockrow" > <span for="subject" class="full_title">{LANG.sendmail}: </span>
            <input type="checkbox" name="sendmail" id="sendmail" {DATA.sendmail} value="1" disabled />
            <i style="font-size: 12px;">{LANG.note_email}</i> </div>
          <div class="blockrow" > <span for="subject" class="full_title">{LANG.sticky}: </span>
            <input type="checkbox" name="sticky" value="1" id="ctrl_sticky" {DATA.sticky}/>
            <i style="font-size: 12px;">{LANG.note_sticky}</i> </div>
          <div class="blockrow">
            <label style="width: 140px;" for="subject" class="full_title">{LANG.captcha}: <span style="color:red">(*)</span></label>
            <input style="width: 90px;" type="text" maxlength="6" value="" id="fcode_iavim" name="fcode" class="form-control input capcha" title="Hãy nhập 6 ký tự mã bảo mật" minlength="6" required />
            <img class="capcha" src="{NV_BASE_SITEURL}index.php?scaptcha=captcha" title="{LANG.captcha}" alt="{LANG.captcha}" id="vimg" /> <img alt="{CAPTCHA_REFRESH}" src="{NV_BASE_SITEURL}images/refresh.png" width="16" height="16" class="refresh" onclick="nv_change_captcha('vimg','fcode_iavim');"  alt="thay doi"/>
            <input type="hidden" id="post_id" name="post_id" value="{DATA.post_id}" />
            <input type="hidden" id="thread_id" name="thread_id" value="{DATA.thread_id}" />
            <input type="hidden" id="checkss" name="checkss" value="{DATA.checkss}" />
            <input type="hidden" id="action" name="action" value="{DATA.action}" />
          </div>
          <div class="section1">
            <div class="blockrow">
				<div id="show_success">
              <ol id="AttachmentList" class="AttachmentList New">
                <!-- BEGIN: attachments -->
                <li id="attachment{attach.attachment_id}" rel="{attach.checkss}" class="AttachedFile  secondaryContent AttachedImage" style="opacity: 1;">
                  <div class="Thumbnail"> <a id="data-href{attach.attachment_id}" href="{attach.file_hash}" data="{attach.file_hash_name}" target="_blank" class="_not_LbTrigger"> <img id="data-src{attach.attachment_id}" src="{attach.file_thumb}" alt="{attach.filename}" class="_not_LbImage" /> </a> </div>
                  <div class="AttachmentText" style="">
                    <div class="Filename"><a href="{attach.file_hash}" target="_blank">{attach.filename}</a></div>
                    <div class="labels">Thêm vào:</div>
                    <div class="controls">
                      <input type="hidden" value="{attach.attachment_id}" name="attachment_id[]">
                      <input type="button" onclick="insertimg({attach.attachment_id}, '{attach.thumbnail_width}', 'small' )" value="Ảnh nhỏ" class="button smallButton AttachmentInserter">
                      <input type="button" onclick="insertimg({attach.attachment_id}, '{attach.width}', 'full' )" value="Ảnh gốc" class="button smallButton AttachmentInserter">
                      <input type="button" onclick="removeimg({attach.attachment_id},{attach.data_id})" value="Xóa" class="button smallButton AttachmentDeleter" style="display: block;">
                      </div>
                  </div>
                </li>
                <!-- END: attachments -->
              </ol>
            </div>
			 <div id="queue"></div>
			 <div class="clear"></div>
              <div class="text-center" style="padding-bottom:20px"> <span id="qr_posting_msg" class="hidden"> <img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/progress.gif" alt="Đang gửi trả lời nhanh - Xin đợi" />&nbsp;<strong>Đang gửi trả - Xin đợi</strong> </span>
                <input type="submit" name="save" id="post_submit" value="{DATA.post_submit}" class="button primary"/>
				<input id="file_upload" name="file_upload" type="file" multiple />
                <!-- <input type="submit" name="preview" id="post_submit" value="Xem trước" class="button primary" /> -->
				</div>
            </div>
           
            
            <!--<a style="position: relative; top: 8px;" href="javascript:$('#file_upload').uploadifive('upload')">{LANG.upload}</a>-->
            <div class="clear"></div>
          </div>
        </div>
        <div class="clear"></div>
      </div>
    </div>
  </div>
</form>
<div id="contents_test"> </div>
<script type="text/javascript">
$(document).ready(function() {
	$("#postform").validate({
		showErrors: function(errorMap, errorList) {
 
          $.each(this.validElements(), function (index, element) {
              var $element = $(element);
 
              $element.data("title", "") 
                  .removeClass("error")
                  .tooltip("destroy");
          });
 
          $.each(errorList, function (index, error) {
              var $element = $(error.element);
 
              $element.tooltip("destroy") 
                  .data("title", error.message)
                  .addClass("error")
                  .tooltip({
					placement:'bottom'
				  }); 
          });
      },
	submitHandler: function(form) {
			$(form).ajaxSubmit({
				type:"POST",
				dataType: "json",
				beforeSubmit:  showloading,
				success: processJson
			});
		}
	});  
})

function processJson (res) {
	var post_submit = document.getElementById('post_submit');	
	
	var message = res.data.message;
	var items = res.data.item;

	if(message == 'success')
	{
		$('#qr_posting_msg').html('<div class="success" style="display: none;"><strong>' + items['thread'] + ' </strong><span class="close" onclick="close();"><img src="' + nv_siteroot + 'themes/{TEMPLATE}/images/close.png" alt="" class="close" /></span></div>').show();
		$('.success').fadeIn('slow');
		$(".close").click(function () {
			post_submit.disabled = false;
			$('#qr_posting_msg').hide();
			window.location.href=''+items['link']+'';
		});	
		setTimeout(function() {
			$('#qr_posting_msg').hide();
			window.location.href=''+items['link']+'';
		}, 1000);
	}
	else if(message == 'unsuccess')
	{
		var a="";
		$.each(items, function (i, item) {
			a+=''+item+'<br />';
		});
		$('#qr_posting_msg').html('<div class="success" style="display: none;"><strong>' + a + ' </strong><span class="close" onclick="close();"><img src="' + nv_siteroot + 'themes/{TEMPLATE}/images/close.png" alt="" class="close" /></span></div>').show();
		$('.success').fadeIn('slow');
		$(".close").click(function () {
			post_submit.disabled = false;
			$('#qr_posting_msg').hide();
		});	
		setTimeout(function() {$('#qr_posting_msg').hide();}, 1000);
	}		
}

function showloading() {
    var loader = $('#qr_posting_msg');
    $(document).ajaxStart(function () {
        post_submit.disabled = true;
        loader.show();
    }).ajaxError(function (a, b, e) {
        throw e;
    });
}

</script> 
<!-- END: main -->