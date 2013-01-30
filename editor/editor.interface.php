<?php
/**
 * 
 *
 *  PageLines Front End Template Class
 *
 *
 *  @package PageLines Framework
 *  @subpackage Sections
 *  @since 3.0.0
 *  
 *
 */
class EditorInterface {


	function __construct( PageLinesPage $pg, EditorSettings $siteset, EditorDraft $draft, EditorTemplates $templates, EditorMap $map ) {
		
		$this->page = $pg;
		$this->draft = $draft;
		$this->siteset = $siteset;
		$this->templates = $templates;
		$this->map = $map;

		add_action( 'wp_footer', array( &$this, 'control_panel' ) );
		add_action( 'wp_print_styles', array(&$this, 'pl_editor_styles' ), 15 );
		$this->url = PL_PARENT_URL . '/editor';
		$this->images = $this->url . '/images';
		
		add_action( 'wp_ajax_the_store_callback', array( &$this, 'the_store_callback' ) );
	}
	
	function pl_editor_styles(){
		
		// Global AjaxURL variable --> http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/
		wp_localize_script( 'global-ajax-url', 'PLAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		
		// PageLines Custom
		wp_enqueue_script( 'js-sprintf', $this->url . '/js/utils.sprintf.js' ); 
		wp_enqueue_script( 'pl-editor-js', $this->url . '/js/pl.editor.js' ); 
		wp_enqueue_script( 'pl-toolbox-js', $this->url . '/js/pl.toolbox.js', array('pagelines-bootstrap-all')); 
		wp_enqueue_script( 'pl-optpanel', $this->url . '/js/pl.optpanel.js'); 
		wp_enqueue_script( 'pl-ajax', $this->url . '/js/pl.ajax.js'); 
		wp_enqueue_script( 'pl-library', $this->url . '/js/pl.library.js'); 
		
		// Isotope
		wp_enqueue_script( 'isotope', $this->url . '/js/utils.isotope.js', array('jquery')); 

		// Jquery UI
		wp_enqueue_script( 'jquery-ui-tabs'); 
		
		$dep = array('jquery-ui-core','jquery-ui-widget', 'jquery-ui-mouse');	
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_enqueue_script( 'jquery-ui-resizable' );
	//	wp_enqueue_script( 'jquery-ui-sortable' );
	
	// Older sortable needs to be used for now
	// 	https://github.com/jquery/jquery-ui/commit/bae06d2b1ef6bbc946dce9fae91f68cc41abccda#commitcomment-2141597
	//	http://bugs.jqueryui.com/ticket/8810
		wp_enqueue_script( 'jquery-new-ui-sortable', PL_ADMIN_JS . '/jquery.ui.sortable.js', $dep, 1.9, true);
		
		wp_enqueue_script( 'jquery-new-ui-effect', PL_ADMIN_JS . '/jquery.ui.effect.js', $dep, 1.9, true);	
		wp_enqueue_script( 'jquery-new-ui-effect-highlight', PL_ADMIN_JS . '/jquery.ui.effect-highlight.js', array('jquery-new-ui-effect'), 1.9, true);
		wp_enqueue_script( 'jquery-mousewheel', $this->url . '/js/utils.mousewheel.js' ); 
	
		// Forms handling
		wp_enqueue_script( 'form-params', $this->url . '/js/form.params.js', array('jquery'), '1.0.0', true ); 
		wp_enqueue_script( 'form-store', $this->url . '/js/form.store.js', array('jquery'), '1.0.0', true ); 
		
		// Prettify
		wp_enqueue_script( 'codemirror', PL_ADMIN_JS . '/codemirror/codemirror.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'codemirror-css', PL_ADMIN_JS . '/codemirror/css/css.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'codemirror-less', PL_ADMIN_JS . '/codemirror/less/less.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'codemirror-js', PL_ADMIN_JS . '/codemirror/javascript/javascript.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'codemirror-xml', PL_ADMIN_JS . '/codemirror/xml/xml.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'codemirror-html', PL_ADMIN_JS . '/codemirror/htmlmixed/htmlmixed.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_style( 'codemirror', PL_ADMIN_JS . '/codemirror/codemirror.css' );
		
		// Less
		wp_enqueue_script( 'lessjs', $this->url . '/js/utils.less.js', array('jquery'), '1.3.1', true ); 
		
		// Less
		wp_enqueue_script( 'bootbox', $this->url . '/js/utils.bootbox.js', array('jquery'), '3.0.0', true );
	
		
		// Colorpicker
		wp_enqueue_style( 'css3colorpicker', $this->url . '/js/colorpicker/colorpicker.css');
		wp_enqueue_script( 'css3colorpicker', $this->url . '/js/colorpicker/colorpicker.js', array('jquery'), '1.3.1', true );
		
		// Image Uploader
		wp_enqueue_script( 'fineupload', $this->url . '/js/fineuploader/jquery.fineuploader-3.2.min.js');
		
		// Images Loaded
		wp_enqueue_script( 'imagesloaded', $this->url . '/js/utils.imagesloaded.js');



	}
	


	function region_start( $region, $area_number ){
		
		printf( 
			'<div class="pl-region-bar area-tag" data-area-number="%s"><a class="btn-region">%s</a></div>', 
			$area_number, 
			ucfirst($region) 
		); 
		
	}
	
	function area_start($a){
		
		printf( 
			'<div class="pl-area area-tag" data-area-number="%s">%s<div class="pl-content"><div class="pl-inner area-region pl-sortable-area editor-row">%s', 
			$a['area_number'], 
			$this->area_controls($a),
			$this->area_sortable_buffer()
		); 
		
	}
	
	/* 
	 * Used to allow for dropping at top of area, gets around floated element problems
	 */ 
	function area_sortable_buffer(){
		
		return sprintf('<div class="pl-sortable pl-sortable-buffer span12 offset0"></div>');
		
	}
	
	function area_end(){
		printf('%s</div></div></div>', $this->area_sortable_buffer());
	}
	

	
	function toolbar_config(){
		
		$data = array(
			
			'add-new' => array(
				'name'	=> 'Add New',
				'icon'	=> 'icon-plus-sign',
				'panel'	=> array(
					'heading'	=> "<i class='icon-random'></i> Drag to Add",
					'add_section'	=> array(
						'name'	=> 'Add Sections', 
						'clip'	=> 'Drag on to page to add',
						'type'	=> 'call',
						'call'	=> array(&$this, 'add_new_callback'),
						'filter'=> '*'
					), 
					'heading2'	=> "<i class='icon-filter'></i> Filters",
					'components'		=> array(
						'name'	=> 'Components', 
						'href'	=> '#add_section', 
						'filter'=> '.component'
					),
					'layouts'		=> array(
						'name'	=> 'Layouts', 
						'href'	=> '#add_section', 
						'filter'=> '.layout'
					),
					'formats'		=> array(
						'name'	=> 'Post Formats', 
						'href'	=> '#add_section', 
						'filter'=> '.format'
					),
					'galleries'		=> array(
						'name'	=> 'Galleries', 
						'href'	=> '#add_section', 
						'filter'=> '.gallery'
					),
					'navigation'	=> array(
						'name'	=> 'Navigation', 
						'href'	=> '#add_section', 
						'filter'=> '.nav'
					),
					'features'		=> array(
						'name'	=> 'Features', 
						'href'	=> '#add_section', 
						'filter'=> '.feature'
					),
					
					'social'	=> array(
						'name'	=> 'Social', 
						'href'	=> '#add_section', 
						'filter'=> '.social'
					),
					'widgets'	=> array(
						'name'	=> 'Widgetized', 
						'href'	=> '#add_section', 
						'filter'=> '.widgetized'
					),
					'misc'		=> array(
						'name'	=> 'Miscellaneous', 
						'href'	=> '#add_section', 
						'filter'=> '.misc'
					),
				)
			),
			
			'page-setup' => array(
				'name'	=> 'Templates',
				'icon'	=> 'icon-paste',
				'panel'	=> array(
					'heading'	=> "Page Templates",
					'tmp_load'	=> array(
						'name'	=> 'Your Templates', 
						'call'	=> array(&$this->templates, 'user_templates'),
					),
					'tmp_save'	=> array(
						'name'	=> 'Save New Template',
						'call'	=> array(&$this->templates, 'save_templates'),
					)
				)
				
			),
			'theme' => array(
				'name'	=> 'Theme',
				'icon'	=> 'icon-picture',
				'panel'	=> array(
					'heading'	=> "Select Theme",
					'tmp_load'	=> array(
						'name'	=> 'Your Templates', 
						'call'	=> array(&$this->templates, 'user_templates'),
					),
					'tmp_save'	=> array(
						'name'	=> 'Save New Template',
						'call'	=> array(&$this->templates, 'save_templates'),
					)
				)
				
			),
			
			'pl-design' => array(
				'name'	=> 'Design',
				'icon'	=> 'icon-magic',
				'panel'	=> array(
					'heading'	=> "Site Design",
					
					'user_less'	=> array(
						'name'	=> 'Custom LESS/CSS', 
						'call'	=> array(&$this, 'custom_less'),
					),
					'user_scripts'	=> array(
						'name'	=> 'Custom Scripts', 
						'call'	=> array(&$this, 'custom_scripts'),
						'flag'	=> 'custom-scripts'
					),
				)
			), 
			'settings' => array(
				'name'	=> 'Settings',
				'icon'	=> 'icon-cog',
				'panel'	=> $this->get_settings_tabs( 'site' )
			), 
			'live' => array(
				'name'	=> 'Live',
				'icon'	=> 'icon-comments',
				
				'panel'	=> array(
					'heading'	=> "<i class='icon-comments'></i> Live Support",
					'support_chat'	=> array(
						'name'	=> 'PageLines Live Chat', 
						'type'	=> 'call',
						'call'	=> array(&$this, 'live_callback'),
					),
				)
			),
			'pl-extend' => array(
				'name'	=> 'Extend',
				'icon'	=> 'icon-download',
				'panel'	=> array(
					'heading'	=> "Extend PageLines",
					'store'		=> array(
						'name'	=> 'PageLines Store', 
						'type'	=> 'call',
						'call'	=> array(&$this, 'the_store_callback'),
						'hook'	=> 'the_store_callback',
						'filter'=> '*'
					),
					'heading2'	=> "Filters",
					'plus'		=> array(
						'name'	=> 'Free with Plus', 
						'href'	=> '#store', 
						'filter'=> '.plugins'
					),
					'sections'		=> array(
						'name'	=> 'Sections', 
						'href'	=> '#store', 
						'filter'=> '.sections'
					),
					'plugins'		=> array(
						'name'	=> 'Plugins', 
						'href'	=> '#store', 
						'filter'=> '.plugins'
					),
					'themes'		=> array(
						'name'	=> 'Themes', 
						'href'	=> '#store', 
						'filter'=> '.themes'
					),
					'heading3'	=> "Tools",
					'upload'	=> array('name'	=> 'Upload'),
					'search'	=> array('name'	=> 'Search'),
				)
			),
			// 'pl-actions' => array(
			// 				'name'	=> 'Actions',
			// 				'icon'	=> 'icon-asterisk',
			// 				'type'	=> 'dropup', 
			// 				'panel'	=> array(
			// 					
			// 					'template'	=> array('name'	=> 'Preview Page'),
			// 					'revert'	=> array('name'	=> 'Revert Changes')
			// 				)
			// 				
			// 			),
			'section-options' => array(
				'name'	=> 'Section Options',
				'icon'	=> 'icon-paste',
				'type'	=> 'hidden', 
				'flag'	=> 'section-opts',
				'panel'	=> $this->section_options_panel()
				
			),
		);
		
		return $data;
		
	}
	
	function section_options_panel(){
		
		$current_page = ($this->page->is_special()) ? $this->page->type_name : $page->id;
		
		$tabs = array(); 
		$tabs['heading'] = "Section Options"; 
		
		$tabs['local'] = array( 'name'	=> 'Current Page <span class="label">'.$current_page.'</span>' ); 
		
		if(!$this->page->is_special())
			$tabs['type'] = array( 'name'	=> 'Post Type <span class="label">'.$this->page->type_name.'</span>' ); 
			
		$tabs['global'] = array( 'name'	=> 'Sitewide Defaults' );
		
		return $tabs;
		
	}

	
	function get_settings_tabs( $panel = 'site' ){
		
		$tabs = array();
		
		if($panel == 'site'){
			$tabs['heading'] = 'Global Settings'; 
		
			foreach( $this->siteset->get_set('site') as $tabkey => $tab ){
				$tabs[ $tabkey ] = array('key' => $tabkey, 'name' => $tab['name']);
			}
		
		}
		
		return $tabs;
		
	}
	
	function control_panel(){
		
		
	?>
	
	<div class="pl-toolbox-pusher">
	</div>
	<div id="PageLinesToolbox" class="pl-toolbox">
		<div class="resizer-handle"></div>
		<div class="toolbox-handle fix">
			
			<ul class="unstyled controls">
				<li ><span class="btn-toolbox btn-closer" title="Close [esc]"><i class="icon-remove-sign"></i></span></li>
				
				<?php 
				
					foreach($this->toolbar_config() as $key => $tab){
						
						if(!isset($tab['type']))
							$tab['type'] = 'panel'; 
						
						if($tab['type'] == 'hidden')
							continue;
						
						$data = '';
						$suffix = '';
						$content = '';
						$li_class = '';	
						
						if($tab['type'] == 'dropup'){
							
							$data = 'data-toggle="dropdown"';
							$suffix = ' <span class="crt icon-caret-right"></span>';
							$li_class = 'dropup';	
							$menu = ''; 
							
							foreach($tab['panel'] as $key => $i){
								$menu .= sprintf('<li><a href="">%s</a></li>', $i['name']);
							}
							$content = sprintf('<ul class="dropdown-menu">%s</ul>', $menu);
						} 
						
					
						  
						$classes = ($tab['type'] == 'panel') ? 'btn-panel' : '';
						
						printf(
							'<li class="%s"><span class="btn-toolbox %s" data-action="%s" %s><i class="uxi %s"></i> <span class="txt">%s %s</span></span>%s</li>', 
							$li_class,
							$classes,
							$key, 
							$data, 
							$tab['icon'], 
							$tab['name'], 
							$suffix, 
							$content
						);
						
					}
				?>
				
			</ul>
		
			
			
			<ul class="unstyled controls send-right">
				
				<li class="dropup">
					<?php
						$state = $this->draft->get_state( array('pageID' => $this->page->id, 'typeID' => $this->page->typeid, 'map_object' => $this->map ) );
					
					?>
					<span class="btn-toolbox btn-state " data-toggle="dropdown">
						<span id="update-state" class="state-draft <?php echo $state;?>">&nbsp;</span>
					</span>
					<ul class="dropdown-menu pull-right state-list <?php echo $state;?>">
						<li class="li-state-multi"><a class="btn-revert" data-revert="all"><span class="update-state state-draft multi">&nbsp;</span>&nbsp; Revert All Unpublished Changes</a></li>
						<li class="li-state-global"><a class="btn-revert" data-revert="global"><span class="update-state state-draft global">&nbsp;</span>&nbsp; Revert Unpublished Global Changes</a></li>
						
						<li class="li-state-type"><a class="btn-revert" data-revert="type"><span class="update-state state-draft type">&nbsp;</span>&nbsp; Revert Unpublished Post Type Changes</a></li>
						<li class="li-state-local"><a class="btn-revert" data-revert="local"><span class="update-state state-draft local">&nbsp;</span>&nbsp; Revert Unpublished Local Changes</a></li>
						<li class="li-state-clean disabled"><a class="txt"><span class="update-state state-draft clean">&nbsp;</span>&nbsp; No Unpublished Changes</a></li>
					</ul>
				</li>
				<li><span class="btn-toolbox btn-save btn-draft" data-mode="draft"><i class="icon-edit"></i> <span class="txt">Preview</span></li>
				<li><span class="btn-toolbox btn-save btn-publish" data-mode="publish"><i class="icon-check"></i> <span class="txt">Publish</span></li>
				
			</ul>
			<ul class="unstyled controls not-btn send-right">
				<li><span class="btn-toolbox btn-saving not-btn"><i class="icon-save"></i> <span class="txt">Saving</span></li>
			</ul>
		</div>
		<div class="toolbox-panel-wrap">
			<div class="toolbox-panel">
				<div class="toolbox-content fix">
					<div class="toolbox-content-pad option-panel">
						<?php 
						foreach($this->toolbar_config() as $key => $tab){
							
							if(isset($tab['panel']) && !empty($tab['panel']))
								$this->panel($key, $tab['panel']);
							else 
								printf('<div class="panel-%s tabbed-set error-panel">There was an issue rendering the panel.</div>', $key);
						}
							 ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php 
	}
	
	function layout_sections(){
		
		$defaults = array(
			'id'			=> '',
			'name'			=> 'No Name', 
			'filter'		=> 'layout',
			'screenshot'	=>  PL_ADMIN_IMAGES . '/thumb-default.png',
			'class_name'	=> '',
			'map'			=> ''
			
		);
		
		$the_layouts = array(
			array(
				'id'			=> 'pl_split_column',
				'name'			=> '2 Columns - Split', 
				'filter'		=> 'layout',
				'screenshot'	=>  PL_ADMIN_IMAGES . '/thumb-default.png',
				'map'			=> array(
									array(
										'object'	=> 'PLColumn',
										'span' 		=> 6, 
										'newrow'	=> true
									),
									array(
										'object'	=> 'PLColumn',
										'span' 	=> 6
									),
								)
			),
			array(
				'id'			=> 'pl_3_column',
				'name'			=> '3 Columns', 
				'filter'		=> 'layout',
				'screenshot'	=>  PL_ADMIN_IMAGES . '/thumb-default.png',
				'map'			=> array(
									array(
										'object'	=> 'PLColumn',
										'span' 		=> 4,
										'newrow'	=> true
									),
									array(
										'object'	=> 'PLColumn',
										'span' 	=> 4
									),
									array(
										'object'	=> 'PLColumn',
										'span' 	=> 4
									),
								)
			)
		);
		
		foreach($the_layouts as $index => $l){
			$l = wp_parse_args($l, $defaults);
			
			$obj = new stdClass();
			$obj->id = $l['id'];
			$obj->name = $l['name'];
			$obj->filter = $l['filter'];
			$obj->screenshot = $l['screenshot'];
			$obj->class_name = $l['class_name']; 
			$obj->map = $l['map'];
			
			$layouts[ $l['id'] ] = $obj;
		}
		
		return $layouts;
	}
	
	function add_new_callback(){
		$sections = get_available_sections(); 
		
		$sections = array_merge($sections, $this->layout_sections());
			
		$section_classes = 'pl-sortable span12 sortable-first sortable-last';
		$list = '';
		foreach($sections as $key => $s){
			
			$img = sprintf('<img src="%s" style=""/>', $s->screenshot); 
			
			if($s->map != ''){
				$map = json_encode( $s->map );
				$special_class = 'section-plcolumn';
			} else {
				$map = '';
				$special_class = '';
			}
				
			
			
			$list .= sprintf(
				"<section class='x-item %s %s %s' data-object='%s' data-sid='%s' data-name='%s' data-image='%s' data-template='%s'>
					<div class='x-item-frame'>
						<div class='pl-vignette'>
							%s
						</div>
					</div>
					<div class='x-item-text'>
						%s
					</div>
				</section>", 
				$section_classes,
				$special_class,
				$s->filter,
				$s->class_name,
				$s->id,
				$s->name,
				$s->screenshot,
				$map,
				$img, 
				$s->name
			);
		}
		 
		printf('<div class="x-list">%s</div>', $list);
	
	}
	
	function the_store_callback(){
		
		$list = '';
	
		foreach(store_mixed_array() as $key => $item){
			$class = array();
			$class[] = $item['type'];
			
			$class[] = ($item['type'] == 'themes') ? 'x-item-size-10' : 'x-item-size-5';
			
			$classes = implode(' ', $class);
			
		

			$img = sprintf('<img src="%s" style=""/>', $item['thumb']); 

			$list .= sprintf(
				"<section class='x-item %s' >
					<div class='x-item-frame'>
						<div class='pl-vignette'>
							%s
						</div>
					</div>
					<div class='x-item-text'>
						%s
					</div>
				</section>", 
				$classes,
				$img,
				$item['name']
			);

		}
		
		printf('<div class="x-list">%s</div>', $list);
	}	
	
	function live_callback(){
		printf('<div class="live-wrap"><iframe class="live_chat_iframe" src="http://pagelines.campfirenow.com/6cd04"></iframe></div>');
	}
	
	function defaults(){
		$d = array(
			'name'		=> '',
			'hook'		=> '', 
			'href'		=> '',
			'filter'	=> '', 
			'type'		=> 'opts', 
			'mode'		=> '',
			'class'		=> '', 
			'call'		=> false,
			'flag'		=> ''
		);
		return $d;
	}
	
	function panel($key, $panel){

		?>
		<div class="<?php echo 'panel-'.$key;?> tabbed-set" data-key="<?php echo $key;?>">
			<div class="tabs-wrap">
				<ul class="tabs-nav unstyled">
				
					<?php 
						foreach($panel as $tab_key => $t){
						
							if($tab_key == 'optPageType' && ($this->page->id == $this->page->type))
								continue;
						
							if( substr($tab_key, 0, 7) == 'heading'){
								printf('<lh>%s</lh>', $t); 
							
							} else {
				

								$t = wp_parse_args($t, $this->defaults());
							
								$href = ($t['href'] != '') ? $t['href'] : '#'.$tab_key;
							
								$hook = ($t['hook'] != '') ? sprintf('data-hook="%s"', $t['hook']) : '';
							
								$filter = ($t['filter'] != '') ? sprintf('data-filter="%s"', $t['filter']) : '';
							
								$flag = ($t['flag'] != '') ? sprintf('data-flag="%s"', $t['flag']) : '';
							
								$class = ($t['class'] != '') ? $t['class'] : '';
							
								printf('<li class="%s" %s %s %s><a href="%s">%s</a></li>', $class, $hook, $filter, $flag, $href, $t['name']);
							}
												
						}
					
					?>
				
				</ul>
			</div>
			<?php 
				foreach($panel as $tab_key => $t){ 
					
					$t = wp_parse_args($t, $this->defaults());
					
					if( substr($tab_key, 0, 7) == 'heading' || $t['href'] != '' ) 
						continue;
						
					if($tab_key == 'optPageType' && ($this->page->id == $this->page->type))
						continue;
						
					$content = '';
						
					if(isset($t['call']) && $t['call'] != ''){
						ob_start(); 
						call_user_func($t['call']);
						$content = ob_get_clean();
					} else {
						$content = sprintf('<div class="error-panel">There was an issue rendering the panel. (%s)</div>', rand());
					}
			
					$clip = ( isset($t['clip']) ) ? sprintf('<span class="clip-desc">%s</span>', $t['clip']) : '';
					
						
					printf(
						'<div id="%s" class="tab-panel" data-panel="%s" data-type="%s">
							<div class="tab-panel-inner">
								<legend>%s %s</legend>
								<div class="panel-tab-content">%s</div>
							</div>
						</div>',
						$tab_key,  
						$tab_key,  
						$t['type'],
						$t['name'], 
						$clip,
						$content
					);
				}
			?>
		
		</div>
		<?php 
	}
	
	function custom_less(){
		?>
		<div class="opt codetext">
			<div class="opt-name">
				Custom LESS/CSS
			</div>
			<div class="opt-box">
				<div class="codetext-meta fix">
					<label class="codetext-label">Custom LESS/CSS</label>
					<span class="codetext-help help-block"><span class="label label-info">Tip</span> Hit [Cmd&#8984;+Return ] or [Ctrl+Return] to Preview Live</span>
				</div>
				<textarea class="custom-less" style=""> </textarea>
			</div>
		</div>
		
		<?php 
	}
	
	function custom_scripts(){
		?>
		<div class="opt codetext">
			<div class="opt-name">
				Custom Scripts
			</div>
			<div class="opt-box">
				<div class="codetext-meta fix">
					<label class="codetext-label">Custom Javascript or Header HTML</label>
				</div>
				<textarea class="custom-scripts" style=""> </textarea>
			</div>
		</div>
		
		<?php 
	}
	
	function area_controls($a){
		
		ob_start();
		?>

		<div class="pl-area-controls">
			<a class="area-move-btn btn-area-up"><i class="icon-caret-up"></i></a>
			<a class="area-move-btn btn-area-down"><i class="icon-caret-down"></i></a>
		</div>
		<?php
		
		return ob_get_clean();
	}
	
	function section_controls($sid, $s){ 
		
		?>
		<div id="<?php echo $sid;?>_control" class="pl-section-controls fix" >
			<div class="controls-left">
				<a title="Section Decrease Width" href="#" class="s-control s-control-icon section-decrease"><i class="icon-caret-left"></i></a>
				<span title="Width" class="s-control section-size"></span>
				<a title="Section Increase Width" href="#" class="s-control s-control-icon section-increase"><i class="icon-caret-right"></i></a>
				<a title="Reduce Offset" href="#" class="s-control s-control-icon section-offset-reduce"><i class="icon-angle-left"></i></a>
				<span title="Offset Size" class="s-control offset-size"></span>
				<a title="Increase Offset" href="#" class="s-control s-control-icon section-offset-increase"><i class="icon-angle-right"></i></a>
				<a title="Force New Row" href="#" class="s-control s-control-icon section-start-row"><i class="icon-double-angle-left"></i></a>
			</div>
			<div class="controls-right">
				<a title="Edit Section" href="#" class="s-control s-control-icon section-edit"><i class="icon-pencil"></i></a>
				<a title="Clone Section" href="#" class="s-control s-control-icon section-clone"><i class="icon-copy"></i></a>
				<a title="Delete Section" href="#" class="s-control s-control-icon section-delete"><i class="icon-remove"></i></a>
			</div>
			<div class="controls-title"><?php echo $s->name;?></div>
		</div>
		<?php
		
	}	


}


