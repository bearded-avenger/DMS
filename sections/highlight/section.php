<?php
/*
	Section: Highlight
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Adds a highlight sections with a splash image and 2-big lines of text.
	Class Name: PageLinesHighlight
	Workswith: templates, main, header, morefoot, sidebar1, sidebar2, sidebar_wrap
	Edition: pro
	Cloning: true
	Filter: component
*/

/**
 * Highlight Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesHighlight extends PageLinesSection {
	
	var $tabID = 'highlight_meta';
	

	function section_opts(){
		$opts = array(
			array(
				'type' 			=> 'select',
				'title' 		=> 'Select Format',
				'key'			=> '_highlight_splash_position',
				'label' 		=> 'Highlight Format',
				'opts'=> array(
					'top'			=> array( 'name' => 'Image on top of text' ),
					'bottom'	 	=> array( 'name' => 'Image on bottom of text' ), 
					'notext'	 	=> array( 'name' => 'No text, just the image' )
				),
			),
			'hl_text' => array(
				'type' 			=> 'multi',
				'title' 		=> 'Highlight Text',
				'opts'	=> array(
					array(
						'key'			=> '_highlight_head',
						'version' 		=> 'pro',
						'type' 			=> 'text',
						'size'			=> 'big',		
						'label' 		=> 'Highlight Header Text (Optional)',
					),
					array(
						'key'			=> '_highlight_subhead',
						'version' 		=> 'pro',
						'type' 			=> 'textarea',
						'label' 		=> 'Highlight Subheader Text (Optional)',
					)

				)
			),
			'hl_image' => array(
				'type' 			=> 'multi',
				'title' 		=> 'Highlight Image and Format',
				'opts'	=> array(

					 array(
						'key'			=> '_highlight_splash',
						'type' 			=> 'image_upload',	
						'label'			=> 'Upload Splash Image'
					),
					array(
						'key'				=> '_highlight_image_frame',
						'type' 				=> 'check',		
						'label' 			=> 'Add frame to image?'
					),
				)
			)
				
		);
		
		return $opts;
		
	}
	/**
	*
	* @TODO document
	*
	*/
	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);
		
		$metatab_array = array(
			
			'hl_options' => array(
				'version' 		=> 'pro',
				'type' 			=> 'multi_option',
				'title' 		=> 'Highlight Header Text (Optional)',
				'shortexp' 		=> 'Add the main header text for the highlight section.',
				'selectvalues'	=> array(
					'_highlight_head' => array(
						'version' 		=> 'pro',
						'type' 			=> 'text',
						'size'			=> 'big',		
						'inputlabel' 	=> 'Highlight Header Text (Optional)',
					),
					'_highlight_subhead' => array(
						'version' 		=> 'pro',
						'type' 			=> 'text',
						'size'			=> 'big',		
						'inputlabel' 	=> 'Highlight Subheader Text (Optional)',
					),

					'_highlight_splash' => array(
						'version' 		=> 'pro',
						'type' 			=> 'image_upload',	
						'inputlabel'	=> 'Upload Splash Image'
					),
					'_highlight_splash_position' => array(
						'version' 		=> 'pro',
						'type' 			=> 'select',		
						'inputlabel' 		=> 'Highlight Image Style',
						'selectvalues'=> array(
							'top'			=> array( 'name' => 'Image on top of text' ),
							'bottom'	 	=> array( 'name' => 'Image on bottom of text' ), 
							'notext'	 	=> array( 'name' => 'No text, just the image' )
						),
					),
					'_highlight_image_frame' => array(
						'type' 				=> 'check',		
						'inputlabel' 		=> 'Add frame to image?'
					),
				)
			)
				
		);
		
		$metatab_settings = array(
				'id' 		=> $this->tabID,
				'name' 		=> 'Highlight',
				'icon' 		=> $this->icon, 
				'clone_id'	=> $settings['clone_id'], 
				'active'	=> $settings['active']
			);
		
		register_metatab($metatab_settings, $metatab_array);
	}

	/**
	*
	* @TODO document
	*
	*/
	function section_template() { 

		$h_head = $this->opt('_highlight_head', $this->tset);
		
		

		$h_subhead = $this->opt('_highlight_subhead', $this->tset);
		
		$h_splash = $this->opt('_highlight_splash', $this->tset);
		$h_splash_position = $this->opt('_highlight_splash_position', $this->oset);
		
		$frame_class = ($this->opt('_highlight_image_frame', $this->oset)) ? 'pl-imageframe' : '';
		
		if(!$h_head && !$h_subhead && !$h_splash){
			$h_head = __("Here's to the crazy ones...", 'pagelines');
			$h_subhead = __("This is your Highlight section. Set up the options to configure.", 'pagelines');
		}
		
		?>
		<div class="highlight-area">
			<?php 
			
				if( $h_splash_position == 'top' && $h_splash)
					printf('<div class="highlight-splash hl-image-top %s"><img src="%s" alt="" /></div>', $frame_class, $h_splash);
				
				if( $h_splash_position != 'notext' ){
					
					if($h_head)
						printf('<h1 class="highlight-head">%s</h1>', __( $h_head, 'pagelines' ) );
				
					if($h_subhead)
						printf('<h3 class="highlight-subhead subhead">%s</h3>', __( $h_subhead, 'pagelines' ) );
						
				}	
				
				if( $h_splash_position != 'top' && $h_splash)
					printf('<div class="highlight-splash hl-image-bottom %s"><img src="%s" alt="" /></div>', $frame_class, apply_filters( 'pl_highlight_splash', $h_splash ) );
			?> 
		</div>
	<?php 
	
	}
}