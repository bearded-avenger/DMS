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
					'key'			=> 'revslider_count',
					'type' 			=> 'count_select',
					'count_start'	=> 1,
					'count_number'	=> 10,
					'default'		=> '3',
					'label' 	=> __( 'Number of Slides to Configure', 'pagelines' ),
				),
				array(
					'key'			=> 'revslider_delay',
					'type' 			=> 'text',
					'default'		=> 9000,
					'label' 	=> __( 'Time Per Slide (in Milliseconds)', 'pagelines' ),
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