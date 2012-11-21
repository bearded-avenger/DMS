!function ($) {
	
	$.optPanel = {
		
		title : 'The Option Panel Title!'
		
		, options: [
		    { key: 'someKey', type: 'text', name: 'Main Text', value: '123123123'}
		    , { key: 'someKey2', type: 'checkbox', name: 'Main Content', value: '--1--1--1'}
		    , { key: 'someKey3', type: 'text', name: 'Enable Something', value: 'the Value is the Value'}
		]
		
		, engine: function() {
			
			var out = sprintf('<legend>%s</legend>', this.title)
			
			$.each( this.options , function(index, o) {
			 	
				out += '<div class="opt">'
			
				if(o.type == 'text'){
					out += sprintf('<label>%s</label>', o.name)
					out += sprintf('<input id="%1$s" type="text" placeholder="" ng-model="%1$s" value="%2$s" />', o.key, o.value )
					
				} else if ( o.type == 'checkbox' ) {
					
					var checked = (!o.value || o.value == 'false') ? '' : 'checked'
					
					out +=  sprintf('<label class="checkbox"><input id="%1$s" type="checkbox" %2$s>%3$s</label>', o.key, checked, o.name )
					
				}
				
				out += '<span class="help-block"></span></div>'

			})
			
			return out
			
		}
		
		, render: function() {
				
			var out = $.optPanel.engine()
			
			$('.option-panel').html(out)
			
		}
	
	}
	
	
	
}(window.jQuery);