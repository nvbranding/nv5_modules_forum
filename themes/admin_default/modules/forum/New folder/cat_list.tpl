<!-- BEGIN: main -->
<!-- BEGIN: cat_title -->
<div style="background:#eee;padding:10px">{CAT_TITLE}</div>
<!-- END: cat_title -->
<!-- BEGIN: data -->
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<caption>{TABLE_CAPTION}</caption>
		<thead>
			<tr>
				<th class="text-center">{LANG.weight}</th>
				<th> {LANG.cat_name}</th>
				<th>{LANG.cat_parent}</th>
				<th class="text-center">{LANG.viewcat_page} </th>
	            <th class="text-center">{LANG.img} </th>
	            <th class="text-center">{LANG.inhome}</th>
	            <th class="text-center">{LANG.cat_active} </th>
				<th class="text-center">{LANG.fun} </th>
			</tr>
		</thead>
		<tbody>
		<!-- BEGIN: loop -->
			<tr>
				<td class="text-center" style="width:15px">
					<!-- BEGIN: stt -->{STT}<!-- END: stt -->
					<!-- BEGIN: weight -->
					<select class="form-control" id="id_weight_{ROW.catid}" onchange="nv_change_weight('{ROW.catid}');">
						<!-- BEGIN: loop -->
						<option value="{WEIGHT.key}"{WEIGHT.selected}>{WEIGHT.title}</option>
						<!-- END: loop -->
					</select>
					<!-- END: weight -->
				</td>
				<td nowrap="nowrap"><a href="{ROW.link}"><strong>{ROW.title}</strong> <!-- BEGIN: numsubcat --><span style="color:#FF0101;">({NUMSUBCAT})</span><!-- END: numsubcat --></a></td>
				<td>
					{parent}
				</td>
				<td class="text-center">
					<!-- BEGIN: viewcat -->
					<select class="form-control" id="id_viewcat_{ROW.catid}" onchange="nv_chang_cat('{ROW.catid}','viewcat');">
						<!-- BEGIN: loop -->
						<option value="{VIEWCAT.key}"{VIEWCAT.selected}>{VIEWCAT.title}</option>
						<!-- END: loop -->
					</select>
					<!-- END: viewcat -->
				</td>
	
				<td class="text-center">
					<!-- BEGIN: img -->
						<img src="{ROW.thumbnail}" width="36"/>
					<!-- END: img -->
				</td>
	            <td class="text-center">
					<!-- BEGIN: disabled_inhome -->{INHOME}<!-- END: disabled_inhome -->
					<!-- BEGIN: inhome -->
					<input type="checkbox" name="inhome" id="change_inhome{ROW.catid}" value="1" {INHOME} onclick="nv_chang_inhome({ROW.catid});" />
					<!-- END: inhome -->
				</td>
				<td class="text-center">
					
					<!-- BEGIN: disabled_status -->
					{STATUS}
					<!-- END: disabled_status -->
					<!-- BEGIN: status -->
					<input type="checkbox" name="active" id="change_status{ROW.catid}" value="1"{STATUS} onclick="nv_chang_cat_status({ROW.catid});" />
					<!-- END: status -->
				</td>
				<td nowrap="nowrap" class="text-center">{ROW.adminfuncs}</td>
			</tr>
		<!-- END: loop -->
		<tbody>
	</table>
</div>
<!-- END: data -->
<!-- END: main -->