<?php



class PageLinesSettingsPanel{

	function __construct(){

		add_filter('pl_toolbar_config', array(&$this, 'toolbar'));
		add_action('pagelines_editor_scripts', array(&$this, 'scripts'));

		$this->url = PL_PARENT_URL . '/editor';
	}

	function scripts(){
		// Colorpicker
		wp_enqueue_script( 'css3colorpicker', $this->url . '/js/colorpicker/colorpicker.js', array('jquery'), '1.3.1', true );

		// Image Uploader
		wp_enqueue_script( 'fineupload', $this->url . '/js/fineuploader/jquery.fineuploader-3.2.min.js', array('jquery'), PL_CORE_VERSION, true );

		wp_enqueue_script( 'pl-js-settings', $this->url . '/js/pl.settings.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}

	function toolbar( $toolbar ){

		$toolbar[ 'settings' ] = array(
			'name'	=> 'Global Options',
			'icon'	=> 'icon-globe',
			'pos'	=> 35,
			'panel'	=> $this->get_settings_tabs()
		);

		$toolbar[ 'section-options' ] = array(
			'name'	=> 'Section Options',
			'icon'	=> 'icon-paste',
			'type'	=> 'hidden',
			'flag'	=> 'section-opts',
			'pos'	=> 1000,
			'panel'	=> $this->section_options_panel()
		);

		return $toolbar;
	}

	function get_settings_tabs( $panel = 'site' ){

		$settings_object = new EditorSettings;

		$tabs = array();

		$tabs['heading'] = 'Global Settings';

		foreach( $settings_object->get_set('site') as $tabkey => $tab ){

			$tabs[ $tabkey ] = array(
				'key' 	=> $tabkey,
				'name' 	=> $tab['name'],
				'icon'	=> isset($tab['icon']) ? $tab['icon'] : ''
			);
		}


		return $tabs;

	}

	function section_options_panel(){
		global $plpg;

		$current_page = ($plpg->is_special()) ? $plpg->type_name : $plpg->id;

		$tabs = array();
		$tabs['heading'] = "Section Options";

		$tabs['settings'] = array( 'name'	=> 'Section Options' );
	
		if( !$plpg->is_special() )
			$tabs['type'] = array( 'name'	=> 'Type Defaults <span class="label">'.$plpg->type_name.'</span>' );

	//	$tabs['global'] = array( 'name'	=> 'Setting Defaults' );



		return $tabs;

	}


}