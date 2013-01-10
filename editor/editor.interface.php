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


	function __construct( PageLinesPage $pg, EditorSettings $siteset ) {
		
		$this->page = $pg;
		$this->siteset = $siteset;

		add_action( 'wp_footer', array( &$this, 'control_panel' ) );
		add_action( 'wp_print_styles', array(&$this, 'pl_editor_styles' ), 15 );
		$this->url = PL_PARENT_URL . '/editor';
		$this->images = $this->url . '/images';
		
		add_action( 'wp_ajax_the_store_callback', array( &$this, 'the_store_callback' ) );
	}
	
	function pl_editor_styles(){
		
		// Global AjaxURL variable --> http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/
		wp_localize_script( 'codemirror', 'PLAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		

		wp_enqueue_script( 'js-sprintf', $this->url . '/js/utils.sprintf.js' ); 
		wp_enqueue_script( 'pl-editor-js', $this->url . '/js/pl.editor.js' ); 
		wp_enqueue_script( 'pl-toolbox-js', $this->url . '/js/pl.toolbox.js', array('pagelines-bootstrap-all')); 
		wp_enqueue_script( 'pl-optpanel', $this->url . '/js/pl.optpanel.js'); 
		wp_enqueue_script( 'isotope', $this->url . '/js/utils.isotope.js', array('jquery')); 

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



	}
	
	function dummy_saved_templates(){
		
		$t = array(
			'default' => array(
				'key'	=> 'default',
				'name'	=> 'Default Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					array(
						'area'	=> 'TemplateAreaID',
						'content'	=> array(
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 8,
								'content'	=> array( 
									'PageLinesPostLoop' => array( ), 
									'PageLinesComments' 	=> array( ),	
								)
							),
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 4,
								'content'	=> array( 
									'PrimarySidebar' => array( )
								)
							),
						)
					)
				)
			), 
			'feature' => array(
				'key'	=> 'feature',
				'name'	=> 'Feature Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					array(
						'area'	=> 'TemplateAreaID',
						'content'	=> array(
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 8,
								'content'	=> array( 
									'PageLinesPostLoop' => array( ), 
									'PageLinesComments' 	=> array( ),	
								)
							),
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 4,
								'content'	=> array( 
									'PrimarySidebar' => array( )
								)
							),
						)
					)
				)
			), 
			'landing' => array(
				'key'	=> 'landing',
				'name'	=> 'Landing Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					array(
						'area'	=> 'TemplateAreaID',
						'content'	=> array(
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 8,
								'content'	=> array( 
									'PageLinesPostLoop' => array( ), 
									'PageLinesComments' 	=> array( ),	
								)
							),
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 4,
								'content'	=> array( 
									'PrimarySidebar' => array( )
								)
							),
						)
					)
				)
			)
			
		);
		
		return $t;
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
			'<div class="pl-area area-tag" data-area-number="%s">%s<div class="pl-content"><div class="pl-inner pl-sortable-area editor-row">%s', 
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
					'heading'	=> "Add To Page",
					'add_section'	=> array(
						'name'	=> 'Available Sections', 
						'type'	=> 'call',
						'call'	=> array(&$this, 'add_stuff_callback')
					), 
					'add_layout'	=> array(
						'name'	=> 'Layouts'
					)
				)
			),
			
			'page-setup' => array(
				'name'	=> 'Templates',
				'icon'	=> 'icon-paste',
				'panel'	=> array(
					'heading'	=> "Page Templates",
					'tmp_load'	=> array(
						'name'	=> 'Your Templates', 
						'call'	=> array(&$this, 'custom_templates'),
					),
					'tmp_theme'	=> array(
						'name'	=> 'Theme Templates', 
						'call'	=> array(&$this, 'custom_templates'),
					),
					'tmp_save'	=> array(
						'name'	=> 'Save As Template'
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
					'theme'		=> array('name'	=> 'Website Theme')
				)
			), 
			'settings' => array(
				'name'	=> 'Settings',
				'icon'	=> 'icon-cog',
				'panel'	=> $this->get_settings_tabs( 'site' )
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
			'pl-actions' => array(
				'name'	=> 'Actions',
				'icon'	=> 'icon-asterisk',
				'type'	=> 'dropup', 
				'panel'	=> array(
					
					'template'	=> array('name'	=> 'Preview Page'),
					'revert'	=> array('name'	=> 'Revert Changes')
				)
				
			),
			'section-options' => array(
				'name'	=> 'Section Options',
				'icon'	=> 'icon-paste',
				'type'	=> 'hidden', 
				'flag'	=> 'section-opts',
				'panel'	=> array(
					'heading'		=> "Section Options",
					'current'	=> array(
						'name'	=> 'Current Page <span class="label">'.$this->page->id.'</span>',
					),
					'post_type'	=> array(
						'name'	=> 'Post Type <span class="label">'.$this->page->type.'</span>',
					),
					'site_defaults'	=> array(
						'name'	=> 'Sitewide Defaults', 		
					),
				)
				
			),
		);
		
		return $data;
		
	}
	
	function custom_templates(){
		
		$templates = '';
		foreach($this->dummy_saved_templates() as $index => $template){
			
			$templates .= sprintf(
							'<div class="list-item" data-key="%s">
								<div class="list-item-pad fix">
									<div class="title">%s</div>
									<div class="desc">%s</div>
									<div class="btns">
										<a class="btn btn-mini btn-primary load-template">Load Template</a>
										<a class="btn btn-mini load-template">Make "%s" Default</a>
										<a class="btn btn-mini delete-template">Delete</a>
									</div>
								</div>
							</div>', 
							$template['key'], 
							$template['name'], 
							$template['desc'],
							$this->page->type
						);
			
		}
		
		printf('<div class="y-list">%s</div>', $templates);
		
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
					<span class="btn-toolbox btn-state " data-toggle="dropdown">
						<span class="update-state">&nbsp;</span>
					</span>
					<ul class="dropdown-menu pull-right">
						<li><a href="">Red &rarr; Unpublished Page Changes</a></li>
						<li><a href="">Orange &rarr; Unpublished Site Changes</a></li>
						<li><a href="">Green &rarr; No Unpublished Changes</a></li>
					</ul>
				</li>
				<li><span class="btn-toolbox btn-publish"><i class="icon-save"></i> <span class="txt">Update <span class="update-state">&nbsp;</span></span></li>
				
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
	
	function add_stuff_callback(){
		$sections = get_available_sections(); 
	//	plprint($sections);
		
		$section_classes = 'pl-section pl-sortable span12 sortable-first sortable-last';
		$list = '';
		foreach($sections as $key => $s){
			
			$list .= sprintf(
				'<section class="x-item %s" data-object="%s" data-sid="%s" data-name="%s" data-image="%s"><div class="x-item-frame"><img src="%s" /></div><div class="x-item-text">%s</div></section>', 
				$section_classes,
				$s->class_name,
				$s->id,
				$s->name,
				$s->screenshot,
				$s->screenshot, 
				$s->name
			);
		}
		 
		printf('<div class="x-list">%s</div>', $list);
	
	}
	
	function the_store_callback(){
		
		$items = '';
	
		foreach(store_mixed_array() as $key => $item){
			$class = array();
			$class[] = $item['type'];
			
			$class[] = ($item['type'] == 'themes') ? 'x-item-size-10' : 'x-item-size-5';
			
			$classes = implode(' ', $class);
			
			$items .= sprintf('<div class="x-item %s"><div class="x-item-frame"><img src="%s" /></div></div>', $classes, $item['thumb']);

		}
		
		printf('<div class="x-list">%s</div>', $items);
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

			<ul class="tabs-nav unstyled">
				
				<?php 
					foreach($panel as $tab_key => $t){
						
						if($tab_key == 'optPageType' && ($this->page->id == $this->page->type_ID))
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
			<?php 
				foreach($panel as $tab_key => $t){ 
					
					$t = wp_parse_args($t, $this->defaults());
					
					if( substr($tab_key, 0, 7) == 'heading' || $t['href'] != '' ) 
						continue;
						
					if($tab_key == 'optPageType' && ($this->page->id == $this->page->type_ID))
						continue;
						
					$content = '';
						
					if(isset($t['call']) && $t['call'] != ''){
						ob_start(); 
						call_user_func($t['call']);
						$content = ob_get_clean();
					} else {
						$content = sprintf('<div class="error-panel">There was an issue rendering the panel. (%s)</div>', rand());
					}
			
					
						
					printf(
						'<div id="%s" class="tab-panel" data-panel="%s" data-type="%s">
							<div class="tab-panel-inner">
								<legend>%s</legend>
								<div class="panel-tab-content">%s</div>
							</div>
						</div>',
						$tab_key,  
						$tab_key,  
						$t['type'],
						$t['name'], 
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
				<a title="Reduce Offset" href="#" class="s-control s-control-icon section-offset-reduce"><i class="icon-step-backward"></i></a>
				<span title="Offset Size" class="s-control offset-size"></span>
				<a title="Increase Offset" href="#" class="s-control s-control-icon section-offset-increase"><i class="icon-step-forward"></i></a>
				<a title="Force New Row" href="#" class="s-control s-control-icon section-start-row"><i class="icon-fast-backward"></i></a>
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


