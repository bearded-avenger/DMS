<?php


class EditorColor{
	
	var $default_bg = '#FFFFFF';
	var $default_text = '#000000';
	
	function __construct( ){
		
		
 		add_filter('pl_settings_array', array(&$this, 'add_settings'));
		add_filter('pless_vars', array(&$this, 'add_less_vars'));
	}
	
	function add_less_vars( $vars ){
		
		return $vars;
	}
	

	
	function add_settings( $settings ){
		
		$settings['color_control'] = array(
			'name' 	=> 'Color Control', 
			'icon'	=> 'icon-tint',
			'pos'	=> 3,
			'opts' 	=> $this->options()
		);
		
		return $settings;
	}
	
	function options(){
		
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
						'key'			=> 'text_primary',
						'type'			=> 'color',			
						'label' 		=> __( 'Main Text Color', 'pagelines' ),
					),
					array(	
						'key'			=> 'headercolor',
						'type'			=> 'color',		
						'label' 		=> __( 'Text Header Color', 'pagelines' ),
						),
					array(		
						'key'			=> 'linkcolor',
						'type'			=> 'color',
						'label' 		=> __( 'Primary Link Color', 'pagelines' ),
					)
				)		
			)


		);
	
			
		return $settings;
		
	}
	
}





