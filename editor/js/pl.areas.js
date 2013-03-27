!function ($) {

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
			$('.area-control').on('click', function(e){
				e.preventDefault()
				
				var action = $(this).data('area-action')
				
				if(action == 'down' ){
					$.areaControl.move($(this), 'down')
				} else if (action == 'up' ){
					$.areaControl.move($(this), 'up')
				} else if (action == 'settings' ){
					$.areaControl.areaSettings($(this))
				
				}else if (action == 'delete' ){
					$.areaControl.deleteArea($(this))

				}
			})
			
			
		} 
		
		, update: function() {
			$('.area-tag').each( function(index) {

				var num = index + 1

			    $(this)
					.data('area-number', num)
					.attr('data-area-number', num)

			})
			
			$.pageBuilder.storeMap()
		}
		
		, areaSettings: function( btn ){
			
			var that = this
			,	theArea = btn.closest('.pl-area')
			,	theID = theArea.attr('id')
				
			$('body').toolbox({
				action: 'show'
				, panel: 'area_settings'
				, info: function(){
					that.areaPanelRender(theID)
				}
			})
			
			
		}
		
		, areaPanelRender: function( theID ){
			
			var theID = theID || store.get('lastAreaConfig')
			,	config = {
						mode: 'object'
					,	panel: 'area_settings'
					, 	settings: $.pl.config.areaSettings
					, 	objectID: theID
				}
			
			$.optPanel.render( config )
			
		}
		
		, deleteArea: function( btn ){
			
			var currentArea = btn.closest('.pl-area')
			, 	confirmText = '<h3>Are you sure?</h3><p>This action will delete this area and all its elements from this page.</p>'
		
			bootbox.confirm( confirmText, function( result ){
				if(result == true){
					
					currentArea.slideUp(500, function(){
						$(this).remove()
						$.areaControl.update()
					})
					

				}

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