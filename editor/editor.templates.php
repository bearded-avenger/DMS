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
							$template['key'], 
							$template['name'], 
							$template['desc'],
							$plpg->type_name
						);
			
		}
		
		printf('<div class="y-list">%s</div>', $templates);
		
	}
	
	function save_templates(){
		
		?>
		
		<form class="opt standard-form">
			<fieldset>
				<span class="help-block">Fill out this form and the current template configuration will be saved for use throughout your site.</span>
				<label>Template Name</label>
				<input type="text">
				
				<label>Template Description</label>
				<textarea rows="4"></textarea>
				<button type="submit" class="btn">Save Template</button>
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

	function default_templates(){
		
		$t = array();
		
		
		
		$t[	'default'] = array(
				'key'	=> 'default',
				'name'	=> 'Default Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					array(
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
				'key'	=> 'feature',
				'name'	=> 'Feature Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					array(
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
				'key'	=> 'landing',
				'name'	=> 'Landing Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					array(
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