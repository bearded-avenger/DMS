<?php


class EditorColor{

	var $default_base = '#FFFFFF';
	var $default_text = '#000000';
	var $default_link = '#225E9B';
	var $background = '';

	function __construct( ){

		$this->background = pl_setting('page_background_image');

 		add_filter('pl_settings_array', array(&$this, 'add_settings'));
		add_filter('pless_vars', array(&$this, 'add_less_vars'));
		if( $this->background );
			add_action( 'wp_enqueue_scripts', array(&$this, 'background_fit'));
	}

	function add_less_vars( $vars ){

		$base = ( pl_setting('bodybg') ) ? pl_setting('bodybg') : $this->default_base;
		$text = ( pl_setting('text_primary') ) ? pl_setting('text_primary') : $this->default_text;
		$link = ( pl_setting('linkcolor') ) ? pl_setting('linkcolor') : $this->default_link;
		$hdrs = ( pl_setting('headercolor') ) ? pl_setting('headercolor') : $this->default_text;

		$this->base = $vars['pl-base'] 	= $this->hash( $base );
		$vars['pl-text']				= $this->hash( $text );
		$vars['pl-link']				= $this->hash( $link );
		$vars['pl-header']				= $this->hash( $hdrs );
		$vars['pl-background']			= $this->background();
		return $vars;
	}

	function background(){

		$fit = pl_setting('supersize_bg');
		$image = $this->background;

		if($image && $fit){
			$background = $this->base;
		}
		elseif($image && !$fit)
			$background = sprintf('%s url("%s") no-repeat', $this->base, $image);
		else
			$background = $this->base;

		return $background;
	}

	function background_fit(){
		wp_enqueue_script( 'pagelines-supersize' );
		add_action('pl_scripts_on_ready', array(&$this, 'run_background_fit'), 20);
	}

	function run_background_fit(){

		$image = pl_setting('page_background_image');
		?>
		jQuery.supersized({ slides: [{ image : '<?php echo $image; ?>' }]})
<?php
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
			),
			array(
				'key'		=> 'background_style',
				'type' 		=> 'multi',

				'title' 	=> __( 'Background Image', 'pagelines' ),
				'help' 		=> __( '', 'pagelines' ),
				'opts'		=> array(
					array(
						'key'			=> 'page_background_image',
						'type'			=> 'image_upload',
						'label' 		=> __( 'Page Background Image', 'pagelines' ),
						'default'		=> ''

					),
					array(
						'key'			=> 'supersize_bg',
						'type'			=> 'check',
						'label' 		=> __( 'Fit image to page?', 'pagelines' ),
						'default'		=> true
						),
				)
			),

		);


		return $settings;

	}

}





