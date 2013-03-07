!function ($) {

$.plThemes = {
	

	btnActions: function(){
		
		$('.btn-theme-activate').on('click', function(){
			var args = {
					mode: 'themes'
				,	flag: 'activate'
				,	savingText: 'Activating Theme'
				,	refreshText: 'Successfully Activated. Refreshing page'
			}
			
			var response = $.plAJAX.run( args )
		})
		
		$('.btn-theme-preview').on('click', function(){
			console.log('hi2')
		})
	
	}
	, actionButtons: function(){
		var buttons = ''
		buttons += sprintf('<a href="#" class="btn btn-primary btn-theme-activate"><i class="icon-bolt"></i> Activate</a> ')
		buttons += sprintf('<a href="#" class="btn btn-theme-preview"><i class="icon-eye-open"></i> Preview</a> ')
		
		return buttons
	}
}

}(window.jQuery);