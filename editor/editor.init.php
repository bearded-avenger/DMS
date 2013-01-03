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
if (!current_user_can('edit_themes') || !defined('PL_UX_DEV') || !PL_UX_DEV)
	return;


$pagelines_editor = new PageLinesEditor; 

class PageLinesEditor {

	function __construct() {

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
	
	function load_libs(){
		$this->page = new PageLinesPage;
		$this->siteset = new EditorSettings;
		$this->foundry = new PageLinesFoundry;
		$this->interface = new EditorInterface( $this->page, $this->siteset );
		$this->handler = new PageLinesTemplateHandler( $this->interface, $this->page, $this->siteset, $this->foundry );
		
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
