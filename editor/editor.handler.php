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

	function __construct( EditorInterface $interface, PageLinesPage $pg ) {


		global $pl_section_factory; 
		
		$this->factory = $pl_section_factory->sections; 
		
		// Dependancy Injection (^^)
		$this->editor = $interface;
		$this->page = $pg;
		
		$this->map = $this->dummy_template_config_data();

		

		$this->parse_config();
		
		$this->setup_processing();
		
		$this->get_options_config();
		
		add_action( 'pagelines_head_last', array( &$this, 'json_data' ) );
		
	}
	
	function json_data(){
		
	
		
		?>
		<script>
		
			!function ($) {
				
				$.PLData = {
					
					pageID: '<?php echo $this->page->id;?>'
					, pageTypeID: '<?php echo $this->page->type_ID;?>'
					, pageType: '<?php echo $this->page->type;?>'
					, optConfig: <?php echo json_encode($this->get_options_config(), JSON_FORCE_OBJECT); ?>
					, pageData:  <?php echo json_encode($this->dummy_page_content_data(), JSON_FORCE_OBJECT); ?>
				}

			
			}(window.jQuery);
			
		
		</script>
		<?php
		
	}
	
	function get_options_config(){
		
		$opts_config = array();
		
		
		// BACKWARDS COMPATIBILITY
		add_action('override_metatab_register', array(&$this, 'get_opts_from_optionator'), 10, 2);

		foreach($this->section_list as $key => $meta){

			if($this->in_factory( $meta['object'] )) {

				$s = $this->factory[ $meta['object'] ];
				
				$opts_config[ $s->id ] = array(
					'name'	=> $s->name
				);
				
				$opts = $s->section_opts(); 
				
				if(!$opts){
					// backwards comp
					$s->section_optionator( array() );
				
					if(isset( $this->current_option_array ))
						$opts = $this->process_to_new_option_format( $this->current_option_array ); 
				}
				
				$opts_config[ $s->id ][ 'opts' ] = $opts; 
					
			}
		}
		
		remove_action('override_metatab_register', array(&$this, 'get_opts_from_optionator'), 10, 2);
		
		return $opts_config;
	}
	
	function process_to_new_option_format( $old_options ){
		
		$new_options = array();
		
		foreach($old_options as $key => $o){
			
			if($o['type'] == 'multi_option' || $o['type'] == 'text_multi'){
			
				$sub_options = array();
				foreach($o['selectvalues'] as $sub_key => $sub_o){
					$sub_options[ ] = $this->process_old_opt($sub_key, $sub_o, $o); 
				}
				$new_options[ ] = array(
					'type' 	=> 'multi', 
					'title'	=> $o['title'],
					'opts'	=> $sub_options
				);
			} else {
				$new_options[ ] = $this->process_old_opt($key, $o);	
			}
			
		}
		
		return $new_options;
	}
	
	function process_old_opt( $key, $old, $otop = array()){
		
		if(isset($otop['type']) && $otop['type'] == 'text_multi')
			$old['type'] = 'text'; 
			
		$defaults = array(
            'type' 			=> 'check',
			'title'			=> '',
			'inputlabel'	=> '', 
			'exp'			=> '', 
			'shortexp'		=> '',
			'selectvalues'	=> array()
		);
		
		$old = wp_parse_args($old, $defaults);
		
		$exp = ($old['exp'] == '' && $old['shortexp'] != '') ? $old['shortexp'] : $old['exp'];
		
		if($old['type'] == 'text_small'){
			$type = 'text'; 
		} else 
			$type = $old['type'];
		
		$new = array(
			'key'	=> $key, 
			'title'	=> $old['title'],
			'label'	=> $old['inputlabel'], 
			'type'	=> $type, 
			'help'	=> $exp, 
			'opts'	=> $old['selectvalues']
		); 
		return $new;
	}
		
	function get_opts_from_optionator($array){
		
		$this->current_option_array = $array;
		
	}	
		
	
	function dummy_option_config_data(){

		$data = array(
			'masthead' => array(
				'name'	=> 'Masthead', 
				'icon'	=> '...',
				'opts'	=> array(
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
					), 
					array(
						'key'	=> 'settingD',
						'label'	=> 'Setting Label', 
						'type'	=> 'checkbox', 
						'help'	=> 'Help Text goes here!'
					),
					array(
						'key'	=> 'settingE',
						'label'	=> 'Setting Label', 
						'type'	=> 'checkbox', 
						'help'	=> 'Help Text goes here!'
					),
				)
				
			
			)
			
		);
		
		return $data;
		
	}
		
	function dummy_page_content_data(){
		
		$d = array(
			'current' => array(
				'settingA' 	=> array(
						'value qqq', 
						'value settingA Clone2'
				),
				'settingB' 		=> array('value BBB', 'value settingB Clone2'),
				'settingC' 		=> array('value CCC', 'value settingC Clone2'),
			), 
			'post_type'	=> array(
				'settingD' 		=> array('value BBB', 'value settingB Clone2'),
			), 
			'site_defaults'	=> array(
				'settingE' 		=> array('value BBB', 'value settingB Clone2'),
			)
		);
		
		return $d;
	}
	
	function dummy_template_config_data(){
			$t = array();

			// Regions
			// --> Areas
			// --> --> Sections

			$t['template'] = array(
				1	=> array(
					'area'	=> 'TemplateAreaID',
					'content'	=> array(
						array(
							'object'	=> 'PLMasthead'
						), 
						array(
							'object'	=> 'PageLinesBoxes'
						),
						array(
							'object'	=> 'PageLinesBoxes',
							'clone'	=> 1, 
							'span'	=> 6,
						),
						array(
							'object'	=> 'PageLinesHighlight'
						),
						array(
							'object'	=> 'PLColumn',
							'span' 	=> 8,
							'content'	=> array( 
								'PageLinesPostLoop' => array( ), 
								'PageLinesComments' 	=> array(),	
							)
						),
						array(
							'object'	=> 'PLColumn',
							'clone'	=> 1, 
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
							'object'	=> 'PageLinesBranding'
						),
						array(
							'object'	=> 'PLNavBar'
						),
					)
				)

			);

			$t['footer'] = array(
				array(
					'areaID'	=> 'FooterArea',
					'content'	=> array(
						array(
							'object'	=> 'SimpleNav'
						)
					)
				)

			);

			return $t;
	}
	

	function meta_defaults($key){
		
		$p = splice_section_slug($key);
		
		$defaults = array(
			'id'		=> $key,
			'object'	=> $key,
			'clone'		=> 0,  
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
							$this->section_list[  ] = $sub_meta;
						}
						unset($sub_meta); // set by reference
					
						$this->section_list[  ] = $meta;
					}else		
						$this->section_list[  ] = $meta;
				}
				unset($meta); // set by reference
			}
			unset($a); // set by reference
		}
	}
	
	function setup_processing(){
		
		global $pl_section_factory;
		
		foreach($this->section_list as $key => $meta){
			
			if( $this->in_factory( $meta['object'] ) ){
				$this->factory[ $meta['object'] ]->meta = $meta;
			}else
				unset($this->section_list[$key]);
				
		}
				
	}
	
	function process_styles(){
		
		/*
			TODO add !has_action('override_pagelines_css_output')
		*/
		foreach($this->section_list as $key => $meta){

			if($this->in_factory( $meta['object'] )) {

				$s = $this->factory[ $meta['object'] ];
				
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
		
			if( $this->in_factory( $meta['object'] ) ){

				$s = $this->factory[ $meta['object'] ];
				
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
		
		if( $this->in_factory( $meta['object'] ) ){
			
			$s = $this->factory[ $meta['object'] ];

			$s->meta = $meta;

			$s->setup_oset( $meta['clone'] ); // refactor
			
			ob_start();

				$this->section_template_load( $s ); // Check if in child theme, if not load section_template

			$output =  ob_get_clean(); // Load in buffer, so we can check if empty
		
			if(isset($output) && $output != ''){
				
				echo pl_source_comment($s->name . ' | Section Template', 2); // Add Comment 
				
				$this->before_section( $s );

				$this->editor->section_controls($meta['id'], $s);

				echo $output;

				$this->after_section( $s );
				
			}
		
			wp_reset_postdata(); // Reset $post data
			wp_reset_query(); // Reset wp_query
			
		}
		
	}
	
	function before_section( $s ){
			
		pagelines_register_hook('pagelines_before_'.$s->id, $s->id); // hook
		
		// Rename to prevent conflicts
		// TODO remove this or check to remove this strange non-algorhythmic code
		if ( 'comments' == $s->id )
			$sid = 'wp-comments';
		elseif ( 'content' == $s->id )
			$sid = 'content-area';
		else
			$sid = $s->id;
		

		$span = (isset($s->meta['span'])) ? sprintf('span%s', $s->meta['span']) : 'span12';
		$offset = (isset($s->meta['offset'])) ? sprintf('offset%s', $s->meta['span']) : 'offset0';
		$clone = $s->meta['clone'];
		
		$class[] = sprintf("pl-section fix section-%s", $sid);
		$class[] = $span;
		$class[] = $offset;
		
		printf(
			'<section id="%s" data-object="%s" data-sid="%s" data-clone="%s" class="%s">', 
			$s->id.$clone, 
			$s->class_name,
			$s->id, 
			$clone, 
			implode(" ", $class)
		);

		pagelines_register_hook('pagelines_outer_'.$s->id, $s->id); // hook
		pagelines_register_hook('pagelines_inside_top_'.$s->id, $s->id); // hook 
		
 	}

	function after_section( $s ){
		
		pagelines_register_hook('pagelines_inside_bottom_'.$s->id, $s->id);
	 	
		printf('</section>');

		pagelines_register_hook('pagelines_after_'.$s->id, $s->id);
	}

	function section_template_load( $s ) {
		
		// Variables for override
		$override_template = 'template.' . $s->id .'.php';
		$override = ( '' != locate_template(array( $override_template), false, false)) ? locate_template(array( $override_template )) : false;

		if( $override != false) 
			require( $override );
		else
			$s->section_template( $s->meta['clone'] );
		
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

