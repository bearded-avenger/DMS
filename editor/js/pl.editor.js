// PageLines Editor - Copyright 2013

!function ($) {
	 

	// --> Initialize
	$(document).ready(function() {

		$('.pl-sortable-area')
			.addClass('editor-row')
			.find('.pl-section')
			.addClass('pl-sortable')

		$(".dropdown-toggle").dropdown()

		$.pageTools.startUp()
		
		
		$.plHotKeys.init()

	})
	
	$.plHotKeys = {
		init: function(){
			
			
			$.jQee('alt+a', function() {

				$('[data-action="toggle-grid"]')
					.trigger('click')
			})
			
			$.jQee('alt+s', function() {

				$('[data-mode="publish"]')
					.trigger('click')
			})
			
		}
	}

	// Event Listening
	$.pageTools = {

		startUp: function(){

			$.pageBuilder.reloadConfig({ location: 'start', storeMap: false})

			this.theToolBox = $('body').toolbox()

			$.pageBuilder.showEditingTools()

			$.plAJAX.init()

			$.plTemplates.init()

			this.bindUIActions()
			
			that.toggleGrid( true )


		}

		, bindUIActions: function() {

			that = this

			$('.btn-toolbox').tooltip({placement: 'top', delay: { show: 1000 }})
			// Click event listener
			$(".btn-toolbox").on("click.toolboxHandle", function(e) {

				e.preventDefault()


				var btn = $(this)
				, 	btnAction = btn.data('action')

				if( btnAction == 'drag-drop' )
					$.pageBuilder.showEditingTools()
				else if( btn.hasClass('btn-panel') )
					that.showPanel(btnAction)
				else if( btn.hasClass('btn-toggle-grid') )
					that.toggleGrid( )
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

		, toggleGrid: function( load, action){

			var load = load || false
			,	action = action || 'toggle'
			
			
			if( action != 'show' 
				&& (
					(!load && $('body').hasClass('drag-drop-editing')) 
					|| (load && store.get('plPagePreview') == true)
				)
			){
				$('[data-action="toggle-grid"]').addClass('active-tab')
				$('body').removeClass('drag-drop-editing width-resize')
				store.set('plPagePreview', true)
			} else {
				$('[data-action="toggle-grid"]').removeClass('active-tab')
				$('body').addClass('drag-drop-editing width-resize')
				store.set('plPagePreview', false)
			}

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

			$('.btn-toolbox:not(.btn-toggle-grid)').removeClass('active-tab')

			allPanels
				.removeClass( 'current-panel' )
				.hide()

			$('.pl-toolbox .ui-tabs').tabs('destroy')

			
	
			selectedPanel.tabs({
				create: function(event, ui){

					selectedPanel.find('.tabs-nav li').on('click.panelTab', function(){
						var theIsotope = selectedPanel.find('.isotope')
						,	removeItems = $('.x-remove')

						if( $(this).data('filter') ){
							theIsotope
								.isotope({ filter: $(this).data('filter') })
								.isotope('remove', removeItems)
								.removeClass('x-pane-mode')
						}


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
		
		, setElementDelete: function( deleted ){
			
			var uniqueID = deleted.data('clone')
			
			deleted.find("[data-clone]").each(function(){
				$.pageBuilder.setElementDelete( $(this) )
			})
			
			// recursive
			deleted.remove()

			if( plIsset($.pl.data.local[ uniqueID ]) )
				delete $.pl.data.local[ uniqueID ]
				
			if( plIsset($.pl.data.type[ uniqueID ]) )
				delete $.pl.data.type[ uniqueID ]
				
			if( plIsset($.pl.data.global[ uniqueID ]) )
				delete $.pl.data.global[ uniqueID ]
		}
		
		, handleCloneData: function( cloned ){

			var newUniqueID = $.pageBuilder.setCloneData( cloned ) // recursive function
			
			return newUniqueID

		}
		
		, setCloneData: function( cloned ){
			
			var oldUniqueID = cloned.data('clone')
			, 	newUniqueID = plUniqueID()
			
			// Recursion
			cloned.find("[data-clone]").each(function(){
				$.pageBuilder.setCloneData( $(this) )
			})
			
			// Set element meta for mapping
			cloned
				.attr('data-clone', newUniqueID)
				.data('clone', newUniqueID)
				.find('.tooltip')
					.removeClass('in')

			var globalDat 	= (plIsset( $.pl.data.global[ oldUniqueID ] )) ? $.pl.data.global[ oldUniqueID ] : ''
			,	typeDat 	= (plIsset( $.pl.data.type[ oldUniqueID ])) ? $.pl.data.type[ oldUniqueID ] : ''
			,	localDat 	= (plIsset( $.pl.data.local[ oldUniqueID ])) ? $.pl.data.local[ oldUniqueID ] : ''
			,	theOpts 	= (plIsset( $.pl.config.opts[ oldUniqueID ])) ? $.pl.config.opts[ oldUniqueID ] : ''

			// Copy and move around meta data
			$.pl.data.global[ newUniqueID ] = $.extend({}, globalDat) // must clone the element, not just assign as they stay connected
				
			$.pl.data.type[ newUniqueID ] 	= $.extend({}, typeDat) // must clone the element, not just assign as they stay connected
			
			$.pl.data.local[ newUniqueID ] 	= $.extend({}, localDat) // must clone the element, not just assign as they stay connected
			
			$.pl.config.opts[ newUniqueID ] = theOpts
			
			return newUniqueID
		}

		, sectionControls: function() {

			$('.s-control').tooltip({placement: 'top'})
			$('.s-control').on('click.sectionControls', function(e){

				e.preventDefault()

				var btn = $(this)
				,	section = btn.closest(".pl-section")
				,	scope = ( section.parents(".template-region-wrap").length == 1 ) ? 'local' : 'global'
				,	config	= {
						sid: section.data('sid')
						, sobj: section.data('object')
						, clone: section.data('clone')
						, uniqueID: section.data('clone')
						, scope: scope
					}
				,	storeData = true
					

				if(btn.hasClass('section-edit')){
					
					storeData = false
					// TODO Open up and load options panel
					
					// This is needed so we can control which scope tabs are shown
					$('[data-key="section-options"]')
						.data('section-uid', config.uniqueID)
						.attr('data-section-uid', config.uniqueID)
						
					$('body').toolbox({
						action: 'show'
						, panel: 'section-options'
						, info: function(){

							$.optPanel.render( config )

						}
					})

				} else if (btn.hasClass('section-delete')){

					storeData = false
					
					bootbox.confirm("<h3>Are you sure?</h3><p>This will remove this section and its settings from this page.</p>", function( result ){

						if(result == true){
							$.pageBuilder.setElementDelete( section ) // recursive function

							$.pageBuilder.reloadConfig( {location: 'section-delete'} )
					
						} 

					})

				} else if (btn.hasClass('section-clone')){

					var	cloned = section.clone( true )

					cloned
						.insertAfter(section)
						.hide()
						.fadeIn()

					$.pageBuilder.handleCloneData( cloned )


				} else if ( btn.hasClass('section-increase')){

					var sizes = $.plMapping.getColumnSize(section)

					if ( sizes[1] )
						section.removeClass( sizes[0] ).addClass( sizes[1] )


				} else if ( btn.hasClass('section-decrease')){

					var sizes = $.plMapping.getColumnSize( section )

					if (sizes[2])
						section.removeClass(sizes[0]).addClass(sizes[2]) // Switch for next class


				} else if ( btn.hasClass('section-offset-increase')){

					var sizes = $.plMapping.getOffsetSize( section )

					if (sizes[1])
						section.removeClass(sizes[0]).addClass(sizes[1])


				} else if ( btn.hasClass('section-offset-reduce')){

					var sizes = $.plMapping.getOffsetSize( section )

					if (sizes[1])
						section.removeClass(sizes[0]).addClass(sizes[2])


				} else if ( btn.hasClass('section-start-row') ){

					section.toggleClass('force-start-row')

				}

				// "delete" has a confirm, so doesn't need this
				if( storeData )
					$.pageBuilder.reloadConfig( { location: 'section-control' } )

			})

		}

		, updatePage: function( obj ){
			
			var templateMode = $.pl.config.templateMode || 'local'
			
			$('.pl-sortable-area').each(function () {
				$.pageBuilder.alignGrid( this )
			})
			
			var map = $.plMapping.getCurrentMap()
			
			
			$.pl.data[templateMode]['custom-map'] = $.extend({}, { template: map.template })
			
			$.pl.data.global.regions = $.extend({}, { header: map.header, footer: map.footer })

			return map
			
		} 

        , reloadConfig: function( obj ) {
			

			var that = this
			,	obj = obj || {}
			, 	location = obj.location || 'none'
			, 	refresh  = obj.refresh || false
			,	storeMap = (typeof obj.storeMap !== 'undefined') ? obj.storeMap : true
			,	templateMode = $.pl.config.templateMode || 'local'
			,	map = that.updatePage( obj )
			
			
			if( storeMap ){

				if( refresh ){

					$.plAJAX.saveData( {
						  run: 'map'
						, refresh: true
						, refreshText: 'Saved! Page refresh required. Refreshing...'
						, map: map
						, templateMode: templateMode
					} )

				} else {

					$.plAJAX.saveData( {
						  run: 'map'
						, map: map
						, templateMode: templateMode
						, postSuccess: function( rsp ){

							if(!rsp)
								return

							if(rsp.changes && rsp.changes.local == 1){
								$('.x-item-actions')
								 	.removeClass('active-template')
							}


						}
					} )
				}


			}

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
					off_size = $.plMapping.getOffsetSize( section )
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
			,	items = ( type == 'section' ) ? '.pl-sortable' : '.pl-area-sortable'
			,	container = ( type == 'section' ) ? '.pl-sortable-area' : '.pl-area-container'
			,	placeholder = ( type == 'section' ) ? 'pl-placeholder' : 'pl-area-placeholder'
			,	handle = ( type == 'section' ) ? false : '.area-reorder'

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
					, 	handle: handle
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
						
						// Use this to help keep data clean when moving between scopes
						var templateMode = $.pl.config.templateMode || 'local'
						,	startScope = (ui.item.parents(".template-region-wrap").length == 1) ? templateMode : 'global'
						
						ui.item.attr('data-start-scope', startScope)

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
							
					
						// Move data when changing scopes
						that.moveDataOnDrag( ui.item )
						

					}

					, over: function(event, ui) {

			           $( "#droppable" ).droppable( "disable" )

						ui.placeholder.css({
							maxWidth: ui.placeholder.parent().width()
						})

			        }
					, update: function( event, ui ) {
						
						// the x-item draggables have to make adjustments before they are recognized
						// Saving twice creates a race condition
						if(!ui.item.hasClass('loading-active'))
							that.reloadConfig( {location: 'update sortable'} )
					}
				}

			return sortableSettings
		}

		// Moves data when changing scopes
		, moveDataOnDrag: function( element ){	
			
			var uniqueID = element.attr('data-clone')
			, 	templateMode = $.pl.config.templateMode || 'local'
			,	newScope = (element.parents(".template-region-wrap").length == 1) ? templateMode : 'global'
			, 	oldScope = element.attr('data-start-scope')
				
			
			// if data wasn't set or scope wasn't changed
			if( !plIsset( $.pl.data[ oldScope ][ uniqueID ] ) || newScope == oldScope ) 
				return
				
			element
				.attr('data-start-scope', newScope)
		
			//	console.log($.pl.data[ oldScope ])
			// move scope, then delete from old scope
			$.pl.data[ newScope ][ uniqueID ] = $.pl.data[ oldScope ][ uniqueID ]
			
			delete $.pl.data[ oldScope ][ uniqueID ]
			
			
			
			$.plAJAX.saveData()
			
		
		}





    }





}(window.jQuery);

