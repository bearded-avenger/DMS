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
		
		, plVerticalCenter: function( container, element, offset ) {

			jQuery( container ).each(function(){

				var colHeight = jQuery(this).height()
				,	centeredElement = jQuery(this).find( element )
				,	infoHeight = centeredElement.height()
				, 	offCenter = offset || 0

				centeredElement.css('margin-top', ((colHeight / 2) - (infoHeight / 2 )) + offCenter )
			})

		}
		

	}

}(window.jQuery);