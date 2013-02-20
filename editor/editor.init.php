<?php
/**
 * This file initializes the PageLines Editor
 *
 * @package PageLines Framework
 * @since 3.0.0
 *
 */

// return;

// Make sure user can handle this.
if (!defined('PL_UX_DEV') || !PL_UX_DEV)
	return;


$pagelines_editor = new PageLinesEditor; 

class PageLinesEditor {

	function __construct() {


		$this->load_files();
		
		// TEMPLATE ACTIONS
		
		add_action('wp', array(&$this, 'load_libs' ));
		
		add_action('wp_enqueue_scripts', array(&$this, 'process_styles' ));
		add_action( 'wp_head', array(&$this, 'process_head' ) );
		
		// RENDER ACTIONS
		add_action( 'pagelines_header', array(&$this, 'process_header' ) );
		add_action( 'pagelines_template', array(&$this, 'process_template' ) );
		add_action( 'pagelines_footer', array(&$this, 'process_footer' ) );
		
		add_action( 'wp_ajax_pl_save_pagebuilder', array(&$this, 'save_configuration_callback' ));

	
	}
	
	function load_files(){
		require_once( PL_EDITOR . '/editor.settings.php' );
		require_once( PL_EDITOR . '/editor.actions.php' );
		require_once( PL_EDITOR . '/editor.draft.php' );
		require_once( PL_EDITOR . '/editor.layout.php' );
		require_once( PL_EDITOR . '/editor.map.php' );
		require_once( PL_EDITOR . '/editor.templates.php' );
		require_once( PL_EDITOR . '/editor.data.php' );
		
		
		require_once( PL_EDITOR . '/editor.settings.config.php' );
		require_once( PL_EDITOR . '/editor.typography.php' );
		require_once( PL_EDITOR . '/editor.color.php' );
			
		require_once( PL_EDITOR . '/editor.interface.php' );
		require_once( PL_EDITOR . '/editor.page.php' );
		require_once( PL_EDITOR . '/editor.pagetype.php' );
		require_once( PL_EDITOR . '/editor.handler.php' );
		require_once( PL_EDITOR . '/editor.less.php' );
		require_once( PL_EDITOR . '/editor.library.php' );
	}
	
	function load_libs(){
		global $plpg; 
		global $pldraft;
		global $plopts;
		
		$plpg = $this->page = new PageLinesPage;
		$pldraft = $this->draft = new EditorDraft( $this->page );
		$plopts = $this->opts = new PageLinesOpts( $this->page, $this->draft );
		
		$this->layout = new EditorLayout();
		$this->map = new EditorMap( $this->draft );
		$this->templates = new EditorTemplates( $this->page );
		$this->siteset = new EditorSettings;
		$this->foundry = new PageLinesFoundry;
		$this->typography = new EditorTypography( $this->foundry );
		$this->color = new EditorColor;
		
		$this->interface = new EditorInterface( $this->page, $this->siteset, $this->draft, $this->templates, $this->map );
		
		$this->handler = new PageLinesTemplateHandler( $this->interface, $this->page, $this->siteset, $this->foundry, $this->map, $this->draft, $this->opts, $this->layout );
		
	}
	
	function process_styles(){

		
		pagelines_add_bodyclass('pl-editor');
		
		$pless = new PageLinesLess;
		$this->editor_less = new EditorLess($pless);
		
		$this->editor_less->enqueue_styles();
		
		$this->handler->process_styles();
	}
	
	function process_head(){
		$this->handler->process_head();
	}
	
	function process_header(){
		$this->handler->process_region('header');
	}
	function process_template(){
		$this->handler->process_region('template');
	}
	function process_footer(){
		$this->handler->process_region('footer');
	}

	function save_configuration_callback(){
		echo 'worked!';
	}
			
}
