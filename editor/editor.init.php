<?php
/**
 * This file initializes the PageLines Editor
 *
 * @package PageLines Framework
 * @since 3.0.0
 *
 */


// Make sure user can handle this.
if (!current_user_can('edit_themes') || true)
	return;


$editor = new PageLinesEditor; 

class PageLinesEditor {

	function __construct() {
		add_action( 'wp_footer', array(&$this, 'pl_editor_palette' ) );
		add_action( 'wp_head', array(&$this, 'pl_editor_js' ) );
		add_action( 'wp_print_styles', array(&$this, 'pl_editor_styles' ) );
		
		add_action( 'pagelines_template', array(&$this, 'process_template' ) );
		
		
		$this->path = get_template_directory() . '/editor';
		$this->url = PARENT_URL . '/editor';
		$this->images = $this->url . '/images';
	}
	
	function pl_editor_styles(){
		wp_enqueue_script( 'pl-editor-js', $this->url . '/js/editor.js' ); 
		wp_enqueue_script( 'jquery-ui-draggable'); 
		wp_enqueue_script( 'jquery-ui-resizable'); 	
		wp_enqueue_script( 'jquery-ui-sortable'); 	
	}

	function pl_editor_js(){
		?>
		
			<script type="text/javascript"> 
			/* <![CDATA[ */ 
			
				/* Draggable Stuff
				jQuery(document).ready(function() {
					jQuery("#pl-edit-palette").css({ top: jQuery.cookie("paletteY")*1, left: jQuery.cookie("paletteX")*1 });
					
					jQuery("#pl-edit-palette").draggable({ stop: function (event, ui) {
					    jQuery.cookie("paletteX", ui.position.left);
					    jQuery.cookie("paletteY", ui.position.top);
					} });
				});
				*/
				function drawStructure(title){
					
					jQuery('body').addClass('pl-editor');
					
					jQuery('.pl-inner').addClass('editor-row');
					
					
					
					jQuery('.pl-content').resizable({ 
						handles: "se",
						
						resize: function(event, ui) { 
							var theWidth = ui.size.width; 
							
							jQuery('.pl-content').width(theWidth).css(top, null).css(left, null); 

						}
					});
					
					jQuery('.pl-area .pl-content').sortable({
						items: "section",
						forcePlaceholderSize: true,
						
					});
				
					jQuery('.pl-section').addClass('span12').hover(
					  function () {
					    jQuery('.pl-section-controls', this).fadeIn();
					  }, 
					  function () {
					    jQuery('.pl-section-controls', this).fadeOut();
					  }
					);
					
					jQuery('.pl-section-width-left').on("click", function(event){
						jQuery(this).parent().parent().removeClass('span12').addClass('span4'); 
						return false;
					});
					

				}
				
				function drawModal(title){
					
					jQuery('#editModal h3').html(title);
					
					jQuery('#editModal').modal();
				}
			/* ]]> */ 
			</script>	
		
		<?php 
	}
	
	function process_template(){
		$handler = new PageLinesTemplateHandler();
		$handler->process();
	}
	
	function draw_new_markup( $hook_id ){
		$t = $this->data_structure();
		
		foreach($t['template'] as $area => $a){
			
			$content = '';
			foreach($a['content'] as $key => $c){
				
				if(is_array($c)){
					if(strpos($key, 'column') !== false){
						foreach($c['content'] as $col_key => $col_element){
							$content .= sprintf('<section class="pl-section">%s</section>', $col_element);
						}
					} else {
						$content .= sprintf('<section class="pl-section">%s</section>', $key);
					}
				}else
					$content .= sprintf('<section class="pl-section">%s</section>', $c);
			}
			
			
			ob_start();
			?>
			<ul class="pl-area-footer nav-pills nav">
				<li class="dropdown" id="add_section">
					<a class="pl-add dropdown-toggle" data-toggle="dropdown" href="#add_section">Add Section <b class="caret"></b></a> 
					<ul class="dropdown-menu">
						<li><a href="#">Drop</a></li>
					</ul>
				</li>
				<li class="dropdown" id="add_element">
					<a class="pl-add dropdown-toggle" data-toggle="dropdown" href="#add_element">Add Element <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="#">Drop</a></li>
						<li><a href="#">Drop II</a></li>
					</ul>
				</li>
				
			</ul>
			<?php
			
			
			$area_footer = ob_get_clean();
			
			$add_area_button = '<div class="pl-area-controls"><a class="pl-add" href="#editModal" onClick="drawModal(\'Page Builder\');">New Section Area</a></div>';
			
			printf('<div class="pl-area">%s<div class="pl-content"><div class="pl-inner">%s</div>%s</div></div>', $add_area_button, $content, $area_footer);
		}
	}
	
	function data_structure(){
		$t = array();
		
		$t['template'] = array(
			'area-1'	=> array(
				'height'	=> 200,
				'content'	=> array(
					'SectionID1', 
					'SectionID2', 
					'column-1' => array( // grid row
						'width' => '50%',
						'content'	=> array(
							'sectionID3'
						)
					), 
					'SectionID4' => array('width' => '50%') 
				)
			),
			'area-2'	=> array(
				'height'	=> 200,
				'content'	=> array(
					'SectionIDX', 
					'SectionIDY', 
					'column-1' => array( // grid row
						'width' => '50%',
						'content'	=> array(
							'sectionID3'
						)
					), 
					'SectionIDZ' => array('width' => '50%') 
				)
			)
			
		);
		
		return $t;
		
		
	}
	
	function pl_editor_palette(){

		global $current_user;
		     get_currentuserinfo();
		?>

		<div id="pl-edit-palette" class="pl-palette">	
			<div class="pl-palette-pad fix">
				<!-- <div class="pl-vignette"><?php echo get_avatar( 'andrew@pagelines.com', 60, $this->images.'/avatar-default.png' ); ?></div> -->
				<div class="pl-palette-icons">
					<a class="pl-palette-icon" href="#editModal" onClick="drawModal('Page Builder');"><img src="<?php echo $this->images.'/icon-builder.png'; ?>" /></a>
					<a class="pl-palette-icon" href="#editModal" onClick="drawModal('Color Control');"><img src="<?php echo $this->images.'/icon-picker.png'; ?>" /></a>
					<a class="pl-palette-icon" href="#editModal" onClick="drawStructure('Site Layout');"><img src="<?php echo $this->images.'/icon-layout.png'; ?>" /></a>	
					<a class="pl-palette-icon" href="#editModal" onClick="drawModal('Global Options');"><img src="<?php echo $this->images.'/icon-settings.png'; ?>" /></a>	
					<a class="pl-palette-icon" href="#editModal" onClick="drawModal('Account');"><img src="<?php echo $this->images.'/icon-account.png'; ?>" /></a>	
				</div>
			</div>
			</div>
			<div class="modal modal-interface fade hide" id="editModal">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>Modal header</h3>
				</div>
				<div class="modal-body">
					<div class="pl-page-builder">
						<div class="navbar pl-color-black-trans fix">
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
