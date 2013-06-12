!function ($) {

$.plSections = {

	init: function(){
		this.bindActions()
		this.makeDraggable()
	}
	, bindActions: function(){
		var that = this

		$('.btn-reload-sections').on('click', function(e){

			e.preventDefault()

			var args = {
						mode: 'sections'
					,	run: 'reload'
					,	confirm: false
					,	savingText: 'Reloading and Registering Sections'
					,	refreshText: 'Sections reloaded. Refreshing page!'
					,	refresh: true
					, 	log: true
				}


			var response = $.plAJAX.run( args )

		})

	}
	, makeDraggable: function( ){

		var that = this

		$('.panel-add-new').find( '.x-item.pl-sortable:not(.x-disable)' ).draggable({
				appendTo: "body"
			, 	helper: "clone"
			, 	cursor: "move"
			, 	connectToSortable: ".pl-sortable-area"
			,	zIndex: 10000
			,	distance: 20
			, 	start: function(event, ui){

					that.switchOnAdd( ui.helper )

					ui.helper
						.css('max-width', '300px')
						.css('height', 'auto')


				}
		})

		$('.panel-add-new').find( '.x-item.pl-area-sortable' ).draggable({
				appendTo: "body"
			, 	helper: "clone"
			, 	cursor: "move"
			, 	connectToSortable: ".pl-area-container"
			,	zIndex: 10000
			,	distance: 20
			, 	start: function(event, ui){

					that.switchOnAdd( ui.helper )

					ui.helper
						.css('width', '100%')
						.css('height', 'auto')


				}
		})


	}
	, switchOnAdd: function( element ){


		var name = element.data('name')
		, 	image = element.data('image')
		, 	imageHTML = sprintf('<div class="pl-touchable banner-frame"><div class="pl-vignette pl-touchable-vignette"><img class="section-thumb" src="%s" /></div></div>', image )
		, 	theHTML = sprintf('<div class="pl-refresh-banner"><div class="banner-content">%s</div></div>', imageHTML	)


		element
			.removeAttr("style")
			.html(theHTML)

		if( !element.hasClass('ui-draggable-dragging') )
			element.hide()

	}
	, switchOnStop: function( element ){

		var that = this
		,	type = (element.hasClass('pl-area-sortable')) ? 'area' : 'section'
		,	activeLoad = (element.hasClass('loading-active')) ? true : false
		,	name = element.data('name')
		,	sid = element.data('sid')
		,	object = element.data('object')
		,	sectionClass = 'section-'+sid
		,	classToAdd = (type == 'section') ? 'pl-section' : 'pl-area'
		
		element
			.removeClass('x-item isotope-item x-add-new x-extension')
			.addClass( classToAdd )
			.addClass( sectionClass )
			

		var newUniqueID = $.pageBuilder.handleCloneData( element )


		if(activeLoad){
			
			var args = {
					run: 'load'
				,	mode: 'sections'
				,	object: object
				, 	uniqueID: newUniqueID
				, 	postSuccess: function(response){
					
						if(!response)
							return
						
						var controlType = (type == 'section') ? '.pl-section-controls' : '.pl-area-controls'
						,	controls = $( controlType ).first().clone()	
					
						controls
							.find('.ctitle')
							.html(name)

						element
							.html( sprintf('<div class="pl-section-pad">%s</div>', response.template) )
							.prepend( controls )
						
						var newOpts = {}
						
						newOpts[newUniqueID] = {
							opts: response.opts
							, name: name
						}
						
						$.extend($.pl.config.opts, newOpts)
						// reload events
						$('.s-control')
							.off('click.sectionControls')

						$.pageBuilder.sectionControls()

						$('.area-control')
							.off('click.areaControl')

						$.areaControl.listen()
						
						$.pageBuilder.reloadConfig( {location: 'active load'} )
				
					}
				,	beforeSend: function( ){
						element
							.html('<div class="pl-refresh-banner"><i class="icon-spinner icon-spin"></i> Loading</div>')
					}
			}

			$.plAJAX.run( args )
			
		} else {
			
			$.pageBuilder.reloadConfig( {location: 'passive load', refresh: true} )
			
		}
		

		if(!element.hasClass('ui-draggable-dragging'))
			element.show()


		
	}
	
	, sectionLoader: function( obj ){
	
		var object = obj.element.data('object')
		
		var args = {
				run: 'load'
			,	mode: 'sections'
			,	object: object
			, 	postSuccess: obj.postSuccess
			,	beforeSend: obj.beforeSend
		}

		$.plAJAX.run( args )
	}
}

}(window.jQuery);