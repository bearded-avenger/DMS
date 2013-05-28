<?php
/*
	Section: Section Area
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a full width area with a nested content width region for placing sections and columns.
	Class Name: PLSectionArea
	Filter: full-width
*/


class PLSectionArea extends PageLinesSection {


	function section_opts(){

		$options = array();

		$options[] = array(

			'key'			=> 'pl_area_bg',
			'type' 			=> 'select',
			'opts'	=> array(
				'pl-trans'		=> array('name'=> 'Transparent (none - default)'),
				'pl-contrast'	=> array('name'=> 'Contast Color'),
				'pl-black'		=> array('name'=> 'Black'),
				'pl-grey'		=> array('name'=> 'Dark Grey'),
				'pl-base'		=> array('name'=> 'Base Background Color'),
			),
			'label' 	=> __( 'Area Background', 'pagelines' ),

		);
		
		$options[] = array(

			'key'			=> 'pl_area_class',
			'type' 			=> 'text',
			'label' 	=> __( 'Styling Classes', 'pagelines' ),
			'help'		=> __( 'Separate with a space " "', 'pagelines' ),
		);
		
		$options[] = array(

			'key'			=> 'pl_area_pad',
			'type' 			=> 'count_select',
			'count_start'	=> 0,
			'count_number'	=> 200,
			'default'		=> 0,
			'label' 	=> __( 'Area Padding (px)', 'pagelines' ),

		);


		return $options;
	}
	
	function before_section_template( $location = '' ) {

		$this->wrapper_classes['background'] = $this->opt('pl_area_bg');

	}

	

	function section_template( ) {
		
		$section_output = render_nested_sections( $this->meta['content'] );
		
		if( $section_output ){
			
			$padding = ($this->opt('pl_area_pad')) ? $this->opt('pl_area_pad') : '20'; 

			$pad_css = sprintf('padding-top: %1$spx; padding-bottom: %1$spx', $padding);

			$content_class = ($padding != '0') ? 'nested-section-area' : '';
			
			$buffer = sprintf('<div class="pl-sortable pl-sortable-buffer span12 offset0"></div>');
			
			$section_output = $buffer . $section_output . $buffer;
			
		} else {
			
			$pad_css = ''; 
			$content_class = '';
			
		}
		

		$class = $this->opt('pl_area_class');
	
	?>
	<div class="pl-area-wrap <?php echo $class;?>" style="<?php echo $pad_css;?>">
		<div class="pl-content <?php echo $content_class;?>">
			<div class="pl-inner area-region pl-sortable-area">
				<?php  echo $section_output; ?>
			</div>
		</div>
	</div>
	<?php
	}


}