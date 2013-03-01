<?php

class EditorLess {

	function __construct( PageLinesLess $pless ) {

		global $wp_styles;

		$this->wp_styles = $wp_styles;

		// Dependancy Injection (^^)
		$this->pless = $pless;

	}

	function enqueue_styles() {

		return;

		// remove main compiles-css
		add_action( 'wp_print_styles', array( &$this, 'dequeue_css' ), 12 );

		// add stylesheet/less to wp_enqueue_styles
		add_filter( 'style_loader_tag', array( &$this, 'fix_less_styletag' ), 5, 2);

		$this->create_file();
		$this->enqueue_less();

	}


	function get_constants() {

		$pless = new PageLinesLess;
		$vars_array = $pless->constants;
		$vars = '';
		foreach($vars_array as $key => $value)
			$vars .= sprintf('@%s:%s%s;%s', $key, " ", $value, "\n");

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




	function create_file() {

		$less = $this->get_constants();

		$core_files = $this->get_core_less();

		foreach( $core_files as $k => $file ) {

			$less .= pl_file_get_contents( trailingslashit( PL_CORE_LESS ) . $file . '.less' );

		}

		$less .= $this->get_sections();

		$this->write_css_file( $less );
	}


	function enqueue_less() {

		wp_enqueue_style( 'editor-less', $this->get_css_dir( 'url' ) . '/editor.less' );
	}

	function fix_less_styletag( $tag, $handle ) {

	    global $wp_styles;

	    $match_pattern = '/\.less$/U';

	    if ( preg_match( $match_pattern, $wp_styles->registered[$handle]->src ) ) {

			$handle = $wp_styles->registered[$handle];

	        $handle = $handle->handle;
	        $media = $handle->args;
	        $href = $handle->src;
	        $rel = isset( $handle->extra['alt'] ) && $handle->extra['alt'] ? 'alternate stylesheet' : 'stylesheet';
	        $title = isset( $handle->extra['title'] ) ? "title='" . esc_attr( $handle->extra['title'] ) . "'" : '';

	        $tag = "<link rel='stylesheet/less' href='$href' type='text/css'>\n";
	    }

	    return $tag;
	}

	function write_css_file( $txt ){

		global $wp_filesystem;

		add_filter('request_filesystem_credentials', '__return_true' );

		$method = '';
		$url = 'themes.php?page=pagelines';

		$folder = $this->get_css_dir( 'path' );
		$file = 'editor.less';

		if( !is_dir( $folder ) )
			wp_mkdir_p( $folder );

		include_once( ABSPATH . 'wp-admin/includes/file.php' );

		if ( is_writable( $folder ) ){
			$creds = request_filesystem_credentials($url, $method, false, false, null);
			if ( ! WP_Filesystem($creds) )
				return false;
		}


		if( is_object( $wp_filesystem ) )
			$wp_filesystem->put_contents( trailingslashit( $folder ) . $file, $txt, FS_CHMOD_FILE);
		else
			return false;

	}

	function get_css_dir( $type = '' ) {

		$folder = wp_upload_dir();

		if( 'path' == $type )
			return trailingslashit( $folder['basedir'] ) . 'pagelines';
		else
			return trailingslashit( $folder['baseurl'] ) . 'pagelines';
	}

	function get_sections() {

		$out = '';
		global $load_sections;
		$available = $load_sections->pagelines_register_sections( true, true );

		$disabled = get_option( 'pagelines_sections_disabled', array() );

		/*
		* Filter out disabled sections
		*/
		foreach( $disabled as $type => $data ){

			if ( isset( $disabled[$type] ) ){

				foreach( $data as $class => $state ){
					unset( $available[$type][ $class ] );
				}

			}

		}

		/*
		* We need to reorder the array so sections css is loaded in the right order.
		* Core, then pagelines-sections, followed by anything else.
		*/
		$sections = array();

		$sections['parent'] = $available['parent'];

		unset( $available['parent'] );

		$sections['child'] = (array) $available['child'];

		unset( $available['child'] );

		if ( is_array( $available ) )
			$sections = array_merge( $sections, $available );

		foreach( $sections as $t ) {
			foreach( $t as $key => $data ) {
				if ( $data['less'] && $data['loadme'] ) {
					if ( is_file( $data['base_dir'] . '/style.less' ) )
						$out .= pl_file_get_contents( $data['base_dir'] . '/style.less' );
					elseif( is_file( $data['base_dir'] . '/color.less' ))
						$out .= pl_file_get_contents( $data['base_dir'] . '/color.less' );
				}
			}
		}

		return apply_filters('pagelines_lesscode', $out);

	}
/*

	function add_constants() {
		printf( "<style id='pl-custom-less' type='text/less'>%s</style>\n",
		$this->get_constants()
		);
	}

	// experimenting with escaping the less variables...
	function escape( $value ) {


	return $value;

		if( preg_match( '#"#', $value ) )
			return '~' . rtrim( $value, '"' ) . '"';
		else
			return $value;
	}

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

		function make_imports() {

		$files = $this->get_core_less();
		$out = '';

		foreach ( $files as $k => $file ) {


			$out .= sprintf( "<style id='pl-custom-less' type='text/less'>%s</style>\n",
				sprintf( '%s@import "%s/%s";', "\n", PL_CORE_LESS_URL, $file )
			);
		}

		echo $out;
	}
*/
} // EditorLess