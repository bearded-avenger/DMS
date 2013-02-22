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
		
		$this->settings['layout'] = array(
			'name' 	=> 'Layout Handling', 
			'icon' 	=> 'icon-fullscreen', 
			'pos'	=> 2,
			'opts' 	=> $this->layout()
		);

				
		$this->settings['social_media'] = array(
			'name' 	=> 'Social Media', 
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
				
		usort($settings, array(&$this, "cmp_by_position") );

		return $settings;
	}
	
	function cmp_by_position($a, $b) {
	  return $a["pos"] - $b["pos"];
	}
	
	function basic(){
		
		$settings = array(
		
			array(
				'key'			=> 'pagelines_favicon',
				'label'			=> 'Upload Favicon (32px by 32px)',
				'type' 			=> 	'image_upload',
				'size' 			=> 	'16',
				'title' 		=> 	__( 'Favicon Image', 'pagelines' ),						
				'help' 			=> 	__( 'Enter the full URL location of your custom <strong>favicon</strong> which is visible in browser favorites and tabs.<br/> <strong>Must be .png or .ico file - 32px by 32px</strong>.', 'pagelines' ),
				'default'		=> PL_EDITOR_URL . '/images/default-favicon.png'
			),		
			
			
			array(
				'key'			=> 'pl_login_image',
				'type' 			=> 	'image_upload',
				'label'			=> 'Upload Icon (80px Height)',
				'size' 			=> 	'80',
				'title' 		=> __( 'Login Page Image', 'pagelines' ),
				'default'		=> PL_EDITOR_URL . '/images/default-login-image.png',						
				'help'			=> __( 'This image will be used on the login page to your admin. Use an image that is approximately <strong>80px</strong> in height.', 'pagelines' )
			),
			
			array(
				'key'			=> 'pagelines_touchicon',
				'label'			=> 'Upload Icon (144px by 144px)',
				'type' 			=> 	'image_upload',
				'size' 			=> 	'72',
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
						'size'			=> '44'
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
	
	function layout(){
		
		
		
		$settings = array(
			array(
				'key'		=> 'disable_responsive',
				'type' 		=> 'check',
				'label' 	=> __( 'Disable Responsive Layout?', 'pagelines' ),
				'title' 	=> __( 'Disable Responsive Layout', 'pagelines' ),
				'help'	 	=> __( 'Check this option if you want to disable responsive/mobile layout on your website', 'pagelines' )
			),
			array(
				'key'		=> 'layout_mode',
				'type' 		=> 'select',
				'label' 	=> __( 'Select Layout Mode', 'pagelines' ),
				'title' 	=> __( 'Layout Mode', 'pagelines' ),
				'opts' 		=> array(
					'pixel' 	=> array('name' => 'Pixel Width Based Layout'),
					'percent' 	=> array('name' => 'Percentage Width Based Layout')
				),
				'default'	=> 'pixel',
				'help'	 	=> __( '', 'pagelines' )
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

}





