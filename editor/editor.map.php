<?php

/**
 * 
 *  Template Map Handler
 *
 */
class EditorMap {
	
	var $map_option_slug = 'pl-template-map';
	
	
	function __construct( PageLinesPage $page ){
		
		$this->page = $page;
		
		$this->global_map_option = get_option($this->map_option_slug); 
		
	}
	
	function get_map(){
	
		$map['header'] = $this->get_header(); 
		$map['footer'] = $this->get_footer(); 
		$map['template'] = $this->get_local_template(); 
		
		return $map;
	}
	
	function get_header(){
		
		if($this->global_map_option && isset($this->global_map_option['header']))
			return $this->global_map_option['header']; 
		else 
			return $this->default_header();
			
	}
	
	function get_footer(){
		
		if($this->global_map_option && isset($this->global_map_option['footer']))
			return $this->global_map_option['footer']; 
		else 
			return $this->default_footer();
	}
	
	
	
	// Get local map for page
	function get_local_template(){

		if( $this->page->is_special() ){
			$template = $this->get_special_template( $this->page->id ); 
		}else 
			$template = $this->get_regular_template( $this->page->id ); 
			
		return $template;
		
	}
	
	function get_regular_template( $id ){
	
		$local = get_post_meta( $id, $this->map_option_slug, true);
	
		
		if( $local ){
			$template = $local; 
		} else 
			$template = $this->default_template(); 
			
		
		return $template; 
		
	}
	
	function get_special_template( $id ){
	
		if( isset($this->global_map_option[ $id ]) ){
			
			return $this->global_map_option[ $id ]; 
		}else 
			return $this->default_template(); 
		
	}
	
	function default_template(){
		$d = array(
			array(
				'area'	=> 'TemplateAreaID',
				'content'	=> array(
					array(
						'object'	=> 'PLColumn',
						'span' 	=> 8,
						'content'	=> array( 
							'PageLinesPostLoop' => array( ), 
							'PageLinesComments' => array(),	
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

		);
		
		return $d;
	}
	
	function default_header(){
		$d = array(
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
		
		return $d;
	}
	
	function default_footer(){
		$d = array(
			array(
				'areaID'	=> 'FooterArea',
				'content'	=> array(
					array(
						'object'	=> 'SimpleNav'
					)
				)
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
				array(
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

		

			return $t;
	}
	
	
}
