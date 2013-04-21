!function ($) {
	$(window).load(function(){
	
		$('.flipper').each(function(){
	    	var $that = $(this);
	    	var scrollSpeed, easing;
					
			(parseInt($(this).attr('data-scroll-speed'))) ? scrollSpeed = parseInt($(this).attr('data-scroll-speed')) : scrollSpeed = 700;
			($(this).attr('data-easing').length > 0) ? easing = $(this).attr('data-easing') : easing = 'linear';
			
	    	$(this).carouFredSel({
	    		circular: true,
	    		responsive: true,
		        items       : {
					width : 353,
			        visible     : {
			            min         : 1,
			            max         : 3
			        }
			    },
			    swipe       : {
			        onTouch     : true
			    },
			    scroll: {
			    	easing          : easing,
		            duration        : scrollSpeed
			    },
		        prev    : {
			        button  : function() {
			           return $that.parents('.flipper-wrap').prev(".flipper-heading").find('.flipper-prev');
			        }
		    	},
			    next    : {
		       		button  : function() {
			           return $that.parents('.flipper-wrap').prev(".flipper-heading").find('.flipper-next');
			        }
			    },
			    auto    : {
			    	play: false
			    }
		    }).animate({'opacity': 1},1300);
		
		
	    });
		
	})
}(window.jQuery);