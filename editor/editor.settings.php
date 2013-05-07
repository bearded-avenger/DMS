<?php


define('PL_SETTINGS', 'pl-settings');
function pl_settings_default(){
	return array( 'draft' => array(), 'live' => array() );
}

function pl_setting( $key, $args = array() ){
	global $plopts;

	if(!is_object($plopts)){
		$plpg = new PageLinesPage;
		$pldraft = new EditorDraft;
		$plopts = new PageLinesOpts( $plpg, $pldraft );
	}

	$setting = $plopts->get_setting( $key, $args );

	return $setting;

}

function pl_setting_update( $args_or_key, $value = false, $mode = 'draft', $scope = 'global' ){
	$settings_handler = new PageLinesSettings;

	if( is_array($args_or_key) ){
		$args = $args_or_key;
	} else {

		$args = array(
			'key' 	=> $args_or_key,
			'val'	=> $value,
			'mode'	=> $mode,
			'scope'	=> $scope
		);

	}

	$settings_handler->update_setting( $args );

}

function pl_meta($id, $key, $default = false){

	$data = new PageLinesData;
	return $data->meta($id, $key, $default);

}


function pl_meta_update($id, $key, $value){

	$data = new PageLinesData;
	return $data->meta_update($id, $key, $value);

}

/*
 * This class contains all methods for interacting with WordPress' data system
 * It has no dependancy so it can be used as a substitute for WordPress native functions
 * The options system inherits from it.
 */
class PageLinesData {

	function meta($id, $key, $default = false){

		$val = get_post_meta($id, $key, true);

		if( (!$val || $val == '') && $default ){

			$val = $default;

		} elseif( is_array($val) && is_array($default)) {

			$val = wp_parse_args( $val, $default );

		}

		return $val;

	}

	function meta_update($id, $key, $value){

		update_post_meta($id, $key, $value);

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

	function opt_update( $key, $value ){

		update_option($key, $value);

	}

	function user( $user_id, $key, $default = false ){

		$val = get_user_meta($user_id, $key, true);

		if( !$val ){

			$val = $default;

		} elseif( is_array($val) && is_array($default)) {

			$val = wp_parse_args( $val, $default );

		}

		return $val;

	}

	function user_update( $user_id, $key, $value ){
		update_user_meta( $user_id, $key, $value );
	}



}

/*
 *  PageLines Settings Interface
 */
class PageLinesSettings extends PageLinesData {

	var $pl_settings = PL_SETTINGS;
	var $default = array( 'draft' => array(), 'live' => array() );

	function global_settings(){

		$set = $this->opt( $this->pl_settings );

		// Have to move this to an action because ploption calls pl_setting before all settings are loaded
		if( !$set || empty($set['draft']) || empty($set['live']) )
			add_action('pl_after_settings_load', array(&$this, 'set_default_settings'));

		return $this->get_by_mode($set);

	}

	/*
	 *  Resets global options to an empty set
	 */
	function reset_global(  ){

		$set = $this->opt( PL_SETTINGS, $this->default );

		$set['draft'] = $this->default['draft'];

		$this->opt_update( PL_SETTINGS, $set );
		
		// why not run after we reset? 
		// This is checked on page load, may not be necessary to do both
		$this->set_default_settings();

	}

	/*
	 *  Resets local options to an empty set based on ID (works for type ID)
	 */
	function reset_local( $metaID ){

		$set = $this->meta( $metaID, PL_SETTINGS, $this->default );

		$set['draft'] = $this->default['draft'];

		$this->meta_update( $metaID, PL_SETTINGS, $set );

	}

	/*
	 *  Sets default values for global settings
	 */
	function set_default_settings(){

		$set = $this->opt( $this->pl_settings );

		$settings_defaults = $this->get_default_settings();

		if( !$set )
			$set = $this->default;

		if(empty($set['draft']))
			$set['draft'] = $settings_defaults;

		if(empty($set['live']))
			$set['live'] = $settings_defaults;

		$this->opt_update( $this->pl_settings, $set);

	}

	/*
	 *  Grabs global settings engine array, and default values (set in array)
	 */
	function get_default_settings(){
		$settings_object = new EditorSettings;

		$settings = $settings_object->get_set();


		$defaults = array();
		foreach($settings as $tab => $tab_settings){
			foreach($tab_settings['opts'] as $index => $opt){
				if($opt['type'] == 'multi'){
					foreach($opt['opts'] as $subi => $sub_opt){
						if(isset($sub_opt['default'])){
							$defaults[ $sub_opt['key'] ] = array( $sub_opt['default'] );
						}
					}
				}
				if(isset($opt['default'])){
					$defaults[ $opt['key'] ] = array( $opt['default'] );
				}
			}
		}

		return $defaults;
	}



	/*
	 *  Update a PageLines setting using arguments
	 */
	function update_setting( $args = array() ){

		$defaults = array(
			'key'	=> '',
			'val'	=> '',
			'mode'	=> 'draft',
			'scope'	=> 'global'
		);

		$a = wp_parse_args( $args, $defaults );

		$scope = $a['scope'];
		$mode = $a['mode'];
		$key = $a['key'];
		$val = $a['val'];

		// Allow for an array of key/value pairs
		$set = ( !is_array($val) && $key != '' ) ? array( $key => $value ) : $val;

		if( $scope == 'global'){

			$settings = $this->opt( $this->pl_settings, $this->default );

			if( isset($settings[ $mode ]) ){

				$settings[ $mode ] = wp_parse_args($set, $settings[ $mode ]);

				pl_opt_update( PL_SETTINGS, $option_set );

			}


		}
	}


	/*
	 *  Parse settings taking the top values over the bottom
	 * 	Deep parsing: Parses arguments on nested arrays then deals with overriding
	 *  Checkboxes: Handles checkboxes by using 'flip' value settings to toggle the value
	 */
	function parse_settings( $top, $bottom ){


		if(!is_array( $bottom ))
			return $top;

		// Parse Args Deep
		foreach($bottom as $key => $set){

			if( !isset($top[$key]) )
				$top[$key] = $set;

			elseif(is_array($set)){
				foreach($set as $clone => $value){
					if( !isset($top[$key][$clone]) )
						$top[$key][$clone] = $value;
				}
			}

		}

		$parsed_args = $top;

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

						if( $flip_val && $bottom_val ){
							$value = '';
						}


					}



				}
			}

		}
		unset($set);
		unset($value);

		return $parsed_args;
	}

}



/**
 *  PageLines *Page Specific* Settings Interface
 * 	Has a dependancy on the PageLinesPage object and EditorDraft object
 */
class PageLinesOpts extends PageLinesSettings {

	function __construct( PageLinesPage $page, EditorDraft $draft ){

		$this->page = $page;
		$this->draft = $draft;

		$this->local = $this->local_settings();
		$this->type = $this->type_settings();
		$this->global = $this->global_settings();
		$this->set = $this->page_settings();

	}


	function page_settings(){

		$set = $this->parse_settings( $this->local, $this->parse_settings($this->type, $this->global));

		return $set;

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

	function get_setting( $key, $args = array() ){

		$not_set = (isset($args['default'])) ? $args['default'] : false;


		$index = ( isset( $args['clone_id']) ) ? $args['clone_id'] : 0;

		return ( isset( $this->set[ $key ][ $index ] ) ) ? $this->set[ $key ][ $index ] : $not_set;

	}


	function get_by_mode( $set ){

		$set = wp_parse_args( $set, $this->default );

		return $set[ $this->draft->mode ];
	}




}

































////////////////////////////////////////////////////////////////////
//
// TODO rewrite all this to use the ^^ above classes methods...
//
////////////////////////////////////////////////////////////////////
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

	// in case of empty, use live/draft default
	$settings = wp_parse_args($settings, pl_settings_default());

	// now compare new with old, prefering new.. 
	$settings[ $mode ] = wp_parse_args( $new_settings, $settings[ $mode ] );

	// lets do some clean up
	// Gonna clear out all the empty values and arrays
	// Also, needs to be array or... deletehammer
	foreach($settings[$mode] as $key => $clones){
		if(is_array($clones)){
			foreach($clones as $clone_id => $val){
				if( empty($val) || $val == '')
					unset( $settings[$mode][$key][$clone_id] );
			}
			
			if(empty($clones))
				unset( $settings[$mode][$key] );
		} else {
			unset( $settings[$mode][$key] );	
		}
		
		
	}

	if( $metaID )
		pl_meta_update( $metaID, PL_SETTINGS, $settings );
	else
		pl_opt_update( PL_SETTINGS, $settings );

	return $settings;
}

function pl_revert_settings( $metaID = false ){

	if( $metaID ){
		$set = pl_meta( $metaID, PL_SETTINGS, pl_settings_default() );

	} else {
		$set = pl_opt(PL_SETTINGS, pl_settings_default());
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
	$settings['local-map'] = pl_meta( $pageID, 'pl-template-map' );
	$settings['global-map'] = pl_opt( 'pl-template-map' );


	foreach($settings as $scope => $set){

		$set = wp_parse_args($set, array('live'=> array(), 'draft' => array()));

		$set['live'] = $set['draft'];

		$settings[ $scope ] = $set;

	}

	pl_meta_update( $pageID, PL_SETTINGS, $settings['local'] );
	pl_meta_update( $typeID, PL_SETTINGS, $settings['type'] );
	pl_opt_update( PL_SETTINGS, $settings['global'] );

	pl_meta_update( $pageID, 'pl-template-map', $settings['local-map'] );
	pl_opt_update( 'pl-template-map', $settings['global-map'] );

	// Flush less
	do_action( 'extend_flush' );

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

