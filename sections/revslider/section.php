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
		
		wp_enqueue_script( 'revslider-plugins', $this->base_url.'/jquery.revslider.plugins.min.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'revslider', $this->base_url.'/jquery.revslider.min.js', array( 'jquery' ), PL_CORE_VERSION, true );
		
		
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

   function section_template( ) { 
	
	
	?>
	<div class="revslider-container">
		<div class="header-shadow"></div>
			<div class="fullwidthbanner" style="display:none;max-height:480px;height:480px;">
				<ul>
					
					<li data-transition="fade" data-slotamount="15" data-masterspeed="2300" data-thumb="images/thumbs/thumb2.jpg">
						<img src="<?php echo $this->base_url;?>/images/bg1.jpg" data-fullwidthcentering="on">

						<div class="caption slider-content left-side sfr str"
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
					<li data-transition="fade" data-slotamount="15" data-masterspeed="2300" data-thumb="images/thumbs/thumb2.jpg">
						<img src="<?php echo $this->base_url;?>/images/bg2.jpg" data-fullwidthcentering="on">

						<div class="caption slider-content right-side sfl stl"
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

					<li data-transition="fade" data-slotamount="7" data-masterspeed="300" data-thumb="<?php echo $this->base_url;?>/images/thumbs/thumb4.jpg">
						
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

				</ul>

				<div class="tp-bannertimer tp-bottom"></div>
			</div>
		</div>

		<?php 
	}


}