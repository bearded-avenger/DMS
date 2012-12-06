!function ($) {
	
	$.optPanel = {
		
		render: function( config ) {
			
			var that = this
			,	opts
			
			that.panel = $('.panel-section-options')
			that.sid = config.sid
			that.clone = config.clone
			that.optConfig = $.PLData.optConfig
			that.pageData = $.PLData.pageData
			
			that.setTabData()
			
			opts = that.optEngine(config)
			
			that.panel.find('legend').html( this.optConfig[config.sid].name )
			
			that.panel.find('.panel-tab-content').html(opts)
			
		}
		
		, setTabData: function(){
			var that = this
		
			$tab = that.panel
				.find('.tabs-nav li')
				.attr('data-sid', that.sid)
				.attr('data-clone', that.clone)
	
		
		}
		
		, optEngine: function( config ) {

			
			var that = this
			, 	sid = config.sid
			,	clone = config.clone
			
			that.optConfig = $.PLData.optConfig
			that.pageData = $.PLData.pageData
			
			
			
			if(!that.optConfig[sid] || that.optConfig[sid].length == 0){
				return
			}
			
			var out = ''
			
			$.each( that.optConfig[sid].opts , function(index, o) {
				
				o.value = (that.pageData && that.pageData[o.key][clone]) ? that.pageData[o.key][clone] : ''
				
				out += '<div class="opt">'
			
				if(o.type == 'text'){
					out += sprintf('<label>%s</label>', o.label, o.key)
					out += sprintf('<input id="%1$s" type="text" placeholder="" ng-model="%1$s" value="%2$s" />', o.key, o.value )
					
				} else if ( o.type == 'checkbox' ) {
					
					var checked = (!o.value || o.value == 'false' || o.value == '') ? '' : 'checked'
					
					out +=  sprintf('<label class="checkbox"><input id="%1$s" type="checkbox" %2$s>%3$s</label>', o.key, checked, o.label )
					
				} else if ( o.type == 'select' ){
					
					var select_opts = ''
					
					$.each(o.opts, function(key, sid){
						select_opts += sprintf('<option value="%s">%s</option>', key, sid.name)
					})
					
					out += sprintf('<label>%s</label><select>%s</select>', o.label, select_opts)
					
				}
				
				out += sprintf('<span class="help-block">%s</span></div>', o.help)

			})
			
			return out
			
		}
		
		
	
	}
	
	
	
}(window.jQuery);