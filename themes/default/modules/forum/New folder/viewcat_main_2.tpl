<!-- BEGIN: main -->

<ol id="forums" class="floatcontainer">
	<!-- BEGIN: item --> 
	<li class="forumbit_nopost new L1" id="cat1">
    <div class="forumhead foruminfo L1">
      <h2> 
		<span class="forumtitle"><a href ="{CAT.link}">{CAT.title}</a></span> 
		<span class="forumlastpost">Bài cuối</span>  
	  </h2>
      <div class="forumrowdata">
        <p class="subforumdescription">{CAT.description}</p>
      </div>
    </div>
    <ol id="c_cat1" class="childforum">
      <!-- BEGIN: subcat --> 
	  <li id="forum{SUB.id}" class="forumbit_post old L2">
        <div class="forumrow table">
          <div class="foruminfo td"> <img src="{SUB.thumbnail}" class="forumicon" id="forum_statusicon_10" alt="">
            <div class="forumdata">
              <div class="datacontainer">
                <div class="titleline">
                  <h2 class="forumtitle"><a href="{SUB.link}">{SUB.title}</a></h2>
                </div>
				<!-- BEGIN: description --> <p class="forumdescription">{description}</p><!-- END: description -->
                <!-- BEGIN: sub_3 --> 
				<div class="sub_forum" style="clear:both">
					<ul class="sub_forum2">
					<!-- BEGIN: sub3 --> 
						<li><img style="width:20px" src="{SUB3.thumbnail}" /><a href="{SUB3.link}">{SUB3.title}</a></li>
					<!-- END: sub3 --> 
					</ul>
				</div>
				<!-- END: sub_3 --> 
              </div>
            </div>
          </div>
          <h4 class="nocss_label">Hoạt động :</h4>
          <div class="forumactionlinks"></div>
          <h4 class="nocss_label">Thống kê diễn đàn:</h4>
          <ul class="forumstats td">
            <li>Chủ đề: {SUB.threadcount}</li>
            <li>Bài viết: {SUB.replycount}</li>
          </ul>
		  <!-- BEGIN: lastpost -->
          <div class="forumlastpost td">
            <h4 class="lastpostlabel">Bài cuối:</h4>
            <div>
              <p class="lastposttitle"><a href="{SUB.last_link}#post{SUB.last_post_id}" class="threadtitle" title="{SUB.last_thread_title}">{SUB.last_thread_title1}</a> <a href="{SUB.last_link}#post{SUB.last_post_id}"><img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/lastpost-right.png" alt="Đến bài cuối" title="Đến bài cuối"></a> </p>
              <div class="lastpostby"> bởi
                <div class="popupmenu memberaction"> <a class="username offline popupctrl" href="#" title="{SUB.last_post_username}"><strong>{SUB.last_post_username}</strong></a>
                </div>
              </div>
              <p class="lastpostdate">{SUB.last_post_date}</p>
            </div>
          </div>
		  <!-- END: lastpost --> 
        </div>
      </li>
     <!-- END: subcat --> 
	 </ol>
  </li>
	<li>{CENTER}
	<div class="clear"></div>
	</li>
	<!-- END: item --> 
</ol>
<!-- END: main -->