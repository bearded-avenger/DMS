<?php
/*
	Section: RevSlider
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A professional and versatile slider section. Can be customized with several transitions and a large number of slides.
	Class Name: plRevSlider	
	Filter: full-width
*/


class plRevSlider extends PageLinesSection {

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



		$slides = ($this->opt('revslider_count')) ? $this->opt('revslider_count') : $this->default_limit;

		for($i = 1; $i <= $slides; $i++){


			$options[] = array(
				'title' 		=> __( 'RevSlider Slide ', 'pagelines' ) . $i,
				'type' 			=> 'multi',
				'opts' => array(
					'revslider_bg_'.$i 	=> array(
						'label' 	=> __( 'Slide Background Image', 'pagelines' ), 
						'type'			=> 'image_upload',
						'help'		=> 'For high resolution, 2000px wide x 800px tall images.'
					),
					
					'revslider_text_'.$i 	=> array(
						'label'	=> __( 'Slide Text', 'pagelines' ), 
						'type'			=> 'text'
					),	
					'revslider_link_'.$i 	=> array(
						'label'	=> __( 'Slide Link URL', 'pagelines' ), 
						'type'			=> 'text'
					),	
					'revslider_text_location_'.$i 	=> array(
						'label'	=> __( 'Slide Text Location', 'pagelines' ), 
						'type'			=> 'select', 
						'opts'	=> array(
							'left-side'	=> array('name'=> 'Text On Left'),
							'right-side'	=> array('name'=> 'Text On Right'),
							'centered'		=> array('name'=> 'Centered'),
						)
					),
					'revslider_transition_'.$i 	=> array(
						'label'		=> __( 'Slide Transition', 'pagelines' ), 
						'type'		=> 'select_same', 
						'opts'		=> $this->slider_transitions()
					),
				),
				
			);

		}

		return $options;
	}
	
	function slider_transitions(){
		
		$transitions = array(
			'boxslide',
			'boxfade',
			'slotzoom-horizontal',
			'slotslide-horizontal',
			'slotfade-horizontal',
			'slotzoom-vertical',
			'slotslide-vertical',
			'slotfade-vertical',
			'curtain-1',
			'curtain-2',
			'curtain-3',
			'slideleft',
			'slideright',
			'slideup',
			'slidedown',
			'fade',
			'random',
			'slidehorizontal',
			'slidevertical',
			'papercut',
			'flyin',
			'turnoff',
			'cube',
			'3dcurtain-vertical',
			'3dcurtain-horizontal',
		);
		
		return $transitions;
		
	}
	function section_styles(){
		
		wp_enqueue_script( 'revslider-plugins', $this->base_url.'/jquery.revslider.plugins.min.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'revslider', $this->base_url.'/jquery.revslider.min.js', array( 'jquery' ), PL_CORE_VERSION, true );
		
		
	}
	
	function section_head( ){
		
		?>
<script>
				jQuery(document).ready(function() {

					jQuery('<?php echo $this->prefix();?> .revslider-full').show().revolution(
						{
							delay:<?php echo $this->opt('revslider_delay', array('default' => 9000));?>,
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
							hideCaptionAtLimit:0,				
							hideAllCaptionAtLilmit:0,			
							hideSliderAtLimit:0,				
							fullWidth:"on",
							shadow:0							

						}
						
						);

				});

</script>	
<?php }

	function render_slides(){
		$slides = ($this->opt('revslider_count', $this->oset)) ? $this->opt('revslider_count', $this->oset) : $this->default_limit;
		
		$output = '';
		for($i = 1; $i <= $slides; $i++){
		
			$the_bg = $this->opt( 'revslider_bg_'.$i );
			
			if( $the_bg ){
				
				$the_text = $this->opt('revslider_text_'.$i);
				$the_link = $this->opt('revslider_link_'.$i);
				$the_location = $this->opt('revslider_text_location_'.$i);
				$transition = $this->opt('revslider_transition_'.$i, array('default' => 'fade'));
				
				if($the_location == 'centered'){
					$the_x = 'center'; 
					$caption_class = 'centered sfb stb';
				} elseif ($the_location == 'right-side'){
					$the_x = '560'; 
					$caption_class = 'right-side sfr str'; 
				} else {
					$the_x =  '0'; 
					$caption_class = 'left-side sfl stl';
				}
				
				$bg = sprintf('<img src="%s" data-fullwidthcentering="on">', $the_bg);
				
				$content = sprintf('<h2><span class="slider-text">%s</span></h2>', $the_text); 
				
				$link = ($the_link) ? sprintf('<a href="%s" class="slider-btn">%s</a>', $the_link, __('Read More', 'pagelines')) : '';
				
				$caption = sprintf(
						'<div class="caption slider-content %s" data-x="%s" data-y="130" data-speed="300" data-start="500" data-easing="easeOutExpo">%s %s</div>', 
						$caption_class, 
						$the_x, 
						$content, 
						$link
				); 
				
				
				
				$output .= sprintf('<li data-transition="%s" data-slotamount="7">%s %s</li>', $transition, $bg, $caption); 
			}
		}
		return $output;
	}
	
	function default_slides(){
		?>
		
			<li data-transition="fade" data-slotamount="10">
				<img src="<?php echo $this->base_url;?>/images/bg1.jpg" data-fullwidthcentering="on">
				<div class="caption slider-content right-side sfr str"
					 data-x="560"
					 data-y="130"
					 data-speed="300"
					 data-start="500"
					 data-easing="easeOutExpo"  >
					
						<h2><span class="slider-text">
						Welcome to PageLines.<br/>
					 	A Drag <span class="spamp">&amp;</span> Drop Platform <br/> for Amazing Websites.
						</span></h2>
					 	<a href="#" class="slider-btn">Read More</a>
					
				</div>
						

			</li>
			<li data-transition="fade" data-slotamount="10"  >
				<img src="<?php echo $this->base_url;?>/images/bg2.jpg" data-fullwidthcentering="on">

				<div class="caption slider-content left-side sfl stl"
					 data-x="0"
					 data-y="130"
					 data-speed="300"
					 data-start="500"
					 data-easing="easeOutExpo">
					
						<h2><span class="slider-text">
						Build Amazing, <br/>
						Ultra-Responsive Sites<br/>
					 	Without Touching Code
						</span></h2>
					 	<a href="#" class="slider-btn">Read More</a>
					
				</div>
			</li>
			

			<li data-transition="fade" data-slotamount="7" data-masterspeed="300" >
				
					<img src="<?php echo $this->base_url;?>/images/bg3.jpg" data-fullwidthcentering="on">

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
		
		<?php 
	}

   function section_template( ) { 
	
	
	?>
	<div class="revslider-container">
		<div class="header-shadow"></div>
			<div class="revslider-full" style="display:none;max-height:480px;height:480px;">
				<ul>
					<?php 
					
						$slides = $this->render_slides(); 
						
						if( $slides == '' ){
							$this->default_slides();
						} else {
							echo $slides;
						}
					?>
					
				</ul>

				<div class="tp-bannertimer tp-bottom"></div>
			</div>
		</div>

		<?php 
	}


}