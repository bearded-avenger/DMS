<?php

class EditorLess extends EditorLessHandler {

	var $pless;
	var $lessfiles;

	function __construct( PageLinesLess $pless ) {

		$this->pless = $pless;
		$this->lessfiles = $this->get_core_lessfiles();
		$this->draft_less_file = sprintf( '%s/editor-draft.css', PageLinesRenderCSS::get_css_dir( 'path' ) );

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

			if( is_file( $this->draft_less_file ) ) {
				echo readfile( $this->draft_less_file );
			} else {
				$core = $this->googlefont_replace( $this->get_draft_core() );
				$css = $this->minify( $core['compiled_core'] );
				$css .= $this->minify( $core['compiled_sections'] );
				
				$css .= $this->minify( $core['dynamic'] );
				$this->write_draft_less_file( $css );
				echo $css;
			}
			die();
		}
	}

} // EditorLess