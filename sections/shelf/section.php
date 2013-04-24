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
	<div class="shelf-wrap">
		<div class="shelf-head">
			<h1>PageLines Shelf</h1>
			<div class=="subhead">A bunch of text</div>
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