<?php

/* 
 * Functions library for editor
 */

function get_available_sections(){
	
	
	global $pl_section_factory; 
	
	return $pl_section_factory->sections; 
	
	
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

 	$raw = array(

 		'sections' => $extension_control->get_latest_cached( 'sections' ),
 		'plugins' => $extension_control->get_latest_cached( 'plugins' ),
 		'themes' => $extension_control->get_latest_cached( 'themes' )

 	);

 	// $raw holds everything straight from the server as the logged in user.

 	// right lets make this uber array...

 	$output = array();

 	foreach ( $raw as $type => $objects) {

 		foreach ( $objects as $data ) {

 		$output[] = array(

				'id'		=> $data->slug,  	// unique id
				'name'		=> $data->name,  	// title of extension
				'type'		=> $type, 		// type (section, plugin, theme)
				'thumb'		=> sprintf( 'http://api.pagelines.com/files/%s/img/%s-thumb.png', $type, $data->slug ),  // thumb
				'overview'	=> sprintf( 'http://www.pagelines.com/store/%s/%s', $type, $data->slug ),	// link to overview
				'rating'	=> 3.5,  			// rating on the store
				'downloads'	=> $data->count,  			// number of downloads
				'featured'	=> $data->featured, 			// is it featured?
				'emphasis'	=> '7', 			// emphasis in teh store, can use this to control size in isotope
				'tags'		=> array('tags', 'for', 'isotope', 'filtering') // tags for filtering

 			);
 		}
 	}
 		return $output;
	}
	function the_store_callback(){
 
		echo( json_encode(get_store_mixed(), JSON_FORCE_OBJECT) );
	}