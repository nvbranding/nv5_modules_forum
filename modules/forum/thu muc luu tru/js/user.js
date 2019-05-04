$(document).ready(function(){
	$("a[rel='building']").on('click', function(e) {
		e.preventDefault();
		alert('Chức năng này đang được xây dựng');
		return false;
	});
	
	
});

function open_comment ( profile_post_id )
{
	$('#commentSubmit-'+ profile_post_id ).show();
	$('#commentSubmit-'+ profile_post_id+' textarea' ).focus();
	
}

function RecentActivity( )
{	

	var checkss = $('input[name="checkss"]').val();
	var profile_user_id = $('input[name="profile_user_id"]').val();
	$.ajax({
		url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=members&nocache=' + new Date().getTime(),
		type: 'POST',
		data: 'action=RecentActivity&profile_user_id=' + profile_user_id + '&checkss=' + checkss,
		dataType: "json",
		async: false,
		success: function (res) {
			var message = res.data.message;
			var items = res.data.item;
			if (message == 'success') {
				$('#likes-wp-'+ profile_post_id).after( $(items['message']).fadeIn('slow') );
				$('#commentMore'+ profile_post_id).remove();
			}else if (message == 'unsuccess') {  
				var a = '';
				$.each(items, function (i, item) {
					 a += '' + item + '\n';
				});
				alert(a);
			}
		 }	
	});
	
	return false;
}


function loadPreComment( profile_post_id, PreTime )
{	

	var checkss = $('input[name="checkss"]').val();
	var profile_user_id = $('input[name="profile_user_id"]').val();
	$.ajax({
		url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=members&nocache=' + new Date().getTime(),
		type: 'POST',
		data: 'action=loadPreComment&profile_post_id=' + profile_post_id + '&profile_user_id=' + profile_user_id + '&PreTime=' + PreTime + '&checkss=' + checkss,
		dataType: "json",
		async: false,
		success: function (res) {
			var message = res.data.message;
			var items = res.data.item;
			if (message == 'success') {
				$('#likes-wp-'+ profile_post_id).after( $(items['message']).fadeIn('slow') );
				$('#commentMore'+ profile_post_id).remove();
			}else if (message == 'unsuccess') {  
				var a = '';
				$.each(items, function (i, item) {
					 a += '' + item + '\n';
				});
				alert(a);
			}
		 }	
	});
	
	return false;
}

function DeleteComment( profile_post_id, profile_post_comment_id )
{	
	if (confirm(nv_is_del_confirm[0])) 
	{
		var checkss = $('input[name="checkss"]').val();
		var profile_user_id = $('input[name="profile_user_id"]').val();
		$.ajax({
			url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=members&nocache=' + new Date().getTime(),
			type: 'POST',
			beforeSend: function(){
				$("#loading_comment"+profile_post_id).append('<img src="'+nv_siteroot+'images/load_bar.gif" />');
			},
			data: 'action=DeleteComment&profile_user_id=' + profile_user_id + '&profile_post_id=' + profile_post_id + '&profile_post_comment_id=' + profile_post_comment_id + '&checkss=' + checkss,
			dataType: "json",
			async: false,
			success: function (res) {
				
				//$("#loading_comment"+profile_post_id).empty();
				var message = res.data.message;
				var items = res.data.item;
				if (message == 'success') {
					location.reload();
				}else if (message == 'unsuccess') {  
					var a = '';
					$.each(items, function (i, item) {
						 a += '' + item + '\n';
					});
					alert(a);
				}
			 }	
		});
	}
	return false;
}

function UpdateProfilePost( profile_post_id )
{	
	$("#UpdateProfilePost").submit( function() {
		return false;   
	});
	var checkss = $('input[name="checkss"]').val();
	var profile_user_id = $('input[name="profile_user_id"]').val();
	var message = $('#ctrl_message').val();
	if( message.length < 5 )
	{	
		alert('Nội dung tin quá ngắn');
		$('#ctrl_message').focus();
	}else{
		$.ajax({
			url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=members&nocache=' + new Date().getTime(),
			type: 'POST',
			data: 'action=UpdateProfilePost&profile_user_id=' + profile_user_id + '&profile_post_id=' + profile_post_id + '&checkss=' + checkss+ '&message=' + encodeURIComponent(message),
			dataType: "json",
			async: false,
			success: function (res) {
				var message = res.data.message;
				var items = res.data.item;
				if (message == 'success') {
					window.location.hash = "#profile-post-"+profile_post_id+"";
					location.reload();
				}else if (message == 'unsuccess') {  
					var a = '';
					$.each(items, function (i, item) {
						 a += '' + item + '\n';
					});
					alert(a);
				}
			 }	
		});
	}
	return false;
}

function EditProfilePost( profile_post_id )
{	
	var checkss = $('input[name="checkss"]').val();
	var profile_user_id = $('input[name="profile_user_id"]').val();
	$.ajax({
		url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=members&nocache=' + new Date().getTime(),
		type: 'POST',
		data: 'action=EditProfilePost&profile_user_id=' + profile_user_id + '&profile_post_id=' + profile_post_id + '&checkss=' + checkss,
		dataType: "json",
		async: false,
		success: function (res) {
			var message = res.data.message;
			var items = res.data.item;
			if (message == 'success') {
				
				$.colorbox({html:items['message'],top:'10%',width:'40%', speed:0});

			}else if (message == 'unsuccess') {  
				var a = '';
				$.each(items, function (i, item) {
					 a += '' + item + '\n';
				});
				alert(a);
			}
		 }	
	});
	return false;
}

function DeleteProfilePost( profile_post_id )
{	
	if (confirm(nv_is_del_confirm[0])) 
	{
		var hard_delete = 1; // 0 neu muon xoa o dang an di co the hien lai
		var checkss = $('input[name="checkss"]').val();
		var profile_user_id = $('input[name="profile_user_id"]').val();
		$.ajax({
			url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=members&nocache=' + new Date().getTime(),
			type: 'POST',
			data: 'action=DeleteProfilePost&profile_user_id=' + profile_user_id + '&profile_post_id=' + profile_post_id + '&hard_delete=' + hard_delete + '&checkss=' + checkss,
			dataType: "json",
			async: false,
			success: function (res) {
				var message = res.data.message;
				var items = res.data.item;
				if (message == 'success') {
					
					location.reload();
				}else if (message == 'unsuccess') {  
					var a = '';
					$.each(items, function (i, item) {
						 a += '' + item + '\n';
					});
					alert(a);
				}
			 }	
		});
	}
	return false;
}

function ProfileComment( profile_user_id, profile_post_id )
{	
	$("#ProfileComment").submit( function() {
            return false;   
	});
	
	var checkss = $('input[name="checkss"]').val();
	var message = $('#message'+ profile_post_id).val();
	if( message.length < 5 )
	{	
		alert('Nội dung tin nhắn quá ngắn');
		$('#message'+ profile_post_id).focus();
	}else{
		
		$.ajax({
			url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=members&nocache=' + new Date().getTime(),
			type: 'POST',
			beforeSend: function(){
				$("#loading_comment"+profile_post_id).append('<img src="'+nv_siteroot+'images/load_bar.gif" />');
			},
			data: 'action=insert_comment&profile_user_id=' + profile_user_id + '&profile_post_id=' + profile_post_id + '&checkss=' + checkss+ '&message=' + encodeURIComponent( message ),
			dataType: "json",
			async: false,
			success: function (res) {
			
				$("#loading_comment"+profile_post_id).empty();
				var message = res.data.message;
				var items = res.data.item;
				if (message == 'success') {
					$('#message'+ profile_post_id).val('');
					$('#commentSubmit-'+ profile_post_id).before( $(items['message']).fadeIn('slow') );
				}else if (message == 'unsuccess') {  
					var a = '';
					$.each(items, function (i, item) {
						 a += '' + item + '\n';
					});
					alert(a);
				}
		    }	
		});
	}
	return false;
}

function ProfilePoster( profile_user_id, checkss )
{	
	var message = $('#message').val();
	if( message.length < 5 )
	{	
		alert('Nội dung tin nhắn quá ngắn');
		$('#message').focus();
	}else{
		
		$.ajax({
			url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=members&nocache=' + new Date().getTime(),
			type: 'POST',
			beforeSend: function(){
				$("#loading_comment").append('<img src="'+nv_siteroot+'images/load_bar.gif" />');
			},
			data: 'action=insert_profile&profile_user_id=' + profile_user_id + '&checkss=' + checkss+ '&message=' + encodeURIComponent(message),
			dataType: "json",
			async: false,
			success: function (res) {
			
				$("#loading_comment").empty();
				var message = res.data.message;
				var items = res.data.item;
				if (message == 'success') {
					$('#message').val('');
					$('#ProfilePostList').prepend( $(items['message']).fadeIn('slow') );
				}else if (message == 'unsuccess') {  
					var a = '';
					$.each(items, function (i, item) {
						 a += '' + item + '\n';
					});
					alert(a);
				}
		   }	
		});
	}
	return false;
}

function submit_ok()
{
	var userid = $('#avatar_userid').val();
	var checkss = $('#avatar_checkss').val();
	var fdelete = $("input[name='delete']").is(':checked') ? 1 : 0;
	$.ajax({
        url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=account&nocache=' + new Date().getTime(),
        type: 'POST',
        data: 'action=update_avatar&fdelete=' + fdelete + '&userid=' + userid + '&checkss=' + checkss,
		dataType: "json",
		async: false,
        success: function (res) {
            var message = res.data.message;
            var items = res.data.item;
			
			if(items['message'] == 'delete')
			{
				$('.Av'+userid+' img').attr('src', items['large_avatar']+'?'+new Date().getTime());
				$('.Av'+userid+'m span').css('background','url('+items['large_avatar']+'?'+new Date().getTime()+') center' );
			}
			jQuery.colorbox.close(); 
        }
    });
	return false;
}

function get_avatar_image()
{
	var formData = new FormData($('#my_avatars')[0]);
    $.ajax({
        url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=account&nocache=' + new Date().getTime(),
        type: 'POST',
		mimeType:"multipart/form-data",
		beforeSend: function(){
			$("#images_loading").append('<img src="'+nv_siteroot+'images/load_bar.gif" />');
        },
        data: formData,
        async: false,
		dataType: "json",
        success: function (res) {
			$("#images_loading").empty(); 
            var message = res.data.message;
            var items = res.data.item;
			
            if (message == 'success') {
				$('.Av'+items['userid']+' img').attr('src', items['large_avatar']+'?'+new Date().getTime());
				$('.Av'+items['userid']+'m img').attr('src', items['medium_avatar']+'?'+new Date().getTime());
				$('.Av'+items['userid']+'m span').css('background','url('+items['large_avatar']+'?'+new Date().getTime()+') center' );
			
		   }else if (message == 'unsuccess') {  
                var a = "";
                $.each(items, function (i, item) {
                     a += '' + item + '\n';
                });
				alert(a);
			}
        },
        cache: false,
        contentType: false,
        processData: false
    });
}
function del_message(catid, thread_id, post_id, checkss) {
    $('#del_post_' + post_id).hide();
    if (confirm(nv_is_del_confirm[0])) {
        $.ajax({
            type: "POST",
            url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=view&nocache=' + new Date().getTime(),
            data: 'action=del_message&catid=' + catid + '&thread_id=' + thread_id + '&post_id=' + post_id + '&checkss=' + checkss,
            dataType: "json",
			success: function (res) {
                var message = res.data.message;
                var items = res.data.item;

                if (message == 'success') {
                    $('#after_content_' + post_id).html('<div class="success" style="display: none;"><strong>' + items['message'] + ' </strong><span class="close" onclick="close();"><img src="' + nv_siteroot + 'themes/'+template+'/images/close.png" alt="" class="close" /></span></div>').show();
                    $('.success').fadeIn('slow');
                    $(".close").click(function () {
                        qr_submit.disabled = false;
                        $('#after_content_' + post_id).hide();

                        window.location.href = '' + items['link'] + '';
						location.reload();

                    });
                    setTimeout(function () {
                        $('#after_content_' + post_id).hide();
                        window.location.href = '' + items['link'] + '';
						location.reload();
                    }, 1000);
                } else if (message == 'unsuccess') {
                    $('#del_post_' + post_id).show();
                    var a = "";
                    $.each(items, function (i, item) {
                        a += '' + item + '\n';
                    });
                    $('#after_content_' + post_id).html('<div class="success" style="display: none;"><strong>' + a + ' </strong><span class="close" onclick="close();"><img src="' + nv_siteroot + 'themes/'+template+'/images/close.png" alt="" class="close" /></span></div>').show();
                    $('.success').fadeIn('slow');
                    $(".close").click(function () {
                        $('#after_content_' + post_id).hide();
                    });
                    setTimeout(function () {
                        $('#after_content_' + post_id).hide();
                    }, 1000);
                }
            }
        });
    } else {
        $('#del_post_' + post_id).show();
    }
}
function load_message( catid, post_id, checkss ) {
	$(".thread_content").each(function (index) {
        var postid = Number($(this).attr('rel'));
        if (postid > 0) {
            $('#thread_' + postid).html($('#thread_tem_' + postid).html());
            cancel_edit(postid);
        }
    });

    $('#edit_post_' + post_id).hide();
    
    var quote = $('#thread_' + post_id);
    $('#thread_tem_' + post_id).html(quote.html());

    $.ajax({
        type: "POST",
        url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=view&nocache=' + new Date().getTime(),
        data: 'action=load_message&catid=' + catid + '&post_id=' + post_id + '&checkss=' + checkss,
        success: function (res) {
            var obj = $.parseJSON(res);
            $('#thread_' + post_id).html(obj.quick_reply);
            $('#thread_' + post_id).attr('rel', post_id);
			$("html, body").animate({ scrollTop: $('#post-'+post_id).offset().top }, 'fast');
    
        }
    });

    return false;
}
function update_post(post_id) {
    var postsubmit = document.getElementById('submit');
    postsubmit.disabled = false;
    var data = $('#thread_edit').serialize();
    var messsage_post = $('#messsage_post_id').sceditor("instance").getWysiwygEditorValue(true);
    $.ajax({
        type: "POST",
        url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=view&nocache=' + new Date().getTime(),
        data: data + '&messsage_post=' + encodeURIComponent( messsage_post ),
        dataType: "json",
        async: false,
        success: function (res) {
            var message = res.data.message;
            var items = res.data.item;

            if (message == 'success') {
                cancel_edit(post_id);
                $('#after_content_' + post_id).html(items['time']);
                $('#thread_' + post_id).html(items['message']);
				$('#thread_tem_' + post_id).empty();

            }
            if (message == 'unsuccess') {
                var a = "";
                $.each(items, function (i, item) {
                    a += '' + item + '\n';
                });

                $('#posting_msg').html('<div class="success" style="display: none;"><strong>' + a + ' </strong><span class="close" onclick="close();"><img src="' + nv_siteroot + 'themes/'+template+'/images/close.png" alt="" class="close" /></span></div>').show();
                $('.success').fadeIn('slow');
                $(".close").click(function () {
                    postsubmit.disabled = false;
                    $('#posting_msg').hide();
                });
                setTimeout(function () {
                    $('#posting_msg').hide();
                }, 3000);

            }
        }
    });

    return false;
}

/* 
function loading() {
    var loader = $('#posting_msg');
    var postsubmit = document.getElementById('submit');
    $(document).ajaxStart(function () {
        postsubmit.disabled = true;
        loader.show();
    }).ajaxError(function (a, b, e) {
        throw e;
    });
}
 */
/* 
function process(data) {
    var postsubmit = document.getElementById('submit');
    var check = 0;
    var a = '';
    var b = '';
    $.each(data, function (i, item) {
        if (item.succe != '' && item.succe != null)
            a += item.succe + "\n";

        if (item.error != '' && item.error != null) {
            b += item.error + "\n";
            check = check + 1;
        }
    });
    $('#posting_msg').html('<div class="success" style="display: none;"><strong>' + a + '' + b + ' </strong><span class="close" onclick="close();"><img src="' + nv_siteroot + 'themes/'+template+'/images/close.png" alt="" class="close" /></span></div>');
    $('.success').fadeIn('slow');
    $(".close").click(function () {
        if (check == 0) {
            location.reload();
        }
        postsubmit.disabled = false;

    });
    if (check == 0) {
        setTimeout(function () {
            $('#posting_msg').hide();
            location.reload();
        }, 1000);
    }

} */


function cancel_edit(post_id) {
    $('#thread_' + post_id).html($('#thread_tem_' + post_id).html());
    $('#edit_post_' + post_id).show();
    $('#thread_' + post_id).attr('rel', '');
    cancel_all_edit();
}


function cancel_all_edit() {
    $(".thread_content").each(function (index) {
        var post_id = Number($(this).attr('rel'));
        if (post_id > 0) {
            $('#thread_' + post_id).html($('#thread_tem_' + post_id).html());
            cancel_edit(post_id);
        }
    });
}


function insert_quote(post_id, username) {
    var quote = $('#thread_' + post_id).text();
    var editor = $("#message").sceditor("instance");
    editor.insert('[quote=' + username + ']' + quote + '[/quote]');

    window.location.hash = '#' + $('#qr_' + post_id).attr('quick_reply');
}


function quickreply(post_id) {
    var q = $('#qr_' + post_id);
    window.location.hash = '#' + $(q).attr('quick_reply');
}


function insertimg(attachment_id, temp_width, w) {
    var editor = $("#message").sceditor("instance");
    if (w == 'full')
        var html = "<a href=\"" + $('#data-href' + attachment_id).attr('href') + "\">" +
            "<img width=\"" + temp_width + "\" src=\"" + $('#data-href' + attachment_id).attr('href') + "\" alt=\"" + $('#data-src' + attachment_id).attr('alt') + "\" />" +
            "</a>";
    else
        var html = "<a href=\"" + $('#data-href' + attachment_id).attr('href') + "\">" +
            "<img  width=\"" + temp_width + "\" src=\"" + $('#data-src' + attachment_id).attr('src') + "\" alt=\"" + $('#data-src' + attachment_id).attr('alt') + "\" />" +
            "</a>";

    editor.wysiwygEditorInsertHtml(html);
    editor.setSourceEditorValue(html);
}


function removeimg(attachment_id, data_id) {
    var editor = $("#message").sceditor("instance");
    var message = editor.getWysiwygEditorValue(true);
    message = message.split('[/url]');
    var length = message.length;
    var name = preg_quote($('#data-href' + attachment_id).attr('data'));
    var search = "/\\[url(.*)" + name + "(.*)url\\]/";
    var cde = "";
    for (var i = 0; i < length; i++) {
        if (i != length - 1) {
            message[i] = message[i] + '[/url]';
        }
        cde += preg_replace(search, '', message[i]);
    }
    editor.val(cde);
	
	var post_id = $('#post_id').val();
    var checkss = $('#attachment'+attachment_id).attr('rel');
    $.ajax({
        type: "POST",
        url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=post&nocache=' + new Date().getTime(),
        data: 'action=del_attachment&post_id=' + post_id + '&data_id=' + data_id + '&attachment_id=' + attachment_id + '&checkss=' + checkss,
        dataType: "json",
		success: function (res) {
            var message = res.data.message;
            var items = res.data.item;
			if (message == 'success') 
			{
				$('#attachment'+attachment_id).remove();
			}else if (message == 'unsuccess') 
			{
				var a = "";
				$.each(items, function (i, item) {
					a += '' + item + '\n';
				});
                alert(a);
            }
		}
    });

}



function quick_action(thread_id, catid, checkss_quickmod) {
    var url = $('#quickmod').attr('action');
    var action = $('#quick-mod-select').val();
    $.ajax({
        type: "POST",
        url: url,
        data: 'action=' + action + '&checkss_quickmod=' + checkss_quickmod + '&catid=' + catid + '&thread_id=' + thread_id + '&nocache=' + new Date().getTime(),
        dataType: "json",
		success: function (res) {
            var message = res.data.message;
            var items = res.data.item;

            if (message == 'success') {

                if (action == 'lock' || action == 'open') {
                    alert(items['message']);
                    location.reload();
                } else if (action == 'delete_thread') {
                    alert(items['message']);
                    window.location.href = items['link'];
					location.reload();
                } else if (action == 'move') {
                    $("#overlay").show().css({
                        "opacity": "0.5"
                    });
                    $('#quickmod_log').html(items['content']).show();

                    $(".close").click(function () {
                        $('#overlay').hide();
                        $('#quickmod_log').hide();
                    });
                    $('#overlay').mousedown(function () {
                        $('#overlay').hide();
                        $('#quickmod_log').hide();
                    })
                    $(document).keydown(function (e) {
                        if (e.keyCode == 27) {
                            $('#overlay').hide();
                            $('#quickmod_log').hide();
                        }
                    });
                }
            } else if (message == 'unsuccess') {
                alert(items['message']);
            }


        }
    });

    return false;
}



function preg_quote(str, delimiter) {
    return (str + '').replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\' + (delimiter || '') + '-]', 'g'), '\\$&');
}


function preg_replace(pattern, replace, subject, limit) {
    if (limit === undefined) {
        limit = -1;
    }

    var _flag = pattern.substr(pattern.lastIndexOf(pattern[0]) + 1),
        _pattern = pattern.substr(1, pattern.lastIndexOf(pattern[0]) - 1),
        reg = new RegExp(_pattern, _flag),
        rs = null,
        res = [],
        x = 0,
        y = 0,
        rtn = subject;

    var tmp = [];
    if (limit === -1) {
        do {
            tmp = reg.exec(subject);
            if (tmp !== null) {
                res.push(tmp);
            }
        } while (tmp !== null && _flag.indexOf('g') !== -1);
    } else {
        res.push(reg.exec(subject));
    }
    for (x = res.length - 1; x > -1; x--) {
        tmp = replace;

        for (y = res[x].length; y > -1; y--) {
            tmp = tmp.replace('${' + y + '}', res[x][y])
                .replace('$' + y, res[x][y])
                .replace('\\' + y, res[x][y]);
        }
        rtn = rtn.replace(res[x][0], tmp);
    }
    return rtn;
}

function like_post(post_id, user_id, username, checkss) {
    if (typeof (post_id) == "undefined") post_id = 0;
    if (typeof (user_id) == "undefined") user_id = 0;
    if (typeof (checkss) == "undefined") checkss = '';
	if (typeof (username) == "undefined") username = '';
	$.ajax({
        type: "POST",
        url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=view&nocache=' + new Date().getTime(),
		data: 'action=like_post&checkss=' + checkss + '&post_id=' + post_id + '&user_id=' + user_id + '&username=' + username + '&nocache=' + new Date().getTime(),  
		dataType: "json",
		success: function (res) {
			clearconsole();
            var message = res.data.message;
            var items = res.data.item;
            if (message == 'success') {
				if( items['like_exist'] == 0 && items['like_ok'] == 1)
				{
					$('#likes-post-'+post_id).html( items['content'] );
					$("#facebookconnect"+ post_id).addClass('hidden_elem');
					$("#facebookdisconnect"+ post_id).removeClass('hidden_elem');
					$('#post_thanks_box_'+post_id).remove();
					if(items['total'] > 0)
					{
						$('<li class="postbitlegacy postbitim" id="post_thanks_box_'+post_id+'">'+items['content']+'</li>').insertAfter('ol li#post'+post_id);
						$('#count_like_'+post_id).text(items['total']).show();
					}else
					{
						$('#count_like_'+post_id).empty().hide();
					}
				}else if( items['like_exist'] == 1 && items['like_ok'] == 1)
				{
					$('#likes-post-'+post_id).html( items['content'] );
					$('#facebookconnect'+ post_id).removeClass('hidden_elem');
					$('#facebookdisconnect'+ post_id).addClass('hidden_elem');
					$('#post_thanks_box_'+post_id).remove();
					
					if(items['total'] > 0)
					{
						$('<li class="postbitlegacy postbitim" id="post_thanks_box_'+post_id+'">'+items['content']+'</li>').insertAfter('ol li#post'+post_id);
						$('#count_like_'+post_id).text(items['total']).show();
					}else
					{
						$('#count_like_'+post_id).empty().hide();
						$('#likes-post-'+post_id).empty();
					}
				}else
				{
					alert(items['err']);
				}
 
            }else if (message == 'unsuccess') 
			{
				var a = "";
				$.each(items, function (i, item) {
					a += '' + item + '\n';
				});
                alert(a);
            }
        }
    });
	return;
}

function show_editor()
{
	$("html, body").animate({ scrollTop: $('#quick_reply').offset().top }, 'fast');
	
}


function jsonEscape(str)  {
    return str.replace(/\n/g, "\\\\n").replace(/\r/g, "\\\\r").replace(/\t/g, "\\\\t");
}

function clearconsole() { 
  console.log(window.console);
  if(window.console || window.console.firebug) {
   console.clear();
  }
}
/*
 * ******************************************************************************
 *  jquery.mb.components
 *  file: jquery.mb.browser.min.js
 *
 *  Copyright (c) 2001-2013. Matteo Bicocchi (Pupunzi);
 *  Open lab srl, Firenze - Italy
 *  email: matteo@open-lab.com
 *  site: 	http://pupunzi.com
 *  blog:	http://pupunzi.open-lab.com
 * 	http://open-lab.com
 *
 *  Licences: MIT, GPL
 *  http://www.opensource.org/licenses/mit-license.php
 *  http://www.gnu.org/licenses/gpl.html
 *
 *  last modified: 17/01/13 0.12
 *  *****************************************************************************
 */
(function(){if(!jQuery.browser){jQuery.browser={};jQuery.browser.mozilla=!1;jQuery.browser.webkit=!1;jQuery.browser.opera=!1;jQuery.browser.msie=!1;var a=navigator.userAgent;jQuery.browser.name=navigator.appName;jQuery.browser.fullVersion=""+parseFloat(navigator.appVersion);jQuery.browser.majorVersion=parseInt(navigator.appVersion,10);var c,b;if(-1!=(b=a.indexOf("Opera"))){if(jQuery.browser.opera=!0,jQuery.browser.name="Opera",jQuery.browser.fullVersion=a.substring(b+6),-1!=(b=a.indexOf("Version")))jQuery.browser.fullVersion=
a.substring(b+8)}else if(-1!=(b=a.indexOf("MSIE")))jQuery.browser.msie=!0,jQuery.browser.name="Microsoft Internet Explorer",jQuery.browser.fullVersion=a.substring(b+5);else if(-1!=(b=a.indexOf("Chrome")))jQuery.browser.webkit=!0,jQuery.browser.name="Chrome",jQuery.browser.fullVersion=a.substring(b+7);else if(-1!=(b=a.indexOf("Safari"))){if(jQuery.browser.webkit=!0,jQuery.browser.name="Safari",jQuery.browser.fullVersion=a.substring(b+7),-1!=(b=a.indexOf("Version")))jQuery.browser.fullVersion=a.substring(b+
8)}else if(-1!=(b=a.indexOf("Firefox")))jQuery.browser.mozilla=!0,jQuery.browser.name="Firefox",jQuery.browser.fullVersion=a.substring(b+8);else if((c=a.lastIndexOf(" ")+1)<(b=a.lastIndexOf("/")))jQuery.browser.name=a.substring(c,b),jQuery.browser.fullVersion=a.substring(b+1),jQuery.browser.name.toLowerCase()==jQuery.browser.name.toUpperCase()&&(jQuery.browser.name=navigator.appName);if(-1!=(a=jQuery.browser.fullVersion.indexOf(";")))jQuery.browser.fullVersion=jQuery.browser.fullVersion.substring(0,
a);if(-1!=(a=jQuery.browser.fullVersion.indexOf(" ")))jQuery.browser.fullVersion=jQuery.browser.fullVersion.substring(0,a);jQuery.browser.majorVersion=parseInt(""+jQuery.browser.fullVersion,10);isNaN(jQuery.browser.majorVersion)&&(jQuery.browser.fullVersion=""+parseFloat(navigator.appVersion),jQuery.browser.majorVersion=parseInt(navigator.appVersion,10));jQuery.browser.version=jQuery.browser.majorVersion}})(jQuery);
