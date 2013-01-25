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

	function __construct( EditorInterface $interface, PageLinesPage $pg, EditorSettings $siteset, PageLinesFoundry $foundry, EditorMap $map) {


		global $pl_section_factory; 
		
		$this->factory = $pl_section_factory->sections; 
		
		// Dependancy Injection (^^)
		$this->editor = $interface;
		$this->page = $pg;
		$this->siteset = $siteset;
		$this->foundry = $foundry;
		
		$this->map = $map->get_map( $this->page );

		$this->parse_config();
		
		$this->setup_processing();
		
		$this->get_options_config();
		
		add_action( 'pagelines_head_last', array( &$this, 'json_data' ) );
		
	}
	
	function json_data(){
		
	
		
		?>
		<script>
		
			!function ($) {
				
				$.pl = {
					data: {
						local:  <?php echo json_encode($this->current_page_data('local'), JSON_FORCE_OBJECT); ?>
						, type:  <?php echo json_encode($this->current_page_data('type'), JSON_FORCE_OBJECT); ?>
						, global:  <?php echo json_encode($this->current_page_data('global'), JSON_FORCE_OBJECT); ?>
					}
					, map: {
						header: {}
						, footer: {}
						, template: {}
					}
					, flags: {
						refreshOnSave: false
					}
					, config: {
						pageID: '<?php echo $this->page->id;?>'
						, typeID: '<?php echo $this->page->typeid;?>'
						, pageTypeID: '<?php echo $this->page->type;?>'
						, pageTypeName: '<?php echo $this->page->type_name;?>'
						, isSpecial: '<?php echo $this->page->is_special();?>'
						, opts: <?php echo json_encode($this->get_options_config(), JSON_FORCE_OBJECT); ?>
						, settings: <?php echo json_encode($this->siteset->get_set('site'), JSON_FORCE_OBJECT); ?>
						, fonts: <?php echo json_encode($this->foundry->get_foundry(), JSON_FORCE_OBJECT); ?>
					}
					, extend: <?php echo json_encode( store_mixed_array(), JSON_FORCE_OBJECT); ?>
					
				}
				
			
			}(window.jQuery);
		</script>
		
		<style id="pl-custom-less" type="text/less"></style>
		
		<?php
		
	}
	
	function get_site_settings(){ }
	
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
				
				$opts = $s->section_opts(); 
				
				// For backwards compatibility with the older optionator format
				// It works by using a hook to hijack the 'register_metapanel' function
				// The hook then sets an attribute of this class to the array of options from the section
				if(!$opts || empty($opts)){
					
					$this->current_option_array = array();
				
					// backwards comp
					$s->section_optionator( array() );
				
					if(isset( $this->current_option_array ))
						$opts = $this->process_to_new_option_format( $this->current_option_array ); 
						
					
				}
				
				$opts_config[ $s->id ][ 'opts' ] = $opts; 
				
				if(!empty($opts)){
					foreach($opts as $okey => $o){
						if($o['type'] == 'multi'){
							foreach($o['opts'] as $okeysub => $osub){
								$this->opts_list[] = $osub['key']; 
							}
						} else {
							$this->opts_list[] = $o['key']; 
						}
					}
				}
				
			}
			
			
		}

		remove_action('override_metatab_register', array(&$this, 'get_opts_from_optionator'), 10, 2);
		
		return $opts_config;
	}
	
	function process_to_new_option_format( $old_options ){
		
		$new_options = array();
		
		foreach($old_options as $key => $o){
			
			if($o['type'] == 'multi_option' || $o['type'] == 'text_multi'){
			
				$sub_options = array();
				foreach($o['selectvalues'] as $sub_key => $sub_o){
					$sub_options[ ] = $this->process_old_opt($sub_key, $sub_o, $o); 
				}
				$new_options[ ] = array(
					'type' 	=> 'multi', 
					'title'	=> $o['title'],
					'opts'	=> $sub_options
				);
			} else {
				$new_options[ ] = $this->process_old_opt($key, $o);	
			}
			
		}
		
		return $new_options;
	}
	
	function process_old_opt( $key, $old, $otop = array()){
		
		if(isset($otop['type']) && $otop['type'] == 'text_multi')
			$old['type'] = 'text'; 
			
		$defaults = array(
            'type' 			=> 'check',
			'title'			=> '',
			'inputlabel'	=> '', 
			'exp'			=> '', 
			'shortexp'		=> '',
			'selectvalues'	=> array()
		);
		
		$old = wp_parse_args($old, $defaults);
		
		$exp = ($old['exp'] == '' && $old['shortexp'] != '') ? $old['shortexp'] : $old['exp'];
		
		if($old['type'] == 'text_small'){
			$type = 'text'; 
		} else 
			$type = $old['type'];
		
		$new = array(
			'key'	=> $key, 
			'title'	=> $old['title'],
			'label'	=> $old['inputlabel'], 
			'type'	=> $type, 
			'help'	=> $exp, 
			'opts'	=> $old['selectvalues']
		); 
		return $new;
	}
		
	function get_opts_from_optionator( $array ){
		
		$this->current_option_array = $array;
		
	}	
		
	
		
	function current_page_data( $scope = 'local' ){
		$d = array();
		
		if($scope == 'local'){
			
			// ** Backwards Compatible Stuff ** //
			if(!is_pagelines_special()){
				foreach($this->opts_list as $key => $opt){

					$val = plmeta( $opt, array('post_id' => $this->page->id) );

					if($val != '')
						$d[$opt] = array( pl_html($val) );
				}
			}
			
		} elseif($scope == 'type'){
			
			
			// ** Backwards Compatible Stuff **
			$old_special = get_option('pagelines-special');

			if( isset( $old_special[ $this->page->type ] ) ){
				foreach($this->opts_list as $key => $opt){

					if(isset($old_special[ $this->page->type ][ $opt ]) && !empty($old_special[ $this->page->type ][ $opt ]) )
						$d[$opt] = array( pl_html($old_special[ $this->page->type ][ $opt ])); 

				}
			}
			
		} else {
			
			// ** Backwards Compatible Stuff **
			$old_special = get_option('pagelines-special');
		
			if( isset( $old_special[ 'default' ] ) ){
				foreach($this->opts_list as $key => $opt){

					if(isset($old_special[ 'default' ][ $opt ]) && !empty($old_special[ 'default' ][ $opt ]) )
						$d[$opt] = array( pl_html($old_special[ 'default' ][ $opt ]) ); 

				}
			}
			
		}
		
		
		
		
		
		
		return $d;
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
			'newrow'	=> 'false'
		);
		
		return $defaults;
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
						$this->section_list_unique[$meta['object']] = $meta;
					}else{
						$this->section_list[  ] = $meta;
						$this->section_list_unique[$meta['object']] = $meta;
					}
						
				}
				unset($meta); // set by reference
			}
			unset($a); // set by reference
		}
	}
	
	function setup_processing(){
		
		global $pl_section_factory;
		
		foreach($this->section_list as $key => $meta){
			
			if( $this->in_factory( $meta['object'] ) ){
				$this->factory[ $meta['object'] ]->meta = $meta;
			}else
				unset($this->section_list[$key]);
				
		}
				
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
		
		$this->editor->region_start( $region, $this->area_number++ );
		
		foreach( $this->map[ $region ] as $area => $a ){
			
			$a['area_number'] = $this->area_number++; 
			
			$this->editor->area_start($a);
			
			if( isset($a['content']) ){
				
				foreach($a['content'] as $key => $meta){

					$this->render_section( $meta );

				}
				
			}

			$this->editor->area_end($a);
			
		}
	}
	
	function render_section( $meta ){
		
		if( $this->in_factory( $meta['object'] ) ){
			
			$s = $this->factory[ $meta['object'] ];

			$s->meta = $meta;

			$s->setup_oset( $meta['clone'] ); // refactor
			
			ob_start();

				$this->section_template_load( $s ); // Check if in child theme, if not load section_template

			$output =  ob_get_clean(); // Load in buffer, so we can check if empty
		
			if(isset($output) && $output != ''){
				
				echo pl_source_comment($s->name . ' | Section Template', 2); // Add Comment 
				
				$this->before_section( $s );

				$this->editor->section_controls($meta['id'], $s);

				echo $output;

				$this->after_section( $s );
				
			}
		
			wp_reset_postdata(); // Reset $post data
			wp_reset_query(); // Reset wp_query
			
		}
		
	}
	
	function before_section( $s ){
			
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
		
		$class[] = sprintf("pl-section fix section-%s", $sid);
		$class[] = $span;
		$class[] = $offset;
		$class[] = $newrow;
		
		printf(
			'<section id="%s" data-object="%s" data-sid="%s" data-clone="%s" class="%s">', 
			$s->id.$clone, 
			$s->class_name,
			$s->id, 
			$clone, 
			implode(" ", $class)
		);

		pagelines_register_hook('pagelines_outer_'.$s->id, $s->id); // hook
		pagelines_register_hook('pagelines_inside_top_'.$s->id, $s->id); // hook 
		
 	}

	function after_section( $s ){
		
		pagelines_register_hook('pagelines_inside_bottom_'.$s->id, $s->id);
	 	
		printf('</section>');

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

		foreach( $sections as $key => $meta )
			$pagelines_editor->handler->render_section( $meta );

	}

}

