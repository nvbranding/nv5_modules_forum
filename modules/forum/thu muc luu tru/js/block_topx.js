function addLoadEventV(func)	{
	var oldload = window.onload;
	if (typeof window.onload != 'function')	{
		window.onload = func;
	}
	else	{
		window.onload = function()	{
			oldload();
			func();
			}
	}
}	


function topxInit() {
	var ul = document.getElementById('vietvbb_topstats_t');
	var li = ul.getElementsByTagName('li');
	for (var i=0; i < li.length-1; i++)	{
		li[i].onclick = function()	{
			viewTabV(this);
			}
	}
	
	var select = document.getElementById('vietvbb_topstats_s');
	select.onchange= function()	{
		viewMenuV(this.value);
		}	
	
	var result_menu = document.getElementById('vietvbb_topstats_result');
	if(result_menu)
		result_menu.onchange= function()	{
			topXReload();
			}	
}


function viewTabV(a) {	
	var id = a.id;
	var ul = document.getElementById('vietvbb_topstats_t');
	var li = ul.getElementsByTagName('li');
	for (var i=0; i < li.length-1; i++)	{
		li[i].className='';
	}
	
	a.className='current';	
	document.getElementById('vietvbb_topstats_t_loading').style.display = 'inline';	
	$.ajax({
        type: "POST",
        url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name+'&nocache=' + new Date().getTime(),
        data: 'topx='+id,
        success: function (res) {
			var obj = $.parseJSON(res);
            var content = obj.data.content;
			document.getElementById('vietvbb_topstats_t_loading').style.display = 'none';				
			document.getElementById('vietvbb_topstats_t_content').innerHTML = content;
		}
    });
}

function handleResponsesT() {
	if (top_requestT.handler.readyState == 4 && top_requestT.handler.status == 200)	{
		document.getElementById('vietvbb_topstats_t_loading').style.display = 'none';				
		document.getElementById('vietvbb_topstats_t_content').innerHTML = top_requestT.handler.responseText;
    }
}


function viewMenuV(a) {
	document.getElementById('vietvbb_topstats_s_loading').style.display = 'inline';		
	
	$.ajax({
        type: "POST",
        url: nv_siteroot + 'index.php?' + nv_lang_variable + '=' + nv_sitelang + '&' + nv_name_variable + '=' + nv_module_name+'&nocache=' + new Date().getTime(),
        data: 'topx='+a,
        success: function (res) {
			var obj = $.parseJSON(res);
            var content = obj.data.content;
			document.getElementById('vietvbb_topstats_s_loading').style.display = 'none';				
			document.getElementById('vietvbb_topstats_s_content').innerHTML = content;
		}
    });
	
	//top_requestS = new vB_AJAX_Handler(true);
	//top_requestS.onreadystatechange(handleResponsesS);
	//var url = 'ajax.php?do=gettop&top=' + a + '&menu=1&ran=' + Math.random();
	//var result = document.getElementById('vietvbb_topstats_result');
	// if (result)	{		
		// url = url + '&result=' + result.value;		
	// }
	// top_requestS.send(url);
}

function handleResponsesS() {
	if (top_requestS.handler.readyState == 4 && top_requestS.handler.status == 200)	{
		document.getElementById('vietvbb_topstats_s_loading').style.display = 'none';				
		document.getElementById('vietvbb_topstats_s_content').innerHTML = top_requestS.handler.responseText;
    }
}

function topXReload()	{
	var ul = document.getElementById('vietvbb_topstats_t');
	var li = ul.getElementsByTagName('li');
	for (var i=0; i < li.length-1; i++)	{
		if (li[i].className == 'current')
			viewTabV(li[i]);
	}	
	var select = document.getElementById('vietvbb_topstats_s');		
	viewMenuV(select.value);
}

function topxTip (content)	{
	Tip(content, PADDING, 1 , BORDERWIDTH, 0, BGCOLOR, '', STICKY, 1, DURATION, 10000, CLICKCLOSE, true);
}


addLoadEventV(topxInit);
