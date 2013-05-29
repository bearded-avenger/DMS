!function ($) {

	// --> Initialize
	$(document).ready(function() {
		$.plCommon.init()
		$.plMobilizer.init()
	
		$(".fitvids").fitVids(); // fit videos
	
		$.plAnimate.initAnimation()
		
		
		
	})
	$(window).load(function() {
		$.plCommon.plVerticalCenter('.pl-centerer', '.pl-centered')
		$('.pl-section').on('resize', function(){
			$.plCommon.plVerticalCenter('.pl-centerer', '.pl-centered')
		})
	})
	
	$.plMobilizer = {
		
		init: function(){
			var that = this
			
			that.mobileMenu()
		}
		
		, mobileMenu: function(){
			var that = this
			, 	theBody = $('body')
			, 	menuToggle = $('.mm-toggle')
			,	siteWrap = $('.site-wrap')
			, 	mobileMenu = $('.pl-mobile-menu')
			
			mobileMenu.css('max-height', siteWrap.height()-10)
			
			menuToggle.on('click.mmToggle', function(e){
				
				e.stopPropagation()
				mobileMenu.css('max-height', siteWrap.height())
				
				if( !siteWrap.hasClass('show-mm') ){
					
					siteWrap.addClass('show-mm')
					
					$('.site-wrap, .mm-close').one('click touchstart', function(){
						
						siteWrap.removeClass('show-mm')
					})
					
					
					
					$('.mm-holder').waypoint(function() {
						siteWrap.removeClass('show-mm')
					}, {
						offset: function() {
							return -$(this).height();
						}
					})
					
				} else {
					
					siteWrap.removeClass('show-mm')
					
				}
			
			})
			
		
			
		}
		
	}

	$.plAnimate = {
		
		initAnimation: function(){
			
			var that = this
						
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
							setTimeout(function(){ element.addClass('animation-loaded') }, (i * 200));
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