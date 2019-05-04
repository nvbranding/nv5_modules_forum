<!-- BEGIN: main -->


<link href="{NV_BASE_SITEURL}{NV_EDITORSDIR}/ckeditor/plugins/codesnippet/lib/highlight/styles/github.css" rel="stylesheet">

<div id="forum-thread"> 
	<div class="panel panel-default padd10">
	<div class="titleBar">			
		<h1>{THREAD.title}</h1>						
		<p id="pageDescription" class="muted ">
			Thảo luận trong '<a href="{NODE.link}">{NODE.title}</a>' đăng bởi <a href="#" class="username" dir="auto">{THREAD.username}</a>, <a href="#"><abbr class="DateTime" data-time="{THREAD.data_time}" >{THREAD.post_date}</abbr></a>.
		</p>
	</div>
	<!-- BEGIN: tag -->
	<div class="tagBlock TagContainer">
	Tags:
		<ul class="tagList">	
			<!-- BEGIN: looptag -->
			<li><a href="#{TAG.tag_url}" class="tag"><span class="arrow"></span>{TAG.tag}</a></li>	
			<!-- END: looptag -->
		</ul>
	</div>
	<!-- END: tag -->
	<div class="pageNavLinkGroup">
		<!-- BEGIN: generate_page_top -->
			<div class="generate_page">
			{GENERATE_PAGE}
			</div>
		<!-- END: generate_page_top -->
		<div class="linkGroup SelectionCountContainer">
			<div class="Popup"><!-- Closed -->
				<a rel="#Menutool" class="PopupControl PopupClosed">Công cụ<span class="arrowWidget"></span></a>				
				
			</div>
			<a href="#/watch-confirm" class="OverlayTrigger">Theo dõi chủ đề</a>
			<a  href="#" class="SelectionCount cloned">Chọn bài viết: <em class="InlineModCheckedTotal">0</em></a>
			
			<div class="Menu" id="Menutool">
					<div class="primaryContent menuHeader"><h3>Công cụ chủ đề</h3></div>
					<ul class="secondaryContent blockLinksList tool">
						<!-- BEGIN: canEditThread -->
						<li><a data-href="{URL_ACTION}" data-thread_id="{THREAD.thread_id}" data-token="{TOKEN}" data-action="EditThread" href="#" class="threadTool OverlayTrigger">Sửa chủ đề</a></li>
						<!-- END: canEditThread -->
						<!-- BEGIN: canEditTitle -->
						<li><a data-href="{URL_ACTION}" data-thread_id="{THREAD.thread_id}" data-token="{TOKEN}" data-action="EditTitle" href="#" class="threadTool OverlayTrigger">Sửa chủ đề</a></li>
						<!-- END: canEditTitle -->
						<!-- <li><a href="#/poll/add">Thêm bầu chọn</a></li> -->
						<!-- BEGIN: canDeleteThread -->
						<li><a data-href="{URL_ACTION}" data-thread_id="{THREAD.thread_id}" data-token="{TOKEN}" data-action="DeleteThread" href="#" class="threadTool OverlayTrigger">Xóa chủ đề</a></li>
						<!-- END: canDeleteThread -->
						<!-- <li><a href="#/move" class="OverlayTrigger">Di chuyển chủ đề</a></li> -->
						<!-- <li><a href="#/reply-bans" class="OverlayTrigger">Quản lý bài bị chặn</a></li> -->
						<!-- <li><a href="#/moderator-actions" class="OverlayTrigger">Moderator Actions</a></li>		 -->		
					</ul>
					
					<form action="#/quick-update" method="post" class="AutoValidator">
						<ul class="secondaryContent blockLinksList checkboxColumns">
							<!-- BEGIN: canLockUnlockThread -->
							<li><label><input type="checkbox" name="discussion_open" value="1" class="SubmitOnChange" checked="checked">Mở chủ đề</label>
								</li>
							<!-- END: canLockUnlockThread -->	
							<!-- BEGIN: canStickUnstickThread -->									
							<li><label><input type="checkbox" name="sticky" value="1" class="SubmitOnChange">
								Chú ý</label>
								 </li>
							<!-- END: canStickUnstickThread -->
						</ul>
						 
					</form>
					
					<!-- <form action="#inline-mod/thread/switch" method="post" class="InlineModForm sectionFooter" id="threadViewThreadCheck" data-cookiename="threads">
						<label><input type="checkbox" name="threads[]" value="30" class="InlineModCheck" title=""> Lựa chọn chỉnh sửa chủ đề</label>
						<input type="hidden" name="token" value="d687fd801abcfe6d2a0993b3e">
					</form> -->

				</div>
				
			<script>
			$( 'body' ).on('click', '.threadTool', function(e) {	 	 
				e.preventDefault();
				var url = ( $(this).attr('data-href') ) ? $(this).attr('data-href') : $(this).attr('href');
				$.ajax({
					type: 'post',
					url:  url,
					data: { action: $(this).attr('data-action'), thread_id: $(this).attr('data-thread_id'), token: $(this).attr('data-token') },
					dataType: 'json',	
					cache: false,
					beforeSend: function() {
						 
					},	
					complete: function() {
						
					},	
					success: function(json) {		
						
						if( json['error'] )
						{
							alert( json['error'] );
							
						}			
						else if( json['template'] )
						{
							$('#PopupFormThread').remove();
							$('body').prepend( json['template'] );
							$('#PopupFormThread').modal();
						}
								 
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "rn" + xhr.statusText + "rn" + xhr.responseText);
					}
				});  
				e.preventDefault();
			});
			
			</script>
		</div>
		<div class="clearfix"></div>
	</div>
	
	<ul class="messageList">
		<!-- BEGIN: post -->
		<li id="post-{POST.post_id}" class="message clearfix" data-author="{POST.username}">
			<div class="messageUserInfo" itemscope="itemscope" itemtype="http://data-vocabulary.org/Person">
				<div class="messageUserBlock ">
					<div class="avatarHolder">
						<span class="helper"></span>
						<a href="#{POST.username}" class="avatar Av2m" data-avatarhtml="true"><img src="{POST.photo}" width="96" height="96" alt="{POST.username}"></a>
						<!-- BEGIN: isOnline -->
						<span data-toggle="tooltip" data-placement="top" class="onlineMarker" title="Đang trực tuyến" ></span>
						<!-- END: isOnline -->
					</div>
					<h3 class="userText">
						<a href="#{POST.username}" class="username" dir="auto" itemprop="name">{POST.username}</a>
						<!-- BEGIN: fullName -->
						<em class="userTitle" itemprop="title">{fullName}</em>
						<!-- END: fullName -->
					</h3>
					<!-- BEGIN: isStaff -->
					<em class="userBanner bannerStaff wrapped" itemprop="title"><span class="before"></span><strong>Điều hành viên</strong><span class="after"></span></em>
					<!-- END: isStaff -->
					<!-- BEGIN: getgroup -->
					<em class="{GROUP.banner_css_class} wrapped" itemprop="title" style="{GROUP.username_css}"><span class="before"></span><strong>{GROUP.banner_text}</strong><span class="after"></span></em>
					<!-- END: getgroup -->
					<span class="arrow"><span></span></span>
				</div>
			</div>
			<div class="messageInfo primaryContent">
				<!-- BEGIN: isNew -->
				<strong class="newIndicator"><span></span>Mới</strong>
				<!-- END: isNew -->
				<div class="messageContent">
					<article>
						<blockquote class="messageText SelectQuoteContainer ugc baseHtml">
							<p>{POST.message}</p>
							<div class="messageTextEndMarker">&nbsp;</div>
						</blockquote>
						<!-- BEGIN: attachment -->
						<div class="attachedFiles">
								<h4 class="attachedFilesHeader">Tệp đính kèm:</h4>
								<ul class="attachmentList SquareThumbs">
								<!-- BEGIN: loop -->
								<li class="attachment" title="{ATTACHMENT.filename}">
									<div class="boxModelFixer primaryContent">
										<div class="thumbnail">	
											<!-- BEGIN: viewimage -->
											<a href="{ATTACHMENT.contentLink}" target="_blank" class="LbTrigger" data-href=""><img src="{ATTACHMENT.thumbnailUrl}" alt="{ATTACHMENT.filename}" class="LbImage" /></a>
											<!-- END: viewimage -->
											<!-- BEGIN: clickimage -->
											<a href="{ATTACHMENT.contentLink}" target="_blank"><img src="{ATTACHMENT.thumbnailUrl}" alt="{ATTACHMENT.filename}" /></a>
											<!-- END: clickimage -->
											<!-- BEGIN: clickfile -->
											<a href="{ATTACHMENT.contentLink}" target="_blank" class="genericAttachment"></a>
											<!-- END: clickfile -->
											 
										
										</div>
										<div class="attachmentInfo pairsJustified">
											<h6 class="filename"><a href="{ATTACHMENT.contentLink}" target="_blank">{ATTACHMENT.filename}</a></h6>
											<dl><dt>Dung lượng:</dt> <dd>{ATTACHMENT.file_size}</dd></dl>
											<dl><dt>Lượt xem:</dt> <dd>{ATTACHMENT.view_count}</dd></dl>
										</div>
									</div>
								</li>
								<!-- END: loop -->
							</ul>
						</div>
						<!-- END: attachment -->
					</article>
				</div>
					<!-- BEGIN: googleAds -->
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<ins class="adsbygoogle"
				 style="display:block"
				 data-ad-client="ca-pub-8020071386180409"
				 data-ad-slot="9452387775"
				 data-ad-format="auto"></ins>
			<script type="text/javascript">
			(adsbygoogle = window.adsbygoogle || []).push({});
			</script>
			<!-- END: googleAds -->
	
				<!-- BEGIN: signature -->
				<div class="baseHtml signature messageText ugc"><aside>{POST.signature}</aside></div>
				<!-- END: signature -->
				<!-- BEGIN: editDate -->
				<div class="editDate">
					<!-- BEGIN:  byModerator-->
					Lần sửa cuối bởi một điều hành viên: <abbr class="DateTime" data-time="{POST.last_time}" >{POST.last_edit_date}</abbr>
					<!-- END:  byModerator-->
					<!-- BEGIN:  byUser-->
					Lần sửa cuối bởi bạn: <abbr class="DateTime" data-time="{POST.last_time}" >{POST.last_edit_date}</abbr>
					<!-- END:  byUser-->
				</div>
				<!-- END: editDate -->
				
				
				<div class="messageMeta ToggleTriggerAnchor">
					<div class="privateControls">
						<span class="item muted">
							<span class="authorEnd"><a href="#" class="username author" dir="auto">{POST.username}</a>,</span>
							<a href="#" title="Permalink" class="datePermalink"><abbr class="DateTime">{POST.post_date}</abbr></a>
						</span>
						<!-- BEGIN: canEdit -->
						<a href="{POST.user_edit}" class="item control edit OverlayTrigger" data-token="{POST.token}" data-href="{POST.post_edit_inline}" data-messageselector="#post-{POST.post_id}"><span></span> Sửa</a>
						<!-- END: canEdit -->
								
						<!-- BEGIN: canDelete -->
						<a href="{POST.user_delete}" data-token="{POST.token}" class="item control delete OverlayTrigger"><span></span>Xóa </a>
						<!-- END: canDelete -->
						
						<!-- BEGIN: canViewIps -->
						<a href="{POST.user_ip}" data-token="{POST.token}" class="item control delete OverlayTrigger"><span></span>Ip </a>
						<!-- END: canViewIps -->
						
						<!-- BEGIN: canWarn -->
						<a href="{POST.user_warn}" data-token="{POST.token}" class="item control delete OverlayTrigger"><span></span>cảnh báo </a>
						<!-- END: canWarn -->
								
						<!-- BEGIN: canReport -->
						<a href="{POST.user_report}" data-token="{POST.token}" class="OverlayTrigger item control report" ><span></span>Báo cáo </a>
						<!-- END: canReport -->
					</div>
					<div class="publicControls">
						<a href="#" title="Permalink" class="item muted postNumber hashPermalink OverlayTrigger" data-href="#">#{POST.post_id}</a>
						
						<!-- BEGIN: canLike -->
						<a href="#" data-posturl="{POST.user_like}" class="LikeLink item control like" data-container="#likes-post-{POST.post_id}"><span></span><span class="LikeLabel">Thích</span></a>
						<!-- END: canLike -->
						
						<!-- BEGIN: canReply -->
						<a href="#" data-posturl="{POST.user_quote}" data-tip="#MQ-{POST.post_id}" class="ReplyQuote item control reply" title="Trả lời"><span></span>Trả lời</a>
						<!-- END: canReply -->
					</div>
				</div>
				
				
				<div id="likes-post-{POST.post_id}">
					<!-- BEGIN: likeUsers -->
					<div class="likesSummary secondaryContent">
						<span class="LikeText">
							 <!-- BEGIN: you_like -->Bạn <!-- END: you_like --><!-- BEGIN: and -->và <!-- END: and --><!-- BEGIN: member_like --> <a href="#{LIKE.userid}" class="username" dir="auto">{LIKE.username}</a> <!-- END: member_like --> thích điều này.
						</span>
					</div>		 
					<!-- END: likeUsers -->
					 
				</div>
			
			</div>
			
		</li>
		<!-- END: post -->
	</ul>
	<div class="clearfix"></div>
	<div class="pageNavLinkGroup">
		
		<!-- BEGIN: generate_page_bottom -->
			<div class="generate_page">
			{GENERATE_PAGE}
			</div>
			<div class="clear"></div>
		<!-- END: generate_page_bottom -->
		
		<!-- BEGIN: guestLogin -->
		<div class="linkGroup">		
			<label for="LoginControl"><a rel="nofollow" href="{USER_LOGIN}" class="concealed element">(Bạn phải đăng nhập hoặc đăng ký để trả lời tại đây.)</a></label>			
		</div>
		<!-- END: guestLogin -->
 
		<!-- BEGIN: noQuickReply -->
		<div class="linkGroup">
			<span class="element">(Bạn không có quyền trả lời tại chủ đề này.)</span>			
		</div>
		<!-- END: noQuickReply -->
 
		<!-- BEGIN: canQuickReply --> 
		<div class="quickReply message">
 
			<form action="{ACTION}" method="post" class="AutoValidator blendedEditor" id="QuickReply">	
				<div class="message">
					{MESSAGE}
				</div>
				<div class="submit">
					<div class="submit2">
					<input type="hidden" name="thread_id" value="{THREAD.thread_id}"> 
					<input type="hidden" name="node_id" value="{THREAD.node_id}"> 
					<input type="hidden" name="last_date" value="{THREAD.last_post_date}"> 
					<input type="hidden" name="token" value="{TOKEN}"> 
					<input type="hidden" name="action" value="QuickReply"> 
					
					<button type="button" id="ThreadPostSubmit" class="btn btn-primary"/><i class="fa fa-spinner fa-lg fa-spin" style="display:none"> </i> Trả lời tin</button>
					<div id="fileupload"> </div>
					<!-- <a type="button" onclick="$('#uploadifive-fileupload input[type=file]').trigger('click');" value="Thêm tệp tin" class="btn btn-primary"/> dsds</a> -->
					<a href="#" class="btn btn-primary tagbutton">Thêm lựa chọn</a> 
					</div>
					<div class="AttachmentEditor" style="display: none;">
						<div class="NoAttachments"></div>	
						
						
						<ol id="AttachmentList" class="AttachmentList New">
							 							
						</ol>
						<input type="hidden" name="attachment_hash" value="{ATTACHMENT_HASH}"> 
					</div>
					<div id="fileupload-queueID"></div>
				</div>
			
			</form>
			
			<link type="text/css" href="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/{MODULE_FILE}/uploadifive/uploadifive.css" rel="stylesheet" />
			<script type="text/javascript" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/{MODULE_FILE}/uploadifive/jquery.uploadifive.js"></script>			 

			<script type="text/javascript">		
			$( document ).ready(function() {
				var validExtensions = ['.rar','.zip','.txt','.pdf','.png','.jpg','.jpeg','.jpe','.gif'];  				
				$('#fileupload').uploadifive({
					'uploadScript': nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=upload&nocache=' + new Date().getTime(),
					'buttonClass': 'btn btn-primary',
					'buttonText': 'Thêm tệp tin',
					'queueID': 'fileupload-queueID',
					'auto': true,
					'multi': true,
					'removeCompleted': true,
					'queueSizeLimit': 10,
					'uploadLimit': 10,
					'fileType': validExtensions,
					'fileSizeLimit': 2048,
					'formData': {
						'node_id': '{THREAD.node_id}',
						'token': '{TOKEN}',
						'attachment_hash': '{ATTACHMENT_HASH}',
					}, 		 
					'onUploadComplete': function(file, res) {
						var obj = $.parseJSON( res );
 
						var item = obj.data;	
						var tmp='';
						tmp+='<li id="attachment'+ item['attachment_id'] +'" class="AttachedFile secondaryContent AttachedImage">';
						tmp+='<div class="Thumbnail">';
						if( item['is_image'] == true )
						{
							tmp+='	<a href="'+ item['image_url'] +'" target="_blank" data-attachmentid="'+ item['attachment_id'] +'" class="_not_LbTrigger" ><img src="'+ item['thumb_url'] +'" alt="'+ item['basename'] +'" class="_not_LbImage" data-src="'+ item['image_url'] +'"></a>';
						}
						else
						{
							tmp+='<span class="genericAttachment"></span>';
						}
						tmp+='</div>';
						tmp+='<div class="AttachmentText">';
						tmp+='	<div class="Filename"><a href="'+ item['image_url'] +'" target="_blank">'+ item['basename'] +'</a></div>';	
						if( item['is_image'] == true )
						{
							tmp+='	<div class="label JsOnly">Chèn:</div>';
							tmp+='	<div class="controls JsOnly">';								
							tmp+='		<input type="button" value="Xóa file" class="button smallButton AttachmentDeleter" data-attachmentid="'+ item['attachment_id'] +'" data-token="'+ item['token'] +'" style="display: block;">';							
							tmp+='		<input type="button" data-attachmentid="'+ item['attachment_id'] +'" name="thumb" value="Ảnh nhỏ" class="button smallButton AttachmentInserter">';
							tmp+='		<input type="button" data-attachmentid="'+ item['attachment_id'] +'" name="image" value="Ảnh lớn" class="button smallButton AttachmentInserter">';									
							tmp+='	</div>'; 
						}else
						{
							tmp+='	<div class="controls JsOnly">';								
							tmp+='		<input type="button" value="Xóa file" class="button smallButton AttachmentDeleter" data-attachmentid="'+ item['attachment_id'] +'" data-token="'+ item['token'] +'" style="display: block;">';							
							tmp+='	</div>'; 
						
						}
						
						tmp+='</div>'; 	
						tmp+='</li>'; 
						$('#AttachmentList').append(tmp).show();
						$('.AttachmentEditor').show();
						
					}
						 
				});		
			});	 			
			$( 'body' ).on('click', '.AttachmentInserter', function(){
				var name = $(this).attr('name');
				var attachmentid = $(this).attr('data-attachmentid');
	 
				if( name=='image' )
				{
					var thumb_url = $('#attachment'+ attachmentid + ' .Thumbnail a img').attr('data-src');
					var html = '<img alt="attachFull'+ attachmentid +'" src="' + thumb_url + '" class="attachFull bbCodeImage" style="max-width:100%"/>';
					CKEDITOR.instances['forum_message'].insertHtml(html);
				} 
				else if( name=='thumb' )
				{
					var thumb_url = $('#attachment'+ attachmentid + ' .Thumbnail a img').attr('src');
					var html = '<img alt="attachThumb'+ attachmentid +'" src="' + thumb_url + '" class="attachThumb bbCodeImage" />';
					CKEDITOR.instances['forum_message'].insertHtml(html);
				}
			
			}) 
			$( 'body' ).on('click', '#ThreadPostSubmit', function(e) {
				 
					var message = CKEDITOR.instances.forum_message.getData();
					if( strip_tags( message, '<img>' ).length >= 10 )
					{
						var form = $('#QuickReply');
						var form_data = form.serializeArray();
						delete form_data.message;
						form_data.push({name: 'message', value: message});
						
						$.ajax({
							type: form.attr('method'),
							url: form.attr('action'),
							data: form_data,
							dataType: 'json',	
							beforeSend: function() {
								$('#ThreadPostSubmit').find('.fa-spinner').css('display', 'inline-block');
								$('#ThreadPostSubmit').prop('disabled',true);
								$('#QuickReply input[type="file"]').prop('disabled',true);
								$('#QuickReply a.tagbutton').addClass('disabled');
								$('#uploadifive-fileupload').addClass('disabled');
							},	
							complete: function() {
								$('#ThreadPostSubmit').find('.fa-spinner').css('display', 'none');
								setTimeout(function() { 
									$('#ThreadPostSubmit').prop('disabled',false) 
									$('#QuickReply input[type="file"]').prop('disabled',false);
									$('#QuickReply a.tagbutton').removeClass('disabled');
									$('#uploadifive-fileupload').removeClass('disabled');
								}, 1000);
							},	
							success: function(json) {		
								if( json['error'] )
								{
									alert( json['error'] );
									return false;
								}
								else if( json['template'] )
								{
									$('ul.messageList').append( json['template'] );
									$('#AttachmentList').empty(); 
									$(json['hastag']).focus(); 
									CKEDITOR.instances.forum_message.setData('');
								}	
								 
							},
							error: function(xhr, ajaxOptions, thrownError) {
								alert(thrownError + "rn" + xhr.statusText + "rn" + xhr.responseText);
							}
						});  
					}
					else
					{
						alert('Tin nhắn phải từ 10 kí tự trở lên');
					
					}
				e.preventDefault();
			});		
			$( 'body' ).on('click', '.AttachmentDeleter', function(e) {
				 
				var attachment_id = $(this).attr('data-attachmentid');		 
				var token = $(this).attr('data-atoken');		 
				var action = 'delete';		 
				$.ajax({
					type: 'post',
					url: nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=attachments&nocache=' + new Date().getTime(),
					data: {attachment_id: attachment_id, token: token, action: action},
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
			</script>
		</div>
		<!-- END: canQuickReply -->
		
		<script type="text/javascript">	
				$( 'body' ).on('click', '.ReplyQuote', function(e) {
			
				var url = $(this).attr('data-posturl');
				$.ajax({
					url: url,
					type: 'post',
					data: {nocache:new Date().getTime()},
					dataType: 'json',	
					beforeSend: function() {
							
					},	
					complete: function() {
						
					},	
					success: function(json) {		
						if( json['error'] )
						{
							alert( json['error'] );
							return false;
						}
						else if( json['quote'] )
						{
							CKEDITOR.instances['forum_message'].setData(json['quote']);
						}	
								 
					},
					error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "rn" + xhr.statusText + "rn" + xhr.responseText);
					}
				});
				
				e.preventDefault();
			});
			$( 'body' ).on('click', '.LikeLink', function(e) {
				var obj = $(this);
				var url = obj.attr('data-posturl');
				$.ajax({
					url: url,
					type: 'post',
					data: {},
					dataType: 'json',	
					beforeSend: function() {
							
					},	
					complete: function() {
						
					},	
					success: function(json) {		
						if( json['error'] )
						{
							alert( json['error'] );
							return false;
						}
						if( json['liked'] )
						{
							obj.find('.LikeLabel').text('Bỏ thích');
						}else{
							obj.find('.LikeLabel').text('Thích');
						}
						if( json['template'] )
						{
							$('#likes-post-' + json['post_id']).html( json['template'] );
						
						}else{
						
							$('#likes-post-' + json['post_id']).html('');
						}				
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "rn" + xhr.statusText + "rn" + xhr.responseText);
					}
				});
				
				e.preventDefault();
			});
				 
			$( 'body' ).on('click', '.privateControls a.control', function(e) {	 	 
				e.preventDefault();
				var url = ( $(this).attr('data-href') ) ? $(this).attr('data-href') : $(this).attr('href');
				$.ajax({
					type: 'post',
					url:  url,
					data: { token: $(this).attr('data-token') },
					dataType: 'json',	
					cache: false,
					beforeSend: function() {
						 
					},	
					complete: function() {
						
					},	
					success: function(json) {		
						
						if( json['error'] )
						{
							alert( json['error'] );
							
						}
						if( json['template'] && json['delete'] )
						{
							$('#DeletePostForm').remove();
							$('body').prepend( json['template'] );
							$('#DeletePostForm').modal();
						}			
						else if( json['template'] )
						{
							$('#EditInline').remove();
							$('body').prepend( json['template'] );
							$('#EditInline').modal();
						}
								 
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "rn" + xhr.statusText + "rn" + xhr.responseText);
					}
				});  
				e.preventDefault();
			});
			$( 'body' ).on('click', '#SubmitEditInline', function(e) {	 	 
				
				var message = CKEDITOR.instances.forum_message.getData();
				if( strip_tags( message, '<img>' ).length >= 10 )
				{
				
					var form = $('#FormEditInline');
					var form_data = form.serializeArray();
					delete form_data.message;
					form_data.push({name: 'message', value: message});
			 
					 
					$.ajax({
						type: form.attr('method'),
						url: form.attr('action'),
						data: form_data,
						dataType: 'json',	
						cache: false,
						beforeSend: function() {
							$('#SubmitEditInline').find('.fa-spinner').css('display', 'inline-block');
						},	
						complete: function() {
							$('#SubmitEditInline').find('.fa-spinner').css('display', 'none');
							setTimeout(function() { $('#SubmitEditInline').prop('disabled',false) }, 1000); 
						},	
						success: function(json) {		
							if( json['template'] )
							{
								$('#post-'+json['post_id']).replaceWith( json['template'] ).focus(); 
								$('#EditInline').modal('hide');
								$('#EditInline').remove();
								$('.modal-backdrop').remove();
								$('body').removeClass('modal-open');
							}		 
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "rn" + xhr.statusText + "rn" + xhr.responseText);
						}
					});  
				}else
				{
					alert('Tin nhắn phải từ 10 kí tự trở lên');
					
				}
				e.preventDefault();
			});
			
			$( 'body' ).on('click', '#SubmitDeletePost', function(e) {	 	 
				
				var form = $('#FormDeletePost');
				var form_data = {
					reason: $('#FormDeletePost input[name="reason"]').val(),
					post_id: $('#FormDeletePost input[name="post_id"]').val(),
					token: $('#FormDeletePost input[name="token"]').val(),
					action: $('#FormDeletePost input[name="action"]').val()				
				};	
		 
						
				$.ajax({
					type: form.attr('method'),
					url: form.attr('action'),
					data: form_data,
					dataType: 'json',	
					cache: false,
					beforeSend: function() {
						$('#SubmitDeletePost').find('.fa-spinner').css('display', 'inline-block');
					},	
					complete: function() {
						$('#SubmitDeletePost').find('.fa-spinner').css('display', 'none');
						setTimeout(function() { $('#SubmitDeletePost').prop('disabled',false) }, 1000); 
					},	
					success: function(json) {		
						if( json['error'] )
						{
							alert( json['error'] );
							
						}else if( json['redirect'] )
						{
							window.location.href = json['redirect'];
							
						}						
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "rn" + xhr.statusText + "rn" + xhr.responseText);
					}
				});  
				e.preventDefault();
			});

		</script>
	</div>
	<div class="clearfix"></div>
	<div class="sharePage">
		<h3 class="textHeading larger">Chia sẻ trang này</h3>
		<div class="tweet shareControl">
<!-- 			<a href="#" class="twitter-share-button" data-count="horizontal" data-lang="en-US" data-url="{SITE_URL}" data-text="">Tweet</a>
		 -->
		</div>
		<div class="facebookLike shareControl">
					
			<div class="fb-like" data-href="{SITE_URL}" data-width="400" data-layout="standard" data-action="recommend" data-show-faces="true" data-colorscheme="light"></div>
		</div>
		 
	</div>
	<div class="clearfix"></div>
	</div>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_EDITORSDIR}/ckeditor/plugins/codesnippet/lib/highlight/highlight.pack.js"></script>
<script type="text/javascript">hljs.initHighlightingOnLoad();</script>
<!-- END: main -->