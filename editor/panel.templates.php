<?php

class EditorTemplates {
	
	var $template_slug = 'pl-user-templates';
	var $default_template_slug = 'pl-default-tpl';
	var $map_option_slug = 'pl-template-map';
	
	
	function __construct( ){
		$this->data = new PageLinesData; 
		
		global $plpg;
		
		$this->page = $plpg;
			
		
		$this->default_type_tpl = ($plpg && $plpg != '') ? $this->data->meta( $plpg->typeid, $this->default_template_slug ) : false;
		
		$this->default_global_tpl = $this->data->opt( $this->default_template_slug );
		
		$this->default_tpl = ($this->default_type_tpl) ? $this->default_type_tpl : $this->default_global_tpl;
		
		$this->url = PL_PARENT_URL . '/editor';
		
		add_filter('pl_toolbar_config', array(&$this, 'toolbar'));
		add_filter('pagelines_editor_scripts', array(&$this, 'scripts'));
		
		add_action( 'admin_init', array(&$this, 'admin_page_meta_box'));
		add_action( 'post_updated', array(&$this, 'save_meta_options') );
		
	}
	
	function admin_page_meta_box(){
		remove_meta_box( 'pageparentdiv', 'page', 'side' );
		add_meta_box('specialpagelines', __('Page Setup'), array(&$this, 'page_attributes_meta_box'), 'page', 'side');
		
	}
	
	function save_meta_options( $postID ){
		$post = $_POST;
		if((isset($post['update']) || isset($post['save']) || isset($post['publish']))){
		
		
			$user_template = (isset($post['pagelines_template'])) ? $post['pagelines_template'] : '';
			
			if($user_template != ''){
				
				pl_meta_update($postID, $this->map_option_slug, array('live' => $user_template, 'draft' => $user_template));
			}
				
		
		}
	}
	function page_attributes_meta_box( $post ){
		$post_type_object = get_post_type_object($post->post_type);

		///// CUSTOM PAGE TEMPLATE STUFF ///// 

			$options = '<option value="">Select Template</option>'; 
			$loaded_user_template = pl_meta($post->ID, $this->load_template_slug, array('live' => '', 'draft' => '')); 
			foreach($this->get_user_templates() as $index => $t){
				$sel = ($loaded_user_template['live'] === $index) ? 'selected' : '';
				
				$options .= sprintf('<option value="%s" %s>%s</option>', $index, $sel, $t['name']); 
			}

			

			printf('<p><strong>%1$s</strong></p>', __('Load PageLines Template', 'pagelines'));

			printf('<select name="pagelines_template" id="pagelines_template">%s</select>', $options);

		///// END TEMPLATE STUFF ///// 

		
		if ( $post_type_object->hierarchical ) {
			$dropdown_args = array(
				'post_type'        => $post->post_type,
				'exclude_tree'     => $post->ID,
				'selected'         => $post->post_parent,
				'name'             => 'parent_id',
				'show_option_none' => __('(no parent)'),
				'sort_column'      => 'menu_order, post_title',
				'echo'             => 0,
			);

			$dropdown_args = apply_filters( 'page_attributes_dropdown_pages_args', $dropdown_args, $post );
			$pages = wp_dropdown_pages( $dropdown_args );
			if ( ! empty($pages) ) {
				printf('<p><strong>%1$s</strong></p>', __('Parent Page'));
				echo $pages;
			}
		}	

		printf('<p><strong>%1$s</strong></p>', __('Page Order'));
		printf('<input name="menu_order" type="text" size="4" id="menu_order" value="%s" /></p>', esc_attr($post->menu_order) );

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
					'icon'	=> 'icon-copy',
					'tools'	=> '<button class="btn btn-mini btn-restore-global-areas"><i class="icon-repeat"></i> Restore Header/Footer</button>',
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
		$slug = $this->default_template_slug;
		$this->xlist = new EditorXList; 
		$templates = '';
		$list = '';
		$tpls = pl_meta( $this->page->id, $this->map_option_slug, pl_settings_default());
		
		foreach( $this->get_user_templates() as $index => $template){
		
			
			$classes = array('x-templates'); 
			$classes[] = sprintf('template_key_%s', $index); 
			
			// echo $index; 
// 			print_r($tpls);
		
			if( $index == $tpls['live'] && $index == $tpls['draft'] ){
				$main_btn_text = 'Active Template'; 
				$main_btn_class = 'btn-success'; 
			} elseif($index == $tpls['live']){
				$main_btn_text = 'Active Template (Live)'; 
				$main_btn_class = 'btn-success'; 
			} elseif($index == $tpls['draft']){
				$main_btn_text = 'Active Template (Draft)'; 
				$main_btn_class = 'btn-success'; 
			} else {
				$main_btn_text = 'Load Template'; 
				$main_btn_class = 'btn-primary load-template'; 
			}
			
		
			
			
			ob_start(); 
			
			?>
			<div class="x-item-actions">
				<?php printf('<button class="btn btn-mini %s">%s</button>', $main_btn_class, $main_btn_text); ?>
				
				<button class="btn btn-mini delete-template">Delete</button>
				<?php if(!$this->page->is_special()): 
	
			
					$active = ($index === $this->default_type_tpl) ? 'btn-inverse' : '';
					$text = ($index === $this->default_type_tpl) ? 'Active' : 'Set as';
					
					
					?>
					<button class="btn btn-mini set-tpl <?php echo $active;?>" data-run="type" data-field="<?php echo $slug;?>"><?php echo $text; ?> Post Type Default</button>
				<?php endif;
				
				$active = ($index === $this->default_global_tpl) ? 'btn-inverse' : '';
				$text = ($index === $this->default_global_tpl) ? 'Active' : 'Set as';
				?>
				
				
				<button class="btn btn-mini set-tpl <?php echo $active;?>" data-run="global" data-field="<?php echo $slug;?>"><?php echo $text; ?> Sitewide Default</button>
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
				
				<label for="template-desc">Template Description</label>
				<textarea rows="4" id="template-desc" name="template-desc" ></textarea>
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
		//var_dump($key);
		if( isset($templates[ $key ]) && isset($templates[ $key ]['map'] ) )
			return $templates[ $key ]['map'];
		else
			return false;
	}
	
	function set_new_local_template( $pageID, $tpl_id ){
			
		$user_map = pl_meta( $pageID, $this->map_option_slug, pl_settings_default());
		
		$user_map['draft'] = $tpl_id;

		pl_meta_update($pageID, $this->map_option_slug, $user_map);
		
		return $user_map;
		
	}
	
	
	function create_template( $name, $desc, $map ){
		
		$templates = $this->get_user_templates();
		
		$templates[ pl_create_id( $name ) ] = array(
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
	
	function load_template( $tpl ){
		
		// if load user template
		$tpl = ( isset( $tpl ) && !is_array( $tpl )) ? $tpl : $this->default_tpl;
		
		$d = $this->get_map_from_template_key( $tpl );
		
		if(!$d || $d == ''){
			$d = array( $this->default_template() );
		}
		
		
		return $d;
	}
	
	function default_template(){
		$t = array(
			'name'	=> 'Content Area',
			'class'	=> 'std-content',
			'content'	=> array(
				array(
					'object'	=> 'PLColumn',
					'span' 	=> 8,
					'content'	=> array( 
						array(
							'object'	=> 'PageLinesPostLoop'
						),
						array(
							'object'	=> 'PageLinesComments'
						),
					)
				),
				array(
					'object'	=> 'PLColumn',
					'span' 	=> 4,
					'content'	=> array( 
						array(
							'object'	=> 'PrimarySidebar'
						),
					)
				),
			)
		);
		
		return $t;
		
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
		
		$t[ 'default' ] = array(
				'name'	=> 'Default Template', 
				'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
				'map'	=> array(
					'template' => $this->default_template()
				)
			);
		
		$t[ 'feature' ] = array(
			'name'	=> 'Feature Template', 
			'desc'	=> 'Standard page configuration with right aligned sidebar and content area.', 
			'map'	=> array(
				'template' => array(
					'area'	=> 'TemplateAreaID',
					'content'	=> array(
						array(
							'object'	=> 'PageLinesQuickSlider',
						),
						array(
							'object'	=> 'PageLinesBoxes',
					
						),
						array(
							'object'	=> 'PageLinesPostLoop',
					
						),
					)
				)
			)
		); 

		$t[ 'landing' ] = array(
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