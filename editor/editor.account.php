<?php 


class PLAccountPanel{

	function __construct(){

		add_filter('pl_toolbar_config', array(&$this, 'toolbar'));

		$this->url = PL_PARENT_URL . '/editor';

	}

	function toolbar( $toolbar ){
		$toolbar['account'] = array(
			'name'	=> 'PageLines',
			'icon'	=> 'icon-pagelines',
			'pos'	=> 110,
		//	'type'	=> 'btn',
			'panel'	=> array(
				'heading'	=> "<i class='icon-pagelines'></i> PageLines Account",
				'welcome'	=> array(
					'name'	=> 'Welcome!',
					'icon'	=> 'icon-star'
				),
				'account'	=> array(
					'name'	=> 'Your Account',
					'icon'	=> 'icon-user'
				),
				'support'	=> array(
					'name'	=> 'Support',
					'icon'	=> 'icon-comments'
				),
			)
		);

		return $toolbar;
	}


}