!function ($) {
	
	$.optPanel = {
		
		engine: function( config ) {

			
			var sid = config.sid
			,	clone = config.clone
			
			this.optConfig = $.PLData.optConfig
			this.pageData = $.PLData.pageData
			
			if(!this.optConfig[sid] || this.optConfig[sid].length == 0){
				return
			}
			
			var out = ''
			
			$.each( this.optConfig[sid].opts , function(index, o) {
			 	
				o.value = (this.pageData[o.key][clone]) ? this.pageData[o.key][clone] : false
				
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
		
		, render: function( config ) {
			
			var out = this.engine(config)
			
			$('.panel-section-options legend').html( this.optConfig[config.sid].name )
			$('.panel-section-options .panel-tab-content').html(out)
			
		}
		
		, drawPanel: function(){
			
		}
	
	}
	
	
	
}(window.jQuery);