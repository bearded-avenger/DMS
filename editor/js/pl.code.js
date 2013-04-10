!function ($) {

$.plCode = {
	
	activateLESS: function( ){
	
		var lessText = $(".custom-less")
		,	scriptsText = $(".custom-scripts")
		
		
		if( !lessText.hasClass('mirrored') ){
			
			var editor = CodeMirror.fromTextArea( lessText.addClass('mirrored').get(0), {
					lineNumbers: true
				,	mode: 'text/x-less'
				, 	lineWrapping: true
				, 	onKeyEvent: function(instance, e){
					
					lessText.val( instance.getValue() )
					var theCode = lessText.parent().formParams()
					
					$.pl.data.global = $.extend(true, $.pl.data.global, theCode)
					
					// Keyboard shortcut for live LESS previewing
					if(e.type == 'keydown' && e.which == 13 && (e.metaKey || e.ctrlKey) ){
						$('#pl-custom-less').text(instance.getValue())
						
					}
				

				}
			})
			
			editor.on('blur', function(instance, changeObj){
				
				$.plAJAX.saveData(	)
			})
			
			
		}
		
		
	}
	
	, 	activateScripts: function(){
		
		var lessText = $(".custom-less")
		,	scriptsText = $(".custom-scripts")
		
		if( !scriptsText.hasClass('mirrored') ){
		
			var editor2 = CodeMirror.fromTextArea( scriptsText.addClass('mirrored').get(0), {
					lineNumbers: true
				,	mode: 'htmlmixed'
				, 	lineWrapping: true
				, 	onKeyEvent: function(instance, e){
					
					scriptsText.val( instance.getValue() )
					var theCode = scriptsText.parent().formParams()
					
					$.pl.data.global = $.extend(true, $.pl.data.global, theCode)
				

				}
				
			})
			editor2.on('blur', function(instance, changeObj){

				$.plAJAX.saveData(	)
			})
	
		}
	}

}

}(window.jQuery);