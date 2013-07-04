<?php
/**
 * This file initializes the PageLines Editor
 *
 * @package PageLines Framework
 * @since 3.0.0
 *
 */


$pagelines_editor = new PageLinesEditor;

class PageLinesEditor {

	function __construct() {

		$this->load_files();

		// TEMPLATE ACTIONS
		add_action( 'wp', array(&$this, 'load_libs' ), 5); // !important - must load after $post variable
		add_action( 'admin_init', array(&$this, 'load_libs' ), 5);

		if( pl_draft_mode() || is_admin() )
			add_action( 'init', array(&$this, 'installer_scripts' ), 9 );

		add_action('wp_enqueue_scripts', array(&$this, 'process_styles' ));
		add_action( 'wp_head', array(&$this, 'process_head' ) );

		// RENDER ACTIONS
		add_action( 'pagelines_header', array(&$this, 'process_header' ) );
		add_action( 'pagelines_template', array(&$this, 'process_template' ) );
		add_action( 'pagelines_footer', array(&$this, 'process_footer' ) );



	}

	function load_files(){
		require_once( PL_EDITOR . '/editor.premium.php' );
		require_once( PL_EDITOR . '/editor.admin.php' ); // admin stuff
		
		require_once( PL_EDITOR . '/editor.settings.php' );
		require_once( PL_EDITOR . '/editor.actions.php' );
		require_once( PL_EDITOR . '/editor.draft.php' );
		require_once( PL_EDITOR . '/editor.layout.php' );

		require_once( PL_EDITOR . '/editor.settings.config.php' );
		require_once( PL_EDITOR . '/editor.typography.php' );
		require_once( PL_EDITOR . '/editor.importexport.php' );
		require_once( PL_EDITOR . '/editor.color.php' );
		
		require_once( PL_EDITOR . '/editor.templates.php' );

		// Mobile
		require_once( PL_EDITOR . '/mobile.menu.php' );

		// Interfaces
		require_once( PL_EDITOR . '/editor.xlist.php' );
		require_once( PL_EDITOR . '/panel.code.php' );
		//require_once( PL_EDITOR . '/panel.live.php' );
		require_once( PL_EDITOR . '/editor.account.php' );
		require_once( PL_EDITOR . '/panel.sections.php' );
		require_once( PL_EDITOR . '/panel.extend.php' );
		require_once( PL_EDITOR . '/panel.themes.php' );
		
		require_once( PL_EDITOR . '/panel.settings.php' );

		require_once( PL_EDITOR . '/editor.extensions.php' );
		require_once( PL_EDITOR . '/editor.interface.php' );
		require_once( PL_EDITOR . '/editor.integrations.php' );
		require_once( PL_EDITOR . '/editor.regions.php' );
		require_once( PL_EDITOR . '/editor.areas.php' );
		require_once( PL_EDITOR . '/editor.page.php' );
		require_once( PL_EDITOR . '/editor.handler.php' );
		require_once( PL_EDITOR . '/editor.less.libs.php' );
		require_once( PL_EDITOR . '/editor.less.php' );
		require_once( PL_EDITOR . '/editor.api.php' );
		require_once( PL_EDITOR . '/editor.fileopts.php' );
	}
	function installer_scripts() {

		require_once (PL_EDITOR . '/editor.installer.libs.php');
		require_once( PL_EDITOR . '/editor.installer.php' );

		new Editor_Plugin_Installer;
	}

	function load_libs(){

		if(!pl_use_editor())
			return;

		global $plpg;
		global $pldraft;
		global $plopts;
		global $editorless;
		global $storeapi;
		global $fileopts;
		
		$plpg = $this->page = new PageLinesPage;
		$pldraft = $this->draft = new EditorDraft( $this->page );
		$storeapi = $this->storeapi = new EditorStoreFront;
		$this->layout = new EditorLayout();

		$this->templates = new EditorTemplates( $this->page );

		// Mapping
		$this->map = new PageLinesTemplates( $this->templates ); // this needs to be rewritten and moved to mapping class

		// Must come before settings
		$this->foundry = new PageLinesFoundry;
		$this->typography = new EditorTypography( $this->foundry );
		$this->importexport = new PLImportExport;
		$this->color = new EditorColor;
		$this->siteset = new EditorSettings;
		$this->extensions = new EditorExtensions;
		$pless = new PageLinesLess;
		$fileOpts = new EditorFileOpts;
		$this->editor_less = new EditorLess($pless);
		pagelines_register_hook('pl_after_settings_load'); // hook

		$plopts = $this->opts = new PageLinesOpts;

		// Mobile
		$this->mobile_menu = new PageLinesMobileMenu; 

		// Interfaces
		$this->xlist = new EditorXList;
		$this->add_sections = new PageLinesSectionsPanel;
		$this->extend_panel = new PageLinesExtendPanel;
		$this->settings_panel = new PageLinesSettingsPanel;
	//	$this->live_panel = new PageLinesLivePanel;
		$this->account_panel = new PLAccountPanel;
		$this->themer = new EditorThemeHandler;

		$this->code = new EditorCode( $this->draft );

		$this->areas = new PageLinesAreas;



		// Editor UX Elements
		$this->interface = new EditorInterface( $this->page, $this->siteset, $this->draft, $this->templates, $this->map, $this->extensions, $this->themer );

		// Master UX Handler
		$this->handler = new PageLinesTemplateHandler(
					$this->interface,
					$this->areas,
					$this->page,
					$this->siteset,
					$this->foundry,
					$this->map,
					$this->draft,
					$this->opts,
					$this->layout,
					$this->extensions

				);

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


}

