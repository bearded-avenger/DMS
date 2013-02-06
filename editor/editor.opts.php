<?php


define('PL_SETTINGS', 'pl-settings'); 

/**
 * 
 *
 *  PageLines Settings
 *
 *
 */
class PageLinesOpts {

	var $pl_settings = 'pl-settings';
	var $default = array( 'draft' => array(), 'live' => array() );

	function __construct( PageLinesPage $page, EditorDraft $draft ){
		
		$this->page = $page; 
		$this->draft = $draft;
		
		$this->local = $this->local_settings();
		$this->type = $this->type_settings();
		$this->global = $this->global_settings();
		$this->set = $this->page_settings();
		//plprint($this->set);
	}
	
	function page_settings(){
		
		$set = $this->parse_settings( $this->local, $this->parse_settings($this->type, $this->global));
//		plprint($set);
		return $set;
		
		
	}
	
	function parse_settings( $top, $bottom ){
	
		
		$parsed_args = wp_parse_args($top, $bottom); // make sure all args are there.. 
		
		foreach($parsed_args as $key => &$set){
			
			if( is_array($set) ){
				foreach($set as $clone => &$value){
			
					if( 
						( !isset($value) || $value == '' || !$value ) 
						&& isset( $bottom[$key][$clone] ) 
					)
						$value = $bottom[$key][$clone];
						
					$flipkey = $key.'-flip';
						
					// flipping checkboxes
					if( isset($parsed_args[$flipkey]) && isset($parsed_args[$flipkey][$clone]) && isset($bottom[$key][$clone]) ){
									
						$flip_val = $parsed_args[$flipkey][$clone];
						$bottom_val = $bottom[$key][$clone];

						if( $flip_val && $bottom_val )
							$value = '';
					
					}
					
	
				}
			}
			
		}
		unset($set); 
		unset($value); 
		
		return $parsed_args;
	}
	
	function setting( $key ){
		
		if( isset( $this->local[$key] ) )
			return $this->local[$key];
		
		elseif( isset( $this->type[$key] ) )
			return $this->type[$key];
			
		elseif( isset( $this->global[$key] ) )
			return $this->global[$key];
			
		else
			return false;
		
	}
	
	function local_settings(){
		
		$set = $this->meta( $this->page->id, $this->pl_settings );
		
		return $this->get_by_mode($set); 
		
	}
	
	function type_settings(){
		
		$set = $this->meta( $this->page->typeid, $this->pl_settings );
		
		return $this->get_by_mode($set); 
		
	}
	
	function global_settings(){
		
		$set = $this->opt( $this->pl_settings );
		
		return $this->get_by_mode($set); 
		
	}
	
	function get_by_mode( $set ){
		
		$set = wp_parse_args( $set, $this->default );
		
		return $set[ $this->draft->mode ]; 
	}
	
	function meta($id, $key, $default = false){

		$val = get_post_meta($id, $key, true);

		if( !$val ){

			$val = $default;

		} elseif( is_array($val) && is_array($default)) {

			$val = wp_parse_args( $val, $default );

		}

		return $val;

	}
	
	function opt( $key, $default = false, $parse = false ){
		
		$val = get_option($key); 

		if( !$val ){

			$val = $default;

		} elseif( $parse && is_array($val) && is_array($default)) {

			$val = wp_parse_args( $val, $default );

		}

		return $val;
		
	}

}


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


function pl_setting( $key ){
	
	// is page set
	
	$plpg->page->id; 
	
	// is type set 
	$plpg->page->typeid;
	
	// is global default
	
	// else return default
	
	
	global $pldrft;
	
	$mode = $pldrft->mode;
	
	$set = pl_meta( $plpg->page->id, PL_SETTINGS );
	$set = pl_meta( $plpg->page->id, PL_SETTINGS );
	$set = pl_meta( $plpg->page->id, PL_SETTINGS );
	
	if( isset( $set[$mode] ) && isset( $set[$mode][$key] ) )
		return $set[$mode][$key]; 
	
	
	
	
	$set = pl_opt( PL_SETTINGS ); 
	
	$settings = ( isset($set[ $mode ]) ) ? $set[ $mode ] : array();
	
	
	
	return ( isset( $settings[ $key ] ) ) ? $settings[ $key ] : false;
	
	
}

function pl_meta_setting( $key, $metaID ){
	
	global $pldrft;
	
	$mode = $pldrft->mode;
	
	$set = pl_meta( $metaID, PL_SETTINGS );
	
	$settings = ( isset($set[ $mode ]) ) ? $set[ $mode ] : array();
	
	return ( isset( $settings[ $key ] ) ) ? $settings[ $key ] : false;
	
}

function pl_global_setting( $key ){
	
	global $pldrft;
	
	$mode = $pldrft->mode;
	
	$set = pl_opt( PL_SETTINGS ); 
	
	$settings = ( isset($set[ $mode ]) ) ? $set[ $mode ] : array();
	
	return ( isset( $settings[ $key ] ) ) ? $settings[ $key ] : false;
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

function pl_settings_update( $new_settings, $mode = 'draft', $metaID = false ){
	
	$default = array( 'draft' => array(), 'live' => array() );
	
	
	if( $metaID )
		$settings = pl_meta( $metaID, PL_SETTINGS );
	else 
		$settings = pl_opt(PL_SETTINGS);
	
	$settings = wp_parse_args($settings, $default);

	$settings[ $mode ] = wp_parse_args( $new_settings, $settings[ $mode ] ); 

	if( $metaID )
		pl_meta_update( $metaID, PL_SETTINGS, $settings );
	else
		pl_opt_update( PL_SETTINGS, $settings );
	
	return $settings;
}

function pl_revert_settings( $metaID = false ){
	
	if( $metaID ){
		$set = pl_meta( $metaID, PL_SETTINGS, $default );
		
	} else {
		$set = pl_opt(PL_SETTINGS, $default); 
	}
	
	$set['draft'] = $set['live']; 
	
	if( $metaID )
		pl_meta_update( $metaID, PL_SETTINGS, $set );
	else
		pl_opt_update( PL_SETTINGS, $set );
	
}

function pl_publish_settings( $pageID, $typeID ){
	
	$settings = array(); 
	
	$settings['local'] = pl_meta( $pageID, PL_SETTINGS );
	$settings['type'] = pl_meta( $typeID, PL_SETTINGS );
	$settings['global'] = pl_opt( PL_SETTINGS  );
	
	
	foreach($settings as $scope => $set){
		
		$set = wp_parse_args($set, array('live'=> array(), 'draft' => array()));
		
		$set['live'] = $set['draft'];
		
		$settings[ $scope ] = $set;
			
	}
	
	pl_meta_update( $pageID, PL_SETTINGS, $settings['local'] );
	pl_meta_update( $typeID, PL_SETTINGS, $settings['type'] );
	pl_opt_update( PL_SETTINGS, $settings['global'] );
	
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

