!function ($) {

	// --> Initialize 
	$(document).ready(function() {
		$.plCommon.init()
	})
	
	$.plFixed = {
		
		update: function(){
			
		}
	}
	
	$.plCommon = {

		init: function(){
			var that = this
			that.setHeight()
			
			$.resize.delay = 100 // resize throttle
			
			$('.pl-fixed-top').on('resize', function(){
				that.setHeight()
			})
			
		}
		
		, setHeight: function(){
			
			var height = $('.pl-fixed-top').height()
			
			$('.fixed-top-pusher').height(height)
			
		}

	}

}(window.jQuery);