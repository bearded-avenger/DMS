<?php

/*
 * Less handler needs to compile live on 'publish' and load in the header of the website
 *
 * It needs to grab variables (or create a filter) that can be added by settings, etc.. (typography)

 * Inline LESS will be used to handle draft mode, and previewing of changes.

 */

class EditorLessHandler{

	var $pless_vars = array();
	var $draft;

	/**
	 *
	 *  Draft mode init.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	public function draft_init(){
		// if we are in banana mode fire up the flux capacitors.
			global $pagelines_template;
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_draft_css' ) );
			add_action( 'wp_print_styles', array( &$this, 'dequeue_live_css' ), 12 );
			add_action( 'template_redirect', array( &$this, 'pagelines_draft_render' ) , 15);
			add_action( 'pl_scripts_on_ready', array( &$pagelines_template, 'print_on_ready_scripts' ), 12 );
	}

	/**
	 *
	 *  Dequeue the regular css.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	static function dequeue_live_css() {
		wp_deregister_style( 'pagelines-less' );
	}

	/**
	 *
	 * Enqueue special draft css.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	static function enqueue_draft_css() {
		wp_register_style( 'pagelines-draft',  sprintf( '%s/?pagedraft=1', site_url() ), false, null, 'all' );
		wp_enqueue_style( 'pagelines-draft' );
	}

	/**
	 *
	 *  Get all less files as an array.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	public function get_core_lessfiles(){

		$files[] = 'reset';

		if(pl_has_editor()){
			$files[] = 'pl-structure';
			$files[] = 'pl-editor';
		} else {

			$files[] = 'pl-core';
			$files[] = 'deprecated';
		}

		$bootstrap = array(
			'pl-wordpress',
			'pl-plugins',
			'grid',
			'alerts',
			'labels-badges',
			'tooltip-popover',
			'buttons',
			'typography',
			'dropdowns',
			'accordion',
			'carousel',
			'navs',
			'modals',
			'thumbnails',
			'component-animations',
			'utilities',
			'pl-objects',
			'pl-tables',
			'wells',
			'forms',
			'breadcrumbs',
			'close',
			'pager',
			'pagination',
			'progress-bars',
			'icons',
			'responsive'
		);

		return array_merge($files, $bootstrap);
	}

	/**
	 *
	 *  Build our 'data' for compile.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	public function draft_core_data() {

			build_pagelines_layout();

			$data = $this->get_dynamic_css();
			$data['sections'] = $this->get_all_active_sections();
			$data['core'] = $this->get_core_lesscode();

			return $data;
	}

	/**
	 *
	 *  Main draft function.
	 *  Fetches data from a cache and compiles befor returning to EditorLess.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	public function get_draft_core() {

		$raw				= pl_cache_get( 'draft_core_raw', array( &$this, 'draft_core_data' ) );
		$compiled_core		= pl_cache_get( 'draft_core_compiled', array( &$this, 'compile' ), array( $raw['core'] ) );
		$compiled_sections	= pl_cache_get( 'draft_sections_compiled', array( &$this, 'compile' ), array( $raw['sections'] ) );

		return array(
			'type'	=> $raw['type'],
			'dynamic'	=> $raw['dynamic'],
			'compiled_core'	=> $compiled_core,
			'compiled_sections'	=> $compiled_sections,
			);
	}

	/**
	 *
	 *  Compare Less
	 *  If PL_LESS_DEV is active compare cached draft with raw less, if different purge cache, this fires before the less is compiled.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	function compare_less() {

		$flush = false;

		if(pl_has_editor()){

			$cached_constants = (array) pl_cache_get('pagelines_less_vars' );

			$diff = array_diff( $this->pless->constants, $cached_constants );

			if( ! empty( $diff ) ){

plprint( $diff );
				// cache new constants version
				pl_cache_put( $this->pless->constants, 'pagelines_less_vars');

				// force recompile
				$flush = true;
			}
		}


		$raw_cached = pl_cache_get( 'draft_core_raw', array( &$this, 'draft_core_data' ) );

		// check if a cache exists. If not dont bother carrying on.
		if( ! isset( $raw_cached['core'] ) )
			return;

		// Load all the less. Not compiled.
		$raw = $this->draft_core_data();

		if( $raw_cached['core'] != $raw['core'] )
			$flush = true;

		if( $raw_cached['dynamic'] != $raw['dynamic'] )
			$flush = true;

		if( $raw_cached['sections'] != $raw['sections'] )
			$flush = true;

		if( true == $flush )
			pl_flush_draft_caches();
	}

	/**
	 *
	 *  Get all core less as uncompiled code.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 *  @uses  load_core_cssfiles
	 */
	private function get_core_lesscode() {

		return $this->load_core_cssfiles( apply_filters( 'pagelines_core_less_files', $this->lessfiles ) );
	}

	/**
	 *
	 *  Load from .less files.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 *  @uses  load_less_file
	 */
	private function load_core_cssfiles( $files ) {

		$code = '';
		foreach( $files as $less ) {

			$code .= $this->load_less_file( $less );
		}
		return apply_filters( 'pagelines_insert_core_less', $code );
	}

	/**
	 *
	 *  Fetch less file from theme folders.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	private function load_less_file( $file ) {

		$file 	= sprintf( '%s.less', $file );
		$parent = sprintf( '%s/%s', PL_CORE_LESS, $file );
		$child 	= sprintf( '%s/%s', PL_CHILD_LESS, $file );

		// check for child 1st if not load the main file.

		if ( is_file( $child ) )
			return pl_file_get_contents( $child );
		else
			return pl_file_get_contents( $parent );
	}

	/**
	 *
	 *  Fetch dynamic and typography css.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	private function get_dynamic_css(){

		$pagelines_dynamic_css = new PageLinesCSS;

		$pagelines_dynamic_css->typography();

		$typography = $pagelines_dynamic_css->css;

		unset( $pagelines_dynamic_css->css );
		$pagelines_dynamic_css->layout();
		$pagelines_dynamic_css->options();

		$out = array(
			'type'		=>	$typography,
			'dynamic'	=>	apply_filters('pl-dynamic-css', $pagelines_dynamic_css->css)
		);
		return $out;
	}

	/**
	 *
	 *  Compile less into css.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	public function compile( $data ) {
		do_action( 'pagelines_max_mem' );
		return $this->pless->raw_less( $data );
	}

	/**
	 *
	 *  Simple minify.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	public function minify( $css ) {
		if( is_pl_debug() )
			return $css;

		if( ! ploption( 'pl_minify') )
			return $css;

		$data = $css;

	    $data = preg_replace( '#/\*.*?\*/#s', '', $data );
	    // remove new lines \\n, tabs and \\r
	    $data = preg_replace('/(\t|\r|\n)/', '', $data);
	    // replace multi spaces with singles
	    $data = preg_replace('/(\s+)/', ' ', $data);
	    //Remove empty rules
	    $data = preg_replace('/[^}{]+{\s?}/', '', $data);
	    // Remove whitespace around selectors and braces
	    $data = preg_replace('/\s*{\s*/', '{', $data);
	    // Remove whitespace at end of rule
	    $data = preg_replace('/\s*}\s*/', '}', $data);
	    // Just for clarity, make every rules 1 line tall
	    $data = preg_replace('/}/', "}\n", $data);
	    $data = str_replace( ';}', '}', $data );
	    $data = str_replace( ', ', ',', $data );
	    $data = str_replace( '; ', ';', $data );
	    $data = str_replace( ': ', ':', $data );
	    $data = preg_replace( '#\s+#', ' ', $data );

		if ( ! preg_last_error() )
			return $data;
		else
			return $css;
	}

	/**
	 *
	 *  Get all active sections.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	private function get_all_active_sections() {

		$out = '';
		global $load_sections;
		$available = $load_sections->pagelines_register_sections( true, true );

		$disabled = get_option( 'pagelines_sections_disabled', array() );

		/*
		* Filter out disabled sections
		*/
		foreach( $disabled as $type => $data )
			if ( isset( $disabled[$type] ) )
				foreach( $data as $class => $state )
					unset( $available[$type][ $class ] );

		/*
		* We need to reorder the array so sections css is loaded in the right order.
		* Core, then pagelines-sections, followed by anything else.
		*/
		$sections = array();
		$sections['parent'] = $available['parent'];
		unset( $available['parent'] );
		$sections['child'] = (array) $available['child'];
		unset( $available['child'] );
		if ( is_array( $available ) )
			$sections = array_merge( $sections, $available );
		foreach( $sections as $t ) {
			foreach( $t as $key => $data ) {
				if ( $data['less'] && $data['loadme'] ) {
					if ( is_file( $data['base_dir'] . '/style.less' ) )
						$out .= pl_file_get_contents( $data['base_dir'] . '/style.less' );
					elseif( is_file( $data['base_dir'] . '/color.less' ))
						$out .= pl_file_get_contents( $data['base_dir'] . '/color.less' );
				}
			}
		}
		return apply_filters('pagelines_lesscode', $out);
	}

	/**
	 *
	 *  Move googlefont code to top of compiled css.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	public function googlefont_replace( $data ) {

		$gfonts = preg_match( '#(@import[^;]*;)#', $data['type'], $g );
		if ( $gfonts ) {
			$data['compiled_core'] = sprintf( "%s\n%s", $g[1], $data['compiled_core'] );
			$data['type'] = str_replace( $g[1], '', $data['type'] );
		}
		return $data;
	}

	/**
	 *
	 *  Are we in draft mode or not?
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 *  @uses  $pldraft
	 */
	public static function is_draft() {
		global $pldraft;

		if( ! is_object( $pldraft ) )
			return false;

		$draft = $pldraft->mode;
		return ( 'draft' == $draft ) ? true : false;
	}
}