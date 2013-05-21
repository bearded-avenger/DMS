<?php
/**
 *
 *
 *  PageLines Default/Standard Options Lib
 *
 *
 *  @package PageLines Framework
 *  @since 3.0.0
 *
 *
 */
class EditorSettings {

	public $settings = array( );


	function __construct(){
		$this->settings['basic_settings'] = array(
			'name' 	=> 'Site Images',
			'icon'	=> 'icon-picture',
			'pos'	=> 1,
			'opts' 	=> $this->basic()
		);

		$this->settings['social_media'] = array(
			'name' 	=> 'Social &amp; Local',
			'icon'	=> 'icon-comments',
			'pos'	=> 5,
			'opts' 	=> $this->social()
		);

		$this->settings['advanced'] = array(
			'name' 	=> 'Advanced',
			'icon'	=> 'icon-wrench',
			'pos'	=> 50,
			'opts' 	=> $this->advanced()
		);

		$this->settings['resets'] = array(
			'name' 	=> 'Resets',
			'icon'	=> 'icon-undo',
			'pos'	=> 55,
			'opts' 	=> $this->resets()
		);
	}

	function get_set( ){

		$settings =  apply_filters('pl_settings_array', $this->settings);

		$default = array(
			'icon'	=> 'icon-edit',
			'pos'	=> 100
		);

		foreach($settings as $key => &$info){
			$info = wp_parse_args( $info, $default );
		}
		unset($info);

		uasort($settings, array(&$this, "cmp_by_position") );

		return apply_filters('pl_sorted_settings_array', $settings);
	}

	function cmp_by_position($a, $b) {

		if( isset( $a['pos'] ) && is_int( $a['pos'] ) && isset( $b['pos'] ) && is_int( $b['pos'] ) )
			return $a['pos'] - $b['pos'];
		else
			return 0;
	}

	function basic(){

		$settings = array(

			array(
				'key'			=> 'pagelines_favicon',
				'label'			=> 'Upload Favicon (32px by 32px)',
				'type' 			=> 	'image_upload',
				'imgsize' 			=> 	'16',
				'title' 		=> 	__( 'Favicon Image', 'pagelines' ),
				'help' 			=> 	__( 'Enter the full URL location of your custom <strong>favicon</strong> which is visible in browser favorites and tabs.<br/> <strong>Must be .png or .ico file - 32px by 32px</strong>.', 'pagelines' ),
				'default'		=> PL_EDITOR_URL . '/images/default-favicon.png'
			),


			array(
				'key'			=> 'pl_login_image',
				'type' 			=> 	'image_upload',
				'label'			=> 'Upload Login Image (160px Height)',
				'imgsize' 			=> 	'80',
				'sizemode'		=> 'height',
				'title' 		=> __( 'Login Page Image', 'pagelines' ),
				'default'		=> PL_EDITOR_URL . '/images/default-login-image.png',
				'help'			=> __( 'This image will be used on the login page to your admin. Use an image that is approximately <strong>80px</strong> in height.', 'pagelines' )
			),

			array(
				'key'			=> 'pagelines_touchicon',
				'label'			=> 'Upload Touch Image (144px by 144px)',
				'type' 			=> 	'image_upload',
				'imgsize' 			=> 	'72',
				'title' 		=> __( 'Mobile Touch Image', 'pagelines' ),
				'default'		=> PL_EDITOR_URL . '/images/default-touch-icon.png',
				'help'			=> __( 'Enter the full URL location of your Apple Touch Icon which is visible when your users set your site as a <strong>webclip</strong> in Apple Iphone and Touch Products. It is an image approximately 57px by 57px in either .jpg, .gif or .png format.', 'pagelines' )
			),

			array(
				'type' 	=> 	'multi',
				'title' 		=> __( 'Website Watermark', 'pagelines' ),
				'help' 		=> __( 'The website watermark is a small version of your logo for your footer. Recommended width/height is 90px.', 'pagelines' ),

				'opts'	=> array(
					array(
						'key'			=> 'watermark_image',
						'type' 			=> 'image_upload',
						'label' 		=> 'Watermark Image',
						'default'		=> PL_EDITOR_URL . '/images/default-watermark.png',
						'imgsize'			=> '44'
					),
					array(
						'key'			=> 'watermark_link',
						'type' 			=> 'text',
						'label'			=> 'Watermark Link (Blank for None)',
						'default' 		=> 'http://www.pagelines.com'
					),
					array(
						'key'			=> 'watermark_alt',
						'type' 			=> 'text',
						'label' 		=> 'Watermark Link alt text',
						'default' 		=> 'Build a website with PageLines'
					),
					array(
						'key'			=> 'watermark_hide',
						'type' 			=> 'check',
						'label'		 	=> "Hide Watermark"
					)
				),

			),
		);

		return $settings;

	}


	function social(){



		$settings = array(
			array(
				'key'		=> 'twittername',
				'type' 		=> 'text',
				'label' 	=> __( 'Your Twitter Username', 'pagelines' ),
				'title' 	=> __( 'Twitter Integration', 'pagelines' ),
				'help' 		=> __( 'This places your Twitter feed on the site. Leave blank if you want to hide or not use.', 'pagelines' )
			),
			array(
				'key'		=> 'facebook_name',
				'type' 		=> 'text',
				'label' 	=> __( 'Your Facebook Page Name', 'pagelines' ),
				'title' 	=> __( 'Facebook Page', 'pagelines' ),
				'help' 		=> __( 'Enter the name component of your Facebook page URL. (For example, what comes after the facebook url: www.facebook.com/[name])', 'pagelines' )
			),
			array(
				'key'		=> 'site-hashtag',
				'type' 		=> 'text',
				'label' 	=> __( 'Your Website Hashtag', 'pagelines' ),
				'title' 	=> __( 'Website Hashtag', 'pagelines' ),
				'help'	 	=> __( 'This hashtag will be used in social media (e.g. Twitter) and elsewhere to create feeds.', 'pagelines' )
			),

		);


		return $settings;

	}





	function advanced(){

		$settings = array(
			array(
					'key'		=> 'load_prettify_libs',
					'type'		=> 'check',
					'label'		=> __( 'Enable Code Prettify?', 'pagelines' ),
					'title'		=> __( 'Google Prettify Code', 'pagelines' ),
					'help'		=> __( "Add a class of 'prettyprint' to code or pre tags, or optionally use the [pl_codebox] shortcode. Wrap the codebox shortcode using [pl_raw] if Wordpress inserts line breaks.", 'pagelines' )
			),
			array(
					'key'		=> 'partner_link',
					'type'		=> 'text',
					'label'		=> __( 'Enter Partner Link', 'pagelines' ),
					'title'		=> __( 'PageLines Affiliate/Partner Link', 'pagelines' ),
					'help'		=> __( "If you are a <a target='_blank' href='http://www.pagelines.com'>PageLines Partner</a> enter your link here and the footer link will become a partner or affiliate link.", 'pagelines' )
			),
			array(
					'key'		=> 'special_body_class',
					'type'		=> 'text',
					'label'		=> __( 'Install Class', 'pagelines' ),
					'title'		=> __( 'Current Install Class', 'pagelines' ),
					'help'		=> __( "Use this option to add a class to the &gt;body&lt; element of the website. This can be useful when using the same child theme on several installations or sub domains and can be used to control CSS customizations.", 'pagelines' )
			)
		);
		return $settings;
	}

	function resets(){

		$settings = array(
			array(
					'key'		=> 'reset_global',
					'type'		=> 'action_button',
					'classes'	=> 'btn-important',
					'label'		=> __( '<i class="icon-undo"></i> Reset Global Settings', 'pagelines' ),
					'title'		=> __( 'Reset Global Site Settings', 'pagelines' ),
					'help'		=> __( "Use this button to reset all global settings to their default state. <br/><strong>Note:</strong> Once you've completed this action, you may want to publish these changes to your live site.", 'pagelines' )
			),
			array(
					'key'		=> 'reset_local',
					'type'		=> 'action_button',
					'classes'	=> 'btn-important',
					'label'		=> __( '<i class="icon-undo"></i> Reset Current Page Settings', 'pagelines' ),
					'title'		=> __( 'Reset Current Page Settings', 'pagelines' ),
					'help'		=> __( "Use this button to reset all settings on the current page back to their default state. <br/><strong>Note:</strong> Once you've completed this action, you may want to publish these changes to your live site.", 'pagelines' )
			),
		);
		return $settings;
	}

}





