// PageLines Editor - Copyright 2013

!function ($) {
	
	// --> Initialize 
	$(document).ready(function() {

		$('.pl-sortable-area .pl-section').addClass('pl-sortable')

		$.pageTools.startUp()

	})
    
	// Event Listening
	$.pageTools = {
		
		startUp: function(){
			
			$.pageBuilder.reloadConfig( 'start' )

			this.theToolBox = $('body').toolbox()
			
			$.pageBuilder.showEditingTools() 
			
			$.plAJAX.init() 
			
			$.plTemplates.init()
			
			this.bindUIActions()
				
		}
		
		, bindUIActions: function() {
		
			that = this
			
			// Click event listener
			$(".btn-toolbox").on("click.toolboxHandle", function(e) {
				
				e.preventDefault()
				
				
				var btn = $(this)
				, 	btnAction = btn.data('action')
			
				if( btnAction == 'drag-drop' ){
					$.pageBuilder.showEditingTools()
				
				} else if( btn.hasClass('btn-panel') )
					that.showPanel(btnAction)
				
				
			})
			
			$(".btn-action").on("click.actionButton", function(e) {
			
				e.preventDefault()
		
				var btn = $(this)
				, 	btnAction = btn.data('action')
				
				if( btnAction == 'pl-toggle' )
					$.plAJAX.toggleEditor( btnAction )

				if( btnAction == 'toggle_grid' )
					that.toggleGrid()
				
			})
			
		
        }

		, toggleGrid: function(){
			
			if($('body').hasClass('drag-drop-editing')){
				$('body').removeClass('drag-drop-editing width-resize')
			} else 
				$('body').addClass('drag-drop-editing width-resize')
		}
		
		, showPanel: function( key ){
		
			var selectedPanel = $('.panel-'+key)
			, 	selectedTab = $('[data-action="'+key+'"]')
			, 	allPanels = $('.tabbed-set')
			
			$('body')
				.toolbox('show')
			
			store.set('toolboxPanel', key)
			
			if(selectedPanel.hasClass('current-panel'))
				return
			
			$('.btn-toolbox').removeClass('active-tab')
			
			allPanels
				.removeClass( 'current-panel' )
				.hide()
				
			$('.ui-tabs').tabs('destroy')
		
			selectedPanel.tabs({
				create: function(event, ui){
					
					selectedPanel.find('.tabs-nav li').on('click.panelTab', function(){
						var theIsotope = selectedPanel.find('.isotope')
						,	removeItems = $('.x-remove')
						
						if( $(this).data('filter') )
							theIsotope.isotope({ filter: $(this).data('filter') }).isotope('remove', removeItems).removeClass('storefront-mode')
						
					})
				}
				, activate: function(e, ui){
					
					var theTab = ui.newTab
					, 	tabAction = theTab.attr('data-tab-action') || ''
					,	tabPanel = $("[data-panel='"+tabAction+"']")
					,	tabFlag = theTab.attr('data-flag') || ''
					
					if (tabFlag == 'custom-scripts'){
						
						
						$.plCode.activateScripts()
						
					} else if ( tabFlag == 'link-storefront' ){
						
						e.preventDefault()
						
						$('.btn-pl-extend')
							.trigger('click')
					
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
				
			}
			else if( key == 'area_settings'){
				areaID = store.get('lastAreaConfig')
				
				if(areaID != ''){
					$.areaControl.areaPanelRender(areaID)
				} else {
					$('body')
						.toolbox('hide')
				}
				
				
				
				
			}
			else if( key == 'live'){
				
				var liveFrame = '<div class="live-wrap"><iframe class="live_chat_iframe" src="http://pagelines.campfirenow.com/6cd04"></iframe></div>'

				selectedPanel
					.find('.panel-tab-content')
					.html(liveFrame)
				
			}
			
			 else if (key == 'pl-design'){
				$.plCode.activateLESS()
				
			} else if (key == 'section-options'){
				
				$('body').toolbox({
					action: 'show'
					, panel: key
					, info: function(){
					
						$.optPanel.render( config )
					
					}
				})
				
			} 
		
			selectedTab.addClass('active-tab')
			
			$.xList.listStop()
			
			$.xList.listStart(selectedPanel, key)
			
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

	// Page Drag/Drop Builder
    $.pageBuilder = {

		toggle: function( ){
			
			var localState = ( localStorage.getItem( 'drag-drop' ) )
			,	theState = (localState == 'true') ? true : false
		
			if( !theState ){
				
				theState = true 
				
				$.pageBuilder.showEditingTools()
				
			} else {
				
				theState = false
			 
				$.pageBuilder.hide()
					
			}
			
			localStorage.setItem( 'drag-drop', theState )
				
		}
		
		, showEditingTools: function() {
			
			// Graphical Flare
			$('[data-action="drag-drop"]').addClass('active')
			
			// Enable CSS
			$('body').addClass('drag-drop-editing')
		
			// JS
			$.pageBuilder.startDroppable()
			
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
		
		, handleCloneData: function( cloned ){
			
			var config	= {
					sid: cloned.data('sid')
					, sobj: cloned.data('object')
					, clone: cloned.data('clone')
				}
			,	clonedSet = ($.pl.config.opts[config.sid] && $.pl.config.opts[config.sid].opts) || {}
			, 	mode = ($.pl.config.isSpecial) ? 'type' : 'local'


			var i = 0
			while ( $( '.section-'+config.sid+'[data-clone="'+i+'"]' ).length != 0) {
			    i++
			}

			cloned
				.attr('data-clone', i)
				.data('clone', i)

			// add clone icon
			cloned.first('.section-controls').find('.title-desc').html(sprintf(" <i class='icon-copy'></i> %s", i))

			console.log(config.clone)
			// set cloned item settings to new clone local settings
			$.each(clonedSet, function(index, opt){
				if( opt.type == 'multi'){
					$.each( opt.opts, function(index2, opt2){

						if( plIsset( $.pl.data.local[opt2.key]) ){
							$.pl.data.local[opt2.key][i] = $.pl.data.local[opt2.key][config.clone]
						}

					})
				} else {

					if( plIsset($.pl.data.local[opt.key]) ){
						$.pl.data.local[opt.key][i] = $.pl.data.local[opt.key][config.clone]
					}

				}
			})

			// save settings data
			$.plAJAX.saveData( 'draft' )
		
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
			            section.addClass('empty-column')
						store.remove('toolboxShown')
						
					}
					
				} else if (btn.hasClass('section-clone')){
				
					var	cloned = section.clone( true )
					
					cloned
						.insertAfter(section)
						.hide()
						.fadeIn()
						
					$.pageBuilder.handleCloneData( cloned )
					
					
				} else if (btn.hasClass('column-popup')){
					
					// Pop to top level
					
					var answer = confirm ("Press OK to pop (move) section to the top level or cancel.")
					
					if (answer)
						section.appendTo('.pl_main_sortable') //insertBefore('.wpb_main_sortable div.wpb_clear:last');
					
					
				} else if ( btn.hasClass('section-increase')){
					
					var sizes = $.plMapping.getColumnSize(section)

					if ( sizes[1] )
						section.removeClass( sizes[0] ).addClass( sizes[1] )

					
				} else if ( btn.hasClass('section-decrease')){

					var sizes = $.plMapping.getColumnSize( section )

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
				
				$.pageBuilder.reloadConfig( 'section-control' )
				
			})
		
		}

	
		
        , reloadConfig: function( source ) {
	
			console.log(source)
			
			$('.editor-row').each(function () {
				$.pageBuilder.alignGrid( this )
			})
			
			if( source !== 'start' )
				$.pageBuilder.storeConfig()
			
        }

		, alignGrid: function( area ) {
		
            var that = this
			,	total_width = 0
            ,	width = 0
            ,	next_width = 0
			,	avail_offset = 0
			, 	sort_area = $(area)
			, 	len = sort_area.children(".pl-sortable").length
			
  			that.isAreaEmpty( sort_area )

            sort_area.children(".pl-sortable:not(.pl-sortable-buffer)").each( function ( index ) {
				
                var section = $(this)
				,	col_size = $.plMapping.getColumnSize( section )
				,	off_size = $.plMapping.getOffsetSize( section )
				
				
				if(sort_area.hasClass('pl-sortable-column')){
				
					if(section.hasClass('level1')){
						section
							.removeClass('level1')
							.removeClass(col_size[0])
							.removeClass(off_size[0])
							.addClass('span12 offset0 level2')
							
						col_size = $.plMapping.getColumnSize( section, true )
						off_size = $.plMapping.getOffsetSize( section, true )
					} else {
						section
							.addClass('level2')
					}
					
				} else {
					
					section
						.removeClass("level2")
						.addClass("level1")
					
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
		
		, storeConfig: function( interrupt ) {
			
			var that = this
			, 	interrupt = interrupt || false
			,	map = $.plMapping.getCurrentMap()
			
			$.pl.map = map
			
			$.plAJAX.ajaxSaveMap( map, interrupt )
			
			return map
			
		
		}
		


	
		, isAreaEmpty: function(area){
			var addTo = (area.hasClass('pl-sortable-column')) ? area.parent() : area
			
			if(!area.children(".pl-sortable").not('.ui-sortable-helper').length)
			    addTo.addClass('empty-area')
			else 
			    addTo.removeClass('empty-area')
			
		}

		

		, startDroppable: function(){
			
			var that = this
			,	sortableArgs = {}
			
		
			
			$( '.section-plcolumn' ).on('mousedown', function(e){
				$('.section-plcolumn .pl-sortable-area').sortable( "disable" )
				$( '.section-plcolumn .pl-section' ).removeClass('pl-sortable')
			}).on('mouseup', function(e){
				$('.section-plcolumn .pl-sortable-area').sortable( "enable" )
				$( '.section-plcolumn .pl-section' ).addClass('pl-sortable')
			})
			
		    $( '.pl-sortable-area' ).sortable( that.sortableArguments( 'section' ) ) 
		
		
			// AREA drag and drop
			$( '.pl-area-container' ).sortable( that.sortableArguments( 'area' ) )
		

			
		
		}
		
		, sortableArguments: function( type ){
			
			var that = this
			,	type = type || 'section'
			,	sortableSettings = {}
			,	items = ( type == 'section' ) ? '.pl-sortable' : '.pl-area'
			,	container = ( type == 'section' ) ? '.pl-sortable-area' : '.pl-area-container'
			,	placeholder = ( type == 'section' ) ? 'pl-placeholder' : 'pl-area-placeholder'
			
				sortableSettings = {
				       	items: 	items
					,	connectWith: container
					,	placeholder: placeholder
					,	forcePlaceholderSize: true
			        ,	tolerance: "pointer"		// basis for calculating where to drop
					,	helper: 	"clone" 		// needed or positioning issues ensue
					,	scrollSensitivity: 50
					,	scrollSpeed: 40
			        ,	cursor: "move"
					,	distance: 3
					,	delay: 100

					, start: function(event, ui){
						
						$('body')
							.addClass('pl-dragging')
							.toolbox('hide')

						if( ui.item.hasClass('x-item') )
							$.plSections.switchOnAdd(ui.item)

						// allows us to change sizes when dragging starts, while keeping good dragging
						$( this ).sortable( "refreshPositions" ) 

						
						// Prevents double nesting columns and other recursion bugs. 
						// Remove all drag and drop elements and disable sortable areas within columns if 
						// the user is dragging a column
						if( ui.item.hasClass('section-plcolumn') ){

							$( '.section-plcolumn .pl-sortable-column' ).removeClass('pl-sortable-area ui-sortable')
							$( '.section-plcolumn .pl-section' ).removeClass('pl-sortable')

							$( '.ui-sortable' ).sortable( 'refresh' )

						}
						
					} 
					, stop: function(event, ui){

						$('body')
							.removeClass('pl-dragging')

						// when new sections are added
						ui.item.find('.banner-refresh').fadeIn('slow')

						if( ui.item.hasClass('section-plcolumn') ){

							$( '.section-plcolumn .pl-sortable-column' ).addClass('pl-sortable-area ui-sortable')
							$( '.section-plcolumn .pl-section' ).addClass('pl-sortable')

							$( '.ui-sortable' ).sortable( 'refresh' )

						}

						if(ui.item.hasClass('x-item'))
							$.plSections.switchOnStop(ui.item)

					}

					, over: function(event, ui) {

			           $( "#droppable" ).droppable( "disable" )

						ui.placeholder.css({
							maxWidth: ui.placeholder.parent().width()
						})

			        }
					, update: function() {
						that.reloadConfig( 'update-sortable' )
					}
				}
			
			return sortableSettings
		}
		
		
		
		
		
		
		
    }

	
	
	
	
}(window.jQuery);

