<?php
/*
	Section: iCallout
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A quick call to action for your users
	Class Name: PLICallout
	Edition: pro
	Filter: component
	Loading: active
*/

class PLICallout extends PageLinesSection {

	var $tabID = 'highlight_meta';


	function section_opts(){
		$opts = array(
			array(
				'type' 			=> 'select',
				'title' 		=> 'Select Format',
				'key'			=> 'icallout_format',
				'label' 		=> 'Callout Format',
				'opts'=> array(
					'top'			=> array( 'name' => 'Text on top of button' ),
					'inline'	 	=> array( 'name' => 'Text/Button Inline' )
				),
			),
			array(
				'type' 			=> 'multi',
				'title' 		=> 'Callout Text',
				'opts'	=> array(
					array(
						'key'			=> 'icallout_text',
						'version' 		=> 'pro',
						'type' 			=> 'text',
						'label' 		=> 'Callout Text',
					),

				)
			),
			array(
				'type' 			=> 'multi',
				'title' 		=> 'Link/Button',
				'opts'	=> array(

					 array(
						'key'			=> 'icallout_link',
						'type' 			=> 'text',
						'label'			=> 'URL'
					),
					array(
						'key'			=> 'icallout_link_text',
						'type' 			=> 'text',
						'label'			=> 'Text on Button'
					),
					array(
						'key'			=> 'icallout_btn_theme',
						'type' 			=> 'select_button',
						'label'			=> 'Button Color',
					),
					
				)
			)

		);

		return $opts;

	}

	function section_template() {

		$text = $this->opt('icallout_text');
		$format = ( $this->opt('icallout_format') ) ? 'format-'.$this->opt('icallout_format') : 'format-inline';
		$link = $this->opt('icallout_link');
		$theme = ($this->opt('icallout_btn_theme')) ? $this->opt('icallout_btn_theme') : 'btn-primary';
		$link_text = ( $this->opt('icallout_link_text') ) ? $this->opt('icallout_link_text') : 'Learn More <i class="icon-angle-right"></i>';
		
		if(!$text && !$link){
			$text = __("Call to action!", 'pagelines');
		}

		?>
		<div class="icallout-container <?php echo $format;?>">
			
			<h2 class="icallout-head" data-sync="icallout_text"><?php echo $text; ?></h2> 
			<a class="icallout-action btn <?php echo $theme;?> btn-large" href="<?php echo $link; ?>"  data-sync="icallout_link_text"><?php echo $link_text; ?></a>
		
		</div>
	<?php

	}
}