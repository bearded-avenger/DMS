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

	var $section_list = array();
	var $area_number = 1;

	function __construct( ) {
		
		// 1. Grab Option for Template Config on Page
		
		// 2. Deserialize and Treat Array, get in right format
		
		// 3. Create An Array of All Section on Page
		
		// 4. Parse And Render Section Areas

		global $pl_section_factory; 
		
		$this->factory = $pl_section_factory->sections; // pass by reference
		
		$this->editor = new EditorInterface;
		
		$this->map = $this->dummy_template_data();

		$this->parse_config();
		
		$this->setup_processing();
		
		add_action( 'pagelines_head_last', array( &$this, 'json_data' ) );
		
	}
	
	function json_data(){
	
		?>
		<script>
		
			var option_config = <?php echo json_encode($this->dummy_option_config_data(), JSON_FORCE_OBJECT); ?>
			
			var page_data = <?php echo json_encode($this->dummy_page_content_data(), JSON_FORCE_OBJECT); ?>
		
		</script>
		<?php
		
	}
	
	function dummy_option_config_data(){
		
		$data = array(
			'PLMasthead' => array(
				array(
					'key'	=> 'settingA',
					'label'	=> 'Setting Label', 
					'type'	=> 'text', 
					'help'	=> 'Help Text goes here!', 
					
				),
				array(
					'key'	=> 'settingB',
					'label'	=> 'Setting Label', 
					'type'	=> 'checkbox', 
					'help'	=> 'Help Text goes here!'
				), 
				array(
					'key'	=> 'settingC',
					'label'	=> 'Setting Label', 
					'type'	=> 'select', 
					'help'	=> 'Help Text goes here!',
					'opts'	=> array(
						'val1'	=> array('name' => 'Value 1'),
						'val2'	=> array('name' => 'Value 2'),
						'val3'	=> array('name' => 'Value 3'),
					)
				)
			
			)
			
		);
		
		return $data;
		
	}
		
	function dummy_page_content_data(){
		
		$d = array(
			'settingA' 		=> array('value qqq', 'value settingA Clone2'),
			'settingB' 		=> array('value BBB', 'value settingB Clone2'),
			'settingC' 		=> array('value CCC', 'value settingC Clone2'),
		);
		
		return $d;
	}
	
	function dummy_template_config_data(){
			$t = array();

			$t['template'] = array(
				1	=> array(
					'area'	=> 'TemplateAreaID',
					'content'	=> array(
						array(
							'id'	=> 'PLMasthead'
						), 
						array(
							'id'	=> 'PageLinesBoxes'
						),
						array(
							'id'	=> 'PageLinesBoxes',
							'clone'	=> 2, 
							'span'	=> 6,
						),
						array(
							'id'	=> 'PageLinesHighlight'
						),
						array(
							'id'	=> 'PLColumn',
							'span' 	=> 8,
							'content'	=> array( 
								'PageLinesPostLoop' => array( ), 
								'PageLinesComments' 	=> array(),	
							)
						),
						array(
							'id'	=> 'PLColumn',
							'clone'	=> 2, 
							'span' 	=> 4,
							'content'	=> array( 
								'PrimarySidebar' => array( )
							)
						),
					)
				)

			);

			$t['header'] = array(
				array(
					'areaID'	=> 'HeaderArea',
					'content'	=> array(
						array(
							'id'	=> 'PageLinesBranding'
						),
						array(
							'id'	=> 'PLNavBar'
						),
					)
				)

			);

			$t['footer'] = array(
				array(
					'areaID'	=> 'FooterArea',
					'content'	=> array(
						array(
							'id'	=> 'SimpleNav'
						)
					)
				)

			);

			return $t;
	}
	
	function dummy_template_data(){
		$t = array();
		
		$t['template'] = array(
			'area-1'	=> array(
				'name'		=> 'Template Area',
				'content'	=> array(
				
					'PLMasthead' => array( ), 
					'PageLinesBoxesID1' => array( ),
					'PageLinesBoxesID2'=> array(
						'clone'	=> 2, 
						'span'	=> 6,
				 	), 
					
					'PageLinesContentBoxID3' => array( 'span' => '8' ),
					'PageLinesHighlight' => array( ), 
					
					'PLColumn' => array( 
						'span' 	=> 8,
						'content'	=> array( 
							'PageLinesPostLoop' => array( ), 
							'PageLinesComments' 	=> array(),	
						)
					),
					'PLColumnID2' => array( 
						'span' 	=> 4,
						'content'	=> array( 
							'PrimarySidebar' => array( )
						)
					),
				)
			)
			
		);
		
		$t['header'] = array(
			'area-1'	=> array(
				'height'	=> 200,
				'name'		=> 'Header',
				'content'	=> array(
			//		'PageLinesBranding' => array( ), 
					'PLNavBar'			=> array()
				)
			)
			
		);
		
		$t['footer'] = array(
			'area-1'	=> array(
				'height'	=> 200,
				'name'		=> 'Body Footer',
				'content'	=> array(
					'SimpleNav' => array( )
				)
			)
			
		);
		
		return $t;
		
		
	}
	
	function meta_defaults($key){
		
		$p = splice_section_slug($key);
		
		$defaults = array(
			'id'		=> $p['section'],
			'clone'		=> $p['clone_id'],  
			'content'	=> array(),
			'span'		=> 12,
		);
		
		return $defaults;
	}
	
	function parse_config(){
		foreach($this->map as $group => &$g){
			foreach($g as $area => &$a){
				foreach($a['content'] as $key => &$meta){
				
					$meta = wp_parse_args($meta, $this->meta_defaults($key));
				
					if(!empty($meta['content'])){
						foreach($meta['content'] as $subkey => &$sub_meta){
							$sub_meta = wp_parse_args($sub_meta, $this->meta_defaults($subkey));
							$this->section_list[$subkey] = $sub_meta;
						}
						unset($sub_meta); // set by reference
					
						$this->section_list[$key] = $meta;
					}else		
						$this->section_list[$key] = $meta;
				}
				unset($meta); // set by reference
			}
			unset($a); // set by reference
		}
		
	}
	
	function setup_processing(){
		
		global $pl_section_factory;
		
		foreach($this->section_list as $key => $meta){
			
			if( $this->in_factory( $meta['id'] ) ){
				$this->factory[ $meta['id'] ]->meta = $meta;
			}else
				unset($this->section_list[$key]);
				
		}
				
	}
	
	function process_styles(){
		
		/*
			TODO add !has_action('override_pagelines_css_output')
		*/
		foreach($this->section_list as $key => $meta){

			if($this->in_factory( $meta['id'] )) {

				$s = $this->factory[ $meta['id'] ];
				
				$s->meta = $meta;
				
				$s->section_styles();
				
				// Auto load style.css for simplicity if its there.
				if( is_file( $s->base_dir . '/style.css' ) ){

					wp_register_style( $s->id, $s->base_url . '/style.css', array(), $s->settings['p_ver'], 'screen');
			 		wp_enqueue_style( $s->id );

				}
			}	
		}
	}
	
	function process_head(){
		
		foreach($this->section_list as $key => $meta){
		
			if( $this->in_factory( $meta['id'] ) ){

				$s = $this->factory[ $meta['id'] ];
				
				$s->meta = $meta;
				
				$s->setup_oset( $meta['clone'] ); // refactor

				ob_start();

					$s->section_head( $meta['clone'] );	

				$head = ob_get_clean();

				if($head != '')
					echo pl_source_comment($s->name.' | Section Head') . $head;
				

			}	
		}
	}
	
	function process_region( $region = 'template' ){
		
		if(!isset($this->map[ $region ]))
			return;
		
		$this->editor->region_start( $region, $this->area_number++ );
		
		foreach( $this->map[ $region ] as $area => $a ){
			
			$a['area_number'] = $this->area_number++; 
			
			$this->editor->area_start($a);
			
			foreach($a['content'] as $key => $meta){
				
				$this->render_section( $meta );
				
			}
			
			$this->editor->area_end($a);
			
		}
	}
	
	function render_section( $meta ){
		
		if( $this->in_factory( $meta['id'] ) ){
			
			$s = $this->factory[ $meta['id'] ];

			$s->meta = $meta;

			$s->setup_oset( $meta['clone'] ); // refactor
			
			ob_start();

				$s->section_template_load( $meta['clone'] ); // Check if in child theme, if not load section_template

			$output =  ob_get_clean(); // Load in buffer, so we can check if empty
		
			if(isset($output) && $output != ''){
				
				echo pl_source_comment($s->name . ' | Section Template', 2); // Add Comment 

				$s->before_section_template(  ); // refactor into before_section
				
				$s->before_section( 'editor', $meta['clone']);

				$this->editor->section_controls($meta['id'], $s);

				echo $output;

				$s->after_section( 'editor' );
				
				$s->after_section_template( );
				
			}
		
			wp_reset_postdata(); // Reset $post data
			wp_reset_query(); // Reset wp_query
			
		}
		
	}
		
	/**
	 * Tests if the section is in the factory singleton
	 */
	function in_factory( $section ){	
		return ( isset($this->factory[ $section ]) && is_object($this->factory[ $section ]) ) ? true : false;
	}	
	
}

/**
 * For use inside of sections
 */
function render_nested_sections( $sections ){

	global $pagelines_editor;

	if( !empty( $sections ) ){

		foreach( $sections as $key => $meta )
			$pagelines_editor->handler->render_section( $meta );

	}

}

