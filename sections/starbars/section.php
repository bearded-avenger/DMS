<?php
/*
	Section: StarBars
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Awesome animated stat bars that animate as the user scrolls. Use them to show stats or other information.
	Class Name: PageLinesStarBars
	Cloning: true
	Workswith: main, templates, sidebar_wrap
	Filter: post-format
*/

class PageLinesStarBars extends PageLinesSection {

	var $default_limit = 3;

	function section_styles(){

		wp_enqueue_script( 'pagelines-viewport', $this->base_url . '/script.viewport.js', array( 'jquery' ), PL_CORE_VERSION, true );

		wp_enqueue_script( 'starbar', $this->base_url.'/starbar.js', array( 'jquery-effects-core' ), PL_CORE_VERSION, true );

	}

	function section_opts(){

		$options = array();

		$options[] = array(

			'title' => __( 'StarBar Configuration', 'pagelines' ),
			'type'	=> 'multi',
			'opts'	=> array(
				array(
					'key'			=> 'starbar_count',
					'type' 			=> 'count_select',
					'count_start'	=> 1,
					'count_number'	=> 12,
					'default'		=> 4,
					'label' 	=> __( 'Number of StarBars to Configure', 'pagelines' ),
				),
				array(
					'key'			=> 'starbar_total',
					'type' 			=> 'text',
					'default'		=> 100,
					'label' 		=> __( 'Starbar Total Count (Number)', 'pagelines' ),
					'help' 			=> __( 'This number will be used to calculate the percent of the bar filled. The StarBar values will be shown as a percentage of this value. Default is 100.', 'pagelines' ),
				),

				array(
					'key'			=> 'starbar_modifier',
					'type' 			=> 'text',
					'default'		=> '%',
					'label' 		=> __( 'Starbar Modifier (Text Added to Stats)', 'pagelines' ),
					'help' 			=> __( 'This will be added to the stat number.', 'pagelines' ),
				),
				array(
					'key'			=> 'starbar_format',
					'type' 			=> 'select',
					'opts'		=> array(
						'append'		=> array( 'name' => 'Append Modifier (Default)' ),
						'prepend'	 	=> array( 'name' => 'Prepend Modifier' ),
					),
					'default'		=> 'append',
					'label' 	=> __( 'Starbar Format', 'pagelines' ),
				),
				array(
					'key'			=> 'starbar_container_title',
					'type' 			=> 'text',
					'default'		=> 'StarBar',
					'label' 	=> __( 'StarBar Title (Optional)', 'pagelines' ),
				),
			)

		);

		$slides = ($this->opt('starbar_count')) ? $this->opt('starbar_count') : $this->default_limit;

		for($i = 1; $i <= $slides; $i++){

			$opts = array(

				'starbar_descriptor_'.$i 	=> array(
					'label'		=> __( 'Descriptor', 'pagelines' ),
					'type'		=> 'text'
				),
				'starbar_value_'.$i 	=> array(
					'label'	=> __( 'Value', 'pagelines' ),
					'type'	=> 'text',
					'help'	=> __( 'Shown as a percentage of the StarBar total in the config.', 'pagelines' ),
				),
			);


			$options[] = array(
				'title' 	=> __( '<i class="icon-star"></i> StarBar #', 'pagelines' ) . $i,
				'type' 		=> 'multi',
				'opts' 		=> $opts,

			);

		}

		return $options;
	}

	function section_template(  ) {

		$starbar_title = $this->opt('starbar_container_title');
		$starbar_mod = $this->opt('starbar_modifier');
		$starbar_total = (int) $this->opt('starbar_total');
		$starbar_count = $this->opt('starbar_count');
		$starbar_format = $this->opt('starbar_format');

		$starbar_title = ($starbar_title) ? sprintf('<h2>%s</h2>', $starbar_title) : '';

		$format = ($starbar_format) ? $starbar_format : 'append';

		$mod = ($starbar_mod) ? $starbar_mod : '%';
		
		$total = ($starbar_total) ? $starbar_total : 100;
		
		$total = apply_filters('starbars_total', $total);
		
		$output = '';
		for($i = 1; $i <= $starbar_count; $i++){

			$descriptor = $this->opt('starbar_descriptor_'.$i);
			$value = (int) $this->opt('starbar_value_'.$i);
			
			$value = apply_filters('starbar_value', $value, $i, $descriptor, $this); 
			

			$desc = ($descriptor) ? sprintf('<p>%s</p>', $descriptor) : '';

			if(!$value)
				continue;

			if(is_int($value) && is_int($total))
				$width = floor( $value / $total * 100 ) ;
			else
				$width = 0;

			$value = ($width > 100) ? $total : $value;
			$width = ($width > 100) ? 100 : $width;


			$tag = ( $format == 'append' ) ? $value . $mod : $mod . $value;

			$total_tag = ( $format == 'append' ) ? $starbar_total . $mod : $mod . $starbar_total;

		//	$draw_total_tag = ($i == 1) ? sprintf('<strong>%s</strong>', $total_tag) : '';

			$output .= sprintf(
				'<li>%s<div class="bar-wrap"><span class="the-bar" data-width="%s"><strong>%s</strong></span></div></li>',
				$desc,
				$width.'%',
				$tag
			);
		}


		if($output == ''){
			$this->do_defaults();
		} else
			printf('<div class="starbars-wrap">%s<ul class="starbars">%s</ul></div>', $starbar_title, $output);



	}

	function do_defaults(){

		?>
		<div class="starbars-wrap">
			<h2>StarBar</h2>
			<ul class="starbars">
				
				<li>
					<p>Ninja Ability</p>
					<div class="bar-wrap">
						<span class="the-bar" data-width="70%"><strong>70%</strong></span>
					</div>
				</li>
				<li>
					<p>Tree Climbing Skills</p>
					<div class="bar-wrap">
						<span class="the-bar" data-width="90%"><strong>90%</strong></span>
					</div>
				</li>
			</ul>
		</div>
		<?php
	}

	/**
	 *
	 * Page-by-page options for PostPins
	 *
	 */
	function section_optionator( $settings ){
		$settings = wp_parse_args( $settings, $this->optionator_default );

			$array = array();

			$array['starbar_count'] = array(
				'type' 			=> 'count_select',
				'count_start'	=> 1,
				'count_number'	=> 12,
				'default'		=> '3',
				'inputlabel' 	=> __( 'Number of Slides to Configure', 'pagelines' ),
				'title' 		=> __( 'Number of Slides', 'pagelines' ),
				'shortexp' 		=> __( 'Enter the number of QuickSlider slides. <strong>Default is 3</strong>', 'pagelines' ),
				'exp' 			=> __( "This number will be used to generate slides and option setup.", 'pagelines' ),

			);

			$array['quick_transition'] = array(
				'type' 			=> 'select',
				'selectvalues' => array(
					'fade' 			=> array('name' => __( 'Use Fading Transition', 'pagelines' ) ),
					'slide_h' 		=> array('name' => __( 'Use Slide/Horizontal Transition', 'pagelines' ) ),
				),
				'inputlabel' 	=> __( 'Select Transition Type', 'pagelines' ),
				'title' 		=> __( 'Slider Transition Type', 'pagelines' ),
				'shortexp' 		=> __( 'Configure the way slides transfer to one another.', 'pagelines' ),
				'exp' 			=> __( "", 'pagelines' ),

			);

			$array['quick_nav'] = array(
				'type' 			=> 'select',
				'selectvalues' => array(
					'both' 			=> array('name' => __( 'Use Both Arrow and Slide Control Navigation', 'pagelines' ) ),
					'none'			=> array('name' => __( 'No Navigation', 'pagelines' ) ),
					'control_only'	=> array('name' => __( 'Slide Controls Only', 'pagelines' ) ),
					'arrow_only'	=> array('name' => __( 'Arrow Navigation Only', 'pagelines' ) ),
				),
				'inputlabel' 	=> __( 'Slider Navigation', 'pagelines' ),
				'title' 		=> __( 'Slider Navigation mode', 'pagelines' ),
				'shortexp' 		=> __( 'Configure the navigation for this slider.', 'pagelines' ),
				'exp' 			=> __( "", 'pagelines' ),

			);

			$array['quick_slideshow'] = array(
				'type' 			=> 'check',

				'inputlabel' 	=> __( 'Animate Slideshow Automatically?', 'pagelines' ),
				'title' 		=> __( 'Automatic Slideshow?', 'pagelines' ),
				'shortexp' 		=> __( 'Autoplay the slides, transitioning every 7 seconds.', 'pagelines' ),
				'exp' 			=> __( "", 'pagelines' ),

			);

			global $post_ID;

			$oset = array(
				'post_id' => $post_ID,
				'clone_id' => $settings['clone_id'],
				'type' => $settings['type']
			);

			$slides = ($this->opt('starbar_count', $oset)) ? $this->opt('starbar_count', $oset) : $this->default_limit;

			for($i = 1; $i <= $slides; $i++){


				$array['quick_slide_'.$i] = array(
					'type' 			=> 'multi_option',
					'selectvalues' => array(
						'quick_image_'.$i 	=> array(
							'inputlabel' 	=> __( 'Slide Image', 'pagelines' ),
							'type'			=> 'image_upload'
						),
						'quick_img_alt_'.$i 	=> array(
							'inputlabel' 	=> __( 'Image Alt', 'pagelines' ),
							'type'			=> 'text'
						),
						'quick_text_'.$i 	=> array(
							'inputlabel'	=> __( 'Slide Text', 'pagelines' ),
							'type'			=> 'textarea'
						),
						'quick_link_'.$i 	=> array(
							'inputlabel'	=> __( 'Slide Link URL', 'pagelines' ),
							'type'			=> 'text'
						),
						'quick_text_location_'.$i 	=> array(
							'inputlabel'	=> __( 'Slide Text Location', 'pagelines' ),
							'type'			=> 'select',
							'selectvalues'	=> array(
								'right_bottom'	=> array('name'=> 'Right/Bottom'),
								'right_top'		=> array('name'=> 'Right/Top'),
								'left_bottom'	=> array('name'=> 'Left/Bottom'),
								'left_top'		=> array('name'=> 'Left/Top')
							)
						),
					),
					'title' 		=> __( 'QuickSlider Slide ', 'pagelines' ) . $i,
					'shortexp' 		=> __( 'Setup options for slide number ', 'pagelines' ) . $i,
					'exp'			=> __( 'For best results all images in the slider should have the same dimensions.', 'pagelines')
				);

			}



			$metatab_settings = array(
					'id' 		=> 'quickslider_options',
					'name' 		=> __( 'QuickSlider', 'pagelines' ),
					'icon' 		=> $this->icon,
					'clone_id'	=> $settings['clone_id'],
					'active'	=> $settings['active']
				);

			register_metatab( $metatab_settings, $array );

	}

}