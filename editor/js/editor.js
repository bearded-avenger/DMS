
// On document ready stuff
jQuery(document).ready(function() {
	
	// Disable Text Selector on Drag
	document.onselectstart = function () { return false };

	// Basic Setup
	jQuery('body').addClass('pl-editor')
	jQuery('.pl-inner').addClass('editor-row')
	
	// Adds class for drag/dropping content sections
	jQuery('.pl-area .pl-content .pl-inner').addClass('pl-sortable-area')
	
	// Adds class for drag/dropping areas
	jQuery('.outline').addClass('pl_area_container')
	
	jQuery('.pl-sortable-area .pl-section').addClass('pl_sortable')
	
//	jQuery.pageBuilder.startResize(); // Layout resize	
	
	
	jQuery.pageTools.startUp()

});


		

!function ($) {
    
	// Event Listening
	$.pageTools = {
		
		startUp: function(){
			
			$.pageBuilder.reloadConfig()
			
			$.pageBuilder.onStart()
			
			var theToolBox = $('body').toolbox()
			
			this.listener()
			
			
			
		}
		
		, listener: function() {
		
			// Click event listener
			$(".btn-toolbox").on("click.toolBar", function(e) {
				
				e.stopPropagation()
				
				var btn = $(this)
				, 	btnAction = btn.data('action')
			
				if( btnAction == 'drag-drop' )
					$.pageBuilder.toggle()
				
			})
        }

	
	}

	// Page Drag/Drop Builder
    $.pageBuilder = {

		onStart: function(){
		
			var localState = ( localStorage.getItem( 'plDragDrop' ) )
			,	theState = (localState == 'true') ? true : false
			
			if(theState)
				$.pageBuilder.show()
			
		}
	
		, toggle: function( ){
			
			var localState = ( localStorage.getItem( 'plDragDrop' ) )
			,	theState = (localState == 'true') ? true : false
		
			if( !theState ){
				
				theState = true 
				
				$.pageBuilder.show()
				
			} else {
				
				theState = false
			 
				$.pageBuilder.hide()
					
			}
			
			localStorage.setItem( 'plDragDrop', theState )
				
		}
		
		, show: function() {
			
			// Graphical Flare
			$('.pl_sortable').effect('highlight', 1500)
			$('[data-action="drag-drop"]').addClass('active')
			
			// Enable CSS
			$('body').addClass('drag-drop-editing')
		
			// JS
			$.pageBuilder.startDroppable()
			
			$.pageBuilder.reloadConfig()
			
			$.pageBuilder.sectionControls()
			
			$.areaControl.toggle($(this))
			
		}
		
		, hide: function() {
			
			$('body').removeClass('drag-drop-editing')
		
			$('[data-action="drag-drop"]').removeClass('active')
	
			$('.s-control')
				.off('click.sectionControls')
				
			$.areaControl.toggle($(this))
			
		}
		
		, sectionControls: function() {
			
			$('.s-control').on('click.sectionControls', function(e){
		
				e.preventDefault()
			
				var btn = $(this)
				,	section = btn.closest(".pl_sortable")
			
				if(btn.hasClass('section-edit')){
					
					// TODO Open up and load options panel
					
					$('body').toolbox({
						action: 'show'
						, panel: function(){
						
							$.optPanel.render()
						
						}
					})
					
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
		
			$('.pl-sortable-area').each(function () {
				$.pageBuilder.alignGrid( this );
			});

        }

		, isAreaEmpty: function(area){
			var addTo = (area.hasClass('pl-column-sortable')) ? area.parent() : area
			
			if(!area.children(".pl_sortable").not('.ui-sortable-helper').length)
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
			
		
			
		    $('.pl-sortable-area').sortable({
			
		        items: 	".pl-section"
				,	placeholder: "pl-placeholder"
				,	connectWith: ".pl-sortable-area"
				,	forcePlaceholderSize: true
		        ,	tolerance: "pointer"		// basis for calculating where to drop
				,	helper: 	"clone" 		// needed or positioning issues ensue
				,	scrollSensitivity: 200
				,	scrollSpeed: 40
		        ,	cursor: "move"
				,	distance: 0.5
				,	delay: 100
				
				, start: function(event, ui){
					$('body')
						.addClass('pl-dragging')
					
					$('.pl-section')
						.effect('highlight', '#ff0000', 1000)
			
				} 
				, stop: function(event, ui){
				
					$('body')
						.removeClass('pl-dragging')
				
				}
				
				, over: function(event, ui) {
		           
					ui.placeholder.css({
						maxWidth: ui.placeholder.parent().width()
					})
		           
		 			ui.placeholder.removeClass('hidden-placeholder')
		
		            if( ui.item.hasClass('section-plcolumn') && ui.placeholder.parent().parent().hasClass('section-plcolumn')) {
		                ui.placeholder.addClass('hidden-placeholder')
		            }

		        }
				, beforeStop: function(event, ui) {
					
		            if( ui.item.hasClass('section-plcolumn') && ui.placeholder.parent().parent().hasClass('section-plcolumn') ) {
		                return false
		            }
		
		        }
				, update: function() {
					$.pageBuilder.reloadConfig()
				}
				
		    })
		
			$('.pl-sortable-area').droppable({
				greedy: true
				,	accept: ".pl-section"
				,	hoverClass: "wpb_ui-state-active"
				,	drop: function( event, ui ) {
						jQuery.pageBuilder.reloadConfig();
					}
			})

			$('.pl-column-sortable').droppable({
			    greedy: true
			    ,	accept: function( dropable_el ) {
				
			        if ( dropable_el.hasClass('dropable_el') && jQuery(this).hasClass('ui-droppable') && dropable_el.hasClass('not_dropable_in_third_level_nav') )
			            return false;
			        else if ( dropable_el.hasClass('dropable_el') == true )
			            return true;
			
			    }
			    ,	hoverClass: "wpb_ui-state-active"
			    ,	over: function( event, ui ) {
			        	jQuery(this).parent().addClass("wpb_ui-state-active");
			    	}
				,	out: function( event, ui ) {
			        	jQuery(this).parent().removeClass("wpb_ui-state-active");
			    	}
				,	drop: function( event, ui ) {
			        	//console.log(jQuery(this));
			        	jQuery(this).parent().removeClass("wpb_ui-state-active");
			        	getElementMarkup(jQuery(this), ui.draggable, "addLastClass");
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


