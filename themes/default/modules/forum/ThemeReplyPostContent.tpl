<!-- BEGIN: main -->
<li id="post-{POST.post_id}" class="message {POST.staff} clearfix" data-author="{POST.username}">
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
		<!-- BEGIN: signature -->
		<div class="baseHtml signature messageText ugc"><aside>{POST.signature}</aside></div>
		<!-- END: signature -->
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
		<div id="likes-post-{POST.post_id}"></div>
	</div>
</li>
<!-- END: main -->