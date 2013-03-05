<?php





class EditorExtensions {

	var $ext = array();

	function get_list(){
		$this->get_themes();
		$this->get_sections();
		$this->get_store();

		return $this->ext;
	}

	function get_themes(){
		// Themes
		$themes = wp_get_themes();


		if(is_array($themes)){

			foreach($themes as $theme => $t){
				$class = array();

				if($t->get_template() != 'pagelines')
					continue;

				$this->ext[ $theme ] = array(
					'name'		=> $t->name,
					'desc'		=> $t->description,
					'thumb'		=> $t->get_screenshot( ),
					'splash'	=> $t->get_screenshot( ),
					'purchase'	=> '',
					'overview'	=> '',
				);
			}
		}
	}

	function get_sections(){
		$sections = $this->get_available_sections();

		foreach($sections as $key => $s){

			$this->ext[ $s->id ] = array(
				'name'		=> $s->name,
				'desc'		=> $s->description,
				'thumb'		=> $s->screenshot,
				'splash'	=> $s->splash,
				'purchase'	=> '',
				'overview'	=> '',

			);

		}
	}

	function get_store(){
		global $mixed_array;
		foreach( $mixed_array as $key => $s ) {
			if( ! isset( $s['name'] ) )
				continue;
			$this->ext[ $key ] = array(
				'name'		=> $s['name'],
				'desc'		=> '',
				'thumb'		=> $s['thumb'],
				'splash'	=> $s['splash'],
				'purchase'	=> '',
				'overview'	=> '',
			);
		}
	}

	/*
	 * Functions library for editor
	 */

	function get_available_sections(){


		global $pl_section_factory;

		$sections = $pl_section_factory->sections;

		$sections = array_merge($sections, $this->layout_sections());

		return $sections;

	}


	function layout_sections(){

		$defaults = array(
			'id'			=> '',
			'name'			=> 'No Name',
			'filter'		=> 'layout',
			'description'	=> 'Layout section',
			'screenshot'	=>  PL_ADMIN_IMAGES . '/thumb-default.png',
			'splash'		=> PL_ADMIN_IMAGES . '/thumb-default.png',
			'class_name'	=> '',
			'map'			=> ''

		);

		$the_layouts = array(
			array(
				'id'			=> 'pl_split_column',
				'name'			=> '2 Columns - Split',
				'filter'		=> 'layout',
				'screenshot'	=>  PL_ADMIN_IMAGES . '/thumb-default.png',
				'map'			=> array(
									array(
										'object'	=> 'PLColumn',
										'span' 		=> 6,
										'newrow'	=> true
									),
									array(
										'object'	=> 'PLColumn',
										'span' 	=> 6
									),
								)
			),
			array(
				'id'			=> 'pl_3_column',
				'name'			=> '3 Columns',
				'filter'		=> 'layout',
				'screenshot'	=>  PL_ADMIN_IMAGES . '/thumb-default.png',
				'map'			=> array(
									array(
										'object'	=> 'PLColumn',
										'span' 		=> 4,
										'newrow'	=> true
									),
									array(
										'object'	=> 'PLColumn',
										'span' 	=> 4
									),
									array(
										'object'	=> 'PLColumn',
										'span' 	=> 4
									),
								)
			)
		);

		foreach($the_layouts as $index => $l){
			$l = wp_parse_args($l, $defaults);

			$obj = new stdClass();
			$obj->id = $l['id'];
			$obj->name = $l['name'];
			$obj->filter = $l['filter'];
			$obj->screenshot = $l['screenshot'];
			$obj->description = $l['description'];
			$obj->splash = $l['splash'];
			$obj->class_name = $l['class_name'];
			$obj->map = $l['map'];

			$layouts[ $l['id'] ] = $obj;
		}

		return $layouts;
	}


}