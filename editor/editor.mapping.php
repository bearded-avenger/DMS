<?php



class EditorMapping{

	function __construct(){

		add_action('pagelines_editor_scripts', array(&$this, 'scripts'));

		$this->url = PL_PARENT_URL . '/editor';
	}

	function scripts(){

		wp_enqueue_script( 'pl-js-mapping', $this->url . '/js/pl.mapping.js', array('jquery'), PL_CORE_VERSION, true);

	}


}