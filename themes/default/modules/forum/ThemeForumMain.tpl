<!-- BEGIN: main -->
<div class="sectionforum">
	<!-- BEGIN: node -->
	<div class="panel-forum" id="{NODE.alias}-{NODE.node_id}">
		<div class="panel-heading-forum"> 
			<a href="#{NODE.alias}-{NODE.node_id}">{NODE.title}</a>		 
		</div>
 
		<div class="pody-forum">
			<!-- BEGIN: subnode -->
			<div class="forum-tms">
			<div class="forum-left">
			<div class="forum-icon" title="Unread messages"><i class="fa fa-gg fa-2x" aria-hidden="true"></i></div>
			
			<div class="forum-title">
			<div class="forum-title-a"> <h3><a href="{SUBNODE.link}" data-description="#nodeDescription-{SUBNODE.node_id}">{SUBNODE.title}</a></h3></div>
			<div class="thongke-forum">Chủ đề: {DATA.discussion_count} - Bài viết: {DATA.message_count}</div>
			</div>
			
			</div>
			
			<div class="forum-right">
				<div class="lastpost">
				<!-- BEGIN: latest_post -->
					<span class="forum-an">
					<a href="{DATA.last_post_link}"title="{DATA.last_thread_title}">{DATA.last_thread_title}</a>
					<br>
					
					gửi bởi <a href="#">{DATA.last_post_username} </a>
					</span>
					
					<span class="forum-post-time">{DATA.last_post_date}</span>	
					<!-- END: latest_post -->
					<!-- BEGIN: no_latest_post -->
					Chưa có bài viết
					<!-- END: no_latest_post -->
					</div>
					
			</div>
			
			</div> 
			<!-- END: subnode -->
			
			</div>
	</div>
	
	<!-- END: node --> 
</div>
<!-- END: main -->