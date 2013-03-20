<?php

class EditorTemplates {
	
	var $template_slug = 'pl-user-templates';
	var $default_template_slug = 'pl-default-tpl';
	
	
	function __construct( ){
		$this->data = new PageLinesData; 
		
		global $plpg;
		
		if($plpg && $plpg != ''){
			$this->page = $plpg;
			$this->default_tpl = $this->data->meta( $plpg->typeid, $this->default_template_slug );
		}
		
		$this->url = PL_PARENT_URL . '/editor';
		
		add_filter('pl_toolbar_config', array(&$this, 'toolbar'));
		add_filter('pagelines_editor_scripts', array(&$this, 'scripts'));
		
	}
	
	function scripts(){
		wp_enqueue_script( 'pl-js-templates', $this->url . '/js/pl.templates.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}
	
	function toolbar( $toolbar ){
		$toolbar['page-setup'] = array(
			'name'	=> 'Templates',
			'icon'	=> 'icon-paste',
			'pos'	=> 30,
			'panel'	=> array(
				'heading'	=> "Page Templates",
				'tmp_load'	=> array(
					'name'	=> 'Your Templates',
					'call'	=> array(&$this, 'user_templates'),
					'icon'	=> 'icon-copy'
				),
				'tmp_save'	=> array(
					'name'	=> 'Save New Template',
					'call'	=> array(&$this, 'save_templates'),
					'icon'	=> 'icon-paste'
				)
			)

		);
		
		return $toolbar;
	}
	
	function user_templates(){
	
		
		$templates = '';
		foreach( $this->get_user_templates() as $index => $template){
		
			if(!$this->page->is_special()){

				if($index === $this->default_tpl){
					$class = 'btn-success';
					$text = 'Active';
				} else {
					$class = 'set-default-tpl';
					$text = 'Set as';
				}

				$post_type_default = sprintf(
					'<a class="btn btn-mini %s btn-tpl-default" data-type="%s" data-field="%s" data-posttype="%s">%s "%s" Default</a>', 
					$class,
					$this->page->type, 
					$this->default_template_slug,
					$this->page->type_name,
					$text,
					$this->page->type_name
				);
			} else
				$post_type_default = '';
		
			$templates .= sprintf(
							'<div class="list-item template_key_%s" data-key="%s">
								<div class="list-item-pad fix">
									<div class="title">%s</div>
									<div class="desc">%s</div>
									<div class="btns">
										<a class="btn btn-mini btn-primary load-template">Load Template</a>
										%s
										<a class="btn btn-mini delete-template">Delete</a>
									</div>
								</div>
							</div>', 
							$index,
							$index,
							$template['name'], 
							$template['desc'],
							$post_type_default
						);
			
		}
		
		printf('<div class="y-list">%s</div>', $templates);
		
	}
	
	function save_templates(){
		
		?>
		
		<form class="opt standard-form form-save-template">
			<fieldset>
				<span class="help-block">Fill out this form and the current template configuration will be saved for use throughout your site.</span>
				<label for="template-name">Template Name (required)</label>
				<input type="text" id="template-name" name="template-name" required />
				
				<label for="template-desc">Template Description (required)</label>
				<textarea rows="4" id="template-desc" name="template-desc" required ></textarea>
				<button type="submit" class="btn btn-primary btn-save-template">Save New Template</button>
			</fieldset>
		</form>
		
		<?php 
		
	}
	
	function get_user_templates(){
		
		// get option
		$templates = pl_opt( $this->template_slug, $this->default_user_templates() );
		
		return $templates;
		
	}
	
	function get_map_from_template_key( $key ){
		
		$templates = $this->get_user_templates();
		
		if( isset($templates[ $key ]) && isset($templates[ $key ]['map'] ) )
			return $templates[ $key ]['map'];
		else
			return false;
	}
	
	
	function create_template( $name, $desc, $map ){
		
		$templates = $this->get_user_templates();
		
		$templates[] = array(
			'name'	=> $name,
			'desc'	=> $desc, 
			'map'	=> $map
		);
		
		pl_opt_update( $this->template_slug, $templates );
		
	}
	
	function delete_template( $key ){
		
		$templates = $this->get_user_templates();
		
		unset( $templates[$key] );
		
		pl_opt_update( $this->template_slug, $templates );
		
	}
	
	function default_template(){
		
		$d = $this->get_map_from_template_key( $this->default_tpl );
		
		if(!$d || $d == ''){
			$d = array(
				array(
					'area'	=> 'TemplateAreaID',
					'content'	=> array(
						array(
							'object'	=> 'PLColumn',
							'span' 	=> 8,
							'content'	=> array( 
								'PageLinesPostLoop' => array( ), 
								'PageLinesComments' => array(),	
							)
						),
						array(
							'object'	=> 'PLColumn',
							'span' 	=> 4,
							'content'	=> array( 
								'PrimarySidebar' => array( )
							)
						),
					)
				)

			);
		}
		
		
		return $d;
	}
	
	function default_header(){
		$d = array(
			array(
				'areaID'	=> 'HeaderArea',
				'content'	=> array(
					array(
						'object'	=> 'PageLinesBranding'
					),
					array(
						'object'	=> 'PLNavBar'
					),
				)
			)

		);
		
		return $d;
	}
	
	function default_footer(){
		$d = array(
			array(
				'areaID'	=> 'FooterArea',
				'content'	=> array(
					array(
						'object'	=> 'SimpleNav'
					)
				)
			)

		);
		
		return $d;
	}

	function default_user_templates(){
		
		$t = array();
		
		$t[	'default'] = array(
				'name'	=> 'Default Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					'template' => array(
						'area'	=> 'TemplateAreaID',
						'content'	=> array(
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 6,
								'content'	=> array( 
									'PageLinesPostLoop' => array( ), 
									'PageLinesComments' 	=> array( ),	
								)
							),
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 6,
								'content'	=> array( 
									'PrimarySidebar' => array( )
								)
							),
						)
					)
				)
		); 
		
		 $t['feature'] = array(
				'name'	=> 'Feature Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					'template' => array(
						'area'	=> 'TemplateAreaID',
						'content'	=> array(
							array(
								'object'	=> 'PageLinesFeatures',
							),
							array(
								'object'	=> 'PageLinesBoxes',
								
							),
						)
					)
				)
			); 
		
		$t['landing'] = array(
				'name'	=> 'Landing Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					'template' => array(
						'area'	=> 'TemplateAreaID',
						'content'	=> array(
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 12,
								'content'	=> array( 
									'PageLinesPostLoop' => array( ), 
									'PageLinesComments' 	=> array( ),	
								)
							)
						)
					)
				)
		);
		
		return $t;
	}

}