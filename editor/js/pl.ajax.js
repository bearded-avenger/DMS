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
		
		, saveData: function( mode, refresh ){
			
			var	that = this
			, 	refresh = refresh || false
			,	savingDialog = $.pl.flags.savingDialog
			,	refreshingDialog = $.pl.flags.refreshingDialog
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
					
					if(refresh)
						bootbox.dialog( that.dialogText( savingDialog ), [], {animate: false})
				}
				, success: function( response ){
				
					$('.btn-saving').removeClass('active')
					$('.state-list').removeClass('clean global local type multi').addClass(response)
					$('.btn-state span').removeClass().addClass('state-draft '+response)
					
					if(refresh){
						bootbox.dialog( that.dialogText( refreshingDialog ), [], {animate: false})
						location.reload()
					}
					
				}
			})
			
		}
		
		, bindUIActions: function(){
			
			
			$( '.btn-save' ).on('click.saveButton', function(){
				
				var btn = $(this)
				,	mode = (btn.data('mode')) ? btn.data('mode') : ''
				
				
				if(mode == 'draft'){
					$.pl.flags.savingDialog = 'Saving Draft';
					$.pl.flags.refreshingDialog = 'Draft saved. Refreshing page.';
				} else if (mode == 'publish'){
					$.pl.flags.savingDialog = 'Publishing draft';
					$.pl.flags.refreshingDialog = 'Published. Refreshing page.';	
				}
					
				
				$.plAJAX.saveData( mode, $.pl.flags.refreshOnSave )
				
				
			})
			

			$('.btn-revert').on('click.revertbutton', function(e){
					e.preventDefault()
				
					var revert = $(this).data('revert')
					,	theData = {
							action: 'pl_save_page'
						,	mode: 'revert'
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
									$('.state-list').removeClass('clean global local type multi').addClass(response)
									$('.btn-state span').removeClass().addClass('state-draft '+response)
									
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

									$('.btn-saving').removeClass('active')
									$('.state-list').removeClass('clean global local local-global').addClass(response)
									$('.btn-state span').removeClass().addClass('state-draft '+response)

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

					var key = $(this).closest('.list-item').data('key')
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

							$(that)
								.removeClass('set-default-template')
								.addClass('btn-success')
								.html('Active ('+theType +') default')

							$('.btn-saving').removeClass('active')
						}
					})

				})

			
		}
		
		, ajaxSaveMap: function( map, interrupt ){
		
			var that = this
			, 	interrupt = interrupt || false
			,	saveData = {
						action: 'pl_save_page'
					, 	mode: 'map'
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
					$('.state-list').removeClass('clean global local type multi map-local map-global').addClass(response)
					$('.btn-state span').removeClass().addClass('state-draft '+response)
				}
			})
		
			
		}
		
		
		, dialogText: function( text ){
			
			var bar = '<div class="progress progress-striped active"><div class="bar" style="width: 100%"></div></div>'
			,	icon = '<i class="icon-spin icon-refresh spin-fast"></i>&nbsp;'
			, 	theHTML = sprintf('<div class="spn"><div class="spn-txt">%s %s</div></div>', icon, text)
		
			return theHTML
			
			
		}
	}
	
	
	
}(window.jQuery);