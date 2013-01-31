<?php 



add_action( 'wp_ajax_pl_save_page', 'pl_save_page' );
function pl_save_page(){

	$mode = (isset($_POST['mode'])) ? $_POST['mode'] : 'draft';

	$draft = new EditorDraft;
	$map = new EditorMap( $draft );
	
	$data = $_POST; 
	$data['map_object'] = $map;

	if( $mode == 'draft' ){
		
		$draft->save_draft( $data );
		
	} elseif ( $mode == 'publish' ) {
		
		$draft->save_draft( $data );
		$draft->publish( $data, $map );
		
	} elseif ( $mode == 'revert' ){
		
		$draft->revert( $data, $map );
		
	} elseif ( $mode == 'map' ){
		
		$map->save_map_draft( $data );  
		
	}

	//echo json_encode( array( 'state' => $draft->get_state( $data )) );
	echo $draft->get_state( $data );	
	
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


add_action( 'wp_ajax_pl_template_action', 'pl_template_action' );
function pl_template_action (){

	$mode = (isset($_POST['mode'])) ? $_POST['mode'] : 'default';

	$tpl = new EditorTemplates;

	if($mode == 'save_template'){
		
		$template_map = $_POST['map']['template'];

		$name = (isset($_POST['template-name'])) ? $_POST['template-name'] : 'Template (No Name)'; 
		$desc = (isset($_POST['template-desc'])) ? $_POST['template-desc'] : 'No description.'; 

		$tpl->create_template($name, $desc, $template_map);
		
	} elseif( $mode == 'delete_template' ){
		
		$key = ( isset($_POST['key']) ) ? $_POST['key'] : false;
		
		$tpl->delete_template( $key );
		
	}	elseif( $mode == 'type_default' ){
		
		$page_type = new PageLinesPageType( $_POST['type'] );

		$key = $_POST['key'];

		$page_type->set_type_field( 'template-default', $key );


	}
	
	echo true;
	die(); // don't forget this, always returns 0 w/o
	
}

add_action( 'wp_ajax_pl_up_image', 'pl_up_image' );
function pl_up_image (){

	global $wpdb;
	
	$files_base = $_FILES[ 'qqfile' ]; 
	
	$arr_file_type = wp_check_filetype( basename( $files_base['name'] ));
		
	$uploaded_file_type = $arr_file_type['type'];
		
	// Set an array containing a list of acceptable formats
	$allowed_file_types = array( 'image/jpg','image/jpeg','image/gif','image/png', 'image/x-icon');
		
	if( in_array( $uploaded_file_type, $allowed_file_types ) ) {
	
		$files_base['name'] = preg_replace( '/[^a-zA-Z0-9._\-]/', '', $files_base['name'] ); 
		
		$override['test_form'] = false;
		$override['action'] = 'wp_handle_upload';    
		
		$uploaded_file = wp_handle_upload( $files_base, $override );
		
	//	$upload_tracking[] = $button_id;
		
		// ( if applicable-Update option here)
	
		$name = 'PageLines- ' . addslashes( $files_base['name'] );
	
		$attachment = array(
						'post_mime_type'	=> $uploaded_file_type,
						'post_title'		=> $name,
						'post_content'		=> '',
						'post_status'		=> 'inherit'
					);
	
		$attach_id = wp_insert_attachment( $attachment, $uploaded_file['file'] );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file['file'] );
		wp_update_attachment_metadata( $attach_id,  $attach_data );
		
	} else
		$uploaded_file['error'] = __( 'Unsupported file type!', 'pagelines' );
	
	if( !empty( $uploaded_file['error'] ) )
		echo sprintf( __('Upload Error: %s', 'pagelines' ) , $uploaded_file['error'] );
	else{
		echo json_encode(array('url' => $uploaded_file['url'], 'success' => TRUE));
		
	}
		
		
	
	
	die(); // don't forget this, always returns 0 w/o
	
}