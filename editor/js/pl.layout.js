!function ($) {

$.widthResize = {
	
	startUp: function(){
		
		var	widthSel = $('.pl-content')
		
		$('body').addClass('width-resize')


		widthSel.resizable({ 
			handles: "e, w",
			minWidth: 400,
			
			start: function(event, ui){
				$('body').addClass('width-resizing')
				
				$('.btn-layout-resize').addClass('active')
			}
			
			, stop: function(event, ui){
				$('body').removeClass('width-resizing')
				$('.btn-layout-resize').removeClass('active')
				
				$.plAJAX.saveData( 'draft' )
			}
			
			, resize: function(event, ui) { 
		
				var resizeWidth = ui.size.width
				,	resizeOrigWidth = ui.originalSize.width
				,	resizeNewWidth = resizeOrigWidth + ((resizeWidth - resizeOrigWidth) * 2)
				, 	windowResizerAdjust = windowWidth - 10
				,	windowWidth = $(window).width()

				resizeNewWidth = (resizeNewWidth < 480) ? 480 : resizeNewWidth
				resizeNewWidth = ( resizeNewWidth  >= windowResizerAdjust ) ? windowResizerAdjust : resizeNewWidth
					
				var percentWidth = Math.round( ( resizeNewWidth / windowResizerAdjust ) * 100 )		
					
	
				widthSel
					.css('left', 'auto')
					.css('height', 'auto')
					.width( 'auto' )
					.attr('data-width', resizeNewWidth)
					.data('width', resizeNewWidth)
					.css('max-width', resizeNewWidth)

				// always set options w/ arrays
				$.pl.data.global.content_width_px = [resizeNewWidth+'px']
				$.pl.data.global.content_width_percent = [percentWidth+'%']
				
				$('.resize-px').html(resizeNewWidth+'px')
				$('.resize-percent').html(percentWidth+'%')
		
			}
		})
		
		$('.ui-resizable-handle')
			.effect('highlight', 2500 )
			.hover(
				function () {
					$('body').addClass("resize-hover")
				}
				, function () {
					$('body').removeClass("resize-hover")
				}
			)
		
	}
	, shutDown: function(){
		
		var	widthSel = $('.pl-content')
		
		$('body').removeClass('width-resize')
		
		$(".ui-resizable-handle").unbind('mouseenter mouseleave')
		
		widthSel.resizable( "destroy" )
		
		
		
	}
}

}(window.jQuery);