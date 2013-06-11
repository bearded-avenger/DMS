<?php
/*
	Section: Columnizer
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Place this section wherever you like and use WordPress widgets and a desired number of columns, to create an instant columnized widget section.
	Class Name: PageLinesColumnizer
	Filter: widgetized
	Loading: active
*/

class PageLinesColumnizer extends PageLinesSection {

	function section_persistent(){

	}

	function section_head(){



	}

	function change_markup( $params ){

		$cols = ($this->opt('columnizer_cols')) ? $this->opt('columnizer_cols') : 3;

		
		$params[0]['before_widget'] = sprintf('<div class="span%s">%s', $cols, $params[0]['before_widget']);
		$params[0]['after_widget'] = sprintf('%s</div>', $params[0]['after_widget']);
		
		if($this->width == 0)
			$params[0]['before_widget'] = sprintf('<div class="columnizer row fix">%s', $params[0]['before_widget']);

		$this->width += $cols;
		if($this->width >= 12 || $this->count == $this->total_widgets){
			$this->width = 0;
			$params[0]['after_widget'] = sprintf('%s</div>', $params[0]['after_widget']);
		}

		$this->count++;
		
		return $params;
	}

	function section_opts(){



		$opts = array(
			array(
				'title' => __( 'Columnizer Configuration', 'pagelines' ),
				'type'	=> 'multi',
				'opts'	=> array(
						array(
							'key'	=> 'columnizer_area',
							'type'	=> 'select',
							'opts'	=> get_sidebar_select(),
							'title'	=> 'Select Widgetized Area',
							'label'		=>	'Select widgetized area',
							'help'		=> "Select the widgetized area you would like to use with this instance.",
						),
						array(
							'key'			=> 'columnizer_cols',
							'type' 			=> 'count_select',
							'count_start'	=> 1,
							'count_number'	=> 12,
							'default'		=> '3',
							'label' 		=> __( 'Number of Grid Columns for Each Widget (12 Col Grid)', 'pagelines' ),
						),
					),
			),

			array(
				'key'	=> 'columnizer_help',
				'type'	=> 'link',
				'url'	=> admin_url( 'widgets.php' ),
				'title'	=> 'Widgetized Areas Help',
				'label'		=>	'<i class="icon-retweet"></i> Edit Widgetized Areas',
				'help'		=> "This section uses widgetized areas that are created and edited in inside your admin.",
			),
			array(
				'key'	=> 'columnizer_description',
				'type'	=> 'textarea',
			
				'title'	=> 'Column Site Description',
				'label'		=>	'Column Site Description',
				'help'		=> "If you use the default display of the columnizer, this field is used as a description of your company. You may want to add your address or links.",
			)
		);

		if(!class_exists('CustomSidebars')){
			$opts[] = array(
				'key'	=> 'widgetizer_custom_sidebars',
				'type'	=> 'link',
				'url'	=> 'http://wordpress.org/extend/plugins/custom-sidebars/',
				'title'	=> 'Get Custom Sidebars',
				'label'		=>	'<i class="icon-external-link"></i> Check out plugin',
				'help'		=> "We have detected that you don't have the Custom Sidebars plugin installed. We recommend you install this plugin to create custom widgetized areas on demand.",
			);
		}

		return $opts;
	}



	/**
	* Section template.
	*/
   function section_template() {

		$area = $this->opt('columnizer_area');


		if($area){

			$this->total_widgets = pl_count_sidebar_widgets( $area );
			$this->width = 0;
			$this->count = 1;

			add_filter('dynamic_sidebar_params', array(&$this, 'change_markup'));
			pagelines_draw_sidebar( $area );
			remove_filter('dynamic_sidebar_params', array(&$this, 'change_markup'));

		} else {
			printf ('<ul class="columnizer row fix sidebar_widgets">%s</ul>', $this->get_default() );
		}



	}

	function get_default(){
		ob_start();

			
		$twitter = $this->opt('twittername'); 
		$facebook = $this->opt('facebook_name'); 
		?>

		<li id="the_default_widget" class="span3 widget">
			<div class="widget-pad">
				<h3 class="widget-title"><?php _e('Stay in Touch!','pagelines'); ?></h3>
				<div class="textwidget">
					<p>
					Thanks for stopping by! Please make sure to stay in touch.
					</p>
					<ul>
					<?php
					if($twitter)
						printf('<li><a href="http://www.twitter.com/%1$s"><i class="icon-twitter"></i> Twitter</i></a></li>', $twitter); 
					
					if($facebook)
						printf('<li><a href="http://www.facebook.com/%1$s"><i class="icon-facebook"></i> Facebook</i></a></li>', $twitter);
					
						printf('<li><a href="%s"><i class="icon-rss"></i> Subscribe</a></li>', get_bloginfo( 'rss2_url' ) );
					?>
					</ul>
						
				</div>
			</div>
		</li>
		
		<?php
		
		?>
		<li id="the_default_widget" class="span3 widget">
			<div class="widget-pad">
				<h3 class="widget-title"><?php _e('The Latest','pagelines'); ?></h3>
				<ul class="media-list">
					<?php
			
					foreach( get_posts( array('numberposts' => 3) ) as $p ){
						$img = (has_post_thumbnail( $p->ID )) ? sprintf('<div class="img"><a class="the-media" href="%s" style="background-image: url(%s)"></a></div>', get_permalink( $p->ID ), pl_the_thumbnail_url( $p->ID, 'thumbnail')) : '';
						
						printf(
							'<li class="media fix">%s<div class="bd"><a class="title" href="%s">%s</a><span class="excerpt">%s</span></div></li>', 
							$img,
							get_permalink( $p->ID ), 
							$p->post_title, 
							pl_short_excerpt($p->ID)
						);
					
					} ?>
				
						
				</ul>
			</div>
		</li>
		
		<li id="the_default_widget" class="span3 widget">
			<div class="widget-pad">
				<h3 class="widget-title"><?php _e('Tags','pagelines'); ?></h3>
				<div class="tags-list">
					<?php
			
					wp_tag_cloud( array('number'=> 6, 'smallest' => 10, 'largest' => 10) );
					 ?>
				
						
				</div>
			</div>
			<div class="widget-pad">
				<h3 class="widget-title"><?php _e('Categories','pagelines'); ?></h3>
				<ul class="media-list">
					<?php
			
					echo wp_list_categories( array( 'number' => 5, 'depth' => 1, 'title_li' => '', 'orderby' => 'count' )); 
					 ?>
				
						
				</ul>
			</div>
		</li>

		<li id="the_default_widget" class="span3 widget">
			<div class="widget-pad">
				<h3 class="widget-title"><?php _e('More Info','pagelines'); ?></h3>
				<div class="textwidget">
					<?php
			
					if($this->opt('columnizer_description')):
						echo $this->opt('columnizer_description'); 
					else: 
					 ?>
					<p>Lorem ipsum dolor sit amet elit, consectetur adipiscing. Vestibulum luctus ipsum id quam euismod a malesuada sapien euismot. Vesti bulum ultricies elementum interdum. </p>

					<address>PageLines Inc.<br/>
					200 Brannan St.<br/>
					San Francisco, CA 94107</address>
				<?php endif; ?>
						
				</div>
			</div>
		</li>
		

	<?php


		return ob_get_clean();
	 }

}