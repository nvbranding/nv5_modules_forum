<!-- BEGIN: main -->
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<tbody>
			<tr>
				<td>
					<form class="form-inline" action="{NV_BASE_ADMINURL}index.php" method="post">
						<input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
						<input type="hidden" name="{NV_OP_VARIABLE}" value="{OP}" />
						<input type="hidden" name="catid" value="{CATID}" />
						<input type="hidden" name="delallcheckss" value="{DELALLCHECKSS}" />
						<center>
							<b>{TITLE}</b>
							<br /><br />
							<input name="action" type="hidden" value="del" />
							<input class="btn btn-primary" name="delcatandrows" type="submit" value="{LANG.delcatandrows}" />
							<br /><br />
							<b>{LANG.delcat_msg_rows_move}</b>: 
							<select class="form-control" name="catidnews">
								<!-- BEGIN: catidnews -->
								<option value="{CATIDNEWS.catid}" {CATIDNEWS.disabled}>{CATIDNEWS.title}</option>
								<!-- END: catidnews -->
							</select>
							<input class="btn btn-primary" name="movecat" type="submit" value="{LANG.action}" onclick="return nv_check_movecat(this.form, '{LANG.delcat_msg_rows_noselect}')"/>
						</center>
					</form>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<!-- END: main -->