!function ($) {

	$.optPanel = {

		defaults: {
			mode: 'section-options'
			, sid: ''
			, sobj: ''
			, clone: 'settings'
			, uniqueID: 'settings'
			, panel: ''
			, settings: {}
			, objectID: ''
			, scope: 'global'
		}

		, cascade: ['local', 'type', 'global']

		, render: function( config ) {

			var that = this
			,	opts
			, 	config = config || store.get('lastSectionConfig')

			that.config = $.extend({}, that.defaults, typeof config == 'object' && config)

			var mode = that.config.mode
			,	panel = (that.config.panel != '') ? that.config.panel : mode

			store.set('lastSectionConfig', config)

			if(mode == 'object')
				store.set('lastAreaConfig', that.config.objectID)

			that.sobj = that.config.sobj
			that.sid = that.config.sid
			that.uniqueID = that.config.clone
			that.optConfig = $.pl.config.opts
			that.data = $.pl.data
			that.scope = that.config.scope || 'global'

			that.panel = $('.panel-'+panel)

			// On tab load, the activation hasn't been fired yet
			// so its hard to tell if panel is active. This is used as a workaround
			that.load = that.config.load || false

			if( mode == 'section-options' )
				that.sectionOptionRender()
			else if ( mode == 'settings' )
				that.settingsRender( that.config.settings )
			else if ( mode == 'panel' )
				that.panelRender( that.config.tab, that.config.settings.opts )

			that.onceOffScripts()

			that.setPanel()

			that.setBinding()

			$('.ui-tabs li').on('click.options-tab', $.proxy(that.setPanel, that))

		}
		
		, panelRender: function( index, theOptions ){
			var that = this
			
			tab = $("[data-panel='"+index+"']")

			opts = that.runEngine( theOptions, index )

			tab.find('.panel-tab-content').html( opts )

			that.runScriptEngine( index, theOptions )
			
		}

		, settingsRender: function( settings ) {
			var that = this

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
			,	uniqueID = that.config.clone
			, 	scope = that.scope

			if( that.optConfig[ uniqueID ] && !$.isEmptyObject( that.optConfig[ uniqueID ].opts ) )
				opt_array = that.optConfig[ uniqueID ].opts
			else{

				opt_array = [{
					help: "There are no options for this section."
					, key: "no-opts"
					, label: "No Options"
					, title: "No Options"
					, type: "help"

				}]
			}

			$.each(cascade, function( i, scope ){
		
				var sel = sprintf("[data-panel='%s']", scope)
				, 	clone_text = sprintf('<i class="icon-screenshot"></i> %s <i class="icon-map-marker"></i> %s scope', uniqueID, scope)
				, 	clone_desc = sprintf(' <span class="clip-desc"> &rarr; %s</span>', clone_text)
				
				tab = $(sel)
				
				tab.attr('data-clone', uniqueID)

				opts = that.runEngine( opt_array, scope )

				if(that.optConfig[ uniqueID ] && that.optConfig[ uniqueID ].name)
					tab.find('legend').html( that.optConfig[ uniqueID ].name + clone_desc)

				tab.find('.panel-tab-content').html( opts )

				that.runScriptEngine( 0, opt_array )
			
			})
			
			var theTabs = $('[data-key="section-options"]')
		
			var section = $('section[data-clone="'+uniqueID+'"]')
			,	panelScope
			
			if( section.closest('[data-region="header"]').length || section.closest('[data-region="footer"]').length )
				panelScope = 'global'
			else 
				panelScope = $.pl.config.templateMode
				
			
			
			$('[data-tab-action]').show()
			
			if(panelScope == 'global'){
				
				theTabs.tabs("option", {
				    "disabled": [1]
				})
				theTabs.tabs( "option", "active", 0 )
				$('[data-tab-action="type"]').hide()
				
			} else if(panelScope == 'local'){
				
				theTabs.tabs("option", {
				    "disabled": [0, 1]
				})
				theTabs.tabs( "option", "active", 2 )
				$('[data-tab-action="global"], [data-tab-action="type"]').hide()
				
			} else {
				theTabs.tabs("option", {
				    "disabled": [0]
				})
				theTabs.tabs( "option", "active", 1 )
				$('[data-tab-action="global"]').hide()
			}
			

		}

		, checkboxDisplay: function( checkgroup ){

			var	globalSet = ( $('.scope-global.checkgroup-'+checkgroup).find('.check-standard .checkbox-input').is(':checked') ) ? true : false
			,	typeSet = ( $('.scope-type.checkgroup-'+checkgroup).find('.check-standard .checkbox-input').is(':checked') ) ? true : false
			,	typeFlipSet = ( $('.scope-type.checkgroup-'+checkgroup).find('.check-flip .checkbox-input').is(':checked') ) ? true : false

			$.each( this.cascade , function(index, currentScope) {

				var showFlip = false

				if( currentScope != 'global' && globalSet )
					showFlip = true

				if( !showFlip && currentScope == 'local' && typeSet )
					showFlip = true

				if( currentScope == 'local' && showFlip && typeFlipSet && globalSet )
					showFlip = false

				var theSelector = sprintf('.scope-%s.checkgroup-%s ', currentScope, checkgroup)

				if(showFlip){
					$( theSelector + '.check-flip').show()
					$( theSelector + '.check-standard').hide()
				} else {
					$( theSelector + '.check-flip').hide()
					$( theSelector + '.check-standard').show()
				}


			})

		}

		, setBinding: function(){
			var that = this

			$('.lstn').on('keyup blur change paste', function( e ){

				var theInput = $(this)
				,	thePanel = theInput.closest('.tab-panel')
				, 	panelScope = thePanel.data('scope')
				,	scope = (panelScope) ? panelScope : that.scope
				,	uniqueID = (thePanel.attr('data-clone')) ? thePanel.attr('data-clone') : false

			
				if($(this).hasClass('checkbox-input')){

					var checkToggle = $(this).prev()
					,	checkGroup = $(this).closest('.checkbox-group').data('checkgroup')

					if( $(this).is(':checked') )
					    checkToggle.val(1)
					else
					    checkToggle.val(0)

					that.checkboxDisplay( checkGroup )

				}

				$.pl.data[scope] = $.extend(true, $.pl.data[scope], that.activeForm.formParams())
		
				$.pl.flags.refreshOnSave = true;

				if(uniqueID)
					var sel = sprintf('[data-clone="%s"] [data-sync="%s"]', uniqueID, theInput.attr('id'))
				else 	
					var sel = sprintf('[data-sync="%s"]', theInput.attr('id'))
					
			

				if( $( sel ).length > 0 ){
					
					$( sel ).each(function(i){
						var el = $(this)
						,	syncMode = el.data('sync-mode') || ''
						,	syncPrepend = el.data('sync-pre') || ''
						,	syncPost = el.data('sync-post') || ''
						,	syncTarget = el.data('sync-target') || ''
						,	tagName = el.prop('tagName')
						, 	myValue = pl_do_shortcode(theInput.val())

						if( tagName == 'IMG'){

							el.attr('src', myValue)

						} else if(syncMode == 'css') {
							el.css( syncTarget, myValue + syncPost)
						} else {
							el.html(myValue)
						}

					})
					
				} else {
				
					$.pl.flags.refreshOnSave = true
					$('.btn-save i').addClass('icon-refresh').removeClass('icon-ok')
				}

				

				if(e.type == 'change' || e.type == 'blur'){
					
					$.plAJAX.saveData( )
				}



			})
		}

		, setPanel: function(){
			var that = this

			$('.opt-form.isotope').isotope( 'destroy' )
			
			that.panel.find('.tab-panel').each(function(){
				
				if( $(this).is(":visible") || that.load == $(this).data('panel') ){
					
					that.activeForm = $(this).find('.opt-form')
					
					that.optScope = that.activeForm.data('scope')
					that.optSID = that.activeForm.data('sid')

					that.activeForm.imagesLoaded( function(){
						
						that.activeForm.isotope({
							itemSelector : '.opt'
							, masonry: {
								columnWidth: 315
							  }
							, layoutMode : 'masonry'
							, sortBy: 'number'
							, getSortData : {
								number : function ( $elem ) {
									return $elem.data('number');
								}
							}
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
				.attr('data-clone', that.uniqueID)


		}

		, runEngine: function( opts, tabKey ){

			var that = this
			, 	optionHTML
			, 	out = ''

			$.each( opts , function(index, o) {

				var specialClass = ''
				, 	number = index
				,	theTitle = o.title || o.label || 'Option'

				if(!o.key)
					o.key = 'no-key'

				if(o.span)
					specialClass += 'opt-span-'+o.span

				optionHTML = that.optEngine( tabKey, o )

				out += sprintf( '<div class="opt opt-%s %s" data-number="%s"><div class="opt-name">%s</div><div class="opt-box">%s</div></div>', o.key, specialClass, number, theTitle, optionHTML )

			})


			return sprintf('<form class="form-%1$s-%2$s form-scope-%2$s opt-area opt-form" data-sid="%1$s" data-scope="%2$s">%3$s</form>', that.sid, tabKey, out)


		}

		, optValue: function( scope, key ){
			var that = this

			var that = this
			, 	pageData = $.pl.data

			// global settings are always related to 'global'
			if (that.config.mode == 'settings' || that.config.mode == 'panel')
				scope = 'global'

			// Set option value
			if( pageData[ scope ] && pageData[ scope ][ that.uniqueID ] && pageData[ scope ][ that.uniqueID ][ key ]){
			
			
				return pl_html_input( pageData[ scope ][ that.uniqueID ][ key ] )
			}
				
			else
				return ''


		}

		, optName: function( scope, key, type ){

			if(o.type == 'check'){

			} else {
				return sprintf('%s[%s]', that.uniqueID,  key )
			}

		}

		, optEngine: function( tabIndex, o, optLevel ) {

			var that = this
			, 	oHTML = ''
			, 	scope = (that.config.mode == 'settings' || that.config.mode == 'panel') ? 'global' : tabIndex
			, 	level = optLevel || 1
			,	optLabel = o.label || o.title
			,	sel = sprintf('[data-clone="%s"] [data-sync="%s"]', that.uniqueID, o.key)
			,	syncType = (o.type != 'multi' && $(sel).length > 0) ? 'exchange' : 'refresh'
			,	syncTooltip = (syncType == 'refresh') ? 'Refresh for preview.' : 'Syncs with element.'
			,	syncIcon = (syncType == 'refresh') ? 'refresh' : 'exchange' 
			,	optDefault = o.default || ''


			o.classes = o.classes || ''
			//o.label = o.label || o.title
			
			if( o.type != 'edit_post' && o.type != 'link' && o.type != 'action_button' ){
				optLabel += sprintf(' <span data-key="%s" class="pl-help-text btn btn-mini pl-tooltip sync-btn-%s" title="%s"><i class="icon-%s"></i></span>', o.key, syncType, syncTooltip, syncIcon)
			}
				
				
				
			o.value =  that.optValue( tabIndex, o.key )
			o.name = sprintf('%s[%s]', that.uniqueID, o.key )




			if( o.type == 'multi' ){
				if(o.opts){
					$.each( o.opts , function(index, osub) {

						oHTML += that.optEngine(tabIndex, osub, 2) // recursive

					})
				}

			}

			else if( o.type == 'disabled' ){ }

			else if( o.type == 'color' ){
				
				var prepend = '<span class="btn add-on trigger-color"> <i class="icon-tint"></i> </span>'
				,	colorVal = (o.value != '') ? o.value : optDefault
				
				oHTML += sprintf('<label for="%s">%s</label>', o.key, optLabel )
				oHTML += sprintf('<div class="input-prepend">%4$s<input type="text" id="%1$s" name="%3$s" class="lstn color-%1$s" value="%2$s" /></div>', o.key, o.value, o.name, prepend )

			}

			else if( o.type == 'image_upload' ){

			  	var size = o.imgsize+'px' || '100%'
				,	sizeMode = o.sizemode || 'width'
				,	remove = '<a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>'
				,	thm = (o.value != '') ? sprintf('<div class="img-wrap"><img src="%s" style="max-%s: %s" /></div>', o.value, sizeMode, size) : ''

				oHTML += '<div class="img-upload-box">'

				oHTML += sprintf('<div class="upload-thumb-%s upload-thumb" data-imgstyle="max-%s: %s">%s</div>', o.key, sizeMode, size, pl_do_shortcode(thm));

				oHTML += sprintf('<label for="%s">%s</label>', o.key, optLabel )

				oHTML += sprintf('<input id="%1$s" name="%2$s" type="text" class="lstn text-input upload-input" placeholder="" value="%3$s" />', o.key, o.name, o.value )

				oHTML += sprintf('<div id="upload-%1$s" class="fineupload upload-%1$s fileupload-new" data-provides="fileupload"></div>', o.key)

				oHTML += '</div>'

			}

			// Text Options
			else if( o.type == 'text' ){

				oHTML += sprintf('<label for="%s">%s</label>', o.key, optLabel )
				oHTML += sprintf('<input id="%1$s" name="%2$s" type="text" class="%4$s lstn" placeholder="" value="%3$s" />', o.key, o.name, o.value, o.classes)

			}

			else if( o.type == 'textarea' ){

				oHTML += sprintf('<label for="%s">%s</label>', o.key, optLabel )
				oHTML += sprintf('<textarea id="%s" name="%s" class="%s type-textarea lstn" >%s</textarea>', o.key, o.name, o.classes, o.value )

			}

			else if( o.type == 'select_menu' ){

				var select_opts = ''
				,	menus = $.pl.config.menus
				,	configure = $.pl.config.urls.menus

				if($.pl.config.menus){
					$.each($.pl.config.menus, function(skey, s){
						var selected = (o.value == s.term_id) ? 'selected' : ''

						select_opts += sprintf('<option value="%s" %s >%s</option>', s.term_id, selected, s.name)
					})
				}

				oHTML += sprintf('<label for="%s">%s</label>', o.key, optLabel )
				oHTML += sprintf('<select id="%s" name="%s" class="font-selector lstn"><option>&mdash; Select Menu &mdash;</option>%s</select>', o.key, o.name, select_opts)

				oHTML += sprintf('<a href="%s" class="btn btn-mini" ><i class="icon-edit"></i> %s</a>', configure, 'Configure Menus' )
			}

			else if( o.type == 'action_button' ){

				oHTML += sprintf('<a href="#" data-action="%s" class="btn settings-action %s" >%s</a>', o.key, o.classes, optLabel )

			}

			else if( o.type == 'edit_post' ){
				var editLink = $.pl.config.urls.editPost

				oHTML += sprintf('<a href="%s" class="btn %s" >%s</a>', editLink, o.classes, optLabel )

			}

			else if( o.type == 'link' ){

				oHTML += sprintf('<div class="center"><a href="%s" class="btn %s" target="_blank" >%s</a></div>', o.url, o.classes, optLabel )

			}


			// Checkbox Options
			else if ( o.type == 'check' ) {

				var checked = (!o.value || o.value == 0 || o.value == '') ? '' : 'checked'
				,	toggleValue = (checked == 'checked') ? 1 : 0
				,	aux = sprintf('<input name="%s" class="checkbox-toggle" type="hidden" value="%s" />', o.name, toggleValue )
				, 	keyFlip = o.key +'-flip'
				,	valFlip =  that.optValue( tabIndex, keyFlip)
				, 	checkedFlip = (!valFlip || valFlip == 0 || valFlip == '') ? '' : 'checked'
				,	toggleValueFlip = (checkedFlip == 'checked') ? 1 : 0
				, 	nameFlip = sprintf('%s[%s]', that.uniqueID, keyFlip)
				,	labelFlip = (o.fliplabel) ? o.fliplabel : '( <i class="icon-undo"></i> reverse ) ' + optLabel
				,	auxFlip = sprintf('<input name="%s" class="checkbox-toggle" type="hidden" value="%s" />', nameFlip, toggleValueFlip )
				, 	showFlip = false
				, 	globalVal = (that.optValue( 'global', o.key ) == 1) ? true : false
				, 	typeVal = (that.optValue( 'type', o.key ) == 1) ? true : false
				, 	typeFlipVal = (that.optValue( 'type', keyFlip ) == 1) ? true : false


				var stdCheck =  sprintf('<label class="checkbox check-standard" >%s<input id="%s" class="checkbox-input lstn" type="checkbox" %s>%s</label>', aux, o.key, checked, optLabel )
				,	flipCheck =  (scope != 'global') ? sprintf('<label class="checkbox check-flip" >%s<input id="%s" class="checkbox-input lstn" type="checkbox" %s>%s</label>', auxFlip, keyFlip , checkedFlip, labelFlip ) : ''


				oHTML +=  sprintf('<div class="checkbox-group scope-%s checkgroup-%s" data-checkgroup="%s">%s %s</div>', scope, o.key, o.key, stdCheck, flipCheck )

			}

			// Select Options
			else if (
				o.type == 'select'
				|| o.type == 'count_select'
				|| o.type == 'count_select_same'
				|| o.type == 'select_same'
				|| o.type == 'select_taxonomy'
				|| o.type == 'select_icon'
				|| o.type == 'select_animation'
				|| o.type == 'select_multi'
				|| o.type == 'select_button'
			){

			
				var select_opts = (o.type != 'select_multi') ? '<option value="" >&mdash; Select &mdash;</option>' : ''
				
				if(o.type == 'count_select' || o.type == 'count_select_same'){

					var cnt_start = (o.count_start) ? o.count_start : 0
					,	cnt_num = (o.count_number) ? o.count_number : 10
					,	suffix = (o.suffix) ? o.suffix : ''

					o.opts = {}
					
					if( o.type == 'count_select_same' ){
						
						for(i = cnt_start; i <= cnt_num; i++)
							o.opts[i+suffix] = {name: i+suffix}
							
					} else {
						
						for(i = cnt_start; i <= cnt_num; i++)
							o.opts[i] = {name: i+suffix}
							
					}
					


				}

				if(o.type == 'select_icon'){

					var icons = $.pl.config.icons

					o.opts = {}
					$.each(icons, function(key, s){
						o.opts[ s ] = {name: s}
					})

				} else if( o.type == 'select_animation' ){

					var anims = $.pl.config.animations

					o.opts = {}
					$.each(anims, function(key, s){
						o.opts[ key ] = {name: s}
					})

				} else if( o.type == 'select_button' ){

					var btns = $.pl.config.btns

					o.opts = {}
					$.each(btns, function(key, s){
						o.opts[ key ] = {name: s}
					})

				}

				if(o.opts){

					$.each(o.opts, function(key, s){
						
						var optValue = (o.type == 'select_same') ? s : key
						,	optName = (o.type == 'select_same') ? s : s.name
						
						// Multi Select
						if(typeof o.value == 'object'){
							var selected = ''
							$.each(o.value, function(k, val){
								if(optValue == val)
									selected = 'selected'
							})
							
						} else {
							var selected = (o.value == optValue) ? 'selected' : ''
						}
							
					
						
						select_opts += sprintf('<option value="%s" %s >%s</option>', optValue, selected, optName)

					})
				}
				

				var multi = (o.type == 'select_multi') ? 'multiple' : ''
					

				oHTML += sprintf('<label for="%s">%s</label>', o.key, optLabel )
				oHTML += sprintf('<select id="%s" name="%s" class="%s lstn" data-type="%s" %s>%s</select>', o.key, o.name, o.classes, o.type, multi, select_opts)

				if(o.type == 'select_taxonomy' && o.post_type)
					oHTML += sprintf(
						'<div style="margin-bottom: 10px;"><a href="%sedit.php?post_type=%s" target="_blank" class="btn btn-mini btn-info"><i class="icon-edit"></i> Edit Sets</a></div>',
						$.pl.config.urls.adminURL,
						o.post_type
					)

			}

			else if( o.type == 'type' || o.type == 'fonts' ){

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

				oHTML += sprintf('<label for="%s">%s</label>', o.key, optLabel )
				oHTML += sprintf('<select id="%s" name="%s" class="font-selector lstn"><option>&mdash; Select Font &mdash;</option>%s</select>', o.key, o.name, select_opts)

				oHTML += sprintf('<label for="preview-%s">Font Preview</label>', o.key)
				oHTML += sprintf('<textarea class="type-preview" id="preview-%s" style="">The quick brown fox jumps over the lazy dog.</textarea>', o.key)
			}

			else if( o.type == 'template' ){
				oHTML += o.template
			}

			else if( o.type == 'help' ){

			} else {
				
				oHTML += sprintf('<div class="needed">%s Type Still Needed</div>', o.type)
			
			}

			// Add help block
			if ( o.help )
				oHTML += sprintf('<div class="help-block">%s</div>', o.help)

			// Add help block
			if ( o.ref )
				oHTML += sprintf('<div class="opt-ref"><a href="#" class="btn btn-info btn-mini btn-ref"><i class="icon-info-sign"></i> More Info</a><div class="help-block">%s</div></div>', o.ref)

			if(level == 2)
				return sprintf('<div class="input-wrap">%s</div>', oHTML)
			else
				return oHTML

		}

		, runScriptEngine: function ( tabIndex, opts ) {

			var that = this
			
			

			$.each(opts, function(index, o){
				that.scriptEngine(tabIndex, o)
			})

		}

		, onceOffScripts: function() {

			var that = this
			

			// Settings Actions
			$(".settings-action").on("click.settingsAction", function(e) {

				e.preventDefault()

				var btn = $(this)
				, 	theAction = btn.data('action')

				if( theAction == 'reset_global' || theAction == 'reset_local' || theAction == 'reset_global_child' ){

					var context = (theAction == 'reset_global') ? "global site options" : "local page options"

					,	confirmText = sprintf("<h3>Are you sure?</h3><p>This will reset <strong>%s</strong> to their defaults.<br/>(Once reset, this will still need to be published live.)</p>", context)

					,	page_tpl_import = $('[data-scope="importexport"] #page_tpl_import').attr('checked') || 'undefined'
					,	global_import = $('[data-scope="importexport"] #global_import').attr('checked') || 'undefined'
					,	type_import = $('[data-scope="importexport"] #type_import').attr('checked') || 'undefined'
					,	page_tpl_ = ('checked' == page_tpl_import ) ? '<span class="btn btn-mini btn-info">Page Templates</span>&nbsp;': ''
					,	global_ = ('checked' == global_import ) ? '<span class="btn btn-mini btn-info">Global Options</span>&nbsp;': ''
					,	type_ = ('checked' == type_import ) ? '<span class="btn btn-mini btn-info">Type Options</span>': ''
					,	savingText = 'Resetting Options'
					,	refreshText = 'Successfully Reset. Refreshing page'
					
					if( theAction == 'reset_global_child' ) {
						
						var confirmText = sprintf( "<h3>Are you sure?</h3><p>Importing this file will replace the following settings.<br /><strong>%s%s%s</strong></p>", page_tpl_, global_,type_ )
						,	savingText = 'Importing From Child Theme'
 						,	refreshText = 'Successfully Imported. Refreshing page'
					}

					var args = {
							mode: 'settings'
						,	run: theAction
						,	confirm: true
						,	confirmText: confirmText
						,	savingText: savingText
						,	refresh: true
						,	refreshText: refreshText
						, 	log: true
						,	page_tpl_import: page_tpl_import
						,	global_import: global_import
						,	type_import: type_import

					}
					
			//		console.log(theAction)

					var response = $.plAJAX.run( args )

				}
				
				if( theAction == 'reset_cache') {
					var confirmText = "<h3>Are you sure?</h3><p>This will clear the LESS/CSS cached data.</p>"
					var args = {
							mode: 'settings'
						,	run: theAction
						,	confirm: true
						,	confirmText: confirmText
						,	savingText: 'Flushing Caches'
						,	refresh: false
						,	refreshText: 'Success! Refreshing page'
						, 	log: true
					}
					var response = $.plAJAX.run( args )
				}
				
				
				if( theAction == 'opt_dump' ){
				
					var formDataObject = $('[data-scope="importexport"]').formParams()
					var dump = formDataObject.publish_config || false
					var confirmText = "<h3>Are you sure?</h3><p>This will write all settings to a config file in your child theme named pl-config.json</p>"
					
					if(dump) {
						
						var args = {
								mode: 'settings'
							,	run: 'exporter'
							,	confirm: dump
							,	confirmText: confirmText
							,	savingText: 'Exporting Options'
							,	refresh: false
							,	refreshText: ''
							, 	log: true
							,	formData: JSON.stringify( formDataObject )
						}
						var response = $.plAJAX.run( args )


					} else if( ! dump) {
						// need to make a special url here...

						var export_global = formDataObject.export_global || false
						var templates = formDataObject.templates || false
						var export_types = formDataObject.export_types || false
						var url = $.pl.config.siteURL + '?pl_exp'

						var endpoint = ''

						if( export_global ) {
							endpoint = endpoint + '&export_global=1'
						}
						if( templates ) {
							
							plPrint(templates)
							var tpls = []
							$.each( templates, function(key, value){
								if(value) {
									tpls.push(key)
								}
							})
							var tplsSlug = tpls.join('|') || false
							if(tplsSlug) {
								endpoint = endpoint + '&templates=' + tplsSlug
							}
						}
						if( export_types ) {
							endpoint = endpoint + '&export_types=1'
						}
						if(endpoint) {
							plPrint(url + endpoint)
							pl_url_refresh(url + endpoint)
						}
					}
					
				}
			})
			
			$('.checklist-tool').on('click', function (e) {
				e.preventDefault();
				var action = $(this).data('action')
				,	field = $(this).closest('fieldset')
				
				if(action == 'checkall'){
					
					field.find(':checkbox').prop('checked', true)
					
				} else if (action == 'uncheckall'){
					
					field.find(':checkbox').prop('checked', false)
					
				}
				
		    })


		
			
			$('#fileupload').fileupload({
				url: ajaxurl
				, dataType: 'json'
				, formData: {
					
				}
				, add: function(e, data){
					var toolBoxOpen = $.toolbox('open')
		
		
					$.toolbox('hide')
					var page_tpl_import = ('checked' == $('[data-scope="importexport"] #page_tpl_import').attr('checked') ) ? '<span class="btn btn-mini btn-info">Page Templates</span>&nbsp;': ''
					, global_import = ('checked' == $('[data-scope="importexport"] #global_import').attr('checked') ) ? '<span class="btn btn-mini btn-info">Global Options</span>&nbsp;': ''
					, type_import = ('checked' == $('[data-scope="importexport"] #type_import').attr('checked') ) ? '<span class="btn btn-mini btn-info">Type Options</span>': ''

					bootbox.confirm(
						sprintf( "<h3>Are you sure?</h3><p>Importing this file will replace the following settings.<br /><strong>%s%s%s</strong></p>", page_tpl_import, global_import,type_import )
						, function( result ){

							if(result == true){

								data.submit()

							} else if( toolBoxOpen ){

								$('body').toolbox('show')

							}

					})
					
				}
				, complete: function (response) {
					window.onbeforeunload = null
					bootbox.dialog( "<h3>Settings Imported</h3>" )
					var url = $.pl.config.siteURL
					pl_url_refresh(url, 2000)
				}
			})
			
			$('#fileupload').bind('fileuploadsubmit', function (e, data) {
			    
			    data.formData = {
					action: 'upload_config_file'
					, mode: 'fileupload'
					, refresh: true
					, refreshText: 'Imported Settings'
					, savingText: 'Importing'
					, run: 'upload_config'
					, page_tpl_import: $('[data-scope="importexport"] #page_tpl_import').attr('checked')
					, global_import: $('[data-scope="importexport"] #global_import').attr('checked')
					, type_import: $('[data-scope="importexport"] #type_import').attr('checked')
				}
				return true

			})


			// Color picker buttons
			$('.trigger-color').on('click', function(){
				$(this)
					.next()
					.find('input')
					.focus()
			})

			// Font previewing
			$('.font-selector, .font-weight').on('change', function(){

				var selector = $(this).closest('.opt').find('.font-selector')
				that.loadFontPreview( selector )

			})
			$('.font-selector, .font-style').on('change', function(){

				var selector = $(this).closest('.opt').find('.font-selector')
				that.loadFontPreview( selector )

			})

			// Image Uploader
			$('.upload-input').on('change', function(){

				var val = $(this).val()
				,	closestOpt = $(this).closest('.opt')

				if(val){
					closestOpt.find('.rmv-upload').fadeIn()
				} else {
				//	closestOpt.find('.upload-thumb').fadeOut()
					closestOpt.find('.rmv-upload').fadeOut()
				}

			})

			$('.rmv-upload').on('click', function(){
				$(this).closest('.opt').find('.upload-input').val('').trigger('change')
				$(this).closest('.opt').find('.upload-thumb').fadeOut()
			})
			
			// Tooltips inside of options
			$('.pl-tooltip')
				.tooltip({placement: 'top'})
				
			// Syncing buttons
			$('.sync-btn-exchange').on('click', function(e){
				
				e.preventDefault()
				
				var btn = $(this)
				,	key = btn.data('key')
				,	sel = sprintf('[data-clone="%s"] [data-sync="%s"]', that.uniqueID, key)
				,	el = $( sel )
				, 	offTop = el.offset().top - 120
				
				
				// Add Actions
				btn.find('i').addClass('icon-spin')
				el.removeClass('stop-focus').addClass('pl-focus')
				
				// Remove Actions
				setTimeout(function () {
				    el.addClass('stop-focus')
					btn.find('i').removeClass('icon-spin')
				}, 1000);
				
				// Scroll Page
				jQuery('html,body').animate({scrollTop: offTop}, 500);
				
				
			})
			
			$('.sync-btn-refresh').on('click', function(e){
			
				e.preventDefault()
				$(this).find('i').addClass('icon-spin')
				window.onbeforeunload = null
				location.reload()
			})

			// Reference Help Toggle
			$('.btn-ref').on('click.ref', function(){
				var closestRef = $(this).closest('.opt-ref')
				,	closestHelp = closestRef.find('.help-block')

				if(closestRef.hasClass('ref-open')){
					closestRef.removeClass('ref-open')
					closestHelp.hide()
				} else {
					closestRef.addClass('ref-open')
					closestHelp.show()
				}

				closestRef.closest('.isotope').isotope( 'reLayout' )

			})
		}

		, loadFontPreview: function( selector ) {

			var	key = selector.attr('id')
			,	selectOpt = selector.find('option:selected')
			, 	fam = selectOpt.data('family')
			, 	uri	= selectOpt.data('gfont')
			, 	ggl	= (uri != '') ? true : false
			, 	loader = 'loader'+key
			, 	weight = selector.closest('.opt').find('.font-weight').val()
			, 	weight = (weight) ? weight : 'normal'
			, 	style = selector.closest('.opt').find('.font-style').val()
			, 	style = (style) ? style : ''

			if(ggl){
				if( $('#'+loader).length != 0 )
					$('#'+loader).attr('href', uri)
				else
					$('head').append( sprintf('<link rel="stylesheet" id="%s" href="%s" />', loader, uri) )
			} else {
				$('#'+loader).remove()
			}

			selector
				.next()
				.next()
				.css('font-family', fam)
				.css('font-weight', weight)
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

				var dflt = ( isset( o.default ) ) ? o.default : '#ffffff'
				
				dflt = dflt.replace('#', '');

				$( '.color-'+o.key ).colorpicker({
					color: dflt
					, allowNull: true
					, beforeShow: function(input, inst){
					
					}
					, onClose: function(color, inst){

						$(this).change() // fire to set page data
					}
				})

			}

			else if( o.type == 'check' ){

				that.checkboxDisplay( o.key )

			}

			else if(  o.type == 'type' ||  o.type == 'fonts' ){


				that.loadFontPreview( $( sprintf('#%s.font-selector', o.key) ) )

			}

			else if( o.type == 'image_upload' ){
				var val = o.value
				, 	sizeLimit = o.sizelimit || 512000 // 500 kB

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
							sizeLimit: sizeLimit
						}
					,	text: {
							uploadButton: '<i class="icon-upload"></i> Upload Image'
						}
					// , 	debug: true
					,	template: '<div class="qq-uploader span12">' +
					                      '<pre class="qq-upload-drop-area span12"><span>{dragZoneText}</span></pre>' +
					                      '<div class="qq-upload-button btn btn-primary" style="width: auto;">{uploadButtonText}</div> <div class="btn rmv-upload"><i class="icon-remove"></i></div>' +
					                      '<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="icon-spinner icon-spin spin-fast"></span></span>' +
					                      '<ul class="qq-upload-list" style="margin-top: 10px; text-align: center;"></ul>' +
					                    '</div>'

				}).on('complete', function(event, id, fileName, response) {

					var optBox = $(this).closest('.img-upload-box')

						if (response.success) {
							var theThumb = optBox.find('.upload-thumb')
							, 	imgStyle = theThumb.data('imgstyle')


							theThumb.fadeIn().html( sprintf('<div class="img-wrap"><img src="%s" style="%s"/></div>', response.url, imgStyle ))
							optBox.find('.text-input').val(response.url).change()
							optBox.imagesLoaded( function(){
								optBox.closest('.isotope').isotope( 'reLayout' )
							})

						}
				})

			}

		}

	}



}(window.jQuery);