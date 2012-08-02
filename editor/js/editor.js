(function($) {
    $.log = function(text) {
        if(typeof(window['console'])!='undefined') console.log(text);
    };

    $.pagebuild = {
        addAlignClass: function(dom_tree) {
            var total_width, width, next_width;
            total_width = 0;
            width = 0;
            next_width = 0;
            $dom_tree = $(dom_tree);

            $dom_tree.children(".wpb_sortable").removeClass("wpb_first wpb_last");

            if ($dom_tree.hasClass("wpb_main_sortable")) {
                $dom_tree.find(".wpb_sortable .wpb_sortable").removeClass("sortable_1st_level");
                $dom_tree.children(".wpb_sortable").addClass("sortable_1st_level");
                $dom_tree.children(".wpb_sortable:eq(0)").addClass("wpb_first");
                $dom_tree.children(".wpb_sortable:last").addClass("wpb_last");
            }

            if ($dom_tree.hasClass("wpb_column_container")) {
                $dom_tree.children(".wpb_sortable:eq(0)").addClass("wpb_first");
                $dom_tree.children(".wpb_sortable:last").addClass("wpb_last");
            }

            $dom_tree.children(".wpb_sortable").each(function (index) {

                var cur_el = $(this);

                // Width of current element
                if (cur_el.hasClass("span12")
                    || cur_el.hasClass("wpb_widget")) {
                    width = 12;
                }
                else if (cur_el.hasClass("span10")) {
                    width = 10;
                }
                else if (cur_el.hasClass("span9")) {
                    width = 9;
                }
                else if (cur_el.hasClass("span8")) {
                    width = 8;
                }
                else if (cur_el.hasClass("span6")) {
                    width = 6;
                }
                else if (cur_el.hasClass("span4")) {
                    width = 4;
                }
                else if (cur_el.hasClass("span3")) {
                    width = 3;
                }
                else if (cur_el.hasClass("span2")) {
                    width = 2;
                }
                total_width += width;// + next_width;

                //console.log(next_width+" "+total_width);

                if (total_width > 10 && total_width <= 12) {
                    cur_el.addClass("wpb_last");
                    cur_el.next('.wpb_sortable').addClass("wpb_first");
                    total_width = 0;
                }
                if (total_width > 12) {
                    cur_el.addClass('wpb_first');
                    cur_el.prev('.wpb_sortable').addClass("wpb_last");
                    total_width = width;
                }

                if (cur_el.hasClass('wpb_vc_column') || cur_el.hasClass('wpb_vc_tabs') || cur_el.hasClass('wpb_vc_tour') || cur_el.hasClass('wpb_vc_accordion')) {

                    if (cur_el.find('.wpb_element_wrapper .wpb_column_container').length > 0) {
                        cur_el.removeClass('empty_column');
                        cur_el.addClass('not_empty_column');
                        //addLastClass(cur_el.find('.wpb_element_wrapper .wpb_column_container'));
                        cur_el.find('.wpb_element_wrapper .wpb_column_container').each(function (index) {
                            $.wpb_composer.addLastClass($(this)); // Seems it does nothing

                            if($(this).find('div:not(.container-helper)').length==0) {
                                $(this).addClass('empty_column');
                                $(this).html($('#container-helper-block').html());
                            } else {
                                $(this).removeClass('empty_column');
                            }
                        });
                    }
                    else if (cur_el.find('.wpb_element_wrapper .wpb_column_container').length == 0) {
                        cur_el.removeClass('not_empty_column');
                        cur_el.addClass('empty_column');
                    }
                    else {
                        cur_el.removeClass('empty_column not_empty_column');
                    }
                }

               
            });
        }, // endjQuery.wpb_composer.addLastClass()
        save_composer_html: function() {
            this.addLastClass($(".wpb_main_sortable"));

            var shortcodes = generateShortcodesFromHtml($(".wpb_main_sortable"));
            //console.log(shortcodes);

            //console.log(tinyMCE.ed.isHidden());

            //if ( tinyMCE.activeEditor == null ) {

            //setActive(wpb_def_wp_editor.editorId);

            if ( isTinyMceActive() != true ) {
                //TODO: WPML and qTranslate
                //tinyMCE.activeEditor.setContent(shortcodes, {format : 'html'});
                $('#content').val(shortcodes);
            } else {
                tinyMCE.activeEditor.setContent(shortcodes, {format : 'html'});
            }



            /*var val = $.trim($(".wpb_main_sortable").html());
             $("#visual_composer_html_code_holder").val(val);

             var shortcodes = generateShortcodesFromHtml($(".wpb_main_sortable"));
             $("#visual_composer_code_holder").val(shortcodes);

             var tiny_val = switchEditors.wpautop(shortcodes);

             //[REVISE] Should determine what mode is currently on Visual/HTML
             tinyMCE.get('content').setContent(tiny_val, {format : 'raw'});

             /*try {
             tinyMCE.get('content').setContent(tiny_val, {format : 'raw'});
             }
             catch (err) {
             switchEditors.go('content', 'html');
             $('#content').val(shortcodes);
             }*/
        }
    }
})(jQuery);


/*jshint eqnull:true */
/*!
 * jQuery Cookie Plugin v1.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function($, document) {

	var pluses = /\+/g;
	function raw(s) {
		return s;
	}
	function decoded(s) {
		return decodeURIComponent(s.replace(pluses, ' '));
	}

	$.cookie = function(key, value, options) {

		// key and at least value given, set cookie...
		if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value == null)) {
			options = $.extend({}, $.cookie.defaults, options);

			if (value == null) {
				options.expires = -1;
			}

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			value = String(value);

			return (document.cookie = [
				encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// key and possibly options given, get cookie...
		options = value || $.cookie.defaults || {};
		var decode = options.raw ? raw : decoded;
		var cookies = document.cookie.split('; ');
		for (var i = 0, parts; (parts = cookies[i] && cookies[i].split('=')); i++) {
			if (decode(parts.shift()) === key) {
				return decode(parts.join('='));
			}
		}
		return null;
	};

	$.cookie.defaults = {};

})(jQuery, document);