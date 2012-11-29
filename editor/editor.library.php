<?php

/* 
 * Functions library for editor
 */

function get_available_sections(){
	
	
	global $pl_section_factory; 
	
	return $pl_section_factory->sections; 
	
	
}