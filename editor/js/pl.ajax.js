!function ($) {
	
	
	/*
	 * AJAX Actions
	 */
	$.plAJAX = {
		
		// Generalized AJAX Function
		run: function( args ){
			
			var	that = this
			,	theData = {
						action: 'pl_editor_actions'
					,	mode: 'default'
					,	run: 'default'
					,	pageID: $.pl.config.pageID
					,	typeID: $.pl.config.typeID
					,	log: false
					,	confirm: false
					, 	confirmText: 'Are you sure?'
					,	savingText: 'Saving'
					,	refresh: 	false
					,	refreshText: 'Refreshing page...'
					, 	toolboxOpen: $.toolbox('open')
					,	beforeSend: ''
					, 	postSuccess: ''
					
				}
			
			// merge args into theData, overwriting theData w/ args	
			$.extend(theData, args)
				
			if( theData.confirm ){
				
				if( theData.toolboxOpen )
					$.toolbox('hide')
				
				bootbox.confirm( theData.confirmText, function( result ){
					
					if(result == true){
						that.runAction( theData )
					} else {
						
						if( theData.toolboxOpen )
							$('body').toolbox('show')
					}
					
				})
				
			} else {
				
				that.runAction( theData )
				
			}
			
			
			return ''
		}
		
		, runAction: function( theData ){
			
			var that = this
			$.ajax( {
					type: 'POST'
				, 	url: ajaxurl
				, 	data: theData	
				, 	beforeSend: function(){
					
						$('.btn-saving').addClass('active')

						if ( $.isFunction( theData.beforeSend ) )
							theData.beforeSend.call( this )

						if( theData.refresh ){
							$.toolbox('hide')
							bootbox.dialog( that.dialogText( theData.savingText ), [ ], {animate: false})
						}
							
					
					}
				, 	success: function( response ){
					
						that.runSuccess( theData, response )

						if( theData.refresh ){
							
							// reopen toolbox after load if it was shown
							if( theData.toolboxOpen )
								store.set('toolboxShown', true)
								
							bootbox.dialog( that.dialogText( theData.refreshText ), [ ], {animate: false})
							location.reload()
							
						} else {
							
							if( theData.toolboxOpen )
								$('body').toolbox('show')
							
						}
						
					}
			}) 
		}
		
		, runSuccess: function( theData, response ){
			var that = this
			,	rsp	= $.parseJSON( response )
			,	log = (rsp.post) ? rsp.post.log || false : ''
			
			if(log == 'true')
				console.log(rsp)
			
			if ( $.isFunction( theData.postSuccess ) )
				theData.postSuccess.call( this, rsp )
			
			that.ajaxSuccess(response)
		}
		
		, init: function(){
			
			
			this.bindUIActions()
			
		}
		
		, saveData: function( mode, refresh ){
			
			var	that = this
			, 	refresh = refresh || false
			, 	mode = mode || 'draft'
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
				
					that.ajaxSuccess(response)
					
					if(refresh){
						bootbox.dialog( that.dialogText( refreshingDialog ), [], {animate: false})
						location.reload()
					}
					
				}
			})
			
		}
		
		, resetOptions: function( mode ){
			
			var that = this
			,	theData = {
					action: 'pl_save_page'
					, 	mode: mode
					,	page: $.pl.config.pageID
					,	pageID: $.pl.config.pageID
					,	typeID: $.pl.config.typeID
				}
				
			if(mode == 'reset_global')
				var resetWhat = "global site options"
			else if(mode == 'reset_local')	
				var resetWhat = "local page options"
			else 
				return
				
			confirmText = sprintf("<h3>Are you sure?</h3><p>This will reset <strong>%s</strong> to their defaults. <br/>(Once reset, these changes will still need to be published to your live site.)</p>", resetWhat)


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
							
							that.ajaxSuccess(response)
							
							bootbox.dialog( that.dialogText('Options reset. Reloading page.'), [], {animate: false})
							
							location.reload()
						}
					})

				}

			})
		}
		
		, toggleEditor: function(){
			
			var that = this
			,	theData = {
					action: 'pl_editor_mode'
					,	userID: $.pl.config.userID
				}
			
			confirmText = sprintf("<h3>Turn Off PageLines Editor?</h3><p>(Note: Draft mode is disabled when editor is off.)</p>")
			bootbox.confirm( confirmText, function( result ){
				if(result == true){
					$.ajax( {
						type: 'POST'
						, url: ajaxurl
						, data: theData	
						, beforeSend: function(){
							bootbox.dialog( that.dialogText('Deactivating...'), [], {animate: false})
						}
						, success: function( response ){
							
							
							bootbox.dialog( that.dialogText('Editor deactivated! Reloading page.'), [], {animate: false})
							
							window.location = $.pl.config.currentURL
						}
					})
				}
			})
			
		}
		
		, bindUIActions: function(){
			
			var that = this
			
			
			
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
									that.ajaxSuccess(response)
									
									bootbox.dialog( that.dialogText('Reloading page.'), [], {animate: false, classes: 'bootbox-reloading'})
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
						bootbox.dialog( that.dialogText('Saving Template'), [], {animate: false})
				}
				, success: function( response ){
					
					if( interrupt ){
						bootbox.dialog( that.dialogText('Success! Reloading Page'), [], {animate: false})
						location.reload()
					}
					
					
					$('.btn-saving').removeClass('active')
					$('.state-list').removeClass('clean global local type multi map-local map-global').addClass(response)
					$('.btn-state span').removeClass().addClass('state-draft '+response)
				}
			})
		
			
		}
		
		, switchThemes: function( ){
		
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
				}
				, success: function( response ){
				}
			})
		
			
		}
		
		, ajaxSuccess: function( response ){
			
				$('.btn-saving').removeClass('active')
				$('.state-list').removeClass('clean global local local-global').addClass(response)
				$('.btn-state span').removeClass().addClass('state-draft '+response)
		}
		
		
		, dialogText: function( text ){
			
			var icon = '<i class="icon-spin icon-refresh"></i>&nbsp;'
			, 	theHTML = sprintf('<div class="spn"><div class="spn-txt">%s %s</div></div>', icon, text)
		
			return theHTML
			
			
		}
	}
	
	
	
}(window.jQuery);