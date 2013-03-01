!function ($) {
	
	$(document).ready(function() {

		$('.toolbox-activate').on('click.toolBoxActivate', function(){
			var url = window.location.href.split("?")[0]
			,	param = 'editor_state=on'
			
			url += '?'+param;
			
			window.location = url;
		})

	})
	
}(window.jQuery);