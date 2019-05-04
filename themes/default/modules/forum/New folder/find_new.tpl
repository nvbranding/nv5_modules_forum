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


<!-- END: main -->