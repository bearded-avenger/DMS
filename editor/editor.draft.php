<?php


class EditorDraft{
	
	var $slug = 'pl-draft';
	
	function __construct( ){
		
		$current_user = wp_get_current_user();
		$state = get_user_meta($current_user->ID, 'pl_editor_state', true);
		
		if( current_user_can('edit_themes') && $state != 'off')
			$this->mode = 'draft';
		else {
			echo 'hi~';
			$this->mode = 'live';
		}
			
			
			
	}

	function save_draft( $data ){
		
		if( isset($data['pageData']['global']) )
			pl_settings_update( stripslashes_deep( $data['pageData']['global'] ), 'draft');
	
		if( isset($data['pageData']['local']) )
			pl_settings_update( $data['pageData']['local'], 'draft', $data['pageID'] );
		
		if( isset($data['pageData']['type']) && $data['pageID'] != $data['typeID'])
			pl_settings_update( $data['pageData']['type'], 'draft', $data['typeID'] );
		
	}

	function set_state( $draft_state, $live_state, $metaID = false ){
		
		$modified = ( $live_state != $draft_state ) ? true : false;
		
		if($modified)
			echo $metaID;
		
		if( $metaID )
			pl_meta_update( $metaID, $this->slug, $modified  );
		else 
			pl_opt_update( $this->slug, $modified );
			
	}

	function publish( $data, EditorMap $map ){
		
		$pageID = $data['pageID']; 
		$typeID = $data['typeID']; 
		
		pl_publish_settings($pageID, $typeID);
		
		$data['map_object']->publish_map( $data['pageID'] );
		
		$this->reset_state( $data['pageID'] );
	}
	
	function revert( $data, EditorMap $map ){
		$revert = $data['revert'];
		$pageID = $data['pageID'];
		$typeID = $data['typeID'];
	
		if( $revert == 'local' || $revert == 'all')
			$this->revert_local($pageID, $map);
			
		if( $revert == 'type' || $revert == 'all')
			$this->revert_type($typeID, $map);
			
		if( $revert == 'global' || $revert == 'all')
			$this->revert_global($map);
		
		
	}
	
	function revert_local( $pageID, $map ){
		$map->revert_local( $pageID );
		pl_revert_settings( $pageID );
		pl_meta_update( $pageID, $this->slug, false );
	}
	
	function revert_type( $typeID ){
		pl_revert_settings( $typeID );
		pl_meta_update( $typeID, $this->slug, false );
	}
	
	function revert_global( $map ){
		$map->revert_global( );
		pl_revert_settings( );
		pl_opt_update( $this->slug, false );
	}

	

	function get_state( $data ){
		
		$state = array();
		$settings = array();
		$pageID = $data['pageID']; 
		$typeID = $data['typeID'];
		$default = array('live'=> array(), 'draft' => array());
		
		
		// Local
		$settings['local'] = pl_meta( $pageID, PL_SETTINGS );
		
		if($typeID != $pageID)
			$settings['type'] = pl_meta( $typeID, PL_SETTINGS );
			
		$settings['global'] = pl_opt( PL_SETTINGS );
		$settings['map-local'] = $data['map_object']->map_local( $pageID );
		$settings['map-global'] = $data['map_object']->map_global();
		
		foreach( $settings as $scope => $set ){
			
			$set = wp_parse_args($set, $default);
			
			$scope = str_replace('map-', '', $scope);
			
			if( $set['draft'] != $set['live'] ){
				$state[$scope] = $scope;
			//	print_r( array_diff($set['draft'], $set['live']) );
			}
				
			
			
		}
		
	//	print_r($state);
			
		if( count( $state ) > 1 )
			$state[] = 'multi';
			
		if(empty($state))
			return 'clean';
		else 
			return join(' ', $state);
		
	}
	
	function reset_state( $pageID ){
		pl_meta_update( $pageID, $this->slug, false );
		pl_opt_update( $this->slug, false );
	}

	function set_local( $pageID, $val = true ){
		pl_meta_update( $pageID, $this->slug, $val );
	}
	
	function set_global( $val = true ){
		pl_opt_update( $this->slug, $val );
	}
	
}