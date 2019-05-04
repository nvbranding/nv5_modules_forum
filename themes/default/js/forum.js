function getData(b){var c={},d=/^data-(.+)$/;$.each(b.get(0).attributes,function(b,a){if(d.test(a.nodeName)){var e=a.nodeName.match(d)[1];c[e]=a.nodeValue}});return c};


/*!
 * Autofill v1.1.0 (http://dangdinhtu.com)
 * Copyright 2013-2015 webdep24.com.
 * Licensed under the MIT license
 */
// Autofill */
(function($) {
	function Autofill(element, options) {
		this.element = element;
		this.options = options;
		this.timer = null;
		this.items = new Array();

		$(element).attr('autocomplete', 'off');
		$(element).on('focus', $.proxy(this.focus, this));
		$(element).on('blur', $.proxy(this.blur, this));
		$(element).on('keydown', $.proxy(this.keydown, this));

		$(element).after('<ul class="dropdown-menu template scrollable-menu" role="menu"></ul>');
		$(element).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));
	}

	Autofill.prototype = {
		focus: function() {
			this.request();
		},
		blur: function() {
			
			//setTimeout(function(object) {
			//	object.hide();
			//}, 200, this);
		},
		click: function(event) {
			event.preventDefault();

			value = $(event.target).parent().attr('data-value');

			if (value && this.items[value]) {
				this.options.select(this.items[value]);
			}
			this.hide();
		},
		keydown: function(event) {
			switch(event.keyCode) {
				case 27: // escape
					this.hide();
					break;
				case 188: // comma
					break;
				default:
					this.request();
					break;
			}
		},
		show: function() {
			var pos = $(this.element).position();

			$(this.element).siblings('ul.dropdown-menu').css({
				top: pos.top + $(this.element).outerHeight(),
				left: pos.left
			});

			$(this.element).siblings('ul.dropdown-menu').show();
		},
		hide: function() {
			$(this.element).siblings('ul.dropdown-menu').hide();
		},
		request: function() {
			clearTimeout(this.timer);

			this.timer = setTimeout(function(object) {
				object.options.source($(object.element).val(), $.proxy(object.response, object));
			}, 200, this);
		},
		response: function(json) {
			html = '';
			if ( json.length ) {
				for (i = 0; i < json.length; i++) {
					this.items[json[i]['value']] = json[i];
				}

				for (i = 0; i < json.length; i++) {
					if (!json[i]['category']) {	
						var content = json[i]['label'].replace(new RegExp(this.element.value, "gi"), '<strong>$&</strong>');	
						html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + content + '</a></li>';
					}
				}

				// Get all the ones with a categories
				var category = new Array();

				for (i = 0; i < json.length; i++) {
					if (json[i]['category']) {
						if (!category[json[i]['category']]) {
							category[json[i]['category']] = new Array();
							category[json[i]['category']]['name'] = json[i]['category'];
							category[json[i]['category']]['item'] = new Array();
						}

						category[json[i]['category']]['item'].push(json[i]);
					}
				}

				for (i in category) {
					html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

					for (j = 0; j < category[i]['item'].length; j++) {
						html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
					}
				}
				 
			}

			if (html) {
				this.show();
			} else {
				this.hide();
			}

			$(this.element).siblings('ul.dropdown-menu').html(html);
		}
	};

	$.fn.autofill = function(option) {
		return this.each(function() {
			var data = $(this).data('autofill');

			if (!data) {
				data = new  Autofill(this, option);

				$(this).data('autofill', data);
			}
		});
	}
})(window.jQuery);  

function addHtmlToEditor( imgFile, title ){
    var html = '<img alt="'+title+'" src="' + imgFile + '" class="attachFull bbCodeImage" style="max-width:100%"/>';
    CKEDITOR.instances['forum_message'].insertHtml(html);
}

// function strip_tags( str ) {
    // str=str.toString();
    // return str.replace(/</?[^>]+>/gi, '');
// }

function strip_tags(input, allowed) {
  
  allowed = (((allowed || '') + '')
    .toLowerCase()
    .match(/<[a-z][a-z0-9]*>/g) || [])
    .join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
  return input.replace(commentsAndPhpTags, '')
    .replace(tags, function($0, $1) {
      return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
}

function getRandomNum(lbound, ubound) {
	return (Math.floor(Math.random() * (ubound - lbound)) + lbound);
}

function getRandomChar() {
 
	var charSet = "0123456789abcdefghijklmnopqrstuvwxyz";
	return charSet.charAt(getRandomNum(0, charSet.length));
}

function getKeyCode(  num )
{
	var rc = '';
	rc = rc + getRandomChar();
	for (var idx = 1; idx < num; ++idx) 
	rc = rc + getRandomChar();
	return rc;
}


/////////////////////////////////////////
$('blockquote.quoteContainer .quote').each(function(){
	var obj = $(this);
	if( obj[0].scrollHeight > 150 )
	{
		obj.next().addClass('quoteCut');	
	}	
})
$('.quoteExpand.quoteCut').on('click', function(){
	$(this).parent().addClass('expanded');	
});

function PopupMenu(obj) {
    $(obj).removeClass('PopupClosed').addClass('PopupOpen');
    var id = $(obj).attr('rel');
    var x = $(obj).parent().position();
    var ptop = x.top + $(obj).parent().outerHeight();
    var pleft = x.left - 2;
    $(id).css({
        'display': 'block',
        'visibility': 'visible',
        'top': ptop + 'px',
        'left': pleft + 'px'
    }).slideDown();

}
$('.Popup a.PopupControl').hover(function(e) {
	PopupMenu(this);
});

$(document).click(function(e) {
    var px = $('#Menutool').offset();
    ofleft = px.left;
    ofright = px.left + $('#Menutool').outerWidth();
    ytop = px.top;
    ybottom = px.top + $('#Menutool').outerHeight();

    if (e.target.className == 'PopupControl PopupOpen') {
        $('#Menutool').slideUp(200, function() {
            $('.PopupControl').removeClass('PopupOpen').addClass('PopupClosed');
        });
    } else if (e.target.className == 'PopupControl PopupClosed') {
		var obj = $('.PopupControl.PopupClosed');
        PopupMenu(obj);
    } else {
        if (e.pageX < ofleft || e.pageX > ofright || e.pageY < ytop || e.pageY > ybottom) {
            $('#Menutool').slideUp(200, function() {
                $('.PopupControl').removeClass('PopupOpen').addClass('PopupClosed');
            });

        }
    }

});