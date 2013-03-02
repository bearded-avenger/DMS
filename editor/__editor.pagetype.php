<?php
/**
 * 
 *
 *  PageLines Page Type Handling
 *
 *
 */
class PageLinesPageType {


	var $opt_type_info = 'pl-type-info';


	function __construct( $type ){
		$this->type = $type;
		$this->type_info = $this->type_info();
	}

	function type_info(  ){
		
		$page_types_info = pl_opt( $this->opt_type_info, array() );
	
		return ( isset($page_types_info[ $this->type ]) ) ? $page_types_info[ $this->type ] : array();
		
	}
	
	function set_type_field( $key, $value ){
		
		$type_info = $this->type_info();
		
		$type_info[ $type ][ $key ] = $value; 
	
		
		pl_opt_update( $this->opt_type_info, $type_info );
		
	}
	
	function get_type_field( $key ){
		
		$type_info = $this->type_info();
	
		if( isset( $type_info[ $key ] ) )
			$type_info[ $key ];
		else 
			return false;
		
	}
	
	

}


