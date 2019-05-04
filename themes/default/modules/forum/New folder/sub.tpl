<!-- BEGIN: main -->
<div id="threadlist" class="threadlist">
  <form class="form-inline" id="thread_inlinemod_form" action="" method="post">
    <div>
      <div class="threadlisthead table">
        <div> <span class="threadinfo"> 
		<span class="threadtitle"> <a href="#" rel="nofollow">Tiêu đề</a> / <a href="#" rel="nofollow">Chủ đề</a> </span> </span> <span class="threadstats td"><a href="#" rel="nofollow">Trả lời</a> / <a href="%" rel="nofollow">Xem</a></span> <span class="threadlastpost td"><a href="#" rel="nofollow">Bài cuối </a></span> </div>
      </div>
      <!-- BEGIN: sticky -->
	  <ol id="stickies" class="stickies">
        <li class="forumbit_nopost L1" id="cat1">
          <div class="forumhead foruminfo L1">
            <div class="forumrowdata">
              <p class="subforumdescription"><strong>Chủ đề chú ý &nbsp;</strong></p>
            </div>
          </div>
        </li>
		<!-- BEGIN: loop_sticky -->
        <li class="threadbit hot" id="thread_2796">
          <div class="rating0 sticky">
            <div class="threadinfo" title=""> 
			<a class="threadstatus"></a>
			<div class="inner">
                <h3 class="threadtitle"> <span id="thread_prefix_{loop.last_post_id}" class="prefix understate"> </span> <a class="title" title="{loop.title}" href="{loop.last_link}#post{loop.last_post_id}" id="thread_title_{loop.last_post_id}">{loop.title}</a> </h3>
                <div class="threadmeta">
                  <div class="author"> <span class="label">Đăng bởi&nbsp;<a href="#" class="username understate" title="Đăng  bởi {loop.last_post_username} lúc {loop.last_post_date}">{loop.last_post_username}</a>‎,&nbsp;{loop.last_post_date}</span>
                    <div class="threaddetails td">
                      <div class="threaddetailicons"> </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <ul class="threadstats td alt" title="">
              <li>Trả lời: {loop.reply_count}</li>
              <li>Xem: {loop.view_count}</li>
              <!-- <li class="hidden">Đánh giá 0 / 5</li> -->
            </ul>
            <dl class="threadlastpost td">
              <dt class="lastpostby hidden">Gửi bởi </dt>
              <dd>
                <div class="popupmenu memberaction" id="yui-gen{loop.last_post_user_id}"> <a class="username offline popupctrl" href="#" title="{loop.last_post_username}" id="yui-gen{loop.last_post_user_id}#post{loop.last_post_id}"><strong>{loop.last_post_username}</strong></a>
                </div>
              </dd>
              <dd>{loop.last_post_date} <a href="{loop.last_link}#post{loop.last_post_id}" class="lastpostdate understate" title="Đến bài cuối"><img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/lastpost-right.png" alt="Đến bài cuối" title="Đến bài cuối"></a> </dd>
            </dl>
          </div>
        </li>
		<!-- END: loop_sticky -->
        
      </ol>
	  <!-- END: sticky -->
	  
	  
	  <!-- BEGIN: normal -->
      <ol id="threads" class="threads">
        <li class="forumbit_nopost L1" id="cat1">
          <div class="forumhead foruminfo L1">
            <div class="forumrowdata">
              <p class="subforumdescription"><strong>Chủ đề thường &nbsp;</strong></p>
            </div>
          </div>
        </li>
		<!-- BEGIN: loop_normal -->
		<li class="threadbit hot attachments" id="thread_2080">
          <div class="rating0 nonsticky">
            <div class="threadinfo"> <a class="threadstatus"></a>
			<div class="inner">
                <h3 class="threadtitle"> <a class="title" title="{loop.title}" href="{loop.last_link}#post{loop.last_post_id}" id="thread_title_{loop.last_post_id}">{loop.title}</a> </h3>
                <div class="threadmeta">
                  <div class="author"> <span class="label">Đăng bởi&nbsp;<a href="{loop.last_link}#post{loop.last_post_id}" class="username understate" title="Đăng bởi {loop.last_post_username} lúc {loop.last_post_date}">{loop.last_post_username}</a>‎,&nbsp;{loop.last_post_date}</span>
                    <div class="threaddetails td">
                      <div class="threaddetailicons"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <ul class="threadstats td alt">
              <li>Trả lời: {loop.reply_count}</li>
              <li>Xem: {loop.view_count}</li>
              <!-- <li class="hidden">Đánh giá0 / 5</li> -->
            </ul>
            <dl class="threadlastpost td">
              <dt class="lastpostby hidden">Bài cuối </dt>
              <dd>
                <div class="popupmenu memberaction" id="yui-gen22"> <a class="username offline popupctrl" href="#" title="{loop.last_post_username}" id="yui-gen24"><strong>{loop.last_post_username}</strong></a>
                </div>
              </dd>
              <dd>{loop.last_post_date} <a href="{loop.link}#thread{loop.last_post_id}" class="lastpostdate understate" title="Đến bài cuối"><img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/lastpost-right.png" alt="Đến bài cuối" title="Đến bài cuối"></a> </dd>
            </dl>
          </div>
        </li>
		<!-- END: loop_normal -->
      </ol>
	  <!-- END: normal -->
		<!-- BEGIN: generate_page -->
		<div class="generatepage">
			{GENERATE_PAGE}
		</div>
		<!-- END: generate_page -->	
    </div>
    <hr>
  </form>
</div>

<!-- END: main -->