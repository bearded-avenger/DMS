<?php
/*
	Section: NavBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive and sticky navigation bar for your website.
	Class Name: PLNavBar
	Workswith: header
	Compatibility: 2.2
	Cloning: false
	Filter: nav
*/

/**
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PLNavBar extends PageLinesSection {

	var $default_limit = 2;

	function section_head() {
		?>
			<!--[if IE 8]>
				<style>
					.nav-collapse.collapse {
						height: auto;
						overflow: visible;
					}
				</style>
			<![endif]-->
		<?php
	}
	
	function section_opts(){
		
		$opts = array(
			 

			array(
				'default' 	=> '',
				'key'		=> 'navbar_multi_option_theme',
				'type' 		=> 'multi',
				'opts'=> array(
					
					 array(
							'key'			=> 'navbar_theme',
							'default'		=> 'black-trans',
							'type' 			=> 'select',
							'label' 	=> __( 'Standard NavBar - Select Theme', 'pagelines' ),
							'opts'	=> array(
								'black-trans'	=> array( 'name'	=> __( 'Black Transparent (Default)', 'pagelines' ) ),
								'blue'			=> array( 'name'	=> __( 'Blue', 'pagelines' ) ),
								'grey'			=> array( 'name'	=> __( 'Light Grey', 'pagelines' ) ),
								'orange'		=> array( 'name'	=> __( 'Orange', 'pagelines' ) ),
								'red'			=> array( 'name'	=> __( 'Red', 'pagelines' ) ),
							),
						),
				),
				'title'					=> __( 'NavBar Theme', 'pagelines' ),	
				'help'					=> __( 'The NavBar comes with several color options. Select one to automatically configure.', 'pagelines' )

			),
			array(
				'key'		=> 'navbar_multi_option_menu',
				'type' 		=> 'multi',
				'title'		=> __( 'NavBar Menu', 'pagelines' ),		
				'help'		=> __( 'The NavBar uses WordPress menus. Select one for use.', 'pagelines' ),
				'opts'		=> array(
					array(
							'key'			=> 'navbar_menu' ,
							'type' 			=> 'select_menu',
							'label' 	=> __( 'Select Menu', 'pagelines' ),
						),
				),
			

			),
			array(
				'key'		=> 'navbar_multi_check',
				'type' 		=> 'multi',
				'title'					=> __( 'NavBar Configuration Options', 'pagelines' ),
				'opts'		=> array(
					array(
						'key'	=> 'navbar_title',
						'type' 		=> 'text',
						'label'=> __( 'NavBar Title', 'pagelines' ),				
						'title'		=> __( 'NavBar Title', 'pagelines' ),
						'help'		=> __( 'Add text to the NavBar to serve as a title, but only displayed on small screens.', 'pagelines' ),
					),
					array(
						'key'			=> 'navbar_enable_hover',
						'type'			=> 'check',
						'label'			=> __( 'Activate dropdowns on hover.', 'pagelines' ),
					),

					array(
						'key'			=> 'navbar_alignment',
						'type'			=> 'check',
						'label'		=> __( 'Align Menu Right? (Defaults Left)', 'pagelines' ),
					),
					array(
						'key'			=> 'navbar_hidesearch',
						'type'			=> 'check',
						'label'		=> __(  'Hide Searchform?', 'pagelines' ),
					),
				),
			
			),
			


		);
		
		return $opts;
		
	}

	function add_settings( $settings ){
		
		$settings[ $this->id ] = array(
				'name' 	=> 'NavBar', 
				'icon'	=> 'icon-list-alt',
				'pos'	=> 5,
				'opts' 	=> $this->fixed_options()
		);
		
		return $settings;
	}
	
	function fixed_options(){
		
		$fixed_opts = array(
			array(
					'key'		=> 'navbar_fixed',
					'default'	=> false,
					'version'	=> 'pro',
					'type'		=> 'check',
					'inputlabel'=> __( 'Enable Fixed Navigation Bar', 'pagelines' ),
					'title'		=> __( 'Enable Fixed Navigation Bar', 'pagelines' ),
					'help'		=> __( 'Use this feature to add the NavBar section as a fixed navigation bar on the top of your site.', 'pagelines' )
				),
			array(
					'key'		=> 'navbar_logo',
					'default'	=> $this->base_url.'/logo.png',
					'version'	=> 'pro',
					'type'		=> 'image_upload',
					'label'		=> __( 'NavBar Image', 'pagelines' ),
					'title'		=> __( 'NavBar Image', 'pagelines' ),
					'ref'		=> __( 'Use this feature to add the NavBar section as a fixed navigation bar on the top of your site.<br/><br/><strong>Notes:</strong> <br/>1. Only visible in Fixed Mode.<br/>2. Image Height is constricted to a maximum 29px.', 'pagelines' )
				),
			
		);
		
		
		$opts = $this->section_opts();
		
		foreach($opts as $index => &$opt){
			if($opt['type'] == 'multi'){
				foreach($opt['opts'] as $sub_index => &$sub_opt){
					
					if( $sub_opt['key'] == 'navbar_title' )
						unset($opt['opts'][$sub_index]);
					
					$sub_opt['key'] = 'fixed_'.$sub_opt['key'];
					
					if(isset($sub_opt['title']))
						$sub_opt['title'] = 'Fixed '.$sub_opt['title'];
						
					
				}
				unset($sub_opt);
			}
			
			$opt['key'] = 'fixed_'.$opt['key'];
			$opt['title'] = 'Fixed '.$opt['title'];
		
		}
		unset($opt);
		
		return array_merge($fixed_opts, $opts);
		
	}
	
	function section_persistent() {


		add_filter('pl_settings_array', array(&$this, 'add_settings'));

		$option_args = array(

			'name'		=> 'NavBar',
			'array'		=> $this->old_options(),
			'icon'		=> $this->icon,
			'position'	=> 6
		);

		pl_add_options_page( $option_args );

		//pl_global_option( array( 'menu' => 'header_and_footer', 'options' => $header_options, 'location' => 'top' ) );


		if($this->opt('navbar_fixed')){

			build_passive_section( array( 'sid' => $this->class_name ) );

			
			
			if(pl_has_editor()){
				
				pagelines_add_bodyclass( 'editor_navbar_fixed' );
				add_action( 'pagelines_fixed_top', array( &$this,'passive_section_template' ), 11, 2);
				
			} else {
				add_action( 'pagelines_before_page', create_function( '',  'echo pl_source_comment("Fixed NavBar Section");' ) );
				add_action( 'pagelines_before_page', array( &$this,'passive_section_template' ), 10, 2);
				pagelines_add_bodyclass( 'navbar_fixed' );
			}
				
		}



	}

	/**
	 * Load styles and scripts
	 */
	function section_styles() {

		wp_enqueue_script( 'navbar', $this->base_url.'/navbar.js', array( 'jquery' ), PL_CORE_VERSION, true );
	}

	function before_section_template( $location = '' ) {

		$format = ( $location == 'passive' ) ? 'open' : 'standard';
		$this->special_classes = ( $location == 'passive' ) ? ' fixed-top' : '';
		$this->settings['format'] = $format;
		
	}
	
	/**
	* Section template.
	*/
   function section_template( $clone_id, $location = '' ) {

	$passive = ( $location == 'passive' ) ? true : false;
	$class = array(); 
	
	// if fixed mode
	if( $passive ){

		$class[] = 'navbar-full-width';
		$content_width_class = (pl_has_editor()) ? 'pl-content' : 'content';
		$theme = ( $this->opt('fixed_navbar_theme' ) ) ? $this->opt( 'fixed_navbar_theme' ) : false;
		$align = ( $this->opt( 'fixed_navbar_alignment' ) ) ? $this->opt( 'fixed_navbar_alignment' ) : false;
		$hidesearch = ( $this->opt( 'fixed_navbar_hidesearch' ) ) ? $this->opt( 'fixed_navbar_hidesearch' ) : false;
		$menu = ( $this->opt( 'fixed_navbar_menu' ) ) ? $this->opt( 'fixed_navbar_menu' ) : null;
		$class[] = ( $this->opt( 'fixed_navbar_enable_hover' ) ) ? 'plnav_hover' : '';

	} else {

		$class[] = 'navbar-content-width';
		$content_width_class = '';
		$theme = ( $this->opt( 'navbar_theme' ) ) ? $this->opt( 'navbar_theme' ) : false;
		$align = ( $this->opt('navbar_alignment' ) ) ? $this->opt( 'navbar_alignment' ) : false;
		$hidesearch = ( $this->opt( 'navbar_hidesearch' ) ) ? $this->opt( 'navbar_hidesearch' ) : false;
		$menu = ( $this->opt( 'navbar_menu' ) ) ? $this->opt( 'navbar_menu' ) : null;
		$class[] = ( $this->opt( 'navbar_enable_hover' ) ) ? 'plnav_hover' : '';
	}

	$pull = ( $align ) ? 'right' : 'left';
	$align_class = sprintf( 'pull-%s', $pull );
	
	
	$class[] = ( $theme ) ? sprintf( 'pl-color-%s', $theme ) : 'pl-color-black-trans';

	$classes = join(' ', $class);

	$brand = ( $this->opt( 'navbar_logo' ) || $this->opt( 'navbar_logo' ) != '') ? sprintf( '<img src="%s" alt="%s" />', $this->opt( 'navbar_logo' ), esc_attr( get_bloginfo('name') ) ) : sprintf( '<h2 class="plbrand-text">%s</h2>', get_bloginfo( 'name' ) );
    $navbartitle = $this->opt( 'navbar_title' );

	?>
	<div class="navbar fix <?php echo $classes; ?>">
	  <div class="navbar-inner <?php echo $content_width_class;?>">
	    <div class="navbar-content-pad fix">
	    	<?php
	   			if($navbartitle)
				printf( '<span class="navbar-title">%s</span>',$navbartitle );
			?>
	      <a href="javascript:void(0)" class="nav-btn nav-btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </a>
			<?php if($passive):
				printf( '<a class="plbrand" href="%s" title="%s">%s</a>',
					esc_url( home_url() ),
					esc_attr( get_bloginfo('name') ),
					apply_filters('navbar_brand', $brand)
				 );
			endif; ?>
	      		<div class="nav-collapse collapse">
	       <?php 	if( !$hidesearch )
						get_search_form();
					if ( is_array( wp_get_nav_menu_items( $menu ) ) || has_nav_menu( 'primary' ) ) {
					wp_nav_menu( 
						array(
							'menu_class'		=> 'font-sub navline pldrop ' . $align_class, 
							'menu'				=> $menu,
							'container'			=> null, 
							'container_class'	=> '', 
							'depth'				=> 3, 
							'fallback_cb'		=> ''
						) 
					);
					} else {
						pl_nav_fallback( 'navline pldrop '.$align_class );
					}
	?>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
<?php }

	
	function old_options(){
			$options = array(
				'navbar_fixed' => array(
						'default'	=> false,
						'version'	=> 'pro',
						'type'		=> 'check',
						'inputlabel'=> __( 'Enable Fixed Navigation Bar', 'pagelines' ),
						'title'		=> __( 'Enable Fixed Navigation Bar', 'pagelines' ),
						'shortexp'	=> __( 'Applies a fixed navigation bar to the top of your site', 'pagelines' ),
						'exp'		=> __( 'Use this feature to add the NavBar section as a fixed navigation bar on the top of your site.', 'pagelines' )
					),
				'navbar_logo' => array(
						'default'	=> $this->base_url.'/logo.png',
						'version'	=> 'pro',
						'type'		=> 'image_upload',
						'inputlabel'=> __( 'Fixed NavBar Logo', 'pagelines' ),
						'title'		=> __( 'Fixed NavBar - NavBar Logo', 'pagelines' ),
						'shortexp'	=> __( 'Applies a fixed navigation bar to the top of your site', 'pagelines' ),
						'exp'		=> __( 'Use this feature to add the NavBar section as a fixed navigation bar on the top of your site.<br/><br/><strong>Notes:</strong> <br/>1. Only visible in Fixed Mode.<br/>2. Image Height is constricted to a maximum 29px.', 'pagelines' )
					),

				'navbar_multi_option_theme' => array(
					'default' => '',
					'type' => 'multi_option',
					'selectvalues'=> array(

						'fixed_navbar_theme' => array(
								'default'		=> 'black-trans',
								'type' 			=> 'select',
								'inputlabel' 	=> __( 'Fixed NavBar - Select Theme', 'pagelines' ),
								'selectvalues'	=> array(
									'black-trans'	=> array( 'name'	=> __( 'Black Transparent (Default)', 'pagelines' ) ),
									'blue'			=> array( 'name'	=> __( 'Blue', 'pagelines' ) ),
									'grey'			=> array( 'name'	=> __( 'Light Grey', 'pagelines' ) ),
									'orange'		=> array( 'name'	=> __( 'Orange', 'pagelines' ) ),
									'red'			=> array( 'name'	=> __( 'Red', 'pagelines' ) ),
								),
							),
						'navbar_theme' => array(
								'default'		=> 'black-trans',
								'type' 			=> 'select',
								'inputlabel' 	=> __( 'Standard NavBar - Select Theme', 'pagelines' ),
								'selectvalues'	=> array(
									'black-trans'	=> array( 'name'	=> __( 'Black Transparent (Default)', 'pagelines' ) ),
									'blue'			=> array( 'name'	=> __( 'Blue', 'pagelines' ) ),
									'grey'			=> array( 'name'	=> __( 'Light Grey', 'pagelines' ) ),
									'orange'		=> array( 'name'	=> __( 'Orange', 'pagelines' ) ),
									'red'			=> array( 'name'	=> __( 'Red', 'pagelines' ) ),
								),
							),
					),
					'title'					=> __( 'NavBar and Fixed NavBar Theme', 'pagelines' ),						
					'shortexp'				=> __( 'Select the color and theme of the NavBar', 'pagelines' ),
					'exp'					=> __( 'The NavBar comes with several color options. Select one to automatically configure.', 'pagelines' )

				),
				'navbar_multi_option_menu' => array(
					'default' => '',
					'type' => 'multi_option',
					'selectvalues'=> array(

						'fixed_navbar_menu' => array(
								'default'		=> 'black-trans',
								'type' 			=> 'select_menu',
								'inputlabel' 	=> __( 'Fixed NavBar - Select Menu', 'pagelines' ),
							),
						'navbar_menu' => array(
								'default'		=> 'black-trans',
								'type' 			=> 'select_menu',
								'inputlabel' 	=> __( 'Standard NavBar - Select Menu', 'pagelines' ),
							),
					),
					'title'					=> __( 'NavBar and Fixed NavBar Menu', 'pagelines' ),						
					'shortexp'				=> __( 'Select the WordPress Menu for the NavBar(s)', 'pagelines' ),
					'exp'					=> __( 'The NavBar uses WordPress menus. Select one for use.', 'pagelines' )

				),

				'navbar_multi_check' => array(
					'default' => '',
					'type' => 'check_multi',
					'selectvalues'=> array(

						'navbar_enable_hover'		=>	array(
							'inputlabel'			=> __( 'Activate dropdowns on hover.', 'pagelines' ),
						),

						'fixed_navbar_alignment'	=> array(
								'inputlabel'		=> __( 'Fixed NavBar - Align Menu Right? (Defaults Left)', 'pagelines' ),
							),
						'fixed_navbar_hidesearch'	=> array(
								'inputlabel'		=> __( 'Fixed NavBar - Hide Searchform?', 'pagelines' ),
							),
						'navbar_alignment'			=> array(
								'inputlabel'		=> __( 'Standard NavBar - Align Menu Right? (Defaults Left)', 'pagelines' ),
							),
						'navbar_hidesearch'			=> array(
								'inputlabel'		=> __(  'Standard NavBar - Hide Searchform?', 'pagelines' ),
							),
					),
					'inputlabel'			=> __( 'Configure Options for NavBars', 'pagelines' ),
					'title'					=> __( 'NavBar and Fixed NavBar Configuration Options', 'pagelines' ),						
					'shortexp'				=> __( 'Control various appearance options for the NavBars', 'pagelines' ),
					'exp'					=> ''			 
				),
				'navbar_title' => array(
						'type' 		=> 'text',
						'inputlabel'=> __( 'NavBar Title', 'pagelines' ),				
						'title'		=> __( 'NavBar Title', 'pagelines' ),
						'shortexp'	=> __( 'Applies text to NavBar on small screens. Not available on Fixed NavBar', 'pagelines' ),
						'exp'		=> __( 'Add text to the NavBar to serve as a title, but only displayed on small screens.', 'pagelines' ),
				),


			);
			
			return $options;
	}
}
