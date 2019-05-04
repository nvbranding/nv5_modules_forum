<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery/jquery.treeview.css" type="text/css"/>
<script src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery/jquery.cookie.js" type="text/javascript"></script>
<script src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery/jquery.treeview.min.js" type="text/javascript"></script>

<div class="row clearfix">
	<!-- BEGIN: post -->
	
			<!-- BEGIN: close_month -->
			</ul><!-- close-month-{show_month} -->
			</li>			
			<!-- END: close_month -->

			<!-- BEGIN: close_year -->
			</ul>
			<!-- END: close_year -->
			
		<!-- BEGIN: year -->
		<h3><strong>{show_year}</strong></h3>
		<ul id="showyear{show_year}" class="year-{show_year}">
		<script type="text/javascript">
			$(document).ready(function() {
				$("#showyear{show_year}").treeview({
					collapsed : true,
					unique : true,
					persist : "location"
				});
			});
		</script>
		<style type="text/css">
			#showyear{show_year} a {
				background-color: transparent !important
			}

			#showyear .current, #showyear .current a {
				font-weight: bold
			}

			#showyear .current ul a {
				font-weight: normal
			}
		</style>
		<!-- END: year -->	
		
			<!-- BEGIN: month -->
			<li>
			<strong>Tháng {show_month}</strong>
			<ul class="show-month-{show_month}"><!-- show-month-{show_month} -->			
			<!-- END: month -->
			
				<li><a title="{blocknews.title}" href="{blocknews.link}">{blocknews.title}</a></li>
	
	<!-- END: post -->
	</ul>
	
</div>
<!-- END: main -->