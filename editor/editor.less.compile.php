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

	function __construct(){

		global $pldraft;
		$this->draft = $pldraft->mode;
		$this->init();
	}

	function init(){
		// this usually loads files or whatever
		// if we are in banana mode fire up the flux capacitors.
		if( 'draft' == $this->draft ) {
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_draft_css' ) );
			add_action( 'wp_print_styles', array( &$this, 'dequeue_live_css' ), 12 );
		}
	}

	// dequeues the regular 'live' css.
	function dequeue_live_css() {
		wp_deregister_style( 'pagelines-less' );
	}
	function enqueue_draft_css() {
		wp_register_style( 'pagelines-draft',  sprintf( '%s/?pageless=1', site_url() ), false, null, 'all' );
		wp_enqueue_style( 'pagelines-draft' );
	}
}