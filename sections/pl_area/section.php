<?php
/*
	Section: Section Area
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a full width area with a nested content width region for placing sections and columns.
	Class Name: PLSectionArea
	Filter: full-width
	Loading: active
*/


class PLSectionArea extends PageLinesSection {


	function section_persistent(){
	
		add_filter('pl_layout_settings', array(&$this, 'add_global_options'));
	}
	
	function add_global_options( $settings ){
		$settings[] = array(

			'key'			=> 'layout_areas',
			'type' 			=> 'textarea',
			'label' 	=> __( 'Section Areas', 'pagelines' ),
			'opts'	=> array(
				array(
					'key'		=> 'section_area_default_css',
					'type' 		=> 'textarea',
					'label' 	=> __( 'Section Area LESS/CSS', 'pagelines' ),
					'suffix'		=> 'px',
					'help'	 	=> __( 'If sections are added to full width areas, the area will be given this default padding.', 'pagelines' )
				),
			),
		);
		
		return $settings;
	}
	
	function section_opts(){

		$options = array();

		$options[] = array(
			'key'			=> 'pl_area_css',
			'type' 			=> 'multi',
			'label' 	=> __( 'Section Area LESS/CSS', 'pagelines' ),
			'opts'	=> array(
				array(
					'key'			=> 'pl_area_css',
					'type' 			=> 'textarea',
					'suffix'		=> 'px',
					'label' 	=> __( 'Section Area LESS/CSS', 'pagelines' ),
				),
			),
			

		);

		$options[] = array(

			'key'			=> 'pl_area_bg',
			'type' 			=> 'select',
			'opts'	=> array(
				'pl-trans'		=> array('name'=> 'transparent Background and Default Text Color'),
				'pl-contrast'	=> array('name'=> 'Contrast Color and Default Text Color'),
				'pl-black'		=> array('name'=> 'Black Background &amp; White Text'),
				'pl-grey'		=> array('name'=> 'Dark Grey Background &amp; White Text'),
				'pl-dark-img'	=> array('name'=> 'Image-Dark: Embossed Light Text.'),
				'pl-light-img'	=> array('name'=> 'Image-Light: Embossed Dark Text.'),
				'pl-base'		=> array('name'=> 'Base Background and Default Text Color'),
			),
			'label' 	=> __( 'Area Theme', 'pagelines' ),

		);
		
		$options[] = array(

			'key'			=> 'pl_area_styling',
			'type' 			=> 'multi',
			'label' 	=> __( 'Area Styling', 'pagelines' ),
			'opts'	=> array(
				array(

					'key'			=> 'pl_area_class',
					'type' 			=> 'text',
					'label' 	=> __( 'Styling Classes', 'pagelines' ),
					'help'		=> __( 'Separate with a space " "', 'pagelines' ),
				),
				array(
					'key'			=> 'pl_area_height',
					'type' 			=> 'text',
					'label' 	=> __( 'Area Minimum Height (px)', 'pagelines' ),
				)
			),
			

		);
		
		$options[] = array(

			'key'			=> 'pl_area_bg',
			'type' 			=> 'multi',
			'label' 	=> __( 'Area Background', 'pagelines' ),
			'opts'	=> array(
				array(

					'key'			=> 'pl_area_image',
					'type' 			=> 'image_upload',
					'sizelimit'		=> 800000,
					'label' 	=> __( 'Background Image', 'pagelines' ),
				),
				array(
					'key'			=> 'pl_area_parallax',
					'type' 			=> 'check',
					'label' 	=> __( 'Enable Background Parallax', 'pagelines' ),
				)
			),
			

		);
		
		
		
		


		return $options;
	}
	
	function before_section_template( $location = '' ) {

		$this->wrapper_classes['background'] = $this->opt('pl_area_bg');
		$this->wrapper_classes['user_classes'] = $this->opt('pl_area_class');

	}

	

	function section_template( ) {
		
		$section_output = (!$this->active_loading) ? render_nested_sections( $this->meta['content'] ) : false;
		
		$style = '';
		$inner_style = '';
		
		$inner_style .= ($this->opt('pl_area_height')) ? sprintf('min-height: %spx;', $this->opt('pl_area_height')) : '';
		
		$style .= ($this->opt('pl_area_image')) ? sprintf('background-image: url(%s);', $this->opt('pl_area_image')) : '';
		
		$classes = ($this->opt('pl_area_parallax')) ? 'pl-parallax' : '';
		
		// If there is no output, there should be no padding or else the empty area will have height.
		if( $section_output ){
						
			$default_css = (pl_setting('section_area_default_css'))	? pl_setting('section_area_default_css') : 'padding:20px;';		
					
			$customcss = ($this->opt('pl_area_css')) ? $this->opt('pl_area_css') : $default_css; 
			
			$customcss = ( strpos($customcss) ) ? $customcss : $customcss;
			
			$style .= sprintf('%s', $customcss);
			
			
			
		
			$content_class = ( $padding != '0px	' ) ? 'nested-section-area' : '';
			
			$buffer = sprintf('<div class="pl-sortable pl-sortable-buffer span12 offset0"></div>');
			
			$section_output = $buffer . $section_output . $buffer;
			
		} else {
			
			$pad_css = ''; 
			$content_class = '';
			
		}
		
	?>
	<div class="pl-area-wrap <?php echo $classes;?>" style="<?php echo $style;?>">
		<div class="pl-content <?php echo $content_class;?>">
			<div class="pl-inner area-region pl-sortable-area" style="<?php echo $inner_style;?>">
				<?php  echo $section_output; ?>
			</div>
		</div>
	</div>
	<?php
	}


}