<?php
/*
	Section: Primary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The main widgetized sidebar.
	Class Name: PrimarySidebar
	Workswith: sidebar1, sidebar2, sidebar_wrap
	Persistant: true
	Filter: widgetized
	Loading: active
*/

/**
 * Primary Sidebar Section
 *
 * @package PageLines Framework
 * @author PageLines
*/
class PrimarySidebar extends PageLinesSection {

	function section_persistent(){
		
		register_sidebar( array(
		    'id'          => $this->id,
		    'name'        => $this->name,
		    'description' => $this->description
		) );
		
	}

	/**
	* Section template.
	*/
   function section_template() {
	 	 pagelines_draw_sidebar($this->id, $this->name, 'includes/widgets.default');
	}

}