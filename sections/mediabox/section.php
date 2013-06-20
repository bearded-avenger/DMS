<?php
/*
	Section: MediaBox
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A simple box for text and HTML.
	Class Name: PageLinesMediaBox
	Filter: component
	Loading: active
*/

class PageLinesMediaBox extends PageLinesSection {

	function section_opts(){
		$opts = array(
	
			array(
				'title'	=> 'MediaBox Media', 
				'type'	=> 'multi',
				'span'	=> 2,
				'opts'	=> array(
					array(
						'type' 			=> 'image_upload',
						'key'			=> 'mediabox_image',
						'label' 		=> 'MediaBox Image',
					),
					array(
						'type' 			=> 'text',
						'key'			=> 'mediabox_title',
						'label' 		=> 'Title',
					),
					array(
						'type' 			=> 'textarea',
						'key'			=> 'mediabox_html',
						'label' 		=> 'Text and Embed HTML',
						'help'			=> 'Enter rich media "embed" HTML in this field to add videos, etc.. instead of an image.'
					),
					
				)
			),
			
			array(
				'title'	=> 'MediaBox Display', 
				'type'	=> 'multi',
				'opts'	=> array(
					array(
						'type' 			=> 'select',
						'key'			=> 'mediabox_align',
						'label' 		=> 'Text/Media Alignment',
						'opts'			=> array(
							'center'		=> array('name' => 'Align Center (Default)'),
							'left'			=> array('name' => 'Align Left'),
							'right'			=> array('name' => 'Align Right'),
						)
					),
					array(
						'type' 			=> 'text',
						'key'			=> 'mediabox_height',
						'default'		=> '300',
						'label' 		=> 'MediaBox Min Height (px)',
						'help'			=> 'Required for "cover" mode. Otherwise the mediabox will be drawn at the height of the media.'
					),
					array(
						'type'			=> 'check',
						'key'			=> 'disable_centering', 
						'label'			=> 'Disable Media Vertical Centering?'
					),
				
				)
			),
			array(
				'title'	=> 'MediaBox Background (Optional)', 
				'type'	=> 'multi',
				'opts'	=> array(
					array(
						'type' 			=> 'image_upload',
						'key'			=> 'mediabox_background',
						'label' 		=> 'MediaBox Background Image',
					),
				)
			),
			array(
				'type' 			=> 'select_animation',
				'key'			=> 'mediabox_animation',
				'label' 		=> 'Viewport Animation',
				'help' 			=> 'Optionally animate the appearance of this section on view.',
			),
			
		

		);

		return $opts;

	}

	function section_template() {

		$image = $this->opt('mediabox_image');
		$media_html = $this->opt('mediabox_html');
		$disable_center = $this->opt('disable_centering');

		$title = ( $this->opt('mediabox_title') ) ? sprintf('<h3 data-sync="mediabox_title">%s</h3>', $this->opt('mediabox_title')) : '';
		$bg = ( $this->opt('mediabox_background') ) ? sprintf('background-image: url(%s);', $this->opt('mediabox_background')) : '';
		
		$set_height = ( $this->opt('mediabox_height') )  ? $this->opt('mediabox_height') : 350;
		$height = sprintf('min-height: %spx', $set_height);
		


		if( $image || $media_html )
			$img = ($image) ? sprintf('<img data-sync="mediabox_image" src="%s" />', $image) : '';
		elseif(!$bg)
			$img = sprintf('<img data-sync="mediabox_image" src="%s" />', $this->base_url.'/default.png'); // DEFAULT
		else 
			$img = '';
		
		$classes = array(); 
		$align_class = array(); 
		
		$align = $this->opt('mediabox_align');
		
		if($align == 'right')
			$align_class = 'textright alignright';
		elseif($align == 'left')
			$align_class = 'textleft alignleft';
		else
			$align_class = 'center';
		
		
		$classes[] = ($disable_center) ? '' : 'pl-centerer';
		$classes[] = ($this->opt('mediabox_animation')) ? $this->opt('mediabox_animation') : 'pla-fade';
		
		
		$html = do_shortcode( wpautop( $media_html ) );
		
		$height_sync_data = (pl_draft_mode()) ? 'data-sync="mediabox_height" data-sync-mode="css" data-sync-target="min-height" data-sync-post="px"' : '';
		
		printf(
			'<div class="mediabox-wrap %s pl-animation fix" %s style="%s%s">
				<div class="the-media fitvids pl-centered %s hentry">
					%s%s
					<div class="the-media-html">%s</div>
				</div>
			</div>', 
			join(' ', $classes), 
			$height_sync_data,
			$bg, 
			$height, 
			$align_class,
			$img, 
			$title,
			$html
		);
	
		
	}
}


