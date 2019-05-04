<!-- BEGIN: main -->
<h2>{DATA.title}</h2>

<p id="pageDescription" class="muted "> Chủ đề trong '<b><a href="{CAT.link}">{CAT.title}</a></b>' đăng bởi <b><a href="#" class="username">{DATA.username}</a></b>, <a href="{DATA.link}"><span class="DateTime" title="{DATA.post_time} lúc {DATA.post_date}">{DATA.post_date}</span></a>. </p>

<!-- BEGIN: loop_page -->
<div class="topic-actions">
    <div class="buttons">
        <div class="reply-icon">
			<!-- BEGIN: show_editor2 -->
			<a onclick="show_editor()" href="javascript:void(0);" title="Trả lời"><span></span>Trả lời</a>
			<!-- END: show_editor2 -->
			<!-- BEGIN: none_user2 -->
			<a onclick="show_boxlogin();" href="javascript:void(0);"  title="Trả lời"><span></span>Trả lời</a>
			<!-- END: none_user2 -->
        </div>
    </div>
    <div class="search-box">
        <form class="form-inline" method="get" id="forum-search" action="#">
            <fieldset>
                <div class="search-box-inner">
                    <input class="button2" type="submit" value="Search">
                    <input class="form-control inputbox search tiny" type="text" name="keywords" id="keywords" size="20" value="Tìm kiếm chủ đề này…" onclick="if (this.value == 'Tìm kiếm chủ đề này…') this.value = '';" onblur="if (this.value == '') this.value = 'Tìm kiếm chủ đề này…';">
                </div>
            </fieldset>
        </form>
    </div>
    <div class="pagination">
		<div class="thread_view">
		  <div class="threadStat">Chủ đề này đã có <font style="color:green;font-weight:bold;">{DATA.view_count}</font> lượt đọc và <font style="color:green;font-weight:bold;">{DATA.reply_count}</font> bài trả lời</div>
		</div>
		<nav> 
		  <!-- BEGIN: generate_page0 -->
		  <div class="generate_page" style="float:left"> {GENERATE_PAGE} </div>
		  <!-- END: generate_page0 --> 
		</nav>
    </div>
	

</div>
<div class="clear"></div>

<!-- BEGIN: loop -->
<div id="post{loop.post_id}" class="thread_view post bg2">

    <div class="postbody">
        <h3 class="first"><img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/question.gif" width="16" height="16" alt=""> <a href="#post{loop.post_id}">{DATA.title}</a></h3>
        <p class="author">
            <a href="#">
                <img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/icon_post_target.png" width="11" height="9" alt="Post" title="Post">
            </a>{LANG.by} <strong><a href="{USER.user_page}" style="color: #AA0000;" class="username-coloured" itemprop="name">{loop.username}</a></strong> » {loop.post_date}
		</p>
		<article>
        <div class="thread_content" id="thread_{loop.post_id}">
			{loop.message}
		</div>
		<div class="after_content" id="after_content_{loop.post_id}">
			  <!-- BEGIN: edit -->   
			  <span class="postcontent lastedited"> Sửa lần cuối bởi {last_edit_user}; {last_edit_date1} lúc <span class="time">{last_edit_date2}</span>. </span>
			  <!-- END: edit --> 
		</div>
		</article>
		<div style="display:none" id="thread_tem_{loop.post_id}"></div>
		
		<div id="likes-post-{loop.post_id}">
			<!-- BEGIN: like -->
			<div class="likesSummary secondaryContent">
				<span class="LikeText"> 
					<!-- BEGIN: looplike -->  
					  <a href="{userpage_like}" title="{fullname_like}" class="username">{username_like}</a><!-- BEGIN: comma -->, <!-- END: comma -->
					<!-- END: looplike -->
					<!-- BEGIN: all_like --> 
					và <a rel='building' href="#likes" class="OverlayTrigger">{all_like} người khác</a> 
					<!-- END: all_like -->
					&nbsp;thích bài này.			
				  </span>

			</div>
			<!-- END: like -->
		</div>
		<div class="messageMeta controlAfterContent">
			<div class="privateControls">
				<img style="display:none" id="progress_{loop.post_id}" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/progress.gif" alt="">
				<!-- BEGIN: user_edit -->
					
					<a class="item control" href="javascript:void(0);" id="edit_post_{loop.post_id}" onclick="load_message('{catid}','{loop.post_id}','{loop.checkss}');" title="Sửa bài viết">
					<img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/clear.gif" id="editimg_{loop.post_id}" alt="Sửa bài viết" title="Sửa bài viết">Sửa</a>
				<a class="item separate announceSeparate">|</a>
				<!-- END: user_edit -->

				<!-- BEGIN: user_del -->

				<a class="item control" href="javascript:void(0);" id="del_post_{loop.post_id}" onclick="del_message('{catid}','{DATA.thread_id}','{loop.post_id}','{loop.checkss_admin}');" title="Xóa bài viết">
					<img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/clear.gif" id="delimg_{loop.post_id}" alt="Xóa bài viết" title="Xóa bài viết">Xóa</a>
				<a class="item separate announceSeparate">|</a>
				<!-- END: user_del -->


				<!-- BEGIN: reply -->
				<a id="qr_{loop.post_id}" class="ReplyQuote item control reply" href="#quick_reply" onclick="quickreply({loop.post_id})" rel="nofollow" title="Trả lời nhanh"> Trả lời</a> 
				<a class="MultiQuoteControl JsOnly item control" href="#quick_reply" rel="nofollow" onclick="insert_quote('{loop.post_id}', '{loop.username}');" id="qrwq_{loop.post_id}" title="Trả lời kèm trích dẫn"><span></span><span id="quoteimg{loop.post_id}" class="symbol">Trích dẫn</span></a> 

				<!-- END: reply -->

				<!-- BEGIN: nonereply -->
				<a class="item separate">|</a> 
				<a class="MultiQuoteControl JsOnly item control" href="#quick_reply" rel="nofollow" id="qrwq_{loop.post_id}" title="Trả lời kèm trích dẫn"><span></span><span class="symbol">Trích dẫn</span></a>
				<!-- END: nonereply -->
				<input type="hidden" id="checkss_{loop.post_id}" name="checkss_{loop.post_id}" value="{loop.checkss}" />

			</div>
			<div class="publicControls">
				<a rel='building' href="#announce" class="AnnounceLink item control announce" data-container="#announce-post-10257548"><span></span><span class="AnnounceLabel">Loan tin</span></a>
				<a class="item separate announceSeparate">|</a> 
				<ul class="profile-icons2">
					<li class="like-icon">
						<form class="form-inline" action="" onsubmit="return false">
							<div class="pluginConnectButton">
								<div id="facebookconnect{loop.post_id}" class="pluginButton pluginButtonInline pluginConnectButtonDisconnected {like}">
									<div>
										<button type="submit" onclick="like_post('{loop.post_id}', '{loop.user_id}', '{loop.username}', '{loop.checkss_like}')"><i class="pluginButtonIcon img sp_like-send sx_like-send_thumb"></i>Thích</button>
									</div>
								</div>
							</div>

							<div id="facebookdisconnect{loop.post_id}" class="pluginButton pluginButtonPressed pluginButtonInline pluginButtonX pluginConnectButtonConnected {unlike}">
								<div>
									<button type="submit" onclick="like_post('{loop.post_id}', '{loop.user_id}','{loop.username}', '{loop.checkss_like}')">
										<i class="pluginButtonIcon pluginButtonXOff img sp_like-send sx_like-send_ch"></i>
									</button>
									Thích</div>

							</div>
							<span class="count_like" id="count_like_{loop.post_id}">{loop.like_count}</span>

						</form>

					</li>
				</ul>
				<a rel='building' href="#" class="OverlayTrigger item control report" data-cacheOverlay="false"><span></span>Báo vi phạm</a> 
			</div>
			<div class="clear"></div>
		</div>
    </div>

    <dl class="postprofile" id="profile{loop.post_id}">
        <dt>
			<a href="{USER.user_page}" class="avatar Av{USER.userid}m"> <img src="{USER.photo}" width="96" height="96" alt="{USER.username}" /> </a><br>
			<a href="{USER.user_page}" style="color: #AA0000;" class="username-coloured">{USER.username}</a>
		</dt>
        <!-- <dd>Site Admin</dd> -->
        <dd>&nbsp;</dd>
        <dd><strong>{LANG.post}:</strong> {USER.message_count}</dd>
        <dd><strong>{LANG.Joined}:</strong> {USER.regdate}</dd>
        <dd><strong>{LANG.like_count}:</strong> {USER.like_count}</dd>

    </dl>


    <div class="back2top"><a href="#wrap" class="top" title="Top">Top</a>
    </div>

</div>

<hr class="divider">
 <!-- END: loop -->


<form class="form-inline" id="viewtopic" method="post" action="#" onsubmit="alert('chức năng này đang được xây dựng'); return false">

    <fieldset class="display-options" style="margin-top: 0; ">

        <label>Hiển thị bài đăng theo:
            <select class="form-control" name="st" id="st">
                <option value="0" selected="selected">Tất cả bài đăng</option>
                <option value="1">1 ngày</option>
                <option value="7">7 ngày</option>
                <option value="14">2 tuần</option>
                <option value="30">1 tháng</option>
                <option value="90">3 tháng</option>
                <option value="180">6 tháng</option>
                <option value="365">1 year</option>
            </select>
        </label>
        <label>Sắp xếp theo
            <select class="form-control" name="sk" id="sk">
                <option value="a">Người đăng</option>
                <option value="t" selected="selected">Thời gian đăng</option>
                <option value="s">Tiêu đề</option>
            </select>
        </label>
        <label>
            <select class="form-control" name="sd" id="sd">
                <option value="a" selected="selected">Cũ dần</option>
                <option value="d">Mới dần </option>
            </select>
            <input type="submit" name="sort" value="Go" class="button2" rel='building'>
        </label>

    </fieldset>

</form>
<hr>


<div class="topic-actions">
    <div class="buttons">
        <div class="reply-icon">
		<!-- BEGIN: show_editor1 -->
		<a onclick="show_editor()" href="javascript:void(0);" title="Trả lời"><span></span>Trả lời</a>
		<!-- END: show_editor1 -->
		<!-- BEGIN: none_user1 -->
		<a onclick="show_boxlogin();" href="javascript:void(0);" title="Trả lời"><span></span>Trả lời</a>
		<!-- END: none_user1 -->
		</div>
    </div>
    <div class="pagination">
        <div class="thread_view">
		  <div class="threadStat">Chủ đề này đã có <font style="color:green;font-weight:bold;">{DATA.view_count}</font> lượt đọc và <font style="color:green;font-weight:bold;">{DATA.reply_count}</font> bài trả lời</div>
		</div>
		<nav> 
		  <!-- BEGIN: generate_page1 -->
		  <div class="generate_page" style="float:left"> {GENERATE_PAGE} </div>
		  <!-- END: generate_page1 --> 
		</nav>
    </div>
</div>
<!-- END: loop_page -->
<a href="{CAT.link}" class="left-box left block-link" accesskey="r">Quay lại chủ đề: {CAT.title}</a>

<!-- BEGIN: user_reply -->
	<div class="clear"></div>
	<div class="quickReply">
	  <form class="form-inline vbform" name="quick_reply" id="quick_reply" action="{ACTION}" method="post" enctype="multipart/form-data">
		<div class="fullwidth">
		  <h3 id="quickreply_title" class="blockhead"><img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/reply_40b.png" alt="Trả lời nhanh" style="float:left;padding-right:10px"/> Trả lời nhanh<a name="quickreply"></a> <img style="display:none" id="progress_newreplylink_bottom" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/progress.gif" alt="" /></h3>
		</div>
		<div class="wysiwyg_block">
		  <div class="blockbody formcontrols"><textarea class="forum_bbcode" id="message" name="message" style="height:150px;width:99%;" title="Nội dung bài viết phải lớn hơn 10 ký tự" minlength="10" required></textarea>
		  </div>
		  <div class="blockfoot actionbuttons">
			<div class="group" class="text-center" style="margin-top:10px">
			  <input type="submit" class="button2" value="Gửi trả lời" name="sbutton"  id="qr_submit" />
			  <!-- <input type="submit" class="button primary" value="Đến bản đầy đủ..." name="preview" id="qr_preview" /> -->
			  <span id="qr_posting_msg" class="hidden"> <img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/progress.gif" alt="Đang gửi trả lời nhanh - Xin đợi" />&nbsp;<strong>Đang gửi trả lời - Xin đợi</strong> </span> 
			  <!-- <input type="submit" class="button primary" value="Nâng cao" accesskey="x" title="(Alt + X)" name="preview" tabindex="1" id="qr_preview"  /> --> 
			</div>
		  </div>
		</div>
		<input type="hidden" name="checkss" value="{CHECKSS}" />
		<input type="hidden" name="action" value="post_reply" />
		<input type="hidden" name="thread_id" value="{DATA.thread_id}" id="thread_id" />
	  	<input type="hidden" name="catid" value="{DATA.catid}" id="catid" />
	  	<input type="hidden" name="page" value="{page}" id="page" />
	  </form>
	  <script type="text/javascript">
		$(function(){
		  $('#quick_reply').submit(function(){
			var message = $('#message').val();
			if( message.length < 5 )
			{
				alert('{LANG.error_message}');
				$('#message').focus();
			}else
			{
				$('#qr_posting_msg').show();
				$.ajax({
					url: $(this).attr('action'),
					type: 'POST',
					data: $(this).serialize(),
					dataType: "json",
					async: false,
					success: function (res) {
						var qr_submit = document.getElementById('qr_submit');	
			
						var message = res.data.message;
						var items = res.data.item;
						qr_submit.disabled = false;
						if(message == 'success')
						{	
							$('#qr_posting_msg').html('<div class="success" style="display: none;"><strong>' + items['message'] + ' </strong><span class="close" onclick="close();"><img src="' + nv_siteroot + 'themes/{TEMPLATE}/images/close.png" alt="" class="close" /></span></div>').show();
							$('.success').fadeIn('slow');
							$(".close").click(function () {
								
								$('#qr_posting_msg').hide();
								window.location.href = items['link']+items['hash'];
								if( $('#page').val() == items['page'] )
								window.location.reload();
							});	
							setTimeout(function() {
								$('#qr_posting_msg').hide();
							
								window.location.href = items['link']+items['hash'];
								if( $('#page').val() == items['page'] )
								window.location.reload();
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
				});
				
			}
			return false;
		  });
		});
		</script>
	</div>
	<!-- END: user_reply -->
	
	<!-- BEGIN: other -->
	<div class="statsList">
		<h3>Chủ đề cùng chuyên mục</h3>
		<ul class="secondaryContent">
		  <!-- BEGIN: loop -->
		  <li> <a href="{LOOP.link}" title="{LOOP.title}">{LOOP.title}</a> </li>
		 <!-- END: loop -->
		</ul>
	 </div>
	 <!-- END: other -->
	 
<form class="form-inline" method="post" id="jumpbox" action="#" onsubmit="if(this.f.value == -1){return false;}">
    <fieldset class="jumpbox">

        <label for="f" accesskey="j">Chuyển tới:</label>
        <select class="form-control" name="f" id="f" onchange="if(this.options[this.selectedIndex].value != -1){ window.location = this.options[this.selectedIndex].value }">

            <option value="-1">Chọn diễn đàn</option>
            <option value="-1">------------------</option>
			<!-- BEGIN: qcat -->
			<option value="{QCAT.link}" {QCAT.selected}> {QCAT.title}</option>
			<!-- END: qcat -->
        </select>
        <input type="submit" value="Go" class="button2">
    </fieldset>
</form>

<!-- END: main -->