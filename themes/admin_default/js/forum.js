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
		response: function(json, key) {
			html = '';
			
			
			if ( json.length ) {

				if( key == 'template')
				{ 
					 html += json;
				}else
				{
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

/*! 
 * getData
*/
function getData(b){var c={},d=/^data\-(.+)$/;$.each(b.get(0).attributes,function(b,a){if(d.test(a.nodeName)){var e=a.nodeName.match(d)[1];c[e]=a.nodeValue}});return c};

/*! 
 * get_alias
*/
function get_alias(mod,id){var title=strip_tags(document.getElementById("input-title").value);if(title!="")$.post(script_name+"?"+nv_name_variable+"="+nv_module_name+"&"+nv_fc_variable+"=alias&nocache="+(new Date).getTime(),"title="+encodeURIComponent(title)+"&mod="+mod+"&id="+id,function(res){if(res!="")document.getElementById("input-alias").value=res;else document.getElementById("input-alias").value=""});return false}; 

/*! 
 * center
*/
$.fn.center=function(){this.css({"position":"absolute","left":"50%","top":"50%"});this.css({"margin-left":-this.outerWidth()/2+"px","margin-top":-($(window).height()/2+100)+"px"});return this};


$(document).ready(function() {
	
	/* $('#mainDiv').center();
 	$(window).bind('resize', function() {
        $('#mainDiv').center( );
    }); */
	$(document).delegate('a[data-toggle=\'image\']', 'click', function(e) {
		e.preventDefault();	
		var element = this;
		var rel = $(this).attr('rel');	
		$(element).popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			content: function() {
				return '<button type="button" onclick="select_image( \'input-image' + rel + '\' )" class="btn btn-primary "><i class="fa fa-pencil rmbutton" id="button-close"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
			}
		});
	
		$(element).popover('toggle');		
 
		$('#button-close').on('click', function() {
			$(element).popover('hide');
		});
		$('#button-clear').on('click', function() {
			$(element).find('img').attr('src', $(element).find('img').attr('data-placeholder'));
			
			$(element).parent().find('input').attr('value', '');
	
			$(element).popover('hide');
		});
		
	});
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body', html: true});
 
	$('button[type=\'submit\']').on('click', function() {
		$("form[id*='form-']").submit();
	});
 
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();
		
		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});
	
	$('.close').on('click', function() {
		$('.alert-danger').remove();
	});
});


