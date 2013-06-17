<?php



class PLImportExport{

	function __construct(){

		add_filter('pl_settings_array', array(&$this, 'add_settings'));

		$this->url = PL_PARENT_URL . '/editor';

	}

	function add_settings( $settings ){

		$settings['importexport'] = array(
			'name' 	=> 'Import + Export',
			'icon'	=> 'icon-exchange',
			'pos'	=> 45,
			'opts' 	=> $this->option_interface()
		);
		
		return $settings;
	}
	
	function import_template(){
		ob_start();
		
		
		?>
		<label>DMS Config Import</label>
		
		<span class="btn btn-success fileinput-button">
	        <i class="icon-plus"></i>
	        <span>Select config file (.json)</span>
	        <!-- The file input field used as target for the file upload widget -->
	        <input id="fileupload" type="file" name="files[]" multiple>
	    </span>
		
		<?php 
		
		return ob_get_clean();
	}
	
	function export_template(){
		ob_start();
		
		$tpls = new EditorTemplates;
		?>
		<label>Select User Templates</label>
		
		<?php
		
		$btns = sprintf(
			'<div class="checklist-btns">
				<button class="btn btn-mini checklist-tool" data-action="checkall"><i class="icon-ok"></i> Select All</button> 
				<button class="btn btn-mini checklist-tool" data-action="uncheckall"><i class="icon-remove"></i> Deselect All</button>
			</div>');
		
		$tpl_selects = ''; 
		foreach( $tpls->get_user_templates() as $index => $template){
			
			$tpl_selects .= sprintf(
				'<label class="checklist-label media" for="%s">
					<div class="img"><input name="templates[]%s" id="%s" type="checkbox" checked /></div>
					<div class="bd"><div class="ttl">%s</div><p>%s</p></div>
				</label>', 
				$index,
				$index,
				$index, 
				$template['name'], 
				$template['desc']
			);
		}
		
		printf('<fieldset>%s%s</fieldset>', $btns, $tpl_selects );
		
		?>
		<label>Global Settings</label>
		<label class="checklist-label media" for="export_global" name="export_global">
			<div class="img"><input name="export_global" id="export_global" type="checkbox" checked /></div>
			<div class="bd">
				<div class="ttl">Export Site Global Settings</div>
				<p>This will export your sites global settings. This includes everything in the options panel, as well as settings directed at sections in your "global" regions like your header and footer.</p>
			</div>
		</label>
		
		<label>Post Type Settings</label>
		<label class="checklist-label media" for="export_types">
			<div class="img"><input name="export_types" id="export_types" type="checkbox" checked /></div>
			<div class="bd">
				<div class="ttl">Export Post Type Settings</div>
				<p>This exports settings such as the template defaults for various post types.</p>
			</div>
		</label>
		
		<label>Theme Config Publishing</label>
		<?php
			
			$publish_active = (is_child_theme() || PL_LESS_DEV) ? true : false;
		
		?>
		<label class="checklist-label media <?php echo (!$publish_active) ? 'disabled': '';?>" for="publish_config">
			<div class="img"><input id="publish_config" name="publish_config" type="checkbox" <?php echo (!$publish_active) ? 'disabled="disabled"': '';?> /></div>
			<div class="bd">
				<div class="ttl"><?php echo (!$publish_active) ? '(Disabled! No child theme active)': '';?> Publish Configuration to Child Theme</div>
				<p>Check this to publish your site configuration as a theme configuration file in your theme's root directory. When a user activates your theme it will ask if it can overwrite their settings to attain a desired initial experience to the theme.</p>
			</div>
		</label>
		
		<div class="center publish-button">
			<button class="btn btn-primary btn-large settings-action" data-action="opt_dump">Publish <span class="spamp">&amp;</span> Download DMS Config</button>
		</div>

		<?php
		
		return ob_get_clean();
	}
	
	function option_interface(){
	
		
		$settings = array(
			
			array(
				'type' 		=> 	'template',
				'title' 	=> __( 'Export DMS Config', 'pagelines' ),
				'span'		=> 2,
				'template'	=> $this->export_template()
			),
			array(
				'type' 		=> 	'template',
				'title' 	=> __( 'Import DMS Config', 'pagelines' ),
				'span'		=> 1,
				'template'	=> $this->import_template()
			),
		
		

		);


		return $settings;
		
	}
	
	// we want to get all the meta from our special posts settings.
	function get_special_settings() {
		
	}

}