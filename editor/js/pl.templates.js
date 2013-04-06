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
						,	key: $(this).closest('.x-item').data('key')
					}

					var response = $.plAJAX.run( args )

					
				})

				$(".delete-template").on("click.deleteTemplate", function(e) {

					e.preventDefault()
					
					var key = $(this).closest('.x-item').data('key')
					,	theIsotope = $(this).closest('.isotope')
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
							,	postSuccess: function(){
								theIsotope.isotope( 'reLayout' )
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
							,	map: $.plMapping.getCurrentMap()
						}
					,	args = $.extend({}, args, form) // add form fields to post
						

					var response = $.plAJAX.run( args )


				})

				$(".set-tpl").on("click.defaultTemplate", function(e) {

					e.preventDefault()

					var that = this
					,	value = $(this).closest('.x-item').data('key')
					,	run = $(this).data('run')
					,	args = {
								mode: 'templates'
							,	run: 'set_'+run
							,	confirm: false
							,	refresh: false
							, 	log: true
							, 	field: $(this).data('field')
							,	value: value
							, 	postSuccess: function( response ){
								
									// console.log("caller is " + arguments.callee.caller.toString());
								
								
									// $.Ajax parses argument values and calles this thing, probably supposed to do that a different way
									if(!response)
										return 
							
									$(that)
										.closest('.x-list')
										.find('.set-tpl[data-run="'+run+'"]')
										.removeClass('btn-inverse')
										.html('Set '+run+' Default')

									if(response.result && response.result != false){
										$(that)
											.addClass('btn-inverse')
											.html('Active '+run+' Default')
									}
										
									
									

								}
						}
						
					var response = $.plAJAX.run( args )


				})
				
			
			
		
	
	}


}

}(window.jQuery);