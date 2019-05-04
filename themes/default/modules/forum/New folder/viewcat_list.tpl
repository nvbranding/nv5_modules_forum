<!-- BEGIN: main -->
<!-- BEGIN: content -->
<div class="forumbg">
	<ul class="topiclist">
		<li class="header">
			<dl class="icon">
				<dt><span class="forum-name">{LANG.topic}<span></span></span></dt>
				<dd class="posts">{LANG.reply}</dd>
				<dd class="views">{LANG.view}</dd>
				<dd class="lastpost"><span>{LANG.lastpost}</span></dd>
			</dl>
		</li>
	</ul>
	<ul class="topiclist topics">
		<!-- BEGIN: loop -->
		<li class="row bg1">
			<dl class="icon">
				<dt title="{LOOP.title}">
				<span style="float: left;"><img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/topic_read.png"></span>
				<a href="{LOOP.link}" class="topictitle">{LOOP.title}</a>
					<br>
					{LANG.by} <a href="{LOOP.user_page}" style="color: #AA0000;" class="username-coloured">{LOOP.username}</a> » {LOOP.post_date}
				</dt>
				<dd class="posts">{LOOP.all_page} <dfn>{LANG.reply}</dfn></dd>
				<dd class="views">{LOOP.view_count} <dfn>{LANG.view}</dfn></dd>
				<dd class="lastpost"><span><dfn>{LANG.lastpost} </dfn>{LANG.by} 
					<a href="{LOOP.user_page}" style="color: #AA0000;" class="username-coloured">{LOOP.last_post_username}</a>
					<a href="{LOOP.link}"><img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/icon_topic_latest.png" width="12" height="10" alt="{LOOP.title}" title="{LOOP.title}"></a> <br>{LOOP.last_post_date}</span>
				</dd>
			</dl>
		</li>
		<!-- END: loop -->
	</ul>
</div>
<!-- END: content -->

<!-- BEGIN: item --> 
<div class="forabg">
    <ul class="topiclist">
        <li class="header">
            <dl class="icon">
                <dt><h2 class="forumtitle"><a href ="{CAT.link}">{CAT.title}</a></h2></dt>
                <dd class="topics">{LANG.topic}</dd>
                <dd class="posts">{LANG.post}</dd>
                <dd class="lastpost"><span>{LANG.lastpost}</span>
                </dd>
            </dl>
        </li>
    </ul>
    <ul class="topiclist forums">
        <!-- BEGIN: subcat -->
		<li class="row">
            <dl class="icon" >
                <dt title="{SUB.title}"> 
					<span class="forumicon"><img src="{SUB.thumbnail}" class="forumicon" alt="{SUB.title}"></span>
					<a href="{SUB.link}" class="forumtitle">{SUB.title}</a><br />
					<!-- BEGIN: description --> 
					<p class="description">{description}</p>
					<!-- END: description -->
					
					<!-- BEGIN: sub_3 --> 
					<div class="sub_forum" style="margin-left: 44px;">
						<ul class="sub_forum2">
						<!-- BEGIN: sub3 --> 
							<li><img style="width:20px" src="{SUB3.thumbnail}" /><a href="{SUB3.link}">{SUB3.title}</a></li>
						<!-- END: sub3 --> 
						</ul>
					</div>
					<!-- END: sub_3 -->
					
				</dt>
				
                <dd class="topics">{SUB.threadcount} <dfn>{LANG.topic}</dfn>
                </dd>
                <dd class="posts">{SUB.replycount} <dfn>{LANG.post}</dfn>
                </dd>
                <!-- BEGIN: last_post -->
				<dd class="lastpost"><span> 
				<p class="lastposttitle"><a href="{SUB.last_link}#post{SUB.last_post_id}" class="threadtitle" title="{SUB.last_thread_title}">{SUB.last_thread_title1}</a></p>
				<dfn>{LANG.lastpost}</dfn> {LANG.by} <a href="#" style="color: #AA0000;" class="username-coloured" title="{SUB.last_post_username}">{SUB.last_post_username}</a> 
				<a href="{SUB.last_link}#post{SUB.last_post_id}"><img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/icon_topic_latest.png" width="12" height="10" alt="{LANG.viewlastpost}" title="{LANG.viewlastpost}" /></a> <br />
                {SUB.last_post_date}</span> 
                </dd>
				<!-- END: last_post -->
            </dl>
        </li>
		<!-- END: subcat -->
        
    </ul>
</div>
<!-- END: item --> 


<!-- END: main -->