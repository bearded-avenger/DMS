<?php

class EditorLess extends EditorLessHandler {

	var $pless;
	var $lessfiles;

	function __construct( PageLinesLess $pless ) {

		$this->pless = $pless;
		$this->lessfiles = $this->get_core_lessfiles();

		if( $this->is_draft() )
			$this->draft_init();
	}

	/**
	 *
	 *  Display Draft Less.
	 *
	 *  @package PageLines Framework
	 *  @since 3.0
	 */
	function pagelines_draft_render() {

		if( isset( $_GET['pagedraft'] ) ) {

			$this->compare_less();

			header( 'Content-type: text/css' );
			header( 'Expires: ' );
			header( 'Cache-Control: max-age=604100, public' );

			$core = $this->googlefont_replace( $this->get_draft_core() );

			echo $this->minify( $core['compiled_core'] );
			echo $this->minify( $core['compiled_sections'] );
			echo $this->minify( $core['type'] );
			echo $this->minify( $core['dynamic'] );
			die();
		}
	}

} // EditorLess