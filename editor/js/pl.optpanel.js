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
			that.sobj = config.sobj
			that.sid = config.sid
			that.clone = config.clone
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
			, 	cascade = ['current', 'post_type', 'site_defaults']
			, 	sid = that.config.sid
			
			$.each( cascade , function(index, o) {
					
				tab = $("[data-panel='"+o+"']")
			
				if( !that.optConfig[sid] )
					return
				else 
					opts = that.optConfig[sid].opts
				
			
				opts = that.runEngine( opts, o )

				if(that.optConfig[ sid ] && that.optConfig[ sid ].name)
					tab.find('legend').html( that.optConfig[ sid ].name )

				tab.find('.panel-tab-content').html( opts )
				
				
			})
			
		}
		
		, setBinding: function(){
			var that = this
			
			$('.lstn').on('keypress blur change', function(){
				
				var scope = that.activeForm.data('scope')
				
				$.pl.data[scope] = $.extend(true, $.pl.data[scope], that.activeForm.formParams())
				
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
		
		, optValue: function( index, key ){
			var that = this
		
			// Set option value
			if(that.data[ index ] && that.data[ index ][ key ] && that.data[ index ][ key ][that.clone])
				return that.data[ index ][ key ][that.clone]
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
			
			else if( o.type == 'color' ){
				oHTML += sprintf('<label for="%s">%s</label>', o.key, o.label )
				oHTML += sprintf('<div class="input-prepend"><span class="btn add-on trigger-color"> <i class="icon-tint"></i> </span><input type="text" id="%1$s" class="color-%1$s" value="%2$s" /></div>', o.key, o.value )
				
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
			
			else if( o.type == 'type' ){
				
				var select_opts = ''
				
				if($.pl.config.fonts){
					$.each($.pl.config.fonts, function(skey, s){
						var google = (s.google) ? ' G' : ''
						, 	webSafe = (s.web_safe) ? ' *' : ''
						, 	uri	= (s.google) ? s.gfont_uri : ''
						
						select_opts += sprintf('<option data-family=\'%s\' data-gfont=\'%s\' value="%s">%s%s%s</option>', s.family, uri, skey, s.name, google, webSafe)
					})
				}
				
				oHTML += sprintf('<label for="%s">%s</label>', o.key, o.label )
				oHTML += sprintf('<select id="%s" class="font-selector">%s</select>', o.key, select_opts)
				
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
			
				$('.color-'+o.key).colorpicker({
					onSelect: function(color, inst){}
				})
				
			}
		
		}
	
	}
	
	
	
}(window.jQuery);