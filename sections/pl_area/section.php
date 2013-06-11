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


	function section_opts(){

		$options = array();

		$options[] = array(

			'key'			=> 'pl_area_pad_selects',
			'type' 			=> 'multi',
			'label' 	=> __( 'Set Area Padding', 'pagelines' ),
			'opts'	=> array(
				array(
					'key'			=> 'pl_area_pad',
					'type' 			=> 'count_select_same',
					'count_start'	=> 0,
					'count_number'	=> 200,
					'suffix'		=> 'px',
					'label' 	=> __( 'Area Padding (px)', 'pagelines' ),
				),
				array(
					'key'			=> 'pl_area_pad_bottom',
					'type' 			=> 'count_select_same',
					'count_start'	=> 0,
					'count_number'	=> 200,
					'suffix'		=> 'px',
					'label' 	=> __( 'Area Padding Bottom (if different)', 'pagelines' ),
				)
			),
			

		);

		$options[] = array(

			'key'			=> 'pl_area_bg',
			'type' 			=> 'select',
			'opts'	=> array(
				'pl-trans'		=> array('name'=> 'Transparent Background and Default Text Color'),
				'pl-contrast'	=> array('name'=> 'Contast Color and Default Text Color'),
				'pl-black'		=> array('name'=> 'Black Background &amp; White Text'),
				'pl-grey'		=> array('name'=> 'Dark Grey Background &amp; White Text'),
				'pl-dark-img'	=> array('name'=> 'Image-Dark: Embossed Light Text.'),
				'pl-light-img'	=> array('name'=> 'Image-Light: Embossed Dark Text.'),
				'pl-base'		=> array('name'=> 'Base Background and Default Text Color'),
			),
			'label' 	=> __( 'Area Theme', 'pagelines' ),

		);
		
		$options[] = array(

			'key'			=> 'pl_area_image',
			'type' 			=> 'image_upload',
			'sizelimit'		=> 800000,
			'label' 	=> __( 'Background Image', 'pagelines' ),
		);
		
		$options[] = array(

			'key'			=> 'pl_area_class',
			'type' 			=> 'text',
			'label' 	=> __( 'Styling Classes', 'pagelines' ),
			'help'		=> __( 'Separate with a space " "', 'pagelines' ),
		);
		
		


		return $options;
	}
	
	function before_section_template( $location = '' ) {

		$this->wrapper_classes['background'] = $this->opt('pl_area_bg');

	}

	

	function section_template( ) {
		
		$section_output = render_nested_sections( $this->meta['content'] );
		
		$style = '';
		
		if( $section_output ){
						
			$padding = ($this->opt('pl_area_pad')) ? $this->opt('pl_area_pad') : '20px'; 
			
			$padding = ( strpos($padding, 'px') ) ? $padding : $padding.'px';
			
			$padding_bottom = ($this->opt('pl_area_pad_bottom')) ? $this->opt('pl_area_pad_bottom') : $padding; 
			
			$style .= sprintf('padding-top: %s; padding-bottom: %s;', $padding, $padding_bottom);
			
			
			if($this->opt('pl_area_image'))
				$style .= sprintf('background-image: url(%s);', $this->opt('pl_area_image')); 
		
			$content_class = ( $padding != '0px	' ) ? 'nested-section-area' : '';
			
			$buffer = sprintf('<div class="pl-sortable pl-sortable-buffer span12 offset0"></div>');
			
			$section_output = $buffer . $section_output . $buffer;
			
		} else {
			
			$pad_css = ''; 
			$content_class = '';
			
		}
		

		$class = $this->opt('pl_area_class');
	
	?>
	<div class="pl-area-wrap <?php echo $class;?>" style="<?php echo $style;?>">
		<div class="pl-content <?php echo $content_class;?>">
			<div class="pl-inner area-region pl-sortable-area">
				<?php  echo $section_output; ?>
			</div>
		</div>
	</div>
	<?php
	}


}