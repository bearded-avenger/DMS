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
					,	args = {
								mode: 'templates'
							,	run: 'delete'
							,	confirm: true
							,	confirmText: '<h3>Are you sure?</h3><p>This will delete this template configuration.</p>'
							,	savingText: 'Deleting Template'
							,	refresh: false
							, 	log: true
							,	key: key
							, 	beforeSend: function(){
									$( '.template_key_'+key ).fadeOut(300, function() { 
										$(this).remove()
									})
								}
						}

					var response = $.plAJAX.run( args )

				})


				$(".form-save-template").on("submit.saveTemplate", function(e) {

					e.preventDefault()

					var form = $(this).formParams()
					,	args = {
								mode: 'templates'
							,	run: 'save'
							,	confirm: false
							,	savingText: 'Saving Template'
							,	refreshText: 'Successfully Saved. Refreshing page'
							,	refresh: true
							, 	log: true
							,	map: $.pageBuilder.getCurrentMap()
							, 	beforeSend: function(){
									$( '.template_key_'+key ).fadeOut(300, function() { 
										$(this).remove()
									})
								}
						}
					,	args = $.extend({}, args, form) // add form fields to post
						

					var response = $.plAJAX.run( args )


				})

				$(".set-default-tpl").on("click.defaultTemplate", function(e) {

					e.preventDefault()

					var that = this
					,	key = $(this).closest('.list-item').data('key')
					,	theType = $(this).data('posttype')
					,	args = {
								mode: 'templates'
							,	run: 'type_default'
							,	confirm: false
							,	refresh: false
							, 	log: true
							, 	field: $(this).data('field')
							,	key: key
							, 	success: function(){
									$(that)
										.closest('.y-list')
										.find('.btn-tpl-default')
										.removeClass('btn-success')
										.html('Set as "'+theType+'" default')

									$(that)
										.removeClass('set-default-tpl')
										.addClass('btn-success')
										.html('Active "'+theType+'" default')

								}
						}
						

					var response = $.plAJAX.run( args )


				})
			
		
	
	}


}

}(window.jQuery);