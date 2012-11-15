
// On document ready stuff
jQuery(document).ready(function() {
	
	// Disable Text Selector on Drag
	document.onselectstart = function () { return false };

	// Basic Setup
	jQuery('body').addClass('pl-editor')
	jQuery('.pl-inner').addClass('editor-row')
	
	// Adds class for drag/dropping content sections
	jQuery('.pl-area .pl-content .pl-inner').addClass('pl_sortable_area')
	
	// Adds class for drag/dropping areas
	jQuery('.outline').addClass('pl_area_container')
	
	jQuery('.pl_sortable_area .pl-section').addClass('pl_sortable')
	
//	jQuery.pageBuilder.startResize(); // Layout resize	
	
	// Aligns things
	jQuery.pageBuilder.reloadConfig()
	
	jQuery.toolBar.listen()
	
	var ml = jQuery('.pl-toolbox').toolbox()

});


		

!function ($) {
    
	// Event Listening
	$.toolBar = {
		listen: function() {
		
			$(".btn-drag-drop").on("click", function(e) {
				e.stopPropagation()
				
				$.pageBuilder.toggle($(this))
				
				$.areaControl.toggle($(this))
				
			})

        },
	}

	// Page Drag/Drop Builder
    $.pageBuilder = {
	
		toggle: function( btn ){
			
			if(!jQuery.pageBuilder.isActive){
				
				
				// Graphical Flare
				$('.pl_sortable').effect('highlight', 1500)
				btn.addClass('active')
				
				// Enable CSS
				$('body').addClass('drag-drop-editing')
			
				// Track Toggling
				$.pageBuilder.isActive = true
			
				// JS
				$.pageBuilder.startDroppable()
				
				$.pageBuilder.reloadConfig()
				
				$.pageBuilder.sectionControls()
				
			} else {
				$('body').removeClass('drag-drop-editing')
				
				btn.removeClass('active')
				
				$.pageBuilder.isActive = false
			
				$('.s-control')
					.off('click.sectionControls')
			}
			
		}
		
		, sectionControls: function() {
			
			$('.s-control').on('click.sectionControls', function(e){
		
				e.preventDefault()
			
				var btn = $(this)
				,	section = btn.closest(".pl_sortable")
			
				if(btn.hasClass('section-edit')){
					
					// TODO Open up and load options panel
					
				} else if (btn.hasClass('section-delete')){
					
					var answer = confirm ("Press OK to delete section or Cancel");
					if (answer) {
			            
						section.remove();
			            section.addClass('empty_column');
						
					}
					
				} else if (btn.hasClass('section-clone')){
				
					var cloned = section.clone( true )

					cloned
						.insertAfter(section)
						.hide()
						.fadeIn()

					// TODO make cloning work
				
				} else if (btn.hasClass('column-popup')){
					
					// Pop to top level
					
					var answer = confirm ("Press OK to pop (move) section to the top level or cancel.")
					
					if (answer)
						section.appendTo('.pl_main_sortable') //insertBefore('.wpb_main_sortable div.wpb_clear:last');
					
					
				} else if ( btn.hasClass('section-increase')){
					
					var sizes = $.pageBuilder.getColumnSize(section)

					if ( sizes[1] )
						section.removeClass( sizes[0] ).addClass( sizes[1] )

					
				} else if ( btn.hasClass('section-decrease')){

					var sizes = $.pageBuilder.getColumnSize( section )

					if (sizes[2])
						section.removeClass(sizes[0]).addClass(sizes[2]) // Switch for next class


				} else if ( btn.hasClass('section-offset-increase')){
					
					var sizes = $.pageBuilder.getOffsetSize( section )

					if (sizes[1])
						section.removeClass(sizes[0]).addClass(sizes[1])
						

				} else if ( btn.hasClass('section-offset-reduce')){

					var sizes = $.pageBuilder.getOffsetSize( section )

					if (sizes[1])
						section.removeClass(sizes[0]).addClass(sizes[2])
					

				} else if ( btn.hasClass('section-start-row') ){
				
					section.toggleClass('force_start_row')
					
				}
				
				$.pageBuilder.reloadConfig()
				
			})
		
		}
	
		, saveConfig: function(){

			$.pageBuilder.reloadConfig()
			
		}
		
        , reloadConfig: function() {
		
			$('.pl_sortable_area').each(function () {
				$.pageBuilder.alignGrid( this );
			});

        }
		, isAreaEmpty: function(area){
			var addTo = (area.hasClass('ecolumn-inner')) ? area.parent() : area
			
			if(!area.children(".pl_sortable").length)
			    addTo.addClass('empty-area')
			else 
			    addTo.removeClass('empty-area')
			
		}

        , alignGrid: function( area ) {
		
            var total_width = 0
            ,	width = 0
            ,	next_width = 0
			,	avail_offset = 0
			, 	sort_area = $(area)
			, 	len = sort_area.children(".pl_sortable").length
	
  			$.pageBuilder.isAreaEmpty( sort_area )

            sort_area.children(".pl_sortable").each(function ( index ) {
				
                var section = $(this)
				,	col_size = $.pageBuilder.getColumnSize( section )
				,	off_size = $.pageBuilder.getOffsetSize( section )
				
				
				// Deal with classes 
				section
					.removeClass("sortable_first sortable_last")
					.addClass("sortable_1st_level")
					.css('opacity', 1)
					.find('.pl_sortable')
						.removeClass("sortable_1st_level")
				
				if ( index == 0 )
					section.addClass("sortable_first")
				else if ( index === len - 1 ) 
					section.addClass("sortable_last")
					
				
				// Deal with width and offset
				width = col_size[4] + off_size[3]
				
				total_width += width
				
				avail_offset = 12 - col_size[4];
			
				if( avail_offset == 0 )
					section.addClass('no_offset')
				else 
					section.removeClass('no_offset')
			
				if(width > 12){
					avail_offset = 12 - col_size[4]; 
					section.removeClass( off_size[0] ).addClass( 'offset'+avail_offset )
					off_size = $.pageBuilder.getOffsetSize( section )
				}

               	// Set Numbers
				section.find(".section-size:first").html( col_size[3] )
				section.find(".offset-size:first").html( off_size[3] )
				
				if (total_width > 12 || section.hasClass('force_start_row')) {
					
                    section
						.addClass('sortable_first')
                    	.prev('.pl_sortable')
						.addClass("sortable_last")
						
                    total_width = width

                } 

            })

        } 

		, getOffsetSize: function( column ) {
			
			var max = 12
			,	sizes = $.pageBuilder.getColumnSize( column )
			,	avail = max - sizes[4]
			,	data = []

			for( i = 0; i <= 12; i++){

					next = ( i == avail ) ? 0 : i+1

					prev = ( i == 0 ) ? avail : i-1	

					if(column.hasClass("offset"+i))
						data = new Array("offset"+i, "offset"+next, "offset"+prev, i)

			}

			if(data.length === 0)
				return new Array("offset0", "offset0", "offset0", 0)
			else
				return data

		}
		

		, getColumnSize: function(column) {

			if (column.hasClass("span12")) //full-width
				return new Array("span12", "span2", "span10", "1/1", 12)

		    else if (column.hasClass("span10")) //five-sixth
		        return new Array("span10", "span12", "span9", "5/6", 10)

			else if (column.hasClass("span9")) //three-fourth
				return new Array("span9", "span10", "span8", "3/4", 9)

			else if (column.hasClass("span8")) //two-third
				return new Array("span8", "span9", "span6", "2/3", 8)

			else if (column.hasClass("span6")) //one-half
				return new Array("span6", "span8", "span4", "1/2", 6)

			else if (column.hasClass("span4")) // one-third
				return new Array("span4", "span6", "span3", "1/3", 4)

			else if (column.hasClass("span3")) // one-fourth
				return new Array("span3", "span4", "span2", "1/4", 3)

		    else if (column.hasClass("span2")) // one-sixth
		        return new Array("span2", "span3", "span12", "1/6", 2)

			else
				return false

		}

		
		
		

		, startDroppable: function(){
			
		    $('.pl_sortable_area').sortable({
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
				start: function(event, ui){
					$('#page').addClass('pl-dragging')
					$('.pl-section').effect('highlight', '#ff0000', 1000)
				}, 
				stop: function(event, ui){
					$('#page').removeClass('pl-dragging');
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
		                return false
		            }
		        },
				update: function() {
					$.pageBuilder.reloadConfig()
				}
				
		    })
		
		}
		
		, startResize: function(){
			// Resizable Content Area
			$('.pl-content').resizable({ 
				handles: "e, w",
				minWidth: 400,
				resize: function(event, ui) { 

					var resizeWidth = ui.size.width, 
						resizeOrigWidth = ui.originalSize.width, 
						resizeNewWidth = resizeOrigWidth + ((resizeWidth - resizeOrigWidth) * 2); 

					jQuery('.pl-content').css('left', 'auto').width(resizeNewWidth); 

				}
			});
			
		}
		
    }

	
	$.areaControl = {
	
        toggle: function(btn) {
		
			if(!jQuery.areaControl.isActive){
				
				$('body')
					.addClass('area-controls')
					.find('area-tag')
					.effect('highlight')
			
				btn.addClass('active')
			
				jQuery.areaControl.isActive = true
			
				jQuery.areaControl.listen()
				
			} else {
				btn.removeClass('active')
				jQuery.areaControl.isActive = false
				$('body').removeClass('area-controls')
				
			}
		
		}

		, listen: function() {
			$(".btn-area-down").on("click", function(e) {
				e.stopPropagation()
				$.areaControl.move($(this), 'down')
			});
			$(".btn-area-up").on("click", function(e) {
				e.stopPropagation()
				$.areaControl.move($(this), 'up')
			});
		} 
		
		, update: function() {
			$('.area-tag').each( function(index) {

				var num = index + 1

			    $(this).data('area-number', num).attr('data-area-number', num)

			})
		}
		
		, move: function( button, direction ){


			var iteration = (direction == 'up') ? -1 : 1
			,	currentArea = button.closest('.pl-area')
			,	areaNumber = currentArea.data('area-number')
			, 	moveAreaNumber = areaNumber + iteration
			,	moveArea = $("[data-area-number='"+moveAreaNumber+"']")



			if(moveArea.hasClass('pl-region-bar') && direction == 'up'){

				moveAreaNumber = moveAreaNumber + iteration

				moveArea = $("[data-area-number='"+moveAreaNumber+"']")

				if(direction == 'up'){
					moveArea
						.after( currentArea )
				} else {
					moveArea
						.before( currentArea )
				}

			} else {

				if(direction == 'up'){
					moveArea
						.before( currentArea )
				} else {
					moveArea
						.after( currentArea )
				}

			}

			currentArea.effect('highlight')

			$.areaControl.update()

		}
		
	}
	
	
}(window.jQuery);









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


