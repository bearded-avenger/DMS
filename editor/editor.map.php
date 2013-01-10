<?php

/**
 * 
 *  Template Map Handler
 *
 */
class EditorMap {
	
	var $map_option_slug = 'pl-template-map';
	var $map_option_slug_draft = 'pl-template-map-draft';
	
	
	function __construct( ){
		
		if( current_user_can('edit_themes') )
			$this->map_slug = $this->map_option_slug_draft;
		else 
			$this->map_slug = $this->map_option_slug;
		
		$this->global_map_option = get_option( $this->map_slug ); 
		
	}
	
	function get_map( PageLinesPage $page ){
	
		$map['header'] = $this->get_header(); 
		$map['footer'] = $this->get_footer(); 
		$map['template'] = $this->get_local_template( $page ); 
		
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
	function get_local_template( $page ){

		if( $page->is_special() ){
			$template = $this->get_special_template( $page->id ); 
		}else 
			$template = $this->get_regular_template( $page->id ); 
			
		return $template;
		
	}
	
	function get_regular_template( $id ){
	
		$local = get_post_meta( $id, $this->map_slug, true);
	
		
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
	
	function save_map_draft( $data ){
	
		$page = $data['page'];
		$special = (bool) $data['special'];
		$map = $data['map'];
		
		$global_map = get_option( $this->map_option_slug_draft );
		
		$global_map['header'] = $map['header'];
		$global_map['footer'] = $map['footer'];
		$template_region_map = $map['template'];

		if( $special )
			$global_map[ $page ] = $template_region_map;
		else
			update_post_meta( $page, $this->map_option_slug_draft, $template_region_map );

		update_option( $this->map_option_slug_draft, $global_map );
		
	}

	
	
}

add_action( 'wp_ajax_pl_save_map_draft', 'save_map_draft' );
function save_map_draft(){
	
	$map = new EditorMap;
	
	$map->save_map_draft( $_POST );  
	
	echo true;
	die(); // don't forget this, always returns 0 w/o
	
}
