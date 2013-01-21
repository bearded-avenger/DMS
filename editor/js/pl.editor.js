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
			
			$('.btn-publish').on('click.toolboxPublish', function(){
				
					var theData = {
						action: 'pl_publish_changes'
						,	page: $.pl.config.pageID
					}

					$.ajax( {
						type: 'POST'
						, url: ajaxurl
						, data: theData	
						, beforeSend: function(){
							$('.btn-saving').addClass('active')
						}
						, success: function( response ){
							$('.btn-saving').removeClass('active')
							$('.state-list').removeClass('clean global local local-global').addClass(response)
							$('.btn-state span').removeClass().addClass('state-draft-'+response)
						}
					})
				
			})
			
			$('.btn-revert').on('click.revertbutton', function(e){
					e.preventDefault()
				
					var revert = $(this).data('revert')
					,	theData = {
						action: 'pl_revert_changes'
						,	revert: revert
						,	page: $.pl.config.pageID
					}
					, 	confirmText = "<h3>Are you sure?</h3><p>This will revert <strong>"+revert+"</strong> changes to your last published configuration.</p>"
					
					$('body').toolbox('hide')
					
					bootbox.confirm( confirmText, function( result ){
						if(result == true){
							
							$.ajax( {
								type: 'POST'
								, url: ajaxurl
								, data: theData	
								, beforeSend: function(){
									$('.btn-saving').addClass('active')
								}
								, success: function( response ){
									console.log(response)
									$('.btn-saving').removeClass('active')
									$('.state-list').removeClass('clean global local local-global').addClass(response)
									$('.btn-state span').removeClass().addClass('state-draft-'+response)
									
									var reloadText = '<div class="spn"><div class="spn-txt">Reloading Page</div><div class="progress progress-striped active"><div class="bar" style="width: 100%"></div></div></div>'
									
									bootbox.dialog( reloadText, [], {animate: false, classes: 'bootbox-reloading'})
									location.reload()
								}
							})
							
						}

					})
				
				
					
				
			})
			
			$(".load-template").on("click.loadTemplate", function(e) {
			
				e.preventDefault()
				
				var key = $(this).closest('.list-item').data('key')
				, 	confirmText = "<h3>Are you sure?</h3><p>Loading a new template will overwrite the current template configuration.</p>"
				,	theData = {
						action: 'pl_load_template'
						,	key: key
						,	page: $.pl.config.pageID
					}
					
				// modal
				bootbox.confirm( confirmText, function( result ){
					if(result == true){
					
						$.ajax( {
							type: 'POST'
							, url: ajaxurl
							, data: theData	
							, beforeSend: function(){
								$('.btn-saving').addClass('active')
							}
							, success: function( response ){
								console.log(response)
								$('.btn-saving').removeClass('active')
								$('.state-list').removeClass('clean global local local-global').addClass(response)
								$('.btn-state span').removeClass().addClass('state-draft-'+response)
								
								var reloadText = '<div class="spn"><div class="spn-txt">Reloading Page</div><div class="progress progress-striped active"><div class="bar" style="width: 100%"></div></div></div>'
								
								bootbox.dialog( reloadText, [], {animate: false, classes: 'bootbox-reloading'})
								location.reload()
							}
						})
					
					}
		
				})
				
			})
			
			$(".delete-template").on("click.deleteTemplate", function(e) {
			
				e.preventDefault()
				
				var key = $(this).closest('.list-item').data('key')
				, 	confirmText = "<h3>Are you sure?</h3><p>This will delete this template configuration.</p>"
				,	theData = {
							action: 'pl_template_action'
						,	mode: 'delete_template'
						,	key: key
						,	page: $.pl.config.pageID
					}
					
				// modal
				bootbox.confirm( confirmText, function( result ){
					if(result == true){
					
						$.ajax( {
							type: 'POST'
							, url: ajaxurl
							, data: theData	
							, beforeSend: function(){
								$( '.template_key_'+key ).fadeOut(300, function() { 
									$(this).remove()
								})
							}
						})
					
					}
		
				})
				
			})
			
			
			$(".form-save-template").on("submit.saveTemplate", function(e) {
			
				e.preventDefault()
				
				var form = $(this).formParams()
				,	theData = {
							action: 'pl_template_action'
						, 	mode: 'save_template'
						, 	map: $.pageBuilder.getCurrentMap()
						,	page: $.pl.config.pageID
					}
				,	theData = $.extend({}, theData, form)

				$.ajax( {
					type: 'POST'
					, url: ajaxurl
					, data: theData	
					, beforeSend: function(){
						
						bootbox.dialog( that.dialogText('Saving Template'), [], {animate: false})
					}
					, success: function( response ){
						bootbox.dialog( that.dialogText('Success! Reloading Page'), [], {animate: false})
						location.reload()
					
					}
				})
			
			})
		
			$(".set-default-template").on("click.defaultTemplate", function(e) {
			
				e.preventDefault()
				
				var that = this
				,	key = $(this).closest('.list-item').data('key')
				,	theType = $.pl.config.pageTypeName
				,	theData = {
							action: 'pl_template_action'
						, 	mode: 'type_default'
						, 	key: key
						,	type: $.pl.config.pageTypeID
						,	page: $.pl.config.pageID
					}

				$.ajax( {
					type: 'POST'
					, url: ajaxurl
					, data: theData	
					, beforeSend: function(){

						$('.btn-saving').addClass('active')
					}
					, success: function( response ){
						console.log(response)
						$(that)
							.removeClass('set-default-template')
							.addClass('btn-success')
							.html('Active ('+theType +') default')
							
						$('.btn-saving').removeClass('active')
					}
				})
			
			})
			
        }

		, dialogText: function( text ){
			
			var bar = '<div class="progress progress-striped active"><div class="bar" style="width: 100%"></div></div>'
			,	icon = '<i class="icon-spin icon-refresh spin-fast"></i>'
			, 	theHTML = sprintf('<div class="spn"><div class="spn-txt">%s %s</div></div>',bar, text)
		
			return theHTML
				

			
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
			, 	imageHTML = sprintf('<div class="pl-touchable banner-frame"><div class="pl-vignette pl-touchable-vignette"><img class="section-thumb" src="%s" /></div></div>', image )
			, 	text = sprintf('<div class="banner-title">%s</div>', name )
			, 	theHTML = sprintf('<div class="pl-refresh-banner">%s %s</div>', imageHTML, text)
			
			element
				.removeAttr("style")
				.addClass('pl-section')
				.html(theHTML)
				
			
				
		}
		, switchOnStop: function( element ){
			$.pageBuilder.storeConfig(true)
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
			$('.pl-section').effect('highlight', 1500)
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
				
				$.pageBuilder.reloadConfig( 'section-control' )
				
			})
		
		}

	
		
        , reloadConfig: function( source ) {
	
			console.log(source)
			
			$('.editor-row').each(function () {
				$.pageBuilder.alignGrid( this )
			})
			
			if( source !== 'start' )
				$.pageBuilder.storeConfig( );
			
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
				,	col_size = that.getColumnSize( section )
				,	off_size = that.getOffsetSize( section )
				
				
				if(sort_area.hasClass('pl-sortable-column')){
				
					if(section.hasClass('level1')){
						section
							.removeClass('level1')
							.removeClass(col_size[0])
							.removeClass(off_size[0])
							.addClass('span12 offset0 level2')
							
						col_size = that.getColumnSize( section, true )
						off_size = that.getOffsetSize( section, true )
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

		, getCurrentMap: function() {
			
			var that = this
			,	map = {}


			$('.pl-region').each( function(regionIndex, o) {

				var region = $(this).data('region')
				, 	areaConfig = []

				$(this).find('.pl-area').each( function(areaIndex, o2) {

					var area = $(this)
					,	areaContent	= []
					, 	areaSet = {}

					$(this).find('.pl-section.level1').each( function(sectionIndex, o3) {

						var section = $(this)

						set = that.sectionConfig( section )

						areaContent.push( set )

					})

					areaSet = {
						area: ''
						, content: areaContent
					}

					areaConfig.push( areaSet )

				})

				map[region] = areaConfig

			})
			
			return map
			
		}

		, storeConfig: function( interrupt ) {
			
			var that = this
			, 	interrupt = interrupt || false
			,	map = that.getCurrentMap()
			
			$.pl.map = map
			
			that.ajaxSaveMap( map, interrupt )
			
			return map
			
		
		}
		
		, sectionConfig: function( section ){
			
			var that = this
			,	set = {}
			
			set.object = section.data('object')
			set.clone = section.data('clone')
			set.sid = section.data('sid')
			set.span = that.getColumnSize( section )[ 4 ]
			set.offset = that.getOffsetSize( section )[ 3 ]
			set.content = []
			
			section.find('.pl-section.level2').each( function() {
			
				set.content.push( that.sectionConfig( $(this) ) )
				
			})
			
			return set
			
		}

		, ajaxSaveMap: function( map, interrupt ){
		
			var that = this
			, 	interrupt = interrupt || false
			,	saveData = {
				action: 'pl_save_map_draft'
				,	map: map
				,	page: $.pl.config.pageID
				, 	special: $.pl.config.isSpecial
			}
			
			$.ajax( {
				type: 'POST'
				, url: ajaxurl
				, data: saveData	
				, beforeSend: function(){
					$('.btn-saving').addClass('active')
					
					if( interrupt )
						bootbox.dialog( $.pageTools.dialogText('Saving Template'), [], {animate: false})
				}
				, success: function( response ){
					
					if( interrupt ){
						bootbox.dialog( $.pageTools.dialogText('Success! Reloading Page'), [], {animate: false})
						location.reload()
					}
					
					$('.btn-saving').removeClass('active')
					$('.state-list').removeClass('clean global local local-global').addClass(response)
					$('.btn-state span').removeClass().addClass('state-draft-'+response)
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

		, getOffsetSize: function( column, defaultValue ) {
			
			var that = this
			,	max = 12
			,	sizes = that.getColumnSize( column )
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
			
			var that = this
			,	sortableArgs = {}
			, 	sortableArgsColumn = {}
			
			sortableArgs = {
			       	items: 	".pl-sortable"
				,	connectWith: ".pl-sortable-area"
				,	placeholder: "pl-placeholder"
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

					if(ui.item.hasClass('x-item'))
						$.xList.switchOnAdd(ui.item)

					// allows us to change sizes when dragging starts, while keeping good dragging
					$( this ).sortable( "refreshPositions" ) 
					
					// Prevents double nesting columns and other recursion bugs. 
					// Remove all drag and drop elements and disable sortable areas within columns if 
					// the user is dragging a column
					if( ui.item.hasClass('section-plcolumn') ){
						
						$( '.section-plcolumn .pl-sortable-column' ).removeClass('pl-sortable-area')
						$( '.section-plcolumn .pl-section' ).removeClass('pl-sortable')
						
						$( this ).sortable( 'refresh' )
						
					}
				

				} 
				, stop: function(event, ui){

					$('body')
						.removeClass('pl-dragging')

					// when new sections are added
					ui.item.find('.banner-refresh').fadeIn('slow')
					
					if( ui.item.hasClass('section-plcolumn') ){
						
						$( '.section-plcolumn .pl-sortable-column' ).addClass('pl-sortable-area')
						$( '.section-plcolumn .pl-section' ).addClass('pl-sortable')
						
						$( this ).sortable( 'refresh' )
						
					}
					
					if(ui.item.hasClass('x-item'))
						$.xList.switchOnStop(ui.item)

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
			
		    $( '.pl-sortable-area' ).sortable( sortableArgs ) 
			$( ".x-item" ).draggable();
		
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

					resizeNewWidth = (resizeNewWidth < 480) ? 480 : resizeNewWidth;
						
					widthSel
						.css('left', 'auto')
						.css('height', 'auto')
						.width( resizeNewWidth )

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

