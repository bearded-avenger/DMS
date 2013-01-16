<?php

class EditorTemplates {
	
	var $template_slug = 'pl-user-templates';
	var $pt_defaults_slug = 'pl-default-templates';
	
	
	function __construct( ){

	}
	
	function user_templates(){
	
		global $plpg;
		$pagetype = new PageLinesPageType( $plpg->type );
		$default_template = $pagetype->get_type_field( 'template-default' );
		
	
		$templates = '';
		foreach( $this->get_user_templates() as $index => $template){
		
			if(!$plpg->is_special()){

				if($index == $default_template){
					$class = 'btn-success';
					$text = 'Current';
				} else {
					$class = 'set-default-template';
					$text = 'Make';
				}

				$post_type_default = sprintf(
					'<a class="btn btn-mini %s" data-type="%s">%s (%s) Default</a>', 
					$class,
					$plpg->type, 
					$text,
					$plpg->type_name
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
	
	function set_post_type_default( $post_type, $index){
		
		$post_type_defaults = pl_opt( $this->pt_defaults_slug, array() );
		
		$post_type_defaults[ $post_type ] = $index;
		
		pl_opt_update( $this->pt_defaults_slug, $post_type_defaults );
		
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