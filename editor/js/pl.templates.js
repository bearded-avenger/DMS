!function ($) {

$.plTemplates = {
	
	init: function(){
		this.bindUIActions()
	}
	
	, bindUIActions: function(){
		var that = this
		
				$(".load-template").on("click.loadTemplate", function(e) {

					e.preventDefault()
					
					var args = {
							mode: 'templates'
						,	run: 'load'
						,	confirm: true
						,	confirmText: '<h3>Are you sure?</h3><p>Loading a new template will overwrite the current template configuration.</p>'
						,	savingText: 'Loading Template'
						,	refresh: true
						,	refreshText: 'Successfully Loaded. Refreshing page'
						, 	log: true
						,	key: $(this).closest('.list-item').data('key')
					}

					var response = $.plAJAX.run( args )

					
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

							bootbox.dialog( $.plAJAX.dialogText('Saving Template'), [], {animate: false})
						}
						, success: function( response ){
							bootbox.dialog( $.plAJAX.dialogText('Success! Reloading Page'), [], {animate: false})
							location.reload()

						}
					})

				})

				$(".set-default-tpl").on("click.defaultTemplate", function(e) {

					e.preventDefault()

					var that = this
					, 	theTemplate = $(this).closest('.list-item').data('key')
					,	theType = $(this).data('posttype')
					,	theData = {
								action: 'pl_template_action'
							, 	mode: 'type_default'
							, 	field: $(this).data('field')
							, 	key: theTemplate
							,	pageID: $.pl.config.pageID
							,	typeID: $.pl.config.typeID
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
								.closest('.y-list')
								.find('.btn-tpl-default')
								.removeClass('btn-success')
								.html('Set as "'+theType+'" default')
								
							$(that)
								.removeClass('set-default-tpl')
								.addClass('btn-success')
								.html('Active "'+theType+'" default')

							$('.btn-saving').removeClass('active')
						}
					})

				})
			
		
	
	}


}

}(window.jQuery);