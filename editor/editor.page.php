<?php
/**
 * 
 *
 *  PageLines Page Handling
 *
 *
 */
class PageLinesPage {

	function __construct( ) {
		
		

		$this->type_ID = $this->type();
		
		$this->type = ucwords( str_replace('_', ' ', $this->type()) ); 
		
		$this->id = $this->id();

	}

	function id(){
		global $post;
		
		if(!$this->is_special() && isset($post) && is_object($post))
			return $post->ID;
		else
			return $this->type();
			
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
	

}


