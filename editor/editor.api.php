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
		global $pldraft;
		$this->draft = $pldraft->mode;
		$this->get_latest();
	}

	function get_latest(){

		if( 'draft' == $this->draft ) {
			$data = $this->json_get( $this->data_url );

			// Add the decoded data to the global for store to use.
			global $mixed_array;
			$mixed_array = $this->make_array( json_decode( $data ) );
		}
	}

}

/*
 * This class handles all interaction with the PageLines APIs
 * !IMPORTANT - This class can be EXTENDED by sub classes that use the API. e.g. the store, account management, etc..
 **/
class PageLinesAPI {

	var $prot = array( 'https://', 'http://' );
	var $base_url = 'api.pagelines.com';

	/*
	 * Turn something into an array.
	 */
	function make_array( $data ) {

		if( is_array( $data ) )
			return $data;

		if( is_object( $data ) )
			return json_decode( json_encode( $data ), true );

		return array();
	}

	/*
	 * Fetch remote json.
	 */
	function json_get( $url ) {

		$options = array(
			'sslverify'	=>	false,
			'timeout'	=>	15,
			'body' => array(
				'username'	=>	( $this->username != '' ) ? $this->username : false,
				'password'	=>	( $this->password != '' ) ? $this->password : false,
			)
		);
		$f  = wp_remote_retrieve_body( $this->try_api( $url, $options ) );
		return $f;
	}

	/*
	 * Retrieve a remote object.
	 */
	function try_api( $url, $args ) {

		$defaults = array(
			'sslverify'	=>	false,
			'timeout'	=>	5,
			'body'		=> array()
		);
		$options = wp_parse_args( $args, $defaults );

		foreach( $this->prot as $type ) {
			// sometimes wamp does not have curl!
			if ( $type === 'https://' && ! function_exists( 'curl_init' ) )
				continue;
			$r = wp_remote_post( $type . $url, $options );
			if ( !is_wp_error($r) && is_array( $r ) ) {
				return $r;
			}
		}
		return false;
	}
}