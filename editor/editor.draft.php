<?php


class EditorDraft{
	
	var $slug = 'pl-draft';
	
	function __construct( ){
		
		if( current_user_can('edit_themes') )
			$this->mode = 'draft';
		else 
			$this->mode = 'live';
			
			
	}

	function publish( $data, EditorMap $map ){
		
		$this->reset_state( $data['page'] );
		
		$map->publish_map( $data['page'] );
		
		
	}

	function get_state( $pageID ){
		
		
		if( pl_meta( $pageID, $this->slug ) )
			$state = 'local';
		elseif( pl_opt( $this->slug ) )
			$state = 'global';
		else
			$state = 'clean';
			
		return $state;
		
	}
	
	function reset_state( $pageID ){
		pl_meta_update( $pageID, $this->slug, false );
		pl_opt_update( $this->slug, false );
	}

	function set_local( $pageID, $val = true ){
		pl_meta_update( $pageID, $this->slug, $val );
	}
	
	function set_global( $val = true ){
		pl_opt_update( $this->slug, $val );
	}
	
}