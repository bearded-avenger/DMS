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


	function __construct( PageLinesPage $pg, EditorSettings $siteset, EditorDraft $draft, EditorTemplates $templates, EditorMap $map, EditorExtensions $extensions, EditorThemeHandler $theme ) {

		$this->theme = $theme;
		$this->page = $pg;
		$this->draft = $draft;
		$this->siteset = $siteset;
		$this->templates = $templates;
		$this->map = $map;
		$this->extensions = $extensions;


		if ( $this->draft->show_editor() ){
			
			add_action( 'wp_footer', array( &$this, 'pagelines_toolbox' ) );
			add_action( 'wp_enqueue_scripts', array(&$this, 'pl_editor_scripts' ) );
			add_action( 'wp_enqueue_scripts', array(&$this, 'pl_editor_styles' ) );
			add_action( 'wp_ajax_the_store_callback', array( &$this, 'the_store_callback' ) );

		} elseif(current_user_can('edit_themes')) {

			add_action( 'wp_enqueue_scripts', array(&$this, 'pl_live_scripts' ) );
			add_action( 'wp_footer', array( &$this, 'pagelines_editor_activate' ) );

		}

		$this->url = PL_PARENT_URL . '/editor';
		$this->images = $this->url . '/images';


	}

	function pl_live_scripts(){
		wp_enqueue_script( 'pl-utility-js', $this->url . '/js/pl.live.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}


	function pl_editor_styles() {

		wp_enqueue_style( 'codemirror', PL_ADMIN_JS . '/codemirror/codemirror.css' );
		wp_enqueue_style( 'css3colorpicker', $this->url . '/js/colorpicker/colorpicker.css');
	}

	function pl_editor_scripts(){

		// PageLines Custom
		wp_enqueue_script( 'js-sprintf', $this->url . '/js/utils.sprintf.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'pl-editor-js', $this->url . '/js/pl.editor.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'pl-toolbox-js', $this->url . '/js/pl.toolbox.js', array('pagelines-bootstrap-all' ) );
		wp_enqueue_script( 'pl-optpanel', $this->url . '/js/pl.optpanel.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'pl-ajax', $this->url . '/js/pl.ajax.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'pl-library', $this->url . '/js/pl.library.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'pl-layout', $this->url . '/js/pl.layout.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'pl-extend', $this->url . '/js/pl.extend.js', array( 'jquery' ), PL_CORE_VERSION );
		wp_enqueue_script( 'pl-js-themes', $this->url . '/js/pl.themes.js', array( 'jquery' ), PL_CORE_VERSION );

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

		// Less
		wp_enqueue_script( 'lessjs', $this->url . '/js/utils.less.js', array('jquery'), '1.3.1', true );

		// Less
		wp_enqueue_script( 'bootbox', $this->url . '/js/utils.bootbox.js', array('jquery'), '3.0.0', true );


		// Colorpicker
		wp_enqueue_script( 'css3colorpicker', $this->url . '/js/colorpicker/colorpicker.js', array('jquery'), '1.3.1', true );

		// Image Uploader
		wp_enqueue_script( 'fineupload', $this->url . '/js/fineuploader/jquery.fineuploader-3.2.min.js');

		// Images Loaded
		wp_enqueue_script( 'imagesloaded', $this->url . '/js/utils.imagesloaded.js');

		// Global AjaxURL variable --> http://www.garyc40.com/2010/03/5-tips-for-using-ajax-in-wordpress/
		wp_localize_script( 'pl-editor-js', 'ajaxurl', array( admin_url( 'admin-ajax.php' ) ) );

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

		return ($this->draft->show_editor()) ? sprintf('<div class="pl-sortable pl-sortable-buffer span12 offset0"></div>') : '';
	}

	function area_end(){
		printf('%s</div></div></div>', $this->area_sortable_buffer());
	}



	function toolbar_config(){

		$data = array(
			'pl-toggle' => array(
				'icon'	=> 'icon-off',
				'type'	=> 'btn'

			),
			'add-new' => array(
				'name'	=> 'Add New',
				'icon'	=> 'icon-plus-sign',
				'panel'	=> array(
					'heading'	=> "<i class='icon-random'></i> Drag to Add",
					'add_section'	=> array(
						'name'	=> 'Your Sections',
						'icon'	=> 'icon-random',
						'clip'	=> 'Drag on to page to add',
						'type'	=> 'call',
						'call'	=> array(&$this, 'add_new_callback'),
						'filter'=> '*'
					),
					'more_sections'	=> array(
						'name'	=> 'Get More Sections',
						'icon'	=> 'icon-download',
						'flag'	=> 'link-storefront'
					),
					'heading2'	=> "<i class='icon-filter'></i> Filters",
					'components'		=> array(
						'name'	=> 'Components',
						'href'	=> '#add_section',
						'filter'=> '.component',
						'icon'	=> 'icon-circle-blank'
					),
					'layouts'		=> array(
						'name'	=> 'Layouts',
						'href'	=> '#add_section',
						'filter'=> '.layout',
						'icon'	=> 'icon-columns'
					),
					'formats'		=> array(
						'name'	=> 'Post Formats',
						'href'	=> '#add_section',
						'filter'=> '.format',
						'icon'	=> 'icon-th'
					),
					'galleries'		=> array(
						'name'	=> 'Galleries',
						'href'	=> '#add_section',
						'filter'=> '.gallery',
						'icon'	=> 'icon-camera'
					),
					'navigation'	=> array(
						'name'	=> 'Navigation',
						'href'	=> '#add_section',
						'filter'=> '.nav',
						'icon'	=> 'icon-circle-arrow-right'
					),
					'features'		=> array(
						'name'	=> 'Features',
						'href'	=> '#add_section',
						'filter'=> '.feature',
						'icon'	=> 'icon-picture'
					),

					'social'	=> array(
						'name'	=> 'Social',
						'href'	=> '#add_section',
						'filter'=> '.social',
						'icon'	=> 'icon-comments'
					),
					'widgets'	=> array(
						'name'	=> 'Widgetized',
						'href'	=> '#add_section',
						'filter'=> '.widgetized',
						'icon'	=> 'icon-retweet'
					),
					'misc'		=> array(
						'name'	=> 'Miscellaneous',
						'href'	=> '#add_section',
						'filter'=> '.misc',
						'icon'	=> 'icon-star'
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
					'avail_themes'	=> array(
						'name'	=> '<i class="icon-picture"></i> Available Themes',
						'call'	=> array(&$this, 'themes_dashboard'),
					),
					'more_themes'	=> array(
						'name'	=> '<i class="icon-download"></i> Get More Themes',
						'flag'	=> 'link-storefront'
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
						'filter'=> '*',
						'type'	=> 'call',
						'call'	=> array(&$this, 'the_store_callback'),
					),
					'heading2'	=> "Filters",
					'plus'		=> array(
						'name'	=> 'Free with Plus',
						'href'	=> '#store',
						'filter'=> '.plus'
					),
					'featured'		=> array(
						'name'	=> 'Featured',
						'href'	=> '#store',
						'filter'=> '.featured'
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
				'name'	=> '',
				'icon'	=> '',
				'type'	=> 'dropup',
				'panel'	=> array(

					'toggle_grid'	=> array('name'	=> '<i class="icon-table"></i> Toggle Editor Grid'),
				)

			),
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

		$current_page = ($this->page->is_special()) ? $this->page->type_name : $this->page->id;

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

				$tabs[ $tabkey ] = array(
					'key' 	=> $tabkey,
					'name' 	=> $tab['name'],
					'icon'	=> isset($tab['icon']) ? $tab['icon'] : ''
				);
			}

		}

		return $tabs;

	}

	function pagelines_editor_activate(){
		?>
			<div class="toolbox-activate"><i class="icon-off"></i> <span class="txt">Activate PageLines Editor</span></span></div>

		<?php
	}

	function pagelines_toolbox(){


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
						$li_class = array();
						$li_class[] = 'type-'.$tab['type'];

						if($tab['type'] == 'dropup'){

							$data = 'data-toggle="dropdown"';
							$suffix = ' <i class="uxi icon-caret-right"></i>';
							$li_class[] = 'dropup';
							$menu = '';

							foreach($tab['panel'] as $key => $i){
								$menu .= sprintf('<li><a href="#" class="btn-action" data-action="%s">%s</a></li>', $key, $i['name']);
							}
							$content = sprintf('<ul class="dropdown-menu">%s</ul>', $menu);
						}

						$li_classes = join(' ', $li_class);

						$class = array();

						$class[] = ($tab['type'] == 'panel') ? 'btn-panel' : '';
						$class[] = ($tab['type'] == 'btn') ? 'btn-action' : '';

						$class[] = 'btn-'.$key;

						$classes = join(' ', $class);

						$name = (isset($tab['name'])) ? sprintf('<span class="txt">%s</span>', $tab['name']) : '';
						$icon = (isset($tab['icon'])) ? sprintf('<i class="uxi %s"></i> ', $tab['icon']) : '';

						printf(
							'<li class="%s"><span class="btn-toolbox %s" data-action="%s" %s>%s%s%s</span>%s</li>',
							$li_classes,
							$classes,
							$key,
							$data,
							$icon,
							$name,
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
				<li class="li-draft"><span class="btn-toolbox btn-save btn-draft" data-mode="draft"><i class="icon-edit"></i> <span class="txt">Preview Changes</span></li>
				<li class="li-publish"><span class="btn-toolbox btn-save btn-publish" data-mode="publish"><i class="icon-check"></i> <span class="txt">Publish Page</span></li>

			</ul>
			<ul class="unstyled controls not-btn send-right">
				<li class="switch-btn btn-saving"><span class="btn-toolbox not-btn"><i class="icon-save"></i> <span class="txt">Saving</span></li>
				<li class="switch-btn btn-layout-resize"><span class="btn-toolbox  not-btn">
					<i class="icon-fullscreen"></i> <span class="txt">Width: <span class="resize-px"></span> / <span class="resize-percent"></span></span>
				</li>
			</ul>
		</div>
		<?php pagelines_register_hook('before_toolbox_panel'); // Hook ?>
		<div class="toolbox-panel-wrap">
			<div class="toolbox-panel">
				<div class="toolbox-content fix">
					<div class="toolbox-content-pad option-panel">
						<?php
						foreach($this->toolbar_config() as $key => $tab){

							if(isset($tab['panel']) && !empty($tab['panel']))
								$this->panel($key, $tab['panel']);
							else
								printf('<div class="panel-%s tabbed-set error-panel"><i class="icon-spinner icon-spin"></i></div>', $key);
						}
							 ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	}



	function add_new_callback(){
		$sections = $this->extensions->get_available_sections();


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


			$args = array(
				'id'			=> $s->id,
				'class_array' 	=> array('x-add-new', $section_classes, $special_class, $s->filter),
				'data_array'	=> array(
					'object' 	=> $s->class_name,
					'sid'		=> $s->id,
					'name'		=> $s->name,
					'image'		=> $s->screenshot,
					'template'	=> $map,
					'clone'		=> '0'
				),
				'thumb'			=> $s->screenshot,
				'splash'		=> $s->splash,
				'name'			=> $s->name
			);

			$list .= $this->get_x_list_item( $args );



		}

		printf('<div class="x-list x-sections" data-panel="x-sections">%s</div>', $list);

	}

	function get_x_list_item( $args ){
		$d = array(
			'id'			=> '',
			'class_array' 	=> array(),
			'data_array'	=> array(),
			'thumb'			=> '',
			'splash'		=> '',
			'name'			=> 'No Name'
		);
		$args = wp_parse_args($args, $d);

		$classes = join(' ', $args['class_array']);

		$popover_content = sprintf('<img src="%s" />', $args['splash']);

		$img = sprintf('<img width="300" height="225" src="%s" />', $args['thumb']);

		$datas = '';
		foreach($args['data_array'] as $field => $val){
			$datas .= sprintf("data-%s='%s' ", $field, $val);
		}

		$list_item = sprintf(
			"<section class='x-item x-extension %s %s' %s data-content='%s' data-extend-id='%s'>
				<div class='x-item-frame'>
					<div class='pl-vignette'>
						%s
					</div>
				</div>
				<div class='x-item-text'>
					%s
				</div>
			</section>",
			$args['id'],
			$classes,
			$datas,
			$popover_content,
			$args['id'],
			$img,
			$args['name']
		);

		return $list_item;

	}

	function themes_dashboard(){
		$themes = wp_get_themes();

		$active_theme = wp_get_theme();

		$list = '';
		$count = 1;
		if(is_array($themes)){

			foreach($themes as $theme => $t){
				$class = array();

				if($t->get_template() != 'pagelines')
					continue;

				if($active_theme->stylesheet == $t->get_stylesheet()){
					$class[] = 'active-theme';
					$active = ' <span class="badge badge-info"><i class="icon-ok"></i> Active</span>';
					$number = 0;
				}else {
					$active = '';
					$number = $count++;
				}
				
				if( is_file( sprintf( '%s/splash.png', $t->get_stylesheet_directory() ) ) )
				 	$splash = sprintf( '%s/splash.png', $t->get_stylesheet_directory_uri()  );
				else 
					$splash = $t->get_stylesheet();
				
				$class[] = 'x-item-size-10';

				$args = array(
					'id'			=> $theme,
					'class_array' 	=> $class,
					'data_array'	=> array(
						'number' 		=> $number,
						'stylesheet'	=> $t->get_stylesheet()
					),
					'thumb'			=> $t->get_screenshot( ),
					'splash'		=> $t->get_screenshot( ),
					'name'			=> $t->name . $active
				);

				$list .= $this->get_x_list_item( $args );


			}

		}


		printf('<div class="x-list x-themes" data-panel="x-themes">%s</div>', $list);
	}

	function the_store_callback(){

		$list = '';
		global $storeapi;
		$mixed_array = $storeapi->get_latest();

		foreach( $mixed_array as $key => $item){

			$class = $item['class_array'];

			$class[] = 'x-storefront';

			$img = sprintf('<img src="%s" style=""/>', $item['thumb']);

			$args = array(
				'id'			=> $item['slug'],
				'class_array' 	=> $class,
				'data_array'	=> array(
					'store-id' 	=> $item['slug']
				),
				'thumb'			=> $item['thumb'],
				'splash'		=> $item['splash'],
				'name'			=> $item['name']
			);

			$list .= $this->get_x_list_item( $args );


		}

		printf('<div class="x-list x-store" data-panel="x-store">%s</div>', $list);
	}

	function live_callback(){
		printf('<div class="live-wrap"><iframe class="live_chat_iframe" src="http://pagelines.campfirenow.com/6cd04"></iframe></div>');
	}

	function defaults(){
		$d = array(
			'name'		=> '',
			'icon'		=> '',
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

								$icon = ($t['icon'] != '') ? sprintf('<i class="%s"></i> ', $t['icon']) : '';


								printf('<li class="%s" data-tab-action="%s" %s %s %s><a href="%s">%s%s</a></li>', $class, $tab_key, $hook, $filter, $flag, $href, $icon, $t['name']);
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
						$content = sprintf('<div class="error-panel"><i class="icon-refresh icon-spin"></i> Loading Panel</div>', rand());
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
			<a class="area-move-btn btn-area-up"><i class="icon-caret-up"></i></a><a class="area-move-btn btn-area-down"><i class="icon-caret-down"></i></a>
		</div>
		<?php

		return ob_get_clean();
	}

	function section_controls( $s ){

		if(!$this->draft->show_editor())
			return;

		$clone_desc = ($s->meta['clone'] != 0) ? sprintf(" <i class='icon-copy'></i> %s", $s->meta['clone']) : '';
		$sid = $s->id;
		ob_start();
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
			<div class="controls-title"><?php echo $s->name;?> <span class="title-desc"><?php echo $clone_desc;?></span></div>
		</div>
		<?php

		return ob_get_clean();

	}


}


