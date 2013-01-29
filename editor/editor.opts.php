<?php


define('PL_SETTINGS', 'pl-settings'); 


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


/*
 *
 * Local Option	
 *
 */
function pl_settings( $mode = 'draft', $metaID = false ){

	$default = array( 'draft' => array(), 'live' => array() );
	
	if( $metaID ){
		
		$set = pl_meta( $metaID, PL_SETTINGS, $default );
	
	} else {

		$set = pl_opt(PL_SETTINGS, $default); 
		
	}
	
	$settings = ( isset($set[ $mode ]) ) ? $set[ $mode ] : $default;
	
	return $settings;
	
}

function pl_settings_update( $settings, $mode = 'draft', $metaID = false ){
	
	$default = array( 'draft' => array(), 'live' => array() );
	
	
	$set = pl_settings( $mode, $metaID );
	
	$set = wp_parse_args($set, $default);

	$set[ $mode ] = wp_parse_args( $settings, $set[ $mode ] ); 

	if( $metaID )
		pl_meta_update( $metaID, PL_SETTINGS, $set );
	else
		pl_opt_update( PL_SETTINGS, $set );
	
	return $set;
}

/*
 *
 * Type Option	
 *
 */

/*
 *
 * Global Option	
 *
 */
function pl_opt_global( $mode = 'draft' ){
	$default = array( 'draft' => array(), 'live' => array() );
	
	$option_set = pl_opt(PL_SETTINGS, $default); 
	
	return $option_set[ $mode ]; 
}

function pl_opt_update_global( $set, $mode = 'draft'){
	
	$default = array( 'draft' => array(), 'live' => array() );
	
	$option_set = pl_opt(PL_SETTINGS, $default); 
	
	if($mode == 'draft'){
		$option_set['draft'] = wp_parse_args($set, $option_set['draft']); 
	}
	
	pl_opt_update( PL_SETTINGS, $option_set ); 
	
}

