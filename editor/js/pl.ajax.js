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
						,	mode: mode
						,	page: $.pl.config.pageID
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
		
		, uploadImage: function( config ) {
			
			
		}
		
	
	}
	
	
	
}(window.jQuery);