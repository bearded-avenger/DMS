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
		return pl_opt( $this->map_option_slug, pl_settings_default(), true ); 
	}
	
	function map_local( $pageID ){
		return pl_meta( $pageID, $this->map_option_slug, pl_settings_default() );
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
	
		if( $map && isset($map['template']) && is_array($map['template']) ){
			return $map['template']; 
		} else 
			return $this->tpl->load_template( $map ); 
		
	}
	

	
	
	function publish_map( $pageID ){
	
		$global_map = pl_opt( $this->map_option_slug, pl_settings_default(), true );
		
		$global_map['live'] = $global_map['draft']; 
		
		pl_opt_update( $this->map_option_slug, $global_map );
		
		$local_map = pl_meta( $pageID, $this->map_option_slug, pl_settings_default()); 
		
		$local_map['live'] = $local_map['draft']; 
		
		pl_meta_update( $pageID, $this->map_option_slug, $local_map );
		
	}
	
	function revert_local( $pageID ){

		
		$local_map = pl_meta( $pageID, $this->map_option_slug, pl_settings_default()); 
		
		$local_map['draft'] = $local_map['live']; 
		
		pl_meta_update( $pageID, $this->map_option_slug, $local_map );
		
	}
	
	function revert_global(){
		
		
		$global_map = pl_opt( $this->map_option_slug, pl_settings_default(), true );
		
		$global_map['draft'] = $global_map['live']; 
		
		pl_opt_update( $this->map_option_slug, $global_map );
	}
	
	
	
	function save_map_draft( $pageID, $the_map ){

		$pageID =  $pageID;
		$map = $the_map;
	
		// global
		$global_map = pl_opt( $this->map_option_slug, pl_settings_default(), true );
		
		$global_map['draft'] = array(
			'header' => $map['header'],
			'footer' => $map['footer']
		);
		

		pl_opt_update( $this->map_option_slug, $global_map );
		
		$local_map = pl_meta( $pageID, $this->map_option_slug, pl_settings_default()); 
		
		$new_map = $local_map;
		
		$new_map['draft'] = array(
			'template' => $map['template']
		);
		
		if($new_map != $local_map){
			$this->save_local_map( $pageID, $new_map );
			$local = 1;
		} else 
			$local = 0;
		

		return array('local' => $local);
	}

	function save_local_map( $pageID, $map ){
		
		pl_meta_update( $pageID, $this->map_option_slug, $map );
	}
	
}


