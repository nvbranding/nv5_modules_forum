<!-- BEGIN: main -->
<!-- BEGIN: thread_sticky -->
<div class="sectionforum">
	<div class="panel-forum">
			
		<div class="panel-heading-forum">
			<div class="node-forum-tieude">Chủ đề</div>
			<div class="node-forum-thongke">Thống kê</div>	
			<div class="node-forum-thaoluan">Thảo luận mới</div>	
		</div>
		
		
		<div class="pody-forum">
		<!-- BEGIN: loop -->
		<div class="node-forum-tms">
			<div class="node-forum-noidung-tieude">
				<div class="node-forum-noidung-icon" title="Unread messages">	<a href="{THREAD.username_link}" class="avatar Av2s" data-avatarhtml="true"><img src="{THREAD.photo}" width="48" height="48" alt="{THREAD.username}"></a></div>
				
				<a href="{THREAD.link}">{THREAD.title}</a>
			<br/>
					<a href="#" class="username" dir="auto" title="Thread starter">{THREAD.username}</a><span class="startDate">,
					<a class="faint"><span  title="">{THREAD.post_date}</span></a></span>

					
				
			
			</div>
			
			<div class="node-forum-noidung-thongke">Trả lời: {THREAD.reply_count} <br> Lượt xem: {THREAD.view_count}</div>	
			<div class="node-forum-noidung-thaoluan"><a href="#" class="username">{THREAD.last_post_username}</a>
					<a href="{THREAD.last_post_link}" class="lastlink"><i class="fa fa-arrow-right"></i></a>
					<span class="clearfix">{THREAD.last_post_date}</span> </div>	
			
		</div> 
			<!-- END: loop --> 	
			
		</div>
	</div>
</div>

<!-- END: thread_sticky -->
<!-- BEGIN: generate_page_top -->
<div class="generate_page" align="right">{GENERATE_PAGE}</div>
<div class="clearfix"></div>
<!-- END: generate_page_top -->

<!-- BEGIN: thread_normal -->
<div class="sectionforum">
	<div class="panel-forum">
			
		<div class="panel-heading-forum">
			<div class="node-forum-tieude">Chủ đề</div>
			<div class="node-forum-thongke">Thống kê</div>	
			<div class="node-forum-thaoluan">Thảo luận mới</div>	
		</div>
		
		
		<div class="pody-forum">
		<!-- BEGIN: loop -->
		<div class="node-forum-tms">
			<div class="node-forum-noidung-tieude">
				<div class="node-forum-noidung-icon" title="Unread messages">	<a href="{THREAD.username_link}" class="avatar Av2s" data-avatarhtml="true"><img src="{THREAD.photo}" width="48" height="48" alt="{THREAD.username}"></a></div>
				
				<a href="{THREAD.link}">{THREAD.title}</a>
			<br/>
					<a href="#" class="username" dir="auto" title="Thread starter">{THREAD.username}</a><span class="startDate">,
					<a class="faint"><span  title="">{THREAD.post_date}</span></a></span>

					
				
			
			</div>
			
			<div class="node-forum-noidung-thongke">Trả lời: {THREAD.reply_count} <br> Lượt xem: {THREAD.view_count}</div>	
			<div class="node-forum-noidung-thaoluan"><a href="#" class="username">{THREAD.last_post_username}</a>
					<a href="{THREAD.last_post_link}" class="lastlink"><i class="fa fa-arrow-right"></i></a>
					<span class="clearfix">{THREAD.last_post_date}</span> </div>	
			
		</div> 
			<!-- END: loop --> 	
			
		</div>
	</div>
</div>
<!-- END: thread_normal -->

<!-- BEGIN: generate_page_bottom -->
<div class="clearfix"></div>
<div class="generate_page" align="right">{GENERATE_PAGE}</div>
<!-- END: generate_page_bottom -->

<!-- END: main -->