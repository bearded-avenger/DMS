!function ($) {

$.plExtend = {

	btnActions: function(){
		var that = this

		$('.btn-purchase-item').on('click', function(){

			var tbOpen = $.toolbox('open')

			var theID = $(this).data('extend-id')

			if(tbOpen)
				$.toolbox('hide')

			theModal = that.purchaseModal( theID )
			bootbox.confirm( theModal, function( result ){

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
		,	Purchase = ext.purchase || false

		if(Purchase)
		buttons += sprintf('<a href="#" class="btn btn-primary btn-purchase-item x-remove %s" data-extend-id="%s"><i class="icon-ok"></i> Purchase</a> ', theID, theID)

		if(overviewLink)
			buttons += sprintf('<a href="%s" class="btn x-remove" target="_blank"><i class="icon-folder-open"></i> Overview</a> ', overviewLink)

		if(demoLink)
			buttons += sprintf('<a href="%s" class="btn x-remove" target="_blank"><i class="icon-desktop"></i> Demo</a> ', demoLink)


		return buttons
	}

	, purchaseModal: function( theID ) {
		var		ext = $.pl.config.extensions[theID] || false
		,    	payLink = ext.purchase || false
console.debug(payLink);

		return sprintf("<iframe style='width: 100%%; height: 650px;' src='https://api.pagelines.com/paypal/checkout/DGsetExpressCheckout.php?paypal=%s'></iframe>", payLink)
	}

}

}(window.jQuery);