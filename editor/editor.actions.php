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