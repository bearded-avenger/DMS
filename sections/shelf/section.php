<?php
/*
	Section: Shelf
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: 
	Class Name: PageLinesShelf
	Filter: full-width
*/


class PageLinesShelf extends PageLinesSection {

	var $default_limit = 3;

	function section_opts(){

		$options = array();

		$options[] = array(

			'title' => __( 'Slider Configuration', 'pagelines' ),
			'type'	=> 'multi',
			'opts'	=> array(
				array(
					'key'			=> 'shelf_html',
					'type' 			=> 'textarea',
					'default'		=> '3',
					'label' 	=> __( 'Shelf HTML', 'pagelines' ),
				),
				array(
					'key'			=> 'shelf_background',
					'type' 			=> 'select',
					'opts'	=> array(
						'base'		=> array('name'=> 'Base Background (none - default)'),
						'contrast'	=> array('name'=> 'Contast Color'),
						'black'		=> array('name'=> 'Black'),
					),
					'label' 	=> __( 'Use Standard Background?', 'pagelines' ),
				)
			)

		);


		return $options;
	}

	

   function section_template( ) {


	?>
	<div class="shelf-wrap pl-viewport">
		
		<div class="pl-caption lft"
			 data-x="560"
			 data-y="130"
			 data-speed="300"
			 data-start="500"
			 data-easing="easeOutExpo"  >

				<h2><span class="slider-text">
				Welcome to Designer.<br/>
			 	A Drag <span class="spamp">&amp;</span> Drop Platform <br/> for Amazing Websites.
				</span></h2>
			 	<a href="#" class="slider-btn">Read More</a>

		</div>
		
		
		<div class="pl-content nested-section-area">
			<div class="pl-inner area-region pl-sortable-area">
				<?php render_nested_sections( $this->meta['content'] ); ?>
			</div>
		</div>
		
	</div>

		<?php
	}


}