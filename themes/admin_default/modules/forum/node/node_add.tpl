<!-- BEGIN: main -->
<div id="forum-content">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title" style="float:left"><i class="fa fa-list"></i> {LANG.node_add}</h3> 
			 
			<div style="clear:both"></div>
		</div>
		<div class="panel-body">
			<form action="{ACTION}" method="post" class="formAdd formOverlay">
				<dl class="ctrlUnit">
					<dt>Loại mục: <dfn>Loại mục bạn muốn thêm?</dfn></dt>
					<dd>
						<ul>
							<!-- BEGIN: loop_node -->
							<li><label for="node_type_id_{NODE.key}"><input type="radio" name="node_type_id" value="{NODE.key}" id="node_type_id_{NODE.key}" {NODE.checked}> {NODE.name}</label></li>
							<!-- END: loop_node -->
						</ul>				
					</dd>
				</dl>
	
				
				<dl class="ctrlUnit submitUnit">
					<dt> </dt>
					<dd>
						<input type="hidden" name="parent_id" value="{DATA.parent_id}">
						<input type="hidden" name="token" value="{TOKEN}">
						<input type="submit" value="Tiến hành..." class="btn btn-primary btn-sm" accesskey="s">
					</dd>
				</dl>
			</form>
		</div>
	</div>
</div>
<!-- END: main -->

