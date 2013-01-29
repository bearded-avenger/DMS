!function ($) {
	
	
	/*
	 * AJAX Actions
	 */
	$.plAJAX = {
		
		init: function(){
			
			this.loadPersistent()
			
			this.bindUIActions()
			
		}
		
		, loadPersistent: function(){
			
			
			
		}
		
		, bindUIActions: function(){
			
			
			$( '.btn-save' ).on('click.saveButton', function(){
				
				var btn = $(this)
				,	mode = (btn.data('mode')) ? btn.data('mode') : ''
				,	refresh = $.pl.flags.refreshOnSave
				,	theData = {
							action: 'pl_save_page'
						,	map: $.pl.map
						,	mode: mode
						,	pageID: $.pl.config.pageID
						,	typeID: $.pl.config.typeID
						,	pageData: $.pl.data
						
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
						,	pageID: $.pl.config.pageID
						,	typeID: $.pl.config.typeID
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
			
		}
		
		, ajaxSaveMap: function( map, interrupt ){
		
			var that = this
			, 	interrupt = interrupt || false
			,	saveData = {
				action: 'pl_save_map_draft'
				,	map: $.pl.map
				,	pageID: $.pl.config.pageID
				,	typeID: $.pl.config.typeID
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
		
	
	}
	
	
	
}(window.jQuery);