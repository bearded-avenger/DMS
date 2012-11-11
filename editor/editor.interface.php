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
	}
	
	function pl_editor_styles(){
		wp_enqueue_script( 'pl-editor-js', $this->url . '/js/editor.js' ); 
		wp_enqueue_script( 'pl-toolbox-js', $this->url . '/js/toolbox.js', array('pagelines-bootstrap-all')); 
		
		// wp_enqueue_script( 'jquery-ui-draggable'); 
		// wp_enqueue_script( 'jquery-ui-droppable'); 
		// wp_enqueue_script( 'jquery-ui-resizable'); 	
		
		wp_enqueue_script( 'jquery-ui-tabs'); 
		
		$dep = array('jquery-new-ui-core','jquery-new-ui-widget', 'jquery-new-ui-mouse');	
		wp_enqueue_script( 'jquery-new-ui-core', PL_ADMIN_JS . '/jquery.ui.core.js', array(), 1.9, true);
		wp_enqueue_script( 'jquery-new-ui-widget', PL_ADMIN_JS . '/jquery.ui.widget.js', array(), 1.9, true);
		wp_enqueue_script( 'jquery-new-ui-mouse', PL_ADMIN_JS . '/jquery.ui.mouse.js', array('jquery-new-ui-widget'), 1.9, true);
		wp_enqueue_script( 'jquery-new-ui-draggable', PL_ADMIN_JS . '/jquery.ui.draggable.js', $dep, 1.9, true);
		wp_enqueue_script( 'jquery-new-ui-droppable', PL_ADMIN_JS . '/jquery.ui.droppable.js', $dep, 1.9, true);
		wp_enqueue_script( 'jquery-new-ui-resizable', PL_ADMIN_JS . '/jquery.ui.resizable.js', $dep, 1.9, true);
		wp_enqueue_script( 'jquery-new-ui-sortable', PL_ADMIN_JS . '/jquery.ui.sortable.js', $dep, 1.9, true);
		wp_enqueue_script( 'jquery-new-ui-effect', PL_ADMIN_JS . '/jquery.ui.effect.js', $dep, 1.9, true);	
		wp_enqueue_script( 'jquery-new-ui-effect-highlight', PL_ADMIN_JS . '/jquery.ui.effect-highlight.js', array('jquery-new-ui-effect'), 1.9, true);
		wp_enqueue_script( 'jquery-mousewheel', $this->url . '/js/mousewheel.js' ); 
		
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
		<div class="toolbox-handle fix">
			
			<ul class="unstyled controls">
				<li ><a class="h-nav h-toggler" data-toggle="toolbox" ><i class="icon-chevron-down"></i></a></li>
				<li><a class="h-nav" ><i class="icon-random"></i> <span class="txt">Drag <span class="spamp">&amp;</span> Drop</span></a></li>
				<li><a class="h-nav" ><i class="icon-check-empty"></i> <span class="txt">Regions/Areas</span></a></li>
				<li><a class="h-nav"><i class="icon-plus-sign"></i> <span class="txt">Add</span></a></li>
				<li><a class="h-nav"><i class="icon-copy"></i> <span class="txt">Templates</span></a></li>
				<li><a class="h-nav"><i class="icon-magic"></i> <span class="txt">Colors</span></a></li>
				<li><a class="h-nav"><i class="icon-font"></i> <span class="txt">Type</span></a></li>
				<li><a class="h-nav"><i class="icon-resize-horizontal"></i> <span class="txt">Layout</span></a></li>
				<li><a class="h-nav"><i class="icon-cog"></i> <span class="txt">Settings</span></a></li>
				<li><a class="h-nav"><i class="icon-user"></i> <span class="txt">Account</span></a></li>
				<li><a class="h-nav"><i class="icon-pagelines"></i> <span class="txt">PageLines</span></a></li>
			</ul>
			
			<ul class="unstyled panel-control send-right">
				
				<li><a class="h-nav h-resizer"><i class="icon-reorder"></i></a></li>
				
			</ul>
		</div>
		<div class="toolbox-panel-wrap">
			<div class="toolbox-panel">
				<div class="toolbox-content fix">
					<div class="tabbed-set">
						<ul class="tabs-nav unstyled">
							<lh>Settings</lh>
							<li><a href="#tab-1"><i class="icon-camera-retro"></i> Tab 1</a></li>
							<li><a href="#tab-2">Tab 2</a></li>
							<li><a href="#tab-3">Tab 3</a></li>
						</ul>
						<div class="tab-panel" id="tab-1">
					
							<div class="tab-panel-inner">
								<div class="opt">
									<div class="opt-input">
										<form class="bs-docs-example">
											<legend>Legend</legend>
											<label>Label name</label>
											<input type="text" placeholder="Type something…">
											<span class="help-block">Example block-level help text here.</span>
											<label class="checkbox">
											<input type="checkbox"> Check me out
											</label>
											<button type="submit" class="btn">Submit</button>
										</form>
									</div>
									<div class="opt-exp">explanation</div>
								</div>
							</div>
						</div>
						<div class="tab-panel" id="tab-2">
							<div class="tab-panel-inner">
						
									<legend>tab 2</legend>
									<label>Label name</label>
									<input type="text" placeholder="Type something…">
							
							</div>
						</div>
						<div class="tab-panel" id="tab-3">
							<div class="tab-panel-inner">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php 
	}
	
	function area_controls($a){
		
		ob_start();
		?>

		<div class="pl-area-controls">
			<div class="controls-toggle-btn ">
				<a class="btn btn-mini btn-inverse btn-area-up"><i class="icon-caret-up"></i></a>
				<a class="btn btn-mini btn-inverse btn-area-down"><i class="icon-caret-down"></i></a>
			</div>
			<div class="controls-buttons btn-toolbar">
				<div class="btn-group">
					<button class="btn btn-mini btn-inverse" href="#editModal" onClick="drawModal(\'Page Builder\');">Add New Area</button>
				</div><div class="btn-group">
					<button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown" >Add Section <b class="caret"></b></button> 
					<ul id="add_section" class="dropdown-menu">
						<li><a href="#">Drop</a></li>
					</ul>
				</div><div class="btn-group">
					<button class="btn btn-mini btn-inverse dropdown-toggle" data-toggle="dropdown">Add Element <b class="caret"></b></button>
					<ul id="add_element" class="dropdown-menu">
						<li><a href="#">Drop</a></li>
						<li><a href="#">Drop II</a></li>
					</ul>
				</div>
			</div>
		</div>
		<?php
		
		return ob_get_clean();
	}
	
	function section_controls($sid, $s){ ?>
		<div id="<?php echo $sid;?>_control" class="pl-section-controls fix">
			<div class="controls-left">
				<a title="Section Decrease Width" href="#" class="pl-control pl-control-icon section-decrease">L</a>
				<span title="Width" class="pl-control section-size">12/12</span>
				<a title="Section Increase Width" href="#" class="pl-control pl-control-icon section-increase">R</a>
				<a title="Increase Offset" href="#" class="pl-control pl-control-icon section-offset-increase">OL</a>
				<span title="Offset Size" class="pl-control offset-size"></span>
				<a title="Reduce Offset" href="#" class="pl-control pl-control-icon section-offset-reduce">OR</a>
				<a title="Force New Row" href="#" class="pl-control pl-control-icon section-start-row">S</a>
			</div>
			<div class="controls-right">
				<a title="Edit Section" href="#" class="pl-control pl-control-icon section-edit">E</a>
				<a title="Clone Section" href="#" class="pl-control pl-control-icon section-clone">C</a>
				<a title="Delete Section" href="#" class="pl-control pl-control-icon section-delete">X</a>
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
		
		$this->the_modal();
	}
	
	function the_modal(){
		?>
			<div class="modal modal-interface fade hide" id="editModal">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>Modal header</h3>
				</div>
				<div class="modal-body">
					<div class="pl-page-builder">
						<div class="navbar fix">
						  <div class="navbar-inner">
						    <div class="container">
						      	<ul class="navline">
									<li class="dropdown">
									  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
									        Common <b class="caret"></b>
									  </a>
									  <ul class="dropdown-menu">
									    	<li class="active">
									    <a href="#">Home</a>
									  </li>
									  <li><a href="#">Link</a></li>
									  <li><a href="#">Link</a></li>
									  </ul>
									</li>
									<li class="dropdown">
									   <a href="#" class="dropdown-toggle" data-toggle="dropdown">
									         Sections <b class="caret"></b>
									   </a>
									   <ul class="dropdown-menu">
									     	<li class="active">
										    <a href="#">Home</a>
										  </li>
										  <li><a href="#">Link</a></li>
										  <li><a href="#">Link</a></li>
									   </ul>
									 </li>
								</ul>
								<ul class="navline pull-right">
									<li class="dropdown">
									  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
									        Templates <b class="caret"></b>
									  </a>
									  <ul class="dropdown-menu">
									    	<li class="active">
									    <a href="#">Home</a>
									  </li>
									  <li><a href="#">Link</a></li>
									  <li><a href="#">Link</a></li>
									  </ul>
									</li>
								
								</ul>
						    </div>
						  </div>
						</div>
						
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Close</a>
					<a href="#" class="btn btn-primary">Save changes</a>
				</div>
			</div>
		<?php 
		
		
	}

}


