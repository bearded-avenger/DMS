!function ($) {

	// --> Initialize
	$(document).ready(function() {
		$.plCommon.init()
	
		$(".fitvids").fitVids(); // fit videos
	
		$.plAnimate.initAnimation()
		
		
		
	})
	$(window).load(function() {
		$.plCommon.plVerticalCenter('.pl-centerer', '.pl-centered')
		$('.pl-section').on('resize', function(){
			$.plCommon.plVerticalCenter('.pl-centerer', '.pl-centered')
		})
	})

	$.plAnimate = {
		
		initAnimation: function(){
			
			var that = this
			
			$.plAnimate.animatedCount = 0
			$.plAnimate.totalAnimations = 0
						// 
						// 
						// if( $('.pl-animation').length > 0 ){
						// 	
						// 	//store the total number of bars that need animating
						// 	$.plAnimate.totalAnimations = $( '.pl-animation' ).length
						// 
						// 
						// 	scrollAnimation = setInterval( that.checkViewport, 150)
						// 	
						// 	$.plAnimate.checkViewport()
						// 
						// }
						
			$.plAnimate.plWaypoints()
		}
		
		, plWaypoints: function(options_passed){
			
			var defaults = { 
					offset: '80%' // 'bottom-in-view' 
					, triggerOnce: true
				}
				, options  = $.extend({}, defaults, options_passed)
				, delay = 150

			$('.pl-animation').each(function(){
				
				var element = $(this)

				setTimeout(function() {
					element.waypoint(function(direction){
						
					 	$(this)
							.addClass('animation-loaded')
							.trigger('animation_loaded')

					}, options )

				} , 150 )
			})
		}
		
		, checkViewport: function(){
			
			var that = this
				// 
				// $('.pl-animation:in-viewport:not(".loaded")').each(function(i){
				// 
				// 	var element = $(this)
				// 	,	action = $(this).data('action') || 'scale'
				// 
				// 	$(this)
				// 		.addClass('loaded')
				// 
				// 	$.plAnimate.animatedCount++
				// 
				// 	if( $.plAnimate.animatedCount == $.plAnimate.totalAnimations){
				// 		
				// 		clearInterval( scrollAnimation )
				// 	}
				// 
				// })
			
			// $('.pl-animation:not(".loaded")').waypoint(function() {
			// 		  
			// 			var element = $(this)
			// 			,	action = $(this).data('action') || 'scale'
			// 
			// 			$(this)
			// 				.addClass('loaded')
			// 
			// 			$.plAnimate.animatedCount++
			// 
			// 			if( $.plAnimate.animatedCount == $.plAnimate.totalAnimations){
			// 
			// 				clearInterval( scrollAnimation )
			// 			}
			// 		
			// 		}, { offset: '90%' });
			
			
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