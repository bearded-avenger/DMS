<?php



class EditorThemeHandler {
	
	var $preview_slug = 'pl-theme-preview';
	
	function __construct(  ){
		
		add_action('pagelines_editor_scripts', array(&$this, 'scripts'));
		add_filter('pl_toolbar_config', array(&$this, 'toolbar'));
	
		$this->url = PL_PARENT_URL . '/editor';
	}
	
	function scripts(){
		wp_enqueue_script( 'pl-js-themes', $this->url . '/js/pl.themes.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}
	
	function toolbar( $toolbar ){
		$toolbar['theme'] = array(
			'name'	=> 'Theme',
			'icon'	=> 'icon-picture',
			'pos'	=> 40,
			'panel'	=> array(
				'heading'	=> "Select Theme",
				'avail_themes'	=> array(
					'name'	=> 'Available Themes',
					'call'	=> array(&$this, 'themes_dashboard'),
					'icon'	=> 'icon-picture'
				),
				'more_themes'	=> array(
					'name'	=> 'Get More Themes',
					'flag'	=> 'link-storefront',
					'icon'	=> 'icon-download' 
				)
			)

		);
		
		return $toolbar;
	}
	
	function themes_dashboard(){
		$this->xlist = new EditorXList; 
		
		$themes = wp_get_themes();

		$active_theme = wp_get_theme();

		$list = '';
		$count = 1;
		if(is_array($themes)){

			foreach($themes as $theme => $t){
				$class = array();

				if($t->get_template() != 'pagelines')
					continue;

				if($active_theme->stylesheet == $t->get_stylesheet()){
					$class[] = 'active-theme';
					$active = ' <span class="badge badge-info"><i class="icon-ok"></i> Active</span>';
					$number = 0;
				}else {
					$active = '';
					$number = $count++;
				}
				
				if( is_file( sprintf( '%s/splash.png', $t->get_stylesheet_directory() ) ) )
				 	$splash = sprintf( '%s/splash.png', $t->get_stylesheet_directory_uri()  );
				else 
					$splash = $t->get_stylesheet();
				
				$class[] = 'x-item-size-10';

				$args = array(
					'id'			=> $theme,
					'class_array' 	=> $class,
					'data_array'	=> array(
						'number' 		=> $number,
						'stylesheet'	=> $t->get_stylesheet()
					),
					'thumb'			=> $t->get_screenshot( ),
					'splash'		=> $t->get_screenshot( ),
					'name'			=> $t->name . $active
				);

				$list .= $this->xlist->get_x_list_item( $args );


			}

		}


		printf('<div class="x-list x-themes" data-panel="x-themes">%s</div>', $list);
	}
	
	// AJAX ACTIONS 
	
	function activate( $response ){
		
		$new = $response['post']['stylesheet'];
		
		$theme = wp_get_theme( $new );
		
		if ( !$new || !$theme->exists() || !$theme->is_allowed() ){
			$response['error'] = 'Theme does not exist or is not allowed';
			return $response;
		}
			
			
		switch_theme( $theme->get_stylesheet() );
		
		$response['success'] = 'Theme Switched!';
		$response['new'] = $new;
		
		return $response;
	}
	
	function set_preview(){
		
		$new = $response['post']['stylesheet'];
		
		$theme = wp_get_theme( $new );
		
		if ( !$new || !$theme->exists() || !$theme->is_allowed() ){
			$response['error'] = 'Theme does not exist or is not allowed';
			return $response;
		} else {
			echo 'here';
			pl_update_setting($this->preview_slug, $new);
			
			return $response;
			
		}
		
	}

	function maybe_load_preview( $active_stylesheet ){
		
		$preview_theme = $this->determine_theme( $active_stylesheet );
	
		if ( $preview_theme ){
			
			$preview_theme_object = wp_get_theme( $preview_theme ); 
			
			add_action('before_toolbox_panel', array(&$this, 'add_preview_banner')); 
			
			return $preview_theme_object->get_stylesheet(); 
			
		} else
			return $active_stylesheet;
		
			
		
	}
	
	function determine_theme( $active_stylesheet ){
		$preview_stylesheet = pl_setting( $this->preview_slug ); 
		
		if( $preview_stylesheet && $preview_stylesheet != $active_stylesheet )
			return $preview_stylesheet; 
		else 
			return false;
	}
	
	function add_preview_banner(){
		
		echo ' this is the end of the world.... >> '; 
	}
		
	
}