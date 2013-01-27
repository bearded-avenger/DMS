<?php


define('PL_GLOBAL_SETTINGS', 'pl-global-settings'); 


function pl_opt( $key, $default = false, $parse = false ){
	
	$val = get_option($key); 
	
	if( !$val ){
		
		$val = $default;

	} elseif( $parse && is_array($val) && is_array($default)) {
		
		$val = wp_parse_args( $val, $default );
		
	}
	
	return $val;
	
}

function pl_opt_update( $key, $value ){
	
	update_option($key, $value);
	
}

function pl_opt_global( $mode = 'draft' ){
	$default = array( 'draft' => array(), 'live' => array() );
	
	$option_set = pl_opt(PL_GLOBAL_SETTINGS, $default); 
	
	return $option_set[ $mode ]; 
}

function pl_opt_update_global( $set, $mode = 'draft'){
	
	$default = array( 'draft' => array(), 'live' => array() );
	
	$option_set = pl_opt(PL_GLOBAL_SETTINGS, $default); 
	
	if($mode == 'draft'){
		$option_set['draft'] = wp_parse_args($set, $option_set['draft']); 
	}
	
	pl_opt_update( PL_GLOBAL_SETTINGS, $option_set ); 
	
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

