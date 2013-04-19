<?php
/**
 *
 *
 *  CSS Selector Groups
 *  for dynamic CSS control
 *
 *  @package PageLines Framework
 *  @subpackage Options
 *  @since 2.0.b6
 *
 */

class PageLinesCSSGroups{

	/**
	 * PHP5 constructor
	 */
	function __construct( ) {

		$this->s = $this->get_groups();

		add_filter('pagelines_css_group', array(&$this, 'extend_selectors'), 10, 2);

	}


	/**
	*
	* @TODO document
	*
	*/
	function extend_selectors($sel, $group){

		global $add_selectors;

		if(is_array($add_selectors) && !empty($add_selectors)){
			foreach($add_selectors as $key => $s){

				if($group == $s['group'])
					$sel .= ','.$s['sel'];

			}
		}

		return $sel;

	}


	/**
	*
	* @TODO document
	*
	*/
	function get_groups(){

		$s = array();

		if(!pl_has_editor()){

			$s['bodybg'] = 'body, body.fixed_width';
			$s['pagebg'] = 'body #page .page-canvas';
			$s['contentbg'] = '.canvas .page-canvas, .thepage .content, .sf-menu li, #primary-nav ul.sf-menu a:focus, .sf-menu a:hover, .sf-menu a:active, .commentlist ul.children .even';
			$s['cascade'] = '.commentlist ul.children .even';
			$s['page_background_image'] = '.full_width #page .page-canvas, body.fixed_width';

			$s['type_headers'] = '.thead, h1, h2, h3, h4, h5, h6, .site-title';
			$s['type_primary'] = 'body, .font1, .font-primary, .commentlist';
			$s['type_secondary'] = '.font-sub, ul.main-nav, #secondnav, .metabar, .post-nav, .subtext, .subhead, .widget-title, .reply a, .editpage, #page .wp-pagenavi, .post-edit-link, #wp-calendar caption, #wp-calendar thead th, .soapbox-links a, .fancybox, .standard-form .admin-links, .pagelines-blink, .ftitle small';
			$s['type_inputs'] = 'input[type="text"], input[type="password"], textarea, #dsq-content textarea';

		}



		return $s;

	}


	public function get_css_group( $group ){

		if( is_array($group) ){

			$sel = '';

			foreach($group as $g)
				$sel .= $this->return_group( $g );

			return $sel;

		} else
			return $this->return_group( $group );

	}


	/**
	*
	* @TODO document
	*
	*/
	function return_group( $g ){

		if( isset( $this->s[ $g ] ) )
			return apply_filters('pagelines_css_group', $this->s[ $g ], $g);
		else
			return apply_filters('pagelines_css_group_'.$g, '');

	}

}

/**
*
* @TODO do
*
*/
function cssgroup( $group ){

	global $css_groups;

	if(!isset($css_groups))
		$GLOBALS['css_groups'] = new PageLinesCSSGroups();

	$get = $css_groups->get_css_group( $group );

	return $get;
}


/**
*
* @TODO do
*
*/
function pl_add_selectors( $group, $selectors ){

	global $add_selectors;

	$add_selectors[] = array( 'group' => $group, 'sel' => $selectors);
}