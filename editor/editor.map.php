<?php

/**
 * 
 *  Template Map Handler
 *
 */
class EditorMap {
	
	var $map_option_slug = 'pl-template-map';
	
	
	var $map_default = array(
		'live' 	=> array(),
		'draft'	=> array()
	);
	
	function __construct( EditorTemplates $tpl, EditorDraft $draft ){
	
		$this->tpl = $tpl;
		$this->draft = $draft;

	}
	
	function get_map( PageLinesPage $page ){
	
		$map_global = $this->map_global( ); 
		$map_local = $this->map_local( $page->id );
		
		$map['header'] = $this->get_header( $map_global[ $this->draft->mode ] ); 
		$map['footer'] = $this->get_footer( $map_global[ $this->draft->mode ] ); 
		$map['template'] = $this->get_template( $map_local[ $this->draft->mode ] ); 
		
		return $map;
		
	}
	
	function map_global(){
		return pl_opt( $this->map_option_slug, $this->map_default, true ); 
	}
	
	function map_local( $pageID ){
		return pl_meta( $pageID, $this->map_option_slug, $this->map_default );
	}
	
	function get_header( $map ){
		
		if( $map && isset($map['header']))
			return $map['header']; 
		else 
			return $this->tpl->default_header();
			
	}
	
	function get_footer( $map ){
		
		if( $map && isset($map['footer']))
			return $map['footer']; 
		else 
			return $this->tpl->default_footer();
	}
	
	function get_template( $map ){
	
		if( $map && isset($map['template']) ){
			return $map['template']; 
		} else 
			return $this->tpl->default_template(); 
		
	}
	

	
	
	function publish_map( $pageID ){
	
		$global_map = pl_opt( $this->map_option_slug, $this->map_default, true );
		
		$global_map['live'] = $global_map['draft']; 
		
		pl_opt_update( $this->map_option_slug, $global_map );
		
		$local_map = pl_meta( $pageID, $this->map_option_slug, $this->map_default); 
		
		$local_map['live'] = $local_map['draft']; 
		
		pl_meta_update( $pageID, $this->map_option_slug, $local_map );
		
	}
	
	function revert_local( $pageID ){

		
		$local_map = pl_meta( $pageID, $this->map_option_slug, $this->map_default); 
		
		$local_map['draft'] = $local_map['live']; 
		
		pl_meta_update( $pageID, $this->map_option_slug, $local_map );
		
	}
	
	function revert_global(){
		
		
		$global_map = pl_opt( $this->map_option_slug, $this->map_default, true );
		
		$global_map['draft'] = $global_map['live']; 
		
		pl_opt_update( $this->map_option_slug, $global_map );
	}
	
	function set_new_local_template( $pageID, $map ){
			
		$local_map = pl_meta( $pageID, $this->map_option_slug, $this->map_default); 
		
		$local_map['draft'] = array(
			'template' => $map
		);

		$this->save_local_map( $pageID, $local_map );
	}
	
	function save_map_draft( $pageID, $the_map ){

		$pageID =  $pageID;
		$map = $the_map;
	
		// global
		$global_map = pl_opt( $this->map_option_slug, $this->map_default, true );
		
		$global_map['draft'] = array(
			'header' => $map['header'],
			'footer' => $map['footer']
		);
		
		if( $global_map['live'] != $global_map['draft'] ){
			$this->draft->set_global();		
		} else {
			$this->draft->set_global( false );
		}
		
		pl_opt_update( $this->map_option_slug, $global_map );
		
		$local_map = pl_meta( $pageID, $this->map_option_slug, $this->map_default); 
		
		$local_map['draft'] = array(
			'template' => $map['template']
		);
		
		$this->save_local_map( $pageID, $local_map );


	}

	function save_local_map( $pageID, $map ){
		
		if( $map['live'] != $map['draft'] )
			$this->draft->set_local( $pageID );
		else
			$this->draft->set_local( $pageID, false );
		
		pl_meta_update( $pageID, $this->map_option_slug, $map );
	}
	
}


