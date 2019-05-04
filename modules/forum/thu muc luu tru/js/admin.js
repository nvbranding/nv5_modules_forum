function get_alias(mod,id) {
	var title = strip_tags(document.getElementById('idtitle').value);
	if (title != '') {
		nv_ajax('post', script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '='+mod+'&title=' + encodeURIComponent(title)+'&id='+id+'&action=getalias', '', 'res_get_alias');
	}
	return false;
}

function res_get_alias(res) {
	if (res != "") {
		document.getElementById('idalias').value = res;
	} else {
		document.getElementById('idalias').value = '';
	}
	return false;
}

function nv_change_weight(catid) {
	var nv_timer = nv_settimeout_disable('id_weight_' + catid, 3000);
	var new_vid = document.getElementById('id_weight_' + catid).options[document.getElementById('id_weight_' + catid).selectedIndex].value;
	nv_ajax("post", script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&action=changeweight&catid=' + catid + '&new_vid=' + new_vid + '&num=' + nv_randomPassword(8), '', 'nv_change_weight_result');
	return;
}

function nv_change_weight_result(res) {
	var r_split = res.split("_");
	if (r_split[0] != 'OK') {
		alert(nv_is_change_act_confirm[2]);
	}
	clearTimeout(nv_timer);
	var parentid = parseInt(r_split[1]);
	nv_show_list_cat(parentid);
	return;
}

function nv_chang_cat(catid, mod) {
	var nv_timer = nv_settimeout_disable('id_' + mod + '_' + catid, 5000);
	var new_vid = document.getElementById('id_' + mod + '_' + catid).options[document.getElementById('id_' + mod + '_' + catid).selectedIndex].value;
	nv_ajax("post", script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&action=viewcat&catid=' + catid + '&mod=' + mod + '&new_vid=' + new_vid + '&num=' + nv_randomPassword(8), '', 'nv_chang_cat_result');
	return;
}

// ---------------------------------------

function nv_chang_cat_result(res) {
	var r_split = res.split("_");
	if (r_split[0] != 'OK') {
		alert(nv_is_change_act_confirm[2]);
	}
	clearTimeout(nv_timer);
	var parentid = parseInt(r_split[1]);
	nv_show_list_cat(parentid);
	return;
}

function nv_show_list_cat(parentid) {
	if (document.getElementById('module_show_list')) {
		nv_ajax("get", script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&action=listcat&parentid=' + parentid + '&num=' + nv_randomPassword(8), 'module_show_list');
	}
	return;
}

function nv_chang_inhome( catid )
{
   var nv_timer = nv_settimeout_disable( 'change_inhome' + catid, 2000 );
   nv_ajax( "post", script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&action=changeinhome&catid=' + catid + '&num=' + nv_randomPassword( 8 ), '', 'nv_chang_cat_status_res' );
   return;
}

function nv_chang_cat_status( catid )
{
   var nv_timer = nv_settimeout_disable( 'change_status' + catid, 2000 );
   nv_ajax( "post", script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&action=changestatus&catid=' + catid + '&num=' + nv_randomPassword( 8 ), '', 'nv_chang_cat_status_res' );
   return;
}

function nv_chang_cat_status_res( res )
{
   if( res != 'OK' )
   {
      alert( nv_is_change_act_confirm[2] );
      window.location.href = window.location.href;
   }
   return;
}

function nv_del_cat(catid) {
	nv_ajax('post', script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&action=del&catid=' + catid, '', 'nv_del_cat_result');
	return false;
}


function nv_del_cat_result(res) {
	var r_split = res.split("_");
	if (r_split[0] == 'OK') {
		var parentid = parseInt(r_split[1]);
		nv_show_list_cat(parentid);
	} else if (r_split[0] == 'CONFIRM') {
		if (confirm(nv_is_del_confirm[0])) {
			var catid = r_split[1];
			var delallcheckss = r_split[2];
			nv_ajax('post', script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&action=del&catid=' + catid + '&delallcheckss=' + delallcheckss, '', 'nv_del_cat_result');
		}
	} else if (r_split[0] == 'ERR' && r_split[1] == 'CAT') {
		alert(r_split[2]);
	} else if (r_split[0] == 'ERR' && r_split[1] == 'ROWS') {
		if (confirm(r_split[4])) {
			var catid = r_split[2];
			var delallcheckss = r_split[3];
			nv_ajax('post', script_name, nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&action=del&catid=' + catid + '&delallcheckss=' + delallcheckss, 'edit', '');
			parent.location='#edit';			
		}
	} else {
		alert(nv_is_del_confirm[2]);
	}
	return false;
}


function nv_check_movecat(oForm, msgnocheck) {
	var fa = oForm['catidnews'];
	if (fa.value == 0) {
		alert(msgnocheck);
		return false;
	}
}

function forum_del_thread( thread_id, catid, checkmod )
{
	if ( confirm( nv_is_del_confirm[0] ) )
	{
		nv_ajax( 'post', script_name, nv_name_variable + '=' + nv_module_name + '&'+ nv_fc_variable + '=main&action=delete_thread&catid='+ catid +'&thread_id=' + thread_id +'&checkmod=' + checkmod, '', 'forum_del_thread_result' );
	}
	return false;
}

function forum_del_thread_result( res )
{
	var obj = $.parseJSON(res);
	var message = obj.data.message;
	var items = obj.data.item;

	if (message == 'success')
	{
		alert( items['message'] );
		location.reload();

	} else if ( message == 'unsuccess') {
		var a = "";
		$.each(items, function (i, item) {
			a += '' + item + '<br />';
		});
		$('#posting_msg').html('<div class="success" style="display: none;"><strong>' + a + ' </strong><span class="close" onclick="close();"><img src="' + nv_siteroot + 'themes/forum/images/close.png" alt="" class="close" /></span></div>').show();
		$('.success').fadeIn('slow');
		$(".close").click(function () {
			$('#posting_msg').hide();
		});
	}
   return false;
}