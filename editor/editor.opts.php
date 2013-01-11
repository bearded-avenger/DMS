<?php


function pl_opt( $key, $default = false ){
	
	$val = get_option($key); 
	
	if( !$val ){
		
		$val = $default;

	} elseif( is_array($val) && is_array($default)) {
		
		$val = wp_parse_args( $val, $default );
		
	}
	
	return $val;
	
}

function pl_opt_update( $key, $value ){
	
	update_option($key, $value);
	
}

function pl_meta($id, $key, $default = false){

	$val = get_post_meta($id, $key, true);
	
	if( !$val ){
		
		$val = $default;

	} elseif( is_array($val) && is_array($default)) {
		
		$val = wp_parse_args( $val, $default );
		
	}
	
	return $val;
	
}


function pl_meta_update($id, $key, $value){

	update_post_meta($id, $key, $value);
	
}

