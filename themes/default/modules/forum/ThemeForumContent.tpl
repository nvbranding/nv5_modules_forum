<!-- BEGIN: main -->
<div class="titleBar">
  <h2>Tạo chủ đề </h2>
  <div class="subcategory">{NODE.title}</div>
</div>
<div class="ThreadCreate xenForm">
	<form class="form-threadcreate" id="form-threadcreate" action="{ACTION}" name="ThreadCreate" method="post" enctype="multipart/form-data">
 
		<input type="hidden" name="node_id" value="{NODE.node_id}"> 
		<input type="hidden" name="token" value="{TOKEN}"> 
		<input type="hidden" name="attachment_hash" value="{ATTACHMENT_HASH}"> 
		<div class="text-center">
			<div class="post-title">
				<input type="text" name="title" value="{TITLE}" placeholder="Tên chủ đề..." id="post-title" class="form-control w300"/>
			</div>
			<div class="post-message">
				 {MESSAGE}
			</div>
			<div class="post-submit">
				<dl class="ctrlUnit">
					<dt>Tags:</dt>
					<dd>
						<input style="display: none;" class="textCtrl TagInput">
						<div style="height: 100%;" class="taggingInput textCtrl verticalShift">
							<!-- BEGIN: tag -->
							<span class="tag"><span>{TAG}</span><a title="" href="#">x</a></span>
							<!-- END: tag -->
							<div class="addTag"><input autocomplete="off" class="AcSingle" style="width: 100%;" id="GetTag" value="" data-value=""></div>
							<div class="tagsClear"></div>
						</div>
						<p class="explain">					
							Nhiều thẻ có thể được cách nhau bằng dấu phẩy 
						</p>
					</dd>
				</dl>	
				
				<div class="submit2">
					<button type="button" id="button-threadcreate" class="btn btn-primary" ><i class="fa fa-spinner fa-lg fa-spin" style="display:none"> </i> Tạo chủ đề </button>
					<!-- BEGIN: CanUploadAttachment1 -->
					<div id="fileupload"> </div>
					<!-- END: CanUploadAttachment1 -->
					<input type="button" value="Xem trước" class="btn btn-primary">				
					<!-- BEGIN: CanUploadAttachment2 -->
					<div class="AttachmentEditor" style="display: none;">
						<div class="NoAttachments"></div>	
 
					
						<ol id="AttachmentList" class="AttachmentList New">
							 							
						</ol>
						
					</div>
					<div id="fileupload-queueID"></div> 
					<!-- END: CanUploadAttachment2 -->
				</div>  
			</div>
			<div class="post-option">
				<fieldset>
					<dl class="ctrlUnit">
						<dt>Tùy chọn:</dt>
						<dd>
							<ul>
								<li><label for="ctrl_watch_thread"><input type="checkbox" name="watch_thread"name="watch_thread" value="1" id="post_watch_thread" class="Disabler" {WatchThreadChecked}> Theo dõi chủ đề này...</label>
								<ul id="ctrl_watch_thread_Disabler" {WatchStateClass}>
									<li><label for="ctrl_watch_thread_email"><input type="checkbox" name="watch_thread_email" value="1" id="post_watch_thread_email" {WatchStateChecked} {WatchStateClass}> và nhận email thông báo</label></li>
								</ul>
								<input type="hidden" name="watch_thread_state" value="1"></li>
							</ul>
						</dd>
					</dl>		
					<dl class="ctrlUnit ">
						<dt><label>Trạng thái chủ đề:</label></dt>
						<dd>
							<ul>
								<li>
									<label for="ctrl_discussion_open">
									<input type="checkbox" name="discussion_open" value="1" id="ctrl_discussion_open" {ThreadDiscussionChecked}> Mở</label>
									<p class="hint">Mọi người có thể trả lời cho chủ đề này</p>
								</li>
								<li>
									<label for="ctrl_sticky"><input type="checkbox" name="sticky" value="1" id="ctrl_sticky" {ThreadStickyChecked}> Ghim chủ đề</label>
									 
									<p class="hint">Hiển thị trên trang đầu tiên của danh sách chủ đề trong diễn đàn chính</p>
								</li>
							</ul>
						</dd>
					</dl>
				</fieldset>
			</div>		
		</div>
	</form>
</div>
<link type="text/css" href="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/{MODULE_FILE}/uploadifive/uploadifive.css" rel="stylesheet" />
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/{MODULE_FILE}/uploadifive/jquery.uploadifive.js"></script>			 
<script type="text/javascript">		
$(document).ready(function() {
<!-- BEGIN: CanUploadAttachment3 --> 
    var validExtensions = [{EXTENSIONS}];
    $('#fileupload').uploadifive({
        'uploadScript': nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=upload&nocache=' + new Date().getTime(),
        'buttonClass': 'btn btn-primary',
        'buttonText': 'Thêm tệp tin',
        'queueID': 'fileupload-queueID',
        'auto': true,
        'multi': true,
        'removeCompleted': true,
        'queueSizeLimit': {UPLOAD_LIMIT},
        'uploadLimit': {UPLOAD_LIMIT},
        'fileType': validExtensions,
        'fileSizeLimit': {MAX_SIZE},
        'formData': {
            'node_id': '{NODE.node_id}',
            'token': '{TOKEN}',
            'attachment_hash': '{ATTACHMENT_HASH}',
        },
        'onUploadComplete': function(file, res) {
            var obj = $.parseJSON(res);

            var item = obj.data;
			if( item['error'] )
			{
				alert( item['error'] );
				return false;
			}
			
            var tmp = '';
            tmp += '<li id="attachment' + item['attachment_id'] + '" class="AttachedFile secondaryContent AttachedImage">';
            tmp += '<div class="Thumbnail">';
            if (item['is_image'] == true) {
                tmp += '	<a href="' + item['image_url'] + '" target="_blank" data-attachmentid="' + item['attachment_id'] + '" class="_not_LbTrigger" ><img src="' + item['thumb_url'] + '" alt="' + item['basename'] + '" class="_not_LbImage" data-src="' + item['image_url'] + '"></a>';
            } else {
                tmp += '<span class="genericAttachment"></span>';
            }
            tmp += '</div>';
            tmp += '<div class="AttachmentText">';
            tmp += '	<div class="Filename"><a href="' + item['image_url'] + '" target="_blank">' + item['basename'] + '</a></div>';
            if (item['is_image'] == true) {
                tmp += '	<div class="label JsOnly">Chèn:</div>';
                tmp += '	<div class="controls JsOnly">';
                tmp += '		<input type="button" value="Delete" class="button smallButton AttachmentDeleter" data-attachmentid="' + item['attachment_id'] + '" data-token="' + item['token'] + '" style="display: block;">';
                tmp += '		<input type="button" data-attachmentid="' + item['attachment_id'] + '" name="thumb" value="Ảnh nhỏ" class="button smallButton AttachmentInserter">';
                tmp += '		<input type="button" data-attachmentid="' + item['attachment_id'] + '" name="image" value="Ảnh lớn" class="button smallButton AttachmentInserter">';
                tmp += '	</div>';
            } else {
                tmp += '	<div class="controls JsOnly">';
                tmp += '		<input type="button" value="Delete" class="button smallButton AttachmentDeleter" data-attachmentid="' + item['attachment_id'] + '" data-token="' + item['token'] + '" style="display: block;">';
                tmp += '	</div>';

            }

            tmp += '</div>';
            tmp += '</li>';
            $('#AttachmentList').append(tmp).show();
            $('.AttachmentEditor').show();

        }

    });
});
$('body').on('click', '.AttachmentDeleter', function(e) {

    var attachment_id = $(this).attr('data-attachmentid');
    var token = $(this).attr('data-atoken');
    var action = 'delete';
    $.ajax({
        type: 'post',
        url: nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=attachments&nocache=' + new Date().getTime(),
        data: {
            attachment_id: attachment_id,
            token: token,
            action: action
        },
        dataType: 'json',
        beforeSend: function() {

        },
        complete: function() {

        },
        success: function(json) {
            $('#attachment' + attachment_id).remove();

        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "rn" + xhr.statusText + "rn" + xhr.responseText);
        }
    });

});
$('body').on('click', '.AttachmentInserter', function() {
    var name = $(this).attr('name');
    var attachmentid = $(this).attr('data-attachmentid');

    if (name == 'image') {
        var thumb_url = $('#attachment' + attachmentid + ' .Thumbnail a img').attr('data-src');
        var html = '<img alt="attachFull' + attachmentid + '" src="' + thumb_url + '" class="attachFull bbCodeImage" style="max-width:100%"/>';
        CKEDITOR.instances['forum_message'].insertHtml(html);
    } else if (name == 'thumb') {
        var thumb_url = $('#attachment' + attachmentid + ' .Thumbnail a img').attr('src');
        var html = '<img alt="attachThumb' + attachmentid + '" src="' + thumb_url + '" class="attachThumb bbCodeImage" />';
        CKEDITOR.instances['forum_message'].insertHtml(html);
    }

}); 
<!-- END: CanUploadAttachment3 --> 
$('#post-title').on('keydown, keyup', function(){
	var title = $(this).val();		 
	if( title != '' ) $('.titleBar h2').html('Tạo chủ đề: <em>' + title + '</em>' );	
	else $('.titleBar h2').html( 'Tạo chủ đề' );	
});

$('#post_watch_thread').on('click', function(){
	if( $(this).prop('checked') )
	{
		$('#post_watch_thread_email').prop('disabled', false).removeClass('pdisabled');
		$('#ctrl_watch_thread_Disabler').removeClass('pdisabled');
	}
	else 
	{
		$('#post_watch_thread_email').prop('disabled', true).addClass('pdisabled');
		$('#ctrl_watch_thread_Disabler').addClass('pdisabled');
 
	}
});

$( 'body' ).on('click', '#button-threadcreate', function(e) {
	if( ! $(this).hasClass('disabled') )
	{  
		
		var message = CKEDITOR.instances.forum_message.getData();     
		var tags = [];
		$('span.tag').each(function( ){
			tags.push($(this).find('span').text());
		});
		var form = $('#form-threadcreate');
		var form_data  = {
			action: 'AddThread',
			node_id: $('input[name="node_id"]').val(),
			token: $('input[name="token"]').val(),
			attachment_hash: $('input[name="attachment_hash"]').val(),
			watch_thread: $('input[name="watch_thread"]').is(':checked') ? 1 : 0,
			watch_thread_email: $('input[name="watch_thread_email"]').is(':checked') ? 1 : 0,
			discussion_open: $('input[name="discussion_open"]').is(':checked') ? 1 : 0,
			discussion_open: $('input[name="sticky"]').is(':checked') ? 1 : 0,
			title: $('input[name="title"]').val(),
			message: message,
			tags: tags.join(),
			
		};
		if( strip_tags( message, '<img>' ).length >= 100 )
		{
			$.ajax({
				type: form.attr('method'),
				url: form.attr('action'),
				data: form_data,
				dataType: 'json',	
				beforeSend: function( ) {	
					$('#form-threadcreate input[type="button"]').prop('disabled', true);
					$('#form-threadcreate button[type="button"]').prop('disabled', true);
					$('#form-threadcreate button[type="file"]').prop('disabled', true);
					$('#uploadifive-fileupload').addClass('disabled');
				},	
				complete: function() {
					setTimeout(function() { 
						$('#form-threadcreate input[type="button"]').prop('disabled', false);
						$('#form-threadcreate button[type="button"]').prop('disabled', false);
						$('#form-threadcreate button[type="file"]').prop('disabled', false);
						$('#uploadifive-fileupload').removeClass('disabled');
					}, 2000);
					
				},
				success: function(json) {		
					if( json['redirect'] )
					{
						window.location.href =json['redirect']; 
					}	
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});  
		}else
		{
			alert('Tin nhắn phải từ 10 kí tự trở lên');
		}
	} 
	e.preventDefault();
});

 
function SaveDraft( ) 
{
    var message = CKEDITOR.instances.forum_message.getData();     
	var tags = [];
	$('span.tag').each(function( ){
		tags.push($(this).find('span').text());
	});
	var form = $('#form-threadcreate');
	var form_data  = {
		action: 'SaveDraft',
		node_id: $('input[name="node_id"]').val(),
		token: $('input[name="token"]').val(),
		attachment_hash: $('input[name="attachment_hash"]').val(),
		watch_thread: $('input[name="watch_thread"]').is(':checked') ? 1 : 0,
		watch_thread_email: $('input[name="watch_thread_email"]').is(':checked') ? 1 : 0,
		discussion_open: $('input[name="discussion_open"]').is(':checked') ? 1 : 0,
		discussion_open: $('input[name="sticky"]').is(':checked') ? 1 : 0,
		title: $('input[name="title"]').val(),
		message: message,
		tags: tags.join(),
		
	};
 
	if( strip_tags( message ).length > 100 )
	{
		$.ajax({
			type: form.attr('method'),
			url: form.attr('action'),
			data: form_data,
			dataType: 'json',		 
		}); 
	}
}
setInterval( SaveDraft, 60000);

$('#GetTag').autofill({
	'source': function(request, response) {
 
		if( $('#GetTag').val().length > 1 )
		{	 
			$.ajax({
				url: nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '='+ nv_func_name +'&action=GetTag&tag=' +  encodeURIComponent(request) + '&nocache=' + new Date().getTime(),
				dataType: 'json',
				success: function(json) {
					 
					response($.map(json, function( item ) {
					
						return {
							label: item['tag'],
							value: item['tag']
						}
					}));
				}
			});
		}
	},
	'select': function(item) {
 
		$('.addTag').before('<span class="tag"><span>'+ item['value'].replace(',', '') +' </span><a title="" href="#">x</a></span>');	 
		$('#GetTag').val('').focus();
	}
}); 

$('body').on('click','span.tag a', function(e) {	
	$(this).parent().remove();	
	e.preventDefault(); 	  
});

$('#GetTag').on('keypress, keydown, keyup', function(event) {
	
	if ( event.which != 188 ) 
	{
		$(this).attr('data-value', $(this).val() ); 
	}
	if ( event.which == 188 ) 
	{
		$('ul.dropdown-menu.template').empty().hide();
		var tag = $(this).attr('data-value');
		if( tag.length > 1 )
		{
			tag = tag.replace(',', '');
			$('.addTag').before('<span class="tag"><span>'+ tag +' </span><a title="" href="#">x</a><input type="hidden" name="tags[]" value="'+ tag +'"></span>');	
			
		}
		$('#GetTag').val('').focus();
	} 
});
</script>
<!-- END: main -->