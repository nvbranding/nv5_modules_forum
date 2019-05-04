<!-- BEGIN: main -->

<!-- BEGIN: topx -->

<div id="vbtopx_content" class="childforum forumbit_post">
    <div class="forumrow table" style="padding-right:0px">
      <div class="left-mainbox">
        <div class="mainbox">
          <ul class="tabs">
            <li class="current"> <span style="padding: 0px 5px;">
              <select class="form-control" id="vietvbb_topstats_s" style="width: auto;">
                <option  value="newest_members">Thành viên mới nhất</option>
                <option value="top_posters">Viết nhiều nhất</option>
                <!-- <option  value="thanked_members">Like nhiều nhất</option> -->
              </select>
              </span> </li>
            <li style="border-right: 0px; display: none;" id="vietvbb_topstats_s_loading"><img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/13x13progress.gif" border="0" align="middle" alt="" /></li>
          </ul>
          <div style="display:block;background: url({NV_BASE_SITEURL}themes/{TEMPLATE}/images/list.gif) no-repeat top left; border-top: 0px none; padding: 0px;">
            <div class="topx-content" id="vietvbb_topstats_s_content">
              
			 <!-- BEGIN: newest_members -->
				<div class="topx-bit">
					<em title="Ngày tham gia">
							{loop.regdate}
					</em>
					<span class="topx-content-menu">
						<a {loop.lev} href="#" title="{loop.username}">{loop.username}</a>
					</span>
				</div>
				<!-- END: newest_members -->
			</div>
          </div>
        </div>
      </div>
      <div class="right-mainbox">
        <div class="mainbox">
          <ul class="tabs" id="vietvbb_topstats_t">
            <li id="latest_posts" class="current"><span style="padding: 0px 8px;">Bài viết mới</span></li>
            <li id="hottest_threads" class=""><span style="padding: 0px 8px;">Chủ đề hot</span></li>
            <li id="most_viewed" class=""><span style="padding: 0px 8px;">Xem nhiều nhất</span></li>
            <!-- <li id="thanked_members" class=""><span style="padding: 0px 8px;">Like nhiều nhất</span></li> -->
            <li id="newest_members" class=""><span style="padding: 0px 8px;">Thành viên mới nhất</span></li>
            <li style="border-right: 0px; display: none;" id="vietvbb_topstats_t_loading"><img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/13x13progress.gif" border="0" align="middle" alt="" /></li>
          </ul>
          <div class="topx-content" id="vietvbb_topstats_t_content">
            <!-- BEGIN: loop -->
			<div class="topx-bit"> <em> <a href="#" title="{loop.last_post_username}"> <font {loop.lev}>{loop.last_post_username}</font> </a> </em> <span class="topx-content-tab"> <img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/post_new.gif" border="0" alt="" /> &nbsp; 
			<a href="{loop.last_link}" title="{loop.title}" >{loop.title1}</a> </span>
            </div>
			<!-- END: loop -->
         </div>
        </div>
      </div>
      <div class="clear"></div>
    </div>
</div>
<script type="text/javascript" src="{NV_BASE_SITEURL}modules/{MODULE_FILE}/js/block_topx.js"></script>
<!-- END: topx -->

<!-- BEGIN: latest_posts -->
<!-- BEGIN: loop -->
<div class="topx-bit"> <em> <a href="#" title="{loop.last_post_username}"> <font {loop.lev}>{loop.last_post_username}</font> </a> </em> <span class="topx-content-tab"> <img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/post_new.gif" border="0" alt="" /> &nbsp; 
	<a href="{loop.last_link}" title="{loop.title}" >{loop.title1}</a> </span>
</div>
<!-- END: loop -->
<!-- END: latest_posts -->


<!-- BEGIN: hottest_threads -->
<!-- BEGIN: loop -->
<div class="topx-bit"> <em title="Trả lời"> {loop.reply_count} </em> <span class="topx-content-tab"> <img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/post_old.gif" border="0" alt="" />&nbsp; <a href="{loop.link}#post{loop.last_post_id}" >{loop.title}</a> </span>
</div>
<!-- END: loop -->
<!-- END: hottest_threads -->

<!-- BEGIN: most_viewed -->
<!-- BEGIN: loop -->
<div class="topx-bit"> <em title="Xem nhiều nhất"> {loop.view_count} </em> <span class="topx-content-tab"> <img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/post_old.gif" border="0" alt="" />&nbsp; <a href="{loop.link}#post{loop.last_post_id}" >{loop.title}</a> </span>
  <div class="floatcontainer forumbit_nopost" id="tip_{loop.thread_id}" style="display:none;">
  </div>
</div>
<!-- END: loop -->
<!-- END: most_viewed -->

<!-- BEGIN: thanked_members -->
<!-- BEGIN: loop -->
<div class="topx-bit">
	<em title="Total Thanks">
    	2649
	</em>
	<span class="topx-content-menu">
		<a href="#" title="EDDIE NGUYEN"><font color="green">username</font></a>
	</span>
</div>
<!-- END: loop -->
<!-- END: thanked_members -->

<!-- BEGIN: newest_members -->
<!-- BEGIN: loop -->
<div class="topx-bit">
	<em title="Ngày tham gia">
			{loop.regdate}
	</em>
	<span class="topx-content-menu">
		<a {loop.lev} href="#" title="{loop.username}">{loop.username}</a>
	</span>
</div>
<!-- END: loop -->
<!-- END: newest_members -->

<!-- BEGIN: top_posters -->
<!-- BEGIN: loop -->
<div class="topx-bit">
	<em title="Bài viết">
    	{loop.total_post}
	</em>
	<span class="topx-content-menu">
		<a href="#" title="{loop.username}"><font {loop.lev}>{loop.username}</font></a>
	</span>
</div>
<!-- END: loop -->
<!-- END: top_posters -->

<div class="clear"></div>

<!-- END: main -->