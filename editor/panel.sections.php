<?php 



class PageLinesSectionsPanel{
	
	function __construct(){
		
		add_filter('pl_toolbar_config', array(&$this, 'toolbar'));
		add_action('pagelines_editor_scripts', array(&$this, 'scripts'));
	
		$this->url = PL_PARENT_URL . '/editor';
	}
	
	function scripts(){
		
	}
	
	function toolbar( $toolbar ){
		$toolbar['add-new'] = array(
			'name'	=> 'Add New',
			'icon'	=> 'icon-plus-sign',
			'pos'	=> 20,
			'panel'	=> array(
				'heading'	=> "<i class='icon-random'></i> Drag to Add",
				'add_section'	=> array(
					'name'	=> 'Your Sections',
					'icon'	=> 'icon-random',
					'clip'	=> 'Drag on to page to add',
					'tools'	=> '<a href="#" class="btn btn-mini btn-rld-sections"><i class="icon-repeat"></i> Reload Sections</a>',
					'type'	=> 'call',
					'call'	=> array(&$this, 'add_new_callback'),
					'filter'=> '*'
				),
				'more_sections'	=> array(
					'name'	=> 'Get More Sections',
					'icon'	=> 'icon-download',
					'flag'	=> 'link-storefront'
				),
				'heading2'	=> "<i class='icon-filter'></i> Filters",
				'components'		=> array(
					'name'	=> 'Components',
					'href'	=> '#add_section',
					'filter'=> '.component',
					'icon'	=> 'icon-circle-blank'
				),
				'layouts'		=> array(
					'name'	=> 'Layouts',
					'href'	=> '#add_section',
					'filter'=> '.layout',
					'icon'	=> 'icon-columns'
				),
				'formats'		=> array(
					'name'	=> 'Post Formats',
					'href'	=> '#add_section',
					'filter'=> '.format',
					'icon'	=> 'icon-th'
				),
				'galleries'		=> array(
					'name'	=> 'Galleries',
					'href'	=> '#add_section',
					'filter'=> '.gallery',
					'icon'	=> 'icon-camera'
				),
				'navigation'	=> array(
					'name'	=> 'Navigation',
					'href'	=> '#add_section',
					'filter'=> '.nav',
					'icon'	=> 'icon-circle-arrow-right'
				),
				'features'		=> array(
					'name'	=> 'Features',
					'href'	=> '#add_section',
					'filter'=> '.feature',
					'icon'	=> 'icon-picture'
				),

				'social'	=> array(
					'name'	=> 'Social',
					'href'	=> '#add_section',
					'filter'=> '.social',
					'icon'	=> 'icon-comments'
				),
				'widgets'	=> array(
					'name'	=> 'Widgetized',
					'href'	=> '#add_section',
					'filter'=> '.widgetized',
					'icon'	=> 'icon-retweet'
				),
				'misc'		=> array(
					'name'	=> 'Miscellaneous',
					'href'	=> '#add_section',
					'filter'=> '.misc',
					'icon'	=> 'icon-star'
				),
			)
		);
		
		return $toolbar;
	}
	
	function add_new_callback(){
		$this->xlist = new EditorXList; 
		$this->extensions = new EditorExtensions;
		$this->page = new PageLinesPage;

		$sections = $this->extensions->get_available_sections();


		$section_classes = 'pl-sortable span12 sortable-first sortable-last';
		$list = '';
		$count = 1;
		foreach($sections as $key => $s){

			$img = sprintf('<img src="%s" style=""/>', $s->screenshot);

			if($s->map != ''){
				$map = json_encode( $s->map );
				$special_class = 'section-plcolumn';
			} else {
				$map = '';
				$special_class = '';
			}

			if($s->filter == 'deprecated')
				continue;
				
			$class = array('x-add-new', $section_classes, $special_class, $s->filter);

			$number = $count++;

			if( !empty($s->isolate) ){
				$disable = true;
				foreach($s->isolate as $isolation){
					if($isolation == 'posts_pages' && $this->page->is_posts_page()){
						$disable = false;
					} elseif ($isolation == '404_page' && is_404()){
						$disable = false;
					}
				}
				
				if( $disable ) {
					$class[] = 'x-disable';
					$number += 100;
				}
					
				
			}
			
			

			$args = array(
				'id'			=> $s->id,
				'class_array' 	=> $class,
				'data_array'	=> array(
					'object' 	=> $s->class_name,
					'sid'		=> $s->id,
					'name'		=> $s->name,
					'image'		=> $s->screenshot,
					'template'	=> $map,
					'clone'		=> '0',
					'number' 	=> $number,
				),
				'thumb'			=> $s->screenshot,
				'splash'		=> $s->splash,
				'name'			=> $s->name,
				'sub'			=> ucfirst($s->filter)
			);

			$list .= $this->xlist->get_x_list_item( $args );



		}

		printf('<div class="x-list x-sections" data-panel="x-sections">%s</div>', $list);

	}

	
}