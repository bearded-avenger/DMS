<?php



class EditorThemeHandler {
	
	var $preview_slug = 'pl-theme-preview';
	
	function activate( $response ){
		
		$new = $response['post']['stylesheet'];
		
		$theme = wp_get_theme( $new );
		
		if ( !$new || !$theme->exists() || !$theme->is_allowed() ){
			$response['error'] = 'Theme does not exist or is not allowed';
			return $response;
		}
			
			
		switch_theme( $theme->get_stylesheet() );
		
		$response['success'] = 'Theme Switched!';
		$response['new'] = $new;
		
		return $response;
	}
	
	function set_preview(){
		
		$new = $response['post']['stylesheet'];
		
		$theme = wp_get_theme( $new );
		
		if ( !$new || !$theme->exists() || !$theme->is_allowed() ){
			$response['error'] = 'Theme does not exist or is not allowed';
			return $response;
		} else {
			echo 'here';
			pl_update_setting($this->preview_slug, $new);
			
			return $response;
			
		}
		
	}

	function maybe_load_preview( $active_stylesheet ){
		
		$preview_theme = $this->determine_theme( $active_stylesheet );
	
		if ( $preview_theme ){
			
			$preview_theme_object = wp_get_theme( $preview_theme ); 
			
			add_action('before_toolbox_panel', array(&$this, 'add_preview_banner')); 
			
			return $preview_theme_object->get_stylesheet(); 
			
		} else
			return $active_stylesheet;
		
			
		
	}
	
	function determine_theme( $active_stylesheet ){
		$preview_stylesheet = pl_setting( $this->preview_slug ); 
		
		if( $preview_stylesheet && $preview_stylesheet != $active_stylesheet )
			return $preview_stylesheet; 
		else 
			return false;
	}
	
	function add_preview_banner(){
		
		echo ' this is the end of the world.... >> '; 
	}
		
	
}