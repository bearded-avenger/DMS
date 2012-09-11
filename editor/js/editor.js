
// On document ready stuff
jQuery(document).ready(function() {
	
	// Disable Text Selector on Drag
	document.onselectstart = function () { return false; };

	// Basic Setup
	jQuery('body').addClass('pl-editor');
	jQuery('.pl-inner').addClass('editor-row');
	jQuery('.pl-area .pl-content .pl-inner').addClass('pl_sortable_area');
	jQuery('.pl_sortable_area .pl-section').addClass('pl_sortable');
	
//	jQuery.pageBuilder.reloadConfig();
//	jQuery.pageBuilder.startDroppable();
//	jQuery.pageBuilder.startResize();
//	columnControls();
	

});


		

(function($) {
    $.log = function(text) {
        if(typeof(window['console'])!='undefined') console.log(text);
    };

    $.pageBuilder = {
	
        reloadConfig: function() {
		
			jQuery('.pl_sortable_area').each(function () {
				jQuery.pageBuilder.alignGrid( this );
			});

        },
		isAreaEmpty: function(area){
			var addTo = (area.hasClass('ecolumn-inner')) ? area.parent() : area;
			if(!area.children(".pl_sortable").length) {
			    addTo.addClass('empty-area');
			} else {
			    addTo.removeClass('empty-area');
			}
		},

        alignGrid: function( area_dom ) {

            var total_width = 0,
            	width = 0,
            	next_width = 0,
				avail_offset = 0;
	
            $dom_tree = $(area_dom);
			
            $dom_tree.children(".pl_sortable").removeClass("sortable_first sortable_last").css('opacity', 1);

  			jQuery.pageBuilder.isAreaEmpty( $dom_tree );

			$dom_tree.find(".pl_sortable .pl_sortable").removeClass("sortable_1st_level");
			$dom_tree.children(".pl_sortable").addClass("sortable_1st_level");
			$dom_tree.children(".pl_sortable:eq(0)").addClass("sortable_first");
			$dom_tree.children(".pl_sortable:last").addClass("sortable_last");

			
            $dom_tree.children(".pl_sortable").each(function (index) {
				
                var cur_el = $(this),
					col_size = getColumnSize(cur_el), 
					off_size = getOffsetSize(cur_el);
				
				width = col_size[4] + off_size[3];
				
				total_width += width;
				
				avail_offset = 12 - col_size[4]; 
			
				if(avail_offset == 0)
					cur_el.addClass('no_offset');
				else 
					cur_el.removeClass('no_offset');
			
				if(width > 12){
					avail_offset = 12 - col_size[4]; 
					cur_el.removeClass(off_size[0]).addClass('offset'+avail_offset);
					off_size = getOffsetSize(cur_el);
				}

               	// Set Numbers
				jQuery(cur_el).find(".section-size:first").html(sizes[4]+'/12');
				jQuery(cur_el).find(".offset-size:first").html(off_size[3]);
				
				if (total_width > 12 || cur_el.hasClass('force_start_row')) {
					
                    cur_el.addClass('sortable_first');
                    cur_el.prev('.pl_sortable').addClass("sortable_last");
                    total_width = width;
                } 

            });
        }, // endjQuery.pageBuilder.alignGrid()

		saveConfig: function(){
			//this.Droppable();
			jQuery.pageBuilder.reloadConfig();
		},
		
		startResize: function(){
			// Resizable Content Area
			jQuery('.pl-content').resizable({ 
				handles: "e, w",
				minWidth: 400,
				resize: function(event, ui) { 

					var resizeWidth = ui.size.width, 
						resizeOrigWidth = ui.originalSize.width, 
						resizeNewWidth = resizeOrigWidth + ((resizeWidth - resizeOrigWidth) * 2); 

					jQuery('.pl-content').css('left', 'auto').width(resizeNewWidth); 

				}
			});
			
		},

		startDroppable: function(){

		    jQuery('.pl_sortable_area').sortable({
		        items: ".pl-section",
				dropOnEmpty: true,
				forcePlaceholderSize: true,
				forceHelperSize: false,
		        connectWith: ".pl_sortable_area",
				scrollSensitivity: 200,
				scrollSpeed: 40,
		        placeholder: "pl-placeholder",
		        cursor: "move",
				distance: 0.5,
				delay: 100,
				opacity: 0.6,
				tolerance: "pointer",
			//	appendTo: '.pl-area',
			// cursorAt: { left: 5 },
			// helper: function(){
			// 	return '<div class="helpit">omg</div>';
			// },
				start: function(event, ui){
					jQuery('#page').addClass('pl-dragging');
					jQuery('.pl-section').effect('highlight', '#ff0000', 1000);
				}, 
				stop: function(event, ui){
					jQuery('#page').removeClass('pl-dragging');
				},
				
				over: function(event, ui) {
		           ui.placeholder.css({maxWidth: ui.placeholder.parent().width()}); 
		           
		 			ui.placeholder.removeClass('hidden-placeholder');
		            if( ui.item.hasClass('section-ecolumn') && ui.placeholder.parent().parent().hasClass('section-ecolumn')) {
		                ui.placeholder.addClass('hidden-placeholder');
		            }

		        },
				beforeStop: function(event, ui) {
		            if( ui.item.hasClass('section-ecolumn') && ui.placeholder.parent().parent().hasClass('section-ecolumn') ) {
		                return false;
		            }
		        },
				update: function() {
					jQuery.pageBuilder.reloadConfig();
				},
				
		    });
		
			
			
			jQuery('.pl_sortable_area').droppable({
				greedy: true,
				accept: ".droppable_el, .droppable_column, .pl-section",
				hoverClass: "wpb_ui-state-active",
				drop: function( event, ui ) {
					jQuery.pageBuilder.reloadConfig();
				}
			});
			
			jQuery('.ecolumn-inner').droppable({
		        greedy: true,
		        accept: function(dropable_el) {
		            if ( dropable_el.hasClass('dropable_el') && jQuery(this).hasClass('ui-droppable') && dropable_el.hasClass('not_dropable_in_third_level_nav') ) {
		                return false;
		            } else if ( dropable_el.hasClass('dropable_el') == true ) {
		                return true;
		            }
		        },
		        hoverClass: "wpb_ui-state-active",
		        over: function( event, ui ) {
		            jQuery(this).parent().addClass("wpb_ui-state-active");
		        },
		        out: function( event, ui ) {
		            jQuery(this).parent().removeClass("wpb_ui-state-active");
		        },
		        drop: function( event, ui ) {
		            //console.log(jQuery(this));
		            jQuery(this).parent().removeClass("wpb_ui-state-active");
		            getElementMarkup(jQuery(this), ui.draggable, "addLastClass");
		        }
		    });
		 

		}, //------------->> end initDroppable() <--------------//
		
    }
})(jQuery);

/* Set action for column size and delete buttons
---------------------------------------------------------- */
function columnControls() {
	
	jQuery('html')
		.on('click', function () {
			jQuery(".pl-area-controls").removeClass('open').find('.controls-toggle-btn').removeClass('active');
		});
	jQuery('body')
		.on('click', '.controls-buttons', function (e) { e.stopPropagation() })
	
	jQuery(".controls-toggle-btn").on("click", function(e) {
		
		var isActive
		  , $parent
		
		e.stopPropagation()
		
		$parent = jQuery(this).parent()
		
		isActive = $parent.hasClass('open')
		
		if (!isActive){
			$parent.toggleClass('open').find('.controls-toggle-btn').addClass('active');
		}
		
	});
	
	jQuery('.pl-section').hover(
	  function () {
	    jQuery('.pl-section-controls:eq(0)', this).show();
	  }, 
	  function () {
	    jQuery('.pl-section-controls:eq(0)', this).hide();
	  }
	);

	
	jQuery(".section-edit").on("click", function(e) {
		e.preventDefault();
		drawModal('The Cool Title');
	});
	
	jQuery(".section-delete").live("click", function(e) {
		e.preventDefault();
		var answer = confirm ("Press OK to delete section, Cancel to leave");
		if (answer) {
            $parent = jQuery(this).closest(".pl_sortable");
			jQuery(this).closest(".pl_sortable").remove();
            $parent.addClass('empty_column');
			jQuery.pageBuilder.reloadConfig();
		}
	});
	jQuery(".section-clone").live("click", function(e) {
		e.preventDefault();
		var closest_el = jQuery(this).closest(".pl_sortable"),
			cloned = closest_el.clone( true );

		cloned.insertAfter(closest_el).hide().fadeIn().find('.pl-section-controls').hide();

		//Fire INIT callback if it is defined
		cloned.find('.pl_initialized').removeClass('pl_initialized');
		cloned.find(".pl_vc_init_callback").each(function(index) {
			var fn = window[jQuery(this).attr("value")];
			if ( typeof fn === 'function' ) {
			    fn(cloned);
			}
		});

		//closest_el.clone().appendTo(jQuery(this).closest(".wpb_main_sortable, .wpb_column_container")).hide().fadeIn();
		jQuery.pageBuilder.reloadConfig();
	});


	
	jQuery(".pl_sortable .pl_sortable .column_popup").live("click", function(e) {
		e.preventDefault();
		var answer = confirm ("Press OK to pop (move) section to the top level, Cancel to leave");
		if (answer) {
			jQuery(this).closest(".pl_sortable").appendTo('.pl_main_sortable');//insertBefore('.wpb_main_sortable div.wpb_clear:last');
			initDroppable();
			jQuery.pageBuilder.reloadConfig();
		}
	});


	jQuery(".section-increase").live("click", function(e) {
		e.preventDefault();
		var column = jQuery(this).closest(".pl_sortable"),
			sizes = getColumnSize(column);
		if (sizes[1]) {
			column.removeClass(sizes[0]).addClass(sizes[1]);
			jQuery.pageBuilder.reloadConfig();
		}
	});

	jQuery(".section-decrease").live("click", function(e) {
		e.preventDefault();
		
		var column = jQuery(this).closest(".pl_sortable"),
			sizes = getColumnSize(column);
			
		if (sizes[2]) {
			column.removeClass(sizes[0]).addClass(sizes[2]);
			jQuery.pageBuilder.reloadConfig();
		}
	});
	
	jQuery(".section-offset-increase").live("click", function(e) {
		e.preventDefault();
		var column = jQuery(this).closest(".pl_sortable"),
			sizes = getOffsetSize(column);
			
		if (sizes[1]) {
			column.removeClass(sizes[0]).addClass(sizes[1]);
			jQuery.pageBuilder.reloadConfig();
		}
	});
	jQuery(".section-offset-reduce").live("click", function(e) {
		e.preventDefault();
		var column = jQuery(this).closest(".pl_sortable"),
			sizes = getOffsetSize(column);
			
		if (sizes[1]) {
			column.removeClass(sizes[0]).addClass(sizes[2]);
			jQuery.pageBuilder.reloadConfig();
		}
	});
	jQuery(".section-start-row").live("click", function(e) {
		e.preventDefault();
		var column = jQuery(this).closest(".pl_sortable");
			
		column.toggleClass('force_start_row');
		
		jQuery.pageBuilder.reloadConfig();
	});
	
} // end columnControls()


function getOffsetSize(column) {


	sizes = getColumnSize(column);
	
	var max = 12, 
		avail = max - sizes[4], 
		data = []; 
	
	for( i = 0; i <= 12; i++){

			next = ( i == avail ) ? 0 : i+1;

			prev = ( i == 0 ) ? avail : i-1;	

			if(column.hasClass("offset"+i))
				data = new Array("offset"+i, "offset"+next, "offset"+prev, i);

	}
	
	if(data.length === 0)
		return new Array("offset0", "offset0", "offset0", 0);
	else
		return data;

}

function getColumnSize(column) {
	
	if (column.hasClass("span12")) //full-width
		return new Array("span12", "span2", "span10", "12/12", 12);

    else if (column.hasClass("span10")) //five-sixth
        return new Array("span10", "span12", "span9", "10/12", 10);

	else if (column.hasClass("span9")) //three-fourth
		return new Array("span9", "span10", "span8", "9/12", 9);

	else if (column.hasClass("span8")) //two-third
		return new Array("span8", "span9", "span6", "8/12", 8);

	else if (column.hasClass("span6")) //one-half
		return new Array("span6", "span8", "span4", "6/12", 6);

	else if (column.hasClass("span4")) // one-third
		return new Array("span4", "span6", "span3", "4/12", 4);

	else if (column.hasClass("span3")) // one-fourth
		return new Array("span3", "span4", "span2", "3/12", 3);
		
    else if (column.hasClass("span2")) // one-sixth
        return new Array("span2", "span3", "span12", "2/12", 2);

	else
		return false;
		
} // end getColumnSize()


/* Get initial html markup for content element. This function
   use AJAX to run do_shortcode and then place output code into
   main content holder
---------------------------------------------------------- */
function getElementMarkup (target, element, action) {

	var data = {
		action: 'pl_save_pagebuilder',
		element: element.attr('id'),
		data_element: element.attr('data-element'),
		data_width: element.attr('data-width')
	};

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, data, function(response) {
		//alert('Got this from the server: ' + response);
		//jQuery(target).append(response);

		//Fire INIT callback if it is defined
		//jQuery(response).find(".wpb_vc_init_callback").each(function(index) {
        // target.removeClass('empty_column');
        // 		jQuery(target).append(response).find(".wpb_vc_init_callback").each(function(index) {
        // 			var fn = window[jQuery(this).attr("value")];
        // 			if ( typeof fn === 'function' ) {
        // 			    fn(jQuery(this).closest('.wpb_content_element').removeClass('empty_column'));
        // 			}
        // 		});
        //         jQuery.wpb_composer.isMainContainerEmpty();

		
		jQuery.pageBuilder.reloadConfig();
	});

} // end getElementMarkup()




// MISC JUNK
function drawStructure(title){
	
}

function drawModal(title){
	
	jQuery('#editModal h3').html(title);
	
	jQuery('#editModal').modal();
}


