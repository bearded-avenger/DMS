<?php




add_action('wp_ajax_pl_editor_actions', 'pl_editor_actions');
function pl_editor_actions(){

	$postdata = $_POST;
	$response = array();
	$response['post'] = $postdata;
	$mode = $postdata['mode'];
	$run = $postdata['run'];
	$pageID = $postdata['pageID'];
	$typeID = $postdata['typeID'];

	if($mode == 'save'){

		$draft = new EditorDraft;
		$tpl = new EditorTemplates;
		$map = $postdata['map_object'] = new PageLinesTemplates( $tpl );

		if ( $run == 'map' || $run == 'all' || $run == 'draft' || $run == 'publish'){
			
			$draft->save_draft( $pageID, $typeID, $postdata['pageData'] );
			// 
			// if( ($run == 'map' || $run == 'all') && isset($postdata['map']) ){
			// 	
			// 	$template_mode = (isset($postdata['templateMode'])) ? $postdata['templateMode'] : 'type';
			// 	
			// 	$response['changes'] = $map->save_map_draft( $pageID, $typeID, $postdata['map'], $template_mode );
			// 	
			// }
			

		}

		if ( $run == 'publish' )
			pl_publish_settings( $pageID, $typeID );
		
		elseif ( $run == 'revert' )
			$draft->revert( $postdata, $map );

		$response['state'] = $draft->get_state( $pageID, $typeID, $map );


	} elseif( $mode == 'sections'){

		if( $run == 'reload'){

			global $load_sections;
			$available = $load_sections->pagelines_register_sections( true, false );
			$response['result'] = $available;
		} elseif( $run == 'load' ){
			
			$section_object = $postdata['object'];
			$section_unique_id = $postdata['uniqueID'];
			
			global $pl_section_factory;
			
			if( is_object($pl_section_factory->sections[ $section_object ]) ){
				
				global $post; 
				$post = get_post($postdata['pageID']);
				
				$s = $pl_section_factory->sections[ $section_object ];
				
				$response['opts'] = $s->section_opts();
				
				ob_start();
					$s->active_loading = true;
					$s->section_template();
				$section_template = ob_get_clean();
				
				$response['template'] = $section_template;
			
			}
			
			
			
		}


	} elseif( $mode == 'themes'){

		$theme = new EditorThemeHandler;

		if( $run == 'activate' ){
			$response = $theme->activate( $response );
			pl_flush_draft_caches( false );
		}


	} elseif ( $mode == 'templates' ){

		$tpl = new EditorTemplates;

		if ( $run == 'load' ){

			$metaID = (isset($postdata['templateMode']) && $postdata['templateMode'] == 'type') ? $typeID : $pageID;

			$response['loaded'] = $tpl->load_template( $metaID, $postdata['key'] );

		} elseif ( $run == 'update'){

			$key = ( isset($postdata['key']) ) ? $postdata['key'] : false;

			$template_map = $postdata['map']['template'];

			$response['tpl'] = $tpl->update_template( $key, $template_map, $postdata['settings'], $pageID );

		} elseif ( $run == 'delete'){

			$key = ( isset($postdata['key']) ) ? $postdata['key'] : false;

			$tpl->delete_template( $key );

		} elseif ( $run == 'save' ){

			$template_map = $postdata['map']['template'];
			$settings = $postdata['settings'];

			$name = (isset($postdata['template-name'])) ? $postdata['template-name'] : false;
			$desc = (isset($postdata['template-desc'])) ? $postdata['template-desc'] : '';

			if( $name )
				$tpl->create_template($name, $desc, $template_map, $settings, $pageID);

		} elseif( $run == 'set_type' ){

			$field = 'page-template';
			$value = $postdata['value'];

			$previous_val = pl_local( $typeID, $field );

			if( $previous_val == $value )
				pl_local_update( $typeID, $field, false );
			else
				pl_local_update( $typeID, $field, $value );

			$response['result'] = pl_local( $typeID, $field );


		} elseif( $run == 'set_global' ){

			$field = 'page-template';
			$value = $postdata['value'];

			$previous_val = pl_global( $field );

			if($previous_val == $value)
				pl_global_update( $field, false );
			else
				pl_global_update( $field, $value );
			

			$response['result'] = pl_global( $field );

		}

	} elseif ( $mode == 'settings' ){

		$plpg = new PageLinesPage( array( 'mode' => 'ajax', 'pageID' => $pageID, 'typeID' => $typeID ) );
		$draft = new EditorDraft;
		$settings = new PageLinesOpts;

		if ($run == 'reset_global'){

			$response['settings'] = $settings->reset_global();

		} elseif( $run == 'reset_local' ){

			$settings->reset_local( $pageID );

		} elseif( $run == 'delete' ){
			
			// delete clone index by keys
			
			
		}elseif( $run == 'exporter' ) {
			
			$data = $postdata['formData'];
			$data = stripslashes_deep( $data );
			$fileOpts = new EditorFileOpts;		
			$response['export'] = $fileOpts->init( $data );
			$response['export_data'] = $data;
			
		} elseif( $run == 'reset_global_child' ) {
			
			$opts = array();
			$opts['global_import'] = $_POST['global_import'];
			$opts['type_import'] = $_POST['type_import'];
			$opts['page_tpl_import'] = $_POST['page_tpl_import'];
			$settings->reset_global_child( $opts );
		
		}

	} 


	// RESPONSE
	echo json_encode(  pl_arrays_to_objects( $response ) );

	die(); // don't forget this, always returns 0 w/o
}


add_action('wp_ajax_upload_config_file', 'pl_upload_config_file');
function pl_upload_config_file(){
	
	$fileOpts = new EditorFileOpts;
	$filename = $_FILES['files']['name'][0];

	$opts = array();
	$opts['global_import'] = $_POST['global_import'];
	$opts['type_import'] = $_POST['type_import'];
	$opts['page_tpl_import'] = $_POST['page_tpl_import'];

	if( preg_match( '/pl\-config[^\.]*\.json/', $filename ) ) {
		$file = $_FILES['files']['tmp_name'][0];
		
	$response['file'] = $file;

	if( isset( $file ) )
		$response['import_reponse'] = $fileOpts->import( $file, $opts );
	

		$response['import_file'] = $file;
		$response['post'] = $_POST;
	} else {
		$reponse['import_error'] = 'filename?';
	}
	
	echo json_encode(  pl_arrays_to_objects( $response ) );
	die();
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

add_action('wp_ajax_pl_dms_admin_actions', 'pl_dms_admin_actions');
function pl_dms_admin_actions(){
	$response = array();
	$postdata = $_POST;
	$response['post'] = $_POST;
	$lessflush = ( isset( $postdata['flag'] ) ) ? $postdata['flag'] : false;

	$field = $postdata['setting'];
	$value = $postdata['value'];
	
	pl_setting_update($field, $value);
	
	echo json_encode(  pl_arrays_to_objects( $response ) );
	if( $lessflush )
		pl_flush_draft_caches( false );
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
