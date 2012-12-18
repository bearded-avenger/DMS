<?php

class EditorLess {
	
	function __construct() {}
		
	function enqueue_styles() {
		
		return; // REMOVE TO ENABLE
		
		
		// need to enqueue a STATIC vars file.
		// we need to enqueue all available less files.
		
		// add constants + imports to head
		add_action( 'wp_head', array( &$this, 'add_constants' ), 3);
		
		// remove main compiles-css
		add_action( 'wp_print_styles', array( &$this, 'dequeue_css' ), 12 );
		
	// these were uses to enqueue the raw files, didnt work.
	//	add_filter( 'style_loader_tag', array( &$this, 'enqueue_less_styles' ), 5, 2);
	//	$this->enqueue_core_less();
		
	}
	
	function add_constants() {		
		printf( "<style type='text/less'>%s%s</style>",
		$this->get_constants(),
		$this->make_imports()		
		);
	}
	
	function make_imports() {
		
		$files = $this->get_core_less();
		$out = '';
		
		foreach ( $files as $k => $file ) {
			
			$out .= sprintf( '%s@import url("%s/%s.less");', "\n", PL_CORE_LESS_URL, $file );
		}
		
		return $out;
	}
	
	function get_constants() {
		
		$pless = new PageLinesLess;
		$vars_array = $pless->constants;
		$vars = '';
		foreach($vars_array as $key => $value)
			$vars .= sprintf('@%s:%s%s;%s', $key, " ", $this->escape( $value ), "\n");
			
		
		return $vars;
	}



	function dequeue_css() {
		
		wp_deregister_style( 'pagelines-less' );
	}
	
	function get_core_less() {
		
		$less = array( 'variables', 'mixins', 'colors' );
		
		global $render_css;
		return array_merge( $less, $render_css->get_core_lessfiles() );
	}
	
	// experimenting with escaping the less variables...
	function escape( $value ) {
	
	
	return $value;
		
		if( preg_match( '#"#', $value ) )		
			return '~' . rtrim( $value, '"' ) . '"';
		else
			return $value;
	}
	
/*	
	function enqueue_core_less() {
		
		foreach ( $this->get_core_less() as $k => $file ) {
			
			$id = $file;
			$file 	= sprintf( '%s.less', $file );
			$parent = sprintf( '%s/%s', PL_CORE_LESS, $file );
			$parent_url = sprintf( '%s/%s', PL_CORE_LESS_URL, $file );
			$child 	= sprintf( '%s/%s', PL_CHILD_LESS, $file );
			$child_url = sprintf( '%s/%s', PL_CHILD_LESS_URL, $file );
			if ( is_file( $child ) )
				wp_enqueue_style( $id, $child_url );
			else
				wp_enqueue_style( $id, $parent_url );
		}
		
	}

	function enqueue_less_styles($tag, $handle) {
	    global $wp_styles;
	    $match_pattern = '/\.less$/U';
	    if ( preg_match( $match_pattern, $wp_styles->registered[$handle]->src ) ) {
	        $handle = $wp_styles->registered[$handle]->handle;
	        $media = $wp_styles->registered[$handle]->args;
	        $href = $wp_styles->registered[$handle]->src;
	        $rel = isset($wp_styles->registered[$handle]->extra['alt']) && $wp_styles->registered[$handle]->extra['alt'] ? 'alternate stylesheet' : 'stylesheet';
	        $title = isset($wp_styles->registered[$handle]->extra['title']) ? "title='" . esc_attr( $wp_styles->registered[$handle]->extra['title'] ) . "'" : '';

	        $tag = "<link rel='stylesheet/less' href='$href' type='text/css'>\n";
	    }
	    return $tag;
	}
*/
	
} // EditorLess