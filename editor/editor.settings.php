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
			'name' 	=> 'Basic Settings', 
			'opts' 	=> $this->basic()
		);
		$this->settings['color_control'] = array(
			'name' 	=> 'Color Control', 
			'opts' 	=> $this->color()
		);
		
		$this->settings['typography'] = array(
			'name' 	=> 'Typography', 
			'opts' 	=> $this->type()
		);
		
		$this->settings['social_media'] = array(
			'name' 	=> 'Social Media', 
			'opts' 	=> $this->social()
		);	
		
		$this->settings['advanced'] = array(
			'name' 	=> 'Advanced', 
			'opts' 	=> $this->social()
		);
	}
	
	function get_set( $panel = 'site' ){
		return $this->settings;
	}
	
	function basic(){
		
		$settings = array(
		
			array(
				'key'			=> 'pagelines_favicon',
				'label'			=> 'Upload Favicon (16px by 16px)',
				'type' 			=> 	'image_upload',
				'size' 			=> 	'16',
				'title' 		=> 	__( 'Favicon Image', 'pagelines' ),						
				'help' 			=> 	__( 'Enter the full URL location of your custom <strong>favicon</strong> which is visible in browser favorites and tabs.<br/> <strong>Must be .png or .ico file - 32px by 32px</strong>.', 'pagelines' )
			),		
			
			
			array(
				'key'			=> 'pl_login_image',
				'type' 			=> 	'image_upload',
				'label'			=> 'Upload Icon (80px Height)',
				'size' 			=> 	'60',
				'title' 		=> __( 'Login Page Image', 'pagelines' ),						
				'help'			=> __( 'This image will be used on the login page to your admin. Use an image that is approximately <strong>80px</strong> in height.', 'pagelines' )
			),
			
			array(
				'key'			=> 'pagelines_touchicon',
				'label'			=> 'Upload Icon (57px by 57px)',
				'type' 			=> 	'image_upload',
				'size' 			=> 	'60',
				'title' 		=> __( 'Mobile Touch Image', 'pagelines' ),	
				'help'			=> __( 'Enter the full URL location of your Apple Touch Icon which is visible when your users set your site as a <strong>webclip</strong> in Apple Iphone and Touch Products. It is an image approximately 57px by 57px in either .jpg, .gif or .png format.', 'pagelines' )
			), 
			
			array(
				'type' 	=> 	'multi',
				'title' 		=> __( 'Website Watermark', 'pagelines' ),						
				'help' 		=> __( 'Configure your website watermark (in footer)', 'pagelines' ),
				'opts'	=> array(
					array(
						'key'			=> 'watermark_image',
						'type' 			=> 'image_upload', 
						'label' 		=> 'Watermark Image', 
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
				'key'		=> 'site-hashtag',
				'type' 		=> 'text',
				'label' 	=> __( 'Your Website Hashtag', 'pagelines' ),
				'title' 	=> __( 'Website Hashtag', 'pagelines' ),
				'help'	 	=> __( 'This hashtag will be used in social media (e.g. Twitter) and elsewhere to create feeds.', 'pagelines' )
			),


		);
	
			
		return $settings;
		
	}
	
	function type(){
		
		$settings = array(
			
			array(
				'key'		=> 'font_headers', 
				'type' 		=> 'type',
				'label' 	=> __( 'Text Header Font', 'pagelines' ),
				'title' 	=> __( 'Text Header Font', 'pagelines' ),
				
			), 
			array(
				'key'		=> 'font_primary', 
				'type' 		=> 'type',
				'label' 	=> __( 'Primary Site Font', 'pagelines' ),
				'title' 	=> __( 'Primary Site Font', 'pagelines' ),
				
			),
			array(
				'key'		=> 'font_secondary', 
				'type' 		=> 'type',
				'label' 	=> __( 'Secondary Site Font', 'pagelines' ),
				'title' 	=> __( 'Secondary Site Font', 'pagelines' ),
				
			)
			


		);
	
			
		return $settings;
		
	}
	
	function color(){
		
		$settings = array(
			array(
				'key'		=> 'canvas_colors', 
				'type' 		=> 'multi',
				'label' 	=> __( 'Page Background Colors', 'pagelines' ),
				'title' 	=> __( 'Page Background Colors', 'pagelines' ),
				'help' 		=> __( 'Configure the basic background colors for your site', 'pagelines' ), 
				'opts'		=> array(
					array(	
						'key'			=> 'bodybg',
						'type'			=> 'color',			
						'label' 		=> __( 'Body Background', 'pagelines' ),
					),
					array(	
						'key'			=> 'pagebg',
						'type'			=> 'color',		
						'label' 		=> __( 'Page Background (Optional)', 'pagelines' ),
						),
					array(		
						'key'			=> 'contentbg',
						'type'			=> 'color',
						'label' 		=> __( 'Content Background (Optional)', 'pagelines' ),
					)
				)		
			),
			array(
				'key'		=> 'text_colors', 
				'type' 		=> 'multi',
				'label' 	=> __( 'Site Text Colors', 'pagelines' ),
				'title' 	=> __( 'Site Text Colors', 'pagelines' ),
				'help' 		=> __( 'Configure the basic text colors for your site', 'pagelines' ), 
				'opts'		=> array(
					array(	
						'key'			=> 'bodybg',
						'type'			=> 'color',			
						'label' 		=> __( 'Main Text Color', 'pagelines' ),
					),
					array(	
						'key'			=> 'pagebg',
						'type'			=> 'color',		
						'label' 		=> __( 'Text Header Color', 'pagelines' ),
						),
					array(		
						'key'			=> 'contentbg',
						'type'			=> 'color',
						'label' 		=> __( 'Primary Link Color', 'pagelines' ),
					)
				)		
			)


		);
	
			
		return $settings;
		
	}

}