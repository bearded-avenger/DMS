<?php

class EditorTemplates {
	
	var $template_slug = 'pl-user-templates';
	
	
	function __construct( ){

	}
	
	function user_templates(){
	
		global $plpg;
	
		$templates = '';
		foreach( $this->get_user_templates() as $index => $template){
			
			$templates .= sprintf(
							'<div class="list-item" data-key="%s">
								<div class="list-item-pad fix">
									<div class="title">%s</div>
									<div class="desc">%s</div>
									<div class="btns">
										<a class="btn btn-mini btn-primary load-template">Load Template</a>
										<a class="btn btn-mini load-template">Make "%s" Default</a>
										<a class="btn btn-mini delete-template">Delete</a>
									</div>
								</div>
							</div>', 
							$index, 
							$template['name'], 
							$template['desc'],
							$plpg->type_name
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
		$templates = pl_opt( $this->template_slug, $this->default_templates() );
		
		return $templates;
		
	}
	
	function get_map_from_template_key( $key ){
		
		$templates = $this->get_user_templates();
		
		if( isset($templates[ $key ]) && isset($templates[ $key ]['map'] ) )
			return $templates[ $key ]['map'];
		else
			return false;
	}
	
	function add_new_template( $name, $desc, $map ){
		
		$templates = $this->get_user_templates();
		
		$templates[] = array(
			'name'	=> $name,
			'desc'	=> $desc, 
			'map'	=> $map
		);
		
		pl_opt_update( $this->template_slug, $templates );
		
	}

	function default_templates(){
		
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