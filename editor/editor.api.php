<?php

/*
 * Storefront should fetch and organize the latest items from the PL store.
 * It extends the PageLines API class and has access to its methods.
 **/
class EditorStoreFront extends PageLinesAPI {

	function __construct(){
		$this->data_url = $this->base_url . '/v4/all';
		$this->username = get_pagelines_credentials( 'user' );
		$this->password = get_pagelines_credentials( 'pass' );
	}
	/*
	 * Sort store items
	 */
	function sort( $data ){
		return $data;
	}

	/*
	 * Parse and return array of store items
	 */
	function get(){

		$storefront = $this->store_mixed();
		return $this->sort( $storefront );
	}

	function store_mixed(){

		if ( false === ( $store_mixed_array = get_transient( 'store_mixed_array' ) ) ) {

	     	$store_mixed_array = $this->fetch_data( $this->data_url );
	     	set_transient( 'store_mixed_array', json_encode( $store_mixed_array ), 3600);
		}
		return $this->make_array( json_decode( $store_mixed_array ) );
	}




}

/*
 * This class handles all interaction with the PageLines APIs
 * !IMPORTANT - This class can be EXTENDED by sub classes that use the API. e.g. the store, account management, etc..
 **/
class PageLinesAPI {

	var $prot = array( 'https://', 'http://' );
	var $base_url = 'www.pagelines.com/api';


	function json_fetch( $url, $args ){

		$defaults = array(
			'sslverify'	=>	false,
			'timeout'	=>	15,
			'body'		=> array()
		);

		$options = wp_parse_args( $args, $defaults );

		foreach( $this->prot as $type ) {
			// sometimes wamp does not have curl!
			if ( $type === 'https://' && !function_exists( 'curl_init' ) )
				continue;
			$r = wp_remote_post( $type . $url, $options );
			if ( !is_wp_error($r) && is_array( $r ) ) {
				return $r;
			}
		}
	return false;
	}

	function fetch_data( $url ) {

		$options = array(
			'body' => array(
				'username'	=>	( $this->username != '' ) ? $this->username : false,
				'password'	=>	( $this->password != '' ) ? $this->password : false,
			)
		);

		$response = $this->json_fetch( $url, $options );

		if ( $response !== false ) {
			// ok we have the data parse and store it
			$api = wp_remote_retrieve_body( $response );
			return json_decode( $api );
		}
		return false;
	}
	function make_array( $data ) {

		if( is_array( $data ) )
			return $data;

		if( is_object( $data ) )
			return json_decode( json_encode( $data ), true );

		return array();
	}
}