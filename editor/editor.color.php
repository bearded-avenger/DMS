<?php


class EditorColor{
	
	var $default_base = '#FFFFFF';
	var $default_text = '#000000';
	var $default_link = '#225E9B';
	
	function __construct( ){
		
		
 		add_filter('pl_settings_array', array(&$this, 'add_settings'));
		add_filter('pless_vars', array(&$this, 'add_less_vars'));
	}
	
	function add_less_vars( $vars ){
		
		$base = $this->colors[] = pl_setting('bodybg');
		$text = $this->colors[] = pl_setting('text_primary');
		$link = $this->colors[] = pl_setting('linkcolor');
		$hdrs = $this->colors[] = pl_setting('headercolor');
		
		$vars['pl-base']		= $this->hash( $base );
		$vars['pl-text']		= $this->hash( $text );
		$vars['pl-link']		= $this->hash( $link );
		$vars['pl-header']		= $this->hash( $hdrs );
		
		return $vars;
	}
	
	function hash( $color ){
		
		$clean = str_replace('#', '', $color);

		return sprintf('#%s', $clean);
		
	}

	
	function add_settings( $settings ){
		
		$settings['color_control'] = array(
			'name' 	=> 'Color <span class="spamp">&amp;</span> Style', 
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
				'title' 	=> __( 'Website Base Color', 'pagelines' ),
				'help' 		=> __( 'The "base" color is used as your background and as a basis for calculating contrast values in elements (like hover effects, etc.. ) Use it as your default background color and refine using custom CSS/LESS or a theme.' ), 
				'opts'		=> array(
					array(	
						'key'			=> 'bodybg',
						'type'			=> 'color',			
						'label' 		=> __( 'Base Color', 'pagelines' ),
						'default'		=> $this->default_base
					),
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
						'default'		=> $this->default_text
						
					),
					array(	
						'key'			=> 'headercolor',
						'type'			=> 'color',		
						'label' 		=> __( 'Text Headers Color', 'pagelines' ),
						'default'		=> $this->default_text
						),
					array(		
						'key'			=> 'linkcolor',
						'type'			=> 'color',
						'label' 		=> __( 'Link Color', 'pagelines' ),
						'default'		=> $this->default_link
					)
				)		
			)


		);
	
			
		return $settings;
		
	}
	
}





