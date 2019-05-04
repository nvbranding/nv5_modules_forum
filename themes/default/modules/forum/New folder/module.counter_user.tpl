<!-- BEGIN: main -->
<div class="online-viewing statsHome">
  <div class="secondaryContent totalOnline"> 
  <span><img src="{NV_BASE_SITEURL}themes/{TEMPLATE}/images/users_online.gif"></span> 
  Có 
  <span class="totalOnlineNumber">{COUNT_ONLINE}</span> người đang online, trong đó có 
  <span class="totalOnlineNumber">{COUNT_USERS}</span> thành viên và <strong>{COUNT_GUESTS}</strong> khách
  <!-- BEGIN: bots -->, {COUNT_BOTS} máy tìm kiếm <!-- END: bots -->
  <span class="dateTime">{current_time}</span> </div>
  
	<!-- BEGIN: thread -->
	
	<div id="boardStats" class="secondaryContent statsList">
		<ol class="listInline">
		  <li class="first"> Có <font color="green" size=3><b>{ONLINE}</b></font> người đang xem chủ đề này<span class="footnote"> (Thành viên: <span class="blackColor">{USERS}</span>, Khách:<span class="blackColor"> {GUEST}</span>)</span>: </li>
		 
		  <!-- BEGIN: user_loop --><li> <a href="{USER.user_page}" class="username">{USER.full_name}</a> </li><!-- END: user_loop -->
		  <li>... <a rel='building' href="#">Xem thêm</a></li>
		</ol>
	</div> 
	<!-- END: thread -->
	<!-- BEGIN: cat -->
	
	<div id="boardStats" class="secondaryContent statsList">
		<ol class="listInline">
		  <li class="first"> Có <font color="green" size=3><b>{ONLINE}</b></font> người đang xem box này<span class="footnote"> (Thành viên: <span class="blackColor">{USERS}</span>, Khách:<span class="blackColor"> {GUEST}</span>)</span>: </li>
		 
		  <!-- BEGIN: user_loop --><li> <a href="{USER.user_page}" class="username">{USER.full_name}</a> </li><!-- END: user_loop -->
		  <li>... <a rel='building' href="#">Xem thêm</a></li>
		</ol>
	</div> 
	<!-- END: cat -->
	
	
</div>
<!-- END: main -->