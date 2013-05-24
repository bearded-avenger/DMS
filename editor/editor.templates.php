<?php

class PageLinesTemplates {

	var $map_option_slug = 'pl-template-map';

	function __construct( EditorTemplates $tpl ){

		$this->tpl = $tpl;
		
		$this->mode = pl_draft_mode() ? 'draft' : 'live';
	
		global $plpg; 
		$this->page = $plpg;
		
		$this->set = new PageLinesOpts;
	}

	function get_map( PageLinesPage $page ){

		$map['header'] = $this->get_region( 'header' );
		$map['footer'] = $this->get_region( 'footer' );
		$map['template'] = $this->get_region( 'template' );
		
		return $map;

	}

	function get_region( $region ){
		
		// TO DEPRECATE for UPGRADE
		$this->upgrade_old_map_data( $region ); 
		
		if($region == 'header' || $region == 'footer'){
			
			$map = $this->set->regions; 
				
		} elseif( $region == 'template' ){
			
			
			$set = $this->set->local; 
			
			$tpl = ( isset($set['page-template']) ) ? $set['page-template'] : false;
			
			if( (!$tpl || $tpl == 'custom') && isset( $set['custom-map'] ) && is_array( $set['custom-map'] ) ){
				
				$map = $set['custom-map'];
				
			} elseif( $tpl ){
				
				$map = $this->get_map_from_template_key( $tpl ); 
			
			} else 
				$map = false;
					
							
			if( !$map && isset( $this->set->type['page-template']) )
				$map = $this->get_map_from_template_key( $this->set->type['page-template'] ); 
				
			
			if( !$map && isset( $this->set->global['page-template']) )
				$map = $this->get_map_from_template_key( $this->set->global['page-template'] ); 
			
		}
		
		// TO DEPRECATE for UPGRADE
		$map = (isset($map['draft'])) ? $map['draft'] : $map;
	
		return ( $map && isset($map[ $region ]) ) ? $map[ $region ] : $this->default_region( $region );		
		
	}
	
	// TO DEPRECATE for BETA UPGRADE
	function upgrade_old_map_data( $region ){
		
		if( ($region == 'header' || $region == 'footer') && !isset( $this->set->regions[ $region ]) ){
		
			$map_global = pl_opt( $this->map_option_slug, pl_settings_default(), true );
			//	plprint($map_global);
			if(isset($map_global['draft'][$region])){
			
				$this->set->regions[$region] = $map_global['draft'][$region]; 
				
				$set = pl_opt( PL_SETTINGS );
				
				$set['regions'] = $this->set->regions; 
				
				pl_opt_update( PL_SETTINGS, $set );
				
			}
				
				
		} elseif ($region == 'template' && !isset($this->set->local['custom-map']) && !isset($this->set->local['page-template'])){
			
			
			$map_local = pl_meta( $this->page->id, $this->map_option_slug, pl_settings_default() );
		
			if(isset($map_local['draft'][$region])){
				
				$this->set->local['custom-map'] = $map_local['draft'];
				$this->set->local['page-template'] = 'custom';
				
				$set = pl_meta( $this->page->id, PL_SETTINGS );
				
				$set['custom-map'] = $this->set->local['custom-map']; 
				
				pl_meta_update( $this->page->id, PL_SETTINGS, $set );
			}
		}
			
		
	}
	
	function get_map_from_template_key( $key ){

		$templates = $this->tpl->get_user_templates();
	
		$map = ( isset($templates[ $key ]) && isset($templates[ $key ]['map'] ) ) ? $templates[ $key ]['map'] : false;
			
		if($map)	
			return array( 'template' => $map );
		else 
			return false;
		
	}
	
	function default_region( $region ){
		
		
		
		if( $region == 'header' ){
			
			$d = array(
				array(
					'areaID'	=> 'HeaderArea',
					'content'	=> array( )
				)

			);
			
		} elseif( $region == 'footer' ){
			
			$d = array(
				array(
					'areaID'	=> 'FooterArea',
					'content'	=> array(
						array(
							'object' => 'SimpleNav'
						)
					)
				)

			);
			
		} elseif( $region == 'template' ){
			
			$d = array( $this->tpl->default_template() );
			
		}
		
		return $d;

		
	}

	function save_map_draft( $pageID, $map ){

		// global
		$global_settings = pl_opt( PL_SETTINGS, pl_settings_default(), true );

		$global_settings['draft']['regions'] = array(
			'header' => $map['header'],
			'footer' => $map['footer']
		);

		pl_opt_update( PL_SETTINGS, $global_settings );

		$local_settings = pl_meta( $pageID, PL_SETTINGS, pl_settings_default());
		
		$new_settings = $local_settings;
		
		$new_settings['draft']['custom-map'] = array(
			'template' => $map['template']
		);

		if($new_settings != $local_settings){
			
			$new_settings['draft']['page-template'] = 'custom'; 
			
			pl_meta_update( $pageID, PL_SETTINGS, $new_settings );
			
			$local = 1;
		
		} else
			$local = 0;


		return array('local' => $local);
	}
}

class EditorTemplates {

	var $template_slug = 'pl-user-templates';
	var $default_template_slug = 'pl-default-tpl';
	var $map_option_slug = 'pl-template-map';
	var $template_id_slug = 'pl-template-id';


	var $page_template_slug = 'pl-page-template'; 

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


	function scripts(){
		wp_enqueue_script( 'pl-js-mapping', $this->url . '/js/pl.mapping.js', array('jquery'), PL_CORE_VERSION, true);
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
				),
				'tmp_build' => array(
					'name'	=> 'Export Templates',
					'call'	=> array(&$this, 'save_templates'),
					'icon'	=> 'icon-download'
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

			$action_classes = array('x-item-actions'); 
			$action_classes[] = ($index === $this->page->template) ? 'active-template' : '';
			$action_classes[] = ($index === $this->default_global_tpl) ? 'active-global' : '';
			$action_classes[] = ($index === $this->default_type_tpl && !$this->page->is_special()) ? 'active-type' : '';
			

			ob_start();
			?>
			<div class="<?php echo join(' ', $action_classes);?>">
				
				<button class="btn btn-mini btn-primary load-template">Load Template</button>
				
				<button class="btn btn-mini btn-inverse the-active-template">Active Template</button>
				
				<div class="btn-group dropup">
				  <a class="btn btn-mini dropdown-toggle actions-toggle" data-toggle="dropdown" href="#">
				    Actions	<i class="icon-caret-down"></i>
				  </a>
				  <ul class="dropdown-menu">
					  <li ><a class="update-template">
						  <i class="icon-edit"></i> Update Template with Current Configuration
					  </a></li>

					  <?php if(!$this->page->is_special()):?>
				    	<li><a class="set-tpl posttype-link" data-run="type" data-field="<?php echo $slug;?>">
							<i class="icon-pushpin"></i> <span class="not-active">Set as</span><span class="badge badge-info">Active</span> "<?php echo $this->page->type_name;?>" post type default
						</a></li>
					<?php endif; ?>

						<li><a class="set-tpl global-link" data-run="global" data-field="<?php echo $slug;?>">
							<i class="icon-globe"></i> <span class="not-active">Set as</span><span class="badge badge-info">Active</span> sitewide default
						</a></li>
						<li><a class="delete-template">
							<i class="icon-remove"></i> Delete This Template
						</a></li>
				  </ul>
				</div>
				<button class="btn btn-mini tpl-tag global-tag" title="Current Sitewide Default"><i class="icon-globe"></i></button>
				<button class="btn btn-mini tpl-tag posttype-tag" title="Current Post Type Default"><i class="icon-pushpin"></i></button>
			</div>


			<?php

			$actions = ob_get_clean();


			$name = $template['name'];




			$args = array(
				'class_array' 	=> $classes,
				'data_array'	=> array(
					'key' 	=> $index
				),
				'name'			=> $name,
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
				<span class="help-block">
					Fill out this form and the current template will be saved for use throughout your site.<br/>
					<strong>Note:</strong> The current pages local settings as well as its section configuration will be saved as well
				</span>
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

	
	
	function get_template_data( $key ){
		
		$d = array(
			'name'	=> 'No Name',
			'desc'	=> '', 
			'map'	=> array(),
			'settings'	=> array()
		); 
		
		
		$templates = $this->get_user_templates();
	
		if( isset($templates[ $key ]) ){
			
			$t = wp_parse_args($templates[ $key ], $d); 
			return $t;
			
		} else
			return false;
	}

	

	function set_new_local_template( $pageID, $templateID ){

		$t = $this->get_template_data( $templateID ); 

		$page_settings = pl_meta( $pageID, PL_SETTINGS, pl_settings_default() ); 

		$page_settings['draft'] = $t['settings'];
		
		$page_settings['draft']['page-template'] = $templateID;
		
		pl_meta_update($pageID, PL_SETTINGS, $page_settings);
		
		return $page_settings;

	}


	function create_template( $name, $desc, $map, $settings ){

		$templates = $this->get_user_templates();

		$templates[ pl_create_id( $name ) ] = array(
			'name'		=> $name,
			'desc'		=> $desc,
			'map'		=> $map, 
			'settings'	=> $settings
		);

		pl_opt_update( $this->template_slug, $templates );

	}

	function update_template( $key, $template_map, $settings, $pageID){

		$templates = $this->get_user_templates();

		$templates[ $key ][ 'map' ] = $template_map;
		$templates[ $key ][ 'settings' ] = $settings;

		pl_opt_update( $this->template_slug, $templates );
		
		pl_local_update( $pageID, 'page-template', $key );
		
		return $key;

	}

	function delete_template( $key ){

		$templates = $this->get_user_templates();

		unset( $templates[$key] );

		pl_opt_update( $this->template_slug, $templates );

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
							'object'	=> 'PLRapidTabs'
						),
						array(
							'object'	=> 'PrimarySidebar'
						),
					)
				),
			)
		);

		return $t;

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
				array(
					'object'	=> 'plRevSlider',
				),
				array(
					'content'	=> array(
						array(
							'object'	=> 'pliBox',

						),
						array(
							'object'	=> 'PageLinesFlipper',

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
								'object'	=> 'PageLinesHighlight',
							),
							array(
								'object'	=> 'PageLinesPostLoop',
								'span'		=> 8, 
								'offset'	=> 2
							),

						)
					)
				)
		);

		return $t;
	}

	function admin_page_meta_box(){
		remove_meta_box( 'pageparentdiv', 'page', 'side' );
		add_meta_box('specialpagelines', __('Page Setup'), array(&$this, 'page_attributes_meta_box'), 'page', 'side');

	}

	/* 
	 * Used for WordPress Post Saving of PageLines Template
	 */ 
	function save_meta_options( $postID ){
		$post = $_POST;
		if((isset($post['update']) || isset($post['save']) || isset($post['publish']))){


			$user_template = (isset($post['pagelines_template'])) ? $post['pagelines_template'] : '';

			if($user_template != ''){

				$set = pl_meta($postID, PL_SETTINGS);
				
				$set['draft']['page-template'] = $user_template; 
				$set['live']['page-template'] = $user_template; 
				
				pl_meta_update($postID, PL_SETTINGS, $set);
			}


		}
	}
	/* 
	 * Adds PageLines Template selector when creating page/post
	 */
	function page_attributes_meta_box( $post ){
		$post_type_object = get_post_type_object($post->post_type);

		///// CUSTOM PAGE TEMPLATE STUFF /////

			$options = '<option value="">Select Template</option>';
			
			$set = pl_meta($postID, PL_SETTINGS);

			foreach($this->get_user_templates() as $index => $t){
				$sel = '';

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

}