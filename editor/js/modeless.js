/* =========================================================
 * ModeLess
 * =========================================================
 */

!function ($) {

  "use strict"; // jshint


 /* MODAL CLASS DEFINITION
  * ====================== */

	var ToolBox = function (element, options) {
	
	    this.options = options
    
		this.$element = $(element)
			.delegate('[data-toggle="toolbox"]', 'click.dismiss.toolbox', $.proxy(this.toggle, this))
	
		this.$panel = this.$element
			.find('.toolbox-panel')
		
		this.resizer = $('.h-resizer')
		this.toggler = $('.h-toggler')

		this.resizePanel()
		this.scrollPanel()

		// TODO needs to work w/ multiple tabbing
		jQuery('.tabbed-set').tabs()
	
	}

  ToolBox.prototype = {

      constructor: ToolBox

    , toggle: function () {
		return this[!this.isShown ? 'show' : 'hide']()
	}

    , show: function () {
	
        var that = this
		,	e = $.Event('show')

        if (this.isShown || e.isDefaultPrevented()) return

        $('body').addClass('toolbox-open')

        this.isShown = true
        this.escape() 

		that.$panel
			.show()
			.css('margin-bottom', 0)
			.addClass('in')
            .focus()
	
		this.resizer
			.show()
		
		this.toggler
			.find('i')
			.removeClass('icon-chevron-up')
			.addClass('icon-chevron-down')
	}

    , hide: function (e) {
	
        e && e.preventDefault()

        var that = this
		,	e = $.Event('hide')
		, 	ht = this.$panel.height()
		
		// Method
        this.$panel.trigger(e)

        if (!this.isShown || e.isDefaultPrevented()) return

        this.isShown = false

        $('body')
			.removeClass('toolbox-open')
		
        this.escape()
		
        this.$panel
          	.removeClass('in')
			.css('margin-bottom', ht * -1)
		
		this.resizer
			.hide()
	
		this.toggler
			.find('i')
			.removeClass('icon-chevron-down')
			.addClass('icon-chevron-up')
      }

		, escape: function () {
			var that = this
		
			if (this.isShown && this.options.keyboard) {
				this.$panel.on('keyup.dismiss.toolbox', function ( e ) {
					e.which == 27 && that.hide()
				})
			} else if (!this.isShown) {
				this.$panel.off('keyup.dismiss.toolbox')
			}
		
		}
		
		, resizePanel: function() {
			
			var obj = this;
			
			this.resizer.on('mousedown', function(evnt) {
				
				var startY = evnt.pageY
				, 	startHeight = obj.$panel.outerHeight()
	
				$(document).on('mousemove.resizehandle', function(e) {
					
					var newY = e.pageY
					,	newHeight = Math.max(0, startHeight + startY - newY)
			
					if(newY > 30)
						obj.$panel.css('height', newHeight)
				});
			})
			
			$(document).mouseup(function(event) {
				$(document).unbind('mousemove.resizehandle')
			});
			
		}
		, scrollPanel: function() {
			
			var obj = this;
			
			obj.$panel.bind('mousewheel', function(e, d) {
					
				var	height = obj.$panel.height()
				,	scrollHeight = obj.$panel[0].scrollHeight
			
		    	if((this.scrollTop === (scrollHeight - height) && d < 0) || (this.scrollTop === 0 && d > 0)) {
					e.preventDefault()
		    	}
			})
			
		}

  }


 /* MODAL PLUGIN DEFINITION
  * ======================= */

  	$.fn.toolbox = function (option) {
	
		return this.each(function () {

			var $this = $(this)
			,	data = $this.data('toolbox')
			,	options = $.extend({}, $.fn.modal.defaults, $this.data(), typeof option == 'object' && option)

			if (!data) 
				$this.data('toolbox', (data = new ToolBox(this, options)))

			if (typeof option == 'string') 
				data[option]()
			else if 
				(options.show) data.show()
				
		})
  
	}

	$.fn.toolbox.defaults = {
		backdrop: true
		, keyboard: true
		, show: true
	}

  $.fn.toolbox.Constructor = ToolBox


}(window.jQuery);