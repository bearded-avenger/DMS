<?php
/*
	Section: Carousel
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a flickr, nextgen, or featured image carousel.
	Class Name: PageLinesCarousel
	Cloning: true
	Workswith: content, header, footer
	Edition: pro
	Filter: gallery
*/

/**
 * Carousel Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesCarousel extends PageLinesSection {

	function section_opts() {
		$opts = array(

			array(
				'title'			=> 'Carousel Settings',
				'type'			=> 'multi',
				'opts'			=> array(

			array(
				'key'			=> 'carousel_items',
				'type'			=> 'text',
				'label'			=> 'Total Carousel Items' ),

			array(
				'key'			=> 'carousel_display_items',
				'type'			=> 'text',
				'label'			=> 'Displayed Carousel Items',
				'default'		=> 7 ),

			array(
				'key'			=> 'carousel_scroll_items',
				'type'			=> 'text',
				'label'			=> 'Scrolled Carousel Items',
				'default'		=> 4 ),

			array(
				'key'			=> 'carousel_animation_speed',
				'type'			=> 'text',
				'label'			=> 'Transition Speed (milliseconds)',
				'default'		=> 800 ),

			array(
				'key'			=> 'carousel_scroll_time',
				'type'			=> 'text',
				'label'			=> 'Autoscroll Speed (milliseconds)',
				'default'		=> 0 )
					)
				),

			array(
				'title'			=> 'Carousel Options',
				'type'			=> 'multi',
				'opts'			=> array(

			array(
				'key'			=> 'carousel_mode',
				'type'			=> 'select',
				'title'			=> 'Carousel Image/Link Mode',
				'opts'			=> array(

				'posts'			=> array( 'name' => 'Post Thumbnails (posts)' ),
				'flickr'		=> array( 'name' => 'Flickr' ),
				'ngen_gallery'	=> array( 'name' => 'NextGen Gallery' ),
				'hook'			=> array( 'name' => 'Hook: pagelines_carousel_list' )
						)
				),

			array(
				'key'			=> 'carousel_image_width',
				'type'			=> 'text',
				'label'			=> 'Max Image Width (in pixels)' ),

			array(
				'key'			=> 'carousel_image_height',
				'type'			=> 'text',
				'label'			=> 'Max Image Height (in pixels)' ),

			array(
				'key'			=> 'carousel_post_id',
				'type'			=> 'select_taxonomy',
				'label'			=> 'Posts Mode - Select Post Category',
				'taxonomy_id'	=> 'category' ),

			array(
				'key'			=> 'carousel_ngen_gallery',
				'type'			=> 'text',
				'label'			=> 'NextGen Gallery ID (NextGen Mode Selected)' )
				)
			)
		);
		return $opts;
	}

	/**
	* Load js
	*/
	function section_styles() {
		wp_enqueue_script( 'jcarousel', $this->base_url . '/jcarousel.js', array( 'jquery' ), null, true );
	}



	/**
	*
	* @TODO document
	*
	*/
	function section_head() {

		$num_items		= ( $this->opt( 'carousel_display_items', $this->oset ) ) ? $this->opt( 'carousel_display_items', $this->oset ) : 9;
		$scroll_items 	= ( $this->opt( 'carousel_scroll_items', $this->oset ) ) ? $this->opt( 'carousel_scroll_items', $this->oset ) : 6;
		$anim_speed 	= ( $this->opt( 'carousel_animation_speed', $this->oset ) ) ? $this->opt( 'carousel_animation_speed', $this->oset ) : 800;
		$callback 		= ( 0 != $this->opt( 'carousel_scroll_time', $this->oset ) ) ? ',initCallback: mycarousel_initCallback' : '';
		$auto 			= ( 0 != $this->opt( 'carousel_scroll_time', $this->oset ) ) ? round( $this->opt( 'carousel_scroll_time', $this->oset ) ) / 1000 : 0;

		$carousel_args 	= sprintf(
			'wrap: "circular", visible: %s,  easing: "%s", scroll: %s, animation: %s, auto: %s, itemFallbackDimension: 64 %s',
			$num_items,
			'swing',
			$scroll_items,
			$anim_speed,
			$auto,
			$callback
		);
	?>

	<script >
	/* <![CDATA[ */
	<?php if ( 0 != $this->opt('carousel_scroll_time', $this->oset) ) : ?>

	function mycarousel_initCallback(carousel)
	{
	    // Disable autoscrolling if the user clicks the prev or next button.
	    carousel.buttonNext.bind('click', function() {
	        carousel.startAuto(0);
	    });

	    carousel.buttonPrev.bind('click', function() {
	        carousel.startAuto(0);
	    });

	    // Pause autoscrolling if the user moves with the cursor over the clip.
	    carousel.clip.hover(function() {
	        carousel.stopAuto();
	    }, function() {
	        carousel.startAuto();
	    });
	};
<?php endif; ?>
jQuery(document).ready(function () {
	 jQuery( '<?php echo $this->prefix();?> .thecarousel')
	 		.show()
	 		.jcarousel({<?php echo $carousel_args;?> })


});
/* ]]> */
</script>
<?php

	}

   function section_template() {

		// Set Up Variables
		$carouselitems 			= ( $this->opt('carousel_items', $this->oset) ) ? $this->opt( 'carousel_items', $this->oset ) : 30;
		$carousel_post_id 		= ( $this->opt('carousel_post_id', $this->oset) ) ? $this->opt( 'carousel_post_id', $this->oset ) : null;
		$carousel_image_width 	= ( $this->opt('carousel_image_width', $this->oset) ) ? $this->opt( 'carousel_image_width', $this->oset ) : 64;
		$carousel_image_height 	= ( $this->opt('carousel_image_height', $this->oset) ) ? $this->opt( 'carousel_image_height', $this->oset ) : 64;
		$cmode 					= ( $this->opt('carousel_mode', $this->oset) ) ? $this->opt( 'carousel_mode', $this->oset ) : null;
		$ngen_id 				= ( $this->opt('carousel_ngen_gallery', $this->oset) ) ? $this->opt( 'carousel_ngen_gallery', $this->oset ) : 1;

		if( ( $cmode == 'flickr' && ! function_exists( 'get_flickrRSS' ) ) || ( $cmode == 'ngen_gallery' && ! function_exists( 'nggDisplayRandomImages' ) ) ){

			echo setup_section_notify( $this, __( "The <strong>plugin</strong> for the selected carousel mode needs to be activated (FlickrRSS or NextGen Gallery).", 'pagelines' ), admin_url() . 'plugins.php', 'Setup Plugin' );

		} else {
	?>

	<div class="thecarousel">
		<ul>
			<?php

			if( function_exists( 'nggDisplayRandomImages' )  && $cmode == 'ngen_gallery' ) {

				echo do_shortcode( '[nggallery id=' . $ngen_id . ' template=plcarousel]' );

			} elseif( function_exists( 'get_flickrRSS' ) && $cmode == 'flickr' ) {

				if( ! function_exists( 'get_and_delete_option' ) ) :  // fixes instantiation within the function in the plugin :/
					get_flickrRSS( array(
						'num_items' => $carouselitems,
						'html' => '<li><a href="%flickr_page%" title="%title%"><img src="%image_square%" alt="%title%"/><span class="list-title">%title%</span></a></li>'
					));
				endif;

			}elseif( $cmode == 'hook' )
				pagelines_register_hook( 'pagelines_carousel_list' );

			else{

				$carousel_post_query = 'numberposts=' . $carouselitems;

				if( $carousel_post_id )
					$carousel_post_query .= '&category_name=' . $carousel_post_id;

				$recentposts = get_posts( $carousel_post_query );

				foreach( $recentposts as $cid => $c ) {

					$a = array();

					if( has_post_thumbnail( $c->ID ) ) {
						$img_data = wp_get_attachment_image_src( get_post_thumbnail_id( $c->ID ));

						$a['img'] = ( $img_data[0] != '' ) ? $img_data[0] : $this->base_url . '/post-blank.jpg';

						$a['width'] 	= $img_data[1];
						$a['height'] 	= $img_data[2];

					} else {
						$a['img'] 		= $this->base_url . '/post-blank.jpg';
						$a['width'] 	= 100;
						$a['height']	= 100;
					}

					$args = array(
						'title'			=> $c->post_title,
						'link'			=> get_permalink( $c->ID ),
						'img'			=> $a['img'],
						'maxheight'		=> $carousel_image_height,
						'maxwidth'		=> $carousel_image_width,
						'height'		=> $a['height'],
						'width'			=> $a['width']
					);



					echo $this->carousel_item( $args );
				}

			} ?>
		</ul>
	</div>

<?php

		}
	}


	/**
	*
	* Image markup.
	*
	*/
	function carousel_item( $args ){

		$d = array(
			'title'			=> '',
			'link'			=> '',
			'height'		=> '100',
			'width'			=> '100',
			'maxheight'		=> '100',
			'maxwidth'		=> '100',
			'img'			=> '',
			'class'			=> '',
		);

		$a = wp_parse_args($args, $d);

		$img_style = sprintf('style="max-height: %spx; max-width: %spx;"', $a['maxheight'], $a['maxwidth']);

		$img = sprintf('<img src="%s" width="%s" height="%s" %s />', $a['img'], $a['width'], $a['height'], $img_style);

		$link = sprintf('<a class="carousel_image_link" href="%s">%s<span class="list-title">%s</span></a>', $a['link'], $img, $a['title']);

		$out = sprintf('<li class="list-item fix">%s</li>', $link);

		return $out;
	}
}