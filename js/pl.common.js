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
						
			$.plAnimate.plWaypoints()
		}
		
		, plWaypoints: function(selector, options_passed){
			
			var defaults = { 
					offset: '85%' // 'bottom-in-view' 
					, triggerOnce: true
				}
				, options  = $.extend({}, defaults, options_passed)
				, delay = 150
				
			$('.pl-animation-group')
				.find('.pl-animation')
				.addClass('pla-group')
				
			$('.pl-animation-group').each(function(){
				
				var element = $(this)

				element.waypoint(function(direction){
				 	$(this)
						.find('.pl-animation')
						.each(function(i){
							var element = $(this);
							setTimeout(function(){ element.addClass('animation-loaded') }, (i * 150));
						})

				}
				, { offset: '80%' 
					, triggerOnce: true
				})
			})

			$('.pl-animation:not(.pla-group)').each(function(){
				
				var element = $(this)

				element.waypoint(function(direction){
					
					 	$(this)
							.addClass('animation-loaded')
							.trigger('animation_loaded')

					}
					, { offset: '85%' 
					, triggerOnce: true
				})

			
			})
		}
		
		, checkViewport: function(){
			
			var that = this
		
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