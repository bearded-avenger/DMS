<?php

/* 
 * Storefront should fetch and organize the latest items from the PL store. 
 * It extends the PageLines API class and has access to its methods.
 **/ 
class EditorStoreFront extends PageLinesAPI {
	
	function __construct(){
		$this->data_url = $this->base_url . '/v4/all';
	}
	/* 
	 * Sort store items
	 */ 
	function sort( $args ){
		
	}
	
	/* 
	 * Parse and return array of store items
	 */
	function get(){
		
		$storefront = $this->fetch(); 
		
		
		return $storefront;
	}
	
}

/* 
 * This class handles all interaction with the PageLines APIs
 * !IMPORTANT - This class can be EXTENDED by sub classes that use the API. e.g. the store, account management, etc.. 
 **/
class PageLinesAPI {
	
	var $prot = array( 'https://', 'http://' );
	var $base_url = 'www.pagelines.com/api'; 
	

	function json_fetch( $url ){
		
	}
	
	
}