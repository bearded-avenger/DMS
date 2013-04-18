<?php
/*
	Section: Columnizer
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Place this section wherever you like and use WordPress widgets and a desired number of columns, to create an instant columnized widget section.
	Class Name: PageLinesColumnizer	
	Filter: widgetized
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
		for($i = 1; $i <= 4;$i++):
		?>
		
		<li id="the_default_widget" class="span3 widget">
			<div class="widget-pad">
				<h3 class="widget-title"><?php _e('Widget','pagelines'); ?></h3>
				<div class="textwidget">
					<p>Lorem ipsum dolor sit amet elit, consectetur adipiscing. Vestibulum luctus ipsum id quam euismod a malesuada sapien euismot. Vesti bulum ultricies elementum interdum. </p>

				</div>
			</div>
		</li>
		
	<?php
		endfor;
	
		return ob_get_clean();
	 }

}