<?php 

/*
 *	Editor functions - Always loaded
 */ 

function pl_has_editor(){
	
	return (class_exists('PageLinesTemplateHandler')) ? true : false;
	
}


// Function to be used w/ compabibility mode to de
function pl_deprecate_v2(){
	
	return true;
	
}


function pl_use_editor(){
	
	return true;
	
}


// Process old function type to new format
function process_to_new_option_format( $old_options ){
	
	$new_options = array();
	
	foreach($old_options as $key => $o){
		
		if($o['type'] == 'multi_option' || $o['type'] == 'text_multi'){
		
			$sub_options = array();
			foreach($o['selectvalues'] as $sub_key => $sub_o){
				$sub_options[ ] = process_old_opt($sub_key, $sub_o, $o); 
			}
			$new_options[ ] = array(
				'type' 	=> 'multi', 
				'title'	=> $o['title'],
				'opts'	=> $sub_options
			);
		} else {
			$new_options[ ] = process_old_opt($key, $o);	
		}
		
	}
	
	return $new_options;
}

function process_old_opt( $key, $old, $otop = array()){
	
	if(isset($otop['type']) && $otop['type'] == 'text_multi')
		$old['type'] = 'text'; 
		
	$defaults = array(
        'type' 			=> 'check',
		'title'			=> '',
		'inputlabel'	=> '', 
		'exp'			=> '', 
		'shortexp'		=> '',
		'count_start'	=> 0,
		'count_number'	=> '',
		'selectvalues'	=> array(),
		'taxonomy_id'	=> '',
		'span'			=> 1
	);
	
	$old = wp_parse_args($old, $defaults);
	
	$exp = ($old['exp'] == '' && $old['shortexp'] != '') ? $old['shortexp'] : $old['exp'];
	
	if($old['type'] == 'text_small'){
		$type = 'text'; 
	} else 
		$type = $old['type'];
	
	$new = array(
		'key'			=> $key, 
		'title'			=> $old['title'],
		'label'			=> $old['inputlabel'], 
		'type'			=> $type, 
		'help'			=> $exp, 
		'opts'			=> $old['selectvalues'],
		'span'			=> $old['span']
	); 
	
	if($old['type'] == 'count_select'){
		$new['count_start'] = $old['count_start'];
		$new['count_number'] = $old['count_number'];
	}
	
	if($old['taxonomy_id'] != '')
		$new['taxonomy_id'] = $old['taxonomy_id'];
	
	return $new;
}
