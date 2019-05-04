<!-- BEGIN: main -->

<div id="blocktopx" class="clrearfix topx">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#newposts" data-cache="0" aria-controls="newposts" role="tab" data-toggle="tab">Bài viết mới</a></a></li>
		<li role="presentation"><a href="#newthreads" data-cache="0" aria-controls="newthreads" role="tab" data-toggle="tab">Chủ đề mới</a></a></li>
		<li role="presentation"><a href="#mostviewed" data-cache="0" aria-controls="mostviewed" role="tab" data-toggle="tab">Xem nhiều</a></a></li>
		<li role="presentation"><a href="#mostpost" data-cache="0" aria-controls="mostpost" role="tab" data-toggle="tab">Nhộn nhịp</a></a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane fade in active" id="newposts">
			<!-- BEGIN: lastest_thread -->
			<ul class="list-topx">
                <!-- BEGIN: loop -->
				<li class="list-item"><div class="itemContent"><div class="topictitle"><a href="{LOOP.last_post_link}">{LOOP.title}</a></div><div class="username"><a href="#{LOOP.last_post_username}">{LOOP.last_post_username}</a></div></div></li>
				<!-- END: loop -->
			</ul>
			<!-- END: lastest_thread -->
		</div>
		<div role="tabpanel" class="tab-pane fade" id="newthreads">
			 
		</div>
		<div role="tabpanel" class="tab-pane fade" id="mostviewed">
			 
		</div>
		<div role="tabpanel" class="tab-pane fade" id="mostpost">
			 
		</div>
	</div>
	<div class="clearfix"></div>
</div>
<script type="text/javascript">
$('#blocktopx>ul>li>a').on('click', function(){
	var obj = $(this);
	var action = obj.attr('aria-controls');
	var check_cache = obj.attr('data-cache');
	if( action != 'newposts' && check_cache == 0 )
	{
		$.ajax({
			url: nv_base_siteurl + '?' + nv_lang_variable + '='+ nv_lang_data +'&'+nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=mod&nocache=' + new Date().getTime(),
			type: 'post',
			dataType: 'json',
			data: 'action=' + action + '&token={TOKEN}',
			success: function(json) {
				if( json['data'] )
				{
					var temp='';
					temp+='<ul class="list-topx">';
					$.each(json['data'], function(i, item) {
						temp+='<li class="list-item"><div class="itemContent"><div class="topictitle"><a href="'+ item['link'] +'">'+ item['title'] +'</a></div><div class="username"><a href="#'+ item['last_post_user_id'] +'">'+ item['last_post_username'] +'</a></div></div></li>';
					});
					temp+='</ul>';
					$('#'+action).html(temp);
					obj.attr('data-cache', 1);			
				}
			}
		});
	
	}
 
});
</script>
<!-- END: main -->