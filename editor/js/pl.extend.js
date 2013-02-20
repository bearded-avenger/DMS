!function ($) {

$.plExtend = {
	
	drawStore: function( key ){
		
		var that = this
		,	items = $.pl.config.extend
		, 	output = ''
		,	itemMarkup = ''
		
		that.panel = $('.panel-pl-extend')
		that.panelContent = that.panel.find('.panel-tab-content')
		
		$.each( items , function(index, i) {
	
			itemMarkup += that.drawItem(i)
			
		})
		
		output = sprintf('<div class="x-list">%s</div>', itemMarkup)
		
		that.panelContent.html(output)
		
	}
	, drawItem: function(i){
	
		var img = sprintf('<div class="x-item-frame"><div class="pl-vignette"><img src="%s" /></div></div>', i.thumb)
		,	txt = sprintf('<div class="x-item-text">%s</div>', i.name)
		,	item = sprintf('<section class="x-item">%s%s</section>', img, txt)
		
		return item
		
	}

}

}(window.jQuery);