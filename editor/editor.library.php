<?php

/* 
 * Functions library for editor
 */

function get_available_sections(){
	
	
	global $pl_section_factory; 
	
	return $pl_section_factory->sections; 
	
	
}

function store_mixed_array(){
	
	if ( false === ( $store_mixed_array = get_transient( 'store_mixed_array' ) ) ) {
	    
	     $store_mixed_array = get_store_mixed();
	     set_transient( 'store_mixed_array', $store_mixed_array );
	}
	
	return $store_mixed_array;
	
}

function get_store_mixed(){
 
 	global $extension_control;

 	if( ! is_object( $extension_control ) ) {
 		

 	require_once ( PL_ADMIN . '/class.extend.php' );
	require_once ( PL_ADMIN . '/class.extend.ui.php' );
	require_once ( PL_ADMIN . '/class.extend.actions.php' );
	require_once ( PL_ADMIN . '/class.extend.integrations.php' );
	require_once ( PL_ADMIN . '/class.extend.themes.php' );
	require_once ( PL_ADMIN . '/class.extend.plugins.php' );
	require_once ( PL_ADMIN . '/class.extend.sections.php' );
	$extension_control = new PagelinesExtensions;
	}
 	$raw =  $extension_control->get_latest_cached( 'all', 100 );
	if( ! is_object( $raw ) )
		return null;
 	// $raw holds everything straight from the server as the logged in user.

 	// right lets make this uber array...

 	$output = array();

 	foreach ( $raw as $key => $data) {

		unset( $tags );
		$tags = array();
		
		if ( 'true' == $data->featured )
			$tags[] = 'featured';

		if ( 'true' == $data->plus_product )
			$tags[] = 'plus';

		if( 'free' != $data->price )
			$tags[] = 'premium';
		else
			$tags[] = 'free';

 		$output[] = array(

				'id'		=> $data->slug,  	// unique id
				'name'		=> $data->name,  	// title of extension
				'type'		=> $data->type, 		// type (section, plugin, theme)
				'thumb'		=> sprintf( 'http://api.pagelines.com/files/%s/img/%s-thumb.png', $data->type, $data->slug ),  // thumb
				'overview'	=> sprintf( 'http://www.pagelines.com/store/%s/%s', $data->type, $data->slug ),	// link to overview
				'rating'	=> 3.5,  			// rating on the store
				'downloads'	=> $data->count,  			// number of downloads
				'featured'	=> $data->featured, 			// is it featured?
				'emphasis'	=> '7', 			// emphasis in teh store, can use this to control size in isotope
				'tags'		=> implode( ',', $tags)

 			);
 		}
		

 		return $output;
	}
	function the_store_callback(){
 
		echo( json_encode(get_store_mixed(), JSON_FORCE_OBJECT) );
	}