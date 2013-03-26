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
		
		$('.panel-add-new').find( '.x-item.pl-area' ).draggable({
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
		
		var name = element.data('name')
		,	controls = $('.pl-section-controls').first().clone()
		, 	btns = sprintf('<div class="btns"><a href="#" class="btn btn-mini btn-block banner-refresh"><i class="icon-repeat"></i> Refresh to Load</a></div>')
			
		// Set controls name from new
		controls
			.find('.ctitle')
			.html(name)
			
		// Remove controls that only work once section fully loaded
		controls
			.find('.s-loaded')
			.hide()
		
		element
			.prepend( controls )
			.addClass('pl-section')
			.find('.banner-content')
			.append( btns )
		
		$.pageBuilder.handleCloneData( element )
		
		if(!element.hasClass('ui-draggable-dragging'))
			element.show()
			
		// reload events
		$('.s-control')
			.off('click.sectionControls')
		
		$.pageBuilder.sectionControls()
		
		$('.banner-refresh')
			.off()
			.on('click', function(e){ 
				e.preventDefault()
				location.reload() 
			})
			
		// Store new page config
		$.pageBuilder.storeConfig()
	}
}

}(window.jQuery);