// PageLines Editor - Copyright 2013

!function ($) {
	
	// --> Initialize 
	$(document).ready(function() {

		$('.pl-inner').addClass('editor-row')
		
		$('.pl-area .pl-content .pl-inner').addClass('pl-sortable-area')

		$('.outline').addClass('pl-area-container')

		$('.pl-sortable-area .pl-section').addClass('pl-sortable')

		$.pageTools.startUp()

	})
    
	// Event Listening
	$.pageTools = {
		
		startUp: function(){
			
			$.pageBuilder.reloadConfig()
			
			this.theToolBox = $('body').toolbox()
			
			this.onStart()
			
			this.listener()
			
			
			
		}
		
		, listener: function() {
		
			that = this
			
			// Click event listener
			$(".btn-toolbox").on("click.toolboxHandle", function(e) {
				
				e.preventDefault()
				
				
				var btn = $(this)
				, 	btnAction = btn.data('action')
			
				if( btnAction == 'drag-drop' )
					that.stateInit(btnAction, function() { $.pageBuilder.show() }, function() { $.pageBuilder.hide() }, true)
				
				else if(btn.hasClass('btn-panel'))
					that.showPanel(btnAction)
				
				
			})
        }
		
		, showPanel: function( key ){
		
			var selectedPanel = $('.panel-'+key)
			, 	selectedTab = $('[data-action="'+key+'"]')
			, 	allPanels = $('.tabbed-set')
			
			$('body').toolbox('show')
			
			if(selectedPanel.hasClass('current-panel'))
				return
			
			$('.btn-toolbox').removeClass('active-tab')
			
			allPanels
				.removeClass('current-panel')
				.hide()
				
			$('.ui-tabs').tabs('destroy')
			

			// TODO needs to work w/ multiple tabbing
			selectedPanel.tabs({
				activate: function(event, ui){
					
					if(ui.newTab.attr('data-filter'))
						selectedPanel.find('.x-list').isotope({ filter: ui.newTab.attr('data-filter') })
					else if (ui.newTab.attr('data-flag') && ui.newTab.attr('data-flag') == 'custom-scripts'){
						var editor2 = CodeMirror.fromTextArea( $(".custom-scripts").get(0), {
							'lineNumbers': true
							,	'mode': 'text/x-less'
							, 	'lineWrapping': true
						})
					}
					
				}
			})
			
			selectedPanel
				.addClass('current-panel')
				.show()
		
			// Has to be after shown
			if( key == 'settings'){
				
				var config = {
					mode: 'settings'
					, sid: 'settings'
					, settings: $.pl.config.settings
				}
				$.optPanel.render( config )
				
			} else if (key == 'pl-design'){
				var editor = CodeMirror.fromTextArea( $(".custom-less").get(0), {
					'lineNumbers': true
					,	'mode': 'text/x-less'
					, 	'lineWrapping': true
					, 	onKeyEvent: function(instance, e){

						if(e.type == 'keydown' && e.which == 13 && (e.metaKey || e.ctrlKey) ){
							$('#pl-custom-less').text(instance.getValue())
						}

					}
				})
				
			} else if (key == 'pl-extend' ){
				
			//	$.xList.renderList( $('[data-panel="store"]'), $.pl.extend )
				
			}
		
			selectedTab.addClass('active-tab')
			
			$.xList.listStop()
			
			$.xList.listStart(selectedPanel, key)
			
		}

		, onStart: function(){
			
			this.stateInit('drag-drop', function() { $.pageBuilder.show() })
		}
		
		, stateInit: function( key, call_on_true, call_on_false, toggle ){
			
			var localState = ( localStorage.getItem( key ) )
			,	theState = (localState == 'true') ? true : false
			 
			
			if( toggle ){
				theState = (theState) ? false : true;
				localStorage.setItem( key, theState )
			}
			
			if (!theState){
					
				$('[data-action="'+key+'"]').removeClass('active-tab')	
					
				if($.isFunction(call_on_false))
					call_on_false.call( key )
			}
			
			if (theState){
				
				$('[data-action="'+key+'"]').addClass('active-tab')
					
				if($.isFunction(call_on_true))
					call_on_true.call( key )
			}
				
				
			
			
		}

	
	}
	
	$.xList = {
		
		renderList: function( panel, list ){
			var items = ''
		
			// console.log(list)
			// return
			$.each( list , function(index, l) {
			
				items += sprintf('<div class="x-item %s"><div class="x-item-frame"><img src="%s" /></div></div>', l.class, l.thumb)
			})
			
			output = sprintf('<div class="x-list">%s</div>', items)
		
			panel.find('.panel-tab-content').html( output )
			
			
		}
		
		, listStart: function( panel, key ){
		
			var layout = (key == 'pl-extend') ? 'masonry' : 'fitRows'; 
			
			panel.find('.x-list').isotope({
				itemSelector : '.x-item'
				, layoutMode : layout
			})
			
			if(key == 'add-new')
				this.makeDraggable(panel)
				
		}
		
		, makeDraggable: function(panel){
			
			list = this
		
			panel.find( '.x-item' ).draggable({
				revert: "invalid"
				, appendTo: "body"
				, helper: "clone"
				, cursor: "move" 
				, connectToSortable: ".pl-sortable-area"
				, start: function(event, ui){
				
					list.switchOnAdd(ui.helper)
					ui.helper
						.css('max-width', '300px')
						.css('height', 'auto')
				}
			})
		
			
		}
		, listStop: function(){
		 	$('.x-list.isotope').isotope( 'destroy' )
		}
		
		, switchOnAdd: function( element ){
			
			
			var name = element.data('name')
			, 	image = element.data('image')
			, 	imageHTML = sprintf('<div class="banner-frame"><img class="section-thumb" src="%s" /></div>', image )
			, 	text = sprintf('<h3 class="banner-title">%s</h3>', name )
			, 	refresh = '<div class="banner-refresh" style="display: none;"><a href="#" class="btn btn-info"><i class="icon-undo"></i> Refresh Page To Load</a></div>'
			, 	theHTML = sprintf('<div class="pl-refresh-banner">%s %s %s</div>', imageHTML, text, refresh)
			
			element
				.removeAttr("style")
				.html(theHTML)
				
		}
		
	}

	// Page Drag/Drop Builder
    $.pageBuilder = {
		// 
		// onStart: function(){
		// 
		// 	var localState = ( localStorage.getItem( 'plDragDrop' ) )
		// 	,	theState = (localState == 'true') ? true : false
		// 	
		// 	if(theState)
		// 		$.pageBuilder.show()
		// 	
		// }
	
		toggle: function( ){
			
			var localState = ( localStorage.getItem( 'drag-drop' ) )
			,	theState = (localState == 'true') ? true : false
		
			if( !theState ){
				
				theState = true 
				
				$.pageBuilder.show()
				
			} else {
				
				theState = false
			 
				$.pageBuilder.hide()
					
			}
			
			localStorage.setItem( 'drag-drop', theState )
				
		}
		
		, show: function() {
			
			// Graphical Flare
			$('.pl-sortable').effect('highlight', 1500)
			$('[data-action="drag-drop"]').addClass('active')
			
			// Enable CSS
			$('body').addClass('drag-drop-editing')
		
			// JS
			$.pageBuilder.startDroppable()
			
			$.pageBuilder.reloadConfig()
			
			$.pageBuilder.sectionControls()
			
			$.areaControl.toggle($(this))
			
			$.widthResize.startUp()
			
			
			
		}
		
		, hide: function() {
			
			$('body').removeClass('drag-drop-editing')
		
			$('[data-action="drag-drop"]').removeClass('active')
	
			$('.s-control')
				.off('click.sectionControls')
				
			$.areaControl.toggle($(this))
			
			$.widthResize.shutDown()
			
		}
		
		, sectionControls: function() {
			
			$('.s-control').on('click.sectionControls', function(e){
		
				e.preventDefault()
			
				var btn = $(this)
				,	section = btn.closest(".pl-sortable")
				,	config	= {
						sid: section.data('sid')
						, sobj: section.data('object')
						, clone: section.data('clone')
					}
			
				if(btn.hasClass('section-edit')){
					
					// TODO Open up and load options panel
					
					$('body').toolbox({
						action: 'show'
						, panel: 'section-options'
						, info: function(){
						
							$.optPanel.render( config )
						
						}
					})
					
				} else if (btn.hasClass('section-delete')){
					
					var answer = confirm ("Press OK to delete section or Cancel");
					if (answer) {
			            
						section.remove();
			            section.addClass('empty-column');
						
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
				
					section.toggleClass('force-start-row')
					
				}
				
				$.pageBuilder.reloadConfig()
				
			})
		
		}
	
		, saveConfig: function(){

			$.pageBuilder.reloadConfig()
			
		}
		
        , reloadConfig: function() {
		
			$('.pl-sortable-area').each(function () {
				$.pageBuilder.alignGrid( this )
			})
			
			$.pageBuilder.storeConfig( );

        }

		, storeConfig: function() {
			
			var map = {}
			
			$('.pl-region').each( function(regionIndex, o) {
				
				var region = $(this).data('region')
				, 	areaConfig = []
				
				$(this).find('.pl-area').each( function(areaIndex, o2) {
					
					var area = $(this)
					,	areaContent	= []
					, 	areaSet = {}
				
					$(this).find('.pl-section').each( function(sectionIndex, o3) {

						var section = $(this)
						, 	set = {}
						
						set.object = section.data('object')
						set.clone = section.data('clone')
						set.sid = section.data('sid')
						set.span = $.pageBuilder.getColumnSize( section )[ 4 ]
						set.offset = $.pageBuilder.getOffsetSize( section )[ 3 ]
						
						areaContent.push(set)

					})
					
					areaSet = {
						area: ''
						, content: areaContent
					}
				
					areaConfig.push(areaSet)
					
				})
				
				map[region] = areaConfig
				
			})
			
			$.pl.map = map
			
		
		}

		, isAreaEmpty: function(area){
			var addTo = (area.hasClass('pl-column-sortable')) ? area.parent() : area
			
			if(!area.children(".pl-sortable").not('.ui-sortable-helper').length)
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
			, 	len = sort_area.children(".pl-sortable").length
	
  			this.isAreaEmpty( sort_area )

            sort_area.children(".pl-sortable").each( function ( index ) {
				
                var section = $(this)
				,	col_size = $.pageBuilder.getColumnSize( section )
				,	off_size = $.pageBuilder.getOffsetSize( section )
				
				
				if(sort_area.hasClass('pl-column-sortable')){
				
					if(section.hasClass('sortable-1st-level')){
						section
							.removeClass('sortable-1st-level')
							.removeClass(col_size[0])
							.removeClass(off_size[0])
							.addClass('span12 offset0')
							
						col_size = this.getColumnSize( section, true )
						off_size = this.getOffsetSize( section, true )
					}
					
				} else {
					
					section
						.addClass("sortable-1st-level")
					
				}
				
				// First/last spacing
				section
					.removeClass("sortable-first sortable-last")
					
				if ( index == 0 )
					section.addClass("sortable-first")
				else if ( index === len - 1 ) 
					section.addClass("sortable-last")
					
				
				// Deal with width and offset
				width = col_size[4] + off_size[3]
				
				total_width += width
				
				avail_offset = 12 - col_size[4];
			
				if( avail_offset == 0 )
					section.addClass('cant-offset')
				else 
					section.removeClass('cant-offset')
			
				if(width > 12){
					avail_offset = 12 - col_size[4]; 
					section.removeClass( off_size[0] ).addClass( 'offset'+avail_offset )
					off_size = $.pageBuilder.getOffsetSize( section )
				}

               	// Set Numbers
				section.find(".section-size:first").html( col_size[3] )
				section.find(".offset-size:first").html( off_size[3] )
				
				if (total_width > 12 || section.hasClass('force-start-row')) {
					
                    section
						.addClass('sortable-first')
                    	.prev('.pl-sortable')
						.addClass("sortable-last")
						
                    total_width = width

                } 

            })

        } 

		, getOffsetSize: function( column, defaultValue ) {
			
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

			if(data.length === 0 || defaultValue)
				return new Array("offset0", "offset0", "offset0", 0)
			else
				return data

		}
		

		, getColumnSize: function(column, defaultValue) {

			if (column.hasClass("span12") || defaultValue) //full-width
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
						.toolbox('hide')
					
					$('.pl-section')
						.effect('highlight', '#ff0000', 1000)
					
					if(ui.item.hasClass('x-item'))
						$.xList.switchOnAdd(ui.item)
					
				} 
				, stop: function(event, ui){
				
					$('body')
						.removeClass('pl-dragging')
					
					ui.item.find('.banner-refresh').fadeIn('slow')
					
				}
				
				, over: function(event, ui) {
					
		           $( "#droppable" ).droppable( "disable" )
		
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
		
		
    }

	$.widthResize = {
		
		startUp: function(){
			
			var	widthSel = $('.pl-content')
			
			$('body').addClass('width-resize')
	
			


			widthSel.resizable({ 
				handles: "e, w",
				minWidth: 400,
				start: function(event, ui){
					$('body').addClass('width-resizing')
				}
				, stop: function(event, ui){
					$('body').removeClass('width-resizing')
				}
				, resize: function(event, ui) { 

					var resizeWidth = ui.size.width
					,	resizeOrigWidth = ui.originalSize.width
					,	resizeNewWidth = resizeOrigWidth + ((resizeWidth - resizeOrigWidth) * 2)

					widthSel
						.css('left', 'auto')
						.css('height', 'auto')
						.width(resizeNewWidth)

				}
			})
			
			$('.ui-resizable-handle')
				.effect('highlight', 2500 )
				.hover(
					function () {
						$('body').addClass("resize-hover")
					}
					, function () {
						$('body').removeClass("resize-hover")
					}
				)
			
		}
		, shutDown: function(){
			
			var	widthSel = $('.pl-content')
			
			$('body').removeClass('width-resize')
			
			$(".ui-resizable-handle").unbind('mouseenter mouseleave')
			
			widthSel.resizable( "destroy" )
			
			
			
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

