!function ($) {

$.plExtend = {
	
	btnActions: function(){
		var that = this
		
		$('.btn-purchase-item').on('click', function(){
			
			var tbOpen = $.toolbox('open')
			
		//	var theID = $(this).closest('.x-pane').data('extend-id')
			
			if(tbOpen)
				$.toolbox('hide')
				
			
			bootbox.confirm( that.purchaseModal, function( result ){
				
				if(result == true){
					console.log('yes!')
					
				} else {
					console.log('no!')
					
					if( tbOpen )
						$.toolbox('show')
				}
				
			})
		})
	
	}
	, actionButtons: function( data ){
		var buttons = ''
		, 	theID	= data.extendId
		,	ext = $.pl.config.extensions[theID] || false
		,	overviewLink = ext.overview || false
		,	demoLink = ext.demo || false
	
		buttons += sprintf('<a href="#" class="btn btn-primary btn-purchase-item x-remove %s"><i class="icon-ok"></i> Purchase</a> ', theID)
		
		if(overviewLink)
			buttons += sprintf('<a href="%s" class="btn x-remove" target="_blank"><i class="icon-folder-open"></i> Overview</a> ', overviewLink)
			
		if(demoLink)
			buttons += sprintf('<a href="%s" class="btn x-remove" target="_blank"><i class="icon-desktop"></i> Demo</a> ', demoLink)

		
		return buttons
	}
	
	, purchaseModal: "<h3>Purchase</h3><p>Ready to purchase this thing? Testing Testing Testing </p>	<iframe style='width: 100%; height: 550px;' src='http://pagelines.campfirenow.com/6cd04'></iframe>"

}

}(window.jQuery);