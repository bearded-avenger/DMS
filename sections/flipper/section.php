<?php
/*
	Section: Flipper
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A great way to flip through posts. Simply select a post type and done.
	Class Name: PageLinesFlipper	
	Cloning: true
	Workswith: main, templates, sidebar_wrap
	Filter: post-format
*/

class PageLinesFlipper extends PageLinesSection {

	var $default_limit = 3;

	function section_persistent(){
		add_image_size( 'portfolio-thumb', 300, 200, true );
	}

	function section_styles(){
		wp_enqueue_script( 'caroufredsel', $this->base_url.'/min.caroufredsel.js', array( 'jquery' ), PL_CORE_VERSION, true );
		wp_enqueue_script( 'flipper', $this->base_url.'/flipper.js', array( 'jquery' ), PL_CORE_VERSION, true );
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
		
		global $post;
		$post_type = 'post'; 
		
		$flipper_link = get_post_type_archive_link( $post_type );

		$the_query = array(
			'posts_per_page' => '10',
			'post_type' => $post_type
		);
		query_posts( $the_query ); 
		
		if(have_posts()) { ?>
				
				<div class="flipper-heading">
					<div class="flipper-title">
						Whatever
						<a href="<?php echo $portfolio_link; ?>" > / View All</a>
					</div>
					<a class="flipper-prev" href="#"><i class="icon-arrow-left"></i></a>
			    	<a class="flipper-next" href="#"><i class="icon-arrow-right"></i></a>
				</div>
	
				<div class="flipper-wrap">
				
				<ul class="row flipper-items text-align-center flipper" data-scroll-speed="800" data-easing="easeInOutQuart">
		<?php } ?>
			
			<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
			
				
			<li>
				
				<div class="flipper-item">
					<?php 
					if ( has_post_thumbnail() ) { 
						echo get_the_post_thumbnail( $post->ID, 'portfolio-thumb', array('title' => '')); 
					} else { 
						echo '<img src="'.$this->base_url.'/no-portfolio-item-small.jpg" alt="no image added yet." />'; 				}
						 ?>
					
					<div class="work-info-bg"></div>
					<div class="work-info">
						
						<div class="vert-center">

						<?php 
						
						$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );  
						$video_embed = get_post_meta($post->ID, '_nectar_video_embed', true);
						$video_m4v = get_post_meta($post->ID, '_nectar_video_m4v', true);
					
						//video 
					    if( !empty($video_embed) || !empty($video_m4v) ) {

						    if( !empty( $video_embed ) ) {
						    	
						    	echo '<a href="#video-popup-'.$post->ID.'" class="pp">'.__("Watch Video", 'pagelines').' </a> ';
								echo '<div id="video-popup-'.$post->ID.'">';
						        echo '<div class="video-wrap">' . stripslashes(htmlspecialchars_decode($video_embed)) . '</div>';
								echo '</div>';
						    } 
						    
						    else {
								 echo '<a href="'.get_template_directory_uri(). '/includes/portfolio-functions/video.php?post-id=' .$post->ID.'&iframe=true&width=854" class="pp" >Watch Video</a> ';	 
						     }

				        } 
						
						//image
					    else {
					       echo '<a href="'. $featured_image[0].'" class="pp">'.__("View Larger", 'pagelines').'</a> ';
					    }

						 echo '<a href="' . get_permalink() . '">'.__("More Details", 'pagelines').'</a>'; ?>
							
							
						</div><!--/vert-center-->
						
					</div>
				</div><!--work-item-->
				
				<div class="work-meta">
					<h4 class="title"><?php the_title(); ?></h4>
					<?php $options = get_option('salient'); 
						if(!empty($options['portfolio_date']) && $options['portfolio_date'] == 1) the_time('F d, Y');
					?>
				</div>
			
				
				<div class="clear"></div>
				
			</li>
			
			<?php endwhile; endif; 
			
			
			if(have_posts())
		 		echo '</ul></div>'; 
	
	}

	function do_defaults(){

		?>
		<h2>StarBar</h2>
		<ul class="starbars">
			<li>
				<p>Jack</p>
				<div class="bar-wrap">
					<span data-width="30%"><strong>30<i class="icon-star"></i></strong></span><strong>100<i class="icon-star"></i></strong>
				</div>
			</li>
			<li>
				<p>Jill</p>
				<div class="bar-wrap">
					<span data-width="60%"><strong>60<i class="icon-star"></i></strong></span>
				</div>
			</li>
		</ul>
		<?php
	}


}