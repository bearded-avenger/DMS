<?php




add_action('wp_ajax_pl_editor_actions', 'pl_editor_actions');
function pl_editor_actions(){

	$post = $_POST;
	$response = array();
	$response['post'] = $post;
	$mode = $post['mode'];
	$run = $post['run'];
	$pageID = $post['pageID'];
	$typeID = $post['typeID'];

	if($mode == 'save'){

		$draft = new EditorDraft;
		$tpl = new EditorTemplates;
		$map = $post['map_object'] = new EditorMap( $tpl, $draft );

		if( $run == 'draft' ){

			$draft->save_draft( $pageID, $typeID, $post['pageData'] );


		} elseif ( $run == 'publish' ) {

			$draft->save_draft( $pageID, $typeID, $post['pageData'] );

			pl_publish_settings( $pageID, $typeID );

		} elseif ( $run == 'revert' ){

			$draft->revert( $post, $map );

		} elseif ( $run == 'map' ){
			
			$draft->save_draft( $pageID, $typeID, $post['pageData'] );

			$response['changes'] = $map->save_map_draft( $pageID, $post['map'] );

		}

		$response['state'] = $draft->get_state( $pageID, $typeID, $map );


	} elseif( $mode == 'sections'){

		if( $run == 'reload'){

			global $load_sections;
			$available = $load_sections->pagelines_register_sections( true, false );
			$response['result'] = $available;
		}


	} elseif( $mode == 'themes'){

		$theme = new EditorThemeHandler;

		if( $run == 'activate' ){
			$response = $theme->activate( $response );
			pl_flush_draft_caches();
		}


	} elseif ( $mode == 'templates' ){

		$tpl = new EditorTemplates;

		if ( $run == 'load' ){

			$response['loaded'] = $tpl->set_new_local_template( $pageID, $post['key'] );

		} elseif ( $run == 'update'){

			$key = ( isset($post['key']) ) ? $post['key'] : false;

			$template_map = $post['map']['template'];

			$tpl->update_template( $key, $template_map );

		} elseif ( $run == 'delete'){

			$key = ( isset($post['key']) ) ? $post['key'] : false;

			$tpl->delete_template( $key );

		} elseif ( $run == 'save' ){

			$template_map = $post['map']['template'];
			$settings = $post['settings'];

			$name = (isset($post['template-name'])) ? $post['template-name'] : false;
			$desc = (isset($post['template-desc'])) ? $post['template-desc'] : '';

			if( $name )
				$tpl->create_template($name, $desc, $template_map, $settings);

		} elseif( $run == 'set_type' ){


			$storage = new PageLinesData;
			$field = $post['field'];
			$value = $post['value'];

			$previous_val = $storage->meta( $typeID, $field );

			if( $previous_val == $value ){
				$storage->meta_update( $typeID, $field, false );
			} else {
				$storage->meta_update( $typeID, $field, $value );
			}

			$response['result'] = $storage->meta( $typeID, $field );


		} elseif( $run == 'set_global' ){


			$storage = new PageLinesData;
			$field = $post['field'];
			$value = $post['value'];

			$previous_val = $storage->opt( $field );

			if($previous_val == $value){
				$storage->opt_update( $field, false );
			} else {
				$storage->opt_update( $field, $value );
			}

			$response['result'] = $storage->opt( $field );

		}

	} elseif ( $mode == 'settings' ){

		$plpg = new PageLinesPage( array( 'mode' => 'ajax', 'pageID' => $pageID, 'typeID' => $typeID ) );
		$draft = new EditorDraft;
		$settings = new PageLinesOpts( $plpg, $draft );

		if ($run == 'reset_global'){

			$settings->reset_global();

		} elseif( $run == 'reset_local' ){

			$settings->reset_local( $pageID );

		} elseif( $run == 'delete' ){
			
			// delete clone index by keys
			
			
		}

	}


	// RESPONSE
	echo json_encode(  pl_arrays_to_objects( $response ) );

	die(); // don't forget this, always returns 0 w/o
}



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