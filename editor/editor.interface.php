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

		} elseif(current_user_can('edit_themes')) {

			add_action( 'wp_footer', array( &$this, 'pagelines_editor_activate' ) );

		}

		$this->url = PL_PARENT_URL . '/editor';
		$this->images = $this->url . '/images';


	}

	function pl_editor_scripts(){

		// PageLines Custom
		wp_enqueue_script( 'js-sprintf', $this->url . '/js/utils.sprintf.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'pl-editor-js', $this->url . '/js/pl.editor.js', array( 'jquery' ), PL_CORE_VERSION , true);
		wp_enqueue_script( 'pl-toolbox-js', $this->url . '/js/pl.toolbox.js', array('pagelines-bootstrap-all' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'pl-optpanel', $this->url . '/js/pl.optpanel.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'pl-ajax', $this->url . '/js/pl.ajax.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'pl-library', $this->url . '/js/pl.library.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'pl-layout', $this->url . '/js/pl.layout.js', array( 'jquery' ), PL_CORE_VERSION, true );
		

		pagelines_register_hook('pagelines_editor_scripts'); // Hook

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
		wp_enqueue_script( 'jquery-mousewheel', $this->url . '/js/utils.mousewheel.js', array('jquery'), PL_CORE_VERSION, true );

		// Forms handling
		wp_enqueue_script( 'form-params', $this->url . '/js/form.params.js', array('jquery'), PL_CORE_VERSION, true );
		wp_enqueue_script( 'form-store', $this->url . '/js/form.store.js', array('jquery'), PL_CORE_VERSION, true );

		

		// Less
		wp_enqueue_script( 'lessjs', $this->url . '/js/utils.less.js', array('jquery'), '1.3.1' );

		// Less
		wp_enqueue_script( 'bootbox', $this->url . '/js/utils.bootbox.js', array('jquery'), '3.0.0', true );


		// Colorpicker
		wp_enqueue_script( 'css3colorpicker', $this->url . '/js/colorpicker/colorpicker.js', array('jquery'), '1.3.1', true );

		// Image Uploader
		wp_enqueue_script( 'fineupload', $this->url . '/js/fineuploader/jquery.fineuploader-3.2.min.js', array('jquery'), PL_CORE_VERSION, true );

		// Images Loaded
		wp_enqueue_script( 'imagesloaded', $this->url . '/js/utils.imagesloaded.js', array('jquery'), PL_CORE_VERSION, true);

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



	function toolbar_config(){

		$data = array(
			'pl-toggle' => array(
				'icon'	=> 'icon-off',
				'type'	=> 'btn',
				'pos'	=> 1
			),
			
			'pl-actions' => array(
				'name'	=> '',
				'icon'	=> '',
				'type'	=> 'dropup',
				'panel'	=> array(
					'toggle_grid'	=> array(
						'name'	=> '<i class="icon-table"></i> Toggle Editor Grid'
					),
				),
				'pos'	=> 200

			),
			
		);

		return $data;

	}
	
	
	function get_toolbar_config( ){
		
	
		$toolbar_config =  apply_filters('pl_toolbar_config', $this->toolbar_config());
		
		$default = array(
			'pos'	=> 100
		);
		
		
		foreach( $toolbar_config as $key => &$info ){
			$info = wp_parse_args( $info, $default ); 
		}
		unset($info); // set by reference ^^
				
		uasort( $toolbar_config, array(&$this, "cmp_by_position") );

		return apply_filters( 'pl_sorted_toolbar_config', $toolbar_config );
	}
	
	function cmp_by_position($a, $b) {
		
	  return $a["pos"] - $b["pos"];
	
	}
	
	
	
	

	



	function pagelines_editor_activate(){
		global $wp;
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		
		$nigl = (count($_GET) > 0) ? '&' : '?'; 
		
		$activate_url = $current_url . $nigl . 'edtr=on';
		
		?>
			<a id="toolbox-activate" href="<?php echo $activate_url;?>" class="toolbox-activate"><i class="icon-off"></i> <span class="txt">Activate PageLines Editor</span></span></a>

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

					foreach($this->get_toolbar_config() as $key => $tab){

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

				<?php
					$state = $this->draft->get_state( $this->page->id, $this->page->typeid, $this->map );
					$state_class = '';
					foreach($state as $st){
						$state_class .= ' '.$st;
					}
					
					
				?>
				<li id="stateTool" class="dropup <?php echo $state_class;?>">
					<span class="btn-toolbox btn-state " data-toggle="dropdown">
						<span id="update-state" class="state-draft state-tag">&nbsp;</span>
					</span>
					<ul class="dropdown-menu pull-right state-list">
						<li class="li-state-multi"><a class="btn-revert" data-revert="all"><span class="update-state state-draft multi">&nbsp;</span>&nbsp; Revert All Unpublished Changes</a></li>
						<li class="li-state-global"><a class="btn-revert" data-revert="global"><span class="update-state state-draft global">&nbsp;</span>&nbsp; Revert Unpublished Global Changes</a></li>

						<li class="li-state-type"><a class="btn-revert" data-revert="type"><span class="update-state state-draft type">&nbsp;</span>&nbsp; Revert Unpublished Post Type Changes</a></li>
						<li class="li-state-local"><a class="btn-revert" data-revert="local"><span class="update-state state-draft local">&nbsp;</span>&nbsp; Revert Unpublished Local Changes</a></li>
						<li class="li-state-clean disabled"><a class="txt"><span class="update-state state-draft clean">&nbsp;</span>&nbsp; No Unpublished Changes</a></li>
					</ul>
				</li>
				<li class="li-draft"><span class="btn-toolbox btn-save btn-draft" data-mode="draft"><i class="icon-save"></i> <span class="txt">Save <span class="spamp">&amp;</span> Preview</span></li>
				<li class="li-publish"><span class="btn-toolbox btn-save btn-publish" data-mode="publish"><i class="icon-ok"></i> <span class="txt">Publish</span></li>

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
						foreach($this->get_toolbar_config() as $key => $tab){
							
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
					
					$tools = ( isset($t['tools']) ) ? sprintf('<span class="clip-tools">%s</span>', $t['tools']) : '';


					printf(
						'<div id="%s" class="tab-panel" data-panel="%s" data-type="%s">
							<div class="tab-panel-inner">
								<legend>%s %s %s</legend>
								<div class="panel-tab-content">%s</div>
							</div>
						</div>',
						$tab_key,
						$tab_key,
						$t['type'],
						$t['name'],
						$clip,
						$tools,
						$content
					);
				}
			?>

		</div>
		<?php
	}




	function section_controls( $s ){

		if(!$this->draft->show_editor())
			return;

		$clone_desc = ($s->meta['clone'] != 0) ? sprintf(" <i class='icon-copy'></i> %s", $s->meta['clone']) : '';
		$sid = $s->id;
		ob_start();
		?>
		<div class="pl-section-controls fix" >
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
				<a title="Edit Section" href="#" class="s-control s-control-icon section-edit s-loaded"><i class="icon-pencil"></i></a>
				<a title="Clone Section" href="#" class="s-control s-control-icon section-clone s-loaded"><i class="icon-copy"></i></a>
				<a title="Delete Section" href="#" class="s-control s-control-icon section-delete"><i class="icon-remove"></i></a>
			</div>
			<div class="controls-title"><span class="ctitle"><?php echo $s->name;?></span> <span class="title-desc"><?php echo $clone_desc;?></span></div>
		</div>
		<?php

		return ob_get_clean();

	}


}


