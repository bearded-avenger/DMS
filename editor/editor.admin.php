<?php


class EditorAdmin {
	
	function __construct(){
		
		
		add_action( 'pagelines_options_dms_less', array(&$this, 'dms_tools_less') );
		add_action( 'pagelines_options_dms_scripts', array(&$this, 'dms_scripts_template') );
		
	}
	
	function admin_interface(){

		$d = array();

		$d =  array(
			'icon'			=> PL_ADMIN_ICONS.'/wrench.png',
			'tools'		=> array(
				'default'	=> '',
				'type'		=> 'dms_less',
				'layout'	=> 'full',
				'title'		=> __( 'DMS Admin Tools and Settings', 'pagelines' ),
				'shortexp'	=> __( 'Most options are in the editor, you will find fallbacks and other controls here.', 'pagelines' ),
			),
			'tools2'		=> array(
				'default'	=> '',
				'type'		=> 'dms_scripts',
				'layout'	=> 'full',
				'title'		=> __( 'DMS Admin Tools and Settings', 'pagelines' ),
				'shortexp'	=> __( 'Most options are in the editor, you will find fallbacks and other controls here.', 'pagelines' ),
			)
		);

		return $d;
	}
	
	function dms_tools_less(){

		?>
		
		<div class="optin">
			<div class="oinputs">
				<div class='oinputs-pad'>
					<form id="pl-dms-less-form">
						<label class="lbl">LESS/CSS Fallback</label>
						<textarea id="pl-dms-less" class="html-textarea code_textarea"><?php echo pl_setting('custom_less');?></textarea>
						<p><input class="button button-primary" type="submit" value="Save LESS" /></p>
					</form>
				</div>
			</div>
		</div>
		
		
		<?php 
		
	}
	
	function dms_scripts_template(){
		?>
			<div class="optin">
				<div class="oinputs">
					<div class='oinputs-pad'>
						<form id="pl-dms-scripts-form">
							<label class="lbl">Custom Scripts Fallback</label>
							<textarea id="pl-dms-scripts" class="html-textarea code_textarea"><?php echo pl_setting('custom_scripts');?></textarea>
							<p><input class="button button-primary" type="submit" value="Save Scripts" /></p>
						</form>
					</div>
				</div>
			</div>
		
		<?php
	}

}