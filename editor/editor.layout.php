<?php

class EditorLayout {


	function __construct(  ){
		
		add_filter('pl_settings_array', array(&$this, 'add_settings'));
		add_filter('pless_vars', array(&$this, 'add_less_vars'));
		add_filter('pagelines_body_classes', array(&$this, 'add_body_classes'));

	}
	
	function add_body_classes($classes){
		
		$classes[] = ( pl_setting( 'layout_display_mode' ) ) ? pl_setting( 'layout_display_mode' ) : 'display-full';
		
		return $classes;
		
	}
	
	function add_settings( $settings ){

		$settings['layout'] = array(
			'name' 	=> 'Layout Handling',
			'icon' 	=> 'icon-fullscreen',
			'pos'	=> 2,
			'opts' 	=> $this->options()
		);

		return $settings;
	}
	
	
	function options(){



		$settings = array(
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
			array(
				'key'		=> 'layout_display_mode',
				'type' 		=> 'select',
				'label' 	=> __( 'Select Layout Display', 'pagelines' ),
				'title' 	=> __( 'Display Mode', 'pagelines' ),
				'opts' 		=> array(
					'display-full' 		=> array('name' => 'Full Width Display'),
					'display-boxed' 	=> array('name' => 'Boxed Display')
				),
				'default'	=> 'full',
				'help'	 	=> __( '', 'pagelines' )
			),


		);


		return $settings;

	}


	function add_less_vars( $less_vars ){

		// if pixel mode assign pixel option

		if( pl_setting( 'layout_mode' ) == 'percent' )
			$value = (pl_setting( 'content_width_percent' )) ? pl_setting( 'content_width_percent' ) : '80%';
		else
			$value = (pl_setting( 'content_width_px' )) ? pl_setting( 'content_width_px' ) : '1100px';

		// if percent mode assign percent option

		$less_vars['plContentWidth'] = $value;

		return $less_vars;

	}

	function get_layout_mode(){

		$value = (pl_setting( 'layout_mode' )) ? pl_setting( 'layout_mode' ) : 'pixel';

		return $value;

	}


}