<?php 



class EditorXList{
	
	function __construct(){
		
		add_action('pagelines_editor_scripts', array(&$this, 'scripts'));
	
		$this->url = PL_PARENT_URL . '/editor';
	}
	
	function scripts(){
		
		// Isotope
		wp_enqueue_script( 'isotope', $this->url . '/js/utils.isotope.js', array('jquery'), PL_CORE_VERSION, true);
		
	}
	
	function get_x_list_item( $args ){
		$d = array(
			'id'			=> '',
			'class_array' 	=> array(),
			'data_array'	=> array(),
			'thumb'			=> '',
			'splash'		=> '',
			'name'			=> 'No Name'
		);
		$args = wp_parse_args($args, $d);

		$classes = join(' ', $args['class_array']);

		$popover_content = sprintf('<img src="%s" />', $args['splash']);

		$img = sprintf('<img width="300" height="225" src="%s" />', $args['thumb']);

		$datas = '';
		foreach($args['data_array'] as $field => $val){
			$datas .= sprintf("data-%s='%s' ", $field, $val);
		}

		$list_item = sprintf(
			"<section class='x-item x-extension %s %s' %s data-content='%s' data-extend-id='%s'>
				<div class='x-item-frame'>
					<div class='pl-vignette'>
						%s
					</div>
				</div>
				<div class='x-item-text'>
					%s
				</div>
			</section>",
			$args['id'],
			$classes,
			$datas,
			$popover_content,
			$args['id'],
			$img,
			$args['name']
		);

		return $list_item;

	}
	
	
}