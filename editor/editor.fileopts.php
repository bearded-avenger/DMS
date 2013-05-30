<?php
/**
 *
 *
 *  PageLines dat file handling Class
 *
 *
 */
class EditorFileOpts {

	var $configfile = 'theme-config.dat';

	var $options = array(
		PL_SETTINGS,
		'pl-template-map',
		'pl-user-templates'
		);
		
	function __construct() {

		// setup some vars...

		$this->dir = get_stylesheet_directory();
	}


	function dump() {

		add_filter( 'request_filesystem_credentials', '__return_true' );

		include_once( ABSPATH . 'wp-admin/includes/file.php' );
		
		if ( is_writable( $folder ) ){
			
			$creds = request_filesystem_credentials( $url, $method, false, false, null );
			if ( ! WP_Filesystem($creds) )
				return false;
		}
		
		global $wp_filesystem;
		
		if( is_object( $wp_filesystem ) )
			$wp_filesystem->put_contents( trailingslashit( $this->dir ) . $this->configfile, $this->getopts(), FS_CHMOD_FILE);
		else
			return false;
	}

	function getopts() {

		foreach( $this->options as $opt ) {
			
			$def = ( PL_SETTINGS == $opt ) ? array( 'draft' => array(), 'live' => array() ) : array();
			
			$option[$opt] = get_option( $opt, $def );
			
		}
		return serialize( $option );
	}

	function read_file() {

		$file = trailingslashit( $this->dir ) . $this->configfile;
		
		if( ! is_file( $file ) )
			return false;
			
		$data = pl_file_get_contents( $file );
		
		return unserialize( $data );
		
	}
	
	function file_exists() {
		
		if( is_file( trailingslashit( $this->dir ) . $this->configfile ) )
			return true;
			
	}
}