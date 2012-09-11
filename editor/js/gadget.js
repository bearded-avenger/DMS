!function ($) {
	$.gadget = {
	
        loadModeless: function() {
		
			var toolbox = $('.pl-modeless')
			,	toolboxHandle = $(".modeless-handle")
			,	toolboxPanel = $(".modeless-panel")
			
		
			toolbox
				.modeless()
			
			toolboxPanel.bind('mousewheel', function(e, d) {
					
				var	height = toolboxPanel.height()
				,	scrollHeight = toolboxPanel[0].scrollHeight
				console.log(this.scrollTop + '   -- --  ' + scrollHeight);
		    	if((this.scrollTop === (scrollHeight - height) && d < 0) || (this.scrollTop === 0 && d > 0)) {
					e.preventDefault()
		    	}
			})
		
			
			toolboxHandle.mousedown(function(evnt) {
				
				var startY = evnt.pageY
				, 	startHeight = toolboxPanel.outerHeight()
	
				$(document).mousemove(function(e) {
					
					var newY = e.pageY
					,	newHeight = Math.max(0, startHeight + startY - newY)
			
					toolboxPanel
						.css('height', newHeight)
				});
			}).mouseup(function(event) {
				$(document).unbind('mousemove')
			});
		

        },

       
    }

}(window.jQuery);