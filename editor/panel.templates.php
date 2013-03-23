<?php

class EditorTemplates {
	
	var $template_slug = 'pl-user-templates';
	var $default_template_slug = 'pl-default-tpl';
	
	
	function __construct( ){
		$this->data = new PageLinesData; 
		
		global $plpg;
		
		if($plpg && $plpg != ''){
			$this->page = $plpg;
			$this->default_type_tpl = $this->data->meta( $plpg->typeid, $this->default_template_slug );
		}
		
		$this->default_global_tpl = $this->data->opt( $this->default_template_slug );
		
		$this->default_tpl = ($this->default_type_tpl) ? $this->default_type_tpl : $this->default_global_tpl;
		
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
	
		$this->xlist = new EditorXList; 
		$templates = '';
		$list = '';
		foreach( $this->get_user_templates() as $index => $template){
		
			
			$classes = array('x-templates'); 
			$classes[] = sprintf('template_key_%s', $index); 
			
			
			ob_start(); 
			
			?>
			<div class="x-item-actions">
				<button class="btn btn-mini btn-primary load-template">Load Template</button>
				<button class="btn btn-mini delete-template">Delete</button>
				<?php if(!$this->page->is_special()): 
	
			
					$active = ($index == $this->default_type_tpl) ? 'btn-inverse' : '';
					$text = ($index == $this->default_type_tpl) ? 'Active' : 'Set';
					
					$slug = $this->default_template_slug;
					?>
					<button class="btn btn-mini set-tpl <?php echo $active;?>" data-run="type" data-field="<?php echo $slug;?>"><?php echo $text; ?> Type Default</button>
				<?php endif;
				
				$active = ($index == $this->default_global_tpl) ? 'btn-inverse' : '';
				$text = ($index == $this->default_global_tpl) ? 'Active' : 'Set';
				?>
				
				
				<button class="btn btn-mini set-tpl <?php echo $active;?>" data-run="global" data-field="<?php echo $slug;?>"><?php echo $text; ?> Global Default</button>
			</div>
			
			
			<?php 
			
			$actions = ob_get_clean();
			
			
			
			
			$args = array(
				'class_array' 	=> $classes,
				'data_array'	=> array(
					'key' 	=> $index
				),
				'name'			=> $template['name'],
				'sub'			=> $template['desc'],
				'actions'		=> $actions,
				'format'		=> 'media',
				'icon'			=> 'icon-copy'
			);

			$list .= $this->xlist->get_x_list_item( $args );
		
		
			
		}
		
		printf('<div class="x-list">%s</div>', $list);
		
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