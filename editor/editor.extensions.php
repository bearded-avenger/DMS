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

				$thumb = $t->get_screenshot( );
				
				if( is_file( sprintf( '%s/splash.png', $t->get_stylesheet_directory() ) ) )
				 	$splash = sprintf( '%s/splash.png', $t->get_stylesheet_directory_uri()  );
				else 
					$splash = $thumb;

				$this->ext[ $theme ] = array(
					'id'		=> $theme,
					'name'		=> $t->name,
					'desc'		=> $t->description,
					'thumb'		=> $thumb,
					'splash'	=> $splash,
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
				'id'		=> $s->id,
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

		global $storeapi;
		foreach( $storeapi->get_latest() as $key => $s ) {
			if( ! isset( $s['name'] ) )
				continue;

			$purchased = ( isset( $s['purchased'] ) ) ? $s['purchased'] : '';

			$this->ext[ $key ] = array(
				'name'		=> $s['name'],
				'desc'		=> $s['description'],
				'thumb'		=> $s['thumb'],
				'splash'	=> $s['splash'],
				'purchase'	=> ( 'free' != $s['price'] && 'purchased' != $purchased ) ? sprintf( '%s,%s|%s|%s', $s['productid'], $s['uid'], $s['price'], $s['name'] ) : '',
				'overview'	=> $s['overview']
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
			'screenshot'	=>  PL_IMAGES . '/thumb-missing.png',
			'splash'		=> PL_IMAGES . '/splash-missing.png',
			'class_name'	=> '',
			'map'			=> ''

		);

		$the_layouts = array(
			array(
				'id'			=> 'pl_split_column',
				'name'			=> '2 Columns - Split',
				'filter'		=> 'layout',
				'screenshot'	=>  PL_EDITOR_URL . '/images/thumb-2column.png',
				'thumb'			=>  PL_EDITOR_URL . '/images/thumb-2column.png',
				'splash'		=>  PL_EDITOR_URL . '/images/splash-2column.png',
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
				'screenshot'	=>  PL_EDITOR_URL . '/images/thumb-3column.png',
				'thumb'			=>  PL_EDITOR_URL . '/images/thumb-3column.png',
				'splash'		=>  PL_EDITOR_URL . '/images/splash-3column.png',
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
			),
			array(
				'id'			=> 'pl_area',
				'name'			=> 'Section Area',
				'filter'		=> 'full-width',
				'screenshot'	=>  PL_EDITOR_URL . '/images/thumb-section-area.png',
				'thumb'			=>  PL_EDITOR_URL . '/images/thumb-section-area.png',
				'splash'		=>  PL_EDITOR_URL . '/images/splash-section-area.png'
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