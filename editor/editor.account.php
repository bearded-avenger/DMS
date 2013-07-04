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
				'heading'	=> "<i class='icon-pagelines'></i> PageLines",
				'welcome'	=> array(
					'name'	=> 'Welcome!',
					'icon'	=> 'icon-star', 
					'call'	=> array(&$this, 'pagelines_welcome'),
				),
				'account'	=> array(
					'name'	=> 'Your Account',
					'icon'	=> 'icon-user', 
					'call'	=> array(&$this, 'pagelines_account'),
				),
				'support'	=> array(
					'name'	=> 'Support',
					'icon'	=> 'icon-comments',
					'call'	=> array(&$this, 'pagelines_support'),
				),
			)
		);

		return $toolbar;
	}
	
	function pagelines_welcome(){
		?>
		
		<h3><i class="icon-pagelines"></i> Congrats! You're using PageLines DMS.</h3>
		<p>
			Welcome to PageLines DMS, the world's first comprehensive drag and drop design management system.<br/>
			You've made it this far, now let's take a minute to show you around. <br/>
			<a href="#" class="dms-tab-link btn btn-success btn-mini"><i class="icon-user"></i> Add Account Info</a>
			
		</p>
		<p>
			<iframe width="560" height="315" src="//www.youtube.com/embed/_EDemMLMcQ0" frameborder="0" allowfullscreen></iframe>
		</p>
		
		<?php
	}

	function pagelines_account(){
		?>
		<h3><i class="icon-user"></i> Enter your PageLines user account information</h3>
		<p>
			This will be used to authenticate purchases and membership level. If you are a Pro member, it will unlock pro features.
		</p>
		<label>PageLines Username</label>
		<input type="text" class="pl-text-input" />
		
		<label>PageLines Password</label>
		<input type="password" class="pl-text-input" />
		<div class="submit-area">
			<button class="btn btn-primary" >Submit</button>
		</div>
		<?php
	}
	
	function pagelines_support(){
		?>
		<h3><i class="icon-thumbs-up"></i> The PageLines Experience</h3>
		<p>
			We want you to have a most amazing time as a PageLines customer. <br/>
			That's why we have a ton of people standing by to make you happy.
		</p>
		<p>
			<a href="http://www.pagelines.com/forum" class="btn" target="_blank"><i class="icon-comments"></i> PageLines Forum</a>
			<a href="http://docs.pagelines.com" class="btn" target="_blank"><i class="icon-file"></i> DMS Documentation</a>
		</p>
		
		<?php
	}
}