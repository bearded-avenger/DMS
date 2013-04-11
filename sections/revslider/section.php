<?php
/*
	Section: RevSlider
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A professional and versatile slider section. Can be customized with several transition and a large number of slides.
	Class Name: plRevSlider	
	Filter: full-width
*/


class plRevSlider extends PageLinesSection {


	function section_styles(){
		
		wp_enqueue_script( 'revslider-plugins', $this->base_url.'/rs-plugin/js/jquery.themepunch.plugins.min.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'revslider', $this->base_url.'/rs-plugin/js/jquery.themepunch.revolution.min.js', array( 'jquery' ), PL_CORE_VERSION, true );
		
		wp_register_style( 'revslider', $this->base_url.'/rs-plugin/css/settings.css', array(), PL_CORE_VERSION, 'screen');
	 	wp_enqueue_style( 'revslider' );
		
	}
	
	function section_head( ){
		?>
<script>
				jQuery(document).ready(function() {

					jQuery('.fullwidthbanner').show().revolution(
						{
							delay:9000,
							startwidth:940,
							startheight:480,
							onHoverStop:"on",
							thumbWidth: 100,	
							thumbHeight: 50,
							thumbAmount: 3,
							hideThumbs: 200,
							navigationType:"bullet",
							navigationArrows:"solo",
							navigationStyle:"round",
							navigationHAlign:"center",
							navigationVAlign:"bottom",
							navigationHOffset:0,
							navigationVOffset:20,

							soloArrowLeftHalign:"left",
							soloArrowLeftValign:"center",
							soloArrowLeftHOffset:20,
							soloArrowLeftVOffset:0,

							soloArrowRightHalign:"right",
							soloArrowRightValign:"center",
							soloArrowRightHOffset:20,
							soloArrowRightVOffset:0,

							touchenabled:"on",
							stopAtSlide:-1,	
							stopAfterLoops:-1,
							hideCaptionAtLimit:0,					// It Defines if a caption should be shown under a Screen Resolution ( Basod on The Width of Browser)
							hideAllCaptionAtLilmit:0,				// Hide all The Captions if Width of Browser is less then this value
							hideSliderAtLimit:0,					// Hide the whole slider, and stop also functions if Width of Browser is less than this value


							fullWidth:"on",

							shadow:0								//0 = no Shadow, 1,2,3 = 3 Different Art of Shadows -  (No Shadow in Fullwidth Version !)

						}
						
						);

				});

</script>	
<?php }

   function section_template( ) { 
	
	
	?>

	<div class="fullwidthbanner-container">
			<div class="fullwidthbanner" style="display:none;max-height:480px;height:480;">
				<ul>
					<!-- THE FIRST SLIDE -->
					<li data-transition="fade" data-slotamount="10" data-masterspeed="300" data-thumb="<?php echo $this->base_url;?>/images/thumbs/thumb1.jpg">
								<!-- THE MAIN IMAGE IN THE FIRST SLIDE -->
								<img src="<?php echo $this->base_url;?>/images/bg1.jpg" data-fullwidthcentering="on">

								<!-- THE CAPTIONS IN THIS SLDIE -->
								<div class="caption large_text fade"
									 data-x="center"
									 data-y="140"
									 data-speed="800"
									 data-start="800"
									 data-easing="easeOutExpo"  >
									<img src="<?php echo $this->base_url;?>/images/leaf.png">
								</div>

					</li>

					<!-- THE SECOND SLIDE -->
					<li data-transition="papercut" data-slotamount="15" data-masterspeed="2300" data-delay="9400" data-thumb="images/thumbs/thumb2.jpg">
								<img src="<?php echo $this->base_url;?>/images/bg2.jpg" data-fullwidthcentering="on">

								<div class="caption very_big_white lfl stl"
									 data-x="18"
									 data-y="293"
									 data-speed="300"
									 data-start="500"
									 data-easing="easeOutExpo" data-end="8800" data-endspeed="300" data-endeasing="easeInSine" >
									TIMELINED CAPTIONS
								</div>

					</li>
					<li data-transition="slidedown" data-slotamount="7" data-masterspeed="300" data-thumb="<?php echo $this->base_url;?>/images/thumbs/thumb4.jpg">
						
							<img src="<?php echo $this->base_url;?>/images/bg1.jpg" data-fullwidthcentering="on">

							<div class="caption fade fullscreenvideo" 
								data-autoplay="false" 
								data-x="0" 
								data-y="0" 
								data-speed="500" 
								data-start="10" 
								data-easing="easeOutBack">
									<iframe src="http://player.vimeo.com/video/22775048?title=0&amp;byline=0&amp;portrait=0;api=1" width="100%" height="100%"></iframe>
							</div>

					</li>

				</ul>

				<div class="tp-bannertimer tp-bottom"></div>
			</div>
		</div>

		<?php 
	}

	function section_optionator( $settings ){
		$settings = wp_parse_args( $settings, $this->optionator_default );
		
			$array = array(); 
			
			$array['quick_slides'] = array(
				'type' 			=> 'count_select',
				'count_start'	=> 1, 
				'count_number'	=> 10,
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
			
			$slides = ($this->opt('quick_slides', $oset)) ? $this->opt('quick_slides', $oset) : $this->default_limit;
			
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