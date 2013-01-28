!function ($) {
	
	$.optPanel = {
		
		defaults: {
			mode: 'section-options'
			, sid: ''
			, sobj: ''
			, clone: 0
			, settings: {}
		}
		
		, render: function( config ) {
			
			var that = this
			,	opts
			
			that.config = $.extend({}, that.defaults, typeof config == 'object' && config)
			
			that.panel = $('.panel-'+that.config.mode)
			that.sobj = that.config.sobj
			that.sid = that.config.sid
			that.clone = that.config.clone
			that.optConfig = $.pl.config.opts
			that.data = $.pl.data
			
			if(that.config.mode == 'section-options')
				that.sectionOptionRender()
			else if (that.config.mode == 'settings')
				that.settingsRender( that.config.settings )
			
			that.setPanel()
			
			that.setBinding()
			
			$('.ui-tabs li').on('click.options-tab', $.proxy(that.setPanel, that))
			
		}
		
		, settingsRender: function( settings ) {
			var that = this;
			
			$.each( settings , function(index, o) {
					
				tab = $("[data-panel='"+index+"']")
			
				opts = that.runEngine( o.opts, index )

				tab.find('.panel-tab-content').html( opts )
				
				that.runScriptEngine( index, o.opts )
				
			})
		}
		
		, sectionOptionRender: function() {
			
			var that = this
			, 	cascade = ['local', 'type', 'global']
			, 	sid = that.config.sid
			
			if( that.optConfig[sid] )
				opt_array = that.optConfig[sid].opts
			else 
				return
				
			$.each( cascade , function(index, o) {
					
				tab = $("[data-panel='"+o+"']")

				opts = that.runEngine( opt_array, o )

				if(that.optConfig[ sid ] && that.optConfig[ sid ].name)
					tab.find('legend').html( that.optConfig[ sid ].name )

				tab.find('.panel-tab-content').html( opts )
				
				that.runScriptEngine( index, opt_array )
				
			})
			
		}
		
		, setBinding: function(){
			var that = this
			
			$('.lstn').on('keypress blur change', function( e ){
				
				var scope = (that.config.mode == 'section-options') ? that.activeForm.data('scope') : 'global'
				
				$.pl.data[scope] = $.extend(true, $.pl.data[scope], that.activeForm.formParams())
				
				console.log('scope: '+scope)
				console.log(e)
				console.log($.pl.data)
			})
		}
		
		, setPanel: function(){
			var that = this
			
			$('.opt-form.isotope').isotope( 'destroy' )
		
			that.panel.find('.tab-panel').each(function(){	
	
				if($(this).is(":visible")){
						
					that.activeForm = $(this).find('.opt-form')
				
					that.optScope = that.activeForm.data('scope')
					that.optSID = that.activeForm.data('sid')
					
					that.activeForm.imagesLoaded( function(){
						that.activeForm.isotope({
							itemSelector : '.opt'
							, layoutMode : 'masonry'
						})
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
		
		, runEngine: function( opts, tabKey ){			
		
			var that = this
			, 	optionHTML
			, 	out = ''			
			
			$.each( opts , function(index, o) {
			
				optionHTML = that.optEngine( tabKey, o )
				
				out += sprintf( '<div class="opt"><div class="opt-name">%s</div><div class="opt-box">%s</div></div>', o.title, optionHTML ) 

			})
		
			
			return sprintf('<form class="form-%1$s-%2$s form-scope-%2$s opt-area opt-form" data-sid="%1$s" data-scope="%2$s">%3$s</form>', that.sid, tabKey, out)

			
		}
		
		, optValue: function( scope, key ){
			var that = this
			, 	pageData = $.pl.data
		
			// global settings are always related to 'global'
			if (that.config.mode == 'settings')
				scope = 'global'
		
			// Set option value
			if( pageData[ scope ] && pageData[ scope ][ key ] && pageData[ scope ][ key ][that.clone])
				return pageData[ scope ][ key ][that.clone]
			else 
				return ''
			
		}
		
		, optName: function( index, key ){
			
		}
		
		, optEngine: function( tabIndex, o ) {

			var that = this
			, 	oHTML = ''

			o.value = that.optValue( tabIndex, o.key )
			o.name = sprintf('%s[%s]', o.key, that.clone)
				
			// Multiple Options
			if( o.type == 'multi' ){
				if(o.opts){
					$.each( o.opts , function(index, osub) {
				
						oHTML += that.optEngine(tabIndex, osub) // recursive
					
					})
				}
				
			}
			
			else if( o.type == 'color' ){
				
				var prepend = '<span class="btn add-on trigger-color"> <i class="icon-tint"></i> </span>';
				oHTML += sprintf('<label for="%s">%s</label>', o.key, o.label )
				oHTML += sprintf('<div class="input-prepend">%4$s<input type="text" id="%1$s" name="%3$s" class="lstn color-%1$s" value="%2$s" /></div>', o.key, o.value, o.name, prepend )
				
			}
			
		
			else if( o.type == 'image_upload' ){
			
				
			  	var remove = '<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>'
				,	thm = (o.value != '') ? sprintf('<img src="%s" />', o.value) : ''
			
				oHTML += sprintf('<div class="upload-thumb-%s upload-thumb">%s</div>', o.key, thm);
			
				oHTML += sprintf('<label for="%s">%s</label>', o.key, o.label )
			
				oHTML += sprintf('<input id="%1$s" name="%2$s" type="text" class="lstn text-input" placeholder="" value="%3$s" />', o.key, o.name, o.value )
		
				oHTML += sprintf('<div id="upload-%1$s" class="fineupload upload-%1$s fileupload-new" data-provides="fileupload"></div>', o.key)
			
			}

			// Text Options
			else if( o.type == 'text' ){
				
				oHTML += sprintf('<label for="%s">%s</label>', o.key, o.label )
				oHTML += sprintf('<input id="%1$s" name="%2$s" type="text" class="lstn" placeholder="" value="%3$s" />', o.key, o.name, o.value )
				
			} 
			
			else if( o.type == 'textarea' ){
				
				oHTML += sprintf('<label for="%s">%s</label>', o.key, o.label )
				oHTML += sprintf('<textarea id="%s" name="%s" class="lstn" >%s</textarea>', o.key, o.name, o.value )
				
			}
			
			// Checkbox Options
			else if ( o.type == 'check' ) {
				
				var checked = (!o.value || o.value == 'false' || o.value == '') ? '' : 'checked'
				
				oHTML +=  sprintf('<label class="checkbox"><input id="%s" name="%s" class="lstn" type="checkbox" %s>%s</label>', o.key, o.name, checked, o.label )
				
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
			
			else if( o.type == 'type' ){
				
				var select_opts = ''
				
				if($.pl.config.fonts){
					$.each($.pl.config.fonts, function(skey, s){
						var google = (s.google) ? ' G' : ''
						, 	webSafe = (s.web_safe) ? ' *' : ''
						, 	uri	= (s.google) ? s.gfont_uri : ''
						,	selected = (o.value == skey) ? 'selected' : ''
						
						select_opts += sprintf('<option data-family=\'%s\' data-gfont=\'%s\' value="%s" %s >%s%s%s</option>', s.family, uri, skey, selected, s.name, google, webSafe)
					})
				}
				
				oHTML += sprintf('<label for="%s">%s</label>', o.key, o.label )
				oHTML += sprintf('<select id="%s" name="%s" class="font-selector lstn">%s</select>', o.key, o.name, select_opts)
				
				oHTML += sprintf('<label for="preview-%s">Font Preview</label>', o.key)
				oHTML += sprintf('<textarea class="type-preview" id="preview-%s" style="">The quick brown fox jumps over the lazy dog.</textarea>', o.key)
			}
		
			
			else {
				oHTML += sprintf('<div class="needed">%s Type Still Needed</div>', o.type)
			}
			
			// Add help block
			if ( o.help )
				oHTML += sprintf('<span class="help-block">%s</span>', o.help)
			

			return oHTML
		}
		
		, runScriptEngine: function ( tabIndex, opts ) {
			
			var that = this
			
			that.onceOffScripts( tabIndex, opts )
			
			$.each(opts, function(index, o){
				that.scriptEngine(tabIndex, o)
			})
		
		}
		
		, onceOffScripts: function( tabIndex, o ) {
		
			// Color picker buttons
			$('.trigger-color').on('click', function(){
				$(this)
					.next()
					.find('input')
					.focus()
			})
			
			// Font previewing
			$('.font-selector').on('change', function(){

				var	key = $(this).attr('id')
				,	selectOpt = $(this).find('option:selected')
				, 	fam = selectOpt.data('family')
				, 	uri	= selectOpt.data('gfont')
				, 	ggl	= (uri != '') ? true : false
				, 	loader = 'loader'+key
			
				if(ggl){
					if( $('#'+loader).length != 0 )
						$('#'+loader).attr('href', uri)
					else 
						$('head').append( sprintf('<link rel="stylesheet" id="%s" href="%s" />', loader, uri) )
				} else {
					$('#'+loader).remove()
				}

				$(this)
					.next()
					.next()
					.css('font-family', fam)
			})
		
		}
		
		, scriptEngine: function( tabIndex, o ) {
		
			var that = this

				
			// Multiple Options
			if( o.type == 'multi' ){
				if(o.opts){
					$.each( o.opts , function(index, osub) {

						that.scriptEngine(tabIndex, osub) // recursive

					})
				}

			}

			else if( o.type == 'color' ){
			
				$( '.color-'+o.key ).colorpicker({
					onClose: function(color, inst){
						console.log( 'hello' )
						console.log( inst )
						$(this).change() // fire to set page data
					}
				})
				
			}
			
			else if( o.type == 'image_upload' ){
		
				$('.fineupload.upload-'+o.key).fineUploader({
					request: {
						endpoint: ajaxurl
						, 	params: {
								action: 'pl_up_image'
								,	scope: 'global'
							}
					}
					
					,	multiple: false
					,	validation: {
							allowedExtensions: ['jpeg', 'jpg', 'gif', 'png'],
							sizeLimit: 204800 // 200 kB = 200 * 1024 bytes
						}
					,	text: {
							uploadButton: '<i class="icon-upload"></i> Upload Image'
						}
					// , 	debug: true
					,	template: '<div class="qq-uploader span12">' +
					                      '<pre class="qq-upload-drop-area span12"><span>{dragZoneText}</span></pre>' +
					                      '<div class="qq-upload-button btn btn-primary" style="width: auto;">{uploadButtonText}</div>' +
					                      '<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="icon-spinner icon-spin spin-fast"></span></span>' +
					                      '<ul class="qq-upload-list" style="margin-top: 10px; text-align: center;"></ul>' +
					                    '</div>'

				}).on('complete', function(event, id, fileName, response) {
					
					var optBox = $(this).closest('.opt-box')
					
						if (response.success) {
							
							optBox.find('.upload-thumb').html( sprintf('<img src="%s" />', response.url ))
							optBox.find('.text-input').val(response.url).change()
							optBox.closest('.isotope').isotope( 'reLayout' )
						}
				})
				
			}
		
		}
	
	}
	
	
	
}(window.jQuery);