<?php 



class PageLinesLivePanel{
	
	function __construct(){
		
		add_filter('pl_toolbar_config', array(&$this, 'toolbar'));
		add_action('pagelines_editor_scripts', array(&$this, 'scripts'));
	
		$this->url = PL_PARENT_URL . '/editor';
		
		$this->chat_frame_url = 'http://pagelines.campfirenow.com/6cd04';
	}
	
	function scripts(){
		
	}
	
	function toolbar( $toolbar ){
		$toolbar['live'] = array(		
			'name'	=> 'Live',
			'icon'	=> 'icon-comments',
			'pos'	=> 70,
			'panel'	=> array(
				'heading'	=> "<i class='icon-comments'></i> Live Support",
				'support_chat'	=> array(
					'name'	=> 'PageLines Live Chat',
					'icon'	=> 'icon-comments'
				),
			)
		);
		
		return $toolbar;
	}

	
}