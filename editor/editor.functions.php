<?php 

/*
 *	Editor functions - Always loaded
 */ 

function pl_has_editor(){
	
	return (class_exists('PageLinesTemplateHandler')) ? true : false;
	
}



