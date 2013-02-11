<?php

class EditorLayout {


	function __construct(  ){
		
		add_filter('pless_vars', array(&$this, 'add_less_vars')); 
	
	}
	

	function add_less_vars( $less_vars ){
		
		// if pixel mode assign pixel option
		
		if( pl_setting( 'layout_mode' ) == 'percent' )
			$value = (pl_setting( 'content_width_percent' )) ? pl_setting( 'content_width_percent' ) : '80%'; 
		else 
			$value = (pl_setting( 'content_width_px' )) ? pl_setting( 'content_width_px' ) : '980px'; 
		
		// if percent mode assign percent option
		
		$less_vars['plContentWidth'] = $value;
		
		return $less_vars;
		
	}
	
	function get_layout_mode(){
		
		$value = (pl_setting( 'layout_mode' )) ? pl_setting( 'layout_mode' ) : 'pixel';
		
		return $value; 
		
	}
	
	
}