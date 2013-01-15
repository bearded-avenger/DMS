<?php 


add_action( 'wp_ajax_pl_save_map_draft', 'save_map_draft' );
function save_map_draft(){
	
	$draft = new EditorDraft;
	$map = new EditorMap( $draft );
	
	$map->save_map_draft( $_POST );  
	
	echo $draft->get_state( $_POST['page'] );
	
	die(); // don't forget this, always returns 0 w/o
	
}

add_action( 'wp_ajax_pl_publish_changes', 'pl_publish_changes' );
function pl_publish_changes(){

	$draft = new EditorDraft;
	$map = new EditorMap( $draft );
	
	$draft->publish( $_POST, $map );

	echo $draft->get_state( $_POST['page'] );
	die(); // don't forget this, always returns 0 w/o
	
}

add_action( 'wp_ajax_pl_revert_changes', 'pl_revert_changes' );
function pl_revert_changes (){

	$draft = new EditorDraft;
	$map = new EditorMap( $draft );
		
	$draft->revert( $_POST, $map );
	
	echo $draft->get_state( $_POST['page'] );
	die(); // don't forget this, always returns 0 w/o
	
}

add_action( 'wp_ajax_pl_load_template', 'pl_load_template' );
function pl_load_template (){

	$tpl = new EditorTemplates;
	$map = new EditorMap( new EditorDraft );
	
	$new_tpl_map = $tpl->get_map_from_template_key( $_POST['key'] );
	
	$map->set_new_local_template( $_POST['page'], $new_tpl_map );
			
	echo true;
	die(); // don't forget this, always returns 0 w/o
	
}


add_action( 'wp_ajax_pl_save_template', 'pl_save_template' );
function pl_save_template (){

	$tpl = new EditorTemplates;

	$template_map = $_POST['map']['template'];
	
	$name = (isset($_POST['template-name'])) ? $_POST['template-name'] : 'Template (No Name)'; 
	$desc = (isset($_POST['template-desc'])) ? $_POST['template-desc'] : 'No description.'; 
	
	$tpl->add_new_template($name, $desc, $template_map);
	
	echo true;
	die(); // don't forget this, always returns 0 w/o
	
}