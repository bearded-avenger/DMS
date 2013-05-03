<?php
/*
	Section: TextBox
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A simple box for text and HTML.
	Class Name: PageLinesTextBox
	Filter: component
*/

class PageLinesTextBox extends PageLinesSection {

	function section_opts(){
		$opts = array(
			array(
				'type' 			=> 'textarea',
				'key'			=> 'textbox_content',
				'label' 		=> 'Text Content',
			),
			array(
				'type' 			=> 'text',
				'key'			=> 'textbox_title',
				'label' 		=> 'Title (Optional)',
			),
			array(
				'type' 			=> 'select',
				'key'			=> 'textbox_align',
				'label' 		=> 'Alignment',
				'opts'			=> array(
					'textleft'		=> array('name' => 'Align Left (Default)'),
					'textright'		=> array('name' => 'Align Right'),
					'textcenter'	=> array('name' => 'Center'),
					'textjustify'	=> array('name' => 'Justify'),
				)
			),
			array(
				'type' 			=> 'select_animation',
				'key'			=> 'textbox_animation',
				'label' 		=> 'Viewport Animation',
				'help' 			=> 'Optionally animate the appearance of this section on view.',
			),

		);

		return $opts;

	}

	function section_template() {

		$text = $this->opt('textbox_content');

		
		
		$title = $this->opt('textbox_title');
		
		$text = (!$text && !$title) ? '<h3>TextBox</h3><p>Add Content!</p>' : sprintf('<div class="hentry">%s</div>', do_shortcode( wpautop($text) ) ); 
		
		$title = ($title) ? sprintf('<h3>%s</h3>', $title) : '';

		$class = $this->opt('textbox_animation');
			
		$align = $this->opt('textbox_align');
		
		printf('<div class="textbox-wrap pl-animation %s %s">%s%s</div>', $align, $class, $title, $text);

	}
}


