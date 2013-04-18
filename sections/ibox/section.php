<?php
/*
	Section: iBox
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: An easy way to create and configure several box type sections at once.
	Class Name: pliBox	
	Filter: post-formats
*/


class pliBox extends PageLinesSection {

	var $default_limit = 4;

	function section_opts(){
		
		$options = array(); 

		$options[] = array(
			
			'title' => __( 'iBox Configuration', 'pagelines' ),
			'type'	=> 'multi',
			'opts'	=> array(
				array(
					'key'			=> 'ibox_count',
					'type' 			=> 'count_select',
					'count_start'	=> 1, 
					'count_number'	=> 12,
					'default'		=> 4,
					'label' 	=> __( 'Number of iBoxes to Configure', 'pagelines' ),
				),
				array(
					'key'			=> 'ibox_cols',
					'type' 			=> 'count_select',
					'count_start'	=> 1, 
					'count_number'	=> 12,
					'default'		=> '3',
					'label' 	=> __( 'Number of Columns for Each Box (12 Col Grid)', 'pagelines' ),
				),
				array(
					'key'			=> 'ibox_media',
					'type' 			=> 'select',
					'opts'		=> array(
						'icon'	 	=> array( 'name' => 'Icon Font' ), 
						'image'		=> array( 'name' => 'Images' ),
						'text'		=> array( 'name' => 'Text Only, No Media' )
					),
					'default'		=> 'icon',
					'label' 	=> __( 'Select iBox Media Type', 'pagelines' ),
				),
				array(
					'key'			=> 'ibox_format',
					'type' 			=> 'select',
					'opts'		=> array(
						'top'		=> array( 'name' => 'Media on Top' ),
						'left'	 	=> array( 'name' => 'Media at Left' ), 
					),
					'default'		=> 'top',
					'label' 	=> __( 'Select the iBox Media Location', 'pagelines' ),
				),
			)

		);

		$slides = ($this->opt('ibox_count')) ? $this->opt('ibox_count') : $this->default_limit;
		$media = ($this->opt('ibox_media')) ? $this->opt('ibox_media') : 'icon';

		for($i = 1; $i <= $slides; $i++){

			$opts = array(
				
				'ibox_title_'.$i 	=> array(
					'label'		=> __( 'iBox Title', 'pagelines' ), 
					'type'		=> 'text'
				),
				'ibox_text_'.$i 	=> array(
					'label'	=> __( 'iBox Text', 'pagelines' ), 
					'type'	=> 'text'
				),	
				'ibox_link_'.$i 	=> array(
					'label'		=> __( 'iBox Link (Optional)', 'pagelines' ), 
					'type'		=> 'text'
				),
			);
			
			if($media == 'icon'){
				$opts['ibox_icon_'.$i] = array(
					'label'		=> __( 'iBox Icon', 'pagelines' ), 
					'type'		=> 'select_icon',
				);
			} elseif($media == 'image'){
				$opts['ibox_image_'.$i] = array(
					'label'		=> __( 'iBox Image', 'pagelines' ), 
					'type'		=> 'image_upload',
				);
			}


			$options[] = array(
				'title' 	=> __( 'iBox ', 'pagelines' ) . $i,
				'type' 		=> 'multi',
				'opts' 		=> $opts,
				
			);

		}

		return $options;
	}
	


   function section_template( ) { 
		$boxes = ($this->opt('ibox_count')) ? $this->opt('ibox_count') : $this->default_limit;
		$cols = ($this->opt('ibox_cols')) ? $this->opt('ibox_cols') : 3;
		
		$media_type = ($this->opt('ibox_media')) ? $this->opt('ibox_media') : 'icon';
		$media_format = ($this->opt('ibox_format')) ? $this->opt('ibox_format') : 'top';
		
		$width = 0;
	
		for($i = 1; $i <= $boxes; $i++):
			
			// TEXT
			$text = ($this->opt('ibox_text_'.$i)) ? $this->opt('ibox_text_'.$i) : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean id lectus sem. Cras consequat lorem.';
			$title = ($this->opt('ibox_title_'.$i)) ? $this->opt('ibox_title_'.$i) : __('iBox '.$i, 'pagelines');
			
			// LINK
			$link = $this->opt('ibox_link_'.$i);	
			$media_link = ($link) ? sprintf('href="%s"', $link) : ''; 
			$text_link = ($link) ? sprintf('<div class="ibox-link"><a href="%s">%s <i class="icon-angle-right"></i></a></div>', $link, __('More', 'pagelines')) : ''; 

			
			if( $media_type == 'icon' ){
				$media = ($this->opt('ibox_icon_'.$i)) ? $this->opt('ibox_icon_'.$i) : false;
				if(!$media){
					$icons = pl_icon_array(); 
					$media = $icons[ array_rand($icons) ];
				}
				$media_html = sprintf('<i class="icon-3x icon-%s"></i>', $media); 
				
			} elseif( $media_type == 'image' ){
				
				$media = ($this->opt('ibox_image_'.$i)) ? $this->opt('ibox_image_'.$i) : false;
				$media_html = ($media) ? sprintf('<img src="%s" />', $media) : ''; 
		
			} else
				$media_html = false;
			
	
			if($width == 0)
				echo '<div class="row fix">'; 
			
			$format_class = ($media_format == 'left') ? 'media' : '';
			$media_class = 'media-type-'.$media_type;
			
			printf(
				'<div class="span%s ibox %s fix">
					<div class="ibox-media img">
						<a class="ibox-icon-border %s" %s>
							%s
						</a>
					</div>
					<div class="ibox-text bd">
						<h3>%s</h3>
						<div class="ibox-desc">
							%s
							%s
						</div>
					</div>
				</div>',
				$cols,
				$format_class, 
				$media_class,
				$media_link,
				$media_html,
				$title, 
				$text, 
				$text_link
			); 
				
			$width += $cols;
			
			if($width >= 12 || $i == $boxes){
				$width = 0;
				echo '</div>';
			}
			
	
		 endfor;

	}


}