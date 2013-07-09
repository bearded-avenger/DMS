<?php 


class PLAccountPanel{

	function __construct(){

		add_filter('pl_toolbar_config', array(&$this, 'toolbar'));

		$this->url = PL_PARENT_URL . '/editor';

	}

	function toolbar( $toolbar ){
		$toolbar['account'] = array(
			'name'	=> 'Account',
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
				'pl_account'	=> array(
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
			<a href="#" class="dms-tab-link btn btn-success btn-mini" data-tab-link="account" data-stab-link="account"><i class="icon-user"></i> Add Account Info</a>
			
		</p>
		<p>
			<iframe width="560" height="315" src="//www.youtube.com/embed/_EDemMLMcQ0" frameborder="0" allowfullscreen></iframe>
		</p>
		
		<?php
	}

	function pagelines_account(){
		
		$disabled = '';
		$email = '';
		$key = '';
		$activate_text = 'Activate';
		if( pl_is_pro() ) {
			$disabled = ' disabled';
			$data = get_option( 'dms_activation' );
			$email = sprintf( 'value="%s"', $data['email'] );
			$key = sprintf( 'value="%s"', $data['key'] );
			printf( '<div class="account-description"><div class="alert alert-info">%s</div></div>', $data['message'] );
			$activate_text = 'Deactivate';
		}
		
		if( ! pl_is_pro() ){
		?>
		<h3><i class="icon-user"></i> Enter your PageLines DMS Activation key</h3>
		<p class="account-description">
			If you are a Pro member, it will unlock pro features.
		</p>
		<?php }
		?>
		<label for="pl_activation">User email</label>
		<input type="text" class="pl-text-input" name="pl_email" id="pl_email" <?php echo $email . $disabled ?> />
		
		<label for="pl_activation">Activation key</label>
		<input type="text" class="pl-text-input" name="pl_activation" id="pl_activation" <?php echo $key . $disabled ?>/>


		<?php
		if( pl_is_pro() ) {
			echo '<input type="hidden" name="pl_revoke" id="pl_revoke" value="true" />';
		}
		
		?>
		<div class="submit-area">
			<button class="btn btn-primary settings-action" data-action="pagelines-account"><?php echo $activate_text; ?></button>
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