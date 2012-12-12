!function ($) {
	
	$.optPanel = {
		
		render: function( config ) {
			
			var that = this
			,	opts
			,	cascade = ['current', 'post_type', 'site_defaults']
			
			that.panel = $('.panel-section-options')
			that.sobj = config.sobj
			that.sid = config.sid
			that.clone = config.clone
			that.optConfig = $.pl.config.opts
			that.data = $.pl.data
			
			 
			that.setTabData()
			
			$.each( cascade , function(index, o) {
					
				tab = $("[data-panel='"+o+"']")
			
				opts = that.runEngine(o, config)

				if(that.optConfig[that.sid] && that.optConfig[that.sid].name)
					tab.find('legend').html( that.optConfig[that.sid].name )

				tab.find('.panel-tab-content').html( opts )
			})
			
			that.setPanel()
			
			that.setBinding()
			
			$('.ui-tabs li').on('click.options-tab', $.proxy(that.setPanel, that))
			
		}
		
		, setBinding: function(){
			var that = this
			
			$('.lstn').on('keypress blur change', function(){
				
				var scope = that.activeForm.data('scope')
				
				$.pl.data[scope] = $.extend(true, $.pl.data[scope], that.activeForm.formParams())

				
			//	console.log($.pl.data[scope])

				
			})
		}
		
		, setPanel: function(){
			var that = this
			
			$('.opt-form.isotope').isotope( 'destroy' )
			
			this.panel.find('.tab-panel').each(function(){
				if($(this).is(":visible")){
					
					that.activeForm = $(this).find('.opt-form')
				
					that.optScope = that.activeForm.data('scope')
					that.optSID = that.activeForm.data('sid')
					
					that.activeForm.isotope({
						itemSelector : '.opt'
						, layoutMode : 'masonry'
					})
					
				}
					
			})
		}
		
		, setTabData: function(){
			var that = this
		
			$tab = that.panel
				.find('.tabs-nav li')
				.attr('data-sid', that.sid)
				.attr('data-clone', that.clone)
	
		
		}
		
		, runEngine: function( tabIndex ){			
		
			var that = this
			, 	sid = that.sid
			,	clone = that.clone
			, 	optionHTML
			, 	out = ''

			if(!that.optConfig[sid]){
				return
			}

			$.each( that.optConfig[sid].opts , function(index, o) {
			
				
				
				optionHTML = that.optEngine(tabIndex, o)
				
				out += sprintf( '<div class="opt"><div class="opt-name">%s</div><div class="opt-box">%s</div></div>', o.title, optionHTML ) 

			})

			return sprintf('<form class="form-%1$s-%2$s opt-area opt-form" data-sid="%1$s" data-scope="%2$s">%3$s</form>', sid, tabIndex, out)
		}
		
		, optValue: function(tabIndex, optionKey){
			var that = this
			
			// Set option value
			if(that.data[tabIndex] && that.data[tabIndex][optionKey] && that.data[tabIndex][optionKey][that.clone])
				return that.data[tabIndex][optionKey][that.clone]
			else 
				return ''
			
		}
		
		, optEngine: function( tabIndex, o ) {

			var that = this
			, 	oHTML = ''

			o.value = that.optValue( tabIndex, o.key )

			// Multiple Options
			if( o.type == 'multi' ){
				if(o.opts){
					$.each( o.opts , function(index, osub) {
				
						oHTML += that.optEngine(tabIndex, osub) // recursive
					
					})
				}
				
			}
			
			else if( o.type == 'image_upload' ){
			
				oHTML += sprintf('<label for="%s">%s</label>', o.key, o.label )
				
				thumb = '<div class="fileupload-new thumbnail"><img src="http://www.placehold.it/50x50/EFEFEF/AAAAAA" /></div>';
				
				upload = '<div class="fileupload-preview fileupload-exists thumbnail"></div>';
				
				simg = '<span class="btn btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span><input type="file" /></span>'
				
			  	remove = '<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>'
			
				oHTML += sprintf('<div class="fileupload fileupload-new" data-provides="fileupload">%s %s %s %s</div>', thumb, upload, simg, remove)
			
			}

			// Text Options
			else if( o.type == 'text' ){
				
				oHTML += sprintf('<label for="%s">%s</label>', o.key, o.label )
				oHTML += sprintf('<input id="%1$s" name="%1$s[%2$s]" type="text" class="lstn" placeholder="" value="%3$s" />', o.key, that.clone, o.value )
				
			} 
			
			else if( o.type == 'textarea' ){
				
				oHTML += sprintf('<label for="%s">%s</label>', o.key, o.label )
				oHTML += sprintf('<textarea id="%s" class="lstn" >%s</textarea>', o.key, o.value )
				
			}
			
			// Checkbox Options
			else if ( o.type == 'check' ) {
				
				var checked = (!o.value || o.value == 'false' || o.value == '') ? '' : 'checked'
				
				oHTML +=  sprintf('<label class="checkbox"><input id="%1$s" class="lstn" type="checkbox" %2$s>%3$s</label>', o.key, checked, o.label )
				
			} 
			
			// Select Options
			else if ( o.type == 'select' ){
				
				var select_opts = ''
				
				if(o.opts){
					$.each(o.opts, function(key, s){
						select_opts += sprintf('<option value="%s">%s</option>', key, s.name)
					})
				}
				
				oHTML += sprintf('<label for="%s">%s</label>', o.key, o.label )
				oHTML += sprintf('<select id="">%s</select>', o.key, select_opts)
				
			}
			
			else {
				oHTML += sprintf('<div class="needed">%s Type Still Needed</div>', o.type)
			}
			
			// Add help block
			if ( o.help )
				oHTML += sprintf('<span class="help-block">%s</span>', o.help)
			

			return oHTML
		}
		
		
	
	}
	
	
	
}(window.jQuery);