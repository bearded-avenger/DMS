<?php

class Editor_Plugin_Installer {

	function __construct() {
		if( pl_draft_mode() || is_admin() )
			add_action( 'tgmpa_register', array( &$this, 'register_plugins' ) );
	}

	function register_plugins() {

		// we fetch all possible plugins, see which are purchased then pass result to installer class, it does the rest.

		$plugins = $this->get_plugins();


	// Change this to your theme text domain, used for internationalising strings
	$theme_text_domain = 'pagelines';

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       		=> $theme_text_domain,         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_menu_slug' 	=> 'admin.php', 				// Default parent menu slug
		'parent_url_slug' 	=> 'admin.php', 				// Default parent URL slug
		'menu'         		=> 'install-pl-extensions', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Your Addons', $theme_text_domain ),
			'menu_title'                       			=> __( 'Your Addons', $theme_text_domain ),
			'installing'                       			=> __( 'Installing Extension: %s', $theme_text_domain ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', $theme_text_domain ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           			=> __( 'Return to Extensions Installer', $theme_text_domain ),
			'plugin_activated'                 			=> __( 'Extension activated successfully.', $theme_text_domain ),
			'complete' 									=> __( 'All extensions installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

	tgmpa( $plugins, $config );
	}

	function build_array( $plugins ) {

		foreach( $plugins as $slug => $data ) {}


	}

	function get_plugins() {
		global $storeapi;

		$storeapi = new EditorStoreFront;
		if( isset( $_GET['api_returned'] ) )
			$storeapi->del( 'store_mixed' );

		$mixed = $storeapi->get_latest();

		$built = array();
		

		// loop through and see if product is owned.
		foreach( $mixed as $slug => $data ) {

			if( isset( $data['plus_product'] ) && defined( 'VPLUS' ) && VPLUS )
				$data['purchased'] = 'purchased';

			if( ! isset( $data['purchased'] ) )
				unset( $mixed[$slug] );

			if( isset( $data['purchased'] ) && 'purchased' != $data['purchased'] )
				unset( $mixed[$slug] );
		}

		foreach( $mixed as $slug => $data ) {

			$end = 'store';

			// calculate source
			if( 'free' == $data['price'] )
				$end = 'store_free';

			$type = rtrim( $data['type'], 's' );

			$source = sprintf( 'http://www.pagelines.com/api/%s/%s-%s.zip', $end, $type, $slug );

			if( 'section' == $type )
				$source = sprintf( 'http://www.pagelines.com/api/%s/v3/%s.zip', $end, $slug );

			if( 'section' == $type )
				$type = 'plugin';

			// ok must be for real then!
			$built[] = array(
				'name'		=> $data['name'],
				'slug'		=> $slug,
				'pl_type'	=> $type,
				'version'	=> $data['version'],
				'source'	=> $source,
				'required'	=> false,
				'desc'		=> $data['description'],
				'overview'	=> $data['overview'],
				'splash'	=> $data['splash'],
				);

		}
		return $built;
	}
}

