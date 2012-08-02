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
class PageLinesTemplateHandler {


	function __construct( ) {
		
		// 1. Grab Option for Template Config on Page
		
		// 2. Deserialize and Treat Array, get in right format
		
		// 3. Create An Array of All Section on Page
		
		// 4. Parse And Render Section Areas
		
		global $pl_section_factory; 
		
		$this->factory = $pl_section_factory->sections;
		$this->map = $this->dummy_data();
		
	}
	
	function process(){
		$t = $this->map;
		
		foreach($t['template'] as $area => $a){
			
			$content = '';
			foreach($a['content'] as $key => $c){
			
					$render = $this->buffer_template( $key );
					
					ob_start(); 
					if($render)
						$this->render_template($render, $key, 'editor');
						
					$content .= ob_get_clean();
			}
			
			
			ob_start();
			?>
			<ul class="pl-area-footer nav-pills nav">
				<li class="dropdown" id="add_section">
					<a class="pl-add pl-control dropdown-toggle" data-toggle="dropdown" href="#add_section">Add Section <b class="caret"></b></a> 
					<ul class="dropdown-menu">
						<li><a href="#">Drop</a></li>
					</ul>
				</li>
				<li class="dropdown" id="add_element">
					<a class="pl-add pl-control dropdown-toggle" data-toggle="dropdown" href="#add_element">Add Element <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="#">Drop</a></li>
						<li><a href="#">Drop II</a></li>
					</ul>
				</li>
				
			</ul>
			<?php
			
			
			$area_footer = ob_get_clean();
			
			$add_area_button = '<div class="pl-area-controls"><a class="pl-add pl-control" href="#editModal" onClick="drawModal(\'Page Builder\');">+ Section Area</a></div>';
			
			printf('<div class="pl-area">%s<div class="pl-content"><div class="pl-inner">%s</div>%s</div></div>', $add_area_button, $content, $area_footer);
		}
	}
	
	/**
	 * Tests if the section is in the factory singleton
	 * @since 1.0.0
	 */
	function in_factory( $section ){	
		return ( isset($this->factory[ $section ]) && is_object($this->factory[ $section ]) ) ? true : false;
	}
	
	/**
	 * Runs template in an output buffer and returns the output
	 */
	function buffer_template( $sid ){
		global $post;
		global $wp_query;
		$save_query = $wp_query;
		$save_post = $post;
		
		/**
		 * If this is a cloned element, remove the clone flag before instantiation here.
		 */
		$p = splice_section_slug($sid);
		$section = $p['section'];
		$clone_id = $p['clone_id'];
		
		if( $this->in_factory( $section ) ){
			
			$s = $this->factory[ $section ];
			
			$s->setup_oset( $clone_id );
			
			/**
			 * Load Template
			 * Get Template in Buffer 
			 *****************************/
			
				ob_start();
		
					// If in child theme get that, if not load the class template function
					$s->section_template_load( $clone_id );
	
				$template_output =  ob_get_clean();
			
			/** END BUFFER *****************************/
		}
		
		// RESET //
			$wp_query = $save_query;
			$post = $save_post;
			
		return (isset($template_output) && $template_output != '') ? $template_output : false;
		
	}
	
	function section_controls(){
		
		?>
		
		<div class="pl-section-controls">
			<a href="#" class="pl-control pl-section-width-left">L</a>
			<span class="pl-control pl-section-width-info">1/3</span>
			<a href="#" class="pl-control pl-section-width-right">R</a>
			<div class="controls-right">
				<a href="#" class="pl-control pl-section-edit">E</a>
				<a href="#" class="pl-control pl-section-clone">C</a>
				<a href="#" class="pl-control pl-section-delete">X</a>
			</div>
		</div>
		<?php
		
	}
	
	
	/**
	 * Renders the HTML template and adds surrounding 'standardized' markup and hooks
	 */
	function render_template($template, $sid, $markup){

		$p = splice_section_slug($sid);
		$section = $p['section'];
		$clone_id = $p['clone_id'];
		
		$s = $this->factory[ $section ];
		
		// Add Comment 
		echo pl_source_comment($s->name . ' | Section Template', 2);
		
		$s->before_section_template( $clone_id );
		
		$s->before_section( $markup, $clone_id);
			
			$this->section_controls();
			
			echo $template;
	
		$s->after_section( $markup );
	
		$s->after_section_template( $clone_id );
		
	}
	
	function dummy_data(){
		$t = array();
		
		$t['template'] = array(
			'area-1'	=> array(
				'height'	=> 200,
				'content'	=> array(
					'PageLinesBoxes' => array(), 
					'PageLinesFeatures'=> array(),  
					'element-column-1' => array( // grid row
						'width' => '50%',
						'content'	=> array(
							'sectionID3'
						)
					), 
					'PageLinesContentBoxID3' => array('width' => '50%') 
				)
			)
			
		);
		
		return $t;
		
		
	}

	
	
}

