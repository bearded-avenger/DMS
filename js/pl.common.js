!function ($) {

	// --> Initialize
	$(document).ready(function() {
		$.plCommon.init()
	
	
	
		$.plAnimate.initAnimation()
		
		
		
	})

	$.plAnimate = {
		
		initAnimation: function(){
			
			var that = this
			
			$.plAnimate.animatedCount = 0
			$.plAnimate.totalAnimations = 0
			
			if( $('.pl-caption').length > 0 ){
				
				//store the total number of bars that need animating
				$.plAnimate.totalAnimations = $( '.pl-caption' ).length

				$.plAnimate.alignCaptions()

				scrollAnimation = setInterval( that.checkViewport, 150)
				
				$.plAnimate.checkViewport()
			
			}
		}
		
		, alignCaptions: function(){
			$('.pl-caption').each( function(){
				var cap = $(this)
				,	xPos = cap.data('x') || 0
				,	yPos = cap.data('y') || 0
				,	loaded = cap.parent().hasClass('loaded')
				, 	xOffset = 0
				,	yOffset = 0
				
				if(!loaded){
					
					if(cap.hasClass('lfr'))
						xOffset = 250
					else if ( cap.hasClass('sfr') )
						xOffset = 50
					else if ( cap.hasClass('sfl') )
						xOffset = -50
					else if ( cap.hasClass('lfl') )
						xOffset = -250

					if ( cap.hasClass('sft') )
						yOffset = -50
					else if ( cap.hasClass('lft') )
						yOffset = -250
					else if ( cap.hasClass('sfb') )
						yOffset = 50
					else if ( cap.hasClass('lfb') )
						yOffset = 250
					
					
				}
				
				cap
					.css('top', yPos + yOffset)
					.css('left', xPos + xOffset)
			})
		}
		
		, checkViewport: function(){
			
			var that = this
		
			$('.pl-caption:in-viewport:not(".loaded")').each(function(i){
				
				var element = $(this)
				,	action = $(this).data('action') || 'scale'

				$(this)
					.addClass('loaded')

				$.plAnimate.alignCaptions()
					
				$.plAnimate.animatedCount++

				if( $.plAnimate.animatedCount == $.plAnimate.totalAnimations){
					
					clearInterval( scrollAnimation )
				}

			})
			
			
		}
	}

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