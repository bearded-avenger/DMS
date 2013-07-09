<?php 


function pl_is_pro(){	
	
	$status = get_option( 'dms_activation', array( 'active' => false, 'key' => '', 'message' => '', 'email' => '' ) );
	
	$pro = (true === $status['active']) ? true : false;
	
	return $pro;
	
}

function pl_is_dev(){
	
	return false;
	
}