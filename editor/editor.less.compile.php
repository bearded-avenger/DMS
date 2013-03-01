<?php 

/*
 * Less handler needs to compile live on 'publish' and load in the header of the website
 * 
 * It needs to grab variables (or create a filter) that can be added by settings, etc.. (typography)

 * Inline LESS will be used to handle draft mode, and previewing of changes. 
 
 */

class EditorLessHandler{
	
	var $pless_vars = array(); 
	
	function __construct(){
		
		$this->init();
		
	}
	
	function init(){
		// this usually loads files or whatever 
	}

	function get_less_vars(){
		
	}
	
	function inline_less(){
		
	}
	
	function compile_live_less(){
		
	}
	
	function output_less_to_head(){
		
	}
	
	
	
}