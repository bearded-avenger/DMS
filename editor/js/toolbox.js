/* =========================================================
 * PageLines Toolbox -- Copyright PageLines 2012
 * =========================================================
 */

!function ($) {

  "use strict"; // jshint


 /* MODAL CLASS DEFINITION
  * ====================== */

	var ToolBox = function (element, options) {
		
	    this.options = options
    
		this.$element = $(element)
		
		
	
		this.$panel = this.$element.find('.toolbox-panel')
		
		this.$pusher =  $('.pl-toolbox-pusher')
		
		this.resizer = $('.resizer-handle')
		this.closer = $('.btn-closer')
		this.handleHeight = 30

		this.resizePanel()
		this.scrollPanel()
		
		this.closer.on('click.toolbox.toggler', $.proxy(this.hide, this))
			

		// TODO needs to work w/ multiple tabbing
		$('.tabbed-set').tabs()
	
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
        this.keyboard() 

		that.setHeight()

		that.$panel
			.show()
			.css('margin-bottom', 0)
			.addClass('in')
            .focus()
	
		that.$pusher
			.show()
	
		this.resizer
			.show()
		
		this.closer
			.fadeIn()
	}

    , hide: function (e) {
	
        var that = this
		,	e = $.Event('hide')
		, 	ht = this.$panel.height()
		
        //if (!this.isShown || e.isDefaultPrevented()) return

        this.isShown = false
			
        $('body')
			.removeClass('toolbox-open')
		
        this.keyboard()
		
        this.$panel
          	.removeClass('in')
			.css('margin-bottom', ht * -1)
		
		this.resizer
			.hide()
		
		this.closer
			.hide()
		
		that.$pusher
			.height(that.handleHeight)
	
      }

	, setHeight: function( newHeight ) {
		
		var obj = this
		,	originalHeight = 440
		,	savedHeight = Number( localStorage.getItem('toolboxHeight') )
		, 	handleHeight = obj.handleHeight
	
		if( newHeight !== undefined ){
		
			obj.$panel.height( newHeight )
			obj.$pusher.height( newHeight + handleHeight )
			
			localStorage.setItem('toolboxHeight', newHeight)
			
		} else {
		
			if( !savedHeight ){
				
				obj.$panel.height( originalHeight )
				obj.$pusher.height( originalHeight + handleHeight)
				
				localStorage.setItem('toolboxHeight', originalHeight)
				
			} else {
				
				obj.$panel.height( savedHeight )
				obj.$pusher.height( savedHeight + handleHeight)
				
				localStorage.setItem('toolboxHeight', savedHeight)
			}
			
			
		}
		
	}

	, resizePanel: function() {
		
		var obj = this
		
		this.resizer.on('mousedown', function(evnt) {
			
			evnt.stopPropagation()
			
			var startY = evnt.pageY
			, 	startHeight = obj.$panel.outerHeight()
			
			obj.resizer.addClass('resizing')
			
			$('body').addClass('disable-select')

			$(document).on('mousemove.resizehandle', function(e) {
				
				var newY = e.pageY
				,	newHeight = Math.max(0, startHeight + startY - newY)
		
				if(newY > 30 && newHeight > 50){
					obj.setHeight(newHeight)
				}
			})
			
		})
		
		$(document).mouseup(function(event) {
			$(document).off('mousemove.resizehandle')
			obj.resizer.removeClass('resizing')
			$('body').removeClass('disable-select')
		})
		
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
	
	, keyboard: function () {
		var that = this
		
		// Escape key
		if ( this.isShown ) {
		
			$('body').on('keyup.dismiss.toolbox', function ( e ) {
				e.which == 27 && that.hide()
			})
		} else if (!this.isShown) {
			this.$panel.off('keyup.dismiss.toolbox')
		}

	}
	

  }


/* MODAL PLUGIN DEFINITION
 * ======================= */

	$.fn.toolbox = function ( option ) {

		return this.each( function() {
			var tbSelector = $('.pl-toolbox')
			,	toolBoxObject = tbSelector.data('toolbox')
			,	options = $.extend({}, $.fn.toolbox.defaults, tbSelector.data(), typeof option == 'object' && option)

			if ( !toolBoxObject ) 
				tbSelector.data( 'toolbox', ( toolBoxObject = new ToolBox( tbSelector, options ) ) )

			// Action
			if ( typeof option == 'string' ) 
				toolBoxObject[option]()
			else if ( $.isFunction( options.action ) )
				options.action.call( this )
			else if ( options.action == 'show' ) 
				toolBoxObject.show()
			else
				toolBoxObject.hide()
				
			// Panel Load
			
			if ( $.isFunction( options.panel ) )
				options.panel.call( this )
	
		})
	}

	$.fn.toolbox.defaults = {
		action: false
		, panel: false
	}

	$.fn.toolbox.Constructor = ToolBox


}(window.jQuery);