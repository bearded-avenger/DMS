!function ($) {
	
	$.optPanel = {
		
		title : 'The Option Panel Title!'
		
		, engine: function( config ) {

			
			var sid = config.sid
			,	clone = config.clone
			
			if(!option_config[sid] || option_config[sid].length == 0)
				return
				
			var out = sprintf('<legend>%s</legend>', this.title)
			
			$.each( option_config[sid].opts , function(index, o) {
			 	
				o.value = (page_data[o.key][clone]) ? page_data[o.key][clone] : false
				
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
			
			var out = $.optPanel.engine(config)
			
			$('.tab-panel-inner').html(out)
			
		}
	
	}
	
	
	
}(window.jQuery);