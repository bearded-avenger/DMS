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
//	return get_store_mixed();
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
	$class = array(); 
	
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
			
		$class[] = $data->type;
		
		$class[] = ($data->type == 'themes') ? 'x-item-size-10' : 'x-item-size-5';

 		$output[] = array(

				'id'		=> $data->slug,  	// unique id
				'name'		=> $data->name,  	// title of extension
				'type'		=> $data->type, 		// type (section, plugin, theme)
				'thumb'		=> $data->thumb,  // thumb
				'splash'	=> $data->splash,  // splash
				'overview'	=> sprintf( 'http://www.pagelines.com/store/%s/%s', $data->type, $data->slug ),	// link to overview
				'rating'	=> 3.5,  			// rating on the store
				'downloads'	=> $data->count,  			// number of downloads
				'featured'	=> $data->featured, 			// is it featured?
				'emphasis'	=> '7', 			// emphasis in teh store, can use this to control size in isotope
				'tags'		=> implode( ',', $tags),
				'class'		=> implode(' ', $class)
 		);
 	}
		

 	return $output; //shuffle( $output ); // shuffle is a temporary work around for demo purposes until we get something algorhythmic
}

function the_store_callback(){
 
	echo( json_encode(get_store_mixed(), JSON_FORCE_OBJECT) );
}
	
	
function editor_get_raw_less( $array = false ) {
	
	$pagelines_dynamic_css = new PageLinesCSS;

	$pagelines_dynamic_css->typography();

	$typography = $pagelines_dynamic_css->css;

	unset( $pagelines_dynamic_css->css );
	global $pagelines_layout;
	$pagelines_layout = new PageLinesLayout();
	$pagelines_dynamic_css->layout();
	$pagelines_dynamic_css->options();
	$dynamic = $pagelines_dynamic_css->css;
	
	
	$core = PageLinesRenderCSS::load_core_cssfiles( PageLinesRenderCSS::get_core_lessfiles());
	
	$pless = new PageLinesLess;
	
	$vars_array = $pless->constants;
	
	$vars = '';
	foreach($vars_array as $key => $value)
		$vars .= sprintf('@%s:%s;%s', $key, $value, "\n");
		
	$bootstrap = $pless->add_bootstrap();
	
	$sections = PageLinesRenderCSS::get_all_active_sections();
	
	$extra = PageLinesRenderCSS::pagelines_insert_core_less_callback( '' );
	
	if( ! $array )
		return $vars . $bootstrap . $core . $sections . $extra . $typography . $dynamic;
		
	return array( 
		
		'vars'		=> $vars,
		'bootstrap'	=> $bootstrap,
		'core'		=> $core,
		'sections'	=> $sections,
		'extra'		=> $extra,
		'typography'=> $typography,
		'dynamic'	=> $dynamic
		);	
}
//add_filter( 'query_vars', 'pagelines_editor_less_var');
//add_action( 'template_redirect', 'pagelines_editor_less_trigger', 15);
//add_action( 'wp_head', 'pagelines_editor_less_styles', 6);

function pagelines_editor_less_styles() {
	
	$url = home_url() . '?raw_less=1';
	echo "\n<link rel='stylesheet' id='editor-less' href='$url' type='text/less' media='all' />\n";
    
}

function pagelines_editor_less_var( $vars ) {
    $vars[] = 'raw_less';
    return $vars;
}
function pagelines_editor_less_trigger() {
	if( intval( get_query_var( 'raw_less' ) ) ) {
		header( 'Content-type: text/less' );
		echo editor_get_raw_less();
		exit();
	}			
}