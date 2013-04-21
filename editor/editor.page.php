<?php
/**
 *
 *
 *  PageLines Page Handling
 *
 *
 */
class PageLinesPage {

	var $special_base = 70000000;
	var $opt_special_lookup = 'pl-special-lookup';
	var $opt_type_info = 'pl-type-info';

	function __construct( $args = array() ) {

		$args = wp_parse_args($args, $this->defaults());

		$mode = $args['mode'];

		if( $mode == 'ajax' ){

			$this->id = $args['pageID'];

			$this->typeid = $args['typeID'];

		} else {

			$this->id = $this->id();

			$this->type = $this->type();

			$this->typeid = $this->special_id();

			$this->type_name = ucwords( str_replace('_', ' ', $this->type()) );

		}

	}

	function defaults(){
		$d = array(
			'mode'		=> 'standard',
			'pageID'	=> '',
			'typeID'	=> ''
		);
		return $d;
	}

	function id(){
		global $post;
		if(!$this->is_special() && isset($post) && is_object($post))
			return $post->ID;
		else
			return $this->special_id();

	}

	function special_id(){

		$index = $this->special_index_lookup();

		$id = $this->special_base + $index;

		return $id;

	}

	function special_index_lookup(){

		$lookup_array = pl_opt( $this->opt_special_lookup );

		if( !$lookup_array ){

			$lookup_array = array(
				'blog',
				'category',
				'search',
				'tag',
				'author',
				'archive',
				'page',
				'post',
				'404_page'
			);

			pl_opt_update( $this->opt_special_lookup, $lookup_array );
		}

		$index = array_search( $this->type(), $lookup_array );

		if( !$index ){

			$lookup_array[]  = $this->type();

			$index = array_search( $this->type(), $lookup_array );

			pl_opt_update( $this->opt_special_lookup, $lookup_array );

		}

		return $index;

	}

	function type(){

		if( is_404() )
			$type = '404_page';

		elseif( pl_is_cpt('archive') )
			$type = get_post_type_plural();

		elseif( is_tag() )
			$type = 'tag';

		elseif( is_search() )
			$type = 'search';

		elseif( is_category() )
			$type = 'category';

		elseif( is_author() )
			$type = 'author';

		elseif( is_archive() )
			$type = 'archive';

		elseif( is_home() )
			$type = 'blog';

		// ID is now set...
		elseif( pl_is_cpt() )
			$type = get_post_type();

		elseif( is_page() )
			$type = 'page';

		elseif( is_single() )
			$type = 'post';

		else
			$type = 'other';

		return $type;

	}

	function is_special(){

		if ( is_404() || is_home() || is_search() || is_archive() )
			return true;
		else
			return false;

	}

	function is_posts_page(){

		if ( is_home() || is_search() || is_archive() || is_category() )
			return true;
		else
			return false;

	}


}


