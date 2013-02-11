<?php

class EditorLayout {


	function __construct(  ){
		
		add_filter('pless_vars', array(&$this, 'add_less_vars')); 
	
	}
	

	function add_less_vars( $less_vars ){
		
		// if pixel mode assign pixel option
		
		$value = (pl_setting( 'content_width_px' )) ? pl_setting( 'content_width_px' ) : '980px'; 
		
		// if percent mode assign percent option
		
		$less_vars['plContentWidth'] = $value;
		
		return $less_vars;
		
	}
	
	
	
}