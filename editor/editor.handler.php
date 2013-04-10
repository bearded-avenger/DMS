<?php
/**
 * 
 *
 *  PageLines Front End Template Class
 *
 *
 *  @package PageLines Framework
 *  @subpackage Sections
 *  @since 3.0.0
 *  
 *
 */
class PageLinesTemplateHandler {

	var $section_list = array();
	var $opts_list	= array();
	var $area_number = 1;
	var $row_width = array();
	var $section_count = 0;

	function __construct( 
		EditorInterface $interface, 
		PageLinesAreas $areas, 
		PageLinesPage $pg, 
		EditorSettings $siteset, 
		PageLinesFoundry $foundry, 
		EditorMap $map, 
		EditorDraft $draft, 
		PageLinesOpts $opts,
		EditorLayout $layout,
		EditorExtensions $extensions
	) {


		global $pl_section_factory; 
		
		$this->factory = $pl_section_factory->sections; 
		
		// Dependancy Injection (^^)
		$this->editor = $interface;
		$this->areas = $areas;
		$this->page = $pg;
		$this->siteset = $siteset;
		$this->foundry = $foundry;
		$this->draft = $draft;
		$this->optset = $opts;
		$this->layout = $layout;
		$this->extensions = $extensions;
		
		
		$this->map = $map->get_map( $this->page );

		$this->parse_config();
		
		$this->opts_config = $this->get_options_config();
		
		$this->setup_processing();
		
		if( $this->draft->show_editor() ){
			add_action( 'wp_footer', array( &$this, 'json_blob' ) );
		}
			
			
		
		
	}
	
	function json_blob(){
		?>
		<script>
		
			!function ($) {
				
				$.pl = {
					data: {
						local:  <?php echo json_encode( pl_arrays_to_objects( $this->current_page_data('local') ) ); ?>
						, type:  <?php echo json_encode( pl_arrays_to_objects( $this->current_page_data('type') ) ); ?>
						, global:  <?php echo json_encode( pl_arrays_to_objects( $this->current_page_data('global') ) ); ?>
					}
					, map: {
						header: {}
						, footer: {}
						, template: {}
					}
					, flags: {
							refreshOnSave: false
						,	savingDialog: 'Saving'
						,	refreshingDialog: 'Success! Reloading page'
						,	layoutMode: '<?php echo $this->layout->get_layout_mode();?>'
					}
					, config: {
						userID: '<?php echo $this->get_user_id();?>'
						, currentURL: '<?php echo $this->current_url();?>'
						, nonce: '<?php echo wp_create_nonce( "tgmpa-install" ); ?>'
						, pageID: '<?php echo $this->page->id;?>'
						, typeID: '<?php echo $this->page->typeid;?>'
						, pageTypeID: '<?php echo $this->page->type;?>'
						, pageTypeName: '<?php echo $this->page->type_name;?>'
						, isSpecial: '<?php echo $this->page->is_special();?>'
						, opts: <?php echo json_encode( pl_arrays_to_objects( $this->get_options_config() ) ); ?>
						, settings: <?php echo json_encode( pl_arrays_to_objects( $this->siteset->get_set('site') ) ); ?>
						, areaSettings: <?php echo json_encode( pl_arrays_to_objects( $this->areas->settings() ) ); ?>
						, fonts: <?php echo json_encode( pl_arrays_to_objects( $this->foundry->get_foundry() ) ); ?>
						, menus: <?php echo json_encode( pl_arrays_to_objects( $this->get_wp_menus() ) ); ?>
						, extensions: <?php echo json_encode( pl_arrays_to_objects( $this->extensions->get_list() ) ); ?>
						, urls: {
							adminURL: '<?php echo admin_url(); ?>'
							, editPost: '<?php echo $this->edit_post_link(); ?>'
							, menus: '<?php echo admin_url( "nav-menus.php" );?>'
							, widgets: '<?php echo $this->edit_post_link();?>'
						}
					}
					
				}
				
			
			}(window.jQuery);
		</script>
		<?php
		
	}
	
	function current_url(){
		global $wp;
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		
		return $current_url;
	}
	

	function edit_post_link(){
		if($this->page->is_special())
			$url = admin_url( 'edit.php' );
		else 
			$url = get_edit_post_link( $this->page->id );
			
		return $url;
	}
	
	function get_wp_menus(){
		$menus = wp_get_nav_menus( array('orderby' => 'name') );
		return $menus;
	}
	
	function meta_defaults($key){
		
		$p = splice_section_slug($key);
		
		$defaults = array(
			'id'		=> $key,
			'object'	=> $key,
			'offset'	=> 0,
			'clone'		=> 0,  
			'content'	=> array(),
			'span'		=> 12,
			'newrow'	=> 'false',
			'set'		=> $this->optset->set
		);
		
		return $defaults;
	}
	
	function get_user_id(){
		$current_user = wp_get_current_user();
		return $current_user->ID;
	}
	
	function parse_config(){
		foreach($this->map as $group => &$g){
			
			if( !isset($g) || !is_array($g) )
				continue;
			
			foreach($g as $area => &$a){
				
				if( !isset($a['content']) || !is_array($a['content']) )
					continue;
				
				foreach($a['content'] as $key => &$meta){
				
					$meta = wp_parse_args($meta, $this->meta_defaults($key));
					
					if(!empty($meta['content'])){
						foreach($meta['content'] as $subkey => &$sub_meta){
							$sub_meta = wp_parse_args($sub_meta, $this->meta_defaults($subkey));
							$this->section_list[  ] = $sub_meta;
							$this->section_list_unique[$sub_meta['object']] = $sub_meta;
						}
						unset($sub_meta); // set by reference
					
						$this->section_list[  ] = $meta;
						$this->section_list_unique[ $meta['object'] ] = $meta;
					} else {
						$this->section_list[  ] = $meta;
						$this->section_list_unique[ $meta['object'] ] = $meta;
					}
						
				}
				unset($meta); // set by reference
			}
			unset($a); // set by reference
		}
	}
	
	function setup_processing(){
		
		global $pl_section_factory;
		
		foreach($this->section_list as $key => &$meta){
			
		//	$meta['set'] = $this->load_section_settings( $meta );
			
			if( $this->in_factory( $meta['object'] ) ){
				$this->factory[ $meta['object'] ]->meta = $meta;
				
			}else
				unset($this->section_list[$key]);
				
		}
		unset($meta);
		
				
	}
	
	function load_section_settings( $meta ){
		
		$settings = array(); 
		
		$sid = $meta['sid']; 
		$clone = $meta['clone'];
		
		foreach( $this->opts_config[ $sid ]['opts'] as $index => $o ){
			
			if( $o['type'] == 'multi' ){
				
				foreach( $o['opts'] as $sub_index => $sub_o ){
					$settings[ $sub_o['key'] ] = (  isset($sub_o['val'][$clone]) ) ? $sub_o['val'][$clone] : '';
				}
				
			} else {
				$settings[ $o['key'] ] = (  isset($o['val'][$clone]) ) ? $o['val'][$clone] : '';
			}
			
		}
		
		
		return $settings;
	}
	

	function get_options_config(){
		
		$opts_config = array();
		
		
		// BACKWARDS COMPATIBILITY
		add_action('override_metatab_register', array(&$this, 'get_opts_from_optionator'), 10, 2);
	
		foreach($this->section_list_unique as $key => $meta){

			if($this->in_factory( $meta['object'] )) {

				$s = $this->factory[ $meta['object'] ];
				
				$opts_config[ $s->id ] = array(
					'name'	=> $s->name
				);
				
				$opts = array();
				
				// Grab the options
				$opts = $s->section_opts(); 
				
				
				// Deal with special case flags... 
				if(is_array($opts)){
					foreach($opts as $index => $opt){
						if(isset($opt['case'])){
							// Special Page Only Option (e.g. used in post loop)
							if($opt['case'] == 'special' && !$this->page->is_special())
								unset($opts[$index]);
							
							if($opt['case'] == 'page' && !is_page())
								unset($opts[$index]);
							
							if($opt['case'] == 'post' && !is_post())
								unset($opts[$index]);
						}
					}
				}
				
				
				// For backwards compatibility with the older optionator format
				// It works by using a hook to hijack the 'register_metapanel' function
				// The hook then sets an attribute of this class to the array of options from the section
				if(!$opts || empty($opts)){
					
					$this->current_option_array = array();
				
					// backwards comp
					$s->section_optionator( array() );
				
					if(isset( $this->current_option_array ))
						$opts = process_to_new_option_format( $this->current_option_array ); 
						
					
				}
				
				
			
				// deals with legacy special stuff
				if(!empty($opts)){
					foreach($opts as $okey => &$o){
						if($o['type'] == 'multi'){
							foreach($o['opts'] as $okeysub => &$osub){
								if(!isset($osub['key']))
									$osub['key'] = $okeysub;
									
								$this->opts_list[] = $osub['key']; 
							}
							unset($osub); // set by reference
						} else {
							
							if(!isset($o['key']))
								$o['key'] = $okey;
								
							$this->opts_list[] = $o['key']; 
						}
					}
					unset($o); // set by reference
				}
				
				$opts_config[ $s->id ][ 'opts' ] = $opts; 
				
			}
			
			
		}

		remove_action('override_metatab_register', array(&$this, 'get_opts_from_optionator'), 10, 2);
		
		
		foreach($opts_config as $item => &$i){
			$i['opts'] = $this->opts_add_values( $i['opts'] );
		}
		unset($i);
		
		
		return $opts_config;
	}
	
	
	
	function opts_add_values( $opts ){
		
		
		foreach($opts as $index => &$o){
			
			if($o['type'] == 'multi'){
				$o['opts'] = $this->opts_add_values( $o['opts'] );
			} else {
				
				if($o['type'] == 'select_taxonomy'){
					
					$terms_array = get_terms( $o['taxonomy_id']); 
					
					if($o['taxonomy_id'] == 'category')
						$o['opts'][ '' ] = array('name' => '*Show All*');

					foreach($terms_array as $term){
						if(is_object($term))
							$o['opts'][ $term->slug ] = array('name' => $term->name);
					}
						
					
					$o['type'] = 'select'; 
					
				}
				
				// Add the value
				$o['val'] = ( isset($this->optset->set[ $o['key'] ]) ) ? $this->optset->set[ $o['key'] ] : array();
				
			}
				
		}
		unset($o); 
		
		return $opts;
	}
	

		
	function get_opts_from_optionator( $array ){
		
		$this->current_option_array = $array;
		
	}	
		
	
		
	function current_page_data( $scope = 'local' ){
		$d = array();
		
		if($scope == 'local'){
			
			$d = pl_settings( $this->draft->mode, $this->page->id );
			
			// ** Backwards Compatible Stuff ** //
			if(!is_pagelines_special()){
				foreach($this->opts_list as $key => $opt){

					$val = plmeta( $opt, array('post_id' => $this->page->id) );

					if( !isset($d[ $opt ]) && $val != '')
						$d[$opt] = array( pl_html($val) );
				}
			}
			
		} elseif($scope == 'type'){
			
			$d = pl_settings( $this->draft->mode, $this->page->typeid );
			
			// ** Backwards Compatible Stuff **
			$old_special = get_option('pagelines-special');

			if( isset( $old_special[ $this->page->type ] ) ){
				foreach($this->opts_list as $key => $opt){

					if( !isset($d[ $opt ]) && isset($old_special[ $this->page->type ][ $opt ]) && !empty($old_special[ $this->page->type ][ $opt ]) )
						$d[$opt] = array( pl_html($old_special[ $this->page->type ][ $opt ])); 

				}
			}
			
		} else {
			
			$d = pl_settings( $this->draft->mode );
			
			// ** Backwards Compatible Stuff **
			$old_special = get_option('pagelines-special');
		
			if( isset( $old_special[ 'default' ] ) ){
				foreach($this->opts_list as $key => $opt){

					if(!isset($d[ $opt ]) && isset($old_special[ 'default' ][ $opt ]) && !empty($old_special[ 'default' ][ $opt ]) )
						$d[ $opt ] = array( pl_html($old_special[ 'default' ][ $opt ]) ); 

				}
			}
			
		}
	
			
		return ($d) ? $d : array();
	}
	

	


	

	
	function process_styles(){
		
		
		
		/*
			TODO add !has_action('override_pagelines_css_output')
		*/
		foreach($this->section_list as $key => $meta){

			if($this->in_factory( $meta['object'] )) {

				$s = $this->factory[ $meta['object'] ];
				
				$s->meta = $meta;
				
				$s->section_styles();
				
				// Auto load style.css for simplicity if its there.
				if( is_file( $s->base_dir . '/style.css' ) ){

					wp_register_style( $s->id, $s->base_url . '/style.css', array(), $s->settings['p_ver'], 'screen');
			 		wp_enqueue_style( $s->id );

				}
			}	
		}
	}
	
	function process_head(){
		
		
		foreach($this->section_list as $key => $meta){
		
			if( $this->in_factory( $meta['object'] ) ){

				$s = $this->factory[ $meta['object'] ];
				
				$s->meta = $meta;
				
				$s->setup_oset( $meta['clone'] ); // refactor

				ob_start();

					$s->section_head( $meta['clone'] );	

				$head = ob_get_clean();

				if($head != '')
					echo pl_source_comment($s->name.' | Section Head') . $head;
				

			}	
		}
	}
	
	function process_region( $region = 'template' ){
		
		if(!isset($this->map[ $region ]))
			return;
		
		if(pl_draft_mode())
			$this->editor->region_start( $region, $this->area_number++ );
		
		
		if( is_array( $this->map[ $region ] ) ){
			foreach( $this->map[ $region ] as $area => $a ){

				$a['area_number'] = $this->area_number++; 

				$this->areas->area_start($a);

				if( isset($a['content']) && !empty($a['content'])){

					$section_count = 0;
					$sections_total = count($a['content']); 

					foreach($a['content'] as $key => $meta){

						$section_count++;
						$this->render_section( $meta, $section_count, $sections_total );

					}

				}

				$this->areas->area_end($a);

			}
		}
		
	}
	
	function render_section( $meta, $count, $total, $level = 1 ){
		
		if( $this->in_factory( $meta['object'] ) ){
			
			$s = $this->factory[ $meta['object'] ];

			$s->meta = $meta;

			$s->setup_oset( $meta['clone'] ); // refactor
			
			ob_start();

				$this->section_template_load( $s ); // Check if in child theme, if not load section_template

			$output =  ob_get_clean(); // Load in buffer, so we can check if empty
		
		
			$render = (!isset($output) || $output == '') ? false : true;
			
			$this->grid_row_start( $s, $count, $total, $render, $level );
			
			if( $render ){
				
				$s->before_section_template( );
				
				$this->before_section( $s );

				echo $output;

				$this->after_section( $s );
				
				$s->after_section_template( );
			}
			
			$this->grid_row_stop( $s, $count, $total, $render, $level );
			
	
		
			wp_reset_postdata(); // Reset $post data
			wp_reset_query(); // Reset wp_query
			
		}
		
	}
	
	function grid_row_start( $s, $count, $total, $render = true, $level = 1 ){
	
		if( $this->draft->show_editor() )
			return;
	
		if( !isset($this->row_width[ $level ]) ){
			$this->row_width[ $level ] = 0;
		}
			
		
		if( $count == 1 ){
		
			$this->row_width[ $level ] = 0;
			printf('<div class="row grid-row">');
		}
		
		if( $render ){
			
			$section_width = $s->meta['span'] + $s->meta['offset']; 
			
			$this->row_width[ $level ] +=  $section_width;
			

			if( $this->row_width[ $level ] > 12 || $s->meta['newrow'] == 'true' ){
					
				$this->row_width[ $level ] = $section_width;
				
				printf('</div>%s<div class="row grid-row">', "\n\n");
			}
			
		}	
	
		
	}
	
	function grid_row_stop( $s, $count, $total, $render, $level = 1 ){
		
		if($this->draft->show_editor())
			return;
			
		if( $count == $total ){
			$this->row_width[ $level ] = 0;
			printf('</div>');
		}	
	}
	
	function before_section( $s ){
		
		echo pl_source_comment($s->name . ' | Section Template', 2); // Add Comment 	
			
		pagelines_register_hook('pagelines_before_'.$s->id, $s->id); // hook
		
		// Rename to prevent conflicts
		// TODO remove this or check to remove this strange non-algorhythmic code
		if ( 'comments' == $s->id )
			$sid = 'wp-comments';
		elseif ( 'content' == $s->id )
			$sid = 'content-area';
		else
			$sid = $s->id;
		

		$span 	= (isset($s->meta['span'])) ? sprintf('span%s', $s->meta['span']) : 'span12';
		$offset = (isset($s->meta['offset'])) ? sprintf('offset%s', $s->meta['offset']) : 'offset0';
		$newrow = ( $s->meta['newrow'] == 'true' ) ? 'force-start-row' : '';
		$clone 	= $s->meta['clone'];
		
		$class[] = sprintf("pl-section section-%s", $sid);
		$class[] = $span;
		$class[] = $offset;
		$class[] = $newrow;
		$class = array_merge($class, $s->wrapper_classes); 
		
		printf(
			'<section id="%s" data-object="%s" data-sid="%s" data-clone="%s" class="%s">%s<div class="pl-section-pad fix">', 
			$s->id.$clone, 
			$s->class_name,
			$s->id, 
			$clone, 
			implode(" ", $class), 
			$this->editor->section_controls( $s )
		);

		pagelines_register_hook('pagelines_outer_'.$s->id, $s->id); // hook
		pagelines_register_hook('pagelines_inside_top_'.$s->id, $s->id); // hook 
		
 	}

	function after_section( $s ){
		
		pagelines_register_hook('pagelines_inside_bottom_'.$s->id, $s->id);
	 	
		printf('</div></section>');

		pagelines_register_hook('pagelines_after_'.$s->id, $s->id);
	}

	function section_template_load( $s ) {
		
		// Variables for override
		$override_template = 'template.' . $s->id .'.php';
		$override = ( '' != locate_template(array( $override_template), false, false)) ? locate_template(array( $override_template )) : false;

		if( $override != false) 
			require( $override );
		else
			$s->section_template( $s->meta['clone'] );
		
	}
		
	/**
	 * Tests if the section is in the factory singleton
	 */
	function in_factory( $section ){	
		return ( isset($this->factory[ $section ]) && is_object($this->factory[ $section ]) ) ? true : false;
	}	
	
}


/**
 * For use inside of sections
 */
function render_nested_sections( $sections ){

	global $pagelines_editor;

	if( !empty( $sections ) ){

		$section_count = 0;
		$sections_total = count($sections);

		foreach( $sections as $key => $meta )
			$pagelines_editor->handler->render_section( $meta, ++$section_count, $sections_total, 2);

	}

}

