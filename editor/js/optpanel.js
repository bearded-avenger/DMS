!function ($) {
	
	$.optPanel = {
		
		title : 'The Option Panel Title!'
		
		, engine: function(sectionID, cloneID) {

			var out = sprintf('<legend>%s</legend>', this.title)
			
		
			if(!option_config[sectionID] || option_config[sectionID].length == 0)
				return
				
				alert('h')
			
			$.each( option_config[sectionID][opts] , function(index, o) {
			 	
				o.value = (page_data[o.key][cloneID]) ? page_data[o.key][cloneID] : false
				
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
		
		, render: function(sectionID, cloneID) {
				
			cloneID = (cloneID != undefined) ? cloneID : 0
				
			var out = $.optPanel.engine(sectionID, cloneID)
			
			$('.tab-panel-inner').html(out)
			
		}
	
	}
	
	
	
}(window.jQuery);