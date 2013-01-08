<?php
/**
 * 
 *  PageLines Editor Data Handling
 *
 */

add_action( 'wp_ajax_pl_save_map_draft', 'save_map_draft' );
function save_map_draft(){
	
	$option_slug = 'pl-template-map';
	$page = $_POST['page'];
	$special = (bool) $_POST['special'];
	$map = $_POST['map'];
	
	$global_map = get_option( $option_slug );
	
	$template_region_map = $map['template'];
	
	$global_map['header'] = $map['header'];
	$global_map['footer'] = $map['footer'];
	
	if( $special ){
	
		$global_map[ $page ] = $template_region_map;
		
		$action = get_option( $option_slug );
		
	} else {
		
		update_post_meta( $page, $option_slug, $template_region_map );
		$action = get_post_meta( $page, $option_slug, true);
	}

	update_option( $option_slug, $global_map );

//	print_r( get_post_meta( $page, $option_slug, true) );
	echo true;
	die(); // don't forget this, always returns 0 w/o
	
}

