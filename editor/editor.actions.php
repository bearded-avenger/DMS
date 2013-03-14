<?php



add_action('wp_ajax_pl_editor_mode', 'pl_editor_mode');
function pl_editor_mode(){

	$data = $_POST;
	$key = 'pl_editor_state';
	$user_id = $data['userID'];

	$current_state = get_user_meta($user_id, $key, true);

	$new_state = ($current_state == 'on') ? 'off' : 'on';

	update_user_meta( $user_id, $key, $new_state );

	echo $new_state;

	die();
}

add_action( 'wp_ajax_pl_save_page', 'pl_save_page' );
function pl_save_page(){

	$data = $_POST;

	$mode = (isset($data['mode'])) ? $data['mode'] : 'draft';





	$plpg = new PageLinesPage( array('mode' => 'ajax', 'pageID' => $data['pageID'], 'typeID' => $data['typeID']) );
	$draft = new EditorDraft;
	$tpl = new EditorTemplates;
	$map = $data['map_object'] = new EditorMap( $tpl, $draft );
	$settings = new PageLinesOpts( $plpg, $draft );



	if( $mode == 'draft' ){

		$draft->save_draft( $data );
		pl_flush_draft_caches();

	} elseif ( $mode == 'publish' ) {

		$draft->save_draft( $data );
		$draft->publish( $data, $map );

	} elseif ( $mode == 'revert' ){

		$draft->revert( $data, $map );

	} elseif ( $mode == 'map' ){

		$map->save_map_draft( $data );

	} elseif ($mode == 'reset_global'){

		$settings->reset_global();

	} elseif( $mode == 'reset_local' ){

		$settings->reset_local( $data['pageID'] );

	}

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

	$data = $_POST;
	$mode = (isset($data['mode'])) ? $data['mode'] : 'default';


	$tpl = new EditorTemplates;

	if( $mode == 'save_template' ){

		$template_map = $data['map']['template'];

		$name = (isset($data['template-name'])) ? $data['template-name'] : 'Template (No Name)';
		$desc = (isset($data['template-desc'])) ? $data['template-desc'] : 'No description.';

		$tpl->create_template($name, $desc, $template_map);

	} elseif( $mode == 'delete_template' ){

		$key = ( isset($data['key']) ) ? $data['key'] : false;

		$tpl->delete_template( $key );

	} elseif( $mode == 'type_default' ){


		$storage = new PageLinesData;

		$storage->meta_update($data['typeID'], $data['field'], $data['key']);

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