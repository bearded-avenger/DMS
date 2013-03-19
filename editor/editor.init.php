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
		
		
		require_once( PL_EDITOR . '/editor.data.php' );

		require_once( PL_EDITOR . '/editor.settings.config.php' );
		require_once( PL_EDITOR . '/editor.typography.php' );
		require_once( PL_EDITOR . '/editor.color.php' );

		// Interfaces
		require_once( PL_EDITOR . '/editor.xlist.php' );
		require_once( PL_EDITOR . '/panel.code.php' );
		require_once( PL_EDITOR . '/panel.live.php' );
		require_once( PL_EDITOR . '/panel.sections.php' );
		require_once( PL_EDITOR . '/panel.extend.php' );
		require_once( PL_EDITOR . '/panel.themes.php' );
		require_once( PL_EDITOR . '/panel.templates.php' );

		require_once( PL_EDITOR . '/editor.extensions.php' );
		require_once( PL_EDITOR . '/editor.interface.php' );
		require_once( PL_EDITOR . '/editor.page.php' );
		require_once( PL_EDITOR . '/editor.handler.php' );
		require_once( PL_EDITOR . '/editor.less.libs.php' );
		require_once( PL_EDITOR . '/editor.less.php' );
		require_once( PL_EDITOR . '/editor.api.php' );

	}

	function load_libs(){
		global $plpg;
		global $pldraft;
		global $plopts;
		global $editorless;
		global $storeapi;

		$plpg = $this->page = new PageLinesPage;
		$pldraft = $this->draft = new EditorDraft( $this->page );
//		$editorless = $this->editorless = new EditorLessHandler;
		$storeapi = $this->storeapi = new EditorStoreFront;
		$this->layout = new EditorLayout();
		
		$this->templates = new EditorTemplates( $this->page );
		$this->map = new EditorMap( $this->templates, $this->draft);

		// Must come before settings
		$this->foundry = new PageLinesFoundry;
		$this->typography = new EditorTypography( $this->foundry );
		$this->color = new EditorColor;
		$this->siteset = new EditorSettings;
		$this->extensions = new EditorExtensions;
		$pless = new PageLinesLess;
		$this->editor_less = new EditorLess($pless);
		pagelines_register_hook('pl_after_settings_load'); // hook

		$plopts = $this->opts = new PageLinesOpts( $this->page, $this->draft );
		
		// Interfaces
		$this->xlist = new EditorXList;
		$this->add_sections = new PageLinesSectionsPanel;
		$this->extend_panel = new PageLinesExtendPanel;
		$this->live_panel = new PageLinesLivePanel;
		$this->themer = new EditorThemeHandler;
		
		$this->code = new EditorCode( $this->draft );
		
		// Editor UX Elements
		$this->interface = new EditorInterface( $this->page, $this->siteset, $this->draft, $this->templates, $this->map, $this->extensions, $this->themer );

		// Master UX Handler
		$this->handler = new PageLinesTemplateHandler( $this->interface, $this->page, $this->siteset, $this->foundry, $this->map, $this->draft, $this->opts, $this->layout, $this->extensions );

	}

	function process_styles(){


		pagelines_add_bodyclass('pl-editor');



//		$this->editor_less->enqueue_styles();

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
