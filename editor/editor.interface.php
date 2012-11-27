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


	function __construct( ) {
	//	add_action( 'wp_footer', array( &$this, 'pl_editor_palette' ) );
		add_action( 'wp_footer', array( &$this, 'control_panel' ) );
		add_action( 'wp_print_styles', array(&$this, 'pl_editor_styles' ), 15 );
		$this->url = PL_PARENT_URL . '/editor';
		$this->images = $this->url . '/images';
		
		// angular
		add_action( 'the_html_tag', array( &$this, 'angular_start' ) );
		
		
	}
	
	function pl_editor_styles(){
		wp_enqueue_script( 'js-sprintf', $this->url . '/js/sprintf.js' ); 
		wp_enqueue_script( 'pl-editor-js', $this->url . '/js/editor.js' ); 
		wp_enqueue_script( 'pl-toolbox-js', $this->url . '/js/toolbox.js', array('pagelines-bootstrap-all')); 
		wp_enqueue_script( 'pl-optpanel', $this->url . '/js/optpanel.js'); 

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
		wp_enqueue_script( 'jquery-mousewheel', $this->url . '/js/mousewheel.js' ); 
		
		wp_enqueue_script( 'angular', $this->url . '/angular/angular.min.js' ); 
		wp_enqueue_script( 'angular-options', $this->url . '/angular/OptionsCtrl.js', array('angular') ); 
		
	}

	function angular_start(){
		echo ' ng-app';
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
			'<div class="pl-area area-tag" data-area-number="%s">%s<div class="pl-content"><div class="pl-inner">', 
			$a['area_number'], 
			$this->area_controls($a)
		); 
		
	}
	
	function area_end(){
		echo '</div></div></div>';
	}
	
	function control_panel(){
		
		
	?>
	
	<div class="pl-toolbox-pusher">
	</div>
	<div class="pl-toolbox">
		<div class="resizer-handle"></div>
		<div class="toolbox-handle fix">
			
			<ul class="unstyled controls">
				<li ><span class="btn-toolbox btn-closer" title="Close [esc]"><i class="icon-remove-sign"></i></span></li>
				
				<?php 
				
					foreach($this->toolbar_config() as $key => $tab){
						
						printf(
							'<li><span class="btn-toolbox btn-panel" data-action="%s" ><i class="%s"></i> <span class="txt">%s</span></span></li>', 
							$key, 
							$tab['icon'], 
							$tab['name']
						);
						
					}
				?>
				
			</ul>
			
			<ul class="unstyled controls send-right">
				<li><span class="btn-toolbox"><i class="icon-check"></i> <span class="txt">Publish</span></span></li>
				
				
			</ul>
		</div>
		<div class="toolbox-panel-wrap">
			<div class="toolbox-panel">
				<div class="toolbox-content fix" ng-controller="OptionsCtrl">
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
	
	function toolbar_config(){
		
		$data = array(
			'drag-drop' => array(
				'name'	=> 'Drag <span class="spamp">&amp;</span> Drop Editing',
				'icon'	=> 'icon-random'
			),
			'page-setup' => array(
				'name'	=> 'Pages',
				'icon'	=> 'icon-paste',
				'panel'	=> array()
				
			),
			'add-new' => array(
				'name'	=> 'Add',
				'icon'	=> 'icon-plus-sign',
				'panel'	=> array(
					'heading'	=> "Add To Page",
					'add_section'	=> array(
						'name'	=> 'Available Sections', 
						'type'	=> 'call',
						'call'	=> array(&$this, 'test_callback')
					)
				)
			), 
			'pl-design' => array(
				'name'	=> 'Design',
				'icon'	=> 'icon-magic',
				'panel'	=> array()
			), 
			'pl-settings' => array(
				'name'	=> 'Settings',
				'icon'	=> 'icon-cog',
				'panel'	=> array(
					'heading'	=> "Global Settings",
					'basic'		=> array('name'	=> 'Basic Setup'),
					'colors'	=> array('name'	=> 'Color Control'),
					'type'		=> array('name'	=> 'Typography'),
					'advanced'	=> array('name'	=> 'Advanced')
				)
			), 
			'pl-extend' => array(
				'name'	=> 'Extend',
				'icon'	=> 'icon-download',
				'panel'	=> array()
			)
		);
		
		return $data;
		
	}
	
	function test_callback(){
		echo 'hello this is the test callback' . rand();
	}
	
	function panel($key, $panel){

		?>
		<div class="<?php echo 'panel-'.$key;?> tabbed-set" data-key="<?php echo $key;?>">
			<ul class="tabs-nav unstyled">
				
				<?php 
					foreach($panel as $tab_key => $t){
						
						if($tab_key == 'heading'){
							printf('<lh>%s</lh>', $t); 
						} else {
							printf('<li><a href="#%s">%s</a></li>', $tab_key, $t['name']);
						}
						
						
					}
					
				?>

			</ul>
			<?php 
				foreach($panel as $tab_key => $t){ 
					
					if($tab_key == 'heading') 
						continue;
						
					if(isset($t['type']) && $t['type'] == 'call'){
						ob_start(); 
						call_user_func($t['call']);
						$content = ob_get_clean();
					}	else {
						$content = 'content --> ' . rand();
					}
						
					printf(
						'<div class="tab-panel" id="%s"><div class="tab-panel-inner"><legend>%s</legend><div class="tab-content">%s</div></div></div>', 
						$tab_key, 
						$t['name'], 
						$content
					);
				}
			?>
		
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
	
	function pl_editor_palette(){

		?>

		<div id="PageLinesGadget" class="pl-gadget">	
			<div class="pl-gadget-menu">
				<a class="gadget-item" href="#editModal" onClick="drawModal('Page Builder');">
					<span class="gadget-icon">
						<img src="<?php echo $this->images.'/icon-builder.png'; ?>" />
					</span>
					<span class="gadget-text">
						Drag &amp; Drop
					</span>
				</a>
				<a class="gadget-item" href="#editModal" onClick="jQuery.gadget.loadModeless();">
					<span class="gadget-icon">
						<img src="<?php echo $this->images.'/icon-builder.png'; ?>" />
					</span>
					<span class="gadget-text">
						Settings
					</span>
				</a>
				<a class="gadget-item" href="#editModal" onClick="jQuery.gadget.loadModeless();">
					<span class="gadget-icon">
						<img src="<?php echo $this->images.'/icon-builder.png'; ?>" />
					</span>
					<span class="gadget-text">
						Colors
					</span>
				</a>
				<a class="gadget-item" href="#editModal" onClick="jQuery.gadget.loadModeless();">
					<span class="gadget-icon">
						<img src="<?php echo $this->images.'/icon-builder.png'; ?>" />
					</span>
					<span class="gadget-text">
						Typography
					</span>
				</a>
				<a class="gadget-item" href="#editModal" onClick="jQuery.gadget.loadModeless();">
					<span class="gadget-icon">
						<img src="<?php echo $this->images.'/icon-builder.png'; ?>" />
					</span>
					<span class="gadget-text">
						Layout Width
					</span>
				</a>
			</div>
		</div>
		
		
		<?php 
	
	}
	


}


