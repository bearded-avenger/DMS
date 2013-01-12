<?php

class EditorTemplates {
	
	function __contruct(){
		
		
		
	}

	function dummy_saved_templates(){
		
		$t = array(
			'default' => array(
				'key'	=> 'default',
				'name'	=> 'Default Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					array(
						'area'	=> 'TemplateAreaID',
						'content'	=> array(
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 8,
								'content'	=> array( 
									'PageLinesPostLoop' => array( ), 
									'PageLinesComments' 	=> array( ),	
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
				)
			), 
			'feature' => array(
				'key'	=> 'feature',
				'name'	=> 'Feature Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					array(
						'area'	=> 'TemplateAreaID',
						'content'	=> array(
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 8,
								'content'	=> array( 
									'PageLinesPostLoop' => array( ), 
									'PageLinesComments' 	=> array( ),	
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
				)
			), 
			'landing' => array(
				'key'	=> 'landing',
				'name'	=> 'Landing Page', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					array(
						'area'	=> 'TemplateAreaID',
						'content'	=> array(
							array(
								'object'	=> 'PLColumn',
								'span' 	=> 8,
								'content'	=> array( 
									'PageLinesPostLoop' => array( ), 
									'PageLinesComments' 	=> array( ),	
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
				)
			)
			
		);
		
		return $t;
	}

}