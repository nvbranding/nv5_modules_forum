jQuery(document).ready(function($) {
	'use strict';

	var $document = $(document);


	/***********************
	 * Add custom CSS *
	 ***********************/
	$('<style type="text/css">' +
		'.sceditor-dropdown { text-align: ' + ($('body').css('direction') === 'rtl' ? 'right' :'left') + '; }' +
		'.sceditor-container .sceditor-button-video div {background-image:url(\''+sceditor_opts.base_mod+'/themes/film.png\');}' +
		'.sceditor-button-vimeo div { background: url(\''+sceditor_opts.base_mod+'/themes/vimeo.png\');}' +
	'</style>').appendTo('body');



	/********************************************
	 * Update editor to use align= as alignment *
	 ********************************************/
	$.sceditor.plugins.bbcode.bbcode
		.set('align', {
			html: function(element, attrs, content) {
				return '<div align="' + (attrs.defaultattr || 'left') + '">' + content + '</div>';
			},
			isInline: false
		})
		.set('center', { format: '[center]{0}[/center]' })
		.set('left', { format: '[left]{0}[/left]' })
		.set('right', { format: '[right]{0}[/right]' })
		.set('justify', { format: '[justify]{0}[/justify]' });

	$.sceditor.command
		.set('center', { txtExec: ['[center]', '[/center]'] })
		.set('left', { txtExec: ['[left]', '[/left]'] })
		.set('right', { txtExec: ['[right]', '[/right]'] })
		.set('justify', { txtExec: ['[justify]', '[/justify]'] });



	/************************************************
	 * Update font to support sividuc's BBCode dialect *
	 ************************************************/
	$.sceditor.plugins.bbcode.bbcode
		.set('list', {
			html: function(element, attrs, content) {
				var type = (attrs.defaultattr === '1' ? 'ol' : 'ul');

				if(attrs.defaultattr === 'a')
					type = 'ol type="a"';

				return '<' + type + '>' + content + '</' + type + '>';
			},

			breakAfter: false
		})
		.set('ul', { format: '[list]{0}[/list]' })
		.set('ol', {
			format: function($elm, content) {
				var type = ($elm.attr('type') === 'a' ? 'a' : '1');

				return '[list=' + type + ']' + content + '[/list]';
			}
		})
		.set('li', { format: '[*]{0}', excludeClosing: true })
		.set('*', { excludeClosing: true, isInline: false });

	$.sceditor.command
		.set('bulletlist', { txtExec: ['[list]\n[*]', '\n[/list]'] })
		.set('orderedlist', { txtExec: ['[list=1]\n[*]', '\n[/list]'] });



	/***********************************************************
	 * Update size tag to use xx-small-xx-large instead of 1-7 *
	 ***********************************************************/
	$.sceditor.plugins.bbcode.bbcode.set('size', {
		format: function($elm, content) {
			var	fontSize,
				sizes = ['xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large'],
				size  = $elm.data('scefontsize');

			if(!size)
			{
				fontSize = $elm.css('fontSize');

				// Most browsers return px value but IE returns 1-7
				if(fontSize.indexOf('px') > -1) {
					// convert size to an int
					fontSize = fontSize.replace('px', '') - 0;
					size     = 1;

					if(fontSize > 9)
						size = 2;
					if(fontSize > 12)
						size = 3;
					if(fontSize > 15)
						size = 4;
					if(fontSize > 17)
						size = 5;
					if(fontSize > 23)
						size = 6;
					if(fontSize > 31)
						size = 7;
				}
				else
					size = (~~fontSize) + 1;

				if(size > 7)
					size = 7;
				if(size < 1)
					size = 1;

				size = sizes[size-1];
			}

			return '[size=' + size + ']' + content + '[/size]';
		},
		html: function(token, attrs, content) {
			return '<span data-scefontsize="' + attrs.defaultattr + '" style="font-size:' + attrs.defaultattr + '">' + content + '</span>';
		}
	});

	$.sceditor.command.set('size', {
		_dropDown: function(editor, caller, callback) {
			var	content   = $('<div />'),
				clickFunc = function (e) {
					callback($(this).data('size'));
					editor.closeDropDown(true);
					e.preventDefault();
				};

			for (var i=1; i < 7; i++)
				content.append($('<a class="sceditor-fontsize-option" data-size="' + i + '" href="#"><font size="' + i + '">' + i + '</font></a>').click(clickFunc));

			editor.createDropDown(caller, 'fontsize-picker', content);
		},
		txtExec: function(caller) {
			var	editor = this,
				sizes = ['xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large'];

			$.sceditor.command.get('size')._dropDown(
				editor,
				caller,
				function(size) {
					size = (~~size);
					size = (size > 7) ? 7 : ( (size < 1) ? 1 : size );

					editor.insertText('[size=' + sizes[size] + ']', '[/size]');
				}
			);
		}
	});



	/********************************************
	 * Update quote to support pid and dateline *
	 ********************************************/
	$.sceditor.plugins.bbcode.bbcode.set('quote', {
		format: function(element, content) {
			var	author = '',
				$elm  = $(element),
				$cite = $elm.children('cite').first();

			if($cite.length === 1 || $elm.data('author'))
			{
				author = $cite.text() || $elm.data('author');

				$elm.data('author', author);
				$cite.remove();

				content	= this.elementToBbcode($(element));
				author  = '=' + author;

				$elm.prepend($cite);
			}

			if($elm.data('pid'))
				author += " pid='" + $elm.data('pid') + "'";

			if($elm.data('dateline'))
				author += " dateline='" + $elm.data('dateline') + "'";

			return '[quote' + author + ']' + content + '[/quote]';
		},
		html: function(token, attrs, content) {
			var data = '';

			if(attrs.pid)
				data += ' data-pid="' + attrs.pid + '"';

			if(attrs.dateline)
				data += ' data-dateline="' + attrs.dateline + '"';

			if(typeof attrs.defaultattr !== "undefined")
				content = '<cite>' + attrs.defaultattr + '</cite>' + content;

			return '<blockquote' + data + '>' + content + '</blockquote>';
		},
		quoteType: function(val, name) {
			return "'" + val.replace("'", "\\'") + "'";
		},
		breakStart: true,
		breakEnd: true
	});



	/************************************************************
	 * Update font tag to allow limiting to only first in stack *
	 ************************************************************/
	$.sceditor.plugins.bbcode.bbcode.set('font', {
		format: function(element, content) {
			var font;

			if(element[0].nodeName.toLowerCase() !== 'font' || !(font = element.attr('face')))
				font = element.css('font-family');

			if(sceditor_opts.limitfont)
				font = font.split(',')[0];

			return '[font=' + this.stripQuotes(font) + ']' + content + '[/font]';
		}
	});



	/**************************
	 * Add video command *
	 **************************/
	$.sceditor.command.set('video', {
		_dropDown: function (editor, caller) {
			var $content, videourl, videotype;
	
			// Excludes MySpace TV and Yahoo Video as I couldn't actually find them. Maybe they are gone now?
			$content = $(
				'<div>' +
					'<label for="videotype">' + editor._('Video Type:') + '</label> ' +
					'<select id="videotype">' +
						'<option value="dailymotion">Dailymotion</option>' +
						'<option value="metacafe">MetaCafe</option>' +
						'<option value="vimeo">Vimeo</option>' +
						'<option value="youtube">Youtube</option>' +
					'</select>'+
				'</div>' +
				'<div>' +
					'<label for="link">' + editor._('Video URL:') + '</label> ' +
					'<input type="text" id="videourl" value="http://" />' +
				'</div>' +
				'<div><input type="button" class="button" value="' + editor._('Insert') + '" /></div>'
			);

			$content.find('.button').click(function (e) {
				videourl  = $content.find('#videourl').val();
				videotype = $content.find('#videotype').val();

				if (videourl !== '' && videourl !== 'http://')
					editor.insert('[video=' + videotype + ']' + videourl + '[/video]');
				
				editor.closeDropDown(true);
				e.preventDefault();
			});

			editor.createDropDown(caller, 'insertvideo', $content);
		},
		exec: function (caller) {
			$.sceditor.command.get('video')._dropDown(this, caller);
			
		},
		txtExec: function (caller) {
			$.sceditor.command.get('video')._dropDown(this, caller);
		},
		tooltip: 'Insert a video'
	});
	$.sceditor.plugins.bbcode.bbcode.set('video', {
		allowsEmpty: true,
		tags: {
			iframe: {
				'data-sividuc-vt': null
			}
		},
		format: function($element, content) {
			return '[video=' + $element.data('sividuc-vt') + ']' + $element.data('sividuc-vsrc') + '[/video]';
		},
		html: function(token, attrs, content) {
			var	matches, url,
				html = {
					dailymotion: '<iframe frameborder="0" width="480" height="270" src="{url}" data-sividuc-vt="{type}" data-sividuc-vsrc="{src}"></iframe>',
					metacafe: '<iframe src="{url}" width="440" height="248" frameborder=0 data-sividuc-vt="{type}" data-sividuc-vsrc="{src}"></iframe>',
					vimeo: '<iframe src="{url}" width="500" height="281" frameborder="0" data-sividuc-vt="{type}" data-sividuc-vsrc="{src}"></iframe>',
					youtube: '<iframe width="560" height="315" src="{url}" frameborder="0" data-sividuc-vt="{type}" data-sividuc-vsrc="{src}"></iframe>'
				};

			if(html[attrs.defaultattr])
			{
				switch(attrs.defaultattr)
				{
					case 'dailymotion':
						matches = content.match(/dailymotion\.com\/video\/([^_]+)/);
						url     = matches ? 'http://www.dailymotion.com/embed/video/' + matches[1] : false;
						break;
					case 'metacafe':
						matches = content.match(/metacafe\.com\/watch\/([^\/]+)/);
						url     = matches ? 'http://www.metacafe.com/embed/' + matches[1] : false;
						break;
					case 'vimeo':
						matches = content.match(/vimeo.com\/(\d+)($|\/)/);
						url     = matches ? '//player.vimeo.com/video/' + matches[1] : false;
						break;
					case 'youtube':
						matches = content.match(/(?:v=|v\/|embed\/|youtu\.be\/)(.{11})/);
						url     = matches ? '//www.youtube.com/embed/' + matches[1] : false;
						break;
				}

				if(url)
				{
					return html[attrs.defaultattr]
						.replace('{url}', url)
						.replace('{src}', content)
						.replace('{type}', attrs.defaultattr);
				}
				
			}

			return token.val + content + (token.closing ? token.closing.val : '');
		}
	});
	
	
	/**************************
	 * Add vimeo command *
	 **************************/
	$.sceditor.command.set('vimeo', {
		exec: function (caller) {
			var matches,
				vimeoHtml = '<center><iframe width="560" height="315" src="http://player.vimeo.com/video/{id}" data-viemo-id="{id}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></center>',
				editor = this,
				
				content = $('<div><label for="link">'+$.sceditor.locale[sceditor_opts.lang]['Video URL:']+':</label> <input type="text" id="link" value="http://" /></div>' +
					'<div><input type="button" class="button" value="'+$.sceditor.locale[sceditor_opts.lang]['Insert']+'" /></div>');

			content.find('.button').click(function (e) {
				var val = content.find("#link").val().replace("http://", "");

				if (val !== "") {
					matches = val.match(/vimeo\..*\/(\d+)(?:$|\/)/);

					if (matches)
						val = matches[1];

					if (/^\d+$/.test(val))
							editor.insert('[vimeo]' + val + '[/vimeo]');
					else
						alert('Invalid Viemo video');
				}

				editor.closeDropDown(true);
				e.preventDefault();
			});

			editor.createDropDown(caller, "insertlink", content);
		},
		txtExec: function (caller) {
			$.sceditor.command.get('vimeo').exec(caller);
		},
		tooltip: "Insert a Vimeo video"
	});

	$.sceditorBBCodePlugin.bbcode.set('vimeo', {
		allowsEmpty: true,
		tags: {
			iframe: {
				'data-vimeo-id': null
			}
		},
		format: function(element, content) {
			if(!(element = element.attr('data-vimeo-id')))
				return content;

			return '[vimeo]' + element + '[/vimeo]';
		},
		html: '<center><iframe width="560" height="315" src="http://player.vimeo.com/video/{0}" data-vimeo-id="{0}"' +
			' frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></center>'
	});
	

	/*************************************
	 * Remove last bits of table support *
	 *************************************/
	$.sceditor.command.remove('table');
	$.sceditor.plugins.bbcode.bbcode.remove('table')
					.remove('tr')
					.remove('th')
					.remove('td');



	/********************************************
	 * Remove code and quote if in partial mode *
	 ********************************************/
	if(sceditor_opts.partialmode)
	{
		$.sceditor.plugins.bbcode.bbcode.remove('code').remove('quote').remove('video');
		$.sceditor.command
			.set('code', {
				exec: function() {
					this.insert('[code]', '[/code]');
				}
			})
			.set('quote', {
				exec: function() {
					this.insert('[quote]', '[/quote]');
				}
			});
	}



	/*******************
	 * Init the editor *
	 *******************/
	var editor_opts = {
		style:			''+sceditor_opts.base_mod+'/css/jquery.sceditor.custom.css',
		toolbar:		'bold,italic,underline,strike,subscript,superscript|left,center,right,justify|' +
					'font,size,color,removeformat|bulletlist,orderedlist|code,quote|horizontalrule,' +
					'image,email,link,unlink|emoticon,youtube,vimeo,date,time|print,source',
		resizeMaxHeight:	800,
		rtl:			null,
		plugins:		'bbcode',
		autofocus:		sceditor_opts.autofocus,
		locale:			sceditor_opts.lang,
		height:			sceditor_opts.height,
		enablePasteFiltering:   true,
		autofocusEnd:           true
	};

	$('#message, #signature').sceditor(editor_opts);



	/******************************
	 * Source mode option support *
	 ******************************/
	if(sceditor_opts.sourcemode == 1)
		$('#message, #signature').sceditor('instance').sourceMode(true);
		
	if(sceditor_opts.emoticons == 1)
		$('#message, #signature').sceditor('instance').emoticons(true);
	
	// chiều rộng và chiều cao khung soạn thảo
	$('#message, #signature').sceditor('instance').dimensions('100%', 300);


	/**************************************************
	 * Init the editor for xmlhttp calls (Quick Edit) *
	 **************************************************/
	// $.fn.on || $.fn.live is for compatibility with old jQuery versions.
	// 1.7+ uses on and 1.3-1.7 uses live
	// ($.fn.on || $.fn.live).call($document, 'focus', 'textarea[id*="quickedit_"]', function () {
		// $(this).sceditor(editor_opts);

		// if(sceditor_opts.sourcemode)
			// $(this).sceditor('instance').sourceMode(true);
	// });



	/**************************
	 * Emoticon click support *
	 **************************/
	// $('#clickable_smilies img').each(function() {
		// $(this).css('cursor', 'pointer');

		// $(this).click(function() {
			// $('#message, #signature').data('sceditor').insert($(this).attr('alt'));
			// return false;
		// });
	// });



	/****************************
	 * Emoticon disable support *
	 ****************************/
	// var $checkbox = $('input[name=postoptions\\[disablesmilies\\]], input[name=options\\[disablesmilies\\]]');

	// $checkbox.change(function() {
		// $('#message, #signature').sceditor('instance').emoticons(!this.checked);
	// });

	// if($checkbox.length)
		// $('#message, #signature').sceditor('instance').emoticons(!$checkbox[0].checked);


	/****************************
	 * Form reset compatibility *
	 ****************************/
	// var textarea = $('#message, #signature').get(0);
	// if(textarea)
	// {
		// $(textarea.form).bind('reset', function() {
			// $('#message, #signature').data('sceditor').val('').emoticons(true);
		// });
	// }
});
