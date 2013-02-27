!function ($) {
	
	$(document).ready(function() {

		$('.toolbox-activate').on('click.toolBoxActivate', function(){
			var url = window.location.href
			,	param = 'editor_state=on'
			
			url += (url.indexOf('?') > -1) ? '&'+param : '?'+param;
			
			window.location.href = url;
		})

	})
	
}(window.jQuery);